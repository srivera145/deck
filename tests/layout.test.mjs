// Layout regression tests: Deck's built stylesheet (dist/deck.css), rendered
// in a real browser.
//
//   npm test        builds dist/, then runs these with Node's test runner
//
// They drive a Chromium-based browser that is already installed: Microsoft
// Edge by default. Set DECK_TEST_BROWSER to "chrome", "msedge", or the path
// of a Chromium executable to use another.
import { after, before, test } from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright-core';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const css = fs.readFileSync(path.join(root, 'dist', 'deck.css'), 'utf8');
const shape = '<svg class="icon icon-sm" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 2h20v20H2z"/></svg>';

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
    `<!doctype html><html lang="en"><head><meta charset="utf-8"><style>${css}</style></head><body>${body}</body></html>`,
  );
}

test('a .modal opened with showModal() is centred in the viewport', async () => {
  await render(`
    <dialog class="modal" id="dialog" aria-labelledby="title">
      <div class="modal-header"><h2 class="modal-title" id="title">Title</h2></div>
      <div class="modal-body"><p>Body text.</p></div>
      <div class="modal-footer"><button class="btn">Close</button></div>
    </dialog>`);
  const box = await page.evaluate(async () => {
    const dialog = document.getElementById('dialog');
    dialog.showModal();
    // The open transition (scale and translate) moves the box; measure where it ends.
    await new Promise((resolve) => requestAnimationFrame(resolve));
    await Promise.all(dialog.getAnimations().map((a) => a.finished));
    const r = dialog.getBoundingClientRect();
    return { x: r.x, y: r.y, width: r.width, height: r.height, vw: innerWidth, vh: innerHeight };
  });
  const detail = JSON.stringify(box);
  assert.ok(box.x > 0 && box.y > 0, `not pinned to the top-left corner: ${detail}`);
  assert.ok(Math.abs(box.x + box.width / 2 - box.vw / 2) < 1.5, `centred horizontally: ${detail}`);
  assert.ok(Math.abs(box.y + box.height / 2 - box.vh / 2) < 1.5, `centred vertically: ${detail}`);
});

// A modal much taller than a short window, in both of Deck's markups: the
// regions as the dialog's children, and wrapped in <form method="dialog">
// the way the docs write it.
const tallRegions = `
  <div class="modal-header"><h2 class="modal-title" id="title">Title</h2></div>
  <div class="modal-body">${'<p>Body text.</p>'.repeat(60)}</div>
  <div class="modal-footer"><button class="btn">Done</button></div>`;

for (const [markup, content] of [
  ['regions in the dialog', tallRegions],
  ['regions in a form', `<form method="dialog" class="stack stack-0">${tallRegions}</form>`],
]) {
  test(`a .modal taller than the viewport scrolls its body and keeps its footer (${markup})`, async () => {
    const normal = page.viewportSize();
    await page.setViewportSize({ ...normal, height: 600 });
    try {
      await render(`<dialog class="modal" id="dialog" aria-labelledby="title">${content}</dialog>`);
      const m = await page.evaluate(async () => {
        const dialog = document.getElementById('dialog');
        dialog.showModal();
        await new Promise((resolve) => requestAnimationFrame(resolve));
        await Promise.all(dialog.getAnimations().map((a) => a.finished));
        const box = dialog.getBoundingClientRect();
        const body = dialog.querySelector('.modal-body');
        const footer = dialog.querySelector('.modal-footer').getBoundingClientRect();
        return {
          top: box.top,
          bottom: box.bottom,
          vh: innerHeight,
          scrollHeight: body.scrollHeight,
          clientHeight: body.clientHeight,
          footerTop: footer.top,
          footerBottom: footer.bottom,
        };
      });
      const detail = JSON.stringify(m);
      assert.ok(m.top >= 0 && m.bottom <= m.vh, `the dialog fits in the viewport: ${detail}`);
      assert.ok(m.scrollHeight > m.clientHeight, `the body scrolls: ${detail}`);
      assert.ok(m.footerTop >= m.top && m.footerBottom <= m.bottom + 0.5, `the footer is inside the dialog: ${detail}`);
      assert.ok(m.footerBottom <= m.vh, `the footer is inside the viewport: ${detail}`);
    } finally {
      await page.setViewportSize(normal);
    }
  });
}

