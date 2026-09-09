#!/usr/bin/env node
/**
 * Documentation extractor.
 *
 *     node tools/docs/extract.mjs
 *
 * Walks src/*.css and emits dist/api.json: every class Deck defines, the file
 * and line it is defined on, the cascade layer it sits in, its declarations,
 * the sibling classes that share its prefix, and the comment block written
 * immediately above it.
 *
 * Deck's source is already heavily commented, so those comments are the raw
 * material for the reference. Nothing about the class inventory is typed by
 * hand anywhere in the docs; drift is caught by tools/docs/verify.mjs.
 *
 * The parser is a small character scanner rather than a regex pass, because
 * strings, url() values, and selectors like :has(use[href$="#x"]) all contain
 * characters that a regex would misread as structure.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const HERE = path.dirname(url.fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..', '..');
const SRC = path.join(ROOT, 'src');
const OUT = path.join(ROOT, 'dist', 'api.json');
const TOKENS_FILE = '01-tokens.css';

/* A comment that is a rule of dashes with a label in it is a section divider,
   not documentation for whichever rule happens to follow. Deck writes them as
   `--- Variants ---------` and `========== 05 Buttons ==========`. This is
   tested against the RAW comment body, before the dashes are stripped, and it
   is what keeps the "documented" count honest rather than flattering. */
const isDivider = (raw) => {
  const t = raw.trim();
  return /^[-=]{3,}/.test(t) || /[-=]{5,}\s*$/.test(t) || /^[-=\s]*$/.test(t);
};

const cleanComment = (raw) =>
  raw
    .split('\n')
    .map((l) => l.replace(/^\s*\*?\s?/, '').replace(/\s+$/, ''))
    .join('\n')
    .replace(/^[-=\s]+|[-=\s]+$/g, '')
    .trim();

/* ---------------------------------------------------------------------------
   Parser
   ------------------------------------------------------------------------ */

