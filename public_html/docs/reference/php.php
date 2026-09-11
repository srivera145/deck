<?php
declare(strict_types=1);

/**
 * The PHP helper reference. Hand-written, like the other two API pages.
 *
 * The class is small enough that a generated signature list would be shorter
 * than this page and much less useful: what people get wrong with it is
 * configuration — where `root` points, why `bust` silently falls back — and a
 * reflection dump has nothing to say about that.
 */

$page = [
    'path' => 'reference/php.php',
    'title' => 'PHP helper',
    'level' => 'Intermediate',
    'description' => "Every method on the EchoDial Deck PHP class: configuration, asset URLs with cache busting, the head tags, per-tenant theming, icons, and the Composer installer.",
];

require __DIR__ . '/../_layout.php';

/** Same shape as the JavaScript reference, so the two read alike. */
function php_method(array $m): void
{
    ?>
    <article class="stack-3" id="<?= e($m['id']) ?>">
      <h3><code><?= e($m['signature']) ?></code></h3>
      <p><?= $m['summary'] ?></p>

      <?php if (!empty($m['params'])): ?>
        <div class="table-wrap">
          <table class="table table-stack">
            <caption class="sr-only">Parameters for <?= e($m['signature']) ?></caption>
            <thead>
              <tr><th scope="col">Parameter</th><th scope="col">Type</th><th scope="col">Meaning</th></tr>
            </thead>
            <tbody>
              <?php foreach ($m['params'] as [$pname, $ptype, $pdesc]): ?>
                <tr>
                  <th scope="row" data-label="Parameter"><code><?= e($pname) ?></code></th>
                  <td data-label="Type"><code class="dx-dim"><?= e($ptype) ?></code></td>
                  <td data-label="Meaning"><?= $pdesc ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <dl class="stack-2">
        <dt><strong>Returns</strong></dt>
        <dd><?= $m['returns'] ?></dd>
        <dt><strong>When what it needs is missing</strong></dt>
        <dd><?= $m['absent'] ?></dd>
      </dl>

      <pre class="dx-code"><code><?= e($m['example']) ?></code></pre>
    </article>
    <?php
}

$OPTIONS = [
    ['base', "'/assets/deck'", 'The public URL prefix the assets are served from. Everything else is built on this.'],
    ['root', 'null', 'The filesystem path that <code>base</code> hangs off, used to read mtimes and to check which files exist. Falls back to <code>$_SERVER["DOCUMENT_ROOT"]</code>.'],
    ['minify', 'null', '<code>true</code> or <code>false</code> to force the choice. <code>null</code> decides by looking for <code>deck.min.css</code> on disk.'],
    ['bust', 'true', 'Append <code>?v=</code> to asset URLs, from the file mtime where the file can be found and from the package version otherwise.'],
    ['extras', 'true', 'Emit the <code>deck-extras.js</code> tag.'],
    ['adapters', 'false', 'Emit the <code>deck-adapters.js</code> tag. Off by default because it does nothing unless one of the optional libraries is on the page.'],
    ['bundle', 'false', 'Emit one combined script instead of two or three tags.'],
    ['defer', 'true', 'Put <code>defer</code> on the script tags.'],
    ['hue', 'null', 'An OKLCH hue angle emitted as an inline <code>:root</code> style from <code>head()</code>.'],
    ['theme', 'null', "<code>'light'</code> or <code>'dark'</code>, emitted as <code>data-theme</code> by <code>htmlAttributes()</code>. <code>null</code> follows the operating system."],
];

