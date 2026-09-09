#!/usr/bin/env node
/**
 * Find prefix collisions.
 *
 *     node tools/docs/collisions.mjs
 *
 * `.stack-depth` was not a variant of `.stack`. It was a different component
 * that happened to start with the same word, and a reader has no way to tell
 * those apart from the name. That is a naming bug, and it is only free to fix
 * before the first publish.
 *
 * A name `a-b` is a suspected collision when `a` is also a class and the two are
 * declared in different files, or in different layers, or far enough apart in
 * the same file that they are plainly not the same block of rules. Same file,
 * same layer, adjacent is what a real family looks like — `.grid` and
 * `.grid-tight` — and those are not reported.
 *
 * The output is a list to judge, not a verdict. It is here so the question gets
 * asked of every name rather than of the ones somebody happened to notice.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const ROOT = path.resolve(path.dirname(url.fileURLToPath(import.meta.url)), '..', '..');
const api = JSON.parse(fs.readFileSync(path.join(ROOT, 'dist', 'api.json'), 'utf8'));

const byName = new Map(api.classes.map((c) => [c.name, c]));

/* Families where the stem is a token vocabulary rather than a component: the
   prefix and the suffix are chosen from lists, and every combination is
   deliberate. Reporting these would bury the real cases. */
const VOCABULARY = [
  /^(p|m|px|py|mx|my|pt|pb|pl|pr|mt|mb|ml|mr)-/,
  /^(text|bg|border|shadow|rounded|gap|opacity|z|w|h|max|min|inset|top|bottom|left|right)-/,
  /^(grid|flex|order|self|items|justify|content|place)-/,
  /^s\d/,
];

const NEAR = 40; /* lines apart, within which two rules read as one block */

const rows = [];
for (const c of api.classes) {
  const parts = c.name.split('-');
  for (let i = 1; i < parts.length; i++) {
    const stem = parts.slice(0, i).join('-');
    const parent = byName.get(stem);
    if (!parent) continue;
    if (VOCABULARY.some((re) => re.test(c.name))) continue;

    /* A component's own file runs to hundreds of lines, so distance within one
       file says nothing: .btn and .btn-ghost are 62 lines apart and obviously
       one family. What a reader cannot resolve is a child declared in a
       different file from its stem — there is nothing next to it to say whether
       it modifies the stem or merely starts with the same word. */
    if (parent.file === c.file) continue;

    rows.push({
      name: c.name,
      stem,
      child: `${c.file}:${c.line} [${c.layer}]`,
      parent: `${parent.file}:${parent.line} [${parent.layer}]`,
      why: parent.layer === c.layer ? 'different file' : 'different file and layer',
    });
    break;
  }
}

rows.sort((a, b) => a.stem.localeCompare(b.stem) || a.name.localeCompare(b.name));

console.log(`\n  prefix collisions: ${rows.length} suspected\n`);
for (const r of rows) {
  console.log(`    .${r.name}`.padEnd(28) + `stem .${r.stem}`.padEnd(22) + r.why);
  console.log(`      child  ${r.child}`);
  console.log(`      stem   ${r.parent}`);
}
console.log('');

fs.writeFileSync(
  path.join(ROOT, 'dist', 'collisions.json'),
  JSON.stringify({ generated: 'tools/docs/collisions.mjs', count: rows.length, rows }, null, 2)
);
