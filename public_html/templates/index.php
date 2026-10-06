<?php
/* =============================================================================
   public_html/templates/index.php
   -----------------------------------------------------------------------------
   The template gallery.

   Two jobs. For a person it is a menu: see the page, open the page, copy the
   file. For a search engine it is the page that answers "css framework
   templates", which is a different query from the one the home page answers and
   so deserves a URL of its own.

   Adding a template is one entry in $templates below plus the file beside this
   one. Everything else — the cards, the previews, the category counts, the
   structured data — follows.

   Line counts are read from the files at render rather than typed, for the same
   reason every byte figure on this site is read from dist/sizes.json: a number
   maintained by hand is a number that is wrong by the third edit.
   ============================================================================= */

declare(strict_types=1);

$siteMeta = require __DIR__ . '/../_site.php';

$site = [
    'base'    => $siteMeta['base'],
    'version' => $siteMeta['version'],
    /* The file name is part of the path, as on every docs page, so the
       canonical this page prints is the address tools/site.mjs puts in the
       sitemap. */
    'path'    => 'templates/index.php',
    'author'  => 'Santos Rivera',
    'repo'    => 'https://github.com/srivera145/deck',

    'title'       => 'Deck templates — free mobile-first CSS page layouts',
    'description' => 'Free page templates built with Deck: storefront, product, '
                   . 'checkout and pricing pages, sites for agencies and local '
                   . 'businesses, app screens, and sets of sidebars, footers, '
                   . 'heroes and carousels. One HTML file each, no build step, '
                   . 'MIT licensed.',
];

$url = static fn(string $path = ''): string => $site['base'] . '/' . ltrim($path, '/');
$e   = static fn(?string $s): string => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

$canonical = $url($site['path']);
$ogImage   = $url('assets/images/deck-og.png');

/* -----------------------------------------------------------------------------
   Categories, in the order they appear. The key is the anchor and the value of
   `cat` on each template.
   -------------------------------------------------------------------------- */
$categories = [
    'websites' => [
        'name' => 'Websites',
        'lede' => 'Complete marketing sites, top bar to footer, each in its own brand '
                . 'colour — which in Deck is one number.',
    ],
    'commerce' => [
        'name' => 'Commerce',
        'lede' => 'The pages that take money. The store, product and checkout pages '
                . 'are one shop and link to each other.',
    ],
    'app' => [
        'name' => 'App screens',
        'lede' => 'One app, four screens, sharing a top bar, a sidebar, a phone tab '
                . 'bar and a drawer. Click between them.',
    ],
    'sections' => [
        'name' => 'Sections',
        'lede' => 'Several takes on one section per page, each self-contained, each '
                . 'with a button that copies its HTML.',
    ],
];

/* -----------------------------------------------------------------------------
   The gallery. `file` and `cat` are the required keys; everything else is
   description. `new` marks a template added in the current release.
   -------------------------------------------------------------------------- */
