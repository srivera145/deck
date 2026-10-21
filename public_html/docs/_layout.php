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
 *
 * The data and helpers — dist/api.json, the class tables, the search — are in
 * _docs.php, so a page can use them before this file prints anything.
 */

declare(strict_types=1);

require_once __DIR__ . '/_docs.php';

/* --------------------------------------------------------------------------
   Navigation. Pages that are not written yet render as muted text rather than
   as links, so the nav is a map of the whole site without any 404s in it.
   -------------------------------------------------------------------------- */

$DOCS_NAV = [
    'Start' => [
        ['start/install.php', 'Install Deck', true],
        ['start/editor.php', 'Set up your editor', true],
        ['start/first-page.php', 'Your first page', true],
    ],
    /* Guides are named for the task, not the feature, because that is what a
       person types into a search engine. "Theming" is what we call it; "change
       your brand colour" is what they came to do. */
    'Guides' => [
        ['guides/theming.php', 'Change your brand colour', true],
        ['guides/layout.php', 'Stop writing margins', true],
        ['guides/layers.php', 'Override without !important', true],
        ['guides/dark-mode.php', 'Add dark mode', true],
        ['guides/forms.php', 'Forms that validate', true],
        ['guides/rtl.php', 'Right-to-left languages', true],
        ['guides/motion.php', 'Animate without a library', true],
        ['guides/printing.php', 'Print properly', true],
        ['guides/migrating-from-tailwind.php', 'Move from Tailwind', true],
    ],
    /* The index first: it is generated, so it lists what this hand-kept list
       can forget, and the classes each page covers, which a title hides. */
    'Components' => [
        ['components/index.php', 'All components', true],
        ['components/3d.php', '3D space', true],
        ['components/accordion.php', 'Accordion', true],
        ['components/alert.php', 'Alert', true],
        ['components/avatar.php', 'Avatar', true],
        ['components/badge.php', 'Badge', true],
        ['components/bar.php', 'Bar', true],
        ['components/breadcrumb.php', 'Breadcrumb', true],
        ['components/button.php', 'Button', true],
        ['components/card.php', 'Card', true],
        ['components/carousel.php', 'Carousel', true],
        ['components/charts.php', 'Charts', true],
        ['components/chat.php', 'Chat', true],
        ['components/check.php', 'Checkbox and radio', true],
        ['components/cluster.php', 'Cluster', true],
        ['components/combobox.php', 'Combobox', true],
        ['components/container.php', 'Container', true],
        ['components/copy.php', 'Copy to clipboard', true],
        ['components/datagrid.php', 'Data grid', true],
        ['components/datepicker.php', 'Date picker', true],
        ['components/drawer.php', 'Drawer', true],
        ['components/editor.php', 'Editor', true],
        ['components/emoji.php', 'Emoji', true],
        ['components/field.php', 'Field', true],
        ['components/fieldset.php', 'Fieldset', true],
        ['components/file.php', 'File upload', true],
        ['components/float.php', 'Floating label', true],
        ['components/footer.php', 'Footer', true],
        ['components/gallery.php', 'Gallery', true],
        ['components/gradients.php', 'Gradients', true],
        ['components/grid.php', 'Grid', true],
        ['components/icon.php', 'Icon', true],
        ['components/indicator.php', 'Indicator', true],
        ['components/input.php', 'Input', true],
        ['components/kanban.php', 'Kanban', true],
        ['components/list.php', 'List', true],
        ['components/menu.php', 'Menu', true],
        ['components/modal.php', 'Modal', true],
        ['components/motion.php', 'Motion', true],
        ['components/nav.php', 'Nav bar', true],
        ['components/number.php', 'Number input', true],
        ['components/pagination.php', 'Pagination', true],
        ['components/phone.php', 'Phone input', true],
        ['components/popover.php', 'Popover', true],
        ['components/print.php', 'Printing', true],
        ['components/progress.php', 'Progress', true],
        ['components/qr.php', 'QR code', true],
        ['components/range.php', 'Range', true],
        ['components/rating.php', 'Rating', true],
        ['components/scroller.php', 'Scroller', true],
        ['components/section.php', 'Section', true],
        ['components/segmented.php', 'Segmented', true],
        ['components/select.php', 'Select', true],
        ['components/sheet.php', 'Sheet', true],
        ['components/sidebar.php', 'Sidebar', true],
        ['components/skeleton.php', 'Skeleton', true],
        ['components/split.php', 'Split', true],
        ['components/stack.php', 'Stack', true],
        ['components/stepper.php', 'Stepper', true],
        ['components/switch.php', 'Switch', true],
        ['components/table.php', 'Table', true],
        ['components/tabs.php', 'Tabs', true],
        ['components/textarea.php', 'Textarea', true],
        ['components/timeline.php', 'Timeline', true],
        ['components/toast.php', 'Toast', true],
        ['components/tooltip.php', 'Tooltip', true],
        ['components/video.php', 'Video', true],
    ],
    'Reference' => [
        ['reference/classes.php', 'All classes', true],
        ['reference/tokens.php', 'All tokens', true],
        ['reference/spacing.php', 'Spacing', true],
        ['reference/typography.php', 'Typography', true],
        ['reference/display.php', 'Display and sizing', true],
        ['reference/position.php', 'Position', true],
        ['reference/color.php', 'Colour and surface', true],
        ['reference/javascript.php', 'JavaScript API', true],
        ['reference/php.php', 'PHP helper', true],
        ['reference/cli.php', 'CLI', true],
        ['reference/changelog.php', 'Changelog', true],
    ],
    'Explanation' => [
        ['explain/why-no-build-step.php', 'Why no build step', true],
        ['explain/why-cascade-layers.php', 'Why cascade layers', true],
        ['explain/why-one-hue.php', 'Why one hue', true],
    ],
];

