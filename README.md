# Deck

**Deck is a CSS framework that ships as one 26.7 KB Brotli (32.7 KB gzip) stylesheet: buttons, forms,
tables, a data grid, charts, overlays, an icon sprite, and a full color system. You add it
with one `<link>` tag. There is no build step, no config file, and zero dependencies.**

```html
<link rel="stylesheet" href="/assets/deck/deck.css">
```

That is the whole install. Nothing to compile, nothing to purge, nothing to configure.

- **No build step.** The file you download is the file the browser reads.
- **Retheme from one number.** Set `--hue-brand` to 0–360 and every brand color, focus
  ring, badge, chart series, and shadow is recomputed — at runtime, no rebuild.
- **Your CSS wins.** Deck ships in cascade layers, so an ordinary unlayered rule
  overrides it without a single `!important`.
- **RTL built in.** Written in logical properties end to end; `dir="rtl"` flips the
  whole page with no second stylesheet.
- **Zero runtime dependencies.** The JavaScript is optional and dependency-free.

Version 0.1.0 · MIT · Chrome 117+, Edge 117+, Safari 17.4+, Firefox 128+

## What Deck weighs

A page that loads the stylesheet, the icon sprite, and the optional JavaScript transfers **62.7 KB** Brotli, or **76.3 KB** gzip. Every browser Deck supports sends `br` in
`Accept-Encoding`, and Cloudflare, Vercel, Netlify, and nginx with `ngx_brotli` negotiate
it for text by default, so Brotli is what most users actually receive.

| File | Brotli | gzip |
| --- | --- | --- |
| `deck.min.css` | **26.7 KB** | 32.7 KB |
| `deck-icons.svg` | **27.1 KB** | 33.6 KB |
| `deck.min.js` | **8.9 KB** | 10.0 KB |
| **All three** | **62.7 KB** | **76.3 KB** |

The sprite is the largest single file, slightly bigger than the stylesheet — worth stating
plainly rather than leaving you to find it in devtools. Swapping `deck.min.js` for the full
`deck.bundle.min.js` (adds the date picker, combobox, data grid, toasts, QR encoder)
makes the JavaScript 17.3 KB and the total 71.1 KB Brotli (85.8 KB gzip).

### The sprite is a manifest, not a fixed cost

The 75 icons are a default so the demo works out of the box, not a floor. `tools/icons/icons.txt`
lists what to extract — delete the lines you do not need and rebuild:

```bash
# keep only the icons you use
$ cat > tools/icons/icons.txt <<'EOF'
check    = check
search   = search
settings = settings
@hand deck-mark
@hand deck-wordmark
EOF

$ npm run icons
```

**A twelve-icon sprite measures 5.7 KB Brotli (6.9 KB gzip)** — generated and measured, not
estimated. Against 27.1 KB for the full set, trimming the manifest is the difference between the
sprite dominating page weight and disappearing into it.

This matters because an external sprite is all-or-nothing per request: the browser fetches
the whole file to resolve a single `<use>`, so an unused icon is not free the way an unused
CSS class is. That is why the manifest exists. Keep the two `@hand` lines — they carry the
brand marks through from the previous sprite, and dropping them drops the marks.

### Everything in the package

| File | Brotli | gzip | What it is |
| --- | --- | --- | --- |
| `deck.min.css` | 26.7 KB | 32.7 KB | The whole framework |
| `deck.min.js` | 8.9 KB | 10.0 KB | Optional behaviour, no dependencies |
| `deck-extras.min.js` | 5.7 KB | 6.5 KB | Date picker, combobox, data grid, toasts, QR encoder |
| `deck-adapters.min.js` | 4.0 KB | 4.5 KB | Optional library integrations, inert unless one is loaded |
| `deck.bundle.min.js` | 17.3 KB | 19.5 KB | All three scripts in one file |
| `deck-icons.svg` | 27.1 KB | 33.6 KB | 152 symbols: 75 icons at two weights, plus two brand marks |
| `src/` | — | — | The 26 source stylesheets, concatenated to build `deck.css` |
| `dist/layers/` | — | — | One file per layer, if you only want part of Deck |
| `php/` | — | — | Optional PHP helper for Composer users |
| `bin/deck.mjs` | — | — | The `npx @echodial/deck` CLI |

Every size above is what `npm run build` prints, in decimal KB, and is written into this
file by the build rather than typed.

The component demo is `public_html/index.php` — every component on one page. Run it with
`npm run demo && npm start`.

## Repository layout

```
deck/
├─ src/                    everything hand-written
│  ├─ 00-layers.css …      26 stylesheets, concatenated in filename order
│  ├─ deck-icons.svg       the sprite — GENERATED, see `npm run icons`
│  ├─ brand/               the logo: one master, the rest derived from it
│  └─ js/                  deck.js, deck-extras.js, deck-adapters.js
├─ dist/                   entirely generated — safe to delete, `npm run build` rebuilds it
│  ├─ deck.css / .min.css
│  ├─ deck.js / -extras / -adapters, plus .min.js of each
│  ├─ deck.bundle.js / .min.js, deck.esm.js
│  ├─ deck-icons.svg
│  ├─ brand/               copy of src/brand/
│  └─ layers/              one file per layer, for partial adoption
├─ php/                    Deck.php and Installer.php (PSR-4: EchoDial\Deck\)
├─ bin/deck.mjs            the `npx @echodial/deck` CLI
├─ public_html/            the Helm docroot — the demo site, not part of the package
│  ├─ index.php            component demo
│  ├─ php-helper.php       the PHP helper, demonstrated
│  └─ assets/               published copies, both gitignored
│     ├─ deck/              dist/
│     └─ images/            dist/brand/
├─ build.mjs
├─ tools/
│  ├─ make-brand.mjs      regenerates the logo family from the master
│  └─ icons/              the icon toolchain: `npm run icons`
│     ├─ icons.txt        the list of icons to extract
│     ├─ build-icons.mjs  writes src/deck-icons.svg from the font
│     └─ Material_Symbols_Rounded/   build time source, never shipped
├─ package.json            npm; `files` ships src, dist, bin, build.mjs
├─ .gitattributes         `export-ignore` keeps tools/ out of source archives
├─ composer.json           Packagist; PSR-4 points at php/
└─ LICENSE
```

Two rules keep this straight:

**`src/` is written, `dist/` is generated.** Never edit anything in `dist/` — the next
build overwrites it. `npm run clean && npm run build` should always reproduce it exactly.

**`dist/` is committed anyway.** Composer has no build step; Packagist just ships the
repository, so the built files have to be in it. That is the one place where the usual
"never commit build output" rule does not apply.

`public_html/` is the demo site for local development under Helm. It is not part of
either package — npm ships `files`, Composer ships `php/` and `dist/`. Its asset folder
is a published copy, so it is gitignored; run `npm run demo` after a clone to fill it.

### Commands