$templates = [
    /* ---- Websites ---- */
    [
        'file'  => 'landing.html',
        'cat'   => 'websites',
        'name'  => 'Landing page',
        'blurb' => 'The marketing page most projects need first: a hero whose '
                 . 'screenshot is built from Deck components, bento features, '
                 . 'testimonials, three-plan pricing with a billing toggle, and an FAQ.',
        'shows' => ['Component screenshot', 'Bento grid', 'Billing toggle', 'Logo marquee'],
        'icon'  => 'star',
    ],
    [
        'file'  => 'ecommerce.html',
        'cat'   => 'websites',
        'name'  => 'Online store',
        'blurb' => 'A storefront with a mega menu, a split hero with a floating '
                 . 'product card, category tiles, a filterable product grid with '
                 . 'sale prices, a bestsellers carousel and a promo bento.',
        'shows' => ['Mega menu', 'Filterable grid', 'Bestsellers carousel', 'Bag badge'],
        'icon'  => 'tag',
        'new'   => true,
    ],
    [
        'file'  => 'consulting.html',
        'cat'   => 'websites',
        'name'  => 'Business consulting',
        'blurb' => 'Navy, ivory and one serif: an editorial hero, a numbered '
                 . 'practice grid, an impact band that stays dark in both themes, '
                 . 'partner portraits and a consultation form.',
        'shows' => ['Serif display', 'Impact band', 'Partner portraits', 'Consultation form'],
        'icon'  => 'users',
        'new'   => true,
    ],
    [
        'file'  => 'it-agency.html',
        'cat'   => 'websites',
        'name'  => 'IT agency',
        'blurb' => 'A dark hero whose monitoring console is built entirely from Deck '
                 . 'components, a services bento with inline charts, per-seat '
                 . 'pricing with a billing toggle, and an FAQ.',
        'shows' => ['Mock console', 'Services bento', 'Billing toggle', 'Tech marquee'],
        'icon'  => 'gauge',
        'new'   => true,
    ],
    [
        'file'  => 'auto-repair.html',
        'cat'   => 'websites',
        'name'  => 'Auto repair shop',
        'blurb' => 'An independent garage: open-now status worked out in the shop\'s '
                 . 'own time zone, a booking card over the hero photo, a price table '
                 . 'and a map drawn in CSS.',
        'shows' => ['Live open status', 'Booking card', 'Price table', 'CSS map'],
        'icon'  => 'car',
        'new'   => true,
    ],
    [
        'file'  => 'digital-marketing.html',
        'cat'   => 'websites',
        'name'  => 'Digital marketing agency',
        'blurb' => 'An agency whose hero dashboard is Deck charts, not a screenshot: '
                 . 'a services bento, case studies led by the metric, a channel '
                 . 'switcher built from radios, and a free-audit form.',
        'shows' => ['Chart dashboard', 'Bento grid', 'CSS-only tabs', 'Audit form'],
        'icon'  => 'trend-up',
        'new'   => true,
    ],
    [
        'file'  => 'travel.html',
        'cat'   => 'websites',
        'name'  => 'Travel agency',
        'blurb' => 'A boutique agency: a full-bleed hero with a booking bar that '
                 . 'restacks on a phone, a destination mosaic, a filterable trips '
                 . 'carousel and a day-by-day itinerary.',
        'shows' => ['Booking bar', 'Photo mosaic', 'Filter carousel', 'Itinerary'],
        'icon'  => 'map-pin',
        'new'   => true,
    ],
    [
        'file'  => 'cover.html',
        'cat'   => 'websites',
        'name'  => 'Cover',
        'blurb' => 'One screen at 100dvh: a photo, a glass top bar, a waitlist form '
                 . 'and a countdown in minutes, not seconds. Nothing scrolls unless '
                 . 'the screen is very short.',
        'shows' => ['100dvh', 'Photo scrim', 'Glass surfaces', 'Waitlist form'],
        'icon'  => 'send',
        'new'   => true,
    ],

    /* ---- Commerce ---- */
    [
        'file'  => 'product.html',
        'cat'   => 'commerce',
        'name'  => 'Product page',
        'blurb' => 'A gallery driven by thumbnails, colour and size pickers on real '
                 . 'radios, a quantity stepper, a review breakdown, and a phone buy '
                 . 'bar that appears once Add scrolls away.',
        'shows' => ['Image gallery', 'Variant pickers', 'Review breakdown', 'Sticky buy bar'],
        'icon'  => 'bookmark',
        'new'   => true,
    ],
    [
        'file'  => 'checkout.html',
        'cat'   => 'commerce',
        'name'  => 'Checkout',
        'blurb' => 'One page, four steps: express wallets, address fields with the '
                 . 'autocomplete tokens browsers fill, shipping as cards, and an '
                 . 'order summary that folds away on a phone.',
        'shows' => ['Stepper', 'Autofill tokens', 'Shipping cards', 'Folding summary'],
        'icon'  => 'credit-card',
        'new'   => true,
    ],
    [
        'file'  => 'pricing.html',
        'cat'   => 'commerce',
        'name'  => 'Pricing',
        'blurb' => 'Four plans with a monthly and annual toggle built from real '
                 . 'radios, a comparison table that scrolls both ways on a phone '
                 . 'without losing its headers, add-ons and a billing FAQ.',
        'shows' => ['Billing toggle', 'Comparison table', 'Enterprise band', 'FAQ accordion'],
        'icon'  => 'dollar',
        'new'   => true,
    ],

    /* ---- App screens ---- */
    [
        'file'  => 'app-shell.html',
        'cat'   => 'app',
        'name'  => 'App shell',
        'blurb' => 'The skeleton the other app screens share: a top bar with search '
                 . 'and menus, a sticky sidebar, and a tab bar plus drawer on phones. '
                 . 'Paste your screen into the middle.',
        'shows' => ['Sticky top bar', 'Popover menus', 'Phone drawer', 'Empty state'],
        'icon'  => 'grid',
    ],
    [
        'file'  => 'dashboard.html',
        'cat'   => 'app',
        'name'  => 'Dashboard',
        'blurb' => 'Sparkline stat tiles, an area chart with a working 7, 30 and '
                 . '90-day range, columns, bars, a donut, goal rings and a table that '
                 . 'restacks below 40rem. No charting library anywhere.',
        'shows' => ['SVG area chart', 'Date range', 'CSS donut', 'Restacking table'],
        'icon'  => 'chart',
    ],
    [
        'file'  => 'settings.html',
        'cat'   => 'app',
        'name'  => 'Settings',
        'blurb' => 'Deck\'s core form controls in label-left rows, a save bar that '
                 . 'clears the tab bar and the home indicator, a hue slider that '
                 . 'retunes the palette live, and a delete you must type to confirm.',
        'shows' => ['Label-left rows', 'Sticky save bar', 'Live theming', 'Confirm dialog'],
        'icon'  => 'settings',
    ],
    [
        'file'  => 'sign-in.html',
        'cat'   => 'app',
        'name'  => 'Sign in',
        'blurb' => 'Single sign-on, a real form the password manager saves from, a '
                 . 'password reveal, and an error state wired up for screen readers. '
                 . 'The brand panel steps aside on phones.',
        'shows' => ['Split layout', 'Password reveal', 'Error state', 'Password manager'],
        'icon'  => 'lock',
    ],

    /* ---- Sections ---- */
    [
        'file'  => 'sidebars.html',
        'cat'   => 'sections',
        'name'  => 'Sidebars',
        'blurb' => 'Seven sidebars in working app windows: grouped links, an icon '
                 . 'rail with tooltips, an always-dark one, a floating card, two-level '
                 . 'chat nav, a docs tree and a real drawer.',
        'shows' => ['Icon rail', 'Always dark', 'Docs tree', 'Off-canvas drawer'],
        'icon'  => 'menu',
        'new'   => true,
    ],
    [
        'file'  => 'footers.html',
        'cat'   => 'sections',
        'name'  => 'Footers',
        'blurb' => 'Eight footers, from a one-liner to a dark mega footer, with a '
                 . 'newsletter signup, an overlapping call to action, opening hours '
                 . 'beside a CSS map, and an accordion for phones.',
        'shows' => ['Overlapping CTA', 'Newsletter', 'CSS map', 'Phone accordion'],
        'icon'  => 'list',
        'new'   => true,
    ],
    [
        'file'  => 'jumbotrons.html',
        'cat'   => 'sections',
        'name'  => 'Jumbotrons',
        'blurb' => 'Eight heroes, from a mesh-washed centred pitch to an always-dark '
                 . 'product hero whose app window is built from Deck\'s own sidebar, '
                 . 'stats and chart.',
        'shows' => ['Gradient mesh', 'Dark-scoped section', 'Stats band', 'Video poster'],
        'icon'  => 'sparkle',
        'new'   => true,
    ],
    [
        'file'  => 'carousels.html',
        'cat'   => 'sections',
        'name'  => 'Carousels',
        'blurb' => 'Seven carousels on one scroll-snap component: a hero slider with '
                 . 'a pause button, a product row, a thumbnail gallery driven by the '
                 . 'carousel API, and a coverflow of books.',
        'shows' => ['Autoplay with pause', 'Thumbnail gallery', 'Logo marquee', '3D coverflow'],
        'icon'  => 'image',
        'new'   => true,
    ],
];

