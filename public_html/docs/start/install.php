<?php
declare(strict_types=1);

/**
 * Install — the first tutorial.
 *
 * Every command on this page was run in an empty directory before the page was
 * written, and the transcript in the "What that looks like" blocks is the real
 * output rather than a reconstruction. Where a channel is not live yet it says
 * so instead of printing a command that 404s: an install page that fails on
 * step one costs more trust than every other page on the site can earn back.
 */

$page = [
    'path' => 'start/install.php',
    'title' => 'Install Deck',
    'level' => 'Beginner',
    'description' => "Get Deck onto a page in about a minute: download two files, or copy them from a clone. No build step, no config file, and nothing to compile.",
];

require __DIR__ . '/../_layout.php';

$RAW = 'https://raw.githubusercontent.com/srivera145/deck/main/dist';
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
    Deck is one stylesheet and one icon sprite. There is no build step, no config file
    and no dependencies, so installing it means putting files in a folder and adding two
    tags. This takes about a minute, and at the end of it you will have a page that
    proves it worked.
  </p>
</header>

<section class="stack-3">
  <h2 id="status">Before you start</h2>
  <div class="alert alert-warn">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#alert-triangle"></use></svg>
    <div>
      <div class="alert-title">Deck is pre-release: npm and Packagist are not live yet</div>
      <p class="alert-body">
        <code>npm install</code>, <code>npx</code> and <code>composer require</code> will
        all fail today, because the package has not been published to either registry.
        The two methods below use the public Git repository and both work right now. The
        registry instructions are on this page too, marked as pending, so you know what
        will change and nothing here sends you at a 404.
      </p>
    </div>
  </div>
</section>

<section class="stack-4">
  <h2 id="download">Method 1: download the files</h2>
  <p>
    The shortest path, and the one to use if you are not running Node in this project at
    all. You need two files to see anything, and two more if you want the behaviour that
    CSS cannot do on its own.
  </p>

  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The files to download and what each one is for</caption>
      <thead>
        <tr><th scope="col">File</th><th scope="col">Size</th><th scope="col">Needed for</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="File"><code>deck.min.css</code></th>
          <td data-label="Size">169 KB</td>
          <td data-label="Needed for">Everything visual. This is the framework.</td>
        </tr>
        <tr>
          <th scope="row" data-label="File"><code>deck-icons.svg</code></th>
          <td data-label="Size">107 KB</td>
          <td data-label="Needed for">The icon sprite. Without it every <code>.icon</code> is an empty box.</td>
        </tr>
        <tr>
          <th scope="row" data-label="File"><code>deck.js</code></th>
          <td data-label="Size">53 KB</td>
          <td data-label="Needed for">Date picker, combobox, data grid, toasts, theme toggle. Optional.</td>
        </tr>
        <tr>
          <th scope="row" data-label="File"><code>deck-extras.js</code></th>
          <td data-label="Size">32 KB</td>
          <td data-label="Needed for">Carousel, drawer, mega menu, copy button, QR codes. Optional.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <p>
    From the directory you want them in:
  </p>
  <pre class="dx-code"><code>mkdir -p assets/deck

curl -sSL -o assets/deck/deck.min.css   <?= e($RAW) ?>/deck.min.css
curl -sSL -o assets/deck/deck-icons.svg <?= e($RAW) ?>/deck-icons.svg
curl -sSL -o assets/deck/deck.js        <?= e($RAW) ?>/deck.js
curl -sSL -o assets/deck/deck-extras.js <?= e($RAW) ?>/deck-extras.js</code></pre>

  <p>
    On Windows without curl, or if you would rather not paste shell commands, open each
    URL in a browser and save the file. They are plain static files on
    <code>raw.githubusercontent.com</code>; there is nothing to sign in to.
  </p>

  <p class="dx-note">
    <strong>Keep <code>deck-icons.svg</code> beside <code>deck.js</code>.</strong> The
    script resolves the sprite against its own URL at load time, so as long as the two
    sit in the same folder nothing needs configuring. If your build puts them apart, set
    <code>data-deck-icons</code> on the script tag — see the
    <a href="../reference/javascript.php#attributes">JavaScript reference</a>.
  </p>
</section>

<section class="stack-4">
  <h2 id="clone">Method 2: copy from a clone</h2>
  <p>
    Use this if you want the source, the docs and the CLI as well as the built files —
    or if you expect to update Deck more than once. The repository ships
    <code>dist/</code> already built, so there is nothing to compile after cloning.
  </p>
  <pre class="dx-code"><code>git clone --depth 1 https://github.com/srivera145/deck.git deck-src

# then, from your own project directory
node ../deck-src/bin/deck.mjs init assets/deck</code></pre>

  <p>
    <code>init</code> copies the files, skips anything already there, and prints the tags
    to paste. It takes <code>--min</code> for the minified stylesheet,
    <code>--bundle</code> for one combined script instead of three,
    <code>--css-only</code> to skip the scripts, and <code>--force</code> to overwrite.
    The full command reference is on the <a href="../reference/cli.php">CLI page</a>.
  </p>

  <h3 id="transcript">What that looks like</h3>
  <p>
    This is the actual output, from an empty directory, with the colour codes stripped:
  </p>
  <pre class="dx-code"><code>$ git clone --depth 1 https://github.com/srivera145/deck.git deck-src