test('a closing .modal keeps its regions stacked while it fades out', async () => {
  await render(`
    <dialog class="modal" id="dialog" aria-labelledby="title">
      <div class="modal-header"><h2 class="modal-title" id="title">Title</h2></div>
      <div class="modal-body"><p>Body text.</p></div>
      <div class="modal-footer"><button class="btn">Close</button></div>
    </dialog>`);
  // The close transition only runs with motion allowed; under reduce it lasts .01ms.
  await page.emulateMedia({ reducedMotion: 'no-preference' });
  try {
    const m = await page.evaluate(async () => {
      const dialog = document.getElementById('dialog');
      dialog.showModal();
      await new Promise((resolve) => requestAnimationFrame(resolve));
      await Promise.all(dialog.getAnimations().map((a) => a.finished));
      dialog.close();
      // display transitions with allow-discrete, so the dialog stays rendered
      // (and keeps its open-state display) until the fade-out ends.
      const header = dialog.querySelector('.modal-header').getBoundingClientRect();
      const footer = dialog.querySelector('.modal-footer').getBoundingClientRect();
      return {
        display: getComputedStyle(dialog).display,
        fading: dialog.getAnimations().length > 0,
        headerBottom: header.bottom,
        footerTop: footer.top,
      };
    });
    const detail = JSON.stringify(m);
    assert.ok(m.display !== 'none' && m.fading, `measured during the fade-out: ${detail}`);
    assert.ok(m.footerTop >= m.headerBottom - 1, `the footer is still below the header: ${detail}`);
  } finally {
    await page.emulateMedia({ reducedMotion: 'reduce' });
  }
});

test('an .icon inside running text stays on the line of text', async () => {
  await render(`
    <p id="with" style="inline-size: 40rem">Press the ${shape} star to pin a page.</p>
    <p id="without" style="inline-size: 40rem">Press the star to pin a page.</p>`);
  const m = await page.evaluate(() => {
    const withIcon = document.getElementById('with');
    const withoutIcon = document.getElementById('without');
    const icon = withIcon.querySelector('svg');
    const range = document.createRange();
    range.selectNodeContents(withIcon.firstChild);
    const text = range.getBoundingClientRect();
    const iconBox = icon.getBoundingClientRect();
    return {
      display: getComputedStyle(icon).display,
      withHeight: withIcon.getBoundingClientRect().height,
      withoutHeight: withoutIcon.getBoundingClientRect().height,
      iconTop: iconBox.top,
      iconBottom: iconBox.bottom,
      textTop: text.top,
      textBottom: text.bottom,
    };
  });
  assert.equal(m.display, 'inline-block');
  assert.ok(m.withHeight <= m.withoutHeight + 2, `one line, like the same sentence without the icon: ${JSON.stringify(m)}`);
  assert.ok(m.iconTop >= m.textTop - 4 && m.iconBottom <= m.textBottom + 4, `the icon sits in the text line: ${JSON.stringify(m)}`);
});

test('icons in flex rows (buttons, alerts) are laid out as before', async () => {
  await render(`
    <button class="btn" id="button">${shape} Save</button>
    <div class="alert" id="alert">${shape}<div>Saved.</div></div>`);
  const displays = await page.evaluate(() =>
    ['button', 'alert'].map((id) => getComputedStyle(document.querySelector(`#${id} svg`)).display),
  );
  // Flex items are blockified, so inline-block on .icon makes no difference there.
  assert.deepEqual(displays, ['block', 'block']);
});