```bash
npm run build     # src/ -> dist/
npm run demo      # build, then publish dist/ into public_html/assets/
npm run brand     # regenerate the logo family after changing the mark
npm run icons     # regenerate src/deck-icons.svg from tools/icons/icons.txt
npm run clean     # delete dist/
npm start         # php -S localhost:4321 -t public_html
```

## Install

Deck ships three ways. Pick whichever matches how the project already works.

### 1. Just the files

Download `deck.css` and `deck-icons.svg`, drop them next to your other assets, and add
one line. No package manager, no build step, no Node on the server.

```html
<link rel="stylesheet" href="/assets/deck.css">
```

Or let the CLI put them there for you — this does not install anything permanently:

```bash
npx @echodial/deck init public/assets/deck
npx @echodial/deck starter public/index.html   # a working page to start from
```

### 2. npm

```bash
npm install @echodial/deck
```

```js
import '@echodial/deck/css';
import Deck from '@echodial/deck';
```

Subpath exports, so you can take only what you need:

| Import | What it is |
|---|---|
| `@echodial/deck/css` | the whole stylesheet |
| `@echodial/deck/css/min` | minified |
| `@echodial/deck/icons` | the sprite |
| `@echodial/deck/js` | core behaviour |
| `@echodial/deck/extras` | datepicker, combobox, grid, toasts, QR |
| `@echodial/deck/adapters` | optional library integrations |
| `@echodial/deck/bundle` | all three in one file |
| `@echodial/deck/layers/tokens.css` | one layer at a time |
| `@echodial/deck/src/*` | the unconcatenated sources |

The `layers/` exports matter if you only want part of Deck. `layers/tokens.css` plus
`layers/reset.css` gives you the design system with none of the components, which is a
reasonable way to adopt it into an existing app one screen at a time.

### 3. CDN

Publishing to npm makes the CDNs work with no extra step:

```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@echodial/deck@0.1/dist/deck.min.css">
<script src="https://cdn.jsdelivr.net/npm/@echodial/deck@0.1/dist/deck.bundle.min.js" defer></script>
```

### 4. Composer

```bash
composer require echodial/deck
```

Assets cannot be served out of `vendor/`, so the package publishes them into your public
directory on install. Configure the destination in your own `composer.json`:

```json
{
  "extra": {
    "deck": {
      "publish-to": "public/assets/deck",
      "auto-publish": true
    }
  }
}
```

Or run it whenever you like:

```bash
composer deck-publish
composer deck-publish -- public/static/deck
composer deck-publish -- --link          # symlink during development
```

Publishing skips files that have not changed, so a redeploy does not churn mtimes and
invalidate every cache-busting URL for nothing.

#### The PHP helper

Optional, framework-agnostic, and about two hundred lines. No container, no service
provider, no facade — it works in Keel, Laravel, Symfony, WordPress, or a single
`index.php`.

```php
use EchoDial\Deck\Deck;

Deck::configure([
    'base'     => '/assets/deck',
    'adapters' => true,
    'bundle'   => true,
]);
```

```php
<html <?= Deck::htmlAttributes(lang: 'en') ?>>
<head>
  <?= Deck::head() ?>
</head>
```

`Deck::head()` emits the viewport meta tag Deck's mobile-first layout assumes, the
stylesheet, the scripts in the right order, and the icon sprite path — with `?v=` cache
busting from the file mtime, so a deploy invalidates the browser cache and nothing else
does.

```php
<?= Deck::icon('check-circle', 'icon icon-lg') ?>
<?= Deck::css() ?>
<?= Deck::js() ?>
```

Per-tenant theming, which is the thing Tailwind needs a rebuild for:

```php
<html <?= Deck::theme(hue: $tenant->brand_hue, mode: $user->theme) ?>>
```

One inline style. No second stylesheet, no rebuild, no per-customer asset pipeline.

### Building from source

```bash
node build.mjs
```

The build script requires nothing. If esbuild happens to be installed it is used for
minification because it is better at it; otherwise a conservative built-in minifier runs
that walks the file character by character so strings, `url()` values, and data URIs are
never touched. The build never depends on a toolchain being present, which is the same
promise the framework makes.


## Theming

Every color in the framework derives from six hue numbers. Change one line and the
buttons, links, focus rings, badges, tab bar, and shadows all follow:

```css
:root {
  --hue-brand: 265;   /* violet instead of harbor teal */
  --chroma-brand: .14; /* more saturated */
}
```

Per-tenant theming in a multi-tenant Keel app becomes a single inline style on
`<html>` — no rebuild, no separate stylesheet per customer.

Dark mode is automatic from the OS. To force it, set `data-theme="dark"` or
`data-theme="light"` on `<html>`.

## Layers

Deck declares its cascade layers up front:

```
deck.reset, deck.tokens, deck.type, deck.layout, deck.components, deck.mobile, deck.utilities
```

Any CSS you write outside a layer beats all of them, so you override Deck by writing
a normal rule. No `!important`, no specificity arms race.

## Icons

```html
<svg class="icon"><use href="/assets/deck-icons.svg#check"></use></svg>
```

Icons inherit `color` and scale with `font-size`, so they sit on the text baseline.
Sizes: `.icon-sm` `.icon` `.icon-lg` `.icon-xl`.

Set: check, check-double, x, plus, minus, chevron-down/up/left/right, arrow-right,
arrow-left, arrow-up-right, search, menu, more-horizontal, more-vertical, filter,
sort, refresh, home, grid, list, chart, trend-up, trend-down, user, users, settings,
log-out, bell, mail, phone, message, calendar, clock, file, folder, clipboard,
download, upload, trash, edit, copy, link, external, tag, image, camera, eye,
eye-off, lock, unlock, shield, star, heart, bookmark, info, alert-circle,
alert-triangle, check-circle, x-circle, help, credit-card, dollar, receipt, car,
truck, wrench, gauge, sun, moon, map-pin, send, sparkle.

### Two cuts, not one stroke width

Every icon ships twice:

| Symbol | Weight | Use with |
| --- | --- | --- |
| `#check` | wght 400 | `.icon`, `.icon-lg`, `.icon-xl` |
| `#check-sm` | wght 500 | `.icon-sm` |

```html
<svg class="icon icon-sm"><use href="/assets/deck-icons.svg#check-sm"></use></svg>
```

The symbols are filled outlines, so `stroke-width` does nothing to them and `.icon-sm`
cannot thicken its way to legibility at 16px. The heavier cut is a real second drawing
instead. CSS cannot rewrite a `<use href>`, so the pairing has to live in the markup —
`.icon-sm` goes with the `-sm` symbol. Everything else takes the plain name.

Both cuts carry `fill="currentColor" stroke="none"`, which beats the `.icon` stroke
defaults by inheritance, so icons follow `color` and retune with `--hue-brand` exactly
as they always did.

### Regenerating the sprite

`src/deck-icons.svg` is **generated**. Editing it by hand loses the edit on the next run.

```
npm run icons
```

