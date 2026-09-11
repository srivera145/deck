<?php
declare(strict_types=1);

/**
 * Install — the first tutorial.
 *
 * Four routes, ordered by how little work each needs: CDN, bundler, npx,
 * Composer. Every command on this page was run from an empty directory with an
 * empty package cache against the published 0.1.2 packages. Every file a reader
 * is told to create was copied out of this page, built where there is a build,
 * served, and put through the same four-point check in Chrome: stylesheet
 * loaded, icon drawn, hue slider retunes, toast fires. The code blocks that
 * become files carry data-test so the test run copies them from here, not from
 * a second copy that could drift.
 *
 * When a command or its output changes, run it again and paste the new output
 * rather than editing the old. An install page that fails on step one costs
 * more trust than every other page on the site can earn back.
 *
 * Every CDN URL is pinned to @0.1, never @latest.
 */

$page = [
    'path' => 'start/install.php',
    'title' => 'Install Deck',
    'level' => 'Beginner',
    'description' => "Install Deck by CDN, bundler, npx or Composer, each tested end to end, with what each costs and where the icon sprite, scripts, emoji and favicon come from.",
];

require __DIR__ . '/../_layout.php';

/* Pinned to the minor version: 0.1.x fixes arrive, 0.2 never does. */
$CDN = 'https://cdn.jsdelivr.net/npm/@echodial/deck@0.1/dist';
$UNPKG = 'https://unpkg.com/@echodial/deck@0.1/dist';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Start</li>
    <li aria-current="page">Install</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Install Deck</h1>
  <p class="lede">
    Installing with npm or Composer puts Deck in <code>node_modules</code> or
    <code>vendor</code>, which a browser cannot reach. Use the CDN and there is nothing to
    install, though icons still need one file saved to your own site. Use a bundler and
    there is nothing to copy. Everything else has one copy step.
  </p>
</header>

<div class="alert alert-info">
  <svg class="icon"><use href="../../assets/deck/deck-icons.svg#info"></use></svg>
  <div>
    <div class="alert-title">public_html/ or public/</div>
    <p class="alert-body">
      Wherever a folder appears, this page gives both names:
      <code>public_html/</code> for cPanel, Plesk and Helm, and <code>public/</code> for
      Laravel and Symfony. Use the one your web server serves, and run every command on this page from the folder that contains it, your project root. Any other name, such as
      <code>web/</code>, works the same way.
    </p>
  </div>
</div>

<section class="stack-3">
  <h2 id="choose">Pick a route</h2>
  <p>
    In order of how little work each one is. All four end with the same
    <a href="#check">check page</a>, so you know when you are done.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The four install routes, the work each needs, and what each costs</caption>
      <thead>
        <tr><th scope="col">Route</th><th scope="col">The work</th><th scope="col">What it costs</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Route"><a href="#cdn">1. CDN</a></th>
          <td data-label="The work">Two tags, and the icon sprite saved to your site if you use icons.</td>
          <td data-label="What it costs">Every page load asks a third party for Deck, and the version is written in your HTML rather than pinned in your repository.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Route"><a href="#bundler">2. Bundler</a></th>
          <td data-label="The work"><code>npm install</code> and three imports. Nothing to copy.</td>
          <td data-label="What it costs">A build step, which is the thing Deck exists so you can do without.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Route"><a href="#npx">3. npx</a></th>
          <td data-label="The work">One command that copies five files.</td>
          <td data-label="What it costs">You run it again after every upgrade, and no lockfile records which version you copied.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Route"><a href="#composer">4. Composer</a></th>
          <td data-label="The work">Three scripts added to your <code>composer.json</code>, then <code>composer require</code>.</td>
          <td data-label="What it costs">Scripts in your own <code>composer.json</code> that you maintain, and remove if you remove Deck.</td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-4">
  <h2 id="cdn">1. CDN</h2>
  <p>
    Nothing to install. jsDelivr and unpkg serve every version published to npm, so these
    two tags in your page's <code>&lt;head&gt;</code> are the whole stylesheet and all of
    the JavaScript:
  </p>
  <pre class="dx-code" data-test="cdn-head"><code>&lt;link rel="stylesheet" href="<?= e($CDN) ?>/deck.min.css"&gt;
&lt;script src="<?= e($CDN) ?>/deck.bundle.min.js" data-deck-icons="/assets/deck/deck-icons.svg" defer&gt;&lt;/script&gt;</code></pre>
  <p>
    <strong>What it costs.</strong> Every page load makes a request to a third party, and
    the version your site runs is written in your HTML rather than pinned in a lockfile in
    your repository.
  </p>
  <p>
    Every URL is pinned to <code>@0.1</code>, never <code>@latest</code>:
    <code>@0.1</code> picks up each 0.1.x fix and never moves to 0.2, while
    <code>@latest</code>, or no version at all, moves your page onto a breaking release the
    day it is published.
  </p>

  <div class="alert alert-bad">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#alert-triangle"></use></svg>
    <div>
      <div class="alert-title">Icons will not draw from the CDN</div>
      <p class="alert-body">
        Browsers refuse an SVG <code>&lt;use&gt;</code> whose <code>href</code> is on another
        origin, so every icon pointed at jsDelivr or unpkg is an empty box. That includes the
        icons Deck's own components draw, because the bundle looks for the sprite beside
        itself, on the CDN. Save the sprite to your own site and point
        <code>data-deck-icons</code> at it, as the tag above does.
        <a href="#icons">What was tested.</a>
      </p>
    </div>
  </div>
  <pre class="dx-code"><code># public_html/ (cPanel, Plesk, Helm)