test('a .banner-close clears the 44px touch target and keeps its icon size', async () => {
  await render(`
    <div class="banner" id="banner">
      <span>One line of announcement.</span>
      <button class="banner-close" id="close" aria-label="Dismiss">${shape}</button>
    </div>`);
  const m = await page.evaluate(() => {
    const close = document.getElementById('close').getBoundingClientRect();
    const icon = document.querySelector('#close svg').getBoundingClientRect();
    const banner = document.getElementById('banner').getBoundingClientRect();
    const tap = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--tap'));
    const fontSize = parseFloat(getComputedStyle(document.getElementById('close')).fontSize);
    return { w: close.width, h: close.height, iconW: icon.width, iconH: icon.height, bannerH: banner.height, tap, fontSize };
  });
  const detail = JSON.stringify(m);
  assert.ok(m.w >= m.tap && m.h >= m.tap, `the button is at least --tap on both axes: ${detail}`);
  // .icon-sm is 1em; the bigger box must not scale it.
  assert.equal(m.iconW, m.fontSize, `the icon keeps its size: ${detail}`);
  assert.equal(m.iconH, m.fontSize, `the icon keeps its size: ${detail}`);
  assert.ok(m.bannerH < 2 * m.tap, `the button reaches into the padding rather than making the strip taller: ${detail}`);
});

// How far the focused element's outline reaches past each edge of every clipping
// ancestor's padding box. All zeros means no clip cuts the ring.
async function tabAndMeasureRing() {
  await page.keyboard.press('Tab');
  // Reduced motion makes every style change a .01ms transition; read after it settles.
  await page.evaluate(() => new Promise((resolve) => requestAnimationFrame(() => setTimeout(resolve))));
  return page.evaluate(() => {
    const el = document.activeElement;
    const cs = getComputedStyle(el);
    const reach = parseFloat(cs.outlineOffset) + parseFloat(cs.outlineWidth);
    const r = el.getBoundingClientRect();
    const cut = { top: 0, right: 0, bottom: 0, left: 0 };
    for (let a = el.parentElement; a; a = a.parentElement) {
      const s = getComputedStyle(a);
      if (s.overflowX === 'visible' && s.overflowY === 'visible') continue;
      const box = a.getBoundingClientRect();
      cut.top = Math.max(cut.top, box.top + parseFloat(s.borderTopWidth) - (r.top - reach));
      cut.left = Math.max(cut.left, box.left + parseFloat(s.borderLeftWidth) - (r.left - reach));
      cut.right = Math.max(cut.right, r.right + reach - (box.right - parseFloat(s.borderRightWidth)));
      cut.bottom = Math.max(cut.bottom, r.bottom + reach - (box.bottom - parseFloat(s.borderBottomWidth)));
    }
    return { name: el.textContent.trim(), outlineStyle: cs.outlineStyle, cut };
  });
}

test('a focused .list-row keeps its whole focus ring inside the clipping .list', async () => {
  await render(`
    <nav class="list" aria-label="Rows">
      <a class="list-row" href="#a">First</a>
      <a class="list-row" href="#b">Middle</a>
      <a class="list-row" href="#c">Last</a>
    </nav>`);
  for (let i = 0; i < 3; i++) {
    const m = await tabAndMeasureRing();
    assert.equal(m.outlineStyle, 'solid', `${m.name} has a ring`);
    assert.deepEqual(m.cut, { top: 0, right: 0, bottom: 0, left: 0 }, `${m.name}: nothing cut`);
  }
});

test('a .scroller leaves room for a focus ring and rests cards at the gutter', async () => {
  await page.setViewportSize({ width: 375, height: 700 });
  try {
    const cards = [1, 2, 3, 4, 5].map((n) => `<a class="card card-link" href="#${n}" style="inline-size: 140px"><div class="card-body">Card ${n}</div></a>`).join('');
    await render(`<div class="container"><div class="scroller">${cards}</div></div>`);
    const m = await tabAndMeasureRing();
    assert.deepEqual(m.cut, { top: 0, right: 0, bottom: 0, left: 0 }, 'the first card keeps its whole ring');
    const fade = await page.evaluate(() => getComputedStyle(document.querySelector('.scroller')).maskImage);
    assert.equal(fade, 'none', 'the end fade lifts while keyboard focus is in the row');
    const rest = await page.evaluate(() => {
      const scroller = document.querySelector('.scroller');
      const third = scroller.children[2];
      third.scrollIntoView({ block: 'nearest', inline: 'start' });
      const cs = getComputedStyle(scroller);
      return {
        padding: parseFloat(cs.paddingInlineStart),
        scrollPadding: parseFloat(cs.scrollPaddingInlineStart),
        inset: third.getBoundingClientRect().left - scroller.getBoundingClientRect().left,
      };
    });
    assert.equal(rest.scrollPadding, rest.padding, `scroll-padding matches the padding: ${JSON.stringify(rest)}`);
    assert.ok(Math.abs(rest.inset - rest.padding) < 1, `a card scrolled to the start rests at the gutter: ${JSON.stringify(rest)}`);
  } finally {
    await page.setViewportSize({ width: 1280, height: 800 });
  }
});

