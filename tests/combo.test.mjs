// Combobox tests: dist/deck.css and dist/deck.js in a real browser. A long
// list opens fast and stays reachable from the keyboard, the selection can be
// read and set from script, a remote selection survives new queries, and the
// list lands next to its field. See layout.test.mjs for how the browser is
// chosen.
import { after, before, test } from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright-core';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const css = fs.readFileSync(path.join(root, 'dist', 'deck.css'), 'utf8');
const js = fs.readFileSync(path.join(root, 'dist', 'deck.js'), 'utf8');

let browser;
let page;

before(async () => {
  const choice = process.env.DECK_TEST_BROWSER ?? 'msedge';
  browser = /[\\/]/.test(choice)
    ? await chromium.launch({ executablePath: choice })
    : await chromium.launch({ channel: choice });
  page = await browser.newPage({ viewport: { width: 1280, height: 800 } });
  await page.emulateMedia({ reducedMotion: 'reduce' });
});

after(async () => {
  await browser?.close();
});

async function render(body) {
  await page.setContent(
    `<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><style>${css}</style></head><body style="margin:0;padding:16px">${body}</body></html>`,
  );
  // deck.js contains a literal "</script", so it cannot go inline.
  await page.addScriptTag({ content: js });
}

// Groups of 500, and a second line on two options in three, so rows differ in height.
const options = (n) => Array.from({ length: n }, (_, i) =>
  `<option value="v${i}" data-group="Group ${Math.floor(i / 500)}"${i % 3 ? ` data-sub="Second line ${i}"` : ''}>Option number ${i}</option>`).join('');

// Where the keyboard is, as a screen reader would be told it.
const active = (id) => page.evaluate((id) => {
  const input = document.querySelector(`#${id} .combo-input`);
  const list = document.querySelector(`#${id} .combo-list`);
  const ref = input.getAttribute('aria-activedescendant');
  const node = ref && document.getElementById(ref);
  const l = list.getBoundingClientRect();
  const r = node?.getBoundingClientRect();
  return {
    ref,
    inList: !!node && list.contains(node),
    role: node?.getAttribute('role'),
    pos: Number(node?.getAttribute('aria-posinset')),
    size: Number(node?.getAttribute('aria-setsize')),
    text: node?.querySelector('.combo-option-main > span')?.textContent,
    visible: !!r && r.top >= l.top - 0.5 && r.bottom <= l.bottom + 0.5,
    drawn: list.querySelectorAll('.combo-option').length,
    controls: input.getAttribute('aria-controls') === list.id,
  };
}, id);

test('10,000 options open in well under 200ms, drawing only the rows in view', async () => {
  await render(`<div style="max-inline-size:26rem"><div class="combo" id="c" data-deck-combo data-multi><select hidden multiple>${options(10000)}</select></div></div>`);
  await page.evaluate(() => Deck.init());
  const ms = await page.evaluate(() => new Promise((resolve) => {
    const t0 = performance.now();
    document.querySelector('#c .combo-control').dispatchEvent(new MouseEvent('mousedown', { bubbles: true, cancelable: true }));
    requestAnimationFrame(() => setTimeout(() => resolve(performance.now() - t0)));
  }));
  const drawn = await page.locator('#c .combo-option').count();
  assert.ok(ms < 200, `opened in ${ms.toFixed(1)}ms`);
  assert.ok(drawn > 0 && drawn < 60, `${drawn} options in the DOM`);
});

