#!/usr/bin/env node
/**
 * One-off: insert doc comments above the 30 most-used classes in src/.
 *
 * Chosen by counting class attributes in public_html/index.php, so the ranking
 * is real usage rather than a guess about what matters. The extractor treats a
 * comment block directly above a rule as that rule's documentation, so this
 * improves every generated table at once.
 *
 * Kept in the repository as a record of which rules were annotated and why,
 * and because the same script re-run against a new ranking is how the next
 * batch gets written.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const ROOT = path.resolve(path.dirname(url.fileURLToPath(import.meta.url)), '..', '..');

/* file -> [ [selector-to-match, comment] ]. Matched on the first line whose
   trimmed text starts with the selector, so a moved rule is still found. */
const DOCS = {
  '04-layout.css': [
    ['.container {', 'Centres content and caps it at --container, with the responsive page gutter already applied. Nest one inside another and the inner one adds a second gutter, so use .container-full instead.'],
    ['.section {', 'Vertical page rhythm: a block of --space-section, which is a clamp that grows with the viewport. For spacing inside a component reach for .stack instead.'],
    ['.stack-2 {', 'Tight vertical rhythm, for a label and its control or a title and its subtitle.'],
    ['.stack-3 {', 'The default rhythm for related blocks inside a card or a section.'],
    ['.stack-6 {', 'Loose vertical rhythm, for separating whole sections of a page.'],
    ['.grid-tight {', 'Narrows the auto-fit column floor to 12rem, so the grid packs more columns in before it wraps.'],
  ],
  '05-buttons.css': [
    ['.btn-primary {', 'The one important action in a view. Solid brand fill; use exactly one per screen so it keeps meaning something.'],
    ['.btn-ghost {', 'No border or background until hover. For toolbars and dense rows where seven bordered buttons would be noise.'],
    ['.btn-sm {', 'The 34px size. Still clears the 44px touch target, because the target is a pseudo-element rather than the box.'],
    ['.btn-icon {', 'Square, icon-only button. It has no text, so it needs an aria-label.'],
  ],
  '06-forms.css': [
    ['.field {', 'One form control and everything attached to it: label, input, help text, error. The vertical gap between them comes from here, so a field never needs margins.'],
    ['.label {', 'The label for a control. A flex row, so a "required" or "optional" marker sits beside the text without extra markup.'],
    ['.check {', 'A checkbox or radio and its text, laid out as a row that clears the 44px touch target. Wrap the text in .check-text to add a second line.'],
    ['.check .check-text {', 'The text beside a checkbox or radio. A column, so a .check-note can sit under the label as a second line.'],
  ],
  '07-components.css': [
    ['.card {', 'A surface with a border, radius and shadow. It brings no padding of its own — put .card-body inside so the header, body and footer can each own their spacing.'],
    ['.card-body {', 'The padded region of a card. A column with a gap, so children stack without margins.'],
    ['.card-title {', 'The heading inside a card. Sized independently of h1-h6 so the same card works at any heading level.'],
    ['.badge {', 'A small inline status pill. Sized in em, so it tracks whatever text it sits next to rather than needing a size variant per context.'],
    ['.table .num {', 'A numeric table cell: aligned to the end edge with tabular figures, so digits line up down the column.'],
    ['.nav-link {', 'One destination in a .navbar. Carries the hover and current-page states; use .btn only for actions, never for navigation.'],
  ],
  '08-mobile.css': [
    ['.icon {', 'An SVG icon sized in em, so it scales with the text it sits beside and lands on the baseline. Icons are filled with currentColor and inherit the surrounding colour.'],
    ['.icon-lg {', 'A 1.6em icon, for icon tiles and empty states where the default would look undersized.'],
    ['.emoji {', 'Pins the emoji font stack and metrics so an emoji renders at the same size and baseline on Windows, iOS and Android instead of jumping between them.'],
  ],
  '09-utilities.css': [
    ['.push {', 'Pushes this element and everything after it to the far end of a flex row. The one-class replacement for a spacer div.'],
    ['.text-sm {', 'One step down the type scale, for secondary text and dense rows.'],
    ['.text-muted {', 'Secondary text colour. Still meets contrast against every surface Deck defines, so it is safe for body copy rather than decoration only.'],
    ['.fw-semi {', 'Semibold. The weight for a label or a name that needs to lead without becoming a heading.'],
    ['.nums {', 'Tabular figures, so numbers line up in a column and do not jitter when they change.'],
  ],
  '12-datagrid.css': [
    ['.dg .dg-num {', 'A numeric column in the data grid: aligned to the end edge, and sorted numerically when the header carries data-sort="num".'],
  ],
  '14-charts.css': [
    ['.s1 {', 'Series 1. Every chart mark reads --series, so a colour is set once on the element and the fill, stroke and legend swatch all follow.'],
  ],
};

let inserted = 0;
let missed = [];

for (const [file, entries] of Object.entries(DOCS)) {
  const full = path.join(ROOT, 'src', file);
  const lines = fs.readFileSync(full, 'utf8').split('\n');

  /* Work from the bottom so earlier insertions do not shift later targets. */
  const targets = entries
    .map(([needle, comment]) => {
      const idx = lines.findIndex((l) => l.trim().startsWith(needle));
      return { idx, needle, comment };
    })
    .sort((a, b) => b.idx - a.idx);

  for (const { idx, needle, comment } of targets) {
    if (idx < 0) { missed.push(`${file}: ${needle}`); continue; }
    const indent = lines[idx].match(/^\s*/)[0];
    /* Skip only when the rule already carries real documentation. A section
       divider directly above it does not count — that is a heading for the
       group, and the rule underneath still deserves its own line. */
    const previous = (lines[idx - 1] || '').trim();
    const isDividerAbove = /^[-=]{3,}|[-=]{5,}\s*\*\/$/.test(previous.replace(/^\/\*\s*/, ''));
    if (previous.endsWith('*/') && !isDividerAbove) {
      missed.push(`${file}: ${needle} (already documented)`);
      continue;
    }

    /* Wrap at 78 columns including the indent, in Deck's comment style. */
    const width = 78 - indent.length - 5;
    const words = comment.split(' ');
    const out = [];
    let line = '';
    for (const w of words) {
      if ((line + ' ' + w).trim().length > width) { out.push(line.trim()); line = w; }
      else line = `${line} ${w}`;
    }
    if (line.trim()) out.push(line.trim());

    const block = out.length === 1
      ? [`${indent}/* ${out[0]} */`]
      : [`${indent}/* ${out[0]}`, ...out.slice(1, -1).map((l) => `${indent}   ${l}`), `${indent}   ${out.at(-1)} */`];

    lines.splice(idx, 0, ...block);
    inserted++;
  }

  fs.writeFileSync(full, lines.join('\n'));
}

console.log(`\n  docs: inserted ${inserted} doc comments into src/`);
if (missed.length) {
  console.log('  not inserted:');
  for (const m of missed) console.log(`    ${m}`);
}
console.log('');