test('a .card-link responds to keyboard focus on the link inside it', async () => {
  await render(`
    <article class="card card-link">
      <div class="card-body"><h3 class="card-title"><a class="stretch" href="#x">Title</a></h3></div>
    </article>`);
  const read = () => page.evaluate(() => {
    const cs = getComputedStyle(document.querySelector('.card-link'));
    return { shadow: cs.boxShadow, border: cs.borderTopColor };
  });
  const rest = await read();
  await page.keyboard.press('Tab');
  await page.evaluate(() => new Promise((resolve) => requestAnimationFrame(() => setTimeout(resolve))));
  const focused = await read();
  assert.notEqual(focused.shadow, rest.shadow, 'the card lifts');
  assert.notEqual(focused.border, rest.border, 'the border strengthens');
});

test('a .nav-link with an icon is as tall as one without', async () => {
  await render(`
    <div class="navbar-links" style="display: flex">
      <a class="nav-link" href="#1">Text</a>
      <a class="nav-link" href="#2">${shape.replace('icon icon-sm', 'icon')} Icon</a>
      <a class="nav-link" href="#3"><svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 2h20v20H2z"/></svg> Bare svg</a>
    </div>`);
  const heights = await page.evaluate(() =>
    [...document.querySelectorAll('.nav-link')].map((a) => Math.round(a.getBoundingClientRect().height * 100) / 100),
  );
  assert.equal(new Set(heights).size, 1, `one height: ${heights}`);
});

test('a .btn label split at a bare span reads as one run of text', async () => {
  await render(`
    <button class="btn" id="whole">Save changes</button>
    <button class="btn" id="split">Save <span>changes</span></button>
    <button class="btn" id="badge">Inbox <span class="badge">4</span></button>`);
  const m = await page.evaluate(() => {
    const width = (id) => document.getElementById(id).getBoundingClientRect().width;
    const badge = document.querySelector('#badge .badge');
    const range = document.createRange();
    range.selectNodeContents(document.getElementById('badge').firstChild);
    return {
      whole: width('whole'),
      split: width('split'),
      badgeGap: badge.getBoundingClientRect().left - range.getBoundingClientRect().right,
      gap: parseFloat(getComputedStyle(document.getElementById('badge')).columnGap),
    };
  });
  assert.ok(Math.abs(m.split - m.whole) < 0.5, `a word space, not the gap: ${JSON.stringify(m)}`);
  assert.ok(Math.abs(m.badgeGap - m.gap) < 0.5, `a classed span keeps its box and the gap: ${JSON.stringify(m)}`);
});

test('.form-actions-sticky sits above a .tabbar, not under it', async () => {
  await page.setViewportSize({ width: 375, height: 667 });
  try {
    const fields = Array.from({ length: 14 }, (_, i) => `<label class="field"><span class="label">Field ${i}</span><input class="input"></label>`).join('');
    await render(`
      <form class="stack">${fields}
        <div class="form-actions form-actions-sticky"><button class="btn" type="button">Cancel</button><button class="btn btn-primary">Save</button></div>
      </form>
      <nav class="tabbar" aria-label="Primary"><a class="tabbar-item" href="#">${shape}<span>Home</span></a><a class="tabbar-item" href="#">${shape}<span>Me</span></a></nav>`);
    const m = await page.evaluate(() => {
      scrollTo(0, 200);
      const actions = document.querySelector('.form-actions-sticky').getBoundingClientRect();
      const tabbar = document.querySelector('.tabbar').getBoundingClientRect();
      return { actionsTop: actions.top, actionsBottom: actions.bottom, tabbarTop: tabbar.top, vh: innerHeight };
    });
    assert.ok(m.actionsBottom <= m.tabbarTop + 0.5, `the bar clears the tab bar: ${JSON.stringify(m)}`);
    assert.ok(m.actionsTop >= 0 && m.actionsTop < m.vh, `the bar is on screen: ${JSON.stringify(m)}`);
  } finally {
    await page.setViewportSize({ width: 1280, height: 800 });
  }
});

