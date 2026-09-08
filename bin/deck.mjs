#!/usr/bin/env node
/**
 * deck — copy Deck's assets into a project.
 *
 *   npx @echodial/deck init
 *   npx @echodial/deck init public/assets
 *   npx @echodial/deck init --min --bundle
 *   npx @echodial/deck starter public/index.html
 *
 * For people who are not using npm as a build tool and just want the files
 * sitting in a folder, which is most of the people Deck is for.
 */

import { copyFile, mkdir, readFile, writeFile, access } from 'node:fs/promises';
import { constants } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.dirname(path.dirname(fileURLToPath(import.meta.url)));
const DIST = path.join(root, 'dist');
const pkg = JSON.parse(await readFile(path.join(root, 'package.json'), 'utf8'));

// Piping into head/less closes stdout early; that is not a crash
process.stdout.on('error', err => { if (err.code === 'EPIPE') process.exit(0); });

const args = process.argv.slice(2);
const cmd = args[0];
const flags = new Set(args.filter(a => a.startsWith('--')));
const positional = args.slice(1).filter(a => !a.startsWith('--'));

const c = {
  dim: s => `\x1b[2m${s}\x1b[0m`,
  bold: s => `\x1b[1m${s}\x1b[0m`,
  green: s => `\x1b[32m${s}\x1b[0m`,
  red: s => `\x1b[31m${s}\x1b[0m`
};

const exists = async p => { try { await access(p, constants.F_OK); return true; } catch { return false; } };

function usage() {
  console.log(`
${c.bold(`Deck v${pkg.version}`)} ${c.dim('— a CSS framework with no build step')}

  ${c.bold('npx @echodial/deck init')} ${c.dim('[dir]')}     copy the assets (default: assets/deck)
  ${c.bold('npx @echodial/deck starter')} ${c.dim('[file]')}  write a starter HTML page
  ${c.bold('npx @echodial/deck list')}              show what is in the package

  ${c.dim('--min')}       copy the minified stylesheet only
  ${c.dim('--bundle')}    copy one combined script instead of three
  ${c.dim('--css-only')}  skip the scripts entirely
  ${c.dim('--force')}     overwrite files that already exist
`);
}

const SETS = {
  css: ['deck.css', 'deck-icons.svg'],
  cssMin: ['deck.min.css', 'deck-icons.svg'],
  js: ['deck.js', 'deck-extras.js', 'deck-adapters.js'],
  jsBundle: ['deck.bundle.min.js']
};

