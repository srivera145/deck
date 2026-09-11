/**
 * Write the measured sizes into the documentation.
 *
 * Three passes in a row were spent correcting figures that had gone stale
 * because the same number was typed into four files. This is the fix: build.mjs
 * measures once, writes dist/sizes.json, and calls syncDocs() to stamp the
 * numbers into README.md and public_html/llms.txt. The demo page reads
 * sizes.json directly at render time, so it needs no stamping at all.
 *
 * Each rule is anchored to stable surrounding prose and must match EXACTLY
 * ONCE. If someone rewrites a sentence and a rule stops matching, the build
 * fails loudly rather than quietly leaving a stale number behind — which is
 * the failure this whole mechanism exists to prevent.
 *
 * The version rides along. README.md and llms.txt both state it, it was typed
 * into both by hand, and sizes.json already carries it from package.json.
 */

import { readFile, writeFile } from 'node:fs/promises';
import path from 'node:path';

const kb = n => `${(n / 1000).toFixed(1)} KB`;

/** A number the docs may carry: 26.6, 8.9, 171.5. */
const N = String.raw`[\d.]+ KB`;

/**
 * Compile an anchor. Spaces in the pattern match any run of whitespace, so a
 * rule keeps working when Markdown prose rewraps across lines.
 */
const anchor = pattern => new RegExp(pattern.replace(/ /g, String.raw`\s+`), 'g');

/** Rules per file: [name, anchor, replacement]. Each must match exactly once. */
function rules(s) {
  const f = s.files;
  const br = n => kb(f[n].brotli);
  const gz = n => kb(f[n].gzip);
  const { sample, totals } = s;

  /* A `file | brotli | gzip` row in a Markdown table. */
  const row = (name, bold, tail = '') =>
    anchor(String.raw`\| \`${name.replace(/[.]/g, String.raw`\.`)}\` \| ` +
      (bold ? String.raw`\*\*${N}\*\*` : N) + String.raw` \| ${N}` +
      (bold ? String.raw` \|` : ` \\| ${tail}`));

  const readme = [
    ['version line',
      anchor(String.raw`Version [0-9.]+ · MIT`),
      () => `Version ${s.version} · MIT`],

    ['headline stylesheet size',
      anchor(String.raw`ships as one ${N} Brotli \(${N} gzip\) stylesheet`),
      () => `ships as one ${br('deck.min.css')} Brotli (${gz('deck.min.css')} gzip) stylesheet`],

    ['weight intro total',
      anchor(String.raw`transfers \*\*${N}\*\* Brotli, or \*\*${N}\*\* gzip`),
      () => `transfers **${kb(totals.core.brotli)}** Brotli, or **${kb(totals.core.gzip)}** gzip`],

    ['weight table css', row('deck.min.css', true),
      () => `| \`deck.min.css\` | **${br('deck.min.css')}** | ${gz('deck.min.css')} |`],
    ['weight table sprite', row('deck-icons.svg', true),
      () => `| \`deck-icons.svg\` | **${br('deck-icons.svg')}** | ${gz('deck-icons.svg')} |`],
    ['weight table js', row('deck.min.js', true),
      () => `| \`deck.min.js\` | **${br('deck.min.js')}** | ${gz('deck.min.js')} |`],
    ['weight table total',
      anchor(String.raw`\| \*\*All three\*\* \| \*\*${N}\*\* \| \*\*${N}\*\* \|`),
      () => `| **All three** | **${kb(totals.core.brotli)}** | **${kb(totals.core.gzip)}** |`],

    ['bundle swap',
      anchor(String.raw`makes the JavaScript ${N} and the total ${N} Brotli \(${N} gzip\)`),
      () => `makes the JavaScript ${br('deck.bundle.min.js')} and the total ` +
            `${kb(totals.bundle.brotli)} Brotli (${kb(totals.bundle.gzip)} gzip)`],

    ['twelve icon sample',
      anchor(String.raw`A twelve-icon sprite measures ${N} Brotli \(${N} gzip\)`),
      () => `A twelve-icon sprite measures ${kb(sample.brotli)} Brotli (${kb(sample.gzip)} gzip)`],
    ['sample comparison',
      anchor(String.raw`Against ${N} for the full set`),
      () => `Against ${br('deck-icons.svg')} for the full set`],
    ['sample in the icons section',
      anchor(String.raw`or a twelve icon one at ${N} Brotli`),
      () => `or a twelve icon one at ${kb(sample.brotli)} Brotli`],

    ['package table css', row('deck.min.css', false, 'The whole framework'),
      () => `| \`deck.min.css\` | ${br('deck.min.css')} | ${gz('deck.min.css')} | The whole framework`],
    ['package table js', row('deck.min.js', false, 'Optional behaviour'),
      () => `| \`deck.min.js\` | ${br('deck.min.js')} | ${gz('deck.min.js')} | Optional behaviour`],
    ['package table extras', row('deck-extras.min.js', false, 'Date picker'),
      () => `| \`deck-extras.min.js\` | ${br('deck-extras.min.js')} | ${gz('deck-extras.min.js')} | Date picker`],
    ['package table adapters', row('deck-adapters.min.js', false, 'Optional library'),
      () => `| \`deck-adapters.min.js\` | ${br('deck-adapters.min.js')} | ${gz('deck-adapters.min.js')} | Optional library`],
    ['package table bundle', row('deck.bundle.min.js', false, 'All three scripts'),
      () => `| \`deck.bundle.min.js\` | ${br('deck.bundle.min.js')} | ${gz('deck.bundle.min.js')} | All three scripts`],
    ['package table sprite', row('deck-icons.svg', false, '152 symbols'),
      () => `| \`deck-icons.svg\` | ${br('deck-icons.svg')} | ${gz('deck-icons.svg')} | 152 symbols`],
  ];

  const llms = [
    ['version line',
      anchor(String.raw`Version [0-9.]+, MIT licensed`),
      () => `Version ${s.version}, MIT licensed`],
    ['blockquote stylesheet size',
      anchor(String.raw`ships as a single ${N} Brotli \(${N} gzip\) stylesheet`),
      () => `ships as a single ${br('deck.min.css')} Brotli (${gz('deck.min.css')} gzip) stylesheet`],
    ['sprite line',
      anchor(String.raw`brand marks, ${N} Brotli \(${N} gzip\)\. Trim it`),
      () => `brand marks, ${br('deck-icons.svg')} Brotli (${gz('deck-icons.svg')} gzip). Trim it`],
    ['sample line',
      anchor(String.raw`twelve icons measure ${N}\.`),
      () => `twelve icons measure ${kb(sample.brotli)}.`],
  ];

  return { 'README.md': readme, 'public_html/llms.txt': llms };
}

export async function syncDocs(sizes, root) {
  if (!sizes.sample) {
    throw new Error(
      'sizes.json has no `sample` block. Run `npm run icons` once to measure the ' +
      'twelve-icon sample sprite, then build again.'
    );
  }

  const touched = [];
  for (const [rel, list] of Object.entries(rules(sizes))) {
    const file = path.join(root, rel);
    let text = await readFile(file, 'utf8');

    for (const [name, find, to] of list) {
      const hits = text.match(find);
      if (!hits || hits.length !== 1) {
        throw new Error(
          `sizes sync: rule "${name}" matched ${hits ? hits.length : 0} times in ${rel}, ` +
          'expected exactly 1. The surrounding wording changed; update the rule in ' +
          'tools/sync-sizes.mjs so the figure cannot go stale.'
        );
      }
      text = text.replace(find, to());
    }

    await writeFile(file, text);
    touched.push([rel, list.length]);
  }
  return touched;
}