test('arrow keys reach the end of 10,000 options, and the reader is told where they are', async () => {
  await render(`<div style="max-inline-size:26rem"><div class="combo" id="c" data-deck-combo data-multi><select hidden multiple>${options(10000)}</select></div></div>`);
  await page.evaluate(() => Deck.init());
  await page.focus('#c .combo-input');

  await page.keyboard.press('ArrowDown');
  let a = await active('c');
  assert.deepEqual([a.pos, a.size, a.text, a.role, a.inList, a.visible, a.controls],
    [1, 10000, 'Option number 0', 'option', true, true, true], JSON.stringify(a));

  // From the first option, up wraps to the last.
  await page.keyboard.press('ArrowUp');
  a = await active('c');
  assert.deepEqual([a.pos, a.text, a.inList, a.visible], [10000, 'Option number 9999', true, true], JSON.stringify(a));

  // Walk the whole list. Synthetic events, because 10,000 real presses take
  // a minute; each one runs the same handler a key press does.
  await page.keyboard.press('ArrowDown');
  const seen = await page.evaluate(() => {
    const input = document.querySelector('#c .combo-input');
    const missing = [];
    for (let i = 1; i < 10000; i++) {
      input.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowDown', bubbles: true, cancelable: true }));
      if (input.getAttribute('aria-activedescendant') !== `${document.querySelector('#c .combo-list').id}-${i}`) missing.push(i);
    }
    return missing;
  });
  assert.deepEqual(seen.slice(0, 5), [], 'every option became the active descendant on the way down');
  // Real presses for the last stretch, with frames and scroll events between them.
  await page.keyboard.press('ArrowUp');
  await page.keyboard.press('ArrowDown');
  a = await active('c');
  assert.deepEqual([a.pos, a.text, a.inList, a.visible], [10000, 'Option number 9999', true, true], JSON.stringify(a));
  assert.ok(a.drawn < 60, `${a.drawn} options in the DOM at the end`);

  await page.keyboard.press('PageUp');
  a = await active('c');
  assert.ok(a.pos < 10000 && a.visible, `PageUp moves up a page: ${JSON.stringify(a)}`);
  await page.keyboard.press('Enter');
  assert.deepEqual(await page.evaluate(() => document.querySelector('#c')._deck.value()), [`v${a.pos - 1}`]);

  // Typing still filters, and the count is of the matches.
  await page.keyboard.type('number 9999');
  a = await active('c');
  assert.deepEqual([a.pos, a.size, a.text], [1, 1, 'Option number 9999'], JSON.stringify(a));
});

test('scrolling with the mouse draws the rows in view, and never leaves the reader pointed at a missing row', async () => {
  await render(`<div style="max-inline-size:26rem"><div class="combo" id="c" data-deck-combo><select hidden>${options(10000)}</select></div></div>`);
  await page.evaluate(() => Deck.init());
  await page.focus('#c .combo-input');
  await page.keyboard.press('ArrowDown');
  // Not 0.5: halfway is option 5000, where a group heading starts.
  for (const where of [0.37, 1]) {
    const m = await page.evaluate(async (where) => {
      const list = document.querySelector('#c .combo-list');
      list.scrollTop = (list.scrollHeight - list.clientHeight) * where;
      await new Promise((resolve) => requestAnimationFrame(() => setTimeout(resolve)));
      const l = list.getBoundingClientRect();
      const hit = document.elementFromPoint((l.left + l.right) / 2, (l.top + l.bottom) / 2)?.closest('.combo-option');
      const ref = document.querySelector('#c .combo-input').getAttribute('aria-activedescendant');
      return { hit: hit ? Number(hit.dataset.index) : null, dangling: !!ref && !document.getElementById(ref), last: !!list.querySelector('[data-index="9999"]') };
    }, where);
    assert.ok(m.hit !== null, `an option under the middle of the list at ${where}: ${JSON.stringify(m)}`);
    assert.ok(!m.dangling, 'aria-activedescendant is dropped rather than left naming a row that is not drawn');
    if (where < 1) assert.ok(m.hit > 3200 && m.hit < 4200, `37% of the way down shows option 3700 or so: ${m.hit}`);
    if (where === 1) assert.ok(m.last, 'the bottom shows the last option');
  }
});