curl -sSL --create-dirs -o public_html/assets/deck/deck-icons.svg <?= e($CDN) ?>/deck-icons.svg

# public/ (Laravel, Symfony)
curl -sSL --create-dirs -o public/assets/deck/deck-icons.svg <?= e($CDN) ?>/deck-icons.svg</code></pre>
  <p>
    It prints nothing and exits with 0. That sprite is the only file this route puts on
    disk.
  </p>

  <h3 id="cdn-files">Every file</h3>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The files Deck publishes to the CDNs</caption>
      <thead>
        <tr><th scope="col">File</th><th scope="col">What it is</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="File"><code>deck.min.css</code></th><td data-label="What it is">The stylesheet. This is the one to link.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck.css</code></th><td data-label="What it is">The same stylesheet, unminified, for reading.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck.js</code></th><td data-label="What it is">The core script. <a href="#javascript">Which components need it.</a></td></tr>
        <tr><th scope="row" data-label="File"><code>deck-extras.js</code></th><td data-label="What it is">Carousel, drawer, mega menu, copy button, QR codes and the editor. Load it after <code>deck.js</code>.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck-adapters.js</code></th><td data-label="What it is">Only for use alongside Floating UI, Chart.js, SortableJS and similar libraries. Load it after <code>deck.js</code>.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck.bundle.min.js</code></th><td data-label="What it is">All three scripts in one file, minified.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck-icons.svg</code></th><td data-label="What it is">The icon sprite. Served, but <a href="#icons">it will not draw from here</a>.</td></tr>
      </tbody>
    </table>
  </div>
  <p>jsDelivr:</p>
  <pre class="dx-code"><code><?= e($CDN) ?>/deck.min.css
<?= e($CDN) ?>/deck.css
<?= e($CDN) ?>/deck.js
<?= e($CDN) ?>/deck-extras.js
<?= e($CDN) ?>/deck-adapters.js
<?= e($CDN) ?>/deck.bundle.min.js
<?= e($CDN) ?>/deck-icons.svg</code></pre>
  <p>
    unpkg, which answers <code>@0.1</code> with a redirect to the exact release, currently
    <code>@0.1.2</code>:
  </p>
  <pre class="dx-code"><code><?= e($UNPKG) ?>/deck.min.css
<?= e($UNPKG) ?>/deck.css
<?= e($UNPKG) ?>/deck.js
<?= e($UNPKG) ?>/deck-extras.js
<?= e($UNPKG) ?>/deck-adapters.js
<?= e($UNPKG) ?>/deck.bundle.min.js
<?= e($UNPKG) ?>/deck-icons.svg</code></pre>

  <h3 id="cdn-check">Check it</h3>
  <p>
    Take the <a href="#check">check page</a>, replace its three asset lines, the stylesheet
    and the two scripts, with the two tags at the top of this section, and open it.
  </p>
</section>

<section class="stack-4">
  <h2 id="bundler">2. Bundler</h2>
  <p>
    Nothing to copy: your bundler reads Deck out of <code>node_modules</code> and emits what
    the browser needs. In your project:
  </p>
  <pre class="dx-code"><code>npm install @echodial/deck</code></pre>
  <pre class="dx-code"><code>$ npm install @echodial/deck

added 1 package, and audited 2 packages in 2s

