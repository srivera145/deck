#!/usr/bin/env node
/**
 * One-off source edits for the public API freeze.
 *
 * Removals and renames are free only until first publish. This applies them to
 * src/ and to every place in the repository that uses them, so the demo and the
 * docs move in the same commit as the source.
 *
 * Kept as a record of exactly what changed and why.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const ROOT = path.resolve(path.dirname(url.fileURLToPath(import.meta.url)), '..', '..');

/* ---- Removals: whole rules deleted from src/ ----------------------------- */
const REMOVE = [
  // 19-logical.css duplicates of 09-utilities.css. The `is-` prefix in that
  // file means inline-size, which collides with the `is-` state convention.
  ['19-logical.css', '.is-full', 'duplicate of .w-full; `is-` collides with the state prefix'],
  ['19-logical.css', '.is-auto', 'duplicate of .w-auto; `is-` collides with the state prefix'],
  ['19-logical.css', '.bs-full', 'duplicate of .h-full'],
  ['19-logical.css', '.bbs', 'duplicate of .border-t'],
  ['19-logical.css', '.bbe', 'duplicate of .border-b'],
  ['19-logical.css', '.mi-auto', 'duplicate of .mx-auto'],
  ['19-logical.css', '.max-is-prose', 'duplicate of .max-w-prose'],
  ['19-logical.css', '.dir-auto', 'duplicate of .bidi-isolate, and misnamed: it sets no direction'],
  // margin-block long forms duplicate the short forms exactly
  ...[0, 1, 2, 3, 4, 6, 8].map((n) => ['19-logical.css', `.mbs-${n}`, `duplicate of .mt-${n}`]),
  ...[0, 1, 2, 3, 4, 6, 8].map((n) => ['19-logical.css', `.mbe-${n}`, `duplicate of .mb-${n}`]),
  // 03-type.css names that duplicate the 09-utilities.css type scale
  ['03-type.css', '.text-small', 'duplicate of .text-sm, and off the scale naming'],
  ['03-type.css', '.text-tiny', 'duplicate of .text-xs, and off the scale naming'],
  // added in the previous pass and immediately redundant
  ...[0, 1, 2, 3, 4, 6, 8].map((n) => ['04-layout.css', `.cluster-${n}`, `duplicate of .stack-${n}; both set --gap`]),
  // colliding, unused, and replaceable
  ['09-utilities.css', '.grid-d', 'collides with the .grid primitive; display:grid with zero uses'],
  ['18-container.css', '.row-meta', 'unused half of a component the .row-cq rename leaves behind'],
  ['18-container.css', '.row-meta-wide', 'unused half of a component the .row-cq rename leaves behind'],
];

/* ---- Renames: applied to src/ and to every consumer ---------------------- */
const RENAME = [
  ['stack-depth', 'pile', 'collided with .stack; it is a 3D card pile, not a vertical rhythm variant'],
  ['flip-rtl', 'mirror-rtl', 'collided with .flip, the 3D flip card; this one mirrors for RTL'],
  ['select-none', 'no-select', 'collided with .select, the form control; matches .no-print and .no-flip'],
  ['row', 'flex-row', 'collided with the .row-* container component, and read like a grid system'],
  ['col', 'flex-col', 'pairs with .flex-row; matches the .flex display utility it modifies'],
  ['row-cq', 'metarow', 'the .row prefix now belongs to nothing else; names the component directly'],
];

const removed = [];
const renamed = [];

/* --- 1. delete the removed rules ----------------------------------------- */
for (const [file, selector, reason] of REMOVE) {
  const full = path.join(ROOT, 'src', file);
  const lines = fs.readFileSync(full, 'utf8').split('\n');
  const name = selector.slice(1);
  const idx = lines.findIndex((l) => {
    const t = l.trim();
    return t.startsWith(`${selector} `) || t.startsWith(`${selector}{`) || t.startsWith(`${selector},`);
  });
  if (idx < 0) { console.error(`  MISSING  ${file} ${selector}`); continue; }
  /* every rule being removed is a one-liner; assert that before deleting */
  if (!/\{[^}]*\}\s*$/.test(lines[idx])) { console.error(`  NOT A ONE-LINER  ${file} ${selector}`); continue; }
  lines.splice(idx, 1);
  fs.writeFileSync(full, lines.join('\n'));
  removed.push({ file, name, reason });
}

/* --- 2. rename across src/ and every consumer ---------------------------- */
const SKIP = new Set(['assets', 'node_modules', 'vendor', '.git', 'dist', '.tmp-f']);
function walk(dir, out = []) {
  if (!fs.existsSync(dir)) return out;
  for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) { if (!SKIP.has(e.name)) walk(p, out); }
    else if (/\.(css|php|html|md|mjs|js)$/.test(e.name)) out.push(p);
  }
  return out;
}
const targets = [
  ...walk(path.join(ROOT, 'src')),
  ...walk(path.join(ROOT, 'public_html')),
  ...walk(path.join(ROOT, 'php')),
  path.join(ROOT, 'README.md'),
  path.join(ROOT, 'bin', 'deck.mjs'),
].filter((f) => fs.existsSync(f));

for (const [from, to, reason] of RENAME) {
  let hits = 0;
  const files = new Set();
  for (const file of targets) {
    const before = fs.readFileSync(file, 'utf8');
    /* Only as a whole class token: after a dot in CSS/selectors, or as a
       standalone word inside a class attribute or a quoted string. Never as
       part of a longer name — .row must not touch .row-cq or .mega-col. */
    const after = before.replace(
      new RegExp(`(?<![\\w-])(\\.)?${from}(?![\\w-])`, 'g'),
      (m, dot) => `${dot ?? ''}${to}`
    );
    if (after !== before) {
      hits += (before.match(new RegExp(`(?<![\\w-])\\.?${from}(?![\\w-])`, 'g')) || []).length;
      files.add(path.relative(ROOT, file).replace(/\\/g, '/'));
      fs.writeFileSync(file, after);
    }
  }
  renamed.push({ from, to, reason, hits, files: [...files] });
}

console.log(`\n  freeze: removed ${removed.length} classes, renamed ${RENAME.length}\n`);
for (const r of renamed) console.log(`    .${r.from} -> .${r.to}   ${r.hits} references in ${r.files.length} files`);
console.log('');
fs.writeFileSync(
  path.join(ROOT, 'tools', 'docs', 'removed.json'),
  `${JSON.stringify({ removed, renamed }, null, 2)}\n`
);