/* Measure the files rather than describing them. */
foreach ($templates as $i => $t) {
    $path = __DIR__ . '/' . $t['file'];
    $templates[$i]['exists'] = is_readable($path);
    $templates[$i]['lines']  = $templates[$i]['exists'] ? count(file($path)) : 0;
}
$totalLines = array_sum(array_column($templates, 'lines'));
$newCount   = count(array_filter($templates, static fn(array $t): bool => !empty($t['new'])));
$byCat      = [];
foreach ($templates as $t) {
    $byCat[$t['cat']][] = $t;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= $e($site['title']) ?></title>
<link rel="canonical" href="<?= $e($canonical) ?>">
<meta name="description" content="<?= $e($site['description']) ?>">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
<meta name="author" content="<?= $e($site['author']) ?>">

<link rel="icon" href="../assets/images/deck-mark.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="../assets/images/deck-apple-touch-icon.png">

<meta property="og:type" content="website">
<meta property="og:site_name" content="Deck">
<meta property="og:url" content="<?= $e($canonical) ?>">
<meta property="og:title" content="<?= $e($site['title']) ?>">
<meta property="og:description" content="<?= $e($site['description']) ?>">
<meta property="og:image" content="<?= $e($ogImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $e($site['title']) ?>">
<meta name="twitter:description" content="<?= $e($site['description']) ?>">
<meta name="twitter:image" content="<?= $e($ogImage) ?>">

<!-- A CollectionPage listing the templates, each one a WebPageElement with a
     direct URL. The software node lives on the home page and is referenced,
     not redeclared: two URLs claiming one @id teaches a crawler that this
     site's structured data cannot be trusted. -->
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'CollectionPage',
            '@id'         => $canonical . '#gallery',
            'name'        => $site['title'],
            'description' => $site['description'],
            'url'         => $canonical,
            'inLanguage'  => 'en',
            'isPartOf'    => ['@id' => $url() . '#website'],
            'about'       => ['@id' => $url() . '#software'],
            'license'     => 'https://opensource.org/licenses/MIT',
            'author'      => ['@type' => 'Person', 'name' => $site['author']],
            'mainEntity'  => [
                '@type'           => 'ItemList',
                'numberOfItems'   => count($templates),
                'itemListElement' => array_map(
                    static fn(int $i, array $t): array => [
                        '@type'    => 'ListItem',
                        'position' => $i + 1,
                        'name'     => $t['name'],
                        'url'      => $url('templates/' . $t['file']),
                    ],
                    array_keys($templates),
                    $templates
                ),
            ],
        ],
        [
            '@type'           => 'BreadcrumbList',
            '@id'             => $canonical . '#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Deck', 'item' => $url()],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Templates', 'item' => $canonical],
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

