<?php
declare(strict_types=1);

/**
 * The command line reference. Hand-written.
 *
 * Three commands and four flags, which is the whole of it. The page is longer
 * than that sounds because the useful part is what each command does when the
 * ground is not clean — a file already there, a dist that was never built, a
 * target directory that is not the URL you think it is.
 */

$page = [
    'path' => 'reference/cli.php',
    'title' => 'CLI',
    'level' => 'Beginner',
    'description' => "The deck command: copy the assets into a project, write a starter page, or list what the package ships, with every flag and what each does when a file is already there.",
];

require __DIR__ . '/../_layout.php';

$COMMANDS = [
    [
        'id' => 'init',
        'usage' => 'npx @echodial/deck init [dir]',
        'summary' => 'Copies Deck\'s built assets out of the package and into your project, then'
            . ' prints the tags to paste into your layout. This is the whole install for anyone'
            . ' not using npm as a build tool, which is most of the people Deck is for.',
        'default' => 'assets/deck, relative to the current directory.',
        'behaviour' => [
            'Creates the target directory, including any parents.',
            'Skips a file that is already there, and says how many it skipped. Pass <code>--force</code> to overwrite.',
            'Counts a file that is not in <code>dist/</code> as missing rather than failing, and tells you to run the build.',
            'Prints the exact <code>&lt;link&gt;</code> and <code>&lt;script&gt;</code> tags for what it copied.',
        ],
        'gotcha' => 'The snippet it prints strips a leading <code>public_html/</code>,'
            . ' <code>public/</code>, <code>web/</code>, <code>htdocs/</code>,'
            . ' <code>httpdocs/</code>, <code>www/</code>, <code>wwwroot/</code>,'
            . ' <code>dist/</code> or <code>static/</code> from the path, because the folder on'
            . ' disk is not the folder in the URL. If your document root is named something else,'
            . ' the printed path will have that segment in it and you will need to take it out'
            . ' yourself.',
        'example' => "npx @echodial/deck init public_html/assets/deck\n\n"
            . "  + public_html/assets/deck/deck.css\n"
            . "  + public_html/assets/deck/deck-icons.svg\n"
            . "  + public_html/assets/deck/deck.js\n"
            . "  + public_html/assets/deck/deck-extras.js\n"
            . "  + public_html/assets/deck/deck-adapters.js\n\n"
            . "Add to your layout:\n\n"
            . "  <link rel=\"stylesheet\" href=\"/assets/deck/deck.css\">\n"
            . "  <script src=\"/assets/deck/deck.js\" defer></script>",
    ],
    [
        'id' => 'starter',
        'usage' => 'npx @echodial/deck starter [file]',
        'summary' => 'Writes a complete starter HTML page: a skip link, a sticky navbar with a'
            . ' working theme toggle, a hero, a card grid, a form field, a live brand-hue slider'
            . ' and a toast button. It is a page that already does something rather than an empty'
            . ' shell.',
        'default' => 'index.html, in the current directory.',
        'behaviour' => [
            'Refuses to overwrite an existing file, and says so. Pass <code>--force</code> to replace it.',
            'Creates any parent directories the path needs.',
            'Assumes the assets are at <code>/assets/deck/</code>. Run <code>init</code> first, or edit the two paths at the top.',
            'Includes an <code>@layer app.pages</code> block, so the first CSS you write is already in the right place to beat Deck without <code>!important</code>.',
        ],
        'gotcha' => 'The page it writes is a demonstration, not a template to build a product on'
            . ' top of unchanged — it has an inline <code>oninput</code> and an inline'
            . ' <code>onclick</code> in it, which are fine for showing what a line of code does'
            . ' and are not how you should write an application.',
        'example' => "npx @echodial/deck starter public_html/index.html\n\n"
            . "  + public_html/index.html",
    ],
    [
        'id' => 'list',
        'usage' => 'npx @echodial/deck list',
        'summary' => 'Lists what is actually in the installed package\'s <code>dist/</code>, with'
            . ' each file\'s size. Useful for deciding what to copy, and for confirming a build'
            . ' produced what you expected.',
        'default' => 'Nothing to pass.',
        'behaviour' => [
            'Reads the package\'s own <code>dist/</code>, not your project.',
            'Sizes are the raw bytes on disk, not compressed — the number your server sends is smaller.',
            'Directories are listed with a trailing slash and not recursed into.',
        ],
        'gotcha' => 'An empty or short listing means the package was installed without a build.'
            . ' Published releases ship <code>dist/</code>; a git clone does not, and needs'
            . ' <code>npm run build</code> first.',
        'example' => "npx @echodial/deck list\n\n"
            . "  deck.css                   311.6 KB\n"
            . "  deck.min.css               172.8 KB\n"
            . "  deck-icons.svg             110.0 KB\n"
            . "  deck.js                     54.0 KB",
    ],
];

