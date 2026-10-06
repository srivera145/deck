<?php
declare(strict_types=1);

/**
 * Set up your editor — installing and using Deck IntelliSense.
 *
 * The extension lives in tools/vscode/ and ships as a .vsix in the repository.
 * It is not on the Visual Studio Marketplace or Open VSX yet, so every route on
 * this page installs that one file. It has been run in Visual Studio Code and in
 * Antigravity. Cursor uses the same extension API and installs a .vsix the same
 * way, but nobody has checked it there yet, and the page says so rather than
 * letting the editor list imply it.
 *
 * The version, the counts, the language list and the settings table are read
 * from tools/vscode/ at render, for the same reason every other number on this
 * site is read rather than typed: the extension regenerates its data on each
 * Deck release, and a page that restates it by hand is wrong by the next one.
 *
 * The one example of a wrong class is written with entities rather than as a
 * class attribute, so neither the build's class verifier nor the extension itself
 * flags a mistake that is there on purpose.
 */

$page = [
    'path' => 'start/editor.php',
    'title' => 'Set up your editor',
    'level' => 'Beginner',
    'description' => "Install Deck IntelliSense in Visual Studio Code, Cursor or Antigravity: class, token and icon completion, hover documentation, and the typos that look almost right underlined as you type.",
];

require __DIR__ . '/../_layout.php';

/* tools/ sits beside public_html/, three levels up from this file. */
$readJson = static function (string $rel): ?array {
    $file = dirname(__DIR__, 3) . '/' . $rel;
    if (!is_readable($file)) {
        return null;
    }
    $json = json_decode((string) file_get_contents($file), true);
    return is_array($json) ? $json : null;
};

$extPkg  = $readJson('tools/vscode/package.json') ?? [];
$extData = $readJson('tools/vscode/data/deck-data.json') ?? [];

$extVersion = $extPkg['version'] ?? '0.1.0';
$vsix       = 'deck-intellisense-' . $extVersion . '.vsix';
$vsixUrl    = 'https://github.com/srivera145/deck/raw/main/tools/vscode/' . $vsix;
$counts     = $extData['counts'] ?? null;
$settings   = $extPkg['contributes']['configuration']['properties'] ?? [];

/* The languages it wakes up for, as the editor names them. */
$languages = array_values(array_map(
    static fn(string $ev): string => substr($ev, strlen('onLanguage:')),
    array_filter($extPkg['activationEvents'] ?? [], static fn(string $ev): bool => str_starts_with($ev, 'onLanguage:'))
));

$showDefault = static function ($v): string {
    if (is_bool($v)) {
        return $v ? 'true' : 'false';
    }
    if (is_array($v)) {
        return '[]';
    }
    return $v === '' ? '""' : (string) $v;
};
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Start</li>
    <li aria-current="page">Set up your editor</li>
  </ol>
</nav>

<header class="stack stack-3">
  <h1>Set up your editor</h1>
  <p class="lede">
    Deck IntelliSense completes Deck's class names, tokens and icon names as you type,
    shows what a class actually declares when you hover over it, and underlines the
    mistakes that look almost right — a spacing step Deck does not have, a class one
    letter off — before they reach a browser. It runs in Visual Studio Code and in the
    editors built on it.
  </p>
</header>

<div class="alert alert-info">
  <svg class="icon" aria-hidden="true"><use href="../../assets/deck/deck-icons.svg#info"></use></svg>
  <div>
    <div class="alert-title">Not in an extension marketplace yet</div>
    <p class="alert-body">
      Searching the Extensions view for Deck finds nothing. The extension is one
      <code>.vsix</code> file in Deck's repository, and you install it from the file. It
      also means the editor will not update it for you: see <a href="#update">updating</a>.
    </p>
  </div>
</div>