<link rel="stylesheet" href="../assets/deck/deck.css">
<script src="../assets/deck/deck.js" defer></script>
<style>
  @layer app.pages {
    .topbar {
      position: sticky; inset-block-start: 0; z-index: var(--z-sticky);
      background: color-mix(in oklab, var(--bg) 84%, transparent);
      backdrop-filter: saturate(1.6) blur(14px);
      -webkit-backdrop-filter: saturate(1.6) blur(14px);
      border-block-end: 1px solid var(--line);
    }

    .gal-head { padding-block: var(--space-12) var(--space-10); border-block-end: 1px solid var(--line); overflow: clip; }
    .gal-hero { display: grid; gap: var(--space-10); align-items: center; }

    /* The fan only exists where there is a second column for it. */
    .hero-fan { display: none; }
    @media (min-width: 64rem) {
      .gal-hero { grid-template-columns: minmax(0, 1fr) minmax(0, 1.1fr); }
      .hero-fan { display: block; position: relative; aspect-ratio: 5 / 4; }
    }
    .fan-card {
      position: absolute;
      inline-size: 74%;
      aspect-ratio: 16 / 10;
      overflow: clip;
      border: 1px solid var(--line);
      border-radius: var(--r-lg);
      background: var(--bg-sunken);
      box-shadow: var(--shadow-4);
    }
    /* Same trick as the gallery previews, at a third rather than a quarter:
       the cards are wider, so 300% still renders the desktop layout. */
    .fan-card iframe {
      position: absolute;
      inset-block-start: 0;
      inset-inline-start: 0;
      inline-size: 300%;
      block-size: 300%;
      max-inline-size: none;
      border: 0;
      scale: calc(1 / 3);
      transform-origin: 0 0;
      pointer-events: none;
    }
    .fan-1 { inset-block-start: 0; inset-inline-start: 0; rotate: -5deg; }
    .fan-2 { inset-block-start: 22%; inset-inline-start: 22%; rotate: 2deg; z-index: 1; }
    .fan-3 { inset-block-start: 40%; inset-inline-start: 8%; rotate: -1.5deg; z-index: 2; }
    .gal-head .display { max-inline-size: 16ch; text-wrap: balance; }
    .gal-head .lede { text-wrap: pretty; }
    .eyebrow { font-size: var(--text-sm); font-weight: 640; color: var(--brand); letter-spacing: .01em; }

    /* The in-page index. Chips, because they wrap on a phone where a tab strip
       would have to scroll, and links, because all they do is jump. */
    .cat-nav .chip { text-decoration: none; color: var(--text); }
    .cat-nav .chip .nums { color: var(--text-faint); }

    .cat { scroll-margin-block-start: var(--space-16); }
    .cat-head .lede { font-size: var(--text-base); max-inline-size: 60ch; }

    /* The prose sections share the wide container's left edge with the
       gallery, but not its width: a 1,376px line is unreadable. */
    .prose-col > * { max-inline-size: 48rem; }

    /* ---------------------------------------------------------------------------
       The preview is the template itself, in an iframe rendered at four times
       the size of its frame and scaled down to a quarter. A frame about 360px
       wide therefore shows the desktop layout at 1440px, whatever the width of
       the gallery — no screenshots to keep in step with the files.

       loading="lazy" means only the previews near the viewport cost anything.
       inert and pointer-events: none keep them out of the tab order and let the
       card's own link take the click; the iframe is decoration, the link below
       it is the content.
       ------------------------------------------------------------------------- */
    .tpl-thumb {
      position: relative;
      aspect-ratio: 16 / 10;
      overflow: clip;
      background: var(--bg-sunken);
      border-block-end: 1px solid var(--line);
    }
    .tpl-thumb iframe {
      position: absolute;
      inset-block-start: 0;
      inset-inline-start: 0;
      inline-size: 400%;
      block-size: 400%;
      max-inline-size: none;   /* deck.reset caps iframes at 100% */
      border: 0;
      scale: .25;
      transform-origin: 0 0;
      pointer-events: none;
      background: var(--bg);
    }
    [dir="rtl"] .tpl-thumb iframe { transform-origin: 100% 0; }

    .tpl-open {
      position: absolute;
      inset-block-end: var(--space-3);
      inset-inline-end: var(--space-3);
      opacity: 0;
      translate: 0 4px;
      transition: opacity var(--dur-2) var(--ease-out), translate var(--dur-2) var(--ease-out);
      box-shadow: var(--shadow-3);
    }
    .tpl-card:is(:hover, :focus-within) .tpl-open { opacity: 1; translate: 0; }
    @media (hover: none) { .tpl-open { display: none; } }

    .tpl-card .card-title a { color: var(--text); text-decoration: none; }
    .tpl-card .card-footer { position: relative; z-index: 1; }
  }
