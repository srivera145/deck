#!/usr/bin/env node
/**
 * Documentation verifier.
 *
 *     node tools/docs/verify.mjs                # check
 *     node tools/docs/verify.mjs --baseline     # re-record the backlog
 *
 * The extractor makes the inventory; this makes drift a build error. Without
 * it, 900-odd documented classes rot within two releases and nobody notices
 * until a reader does.
 *
 * Three failures, all hard:
 *
 *   1. A docs page claims a class that no longer exists in src/.
 *   2. A docs page uses a class in markup or an example that is not real.
 *   3. A class exists in src/ with no docs entry and is not in the known
 *      backlog — i.e. someone added a class and did not document it.
 *
 * The backlog in tools/docs/undocumented.txt is the honest count of what is
 * not written yet. It is allowed to shrink and never to grow: a new class must
 * either be documented or explicitly added to it, which forces the decision to
 * be made rather than defaulted.
 *
 * Classes local to the docs site itself are prefixed `dx-` and are exempt.
 * That is the whole convention: everything else in a docs page must be a real
 * Deck class, which is what catches a typo in an example.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';

const HERE = path.dirname(url.fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..', '..');
const API = path.join(ROOT, 'dist', 'api.json');
const DOCS = path.join(ROOT, 'public_html', 'docs');
/* Every authored page, not just the docs. `.stack-5` sat in the demo for
   months rendering the wrong gap because nothing checked it: CSS cannot throw,
   so an unknown class is silent by nature. This is where it stops being
   silent. Generated copies under assets/ are excluded — they are build output,
   not authorship. */
const AUTHORED = path.join(ROOT, 'public_html');
const SKIP_DIRS = new Set(['assets', 'node_modules', 'vendor']);
const BASELINE = path.join(HERE, 'undocumented.txt');
const BUCKETS = path.join(ROOT, 'dist', 'api-buckets.json');
const API_MD = path.join(ROOT, 'API.md');

const DOCS_PREFIX = 'dx-';

/* is-* classes that are not states and so have no owning component. Both set
   inline-size: in `19-logical.css` the `is` prefix means inline-size, not "is",
   which collides with the state convention used everywhere else. Renaming them
   is a source fix for another pass; recorded in FINDINGS.md. */
const NOT_STATES = new Map([
  ['is-auto', 'inline-size: auto — an inline-size utility, not a state'],
  ['is-full', 'inline-size: 100% — an inline-size utility, not a state'],
]);

/* Only a class attribute that is a plain list of class tokens can be checked
   statically. One built by PHP concatenation — class="' . $variant . '" — is
   skipped here; those classes are covered instead by the page's `documents`
   claim, which is checked against the inventory just as strictly. */
const STATIC_CLASS_LIST = /^[A-Za-z0-9_\- ]+$/;
const isStatic = (value) => STATIC_CLASS_LIST.test(value.trim());

/* A page may define its own classes in an inline <style> block — the demo's
   .hero and .ramp, the docs site's dx-* chrome. Those are legitimate and
   local, so collect them per file and count them as real for that file only.
   A class that is neither in src/ nor defined on the page is the error. */
function localClasses(text) {
  const names = new Set();
  for (const block of text.matchAll(/<style\b[^>]*>([\s\S]*?)<\/style>/gi)) {
    const css = block[1].replace(/\/\*[\s\S]*?\*\//g, '');
    for (const m of css.matchAll(/\.(-?[_a-zA-Z][\w-]*)/g)) names.add(m[1]);
  }
  return names;
}

function walk(dir) {
  if (!fs.existsSync(dir)) return [];
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap((e) => {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) return SKIP_DIRS.has(e.name) ? [] : walk(p);
    return /\.(php|html)$/.test(e.name) ? [p] : [];
  });
}

