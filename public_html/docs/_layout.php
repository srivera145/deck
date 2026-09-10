<?php
/**
 * Shared shell for the documentation site.
 *
 * Every page sets $page before requiring this file:
 *
 *     $page = [
 *         'title'       => 'Button',              // <60 chars with the suffix
 *         'description' => '…',                   // 140-160, unique per page
 *         'section'     => 'components',
 *         'documents'   => ['btn', 'btn-primary'] // classes this page owns
 *     ];
 *     require __DIR__ . '/../_layout.php';
 *
 * Then it echoes its body and calls docs_footer().
 *
 * The docs are built with Deck itself. Classes that belong to the docs
 * chrome rather than to Deck are prefixed `dx-`; tools/docs/verify.mjs treats
 * every other class on the page as a claim that it exists in src/, which is
 * what catches a typo in an example.
 */

declare(strict_types=1);

/* --------------------------------------------------------------------------
   Data
   -------------------------------------------------------------------------- */

$DOCS_ROOT = __DIR__;
$REPO = dirname(__DIR__, 2);

$apiFile = $REPO . '/dist/api.json';
if (!is_readable($apiFile)) {
    http_response_code(500);
    exit('dist/api.json is missing. Run `node tools/docs/extract.mjs`.');
}
$api = json_decode(file_get_contents($apiFile), true, 512, JSON_THROW_ON_ERROR);

$sizesFile = $REPO . '/dist/sizes.json';
$sizes = is_readable($sizesFile)
    ? json_decode(file_get_contents($sizesFile), true, 512, JSON_THROW_ON_ERROR)
    : null;

$DOCS_BASE = rtrim(getenv('DECK_SITE_BASE') ?: 'https://get-keel.dev/deck', '/') . '/docs';

/* --------------------------------------------------------------------------
   Helpers
   -------------------------------------------------------------------------- */

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** Look a class up in the generated inventory. */
function api_class(string $name): ?array
{
    global $api;
    static $index = null;
    if ($index === null) {
        $index = [];
        foreach ($api['classes'] as $c) {
            $index[$c['name']] = $c;
        }
    }
    return $index[$name] ?? null;
}

function api_token(string $name): ?array
{
    global $api;
    static $index = null;
    if ($index === null) {
        $index = [];
        foreach ($api['tokens'] as $t) {
            $index[$t['name']] = $t;
        }
    }
    return $index[$name] ?? null;
}

/**
 * Render an example once and show its source beside it.
 *
 * The markup is passed in as a string and used twice — rendered into the page
 * and printed as the source — so the two cannot disagree. That is the whole
 * point: a screenshot or a retyped snippet is a second copy that goes stale.
 */
function docs_example(string $html, string $caption = '', string $layout = 'cluster'): void
{
    static $n = 0;
    $n++;
    $id = 'dx-ex-' . $n;
    $source = trim($html);
    ?>
    <figure class="dx-example">
        <?php if ($caption !== ''): ?>
            <figcaption class="dx-example-caption"><?= e($caption) ?></figcaption>
        <?php endif; ?>
        <div class="dx-example-preview <?= $layout === 'cluster' ? 'cluster' : 'stack-3' ?>">
            <?= $source ?>
        </div>
        <div class="dx-example-source">
            <div class="copy">
                <code class="copy-value" id="<?= e($id) ?>"><?= e($source) ?></code>
                <button class="copy-btn" data-deck-copy aria-label="Copy this example">
                    <span class="copy-idle">Copy</span>
                    <span class="copy-done">Copied</span>
                </button>
            </div>
            <pre class="dx-code"><code><?= e($source) ?></code></pre>
        </div>
    </figure>
    <?php
}

