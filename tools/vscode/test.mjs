/* Headless harness: stubs enough of the VS Code API to run the real
   extension.js and assert on what it would report. Run: node test.mjs */

import { createRequire } from 'node:module';
import { readFileSync } from 'node:fs';
import Module from 'node:module';

const require_ = createRequire(import.meta.url);

/* ---- Minimal vscode stub ------------------------------------------------- */
const Severity = { Error: 0, Warning: 1, Information: 2, Hint: 3 };
const SEVNAME = ['Error', 'Warning', 'Info', 'Hint'];

let CONFIG = {
  enable: true,
  languages: [],
  'completion.classes': true,
  'completion.tokens': true,
  'completion.icons': true,
  'diagnostics.enable': true,
  'diagnostics.unknownClasses': 'likely',
  'diagnostics.internalClasses': true,
  'diagnostics.stateClasses': true,
  sourceRoot: '',
};

class Position { constructor(line, character) { this.line = line; this.character = character; } }
class Range { constructor(a, b) { this.start = a; this.end = b; } }
class MarkdownString {
  constructor(v = '') { this.value = v; }
  appendMarkdown(v) { this.value += v; return this; }
  appendCodeblock(v, lang) { this.value += '\n```' + lang + '\n' + v + '\n```\n'; return this; }
}
class CompletionItem { constructor(label, kind) { this.label = label; this.kind = kind; } }
class CompletionList { constructor(items, incomplete) { this.items = items; this.isIncomplete = incomplete; } }
class Diagnostic {
  constructor(range, message, severity) { this.range = range; this.message = message; this.severity = severity; }
}
class Hover { constructor(contents, range) { this.contents = contents; this.range = range; } }
class CodeAction { constructor(title, kind) { this.title = title; this.kind = kind; } }
class WorkspaceEdit { replace(uri, range, text) { this.edit = { uri, range, text }; } }
class Location { constructor(uri, pos) { this.uri = uri; this.position = pos; } }

const captured = new Map();

const vscodeStub = {
  Position, Range, MarkdownString, CompletionItem, CompletionList,
  Diagnostic, Hover, CodeAction, WorkspaceEdit, Location,
  DiagnosticSeverity: Severity,
  CompletionItemKind: { Constant: 20, Color: 15, Variable: 5, Value: 11 },
  CodeActionKind: { QuickFix: 'quickfix' },
  Uri: { file: (p) => ({ fsPath: p, toString: () => 'file://' + p, scheme: 'file' }) },
  languages: {
    createDiagnosticCollection: () => ({
      set: (uri, diags) => captured.set(uri.toString(), diags),
      delete: (uri) => captured.delete(uri.toString()),
    }),
    registerCompletionItemProvider: (_s, p) => (vscodeStub.__completion = p, { dispose() {} }),
    registerHoverProvider: (_s, p) => (vscodeStub.__hover = p, { dispose() {} }),
    registerCodeActionsProvider: (_s, p) => (vscodeStub.__actions = p, { dispose() {} }),
    registerDefinitionProvider: (_s, p) => (vscodeStub.__definition = p, { dispose() {} }),
  },
  workspace: {
    getConfiguration: () => ({ get: (k) => CONFIG[k] }),
    workspaceFolders: [],
    textDocuments: [],
    onDidChangeTextDocument: () => ({ dispose() {} }),
    onDidOpenTextDocument: () => ({ dispose() {} }),
    onDidCloseTextDocument: () => ({ dispose() {} }),
    onDidChangeConfiguration: () => ({ dispose() {} }),
  },
  window: { showErrorMessage: (m) => console.error('  [error toast]', m), showInformationMessage: () => {} },
  commands: { registerCommand: () => ({ dispose() {} }) },
};

/* Intercept require('vscode') */
const origResolve = Module._resolveFilename;
Module._resolveFilename = function (req, ...rest) {
  if (req === 'vscode') return 'vscode';
  return origResolve.call(this, req, ...rest);
};
require_.cache['vscode'] = { id: 'vscode', filename: 'vscode', loaded: true, exports: vscodeStub };