$here = $page['path'] ?? '';
$depth = substr_count($here, '/');
$up = str_repeat('../', $depth);
$assets = $up . '../assets';

$title = ($page['title'] ?? 'Documentation') . ' — Deck';
$canonical = $DOCS_BASE . '/' . ($here ?: 'index.php');

/* Set when search.php sent the reader straight here, so the page can say why
   and offer the other results. */
$found = is_string($_GET['found'] ?? null) ? trim($_GET['found']) : '';
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
<meta property="og:image" content="<?= e($SITE['base']) ?>/assets/images/deck-og.png">
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

<script>
  /* Apply a saved theme before first paint. deck.js restores it too, but it is
     deferred, and a long page can paint before a deferred script runs. */
  try { var dxTheme = localStorage.getItem('deck-theme'); if (dxTheme) document.documentElement.setAttribute('data-theme', dxTheme); } catch (e) {}
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
    /* The cap is a length, not a percentage: a table column is sized from its
       content, and a percentage of a width still being worked out caps nothing.
       The exact value is printed in the next column; the bar is a sense of scale. */
    .dx-rule { display: inline-block; block-size: .5rem; background: var(--brand); border-radius: var(--r-full); vertical-align: middle; max-inline-size: 16rem; }
    .dx-radius { display: inline-block; inline-size: 2.5rem; block-size: 1.5rem; border: 2px solid var(--brand); background: var(--brand-soft); vertical-align: middle; }
    .dx-shadow { display: inline-block; inline-size: 2.5rem; block-size: 1.25rem; border-radius: var(--r-xs); background: var(--surface); vertical-align: middle; }
    /* A dot either side, the right one under the blur: how far it spreads is
       the strength. Fine stripes look the same at 8px as at 16px. */
    .dx-blur { display: inline-block; position: relative; overflow: clip; inline-size: 7rem; block-size: 2rem; border: 1px solid var(--line); border-radius: var(--r-xs); background: radial-gradient(circle at 25% 50%, var(--brand) 6px, transparent 7px), radial-gradient(circle at 75% 50%, var(--brand) 6px, transparent 7px); vertical-align: middle; }
    .dx-blur > span { position: absolute; inset: 0 0 0 50%; }
    .dx-font { font-size: var(--text-md); }
    /* A duration and an easing curve are only legible in motion, so the cell
       animates with the token itself rather than describing it. Held still for
       anyone who asked their system to calm things down. */
    .dx-motion { display: inline-block; inline-size: 3.5rem; block-size: .75rem; background: var(--bg-sunken); border-radius: var(--r-full); position: relative; overflow: hidden; vertical-align: middle; }
    .dx-motion-dot { position: absolute; inset-block-start: .125rem; inline-size: .5rem; block-size: .5rem; border-radius: var(--r-full); background: var(--brand); animation: dx-slide 1.2s var(--ease-out) infinite alternate; }
    @keyframes dx-slide { from { inset-inline-start: .125rem; } to { inset-inline-start: 2.875rem; } }
    @media (prefers-reduced-motion: reduce) { .dx-motion-dot { animation: none; inset-inline-start: .125rem; } }
    .dx-dim { color: var(--text-faint); font-size: var(--text-xs); }
    .dx-toc { font-size: var(--text-sm); }
    .dx-note { border-inline-start: 3px solid var(--brand); padding-inline-start: var(--space-4); }
    /* A search or the component index can land on a class's row; mark it, or
       it is one row among sixty. */
    .dx-shell tr:target > * { background: var(--brand-soft); }

    /* The theme switch shows where a press will take you: a moon on a light
       page, a sun on a dark one. With nothing saved there is no data-theme
       attribute and the OS decides, so the media queries cover that case and
       the icon is right before the first press. Only hiding rules, so the
       visible icon keeps whatever display .icon gives it. */
    :root[data-theme="light"] .dx-theme .dx-sun,
    :root[data-theme="dark"] .dx-theme .dx-moon { display: none; }
    @media (prefers-color-scheme: light) { :root:not([data-theme]) .dx-theme .dx-sun { display: none; } }
    @media (prefers-color-scheme: dark) { :root:not([data-theme]) .dx-theme .dx-moon { display: none; } }
  }
