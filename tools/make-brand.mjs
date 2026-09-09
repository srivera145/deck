#!/usr/bin/env node
/**
 * Deck brand assets
 *
 *   node tools/make-brand.mjs
 *
 * One master drawing, everything else derived from it. The master is
 * src/brand/deck-logo.svg (the horizontal lockup, in currentColor) plus the
 * #deck-mark symbol in src/deck-icons.svg. From those this writes:
 *
 *   src/brand/deck-logo-light.svg      lockup with an explicit dark fill
 *   src/brand/deck-logo-dark.svg       lockup with an explicit light fill
 *   src/brand/deck-mark.svg            mark-only favicon, theme-aware
 *   src/brand/deck-og.png              1200x630 social card
 *   src/brand/deck-apple-touch-icon.png  180x180 home-screen icon
 *
 * The two SVGs exist because an externally referenced SVG has no colour
 * context: currentColor resolves to black, so the master is invisible on a
 * dark background. GitHub renders READMEs both ways, hence a pair.
 *
 * The PNGs exist because social platforms will not render SVG for og:image
 * and Apple will not render it for apple-touch-icon. Rasterising needs a
 * renderer, so this shells out to headless Chrome (or Edge) via its
 * --screenshot flag. That keeps the framework itself dependency-free: the
 * browser is only needed when the mark changes, not to build or use Deck.
 *
 * The outputs are committed, and this writes src/brand/sources.json holding a
 * hash of every master it read. build.mjs recomputes those hashes and fails if
 * they have moved, so a changed mark cannot ship with stale artwork. Hashes
 * rather than timestamps, because a fresh clone gives every file the same
 * mtime and a timestamp check would be meaningless.
 */

import { readFile, writeFile, mkdir, rm } from 'node:fs/promises';
import { createHash } from 'node:crypto';
import { existsSync } from 'node:fs';
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import path from 'node:path';
import os from 'node:os';
import { fileURLToPath } from 'node:url';

const run = promisify(execFile);
export const sha = s => createHash('sha256').update(s, 'utf8').digest('hex').slice(0, 16);
const root = path.dirname(path.dirname(fileURLToPath(import.meta.url)));
const BRAND = path.join(root, 'src', 'brand');
const SPRITE = path.join(root, 'src', 'deck-icons.svg');
const MASTER = path.join(BRAND, 'deck-logo.svg');

/* Palette. These are Deck's own tokens at the shipped hues, resolved to sRGB
   once and written down, because a static file cannot carry an oklch() that
   every consumer renders identically. Keep them in step with 01-tokens.css. */
const INK_DARK = '#151b1f';   /* --ink-900  on a light ground */
const INK_LIGHT = '#eef2f4';  /* --ink-100  on a dark ground  */
const BRAND_600 = '#0b6e84';  /* --brand-600 at --hue-brand: 196 */

/* ---------------------------------------------------------------- browser */

function findBrowser() {
  const candidates = [
    process.env.CHROME_PATH,
    'C:/Program Files/Google/Chrome/Application/chrome.exe',
    'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
    'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',
    'C:/Program Files/Microsoft/Edge/Application/msedge.exe',
    '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
    '/usr/bin/google-chrome',
    '/usr/bin/chromium',
    '/usr/bin/chromium-browser'
  ].filter(Boolean);
  return candidates.find(p => existsSync(p)) || null;
}

async function shoot(browser, html, out, width, height) {
  const tmp = await mkdir(path.join(os.tmpdir(), 'deck-brand-'), { recursive: true })
    .then(() => path.join(os.tmpdir(), 'deck-brand-'));
  await mkdir(tmp, { recursive: true });
  const page = path.join(tmp, 'page.html');
  await writeFile(page, html, 'utf8');

  await run(browser, [
    '--headless',
    '--disable-gpu',
    '--hide-scrollbars',
    '--force-device-scale-factor=1',
    '--default-background-color=00000000',
    `--window-size=${width},${height}`,
    `--screenshot=${out}`,
    'file:///' + page.replace(/\\/g, '/')
  ], { timeout: 60000 });

  await rm(tmp, { recursive: true, force: true });
}

/* ------------------------------------------------------------------ pages */