async function init() {
  const target = path.resolve(process.cwd(), positional[0] || 'assets/deck');
  const files = [
    ...(flags.has('--min') ? SETS.cssMin : SETS.css),
    ...(flags.has('--css-only') ? [] : flags.has('--bundle') ? SETS.jsBundle : SETS.js)
  ];

  await mkdir(target, { recursive: true });

  let written = 0, skipped = 0, missing = 0;
  for (const file of files) {
    const from = path.join(DIST, file);
    const to = path.join(target, file);
    if (!(await exists(from))) { missing++; continue; }
    if (await exists(to) && !flags.has('--force')) { skipped++; continue; }
    await copyFile(from, to);
    written++;
    console.log(`  ${c.green('+')} ${path.relative(process.cwd(), to)}`);
  }

  if (skipped) console.log(c.dim(`  ${skipped} already there (use --force to overwrite)`));
  if (missing) console.log(c.dim(`  ${missing} not built (run \`npm run build\` in the package)`));

  const rel = path.relative(process.cwd(), target).replace(/\\/g, '/');
  // The folder on disk is not the folder in the URL. Strip the usual document
  // roots so the snippet we print is actually pasteable.
  const web = rel.replace(/^(public_html|public|web|htdocs|httpdocs|www|wwwroot|dist|static)\//, '');
  const cssFile = flags.has('--min') ? 'deck.min.css' : 'deck.css';

  console.log(`
${c.bold('Add to your layout:')}

  <link rel="stylesheet" href="/${web}/${cssFile}">${flags.has('--css-only') ? '' : `
  <script src="/${web}/${flags.has('--bundle') ? 'deck.bundle.min.js' : 'deck.js'}" defer></script>`}

${c.dim('deck-icons.svg sits beside deck.js and is found automatically.')}
${c.dim('To load the sprite from somewhere else:')}

  <script src="/${web}/deck.js" data-deck-icons="/your/path/deck-icons.svg" defer></script>
`);

  return written;
}

async function starter() {
  const file = path.resolve(process.cwd(), positional[0] || 'index.html');
  if (await exists(file) && !flags.has('--force')) {
    console.log(c.red(`  ${path.relative(process.cwd(), file)} already exists. Use --force to overwrite.`));
    return;
  }

  const html = `<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Built with Deck</title>
<link rel="stylesheet" href="/assets/deck/deck.css">
<script src="/assets/deck/deck.js" defer></script>
<script src="/assets/deck/deck-extras.js" defer></script>
<style>
  /* Your CSS goes in a layer and beats every Deck rule with no !important. */
  @layer app.pages {
    .hero { padding-block: var(--space-section); }
  }
</style>
</head>
<body>

<a class="skip-link" href="#main">Skip to content</a>

<header class="sticky-top">
  <div class="container">
    <nav class="navbar">
      <a class="navbar-brand" href="/">Your app</a>
      <div class="navbar-links">
        <a class="nav-link" aria-current="page" href="/">Home</a>
        <a class="nav-link" href="/about">About</a>
      </div>
      <button class="btn btn-icon btn-ghost push" data-deck-theme aria-label="Switch theme">
        <svg class="icon"><use href="/assets/deck/deck-icons.svg#moon"></use></svg>
      </button>
    </nav>
  </div>
</header>

<main id="main" class="container section stack-8">

  <section class="hero stack-5">
    <h1 class="display">Start here.</h1>
    <p class="lede">This page is using Deck. There is no build step, no config file,
      and no dependencies. Change one number below and the whole page follows.</p>
    <div class="cluster">
      <button class="btn btn-primary btn-lg">Primary action</button>
      <button class="btn btn-lg">Secondary</button>
    </div>
  </section>

  <section class="stack-4">
    <label class="label" for="hue">Brand hue</label>
    <input id="hue" class="range" type="range" min="0" max="360" value="196"
           oninput="Deck.hue(this.value)">
    <div class="grid">
      <div class="card"><div class="card-body">
        <h3 class="card-title">Cards</h3>
        <p class="text-sm text-muted">Every surface, badge, and focus ring follows the hue.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <h3 class="card-title">Forms</h3>
        <div class="field">
          <label class="label" for="email">Email</label>
          <input class="input" id="email" type="email" placeholder="you@example.com">
        </div>
      </div></div>
      <div class="card"><div class="card-body">
        <h3 class="card-title">Feedback</h3>
        <button class="btn btn-sm btn-soft"
                onclick="Deck.toast({kind:'good', title:'It works'})">Fire a toast</button>
      </div></div>
    </div>
  </section>

</main>

<footer class="footer">
  <div class="container footer-bottom">
    <span>Built with Deck</span>
  </div>
</footer>

</body>
</html>
`;

  await mkdir(path.dirname(file), { recursive: true });
  await writeFile(file, html);
  console.log(`  ${c.green('+')} ${path.relative(process.cwd(), file)}`);
  console.log(c.dim(`\n  Run \`npx @echodial/deck init public/assets/deck\` if you have not copied the assets yet.\n`));
}

async function list() {
  const { readdir, stat } = await import('node:fs/promises');
  const files = (await readdir(DIST)).filter(f => !f.startsWith('.')).sort();
  console.log(`\n${c.bold(`Deck v${pkg.version}`)} ${c.dim(DIST)}\n`);
  for (const f of files) {
    const s = await stat(path.join(DIST, f));
    if (s.isDirectory()) { console.log(`  ${f}/`); continue; }
    console.log(`  ${f.padEnd(26)} ${c.dim((s.size / 1024).toFixed(1) + ' KB')}`);
  }
  console.log();
}

switch (cmd) {
  case 'init': await init(); break;
  case 'starter': await starter(); break;
  case 'list': await list(); break;
  default: usage();
}