<section class="stack stack-4">
  <h2 id="editors">Which editors</h2>
  <p>
    Any editor built on Visual Studio Code can run it, because it is written against the
    VS Code extension API and nothing else. Editors with their own plugin systems cannot.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Editors Deck IntelliSense runs in, and whether each has been checked</caption>
      <thead>
        <tr><th scope="col">Editor</th><th scope="col">Status</th><th scope="col">Notes</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Editor">Visual Studio Code</th>
          <td data-label="Status"><span><span class="badge badge-good">Tested</span></span></td>
          <td data-label="Notes"><span>Needs 1.84 or later.</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Editor">Antigravity</th>
          <td data-label="Status"><span><span class="badge badge-good">Tested</span></span></td>
          <td data-label="Notes"><span>Install from the Command Palette, as below.</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Editor">Cursor</th>
          <td data-label="Status"><span><span class="badge badge-warn">Expected to work</span></span></td>
          <td data-label="Notes"><span>Same extension API and the same install command. Not yet checked.</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Editor">Other VS Code editors</th>
          <td data-label="Status"><span><span class="badge badge-warn">Expected to work</span></span></td>
          <td data-label="Notes"><span>Windsurf, VSCodium and the like install a <code>.vsix</code> the same way. Not checked.</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Editor">JetBrains, Zed, Sublime Text, Neovim</th>
          <td data-label="Status"><span><span class="badge">Not supported</span></span></td>
          <td data-label="Notes"><span>Different plugin systems. The <a href="../reference/classes.php">class reference</a> is the substitute.</span></td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section class="stack stack-4">
  <h2 id="install">Install it</h2>
  <ol class="stack stack-3" style="list-style:decimal;padding-inline-start:var(--space-6)">
    <li>
      <!-- The stack is on a child, not the li: a flex li loses its list number. -->
      <div class="stack stack-2">
        <span>
          <strong>Download the extension.</strong> It is
          <code><?= e($vsix) ?></code>, in <code>tools/vscode/</code> of Deck's repository.
          If you cloned the repository, it is already on your disk.
        </span>
        <span>
          <a class="btn btn-primary" href="<?= e($vsixUrl) ?>">
            <svg class="icon icon-sm" aria-hidden="true"><use href="../../assets/deck/deck-icons.svg#download-sm"></use></svg>
            Download <?= e($vsix) ?>
          </a>
        </span>
      </div>
    </li>
    <li>
      <strong>Open the Command Palette.</strong>
      <kbd>Ctrl</kbd>+<kbd>Shift</kbd>+<kbd>P</kbd> on Windows and Linux,
      <kbd>Cmd</kbd>+<kbd>Shift</kbd>+<kbd>P</kbd> on macOS. The shortcut is the same in
      Visual Studio Code, Cursor and Antigravity.
    </li>
    <li>
      <strong>Run <em>Extensions: Install from VSIX…</em></strong> and choose the file you
      downloaded. The same command is also in the <strong>…</strong> menu at the top of the
      Extensions view.
    </li>
    <li>
      <strong>Reload the window if the editor asks.</strong> The extension starts the next
      time you open an HTML, PHP or template file.
    </li>
  </ol>

  <p>Or from a terminal, in the folder you downloaded it to:</p>
  <pre class="dx-code"><code><?= e("# Visual Studio Code\ncode --install-extension " . $vsix . "\n\n# Cursor\ncursor --install-extension " . $vsix) ?></code></pre>
  <p class="dx-note text-muted">
    Both commands need the editor's shell command on your <code>PATH</code>. If the
    terminal says the command is not found, use the Command Palette route above, or run
    <em>Shell Command: Install 'code' command in PATH</em> (or Cursor's equivalent) from
    the Command Palette first.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="check">Check that it works</h2>
  <ol class="stack stack-3" style="list-style:decimal;padding-inline-start:var(--space-6)">
    <li>
      <strong>Ask it what it loaded.</strong> Run <em>Deck: Show loaded API version and
      counts</em> from the Command Palette.
      <?php if ($counts): ?>
        It answers with one line, along the lines of
        <em>Deck 0.1.x — <?= (int) $counts['public'] ?> public classes,
        <?= (int) $counts['internal'] ?> internal, <?= (int) $counts['removed'] ?> removed,
        <?= (int) $counts['tokens'] ?> tokens, <?= (int) $counts['icons'] ?> icons.</em>
      <?php else: ?>
        It answers with the Deck version it describes and how many classes, tokens and
        icons it knows.
      <?php endif; ?>
      If the command is not there, the extension is not installed.
    </li>
    <li>
      <strong>Type a class.</strong> In any HTML file, type <code>class="btn-</code>. A list
      of every button class appears, each with the cascade layer it belongs to and the
      declarations it sets.
    </li>
    <li>
      <strong>Make a mistake on purpose.</strong> Give a stack a spacing step Deck does not
      have:
    </li>
  </ol>
  <pre class="dx-code"><code>&lt;div class=&quot;stack stack-5&quot;&gt;</code></pre>
  <p>
    <code>stack-5</code> is underlined in red: Deck's spacing scale is 0, 1, 2, 3, 4, 6
    and 8. The quick fix offers both neighbours, <code>stack-4</code> and
    <code>stack-6</code>, because 5 sits exactly between them and either could be what you
    meant. This is the bug the extension exists for. <code>.stack</code> on its own is a
    real class with a default gap, so <code>stack-5</code> never fails — the page looks
    nearly right and nothing tells you.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="features">What it does</h2>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">What Deck IntelliSense adds to the editor</caption>
      <thead>
        <tr><th scope="col">Feature</th><th scope="col">Where</th><th scope="col">What you get</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Feature">Class completion</th>
          <td data-label="Where"><span>Inside <code>class</code> and <code>className</code></span></td>
          <td data-label="What you get"><span>
            <?= $counts ? (int) $counts['public'] . ' public classes' : 'Every public class' ?>,
            each with its layer and the declarations it sets.
          </span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Feature">Token completion</th>
          <td data-label="Where"><span>Inside <code>var(</code></span></td>
          <td data-label="What you get"><span>
            <?= $counts ? 'All ' . (int) $counts['tokens'] . ' custom properties' : 'Every custom property' ?>,
            with a colour swatch on the colours and the resolved value on the rest.
          </span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Feature">Icon completion</th>
          <td data-label="Where"><span>After <code>deck-icons.svg#</code></span></td>
          <td data-label="What you get"><span>
            <?= $counts ? 'All ' . (int) $counts['icons'] . ' symbols' : 'Every symbol in the sprite' ?>,
            with the markup to paste.
          </span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Feature">Hover</th>
          <td data-label="Where"><span>On any class or token</span></td>
          <td data-label="What you get"><span>What it declares, which cascade layer owns it, its siblings in the family, and the source file and line.</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Feature">Go to definition</th>
          <td data-label="Where"><span>On any class</span></td>
          <td data-label="What you get"><span>Opens the rule in Deck's <code>src/</code>. See <a href="#source">where it looks</a>.</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Feature">Diagnostics</th>
          <td data-label="Where"><span>As you type</span></td>
          <td data-label="What you get"><span>The problems in the next table, with a quick fix wherever there is a right answer.</span></td>
        </tr>
      </tbody>
    </table>
  </div>

  <h3 id="reports">What it reports</h3>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The problems Deck IntelliSense reports, and how severe each is</caption>
      <thead>
        <tr><th scope="col">Case</th><th scope="col">Severity</th><th scope="col">Example</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Case">A number off a real scale</th>
          <td data-label="Severity"><span>Error, with fixes</span></td>
          <td data-label="Example"><span><code>gap-5</code>, <code>p-7</code>, <code>stack-5</code></span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Case">A class removed from Deck</th>
          <td data-label="Severity"><span>Error, with the replacement</span></td>
          <td data-label="Example"><span><code>is-full</code> → <code>w-full</code></span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Case">One character off a real class</th>
          <td data-label="Severity"><span>Error, with a fix</span></td>
          <td data-label="Example"><span><code>btn-primery</code> → <code>btn-primary</code></span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Case">A class internal to Deck</th>
          <td data-label="Severity"><span>Warning</span></td>
          <td data-label="Example"><span><code>carousel-dot</code>, which Deck's JavaScript draws for you</span></td>
        </tr>
        <tr>
          <th scope="row" data-label="Case">An <code>is-*</code> state class written by hand</th>
          <td data-label="Severity"><span>Hint</span></td>
          <td data-label="Example"><span><code>is-copied</code>, which Deck's JavaScript sets</span></td>
        </tr>
      </tbody>
    </table>
  </div>

  <h3 id="silent">What it leaves alone</h3>
  <p>
    Your own classes. With the default setting a class is reported only when it looks like
    a Deck class gone wrong, so <code>site-header</code>, <code>hero</code> and
    <code>row</code> stay silent. A typo needs at least five characters before it is
    matched at all, because below that almost any string is one edit from something.
  </p>
  <p>
    If a project uses Deck classes and nothing else, set
    <code>deck.diagnostics.unknownClasses</code> to <code>all</code> and every class Deck
    does not define is reported. Set it to <code>off</code> and none are.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="languages">Languages</h2>
  <p>
    It switches on for markup and template files, and for stylesheets, where it completes
    tokens:
  </p>
  <?php if ($languages): ?>
    <ul class="cluster cluster-tight" style="list-style:none;padding:0;margin:0" aria-label="Language IDs">
      <?php foreach ($languages as $lang): ?>
        <li><code><?= e($lang) ?></code></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  <p>
    These are the editor's language IDs, the name shown in the bottom-right corner of the
    window. For any other templating language, add its ID to <code>deck.languages</code>,
    for example <code>["liquid", "njk"]</code>.
  </p>
  <p>
    Template expressions are blanked out before anything is checked, so a PHP echo, Blade
    braces or a JavaScript interpolation inside a <code>class</code> attribute is never
    mistaken for a class name. Classes inside those expressions are skipped rather than
    guessed at.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="source">Go to definition</h2>
  <p>
    Jumping from a class to its rule needs Deck's source on disk. The extension looks in
    <code>node_modules/@echodial/deck/src</code> and <code>vendor/echodial/deck/src</code>
    in each folder of your workspace, which covers the npm and Composer installs. If you
    copied Deck into your project by hand, or keep it somewhere else, set
    <code>deck.sourceRoot</code> to the folder that holds its <code>src/</code> files.
    Completion, hover and diagnostics do not need the source; they carry their own copy of
    the class list.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="settings">Settings</h2>
  <p>
    Search for <em>Deck</em> in the editor's Settings, or add these to
    <code>settings.json</code>.
  </p>
  <?php if ($settings): ?>
    <div class="table-wrap">
      <table class="table table-stack">
        <caption class="sr-only">Every Deck IntelliSense setting, its default and what it does</caption>
        <thead>
          <tr><th scope="col">Setting</th><th scope="col">Default</th><th scope="col">What it does</th></tr>
        </thead>
        <tbody>
          <?php foreach ($settings as $key => $s): ?>
            <tr>
              <th scope="row" data-label="Setting"><code><?= e($key) ?></code></th>
              <td data-label="Default"><span><code><?= e($showDefault($s['default'] ?? '')) ?></code></span></td>
              <td data-label="What it does"><span><?= e(strip_tags(str_replace('`', '', $s['description'] ?? $s['markdownDescription'] ?? ''))) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
  <p class="dx-note text-muted">
    Two commands go with them: <em>Deck: Show loaded API version and counts</em>, and
    <em>Deck: Re-check all open files</em> after you change a setting.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="update">Updating</h2>
  <p>
    Each Deck release can add, rename or remove classes, and the extension's class list is
    generated from the release it was built against. Because it was installed from a file,
    nothing updates it: when you upgrade Deck, download the new <code>.vsix</code> from the
    same folder and install it the same way. It replaces the old one in place, settings and
    all.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="trouble">If something is off</h2>
  <dl class="stack stack-4">
    <div class="stack stack-1">
      <dt><strong>No suggestions appear</strong></dt>
      <dd>
        Check the language shown in the bottom-right corner of the window against the
        <a href="#languages">list above</a>, and add it to <code>deck.languages</code> if it
        is missing. Then check <code>deck.enable</code> is on.
      </dd>
    </div>
    <div class="stack stack-1">
      <dt><strong>One of your own classes is underlined</strong></dt>
      <dd>
        It is one character away from a Deck class. <code>swatch</code> is one letter from
        Deck's <code>switch</code>, so it is reported as a typo. Rename it — that is what
        Deck's own templates did — or set <code>deck.diagnostics.unknownClasses</code> to
        <code>off</code>.
      </dd>
    </div>
    <div class="stack stack-1">
      <dt><strong>A class Deck ships is underlined as internal</strong></dt>
      <dd>
        Internal classes are pieces of markup Deck's JavaScript writes for itself. The
        warning means the name may change in a patch release, not that it is broken. Turn
        it off with <code>deck.diagnostics.internalClasses</code> if you are writing that
        markup on purpose.
      </dd>
    </div>
    <div class="stack stack-1">
      <dt><strong>Go to definition does nothing</strong></dt>
      <dd>
        It cannot find Deck's source. <a href="#source">Set <code>deck.sourceRoot</code></a>.
      </dd>
    </div>
    <div class="stack stack-1">
      <dt><strong><em>Install from VSIX</em> is not in the Command Palette</strong></dt>
      <dd>
        The editor is not built on Visual Studio Code, so the extension cannot run in it.
        <a href="#editors">Check the list.</a>
      </dd>
    </div>
  </dl>
</section>

<section class="stack stack-3">
  <h2 id="next">Next</h2>
  <p>
    With the editor finishing class names for you, build something: a full account
    settings page, typed line by line, in one sitting.
  </p>
  <div class="cluster">
    <a class="btn btn-primary" href="first-page.php">Build your first page</a>
    <a class="btn" href="../../templates/index.php">Or start from a template</a>
  </div>
</section>

<?php docs_footer(); ?>
