#!/usr/bin/env node
/**
 * Deck build
 *
 *   node build.mjs
 *
 * Everything under src/ is a source file. Everything under dist/ is generated
 * and safe to delete: this script rebuilds all of it. src/*.css concatenates
 * into dist/deck.css, src/js/* is copied and minified, and src/deck-icons.svg
 * is copied across. Requires nothing. If esbuild happens to be installed it is
 * used for minification because it is better at it; otherwise a conservative
 * built-in minifier runs, so the build never depends on a toolchain being
 * present. That is the same promise the framework itself makes.
 */

import { readdir, readFile, writeFile, mkdir, copyFile } from 'node:fs/promises';
import { createHash } from 'node:crypto';
import { gzipSync, brotliCompressSync, constants as Z } from 'node:zlib';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.dirname(fileURLToPath(import.meta.url));
const SRC = path.join(root, 'src');
const JS = path.join(SRC, 'js');
const BRAND = path.join(SRC, 'brand');
const DIST = path.join(root, 'dist');

const pkg = JSON.parse(await readFile(path.join(root, 'package.json'), 'utf8'));

const BANNER = `/*! ==========================================================================
 *  Deck v${pkg.version} — the CSS framework for Keel
 *  Mobile first. One file. No build step. No config. No dependencies.
 *
 *  <link rel="stylesheet" href="/assets/deck.css">
 *
 *  Retheme the entire app from one line:
 *    :root { --hue-brand: 265; }
 *
 *  Layer order (so your app CSS always wins without !important):
 *    deck.reset, deck.tokens, deck.type, deck.layout, deck.components,
 *    deck.mobile, deck.motion, deck.effects, deck.utilities, deck.rtl,
 *    deck.print, then app.base, app.components, app.pages, app.overrides
 *
 *  ${pkg.license} licence. ${pkg.homepage}
 *  ======================================================================== */

`;

/* -------------------------------------------------------------------------
   Built-in CSS minifier.
   Deliberately conservative: it walks the file character by character so that
   strings, url() values, and data URIs are never touched, because a minifier
   that mangles a data URI produces a stylesheet that looks fine and renders
   wrong. It only removes comments, collapses runs of whitespace, and drops
   whitespace that is provably optional.
   ------------------------------------------------------------------------- */
function minifyCSS(css) {
  let out = '';
  let i = 0;
  const n = css.length;
  const NEEDS_SPACE = /[a-zA-Z0-9_%)\]'"-]/;

  while (i < n) {
    const c = css[i];

    // Preserve /*! banner comments, drop the rest
    if (c === '/' && css[i + 1] === '*') {
      const bang = css[i + 2] === '!';
      const end = css.indexOf('*/', i + 2);
      const stop = end === -1 ? n : end + 2;
      if (bang) out += css.slice(i, stop);
      i = stop;
      continue;
    }

    // Strings pass through untouched
    if (c === '"' || c === "'") {
      const quote = c;
      let j = i + 1;
      while (j < n && !(css[j] === quote && css[j - 1] !== '\\')) j++;
      out += css.slice(i, j + 1);
      i = j + 1;
      continue;
    }

    // url(...) passes through untouched, data URIs included
    if ((c === 'u' || c === 'U') && /^url\(/i.test(css.slice(i, i + 4))) {
      let j = i + 4, depth = 1;
      while (j < n && depth > 0) {
        if (css[j] === '(') depth++;
        else if (css[j] === ')') depth--;
        else if (css[j] === '"' || css[j] === "'") {
          const q = css[j++];
          while (j < n && !(css[j] === q && css[j - 1] !== '\\')) j++;
        }
        j++;
      }
      out += css.slice(i, j).replace(/\s+/g, '');
      i = j;
      continue;
    }

    // Collapse whitespace, then decide whether any is still needed
    if (/\s/.test(c)) {
      let j = i;
      while (j < n && /\s/.test(css[j])) j++;
      const prev = out[out.length - 1] || '';
      const next = css[j] || '';
      const structural = '{}:;,>~+()[]';
      const keep =
        NEEDS_SPACE.test(prev) && NEEDS_SPACE.test(next) ||
        // a descendant combinator, or the space before a ( in a media query
        (prev === ')' && NEEDS_SPACE.test(next)) ||
        // never glue "and(" in @media, or a calc operand
        (/[+\-*/]/.test(next) && prev !== '(' && !structural.includes(prev));
      if (keep) out += ' ';
      i = j;
      continue;
    }

    // Drop the semicolon before a closing brace
    if (c === ';') {
      let j = i + 1;
      while (j < n && /\s/.test(css[j])) j++;
      if (css[j] === '}') { i = j; continue; }
      out += ';';
      i++;
      continue;
    }

    out += c;
    i++;
  }

  return out.replace(/\s*([{};,])\s*/g, (m, ch) => ch === ',' ? ',' : ch).trim();
}

/* ------------------------------------------------------------------------- */

