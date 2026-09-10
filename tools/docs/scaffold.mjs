#!/usr/bin/env node
/**
 * What a component page has to declare.
 *
 *     node tools/docs/scaffold.mjs card
 *     node tools/docs/scaffold.mjs card input check --unclaimed
 *
 * tools/docs/verify.mjs requires a component page to list every class and state
 * in its component, and to say something about every source file that styles it.
 * `.btn` turned out to be 55 rules across ten files, and reading the source
 * found nine. So this prints the answer rather than leaving it to be
 * remembered: the `documents` array and an `accounts` skeleton with the rule
 * count per file, ready to paste and annotate.
 *
 * `--unclaimed` additionally lists classes in the component's files that no
 * page has claimed yet, which is how the leftovers around a component — the
 * ones that are not members of any root — stop being invisible.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const ROOT = path.resolve(path.dirname(url.fileURLToPath(import.meta.url)), '..', '..');
const api = JSON.parse(fs.readFileSync(path.join(ROOT, 'dist', 'api.json'), 'utf8'));

const names = process.argv.slice(2).filter((a) => !a.startsWith('--'));
const wantUnclaimed = process.argv.includes('--unclaimed');

/* Everything already claimed by a page, so the leftovers can be told apart. */
function claimedClasses() {
  const claimed = new Set();
  const dir = path.join(ROOT, 'public_html', 'docs');
  const walk = (d) => {
    if (!fs.existsSync(d)) return;
    for (const e of fs.readdirSync(d, { withFileTypes: true })) {
      const p = path.join(d, e.name);
      if (e.isDirectory()) { walk(p); continue; }
      if (!e.name.endsWith('.php') || e.name.startsWith('_')) continue;
      const text = fs.readFileSync(p, 'utf8');
      const m = text.match(/['"]documents['"]\s*=>\s*\[((?:\s*['"][A-Za-z0-9_-]+['"]\s*,?)+)\s*\]/);
      if (!m) continue;
      for (const q of m[1].match(/['"]([^'"]+)['"]/g) || []) claimed.add(q.slice(1, -1));
    }
  };
  walk(dir);
  return claimed;
}

if (!names.length) {
  const roots = Object.entries(api.components)
    .map(([n, c]) => [n, c.members.length + c.states.length, c.files.length, c.ruleCount])
    .sort((a, b) => b[3] - a[3]);
  console.log(`\n  ${roots.length} component roots, by rule count:\n`);
  for (const [n, m, f, r] of roots) {
    console.log(`    .${n}`.padEnd(24) + `${String(m).padStart(3)} classes  ${String(f).padStart(2)} files  ${String(r).padStart(3)} rules`);
  }
  console.log('');
  process.exit(0);
}

const claimed = claimedClasses();

for (const name of names) {
  const comp = api.components[name];
  if (!comp) {
    console.log(`\n  .${name} is not a component root. Classes that start with it:`);
    const near = api.classes.filter((c) => c.name === name || c.name.startsWith(`${name}-`));
    for (const c of near) console.log(`    .${c.name}  ${c.file}:${c.line}`);
    console.log('');
    continue;
  }

  const members = [...comp.members].sort();
  const states = [...comp.states].sort();

  console.log(`\n/* ---- .${name} — ${members.length} classes, ${states.length} states, ${comp.files.length} files, ${comp.ruleCount} rules ---- */\n`);

  console.log("    'documents' => [");
  const all = [...members, ...states];
  for (let i = 0; i < all.length; i += 5) {
    console.log(`        ${all.slice(i, i + 5).map((n) => `'${n}'`).join(', ')},`);
  }
  console.log('    ],');

  console.log(`    'component' => '${name}',`);
  console.log("    'accounts' => [");
  const byRules = [...comp.files].sort(
    (a, b) => comp.rules.filter((r) => r.file === b).length - comp.rules.filter((r) => r.file === a).length
  );
  for (const f of byRules) {
    const n = comp.rules.filter((r) => r.file === f).length;
    console.log(`        '${f}'${' '.repeat(Math.max(0, 20 - f.length))}=> '??? — ${n} rule${n === 1 ? '' : 's'}',`);
  }
  console.log('    ],');

  /* What each file actually does to the component, so the note can be written
     from evidence instead of from a guess. */
  console.log('\n  what each file contributes:');
  for (const f of byRules) {
    const rules = comp.rules.filter((r) => r.file === f);
    console.log(`\n    ${f}  (${rules.length})`);
    for (const r of rules.slice(0, 8)) {
      const cond = r.conditions?.length ? `  under ${r.conditions.join(' ')}` : '';
      console.log(`      ${String(r.line).padStart(4)}  ${r.selector}${cond}`);
    }
    if (rules.length > 8) console.log(`      … and ${rules.length - 8} more`);
  }

  if (wantUnclaimed) {
    const near = api.classes.filter(
      (c) => comp.files.includes(c.file) && !claimed.has(c.name) && !all.includes(c.name)
    );
    console.log(`\n  unclaimed classes defined in those files (${near.length}):`);
    for (const c of near) console.log(`      .${c.name}`.padEnd(28) + `${c.file}:${c.line}`);
  }
  console.log('');
}
