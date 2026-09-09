#!/usr/bin/env node
/**
 * Documentation verifier.
 *
 *     node tools/docs/verify.mjs                # check
 *     node tools/docs/verify.mjs --baseline     # re-record the backlog
 *
 * The extractor makes the inventory; this makes drift a build error. Without
 * it, 900-odd documented classes rot within two releases and nobody notices
 * until a reader does.
 *
 * Three failures, all hard:
 *
 *   1. A docs page claims a class that no longer exists in src/.
 *   2. A docs page uses a class in markup or an example that is not real.
 *   3. A class exists in src/ with no docs entry and is not in the known
 *      backlog — i.e. someone added a class and did not document it.
 *
 * The backlog in tools/docs/undocumented.txt is the honest count of what is
 * not written yet. It is allowed to shrink and never to grow: a new class must
 * either be documented or explicitly added to it, which forces the decision to
 * be made rather than defaulted.
 *
 * Classes local to the docs site itself are prefixed `dx-` and are exempt.
 * That is the whole convention: everything else in a docs page must be a real
 * Deck class, which is what catches a typo in an example.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const HERE = path.dirname(url.fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..', '..');
const API = path.join(ROOT, 'dist', 'api.json');
const DOCS = path.join(ROOT, 'public_html', 'docs');
/* Every authored page, not just the docs. `.stack-5` sat in the demo for
   months rendering the wrong gap because nothing checked it: CSS cannot throw,
   so an unknown class is silent by nature. This is where it stops being
   silent. Generated copies under assets/ are excluded — they are build output,
   not authorship. */
const AUTHORED = path.join(ROOT, 'public_html');
const SKIP_DIRS = new Set(['assets', 'node_modules', 'vendor']);
const BASELINE = path.join(HERE, 'undocumented.txt');

const DOCS_PREFIX = 'dx-';

/* is-* classes that are not states and so have no owning component. Both set
   inline-size: in `19-logical.css` the `is` prefix means inline-size, not "is",
   which collides with the state convention used everywhere else. Renaming them
   is a source fix for another pass; recorded in FINDINGS.md. */
const NOT_STATES = new Map([
  ['is-auto', 'inline-size: auto — an inline-size utility, not a state'],
  ['is-full', 'inline-size: 100% — an inline-size utility, not a state'],
]);

/* Only a class attribute that is a plain list of class tokens can be checked
   statically. One built by PHP concatenation — class="' . $variant . '" — is
   skipped here; those classes are covered instead by the page's `documents`
   claim, which is checked against the inventory just as strictly. */
const STATIC_CLASS_LIST = /^[A-Za-z0-9_\- ]+$/;
const isStatic = (value) => STATIC_CLASS_LIST.test(value.trim());

/* A page may define its own classes in an inline <style> block — the demo's
   .hero and .ramp, the docs site's dx-* chrome. Those are legitimate and
   local, so collect them per file and count them as real for that file only.
   A class that is neither in src/ nor defined on the page is the error. */
function localClasses(text) {
  const names = new Set();
  for (const block of text.matchAll(/<style\b[^>]*>([\s\S]*?)<\/style>/gi)) {
    const css = block[1].replace(/\/\*[\s\S]*?\*\//g, '');
    for (const m of css.matchAll(/\.(-?[_a-zA-Z][\w-]*)/g)) names.add(m[1]);
  }
  return names;
}

function walk(dir) {
  if (!fs.existsSync(dir)) return [];
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap((e) => {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) return SKIP_DIRS.has(e.name) ? [] : walk(p);
    return /\.(php|html)$/.test(e.name) ? [p] : [];
  });
}