test('the -sm cut of a pointing icon mirrors under dir="rtl", and a transport icon does not', async () => {
  await render(`
    <div dir="rtl">
      <svg class="icon" id="regular"><use href="#chevron-right"></use></svg>
      <svg class="icon icon-sm" id="small"><use href="#chevron-right-sm"></use></svg>
      <svg class="icon" id="new"><use href="#arrow-down-right-sm"></use></svg>
      <svg class="icon" id="play"><use href="#play-sm"></use></svg>
    </div>`);
  const scale = await page.evaluate(() => Object.fromEntries(['regular', 'small', 'new', 'play']
    .map((id) => [id, getComputedStyle(document.getElementById(id)).scale])));
  assert.deepEqual(scale, { regular: '-1 1', small: '-1 1', new: '-1 1', play: 'none' });
});

test('.input-sm and .input-lg are the heights of .btn-sm and .btn-lg', async () => {
  await render(`
    <input class="input input-sm" id="is"><button class="btn btn-sm" id="bs">Go</button>
    <input class="input input-lg" id="il"><button class="btn btn-lg" id="bl">Go</button>
    <select class="select input-sm" id="ss"><option>One</option></select>`);
  const h = await page.evaluate(() => Object.fromEntries(['is', 'bs', 'il', 'bl', 'ss']
    .map((id) => [id, document.getElementById(id).getBoundingClientRect().height])));
  assert.equal(h.is, h.bs, `small: ${JSON.stringify(h)}`);
  assert.equal(h.il, h.bl, `large: ${JSON.stringify(h)}`);
  assert.equal(h.ss, h.bs, `a small select: ${JSON.stringify(h)}`);
  const pad = await page.evaluate(() => getComputedStyle(document.getElementById('ss')).paddingInlineEnd);
  assert.equal(pad, '40px', 'the select keeps room for its arrow');
});

test('.break-words lets a flex item shrink below a long unbroken string', async () => {
  const url = 'https://example.com/films/2026/the-long-unbroken-path-to-a-poster-image.jpg';
  await render(`
    <div class="flex" style="inline-size:240px" id="plain"><span class="shrink-0">Source</span><span>${url}</span></div>
    <div class="flex" style="inline-size:240px" id="broken"><span class="shrink-0">Source</span><span class="break-words">${url}</span></div>`);
  const w = await page.evaluate(() => Object.fromEntries(['plain', 'broken']
    .map((id) => [id, document.getElementById(id).scrollWidth])));
  assert.ok(w.plain > 240, `the body's break-word alone still overflows: ${JSON.stringify(w)}`);
  assert.ok(w.broken <= 240, `.break-words keeps the row inside its box: ${JSON.stringify(w)}`);
});

test('.object-contain beats the cover a .ratio-* frame sets; .list-flush drops the box', async () => {
  await render(`
    <img class="ratio-2x3 object-contain" id="poster" alt="" style="inline-size:120px">
    <div class="list list-flush" id="flush"><div class="list-row">A</div><div class="list-row">B</div></div>`);
  const m = await page.evaluate(() => {
    const img = getComputedStyle(document.getElementById('poster'));
    const list = getComputedStyle(document.getElementById('flush'));
    const row = getComputedStyle(document.querySelector('#flush .list-row + .list-row'));
    return { fit: img.objectFit, ratio: img.aspectRatio, border: list.borderTopWidth, radius: list.borderTopLeftRadius,
      bg: list.backgroundColor, divider: row.borderTopWidth };
  });
  assert.deepEqual(m, { fit: 'contain', ratio: '2 / 3', border: '0px', radius: '0px', bg: 'rgba(0, 0, 0, 0)', divider: '1px' });
});

