<?php
declare(strict_types=1);

/**
 * Install — the first tutorial.
 *
 * Deck is on npm as @echodial/deck and on Packagist as echodial/deck, and
 * jsDelivr and unpkg mirror npm. Each path below was run in an empty directory
 * against the published 0.1.1 packages, and the output shown is what that run
 * printed, with colour codes stripped. An install page that fails on step one
 * costs more trust than every other page on the site can earn back, so when a
 * command or its output changes, run it again and paste the new output rather
 * than editing the old.
 *
 * The CDN URL is pinned to @0.1 everywhere, never @latest: a floating tag moves
 * a reader's page onto the next breaking release without anyone deciding to.
 */

$page = [
    'path' => 'start/install.php',
    'title' => 'Install Deck',
    'level' => 'Beginner',
    'description' => "Install Deck four ways: a CDN link, npm, npx or Composer. Each path has the exact command and the output it prints. No build step and no config file.",
];

require __DIR__ . '/../_layout.php';

/* Pinned to the minor version. See "Pin the version" under the CDN section. */
$CDN = 'https://cdn.jsdelivr.net/npm/@echodial/deck@0.1/dist';
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
    Deck is one stylesheet and one icon sprite, published to npm as
    <code>@echodial/deck</code> and to Packagist as <code>echodial/deck</code>. There is no
    build step and no config file, so every way of installing it ends the same way: the
    files in a folder your site serves, and two tags in your page. This takes about a
    minute, and at the end of it you will have a page that proves it worked.
  </p>
</header>

<section class="stack-3">
  <h2 id="choose">Pick a path</h2>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The four ways to install Deck and when each one fits</caption>
      <thead>
        <tr><th scope="col">If your project</th><th scope="col">Use</th><th scope="col">Command</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="If your project">has no package manager, or you are trying Deck out</th>
          <td data-label="Use"><a href="#cdn">CDN</a></td>
          <td data-label="Command">a <code>&lt;link&gt;</code> to jsDelivr</td>
        </tr>
        <tr>
          <th scope="row" data-label="If your project">bundles its CSS and JavaScript from npm</th>
          <td data-label="Use"><a href="#npm">npm</a></td>
          <td data-label="Command"><code>npm install @echodial/deck</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="If your project">serves plain files, and Node is on your machine</th>
          <td data-label="Use"><a href="#npx">npx</a></td>
          <td data-label="Command"><code>npx @echodial/deck init</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="If your project">is PHP</th>
          <td data-label="Use"><a href="#composer">Composer</a></td>
          <td data-label="Command"><code>composer require echodial/deck</code></td>
        </tr>
      </tbody>
    </table>
  </div>
  <p>
    All four end with the files under <code>public/assets/deck/</code>, so the page you
    build at the end is the same whichever you pick. If your site's public folder has
    another name, use it wherever this page says <code>public</code>.
  </p>
</section>

<section class="stack-4">
  <h2 id="cdn">CDN: nothing to install</h2>
  <p>
    jsDelivr and unpkg mirror every version published to npm. Put these two tags in your
    page's <code>&lt;head&gt;</code>. The script is optional; it drives the components CSS
    cannot do alone.
  </p>
  <pre class="dx-code"><code>&lt;link rel="stylesheet" href="<?= e($CDN) ?>/deck.min.css"&gt;
&lt;script src="<?= e($CDN) ?>/deck.bundle.min.js" data-deck-icons="assets/deck/deck-icons.svg" defer&gt;&lt;/script&gt;</code></pre>

  <h3 id="pin">Pin the version</h3>
  <p>
    <code>@0.1</code> follows every 0.1.x release, so fixes arrive without an edit, and it
    never moves to 0.2. Do not write <code>@latest</code>, and do not leave the version
    off, which means the same thing: either one moves every page that uses it onto the next
    breaking release the day it is published, with nothing in your own code to show why the
    page changed.
  </p>
  <p>
    unpkg serves the same files at the same paths, as
    <code>https://unpkg.com/@echodial/deck@0.1/dist/deck.min.css</code>, if you would
    rather use it.
  </p>

  <div class="alert alert-warn">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#alert-triangle"></use></svg>
    <div>
      <div class="alert-title">The icon sprite cannot come from the CDN</div>
      <p class="alert-body">
        Browsers refuse an SVG <code>&lt;use&gt;</code> whose <code>href</code> is on
        another origin, so an icon that points at jsDelivr draws nothing. Save the sprite
        into your own site once, and point <code>data-deck-icons</code> at it, as the script
        tag above does.
      </p>
    </div>
  </div>
  <pre class="dx-code"><code>curl -sSL --create-dirs -o public/assets/deck/deck-icons.svg <?= e($CDN) ?>/deck-icons.svg</code></pre>
  <p>
    That is the only file this path puts on disk.
  </p>