</style>
</head>
<body>
<!-- The back-to-top link's target. tabindex="-1" moves keyboard focus here too,
     so the next Tab lands on the skip link rather than wherever it was left. -->
<span id="dx-top" tabindex="-1"></span>
<a class="skip-link" href="#dx-main">Skip to content</a>

<header class="sticky-top">
  <div class="container">
    <nav class="navbar" aria-label="Documentation">
      <a class="navbar-brand" href="<?= e($up) ?>index.php">
        <svg class="icon icon-lg icon-fill" style="color:var(--brand)"><use href="<?= e($assets) ?>/deck/deck-icons.svg#deck-mark"></use></svg>
        Deck <span class="badge">docs</span>
      </a>
      <!-- go=1 lets search.php open the page itself when one match is clearly
           best, so "date picker" lands on the date picker rather than on a
           list with it in. The search page's own form leaves it off. -->
      <form class="push" role="search" action="<?= e($up) ?>search.php" method="get">
        <div class="search">
          <svg class="icon"><use href="<?= e($assets) ?>/deck/deck-icons.svg#search"></use></svg>
          <label class="sr-only" for="dx-q">Search the documentation</label>
          <input class="input" type="search" id="dx-q" name="q" placeholder="Find a component or class" value="<?= e(is_string($_GET['q'] ?? null) ? $_GET['q'] : $found) ?>">
          <input type="hidden" name="go" value="1">
        </div>
      </form>
      <a class="btn btn-sm btn-ghost" href="<?= e($up) ?>../index.php#demo">Demo</a>
      <button class="btn btn-icon btn-ghost dx-theme" type="button" data-deck-theme aria-label="Switch between light and dark theme">
        <svg class="icon dx-moon" aria-hidden="true"><use href="<?= e($assets) ?>/deck/deck-icons.svg#moon"></use></svg>
        <svg class="icon dx-sun" aria-hidden="true"><use href="<?= e($assets) ?>/deck/deck-icons.svg#sun"></use></svg>
      </button>
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

    <main id="dx-main" class="stack stack-8">
      <?php if ($found !== ''): ?>
        <p class="text-sm text-muted" role="status">
          Best match for “<?= e($found) ?>”.
          <a href="<?= e($up) ?>search.php?q=<?= e(rawurlencode($found)) ?>">See every result</a>
        </p>
      <?php endif; ?>
<?php
/* ------------------------------------------------------------------------- */
function docs_footer(): void
{
    global $up, $api, $DOCS_SIZES, $assets, $SITE;
    ?>
      <hr>
      <footer class="stack stack-3 text-muted">
        <p class="text-sm">
          Generated against <?= (int) $api['counts']['classes'] ?> classes and
          <?= (int) $api['counts']['tokens'] ?> tokens in
          <?= (int) $api['stylesheets'] ?> stylesheets.
          <?php if ($DOCS_SIZES): ?>
            Deck is <?= e(docs_kb('deck.min.css')) ?> Brotli.
          <?php endif; ?>
        </p>
        <p class="text-sm">
          Every class table on this site is built from the stylesheet source by
          <code>tools/docs/extract.mjs</code>, and
          <code>tools/docs/verify.mjs</code> fails the build if a page and the
          source disagree.
        </p>
        <?php /* The ways off a docs page that the sidebar does not give: the
           templates and the repository are outside the docs, and the version
           links to what changed in it. */ ?>
        <nav class="cluster text-sm" aria-label="More from Deck">
          <a href="<?= e($up) ?>../templates/index.php">Templates</a>
          <a href="<?= e($up) ?>start/editor.php">Editor extension</a>
          <a href="<?= e($up) ?>reference/changelog.php">Changelog (<?= e($SITE['version']) ?>)</a>
          <a href="https://github.com/srivera145/deck">GitHub</a>
          <a href="https://github.com/srivera145/deck/issues">Report a problem</a>
        </nav>
      </footer>
    </main>
  </div>
</div>

<!-- Deck's own .back-to-top: it stays hidden until the page has scrolled 400px,
     through a scroll-driven animation where the browser has one and deck.js
     where it does not, and it is simply always visible with JavaScript off. -->
<a class="back-to-top" href="#dx-top" aria-label="Back to top">
  <svg class="icon" aria-hidden="true"><use href="<?= e($assets) ?>/deck/deck-icons.svg#chevron-up"></use></svg>
</a>
</body>
</html>
    <?php
}
