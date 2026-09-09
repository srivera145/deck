<?php
/* =============================================================================
   Page config.
   -----------------------------------------------------------------------------
   Every meta tag, the canonical URL, and the JSON-LD below read from this one
   array. Change a fact here and it changes everywhere on the page.

   DECK_SITE_BASE lets the same file serve the canonical deployment and a local
   Helm vhost without editing anything: set it in the environment and the
   canonical, Open Graph, and structured-data URLs all follow. The fallback is
   the canonical URL from package.json.

       DECK_SITE_BASE=http://deck.local php -S 0.0.0.0:80 -t public_html

   No size is typed into this file. Every one is read from dist/sizes.json,
   which build.mjs writes after measuring the files it just produced, so a
   rebuild updates the page and nothing can drift. The counts below are the
   only hand-kept numbers; reproduce them with:

       ls src/*.css | wc -l                   -> 26      source_files
       grep -c '<symbol' src/deck-icons.svg    -> 152 symbols (75 x 2 + 2 marks)
       grep -rhoE '\.[a-zA-Z][\w-]*' src/*.css | sort -u | wc -l   -> classes
   ============================================================================= */

/* dist/sizes.json is generated. If it is missing the build has not run, and
   guessing a number here is exactly the failure this indirection removes. */
$sizesFile = __DIR__ . '/../dist/sizes.json';
if (!is_readable($sizesFile)) {
    http_response_code(500);
    exit('dist/sizes.json is missing. Run `npm run build` first.');
}
$sizes = json_decode(file_get_contents($sizesFile), true, 512, JSON_THROW_ON_ERROR);

/* Bytes -> "26.6 KB", the same decimal KB the build prints. */
$kb = static fn(int $bytes): string => number_format($bytes / 1000, 1) . ' KB';
$br = static fn(string $f): string => $kb($sizes['files'][$f]['brotli']);
$gz = static fn(string $f): string => $kb($sizes['files'][$f]['gzip']);

$site = [
    'name'         => 'Deck',
    'version'      => '0.1.0',
    'base'         => rtrim(getenv('DECK_SITE_BASE') ?: 'https://get-keel.dev/deck', '/'),
    'locale'       => 'en_US',
    'author'       => 'Santos Rivera',
    'repository'   => 'https://github.com/srivera145/deck',
    'license'      => 'https://opensource.org/licenses/MIT',
    'license_name' => 'MIT',

    /* Title: 49 characters. Description: 153. Both carry "CSS framework",
       which is the term people actually search for. */
    'title'        => 'Deck — a CSS framework in one file, no build step',
    'description'  => 'Deck is a CSS framework that ships as one ' . $br('deck.min.css')
                      . ' stylesheet with components, icons, and runtime theming.'
                      . ' No build step, no config file, no dependencies.',

    /* Every size below comes from dist/sizes.json. Brotli leads because that is
       what servers negotiate; gzip is quoted beside it as the fallback. */
    'css_br'       => $br('deck.min.css'),
    'css_gzip'     => $gz('deck.min.css'),
    'sprite_br'    => $br('deck-icons.svg'),
    'sprite_gzip'  => $gz('deck-icons.svg'),
    'js_br'        => $br('deck.min.js'),
    'js_gzip'      => $gz('deck.min.js'),
    'bundle_br'    => $br('deck.bundle.min.js'),
    'bundle_gzip'  => $gz('deck.bundle.min.js'),
    'total_br'     => $kb($sizes['totals']['core']['brotli']),
    'total_gzip'   => $kb($sizes['totals']['core']['gzip']),
    'total_bundle_br'   => $kb($sizes['totals']['bundle']['brotli']),
    'total_bundle_gzip' => $kb($sizes['totals']['bundle']['gzip']),
    'sprite_12_br'      => $kb($sizes['sample']['brotli']),
    'sprite_12_gzip'    => $kb($sizes['sample']['gzip']),
    'source_files' => 26,
    'classes'      => 927,
    'icons'        => 75,
    'symbols'      => 152,
    'icons_avail'  => '4,025',
    'deps'         => 0,
];

/* Absolute URL for a path under the site base. */
$url = static fn(string $path = ''): string =>
    $site['base'] . ($path === '' ? '' : '/' . ltrim($path, '/'));

/* Escape for an HTML attribute or text node. */
$e = static fn(?string $s): string => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

$ogImage = $url('assets/images/deck-og.png');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= $e($site['title']) ?></title>
<link rel="canonical" href="<?= $e($url()) ?>">
<meta name="description" content="<?= $e($site['description']) ?>">

<!-- Let search and answer engines quote the page in full. Nothing here is
     paywalled or time-sensitive, so there is no reason to cap the snippet. -->
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="author" content="<?= $e($site['author']) ?>">

<!-- Brand marks. These are the static ones: an <img> or a <link> gets no colour
     context, so currentColor would resolve to black. Everything on the page
     that should follow --hue-brand uses <use> against the sprite instead.
     Published from src/brand/ by npm run demo. -->
<link rel="icon" href="assets/images/deck-mark.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="assets/images/deck-apple-touch-icon.png">

<!-- Open Graph and Twitter both need an absolute URL and a raster image;
     no social platform renders an SVG og:image. -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= $e($site['name']) ?>">
<meta property="og:locale" content="<?= $e($site['locale']) ?>">
<meta property="og:url" content="<?= $e($url()) ?>">
<meta property="og:title" content="<?= $e($site['title']) ?>">
<meta property="og:description" content="<?= $e($site['description']) ?>">
<meta property="og:image" content="<?= $e($ogImage) ?>">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="The Deck logo, white on the brand teal.">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $e($site['title']) ?>">
<meta name="twitter:description" content="<?= $e($site['description']) ?>">
<meta name="twitter:image" content="<?= $e($ogImage) ?>">
<meta name="twitter:image:alt" content="The Deck logo, white on the brand teal.">