test('a photo child of .g-scrim sits under the scrim, and its caption above it', async () => {
  await render(`
    <figure class="g-scrim" id="scrim" style="inline-size:200px;margin:0">
      <img src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt="" style="inline-size:200px;block-size:200px">
      <figcaption id="cap">Caption</figcaption>
    </figure>`);
  // Inside the isolated scrim, -2 paints before the ::after at -1, and the
  // caption (positioned, z-index auto) paints after it.
  const z = await page.evaluate(() => ({
    img: getComputedStyle(document.querySelector('#scrim img')).zIndex,
    scrim: getComputedStyle(document.getElementById('scrim'), '::after').zIndex,
    caption: getComputedStyle(document.getElementById('cap')).zIndex,
    captionPosition: getComputedStyle(document.getElementById('cap')).position,
  }));
  assert.deepEqual(z, { img: '-2', scrim: '-1', caption: 'auto', captionPosition: 'relative' });
});

test('.grid-fill keeps a short row card-sized and works without .grid', async () => {
  await render(`
    <div style="inline-size:1000px">
      <div class="grid" style="--min:12rem" id="fit"><div>A</div><div>B</div></div>
      <div class="grid-fill" style="--min:12rem" id="fill"><div>A</div><div>B</div></div>
    </div>`);
  const w = await page.evaluate(() => ['fit', 'fill'].map((id) => ({
    display: getComputedStyle(document.getElementById(id)).display,
    item: document.querySelector(`#${id} > div`).getBoundingClientRect().width,
  })));
  assert.equal(w[1].display, 'grid');
  assert.ok(w[0].item > 450, `.grid stretches two items across the row: ${JSON.stringify(w)}`);
  assert.ok(w[1].item < 250, `.grid-fill keeps them near the 12rem floor: ${JSON.stringify(w)}`);
});

test('.g-scrim-auto follows the theme, and .glass is a translucent blurred surface', async () => {
  await render(`
    <figure class="g-scrim-auto" id="light" style="margin:0"><img alt="" src="data:image/gif;base64,R0lGODlhAQABAAAAACw="><figcaption>Title</figcaption></figure>
    <figure class="g-scrim-auto" id="dark" data-theme="dark" style="margin:0"><img alt="" src="data:image/gif;base64,R0lGODlhAQABAAAAACw="><figcaption>Title</figcaption></figure>
    <article class="card glass" id="glass">Glass</article>`);
  const m = await page.evaluate(() => {
    // Lightness from 0 to 1, whether the browser reports oklch(), oklab() or rgb().
    const lum = (c) => { const n = c.match(/[\d.]+/g).map(Number); return /^okl/.test(c) ? n[0] : (n[0] + n[1] + n[2]) / 765; };
    const g = getComputedStyle(document.getElementById('glass'));
    return {
      light: lum(getComputedStyle(document.querySelector('#light figcaption')).color),
      dark: lum(getComputedStyle(document.querySelector('#dark figcaption')).color),
      img: getComputedStyle(document.querySelector('#light img')).zIndex,
      scrim: getComputedStyle(document.getElementById('light'), '::after').backgroundImage.startsWith('linear-gradient'),
      glassBlur: g.backdropFilter,
      glassBg: g.backgroundColor,
    };
  });
  assert.ok(m.light < 0.4 && m.dark > 0.8, `dark text in a light theme, light text in a dark one: ${JSON.stringify(m)}`);
  assert.equal(m.img, '-2');
  assert.equal(m.scrim, true);
  assert.match(m.glassBlur, /blur\(16px\)/);
  assert.match(m.glassBg, /\/ 0\.76\)|, 0\.76\)/, `the surface at 76%: ${m.glassBg}`);
});