</style>
</head>
<body>

<a class="skip-link" href="#main">Skip to content</a>

<div class="topbar">
  <div class="container container-xl">
    <nav class="navbar" style="border:0" aria-label="Site">
      <a class="navbar-brand" href="../index.php">
        <svg class="icon icon-lg icon-fill" style="color:var(--brand)" aria-hidden="true">
          <use href="../assets/deck/deck-icons.svg#deck-mark"></use>
        </svg>
        Deck
      </a>
      <div class="navbar-links">
        <a class="nav-link" href="../index.php">Home</a>
        <a class="nav-link" href="../docs/index.php">Docs</a>
        <a class="nav-link" href="index.php" aria-current="page">Templates</a>
      </div>
      <div class="push cluster cluster-tight">
        <a class="btn btn-sm btn-ghost" href="../docs/start/install.php">Install</a>
        <button class="btn btn-icon btn-ghost" data-deck-theme aria-label="Switch theme">
          <svg class="icon" aria-hidden="true"><use href="../assets/deck/deck-icons.svg#moon"></use></svg>
        </button>
      </div>
    </nav>
  </div>
</div>

<main id="main">

  <section class="g-mesh-subtle gal-head">
    <div class="container container-xl gal-hero">
    <div class="stack stack-6">
      <nav aria-label="Breadcrumb">
        <ol class="breadcrumb">
          <li><a href="../index.php">Deck</a></li>
          <li aria-current="page">Templates</li>
        </ol>
      </nav>

      <div class="stack stack-4">
        <p class="eyebrow">Templates</p>
        <h1 class="display">Templates you can copy.</h1>
        <p class="lede">
          <?= count($templates) ?> complete pages built with Deck
          <?= $e($site['version']) ?>. Each one is a single HTML file you can open
          in a browser right now — no build step, no package install, nothing to
          compile. Copy the file, repoint the paths at your copy of Deck, and start
          deleting the parts you do not want.
        </p>
      </div>

      <div class="cluster cluster-tight">
        <span class="badge badge-brand">MIT licensed</span>
        <?php if ($newCount): ?>
          <span class="badge badge-solid"><?= $newCount ?> new</span>
        <?php endif; ?>
        <span class="badge">One file each</span>
        <span class="badge nums"><?= number_format($totalLines) ?> lines total</span>
        <span class="badge">No dependencies</span>
      </div>

      <nav class="cat-nav" aria-label="Template categories">
        <ul class="cluster cluster-tight" style="list-style:none;padding:0;margin:0">
          <?php foreach ($categories as $key => $c): ?>
            <li>
              <a class="chip" href="#<?= $e($key) ?>">
                <?= $e($c['name']) ?> <span class="nums"><?= count($byCat[$key] ?? []) ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>

    <!-- Three of the templates, live, fanned out. Decoration: the same pages
         are linked properly in the gallery below, so this is hidden from
         assistive technology and from the tab order. -->
    <div class="hero-fan" aria-hidden="true">
      <?php foreach (['travel.html', 'dashboard.html', 'ecommerce.html'] as $i => $f): ?>
        <?php if (is_readable(__DIR__ . '/' . $f)): ?>
          <div class="fan-card fan-<?= $i + 1 ?>">
            <iframe src="<?= $e($f) ?>" title="" tabindex="-1" inert scrolling="no"></iframe>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
    </div>
  </section>

  <!-- ===== The gallery, one section per category ===== -->
  <div class="container container-xl section stack stack-8">
    <?php foreach ($categories as $key => $c): ?>
      <?php if (empty($byCat[$key])) continue; ?>
      <section class="cat stack stack-6" id="<?= $e($key) ?>" aria-labelledby="<?= $e($key) ?>-title">
        <div class="cat-head stack stack-2">
          <h2 id="<?= $e($key) ?>-title"><?= $e($c['name']) ?></h2>
          <p class="lede"><?= $e($c['lede']) ?></p>
        </div>

        <div class="grid" style="--min:18rem">
          <?php foreach ($byCat[$key] as $t): ?>
            <article class="card card-link tpl-card lift">
              <div class="tpl-thumb">
                <?php if ($t['exists']): ?>
                  <iframe src="<?= $e($t['file']) ?>" title="Preview of the <?= $e($t['name']) ?> template"
                          loading="lazy" tabindex="-1" aria-hidden="true" inert scrolling="no"></iframe>
                <?php endif; ?>
                <span class="tpl-open badge badge-solid" aria-hidden="true">
                  Open preview
                  <svg class="icon icon-sm"><use href="../assets/deck/deck-icons.svg#arrow-up-right-sm"></use></svg>
                </span>
              </div>

              <div class="card-body">
                <div class="bar">
                  <span class="icon-tile" aria-hidden="true">
                    <svg class="icon"><use href="../assets/deck/deck-icons.svg#<?= $e($t['icon']) ?>"></use></svg>
                  </span>
                  <?php if (!empty($t['new'])): ?>
                    <span class="badge badge-brand">New</span>
                  <?php endif; ?>
                  <span class="push text-xs text-muted nums">
                    <?= $t['exists'] ? number_format($t['lines']) . ' lines' : 'missing' ?>
                  </span>
                </div>

                <h3 class="card-title">
                  <a class="stretch" href="<?= $e($t['file']) ?>" target="_blank" rel="noopener">
                    <?= $e($t['name']) ?><span class="sr-only"> (opens in a new tab)</span>
                  </a>
                </h3>
                <p class="text-sm text-muted"><?= $e($t['blurb']) ?></p>

                <ul class="cluster cluster-tight" style="list-style:none;padding:0;margin:0" aria-label="Shows">
                  <?php foreach ($t['shows'] as $s): ?>
                    <li class="badge"><?= $e($s) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>

              <div class="card-footer">
                <a class="btn btn-primary btn-sm" target="_blank" rel="noopener" href="<?= $e($t['file']) ?>">
                  Open it
                  <svg class="icon icon-sm" aria-hidden="true"><use href="../assets/deck/deck-icons.svg#arrow-up-right-sm"></use></svg>
                </a>
                <a class="btn btn-ghost btn-sm"
                   href="<?= $e($site['repo']) ?>/blob/main/public_html/templates/<?= $e($t['file']) ?>"
                   rel="noopener">
                  <svg class="icon icon-sm" aria-hidden="true"><use href="../assets/deck/deck-icons.svg#file-sm"></use></svg>
                  View source
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>

  <hr>

  <!-- ===== How to use one ===== -->
  <section class="container container-xl section stack stack-6 prose-col">
    <h2>How do I use one of these?</h2>

    <p class="lede">
      Save the file, change the paths, open it. There is no step three, because
      there is nothing to build.
    </p>

    <p>
      Every template loads Deck with tags near the top. They point two folders
      up, which is where Deck sits on this site. Repoint them at wherever you put
      Deck and the rest of the file works unchanged. Templates with a carousel, a
      drawer or a quantity stepper load the optional extras file as well:
    </p>

    <pre><code>&lt;link rel="stylesheet" href="../assets/deck/deck.css"&gt;
