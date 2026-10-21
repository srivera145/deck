// Findability tests for the documentation site: the search, and the
// generated component index.
//
//   npm test        builds dist/, then runs these with Node's test runner
//
// They serve public_html/ with PHP's built-in server, so they need `php` on
// PATH (or DECK_TEST_PHP set to its path) and are skipped without it.
//
// What they pin is the failure that produced false gap reports: a date picker,
// sparklines, donuts and rings that were all documented, and a search and a
// docs home that could not lead anyone to them.
import { after, before, test } from 'node:test';
import assert from 'node:assert/strict';
import { spawn, spawnSync } from 'node:child_process';
import fs from 'node:fs';
import net from 'node:net';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const php = process.env.DECK_TEST_PHP ?? 'php';
const skip = spawnSync(php, ['-v']).status === 0 ? false : `${php} is not runnable`;

let server;
let base;

before(async () => {
  if (skip) return;
  const port = await new Promise((resolve) => {
    const probe = net.createServer().listen(0, '127.0.0.1', () => {
      const { port } = probe.address();
      probe.close(() => resolve(port));
    });
  });
  server = spawn(php, ['-S', `127.0.0.1:${port}`, '-t', path.join(root, 'public_html')], { stdio: 'ignore' });
  base = `http://127.0.0.1:${port}/docs/`;
  for (let i = 0; i < 50; i++) {
    try {
      if ((await fetch(base + 'index.php')).ok) return;
    } catch {}
    await new Promise((r) => setTimeout(r, 100));
  }
  throw new Error('the PHP server did not start');
});

after(() => {
  server?.kill();
});

const get = async (rel, init) => {
  const res = await fetch(new URL(rel, base), init);
  return { res, text: await res.text() };
};

/* Every term from the brief, the question it came from, and two names whose
   page is called something else. [query, page, the id it should land on]. */
const NAMED = [
  ['date picker', 'components/datepicker.php', null],
  ['does Deck have a date picker?', 'components/datepicker.php', null],
  ['donut', 'components/charts.php', 't-donut'],
  ['sparkline', 'components/charts.php', 't-spark'],
  ['ring', 'components/progress.php', 'ring'],
  ['timeline', 'components/timeline.php', null],
  ['upload', 'components/file.php', null],
  ['kanban', 'components/kanban.php', null],
  ['QR', 'components/qr.php', null],
  ['emoji', 'components/emoji.php', null],
  ['heatmap', 'components/charts.php', 't-heat'],
  ['btn-primary', 'components/button.php', 'class-btn-primary'],
];

test('the navbar search opens the page each name is on', { skip }, async () => {
  for (const [q, page, id] of NAMED) {
    const { res } = await get(`search.php?go=1&q=${encodeURIComponent(q)}`, { redirect: 'manual' });
    assert.equal(res.status, 302, `"${q}" should go straight to a page`);
    const to = new URL(res.headers.get('location'), new URL('search.php', base));
    assert.equal(to.pathname, `/docs/${page}`, `"${q}"`);
    assert.equal(to.hash, id ? `#${id}` : '', `"${q}" should land at ${id ?? 'the top'}`);
    if (id) {
      const { text } = await get(to.href);
      assert.match(text, new RegExp(`id="${id}"`), `${page} has no #${id} for "${q}" to land on`);
    }
  }
});

test('a page the search opened says so and links to every result', { skip }, async () => {
  const { text } = await get('components/charts.php?found=donut');
  assert.match(text, /Best match for “donut”/);
  assert.match(text, /href="\.\.\/search\.php\?q=donut"/);
  assert.match(text, /id="dx-q"[^>]*value="donut"/);
});

test('without go=1 the search lists pages first, best first, and classes link', { skip }, async () => {
  const { res, text } = await get('search.php?q=grid');
  assert.equal(res.status, 200);
  const pages = text.slice(text.indexOf('id="dx-pages"'), text.indexOf('id="dx-count"'));
  const first = pages.match(/<th scope="row" data-label="Page">\s*<a href="([^"]+)"/);
  assert.equal(first?.[1], 'components/grid.php');
  assert.match(pages, /href="components\/datagrid\.php"/);

  // The exact class heads the table, ahead of every comment with "ring"
  // inside a word, and says which page it is on.
  const ring = await get('search.php?q=ring');
  const table = ring.text.slice(ring.text.indexOf('id="dx-table"'));
  assert.equal(table.match(/data-label="Class"><code>([^<]+)<\/code>/)?.[1], '.ring');
  assert.match(table, /data-label="Documented in"><a href="components\/progress\.php">Progress<\/a>/);
});

test('every public class is in the component index, and in its JSON', { skip }, async () => {
  const buckets = JSON.parse(fs.readFileSync(path.join(root, 'dist', 'api-buckets.json'), 'utf8'));
  const pub = new Set(buckets.buckets.public);

  const json = JSON.parse((await get('components/index.php?format=json')).text);
  const listed = new Set([
    ...json.pages.flatMap((p) => p.classes),
    ...json.unclaimed.flatMap((u) => u.classes),
  ]);
  assert.deepEqual([...pub].filter((n) => !listed.has(n)), [], 'public classes missing from the JSON');
  assert.deepEqual([...listed].filter((n) => !pub.has(n)), [], 'non-public classes in the JSON');
  assert.equal(json.counts.public, pub.size);

  const { text } = await get('components/index.php');
  const shown = new Set([...text.matchAll(/<code>\.([A-Za-z0-9_-]+)<\/code>/g)].map((m) => m[1]));
  assert.deepEqual([...pub].filter((n) => !shown.has(n)), [], 'public classes missing from the page');
});

test('every link in the component index lands on something', { skip }, async () => {
  const { text } = await get('components/index.php');
  const targets = new Map();
  for (const [, file, id] of text.matchAll(/<a href="(\.\.\/[^"#]+\.php)(?:#([^"]+))?"/g)) {
    if (!targets.has(file)) targets.set(file, new Set());
    if (id) targets.get(file).add(id);
  }
  assert.ok(targets.size > 60, `only ${targets.size} pages linked`);
  const missing = [];
  for (const [file, ids] of targets) {
    const { res, text: page } = await get(new URL(file, new URL('components/index.php', base)).href);
    assert.equal(res.status, 200, file);
    for (const id of ids) if (!page.includes(`id="${id}"`)) missing.push(`${file}#${id}`);
  }
  assert.deepEqual(missing, []);
});

test('the index filter keeps the rows a name or a purpose points at', { skip }, async () => {
  const visible = (html) => [...html.matchAll(/<tr id="([^"]+)" data-terms="[^"]*">/g)].map((m) => m[1]);
  // Whichever family has no page today, so this does not fail when one gets one.
  const stray = JSON.parse((await get('components/index.php?format=json')).text).unclaimed[0];
  const cases = [
    ['date picker', 'components-datepicker'],
    ['upload', 'components-file'],
    ['donut', 'components-charts'],
    ...(stray ? [[stray.classes[0], stray.url.split('#')[1]]] : []),
  ];
  for (const [q, row] of cases) {
    const rows = visible((await get(`components/index.php?q=${encodeURIComponent(q)}`)).text);
    assert.ok(rows.includes(row), `"${q}" hid ${row}; kept ${rows.join(', ')}`);
  }
});
