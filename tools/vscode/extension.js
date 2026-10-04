/* =============================================================================
   tools/vscode/extension.js — Deck IntelliSense
   -----------------------------------------------------------------------------
   Plain CommonJS on purpose. Deck's whole argument is that you should not need
   a toolchain to use it, and an extension for Deck that needed a TypeScript
   build to read would be arguing against its own framework. There is nothing to
   compile: `vsce package` ships this file as it is.

   What it does, in the order the features matter:

   1. DIAGNOSTICS. The reason this exists. Deck's spacing scales skip 5 and 7
      (0, 1, 2, 3, 4, 6, 8), so `.stack-5` is not a class — but `.stack` alone
      is, and it carries a default gap. Write `.stack-5` and the browser applies
      the default, the page looks almost right, and nothing anywhere tells you.
      That bug shipped once. An editor squiggle is the only place it can be
      caught cheaply, which is why diagnostics come before completion here.

   2. COMPLETION of the 828 public classes inside class attributes, the 144
      tokens inside var( ), and the 152 icon symbols after the sprite's #.

   3. HOVER showing what a class actually declares, which layer owns it, and
      where it lives in the source.

   4. GO TO DEFINITION, opening the real rule in Deck's src/.

   Everything it knows comes from data/deck-data.json, which generate.mjs builds
   from dist/api.json and dist/api-buckets.json. Nothing here is hand-kept, so
   nothing here can disagree with the stylesheet it describes.
   ============================================================================= */

'use strict';

const vscode = require('vscode');
const path = require('path');
const fs = require('fs');

/* ========================================================================== *
   Data
   ========================================================================== */

let DATA = null;
let PUBLIC = new Map();   // name -> record
let REMOVED = {};
let INTERNAL = {};
let FAMILIES = {};
let TOKENS = new Map();   // --name -> record
let ICONS = [];

/* Completion items are built once at activation. Rebuilding 828 items on every
   keystroke is the difference between an extension that feels native and one
   people uninstall. */
let CLASS_ITEMS = [];
let TOKEN_ITEMS = [];
let ICON_ITEMS = [];

function loadData(context) {
  const file = path.join(context.extensionPath, 'data', 'deck-data.json');
  DATA = JSON.parse(fs.readFileSync(file, 'utf8'));

  PUBLIC = new Map(DATA.classes.map((c) => [c.n, c]));
  REMOVED = DATA.removed || {};
  INTERNAL = DATA.internal || {};
  FAMILIES = DATA.families || {};
  TOKENS = new Map((DATA.tokens || []).map((t) => [t.n, t]));
  ICONS = DATA.icons || [];

  buildItems();
}

/* --- Presentation helpers ------------------------------------------------- */

const LAYER_LABEL = {
  'deck.reset': 'reset',
  'deck.tokens': 'tokens',
  'deck.type': 'type',
  'deck.layout': 'layout',
  'deck.components': 'component',
  'deck.mobile': 'mobile',
  'deck.motion': 'motion',
  'deck.effects': 'effects',
  'deck.utilities': 'utility',
  'deck.rtl': 'rtl',
  'deck.print': 'print',
};

/* Components and layout primitives are what people reach for; utilities are
   numerous and would otherwise bury them in the list. sortText groups rather
   than reorders, so typing still filters normally. */
function sortGroup(layer) {
  if (layer === 'deck.components') return '1';
  if (layer === 'deck.layout') return '2';
  if (layer === 'deck.type') return '3';
  if (layer === 'deck.utilities') return '5';
  return '4';
}