$METHODS = [
    [
        'id' => 'configure',
        'signature' => 'Deck::configure(array $options): void',
        'summary' => 'Merges options into the configuration. Call it once, early — before'
            . ' anything else on this page, since every other method reads from it.',
        'params' => [
            ['$options', 'array', 'Any of the keys in the table above. Anything you leave out keeps its default.'],
        ],
        'returns' => 'Nothing.',
        'absent' => 'The configuration is static, so it is per-request and shared across the'
            . ' whole request. Calling it twice merges rather than replaces, which means you'
            . ' cannot unset a key by omitting it — pass the default back explicitly. There is no'
            . ' validation: an unknown key is stored and ignored, so a typo is silent.',
        'example' => "use EchoDial\\Deck\\Deck;\n\n"
            . "Deck::configure([\n"
            . "    'base'   => '/assets/deck',\n"
            . "    'root'   => __DIR__,        // this file's directory is the web root\n"
            . "    'extras' => true,\n"
            . "]);",
    ],
    [
        'id' => 'head',
        'signature' => 'Deck::head(bool $viewport = true): string',
        'summary' => 'Everything for the <code>&lt;head&gt;</code>, in one call: the viewport meta'
            . ' tag Deck\'s mobile-first layout assumes, the stylesheet link, any theme override,'
            . ' the script tags, and a small inline script pointing <code>Deck.iconSprite</code>'
            . ' at the right URL.',
        'params' => [
            ['$viewport', 'bool', 'Pass <code>false</code> if your layout already emits the viewport meta tag. Emitting it twice is harmless but untidy.'],
        ],
        'returns' => 'An HTML string of several tags separated by newlines. Not escaped — it is'
            . ' markup, and it is meant to be echoed raw.',
        'absent' => 'The viewport tag includes <code>viewport-fit=cover</code>, which is what makes'
            . ' <code>.safe-top</code> and <code>.safe-bottom</code> do anything on a notched'
            . ' phone. Suppress it with <code>false</code> and you must include that yourself or'
            . ' the safe-area utilities become no-ops.',
        'example' => "<head>\n"
            . "  <meta charset=\"utf-8\">\n"
            . "  <title>Orders</title>\n"
            . "  <?= Deck::head() ?>\n"
            . "</head>",
    ],
    [
        'id' => 'css',
        'signature' => 'Deck::css(): string',
        'summary' => 'The stylesheet <code>&lt;link&gt;</code> tag on its own, for a layout that'
            . ' wants to place the scripts somewhere else.',
        'params' => [],
        'returns' => 'One <code>&lt;link rel="stylesheet"&gt;</code> tag with the URL escaped.',
        'absent' => 'Which file it links is <code>stylesheet()</code>\'s decision. The URL carries'
            . ' a cache-busting query where one could be worked out.',
        'example' => "<?= Deck::css() ?>\n"
            . "<!-- <link rel=\"stylesheet\" href=\"/assets/deck/deck.min.css?v=1725900000\"> -->",
    ],
    [
        'id' => 'js',
        'signature' => 'Deck::js(): string',
        'summary' => 'The script tags, in the right order, honouring the'
            . ' <code>extras</code>, <code>adapters</code>, <code>bundle</code> and'
            . ' <code>defer</code> options.',
        'params' => [],
        'returns' => 'One to three <code>&lt;script&gt;</code> tags separated by newlines.',
        'absent' => 'With <code>bundle</code> on, it looks for <code>deck.bundle.min.js</code> on'
            . ' disk and falls back to the unminified bundle when it is not there — so a project'
            . ' that only published the readable build still gets a working tag rather than a 404.'
            . ' That check needs <code>root</code> to be right.',
        'example' => "Deck::configure(['bundle' => true]);\n"
            . "echo Deck::js();\n"
            . "// <script src=\"/assets/deck/deck.bundle.min.js?v=…\" defer></script>",
    ],
    [
        'id' => 'asset',
        'signature' => 'Deck::asset(string $file): string',
        'summary' => 'The public URL for one file under <code>base</code>, with a cache-busting'
            . ' query where one can be worked out. This is what everything else here calls.',
        'params' => [
            ['$file', 'string', "A filename such as <code>'deck-icons.svg'</code>."],
        ],
        'returns' => 'The URL, with <code>?v=</code> appended unless <code>bust</code> is off.',
        'absent' => 'The version comes from the file\'s mtime, which requires finding it on disk:'
            . ' <code>root</code> if you set it, otherwise <code>$_SERVER["DOCUMENT_ROOT"]</code>.'
            . ' On a CLI request, or behind a server that does not set that, or where the path'
            . ' does not resolve, it falls back to the package version instead — so the URL is'
            . ' still stable and still works, but a deploy that does not change the version will'
            . ' not bust the cache. If cache busting matters, set <code>root</code> explicitly.',
        'example' => "Deck::asset('deck-icons.svg');\n"
            . "// '/assets/deck/deck-icons.svg?v=1725900000'",
    ],
    [
        'id' => 'stylesheet',
        'signature' => 'Deck::stylesheet(): string',
        'summary' => 'Which stylesheet filename Deck should link.',
        'params' => [],
        'returns' => "<code>'deck.min.css'</code> or <code>'deck.css'</code>.",
        'absent' => 'With <code>minify</code> at its default of <code>null</code>, it checks'
            . ' whether <code>deck.min.css</code> exists under <code>root</code>. Where'
            . ' <code>root</code> cannot be worked out, that check fails and it returns the'
            . ' unminified name — correct, but larger than you wanted. Set <code>minify</code> to'
            . ' <code>true</code> in production and the check is skipped.',
        'example' => "Deck::stylesheet();   // 'deck.min.css' in production",
    ],
    [
        'id' => 'htmlAttributes',
        'signature' => 'Deck::htmlAttributes(?string $lang = null, ?string $dir = null): string',
        'summary' => 'Attributes for the <code>&lt;html&gt;</code> tag: language, direction, and'
            . ' the configured theme.',
        'params' => [
            ['$lang', '?string', "The document language, such as <code>'en'</code>."],
            ['$dir', '?string', "<code>'ltr'</code> or <code>'rtl'</code>."],
        ],
        'returns' => 'A space-separated attribute string with no leading space, ready to drop'
            . ' straight into the tag. Empty when nothing is configured.',
        'absent' => 'Only emits <code>data-theme</code> when the <code>theme</code> option is not'
            . ' null. Leaving it null is usually right: the operating system decides, and the'
            . ' script restores a saved choice before first paint.',
        'example' => "<html <?= Deck::htmlAttributes(lang: 'en', dir: 'ltr') ?>>",
    ],
    [
        'id' => 'theme',
        'signature' => 'Deck::theme(?int $hue = null, ?string $mode = null, ?float $chroma = null): string',
        'summary' => 'The per-tenant theming this whole framework was built for: one inline style'
            . ' on the <code>&lt;html&gt;</code> tag, no rebuild, no second stylesheet, and the'
            . ' right colours in the very first paint.',
        'params' => [
            ['$hue', '?int', 'An OKLCH hue angle, 0 to 360, emitted as <code>--hue-brand</code>.'],
            ['$mode', '?string', "<code>'light'</code> or <code>'dark'</code>, emitted as <code>data-theme</code>."],
            ['$chroma', '?float', 'Emitted as <code>--chroma-brand</code>. Lower is more muted. Trailing zeros are trimmed.'],
        ],
        'returns' => 'A <code>style</code> attribute, a <code>data-theme</code> attribute, both,'
            . ' or an empty string. Values are escaped.',
        'absent' => 'Every argument is optional and a null one is simply omitted, so a tenant with'
            . ' no brand colour and a user with no theme preference produce an empty string and'
            . ' the defaults apply. Because it is server-rendered there is no flash of the wrong'
            . ' brand colour, which is the reason to prefer it over <code>Deck.hue()</code> in'
            . ' JavaScript.',
        'example' => "<html <?= Deck::htmlAttributes(lang: 'en') ?>\n"
            . "      <?= Deck::theme(hue: \$tenant->hue, mode: \$user->theme) ?>>\n"
            . "<!-- <html lang=\"en\" style=\"--hue-brand:265\" data-theme=\"dark\"> -->",
    ],
    [
        'id' => 'icon',
        'signature' => 'Deck::icon(string $name, string $class = "icon", ?string $label = null): string',
        'summary' => 'One <code>&lt;svg&gt;&lt;use&gt;</code> pointing at Deck\'s sprite.',
        'params' => [
            ['$name', 'string', "The icon id, such as <code>'check-circle'</code>."],
            ['$class', 'string', 'Classes for the svg. <code>icon-sm</code>, <code>icon-lg</code> and <code>icon-fill</code> are the usual additions.'],
            ['$label', '?string', 'An accessible name. Omit it for a decorative icon.'],
        ],
        'returns' => 'An SVG element as a string. The name, the class and the sprite URL are all'
            . ' escaped.',
        'absent' => 'With no <code>$label</code> the icon gets <code>aria-hidden="true"</code>,'
            . ' which is right when there is text beside it and wrong when the icon is the only'
            . ' content — an icon-only button with no label is announced as "button". Pass'
            . ' <code>$label</code> there, or put the words in a <code>.sr-only</code> span.'
            . ' A name that is not in the sprite renders an empty svg rather than an error, so'
            . ' check the spelling if an icon simply does not appear.',
        'example' => "<?= Deck::icon('check-circle', 'icon icon-lg', 'Paid') ?>\n"
            . "<?= Deck::icon('chevron-down') ?>   <!-- decorative -->",
    ],
    [
        'id' => 'config',
        'signature' => 'Deck::config(string $key, mixed $default = null): mixed',
        'summary' => 'Reads one configuration value back.',
        'params' => [
            ['$key', 'string', 'The option name.'],
            ['$default', 'mixed', 'Returned when the key was never set.'],
        ],
        'returns' => 'The value, or the default.',
        'absent' => 'A key that was never set and has no default returns <code>null</code>, which'
            . ' is indistinguishable from an option genuinely set to null — <code>hue</code> and'
            . ' <code>theme</code> both default to null. Pass a sentinel default if you need to'
            . ' tell those apart.',
        'example' => "Deck::config('base');            // '/assets/deck'\n"
            . "Deck::config('missing', 'fallback');",
    ],
    [
        'id' => 'distPath',
        'signature' => 'Deck::distPath(): string',
        'summary' => 'The absolute filesystem path to Deck\'s own <code>dist</code> directory'
            . ' inside <code>vendor/</code>. For a build step or a deploy script that wants to'
            . ' copy the files somewhere itself.',
        'params' => [],
        'returns' => 'An absolute path.',
        'absent' => 'It is the path inside the installed package, so a browser cannot read it —'
            . ' the files still have to be published into a public directory. That is what the'
            . ' Composer installer below does.',
        'example' => "copy(Deck::distPath() . '/deck.min.css', __DIR__ . '/public/deck.min.css');",
    ],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">PHP helper</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>PHP helper</h1>
  <p class="lede">
    Deck is a stylesheet, not a PHP library, so this class does three things: tell you
    where the assets are, emit the tags, and version the URLs so a browser picks up a new
    build. None of it is required — if you would rather write the
    <code>&lt;link&gt;</code> tag yourself, write the <code>&lt;link&gt;</code> tag
    yourself, and nothing else in Deck will notice.
  </p>
</header>

<section class="stack-4">
  <h2 id="install">Installing</h2>
  <pre class="dx-code"><code>composer require echodial/deck</code></pre>
  <p>
    PHP 8.1 or newer, and no runtime dependencies. It is framework-agnostic on purpose —
    no container, no service provider, no facade — so the same class works in Keel, in
    Laravel, in Symfony, in WordPress, and in a single <code>index.php</code>.
  </p>
  <p>
    A browser cannot read <code>vendor/</code>, so the assets have to be copied into a
    public directory, and Composer will not do it for you: it never runs scripts from a
    package you install, only the ones in your own <code>composer.json</code>. Add the
    publisher there:
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
  <p>
    <code>composer require</code>, <code>composer install</code> and
    <code>composer update</code> then publish, and <code>composer deck-publish</code>
    publishes on demand. <code>extra.deck</code> is read from your
    <code>composer.json</code>, never from Deck's.
  </p>
  <pre class="dx-code"><code>composer deck-publish
composer deck-publish -- public/static/deck
composer deck-publish -- --link          # symlink where the platform allows it</code></pre>
  <p>
    <code>publish-to</code> defaults to <code>public/assets/deck</code>. Change it if your
    document root is not <code>public/</code>: on cPanel and Helm it is
    <code>public_html/</code>. With <code>auto-publish</code> left out or
    <code>false</code>, the install and update hooks print a reminder instead of copying.
    The <a href="../start/install.php#composer">install page</a> covers the two ways to get
    the files without any scripts.
  </p>
  <p class="dx-note">
    The installer skips a file whose contents are unchanged rather than recopying it. That
    is deliberate: recopying rewrites the mtime, and the mtime is what
    <code>Deck::asset()</code> uses to bust the cache — so a churning deploy would
    invalidate every asset URL on every release for no reason. Symlinks fall back to a
    copy where the platform refuses them, which is Windows without developer mode and
    some containers.
  </p>
</section>

<section class="stack-4">
  <h2 id="configuration">Configuration</h2>
  <p>
    Ten options, all with defaults that work. The two worth setting explicitly are
    <code>base</code>, which nothing can guess, and <code>root</code>, which everything
    to do with cache busting depends on.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Configuration options and their defaults</caption>
      <thead>
        <tr><th scope="col">Option</th><th scope="col">Default</th><th scope="col">What it does</th></tr>
      </thead>
      <tbody>
        <?php foreach ($OPTIONS as [$key, $default, $what]): ?>
          <tr>
            <th scope="row" data-label="Option"><code><?= e($key) ?></code></th>
            <td data-label="Default"><code class="dx-dim"><?= e($default) ?></code></td>
            <td data-label="What it does"><?= $what ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="dx-note">
    <code>root</code> is the one that catches people. It is the filesystem path that
    <code>base</code> is relative to — usually your document root, not the directory the
    class is in. Get it wrong and nothing breaks visibly: the tags still render, the URLs
    still work, and <code>?v=</code> quietly falls back to the package version, so a
    deploy stops busting caches and nobody finds out until a stale stylesheet is on a
    customer's screen.
  </p>
</section>

<section class="stack-6">
  <h2 id="methods">Methods</h2>
  <p>
    All static, all on <code>EchoDial\Deck\Deck</code>. <code>Deck::VERSION</code> is the
    package version as a string.
  </p>
  <?php foreach ($METHODS as $m) { php_method($m); } ?>
</section>

<section class="stack-4">
  <h2 id="together">All together</h2>
  <p>
    A complete page. The tenant's brand colour and the user's theme come out of the
    database and land in the first byte of HTML, so there is no flash of the wrong colour
    and no second request.
  </p>
  <pre class="dx-code"><code>&lt;?php
use EchoDial\Deck\Deck;

Deck::configure([
    'base'   => '/assets/deck',
    'root'   => __DIR__,
    'minify' => true,
]);
?&gt;
&lt;!doctype html&gt;
&lt;html &lt;?= Deck::htmlAttributes(lang: 'en') ?&gt; &lt;?= Deck::theme(hue: $tenant-&gt;hue, mode: $user-&gt;theme) ?&gt;&gt;
&lt;head&gt;
&lt;meta charset="utf-8"&gt;
&lt;title&gt;&lt;?= htmlspecialchars($tenant-&gt;name) ?&gt;&lt;/title&gt;
&lt;?= Deck::head() ?&gt;
&lt;/head&gt;
&lt;body&gt;
  &lt;header class="sticky-top"&gt;
    &lt;div class="container"&gt;
      &lt;nav class="navbar"&gt;
        &lt;a class="navbar-brand" href="/"&gt;&lt;?= Deck::icon('grid', 'icon icon-lg') ?&gt; &lt;?= htmlspecialchars($tenant-&gt;name) ?&gt;&lt;/a&gt;
        &lt;button class="btn btn-icon btn-ghost push" data-deck-theme aria-label="Switch theme"&gt;
          &lt;?= Deck::icon('moon') ?&gt;
        &lt;/button&gt;
      &lt;/nav&gt;
    &lt;/div&gt;
  &lt;/header&gt;
  &lt;main class="container section stack-6"&gt;…&lt;/main&gt;
&lt;/body&gt;
&lt;/html&gt;</code></pre>
  <p>
    There is a runnable version of this in the repository at
    <code>public_html/php-helper.php</code>. It deliberately requires the class by path
    rather than through the autoloader, so it works in a bare PHP site with nothing
    installed.
  </p>
</section>

<section class="stack-3">
  <h2 id="notes">Things worth knowing</h2>
  <ul class="stack-3">
    <li>
      <strong>Everything returns a string.</strong> Nothing echoes, nothing buffers, and
      nothing touches global state beyond the static configuration. The methods are safe
      to call in a template.
    </li>
    <li>
      <strong>Markup is not escaped; values are.</strong> The return values are HTML and
      are meant to be echoed raw. The parts that come from you — the icon name, the
      class, the theme, the URLs — are escaped inside.
    </li>
    <li>
      <strong>The configuration is static.</strong> Per request, shared across the
      request. In a long-running worker — Swoole, RoadRunner, Octane — it survives
      between requests, so configure it once at boot rather than per request, or reset it
      explicitly.
    </li>
    <li>
      <strong>It does not read the stylesheet.</strong> Nothing here parses CSS or knows
      what classes exist. If you want that inventory in PHP, read
      <code>dist/api.json</code> — the documentation site does exactly that.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