async function tryEsbuild() {
  try { return (await import('esbuild')).default ?? (await import('esbuild')); }
  catch { return null; }
}

/* Every published size comes from here. Brotli is what browsers actually
   negotiate for text on Cloudflare, Vercel, Netlify, and nginx with
   ngx_brotli; gzip is the fallback, so both are measured and both are
   reported. Quality 11 with a size hint is what a CDN uses for a static
   asset it caches. */
const brotliSize = buf => brotliCompressSync(buf, {
  params: {
    [Z.BROTLI_PARAM_QUALITY]: 11,
    [Z.BROTLI_PARAM_SIZE_HINT]: buf.length,
  },
}).length;

/* filename in dist/ -> { raw, gzip, brotli }. Written to dist/sizes.json at
   the end of the build; the docs and the demo page read it from there rather
   than carrying hand-typed numbers. */
const SIZES = {};

function measure(distName, buf) {
  const b = Buffer.isBuffer(buf) ? buf : Buffer.from(buf);
  SIZES[distName] = { raw: b.length, gzip: gzipSync(b).length, brotli: brotliSize(b) };
  return SIZES[distName];
}

const kb = b => (b / 1000).toFixed(1).padStart(6) + ' KB';

function report(label, raw, min, distName) {
  const s = measure(distName, min);
  console.log(
    `  ${label.padEnd(22)} ${kb(raw.length)} raw   ${kb(s.raw)} min   ` +
    `${kb(s.gzip)} gzip   ${kb(s.brotli)} brotli`
  );
}