$FLAGS = [
    ['--min', 'init', 'Copy <code>deck.min.css</code> instead of <code>deck.css</code>, and print the minified name in the snippet. The readable build is easier to learn from; the minified one is what you deploy.'],
    ['--bundle', 'init', 'Copy one combined <code>deck.bundle.min.js</code> instead of the three separate scripts. One request instead of three, at the cost of sending the adapter code to pages that do not use it.'],
    ['--css-only', 'init', 'Skip the scripts entirely. Everything HTML and CSS can carry on their own still works — the dialog-based overlays, <code>&lt;details&gt;</code> accordions, tabs, forms, the whole layout.'],
    ['--force', 'init, starter', 'Overwrite files that already exist. Without it both commands leave what is there alone.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">CLI</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>CLI</h1>
  <p class="lede">
    One command, three subcommands, four flags. It exists because Deck has no build step
    and most of the people it is for are not running one — they want the files in a folder
    and a tag to paste. <code>npx</code> runs it without installing anything.
  </p>
</header>

<section class="stack-4">
  <h2 id="quickstart">The whole thing</h2>
  <pre class="dx-code"><code>npx @echodial/deck init public_html/assets/deck
npx @echodial/deck starter public_html/index.html</code></pre>
  <p>
    That is a working page. Open it and every component in Deck is available to it, with
    no config file and nothing to compile. If you are using npm properly you can also
    <code>npm install @echodial/deck</code> and point your bundler at
    <code>@echodial/deck/css</code> — the CLI is for the other case.
  </p>
  <p>
    Running it with no arguments, or with a subcommand it does not recognise, prints the
    usage summary and exits cleanly. Piping the output into <code>head</code> or
    <code>less</code> is handled: the broken pipe is treated as the end of the output
    rather than as a crash.
  </p>
</section>

<section class="stack-6">
  <h2 id="commands">Commands</h2>
  <?php foreach ($COMMANDS as $c): ?>
    <article class="stack-3" id="<?= e($c['id']) ?>">
      <h3><code><?= e($c['usage']) ?></code></h3>
      <p><?= $c['summary'] ?></p>
      <dl class="stack-2">
        <dt><strong>Default argument</strong></dt>
        <dd><?= e($c['default']) ?></dd>
        <dt><strong>What it does</strong></dt>
        <dd>
          <ul class="stack-1">
            <?php foreach ($c['behaviour'] as $b): ?>
              <li><?= $b ?></li>
            <?php endforeach; ?>
          </ul>
        </dd>
        <dt><strong>Worth knowing</strong></dt>
        <dd><?= $c['gotcha'] ?></dd>
      </dl>
      <pre class="dx-code"><code><?= e($c['example']) ?></code></pre>
    </article>
  <?php endforeach; ?>
</section>

<section class="stack-4">
  <h2 id="flags">Flags</h2>
  <p>
    Flags are position-independent — anywhere after the subcommand is fine — and unknown
    ones are ignored rather than rejected.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Command line flags</caption>
      <thead>
        <tr><th scope="col">Flag</th><th scope="col">Applies to</th><th scope="col">What it does</th></tr>
      </thead>
      <tbody>
        <?php foreach ($FLAGS as [$flag, $applies, $what]): ?>
          <tr>
            <th scope="row" data-label="Flag"><code><?= e($flag) ?></code></th>
            <td data-label="Applies to"><code class="dx-dim"><?= e($applies) ?></code></td>
            <td data-label="What it does"><?= $what ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-4">
  <h2 id="files">What init copies</h2>
  <p>
    The set depends on the flags. <code>deck-icons.svg</code> is always included, because
    the icon component is useless without it and the script resolves the sprite against
    its own location — so as long as the two land in the same folder, nothing needs
    configuring.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Which files each flag combination copies</caption>
      <thead>
        <tr><th scope="col">Invocation</th><th scope="col">Files</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Invocation"><code>init</code></th>
          <td data-label="Files"><code>deck.css deck-icons.svg deck.js deck-extras.js deck-adapters.js</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="Invocation"><code>init --min</code></th>
          <td data-label="Files"><code>deck.min.css deck-icons.svg deck.js deck-extras.js deck-adapters.js</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="Invocation"><code>init --bundle</code></th>
          <td data-label="Files"><code>deck.css deck-icons.svg deck.bundle.min.js</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="Invocation"><code>init --min --bundle</code></th>
          <td data-label="Files"><code>deck.min.css deck-icons.svg deck.bundle.min.js</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="Invocation"><code>init --css-only</code></th>
          <td data-label="Files"><code>deck.css deck-icons.svg</code></td>
        </tr>
      </tbody>
    </table>
  </div>
  <p class="dx-note">
    To load the sprite from somewhere else — a CDN, or a hashed asset path — put
    <code>data-deck-icons</code> on the script tag. <code>init</code> prints that line as
    a reminder every time it runs.
  </p>
  <pre class="dx-code"><code>&lt;script src="/assets/deck/deck.js" data-deck-icons="/cdn/deck-icons.abc123.svg" defer&gt;&lt;/script&gt;</code></pre>
</section>

<section class="stack-4">
  <h2 id="composer">The Composer equivalent</h2>
  <p>
    A PHP project does not need the Node CLI at all. <code>composer require
    echodial/deck</code> publishes the same files into your public directory on install
    and update, and <code>composer deck-publish</code> runs it again on demand. That path
    is documented in full on the <a href="php.php">PHP helper</a> page.
  </p>
</section>

<section class="stack-4">
  <h2 id="scripts">Scripts in the package itself</h2>
  <p>
    These are for working <em>on</em> Deck rather than with it, and they run in a clone of
    the repository rather than through <code>npx</code>.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">npm scripts in the Deck repository</caption>
      <thead>
        <tr><th scope="col">Script</th><th scope="col">What it does</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Script"><code>npm run build</code></th>
          <td data-label="What it does">Concatenates <code>src/*.css</code> into <code>dist/</code>, minifies, bundles the scripts, and writes the size report. Needed after a clone, because <code>dist/</code> is not in git.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Script"><code>npm run demo</code></th>
          <td data-label="What it does">Builds, then publishes the result into <code>public_html/assets/</code> so the demo and these docs are running against the current source.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Script"><code>npm start</code></th>
          <td data-label="What it does">Serves <code>public_html/</code> on port 4321 with PHP's built-in server.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Script"><code>npm run clean</code></th>
          <td data-label="What it does">Removes <code>dist/</code>.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Script"><code>node tools/docs/extract.mjs</code></th>
          <td data-label="What it does">Regenerates <code>dist/api.json</code> — every class, token, layer and source line — which is what every table on this site is built from.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Script"><code>node tools/docs/verify.mjs</code></th>
          <td data-label="What it does">Fails the build if a page claims a class that does not exist, uses one that does not exist, or if a new class was added with no documentation and no backlog entry.</td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-3">
  <h2 id="troubleshooting">When it does not do what you expected</h2>
  <ul class="stack-3">
    <li>
      <strong>It says files are "not built".</strong> The installed package has no
      <code>dist/</code>. That is normal in a git clone — run <code>npm run build</code>
      in the package. A release from npm always ships it.
    </li>
    <li>
      <strong>It says everything was already there.</strong> That is the skip behaviour,
      not a failure. Add <code>--force</code> to overwrite.
    </li>
    <li>
      <strong>The page loads but has no styling.</strong> The path in the printed snippet
      is a URL, and it assumed a standard document root name. Check that the
      <code>href</code> actually resolves from the browser.
    </li>
    <li>
      <strong>Icons are empty boxes.</strong> <code>deck-icons.svg</code> is not beside
      <code>deck.js</code>. Either put it there or set
      <code>data-deck-icons</code> on the script tag.
    </li>
    <li>
      <strong>Nothing interactive works.</strong> The scripts were skipped —
      <code>--css-only</code>, or the tags were not pasted in. Everything that needs
      script is listed at the end of the
      <a href="javascript.php">JavaScript reference</a>.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
