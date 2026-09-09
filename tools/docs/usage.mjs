#!/usr/bin/env node
/**
 * Where is each class actually used?
 *
 *     node tools/docs/usage.mjs
 *
 * Counts references across everything in the repository that can name a class:
 * authored markup, the PHP helper, the CLI, and — the part a naive grep misses —
 * src/js, where components like the toast queue and the date picker build their
 * own markup at runtime. A class that only ever appears in a template literal
 * inside deck-extras.js is used, not dead.
 *
 * Writes dist/usage.json for tools/docs/classify.mjs to read.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const ROOT = path.resolve(path.dirname(url.fileURLToPath(import.meta.url)), '..', '..');
const SKIP = new Set(['assets', 'node_modules', 'vendor', '.git', 'dist', '.tmp']);

const api = JSON.parse(fs.readFileSync(path.join(ROOT, 'dist', 'api.json'), 'utf8'));
const real = new Set(api.classes.map((c) => c.name));

function walk(dir, out = []) {
  if (!fs.existsSync(dir)) return out;
  for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) { if (!SKIP.has(e.name)) walk(p, out); }
    else if (/\.(php|html|md|mjs|js|txt)$/.test(e.name)) out.push(p);
  }
  return out;
}

const files = [
  ...walk(path.join(ROOT, 'public_html')),
  ...walk(path.join(ROOT, 'src', 'js')),
  ...walk(path.join(ROOT, 'php')),
  path.join(ROOT, 'bin', 'deck.mjs'),
  path.join(ROOT, 'README.md'),
  path.join(ROOT, 'FINDINGS.md'),
].filter((f) => fs.existsSync(f));

const use = new Map();
const where = new Map();
const bump = (name, file) => {
  if (!real.has(name)) return;
  use.set(name, (use.get(name) || 0) + 1);
  if (!where.has(name)) where.set(name, new Set());
  where.get(name).add(path.relative(ROOT, file).replace(/\\/g, '/'));
};

for (const file of files) {
  const text = fs.readFileSync(file, 'utf8');

  /* class="a b c", class='a b', class=`a b` */
  for (const m of text.matchAll(/class=["'`]([^"'`]*)["'`]/g)) {
    for (const n of m[1].split(/\s+/).filter(Boolean)) bump(n, file);
  }
  /* classList.add('x', 'y') and friends */
  for (const m of text.matchAll(/classList\.\w+\(([^)]*)\)/g)) {
    for (const q of m[1].match(/["'`]([^"'`]+)["'`]/g) || []) bump(q.slice(1, -1), file);
  }
  /* any quoted CSS selector: '.combo-list', `.toast .toast-close` */
  for (const m of text.matchAll(/["'`]([^"'`\n]{0,120})["'`]/g)) {
    for (const s of m[1].matchAll(/\.([a-zA-Z][\w-]*)/g)) bump(s[1], file);
  }
  /* Deck::icon('name', 'icon icon-lg') style helper arguments are covered by
     the quoted-selector pass above only when they start with a dot, so also
     take bare quoted strings that are entirely a known class list. */
  for (const m of text.matchAll(/["'`]([a-z][\w- ]{2,60})["'`]/g)) {
    const parts = m[1].split(/\s+/);
    if (parts.every((p) => real.has(p))) for (const p of parts) bump(p, file);
  }
}

const unused = [...real].filter((n) => !use.has(n)).sort();
const out = {
  generated: 'tools/docs/usage.mjs — do not edit',
  filesScanned: files.length,
  counts: { classes: real.size, used: real.size - unused.length, unused: unused.length },
  uses: Object.fromEntries([...use].sort((a, b) => b[1] - a[1])),
  where: Object.fromEntries([...where].map(([k, v]) => [k, [...v].sort()])),
  unused,
};
fs.writeFileSync(path.join(ROOT, 'dist', 'usage.json'), `${JSON.stringify(out, null, 2)}\n`);

console.log(`
  usage: scanned ${files.length} files
    ${out.counts.used} of ${real.size} classes referenced somewhere
    ${unused.length} never referenced in the repository
`);
