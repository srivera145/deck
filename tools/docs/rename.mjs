#!/usr/bin/env node
/**
 * Rename a class everywhere, safely.
 *
 *     node tools/docs/rename.mjs                 # apply the freeze renames
 *     node tools/docs/rename.mjs --reverse       # undo them
 *
 * `row` and `col` are ordinary English words and legal HTML attribute values
 * (`scope="col"`), so a bare word-boundary substitution corrupts prose and
 * markup. Every replacement here is anchored to a place a class name can
 * actually appear:
 *
 *   CSS / Markdown / JS   `.name` with the leading dot
 *   HTML and PHP          a whole token inside a class="..." attribute
 *
 * Nothing else is touched.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const ROOT = path.resolve(path.dirname(url.fileURLToPath(import.meta.url)), '..', '..');
const REVERSE = process.argv.includes('--reverse');

const RENAME = [
  ['stack-depth', 'pile'],
  ['flip-rtl', 'mirror-rtl'],
  ['select-none', 'no-select'],
  ['row-cq', 'metarow'],
  ['row', 'flex-row'],
  ['col', 'flex-col'],
];

/* Longest source name first, so `row-cq` is renamed before `row` can see it. */
const pairs = (REVERSE ? RENAME.map(([a, b]) => [b, a]) : RENAME)
  .sort((a, b) => b[0].length - a[0].length);

const SKIP = new Set(['assets', 'node_modules', 'vendor', '.git', 'dist']);
function walk(dir, out = []) {
  if (!fs.existsSync(dir)) return out;
  for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) { if (!SKIP.has(e.name)) walk(p, out); }
    else if (/\.(css|php|html|md|mjs|js)$/.test(e.name)) out.push(p);
  }
  return out;
}

const files = [
  ...walk(path.join(ROOT, 'src')),
  ...walk(path.join(ROOT, 'public_html')),
  ...walk(path.join(ROOT, 'php')),
  path.join(ROOT, 'README.md'),
  path.join(ROOT, 'bin', 'deck.mjs'),
].filter((f) => fs.existsSync(f));

const report = new Map(pairs.map(([from]) => [from, { hits: 0, files: new Set() }]));

for (const file of files) {
  const before = fs.readFileSync(file, 'utf8');
  let text = before;

  /* 1. class="a b c" — rewrite whole tokens only */
  text = text.replace(/(class=)(["'])([^"'<>]*)\2/g, (m, lead, q, value) => {
    const out = value.split(/(\s+)/).map((tok) => {
      const hit = pairs.find(([from]) => from === tok);
      if (!hit) return tok;
      report.get(hit[0]).hits++;
      return hit[1];
    }).join('');
    return `${lead}${q}${out}${q}`;
  });

  /* 2. `.name` with a leading dot, anywhere (selectors, prose, JS strings) */
  for (const [from, to] of pairs) {
    const re = new RegExp(`(?<![\\w-])\\.${from}(?![\\w-])`, 'g');
    const n = (text.match(re) || []).length;
    if (n) { report.get(from).hits += n; text = text.replace(re, `.${to}`); }
  }

  if (text !== before) {
    fs.writeFileSync(file, text);
    for (const [from] of pairs) {
      if (before.includes(from)) report.get(from).files.add(path.relative(ROOT, file).replace(/\\/g, '/'));
    }
  }
}

console.log(`\n  rename${REVERSE ? ' (reverse)' : ''}:`);
for (const [from, to] of pairs) {
  const r = report.get(from);
  console.log(`    .${from} -> .${to}`.padEnd(40) + `${String(r.hits).padStart(4)} replacements`);
}
console.log('');
