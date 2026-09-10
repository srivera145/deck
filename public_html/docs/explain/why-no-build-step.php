<?php
declare(strict_types=1);

/**
 * Why Deck has no build step — an essay.
 *
 * Written last, after the component pages, the reference, the tutorials and
 * the guides, so it argues about the costs those pages actually ran into
 * rather than the ones a framework author would guess at. Every number here
 * was measured or read from a named source while it was written:
 * dist/sizes.json for file sizes, a class-based purge of the first tutorial's
 * finished page for what tree-shaking would save, caniuse for first-shipping
 * browser versions, MDN for Baseline dates, and tailwindcss.com for claims
 * about Tailwind.
 *
 * Paragraphs are written to stand alone. Each one names its subject rather
 * than leaning on the paragraph before it, because this is the page most
 * likely to be quoted a paragraph at a time.
 */

$page = [
    'path' => 'explain/why-no-build-step.php',
    'title' => 'Why Deck has no build step',
    'level' => 'Intermediate',
    'description' => "Deck ships one fixed stylesheet with no tree-shaking. What that costs in kilobytes and browser support, what it buys in exchange, and who should use a build step instead.",
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Explanation</li>
    <li aria-current="page">Why no build step</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Why Deck has no build step</h1>
  <p class="lede">
    Deck has no build step, so it cannot strip out the parts of its stylesheet that a page
    does not use. Every page that loads Deck downloads all of it: 26.7 KB compressed with
    Brotli, or 32.7 KB with gzip, whether the page uses ten classes or nine hundred. Deck
    makes that trade because a stylesheet with no build
    step styles content that did not exist when the site was deployed, rethemes at runtime
    from a single value, and adopts new browser features without a redeploy.
  </p>
</header>

<section class="stack-4">
  <h2 id="cost">What having no build step costs</h2>
  <p>
    Deck's first tutorial builds an account settings page that uses 59 classes, which is
    7.1% of the 828 classes in Deck's public API. A class-based purge of that page — the
    technique build tools use to keep only the rules a page can match, counting the
    classes Deck's own scripts add at runtime — reduces Deck's stylesheet from 26.4 KB to
    14.1 KB with Brotli, with both versions minified the same way. Deck has no step that
    could make that cut, so the settings page downloads the other 12.3 KB, 46% of the
    stylesheet, and every other page that uses Deck pays the same way.
  </p>
  <p>
    Deck's icon sprite carries the same kind of cost. The published sprite is 27.1 KB with
    Brotli and contains every icon in the set, while a sprite holding only twelve common
    icons measures 5.7 KB. A page that shows a search glass and a close button still
    downloads all of them.
  </p>
  <p>
    Having no build step also means having no autoprefixer. Deck's source contains 69
    vendor-prefixed tokens written by hand. Some of them, such as
    <code>-webkit-backdrop-filter</code> and <code>-webkit-mask-image</code>, are exactly
    what an autoprefixer exists to generate and later retire; without a build step, noticing
    that a browser no longer needs a prefix, and deleting it, is manual work.
  </p>
  <p>
    Every CSS feature Deck depends on has to exist natively in the browser, because nothing
    compiles it down. Deck's colour system is built on the <code>light-dark()</code>
    function, which first shipped in Chrome and Edge 123, Firefox 120 and Safari 17.5,
    according to caniuse. A browser without <code>light-dark()</code> does not show a
    slightly degraded page. MDN documents that when a custom property holds a value that is
    invalid where it is used, the property falls back to its inherited or initial value, so
    in that browser Deck's brand colours, surfaces and text colours are all lost at once.
  </p>
  <p>
    Deck's <code>package.json</code> currently declares Chrome and Edge 117, Firefox 128
    and Safari 17.4 as its minimum versions. For Chrome, Edge and Safari that is lower than
    the <code>light-dark()</code> function allows, so the practical floor for Deck is
    Chrome and Edge 123 and Safari 17.5, whatever the package metadata says.
  </p>
</section>

<section class="stack-4">
  <h2 id="history">What build steps were for</h2>
  <p>
    Much of what CSS build tools were adopted to provide now ships in browsers. Custom
    properties cover what preprocessor variables were used for, and unlike them they can
    change while the page is running. The <code>oklch()</code> and
    <code>color-mix()</code> functions, which let a stylesheet generate a palette from
    arithmetic, have been Baseline widely available since May 2023. Cascade layers, which
    settle which rules win without selector tricks, have been Baseline widely available
    since March 2022. Container queries first shipped in Chrome and Edge 106, Firefox 110
    and Safari 16. Each of these once needed a compiler or JavaScript to approximate, and
    Deck uses all of them directly.
  </p>
  <p>
    The capability a CSS build step still has that the browser does not is removing code a
    page never uses. That capability is real, and on Deck's tutorial settings page it is
    worth 46% of the stylesheet. Deck gives that capability up deliberately, in exchange
    for styling every
    class that later content might use, retheming at runtime, and adopting new browser
    features without a rebuild.
  </p>
</section>

<section class="stack-4">
  <h2 id="exchange">What Deck gets in exchange</h2>
  <p>
    Deck ships <code>deck.css</code>, which is its 26 source stylesheets concatenated in
    order, and <code>deck.min.css</code>, which is the same file minified. A project
    installs Deck by linking one of those files exactly as published, and no tool in the
    project rewrites it. The rules a browser's developer tools show are the rules in Deck's
    repository, and a bug report can point at a file and a line.
  </p>
  <pre class="dx-code"><code>&lt;link rel="stylesheet" href="/assets/deck/deck.min.css"&gt;</code></pre>
  <p>
    A build step can only generate CSS for class names it finds while it runs. Tailwind CSS
    v4, for example, scans a project's source files for class names and generates CSS for
    those, and provides an <code>@source inline()</code> directive to safelist names the
    scan would miss. A class that first appears after deployment — in a content management
    field, a database row, or markup returned by another service — has no CSS in a
    build-step framework unless someone anticipated it. All 931 of Deck's classes are always
    present, so markup that did not exist when the site was deployed is styled exactly like
    markup in a template.
  </p>
  <p>
    Because nothing about Deck is resolved at build time, Deck's theme is resolved in the
    browser. Setting one custom property, <code>--hue-brand</code>, recalculates 18
    brand-derived tokens, and 198 declarations spread across 20 of Deck's 26 stylesheets
    read those tokens. A multi-tenant application can give each customer a different brand
    colour from a single value stored in a database, emitted as one inline style, while
    every tenant shares one stylesheet file and no tenant needs a build.
  </p>
  <pre class="dx-code"><code>&lt;html style="--hue-brand: 265"&gt;</code></pre>
  <p>
    A stylesheet that is identical on every page is downloaded once per site and then
    served from the browser cache for every page after the first. That saving applies
    within a site and not across sites: Chrome since version 86, Firefox since version 85
    and Safari all partition the HTTP cache by the site that requested a file, so a copy of
    Deck cached while visiting one website is not reused by another. Deck's PHP helper adds
    the file's modification time to the stylesheet URL, which makes a long cache lifetime
    safe while still letting a deploy replace the file.
  </p>
  <p>
    A stylesheet that is not compiled for a fixed list of browsers can improve as browsers
    do. Deck's source contains 25 <code>@supports</code> blocks testing 10 features,
    including <code>contrast-color()</code>, scroll-driven animations and anchor
    positioning. When a browser ships one of those features, pages already using Deck begin
    using it on their next load, with no rebuild and no redeploy.
  </p>
</section>

<section class="stack-4">
  <h2 id="trimming">Where Deck can still be trimmed</h2>
  <p>
    Deck offers a manual, coarse form of trimming. The <code>dist/layers</code> folder
    holds each of Deck's 26 source stylesheets as a separate file, so a project can link
    only the ones it needs, loading <code>layers.css</code> and <code>tokens.css</code>
    first. Eight of those files — the layer order, tokens, reset, type, layout, buttons,
    forms and utilities — measure 13.7 KB with Brotli as shipped, and 7.0 KB once minified.
    The trimming works by file rather than by class, choosing the files is the project's
    job, and minifying them is a step Deck does not do for that subset.
  </p>
  <p>
    Deck's icon sprite is the one asset with a real build step, and that step is optional.
    A project that clones Deck's repository can list the icons it wants in
    <code>tools/icons/icons.txt</code> and run <code>npm run icons</code>, which needs the
    <code>opentype.js</code> development dependency and the icon font kept in the
    repository, to produce a sprite containing only those icons. Deck's published
    twelve-icon sample measures 5.7 KB with Brotli, against 27.1 KB for the full sprite.
  </p>
</section>

<section class="stack-4">
  <h2 id="not-for">Who Deck is not for</h2>
  <p>
    A team already productive in Tailwind CSS, with a design system built on it, should
    probably stay there. Tailwind's official IntelliSense extension gives Visual Studio Code
    autocompletion, linting and hover previews of the generated CSS; Zed and JetBrains IDEs
    include equivalent support; an official Prettier plugin sorts class names; and
    first-party plugins such as the typography plugin extend the framework. Deck has none of
    that tooling, and for a team whose toolchain already works, trading it for a stylesheet
    without one is rarely a good exchange.
  </p>
  <p>
    A small marketing site that uses a few of Deck's components and none of its application
    widgets pays for all of them anyway. For a site like that, a framework with a build step
    that ships only the classes in use is likely to produce a smaller stylesheet, and Deck's
    data grid, date picker and chart styles are weight the site gets no use from.
  </p>
  <p>
    A product that must support Safari 17.4 or earlier, or Chrome and Edge 122 or earlier,
    cannot use Deck, because Deck's colour system depends on <code>light-dark()</code>.
    Tailwind CSS v4 states support for Safari 16.4, Chrome 111 and Firefox 128, so for
    Safari and Chrome it reaches further back than Deck does.
  </p>
  <p>
    A team that may only install dependencies from npm or Packagist cannot use Deck today.
    Deck is at version 0.1 and is not yet published to either registry, so installing it
    means downloading the files or copying them from the Git repository, as the
    <a href="../start/install.php">install page</a> describes.
  </p>
</section>

<section class="stack-4">
  <h2 id="case">The case in one paragraph</h2>
  <p>
    Deck's argument is that for server-rendered applications with many pages, many tenants
    or content that changes after deployment, a fixed 26.7 KB stylesheet is a better deal
    than a CSS toolchain. That one file is cached once per site, styles every class that
    later content might use, rethemes from a single value at runtime, and adopts new browser
    features without a redeploy. The price is 12.3 KB of unused CSS on a typical settings
    page, 69 hand-maintained vendor prefixes, and a minimum of Chrome 123, Firefox 120 and
    Safari 17.5 — each of which is a number a team can weigh before choosing Deck.
  </p>
  <p class="text-sm text-muted">
    Related: <a href="why-cascade-layers.php">why Deck uses cascade layers</a> and
    <a href="why-one-hue.php">why Deck themes from one hue</a>.
  </p>
</section>

<?php docs_footer(); ?>