function classDoc(c) {
  const md = new vscode.MarkdownString();
  md.supportHtml = false;

  if (c.d) md.appendMarkdown(c.d + '\n\n');

  if (c.role) {
    md.appendMarkdown(
      `**State class.** Deck's JavaScript applies this one; markup does not normally set it by hand.\n\n`
    );
    if (c.owners && c.owners.length) {
      md.appendMarkdown(`Applies to: ${c.owners.map((o) => '`.' + o + '`').join(', ')}\n\n`);
    }
  }

  if (c.cond) {
    md.appendMarkdown(
      `**Conditional.** Only declared inside a media or container query, so it does nothing at other sizes.\n\n`
    );
  }

  if (c.x && c.x.length) {
    md.appendCodeblock('.' + c.n + ' {\n  ' + c.x.join(';\n  ') + ';\n}', 'css');
  }

  if (c.v && c.v.length) {
    const shown = c.v.slice(0, 12).map((v) => '`.' + v + '`').join(' · ');
    md.appendMarkdown(
      `\n**In this family:** ${shown}${c.v.length > 12 ? ` _+${c.v.length - 12} more_` : ''}\n`
    );
  }

  if (c.f) {
    md.appendMarkdown(`\n\`src/${c.f}\`${c.ln ? ':' + c.ln : ''}`);
  }
  return md;
}

function tokenDoc(t) {
  const md = new vscode.MarkdownString();
  if (t.d) md.appendMarkdown(t.d + '\n\n');
  md.appendCodeblock(`${t.n}: ${t.v};`, 'css');
  if (t.cond) {
    md.appendMarkdown(
      `\n**Redefined conditionally** — dark mode or another query gives this a different value.\n`
    );
  }
  if (t.g) md.appendMarkdown(`\nGroup: ${t.g}\n`);
  if (t.f) md.appendMarkdown(`\n\`src/${t.f}\`${t.ln ? ':' + t.ln : ''}`);
  return md;
}

function buildItems() {
  /* Classes */
  CLASS_ITEMS = DATA.classes.map((c) => {
    const it = new vscode.CompletionItem(c.n, vscode.CompletionItemKind.Constant);
    it.detail = LAYER_LABEL[c.l] || 'deck';
    it.documentation = classDoc(c);
    it.sortText = sortGroup(c.l) + c.n;
    it.filterText = c.n;
    return it;
  });

  /* Internal classes are offered too, at the bottom and marked, because
     somebody debugging Deck itself needs them. They are never silently equal
     to a public class in the list. */
  for (const [name, reason] of Object.entries(INTERNAL)) {
    const it = new vscode.CompletionItem(name, vscode.CompletionItemKind.Constant);
    it.detail = 'internal';
    it.documentation = new vscode.MarkdownString(
      `**Internal to Deck.** ${reason}\n\nNot part of the public API — it can change in a patch release.`
    );
    it.sortText = '9' + name;
    CLASS_ITEMS.push(it);
  }

  /* Tokens. Colour kind makes VS Code draw a swatch from `detail`. */
  TOKEN_ITEMS = DATA.tokens.map((t) => {
    const isColor = t.k === 'color';
    const it = new vscode.CompletionItem(
      t.n,
      isColor ? vscode.CompletionItemKind.Color : vscode.CompletionItemKind.Variable
    );
    it.detail = t.v;
    it.documentation = tokenDoc(t);
    it.sortText = (isColor ? '1' : t.k === 'length' ? '2' : '3') + t.n;
    return it;
  });

  /* Icons */
  ICON_ITEMS = ICONS.map((id) => {
    const it = new vscode.CompletionItem(id, vscode.CompletionItemKind.Value);
    it.detail = 'Deck icon';
    it.documentation = new vscode.MarkdownString(
      `Icon \`#${id}\` from the Deck sprite.\n\n` +
        '```html\n<svg class="icon"><use href="/assets/deck/deck-icons.svg#' +
        id +
        '"></use></svg>\n```\n\n' +
        '_The sprite must be served from your own origin — a cross-origin `<use href>` renders nothing._'
    );
    return it;
  });
}

/* ========================================================================== *
   Context detection
   --------------------------------------------------------------------------
   Deciding whether the cursor sits inside a class attribute. Done by text, not
   by a parser, because the same extension has to work in PHP, Blade, Twig,
   JSX, Vue and Svelte, and none of them agree on grammar.
   ========================================================================== */