const ext = require_('./extension.js');

/* ---- Fake TextDocument --------------------------------------------------- */
function makeDoc(text, languageId = 'php', name = 'test.php') {
  const lines = text.split('\n');
  const offsets = [0];
  for (const l of lines) offsets.push(offsets[offsets.length - 1] + l.length + 1);
  return {
    languageId,
    uri: vscodeStub.Uri.file('/w/' + name),
    getText(range) {
      if (!range) return text;
      const a = offsets[range.start.line] + range.start.character;
      const b = offsets[range.end.line] + range.end.character;
      return text.slice(a, b);
    },
    lineAt: (n) => ({ text: lines[n] }),
    positionAt(off) {
      let line = 0;
      while (line + 1 < offsets.length && offsets[line + 1] <= off) line++;
      return new Position(line, off - offsets[line]);
    },
    getWordRangeAtPosition(pos, re) {
      const l = lines[pos.line];
      const g = new RegExp(re.source, 'g');
      let m;
      while ((m = g.exec(l))) {
        if (m.index <= pos.character && pos.character <= m.index + m[0].length) {
          return new Range(new Position(pos.line, m.index), new Position(pos.line, m.index + m[0].length));
        }
      }
      return undefined;
    },
  };
}

ext.activate({ extensionPath: '.', subscriptions: [] });

/* ======================================================================== */
let pass = 0, fail = 0;
function check(label, got, want) {
  const ok = got === want;
  ok ? pass++ : fail++;
  console.log(`  ${ok ? 'ok  ' : 'FAIL'} ${label}`);
  if (!ok) console.log(`         got:  ${got}\n         want: ${want}`);
}

/* --- 1. Diagnostics on a realistic PHP template -------------------------- */
console.log('\n=== diagnostics: realistic PHP template ===');
const php = `<div class="card stack-5 shadow">
  <h2 class="card-title">Hi</h2>
  <div class="my-app-header site-wrap row">own classes, must be silent</div>
  <p class="is-full">removed class</p>
  <span class="btn-primery">one-char slip</span>
  <div class="carousel-dot">internal</div>
  <a class="btn btn-primary <?= $active ? 'is-active' : '' ?>">php interpolation</a>
  <div class="gap-5 p-5">off-scale numbers</div>
</div>`;

const doc = makeDoc(php);
// drive the private lint() through the public surface: re-run activate's handler
// by calling the exported lint if present, else via the diagnostic collection path
const collection = { store: [], set(_u, d) { this.store = d; }, delete() { this.store = []; } };
// extension.js keeps lint internal; exercise it through onDidOpen by re-activating
// with a workspace that reports our doc.
vscodeStub.workspace.textDocuments = [doc];
captured.clear();
ext.activate({ extensionPath: '.', subscriptions: [] });
// the debounce is 250ms
await new Promise((r) => setTimeout(r, 400));

const diags = captured.get(doc.uri.toString()) || [];
const byText = {};
for (const d of diags) {
  const tok = php.slice(
    php.split('\n').slice(0, d.range.start.line).join('\n').length + (d.range.start.line ? 1 : 0) + d.range.start.character,
    php.split('\n').slice(0, d.range.start.line).join('\n').length + (d.range.start.line ? 1 : 0) + d.range.end.character
  );
  byText[tok] = { sev: SEVNAME[d.severity], msg: d.message, fix: d.deckFix };
}
console.log('  reported tokens:', Object.keys(byText).sort().join(', ') || '(none)');
for (const [k, v] of Object.entries(byText)) {
  console.log(`    .${k.padEnd(14)} ${v.sev.padEnd(7)} fix=${v.fix || '-'}`);
  console.log(`      ${v.msg.slice(0, 110)}`);
}