function parse(css, file) {
  const rules = [];
  const atStack = [];          // { name, prelude }
  let i = 0;
  let line = 1;
  let buf = '';                // selector / at-rule prelude being accumulated
  let bufLine = 1;
  let pending = null;          // the comment block most recently closed

  const layerOf = () => {
    for (let k = atStack.length - 1; k >= 0; k--) {
      if (atStack[k].name === 'layer' && atStack[k].prelude) return atStack[k].prelude.trim();
    }
    return null;
  };
  const conditionsOf = () =>
    atStack
      .filter((a) => a.name !== 'layer' && a.prelude)
      .map((a) => `@${a.name} ${a.prelude.trim()}`);

  while (i < css.length) {
    const c = css[i];

    if (c === '\n') { line++; i++; buf += c; continue; }

    /* comment */
    if (c === '/' && css[i + 1] === '*') {
      const end = css.indexOf('*/', i + 2);
      const stop = end === -1 ? css.length : end + 2;
      const body = css.slice(i + 2, stop - 2);
      const startLine = line;
      for (const ch of css.slice(i, stop)) if (ch === '\n') line++;
      const text = cleanComment(body);
      /* A ruled block is a section header when it is only a label, and
         documentation when it carries prose. Deck writes many of both, and
         several of its best explanations live inside a ruled block:

           /* --- Icons ------------------------------------------------
              Icons inherit color and font-size, so they line up with text.
              ------------------------------------------------------- *​/

         Treating every ruled block as a section threw that away and reported
         .icon, .card and .emoji as undocumented when they are anything but. */
      const ruled = isDivider(body);
      const lines = text.split('\n').filter((l) => l.trim());
      const labelOnly = lines.length <= 1 && text.length <= 48;
      const divider = ruled && labelOnly;
      const sectionTitle = ruled && !labelOnly
        ? lines[0].replace(/[-=\s]+$/, '').trim()
        : null;
      const prose = ruled && !labelOnly ? lines.slice(1).join('\n').trim() : text;
      /* Only a comment that is alone on the lines directly above a rule counts
         as documentation for it, so a comment that trails a declaration on the
         same line is ignored. */
      const before = css.slice(0, i);
      const aloneOnLine = /(^|\n)[ \t]*$/.test(before);
      pending = aloneOnLine && text
        ? {
            text: divider ? text : prose,
            sectionTitle,
            line: startLine,
            kind: divider ? 'section' : 'doc',
          }
        : pending;
      i = stop;
      buf = '';
      continue;
    }

    /* string */
    if (c === '"' || c === "'") {
      const q = c;
      let j = i + 1;
      while (j < css.length && !(css[j] === q && css[j - 1] !== '\\')) {
        if (css[j] === '\n') line++;
        j++;
      }
      buf += css.slice(i, j + 1);
      i = j + 1;
      continue;
    }

    if (c === '{') {
      const prelude = buf.trim();
      buf = '';
      i++;

      if (prelude.startsWith('@')) {
        const m = prelude.match(/^@([\w-]+)\s*([\s\S]*)$/);
        atStack.push({ name: m ? m[1] : '', prelude: m ? m[2] : '' });
        pending = null;
        continue;
      }

      /* a style rule: read to its matching brace */
      let depth = 1;
      let body = '';
      while (i < css.length && depth > 0) {
        const d = css[i];
        if (d === '/' && css[i + 1] === '*') {
          const end = css.indexOf('*/', i + 2);
          const stop = end === -1 ? css.length : end + 2;
          for (const ch of css.slice(i, stop)) if (ch === '\n') line++;
          i = stop;
          continue;
        }
        if (d === '"' || d === "'") {
          const q = d;
          let j = i + 1;
          while (j < css.length && !(css[j] === q && css[j - 1] !== '\\')) { if (css[j] === '\n') line++; j++; }
          body += css.slice(i, j + 1);
          i = j + 1;
          continue;
        }
        if (d === '{') depth++;
        if (d === '}') { depth--; if (depth === 0) { i++; break; } }
        if (d === '\n') line++;
        body += d;
        i++;
      }

      rules.push({
        selectors: splitSelectors(prelude),
        declarations: readDeclarations(body),
        file,
        line: bufLine,
        layer: layerOf(),
        conditions: conditionsOf(),
        comment: pending && pending.kind === 'doc' ? pending.text : null,
        section: pending
          ? (pending.kind === 'section' ? pending.text : pending.sectionTitle)
          : null,
      });
      pending = null;
      continue;
    }

    if (c === '}') {
      atStack.pop();
      buf = '';
      i++;
      continue;
    }

    if (c === ';' && !buf.trim().startsWith('@')) { buf = ''; i++; continue; }
    if (c === ';') { buf = ''; i++; continue; }

    if (!buf.trim()) bufLine = line;
    buf += c;
    i++;
  }

  return rules;
}

/** Split a declaration block into { prop, value } pairs. Nested blocks were
 *  already consumed by the caller, so this only sees flat declarations. */
function readDeclarations(body) {
  const out = [];
  let depth = 0;
  let current = '';
  for (let i = 0; i < body.length; i++) {
    const c = body[i];
    if (c === '(') depth++;
    if (c === ')') depth--;
    if (c === ';' && depth === 0) { push(current); current = ''; continue; }
    current += c;
  }
  push(current);
  function push(chunk) {
    const t = chunk.trim();
    if (!t) return;
    const at = t.indexOf(':');
    if (at < 0) return;
    const prop = t.slice(0, at).trim();
    const value = t.slice(at + 1).trim().replace(/\s+/g, ' ');
    if (!prop || prop.includes('{')) return;
    out.push({ prop, value });
  }
  return out;
}

/**
 * Split a selector list on its top-level commas only.
 *
 * `.will-move:is(:hover, :focus-within, .is-animating)` is ONE selector, not
 * three. Splitting naively produced a fragment reading `.is-animating)` with no
 * companion class, which made the state look unattributable when it is simply
 * a state of .will-move.
 */
function splitSelectors(prelude) {
  const out = [];
  let depth = 0;
  let current = '';
  for (const c of prelude) {
    if (c === '(' || c === '[') depth++;
    else if (c === ')' || c === ']') depth--;
    if (c === ',' && depth === 0) { out.push(current); current = ''; continue; }
    current += c;
  }
  out.push(current);
  return out.map((s) => s.trim().replace(/\s+/g, ' ')).filter(Boolean);
}

/** Every class name a selector defines or targets. */
function classesIn(selector) {
  const found = [];
  /* strip attribute values so [href$=".btn"] cannot masquerade as a class */
  const bare = selector.replace(/\[[^\]]*\]/g, '[]');
  for (const m of bare.matchAll(/\.(-?[_a-zA-Z][\w-]*)/g)) found.push(m[1]);
  return found;
}