const CLASS_ATTR_OPEN = /\b(?:class|className|classList)\s*=\s*(["'`])/g;

/** The partial class list typed so far, or null if the cursor is not in one. */
function classAttrPrefix(before) {
  CLASS_ATTR_OPEN.lastIndex = 0;
  let m;
  let last = null;
  while ((m = CLASS_ATTR_OPEN.exec(before))) last = m;
  if (!last) return null;

  const quote = last[1];
  const inside = before.slice(last.index + last[0].length);
  /* If the quote closed before the cursor, we are past the attribute. */
  if (inside.includes(quote)) return null;
  return inside;
}

const VAR_OPEN = /var\(\s*(--[A-Za-z0-9-]*)$/;
const ICON_REF = /deck-icons\.svg#([A-Za-z0-9-]*)$/;

function isMarkup(languageId) {
  const extra = vscode.workspace.getConfiguration('deck').get('languages') || [];
  return BUILTIN_MARKUP.has(languageId) || extra.includes(languageId);
}

const BUILTIN_MARKUP = new Set([
  'html', 'php', 'blade', 'twig', 'handlebars', 'erb', 'django-html',
  'jinja-html', 'razor', 'astro', 'vue', 'svelte',
  'javascript', 'javascriptreact', 'typescript', 'typescriptreact',
]);

const STYLE_LANGS = new Set(['css', 'scss', 'less', 'postcss']);

/* ========================================================================== *
   Completion
   ========================================================================== */

function registerCompletion(context) {
  const cfg = () => vscode.workspace.getConfiguration('deck');

  const provider = {
    provideCompletionItems(doc, pos) {
      if (!cfg().get('enable')) return;
      const line = doc.lineAt(pos.line).text.slice(0, pos.character);
      /* Look back a little further than the current line so a class attribute
         wrapped across lines still completes. */
      const start = new vscode.Position(Math.max(0, pos.line - 8), 0);
      const before = doc.getText(new vscode.Range(start, pos));

      /* Icons first: the most specific context wins. */
      if (cfg().get('completion.icons') && ICON_REF.test(line)) {
        return new vscode.CompletionList(ICON_ITEMS, false);
      }

      /* Tokens, both in a style attribute and in a stylesheet. */
      if (cfg().get('completion.tokens') && VAR_OPEN.test(line)) {
        return new vscode.CompletionList(TOKEN_ITEMS, false);
      }

      /* Classes, only inside a class attribute. */
      if (
        cfg().get('completion.classes') &&
        isMarkup(doc.languageId) &&
        classAttrPrefix(before) !== null
      ) {
        return new vscode.CompletionList(CLASS_ITEMS, false);
      }

      return undefined;
    },
  };

  const markupSelectors = [...BUILTIN_MARKUP].map((language) => ({ language, scheme: '*' }));
  const styleSelectors = [...STYLE_LANGS].map((language) => ({ language, scheme: '*' }));

  context.subscriptions.push(
    vscode.languages.registerCompletionItemProvider(
      [...markupSelectors, ...styleSelectors],
      provider,
      /* Trigger characters: a dash opens token completion, a hash opens icons,
         a quote and a space open class completion mid-attribute. */
      '-', '#', '"', "'", '`', ' '
    )
  );
}

/* ========================================================================== *
   Hover
   ========================================================================== */

function registerHover(context) {
  const provider = {
    provideHover(doc, pos) {
      if (!vscode.workspace.getConfiguration('deck').get('enable')) return;

      /* A token under the cursor, in markup or CSS alike. */
      const tokenRange = doc.getWordRangeAtPosition(pos, /--[A-Za-z0-9-]+/);
      if (tokenRange) {
        const t = TOKENS.get(doc.getText(tokenRange));
        if (t) return new vscode.Hover(tokenDoc(t), tokenRange);
      }

      /* An icon id after the sprite's hash. */
      const iconRange = doc.getWordRangeAtPosition(pos, /[A-Za-z0-9-]+/);
      if (iconRange) {
        const lineToRange = doc
          .lineAt(pos.line)
          .text.slice(0, iconRange.start.character);
        if (/deck-icons\.svg#$/.test(lineToRange)) {
          const id = doc.getText(iconRange);
          if (ICONS.includes(id)) {
            return new vscode.Hover(
              new vscode.MarkdownString(`Deck icon \`#${id}\` — one of ${ICONS.length} in the sprite.`),
              iconRange
            );
          }
        }
      }

      /* A class name, but only if the cursor really is in a class attribute. */
      if (!isMarkup(doc.languageId) && !STYLE_LANGS.has(doc.languageId)) return;
      const wordRange = doc.getWordRangeAtPosition(pos, /[A-Za-z0-9_-]+/);
      if (!wordRange) return;
      const word = doc.getText(wordRange);

      const inAttr =
        classAttrPrefix(
          doc.getText(new vscode.Range(new vscode.Position(Math.max(0, pos.line - 8), 0), wordRange.start))
        ) !== null;
      const inCss =
        STYLE_LANGS.has(doc.languageId) &&
        doc.lineAt(pos.line).text[wordRange.start.character - 1] === '.';
      if (!inAttr && !inCss) return;

      const c = PUBLIC.get(word);
      if (c) return new vscode.Hover(classDoc(c), wordRange);

      if (REMOVED[word]) {
        const r = REMOVED[word];
        return new vscode.Hover(
          new vscode.MarkdownString(
            `**\`.${word}\` was removed from Deck.** ${r.reason}` +
              (r.replace ? `\n\nUse \`.${r.replace}\` instead.` : '')
          ),
          wordRange
        );
      }
      if (INTERNAL[word]) {
        return new vscode.Hover(
          new vscode.MarkdownString(
            `**\`.${word}\` is internal to Deck.** ${INTERNAL[word]}\n\n` +
              `It is not part of the public API and can change in a patch release.`
          ),
          wordRange
        );
      }
      return undefined;
    },
  };

  context.subscriptions.push(
    vscode.languages.registerHoverProvider(
      [...BUILTIN_MARKUP, ...STYLE_LANGS].map((language) => ({ language, scheme: '*' })),
      provider
    )
  );
}

/* ========================================================================== *
   Diagnostics
   --------------------------------------------------------------------------
   The feature that pays for the extension. See the thresholds below; each one
   is set where it is to avoid underlining markup that is actually correct,
   because an extension that cries wolf gets disabled and then catches nothing.
   ========================================================================== */

/** Edit distance, abandoned as soon as it exceeds `cap`. */
function distance(a, b, cap) {
  if (a === b) return 0;
  if (Math.abs(a.length - b.length) > cap) return cap + 1;
  let prev = Array.from({ length: b.length + 1 }, (_, i) => i);
  for (let i = 1; i <= a.length; i++) {
    const cur = [i];
    let rowMin = i;
    for (let j = 1; j <= b.length; j++) {
      cur[j] = Math.min(
        prev[j] + 1,
        cur[j - 1] + 1,
        prev[j - 1] + (a[i - 1] === b[j - 1] ? 0 : 1)
      );
      if (cur[j] < rowMin) rowMin = cur[j];
    }
    if (rowMin > cap) return cap + 1;
    prev = cur;
  }
  return prev[b.length];
}

/* A generic typo suggestion needs a token this long. Below it, almost any
   string is one edit from something: `.row` is one edit from `.grow`, and
   `.row` is Bootstrap's grid class, which turns up constantly in markup being
   migrated. Flagging it would be wrong and loud. The numeric-scale check below
   has no length floor, because that case is unambiguous. */
const MIN_TYPO_LENGTH = 5;

/** Classify one class token. Returns null when there is nothing to say. */
function classify(token, cfg) {
  if (PUBLIC.has(token)) {
    const c = PUBLIC.get(token);
    if (c.role && cfg.states) {
      return {
        severity: vscode.DiagnosticSeverity.Hint,
        message:
          `.${token} is a state class — Deck's JavaScript applies it. ` +
          `Setting it by hand usually means the component is being driven the wrong way.`,
      };
    }
    return null;
  }

  if (REMOVED[token]) {
    const r = REMOVED[token];
    return {
      severity: vscode.DiagnosticSeverity.Error,
      message:
        `.${token} was removed from Deck ${DATA.version}. ${r.reason}` +
        (r.replace ? ` Use .${r.replace}.` : ''),
      fix: r.replace || null,
    };
  }

  if (INTERNAL[token]) {
    if (!cfg.internal) return null;
    return {
      severity: vscode.DiagnosticSeverity.Warning,
      message:
        `.${token} is internal to Deck and can change in a patch release. ${INTERNAL[token]}`,
    };
  }

  if (cfg.unknown === 'off') return null;

  /* --- The off-the-scale number ----------------------------------------- *
     Deck's spacing scales are 0, 1, 2, 3, 4, 6, 8. `.stack-5` is not a class,
     but `.stack` is, so the browser falls back to the default gap and the page
     looks nearly right. This is the exact bug the extension exists to catch,
     so it fires at any token length and regardless of the `all`/`likely`
     setting.
     -------------------------------------------------------------------- */
  const dash = token.lastIndexOf('-');
  if (dash > 0) {
    const root = token.slice(0, dash);
    const suffix = token.slice(dash + 1);
    const fam = FAMILIES[root];
    if (fam) {
      if (/^\d+$/.test(suffix) && fam.num) {
        const scale = fam.s.filter((s) => /^\d+$/.test(s)).map(Number);
        const want = Number(suffix);
        const best = Math.min(...scale.map((n) => Math.abs(n - want)));
        /* `.stack-5` sits exactly between 4 and 6. Picking one would be a
           guess about whether the author wanted a tighter or looser gap, so
           offer both and let them choose. */
        const nearest = scale.filter((n) => Math.abs(n - want) === best);
        return {
          severity: vscode.DiagnosticSeverity.Error,
          message:
            `.${token} is not a Deck class — the ${root} scale is ${scale.join(', ')}. ` +
            `.${root} on its own is valid, so this silently falls back to the default instead of failing.`,
          fix: nearest.map((n) => `${root}-${n}`),
        };
      }
      /* One character off a real sibling: .btn-primery for .btn-primary. */
      for (const s of fam.s) {
        if (distance(suffix, s, 1) <= 1) {
          return {
            severity: vscode.DiagnosticSeverity.Error,
            message: `.${token} is not a Deck class. Did you mean .${root}-${s}?`,
            fix: `${root}-${s}`,
          };
        }
      }
    }
  }

  /* --- A one-character slip on a whole name ----------------------------- */
  if (token.length >= MIN_TYPO_LENGTH) {
    for (const name of PUBLIC.keys()) {
      if (distance(token, name, 1) <= 1) {
        return {
          severity: vscode.DiagnosticSeverity.Error,
          message: `.${token} is not a Deck class. Did you mean .${name}?`,
          fix: name,
        };
      }
    }
  }

  /* --- Everything else --------------------------------------------------- *
     Silent by default. An unrecognised class is far more often the author's
     own than a mistake, and this is the judgement that decides whether the
     extension is usable in a real project.
     -------------------------------------------------------------------- */
  if (cfg.unknown === 'all') {
    return {
      severity: vscode.DiagnosticSeverity.Information,
      message: `.${token} is not defined by Deck ${DATA.version}.`,
    };
  }
  return null;
}

/* Template expressions are blanked before tokenising, so PHP short echoes,
   Blade braces and JS interpolation never look like class names. Blanking
   preserves length, which keeps every offset below correct. */
const TEMPLATE_BLOCKS = [
  /<\?[\s\S]*?\?>/g,       // PHP
  /<%[\s\S]*?%>/g,         // ERB, EJS
  /\{\{[\s\S]*?\}\}/g,     // Blade, Vue, Handlebars, Mustache
  /\{%[\s\S]*?%\}/g,       // Twig, Jinja, Django, Liquid
  /\$\{[\s\S]*?\}/g,       // JS template literal
  /@\w+\([^)]*\)/g,        // Blade directives
];

function blankTemplates(s) {
  let out = s;
  for (const re of TEMPLATE_BLOCKS) {
    out = out.replace(re, (m) => ' '.repeat(m.length));
  }
  return out;
}

const PLAIN_CLASS = /^[A-Za-z][A-Za-z0-9_-]*$/;
const ATTR_RE = /\b(?:class|className)\s*=\s*(["'`])([\s\S]*?)\1/g;

function lint(doc, collection) {
  const root = vscode.workspace.getConfiguration('deck');
  if (!root.get('enable') || !root.get('diagnostics.enable')) {
    collection.delete(doc.uri);
    return;
  }
  if (!isMarkup(doc.languageId)) {
    collection.delete(doc.uri);
    return;
  }

  const cfg = {
    unknown: root.get('diagnostics.unknownClasses'),
    internal: root.get('diagnostics.internalClasses'),
    states: root.get('diagnostics.stateClasses'),
  };

  const text = doc.getText();
  const out = [];

  ATTR_RE.lastIndex = 0;
  let m;
  while ((m = ATTR_RE.exec(text))) {
    const listStart = m.index + m[0].indexOf(m[2], m[1].length);
    const list = blankTemplates(m[2]);

    /* Walk the whitespace-separated tokens, tracking each one's offset. */
    const tokenRe = /\S+/g;
    let t;
    while ((t = tokenRe.exec(list))) {
      const token = t[0];
      if (!PLAIN_CLASS.test(token)) continue; // dynamic or punctuation

      const verdict = classify(token, cfg);
      if (!verdict) continue;

      const from = doc.positionAt(listStart + t.index);
      const to = doc.positionAt(listStart + t.index + token.length);
      const d = new vscode.Diagnostic(
        new vscode.Range(from, to),
        verdict.message,
        verdict.severity
      );
      d.source = 'Deck';
      d.code = 'deck.class';
      /* Carried for the quick-fix provider; VS Code hands back these same
         objects in CodeActionContext.diagnostics. Always an array, so one
         suggestion and two are handled by the same code path. */
      if (verdict.fix) {
        d.deckFix = Array.isArray(verdict.fix) ? verdict.fix : [verdict.fix];
      }
      out.push(d);
    }
  }

  collection.set(doc.uri, out);
}

/* ========================================================================== *
   Quick fixes
   ========================================================================== */

function registerCodeActions(context) {
  const provider = {
    provideCodeActions(doc, _range, ctx) {
      const actions = [];
      for (const d of ctx.diagnostics) {
        if (d.source !== 'Deck' || !d.deckFix) continue;
        d.deckFix.forEach((replacement, i) => {
          const fix = new vscode.CodeAction(
            `Replace with .${replacement}`,
            vscode.CodeActionKind.QuickFix
          );
          fix.edit = new vscode.WorkspaceEdit();
          fix.edit.replace(doc.uri, d.range, replacement);
          fix.diagnostics = [d];
          /* Mark one preferred only when it is the single candidate; with two
             equidistant options neither deserves to be the default action. */
          fix.isPreferred = d.deckFix.length === 1;
          actions.push(fix);
        });
      }
      return actions;
    },
  };

  context.subscriptions.push(
    vscode.languages.registerCodeActionsProvider(
      [...BUILTIN_MARKUP].map((language) => ({ language, scheme: '*' })),
      provider,
      { providedCodeActionKinds: [vscode.CodeActionKind.QuickFix] }
    )
  );
}

/* ========================================================================== *
   Go to definition
   ========================================================================== */

function sourceRoots() {
  const configured = vscode.workspace.getConfiguration('deck').get('sourceRoot');
  const roots = [];
  if (configured) roots.push(configured);

  for (const folder of vscode.workspace.workspaceFolders || []) {
    const base = folder.uri.fsPath;
    roots.push(
      path.join(base, 'node_modules', '@echodial', 'deck', 'src'),
      path.join(base, 'vendor', 'echodial', 'deck', 'src'),
      path.join(base, 'src')
    );
  }
  return roots.filter((r) => {
    try {
      return fs.existsSync(path.join(r, '00-layers.css'));
    } catch {
      return false;
    }
  });
}

function registerDefinition(context) {
  const provider = {
    provideDefinition(doc, pos) {
      if (!vscode.workspace.getConfiguration('deck').get('enable')) return;

      const wordRange = doc.getWordRangeAtPosition(pos, /[A-Za-z0-9_-]+/);
      if (!wordRange) return;
      const word = doc.getText(wordRange);

      let file, line;
      const c = PUBLIC.get(word);
      if (c && c.f) {
        file = c.f;
        line = c.ln;
      } else {
        const tokenRange = doc.getWordRangeAtPosition(pos, /--[A-Za-z0-9-]+/);
        const t = tokenRange && TOKENS.get(doc.getText(tokenRange));
        if (!t || !t.f) return;
        file = t.f;
        line = t.ln;
      }

      for (const root of sourceRoots()) {
        const full = path.join(root, file);
        if (fs.existsSync(full)) {
          return new vscode.Location(
            vscode.Uri.file(full),
            new vscode.Position(Math.max(0, (line || 1) - 1), 0)
          );
        }
      }
      return undefined;
    },
  };

  context.subscriptions.push(
    vscode.languages.registerDefinitionProvider(
      [...BUILTIN_MARKUP, ...STYLE_LANGS].map((language) => ({ language, scheme: '*' })),
      provider
    )
  );
}

/* ========================================================================== *
   Activation
   ========================================================================== */

function activate(context) {
  try {
    loadData(context);
  } catch (err) {
    vscode.window.showErrorMessage(
      `Deck IntelliSense could not read its class data (${err.message}). ` +
        `Run \`node generate.mjs\` in tools/vscode and reinstall.`
    );
    return;
  }

  const collection = vscode.languages.createDiagnosticCollection('deck');
  context.subscriptions.push(collection);

  registerCompletion(context);
  registerHover(context);
  registerCodeActions(context);
  registerDefinition(context);

  /* Debounced so a fast typist is not re-linting a 2,000-line template on
     every character. */
  const pending = new Map();
  const schedule = (doc) => {
    if (!doc || doc.uri.scheme === 'output') return;
    clearTimeout(pending.get(doc.uri.toString()));
    pending.set(
      doc.uri.toString(),
      setTimeout(() => {
        pending.delete(doc.uri.toString());
        try {
          lint(doc, collection);
        } catch {
          /* A malformed template must never surface as an extension crash. */
        }
      }, 250)
    );
  };

  context.subscriptions.push(
    vscode.workspace.onDidChangeTextDocument((e) => schedule(e.document)),
    vscode.workspace.onDidOpenTextDocument(schedule),
    vscode.workspace.onDidCloseTextDocument((doc) => collection.delete(doc.uri)),
    vscode.workspace.onDidChangeConfiguration((e) => {
      if (!e.affectsConfiguration('deck')) return;
      buildItems();
      vscode.workspace.textDocuments.forEach((d) => lint(d, collection));
    })
  );

  vscode.workspace.textDocuments.forEach(schedule);

  context.subscriptions.push(
    vscode.commands.registerCommand('deck.showInfo', () => {
      const c = DATA.counts;
      vscode.window.showInformationMessage(
        `Deck ${DATA.version} — ${c.public} public classes, ${c.internal} internal, ` +
          `${c.removed} removed, ${c.tokens} tokens, ${c.icons} icons.`
      );
    }),
    vscode.commands.registerCommand('deck.recheckAll', () => {
      vscode.workspace.textDocuments.forEach((d) => lint(d, collection));
      vscode.window.showInformationMessage('Deck: re-checked all open files.');
    })
  );
}

function deactivate() {}

module.exports = { activate, deactivate };