test('.grid-fixed-3 is a grid without .grid, and .split-rail-start gives the first child the rail', async () => {
  await render(`
    <div class="grid-fixed-3" id="fixed" style="inline-size:900px"><div>A</div><div>B</div><div>C</div></div>
    <div class="split split-rail-start" id="split"><aside>Rail</aside><div>Main</div></div>`);
  const m = await page.evaluate(() => {
    const fixed = document.getElementById('fixed');
    const cols = [...fixed.children].map((c) => Math.round(c.getBoundingClientRect().width));
    const [rail, main] = [...document.getElementById('split').children].map((c) => c.getBoundingClientRect());
    return { display: getComputedStyle(fixed).display, gap: getComputedStyle(fixed).columnGap, cols,
      railWidth: Math.round(rail.width), railFirst: rail.left < main.left };
  });
  assert.deepEqual(m, { display: 'grid', gap: '16px', cols: [289, 289, 289], railWidth: 320, railFirst: true });
});

test('a .video-poster shows its play button in the middle of the frame, over the image', async () => {
  await render(`
    <div class="video" id="frame" style="inline-size:480px">
      <button type="button" class="video-poster" aria-label="Play">
        <img src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt="" width="640" height="480">
        <span class="video-play" id="play">${shape}</span>
      </button>
    </div>`);
  const m = await page.evaluate(() => {
    const f = document.getElementById('frame').getBoundingClientRect();
    const p = document.getElementById('play').getBoundingClientRect();
    const i = document.querySelector('#frame img').getBoundingClientRect();
    return { frame: [f.width, f.height], img: [i.width, i.height],
      playCentre: [p.x + p.width / 2 - f.x, p.y + p.height / 2 - f.y] };
  });
  assert.deepEqual(m.img, m.frame, `the image fills the frame: ${JSON.stringify(m)}`);
  assert.deepEqual(m.playCentre, [m.frame[0] / 2, m.frame[1] / 2], `the button is centred: ${JSON.stringify(m)}`);
});

// WCAG contrast between two computed colours, read back through a canvas as sRGB.
const contrastOf = (a, b) => page.evaluate(([a, b]) => {
  const x = document.createElement('canvas').getContext('2d', { willReadFrequently: true });
  const lum = (c) => {
    x.clearRect(0, 0, 1, 1); x.fillStyle = c; x.fillRect(0, 0, 1, 1);
    const ch = [...x.getImageData(0, 0, 1, 1).data.slice(0, 3)].map((v) => (v /= 255) <= 0.04045 ? v / 12.92 : ((v + 0.055) / 1.055) ** 2.4);
    return 0.2126 * ch[0] + 0.7152 * ch[1] + 0.0722 * ch[2];
  };
  const [hi, lo] = [lum(a), lum(b)].sort((p, q) => q - p);
  return (hi + 0.05) / (lo + 0.05);
}, [a, b]);

test('.btn-primary fills with --brand, so an exact brand colour reaches it in both schemes', async () => {
  const markup = (scheme) => `
    <div data-theme="${scheme}">
      <button class="btn btn-primary" id="${scheme}">Save</button>
      <span id="${scheme}-brand" style="background: var(--brand)"></span>
      <span id="${scheme}-600" style="background: var(--brand-600)"></span>
    </div>`;
  const read = () => page.evaluate(() => Object.fromEntries(['light', 'dark'].map((s) => {
    const bg = (id) => getComputedStyle(document.getElementById(id)).backgroundColor;
    return [s, { bg: bg(s), text: getComputedStyle(document.getElementById(s)).color, brand: bg(s + '-brand'), step600: bg(s + '-600') }];
  })));

  await render(markup('light') + markup('dark'));
  const plain = await read();
  assert.equal(plain.light.bg, plain.light.step600, 'light mode is unchanged: the 600 step');
  assert.equal(plain.dark.bg, plain.dark.brand, 'dark mode follows --brand, the lighter step');
  for (const s of ['light', 'dark']) {
    assert.ok(await contrastOf(plain[s].text, plain[s].bg) >= 4.5, `${s}: text clears 4.5:1 on the fill ${JSON.stringify(plain[s])}`);
  }

  await render(`<style>@layer app.base { :root { --brand: #0b5cff; --brand-hover: #0847cc; } }</style>` + markup('light') + markup('dark'));
  const exact = await read();
  for (const s of ['light', 'dark']) {
    assert.equal(exact[s].bg, 'rgb(11, 92, 255)', `${s}: the :root --brand override reaches the button`);
    assert.ok(await contrastOf(exact[s].text, exact[s].bg) >= 4.5, `${s}: text clears 4.5:1 on the override ${JSON.stringify(exact[s])}`);
  }
});