/* ---------------------------------------------------------------------------
   Tokens
   ------------------------------------------------------------------------ */

function extractTokens(rules) {
  const tokens = [];
  const seen = new Set();
  for (const rule of rules) {
    if (rule.file !== TOKENS_FILE) continue;
    for (const d of rule.declarations) {
      if (!d.prop.startsWith('--') || seen.has(d.prop)) continue;
      seen.add(d.prop);
      tokens.push({
        name: d.prop,
        value: d.value,
        file: rule.file,
        line: rule.line,
        scope: rule.selectors.join(', '),
        conditions: rule.conditions,
        doc: rule.comment,
        group: rule.section || null,
        /* Anything a live swatch can show. */
        kind: /color|hue|brand|ink|surface|text|line|good|warn|bad|accent|focus/.test(d.prop)
          ? 'color'
          : /space|size|gap|h-|radius|^--r-/.test(d.prop)
            ? 'length'
            : /dur|ease/.test(d.prop)
              ? 'motion'
              : 'other',
      });
    }
  }
  return tokens;
}

/* ---------------------------------------------------------------------------
   Run
   ------------------------------------------------------------------------ */

function main() {
  const files = fs.readdirSync(SRC).filter((f) => f.endsWith('.css')).sort();
  const rules = [];
  for (const f of files) {
    rules.push(...parse(fs.readFileSync(path.join(SRC, f), 'utf8'), f));
  }

  /* class name -> entry */
  const classes = new Map();
  for (const rule of rules) {
    for (const sel of rule.selectors) {
      const names = classesIn(sel);
      /* The subject of the selector is its last class; the others are context
         (`.btn-group > .btn` documents .btn, under .btn-group). */
      names.forEach((name, idx) => {
        if (!classes.has(name)) {
          classes.set(name, {
            name,
            file: rule.file,
            line: rule.line,
            layer: rule.layer,
            section: rule.section,
            doc: null,
            declarations: [],
            selectors: [],
            variants: [],
            tokensUsed: [],
            defined: false,
          });
        }
        const entry = classes.get(name);
        const isSubject = idx === names.length - 1;
        entry.selectors.push({ selector: sel, file: rule.file, line: rule.line, conditions: rule.conditions });

        /* The defining rule is the simplest one whose subject is this class:
           `.btn { … }` rather than `.btn:hover` or `.btn-group > .btn`. */
        /* The defining rule is the FIRST plain `.name { … }` in file order.
           Later ones are overrides — .btn is redefined in deck.print, and
           pointing the docs at the print sheet would be actively misleading. */
        const simple = isSubject && sel === `.${name}`;
        if (simple && !entry.defined) {
          entry.defined = true;
          entry.file = rule.file;
          entry.line = rule.line;
          entry.layer = rule.layer;
          entry.declarations = rule.declarations;
        }
        /* Only take prose from the file the class is defined in. .fab is
           redefined in deck.print under a "should never print" divider, and
           borrowing that as its section would describe the wrong thing. */
        const sameFile = rule.file === entry.file;
        if (simple && sameFile && rule.section && !entry.section) entry.section = rule.section;
        if (simple && sameFile && rule.comment && !entry.doc) entry.doc = rule.comment;
        if (!simple && isSubject && sameFile && !entry.doc && rule.comment && sel.startsWith(`.${name}`)) {
          entry.doc = rule.comment;
        }
        for (const d of rule.declarations) {
          for (const m of d.value.matchAll(/var\((--[\w-]+)/g)) {
            if (isSubject && !entry.tokensUsed.includes(m[1])) entry.tokensUsed.push(m[1]);
          }
        }
      });
    }
  }

  /* Variants: classes sharing a prefix with a shorter defined class. */
  const names = [...classes.keys()].sort();
  for (const entry of classes.values()) {
    entry.variants = names.filter(
      (n) => n !== entry.name && n.startsWith(`${entry.name}-`)
    );
  }

  /* ---- State attribution --------------------------------------------------
     An is-* class is a state OF something. The something is whichever class it
     is compounded with or descended from: `.chip.is-active` makes .chip an
     owner of .is-active, `.coverflow > .is-behind-end` makes .coverflow one.
     A state with no owner is either dead or misnamed, and the verifier fails
     on it rather than leaving it unattributable. */
  const rootOf = (name) => {
    let best = name;
    for (const candidate of classes.keys()) {
      if (name.startsWith(`${candidate}-`) && candidate.length < best.length) best = candidate;
    }
    return best;
  };
  for (const entry of classes.values()) {
    if (!entry.name.startsWith('is-')) continue;
    const owners = new Set();
    const direct = new Set();
    for (const { selector } of entry.selectors) {
      for (const other of classesIn(selector)) {
        if (other === entry.name || other.startsWith('is-')) continue;
        if (!classes.has(other)) continue;
        direct.add(other);
        owners.add(rootOf(other));
      }
    }
    /* `owners` is reduced to component roots so a page can claim a state;
       `ownerClasses` keeps the exact classes, because the reduction conflates
       families that merely share a prefix (.stack the layout primitive and
       .stack-depth the 3D pile both reduce to `stack`). */
    entry.owners = [...owners].sort();
    entry.ownerClasses = [...direct].sort();
    entry.role = entry.owners.length ? 'state' : 'unattributed';
  }

  /* ---- Component inventories ----------------------------------------------
     A component page is complete when it has accounted for every rule that
     touches its component, wherever that rule lives. .btn is styled in nine
     files; no author reliably remembers all nine. This produces the list so
     the verifier can require it. */
  const componentRoots = [...classes.keys()].filter((name) => {
    if (name.startsWith('is-')) return false;
    const hasVariants = [...classes.keys()].some((n) => n !== name && n.startsWith(`${name}-`));
    const isExtension = [...classes.keys()].some((n) => n !== name && name.startsWith(`${n}-`));
    return hasVariants && !isExtension;
  }).sort();

  const components = {};
  for (const root of componentRoots) {
    const members = [root, ...[...classes.keys()].filter((n) => n.startsWith(`${root}-`))].sort();
    const seen = new Set();
    const ruleList = [];
    for (const member of members) {
      for (const s of classes.get(member).selectors) {
        const key = `${s.file}:${s.line}:${s.selector}`;
        if (seen.has(key)) continue;
        seen.add(key);
        ruleList.push({ selector: s.selector, file: s.file, line: s.line, conditions: s.conditions });
      }
    }
    ruleList.sort((a, b) => a.file.localeCompare(b.file) || a.line - b.line);
    components[root] = {
      members,
      files: [...new Set(ruleList.map((r) => r.file))].sort(),
      states: [...classes.values()]
        .filter((c) => c.role === 'state' && c.owners.includes(root))
        .map((c) => c.name),
      ruleCount: ruleList.length,
      rules: ruleList,
    };
  }

  const tokens = extractTokens(rules);
  const list = [...classes.values()].sort((a, b) => a.name.localeCompare(b.name));

  const documented = list.filter((c) => c.doc).length;
  const states = list.filter((c) => c.name.startsWith('is-'));
  const unattributed = states.filter((c) => c.role === 'unattributed').map((c) => c.name);

  const api = {
    generated: 'tools/docs/extract.mjs — do not edit',
    stylesheets: files.length,
    counts: {
      classes: list.length,
      documented,
      undocumented: list.length - documented,
      tokens: tokens.length,
      rules: rules.length,
      components: Object.keys(components).length,
      states: states.length,
      unattributedStates: unattributed.length,
    },
    unattributedStates: unattributed,
    components,
    layers: [...new Set(list.map((c) => c.layer).filter(Boolean))].sort(),
    classes: list,
    tokens,
  };

  fs.mkdirSync(path.dirname(OUT), { recursive: true });
  fs.writeFileSync(OUT, `${JSON.stringify(api, null, 2)}\n`);

  const pct = ((documented / list.length) * 100).toFixed(1);
  console.log(`
  docs: wrote ${path.relative(ROOT, OUT)}

    ${files.length} stylesheets, ${rules.length} rules
    ${list.length} classes across ${api.layers.length} layers
    ${documented} documented, ${list.length - documented} undocumented (${pct}% covered)
    ${tokens.length} tokens
    ${Object.keys(components).length} components, ${states.length} states${unattributed.length ? `, ${unattributed.length} unattributed` : ', all attributed'}
`);
  return api;
}

const api = main();
export default api;