test('el._deck.value() and clear() read and set the selection and keep the select in step', async () => {
  await render(`
    <form id="f">
      <div class="combo" id="one" data-deck-combo>
        <select hidden name="one"><option value="">Choose</option><option value="a">Alpha</option><option value="b">Beta</option></select>
      </div>
      <div class="combo" id="many" data-deck-combo data-multi>
        <select hidden multiple name="many"><option value="a" selected>Alpha</option><option value="b">Beta</option><option value="c">Gamma</option></select>
      </div>
      <div class="combo" id="tags" data-deck-combo data-multi data-create>
        <select hidden multiple name="tags"><option value="x">X</option></select>
      </div>
    </form>`);
  const r = await page.evaluate(() => {
    Deck.init();
    const one = document.getElementById('one');
    const many = document.getElementById('many');
    const tags = document.getElementById('tags');
    const events = [];
    for (const el of [one, many, tags]) el.addEventListener('deck:change', (e) => events.push([el.id, e.detail.values]));
    const posted = (name) => new FormData(document.getElementById('f')).getAll(name);
    const input = (el) => el.querySelector('.combo-input').value;
    const tokens = (el) => [...el.querySelectorAll('.combo-token > span')].map((s) => s.textContent);
    return {
      oneStart: one._deck.value(),
      oneSet: [one._deck.value('b'), input(one), posted('one')],
      oneUnknown: [one._deck.value('nope'), posted('one')],
      oneAgain: one._deck.value('a'),
      oneClear: [one._deck.clear(), input(one), posted('one')],
      manyStart: many._deck.value(),
      manySet: [many._deck.value(['b', 'c']), tokens(many), posted('many')],
      manyOne: many._deck.value('a'),
      manyClear: [many._deck.clear(), tokens(many), posted('many')],
      silent: [many._deck.value(['c'], { silent: true }), events.filter(([id]) => id === 'many').length],
      created: [tags._deck.value(['x', { value: 'new', label: 'Brand new' }]), tokens(tags), posted('tags')],
      events,
    };
  });
  assert.equal(r.oneStart, '');
  assert.deepEqual(r.oneSet, ['b', 'Beta', ['b']]);
  assert.deepEqual(r.oneUnknown, ['', []], 'a value the list does not have selects nothing, as on a native select');
  assert.equal(r.oneAgain, 'a');
  assert.deepEqual(r.oneClear, ['', '', []]);
  assert.deepEqual(r.manyStart, ['a']);
  assert.deepEqual(r.manySet, [['b', 'c'], ['Beta', 'Gamma'], ['b', 'c']]);
  assert.deepEqual(r.manyOne, ['a'], 'value() replaces rather than adds');
  assert.deepEqual(r.manyClear, [[], [], []]);
  assert.deepEqual(r.silent, [['c'], 3], '{ silent: true } fires nothing');
  assert.deepEqual(r.created, [['x', 'new'], ['X', 'Brand new'], ['x', 'new']], 'data-create takes a value the list does not have');
  assert.deepEqual(r.events.slice(0, 4), [['one', ['b']], ['one', []], ['one', ['a']], ['one', []]]);
});

test('a remote multi-select keeps every choice across three queries, and posts them', async () => {
  await render(`
    <form id="f"><div style="max-inline-size:26rem">
      <div class="combo" id="r" data-deck-combo data-multi data-url="/people?q=" data-debounce="0">
        <select hidden multiple name="people"></select>
      </div>
    </div></form>`);
  await page.evaluate(() => {
    const people = {
      ad: [[1, 'Ada Lovelace'], [2, 'Adele Goldberg']],
      gr: [[3, 'Grace Hopper'], [2, 'Adele Goldberg']],
      ka: [[4, 'Katherine Johnson'], [5, 'Karen Spärck Jones']],
    };
    window.fetch = async (url) => ({
      json: async () => (people[decodeURIComponent(url.split('q=')[1])] ?? []).map(([id, name]) => ({ id, name })),
    });
    Deck.init();
  });
  const pick = async (q, id) => {
    await page.fill('#r .combo-input', q);
    await page.click(`#r .combo-option[data-value="${id}"]`);
  };
  await pick('ad', 1);
  await pick('gr', 3);
  await pick('ka', 4);
  const state = () => page.evaluate(() => ({
    value: document.getElementById('r')._deck.value(),
    tokens: [...document.querySelectorAll('#r .combo-token > span')].map((s) => s.textContent),
    posted: new FormData(document.getElementById('f')).getAll('people'),
  }));
  assert.deepEqual(await state(), {
    value: ['1', '3', '4'],
    tokens: ['Ada Lovelace', 'Grace Hopper', 'Katherine Johnson'],
    posted: ['1', '3', '4'],
  });

  // Back to the first query: its choice shows as chosen.
  await page.fill('#r .combo-input', 'ad');
  await page.waitForSelector('#r .combo-option[data-value="1"]');
  assert.equal(await page.getAttribute('#r .combo-option[data-value="1"]', 'aria-selected'), 'true');

  // A token whose option is not in the current results can still be removed.
  await page.click('#r .combo-token button[data-value="3"]');
  assert.deepEqual((await state()).posted, ['1', '4']);
});