That reads `tools/icons/icons.txt` — one `sprite-id = material-glyph-name` per line —
out of the Material Symbols Rounded variable font and writes both cuts of every listed
icon. To add, drop or swap an icon, edit that list and rerun. Any of the 4,025 glyphs in
the font is available; only the names on the list end up in the sprite, which is how a
4,025 icon library ships as a 75 icon file — or a twelve icon one at 5.7 KB Brotli, if
that is all your project uses.

The font is a **build time source only**. It never reaches a browser: a webfont would be
a 15 MB download or a subsetting step, and Deck's whole premise is not having a build
step. It lives in `tools/`, which is excluded from the npm package (`files` in
`package.json`) and from GitHub source archives (`export-ignore` in `.gitattributes`).
Nobody installing Deck needs it — the generated sprite is committed.

For the same reason `npm run icons` is deliberately **not** part of `npm run build`. It
is a maintainer task, run on purpose:

```
npm run icons     # regenerate src/deck-icons.svg from the font
npm run build     # everything else, needs no font
```

If the font is missing, the script says so and stops. Download Material Symbols Rounded
from [fonts.google.com/icons](https://fonts.google.com/icons) and unzip it into
`tools/icons/`.

`#deck-mark` and `#deck-wordmark` are not in the font. They are Deck's own drawings,
marked `@hand` in the list, read out of the existing sprite and carried through verbatim;
the script fails rather than regenerate a sprite without them.

### Attribution

The icon outlines are derived from **Material Symbols** by Google, used under the
[Apache License, Version 2.0](https://www.apache.org/licenses/LICENSE-2.0). The full
licence text is kept at `tools/icons/LICENSE.txt` and the notice is reproduced in the
header comment of `src/deck-icons.svg`, which is the file that actually ships — keep it
there. The two brand marks are not derived from the font and are not covered by it.

Deck's own code and stylesheets remain MIT, per `LICENSE`.

## Logo

<picture>
  <source media="(prefers-color-scheme: dark)" srcset="src/brand/deck-logo-dark.svg">
  <img alt="Deck" src="src/brand/deck-logo-light.svg" width="300">
</picture>

The mark is three planks in perspective — a deck of cards, a deck of layers, the thing
the framework is named after. It is drawn on the same 24×24 grid as the icons and it
lives in the same sprite.

### Which copy to use

This is the one rule that matters, and it is easy to get wrong:

| You want | Use | Because |
| --- | --- | --- |
| A logo that follows `--hue-brand` | `<use href="deck-icons.svg#deck-mark">` | A `<use>` against a sprite in the same document inherits `color` |
| A logo in an `<img>`, a `<link>`, or `og:image` | a file from `src/brand/` | An externally referenced SVG has no colour context, so `currentColor` resolves to black |

```html
<!-- retunes with the palette -->
<svg class="icon icon-lg icon-fill" style="color: var(--brand)">
  <use href="/assets/deck-icons.svg#deck-mark"></use>
</svg>

<!-- static, for a favicon or a social card -->
<link rel="icon" href="/assets/images/deck-mark.svg" type="image/svg+xml">
```

Note `.icon-fill`. The brand marks are filled, not stroked, so the `.icon` stroke
defaults draw them as hollow outlines without it. `#deck-wordmark` is the lettering on
its own, at `0 0 91.81 24`, for when you are setting the lockup yourself.

### The favicon is a different drawing

`deck-mark.svg` is the mark alone, never the lockup — a 124×24 lockup is a smear at
16px. It carries its own `prefers-color-scheme` block, so it follows the browser chrome
instead of picking a side and disappearing in the other one.

### Family

Two files are drawn by hand; `tools/make-brand.mjs` derives the rest from them.

| File | Drawn or derived | What it is |
| --- | --- | --- |
| `deck-logo.svg` | drawn | Horizontal lockup, `currentColor`, `0 0 123.81 24` |
| `deck-logo-stacked.svg` | drawn | Mark over wordmark, `currentColor`, `0 0 91.81 54` |
| `deck-logo-light.svg` | from the lockup | Explicit dark fill, for light backgrounds |
| `deck-logo-dark.svg` | from the lockup | Explicit light fill, for dark backgrounds |
| `deck-mark.svg` | from `#deck-mark` | Mark only, theme-aware, for `rel="icon"` |
| `deck-og.png` | from the lockup | 1200×630 social card, white on the brand teal |
| `deck-apple-touch-icon.png` | from `#deck-mark` | 180×180, for `rel="apple-touch-icon"` |

`npm run demo` publishes all of them to `public_html/assets/images/`, which is
gitignored for the same reason `assets/deck/` is: it is a copy, not a source.

### Regenerating

```bash
npm run brand     # after changing the mark
```

The two PNGs cannot be produced in-process — rasterising needs a renderer — so the
generator shells out to headless Chrome or Edge via its `--screenshot` flag, and the
results are committed. Deck itself stays dependency-free; the browser is only needed
when the drawing changes, not to build or use the framework.

Because those rasters are committed, they can go stale. `tools/make-brand.mjs` records
a hash of every master it read in `src/brand/sources.json`, and `build.mjs` recomputes
them and **fails the build** if one has moved:

```
Brand assets are stale. These masters have changed since
tools/make-brand.mjs last ran:

  src/brand/deck-logo.svg  recorded b44a710d0269aada, now a81ac26bc87ec010

Run: node tools/make-brand.mjs
```

Hashes rather than timestamps, because a fresh clone gives every file the same mtime.

## Emoji

`.emoji` pins the emoji font stack and the baseline so they render consistently on
Windows, iOS, and Android. Also `.emoji-lg`, `.emoji-xl`, `.emoji-hero`,
`.emoji-tile`, `.emoji-grid`, `.reaction`.

## Layout primitives

`.container` `.stack` `.cluster` `.bar` `.grid` `.split` `.center` `.section`
`.scroller` `.sticky-top` `.app-shell` `.cq`

`.grid` auto-fits by content width, so most layouts need no breakpoints at all.

## Components

Buttons, forms (input, textarea, select, check, radio, switch, range, file, input
group, search, fieldset), card, panel, badge, chip, alert, avatar, table (restacks
below 640px), list rows, tabs, segmented control, accordion, breadcrumb, pagination,
progress, ring, spinner, skeleton, tooltip, menu, modal, bottom sheet, toast, empty
state, stat, timeline, navbar, sidebar, tab bar, FAB.

## Mobile specifics

- Every interactive control clears a 44px touch target
- Inputs render at 16px on coarse pointers, so iOS never zooms on focus
- `env(safe-area-inset-*)` handled on the tab bar, FAB, sticky form bar, and sheets
- `100dvh` instead of `100vh`, so the URL bar doesn't cut off the last row
- Bottom sheet on a phone becomes a centered dialog at 640px and up

## Browser support

Chrome/Edge 117+, Safari 17.4+, Firefox 128+. Deck uses `oklch()`, `light-dark()`,
`@layer`, `:has()`, `@starting-style`, popover, and `field-sizing`. Older browsers
still get a usable page — they lose the entry animations and auto-growing textareas,
not the layout.


## Date picker

```html
<div class="datefield" data-deck-datepicker data-mode="range" data-months="2" data-presets>
  <input class="input" name="period">
</div>
```

Attributes: `data-mode="single|range"`, `data-format="mdy|dmy|iso"`, `data-min`,
`data-max` (ISO dates), `data-months`, `data-week-start`, `data-presets`.

Fires `deck:change` on the input with `{ start, end }` as ISO strings. Under 480px
the panel becomes a bottom sheet.

## Combobox

```html
<div class="combo" data-deck-combo data-multi data-create data-placeholder="Add people">
  <select name="assignees[]" multiple hidden>
    <option value="rissa" selected>Rissa Molina</option>
    <option value="ken" data-sub="Fixed ops" data-group="Managers">Ken Spence</option>
  </select>
</div>
```

The real `<select>` stays in the DOM and stays in sync, so a normal PHP form post
works with nothing extra on the server. `data-sub` adds a second line, `data-group`
groups options, `data-create` allows adding new values, `data-multi` gives tokens.

For a remote source, set `data-url="/api/repos?q="` — deck.js appends the query,
debounces (`data-debounce`, default 220ms), and expects JSON rows of
`{ value, label, sub, group, disabled }`. Use `data-min-chars` to hold off until
the user has typed enough.

Events: `deck:change` with `{ values }`, `deck:create` with `{ value }`.

## Data grid

```html
<div class="dg-wrap" data-deck-grid style="--dg-height:360px">
  <table class="dg dg-zebra">
    <thead><tr>
      <th class="dg-check dg-pin-start">…</th>
      <th class="dg-pin-start-2" data-sort="text" data-resize>Order</th>
      <th class="dg-num" data-sort="num">Total</th>
      <th class="dg-actions dg-pin-end"></th>
    </tr></thead>
```

- `dg-pin-start` / `dg-pin-start-2` / `dg-pin-end` freeze columns. The drop shadow
  only appears once the grid is actually scrolled sideways.
- `data-sort="text|num|date"` makes a header sortable. Put `data-value` on a cell
  when the display text isn't sortable (formatted currency, relative dates).
- `data-resize` adds a drag grip to a column.
- `dg-compact` / `dg-comfy` change row density; `dg-zebra` adds striping.
- `tfoot` sticks to the bottom for totals.
- `dg-cards` plus `data-label` on each `td` restacks the grid into cards below 44rem.

Events: `deck:sort`, `deck:select`.

## Toasts

```js
Deck.toast('Deploy succeeded');

Deck.toast({
  kind: 'warn',            // good | warn | bad | info | loading | ''
  title: 'Project archived',
  text: 'You can undo this.',
  duration: 8000,          // 0 keeps it until dismissed
  actions: [{ label: 'Undo', onClick: () => restore() }]
});

const t = Deck.toast({ kind: 'loading', title: 'Submitting…', duration: 0 });
t.update({ kind: 'good', title: 'Submitted', duration: 4000 });
t.dismiss();

Deck.toasts.clear();
```

Toasts stack rather than stringing down the screen. Hovering the stack fans it out
and pauses every timer. Drag or swipe one sideways to dismiss. Position the region
with `.toast-region-start`, `.toast-region-center`, or `.toast-region-top`.

## Charts

Bars and donuts are CSS driven by `--value` (0–100). Lines are inline SVG you style
with classes. Series colors `s1`–`s6` are derived from `--hue-brand`, so charts
retheme with everything else.

```html
<div class="chart-columns">
  <div class="chart-col s1" style="--value:73" data-label="Jun" data-value="146"></div>
</div>

<div class="chart-bar">
  <span class="chart-bar-label">Cooler line</span>
  <span class="chart-bar-track"><span class="chart-bar-fill s1" style="--value:92"></span></span>
  <span class="chart-bar-value">92</span>
</div>

<div class="donut" style="--stops: var(--c1) 0 62%, var(--c3) 62% 84%, var(--c4) 84% 100%"></div>

<svg class="chart-svg" viewBox="0 0 300 120" preserveAspectRatio="none">
  <path class="chart-area s1" d="…"/>
  <path class="chart-line s1" d="…"/>
</svg>
```

Also: `.chart-col-stack`, `.chart-group`, `.chart-meter`, `.chart-heat`,
`.sparkline`, `.chart-legend`, `.chart-x`, `.chart-y`, `.chart-gridline`.

## Print

Printing is handled in the `deck.print` layer. Nav, tab bar, buttons, toasts,
pickers, and menus drop out. The grid unfreezes and prints every column with the
header repeated on each page. Mobile card fallbacks revert to real tables. Dark mode
is forced back to light. External link targets are printed in parentheses.

Helpers: `.page-break`, `.page-break-after`, `.keep-together`, `.no-print`,
`.print-only`, `.print-keep` (for a button you do want on paper), `.no-print-url`
(suppress the printed href), `.print-header`, `.print-footer`.

Change the paper size in one place:

```css
@page { size: A4; margin: 18mm 15mm; }
```

## JS API

```js
Deck.init(container)   // wire up anything with data-deck-* inside container
Deck.toast(opts)       // returns { update, dismiss }
Deck.toasts.clear()
Deck.theme('dark')     // 'light' | 'dark', persisted to localStorage
Deck.theme()           // read current
Deck.hue(265)          // retint the whole app at runtime
Deck.iconSprite        // path to deck-icons.svg
```


## Motion

Nothing animates unless you ask for it by class. Everything is wrapped in
`prefers-reduced-motion: no-preference`, with one deliberate exception: spinners,
skeletons, and progress bars keep moving under reduced motion, just slower. A frozen
spinner reads as broken, and progress feedback is information rather than decoration.

### Transition utilities

`.transition` `.transition-colors` `.transition-move` `.transition-size`
`.transition-opacity`, sized with `.dur-1` through `.dur-5`, timed with `.ease-out`
`.ease-in` `.ease-spring` `.ease-bounce` `.ease-overshoot` `.ease-linear`, offset with
`.delay-1` `.delay-2` `.delay-3`. `.no-motion` opts a single element out.

The bounce and overshoot easings are `linear()` springs, so you get a real spring
curve with no physics library.

### Entrances

`.enter` `.enter-rise` `.enter-drop` `.enter-start` `.enter-end` `.enter-pop`
`.enter-blur`. Put `.stagger` on the parent and children sequence in; the first twelve
are pure CSS and deck.js sets the index past that. `--stagger-step` controls the gap,
`--travel` controls how far things move.

### Scroll reveals

```html
<div class="card reveal">…</div>
<div class="scroll-progress"></div>
```

`.reveal` `.reveal-fade` `.reveal-pop` use `animation-timeline: view()`, so the
animation is tied to scroll position with no IntersectionObserver at all. deck.js adds
an observer fallback for browsers that don't support it yet. `.scroll-progress` is a
reading-progress bar driven by `scroll(root block)`, and `.shrink-on-scroll` condenses
a sticky header past 120px.

### Attention

`.shake` `.flash` `.flash-good` `.pulse` `.ping` `.nudge`. A `.field.is-invalid`
shakes once on its own and won't repeat, and deck.js clears the state as soon as the
input becomes valid.

### Micro-interactions

`.lift` `.press` `.sweep` (underline draws in), `.icon-follow` (arrow steps forward
when its button is hovered), and `.ripple` — add the class and deck.js handles the ink
from the pointer position.

### Expand and collapse

```js
Deck.toggle(panel);
```

A real `height: auto` transition using `interpolate-size`. No measuring in JavaScript,
no `max-height` guess that clips long content.

### View transitions

This is the one that matters most for Keel. Add this to your app CSS:

```css
@view-transition { navigation: auto; }
```

Full page loads in a plain PHP multi-page app now cross-fade like a single page app —
no router, no JavaScript, no client-side rendering. Deck styles what the browser
generates: content moves, and the header and tab bar hold still.

Give the same `view-transition-name` to matching elements on both pages and the
browser tweens between them — a row in a list morphing into a detail page header:

```html
<!-- list page -->  <tr style="view-transition-name: order-1042">
<!-- detail page --> <h1 style="view-transition-name: order-1042">
```

Helpers: `.vt-header` `.vt-main` `.vt-tabbar`, and `.vt-hold` with `--vt` for a
dynamic name. Back navigations slide the other way; deck.js sets the direction on
popstate.

For same-page DOM changes, wrap the update:

```js
Deck.transition(() => row.remove());
Deck.transition(() => list.prepend(newRow), { direction: 'back' });
```

It falls back to running the change immediately where unsupported or where the person
asked for reduced motion.

### Ticker

`.marquee` with two identical `.marquee-track` children scrolls a status strip —
recalls, backordered parts, campaign notices. It pauses on hover and the duration is
`--marquee-dur`.

### JS additions

```js
Deck.play(node, 'shake')     // one-shot class, cleans up after itself, returns a promise
Deck.toggle(node, force)     // height:auto expand/collapse
Deck.transition(fn, opts)    // view-transition wrapper with fallback
Deck.reduced()               // true when the person asked for reduced motion
```

Mark a number with `data-deck-tick` and it animates up green or down red whenever its
text changes.


## Cascade layers

The whole cascade contract lives in `00-layers.css`, declared before any rule exists.
Order is decided there — not by file order, not by specificity, never by `!important`.

```css
@layer
  deck.reset, deck.tokens, deck.type, deck.layout,
  deck.components, deck.mobile, deck.motion, deck.effects,
  deck.utilities, deck.rtl, deck.print,

  app.base, app.components, app.pages, app.overrides;
```

Four `app.*` layers are reserved and left empty for you. A rule in `app.pages` beats
every Deck rule with a single class selector — no `.page .card .btn` chains, no
escalation. Anything you write **outside** a layer beats all layers, so a one-off rule
in a Keel view template always wins.

Wrap vendor CSS so it stops fighting you:

```css
@import url("vendor/thing.css") layer(vendor);
```

`00-layers.css` also registers the typed custom properties (`@property`) that make
angles, colors, and lengths interpolable — that's what lets a gradient angle or a tilt
animate at all. Registered: `--g-angle`, `--g-from`, `--g-to`, `--g-stop`, `--sheen`,
`--tilt-x`, `--tilt-y`, `--depth`.

## Container queries

Deck already used `container-type` for the `.cq` helper; this is the full set.

```html
<div class="cq">
  <article class="card card-flex">…</article>
</div>
```

The same markup goes horizontal in a wide column and stays stacked in a narrow rail,
without either one knowing where it was placed.

- **Declaring:** `.cq`, `.cq-size`, and named containers `.cq-panel` `.cq-pane`
  `.cq-row` `.cq-shell`.
- **Container units:** `.text-cq` `.display-cq` `.pad-cq` `.gap-cq` scale with `cqi`,
  so a heading in a sidebar stays small on a 32-inch monitor.
- **Adaptive components:** `.card-flex` `.stat-cq` `.metarow` `.field-row-cq`
  `.actions-cq` `.dg-cq`.
- **Breakpoint utilities:** `.cq-sm\:row` `.cq-md\:hidden` `.cq-lg\:grid-2` and so on.

`.field-row-cq` and `.dg-cq` are strictly better than their media-query versions: a
two-up field row or a data grid inside a modal or sheet is narrow no matter how wide
the screen is.

**Style queries.** Set `--tone` on a container and children adapt with no extra classes:

```html
<div class="cq-tone" style="--tone: critical">
  <div class="card tone-surface"><span class="tone-text">Out of coverage</span></div>
</div>
```

Tones: `clear`, `caution`, `critical`. Where style queries aren't supported the
fallback is simply no change.

## Logical properties and RTL

Deck is written in logical properties end to end — `inline-size`, `block-size`,
`inset-inline-start`, `padding-block`, `border-start-start-radius`. A full right-to-left
flip needs nothing but `dir="rtl"` on `<html>`:

```js
Deck.dir('rtl');
```

What logical properties can't fix by themselves is content, so `19-logical.css` handles
the rest in the `deck.rtl` layer: pointing icons mirror (chevrons, arrows, send, log-out)
while checkmarks, wrenches, and clocks don't; the select arrow, search icon, switch knob,
grid pin shadows, chart fills, marquee, and entrance animations all flip; and the
breadcrumb separator swaps.

- **Explicit direction:** `.dir-ltr` `.dir-rtl` `.bidi-isolate` `.bidi-plaintext`.
  `.code-ltr`, `.mono`, `code`, and `.nums` are isolated by default — an identifier like
  a commit hash or an order number reads left to right in every language and must not
  scramble the text around it. Reach for `.code-ltr` when you need to force the direction
  on something that is not already monospace.
- **Mirroring control:** `.mirror-rtl` to mirror, `.no-flip` to never mirror.
- **Logical utilities:** `.mis-*` `.mie-*` `.pis-*` `.pie-*` `.bis` `.bie`
  `.inset-is-0` `.r-start` `.r-end`. For the block axis reach for `.mt-*` and `.mb-*`,
  which are named after the physical edge but declare `margin-block-start` and
  `margin-block-end`, and for full sizes `.w-full` and `.h-full`, which declare
  `inline-size` and `block-size`. Deck used to ship logically-named spellings of those
  as well; two names for one declaration is worse than one name that needs a sentence.
- **Writing modes:** `.writing-vertical` `.writing-upright` `.writing-sideways`, and
  `.th-vertical` for a rotated column header that still measures correctly.

## Gradients

Every gradient derives from `--hue-brand` and interpolates in oklab, which avoids the
grey dead zone sRGB produces when blending two saturated colors. The hue slider retunes
all of them.

- **Surfaces:** `.g-surface` `.g-sunken` `.g-brand` `.g-brand-soft` `.g-dark`
- **Mesh:** `.g-mesh` `.g-mesh-subtle` `.g-mesh-drift` — three soft radial blooms, no
  image, no SVG filter, and no `blur()` over a large area, which is expensive.
- **Text:** `.g-text` `.g-text-shine` — clipped to the glyphs with a real color
  underneath so the text survives if the clip fails.
- **Borders:** `.g-border` `.g-border-soft` `.g-border-spin` — two clip boxes rather
  than `border-image`, so it works with any `border-radius`. The spin animates because
  `--g-angle` is registered.
- **Scrims:** `.g-scrim` `.g-scrim-top` — an eased floor under a caption, instead of a
  flat overlay that dulls the whole image.
- **Fade masks:** `.g-fade-inline` `.g-fade-end` `.g-fade-block` `.g-fade-more` — for
  content that runs off an edge or is collapsed.
- **Sheen:** `.g-sheen` — a highlight sweeping on hover, driven by the registered
  `--sheen` percentage so it eases instead of jumping.
- **Patterns:** `.g-grid-lines` `.g-dots` `.g-stripes` `.g-hatch` — gradients standing
  in for images, so they cost nothing to download and retint automatically.
- **Status and accents:** `.g-good` `.g-warn` `.g-bad` `.g-conic` `.g-ring`
  `.g-ring-spin` `.g-shimmer` `.g-bar-fill`

`.chart-area-g` expects an SVG gradient def with `id="deck-area-gradient"` in the
document. Engines without `oklch()` fall back to the flat brand color rather than a
broken gradient.

## 3D transforms

Depth when it carries meaning: a card with two sides, a stack-depth you're working down
through, a control that physically depresses.

Everything uses `rotate`, `translate`, and `scale` as **individual properties** rather
than the `transform` shorthand, so two effects on one element compose instead of
overwriting each other.

- **Scene:** `.scene` `.scene-near` `.scene-far` set the vanishing point; `.space`
  applies `preserve-3d`.
- **Flip:** `.flip` / `.flip-x` with `.flip-front` and `.flip-back` stacked in one grid
  cell, so the card is exactly as tall as its taller side. `data-deck-flip` on a button
  wires it up and marks the hidden face `inert` so it's off the keyboard path.
- **Tilt:** `.tilt` with `data-tilt="10"`. deck.js writes a single rotation about a
  computed axis. `.tilt-lift` floats content above the face on Z.
- **Depth stack:** `.pile` for a stack-depth of records, `.is-fanned` to spread it,
  `Deck.advance(stack)` to dismiss the top card.
- **Coverflow:** `.coverflow` — scroll snap does the mechanics, 3D only does the read.
- **Cube:** `.cube` with six faces and `data-face="front|back|start|end|top|bottom"`,
  or `.cube-spin`.
- **Depressible:** `.btn-3d` — the face moves down into its own shadow.
- **Parallax:** `.parallax` with `.parallax-back` `.parallax-mid` `.parallax-front` —
  true Z-depth parallax on the compositor, no scroll handler and no jank.
- **Page turn:** `.turn-out` / `.turn-in`, pairs with `Deck.transition()`.

Under reduced motion, flips still flip (the state change is the information) but the
tilt, cube, and coverflow rotations are dropped.

### JS additions

```js
Deck.dir('rtl')        // read or set direction, persisted
Deck.flip(card, true)  // flip a card
Deck.advance(stack)    // dismiss the top card of a depth stack
Deck.face(cube, 'top') // rotate a cube to a face
```


## Extended components

`deck-extras.js` is optional and loads after `deck.js`. Everything below has CSS that
works without it; the script adds behaviour.

### Carousel

```html
<div class="carousel carousel-peek" data-deck-carousel data-autoplay="6000">
  <button class="carousel-arrow carousel-prev">…</button>
  <div class="carousel-track">
    <div class="carousel-slide">…</div>
  </div>
  <button class="carousel-arrow carousel-next">…</button>
  <div class="carousel-dots"></div>
</div>
```

Scroll snap does the work, so it swipes correctly with JavaScript off — arrows and dots
are enhancement. Variants: `.carousel-peek` shows a sliver of the next slide,
`.carousel-multi` shows three. Autoplay pauses on hover, on focus, and when the tab is
hidden, and never starts under reduced motion. Fires `deck:slide`.

### Drawer, mega menu, speed dial, banner

- `.drawer` / `.drawer-end` — a side panel on `<dialog>`, so focus trapping and escape
  are the browser's. `data-deck-drawer="#id"` on a trigger; `data-drawer-close` on any
  button inside. Clicking the backdrop closes it.
- `.mega` — a wide popover panel with `.mega-grid` `.mega-col` `.mega-item`
  `.mega-feature` `.mega-footer`. `data-deck-mega="#id"` adds hover intent on pointer
  devices and click everywhere else.
- `.speed-dial` — a FAB that fans out into labelled actions, with a staggered entrance
  and the plus rotating into a close. Sits above the tab bar and the safe area.
- `.banner` / `.banner-bottom` — a sticky announcement strip. Add `data-dismiss-key="x"`
  and the dismissal persists in localStorage.

### Back to top

```html
<span id="top" tabindex="-1"></span>
…
<a class="back-to-top" href="#top" aria-label="Back to top">
  <svg class="icon"><use href="/assets/deck-icons.svg#chevron-up"></use></svg>
</a>
```

A **link**, not a button. `#top` is a real target at the head of the document, so the
browser moves focus there along with the scroll. A button calling `scrollTo()` scrolls
the page and leaves a keyboard user parked at the bottom of it — they press Tab and land
back in the footer. Give the target `tabindex="-1"` so it can receive that focus.

Show and hide is a scroll-driven animation on `scroll(root block)`, ranged `400px 520px`
— the same mechanism as `.scroll-progress`. There is no scroll listener anywhere.
`visibility` is part of the keyframe on purpose: it takes the link out of the tab order
and out of the accessibility tree while it is off screen, which is what `aria-hidden` is
reaching for and which CSS can do on its own.

Where `animation-timeline` is missing, `deck.js` marks the link with `data-deck-btt` and
toggles `.is-visible` from an **IntersectionObserver** on a 400px sentinel at the top of
the document — still not a scroll handler. Unmarked, with no JavaScript at all, the link
simply stays visible and still works.

It parks at the bottom inline-end corner and stacks over whatever else is there:
`body:has(.fab, .speed-dial)` lifts it a FAB's height, `body:has(.tabbar)` lifts it a tab
bar's height, and both together lift it over both. Every branch clears
`env(safe-area-inset-bottom)`. Written in logical properties, so `dir="rtl"` moves it to
the other corner with no extra rule. It never prints.

Under `prefers-reduced-motion: reduce` the reveal animation does not apply at all, so the
link is simply always there with no entrance, and `02-reset` has already put `html` back
to `scroll-behavior: auto` — the jump is instant.

### Stepper

`.stepper` with `.step`, `.step-marker`, `.step-label`, `.step-note`. States are
`.is-done` and `.is-current`. Numbers come from a CSS counter, so inserting a step
renumbers everything. `.stepper-vertical` always stacks; `.stepper-auto` stacks below
40rem, which is what five steps need on a phone.

The stepper is for a process you're moving through. The timeline in `07-components` is
for recording what already happened — they aren't the same component.

### Inputs

- **Floating label** — `.float` with `placeholder=" "` on the input. Pure CSS via
  `:placeholder-shown`, so the label can never get out of sync with the value.
  `.float-outline` notches the label into the border.
- **Number** — `.number` with real buttons instead of the native spinner, press-and-hold
  to repeat, min/max disabling, and an optional `.number-unit`.
- **Phone** — `.phone` with a country select welded to the field. `data-mask="(###) ###-####"`
  formats as you type; `data-code` and `data-flag` drive the prefix. The number stays LTR
  and bidi-isolated even in an RTL document. Fires `deck:change` with `{ code, number, e164 }`.
- **Rating** — `.rating` over real radio inputs, so it posts a value and works with the
  keyboard. `.rating-static` with `--value` shows a partial fill for an average. Use the
  solid `#star-fill` symbol, not `#star`: both components tell a selected star from an
  empty one by colour alone, so an outlined glyph leaves the two states identical.
- **Range selector** — `.range-pair` with two native range inputs stacked. Real inputs
  mean real keyboard support and a real form post; `data-gap` keeps the handles apart.
  Fires `deck:change` with `{ min, max }`.
- **Copy** — `.copy` with `.copy-btn data-deck-copy`, or `.copy-inline` for an icon
  beside an identifier in a table. Falls back to `execCommand` on http origins where the
  clipboard API is unavailable.

### WYSIWYG editor

`.editor` with `.editor-toolbar`, `.editor-content`, `.editor-footer`. Buttons carry
`data-cmd`; `data-target="#hidden-input"` keeps a hidden field in sync for a normal form
post. Paste arrives as plain text, so a paste out of Word doesn't drag its styling in.
`data-limit` drives the character counter. Fires `deck:change` with `{ html, text }`.

### Video, gallery, lazy loading

- `.video` — a responsive frame for `<video>` or an embed, with `.video-poster` and
  `.video-play` for click-to-load. Ratios: `.video-square` `.video-portrait` `.video-wide`.
- `.masonry` — CSS columns by default, upgrading to real `grid-template-rows: masonry`
  where supported, which preserves row order. Use it when the images have different
  shapes and you would rather not crop them; use `.gallery` below when they should all
  be the same size.
- `.lazy` — a frame that holds its aspect ratio so nothing shifts, shimmers while
  waiting, and fades the image in on decode. Put the URL in `data-src` and
  `deck-extras.js` loads it 200px before it enters view.

### Chat

`.chat` with `.msg` / `.msg-out`, `.bubble`, `.bubble-meta`, `.bubble-name`,
`.bubble-attachment`, `.bubble-system`, `.bubble-typing`, and `.chat-composer`.
Consecutive messages from one side group automatically — only the last bubble in a run
keeps its tail, and repeated avatars hide themselves.

### QR code

```html
<div class="qr" data-deck-qr="https://example.com/orders/1042" data-ecl="M"></div>
```

```js
const svg = Deck.qr.svg('1FTFW1E85MFA12345', 'H');
```

A complete encoder, written for this framework: byte mode, versions 1 through 10, error
correction L / M / Q / H, Reed-Solomon over GF(256), all eight masks scored by the four
standard penalty rules, and BCH format and version information. No library, no network
call, no canvas — it emits SVG with horizontal runs merged into rects, so the markup
stays small even at version 10.

It's verified by round-trip: encode, then read the matrix back out through the format
information, the mask, the zig-zag, and the block de-interleave, and confirm the
original string comes back. Six cases across all four correction levels and versions 1
through 10 pass.

Sizes: `.qr-sm` `.qr` `.qr-lg`, or set `--qr-size`. `--qr-fg` and `--qr-bg` control the
colors — keep the contrast high or scanners will struggle. `.qr-logo` punches a mark
out of the middle, which is only safe at correction level Q or H.

Content longer than a version 10 code can hold throws with a readable message rather
than rendering something unscannable. If you hit it, link to the content instead of
embedding it.

### Indicators

`.indicator` with `-good` `-warn` `-bad` `-brand` `-lg` `-ring`, `.status-line` for a
dot plus a label, and `.with-indicator` + `.indicator-badge` for a count on an icon
(`.indicator-badge-dot` for a bare dot).

### Jumbotron and footer

`.jumbotron` / `.jumbotron-center` / `.jumbotron-media` for a hero, and `.footer` with
`.footer-grid` `.footer-brand` `.footer-col` `.footer-heading` `.footer-bottom`
`.footer-social`.

### Sidebar

```html
<aside class="cq-shell">
  <nav class="panel sidebar" aria-label="Workspace">
    <span class="sidebar-group">Issues</span>
    <a class="sidebar-link" aria-current="page" href="/issues">
      <svg class="icon">…</svg><span>Open</span><span class="badge push">42</span>
    </a>
  </nav>
</aside>
```

A bare nav list: `.sidebar-group` for a heading, `.sidebar-link` for a row, `.push` to
shove a count to the far end, `aria-current` for the active one. It brings no width and
no chrome of its own, so put it in whatever rail your shell already has — a `.split`
rail, a `.drawer`, a `.panel`.

Put `.cq-shell` on that rail and the links collapse to icons below 15rem. That is a
container query keyed to the rail, not a media query keyed to the window, so a sidebar
in a narrow column collapses on a 32 inch monitor and the same markup in a wide column
does not. The rule lives in `18-container.css` as the worked example of a named
container.

### Tooltips

There are two, and the difference matters.

```html
<button class="btn btn-icon tooltip" data-tip="Re-run failed jobs" aria-label="Re-run failed jobs">…</button>

<button class="btn" popovertarget="tipBuild">Why did this build fail?</button>
<div class="tip" id="tipBuild" popover>The integration suite timed out.<span class="tip-arrow"></span></div>
```

- `.tooltip` is a `::after` on the trigger reading `data-tip`. No extra markup, no
  JavaScript, nothing to keep in sync. It is pinned above the trigger and **cannot
  flip**, so near the top of a scrollport it runs off the edge, and it hides itself
  under `(pointer: coarse)` because a hover tip never worked on a phone anyway.
- `.tip` is a real popover placed with CSS anchor positioning (`25-anchor.css`), so it
  **can** flip — `position-try-fallbacks: flip-block, flip-inline` — and it carries a
  `.tip-arrow` that stays pointed at its anchor. `deck.js` pairs the trigger and the
  panel automatically from `popovertarget`.

Reach for `.tooltip` for a short label on an icon button in the middle of a page. Reach
for `.tip` when the text is longer, has to survive an edge, or should open on click.

### Gallery

```html
<div class="gallery">
  <a class="span-2" href="…"><img src="…" alt="Dashboard screenshot"></a>
  <a href="…"><img src="…" alt="Logo on a light background"></a>
  <a class="span-wide" href="…"><img src="…" alt="Social card, 1200 by 630"></a>
</div>
```

Equal square tiles on `auto-fill`, so nine photos and three photos both come out tidy
without a breakpoint. `.span-2` promotes a tile to 2&times;2 and `.span-wide` to
2&times;1, which is how you lead with the shot that matters. Images cover their cell and
scale slightly on hover when the tile is a link. `.masonry` is the other choice — use it
when the images have different shapes and you would rather not crop them.

### JS additions

```js
Deck.qr.svg(text, 'M')     // SVG string
Deck.qr.build(text, 'M')   // { modules, size, version } for your own renderer
Deck.copy(text)            // clipboard write with a fallback, returns a promise
```


## Libraries

Deck's core is zero-dependency and that is deliberate — it is the one thing Tailwind
cannot claim, and it is why Deck drops into a Keel view with a single `<link>` tag. So
a dependency has to earn its place by doing a job Deck genuinely does worse.

`deck-adapters.js` is the mechanism. Each adapter activates **only** if the library is
already on the page. Load none of them and nothing changes. Load one and Deck hands
that job over while keeping its own markup, classes, and styling.

```html
<script src="/assets/deck.js" defer></script>
<script src="/assets/deck-extras.js" defer></script>
<script src="/assets/deck-adapters.js" defer></script>

<!-- add only what you want -->
<script src="https://cdn.jsdelivr.net/npm/@floating-ui/dom" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs" defer></script>
```

| Job | Deck alone | With a library | Verdict |
|---|---|---|---|
| Placement | CSS anchor positioning | Floating UI (~9 KB) | Library only where anchor positioning is missing |
| Rich text | `execCommand`, deprecated | Tiptap or Quill | Use the library |
| Charts | CSS charts that retheme | Chart.js, themed by Deck | CSS for tiles, Chart.js for real axes |
| Drag and drop | Native HTML DnD, poor on touch | SortableJS | Use the library |
| Icons | 74-icon sprite | Lucide (1500 icons) | Sprite covers Deck; Lucide for the rest |
| Long lists | `content-visibility` | A virtualizer (~5 KB) | Keep the browser |
| Dates and locales | `Intl` | date-fns and friends | Keep `Intl` |

`Deck.adapters.report()` names what is actually doing each job on the current page.
Worth running when a component behaves differently between two environments.

### Placement

Every floating thing in Deck was positioned by hand with `getBoundingClientRect`, which
does not flip at the bottom of the window, does not shift back inside at an edge, and
does not follow its anchor inside a scrolling container. `25-anchor.css` fixes that with
CSS anchor positioning — natively, on the compositor, with no listeners.

`deck.js` pairs every `popovertarget` with its panel and generates a unique
`anchor-name`, so you write no extra markup. Where the browser lacks it, the Floating UI
adapter takes over with `flip`, `shift`, `size`, and `arrow`; where neither is present,
Deck's own placement runs as before.

New anchored components: `.tip` (a tooltip that can flip and carry an arrow, unlike the
`::after` one) and `.pop` (a popover card with a title, body, and actions).

The real win is subtler: an anchored panel goes in the top layer, which is the fix for
the bug that bites every combobox nested inside a modal or an `overflow: hidden` card.

### Rich text

`document.execCommand` is deprecated and inconsistent, and rewriting onto Selection and
Range means building a document model — which is what Tiptap and Quill already are. The
adapter hands `.editor-content` over to whichever is present and keeps Deck's toolbar
chrome, so the markup and CSS are unchanged. Toolbar `data-cmd` values are mapped to
each library's command set, and active state still lights the buttons.

Deck's built-in editor stays as the fallback so a form still works with no library.

### Charts

Deck's CSS charts retheme with the hue slider and cost nothing, which is right for
dashboard tiles. What they cannot do is a time axis, a crosshair, a zoom, or twenty
thousand points. The adapter sets Chart.js defaults from Deck's tokens — fonts, grid
color, tooltip surface, point styles — and re-reads them when the theme or hue changes,
so a Chart.js canvas follows the slider like everything else.

```js
Deck.chart(canvas, config);   // same as new Chart(), with the series palette applied
```

### Drag and drop

Deck's CSS already uses SortableJS's default class names (`sortable-ghost`,
`sortable-chosen`, `sortable-drag`), so no configuration is needed. Add
`data-deck-sortable="groupname"` and optionally `data-handle=".drag-handle"`. Without
the library it falls back to native HTML drag and drop, which works on a desktop and is
poor on touch — that's the honest reason to load SortableJS.

Comes with `.kanban`, `.kanban-col`, `.kanban-head`, `.kanban-body`, `.kanban-card`, and
`.kanban-empty`. Fires `deck:reorder` with the new order as an array of `data-id` values,
which is what you POST back to Keel.

### Long lists

The usual answer to a ten thousand row grid is a virtualization library: measure the
viewport, render a window, position a spacer, reconcile every scroll frame. It works and
it breaks find-in-page, printing, accessibility tree order, and selection across the
boundary.

`content-visibility: auto` does the same job in the engine. Off-screen subtrees are
skipped during layout, style, paint, and hit testing but stay in the DOM, so Ctrl+F still
finds them and the print stylesheet still prints them. One line of CSS, no JavaScript.

```html
<table class="dg dg-virtual">
```

Also `.list-virtual`, `.virtual` (with `--item-size`), and `.defer` for whole sections
below the fold. The print stylesheet forces all of them back to `visible`, or half a
report comes out blank.

`.contain` and `.contain-paint` are the companion: on a dashboard with twenty cards,
containment is the difference between one layout pass and twenty.

### Dates and locales

Month names, weekday names, and the first day of the week now come from `Intl` rather
than a hardcoded English array. It is built into every browser and correct in every
locale, so a date library adds nothing.

```html
<div class="datefield" data-deck-datepicker data-locale="de-DE" data-format="dmy">
```

`data-locale` overrides the document language. Week start comes from
`Intl.Locale.getWeekInfo()` — Sunday in the US, Monday across most of Europe — and can
still be forced with `data-week-start`. Day cell labels use `dateStyle: 'full'`, so a
screen reader reads a properly localized date.

```js
Deck.locale('es-MX')   // { months, monthsShort, days, weekStart, long, full }
```

### Icons

The 74-icon sprite covers what the framework itself needs plus the automotive set it was
built for. Past that, the cheapest move is to add the name to `tools/icons/icons.txt` and
run `npm run icons` — any of the 4,025 Material Symbols glyphs is one line away.

If you would rather not regenerate, write `<span data-icon="briefcase" class="icon">` and
the Lucide adapter swaps in the path data, keeping Deck's `.icon` sizing rules. No
adapter, no swap, and the sprite still works. Be aware that Lucide draws real strokes
while the sprite is filled outlines, so the two do not match at close range; use one or
the other in a given screen.