/** The class table for a component, generated from dist/api.json. */
function docs_class_table(array $names): void
{
    ?>
    <div class="table-wrap">
        <table class="table table-stack">
            <caption class="sr-only">Classes, generated from the stylesheet source</caption>
            <thead>
                <tr>
                    <th scope="col">Class</th>
                    <th scope="col">Layer</th>
                    <th scope="col">What it does</th>
                    <th scope="col">Source</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($names as $name): $c = api_class($name); ?>
                    <?php if (!$c) { continue; } ?>
                    <tr>
                        <th scope="row" data-label="Class"><code><?= e('.' . $c['name']) ?></code></th>
                        <td data-label="Layer"><code class="dx-dim"><?= e($c['layer'] ?? '—') ?></code></td>
                        <td data-label="What it does">
                            <?php if ($c['doc']): ?>
                                <?= e($c['doc']) ?>
                            <?php else: ?>
                                <span class="dx-dim"><?= e(docs_summarise($c)) ?></span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Source"><code class="dx-dim"><?= e($c['file'] . ':' . $c['line']) ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/** A one-line description built from the declarations, for classes with no
 *  comment in the source. Honest filler: it says what the rule sets. */
function docs_summarise(array $c): string
{
    $props = array_map(static fn(array $d): string => $d['prop'], $c['declarations']);
    $props = array_values(array_filter($props, static fn(string $p): bool => !str_starts_with($p, '--')));
    if (!$props) {
        $custom = array_map(static fn(array $d): string => $d['prop'], $c['declarations']);
        return $custom ? 'Sets ' . implode(', ', array_slice($custom, 0, 3)) . '.' : 'No declarations.';
    }
    $shown = array_slice($props, 0, 4);
    $more = count($props) - count($shown);
    return 'Sets ' . implode(', ', $shown) . ($more > 0 ? ", and {$more} more" : '') . '.';
}

/** Token rows with a live swatch, so the value shown is the value in effect. */
function docs_token_table(array $names): void
{
    ?>
    <div class="table-wrap">
        <table class="table table-stack">
            <caption class="sr-only">Design tokens this component reads</caption>
            <thead>
                <tr>
                    <th scope="col">Token</th>
                    <th scope="col">Live</th>
                    <th scope="col">Declared value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($names as $name): $t = api_token($name); ?>
                    <tr>
                        <th scope="row" data-label="Token"><code><?= e($name) ?></code></th>
                        <td data-label="Live">
                            <?php if ($t && $t['kind'] === 'color'): ?>
                                <span class="dx-swatch" style="background: var(<?= e($name) ?>)"></span>
                            <?php else: ?>
                                <span class="dx-rule" style="inline-size: var(<?= e($name) ?>, 1rem)"></span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Declared value"><code class="dx-dim"><?= e($t['value'] ?? 'inherited') ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/* --------------------------------------------------------------------------
   Navigation. Pages that are not written yet render as muted text rather than
   as links, so the nav is a map of the whole site without any 404s in it.
   -------------------------------------------------------------------------- */

$DOCS_NAV = [
    'Start' => [
        ['start/install.php', 'Install', false],
        ['start/first-page.php', 'Your first page', false],
    ],
    'Guides' => [
        ['guides/theming.php', 'Theming', false],
        ['guides/layers.php', 'Cascade layers', false],
        ['guides/dark-mode.php', 'Dark mode', false],
        ['guides/rtl.php', 'Right to left', false],
        ['guides/forms.php', 'Forms', false],
        ['guides/layout.php', 'Layout', false],
        ['guides/motion.php', 'Motion', false],
        ['guides/printing.php', 'Printing', false],
        ['guides/migrating-from-tailwind.php', 'Migrating from Tailwind', false],
    ],
    'Components' => [
        ['components/alert.php', 'Alert', true],
        ['components/avatar.php', 'Avatar', true],
        ['components/badge.php', 'Badge', true],
        ['components/bar.php', 'Bar', true],
        ['components/button.php', 'Button', true],
        ['components/card.php', 'Card', true],
        ['components/check.php', 'Checkbox and radio', true],
        ['components/cluster.php', 'Cluster', true],
        ['components/container.php', 'Container', true],
        ['components/field.php', 'Field', true],
        ['components/fieldset.php', 'Fieldset', true],
        ['components/file.php', 'File', true],
        ['components/grid.php', 'Grid', true],
        ['components/input.php', 'Input', true],
        ['components/list.php', 'List', true],
        ['components/range.php', 'Range', true],
        ['components/scroller.php', 'Scroller', true],
        ['components/section.php', 'Section', true],
        ['components/select.php', 'Select', true],
        ['components/split.php', 'Split', true],
        ['components/stack.php', 'Stack', true],
        ['components/switch.php', 'Switch', true],
        ['components/table.php', 'Table', true],
        ['components/textarea.php', 'Textarea', true],
    ],
    'Reference' => [
        ['reference/classes.php', 'All classes', false],
        ['reference/tokens.php', 'All tokens', false],
        ['reference/javascript.php', 'JavaScript API', false],
        ['reference/php.php', 'PHP helper', false],
        ['reference/cli.php', 'CLI', false],
    ],
    'Explanation' => [
        ['explain/why-no-build-step.php', 'Why no build step', false],
        ['explain/why-cascade-layers.php', 'Why cascade layers', false],
        ['explain/why-one-hue.php', 'Why one hue', false],
    ],
];

$here = $page['path'] ?? '';
$depth = substr_count($here, '/');
$up = str_repeat('../', $depth);
$assets = $up . '../assets';

$title = ($page['title'] ?? 'Documentation') . ' — Deck';
$canonical = $DOCS_BASE . '/' . ($here ?: 'index.php');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title) ?></title>
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="description" content="<?= e($page['description'] ?? '') ?>">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
<link rel="icon" href="<?= e($assets) ?>/images/deck-mark.svg" type="image/svg+xml">

<meta property="og:type" content="article">
<meta property="og:site_name" content="Deck documentation">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($page['description'] ?? '') ?>">
<meta property="og:image" content="<?= e($DOCS_BASE) ?>/../assets/images/deck-og.png">
<meta name="twitter:card" content="summary_large_image">

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'TechArticle',
    'headline' => $page['title'] ?? 'Documentation',
    'description' => $page['description'] ?? '',
    'url' => $canonical,
    'inLanguage' => 'en',
    'isPartOf' => [
        '@type' => 'TechArticle',
        'name' => 'Deck documentation',
        'url' => $DOCS_BASE . '/',
    ],
    'about' => [
        '@type' => 'SoftwareSourceCode',
        'name' => 'Deck',
        'programmingLanguage' => ['@type' => 'ComputerLanguage', 'name' => 'CSS'],
        'codeRepository' => 'https://github.com/srivera145/deck',
    ],
    'author' => ['@type' => 'Person', 'name' => 'Santos Rivera'],
    'proficiencyLevel' => $page['level'] ?? 'Beginner',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

<link rel="stylesheet" href="<?= e($assets) ?>/deck/deck.css">
<script src="<?= e($assets) ?>/deck/deck.js" defer></script>
<script src="<?= e($assets) ?>/deck/deck-extras.js" defer></script>
<style>
  /* Docs chrome only. Everything else on this page is Deck.
     These are all `dx-` prefixed so the verifier can tell them apart from
     classes that are supposed to exist in src/. */
  @layer app.pages {
    .dx-shell { display: grid; grid-template-columns: 1fr; gap: var(--space-8); }
    /* A grid item's min-inline-size is auto, so a wide code block or a long
       table would push the whole track past the viewport and drag the sidebar
       out with it. Both children have to be allowed to shrink. */
    .dx-shell > * { min-inline-size: 0; }
    @media (min-width: 60rem) {
      .dx-shell { grid-template-columns: 16rem minmax(0, 1fr); align-items: start; }
      .dx-side { position: sticky; inset-block-start: 4.5rem; max-block-size: calc(100dvh - 6rem); overflow-y: auto; }
    }
    /* On a phone the nav sits above the content, and twenty entries would push
       the first heading a screen and a half down. Bound it and let it scroll on
       its own instead. No JavaScript involved, so it survives with JS off. */
    @media (max-width: 59.99rem) {
      .dx-side { max-block-size: 40dvh; overflow-y: auto; border: 1px solid var(--line); border-radius: var(--r-md); }
    }
    .dx-side details { border: 0; }
    .dx-side summary { font-size: var(--text-xs); font-weight: 620; color: var(--text-faint); text-transform: uppercase; letter-spacing: .06em; padding-block: var(--space-2); cursor: pointer; }
    .dx-soon { color: var(--text-faint); padding: var(--space-2) var(--space-3); display: block; font-size: var(--text-sm); }
    .dx-soon::after { content: " soon"; font-size: var(--text-xs); opacity: .7; }

    .dx-example { margin: 0; border: 1px solid var(--line); border-radius: var(--r-md); overflow: hidden; }
    .dx-example-caption { padding: var(--space-3) var(--space-4); font-size: var(--text-sm); color: var(--text-muted); border-block-end: 1px solid var(--line); background: var(--surface); }
    .dx-example-preview { padding: var(--space-5); background: var(--bg); }
    .dx-example-source { border-block-start: 1px solid var(--line); background: var(--surface); }
    .dx-example-source .copy { border: 0; border-radius: 0; border-block-end: 1px solid var(--line); }
    .dx-example-source .copy-value { display: none; }
    .dx-code { margin: 0; border-radius: 0; max-block-size: 22rem; }

    .dx-swatch { display: inline-block; inline-size: 2.5rem; block-size: 1.25rem; border-radius: var(--r-xs); border: 1px solid var(--line); vertical-align: middle; }
    .dx-rule { display: inline-block; block-size: .5rem; background: var(--brand); border-radius: var(--r-full); vertical-align: middle; }
    .dx-dim { color: var(--text-faint); font-size: var(--text-xs); }
    .dx-toc { font-size: var(--text-sm); }
    .dx-note { border-inline-start: 3px solid var(--brand); padding-inline-start: var(--space-4); }
  }
</style>
</head>
<body>
<a class="skip-link" href="#dx-main">Skip to content</a>

<header class="sticky-top">
  <div class="container">
    <nav class="navbar" aria-label="Documentation">
      <a class="navbar-brand" href="<?= e($up) ?>index.php">
        <svg class="icon icon-lg icon-fill" style="color:var(--brand)"><use href="<?= e($assets) ?>/deck/deck-icons.svg#deck-mark"></use></svg>
        Deck <span class="badge">docs</span>
      </a>
      <form class="push" role="search" action="<?= e($up) ?>search.php" method="get">
        <div class="search">
          <svg class="icon"><use href="<?= e($assets) ?>/deck/deck-icons.svg#search"></use></svg>
          <label class="sr-only" for="dx-q">Search the class reference</label>
          <input class="input" type="search" id="dx-q" name="q" placeholder="Search <?= (int) $api['counts']['classes'] ?> classes" value="<?= e($_GET['q'] ?? '') ?>">
        </div>
      </form>
      <a class="btn btn-sm btn-ghost" href="<?= e($up) ?>../index.php">Demo</a>
    </nav>
  </div>
</header>

<div class="container section">
  <div class="dx-shell">
    <aside class="dx-side" aria-label="Documentation sections">
      <nav class="panel sidebar">
        <?php foreach ($DOCS_NAV as $group => $items): ?>
          <span class="sidebar-group"><?= e($group) ?></span>
          <?php foreach ($items as [$path, $label, $written]): ?>
            <?php if ($written): ?>
              <a class="sidebar-link" href="<?= e($up . $path) ?>"<?= $path === $here ? ' aria-current="page"' : '' ?>>
                <span><?= e($label) ?></span>
              </a>
            <?php else: ?>
              <span class="dx-soon"><?= e($label) ?></span>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </nav>
    </aside>

    <main id="dx-main" class="stack-8">
<?php
/* ------------------------------------------------------------------------- */
function docs_footer(): void
{
    global $up, $api, $sizes;
    ?>
      <hr>
      <footer class="stack-3 text-muted">
        <p class="text-sm">
          Generated against <?= (int) $api['counts']['classes'] ?> classes and
          <?= (int) $api['counts']['tokens'] ?> tokens in
          <?= (int) $api['stylesheets'] ?> stylesheets.
          <?php if ($sizes): ?>
            Deck is <?= e(number_format($sizes['files']['deck.min.css']['brotli'] / 1000, 1)) ?> KB Brotli.
          <?php endif; ?>
        </p>
        <p class="text-sm">
          Every class table on this site is built from the stylesheet source by
          <code>tools/docs/extract.mjs</code>, and
          <code>tools/docs/verify.mjs</code> fails the build if a page and the
          source disagree.
        </p>
      </footer>
    </main>
  </div>
</div>
</body>
</html>
    <?php
}