&lt;script src="../assets/deck/deck.js" defer&gt;&lt;/script&gt;
&lt;script src="../assets/deck/deck-extras.js" defer&gt;&lt;/script&gt;</code></pre>

    <p>
      Icons are the one thing that needs care. They are referenced as
      <code>../assets/deck/deck-icons.svg#name</code>, and that file has to be
      served from your own site. A cross-origin <code>&lt;use href&gt;</code> is
      blocked by the browser and renders nothing at all — so a CDN URL works
      perfectly for the stylesheet and silently fails for icons.
    </p>

    <div class="alert alert-info">
      <svg class="icon" aria-hidden="true"><use href="../assets/deck/deck-icons.svg#info"></use></svg>
      <div>
        <div class="alert-title">They are HTML, not PHP</div>
        <p class="alert-body">
          Deliberately. A plain HTML file drops into Rails, Django, Go, Laravel,
          Astro, a static site, or nothing at all. Rename to
          <code>.php</code> and it keeps working.
        </p>
      </div>
    </div>

    <div class="alert">
      <svg class="icon" aria-hidden="true"><use href="../assets/deck/deck-icons.svg#image"></use></svg>
      <div>
        <div class="alert-title">The photos are placeholders</div>
        <p class="alert-body">
          Templates that use photography load it from Unsplash so the preview
          looks finished. Swap in your own images before you ship; every image
          has its width and height set, so the layout does not move when you do.
        </p>
      </div>
    </div>

    <div class="cluster">
      <a class="btn btn-primary" href="../docs/start/install.php">
        Install Deck
        <svg class="icon icon-sm icon-follow mirror-rtl" aria-hidden="true"><use href="../assets/deck/deck-icons.svg#arrow-right-sm"></use></svg>
      </a>
      <a class="btn" href="../docs/index.php">Read the docs</a>
    </div>
  </section>

  <section class="container container-xl section stack stack-4 prose-col">
    <h2>What is in them?</h2>
    <p>
      Only classes Deck actually ships, plus a short page-local style block in
      <code>@layer app.pages</code> where a layout needs one. The build fails if
      a template uses a class that is neither, so nothing here can quietly rot
      when a class is renamed — the same check that keeps the documentation
      honest covers these files too.
    </p>
    <p class="text-sm text-muted">
      Everything is MIT licensed, like Deck itself. Use them commercially, change
      them, ship them, no attribution needed.
    </p>
  </section>