function readBaseline() {
  if (!fs.existsSync(BASELINE)) return new Set();
  return new Set(
    fs.readFileSync(BASELINE, 'utf8')
      .split(/\r?\n/)
      .map((l) => l.replace(/#.*/, '').trim())
      .filter(Boolean)
  );
}

export function verifyDocs() {
  if (!fs.existsSync(API)) {
    console.error('\n  docs: dist/api.json is missing. Run tools/docs/extract.mjs first.\n');
    process.exit(1);
  }
  const api = JSON.parse(fs.readFileSync(API, 'utf8'));
  const real = new Set(api.classes.map((c) => c.name));

  /* Things that start with a dot and are not classes. `.gitattributes` in the
     README is a filename, and no amount of pattern matching will tell it apart
     from a class name, so it is listed. */
  const NOT_A_CLASS = new Set([
    'gitattributes', 'gitignore', 'gitkeep', 'npmrc', 'npmignore', 'nvmrc',
    'editorconfig', 'env', 'htaccess', 'mjs', 'js', 'cjs', 'css', 'html', 'php',
    'json', 'md', 'svg', 'txt', 'map', 'lock', 'ts', 'woff2',
  ]);

  /* FINDINGS.md names removed classes deliberately — it is the record of what
     went and why. Naming one is not a claim that you can still use it. */
  const editsFile = path.join(ROOT, 'tools', 'docs', 'removed.json');
  const REMOVED_OK = new Set(
    fs.existsSync(editsFile)
      ? JSON.parse(fs.readFileSync(editsFile, 'utf8')).removed.map((r) => r.name)
      : []
  );
  const pages = walk(AUTHORED);

  const claimed = new Map();   // class -> [pages]
  const used = new Map();      // class -> [pages]
  const staleClaims = [];
  const unrealUses = [];
  const componentPages = [];

  /* Prose drifts too, and nothing was checking it. The freeze removed .is-full,
     .bs-full, .mbs-* and .mbe-*, and README.md went on listing all of them as
     logical utilities — a reader would have typed a class that does not exist.
     Backticked `.name` in the Markdown is specific enough to check without
     catching ordinary prose; a trailing -* is a family, so it passes if any
     class starts with that stem.

     README.md only. FINDINGS.md names .stack-depth, .row-cq and .p-5 on
     purpose — it is the record of what was renamed and what never existed, and
     a history that cannot mention anything that has gone is not a history. */
  const proseGhosts = [];
  for (const rel of ['README.md']) {
    const file = path.join(ROOT, rel);
    if (!fs.existsSync(file)) continue;
    const text = fs.readFileSync(file, 'utf8');
    let line = 1;
    for (let i = 0; i < text.length; i++) {
      if (text[i] === '\n') { line++; continue; }
      if (text[i] !== '`') continue;
      const end = text.indexOf('`', i + 1);
      if (end < 0) break;
      const token = text.slice(i + 1, end);
      i = end;
      const m = /^\.([a-z][\w-]*?)(-\*)?$/.exec(token);
      if (!m) continue;
      const [, name, family] = m;
      if (NOT_A_CLASS.has(name)) continue;
      const ok = family
        ? [...real].some((r) => r.startsWith(`${name}-`))
        : real.has(name) || REMOVED_OK.has(name);
      if (!ok) proseGhosts.push({ name: token, page: rel, line });
    }
  }

  for (const file of pages) {
    const rel = path.relative(ROOT, file).replace(/\\/g, '/');
    const text = fs.readFileSync(file, 'utf8');

    /* 1. what this page claims to document. The body must be nothing but
       quoted class names, so the example in _layout.php's own docblock cannot
       be mistaken for a claim. Partials, named with a leading underscore, are
       shell rather than pages and never claim anything. */
    const isPartial = path.basename(file).startsWith('_');

    /* A component page names the component it covers and accounts for every
       source file that styles it. `.btn` is styled in ten files; nobody
       remembers ten, so the page has to say something about each one. */
    const compMatch = isPartial ? null : text.match(/['"]component['"]\s*=>\s*['"]([A-Za-z0-9_-]+)['"]/);
    if (compMatch) {
      const accounts = new Map();
      const block = text.match(/['"]accounts['"]\s*=>\s*\[([\s\S]*?)\n\s*\]/);
      if (block) {
        for (const m of block[1].matchAll(/['"]([\w.-]+\.css)['"]\s*=>\s*['"]([^'"]*)['"]/g)) {
          accounts.set(m[1], m[2]);
        }
      }
      componentPages.push({ page: rel, component: compMatch[1], accounts });
    }

    const claim = isPartial
      ? null
      : text.match(/['"]documents['"]\s*=>\s*\[((?:\s*['"][A-Za-z0-9_-]+['"]\s*,?)+)\s*\]/);
    if (claim) {
      for (const m of claim[1].matchAll(/['"]([^'"]+)['"]/g)) {
        const name = m[1];
        if (!claimed.has(name)) claimed.set(name, []);
        claimed.get(name).push(rel);
        if (!real.has(name)) staleClaims.push({ name, page: rel });
      }
    }

    /* 2. every class this page puts in markup, including inside examples */
    const local = localClasses(text);
    for (const m of text.matchAll(/class=(["'])([\s\S]*?)\1/g)) {
      if (!isStatic(m[2])) continue;
      const at = text.slice(0, m.index).split('\n').length;
      for (const name of m[2].split(/\s+/).filter(Boolean)) {
        if (name.startsWith(DOCS_PREFIX) || local.has(name)) continue;
        if (!used.has(name)) used.set(name, []);
        used.get(name).push(rel);
        if (!real.has(name)) unrealUses.push({ name, page: rel, line: at });
      }
    }
  }

  /* ---- Component completeness --------------------------------------------
     A page that claims a component must account for every file that styles it
     and every class and state in it. Completeness stops being a matter of the
     author's memory. */
  const incomplete = [];
  for (const { page: rel, component, accounts } of componentPages) {
    const comp = api.components?.[component];
    if (!comp) {
      incomplete.push({ page: rel, kind: `no component named "${component}" in the inventory` });
      continue;
    }
    const claimedHere = new Set(
      (fs.readFileSync(path.join(ROOT, rel), 'utf8')
        .match(/['"]documents['"]\s*=>\s*\[((?:\s*['"][A-Za-z0-9_-]+['"]\s*,?)+)\s*\]/)?.[1]
        .match(/['"]([^'"]+)['"]/g) || []).map((q) => q.slice(1, -1))
    );
    for (const m of comp.members) {
      if (!claimedHere.has(m)) incomplete.push({ page: rel, kind: `class .${m} is part of the ${component} component but is not in documents` });
    }
    for (const st of comp.states) {
      if (!claimedHere.has(st)) incomplete.push({ page: rel, kind: `state .${st} belongs to ${component} but is not in documents` });
    }
    for (const f of comp.files) {
      if (!accounts.has(f)) {
        const n = comp.rules.filter((r) => r.file === f).length;
        incomplete.push({ page: rel, kind: `${f} has ${n} rule(s) affecting .${component} and is not in accounts` });
      }
    }
    for (const f of accounts.keys()) {
      if (!comp.files.includes(f)) incomplete.push({ page: rel, kind: `accounts lists ${f}, which has no rules affecting .${component}` });
    }
  }

  /* ---- The public API freeze ----------------------------------------------
     Every class must be in a bucket, and every public class must appear in
     API.md. A class added to src/ without a decision fails the build, which is
     the whole point: public is something you choose, not something you get by
     forgetting. */
  const unclassified = [];
  const missingFromApi = [];
  const strayInApi = [];
  if (fs.existsSync(BUCKETS)) {
    const bk = JSON.parse(fs.readFileSync(BUCKETS, 'utf8'));
    const decided = new Set(Object.keys(bk.decisions));
    for (const name of real) if (!decided.has(name)) unclassified.push(name);

    const md = fs.existsSync(API_MD) ? fs.readFileSync(API_MD, 'utf8') : '';
    const listed = new Set([...md.matchAll(/^\| `\.([A-Za-z0-9_-]+)` \|/gm)].map((m) => m[1]));
    for (const name of bk.buckets.public) if (!listed.has(name)) missingFromApi.push(name);
    for (const name of listed) {
      if (!real.has(name)) continue;
      if (!bk.buckets.public.includes(name)) strayInApi.push(name);
    }
  }

  /* ---- State attribution -------------------------------------------------- */
  const unattributed = (api.unattributedStates || []).filter((n) => !NOT_STATES.has(n));

  const documented = new Set(claimed.keys());
  const undocumented = [...real].filter((c) => !documented.has(c)).sort();

  if (process.argv.includes('--baseline')) {
    const header =
      '# Classes with no documentation page yet. Generated by\n' +
      '#   node tools/docs/verify.mjs --baseline\n' +
      '#\n' +
      '# This is the backlog, not a permission slip. It may shrink and must never\n' +
      '# grow: a new class must be documented or added here on purpose, and the\n' +
      '# build fails either way until someone decides which.\n#\n' +
      `# ${undocumented.length} classes outstanding.\n\n`;
    fs.writeFileSync(BASELINE, header + undocumented.join('\n') + '\n');
    console.log(`\n  docs: recorded ${undocumented.length} undocumented classes in ${path.relative(ROOT, BASELINE)}\n`);
    return;
  }

  const baseline = readBaseline();
  const newlyUndocumented = undocumented.filter((c) => !baseline.has(c));
  const staleBaseline = [...baseline].filter((c) => !real.has(c)).sort();

  const problems = [];
  if (staleClaims.length) {
    problems.push([
      `${staleClaims.length} docs entr${staleClaims.length === 1 ? 'y' : 'ies'} reference a class that no longer exists in src/`,
      staleClaims.map((s) => `    .${s.name}  claimed by ${s.page}`),
    ]);
  }
  if (unrealUses.length) {
    const uniq = [...new Map(unrealUses.map((u) => [`${u.name}@${u.page}`, u])).values()];
    problems.push([
      `${uniq.length} use${uniq.length === 1 ? '' : 's'} of a class that does not exist in src/`,
      uniq.map((u) => `    .${u.name}  in ${u.page}:${u.line}`),
    ]);
  }
  if (proseGhosts.length) {
    problems.push([
      `${proseGhosts.length} class name${proseGhosts.length === 1 ? '' : 's'} in prose that no longer exist${proseGhosts.length === 1 ? 's' : ''} in src/`,
      proseGhosts.map((g) => `    ${g.name}  in ${g.page}:${g.line}`),
    ]);
  }
  if (newlyUndocumented.length) {
    problems.push([
      `${newlyUndocumented.length} class${newlyUndocumented.length === 1 ? '' : 'es'} exist in src/ with no docs entry and are not in the known backlog`,
      newlyUndocumented.slice(0, 20).map((c) => `    .${c}`)
        .concat(newlyUndocumented.length > 20 ? [`    … and ${newlyUndocumented.length - 20} more`] : []),
      'Document them, or run `node tools/docs/verify.mjs --baseline` to accept them as backlog.',
    ]);
  }
  if (incomplete.length) {
    problems.push([
      `${incomplete.length} component completeness gap${incomplete.length === 1 ? '' : 's'}`,
      incomplete.slice(0, 25).map((i) => `    ${i.page}: ${i.kind}`)
        .concat(incomplete.length > 25 ? [`    … and ${incomplete.length - 25} more`] : []),
    ]);
  }
  if (unattributed.length) {
    problems.push([
      `${unattributed.length} state class${unattributed.length === 1 ? '' : 'es'} could not be attributed to a component`,
      unattributed.map((n) => `    .${n}`),
      'Add it to a component selector, or record it in NOT_STATES with a reason.',
    ]);
  }
  if (unclassified.length) {
    problems.push([
      `${unclassified.length} class${unclassified.length === 1 ? '' : 'es'} in src/ are in no API bucket`,
      unclassified.slice(0, 20).map((c) => `    .${c}`)
        .concat(unclassified.length > 20 ? [`    … and ${unclassified.length - 20} more`] : []),
      'Run `node tools/docs/classify.mjs`, and add a rule for it if none matches.',
    ]);
  }
  if (missingFromApi.length) {
    problems.push([
      `${missingFromApi.length} public class${missingFromApi.length === 1 ? ' is' : 'es are'} missing from API.md`,
      missingFromApi.slice(0, 20).map((c) => `    .${c}`),
      'API.md is generated; run `node tools/docs/classify.mjs`.',
    ]);
  }
  if (strayInApi.length) {
    problems.push([
      `${strayInApi.length} class${strayInApi.length === 1 ? '' : 'es'} in API.md are not in the public bucket`,
      strayInApi.slice(0, 20).map((c) => `    .${c}`),
    ]);
  }
  if (staleBaseline.length) {
    problems.push([
      `${staleBaseline.length} backlog entr${staleBaseline.length === 1 ? 'y is' : 'ies are'} stale — the class is gone from src/`,
      staleBaseline.slice(0, 20).map((c) => `    .${c}`),
      'Run `node tools/docs/verify.mjs --baseline` to re-record.',
    ]);
  }

  const pad = (n) => String(n).padStart(4);
  console.log(`
  docs: verified ${pages.length} authored page${pages.length === 1 ? '' : 's'} against ${real.size} classes

  ${pad(documented.size)} classes documented
  ${pad(undocumented.length)} classes outstanding (${baseline.size} in the recorded backlog)
  ${pad(used.size)} distinct classes used in docs markup and examples
  ${pad(staleClaims.length)} stale claims
  ${pad(new Set(unrealUses.map((u) => u.name)).size)} classes used that do not exist
  ${pad(proseGhosts.length)} class names in prose that do not exist
  ${pad(newlyUndocumented.length)} undocumented and unrecorded
  ${pad(staleBaseline.length)} stale backlog entries
  ${pad(componentPages.length)} component page(s), ${incomplete.length} completeness gap(s)
  ${pad((api.counts?.states) || 0)} state classes, ${unattributed.length} unattributed
  ${pad(unclassified.length)} unclassified, ${missingFromApi.length} missing from API.md, ${strayInApi.length} stray in API.md`);

  if (!problems.length) {
    console.log('\n  No drift.\n');
    return;
  }

  console.error('\n  docs: verification failed\n');
  for (const [title, lines, hint] of problems) {
    console.error(`  ${title}:`);
    for (const l of lines) console.error(l);
    if (hint) console.error(`    ${hint}`);
    console.error('');
  }
  process.exit(1);
}

/* Runs standalone as well as being imported by build.mjs. */
if (process.argv[1]?.endsWith('verify.mjs')) verifyDocs();