</section>

<section class="stack-4">
  <h2 id="npm">npm: for a bundler</h2>
  <pre class="dx-code"><code>npm install @echodial/deck</code></pre>
  <p>
    That is the whole output below. Deck has no dependencies, so npm adds exactly one
    package:
  </p>
  <pre class="dx-code"><code>$ npm install @echodial/deck

added 1 package in 1s</code></pre>
  <p>
    Then import it from the entry point that imports your other styles:
  </p>
  <pre class="dx-code"><code>import '@echodial/deck/css';
import Deck from '@echodial/deck';</code></pre>
  <p>
    Both resolve through the package's <code>exports</code> map, to
    <code>dist/deck.css</code> and <code>dist/deck.esm.js</code>. The map also offers
    <code>@echodial/deck/css/min</code>, <code>@echodial/deck/icons</code>, and
    <code>@echodial/deck/layers/*</code> for one cascade layer at a time. The
    <a href="../reference/javascript.php">JavaScript reference</a> covers what
    <code>Deck</code> exposes.
  </p>
  <p class="dx-note">
    <strong>The sprite still has to be a file.</strong> Whatever your bundler does with the
    CSS, an icon's <code>&lt;use&gt;</code> needs <code>deck-icons.svg</code> at a URL on
    your own site. The package includes the CLI, so the next section's command copies it
    out of <code>node_modules</code> for you.
  </p>
</section>

<section class="stack-4">
  <h2 id="npx">npx: copy the files into a project</h2>
  <p>
    For a site that serves plain files. <code>npx</code> fetches the package and runs its
    <code>init</code> command, and your project ends up with the copied files and nothing
    else: no <code>package.json</code>, no <code>node_modules</code>.
  </p>
  <pre class="dx-code"><code>npx @echodial/deck init public/assets/deck</code></pre>

  <h3 id="npx-output">What that prints</h3>
  <p>
    From an empty directory, with the colour codes stripped:
  </p>
  <pre class="dx-code"><code>$ npx @echodial/deck init public/assets/deck
npm warn exec The following package was not found and will be installed: @echodial/deck@0.1.1
  + public\assets\deck\deck.css
  + public\assets\deck\deck-icons.svg
  + public\assets\deck\deck.js
  + public\assets\deck\deck-extras.js
  + public\assets\deck\deck-adapters.js

Add to your layout:

  &lt;link rel="stylesheet" href="/assets/deck/deck.css"&gt;
  &lt;script src="/assets/deck/deck.js" defer&gt;&lt;/script&gt;

deck-icons.svg sits beside deck.js and is found automatically.
To load the sprite from somewhere else:

  &lt;script src="/assets/deck/deck.js" data-deck-icons="/your/path/deck-icons.svg" defer&gt;&lt;/script&gt;</code></pre>

  <p class="dx-note">
    Three things in that output are worth knowing before they confuse you. The
    <code>npm warn exec</code> line is npx saying it had to download the package; in an
    interactive terminal it asks first, and you answer <kbd>y</kbd>. The path separators
    are backslashes because this run was on Windows. And the printed <code>href</code> has
    no <code>public</code> in it, because <code>init</code> takes that folder to be your
    site's document root, which is not part of the URL. If your site serves the project
    folder itself, put <code>public</code> back.
  </p>
  <p>
    <code>init</code> copies the files, skips any already there, and prints the tags to
    paste. It takes <code>--min</code> for the minified stylesheet, <code>--bundle</code>
    for one combined script instead of three, <code>--css-only</code> to skip the scripts,
    and <code>--force</code> to overwrite. The full command reference is on the
    <a href="../reference/cli.php">CLI page</a>.
  </p>
</section>

<section class="stack-4">
  <h2 id="composer">Composer: for a PHP project</h2>
  <p>
    <code>composer require echodial/deck</code> installs Deck into <code>vendor/</code> and
    does nothing else. A browser cannot read <code>vendor/</code>, and Composer never runs
    scripts from a package you install, only the ones in your own
    <code>composer.json</code>. So a bare install copies no assets, and
    <code>composer deck-publish</code> is not a command yet. From an empty directory:
  </p>
  <pre class="dx-code"><code>$ composer require echodial/deck
./composer.json has been created
Running composer update echodial/deck
Loading composer repositories with package information
Updating dependencies
Lock file operations: 1 install, 0 updates, 0 removals
  - Locking echodial/deck (v0.1.1)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Downloading echodial/deck (v0.1.1)
  - Installing echodial/deck (v0.1.1): Extracting archive
Generating autoload files
No security vulnerability advisories found.
Using version ^0.1.1 for echodial/deck

$ composer deck-publish

  Command "deck-publish" is not defined.
</code></pre>
  <p>
    Nothing was written outside <code>vendor/</code> except <code>composer.json</code> and
    <code>composer.lock</code>. Get the files into your public folder one of three ways.
  </p>

  <h3 id="composer-scripts">1. Opt-in scripts</h3>
  <p>
    Add these two blocks to your <code>composer.json</code>, then run
    <code>composer require echodial/deck</code>:
  </p>
  <pre class="dx-code"><code>"scripts": {
  "post-install-cmd": ["EchoDial\\Deck\\Installer::postInstall"],
  "post-update-cmd": ["EchoDial\\Deck\\Installer::postInstall"],
  "deck-publish": "EchoDial\\Deck\\Installer::publish"
},
"extra": {
  "deck": {
    "publish-to": "public/assets/deck",
    "auto-publish": true
  }
}</code></pre>

  <div class="alert alert-warn">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#alert-triangle"></use></svg>
    <div>
      <div class="alert-title">Set publish-to to the folder your web server serves</div>
      <p class="alert-body">
        The default, <code>public/assets/deck</code>, is right for Laravel and Symfony,
        whose document root is <code>public/</code>. On cPanel hosting and Helm sites the
        document root is <code>public_html/</code>, so use
        <code>public_html/assets/deck</code>. Deck cannot tell which folder is served; if
        <code>publish-to</code> names the wrong one, the files land where no URL reaches.
      </p>
    </div>
  </div>

  <p>
    From an empty directory holding only those two blocks:
  </p>
  <pre class="dx-code"><code>$ composer require echodial/deck
./composer.json has been updated
Running composer update echodial/deck
Loading composer repositories with package information
Updating dependencies
Lock file operations: 1 install, 0 updates, 0 removals
  - Locking echodial/deck (v0.1.1)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Downloading echodial/deck (v0.1.1)
  - Installing echodial/deck (v0.1.1): Extracting archive
Generating autoload files
&gt; EchoDial\Deck\Installer::postInstall
Deck 0.1.1 published to public/assets/deck (8 copied, 0 unchanged)
  Add to your layout:
    &lt;link rel="stylesheet" href="/assets/deck/deck.css"&gt;
    &lt;script src="/assets/deck/deck.js" defer&gt;&lt;/script&gt;
No security vulnerability advisories found.
Using version ^0.1.1 for echodial/deck</code></pre>
  <p>
    Every <code>composer install</code> and <code>composer update</code> after that
    publishes again, skipping files that have not changed. If Deck was already installed
    when you added the scripts, run <code>composer deck-publish</code> once;
    <code>composer deck-publish -- public/static/deck</code> publishes somewhere else for
    one run. With <code>auto-publish</code> left out or <code>false</code>, the install and
    update hooks print a reminder instead of copying, and <code>composer deck-publish</code>
    is the only thing that publishes.
  </p>
  <p class="dx-note">
    <strong>Take the scripts out if you remove Deck.</strong> Composer keeps calling them,
    and without the class every install prints <em>Class EchoDial\Deck\Installer is not
    autoloadable, can not call post-install-cmd script</em>. The command still succeeds, but
    the line will puzzle whoever reads it next.
  </p>

  <h3 id="composer-copy">2. Copy by hand</h3>
  <p>
    No scripts at all. After <code>composer require</code>:
  </p>
  <pre class="dx-code"><code>mkdir -p public/assets/deck
cp -r vendor/echodial/deck/dist/* public/assets/deck/</code></pre>
  <p>
    That copies everything in <code>dist/</code>: 50 files and 3.2 MB, where the page
    below loads four. <code>api.json</code> alone is 1.4 MB of build data that no browser
    requests, so delete what you do not serve if the size matters. Run the copy again after
    every <code>composer update</code>, or the files fall behind the package. The commands
    are for a POSIX shell: Git Bash on Windows, macOS, or Linux.
  </p>

  <h3 id="composer-npx">3. npx</h3>
  <p>
    If Node is on the machine, the <a href="#npx">npx command</a> copies the five files a
    page uses without touching <code>composer.json</code>. It takes them from npm rather
    than from <code>vendor/</code>, so make sure the version it installs matches the one in
    your <code>composer.lock</code>.
  </p>
  <p>
    Whichever way the files arrive, the <a href="../reference/php.php">PHP helper</a> can
    write the tags for you, with cache-busting URLs.
  </p>
</section>

<section class="stack-4">
  <h2 id="page">Add the tags</h2>
  <p>
    Make <code>public/index.html</code>, beside the <code>assets</code> folder, and type
    this. It is the whole install: one stylesheet, two optional scripts, and a viewport tag
    that Deck's mobile-first layout assumes.
  </p>
  <pre class="dx-code"><code>&lt;!doctype html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
&lt;meta charset="utf-8"&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"&gt;
&lt;title&gt;Deck is installed&lt;/title&gt;
&lt;link rel="stylesheet" href="assets/deck/deck.css"&gt;
&lt;script src="assets/deck/deck.js" defer&gt;&lt;/script&gt;
&lt;script src="assets/deck/deck-extras.js" defer&gt;&lt;/script&gt;
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
      &lt;use href="assets/deck/deck-icons.svg#check-circle"&gt;&lt;/use&gt;
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
    On the CDN path, swap the stylesheet and the two scripts for the two CDN tags
    <a href="#cdn">above</a>. The sprite path stays as it is, because the sprite is on
    your own site.
  </p>
  <p>
    <code>viewport-fit=cover</code> is not decoration. It is what lets
    <code>.safe-top</code> and <code>.safe-bottom</code> reach around the notch and the
    home indicator on a phone. Leave it out and those become no-ops.
  </p>
</section>

<section class="stack-4">
  <h2 id="serve">Open it</h2>
  <p>
    Serve the folder rather than opening the file directly. Icons are the reason: an
    <code>&lt;svg&gt;&lt;use href="…"&gt;</code> pointing at an external sprite is blocked
    by the browser's file-origin rules, so on <code>file://</code> every icon comes out
    empty while everything else looks fine — a confusing five minutes.
  </p>
  <pre class="dx-code"><code>cd public
python -m http.server 8000
# or
npx serve .
# or, if the project is PHP
php -S localhost:8000</code></pre>
  <p>
    Then open <code>http://localhost:8000</code>. You should see this:
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
      'The check page, rendered here by the same stylesheet you just installed',
      'stack'
  ); ?>
  <p>
    Press <strong>Fire a toast</strong> on your own copy. A notification should slide in
    from the corner and dismiss itself after five seconds. That tells you the scripts
    loaded as well as the stylesheet, which is the half people usually get wrong.
  </p>
  <p>
    Then drag the slider. The button and the icon change colour together, because every
    brand colour in Deck is computed from one custom property, <code>--hue-brand</code>,
    and <code>Deck.hue()</code> sets it. The slider in the example above does the same to
    this page.
  </p>
</section>

<section class="stack-3">
  <h2 id="trouble">If something did not work</h2>
  <dl class="stack-3">
    <dt><strong>The page is unstyled — plain black Times New Roman</strong></dt>
    <dd>
      The stylesheet did not load. Open the network tab and find the request for
      <code>deck.css</code>, or <code>deck.min.css</code> on the CDN. A 404 there is nearly
      always a path: the <code>href</code> is root-relative (<code>/assets/…</code>) and the
      site is served from a subfolder, or the other way round. On the CDN, check the URL
      still says <code>@echodial/deck@0.1</code>.
    </dd>
    <dt><strong>Everything is styled but every icon is an empty box</strong></dt>
    <dd>
      The sprite is not reachable from the page's own origin. Either you are on
      <code>file://</code> — serve the folder instead — or the <code>&lt;use&gt;</code>
      points at another origin such as the CDN, or <code>deck-icons.svg</code> was never
      copied. Open the sprite's URL in the browser before you check anything else.
    </dd>
    <dt><strong>The button looks right but the toast and the slider do nothing</strong></dt>
    <dd>
      The script is missing or failed to parse. Type <code>Deck</code> into the console:
      if it says <code>undefined</code> the script never ran. If you see a warning reading
      <em>load deck.js first</em>, the two script tags are in the wrong order —
      <code>deck-extras.js</code> extends the core and has to come after it. The CDN path
      loads one bundle, so it cannot hit that one.
    </dd>
    <dt><strong>The page is dark and you expected light</strong></dt>
    <dd>
      That is correct behaviour: Deck follows the operating system's colour scheme with
      no configuration. <a href="../guides/dark-mode.php">Add a theme switch</a> if you
      want a person to be able to override it.
    </dd>
    <dt><strong>It looks right but everything is a bit too big or too small</strong></dt>
    <dd>
      Deck sizes in <code>rem</code>, so it follows the browser's font size on purpose.
      That is a reader's setting and Deck respects it rather than overriding it.
    </dd>
  </dl>
</section>

<section class="stack-3">
  <h2 id="next">Next</h2>
  <p>
    You have a page that loads Deck. The next tutorial builds something real with it —
    a full account settings page, typed line by line, in one sitting.
  </p>
  <a class="btn btn-primary" href="first-page.php">Build your first page</a>
</section>

<?php docs_footer(); ?>