test('a slow reply to an old query does not replace the results of a newer one', async () => {
  await render(`<div class="combo" id="r" data-deck-combo data-url="/q?q=" data-debounce="0"><select hidden name="q"></select></div>`);
  await page.evaluate(() => {
    window.fetch = (url) => {
      const q = decodeURIComponent(url.split('q=')[1]);
      return new Promise((resolve) => setTimeout(() => resolve({ json: async () => [{ id: q, name: `Result for ${q}` }] }), q === 'old' ? 300 : 10));
    };
    Deck.init();
  });
  await page.fill('#r .combo-input', 'old');
  await page.waitForTimeout(20);
  await page.fill('#r .combo-input', 'new');
  await page.waitForTimeout(500);
  assert.deepEqual(await page.locator('#r .combo-option').allTextContents().then((t) => t.map((s) => s.trim())), ['Result for new']);
});

// The list opens next to its field, the same width, clear of it and fully on
// screen: inside a .card on a phone, where contain: layout used to pin it to
// the card and overflow: clip cut it off, and on a desktop page with no card,
// where anchor positioning used to put it over the field.
for (const [label, viewport, wrap] of [
  ['inside a .card at 375px', { width: 375, height: 667 }, (c) => `<div class="card"><div class="card-body stack stack-3">${c}</div></div>`],
  ['on a desktop page', { width: 1280, height: 800 }, (c) => c],
]) {
  test(`the list opens aligned to its field and fully on screen, ${label}`, async () => {
    const normal = page.viewportSize();
    await page.setViewportSize(viewport);
    try {
      for (const space of [0, Math.round(viewport.height * 0.45), viewport.height - 160]) {
        const field = (id) => `<div class="combo" id="${id}" data-deck-combo><select hidden>${options(200)}</select></div>`;
        await render(`<div style="block-size:${space}px"></div>${wrap(field('a') + field('b'))}<div style="block-size:2000px"></div>`);
        await page.evaluate(() => Deck.init());
        for (const id of ['a', 'b']) {
          await page.click(`#${id} .combo-input`);
          const m = await page.evaluate((id) => {
            const list = document.querySelector(`#${id} .combo-list`);
            const l = list.getBoundingClientRect();
            const c = document.querySelector(`#${id} .combo-control`).getBoundingClientRect();
            const hit = document.elementFromPoint((l.left + l.right) / 2, (l.top + l.bottom) / 2);
            return {
              aligned: Math.abs(l.left - c.left) < 1 && Math.abs(l.right - c.right) < 1,
              clear: l.top >= c.bottom - 0.5 || l.bottom <= c.top + 0.5,
              onScreen: l.top >= 0 && l.bottom <= innerHeight + 0.5 && l.left >= 0 && l.right <= innerWidth + 0.5,
              reachable: !!hit && list.contains(hit),
              list: [l.top, l.bottom, l.left, l.right].map(Math.round),
              control: [c.top, c.bottom, c.left, c.right].map(Math.round),
            };
          }, id);
          const detail = `#${id} after ${space}px: ${JSON.stringify(m)}`;
          assert.ok(m.aligned, `same edges as the field: ${detail}`);
          assert.ok(m.clear, `does not cover the field: ${detail}`);
          assert.ok(m.onScreen, `fully on screen: ${detail}`);
          assert.ok(m.reachable, `not clipped or painted over: ${detail}`);
          await page.keyboard.press('Escape');
        }
      }
    } finally {
      await page.setViewportSize(normal);
    }
  });
}