check('stack-5 flagged', !!byText['stack-5'], true);
check('stack-5 offers a fix', !!(byText['stack-5'] && byText['stack-5'].fix), true);
check('is-full flagged as removed', !!byText['is-full'], true);
check('is-full fix is w-full', byText['is-full'] && String(byText['is-full'].fix), 'w-full');
check('btn-primery flagged', !!byText['btn-primery'], true);
check('carousel-dot warns (internal)', byText['carousel-dot'] && byText['carousel-dot'].sev, 'Warning');
check('gap-5 flagged', !!byText['gap-5'], true);
check('p-5 flagged', !!byText['p-5'], true);
check('card NOT flagged', !!byText['card'], false);
check('card-title NOT flagged', !!byText['card-title'], false);
check('btn-primary NOT flagged', !!byText['btn-primary'], false);
check('my-app-header NOT flagged', !!byText['my-app-header'], false);
check('site-wrap NOT flagged', !!byText['site-wrap'], false);
check('row NOT flagged (bootstrap)', !!byText['row'], false);
check('shadow NOT flagged', !!byText['shadow'], false);
check('PHP $active not flagged', !!byText['$active'], false);

/* --- 2. Completion context ------------------------------------------------ */
console.log('\n=== completion context ===');
const cp = vscodeStub.__completion;

function completeAt(text, lang = 'php') {
  const d = makeDoc(text, lang);
  const lines = text.split('\n');
  const pos = new Position(lines.length - 1, lines[lines.length - 1].length);
  const r = cp.provideCompletionItems(d, pos);
  return r ? r.items.length : 0;
}
const nClass = completeAt('<div class="');
const nTok = completeAt('<div style="color: var(--');
const nIcon = completeAt('<use href="/assets/deck/deck-icons.svg#');
console.log(`  inside class="       -> ${nClass} items`);
console.log(`  inside var(--        -> ${nTok} items`);
console.log(`  after sprite #       -> ${nIcon} items`);
console.log(`  plain text           -> ${completeAt('<p>hello ')} items`);
console.log(`  after class closed   -> ${completeAt('<div class="btn"> ')} items`);

const data = JSON.parse(readFileSync('./data/deck-data.json', 'utf8'));
check('class completion offers public+internal', nClass, data.counts.public + data.counts.internal);
check('token completion count', nTok, data.counts.tokens);
check('icon completion count', nIcon, data.counts.icons);
check('no completion in plain text', completeAt('<p>hello '), 0);
check('no completion after attr closed', completeAt('<div class="btn"> '), 0);

/* --- 3. JSX + Blade ------------------------------------------------------- */
console.log('\n=== other languages ===');
check('JSX className completes', completeAt('<div className="', 'javascriptreact') > 0, true);
check('Blade completes', completeAt('<div class="', 'blade') > 0, true);

/* --- 4. Hover ------------------------------------------------------------- */
console.log('\n=== hover ===');
const hp = vscodeStub.__hover;
const hdoc = makeDoc('<div class="card stack-5">x</div>');
const hv = hp.provideHover(hdoc, new Position(0, 14));
console.log('  hover on .card:', hv ? hv.contents.value.slice(0, 90).replace(/\n/g, ' ') : 'none');
check('hover returns content for .card', !!hv, true);

const tdoc = makeDoc('a { color: var(--brand); }', 'css');
const tv = hp.provideHover(tdoc, new Position(0, 20));
console.log('  hover on --brand:', tv ? tv.contents.value.slice(0, 90).replace(/\n/g, ' ') : 'none');
check('hover returns content for --brand', !!tv, true);

/* --- 5. Quick fix -------------------------------------------------------- */
console.log('\n=== quick fix ===');
const ap = vscodeStub.__actions;
const fixDiag = diags.find((d) => d.deckFix);
const acts = ap.provideCodeActions(doc, null, { diagnostics: diags });
console.log(`  ${acts.length} quick fixes offered; first: "${acts[0] ? acts[0].title : 'none'}"`);
check('quick fixes offered', acts.length > 0, true);
check('fix replaces with a real class', acts[0] && data.classes.some((c) => c.n === acts[0].edit.edit.text), true);
check('stack-5 offers both 4 and 6', acts.filter(a=>/stack-[46]/.test(a.title)).length, 2);

console.log(`\n${pass} passed, ${fail} failed\n`);
process.exit(fail ? 1 : 0);