</main>

<footer class="footer">
  <div class="container container-xl footer-bottom">
    <span>Deck <?= $e($site['version']) ?> · MIT</span>
    <span class="push"><a href="../index.php">Home</a></span>
    <span><a href="../docs/index.php">Docs</a></span>
    <span><a href="<?= $e($site['repo']) ?>" rel="noopener">GitHub</a></span>
  </div>
</footer>

<script>
  /* The previews are same-origin documents running their own copy of deck.js,
     so they pick up a saved theme when they load — but not a change made after
     that. Mirror the attribute into every preview that has already loaded. */
  (() => {
    const root = document.documentElement;
    const frames = () => document.querySelectorAll('.tpl-thumb iframe, .fan-card iframe');
    const apply = (frame) => {
      try {
        const doc = frame.contentDocument;
        const mode = root.getAttribute('data-theme');
        if (!doc) return;
        mode ? doc.documentElement.setAttribute('data-theme', mode)
             : doc.documentElement.removeAttribute('data-theme');
      } catch (e) { /* a preview that is not same-origin keeps its own theme */ }
    };
    new MutationObserver(() => frames().forEach(apply))
      .observe(root, { attributes: true, attributeFilter: ['data-theme'] });
    frames().forEach((f) => f.addEventListener('load', () => apply(f)));
  })();
</script>

</body>
</html>