found 0 vulnerabilities</code></pre>
  <p>
    <strong>What it costs.</strong> A build step, which is the thing Deck exists so you can
    do without. This route is for a project that already has one.
  </p>

  <h3 id="exports">Every import</h3>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The subpaths in the package's exports map</caption>
      <thead>
        <tr><th scope="col">Import</th><th scope="col">File</th><th scope="col">Use it for</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="Import"><code>@echodial/deck/css</code></th><td data-label="File"><code>dist/deck.css</code></td><td data-label="Use it for">The stylesheet.</td></tr>
        <tr><th scope="row" data-label="Import"><code>@echodial/deck/css/min</code></th><td data-label="File"><code>dist/deck.min.css</code></td><td data-label="Use it for">The stylesheet, already minified.</td></tr>
        <tr><th scope="row" data-label="Import"><code>@echodial/deck/icons</code></th><td data-label="File"><code>dist/deck-icons.svg</code></td><td data-label="Use it for">The sprite, imported for its URL. See below.</td></tr>
        <tr><th scope="row" data-label="Import"><code>@echodial/deck/js</code></th><td data-label="File"><code>dist/deck.js</code></td><td data-label="Use it for">The core script on its own.</td></tr>
        <tr><th scope="row" data-label="Import"><code>@echodial/deck/extras</code></th><td data-label="File"><code>dist/deck-extras.js</code></td><td data-label="Use it for">The extended components. Import it after <code>/js</code>.</td></tr>
        <tr><th scope="row" data-label="Import"><code>@echodial/deck/adapters</code></th><td data-label="File"><code>dist/deck-adapters.js</code></td><td data-label="Use it for">Third-party library adapters. Import it after <code>/js</code>.</td></tr>
        <tr><th scope="row" data-label="Import"><code>@echodial/deck/bundle</code></th><td data-label="File"><code>dist/deck.bundle.js</code></td><td data-label="Use it for">All three scripts. Sets <code>window.Deck</code>.</td></tr>
        <tr><th scope="row" data-label="Import"><code>@echodial/deck/layers/*</code></th><td data-label="File"><code>dist/layers/*.css</code></td><td data-label="Use it for">One cascade layer at a time, such as <code>/layers/tokens.css</code>.</td></tr>
        <tr><th scope="row" data-label="Import"><code>@echodial/deck</code></th><td data-label="File"><code>dist/deck.esm.js</code></td><td data-label="Use it for">Nothing, in 0.1.2. See the warning below.</td></tr>
      </tbody>
    </table>
  </div>
  <p>
    Every one of these resolved through the exports map in a production build with Vite 8.3
    and with webpack 5.110.
  </p>

  <div class="alert alert-warn">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#alert-triangle"></use></svg>
    <div>
      <div class="alert-title">In 0.1.2, import Deck from '@echodial/deck' does not build</div>
      <p class="alert-body">
        The module it points at ends with an export that is not valid JavaScript, so Node,
        Vite and webpack all stop on it; webpack says <em>Module parse failed: Export
        'globalThis' is not defined</em>. Import the bundle for its side effect instead, as
        below. It sets <code>window.Deck</code>, which is also what inline handlers such as
        <code>onclick="Deck.toast(…)"</code> call.
      </p>
    </div>
  </div>

  <h3 id="vite">Vite</h3>
  <p>
    <code>main.js</code>, in your project root beside <code>package.json</code>:
  </p>
  <pre class="dx-code" data-test="vite-main"><code>import '@echodial/deck/css';
import '@echodial/deck/bundle';
import sprite from '@echodial/deck/icons?url';

const { Deck } = window;

// &lt;use href&gt; needs the sprite's real URL, which exists only once the build has
// emitted the file, so fill it in here: for Deck's own components, and for
// every icon in your markup that names just a symbol.
Deck.iconSprite = sprite;
for (const use of document.querySelectorAll('use[href^="#"]')) {
  use.setAttribute('href', sprite + use.getAttribute('href'));
}</code></pre>
  <p>
    An import is not a URL, and <code>&lt;use href&gt;</code> needs one. <code>?url</code>
    asks Vite for the address of the file it emits. The loop puts that address in front of
    every icon in your markup written as just <code>#name</code>, and
    <code>Deck.iconSprite</code> does the same for the icons Deck's own components draw.
  </p>
  <p>
    <code>index.html</code>, in the same folder, is the <a href="#check">check page</a> with one module script in
    place of its three asset lines, and its icon written as <code>#check-circle</code>:
  </p>
  <pre class="dx-code" data-test="vite-index"><code>&lt;!doctype html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
&lt;meta charset="utf-8"&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"&gt;
&lt;title&gt;Deck is installed&lt;/title&gt;
&lt;script type="module" src="/main.js"&gt;&lt;/script&gt;
&lt;/head&gt;
&lt;body&gt;

&lt;main class="container section stack-6"&gt;
  &lt;h1&gt;Deck is installed&lt;/h1&gt;
  &lt;p class="lede"&gt;If this line is muted grey and the button below is a filled,
    rounded, brand-coloured rectangle, the stylesheet loaded.&lt;/p&gt;

  &lt;div class="cluster"&gt;
    &lt;button class="btn btn-primary"&gt;A button&lt;/button&gt;
    &lt;span class="badge badge-good"&gt;A badge&lt;/span&gt;
    &lt;svg class="icon icon-lg" style="color:var(--brand)"&gt;
      &lt;use href="#check-circle"&gt;&lt;/use&gt;
    &lt;/svg&gt;
  &lt;/div&gt;

  &lt;input class="range" type="range" min="0" max="360" value="196"
    aria-label="Brand hue" oninput="Deck.hue(this.value)"&gt;

  &lt;button class="btn" onclick="Deck.toast({kind:'good', title:'JavaScript works too'})"&gt;
    Fire a toast
  &lt;/button&gt;
&lt;/main&gt;

&lt;/body&gt;
&lt;/html&gt;</code></pre>
  <pre class="dx-code"><code>npm install --save-dev vite
npx vite build
npx vite preview</code></pre>
  <p>
    <code>npx vite build</code> printed:
  </p>
  <pre class="dx-code"><code>$ npx vite build
vite v8.3.0 building client environment for production...
transforming...
✓ 7 modules transformed.
rendering chunks...
computing gzip size...
dist/index.html                        1.08 kB │ gzip:  0.62 kB
dist/assets/deck-icons-zJMcLAdo.svg  110.03 kB │ gzip: 34.47 kB
dist/assets/index-BRXtUJ3R.css       172.36 kB │ gzip: 32.91 kB
dist/assets/index-61EmezT8.js         57.89 kB │ gzip: 18.23 kB

✓ built in 236ms</code></pre>
  <p class="dx-note">
    Vite 8 also prints a warning for each of Deck's carousel rules that use
    <code>::scroll-marker</code> and <code>::scroll-button</code>, beginning
    <em>[lightningcss minify] 'scroll-marker-group' is not recognized as a valid
    pseudo-element</em>. They are left out of the output above. The rules themselves are not
    lost: the built stylesheet has every one of them, the same number as
    <code>deck.min.css</code>.
  </p>

  <h3 id="webpack">webpack</h3>
  <pre class="dx-code"><code>npm install --save-dev webpack webpack-cli css-loader style-loader html-webpack-plugin</code></pre>
  <p>
    <code>webpack.config.js</code>:
  </p>
  <pre class="dx-code" data-test="webpack-config"><code>const HtmlWebpackPlugin = require('html-webpack-plugin');

module.exports = {
  mode: 'production',
  entry: './src/main.mjs',
  module: {
    rules: [
      { test: /\.css$/, use: ['style-loader', 'css-loader'] },
      { test: /deck-icons\.svg$/, type: 'asset/resource' },
    ],
  },
  plugins: [new HtmlWebpackPlugin({ template: './src/index.html' })],
};</code></pre>
  <p>
    <code>src/main.mjs</code> is the Vite file with the sprite imported without
    <code>?url</code>. webpack reads a query on a package import as part of the export's
    name and stops with <em>"./icons?url" is not exported</em>; the
    <code>asset/resource</code> rule above is what makes the plain import a URL.
  </p>
  <pre class="dx-code" data-test="webpack-main"><code>import '@echodial/deck/css';
import '@echodial/deck/bundle';
import sprite from '@echodial/deck/icons';

const { Deck } = window;

// &lt;use href&gt; needs the sprite's real URL, which exists only once the build has
// emitted the file, so fill it in here: for Deck's own components, and for
// every icon in your markup that names just a symbol.
Deck.iconSprite = sprite;
for (const use of document.querySelectorAll('use[href^="#"]')) {
  use.setAttribute('href', sprite + use.getAttribute('href'));
}</code></pre>
  <p>
    <code>src/index.html</code> is the Vite <code>index.html</code> without its script tag,
    which the HTML plugin adds. Then <code>npx webpack</code> builds into
    <code>dist/</code>, which is the folder to serve. It warns that <code>main.js</code> is
    over its 244 KiB recommendation, because <code>style-loader</code> carries the stylesheet
    inside the script.
  </p>
  <p class="dx-note">
    The entry is <code>.mjs</code> because <code>npm init -y</code> writes
    <code>"type": "commonjs"</code> into <code>package.json</code>, and webpack then refuses
    <code>import</code> in a <code>.js</code> file: <em>'import' and 'export' may appear only
    with 'sourceType: module'</em>.
  </p>

  <h3 id="bundler-check">Check it</h3>
  <p>
    Open the address <code>npx vite preview</code> prints, or serve webpack's
    <code>dist/</code>, and look for the <a href="#check-success">four signs</a>.
  </p>
</section>

<section class="stack-4">
  <h2 id="npx">3. npx</h2>
  <p>
    One command copies Deck out of npm and into your site, and installs nothing in your
    project:
  </p>
  <pre class="dx-code"><code># public_html/ (cPanel, Plesk, Helm)
npx @echodial/deck@0.1 init public_html/assets/deck

# public/ (Laravel, Symfony)
npx @echodial/deck@0.1 init public/assets/deck</code></pre>
  <p>
    <strong>What it costs.</strong> You run it again after every upgrade, and no lockfile
    records which version you copied; the version is on the second line of
    <code>deck.css</code>.
  </p>
  <p>
    <code>@0.1</code> is there for the same reason the CDN URLs carry it. From an empty
    directory, with nothing installed and an empty npm cache:
  </p>
  <pre class="dx-code"><code>$ npx @echodial/deck@0.1 init public_html/assets/deck
npm warn exec The following package was not found and will be installed: @echodial/deck@0.1.2
  + public_html\assets\deck\deck.css
  + public_html\assets\deck\deck-icons.svg
  + public_html\assets\deck\deck.js
  + public_html\assets\deck\deck-extras.js
  + public_html\assets\deck\deck-adapters.js

Add to your layout:

  &lt;link rel="stylesheet" href="/assets/deck/deck.css"&gt;
  &lt;script src="/assets/deck/deck.js" defer&gt;&lt;/script&gt;

deck-icons.svg sits beside deck.js and is found automatically.
To load the sprite from somewhere else:

  &lt;script src="/assets/deck/deck.js" data-deck-icons="/your/path/deck-icons.svg" defer&gt;&lt;/script&gt;</code></pre>
  <p class="dx-note">
    Three things in that output are worth knowing before they confuse you. In an
    interactive terminal npx asks before it downloads the package; answer <kbd>y</kbd>. The
    paths use backslashes because this run was on Windows. And the tags start at
    <code>/assets</code>, because <code>init</code> takes <code>public_html/</code> or
    <code>public/</code> to be your document root, which is not part of a URL.
  </p>

  <h3 id="npx-flags">Flags</h3>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">What init copies with each flag</caption>
      <thead>
        <tr><th scope="col">Flag</th><th scope="col">What lands</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="Flag">none</th><td data-label="What lands"><code>deck.css</code>, <code>deck-icons.svg</code>, <code>deck.js</code>, <code>deck-extras.js</code>, <code>deck-adapters.js</code></td></tr>
        <tr><th scope="row" data-label="Flag"><code>--min</code></th><td data-label="What lands"><code>deck.min.css</code> in place of <code>deck.css</code></td></tr>
        <tr><th scope="row" data-label="Flag"><code>--bundle</code></th><td data-label="What lands"><code>deck.bundle.min.js</code> in place of the three scripts</td></tr>
        <tr><th scope="row" data-label="Flag"><code>--css-only</code></th><td data-label="What lands"><code>deck.css</code> and <code>deck-icons.svg</code>, no scripts</td></tr>
        <tr><th scope="row" data-label="Flag"><code>--force</code></th><td data-label="What lands">The same files, overwriting any already there. Without it a second run copies nothing and prints <em>5 already there (use --force to overwrite)</em>.</td></tr>
      </tbody>
    </table>
  </div>
  <p>
    Flags combine: <code>--min --bundle</code> copies <code>deck.min.css</code>,
    <code>deck-icons.svg</code> and <code>deck.bundle.min.js</code>. With
    <code>--bundle</code> or <code>--css-only</code>, the last lines <code>init</code> prints
    still say the sprite sits beside <code>deck.js</code>. Ignore them: the sprite is beside
    whichever files were copied.
  </p>

  <h3 id="npx-missing">What does not land</h3>
  <p>
    The minified single scripts (<code>deck.min.js</code>, <code>deck-extras.min.js</code>,
    <code>deck-adapters.min.js</code>), the unminified <code>deck.bundle.js</code>, the module
    entry <code>deck.esm.js</code>, the per-layer stylesheets in <code>layers/</code>, the
    <a href="#brand">logo and favicon files</a>, and the build data such as
    <code>api.json</code>. A page needs none of them. <code>npx @echodial/deck@0.1 list</code>
    lists what is in the package's <code>dist/</code> folder, with sizes.
  </p>

  <h3 id="npx-check">Check it</h3>
  <p>
    The <a href="#check">check page</a> works unchanged.
  </p>
</section>

<section class="stack-4">
  <h2 id="composer">4. Composer</h2>
  <p>
    <code>composer require echodial/deck</code> puts Deck in <code>vendor/</code>, which a
    browser cannot reach, and copies nothing out of it on its own. Deck ships a class that
    does the copy, <code>EchoDial\Deck\Installer</code>. But Composer runs scripts only from
    the root <code>composer.json</code>, the project you run <code>composer</code> in, and
    never from a package you install. A script in Deck's own <code>composer.json</code> would
    never fire in your project, so the call has to live in yours.
  </p>
  <p>
    <strong>What it costs.</strong> Three scripts in your own <code>composer.json</code>
    that you maintain, and remove if you remove Deck.
  </p>
  <p>
    Add these two blocks to your <code>composer.json</code> before you require Deck:
  </p>
  <pre class="dx-code"><code>"scripts": {
  "post-install-cmd": ["EchoDial\\Deck\\Installer::postInstall"],
  "post-update-cmd": ["EchoDial\\Deck\\Installer::postInstall"],
  "deck-publish": "EchoDial\\Deck\\Installer::publish"
},
"extra": {
  "deck": {
    "publish-to": "public_html/assets/deck",
    "auto-publish": true
  }
}</code></pre>
  <dl class="stack-3">
    <dt><strong><code>publish-to</code></strong></dt>
    <dd>
      Where the files go, relative to <code>composer.json</code>. Make it your document root
      plus <code>/assets/deck</code>: <code>public_html/assets/deck</code> on cPanel, Plesk
      and Helm, <code>public/assets/deck</code> on Laravel and Symfony. Left out, it is
      <code>public/assets/deck</code>, which is wrong for every <code>public_html/</code>
      site.
    </dd>
    <dt><strong><code>auto-publish</code></strong></dt>
    <dd>
      <code>true</code>: every <code>composer install</code> and <code>composer update</code>
      copies the files, skipping any that have not changed. <code>false</code> or left out:
      those commands print a one-line reminder instead, and <code>composer deck-publish</code>
      copies on demand.
    </dd>
  </dl>
  <p>
    Then:
  </p>
  <pre class="dx-code"><code>composer require echodial/deck</code></pre>
  <p>
    From an empty directory holding only those two blocks, with an empty Composer cache:
  </p>
  <pre class="dx-code"><code>$ composer require echodial/deck
./composer.json has been updated
Running composer update echodial/deck
Loading composer repositories with package information
Updating dependencies
Lock file operations: 1 install, 0 updates, 0 removals
  - Locking echodial/deck (v0.1.2)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Downloading echodial/deck (v0.1.2)
  - Installing echodial/deck (v0.1.2): Extracting archive
Generating autoload files
&gt; EchoDial\Deck\Installer::postInstall
Deck 0.1.1 published to public_html/assets/deck (8 copied, 0 unchanged)
  Add to your layout:
    &lt;link rel="stylesheet" href="/assets/deck/deck.css"&gt;
    &lt;script src="/assets/deck/deck.js" defer&gt;&lt;/script&gt;
No security vulnerability advisories found.
Using version ^0.1.2 for echodial/deck</code></pre>
  <p class="dx-note">
    <strong>Why it says 0.1.1.</strong> The 0.1.2 package on Packagist was built from a commit
    made before its version number changed, so it calls itself 0.1.1 in that line and in
    its file banners. It is the package this page was tested against.
  </p>
  <p>
    It copies eight files: <code>deck.css</code>, <code>deck.min.css</code>,
    <code>deck-icons.svg</code>, <code>deck.js</code>, <code>deck-extras.js</code>,
    <code>deck-adapters.js</code>, <code>deck.bundle.js</code> and
    <code>deck.bundle.min.js</code>. If Deck was already installed when you added the
    scripts, run <code>composer deck-publish</code> once. If you remove Deck, remove the
    scripts too: Composer keeps calling them, and without the class every install prints
    <em>Class EchoDial\Deck\Installer is not autoloadable, can not call post-install-cmd
    script</em>.
  </p>
  <p>
    To keep scripts out of <code>composer.json</code>, use <a href="#npx">npx</a> instead,
    or copy the files by hand as <a href="#without">described below</a>.
  </p>

  <h3 id="composer-check">Check it</h3>
  <p>
    The <a href="#check">check page</a> works unchanged.
  </p>
</section>

<section class="stack-4">
  <h2 id="check">The check page</h2>
  <p>
    Every route ends here. Save this as <code>public_html/deck-check.html</code>, or
    <code>public/deck-check.html</code>, beside the <code>assets</code> folder. It has a name of its own so it cannot replace a home page you already have. It links the
    files npx and Composer copy; the CDN and bundler routes above say what to change.
  </p>
  <pre class="dx-code" data-test="check-page"><code>&lt;!doctype html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
&lt;meta charset="utf-8"&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"&gt;
&lt;title&gt;Deck is installed&lt;/title&gt;
&lt;link rel="stylesheet" href="/assets/deck/deck.css"&gt;
&lt;script src="/assets/deck/deck.js" defer&gt;&lt;/script&gt;
&lt;script src="/assets/deck/deck-extras.js" defer&gt;&lt;/script&gt;
&lt;/head&gt;
&lt;body&gt;

&lt;main class="container section stack-6"&gt;
  &lt;h1&gt;Deck is installed&lt;/h1&gt;
  &lt;p class="lede"&gt;If this line is muted grey and the button below is a filled,
    rounded, brand-coloured rectangle, the stylesheet loaded.&lt;/p&gt;

  &lt;div class="cluster"&gt;
    &lt;button class="btn btn-primary"&gt;A button&lt;/button&gt;
    &lt;span class="badge badge-good"&gt;A badge&lt;/span&gt;
    &lt;svg class="icon icon-lg" style="color:var(--brand)"&gt;
      &lt;use href="/assets/deck/deck-icons.svg#check-circle"&gt;&lt;/use&gt;
    &lt;/svg&gt;
  &lt;/div&gt;

  &lt;input class="range" type="range" min="0" max="360" value="196"
    aria-label="Brand hue" oninput="Deck.hue(this.value)"&gt;

  &lt;button class="btn" onclick="Deck.toast({kind:'good', title:'JavaScript works too'})"&gt;
    Fire a toast
  &lt;/button&gt;
&lt;/main&gt;

&lt;/body&gt;
&lt;/html&gt;</code></pre>
  <p>
    Serve the folder rather than opening the file: the paths start at the site root, and a
    <code>file://</code> address has no site root for them to start from.
  </p>
  <pre class="dx-code"><code># public_html/ (cPanel, Plesk, Helm)
php -S localhost:8000 -t public_html
python -m http.server 8000 --directory public_html

# public/ (Laravel, Symfony)
php -S localhost:8000 -t public
python -m http.server 8000 --directory public</code></pre>
  <p>
    Then open <code>http://localhost:8000/deck-check.html</code>, and delete the file once all four signs below are there.
  </p>

  <h3 id="check-success">What success looks like</h3>
  <ol class="stack-2">
    <li><strong>The stylesheet loaded.</strong> The button is a filled, rounded, brand-coloured rectangle, not a grey system button.</li>
    <li><strong>An icon rendered.</strong> A check mark in a circle sits beside the badge, not an empty gap.</li>
    <li><strong>The hue slider retunes.</strong> Drag it, and the button and the icon change colour together, because every brand colour comes from one custom property, <code>--hue-brand</code>, and <code>Deck.hue()</code> sets it.</li>
    <li><strong>A toast fires.</strong> Press <strong>Fire a toast</strong>, and a notification saying <em>JavaScript works too</em> slides in from the corner.</li>
  </ol>
  <p>
    Rendered here by the same stylesheet, without the toast button. The slider retunes this
    page too:
  </p>
  <?php docs_example(
      '<div class="stack-4">' . "\n" .
      '  <h3>Deck is installed</h3>' . "\n" .
      '  <p class="lede">If this line is muted grey and the button below is a filled, rounded, brand-coloured rectangle, the stylesheet loaded.</p>' . "\n" .
      '  <div class="cluster">' . "\n" .
      '    <button class="btn btn-primary">A button</button>' . "\n" .
      '    <span class="badge badge-good">A badge</span>' . "\n" .
      '    <svg class="icon icon-lg" style="color:var(--brand)"><use href="../../assets/deck/deck-icons.svg#check-circle"></use></svg>' . "\n" .
      '  </div>' . "\n" .
      '  <input class="range" type="range" min="0" max="360" value="196" aria-label="Brand hue" oninput="Deck.hue(this.value)">' . "\n" .
      '</div>',
      'The check page, rendered by the stylesheet this site uses',
      'stack'
  ); ?>
  <p>
    <code>viewport-fit=cover</code> is not decoration. It is what lets
    <code>.safe-top</code> and <code>.safe-bottom</code> reach around the notch and the home
    indicator on a phone. Leave it out and those become no-ops.
  </p>
</section>

<section class="stack-4">
  <h2 id="assets">Assets</h2>
  <p>
    The stylesheet is not all Deck ships. This is where each of the other pieces comes from.
  </p>

  <h3 id="icons">Icons</h3>
  <p>
    The icon sprite, <code>deck-icons.svg</code>, is a file of its own, separate from the
    stylesheet, and it has to be served from the same origin as your page. An SVG
    <code>&lt;use&gt;</code> that points at another origin is blocked by the browser, so the
    copy on a CDN will not draw. Tested in Chrome, from a page served on
    <code>127.0.0.1</code>:
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Where the sprite was loaded from and what drew</caption>
      <thead>
        <tr><th scope="col">How the sprite was referenced</th><th scope="col">What drew</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="Referenced as"><code>&lt;use href&gt;</code> to the jsDelivr URL</th><td data-label="What drew">Nothing. Chrome logs <em>Unsafe attempt to load URL … Domains, protocols and ports must match.</em></td></tr>
        <tr><th scope="row" data-label="Referenced as"><code>&lt;use href&gt;</code> to the unpkg URL</th><td data-label="What drew">Nothing, with the same message.</td></tr>
        <tr><th scope="row" data-label="Referenced as"><code>Deck.iconSprite</code> as the bundle works it out when it is loaded from jsDelivr</th><td data-label="What drew">Nothing: it points at jsDelivr.</td></tr>
        <tr><th scope="row" data-label="Referenced as">The same file saved beside the page</th><td data-label="What drew">The icon.</td></tr>
        <tr><th scope="row" data-label="Referenced as">The jsDelivr file fetched with <code>fetch()</code> and inserted into the page</th><td data-label="What drew">The icon.</td></tr>
      </tbody>
    </table>
  </div>
  <p>
    Two settings tell Deck where the sprite is. On the script tag,
    <code>data-deck-icons</code>, read once as the script loads. In code,
    <code>Deck.iconSprite</code>, which applies to every icon Deck draws after you set it:
  </p>
  <pre class="dx-code"><code>&lt;script src="/assets/deck/deck.js" data-deck-icons="/assets/deck/deck-icons.svg" defer&gt;&lt;/script&gt;

Deck.iconSprite = '/assets/deck/deck-icons.svg';</code></pre>
  <p>
    With neither, <code>deck.js</code> looks for the sprite beside its own URL, which is why
    the npx and Composer routes need no setting. Neither setting touches your own markup: an
    icon you write names the file itself, as
    <code>&lt;svg class="icon"&gt;&lt;use href="/assets/deck/deck-icons.svg#name"&gt;&lt;/use&gt;&lt;/svg&gt;</code>.
  </p>
  <p>
    If the sprite really has to come from a CDN, fetch it and put it in the page, then write
    icons as <code>&lt;use href="#name"&gt;</code>, which refers to the copy in the document.
    Until the fetch finishes every icon is blank, and each page carries the whole 110 KB
    sprite in its markup.
  </p>
  <pre class="dx-code"><code>fetch('<?= e($CDN) ?>/deck-icons.svg')
  .then((r) =&gt; r.text())
  .then((svg) =&gt; {
    const holder = document.createElement('div');
    holder.style.cssText = 'position:absolute;inline-size:0;block-size:0;overflow:hidden';
    holder.innerHTML = svg;
    document.body.prepend(holder);
  });</code></pre>
  <p>
    Icon names, sizes and the two weights are on the <a href="../components/icon.php">icon
    page</a>.
  </p>

  <h3 id="emoji">Emoji</h3>
  <p>
    Nothing to install. An emoji is a text character, drawn by the emoji font already on the
    reader's device, so there is no emoji file in Deck and none to download. What Deck
    supplies is <code>.emoji</code>, which names the colour emoji font stack and fixes the
    size and baseline so the character sits on the line with its text. The
    <a href="../components/emoji.php">emoji page</a> has the sizes and tiles.
  </p>

  <h3 id="javascript">JavaScript</h3>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Which components need which script</caption>
      <thead>
        <tr><th scope="col">File</th><th scope="col">What needs it</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="File"><code>deck.js</code></th><td data-label="What needs it">Toasts, the theme switch, <code>Deck.hue()</code>, the date picker, the combobox, the data grid, count-up numbers, flip cards, back-to-top, and pairing each popover with the button that opens it so the browser's anchor positioning can place it.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck-extras.js</code></th><td data-label="What needs it">The carousel, the drawer, the mega menu, the copy button, QR codes and the rich-text editor. Load it after <code>deck.js</code>.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck-adapters.js</code></th><td data-label="What needs it">Nothing on its own. When Floating UI, Quill, Tiptap, Chart.js, SortableJS or Lucide is already on the page, it hands that job to the library. Load it after <code>deck.js</code>.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck.bundle.min.js</code></th><td data-label="What needs it">All three, in that order, in one file.</td></tr>
      </tbody>
    </table>
  </div>
  <p>
    Everything else is CSS and needs no script at all: the layout, buttons, forms, tables,
    cards, the dialog-based overlays, <code>&lt;details&gt;</code> accordions and tabs. The
    <a href="../reference/javascript.php">JavaScript reference</a> lists every attribute and
    method.
  </p>

  <h3 id="brand">Brand and favicon</h3>
  <p>
    Deck's logo files are in <code>dist/brand/</code> of the package. No route copies them;
    take the ones you use.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The brand files in the package</caption>
      <thead>
        <tr><th scope="col">File</th><th scope="col">For</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="File"><code>deck-mark.svg</code></th><td data-label="For">The favicon: the mark alone, with its own light and dark colours. There is no <code>favicon.svg</code>; this is it.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck-apple-touch-icon.png</code></th><td data-label="For">180 × 180, for <code>rel="apple-touch-icon"</code>.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck-logo.svg</code>, <code>deck-logo-stacked.svg</code></th><td data-label="For">The lockup, horizontal and stacked, drawn in <code>currentColor</code>.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck-logo-light.svg</code>, <code>deck-logo-dark.svg</code></th><td data-label="For">The lockup with a fixed fill, for light and for dark backgrounds.</td></tr>
        <tr><th scope="row" data-label="File"><code>deck-og.png</code></th><td data-label="For">A 1200 × 630 social card.</td></tr>
      </tbody>
    </table>
  </div>
  <pre class="dx-code"><code>node_modules/@echodial/deck/dist/brand/     npm and bundlers
vendor/echodial/deck/dist/brand/            Composer
<?= e($CDN) ?>/brand/deck-mark.svg</code></pre>
  <p>
    Put the favicon beside Deck's other files and link it:
  </p>
  <pre class="dx-code"><code>&lt;link rel="icon" href="/assets/deck/deck-mark.svg" type="image/svg+xml"&gt;</code></pre>
  <p>
    For a logo that follows <code>--hue-brand</code>, use the sprite instead of a file. A
    logo in an <code>&lt;img&gt;</code> or a <code>&lt;link&gt;</code> has no colour context,
    so its <code>currentColor</code> comes out black, which is why the fixed-fill files
    exist. The marks are filled rather than stroked, hence <code>.icon-fill</code>:
  </p>
  <pre class="dx-code"><code>&lt;svg class="icon icon-lg icon-fill" style="color:var(--brand)"&gt;
  &lt;use href="/assets/deck/deck-icons.svg#deck-mark"&gt;&lt;/use&gt;
&lt;/svg&gt;</code></pre>
</section>

<section class="stack-3">
  <h2 id="without">Without Node or Composer</h2>
  <div class="accordion">
    <details>
      <summary>Download or copy the files by hand</summary>
      <div class="accordion-body stack-3">
        <p>
          Only if none of the four routes fits. Download the four files the check page
          loads:
        </p>
        <pre class="dx-code"><code># public_html on cPanel, Plesk and Helm; public on Laravel and Symfony
root=public_html
curl -sSL --create-dirs -o $root/assets/deck/deck.css <?= e($CDN) ?>/deck.css
curl -sSL --create-dirs -o $root/assets/deck/deck-icons.svg <?= e($CDN) ?>/deck-icons.svg
curl -sSL --create-dirs -o $root/assets/deck/deck.js <?= e($CDN) ?>/deck.js
curl -sSL --create-dirs -o $root/assets/deck/deck-extras.js <?= e($CDN) ?>/deck-extras.js</code></pre>
        <p>
          Or, with Deck already in <code>vendor/</code> and no scripts in
          <code>composer.json</code>, copy the same four out of it:
        </p>
        <pre class="dx-code"><code># public_html on cPanel, Plesk and Helm; public on Laravel and Symfony
root=public_html
mkdir -p $root/assets/deck
for f in deck.css deck-icons.svg deck.js deck-extras.js; do cp "vendor/echodial/deck/dist/$f" "$root/assets/deck/"; done</code></pre>
        <p>
          Set <code>root</code> to the folder your web server serves first. Both are for a
          POSIX shell: Git Bash on Windows, macOS, or Linux. The check page works unchanged
          afterwards. Repeat the download or the copy after every upgrade.
        </p>
      </div>
    </details>
  </div>
</section>

<section class="stack-3">
  <h2 id="trouble">If something did not work</h2>
  <dl class="stack-3">
    <dt><strong>The page is unstyled: plain black Times New Roman</strong></dt>
    <dd>
      The stylesheet did not load. Open the network tab and find the request for
      <code>deck.css</code>, or <code>deck.min.css</code> on the CDN. A 404 there is nearly
      always a path: the page links <code>/assets/…</code> from the site root, and the site is
      served from a subfolder, or the files went to the wrong folder.
    </dd>
    <dt><strong>Everything is styled but every icon is an empty box</strong></dt>
    <dd>
      The sprite is not reachable from the page's own origin. Either it points at a CDN, which
      <a href="#icons">never works</a>, or the file is not where the <code>href</code> says.
      Open the sprite's URL in the browser before you check anything else.
    </dd>
    <dt><strong>The button looks right but the toast and the slider do nothing</strong></dt>
    <dd>
      The script is missing or failed to parse. Type <code>Deck</code> into the console: if it
      says <code>undefined</code>, the script never ran. If you see a warning reading
      <em>load deck.js first</em>, the script tags are in the wrong order.
    </dd>
    <dt><strong>A bundler stops with <em>Export 'globalThis' is not defined</em></strong></dt>
    <dd>
      You wrote <code>import Deck from '@echodial/deck'</code>, which does not build in 0.1.2.
      <a href="#bundler">Import <code>@echodial/deck/bundle</code></a> and use
      <code>window.Deck</code>.
    </dd>
    <dt><strong>webpack says <em>"./icons?url" is not exported</em>, or that <em>'import' and 'export' may appear only with 'sourceType: module'</em></strong></dt>
    <dd>
      Drop the <code>?url</code> and let the <code>asset/resource</code> rule make the import a
      URL. For the second message, name the entry <code>.mjs</code>.
      <a href="#webpack">Both are shown above.</a>
    </dd>
    <dt><strong>Composer says <em>Command "deck-publish" is not defined</em></strong></dt>
    <dd>
      The scripts are not in your <code>composer.json</code>. Composer never runs the ones
      inside a package. <a href="#composer">Add the two blocks.</a>
    </dd>
    <dt><strong>The page is dark and you expected light</strong></dt>
    <dd>
      That is correct behaviour: Deck follows the operating system's colour scheme with no
      configuration. <a href="../guides/dark-mode.php">Add a theme switch</a> if you want a
      person to be able to override it.
    </dd>
    <dt><strong>It looks right but everything is a bit too big or too small</strong></dt>
    <dd>
      Deck sizes in <code>rem</code>, so it follows the browser's font size on purpose. That is
      a reader's setting, and Deck respects it rather than overriding it.
    </dd>
  </dl>
</section>

<section class="stack-3">
  <h2 id="next">Next</h2>
  <p>
    You have a page that loads Deck. The next tutorial builds something real with it: a full
    account settings page, typed line by line, in one sitting.
  </p>
  <a class="btn btn-primary" href="first-page.php">Build your first page</a>
</section>

<?php docs_footer(); ?>