<!-- SoftwareApplication describes the thing you install; SoftwareSourceCode
     describes the repository it is built from. They point at each other.
     No FAQPage: Google retired FAQ rich results on 7 May 2026, and its
     generative-AI guidance (15 May 2026) states no special markup is needed
     for AI Overviews or AI Mode. The question-shaped headings below are the
     part that actually works. -->
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'               => 'SoftwareApplication',
            '@id'                 => $url() . '#software',
            'name'                => $site['name'],
            'description'         => $site['description'],
            'applicationCategory' => 'DeveloperApplication',
            'applicationSubCategory' => 'CSS framework',
            'operatingSystem'     => 'Any modern web browser',
            'softwareVersion'     => $site['version'],
            'url'                 => $url(),
            'image'               => $ogImage,
            'license'             => $site['license'],
            'isAccessibleForFree' => true,
            'author'              => ['@type' => 'Person', 'name' => $site['author']],
            'publisher'           => ['@type' => 'Person', 'name' => $site['author']],
            'offers'              => [
                '@type'         => 'Offer',
                'price'         => '0',
                'priceCurrency' => 'USD',
            ],
            'featureList' => [
                'No build step: one stylesheet linked with a single link tag',
                'No configuration file',
                'Zero runtime dependencies',
                'Retheme the whole palette from one CSS custom property at runtime',
                'Ships in cascade layers, so application CSS overrides it without !important',
                'Right-to-left support built in through logical properties',
                'SVG icon sprite',
            ],
            'isBasedOn'           => ['@id' => $url() . '#source'],
        ],
        [
            '@type'               => 'SoftwareSourceCode',
            '@id'                 => $url() . '#source',
            'name'                => $site['name'],
            'description'         => $site['description'],
            'codeRepository'      => $site['repository'],
            'programmingLanguage' => [
                ['@type' => 'ComputerLanguage', 'name' => 'CSS'],
                ['@type' => 'ComputerLanguage', 'name' => 'JavaScript'],
            ],
            'runtimePlatform'     => 'Web browser',
            'codeSampleType'      => 'full solution',
            'version'             => $site['version'],
            'license'             => $site['license'],
            'author'              => ['@type' => 'Person', 'name' => $site['author']],
            'targetProduct'       => ['@id' => $url() . '#software'],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

<link rel="stylesheet" href="assets/deck/deck.css">
<script src="assets/deck/deck.js" defer></script>
<script src="assets/deck/deck-extras.js" defer></script>
<script src="assets/deck/deck-adapters.js" defer></script>
<style>
  /* Page-specific styles only. Everything else comes from Deck. */
  .hero { padding-block: var(--space-12) var(--space-10); }
  .hero .display { max-inline-size: 15ch; }
  .swatch { block-size: 44px; border-radius: var(--r-sm); border: 1px solid var(--line); }
  .theme-dock {
    position: sticky;
    inset-block-start: 0;
    z-index: var(--z-sticky);
    background: color-mix(in oklab, var(--bg) 84%, transparent);
    backdrop-filter: saturate(1.6) blur(14px);
    -webkit-backdrop-filter: saturate(1.6) blur(14px);
    border-block-end: 1px solid var(--line);
  }
  .ramp { display: grid; grid-template-columns: repeat(11, 1fr); gap: 2px; }
  .ramp > div { block-size: 40px; border-radius: 3px; }
  .demo-label { font-size: var(--text-xs); font-weight: 620; color: var(--text-faint); }

  /* This demo puts more links in the nav than a 1216px container comfortably
     holds. Left alone, the row runs out of width and two things give: a
     two-word .nav-link breaks mid-label, and the .cluster holding the hue
     slider and the theme button wraps, dropping the button onto its own line.
     .cluster is a wrapping primitive by design, so the fix belongs here on the
     dock rather than in the framework.

     Labels never break; the controls stay pinned to the end of the row; and if
     the links ever outgrow the space again they scroll sideways instead of
     pushing the controls off. */
  .theme-dock .nav-link { white-space: nowrap; flex: 0 0 auto; }
  .theme-dock .navbar-links {
    min-inline-size: 0;          /* a flex item will not shrink below content without this */
    overflow-x: auto;
    scrollbar-width: none;
  }
  .theme-dock .navbar-links::-webkit-scrollbar { display: none; }
  .theme-dock .navbar > .push {
    flex: 0 0 auto;
    flex-wrap: nowrap;
  }
</style>
</head>
<body>

<span id="top" tabindex="-1"></span>
<a class="skip-link" href="#main">Skip to content</a>
<div class="banner" data-dismiss-key="demo">
  <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#sparkle-sm"></use></svg>
  <span>Deck v0.1 — one stylesheet, no build step, no dependencies.</span>
  <button class="banner-close" aria-label="Dismiss"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#x-sm"></use></svg></button>
</div>
<div class="scroll-progress" aria-hidden="true"></div>

<!-- ===================== Theme dock ===================== -->
<div class="theme-dock">
  <div class="container">
    <div class="navbar" style="border:0">
      <a class="navbar-brand" href="#">
        <svg class="icon icon-lg icon-fill" style="color:var(--brand)"><use href="assets/deck/deck-icons.svg#deck-mark"></use></svg>
        Deck
      </a>
      <nav class="navbar-links">
        <!-- The brand mark to the left already links to the top, so a separate
             "Overview" link was redundant and cost the row 83px it did not have. -->
        <a class="nav-link" aria-current="page" href="#about">About</a>
        <a class="nav-link" href="#compare">Compare</a>
        <a class="nav-link" href="#forms">Forms</a>
        <a class="nav-link" href="#grid">Grid</a>
        <a class="nav-link" href="#charts">Charts</a>
        <a class="nav-link" href="#motion">Motion</a>
        <a class="nav-link" href="#gradients">Gradients</a>
        <a class="nav-link" href="#space">3D</a>
        <a class="nav-link" href="#more">More</a>
        <a class="nav-link" href="#gallery">Gallery</a>
        <a class="nav-link" href="#sidebar">Sidebar</a>
        <a class="nav-link" href="#tooltips">Tooltips</a>
        <a class="nav-link" href="#libs">Libraries</a>
      </nav>
      <div class="push cluster cluster-tight">
        <label class="sr-only" for="hue">Brand hue</label>
        <input id="hue" class="range" type="range" min="0" max="360" value="196"
               style="inline-size:104px" oninput="document.documentElement.style.setProperty('--hue-brand', this.value)">
        <button class="btn btn-icon btn-ghost" id="themeBtn" aria-label="Switch theme">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#moon"></use></svg>
        </button>
      </div>
    </div>
  </div>
</div>

<main id="main">

  <!-- ===================== Hero ===================== -->
  <section class="container hero stack-6">
    <div class="cluster cluster-tight">
      <span class="badge badge-brand badge-dot">v0.1</span>
      <span class="badge">One stylesheet</span>
      <span class="badge">No build step</span>
      <span class="badge">0 dependencies</span>
    </div>
    <h1 class="display mb-3">A CSS framework you add with one link tag.</h1>
    <p class="lede mb-2">
      Deck is a single stylesheet: buttons, forms, tables, a data grid, charts,
      overlays, an icon sprite, and a full color system. There is no build step, no
      config file, no purge pass, and nothing to install. It ships in cascade layers,
      so your own CSS wins without <code>!important</code>, and every color on the page
      derives from one number you can change at runtime.
    </p>
    <div class="cluster">
      <a class="btn btn-primary btn-lg" href="#main">
        Get started
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#arrow-right-sm"></use></svg>
      </a>
      <button class="btn btn-lg" popovertarget="installMenu">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#copy-sm"></use></svg>
        Copy the link tag
      </button>
      <div class="menu" id="installMenu" popover style="position:fixed;inset-block-start:auto">
        <div class="menu-label">That is the whole install</div>
        <code style="display:block;padding:var(--space-3);white-space:pre-wrap;font-size:var(--text-xs)">&lt;link rel="stylesheet" href="/assets/deck/deck.css"&gt;</code>
      </div>
    </div>

    <div class="ramp mt-4" aria-hidden="true">
      <div style="background:var(--brand-50)"></div>
      <div style="background:var(--brand-100)"></div>
      <div style="background:var(--brand-200)"></div>
      <div style="background:var(--brand-300)"></div>
      <div style="background:var(--brand-400)"></div>
      <div style="background:var(--brand-500)"></div>
      <div style="background:var(--brand-600)"></div>
      <div style="background:var(--brand-700)"></div>
      <div style="background:var(--brand-800)"></div>
      <div style="background:var(--brand-900)"></div>
      <div style="background:var(--brand-950)"></div>
    </div>
    <p class="text-sm text-muted">Those eleven steps, every button, link, focus ring,
      badge, chart series, and shadow on this page are computed from
      <code>--hue-brand</code>. Drag the slider in the header and watch all of it
      retune, live, with no rebuild.</p>
  </section>

  <hr>

  <!-- ===================== What / how / why =====================
       Question-shaped headings, each answered completely in its first
       sentence, so a paragraph still makes sense lifted out on its own. -->
  <section class="container section stack-6" id="about">
    <h2>What is Deck?</h2>
    <p class="lede">Deck is a CSS framework that ships as a single
      <?= $e($site['css_br']) ?> stylesheet containing buttons, forms, tables,
      a data grid, charts, overlays, an icon sprite, and a complete color system. You add
      it to a page with one <code>&lt;link&gt;</code> tag, and it has no build step, no
      configuration file, and <?= (int) $site['deps'] ?> runtime dependencies.</p>

    <p class="text-muted">Deck is written as <?= (int) $site['source_files'] ?> plain CSS
      files that are concatenated into <code>deck.css</code>, defining
      <?= number_format($site['classes']) ?> classes. It is published under the
      <?= $e($site['license_name']) ?> license, and version
      <?= $e($site['version']) ?> is the release documented on this page.</p>

    <h3>What does Deck actually weigh?</h3>
    <p>A page that loads the stylesheet, the icon sprite, and the optional JavaScript
      transfers <?= $e($site['total_br']) ?> Brotli, or <?= $e($site['total_gzip']) ?> gzip.
      Every browser Deck supports sends <code>br</code> in <code>Accept-Encoding</code>, and
      Cloudflare, Vercel, Netlify, and nginx with <code>ngx_brotli</code> negotiate it for
      text by default, so Brotli is what most users receive.</p>

    <div class="table-wrap">
      <table class="table table-stack">
        <caption class="sr-only">Transfer size of each Deck file, Brotli and gzip</caption>
        <thead>
          <tr>
            <th scope="col">File</th>
            <th scope="col" class="num">Brotli</th>
            <th scope="col" class="num">gzip</th>
            <th scope="col">What it is</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row" data-label="File"><code>deck.min.css</code></th>
            <td data-label="Brotli" class="num nums"><strong><?= $e($site['css_br']) ?></strong></td>
            <td data-label="gzip" class="num nums"><?= $e($site['css_gzip']) ?></td>
            <td data-label="What it is">The whole framework. Fixed size.</td>
          </tr>
          <tr>
            <th scope="row" data-label="File"><code>deck-icons.svg</code></th>
            <td data-label="Brotli" class="num nums"><strong><?= $e($site['sprite_br']) ?></strong></td>
            <td data-label="gzip" class="num nums"><?= $e($site['sprite_gzip']) ?></td>
            <td data-label="What it is"><?= (int) $site['icons'] ?> icons at two weights,
              <?= (int) $site['symbols'] ?> symbols. Only if you use the icons.</td>
          </tr>
          <tr>
            <th scope="row" data-label="File"><code>deck.min.js</code></th>
            <td data-label="Brotli" class="num nums"><strong><?= $e($site['js_br']) ?></strong></td>
            <td data-label="gzip" class="num nums"><?= $e($site['js_gzip']) ?></td>
            <td data-label="What it is">Optional. Only for components that need behaviour.</td>
          </tr>
          <tr>
            <th scope="row" data-label="File"><strong>All three</strong></th>
            <td data-label="Brotli" class="num nums"><strong><?= $e($site['total_br']) ?></strong></td>
            <td data-label="gzip" class="num nums"><strong><?= $e($site['total_gzip']) ?></strong></td>
            <td data-label="What it is">The honest total for a page that uses everything.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p class="text-muted">The sprite is the largest single file, slightly bigger than the
      stylesheet, which is worth saying plainly rather than leaving for you to find in
      devtools. Swapping <code>deck.min.js</code> for the full
      <code>deck.bundle.min.js</code>, which adds the date picker, combobox, data grid,
      toasts, and QR encoder, makes the JavaScript <?= $e($site['bundle_br']) ?> and the
      total <?= $e($site['total_bundle_br']) ?> Brotli
      (<?= $e($site['total_bundle_gzip']) ?> gzip). Every figure here is measured by
      <code>npm run build</code>; the page reads them from <code>dist/sizes.json</code>
      rather than carrying its own copy.</p>

    <h3>The sprite is a manifest, not a fixed cost</h3>
    <p>The <?= (int) $site['icons'] ?> icons in the sprite are a default so the demo works
      out of the box, not a floor you have to pay. <code>tools/icons/icons.txt</code> is a
      plain list of the icons to extract: delete the lines you do not need, run
      <code>npm run icons</code>, and the sprite is rebuilt with only what is left.</p>

    <pre><code># keep only the icons you use
$ cat &gt; tools/icons/icons.txt &lt;&lt;'EOF'
check    = check
search   = search
settings = settings
@hand deck-mark
@hand deck-wordmark
EOF

$ npm run icons</code></pre>

    <p><strong>A twelve icon sprite measures <?= $e($site['sprite_12_br']) ?> Brotli
      (<?= $e($site['sprite_12_gzip']) ?> gzip)</strong> — that figure was generated and
      measured, not estimated. Against
      <?= $e($site['sprite_br']) ?> for the full set, trimming the manifest to what a
      project actually uses is the difference between the sprite dominating the page
      weight and disappearing into it.</p>

    <p class="text-muted">This matters because an external sprite is all or nothing per
      request: the browser fetches the whole file to resolve one
      <code>&lt;use&gt;</code>, so an unused icon is not free the way an unused CSS class
      is. That is the reason the manifest exists. The two <code>@hand</code> lines carry
      the brand marks through from the previous sprite; drop them and the marks are
      dropped too.</p>

    <h3>Do I need a build step to use Deck?</h3>
    <p>No — Deck needs no build step, because it is distributed as a finished stylesheet
      with no compiler, no bundler plugin, no PostCSS pipeline, and no purge pass between
      you and a styled page. The file you download is the file the browser reads, which means a Deck
      project has nothing to rebuild when you change a color, and nothing to reinstall
      when you clone it onto a new machine.</p>

    <h3>How do I install Deck?</h3>
    <p>Add one <code>&lt;link&gt;</code> tag pointing at <code>deck.css</code> and the
      framework is installed. The JavaScript file is optional and only needed for
      components that require behaviour, such as the date picker, the combobox, and the
      toast queue; every other component works as pure CSS with the script absent.</p>
    <pre><code>&lt;link rel="stylesheet" href="/assets/deck/deck.css"&gt;
&lt;script src="/assets/deck/deck.js" defer&gt;&lt;/script&gt;  &lt;!-- optional --&gt;</code></pre>
    <p class="text-muted">Deck is also on npm as <code>@echodial/deck</code> and on
      Packagist as <code>echodial/deck</code>, and the
      <code>npx @echodial/deck init public/assets/deck</code> command copies the files
      into a project without installing anything permanently.</p>

    <h3>How do I change the color scheme?</h3>
    <p>Set <code>--hue-brand</code> to a number between 0 and 360 and every brand color
      in the framework is recomputed from it. Deck derives its whole palette from six hue
      values in <a href="#layers">oklch</a>, so a theme change is one custom property
      rather than a rebuild, a second stylesheet, or a set of overrides.</p>
    <pre><code>:root { --hue-brand: 265; }        /* violet instead of teal */
&lt;html style="--hue-brand: 320"&gt;   /* or per tenant, at runtime */</code></pre>

    <h3>What browsers does Deck support?</h3>
    <p>Deck targets Chrome 117, Edge 117, Safari 17.4, and Firefox 128 and newer, which
      are the versions that shipped cascade layers, container queries, <code>oklch()</code>,
      and the <code>popover</code> attribute. Those features are load-bearing rather than
      progressive enhancements, so Deck does not attempt to support browsers released
      before them.</p>

    <h3>Is Deck free to use commercially?</h3>
    <p>Yes — Deck is <?= $e($site['license_name']) ?> licensed, which permits commercial
      use, modification, and redistribution provided the copyright notice is kept. The
      bundled icon outlines are derived from Google's Material Symbols and carry the
      Apache License 2.0, whose notice is reproduced in the sprite's header comment.</p>
  </section>

  <hr>

  <!-- ===================== Comparison ===================== -->
  <section class="container section stack-6" id="compare">
    <h2>How is Deck different from Tailwind CSS and Bootstrap?</h2>
    <p class="lede">Deck differs from Tailwind CSS and Bootstrap in that it has no build
      step and no configuration file, and its entire palette can be rethemed at runtime
      by changing one CSS custom property. Tailwind generates a stylesheet from your
      markup at build time, and Bootstrap needs a Sass compile to customise beyond its
      CSS variables; Deck is a fixed file that you link and then override with ordinary
      CSS.</p>

    <div class="table-wrap">
      <table class="table table-stack">
        <caption class="sr-only">Deck compared with Tailwind CSS and Bootstrap</caption>
        <thead>
          <tr><th scope="col">Question</th><th scope="col">Deck</th><th scope="col">Tailwind CSS</th><th scope="col">Bootstrap</th></tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row" data-label="Question">Build step</th>
            <td data-label="Deck">None</td>
            <td data-label="Tailwind CSS">Required — the stylesheet is generated from your markup</td>
            <td data-label="Bootstrap">Not for the prebuilt CSS; required to customise via Sass</td>
          </tr>
          <tr>
            <th scope="row" data-label="Question">Config file</th>
            <td data-label="Deck">None</td>
            <td data-label="Tailwind CSS"><code>tailwind.config.js</code>, or a CSS <code>@theme</code> block in v4</td>
            <td data-label="Bootstrap">Sass variables</td>
          </tr>
          <tr>
            <th scope="row" data-label="Question">Runtime dependencies</th>
            <td data-label="Deck">0</td>
            <td data-label="Tailwind CSS">0 in the output CSS; a Node toolchain to produce it</td>
            <td data-label="Bootstrap">0 for the CSS; Popper for dropdown and tooltip JavaScript</td>
          </tr>
          <tr>
            <th scope="row" data-label="Question">CSS size, compressed</th>
            <td data-label="Deck"><?= $e($site['css_br']) ?> Brotli, fixed</td>
            <td data-label="Tailwind CSS">Varies with how many utilities you use</td>
            <td data-label="Bootstrap">Fixed; see their release notes for the current figure</td>
          </tr>
          <tr>
            <th scope="row" data-label="Question">Retheme without rebuilding</th>
            <td data-label="Deck">Yes — one custom property, at runtime</td>
            <td data-label="Tailwind CSS">Partly — v4 exposes CSS variables; config changes need a rebuild</td>
            <td data-label="Bootstrap">Partly — v5.3 exposes CSS variables; Sass changes need a recompile</td>
          </tr>
          <tr>
            <th scope="row" data-label="Question">Right-to-left</th>
            <td data-label="Deck">Built in — set <code>dir="rtl"</code>, no second file</td>
            <td data-label="Tailwind CSS">Logical-property utilities; a plugin for full coverage</td>
            <td data-label="Bootstrap">A separate right-to-left stylesheet build</td>
          </tr>
          <tr>
            <th scope="row" data-label="Question">Overriding the framework</th>
            <td data-label="Deck">Cascade layers — unlayered CSS always wins</td>
            <td data-label="Tailwind CSS">Utility order and <code>!important</code> where needed</td>
            <td data-label="Bootstrap">Specificity, or <code>!important</code></td>
          </tr>
          <tr>
            <th scope="row" data-label="Question">Ecosystem and plugins</th>
            <td data-label="Deck">None</td>
            <td data-label="Tailwind CSS">Large — component kits, plugins, templates</td>
            <td data-label="Bootstrap">Large — themes, plugins, long-standing community</td>
          </tr>
          <tr>
            <th scope="row" data-label="Question">Editor tooling</th>
            <td data-label="Deck">None</td>
            <td data-label="Tailwind CSS">Official IntelliSense extension</td>
            <td data-label="Bootstrap">Community extensions and snippets</td>
          </tr>
          <tr>
            <th scope="row" data-label="Question">Maturity</th>
            <td data-label="Deck">Version <?= $e($site['version']) ?>, one author</td>
            <td data-label="Tailwind CSS">Established, funded, large team</td>
            <td data-label="Bootstrap">Established since 2011, large team</td>
          </tr>
        </tbody>
      </table>
    </div>

    <h3>Where Deck loses</h3>
    <p>Deck has no plugin marketplace, no third-party component kits, no editor
      autocomplete extension, and one maintainer, so a team that needs a large hiring
      pool or an off-the-shelf admin template is better served by Tailwind CSS or
      Bootstrap. Deck also has a fixed stylesheet size: a page that uses six components
      downloads the same <?= $e($site['css_br']) ?> as a page that uses all of them,
      whereas Tailwind's generated output scales down with usage.</p>

    <p class="text-muted">Deck's figures above are measured from this repository with
      <code>npm run build</code>. The Tailwind CSS and Bootstrap columns describe
      documented behaviour rather than measurements taken here; both projects change
      between releases, so check their current documentation before relying on a number.</p>
  </section>

  <hr>

  <!-- ===================== Buttons ===================== -->
  <section class="container section stack-6">
    <div class="stack-4">
      <h2>Buttons</h2>
      <p class="text-muted mb-1">Seven variants, three sizes, groups, and a loading state.</p>
    </div>

    <div class="cluster mb-2">
      <button class="btn btn-primary">Save changes</button>
      <button class="btn">Cancel</button>
      <button class="btn btn-soft">Duplicate</button>
      <button class="btn btn-outline">Preview</button>
      <button class="btn btn-ghost">Skip</button>
      <button class="btn btn-accent">Upgrade</button>
      <button class="btn btn-danger">Delete project</button>
      <button class="btn" disabled>Unavailable</button>
      <button class="btn btn-primary is-loading">Submitting</button>
    </div>

    <div class="cluster">
      <button class="btn btn-sm">Small</button>
      <button class="btn">Default</button>
      <button class="btn btn-lg">Large</button>
      <button class="btn btn-icon" aria-label="Settings">
        <svg class="icon"><use href="assets/deck/deck-icons.svg#settings"></use></svg>
      </button>
      <button class="btn btn-icon btn-round btn-primary" aria-label="Add">
        <svg class="icon"><use href="assets/deck/deck-icons.svg#plus"></use></svg>
      </button>
      <div class="btn-group">
        <button class="btn btn-sm">Day</button>
        <button class="btn btn-sm">Week</button>
        <button class="btn btn-sm">Month</button>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Cards + stats ===================== -->
  <section class="container section stack-6">
    <h2 class="mb-2">Cards and stats</h2>

    <div class="grid mb-3">
      <article class="card">
        <div class="card-body">
          <div class="stat">
            <span class="stat-label">Orders this month</span>
            <span class="stat-value">1,284</span>
            <span class="stat-delta stat-delta-up">
              <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#trend-up-sm"></use></svg>
              12.4% vs last month
            </span>
          </div>
        </div>
      </article>

      <article class="card">
        <div class="card-body">
          <div class="stat">
            <span class="stat-label">Average time to ship</span>
            <span class="stat-value">3.2 days</span>
            <span class="stat-delta stat-delta-down">
              <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#trend-down-sm"></use></svg>
              0.6 days faster
            </span>
          </div>
        </div>
      </article>

      <article class="card card-link">
        <div class="card-body">
          <div class="cluster cluster-tight">
            <span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#clipboard"></use></svg></span>
            <div class="grow">
              <h3 class="card-title"><a class="link-quiet stretch" href="#main">Open support tickets</a></h3>
              <p class="text-sm text-muted">17 waiting on a reply</p>
            </div>
            <svg class="icon icon-muted"><use href="assets/deck/deck-icons.svg#chevron-right"></use></svg>
          </div>
        </div>
      </article>
    </div>

    <div class="grid grid-wide">
      <article class="card">
        <header class="card-header">
          <span class="avatar">PL</span>
          <div class="grow">
            <div class="fw-semi">Priya Lakhani</div>
            <div class="text-sm text-muted">Engineering manager</div>
          </div>
          <button class="btn btn-icon btn-ghost btn-sm" aria-label="More">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#more-horizontal"></use></svg>
          </button>
        </header>
        <div class="card-body">
          <p class="text-muted">Opened eleven pull requests this week. Two are blocked on
            a review from the platform team.</p>
          <div class="cluster cluster-tight">
            <span class="badge badge-good">9 merged</span>
            <span class="badge badge-warn">2 blocked</span>
          </div>
        </div>
        <footer class="card-footer">
          <button class="btn btn-sm">Message</button>
          <button class="btn btn-sm btn-primary">Review queue</button>
        </footer>
      </article>

      <div class="stack-4">
        <div class="alert alert-info mb-2">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#info"></use></svg>
          <div>
            <div class="alert-title">Scheduled maintenance</div>
            <p class="alert-body">The API will be read only on Sunday from 02:00 to 04:00 UTC.</p>
          </div>
        </div>
        <div class="alert alert-good mb-2">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#check-circle"></use></svg>
          <div>
            <div class="alert-title">Deploy succeeded</div>
            <p class="alert-body">Build 4471 is live on production. 42 seconds, no errors.</p>
          </div>
        </div>
        <div class="alert alert-warn mb-2">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#alert-triangle"></use></svg>
          <div>
            <div class="alert-title">Storage is nearly full</div>
            <p class="alert-body">You are using 47 GB of your 50 GB plan.</p>
          </div>
        </div>
        <div class="alert alert-bad mb-2">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#x-circle"></use></svg>
          <div>
            <div class="alert-title">Payment failed</div>
            <p class="alert-body">The card ending 4242 was declined. Update it to keep the plan active.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Forms ===================== -->
  <section class="container section stack-6" id="forms">
    <div class="stack-2">
      <h2>Forms</h2>
      <p class="text-muted">Inputs are 16px on touch devices, so iOS never zooms when a
        field takes focus. Every control clears a 44px target.</p>
    </div>

    <div class="split">
      <form class="stack-5">
        <div class="field-row">
          <div class="field">
            <label class="label" for="wsname">Workspace name <span class="required">*</span></label>
            <input class="input" id="wsname" placeholder="Acme Design" required>
            <span class="help">Shown in the header and on invoices.</span>
          </div>
          <div class="field">
            <label class="label" for="wsslug">URL slug <span class="optional">optional</span></label>
            <input class="input" id="wsslug" placeholder="acme-design">
          </div>
        </div>

        <div class="field">
          <label class="label" for="email">Billing email</label>
          <input class="input" id="email" type="email" value="not-an-email" required>
          <span class="error">
            <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#alert-circle-sm"></use></svg>
            Enter an address in the form name@example.com
          </span>
        </div>

        <div class="field">
          <label class="label" for="type">Default role for new members</label>
          <select class="select" id="type">
            <option>Viewer</option>
            <option>Editor</option>
            <option>Admin</option>
            <option>Owner</option>
          </select>
        </div>

        <div class="field">
          <label class="label" for="seatrate">Seat price</label>
          <div class="input-group">
            <span class="addon">$</span>
            <input class="input" id="seatrate" inputmode="decimal" placeholder="0.00">
            <span class="addon">per month</span>
          </div>
        </div>

        <div class="field">
          <label class="label" for="story">Workspace description</label>
          <textarea class="textarea" id="story" placeholder="What this workspace is for, and who should join it."></textarea>
          <span class="help">This box grows as you type.</span>
        </div>

        <div class="field">
          <span class="label">Email me when</span>
          <label class="check">
            <input type="checkbox" checked>
            <span class="check-text">
              <span>Someone mentions me</span>
              <span class="check-note">Sent within a minute of the comment.</span>
            </span>
          </label>
          <label class="check">
            <input type="checkbox">
            <span class="check-text"><span>A deploy fails</span></span>
          </label>
          <label class="check">
            <input type="checkbox" id="indet">
            <span class="check-text"><span>Weekly digest</span></span>
          </label>
        </div>

        <label class="switch">
          <input type="checkbox" checked>
          <span>Require two-factor authentication for everyone</span>
        </label>

        <div class="stack-3">
          <span class="label">Plan</span>
          <div class="grid grid-tight">
            <label class="check check-card">
              <input type="radio" name="pri" checked>
              <span class="check-text">
                <span class="fw-semi">Team</span>
                <span class="check-note">$12 per seat, billed monthly</span>
              </span>
            </label>
            <label class="check check-card">
              <input type="radio" name="pri">
              <span class="check-text">
                <span class="fw-semi">Enterprise</span>
                <span class="check-note">SSO, audit log, and a support SLA</span>
              </span>
            </label>
          </div>
        </div>

        <label class="file">
          <input type="file">
          <svg class="icon icon-xl icon-muted"><use href="assets/deck/deck-icons.svg#upload"></use></svg>
          <span class="fw-semi text-inherit">Drop a workspace logo here</span>
          <span class="text-sm">SVG, PNG, or JPG up to 20 MB</span>
        </label>

        <div class="form-actions">
          <button class="btn btn-primary" type="button">Save settings</button>
          <button class="btn" type="button">Cancel</button>
        </div>
      </form>

      <aside class="stack-4">
        <div class="search">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#search"></use></svg>
          <input class="input" type="search" placeholder="Search invoices">
        </div>

        <div class="segmented" role="tablist">
          <button role="tab" aria-selected="true">Paid</button>
          <button role="tab" aria-selected="false">Open</button>
          <button role="tab" aria-selected="false">Overdue</button>
        </div>

        <div class="list">
          <div class="list-header">Today</div>
          <a class="list-row" href="#forms">
            <span class="icon-tile icon-tile-good"><svg class="icon"><use href="assets/deck/deck-icons.svg#check"></use></svg></span>
            <span class="list-main">
              <span class="list-title">INV-2041</span>
              <span class="list-sub truncate">Northwind Traders · 24 seats, March</span>
            </span>
            <span class="list-trail nums">$842.16</span>
          </a>
          <a class="list-row" href="#forms">
            <span class="icon-tile icon-tile-warn"><svg class="icon"><use href="assets/deck/deck-icons.svg#clock"></use></svg></span>
            <span class="list-main">
              <span class="list-title">INV-2040</span>
              <span class="list-sub truncate">Globex Corp · awaiting a purchase order</span>
            </span>
            <span class="list-trail nums">$318.00</span>
          </a>
          <a class="list-row" href="#forms">
            <span class="icon-tile icon-tile-bad"><svg class="icon"><use href="assets/deck/deck-icons.svg#x"></use></svg></span>
            <span class="list-main">
              <span class="list-title">INV-2038</span>
              <span class="list-sub truncate">Initech · card declined twice</span>
            </span>
            <span class="list-trail nums">$0.00</span>
          </a>
        </div>

        <div class="stack-2">
          <span class="demo-label">Progress</span>
          <progress class="progress" value="68" max="100"></progress>
          <div class="cluster">
            <div class="ring-wrap">
              <div class="ring" style="--value:68"></div>
              <span class="ring-label">68%</span>
            </div>
            <div class="spinner"></div>
            <span class="text-sm text-muted">Importing from CSV</span>
          </div>
        </div>

        <div class="stack-2">
          <span class="demo-label">Loading</span>
          <div class="card"><div class="card-body">
            <div class="cluster cluster-tight">
              <div class="skeleton skeleton-circle" style="inline-size:40px;block-size:40px"></div>
              <div class="grow stack-1">
                <div class="skeleton skeleton-text" style="inline-size:60%"></div>
                <div class="skeleton skeleton-text" style="inline-size:40%"></div>
              </div>
            </div>
          </div></div>
        </div>
      </aside>
    </div>
  </section>

  <hr>

  <!-- ===================== Table ===================== -->
  <section class="container section stack-6">
    <div class="bar">
      <h2>Tables</h2>
      <div class="push cluster cluster-tight">
        <button class="btn btn-sm">
          <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#filter-sm"></use></svg>
          Filter
        </button>
        <button class="btn btn-sm">
          <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#download-sm"></use></svg>
          Export
        </button>
      </div>
    </div>
    <p class="text-muted">Below 640px this table restacks into labelled rows instead of
      forcing a sideways scroll. Resize the window to see it.</p>

    <div class="table-wrap">
      <table class="table table-stack">
        <thead>
          <tr><th>Order</th><th>Customer</th><th>Status</th><th class="num">Total</th><th class="num">Age</th></tr>
        </thead>
        <tbody>
          <tr>
            <td data-label="Order"><a href="#main">#1042</a></td>
            <td data-label="Customer">Northwind Traders</td>
            <td data-label="Status"><span class="badge badge-good">Shipped</span></td>
            <td data-label="Total" class="num nums">$842.16</td>
            <td data-label="Age" class="num nums">2d</td>
          </tr>
          <tr>
            <td data-label="Order"><a href="#main">#1041</a></td>
            <td data-label="Customer">Globex Corp</td>
            <td data-label="Status"><span class="badge badge-warn">Packing</span></td>
            <td data-label="Total" class="num nums">$318.00</td>
            <td data-label="Age" class="num nums">5d</td>
          </tr>
          <tr>
            <td data-label="Order"><a href="#main">#1039</a></td>
            <td data-label="Customer">Initech</td>
            <td data-label="Status"><span class="badge badge-bad">Refunded</span></td>
            <td data-label="Total" class="num nums">$0.00</td>
            <td data-label="Age" class="num nums">11d</td>
          </tr>
        </tbody>
      </table>
    </div>

    <nav class="pagination" aria-label="Pages">
      <a href="#main" aria-label="Previous"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#chevron-left-sm"></use></svg></a>
      <a href="#main" aria-current="page">1</a>
      <a href="#main">2</a>
      <a href="#main">3</a>
      <span>…</span>
      <a href="#main">18</a>
      <a href="#main" aria-label="Next"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#chevron-right-sm"></use></svg></a>
    </nav>
  </section>

  <hr>

  <!-- ===================== Navigation + disclosure ===================== -->
  <section class="container section stack-6">
    <h2>Navigation and disclosure</h2>

    <nav class="breadcrumb" aria-label="Breadcrumb">
      <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
        <li><a href="#main">Dashboard</a></li>
        <li><a href="#main">Orders</a></li>
        <li aria-current="page">#1042</li>
      </ol>
    </nav>

    <div class="tabs" role="tablist">
      <button class="tab" role="tab" aria-selected="true">Summary</button>
      <button class="tab" role="tab" aria-selected="false">Items</button>
      <button class="tab" role="tab" aria-selected="false">Shipping</button>
      <button class="tab" role="tab" aria-selected="false">Invoices</button>
      <button class="tab" role="tab" aria-selected="false">History</button>
    </div>

    <div class="split">
      <div class="accordion">
        <details open>
          <summary>
            <svg class="icon icon-muted"><use href="assets/deck/deck-icons.svg#help"></use></svg>
            How do I invite someone to a workspace?
          </summary>
          <div class="accordion-body">Settings, then Members, then Invite. They get an
            email with a link that expires after seven days.</div>
        </details>
        <details>
          <summary>
            <svg class="icon icon-muted"><use href="assets/deck/deck-icons.svg#help"></use></svg>
            Can I change my plan mid-cycle?
          </summary>
          <div class="accordion-body">Yes. Upgrades take effect immediately and we prorate
            the difference. Downgrades apply at the start of the next billing period.</div>
        </details>
        <details>
          <summary>
            <svg class="icon icon-muted"><use href="assets/deck/deck-icons.svg#help"></use></svg>
            Where do I find my API key?
          </summary>
          <div class="accordion-body">Settings, then Developers. Keys are shown once at
            creation time, so store it somewhere safe before you close the dialog.</div>
        </details>
      </div>

      <div class="timeline">
        <div class="timeline-item is-done">
          <span class="timeline-dot"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-sm"></use></svg></span>
          <div><div class="fw-semi">Order placed</div><div class="text-sm text-muted">Mar 3, 8:14 AM</div></div>
        </div>
        <div class="timeline-item is-done">
          <span class="timeline-dot"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-sm"></use></svg></span>
          <div><div class="fw-semi">Handed to the carrier</div><div class="text-sm text-muted">Mar 3, 2:40 PM</div></div>
        </div>
        <div class="timeline-item">
          <span class="timeline-dot"></span>
          <div><div class="fw-semi">Delivery</div><div class="text-sm text-muted">Expected Mar 12</div></div>
        </div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Icons + emoji ===================== -->
  <section class="container section stack-6" id="mobile">
    <div class="stack-2">
      <h2>Icons and emoji</h2>
      <p class="text-muted">Deck ships <?= (int) $site['icons'] ?> icons as a single SVG
        sprite of <?= (int) $site['symbols'] ?> symbols — each icon at two weights, plus two
        brand marks, <?= $e($site['sprite_br']) ?> Brotli — and they
        inherit color and font size, so they sit on the text baseline without nudging. The
        sprite is generated from the Material Symbols variable font, so any of its
        <?= $e($site['icons_avail']) ?> icons can be added by putting its name in
        <code>tools/icons/icons.txt</code> and running <code>npm run icons</code>; only
        the listed names are extracted, which is how a <?= $e($site['icons_avail']) ?> icon
        library ships as an <?= (int) $site['icons'] ?> icon file. Emoji get a pinned font
        stack so they render the same on Windows, iOS, and Android.</p>
    </div>

    <div class="scroller">
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#home"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#user"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#folder"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#calendar"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#mail"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#chart"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#shield"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#sparkle"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#map-pin"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#send"></use></svg></span>
    </div>

    <div class="cluster">
      <span class="emoji-tile">🚀</span>
      <span class="emoji-tile emoji-tile-round">🎨</span>
      <span class="emoji-tile">📦</span>
      <button class="reaction" aria-pressed="true"><span class="emoji">👍</span> 4</button>
      <button class="reaction"><span class="emoji">🎉</span> 2</button>
      <button class="reaction"><span class="emoji">🔥</span> 1</button>
      <span class="text-muted">Inline <span class="emoji">✅</span> emoji stay on the baseline.</span>
    </div>

    <div class="card"><div class="card-body">
      <span class="demo-label">Emoji picker</span>
      <div class="emoji-grid">
        <button>😀</button><button>😄</button><button>🙂</button><button>😉</button>
        <button>😊</button><button>🤝</button><button>👍</button><button>👏</button>
        <button>🙌</button><button>💪</button><button>🔥</button><button>✨</button>
        <button>🎉</button><button>✅</button><button>⚠️</button><button>🚀</button>
        <button>🐛</button><button>📦</button><button>📈</button><button>🎨</button>
      </div>
    </div></div>
  </section>

  <hr>

  <!-- ===================== Overlays ===================== -->
  <section class="container section stack-6">
    <div class="stack-2">
      <h2>Overlays</h2>
      <p class="text-muted">Built on native <code>&lt;dialog&gt;</code> and the popover
        attribute, so focus trapping, escape-to-close, and the top layer are handled by
        the browser. The sheet slides up from the bottom on a phone and centers on a
        desktop.</p>
    </div>

    <div class="cluster">
      <button class="btn btn-primary" onclick="document.getElementById('m1').showModal()">Open modal</button>
      <button class="btn" onclick="document.getElementById('s1').showModal()">Open bottom sheet</button>
      <button class="btn" popovertarget="menu1">Open menu</button>
      <button class="btn" onclick="toast('Project archived', 'good')">Fire a toast</button>
      <span class="btn btn-ghost tooltip" data-tip="Tooltips hide themselves on touch devices, where they never worked anyway.">
        <svg class="icon"><use href="assets/deck/deck-icons.svg#info"></use></svg>
        Hover me
      </span>
    </div>

    <div class="menu" id="menu1" popover>
      <div class="menu-label">Project actions</div>
      <button class="menu-item"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#edit-sm"></use></svg> Rename</button>
      <button class="menu-item"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#copy-sm"></use></svg> Duplicate <kbd class="push">⌘D</kbd></button>
      <button class="menu-item"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#download-sm"></use></svg> Export as JSON</button>
      <div class="menu-sep"></div>
      <button class="menu-item menu-item-danger"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#trash-sm"></use></svg> Delete project</button>
    </div>

    <dialog class="modal" id="m1">
      <div class="modal-header">
        <span class="icon-tile icon-tile-bad"><svg class="icon"><use href="assets/deck/deck-icons.svg#alert-triangle"></use></svg></span>
        <div class="grow">
          <h3 class="modal-title">Delete the api-gateway project?</h3>
          <p class="text-sm text-muted mt-1">This removes 340 issues and every deploy
            record. It cannot be undone.</p>
        </div>
      </div>
      <div class="modal-body">
        <div class="field">
          <label class="label" for="reason">Reason</label>
          <select class="select" id="reason">
            <option>Replaced by another project</option>
            <option>Created by mistake</option>
            <option>No longer maintained</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn" onclick="this.closest('dialog').close()">Keep it</button>
        <button class="btn btn-danger" onclick="this.closest('dialog').close(); toast('Project deleted','bad')">Delete project</button>
      </div>
    </dialog>

    <dialog class="sheet" id="s1">
      <div class="sheet-grip"></div>
      <div class="sheet-header">
        <h3 class="sheet-title grow">Filter issues</h3>
        <button class="btn btn-icon btn-ghost btn-sm" aria-label="Close" onclick="this.closest('dialog').close()">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#x"></use></svg>
        </button>
      </div>
      <div class="sheet-body">
        <div class="segmented">
          <button aria-selected="true">All</button>
          <button aria-selected="false">Mine</button>
          <button aria-selected="false">Starred</button>
        </div>
        <div class="field">
          <span class="label">Status</span>
          <label class="check"><input type="checkbox" checked><span class="check-text"><span>Open</span></span></label>
          <label class="check"><input type="checkbox" checked><span class="check-text"><span>In progress</span></span></label>
          <label class="check"><input type="checkbox"><span class="check-text"><span>Closed</span></span></label>
        </div>
        <div class="field">
          <label class="label" for="amt">Minimum story points</label>
          <input class="range" id="amt" type="range" min="0" max="2000" value="250">
        </div>
        <button class="btn btn-primary btn-block btn-lg" onclick="this.closest('dialog').close()">Show 42 issues</button>
      </div>
    </dialog>

    <div class="card">
      <div class="empty">
        <span class="empty-art"><span class="emoji">📭</span></span>
        <span class="empty-title">No issues match those filters</span>
        <p>Clear a filter or widen the date range to see more.</p>
        <button class="btn btn-soft">Clear filters</button>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Date picker ===================== -->
  <section class="container section stack-6" id="dates">
    <div class="stack-2">
      <h2>Date picker</h2>
      <p class="text-muted">Single date, range with two months, and presets. It's a
        popover on a desktop and slides up as a sheet under 480px.</p>
    </div>

    <div class="grid grid-tight">
      <div class="field">
        <label class="label" for="d1">Publish on</label>
        <div class="datefield" data-deck-datepicker data-format="mdy">
          <input class="input" id="d1" placeholder="Pick a date">
        </div>
      </div>

      <div class="field">
        <label class="label" for="d2">Report period</label>
        <div class="datefield" data-deck-datepicker data-mode="range" data-months="2" data-presets>
          <input class="input" id="d2" placeholder="Start – end">
        </div>
        <span class="help">Try "Last 30 days" in the rail.</span>
      </div>

      <div class="field">
        <label class="label" for="d3">Inside the trial window only</label>
        <div class="datefield" data-deck-datepicker data-min="2026-08-15" data-max="2026-10-15">
          <input class="input" id="d3" placeholder="Aug 15 – Oct 15">
        </div>
        <span class="help">Dates outside the range are struck through and unclickable.</span>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Combobox ===================== -->
  <section class="container section stack-6" id="combo">
    <div class="stack-2">
      <h2>Combobox</h2>
      <p class="text-muted">Type to filter, arrows to move, enter to pick. The real
        <code>&lt;select&gt;</code> stays in the DOM and stays in sync, so a plain PHP
        form post works with nothing extra on the server.</p>
    </div>

    <div class="grid grid-tight">
      <div class="field">
        <label class="label" for="c1">Repository</label>
        <div class="combo" data-deck-combo data-placeholder="Search repositories">
          <select id="c1" hidden>
            <option value="api-gateway" data-sub="Go · 41 open issues" data-group="Backend">api-gateway</option>
            <option value="billing-service" data-sub="Go · 12 open issues" data-group="Backend">billing-service</option>
            <option value="search-indexer" data-sub="Rust · 7 open issues" data-group="Backend">search-indexer</option>
            <option value="web-app" data-sub="TypeScript · 63 open issues" data-group="Frontend">web-app</option>
            <option value="design-system" data-sub="CSS · 9 open issues" data-group="Frontend">design-system</option>
            <option value="terraform-modules" data-sub="HCL · 3 open issues" data-group="Infrastructure">terraform-modules</option>
            <option value="runbooks" data-sub="Markdown · 1 open issue" data-group="Infrastructure">runbooks</option>
          </select>
        </div>
        <span class="help">Grouped, with a second line of detail per option.</span>
      </div>

      <div class="field">
        <label class="label" for="c2">Assign to</label>
        <div class="combo" data-deck-combo data-multi data-create data-placeholder="Add people">
          <select id="c2" multiple hidden>
            <option value="priya" selected>Priya Lakhani</option>
            <option value="ken">Ken Spence</option>
            <option value="dana">Dana Whitfield</option>
            <option value="marco">Marco Reyes</option>
            <option value="tina">Tina Okafor</option>
          </select>
        </div>
        <span class="help">Multi-select with tokens. Type a new name to add it.</span>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Data grid ===================== -->
  <section class="container section stack-4" id="grid">
    <div class="stack-2">
      <h2>Data grid</h2>
      <p class="text-muted">Frozen header, pinned first and last columns, sortable
        headers, resizable columns, and a totals row that sticks to the bottom. Scroll
        the grid sideways — the shadow only appears once you do.</p>
    </div>

    <div class="dg-toolbar">
      <div class="search">
        <svg class="icon"><use href="assets/deck/deck-icons.svg#search"></use></svg>
        <input class="input" type="search" placeholder="Filter orders">
      </div>
      <div class="segmented" style="inline-size:auto">
        <button aria-selected="true">Comfortable</button>
        <button aria-selected="false">Compact</button>
      </div>
      <button class="btn btn-sm">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#download-sm"></use></svg>
        Export
      </button>
    </div>

    <div class="dg-selection" data-dg-selection hidden>
      <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-circle-sm"></use></svg>
      <span><span data-dg-count>0</span> selected</span>
      <button class="btn btn-sm push">Mark as fulfilled</button>
      <button class="btn btn-sm btn-danger">Refund</button>
    </div>

    <div class="dg-wrap dg-cards-wrap" data-deck-grid style="--dg-height:360px">
      <table class="dg dg-zebra dg-cards">
        <thead>
          <tr>
            <th class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select all"></label></th>
            <th class="dg-pin-start-2" data-sort="text" data-resize>Order</th>
            <th data-sort="text" data-resize>Customer ID</th>
            <th data-sort="text">Customer</th>
            <th data-sort="text">Product</th>
            <th data-sort="text">Rep</th>
            <th data-sort="text">Status</th>
            <th class="dg-num" data-sort="num">Subtotal</th>
            <th class="dg-num" data-sort="num">Tax</th>
            <th class="dg-num" data-sort="num">Total</th>
            <th class="dg-num" data-sort="date">Placed</th>
            <th class="dg-actions dg-pin-end"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select order 1042"></label></td>
            <td class="dg-pin-start-2" data-label="Order"><a href="#grid">#1042</a></td>
            <td data-label="Customer ID" class="mono">cus_Q4nR8vTk2LpZ</td>
            <td data-label="Customer">Northwind Traders</td>
            <td data-label="Product">Standard plan, 24 seats</td>
            <td data-label="Rep">Marco Reyes</td>
            <td data-label="Status"><span class="badge badge-good">Paid</span></td>
            <td data-label="Subtotal" class="dg-num">$780.00</td>
            <td data-label="Tax" class="dg-num">$62.16</td>
            <td data-label="Total" class="dg-num">$842.16</td>
            <td data-label="Placed" class="dg-num">2026-09-03</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select order 1041"></label></td>
            <td class="dg-pin-start-2" data-label="Order"><a href="#grid">#1041</a></td>
            <td data-label="Customer ID" class="mono">cus_H7mE3bXw9Ktf</td>
            <td data-label="Customer">Globex Corp</td>
            <td data-label="Product">Team plan, 8 seats</td>
            <td data-label="Rep">Ken Spence</td>
            <td data-label="Status"><span class="badge badge-warn">Pending</span></td>
            <td data-label="Subtotal" class="dg-num">$295.00</td>
            <td data-label="Tax" class="dg-num">$23.00</td>
            <td data-label="Total" class="dg-num">$318.00</td>
            <td data-label="Placed" class="dg-num">2026-08-31</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select order 1039"></label></td>
            <td class="dg-pin-start-2" data-label="Order"><a href="#grid">#1039</a></td>
            <td data-label="Customer ID" class="mono">cus_K2pA9zLd4Rye</td>
            <td data-label="Customer">Initech</td>
            <td data-label="Product">Starter plan, 3 seats</td>
            <td data-label="Rep">Dana Whitfield</td>
            <td data-label="Status"><span class="badge badge-bad">Refunded</span></td>
            <td data-label="Subtotal" class="dg-num">$0.00</td>
            <td data-label="Tax" class="dg-num">$0.00</td>
            <td data-label="Total" class="dg-num">$0.00</td>
            <td data-label="Placed" class="dg-num">2026-08-27</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select order 1038"></label></td>
            <td class="dg-pin-start-2" data-label="Order"><a href="#grid">#1038</a></td>
            <td data-label="Customer ID" class="mono">cus_R5tY6wQn1Bge</td>
            <td data-label="Customer">Umbrella Ltd</td>
            <td data-label="Product">Enterprise add-on, SSO</td>
            <td data-label="Rep">Priya Lakhani</td>
            <td data-label="Status"><span class="badge badge-good">Paid</span></td>
            <td data-label="Subtotal" class="dg-num">$298.00</td>
            <td data-label="Tax" class="dg-num">$23.40</td>
            <td data-label="Total" class="dg-num">$321.40</td>
            <td data-label="Placed" class="dg-num">2026-09-05</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select order 1037"></label></td>
            <td class="dg-pin-start-2" data-label="Order"><a href="#grid">#1037</a></td>
            <td data-label="Customer ID" class="mono">cus_B8xC4uMs7Wdq</td>
            <td data-label="Customer">Vandelay Industries</td>
            <td data-label="Product">Team plan, 6 seats</td>
            <td data-label="Rep">Tina Okafor</td>
            <td data-label="Status"><span class="badge badge-warn">Pending</span></td>
            <td data-label="Subtotal" class="dg-num">$278.75</td>
            <td data-label="Tax" class="dg-num">$22.00</td>
            <td data-label="Total" class="dg-num">$300.75</td>
            <td data-label="Placed" class="dg-num">2026-09-06</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td class="dg-pin-start"></td>
            <td class="dg-pin-start-2">5 orders</td>
            <td colspan="5"></td>
            <td class="dg-num">$1,651.75</td>
            <td class="dg-num">$130.56</td>
            <td class="dg-num">$1,782.31</td>
            <td></td>
            <td class="dg-pin-end"></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="dg-statusbar">
      <span>Showing 5 of 412</span>
      <span class="push">Rows per page</span>
      <select class="select" style="inline-size:auto"><option>25</option><option>50</option><option>100</option></select>
    </div>
  </section>

  <hr>

  <!-- ===================== Toasts ===================== -->
  <section class="container section stack-6" id="toasts-demo">
    <div class="stack-2">
      <h2>Toast queue</h2>
      <p class="text-muted">Toasts stack instead of stringing down the screen. Hover the
        stack to fan it out and pause every timer. Swipe or drag one sideways to
        dismiss it. Fire several in a row to see the queue work.</p>
    </div>

    <div class="cluster">
      <button class="btn" onclick="Deck.toast({kind:'good', title:'Deploy succeeded', text:'Build 4471 is live on production.'})">Success</button>
      <button class="btn" onclick="Deck.toast({kind:'warn', title:'Storage is nearly full', text:'47 GB of your 50 GB plan is in use.'})">Warning</button>
      <button class="btn" onclick="Deck.toast({kind:'bad', title:'Payment failed', text:'The card ending 4242 was declined.'})">Error</button>
      <button class="btn" onclick="Deck.toast({kind:'info', title:'Scheduled maintenance', text:'The API is read only on Sunday, 02:00 UTC.'})">Info</button>
      <button class="btn btn-soft" onclick="Deck.toast({kind:'', title:'Project archived', text:'It is out of the sidebar but nothing was deleted.', duration:9000, actions:[{label:'Undo', onClick:()=>Deck.toast({kind:'good',title:'Restored'})}]})">With an action</button>
      <button class="btn btn-soft" onclick="demoProgress()">Loading, then done</button>
      <button class="btn btn-ghost" onclick="Deck.toasts.clear()">Clear all</button>
    </div>
  </section>

  <hr>

  <!-- ===================== Charts ===================== -->
  <section class="container section stack-6" id="charts">
    <div class="stack-2">
      <h2>Charts</h2>
      <p class="text-muted">No charting library. Bars and donuts are CSS driven by a
        <code>--value</code> property; lines are inline SVG you style with classes.
        Every series reads the brand palette, so the hue slider retunes these too.</p>
    </div>

    <div class="grid grid-wide">
      <div class="card"><div class="card-body">
        <div class="chart">
          <div class="chart-head">
            <span class="chart-title">New signups by month</span>
            <span class="chart-note push">2026</span>
          </div>
          <div class="chart-columns">
            <div class="chart-col s1" style="--value:42" data-label="Mar" data-value="84"></div>
            <div class="chart-col s1" style="--value:58" data-label="Apr" data-value="116"></div>
            <div class="chart-col s1" style="--value:51" data-label="May" data-value="102"></div>
            <div class="chart-col s1" style="--value:73" data-label="Jun" data-value="146"></div>
            <div class="chart-col s1" style="--value:66" data-label="Jul" data-value="132"></div>
            <div class="chart-col s1" style="--value:88" data-label="Aug" data-value="176"></div>
            <div class="chart-col s2" style="--value:100" data-label="Sep" data-value="200"></div>
          </div>
        </div>
      </div></div>

      <div class="card"><div class="card-body">
        <div class="chart">
          <div class="chart-head"><span class="chart-title">Weekly active users</span></div>
          <svg class="chart-svg" viewBox="0 0 300 120" preserveAspectRatio="none" role="img" aria-label="Weekly active users trending up">
            <line class="chart-gridline" x1="0" y1="30" x2="300" y2="30"/>
            <line class="chart-gridline" x1="0" y1="60" x2="300" y2="60"/>
            <line class="chart-gridline" x1="0" y1="90" x2="300" y2="90"/>
            <path class="chart-area s1" d="M0,86 L50,74 L100,80 L150,52 L200,58 L250,34 L300,26 L300,120 L0,120 Z"/>
            <path class="chart-line s1" d="M0,86 L50,74 L100,80 L150,52 L200,58 L250,34 L300,26"/>
            <path class="chart-line chart-line-dashed s-muted" d="M0,96 L50,92 L100,94 L150,84 L200,88 L250,78 L300,74"/>
            <line class="chart-baseline" x1="0" y1="120" x2="300" y2="120"/>
          </svg>
          <div class="chart-x"><span>Mar</span><span>May</span><span>Jul</span><span>Sep</span></div>
          <div class="chart-legend">
            <span class="s1">This workspace</span>
            <span class="s-muted">Account average</span>
          </div>
        </div>
      </div></div>
    </div>

    <div class="grid">
      <div class="card"><div class="card-body">
        <span class="chart-title">Sessions by device</span>
        <div class="cluster" style="justify-content:center;padding-block:var(--space-2)">
          <div class="donut-wrap">
            <div class="donut" style="--stops: var(--c1) 0 62%, var(--c3) 62% 84%, var(--c4) 84% 100%"></div>
            <div class="donut-center">
              <span class="donut-value">412</span>
              <span class="donut-label">total</span>
            </div>
          </div>
        </div>
        <div class="chart-legend" style="justify-content:center">
          <span class="s1">Desktop 62%</span>
          <span class="s3">Mobile 22%</span>
          <span class="s4">Tablet 16%</span>
        </div>
      </div></div>

      <div class="card"><div class="card-body">
        <span class="chart-title mb-3">Top traffic sources</span>
        <div class="chart chart-bars">
          <div class="chart-bar">
            <span class="chart-bar-label">Direct</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s1" style="--value:92"></span></span>
            <span class="chart-bar-value">92</span>
          </div>
          <div class="chart-bar">
            <span class="chart-bar-label">Search</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s2" style="--value:71"></span></span>
            <span class="chart-bar-value">71</span>
          </div>
          <div class="chart-bar">
            <span class="chart-bar-label">Referral</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s3" style="--value:54"></span></span>
            <span class="chart-bar-value">54</span>
          </div>
          <div class="chart-bar">
            <span class="chart-bar-label">Newsletter</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s4" style="--value:38"></span></span>
            <span class="chart-bar-value">38</span>
          </div>
          <div class="chart-bar">
            <span class="chart-bar-label">Social</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s5" style="--value:21"></span></span>
            <span class="chart-bar-value">21</span>
          </div>
        </div>
      </div></div>

      <div class="card"><div class="card-body stack-4">
        <div class="stat">
          <span class="stat-label">Revenue this week</span>
          <div class="cluster cluster-tight">
            <span class="stat-value">$18,402</span>
            <svg class="sparkline sparkline-good" viewBox="0 0 88 28" preserveAspectRatio="none">
              <path class="chart-line" d="M0,22 L11,19 L22,24 L33,14 L44,17 L55,9 L66,12 L77,5 L88,3"/>
            </svg>
          </div>
          <span class="stat-delta stat-delta-up">
            <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#trend-up-sm"></use></svg>
            8.1% vs last week
          </span>
        </div>
        <div class="stack-2">
          <span class="chart-note">Plan mix</span>
          <div class="chart-meter">
            <span class="s1" style="--value:62"></span>
            <span class="s3" style="--value:22"></span>
            <span class="s4" style="--value:16"></span>
          </div>
        </div>
        <div class="stack-2">
          <span class="chart-note">Commits, last five weeks</span>
          <div class="chart-heat" style="--cols:7">
            <div style="--value:10"></div><div style="--value:35"></div><div style="--value:80"></div>
            <div style="--value:55"></div><div style="--value:95"></div><div style="--value:20"></div>
            <div style="--value:5"></div><div style="--value:15"></div><div style="--value:60"></div>
            <div style="--value:100"></div><div style="--value:70"></div><div style="--value:45"></div>
            <div style="--value:25"></div><div style="--value:8"></div><div style="--value:30"></div>
            <div style="--value:85"></div><div style="--value:50"></div><div style="--value:90"></div>
            <div style="--value:40"></div><div style="--value:12"></div><div style="--value:3"></div>
          </div>
        </div>
      </div></div>
    </div>
  </section>

  <hr>

  <hr>

  <!-- ===================== Motion ===================== -->
  <section class="container section stack-6" id="motion">
    <div class="stack-2">
      <h2>Transitions and animations</h2>
      <p class="text-muted">Motion answers an action or shows what changed. Nothing here
        runs unless you ask for it by class, and everything is off for anyone whose OS
        asks for reduced motion — except loading indicators, which keep turning slowly,
        because a frozen spinner reads as broken.</p>
    </div>

    <div class="stack-3">
      <span class="demo-label">Entrances, staggered</span>
      <div class="grid grid-tight stagger" id="entranceDemo">
        <div class="card enter-rise"><div class="card-body"><span class="fw-semi">Backlog</span><span class="text-sm text-muted">42 issues</span></div></div>
        <div class="card enter-rise"><div class="card-body"><span class="fw-semi">In progress</span><span class="text-sm text-muted">18 issues</span></div></div>
        <div class="card enter-rise"><div class="card-body"><span class="fw-semi">Shipped</span><span class="text-sm text-muted">311 issues</span></div></div>
        <div class="card enter-rise"><div class="card-body"><span class="fw-semi">Closed</span><span class="text-sm text-muted">41 issues</span></div></div>
      </div>
      <button class="btn btn-sm" onclick="replayEntrances()">Replay</button>
    </div>

    <div class="stack-3">
      <span class="demo-label">Scroll reveal</span>
      <p class="text-muted text-sm">These use <code>animation-timeline: view()</code> —
        tied to scroll position with no IntersectionObserver. deck.js falls back to an
        observer where the browser doesn't support it.</p>
      <div class="grid grid-tight">
        <div class="card reveal"><div class="card-body"><span class="fw-semi">Reveal</span></div></div>
        <div class="card reveal-fade"><div class="card-body"><span class="fw-semi">Fade</span></div></div>
        <div class="card reveal-pop"><div class="card-body"><span class="fw-semi">Pop</span></div></div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Micro-interactions</span>
      <div class="cluster">
        <button class="btn lift">Lift on hover</button>
        <button class="btn press">Press</button>
        <button class="btn btn-primary ripple">Ripple</button>
        <button class="btn btn-soft">
          Continue
          <svg class="icon icon-sm icon-follow"><use href="assets/deck/deck-icons.svg#arrow-right-sm"></use></svg>
        </button>
        <a class="sweep" href="#motion">Underline sweep</a>
        <span class="badge badge-good"><span class="ping" style="display:inline-block;inline-size:6px;block-size:6px;border-radius:99px;background:currentColor"></span> Live</span>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Attention and feedback</span>
      <div class="cluster">
        <button class="btn" onclick="Deck.play(this, 'shake')">Shake</button>
        <button class="btn" onclick="Deck.play(this.closest('.cluster'), 'flash')">Flash</button>
        <button class="btn" onclick="Deck.play(document.getElementById('flashRow'), 'flash-good')">Flash a row</button>
        <button class="btn" onclick="bumpTotal()">Change a number</button>
        <span class="badge badge-warn pulse">Pulsing</span>
      </div>
      <div class="list">
        <div class="list-row" id="flashRow">
          <span class="icon-tile icon-tile-good"><svg class="icon"><use href="assets/deck/deck-icons.svg#check"></use></svg></span>
          <span class="list-main">
            <span class="list-title">Order #1042</span>
            <span class="list-sub">Order total</span>
          </span>
          <span class="list-trail nums fw-semi" data-deck-tick id="total">$842.16</span>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Expand and collapse</span>
      <p class="text-muted text-sm">A real <code>height: auto</code> transition using
        <code>interpolate-size</code>. No measuring in JavaScript, no max-height guess.</p>
      <button class="btn btn-sm" onclick="Deck.toggle(document.getElementById('exp'))">Toggle details</button>
      <div class="expand" id="exp">
        <div class="panel">
          <p class="text-muted">The connection pool was exhausted because a background
            job opened a transaction per row instead of per batch. The fix batches at a
            thousand rows and returns the connection in a <code>finally</code> block.</p>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Ticker</span>
      <div class="marquee card" style="padding-block:var(--space-3)">
        <div class="marquee-track text-sm text-muted">
          <span>v2.4.0 is rolling out to 12% of traffic</span>
          <span>Postgres upgrade to 17 scheduled for Sep 19</span>
          <span>The legacy /v1 API retires at the end of the quarter</span>
          <span>Invoices post on Friday</span>
        </div>
        <div class="marquee-track text-sm text-muted" aria-hidden="true">
          <span>v2.4.0 is rolling out to 12% of traffic</span>
          <span>Postgres upgrade to 17 scheduled for Sep 19</span>
          <span>The legacy /v1 API retires at the end of the quarter</span>
          <span>Invoices post on Friday</span>
        </div>
      </div>
    </div>

    <div class="alert alert-info">
      <svg class="icon"><use href="assets/deck/deck-icons.svg#sparkle"></use></svg>
      <div>
        <div class="alert-title">Page transitions without a router</div>
        <p class="alert-body">Add <code>@view-transition { navigation: auto; }</code> to
          your app CSS and full page loads cross-fade like a single page app — no
          router, no JavaScript, no framework. Deck styles what the browser generates,
          including holding the header and tab bar still while the content changes.</p>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Cascade layers ===================== -->
  <section class="container section stack-6" id="layers">
    <div class="stack-2">
      <h2>Cascade layers</h2>
      <p class="text-muted">Deck declares the whole cascade contract in
        <code>00-layers.css</code> before a single rule exists. Order is decided there —
        not by file order, not by specificity, and never by <code>!important</code>.
        Four empty <code>app.*</code> layers are reserved for you.</p>
    </div>

    <pre><code>@layer
  deck.reset, deck.tokens, deck.type, deck.layout,
  deck.components, deck.mobile, deck.motion, deck.effects,
  deck.utilities, deck.rtl, deck.print,

  app.base, app.components, app.pages, app.overrides;</code></pre>

    <div class="grid grid-tight">
      <div class="card"><div class="card-body">
        <span class="card-title">Write in a slot</span>
        <p class="text-sm text-muted">A rule in <code>app.pages</code> beats every Deck
          rule with a single class selector. No specificity war, no stacked
          <code>.page .card .btn</code> chains.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <span class="card-title">Unlayered always wins</span>
        <p class="text-sm text-muted">Anything outside a layer beats all layers. A one-off
          rule in a page template overrides Deck with nothing special.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <span class="card-title">Third-party CSS</span>
        <p class="text-sm text-muted">Wrap a vendor stylesheet in its own layer with
          <code>@import url(x.css) layer(vendor)</code> and it stops fighting you.</p>
      </div></div>
    </div>
  </section>

  <hr>

  <!-- ===================== Container queries ===================== -->
  <section class="container section stack-6" id="containers">
    <div class="stack-2">
      <h2>Container queries</h2>
      <p class="text-muted">The same card markup, twice. The one in the wide column goes
        horizontal; the one in the rail stays stacked. Neither knows where it was
        placed — they respond to the space they were given, not the window.</p>
    </div>

    <div class="split">
      <div class="cq">
        <article class="card card-flex">
          <figure class="card-media"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt=""></figure>
          <div class="card-body">
            <h3 class="card-title">Designing with container queries</h3>
            <p class="text-sm text-muted">Guides · 8 min read · updated Mar 3</p>
            <div class="actions-cq">
              <button class="btn btn-sm btn-primary">Read it</button>
              <button class="btn btn-sm">Share</button>
            </div>
          </div>
        </article>
      </div>

      <aside class="cq">
        <article class="card card-flex">
          <figure class="card-media"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt=""></figure>
          <div class="card-body">
            <h3 class="card-title">Designing with container queries</h3>
            <p class="text-sm text-muted">Guides · 8 min read · updated Mar 3</p>
            <div class="actions-cq">
              <button class="btn btn-sm btn-primary">Read it</button>
              <button class="btn btn-sm">Share</button>
            </div>
          </div>
        </article>
      </aside>
    </div>

    <div class="stack-3">
      <span class="demo-label">Style queries — set <code>--tone</code> on the container, children follow</span>
      <div class="grid grid-tight">
        <div class="cq-tone" style="--tone: clear">
          <div class="card tone-surface"><div class="card-body cluster cluster-tight">
            <svg class="icon icon-lg tone-icon"><use href="assets/deck/deck-icons.svg#check-circle"></use></svg>
            <span class="tone-text fw-semi">All checks passed</span>
          </div></div>
        </div>
        <div class="cq-tone" style="--tone: caution">
          <div class="card tone-surface"><div class="card-body cluster cluster-tight">
            <svg class="icon icon-lg tone-icon"><use href="assets/deck/deck-icons.svg#alert-triangle"></use></svg>
            <span class="tone-text fw-semi">Review requested</span>
          </div></div>
        </div>
        <div class="cq-tone" style="--tone: critical">
          <div class="card tone-surface"><div class="card-body cluster cluster-tight">
            <svg class="icon icon-lg tone-icon"><use href="assets/deck/deck-icons.svg#x-circle"></use></svg>
            <span class="tone-text fw-semi">Build failed</span>
          </div></div>
        </div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Logical properties ===================== -->
  <section class="container section stack-6" id="logical">
    <div class="stack-2">
      <h2>Logical properties</h2>
      <p class="text-muted">Deck is written in logical properties end to end, so a full
        right-to-left flip needs nothing but <code>dir="rtl"</code>. Try the switch — the
        layout, the switch knob, the chart fills, the sidebar, and the pointing icons all
        mirror. The commit hash does not, because an identifier reads left to right in
        every language.</p>
    </div>

    <div class="cluster">
      <button class="btn" onclick="Deck.dir(Deck.dir() === 'rtl' ? 'ltr' : 'rtl')">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#refresh-sm"></use></svg>
        Flip the whole page
      </button>
      <span class="badge">current: <span id="dirLabel">ltr</span></span>
    </div>

    <div class="grid grid-tight">
      <div class="card"><div class="card-body stack-3">
        <span class="card-title">Mirrors</span>
        <div class="cluster cluster-tight">
          <button class="btn btn-sm">Next <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#arrow-right-sm"></use></svg></button>
          <label class="switch"><input type="checkbox" checked><span class="text-sm">Auto-merge</span></label>
        </div>
        <div class="chart-bar">
          <span class="chart-bar-label">Coverage</span>
          <span class="chart-bar-track"><span class="chart-bar-fill s1" style="--value:74"></span></span>
          <span class="chart-bar-value">74</span>
        </div>
      </div></div>

      <div class="card"><div class="card-body stack-3">
        <span class="card-title">Does not mirror</span>
        <p class="text-sm text-muted">Identifiers stay in their own direction and stay
          isolated from the text around them.</p>
        <div class="stack-2">
          <span class="mono code-ltr">a81ac26bc87ec010f4d9</span>
          <span class="cluster cluster-tight">
            <svg class="icon no-flip"><use href="assets/deck/deck-icons.svg#settings"></use></svg>
            <svg class="icon no-flip"><use href="assets/deck/deck-icons.svg#clock"></use></svg>
            <svg class="icon no-flip"><use href="assets/deck/deck-icons.svg#check"></use></svg>
            <span class="text-sm text-muted">glyphs with no direction</span>
          </span>
        </div>
      </div></div>

      <div class="card"><div class="card-body">
        <span class="card-title mbe-2">Writing modes</span>
        <div class="cluster">
          <span class="writing-vertical text-sm text-muted" style="block-size:6rem">Vertical column header</span>
          <span class="writing-vertical writing-upright text-sm text-muted" style="block-size:6rem">UPRIGHT</span>
        </div>
      </div></div>
    </div>
  </section>

  <hr>

  <!-- ===================== Gradients ===================== -->
  <section class="container section stack-6" id="gradients">
    <div class="stack-2">
      <h2>Gradients</h2>
      <p class="text-muted">All built from the brand hue and interpolated in oklab, which
        avoids the grey dead zone you get blending two saturated colors in sRGB. Drag the
        hue slider and every one of these retunes.</p>
    </div>

    <div class="card g-mesh g-mesh-drift" style="min-block-size:200px">
      <div class="card-body center" style="justify-content:center;block-size:100%">
        <h3 class="display-cq g-text" style="font-size:var(--text-2xl)">Ship it on Friday</h3>
        <p class="text-muted">Mesh background, gradient text, no images</p>
      </div>
    </div>

    <div class="grid grid-tight">
      <div class="card g-border"><div class="card-body">
        <span class="fw-semi">Gradient border</span>
        <p class="text-sm text-muted">Two clip boxes, works with any radius.</p>
      </div></div>
      <div class="card g-border g-border-spin"><div class="card-body">
        <span class="fw-semi">Animated border</span>
        <p class="text-sm text-muted">The angle is a registered <code>@property</code>, so it interpolates.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <span class="fw-semi g-text-shine">Shine sweep on text</span>
        <p class="text-sm text-muted">Clipped to the glyphs, with a real color underneath.</p>
      </div></div>
    </div>

    <div class="cluster">
      <button class="btn btn-primary g-sheen">Hover for sheen</button>
      <span class="badge g-good">Passing</span>
      <span class="badge g-warn">Queued</span>
      <span class="badge g-bad">Failing</span>
      <span class="g-ring g-ring-spin" style="inline-size:28px;block-size:28px;display:inline-block"></span>
    </div>

    <div class="grid grid-tight">
      <div class="card g-grid-lines" style="min-block-size:110px"><div class="card-body"><span class="text-sm text-muted">Grid lines</span></div></div>
      <div class="card g-dots" style="min-block-size:110px"><div class="card-body"><span class="text-sm text-muted">Dots</span></div></div>
      <div class="card g-hatch" style="min-block-size:110px"><div class="card-body"><span class="text-sm text-muted">Hatch — unavailable slot</span></div></div>
      <div class="card g-scrim" style="min-block-size:110px;background:var(--ink-400)">
        <div class="card-body" style="justify-content:flex-end;block-size:100%">
          <span class="fw-semi" style="color:#fff">Scrim over media</span>
        </div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== 3D ===================== -->
  <section class="container section stack-6" id="space">
    <div class="stack-2">
      <h2>3D transforms</h2>
      <p class="text-muted">Depth earns its place when it carries meaning: a card with two
        sides, a pile you are working down through, a control that physically depresses.
        Everything uses <code>rotate</code> and <code>translate</code> as individual
        properties, so two effects compose instead of overwriting each other.</p>
    </div>

    <div class="grid grid-tight">
      <div class="scene">
        <div class="flip card" id="flipCard" style="min-block-size:170px">
          <div class="flip-front card-body stack-2">
            <span class="card-title">Order #1042</span>
            <p class="text-sm text-muted">Northwind Traders · 24 seats</p>
            <button class="btn btn-sm push" data-deck-flip>See the breakdown</button>
          </div>
          <div class="flip-back card-body stack-2 g-brand-soft">
            <span class="card-title">Breakdown</span>
            <p class="text-sm">Subtotal $780.00 · Tax $62.16</p>
            <button class="btn btn-sm" data-deck-flip>Back</button>
          </div>
        </div>
      </div>

      <div class="scene scene-near">
        <div class="card tilt" data-tilt="10" style="min-block-size:170px">
          <div class="card-body space">
            <span class="card-title tilt-lift">Tilt</span>
            <p class="text-sm text-muted tilt-lift-sm">Move the pointer across this card.
              The title floats above the face on the Z axis.</p>
          </div>
        </div>
      </div>

      <div class="stack-3">
        <div class="stack-depth" id="pile" style="min-block-size:150px">
          <div class="card"><div class="card-body"><span class="fw-semi">PR #418</span><span class="text-sm text-muted">Awaiting review</span></div></div>
          <div class="card"><div class="card-body"><span class="fw-semi">PR #421</span><span class="text-sm text-muted">Awaiting review</span></div></div>
          <div class="card"><div class="card-body"><span class="fw-semi">PR #423</span><span class="text-sm text-muted">Awaiting review</span></div></div>
          <div class="card"><div class="card-body"><span class="fw-semi">PR #427</span><span class="text-sm text-muted">Awaiting review</span></div></div>
        </div>
        <div class="cluster cluster-tight">
          <button class="btn btn-sm" onclick="document.getElementById('pile').classList.toggle('is-fanned')">Fan out</button>
          <button class="btn btn-sm" onclick="Deck.advance(document.getElementById('pile'))">Next card</button>
        </div>
      </div>
    </div>

    <div class="cluster">
      <button class="btn btn-primary btn-3d">Press me</button>
      <div class="scene">
        <div class="cube cube-spin" style="--size:88px">
          <div class="face-front"><span class="emoji">🚀</span></div>
          <div class="face-back"><span class="emoji">📦</span></div>
          <div class="face-end"><span class="emoji">🎨</span></div>
          <div class="face-start"><span class="emoji">📈</span></div>
          <div class="face-top"><span class="emoji">✅</span></div>
          <div class="face-bottom"><span class="emoji">🐛</span></div>
        </div>
      </div>
    </div>

    <div class="stack-2">
      <span class="demo-label">Coverflow — scroll it sideways</span>
      <div class="coverflow">
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">March</span><span class="text-sm text-muted">84 releases</span></div></div>
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">April</span><span class="text-sm text-muted">116 releases</span></div></div>
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">May</span><span class="text-sm text-muted">102 releases</span></div></div>
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">June</span><span class="text-sm text-muted">146 releases</span></div></div>
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">July</span><span class="text-sm text-muted">132 releases</span></div></div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Added components ===================== -->
  <section class="container section stack-8" id="more">
    <div class="stack-2">
      <h2>Carousel, drawer, mega menu, speed dial</h2>
      <p class="text-muted">The carousel is scroll snap underneath, so it swipes correctly
        with JavaScript off. Arrows and dots are enhancement.</p>
    </div>

    <div class="carousel carousel-peek" data-deck-carousel tabindex="0">
      <button class="carousel-arrow carousel-prev" aria-label="Previous"><svg class="icon"><use href="assets/deck/deck-icons.svg#chevron-left"></use></svg></button>
      <div class="carousel-track">
        <div class="carousel-slide card"><div class="card-body"><span class="fw-semi">Open issues</span><span class="stat-value">42</span></div></div>
        <div class="carousel-slide card"><div class="card-body"><span class="fw-semi">Merged this week</span><span class="stat-value">311</span></div></div>
        <div class="carousel-slide card"><div class="card-body"><span class="fw-semi">Average order</span><span class="stat-value">$614</span></div></div>
        <div class="carousel-slide card"><div class="card-body"><span class="fw-semi">Awaiting review</span><span class="stat-value">17</span></div></div>
      </div>
      <button class="carousel-arrow carousel-next" aria-label="Next"><svg class="icon"><use href="assets/deck/deck-icons.svg#chevron-right"></use></svg></button>
      <div class="carousel-dots"></div>
    </div>

    <div class="cluster">
      <button class="btn" data-deck-drawer="#drawer1">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#filter-sm"></use></svg> Open drawer
      </button>
      <button class="btn" popovertarget="mega1" data-deck-mega="#mega1">Products
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#chevron-down-sm"></use></svg>
      </button>
      <span class="with-indicator">
        <button class="btn btn-icon" aria-label="Notifications"><svg class="icon"><use href="assets/deck/deck-icons.svg#bell"></use></svg></button>
        <span class="indicator-badge">7</span>
      </span>
      <span class="status-line"><span class="indicator indicator-good ping"></span> All systems normal</span>
      <span class="status-line"><span class="indicator indicator-warn"></span> Degraded</span>
    </div>

    <dialog class="drawer" id="drawer1">
      <div class="drawer-header">
        <h3 class="drawer-title grow">Filter orders</h3>
        <button class="btn btn-icon btn-ghost btn-sm" data-drawer-close aria-label="Close"><svg class="icon"><use href="assets/deck/deck-icons.svg#x"></use></svg></button>
      </div>
      <div class="drawer-body">
        <div class="field">
          <span class="label">Status</span>
          <label class="check"><input type="checkbox" checked><span class="check-text"><span>Paid</span></span></label>
          <label class="check"><input type="checkbox" checked><span class="check-text"><span>Pending</span></span></label>
          <label class="check"><input type="checkbox"><span class="check-text"><span>Refunded</span></span></label>
        </div>
        <div class="field">
          <span class="label">Order total</span>
          <div class="range-pair" data-gap="5">
            <input type="range" min="0" max="2000" value="250" data-prefix="$" aria-label="Minimum">
            <input type="range" min="0" max="2000" value="1400" data-prefix="$" aria-label="Maximum">
          </div>
          <div class="range-readout"><span>$250</span><span>$1400</span></div>
        </div>
      </div>
      <div class="drawer-footer">
        <button class="btn" data-drawer-close>Reset</button>
        <button class="btn btn-primary" data-drawer-close>Apply</button>
      </div>
    </dialog>

    <div class="mega" id="mega1" popover>
      <div class="mega-grid">
        <div class="mega-col">
          <span class="mega-heading">Build</span>
          <a class="mega-item" href="#more"><span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#clipboard"></use></svg></span><span><span class="mega-item-title">Issue tracker</span><span class="mega-item-note">Boards, sprints, and labels</span></span></a>
          <a class="mega-item" href="#more"><span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#search"></use></svg></span><span><span class="mega-item-title">Code search</span><span class="mega-item-note">Across every repository</span></span></a>
        </div>
        <div class="mega-col">
          <span class="mega-heading">Measure</span>
          <a class="mega-item" href="#more"><span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#chart"></use></svg></span><span><span class="mega-item-title">Analytics</span><span class="mega-item-note">Funnels, retention, cohorts</span></span></a>
          <a class="mega-item" href="#more"><span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#credit-card"></use></svg></span><span><span class="mega-item-title">Billing</span><span class="mega-item-note">Invoices and usage</span></span></a>
        </div>
        <div class="mega-feature">
          <span class="fw-semi">What's new in v2.4</span>
          <p class="text-sm mt-1">Saved views, a public API for boards, and faster search.</p>
        </div>
      </div>
      <div class="mega-footer"><a href="#more">Documentation</a><a href="#more">Release notes</a><a href="#more">Contact support</a></div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Stepper</span>
      <div class="stepper stepper-auto">
        <div class="step is-done"><div class="step-marker"></div><span class="step-label">Account</span><span class="step-note">Email confirmed</span></div>
        <div class="step is-done"><div class="step-marker"></div><span class="step-label">Workspace</span><span class="step-note">Named and branded</span></div>
        <div class="step is-current"><div class="step-marker"></div><span class="step-label">Invite the team</span><span class="step-note">Three seats left</span></div>
        <div class="step"><div class="step-marker"></div><span class="step-label">Billing</span><span class="step-note">After the trial</span></div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Inputs</span>
      <div class="grid grid-tight">
        <div class="float">
          <input class="input" id="f1" placeholder=" ">
          <label for="f1">Project name</label>
        </div>
        <div class="float float-outline">
          <input class="input" id="f2" placeholder=" ">
          <label for="f2">Customer name</label>
        </div>
        <div class="field">
          <label class="label" for="n1">Seats</label>
          <div class="number">
            <button type="button" data-step="-1" aria-label="Decrease"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#minus-sm"></use></svg></button>
            <input id="n1" type="number" value="12" step="1" min="0" max="500">
            <button type="button" data-step="1" aria-label="Increase"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#plus-sm"></use></svg></button>
            <span class="number-unit">seats</span>
          </div>
        </div>
        <div class="field">
          <label class="label" for="p1">Phone</label>
          <div class="phone">
            <span class="phone-country">
              <span class="phone-flag">🇺🇸</span>
              <span class="phone-code">+1</span>
              <select aria-label="Country">
                <option data-code="+1" data-flag="🇺🇸" data-mask="(###) ###-####" selected>US</option>
                <option data-code="+1" data-flag="🇨🇦" data-mask="(###) ###-####">CA</option>
                <option data-code="+52" data-flag="🇲🇽" data-mask="## #### ####">MX</option>
              </select>
            </span>
            <input id="p1" type="tel" placeholder="(000) 000-0000">
            <span class="phone-status"><svg class="icon icon-sm text-good"><use href="assets/deck/deck-icons.svg#check-sm"></use></svg></span>
          </div>
        </div>
        <div class="field">
          <span class="label">Rate this release</span>
          <div class="rating">
            <input type="radio" name="rate" id="r5" value="5"><label for="r5"><svg class="icon"><use href="assets/deck/deck-icons.svg#star-fill"></use></svg></label>
            <input type="radio" name="rate" id="r4" value="4"><label for="r4"><svg class="icon"><use href="assets/deck/deck-icons.svg#star-fill"></use></svg></label>
            <input type="radio" name="rate" id="r3" value="3" checked><label for="r3"><svg class="icon"><use href="assets/deck/deck-icons.svg#star-fill"></use></svg></label>
            <input type="radio" name="rate" id="r2" value="2"><label for="r2"><svg class="icon"><use href="assets/deck/deck-icons.svg#star-fill"></use></svg></label>
            <input type="radio" name="rate" id="r1" value="1"><label for="r1"><svg class="icon"><use href="assets/deck/deck-icons.svg#star-fill"></use></svg></label>
          </div>
        </div>
        <div class="field">
          <span class="label">API key</span>
          <div class="copy">
            <code class="copy-value">sk_live_7Kd2Rq9XmB4tPw</code>
            <button class="copy-btn" data-deck-copy>
              <span class="copy-idle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#copy-sm"></use></svg> Copy</span>
              <span class="copy-done"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-sm"></use></svg> Copied</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Editor</span>
      <div class="editor" data-limit="600">
        <div class="editor-toolbar">
          <select class="editor-select" aria-label="Block format">
            <option value="p">Paragraph</option>
            <option value="h2">Heading</option>
            <option value="h3">Subheading</option>
          </select>
          <span class="editor-sep"></span>
          <button class="editor-tool" data-cmd="bold" aria-label="Bold">B</button>
          <button class="editor-tool" data-cmd="italic" aria-label="Italic" style="font-style:italic">I</button>
          <button class="editor-tool" data-cmd="underline" aria-label="Underline" style="text-decoration:underline">U</button>
          <span class="editor-sep"></span>
          <button class="editor-tool" data-cmd="insertUnorderedList" aria-label="Bullet list"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#list-sm"></use></svg></button>
          <button class="editor-tool" data-cmd="createLink" aria-label="Link"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#link-sm"></use></svg></button>
          <span class="editor-sep"></span>
          <button class="editor-tool" data-cmd="undo" aria-label="Undo"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#refresh-sm"></use></svg></button>
        </div>
        <div class="editor-content" data-placeholder="Describe the bug: what you expected, and what happened instead."></div>
        <div class="editor-footer"><span>Pastes arrive as plain text</span><span class="editor-count">0 / 600</span></div>
      </div>
    </div>

    <div class="grid grid-wide">
      <div class="stack-3">
        <span class="demo-label">Chat</span>
        <div class="card">
          <div class="chat" style="max-block-size:280px">
            <span class="chat-day">Today</span>
            <div class="msg">
              <span class="avatar avatar-xs">PL</span>
              <div><div class="bubble"><span class="bubble-name">Priya</span>The CSV export drops the last row on files over 10 MB.</div></div>
            </div>
            <div class="msg">
              <span class="avatar avatar-xs">PL</span>
              <div><div class="bubble">Can you check whether the stream is flushed before the response closes?<div class="bubble-meta">8:14 AM</div></div></div>
            </div>
            <div class="msg msg-out">
              <div><div class="bubble">Found it — the writer is buffered and never flushed on the last chunk. Patch is up.<div class="bubble-meta">8:16 AM <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-double-sm"></use></svg></div></div></div>
            </div>
            <div class="msg">
              <span class="avatar avatar-xs">PL</span>
              <div><div class="bubble bubble-typing"><span></span><span></span><span></span></div></div>
            </div>
          </div>
          <div class="chat-composer">
            <textarea class="textarea" placeholder="Write a message" rows="1"></textarea>
            <button class="btn btn-icon btn-primary btn-round" aria-label="Send"><svg class="icon"><use href="assets/deck/deck-icons.svg#send"></use></svg></button>
          </div>
        </div>
      </div>

      <div class="stack-3">
        <span class="demo-label">QR code — encoded in the browser, no library and no network call</span>
        <div class="cluster">
          <div class="qr" data-deck-qr="https://example.com/orders/1042" data-ecl="M"></div>
          <div class="qr qr-sm" data-deck-qr="sk_live_7Kd2Rq9XmB4tPw" data-ecl="H"></div>
        </div>
        <span class="demo-label">Video</span>
        <div class="video">
          <button class="video-poster">
            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 360'%3E%3Crect width='640' height='360' fill='%2378909c'/%3E%3C/svg%3E" alt="">
            <span class="video-play"><svg class="icon icon-xl"><use href="assets/deck/deck-icons.svg#chevron-right"></use></svg></span>
          </button>
          <span class="video-meta"><span class="video-duration">4:12</span></span>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Masonry gallery with lazy loading</span>
      <div class="masonry masonry-3">
        <div class="lazy" style="--ratio:3/4"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 400'%3E%3Crect width='300' height='400' fill='%2390a4ae'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:4/3"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23b0bec5'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:1"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:3/5"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 500'%3E%3Crect width='300' height='500' fill='%2378909c'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:16/9"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 360'%3E%3Crect width='640' height='360' fill='%23b0bec5'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:4/5"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 500'%3E%3Crect width='400' height='500' fill='%2390a4ae'/%3E%3C/svg%3E" alt=""></div>
      </div>
    </div>

    <div class="jumbotron jumbotron-center g-mesh-subtle">
      <span class="badge badge-brand">Jumbotron</span>
      <!-- h3, not h2: this promo band sits inside the section above, and a
           marketing line should not read as a top-level topic in the outline.
           .h2 keeps the visual size. -->
      <h3 class="h2">Everything you need on the first page load</h3>
      <p class="lede">One stylesheet, one link tag, and no dependencies. Every component
        on this page came out of the box.</p>
      <div class="jumbotron-actions">
        <button class="btn btn-primary btn-lg">Read the docs</button>
        <button class="btn btn-lg">View on GitHub</button>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Gallery ===================== -->
  <section class="container section stack-6" id="gallery">
    <div class="stack-2">
      <h2>Gallery</h2>
      <p class="text-muted">Equal tiles for a media library — screenshots, exports, brand
        assets. <code>.gallery</code> is a grid of square cells that reflows on its own,
        so someone uploading nine files and someone uploading three both get a tidy
        block. <code>.span-2</code> promotes a tile to a 2&times;2 feature and
        <code>.span-wide</code> makes it a 2&times;1 banner, which is how you lead with
        the one image that should be seen first.</p>
    </div>

    <div class="stack-3">
      <span class="demo-label">Assets &middot; Acme Design &middot; 8 files, 24 MB</span>
      <div class="gallery">
        <a class="span-2" href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect width='400' height='400' fill='%2378909c'/%3E%3C/svg%3E" alt="Dashboard screenshot, dark theme"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%2390a4ae'/%3E%3C/svg%3E" alt="Logo on a light background"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23b0bec5'/%3E%3C/svg%3E" alt="Color palette sheet"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt="Icon set contact sheet"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%2390a4ae'/%3E%3C/svg%3E" alt="Mobile layout, three breakpoints"></a>
        <a class="span-wide" href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 300'%3E%3Crect width='600' height='300' fill='%2378909c'/%3E%3C/svg%3E" alt="Social card, 1200 by 630"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23b0bec5'/%3E%3C/svg%3E" alt="Empty state illustration"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt="Typography specimen"></a>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Sidebar ===================== -->
  <section class="container section stack-6" id="sidebar">
    <div class="stack-2">
      <h2>Sidebar</h2>
      <p class="text-muted"><code>.sidebar</code> is a bare nav list — <code>.sidebar-group</code>
        for a heading, <code>.sidebar-link</code> for a row, <code>.push</code> to shove a
        count to the far end. It brings no width and no chrome of its own, so it goes in
        whatever rail your shell already has. Give that rail <code>.cq-shell</code> and the
        links collapse to icons below 15rem — a container query, not a media query, so it
        keys off the rail and not the window. <strong>Drag the rail by its bottom
        corner</strong> and watch the labels drop out while the page width never
        changes.</p>
    </div>

    <div class="split" style="--rail: 17rem">
      <div class="stack-4">
        <div class="card"><div class="card-body stack-2">
          <h3 class="card-title">Open issues</h3>
          <p class="text-sm text-muted">Forty-two issues are waiting on something. Thirty-one
            of them are waiting on a reviewer, not on the build.</p>
          <div class="cluster cluster-tight">
            <span class="badge badge-brand">42 open</span>
            <span class="badge">17 in review</span>
            <span class="badge">6 need triage</span>
          </div>
        </div></div>
        <p class="text-sm text-muted">The rail beside this column is the same markup at every
          width. Nothing inside the sidebar knows how wide it is — the container does.</p>
      </div>

      <aside class="cq-shell" style="resize: horizontal; overflow: auto; min-inline-size: 7rem; max-inline-size: 22rem">
        <nav class="panel sidebar" aria-label="Workspace">
          <span class="sidebar-group">Issues</span>
          <a class="sidebar-link" aria-current="page" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#clipboard"></use></svg>
            <span>Open</span><span class="badge push">42</span>
          </a>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#check-circle"></use></svg>
            <span>Closed</span><span class="badge push">311</span>
          </a>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#alert-triangle"></use></svg>
            <span>Needs triage</span><span class="badge push">6</span>
          </a>
          <span class="sidebar-group">Code</span>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#search"></use></svg>
            <span>Search</span>
          </a>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#folder"></use></svg>
            <span>Repositories</span>
          </a>
          <span class="sidebar-group">Workspace</span>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#users"></use></svg>
            <span>Members</span>
          </a>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#credit-card"></use></svg>
            <span>Billing</span>
          </a>
        </nav>
      </aside>
    </div>
  </section>

  <hr>

  <!-- ===================== Tooltips ===================== -->
  <section class="container section stack-6" id="tooltips">
    <div class="stack-2">
      <h2>Tooltips</h2>
      <p class="text-muted">There are two of them and the difference matters.
        <code>.tooltip</code> is a <code>::after</code> on the trigger with the text in
        <code>data-tip</code> — no extra markup, no JavaScript, nothing to keep in sync.
        But it is pinned above the trigger and <em>cannot flip</em>, so near the top of a
        scrollport it runs off the edge, and it hides itself on coarse pointers because a
        hover tip never worked on a phone anyway. <code>.tip</code> is a real popover
        placed with CSS anchor positioning, so it <em>can</em> flip, and it carries an
        arrow that stays pointed at its anchor. Reach for <code>.tooltip</code> for a
        short label on an icon button in the middle of a page; reach for <code>.tip</code>
        when the text is longer, has to survive an edge, or should open on click.</p>
    </div>

    <div class="stack-3">
      <span class="demo-label">.tooltip &mdash; CSS only, hover or focus, never flips, gone on touch</span>
      <div class="cluster">
        <button class="btn btn-icon tooltip" data-tip="Re-run the failed jobs" aria-label="Re-run the failed jobs">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#refresh"></use></svg>
        </button>
        <button class="btn btn-icon tooltip" data-tip="Upload a build artifact" aria-label="Upload a build artifact">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#upload"></use></svg>
        </button>
        <span class="btn btn-ghost tooltip" data-tip="Rate limit is 1,000 requests per minute per key" tabindex="0">
          Rate limits <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#help-sm"></use></svg>
        </span>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">.tip &mdash; anchored popover, flips at an edge, carries an arrow</span>
      <div class="cluster">
        <button class="btn" popovertarget="tipBuild">Why did this build fail?</button>
        <div class="tip" id="tipBuild" popover>The integration suite timed out after 10 minutes
          waiting on a database container that never became healthy. Re-run the job, or raise
          the health check timeout in the workflow file.<span class="tip-arrow"></span></div>

        <button class="btn" popovertarget="tipPayout">Invoice breakdown</button>
        <div class="tip" id="tipPayout">24 seats at $32.50, less the 10% annual discount, plus
          $62.16 tax. Billed to Northwind Traders.<span class="tip-arrow"></span></div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Libraries and performance ===================== -->
  <section class="container section stack-8" id="libs">
    <div class="stack-2">
      <h2>Optional libraries</h2>
      <p class="text-muted">Deck's core stays zero-dependency. <code>deck-adapters.js</code>
        detects a library if you've loaded it and hands that job over, keeping Deck's
        markup and styling unchanged. Load none and everything still works.</p>
    </div>

    <div class="table-wrap">
      <table class="table table-stack">
        <thead><tr><th>Job</th><th>Deck alone</th><th>With a library</th><th>Verdict</th></tr></thead>
        <tbody>
          <tr>
            <td data-label="Job">Placement</td>
            <td data-label="Deck alone">CSS anchor positioning</td>
            <td data-label="With a library">Floating UI, 9 KB</td>
            <td data-label="Verdict">Library only where anchor positioning is missing</td>
          </tr>
          <tr>
            <td data-label="Job">Rich text</td>
            <td data-label="Deck alone"><code>execCommand</code>, deprecated</td>
            <td data-label="With a library">Tiptap or Quill</td>
            <td data-label="Verdict">Use the library</td>
          </tr>
          <tr>
            <td data-label="Job">Charts</td>
            <td data-label="Deck alone">CSS charts that retheme</td>
            <td data-label="With a library">Chart.js, themed by Deck</td>
            <td data-label="Verdict">CSS for tiles, Chart.js for real axes</td>
          </tr>
          <tr>
            <td data-label="Job">Drag and drop</td>
            <td data-label="Deck alone">Native HTML DnD, poor on touch</td>
            <td data-label="With a library">SortableJS</td>
            <td data-label="Verdict">Use the library</td>
          </tr>
          <tr>
            <td data-label="Job">Icons</td>
            <td data-label="Deck alone">75-icon sprite, trimmable to what you use</td>
            <td data-label="With a library">Lucide, 1500 icons</td>
            <td data-label="Verdict">Sprite is enough for Deck; Lucide for the rest</td>
          </tr>
          <tr>
            <td data-label="Job">Long lists</td>
            <td data-label="Deck alone"><code>content-visibility</code></td>
            <td data-label="With a library">A virtualizer, 5 KB</td>
            <td data-label="Verdict">Keep the browser; it doesn't break find-in-page</td>
          </tr>
          <tr>
            <td data-label="Job">Dates and locales</td>
            <td data-label="Deck alone"><code>Intl</code>, built in</td>
            <td data-label="With a library">date-fns or similar</td>
            <td data-label="Verdict">Keep <code>Intl</code></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="alert alert-info">
      <svg class="icon"><use href="assets/deck/deck-icons.svg#info"></use></svg>
      <div>
        <div class="alert-title">What is running right now</div>
        <p class="alert-body">Call <code>Deck.adapters.report()</code> in the console. It
          names what is actually doing each job on this page, which is what you want when
          a component behaves differently between two environments.</p>
        <button class="btn btn-sm mt-2" onclick="showReport()">Show the report</button>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Anchored tooltips and popovers — these flip and shift on their own</span>
      <div class="cluster">
        <button class="btn" popovertarget="tip1">Hover-free tooltip</button>
        <div class="tip" id="tip1" popover>Placement is native. Scroll the page to the edge and it flips instead of running off.<span class="tip-arrow"></span></div>
        <button class="btn" popovertarget="pop1">What is a cascade layer?</button>
        <div class="pop" id="pop1" popover>
          <div class="pop-title">Cascade layers</div>
          <p class="pop-body">An ordering mechanism that sits above specificity. A rule in
            a later layer beats one in an earlier layer no matter how the selectors compare,
            which is how your CSS overrides Deck without <code>!important</code>.</p>
          <div class="pop-actions">
            <button class="btn btn-sm btn-primary" popovertarget="pop1" popovertargetaction="hide">Got it</button>
            <button class="btn btn-sm">Read the docs</button>
          </div>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Kanban with drag reordering</span>
      <div class="kanban">
        <div class="kanban-col">
          <div class="kanban-head">Backlog <span class="kanban-count">3</span></div>
          <div class="kanban-body" data-deck-sortable="issues" data-handle=".drag-handle">
            <div class="kanban-card" data-id="418">
              <div class="bar"><span class="kanban-card-title grow">#418 · CSV export truncates</span><span class="drag-handle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-vertical-sm"></use></svg></span></div>
              <div class="kanban-card-meta"><span class="indicator indicator-warn"></span> api-gateway · 3 points</div>
            </div>
            <div class="kanban-card" data-id="421">
              <div class="bar"><span class="kanban-card-title grow">#421 · Dark mode focus rings</span><span class="drag-handle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-vertical-sm"></use></svg></span></div>
              <div class="kanban-card-meta"><span class="indicator indicator-warn"></span> design-system · 1 point</div>
            </div>
            <div class="kanban-card" data-id="423">
              <div class="bar"><span class="kanban-card-title grow">#423 · Rate limit headers</span><span class="drag-handle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-vertical-sm"></use></svg></span></div>
              <div class="kanban-card-meta"><span class="indicator indicator-warn"></span> api-gateway · 2 points</div>
            </div>
            <div class="kanban-empty">Drop an issue here</div>
          </div>
        </div>
        <div class="kanban-col">
          <div class="kanban-head">In progress <span class="kanban-count">1</span></div>
          <div class="kanban-body" data-deck-sortable="issues" data-handle=".drag-handle">
            <div class="kanban-card" data-id="427">
              <div class="bar"><span class="kanban-card-title grow">#427 · Search pagination</span><span class="drag-handle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-vertical-sm"></use></svg></span></div>
              <div class="kanban-card-meta"><span class="indicator indicator-brand"></span> search-indexer · 5 points</div>
            </div>
            <div class="kanban-empty">Drop an issue here</div>
          </div>
        </div>
        <div class="kanban-col">
          <div class="kanban-head">Shipped <span class="kanban-count">0</span></div>
          <div class="kanban-body" data-deck-sortable="issues" data-handle=".drag-handle">
            <div class="kanban-empty">Drop an issue here</div>
          </div>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Virtualized grid — 5,000 rows, no virtualization library</span>
      <p class="text-muted text-sm">One line of CSS. Off-screen rows are skipped during
        layout, style, and paint, but they stay in the DOM — so Ctrl+F still finds them
        and the print stylesheet still prints them, which a JavaScript virtualizer breaks.
        No library, no fixed row height, no scroll listener.</p>
      <div class="dg-wrap" data-deck-grid style="--dg-height:320px">
        <table class="dg dg-virtual dg-compact dg-zebra">
          <thead><tr>
            <th class="dg-pin-start" data-sort="text">Order</th>
            <th>Customer ID</th><th>Customer</th><th>Rep</th><th>Status</th>
            <th class="dg-num" data-sort="num">Total</th>
          </tr></thead>
          <tbody id="bigRows"></tbody>
        </table>
      </div>
      <span class="text-sm text-muted" id="rowStat"></span>
    </div>

    <div class="stack-3">
      <span class="demo-label">Datepicker localization — <code>Intl</code>, not a date library</span>
      <div class="grid grid-tight">
        <div class="field">
          <label class="label" for="dl1">English (US)</label>
          <div class="datefield" data-deck-datepicker data-locale="en-US">
            <input class="input" id="dl1" placeholder="Pick a date">
          </div>
        </div>
        <div class="field">
          <label class="label" for="dl2">Español (México)</label>
          <div class="datefield" data-deck-datepicker data-locale="es-MX" data-format="dmy">
            <input class="input" id="dl2" placeholder="Elige una fecha">
          </div>
        </div>
        <div class="field">
          <label class="label" for="dl3">Deutsch — week starts Monday</label>
          <div class="datefield" data-deck-datepicker data-locale="de-DE" data-format="dmy">
            <input class="input" id="dl3" placeholder="Datum wählen">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== Print ===================== -->
  <section class="container section stack-6" id="print">
    <div class="stack-2">
      <h2>Print</h2>
      <p class="text-muted">Invoices, packing slips, and reports still end up on paper.
        Print this page and the nav, tab bar, buttons, toasts, and theme dock drop out;
        the grid unfreezes and prints every column; the mobile card fallbacks revert to
        real tables; dark mode is forced back to light.</p>
    </div>

    <div class="cluster">
      <button class="btn btn-primary" onclick="window.print()">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#file-sm"></use></svg>
        Print preview
      </button>
      <span class="badge">.page-break</span>
      <span class="badge">.keep-together</span>
      <span class="badge">.no-print</span>
      <span class="badge">.print-only</span>
      <span class="badge">.print-keep</span>
    </div>

    <div class="alert alert-info">
      <svg class="icon"><use href="assets/deck/deck-icons.svg#info"></use></svg>
      <div>
        <div class="alert-title">This line only exists on paper</div>
        <p class="alert-body">The block below is hidden on screen and appears in the
          print preview.</p>
      </div>
    </div>

    <div class="print-only panel">
      <h3>Invoice INV-2041 — Northwind Traders</h3>
      <p>Printed on <span class="mono">2026-09-07</span>.</p>
    </div>
  </section>


  <!-- ===================== Footer ===================== -->
  <footer class="container section stack-4 text-muted">
    <hr>
    <div class="cluster cluster-between">
      <div class="cluster cluster-tight">
        <svg class="icon icon-fill" style="color:var(--brand)"><use href="assets/deck/deck-icons.svg#deck-mark"></use></svg>
        <span class="fw-semi text-inherit">Deck</span>
        <span class="text-sm">one stylesheet, no build step</span>
      </div>
      <div class="cluster cluster-tight text-sm">
        <span class="badge" id="lineCount">CSS + optional JS</span>
        <span class="badge">0 dependencies</span>
      </div>
    </div>
  </footer>
</main>

<!-- ===================== Mobile tab bar ===================== -->
<div class="speed-dial">
  <div class="speed-dial-actions">
    <div class="speed-dial-action"><span class="speed-dial-label">New issue</span><button class="speed-dial-button" aria-label="New issue"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#clipboard-sm"></use></svg></button></div>
    <div class="speed-dial-action"><span class="speed-dial-label">Search</span><button class="speed-dial-button" aria-label="Search"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#search-sm"></use></svg></button></div>
    <div class="speed-dial-action"><span class="speed-dial-label">Upload a file</span><button class="speed-dial-button" aria-label="Upload a file"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#camera-sm"></use></svg></button></div>
  </div>
  <button class="fab" aria-label="Quick actions" aria-expanded="false"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#plus"></use></svg></button>
</div>

<a class="back-to-top" href="#top" aria-label="Back to top">
  <svg class="icon"><use href="assets/deck/deck-icons.svg#chevron-up"></use></svg>
</a>

<nav class="tabbar" aria-label="Main">
  <a class="tabbar-item" aria-current="page" href="#main">
    <svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#home"></use></svg> Home
  </a>
  <a class="tabbar-item" href="#forms">
    <svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#clipboard"></use></svg> Issues
  </a>
  <a class="tabbar-item" href="#mobile">
    <svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#chart"></use></svg> Reports
  </a>
  <a class="tabbar-item" href="#main">
    <svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#user"></use></svg> Account
  </a>
</nav>

<div class="toast-region" id="toasts"></div>

<script>
  /* deck.js is deferred, so Deck only exists once parsing finishes. This
     page code runs on DOMContentLoaded, which is after every deferred script.
     Note the library has already self-initialised by then, so this is too late
     to configure it -- deck.js resolves the icon sprite from its own URL, and
     data-deck-icons on the script tag overrides it. This init() is just a
     rescan; it is idempotent, guarded by data-deck-wired. */
  document.addEventListener('DOMContentLoaded', function () {
    Deck.init();

    // Theme switch
    const btn = document.getElementById('themeBtn');
    btn.addEventListener('click', () => {
      const dark = Deck.theme() === 'dark';
      Deck.theme(dark ? 'light' : 'dark');
      btn.querySelector('use').setAttribute('href', 'assets/deck/deck-icons.svg#' + (dark ? 'moon' : 'sun'));
    });

    // Indeterminate checkbox demo
    document.getElementById('indet').indeterminate = true;

    // Older buttons in this page call toast(); route them through the queue
    window.toast = function toast(message, kind) { Deck.toast({ title: message, kind: kind || '' }); };

    // Loading toast that resolves into a success toast
    window.demoProgress = function demoProgress() {
      const t = Deck.toast({ kind: 'loading', title: 'Deploying build 4471', duration: 0, dismissible: false });
      setTimeout(() => t.update({ kind: 'good', title: 'Build 4471 is live', text: 'Deployed to production in 42 seconds.', duration: 5000, dismissible: true }), 2200);
    };

    // Density toggle on the grid demo
    document.querySelector('#grid .segmented').addEventListener('deck:change', e => {
      document.querySelector('#grid .dg').classList.toggle('dg-compact', e.detail.value === 'Compact');
    });

    // Motion demos
    window.replayEntrances = function replayEntrances() {
      document.querySelectorAll('#entranceDemo > .card').forEach(c => {
        c.classList.remove('enter-rise');
        void c.offsetWidth;
        c.classList.add('enter-rise');
      });
    };
    window.bumpTotal = function bumpTotal() {
      const el = document.getElementById('total');
      const now = parseFloat(el.textContent.replace(/[^0-9.]/g, ''));
      const next = now + (Math.random() > 0.5 ? 1 : -1) * (40 + Math.random() * 300);
      el.textContent = '$' + next.toFixed(2);
    };

    // Direction label
    const dirLabel = document.getElementById('dirLabel');
    if (dirLabel) {
      new MutationObserver(() => { dirLabel.textContent = Deck.dir(); })
        .observe(document.documentElement, { attributes: true, attributeFilter: ['dir'] });
      dirLabel.textContent = Deck.dir();
    }

    // Adapter report
    window.showReport = function showReport() {
      const r = Deck.adapters.report();
      Deck.toast({
        kind: 'info',
        title: 'Doing the work right now',
        text: Object.entries(r).map(([k, v]) => k + ': ' + v).join(' · '),
        duration: 12000
      });
    };

    // 5,000 rows, rendered as plain DOM and skipped by content-visibility
    (function () {
      const body = document.getElementById('bigRows');
      if (!body) return;
      const t0 = performance.now();
      const customers = ['Northwind Traders','Globex Corp','Initech','Umbrella Ltd','Vandelay Industries'];
      const reps = ['Priya Lakhani','Ken Spence','Dana Whitfield','Marco Reyes','Tina Okafor'];
      const statuses = [['good','Paid'],['warn','Pending'],['bad','Refunded']];
      const frag = document.createDocumentFragment();
      for (let i = 0; i < 5000; i++) {
        const [kind, label] = statuses[i % 3];
        const tr = document.createElement('tr');
        tr.innerHTML =
          '<td class="dg-pin-start"><a href="#libs">#' + (1000 + i) + '</a></td>' +
          '<td class="mono">cus_Q4nR8uTk' + String(10000 + i).slice(-5) + '</td>' +
          '<td>' + customers[i % 5] + '</td>' +
          '<td>' + reps[i % 5] + '</td>' +
          '<td><span class="badge badge-' + kind + '">' + label + '</span></td>' +
          '<td class="dg-num">$' + ((i * 37) % 1900 + 100).toFixed(2) + '</td>';
        frag.append(tr);
      }
      body.append(frag);
      const ms = (performance.now() - t0).toFixed(0);
      document.getElementById('rowStat').textContent =
        '5,000 rows built and inserted in ' + ms + ' ms. Try Ctrl+F for an order number near the bottom.';
    })();

    // Reactions
    document.querySelectorAll('.reaction').forEach(r => {
      r.addEventListener('click', () => {
        r.setAttribute('aria-pressed', r.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
      });
    });
  });
</script>
</body>
</html>