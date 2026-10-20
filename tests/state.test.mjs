// On-state and behaviour tests: dist/deck.css and dist/deck.js in a real
// browser. See layout.test.mjs for how the browser is chosen.
import { after, before, test } from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright-core';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const css = fs.readFileSync(path.join(root, 'dist', 'deck.css'), 'utf8');
const js = fs.readFileSync(path.join(root, 'dist', 'deck.js'), 'utf8');
const paint = ['background-color', 'color', 'border-top-color', 'box-shadow'];

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

async function render(body, { script = false } = {}) {
  await page.setContent(
    `<!doctype html><html lang="en"><head><meta charset="utf-8"><style>${css}</style></head><body>${body}</body></html>`,
  );
  // deck.js contains a literal "</script", so it cannot go inline.
  if (script) {
    await page.addScriptTag({ content: js });
    await page.evaluate(() => { Deck.init(); });
  }
}

const looks = (ids) => page.evaluate(([ids, paint]) => Object.fromEntries(ids.map((id) => {
  const cs = getComputedStyle(document.getElementById(id));
  return [id, paint.map((p) => cs.getPropertyValue(p)).join(' | ')];
})), [ids, paint]);

test('aria-pressed and aria-selected are equal "on" states', async () => {
  await render(`
    <button class="chip" id="chip-pressed" aria-pressed="true">A</button>
    <button class="chip" id="chip-selected" aria-selected="true">A</button>
    <button class="reaction" id="reaction-pressed" aria-pressed="true">1</button>
    <button class="reaction" id="reaction-selected" aria-selected="true">1</button>
    <button class="datepicker-preset" id="preset-pressed" aria-pressed="true">Today</button>
    <button class="datepicker-preset" id="preset-selected" aria-selected="true">Today</button>
    <div class="segmented"><button class="btn btn-ghost" id="seg-pressed" aria-pressed="true">A</button><button class="btn btn-ghost">B</button></div>
    <div class="segmented"><button class="btn btn-ghost" id="seg-selected" aria-selected="true">A</button><button class="btn btn-ghost">B</button></div>
    <div class="segmented"><button class="btn" id="seg-btn-pressed" aria-pressed="true">A</button><button class="btn">B</button></div>
    <div class="segmented"><button class="btn" id="seg-btn-selected" aria-selected="true">A</button><button class="btn">B</button></div>`);
  const l = await looks(['chip-pressed', 'chip-selected', 'reaction-pressed', 'reaction-selected',
    'preset-pressed', 'preset-selected', 'seg-pressed', 'seg-selected', 'seg-btn-pressed', 'seg-btn-selected']);
  for (const name of ['chip', 'reaction', 'preset', 'seg', 'seg-btn']) {
    assert.equal(l[`${name}-pressed`], l[`${name}-selected`], `.${name}: aria-pressed looks like aria-selected`);
  }
});

test('a .btn with aria-pressed="true" looks different from one without, on every variant', async () => {
  const variants = ['', 'btn-primary', 'btn-accent', 'btn-danger', 'btn-soft', 'btn-ghost', 'btn-outline', 'btn-link'];
  await render(variants.map((v, i) => `
    <button class="btn ${v}" id="off-${i}">Bold</button>
    <button class="btn ${v}" id="false-${i}" aria-pressed="false">Bold</button>
    <button class="btn ${v}" id="on-${i}" aria-pressed="true">Bold</button>`).join(''));
  const l = await looks(variants.flatMap((_, i) => [`off-${i}`, `false-${i}`, `on-${i}`]));
  variants.forEach((v, i) => {
    assert.notEqual(l[`on-${i}`], l[`off-${i}`], `.btn ${v}: pressed is visible`);
    assert.equal(l[`false-${i}`], l[`off-${i}`], `.btn ${v}: aria-pressed="false" looks like no attribute`);
  });
});

test('the combobox shows data-placeholder as text, never as markup', async () => {
  const payload = '" onfocus="window.__pwned = 1';
  const attr = payload.replace(/&/g, '&amp;').replace(/'/g, '&#39;');
  await render(`<div class="combo" id="combo" data-deck-combo data-placeholder='${attr}'><select hidden><option>One</option></select></div>`, { script: true });
  await page.focus('#combo .combo-input');
  const m = await page.evaluate(() => {
    const input = document.querySelector('#combo .combo-input');
    return { placeholder: input.placeholder, attrs: input.getAttributeNames(), pwned: window.__pwned ?? null };
  });
  assert.equal(m.placeholder, payload);
  assert.ok(!m.attrs.includes('onfocus'), `no injected attribute: ${m.attrs}`);
  assert.equal(m.pwned, null);
});

test('deck.js moves the on state of a .segmented, whichever attribute it uses', async () => {
  await render(`
    <div class="segmented" id="pressed"><button aria-pressed="true">A</button><button aria-pressed="false">B</button></div>
    <div class="segmented" id="selected"><button role="tab" aria-selected="true">A</button><button role="tab" aria-selected="false">B</button></div>
    <div class="tabs" id="tabs"><button class="tab" aria-selected="true">A</button><button class="btn" id="pin" aria-pressed="false">Pin</button></div>`, { script: true });
  const state = (id) => page.evaluate((id) => [...document.getElementById(id).children]
    .map((b) => b.getAttribute('aria-pressed') ?? b.getAttribute('aria-selected')).join(','), id);
  await page.click('#pressed > :nth-child(2)');
  await page.click('#selected > :nth-child(2)');
  await page.click('#pin');
  assert.equal(await state('pressed'), 'false,true');
  assert.equal(await state('selected'), 'false,true');
  // .tabs is a tablist: a toggle button inside it is not one of its items.
  assert.equal(await state('tabs'), 'true,false');
});