Cloning into 'deck-src'...

$ mkdir mysite &amp;&amp; cd mysite
$ node ../deck-src/bin/deck.mjs init assets/deck
  + assets\deck\deck.css
  + assets\deck\deck-icons.svg
  + assets\deck\deck.js
  + assets\deck\deck-extras.js
  + assets\deck\deck-adapters.js

Add to your layout:

  &lt;link rel="stylesheet" href="/assets/deck/deck.css"&gt;
  &lt;script src="/assets/deck/deck.js" defer&gt;&lt;/script&gt;

deck-icons.svg sits beside deck.js and is found automatically.</code></pre>

  <p class="dx-note">
    Two things about that output worth knowing before they confuse you. The path
    separators are backslashes because this was run on Windows. And the printed
    <code>href</code> starts with a slash, which is a URL from the site root — correct if
    you serve the project directory as the web root, wrong if your site lives in a
    subfolder. Check it resolves in the browser before assuming the stylesheet is broken.
  </p>
</section>

<section class="stack-4">
  <h2 id="page">Add the tags</h2>
  <p>
    Make an <code>index.html</code> beside the <code>assets</code> folder and type this.
    It is the whole install: one stylesheet, two optional scripts, and a viewport tag
    that Deck's mobile-first layout assumes.
  </p>
  <pre class="dx-code"><code>&lt;!doctype html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
&lt;meta charset="utf-8"&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"&gt;
&lt;title&gt;Deck is installed&lt;/title&gt;
&lt;link rel="stylesheet" href="assets/deck/deck.min.css"&gt;
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

  &lt;button class="btn" onclick="Deck.toast({kind:'good', title:'JavaScript works too'})"&gt;
    Fire a toast
  &lt;/button&gt;
&lt;/main&gt;

&lt;/body&gt;
&lt;/html&gt;</code></pre>

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
  <pre class="dx-code"><code>python -m http.server 8000
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
      '</div>',
      'The check page, rendered here by the same stylesheet you just installed',
      'stack'
  ); ?>
  <p>
    Press <strong>Fire a toast</strong> on your own copy. A notification should slide in
    from the corner and dismiss itself after five seconds. That tells you the scripts
    loaded as well as the stylesheet, which is the half people usually get wrong.
  </p>
</section>

<section class="stack-4">
  <h2 id="pending">The registry methods, when they land</h2>
  <p>
    Neither of these works today. They are here so you can recognise them when they do,
    and so nobody has to guess what the published names will be.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Distribution channels and their current status</caption>
      <thead>
        <tr><th scope="col">Channel</th><th scope="col">Command</th><th scope="col">Status</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Channel">Git</th>
          <td data-label="Command"><code>git clone …/srivera145/deck.git</code></td>
          <td data-label="Status"><span class="badge badge-good">Works now</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Channel">Direct download</th>
          <td data-label="Command"><code>curl … raw.githubusercontent.com/…</code></td>
          <td data-label="Status"><span class="badge badge-good">Works now</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Channel">npm</th>
          <td data-label="Command"><code>npm install @echodial/deck</code></td>
          <td data-label="Status"><span class="badge badge-warn">Pending release</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Channel">npx</th>
          <td data-label="Command"><code>npx @echodial/deck init</code></td>
          <td data-label="Status"><span class="badge badge-warn">Pending release</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Channel">Composer</th>
          <td data-label="Command"><code>composer require echodial/deck</code></td>
          <td data-label="Status"><span class="badge badge-warn">Pending release</span></td>
        </tr>
      </tbody>
    </table>
  </div>
  <p class="dx-note">
    The CLI's own <code>starter</code> command prints a hint telling you to run
    <code>npx @echodial/deck init</code>. That hint is written for the published future
    and does not work yet — use <code>node ../deck-src/bin/deck.mjs init</code> instead.
    Once Deck is on npm the two are the same command.
  </p>
</section>

<section class="stack-3">
  <h2 id="trouble">If something did not work</h2>
  <dl class="stack-3">
    <dt><strong>The page is unstyled — plain black Times New Roman</strong></dt>
    <dd>
      The stylesheet 404'd. Open the network tab and look at the request for
      <code>deck.min.css</code>. Nine times in ten the <code>href</code> is root-relative
      (<code>/assets/…</code>) and the site is served from a subfolder, or the other way
      round.
    </dd>
    <dt><strong>Everything is styled but every icon is an empty box</strong></dt>
    <dd>
      Either you are on <code>file://</code> — serve the folder instead — or
      <code>deck-icons.svg</code> did not get downloaded. Check the folder listing before
      you check anything else.
    </dd>
    <dt><strong>The button looks right but the toast does nothing</strong></dt>
    <dd>
      <code>deck.js</code> is missing or failed to parse. Type <code>Deck</code> into the
      console: if it says <code>undefined</code> the script never ran. If you see a
      warning reading <em>load deck.js first</em>, the two script tags are in the wrong
      order — <code>deck-extras.js</code> extends the core and has to come after it.
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
