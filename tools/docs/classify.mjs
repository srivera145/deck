#!/usr/bin/env node
/**
 * The public API freeze.
 *
 *     node tools/docs/classify.mjs
 *
 * Puts every class in src/ into exactly one of four buckets and writes
 * dist/api-buckets.json plus API.md. tools/docs/verify.mjs fails the build if a
 * class ends up in no bucket, so a new class has to be classified on purpose
 * rather than becoming public by default.
 *
 *   public      documented, supported, covered by semver
 *   internal    Deck's own components use it; authors do not write it
 *   deprecated  still works, warns, goes at v1
 *   remove      already gone, kept here as the record of what went and why
 *
 * Rules are ordered and the first match wins, so a specific override beats a
 * family rule. The reason on the rule is the reason recorded for every class it
 * matches — that is what makes 931 decisions reviewable rather than a list.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const ROOT = path.resolve(path.dirname(url.fileURLToPath(import.meta.url)), '..', '..');
const api = JSON.parse(fs.readFileSync(path.join(ROOT, 'dist', 'api.json'), 'utf8'));
const usage = JSON.parse(fs.readFileSync(path.join(ROOT, 'dist', 'usage.json'), 'utf8'));

/* The committed record of every decision. A class that is not in this file has
   never been classified, and the rules below are only used to PROPOSE a bucket
   for it — never to assign one silently. Run with --accept to write proposals
   in. That extra step is the difference between choosing to make something
   public and getting a public class because nobody looked. */
const LEDGER = path.join(ROOT, 'tools', 'docs', 'api-decisions.txt');
const ACCEPT = process.argv.includes('--accept');