function readBaseline() {
  if (!fs.existsSync(BASELINE)) return new Set();
  return new Set(
    fs.readFileSync(BASELINE, 'utf8')
      .split(/\r?\n/)
      .map((l) => l.replace(/#.*/, '').trim())
      .filter(Boolean)
  );
}

export function verifyDocs() {
  if (!fs.existsSync(API)) {
    console.error('\n  docs: dist/api.json is missing. Run tools/docs/extract.mjs first.\n');
    process.exit(1);
  }
  const api = JSON.parse(fs.readFileSync(API, 'utf8'));
  const real = new Set(api.classes.map((c) => c.name));
  const pages = walk(AUTHORED);

  const claimed = new Map();   // class -> [pages]
  const used = new Map();      // class -> [pages]
  const staleClaims = [];
  const unrealUses = [];
  const componentPages = [];

  for (const file of pages) {
    const rel = path.relative(ROOT, file).replace(/\\/g, '/');
    const text = fs.readFileSync(file, 'utf8');

    /* 1. what this page claims to document. The body must be nothing but
       quoted class names, so the example in _layout.php's own docblock cannot
       be mistaken for a claim. Partials, named with a leading underscore, are
       shell rather than pages and never claim anything. */
    const isPartial = path.basename(file).startsWith('_');

    /* A component page names the component it covers and accounts for every
       source file that styles it. `.btn` is styled in ten files; nobody
       remembers ten, so the page has to say something about each one. */
    const compMatch = isPartial ? null : text.match(/['"]component['"]\s*=>\s*['"]([A-Za-z0-9_-]+)['"]/);
    if (compMatch) {
      const accounts = new Map();
      const block = text.match(/['"]accounts['"]\s*=>\s*\[([\s\S]*?)\n\s*\]/);
      if (block) {
        for (const m of block[1].matchAll(/['"]([\w.-]+\.css)['"]\s*=>\s*['"]([^'"]*)['"]/g)) {
          accounts.set(m[1], m[2]);
        }
      }
      componentPages.push({ page: rel, component: compMatch[1], accounts });
    }

    const claim = isPartial
      ? null
      : text.match(/['"]documents['"]\s*=>\s*\[((?:\s*['"][A-Za-z0-9_-]+['"]\s*,?)+)\s*\]/);
    if (claim) {
      for (const m of claim[1].matchAll(/['"]([^'"]+)['"]/g)) {
        const name = m[1];
        if (!claimed.has(name)) claimed.set(name, []);
        claimed.get(name).push(rel);
        if (!real.has(name)) staleClaims.push({ name, page: rel });
      }
    }

    /* 2. every class this page puts in markup, including inside examples */
    const local = localClasses(text);
    for (const m of text.matchAll(/class=(["'])([\s\S]*?)\1/g)) {
      if (!isStatic(m[2])) continue;
      const at = text.slice(0, m.index).split('\n').length;
      for (const name of m[2].split(/\s+/).filter(Boolean)) {
        if (name.startsWith(DOCS_PREFIX) || local.has(name)) continue;
        if (!used.has(name)) used.set(name, []);
        used.get(name).push(rel);
        if (!real.has(name)) unrealUses.push({ name, page: rel, line: at });
      }
    }
  }

  /* ---- Component completeness --------------------------------------------
     A page that claims a component must account for every file that styles it
     and every class and state in it. Completeness stops being a matter of the
     author's memory. */
  const incomplete = [];
  for (const { page: rel, component, accounts } of componentPages) {
    const comp = api.components?.[component];
    if (!comp) {
      incomplete.push({ page: rel, kind: `no component named "${component}" in the inventory` });
      continue;
    }
    const claimedHere = new Set(
      (fs.readFileSync(path.join(ROOT, rel), 'utf8')
        .match(/['"]documents['"]\s*=>\s*\[((?:\s*['"][A-Za-z0-9_-]+['"]\s*,?)+)\s*\]/)?.[1]
        .match(/['"]([^'"]+)['"]/g) || []).map((q) => q.slice(1, -1))
    );
    for (const m of comp.members) {
      if (!claimedHere.has(m)) incomplete.push({ page: rel, kind: `class .${m} is part of the ${component} component but is not in documents` });
    }
    for (const st of comp.states) {
      if (!claimedHere.has(st)) incomplete.push({ page: rel, kind: `state .${st} belongs to ${component} but is not in documents` });
    }
    for (const f of comp.files) {
      if (!accounts.has(f)) {
        const n = comp.rules.filter((r) => r.file === f).length;
        incomplete.push({ page: rel, kind: `${f} has ${n} rule(s) affecting .${component} and is not in accounts` });
      }
    }
    for (const f of accounts.keys()) {
      if (!comp.files.includes(f)) incomplete.push({ page: rel, kind: `accounts lists ${f}, which has no rules affecting .${component}` });
    }
  }

  /* ---- State attribution -------------------------------------------------- */
  const unattributed = (api.unattributedStates || []).filter((n) => !NOT_STATES.has(n));

  const documented = new Set(claimed.keys());
  const undocumented = [...real].filter((c) => !documented.has(c)).sort();

  if (process.argv.includes('--baseline')) {
    const header =
      '# Classes with no documentation page yet. Generated by\n' +
      '#   node tools/docs/verify.mjs --baseline\n' +
      '#\n' +
      '# This is the backlog, not a permission slip. It may shrink and must never\n' +
      '# grow: a new class must be documented or added here on purpose, and the\n' +
      '# build fails either way until someone decides which.\n#\n' +
      `# ${undocumented.length} classes outstanding.\n\n`;
    fs.writeFileSync(BASELINE, header + undocumented.join('\n') + '\n');
    console.log(`\n  docs: recorded ${undocumented.length} undocumented classes in ${path.relative(ROOT, BASELINE)}\n`);
    return;
  }

  const baseline = readBaseline();
  const newlyUndocumented = undocumented.filter((c) => !baseline.has(c));
  const staleBaseline = [...baseline].filter((c) => !real.has(c)).sort();

  const problems = [];
  if (staleClaims.length) {
    problems.push([
      `${staleClaims.length} docs entr${staleClaims.length === 1 ? 'y' : 'ies'} reference a class that no longer exists in src/`,
      staleClaims.map((s) => `    .${s.name}  claimed by ${s.page}`),
    ]);
  }
  if (unrealUses.length) {
    const uniq = [...new Map(unrealUses.map((u) => [`${u.name}@${u.page}`, u])).values()];
    problems.push([
      `${uniq.length} use${uniq.length === 1 ? '' : 's'} of a class that does not exist in src/`,
      uniq.map((u) => `    .${u.name}  in ${u.page}:${u.line}`),
    ]);
  }
  if (newlyUndocumented.length) {
    problems.push([
      `${newlyUndocumented.length} class${newlyUndocumented.length === 1 ? '' : 'es'} exist in src/ with no docs entry and are not in the known backlog`,
      newlyUndocumented.slice(0, 20).map((c) => `    .${c}`)
        .concat(newlyUndocumented.length > 20 ? [`    … and ${newlyUndocumented.length - 20} more`] : []),
      'Document them, or run `node tools/docs/verify.mjs --baseline` to accept them as backlog.',
    ]);
  }
  if (incomplete.length) {
    problems.push([
      `${incomplete.length} component completeness gap${incomplete.length === 1 ? '' : 's'}`,
      incomplete.slice(0, 25).map((i) => `    ${i.page}: ${i.kind}`)
        .concat(incomplete.length > 25 ? [`    … and ${incomplete.length - 25} more`] : []),
    ]);
  }
  if (unattributed.length) {
    problems.push([
      `${unattributed.length} state class${unattributed.length === 1 ? '' : 'es'} could not be attributed to a component`,
      unattributed.map((n) => `    .${n}`),
      'Add it to a component selector, or record it in NOT_STATES with a reason.',
    ]);
  }
  if (staleBaseline.length) {
    problems.push([
      `${staleBaseline.length} backlog entr${staleBaseline.length === 1 ? 'y is' : 'ies are'} stale — the class is gone from src/`,
      staleBaseline.slice(0, 20).map((c) => `    .${c}`),
      'Run `node tools/docs/verify.mjs --baseline` to re-record.',
    ]);
  }

  const pad = (n) => String(n).padStart(4);
  console.log(`
  docs: verified ${pages.length} authored page${pages.length === 1 ? '' : 's'} against ${real.size} classes

  ${pad(documented.size)} classes documented
  ${pad(undocumented.length)} classes outstanding (${baseline.size} in the recorded backlog)
  ${pad(used.size)} distinct classes used in docs markup and examples
  ${pad(staleClaims.length)} stale claims
  ${pad(new Set(unrealUses.map((u) => u.name)).size)} classes used that do not exist
  ${pad(newlyUndocumented.length)} undocumented and unrecorded
  ${pad(staleBaseline.length)} stale backlog entries
  ${pad(componentPages.length)} component page(s), ${incomplete.length} completeness gap(s)
  ${pad((api.counts?.states) || 0)} state classes, ${unattributed.length} unattributed (${NOT_STATES.size} exempt: ${[...NOT_STATES.keys()].join(', ')})`);

  if (!problems.length) {
    console.log('\n  No drift.\n');
    return;
  }

  console.error('\n  docs: verification failed\n');
  for (const [title, lines, hint] of problems) {
    console.error(`  ${title}:`);
    for (const l of lines) console.error(l);
    if (hint) console.error(`    ${hint}`);
    console.error('');
  }
  process.exit(1);
}

/* Runs standalone as well as being imported by build.mjs. */
if (process.argv[1]?.endsWith('verify.mjs')) verifyDocs();