test('[data-theme-scope] works the brand out from its own hue; a bare hue reaches only the chart', async () => {
  const region = (id, attrs) => `
    <section id="${id}" ${attrs}>
      <button class="btn btn-primary">Save</button> <a href="#x">Link</a>
      <div class="chart"><div class="chart-bars"><div class="chart-bar s1"><span class="chart-bar-track"><span class="chart-bar-fill" style="--value:50"></span></span></div></div></div>
    </section>`;
  await render(`<div data-theme="light">
    ${region('page', '')}
    ${region('rust', 'data-theme-scope style="--hue-brand: 24"')}
    ${region('violet', 'data-theme-scope style="--hue-brand: 265"')}
    ${region('bare', 'style="--hue-brand: 24"')}
    <section data-theme-scope style="--hue-brand: 265">${region('nested', 'data-theme-scope style="--hue-brand: 24"')}</section>
  </div>`);
  const r = await page.evaluate(() => Object.fromEntries(['page', 'rust', 'violet', 'bare', 'nested'].map((id) => {
    const q = (s) => getComputedStyle(document.querySelector(`#${id} ${s}`));
    return [id, { btn: q('.btn').backgroundColor, link: q('a').color, s1: q('.chart-bar-fill').backgroundColor }];
  })));
  const detail = JSON.stringify(r);
  assert.equal(r.page.btn, 'oklch(0.516 0.118 196)', `the page keeps its hue: ${detail}`);
  for (const [id, hue] of [['rust', 24], ['violet', 265], ['nested', 24]]) {
    assert.equal(r[id].btn, `oklch(0.516 0.118 ${hue})`, `${id}: the button takes the region's hue: ${detail}`);
    assert.equal(r[id].link, r[id].btn, `${id}: so does the link: ${detail}`);
    assert.equal(r[id].s1, `oklch(0.608 0.118 ${hue})`, `${id}: chart series 1 matches the region: ${detail}`);
  }
  assert.equal(r.bare.btn, r.page.btn, `without the attribute the button keeps the page's colour: ${detail}`);
  assert.equal(r.bare.s1, 'oklch(0.608 0.118 24)', `series 1 reads the hue directly, like series 2 to 6: ${detail}`);
});

test('a :root --brand override stops at a scope, and reaches inside when set on the scope', async () => {
  await render(`<style>@layer app.base { :root { --brand: #0b5cff; } }</style>
    <div data-theme="light">
      <button class="btn btn-primary" id="root">Page</button>
      <section data-theme-scope style="--hue-brand: 24"><button class="btn btn-primary" id="scoped">Scope</button></section>
      <section data-theme-scope style="--hue-brand: 24; --brand: #ff5a1f"><button class="btn btn-primary" id="own">Own</button></section>
    </div>`);
  const bg = (id) => page.evaluate((id) => getComputedStyle(document.getElementById(id)).backgroundColor, id);
  assert.equal(await bg('root'), 'rgb(11, 92, 255)');
  assert.equal(await bg('scoped'), 'oklch(0.516 0.118 24)');
  assert.equal(await bg('own'), 'rgb(255, 90, 31)');
});

test('data-theme on a wrapper gives its text the wrapper\'s ink, so a dark card on a light page is readable', async () => {
  await render(`
    <div data-theme="light">
      <section data-theme="dark"><div class="card" id="card"><div class="card-body"><p id="text">Card text</p></div></div></section>
    </div>`);
  const { text, bg } = await page.evaluate(() => ({
    text: getComputedStyle(document.getElementById('text')).color,
    bg: getComputedStyle(document.getElementById('card')).backgroundColor,
  }));
  const ratio = await contrastOf(text, bg);
  assert.ok(ratio >= 4.5, `text ${text} on ${bg} is ${ratio.toFixed(2)}:1`);
});