async function build() {
  await mkdir(DIST, { recursive: true });
  const esbuild = await tryEsbuild();
  console.log(`\nDeck v${pkg.version}  ${esbuild ? '(esbuild)' : '(built-in minifier)'}\n`);

  // ---- CSS ----------------------------------------------------------------
  const files = (await readdir(SRC)).filter(f => f.endsWith('.css')).sort();
  const parts = await Promise.all(
    files.map(f => readFile(path.join(SRC, f), 'utf8'))
  );
  const css = BANNER + parts.join('\n');
  await writeFile(path.join(DIST, 'deck.css'), css);

  // The layer statement has to stay first, so minify the body and re-attach
  const minCss = esbuild
    ? BANNER.trim() + '\n' + (await esbuild.transform(css, {
        loader: 'css', minify: true, target: ['chrome117', 'safari17.4', 'firefox128']
      })).code
    : minifyCSS(css);
  await writeFile(path.join(DIST, 'deck.min.css'), minCss);
  report('deck.css', css, minCss, 'deck.min.css');

  // Per-layer files, for anyone who wants only part of Deck
  await mkdir(path.join(DIST, 'layers'), { recursive: true });
  for (const [i, f] of files.entries()) {
    await writeFile(path.join(DIST, 'layers', f.replace(/^\d+-/, '')), parts[i]);
  }
  console.log(`  ${'layers/'.padEnd(22)} ${files.length} files`);

  // ---- Static assets ------------------------------------------------------
  // The sprite is a source file, not a generated one, so it is copied rather
  // than transformed. dist/ stays entirely disposable.
  const sprite = await readFile(path.join(SRC, 'deck-icons.svg'));
  await writeFile(path.join(DIST, 'deck-icons.svg'), sprite);
  const spriteSize = measure('deck-icons.svg', sprite);
  console.log(
    `  ${'deck-icons.svg'.padEnd(22)} ${kb(spriteSize.raw)} raw   ${' '.repeat(9)}   ` +
    `${kb(spriteSize.gzip)} gzip   ${kb(spriteSize.brotli)} brotli`
  );

  // ---- Brand --------------------------------------------------------------
  // src/brand/ owns the logo. public_html/assets/images/ is a published copy of
  // it and nothing else, so there is one drawing on disk and no second sprite.
  //
  // Most of src/brand/ is itself derived from the master lockup by
  // tools/make-brand.mjs, which records the hash of every master it read. The
  // rasters cannot be regenerated in-process without a renderer, so rather
  // than silently shipping stale artwork the build stops when a master moves.
  await mkdir(path.join(DIST, 'brand'), { recursive: true });
  const brandFiles = (await readdir(BRAND)).filter(f => f !== 'sources.json').sort();
  for (const f of brandFiles) {
    await copyFile(path.join(BRAND, f), path.join(DIST, 'brand', f));
  }
  console.log(`  ${'brand/'.padEnd(22)} ${brandFiles.length} files`);

  const sha = t => createHash('sha256').update(t, 'utf8').digest('hex').slice(0, 16);
  const manifest = JSON.parse(await readFile(path.join(BRAND, 'sources.json'), 'utf8'));
  const stale = [];
  for (const [rel, recorded] of Object.entries(manifest.masters)) {
    const actual = sha(await readFile(path.join(root, rel), 'utf8'));
    if (actual !== recorded) stale.push(`${rel}  recorded ${recorded}, now ${actual}`);
  }
  if (stale.length) {
    console.error('\n  Brand assets are stale. These masters have changed since');
    console.error('  tools/make-brand.mjs last ran:\n');
    for (const line of stale) console.error(`    ${line}`);
    console.error('\n  Run: node tools/make-brand.mjs\n');
    process.exit(1);
  }

  // ---- JS -----------------------------------------------------------------
  const scripts = ['deck.js', 'deck-extras.js', 'deck-adapters.js'];
  const sources = await Promise.all(
    scripts.map(f => readFile(path.join(JS, f), 'utf8'))
  );

  for (const [i, name] of scripts.entries()) {
    // The unminified copy is what most people load, so ship it verbatim
    await writeFile(path.join(DIST, name), sources[i]);
    if (!esbuild) continue;
    const { code } = await esbuild.transform(sources[i], {
      loader: 'js', minify: true, target: 'es2022'
    });
    await writeFile(path.join(DIST, name.replace('.js', '.min.js')), code);
    report(name, sources[i], code, name.replace('.js', '.min.js'));
  }

  // Everything in one request
  const bundle = sources.join('\n');
  await writeFile(path.join(DIST, 'deck.bundle.js'), bundle);
  if (esbuild) {
    const { code } = await esbuild.transform(bundle, { loader: 'js', minify: true, target: 'es2022' });
    await writeFile(path.join(DIST, 'deck.bundle.min.js'), code);
    report('deck.bundle.js', bundle, code, 'deck.bundle.min.js');
  }

  // ESM entry, for anyone importing Deck from a bundler
  await writeFile(path.join(DIST, 'deck.esm.js'),
    `/* Deck v${pkg.version} — ES module entry.\n` +
    `   The scripts attach Deck to globalThis; this re-exports it so that\n` +
    `   \`import Deck from '@echodial/deck'\` behaves the way you expect. */\n` +
    bundle + '\nexport default globalThis.Deck;\nexport { globalThis as __deckGlobal };\n');

  // ---- Sizes ---------------------------------------------------------------
  // One place for every published figure. dist/sizes.json is the single source
  // of truth: the demo page reads it at render time, and tools/sync-sizes.mjs
  // writes it into README.md and llms.txt. Nothing is typed by hand.
  const sum = (...names) => ({
    gzip: names.reduce((n, f) => n + SIZES[f].gzip, 0),
    brotli: names.reduce((n, f) => n + SIZES[f].brotli, 0),
  });
  const previous = await readFile(path.join(DIST, 'sizes.json'), 'utf8')
    .then(JSON.parse).catch(() => ({}));
  const sizes = {
    version: pkg.version,
    note: 'Generated by build.mjs. Do not edit. Brotli is quality 11 with a size hint.',
    files: SIZES,
    totals: {
      // A page that loads the stylesheet, the sprite, and the optional core JS
      core: sum('deck.min.css', 'deck-icons.svg', 'deck.min.js'),
      // The same page with the full bundle instead of deck.min.js
      bundle: sum('deck.min.css', 'deck-icons.svg', 'deck.bundle.min.js'),
    },
    // Owned by tools/icons/build-icons.mjs, which needs the font. Preserved
    // across builds so a plain `npm run build` never drops it.
    sample: previous.sample ?? null,
  };
  await writeFile(path.join(DIST, 'sizes.json'), JSON.stringify(sizes, null, 2) + '\n');

  // ---- Documentation --------------------------------------------------------
  // The class inventory is generated from src/ and every docs page is checked
  // against it, so a class that is added, renamed, or removed cannot silently
  // leave the documentation describing something that no longer exists.
  try {
    const { default: docsApi } = await import('./tools/docs/extract.mjs');
    console.log(
      `  ${'api.json'.padEnd(22)} ${String(docsApi.counts.classes).padStart(6)} classes, ` +
      `${docsApi.counts.documented} documented, ${docsApi.counts.undocumented} outstanding`
    );
    await import('./tools/docs/usage.mjs');
    await import('./tools/docs/classify.mjs');
    const { verifyDocs } = await import('./tools/docs/verify.mjs');
    verifyDocs();
  } catch (err) {
    if (err.code !== 'ERR_MODULE_NOT_FOUND') throw err;
  }

  try {
    const { syncDocs } = await import('./tools/sync-sizes.mjs');
    const touched = await syncDocs(sizes, root);
    console.log(`\n  ${'sizes.json'.padEnd(22)} ${Object.keys(SIZES).length} files measured`);
    for (const [f, n] of touched) console.log(`  ${f.padEnd(22)} ${String(n).padStart(6)} figures synced`);
  } catch (err) {
    // tools/ is not shipped to npm, so an install can still run this build.
    if (err.code !== 'ERR_MODULE_NOT_FOUND') throw err;
  }

  console.log('\nDone.\n');
}

build().catch(err => { console.error(err); process.exit(1); });