function readLedger() {
  if (!fs.existsSync(LEDGER)) return new Map();
  return new Map(
    fs.readFileSync(LEDGER, 'utf8')
      .split(/\r?\n/)
      .map((l) => l.replace(/#.*/, '').trim())
      .filter(Boolean)
      .map((l) => l.split(/\s*=\s*/))
      .filter((p) => p.length === 2)
      .map(([n, b]) => [n, b])
  );
}

/* Classes an author never writes: Deck's own JavaScript adds and removes them.
   Marking them internal is the single biggest reduction in the public surface,
   and it is honest — an author toggling .is-dragging by hand is doing something
   wrong. The states an author DOES write are listed as public further down. */
const AUTHOR_WRITTEN_STATES = new Set([
  'is-active', 'is-current', 'is-disabled', 'is-done', 'is-invalid', 'is-loading',
  'is-open', 'is-selected', 'is-valid',
]);

/* Sub-parts of a component that only ever appear in markup Deck's JS generates. */
const JS_GENERATED_PREFIXES = [
  'combo-', 'datepicker-', 'toast-', 'qr-', 'dg-sort', 'carousel-dot',
  'sortable', 'drop-', 'reorder',
];

const RULES = [
  /* ---- deliberate exclusions ------------------------------------------- */
  {
    id: 'demo-leak',
    match: (c) => c.name === 'theme-dock',
    bucket: 'internal',
    reason: 'A demo-page class that src/99-print.css names directly. The framework should not know about it; tracked in FINDINGS.md.',
  },

  /* ---- states ----------------------------------------------------------- */
  {
    id: 'state-author',
    match: (c) => c.name.startsWith('is-') && AUTHOR_WRITTEN_STATES.has(c.name),
    bucket: 'public',
    reason: 'A state an author sets in markup or toggles from their own code.',
  },
  {
    id: 'state-runtime',
    match: (c) => c.name.startsWith('is-'),
    bucket: 'internal',
    reason: 'A state deck.js sets and clears at runtime. Style it if you like; do not write it.',
  },

  /* ---- component internals ---------------------------------------------- */
  {
    id: 'js-generated',
    match: (c) => JS_GENERATED_PREFIXES.some((p) => c.name.startsWith(p)),
    bucket: 'internal',
    reason: 'Part of markup Deck generates. The component is public; this piece of its inside is not.',
  },
  {
    id: 'series-internal',
    match: (c) => /^s\d$|^s-(good|warn|bad|muted)$/.test(c.name),
    bucket: 'public',
    reason: 'Chart series colour. Set on a mark; every fill, stroke and legend swatch follows.',
  },

  /* ---- everything a page can use ---------------------------------------- */
  {
    id: 'layout',
    match: (c) => c.layer === 'deck.layout',
    bucket: 'public',
    reason: 'Layout primitive.',
  },
  {
    id: 'utility',
    match: (c) => c.layer === 'deck.utilities',
    bucket: 'public',
    reason: 'Utility class.',
  },
  {
    id: 'type',
    match: (c) => c.layer === 'deck.type',
    bucket: 'public',
    reason: 'Typography class.',
  },
  {
    id: 'component',
    match: (c) => c.layer === 'deck.components' || c.layer === 'deck.mobile',
    bucket: 'public',
    reason: 'Component class.',
  },
  {
    id: 'effect',
    match: (c) => c.layer === 'deck.effects' || c.layer === 'deck.motion',
    bucket: 'public',
    reason: 'Motion or effect class. Opt in by adding it; nothing runs unless you do.',
  },
  {
    id: 'rtl',
    match: (c) => c.layer === 'deck.rtl',
    bucket: 'public',
    reason: 'Direction control, for the few things logical properties cannot decide.',
  },
  {
    id: 'print',
    match: (c) => c.layer === 'deck.print',
    bucket: 'public',
    reason: 'Print control.',
  },
  {
    id: 'reset',
    match: (c) => c.layer === 'deck.reset',
    bucket: 'internal',
    reason: 'Declared in the reset layer as a global default rather than as a class to reach for.',
  },
  {
    id: 'unlayered',
    match: () => true,
    bucket: 'internal',
    reason: 'Outside every deck.* layer, so not part of the supported surface.',
  },
];

/* Removed in this pass. Kept as the record: what went, why, and how many times
   it was used in the repository when it went. Removal is only free before the
   first publish, and this is that moment. */
const REMOVED = JSON.parse(
  fs.readFileSync(path.join(ROOT, 'tools', 'docs', 'removed.json'), 'utf8')
).removed;

function classify(c) {
  for (const rule of RULES) {
    if (rule.match(c)) return { bucket: rule.bucket, reason: rule.reason, rule: rule.id };
  }
  return null;
}

const ledger = readLedger();
const buckets = { public: [], internal: [], deprecated: [], remove: [] };
const decisions = {};
const unrecorded = [];

for (const c of api.classes) {
  const proposal = classify(c);
  const recorded = ledger.get(c.name);
  if (!recorded && !ACCEPT) { unrecorded.push({ name: c.name, proposal: proposal.bucket }); continue; }
  const bucket = recorded ?? proposal.bucket;
  decisions[c.name] = {
    bucket,
    reason: proposal.reason,
    rule: proposal.rule,
    overridden: Boolean(recorded) && recorded !== proposal.bucket,
    layer: c.layer,
    file: c.file,
    line: c.line,
    uses: usage.uses[c.name] || 0,
  };
  buckets[bucket].push(c.name);
}

if (unrecorded.length) {
  console.error(`
  freeze: ${unrecorded.length} class(es) have never been classified:
`);
  for (const u of unrecorded.slice(0, 20)) console.error(`    .${u.name}   (rules would propose: ${u.proposal})`);
  if (unrecorded.length > 20) console.error(`    … and ${unrecorded.length - 20} more`);
  console.error(`
  Public is a decision, not a default. Review each one, then record it:

      node tools/docs/classify.mjs --accept

  or add an override rule in tools/docs/classify.mjs first.
`);
  process.exit(1);
}

/* The other direction: a line for a class that no longer exists. Left alone it
   quietly turns the ledger into a list of things that used to be true. */
const live = new Set(api.classes.map((c) => c.name));
const stale = [...ledger.keys()].filter((n) => !live.has(n));
if (stale.length && !ACCEPT) {
  console.error(`\n  freeze: ${stale.length} ledger entr(ies) name a class that is no longer in src/:\n`);
  for (const n of stale) console.error(`    .${n} = ${ledger.get(n)}`);
  console.error(`\n  Delete the line, or record the removal in tools/docs/freeze-edits.mjs.\n`);
  process.exit(1);
}

if (ACCEPT) {
  const lines = api.classes
    .map((c) => [c.name, ledger.get(c.name) ?? classify(c).bucket])
    .sort((a, b) => a[0].localeCompare(b[0]))
    .map(([n, b]) => `${n} = ${b}`);
  const header = [
    '# The public API ledger. One line per class: name = bucket.',
    '#',
    '# public      documented, supported, covered by semver',
    '# internal    Deck uses it; authors do not write it, and it can change',
    '# deprecated  still works, warns, goes at v1',
    '#',
    '# Written by: node tools/docs/classify.mjs --accept',
    '# Edit a line by hand to overrule what the rules propose.',
    `# ${lines.length} classes.`,
    '',
  ];
  fs.writeFileSync(LEDGER, header.concat(lines, '').join('\n'));
  console.log(`\n  freeze: recorded ${lines.length} decisions in ${path.relative(ROOT, LEDGER)}\n`);
}

const out = {
  generated: 'tools/docs/classify.mjs — do not edit',
  counts: {
    total: api.classes.length,
    public: buckets.public.length,
    internal: buckets.internal.length,
    deprecated: buckets.deprecated.length,
    removed: REMOVED.length,
  },
  buckets,
  decisions,
  removed: REMOVED,
};
fs.writeFileSync(path.join(ROOT, 'dist', 'api-buckets.json'), `${JSON.stringify(out, null, 2)}\n`);

/* ---- API.md -------------------------------------------------------------- */
const byName = new Map(api.classes.map((c) => [c.name, c]));
const summarise = (c) => {
  if (c.doc) return c.doc.split('\n')[0].replace(/\s+/g, ' ').slice(0, 150);
  const props = c.declarations.map((d) => d.prop).filter((p) => !p.startsWith('--'));
  if (!props.length) {
    const custom = c.declarations.map((d) => d.prop);
    return custom.length ? `Sets ${custom.slice(0, 3).join(', ')}.` : 'Modifier; see the component.';
  }
  return `Sets ${props.slice(0, 4).join(', ')}${props.length > 4 ? `, and ${props.length - 4} more` : ''}.`;
};

/* Group by component root where there is one, else by source file. */
const roots = Object.keys(api.components);
const groupOf = (name) => {
  const owned = roots
    .filter((r) => name === r || name.startsWith(`${r}-`))
    .sort((a, b) => b.length - a.length)[0];
  return owned ?? byName.get(name).file.replace(/^\d+-/, '').replace(/\.css$/, '');
};

const groups = new Map();
for (const name of buckets.public.sort()) {
  const g = groupOf(name);
  if (!groups.has(g)) groups.set(g, []);
  groups.get(g).push(name);
}

/* The surface breakdown, measured rather than asserted. Grouping by layer is
   the closest thing to grouping by "how you learn it": the utility layer is a
   grammar you learn once, the component layers are names you look up. */
const LAYER_COST = {
  'deck.utilities': 'A grammar. Learn the pattern, get the family.',
  'deck.components': 'Looked up per component, not memorised.',
  'deck.layout': 'The eight or so primitives most pages actually use.',
  'deck.effects': 'Opt-in. Nothing runs unless you add the class.',
  'deck.motion': 'Opt-in. Nothing runs unless you add the class.',
  'deck.mobile': 'Only if you build the mobile shell.',
  'deck.type': 'Small, and mostly obvious from the name.',
  'deck.print': 'Four or five you reach for once.',
  'deck.rtl': 'Two escape hatches for what logical properties cannot decide.',
  'deck.reset': 'Declared as a global default, not a class to reach for.',
};
const publicByLayer = {};
for (const n of buckets.public) {
  const l = decisions[n].layer || '(unlayered)';
  publicByLayer[l] = (publicByLayer[l] || 0) + 1;
}
const surfaceRows = Object.entries(publicByLayer)
  .sort((a, b) => b[1] - a[1])
  .map(([l, n]) => `| \`${l}\` | ${n} | ${LAYER_COST[l] || '—'} |`)
  .join('\n');
const componentRoots = Object.keys(api.components || {}).length;
const unusedPublic = buckets.public.filter((n) => (usage.uses[n] || 0) === 0).length;

let md = `# Deck public API

This file is the contract. Every class listed here is public: it is documented,
supported, and covered by semantic versioning. Renaming or removing one is a
breaking change requiring a major version.

**Anything not listed here is not public.** Internal classes, the states Deck's
JavaScript sets at runtime, and everything outside the \`deck.*\` layers may be
renamed or removed in any release without a major version bump. If you depend on
one, pin your version.

Generated by \`tools/docs/classify.mjs\` from \`src/\`. \`tools/docs/verify.mjs\`
fails the build if a public class is missing from this file, or if a class in
\`src/\` belongs to no bucket.

| bucket | count | meaning |
| --- | ---: | --- |
| public | ${buckets.public.length} | in the freeze, semver applies |
| internal | ${buckets.internal.length} | Deck's own; may change any release |
| deprecated | ${buckets.deprecated.length} | works, warns, removed at v1 |
| removed | ${REMOVED.length} | already gone, before first publish |

Total classes in \`src/\`: **${api.classes.length}**.

## How big this actually is

${buckets.public.length} is more names than anyone will learn, and this document should say
so rather than let a reader discover it by scrolling. What makes it workable is
that the surface is not flat:

| group | public classes | what learning it costs |
| --- | ---: | --- |
${surfaceRows}

The utility layer is a grammar — once you know that \`-1\` through \`-8\` are the
spacing steps and that \`p\`, \`m\` and \`gap\` take them, you know the whole family
without reading it. The component layers are the opposite: ${componentRoots} components,
each a handful of names, and a page that uses six of them needs six.

One number that is not comfortable: **${unusedPublic} of the ${buckets.public.length} public classes are
used nowhere in this repository** — not in the demo, not in the docs, not in
Deck's own JavaScript. They are frozen on the strength of their source alone.

---

`;

for (const [group, names] of [...groups].sort((a, b) => a[0].localeCompare(b[0]))) {
  md += `## ${group}\n\n| class | layer | purpose |\n| --- | --- | --- |\n`;
  for (const name of names) {
    const c = byName.get(name);
    md += `| \`.${name}\` | ${c.layer ?? '—'} | ${summarise(c).replace(/\|/g, '\\|')} |\n`;
  }
  md += '\n';
}

md += `---

## Removed before first publish

These existed during development and are gone. None had external users, because
Deck had not been published; removing them after publication would have required
a major version.

| class | reason | uses when removed | where |
| --- | --- | ---: | --- |
`;
/* The count has to come from the record rather than from a scan of the tree as
   it is now: the class is gone, so scanning now always answers zero, and zero
   reads as "nothing used it" whether or not that was ever true. */
for (const r of REMOVED.sort((a, b) => a.name.localeCompare(b.name))) {
  const where = (r.usedIn || []).join(', ') || '—';
  md += `| \`.${r.name}\` | ${r.reason} | ${r.uses ?? '?'} | ${where} |\n`;
}

md += `
## Internal classes

${buckets.internal.length} classes are internal. They are real and they work, but
they are Deck's own plumbing: states its JavaScript toggles, and the inside of
components whose markup Deck generates. Style them if you need to; do not rely on
the names.

<details><summary>Show all ${buckets.internal.length}</summary>

${buckets.internal.sort().map((n) => `\`.${n}\``).join(', ')}

</details>
`;

fs.writeFileSync(path.join(ROOT, 'API.md'), md);

console.log(`
  freeze: ${api.classes.length} classes classified

    ${String(buckets.public.length).padStart(4)} public      (in API.md, semver applies)
    ${String(buckets.internal.length).padStart(4)} internal    (Deck's own, may change)
    ${String(buckets.deprecated.length).padStart(4)} deprecated  (works, warns, goes at v1)
    ${String(REMOVED.length).padStart(4)} removed     (already gone)

  wrote API.md and dist/api-buckets.json
`);