const shell = (width, height, background, body) => `<!doctype html>
<html><head><meta charset="utf-8"><style>
  html, body { margin: 0; padding: 0; }
  body {
    inline-size: ${width}px; block-size: ${height}px;
    background: ${background};
    display: grid; place-items: center;
  }
  svg { display: block; }
</style></head><body>${body}</body></html>`;

/* Pull the mark's paths straight out of the sprite so the icon and the sprite
   can never disagree about what the mark is. */
async function markPaths() {
  const sprite = await readFile(SPRITE, 'utf8');
  const open = sprite.indexOf('<symbol id="deck-mark"');
  if (open < 0) throw new Error('src/deck-icons.svg has no #deck-mark symbol');
  const close = sprite.indexOf('</symbol>', open);
  const symbol = sprite.slice(open, close);
  return symbol.slice(symbol.indexOf('>') + 1);
}

/* -------------------------------------------------------------------- run */

async function main() {
  const master = await readFile(MASTER, 'utf8');
  if (!master.includes('currentColor')) {
    throw new Error('src/brand/deck-logo.svg should be authored in currentColor');
  }

  /* 1. themed SVG copies -------------------------------------------------- */
  const themed = (fill) => master.replace(/currentColor/g, fill);
  await writeFile(path.join(BRAND, 'deck-logo-light.svg'), themed(INK_DARK), 'utf8');
  await writeFile(path.join(BRAND, 'deck-logo-dark.svg'), themed(INK_LIGHT), 'utf8');
  console.log('  deck-logo-light.svg          fill ' + INK_DARK);
  console.log('  deck-logo-dark.svg           fill ' + INK_LIGHT);

  /* 2. favicon ------------------------------------------------------------
     The mark alone: a 124x24 lockup is a smear at 16px. An SVG favicon can
     carry its own media query, so this one follows the browser chrome
     instead of picking a side. */
  const paths = await markPaths();
  const favicon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-label="Deck">
<style>
  g { fill: ${INK_DARK}; stroke: ${INK_DARK}; }
  @media (prefers-color-scheme: dark) { g { fill: ${INK_LIGHT}; stroke: ${INK_LIGHT}; } }
</style>
<g stroke-width="0.5" stroke-linejoin="round" stroke-linecap="round">${paths}</g>
</svg>
`;
  await writeFile(path.join(BRAND, 'deck-mark.svg'), favicon, 'utf8');
  console.log('  deck-mark.svg                favicon, follows browser chrome');

  /* 3. rasters ------------------------------------------------------------ */
  const browser = findBrowser();
  if (!browser) {
    console.error('\n  No Chrome or Edge found, so the PNGs were not regenerated.');
    console.error('  Set CHROME_PATH=/path/to/chrome and run this again.\n');
    process.exitCode = 1;
    return;
  }
  console.log('  rasterising with ' + path.basename(browser));

  /* og:image — the lockup centred, white on brand, with generous clear space.
     The lockup is 123.81 x 24; at 520px wide it leaves ~340px of margin each
     side and ~265px above and below. */
  const ogSvg = master.replace(/currentColor/g, '#ffffff')
    .replace('<svg ', '<svg width="520" ');
  await shoot(browser, shell(1200, 630, BRAND_600, ogSvg),
    path.join(BRAND, 'deck-og.png'), 1200, 630);
  console.log('  deck-og.png                  1200x630 on ' + BRAND_600);

  /* apple-touch-icon — the mark alone. A wordmark is illegible at 180px. */
  const icon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="116" height="116"
     fill="#ffffff" stroke="#ffffff" stroke-width="0.5" stroke-linejoin="round"
     stroke-linecap="round">${paths}</svg>`;
  await shoot(browser, shell(180, 180, BRAND_600, icon),
    path.join(BRAND, 'deck-apple-touch-icon.png'), 180, 180);
  console.log('  deck-apple-touch-icon.png    180x180 on ' + BRAND_600);

  /* 4. record what these were derived from -------------------------------- */
  await writeFile(path.join(BRAND, 'sources.json'), JSON.stringify({
    note: 'Written by tools/make-brand.mjs. build.mjs checks these hashes.',
    masters: {
      'src/brand/deck-logo.svg': sha(master),
      'src/deck-icons.svg': sha(await readFile(SPRITE, 'utf8'))
    }
  }, null, 2) + '\n', 'utf8');
  console.log('  sources.json                 master hashes recorded');
}

console.log('\nDeck brand assets\n');
await main();
console.log('\nDone.\n');
