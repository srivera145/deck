<?php
declare(strict_types=1);

$page = [
    'path' => 'components/split.php',
    'title' => 'Split',
    'level' => 'Beginner',
    'description' => 'Deck\'s .split is content plus a rail: one column below 64rem, two above it, with the rail width set by --rail. Includes the min-inline-size: 0 rule that stops a grid child pushing the page sideways.',
    'documents' => [
        'split', 'split-rail-start',
    ],

    'component' => 'split',
    'accounts' => [
        '04-layout.css'    => 'documented: the two-column grid, the rail width, the start-side variant and the min-inline-size fix',
        '18-container.css' => 'documented: a .cq child of a split becomes its own query container — the A rail that knows it is narrow section',
        '99-print.css'     => 'documented: the split is unwound to display: block for print — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Split</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Split</h1>
  <p class="lede">
    <code>.split</code> is content plus a rail. One column below
    <code>64rem</code>, two above it: a flexible main column and a fixed-width sidebar
    whose size comes from <code>--rail</code>, defaulting to
    <code>20rem</code>. It is a grid, not a flex row, because a grid can give one track
    a fixed width without either child having to know about the other.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for a page or a panel that has a primary column and a secondary one:
    an article with a table of contents, a form with a summary, a dashboard with
    filters. The rail goes second in the markup, which is also the order it should be
    read in.
  </p>
  <?php
  docs_example(
      '<div class="split">' . "\n" .
      '  <div class="stack-3">' . "\n" .
      '    <h3>Invoice INV-2291</h3>' . "\n" .
      '    <p>Two licences, billed annually. Charged to the card ending 4242.</p>' . "\n" .
      '    <p class="text-muted">The main column takes whatever is left.</p>' . "\n" .
      '  </div>' . "\n" .
      '  <aside class="panel stack-2">' . "\n" .
      '    <strong>Summary</strong>' . "\n" .
      '    <p class="text-muted">The rail is 20rem, and only exists above 64rem.</p>' . "\n" .
      '  </aside>' . "\n" .
      '</div>',
      'Wide enough for two columns; narrow the window and it becomes one',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="rail">The rail width</h2>
  <p>
    <code>--rail</code> sets the second track. It takes a length rather than a fraction,
    which is the point: a sidebar with a fixed width is predictable, and the main column
    absorbs the difference as the window changes.
  </p>
  <?php
  docs_example(
      '<div class="split" style="--rail:12rem">' . "\n" .
      '  <div class="stack-2">' . "\n" .
      '    <h3>A narrower rail</h3>' . "\n" .
      '    <p class="text-muted">--rail:12rem, set inline.</p>' . "\n" .
      '  </div>' . "\n" .
      '  <aside class="panel"><p class="text-muted">12rem</p></aside>' . "\n" .
      '</div>',
      'No new class needed for a different rail',
      'stack'
  );
  ?>
  <p class="text-muted">
    The main track is <code>minmax(0, 1fr)</code> rather than <code>1fr</code>. That
    zero minimum is what lets the main column shrink below its content's natural width,
    and it is the same defence as the <code>min-inline-size</code> rule below.
  </p>
</section>

<section class="stack-3">
  <h2 id="rail-start">Rail on the starting edge</h2>
  <p>
    <code>.split-rail-start</code> swaps the track order so the rail comes first
    visually. <strong>The markup order does not change</strong> — the rail is still the
    second child — so this is a case where visual order and DOM order deliberately
    differ. That is defensible for a navigation rail that should be read after the
    content but appear before it; it is not defensible if the rail contains the first
    thing a reader needs.
  </p>
  <?php
  docs_example(
      '<div class="split split-rail-start">' . "\n" .
      '  <div class="stack-2">' . "\n" .
      '    <h3>Main content, second visually</h3>' . "\n" .
      '    <p class="text-muted">Still first in the markup, so still first for a screen reader.</p>' . "\n" .
      '  </div>' . "\n" .
      '  <aside class="panel"><p class="text-muted">Rail, on the starting edge</p></aside>' . "\n" .
      '</div>',
      'Visual order reversed; reading order unchanged',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Because the rail is placed by <code>grid-template-columns</code> rather than by
    <code>order</code>, the tab order still follows the markup: a keyboard user reaches
    the main content first and then the rail, even though the rail is on the left. Decide
    whether that is what you want before reaching for this class.
  </p>
</section>

<section class="stack-6">
  <h2 id="min-inline-size">The one line that stops a page scrolling sideways</h2>
  <pre class="dx-code"><code><?= e('.split > * { min-inline-size: 0; }') ?></code></pre>
  <p>
    A grid item's <code>min-inline-size</code> is <code>auto</code>, which means its
    minimum size is its content. A child that cannot shrink — a wide table, a long code
    block, a form row, an unbroken URL — therefore makes its track wider than the track
    was supposed to be, and the whole grid grows past its container. The page then
    scrolls sideways, and the thing that caused it is usually nowhere near the thing that
    looks wrong.
  </p>
  <p>
    This is the single most common CSS grid bug there is, and Deck has hit it twice: once
    in the docs shell, where a wide table dragged the sidebar off-screen, and once in the
    demo, where a two-column form overflowed a 375px viewport by exactly 16px until this
    line existed.
  </p>
  <?php
  docs_example(
      '<div class="split" style="--rail:8rem">' . "\n" .
      '  <div class="stack-2">' . "\n" .
      '    <p class="truncate">https://example.com/exports/2026/03/a-very-long-generated-filename-that-cannot-wrap.csv</p>' . "\n" .
      '    <p class="text-muted">The column shrinks because it is allowed to.</p>' . "\n" .
      '  </div>' . "\n" .
      '  <aside class="panel"><p class="text-muted">Rail</p></aside>' . "\n" .
      '</div>',
      'A string that cannot wrap, in a column that is allowed to shrink',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>min-inline-size: 0</code> lets the track shrink; it does not decide what
    happens to the content inside it. Pair it with <code>.truncate</code>,
    <code>overflow-x: auto</code>, or <code>overflow-wrap: anywhere</code> depending on
    whether the content should be clipped, scrolled or broken.
  </p>
</section>

<section class="stack-3">
  <h2 id="cq">A rail that knows it is narrow</h2>
  <p>
    <code>src/18-container.css</code> makes a <code>.cq</code> child of a split its own
    query container, so what is inside the rail can respond to the rail's width rather
    than the viewport's. That matters here more than anywhere else in Deck: a component
    in a <code>20rem</code> rail on a <code>1600px</code> monitor is in a narrow space
    while every viewport media query says the page is wide.
  </p>
  <?php
  docs_example(
      '<div class="split" style="--rail:16rem">' . "\n" .
      '  <div class="stack-2"><h3>Main</h3><p class="text-muted">Plenty of room here.</p></div>' . "\n" .
      '  <aside class="cq">' . "\n" .
      '    <div class="card">' . "\n" .
      '      <div class="card-body"><h3 class="card-title text-cq">Sized by the rail</h3></div>' . "\n" .
      '    </div>' . "\n" .
      '  </aside>' . "\n" .
      '</div>',
      'The heading in the rail is sized by the rail, not by the window',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="breakpoint">The one breakpoint</h2>
  <p>
    <code>.split</code> contains a <code>@media (min-width: 64rem)</code>, which makes it
    one of the few things in Deck that has a breakpoint at all. It is here because the
    decision — is there room for a sidebar beside the content — genuinely depends on how
    wide the <em>page</em> is, and because a split is nearly always the outermost layout
    on a page.
  </p>
  <p>
    If your split is not the outermost layout — a split inside a modal, a drawer or
    another rail — the viewport is the wrong thing to ask, and the media query will give
    you two columns in a space that has room for one. Use a container query of your own
    for that case; <a href="field.php#row-cq"><code>.field-row-cq</code></a> is the same
    problem solved for form fields.
  </p>
</section>

<section class="stack-3">
  <h2 id="gap">Gap</h2>
  <p>
    Same <code>--gap</code>, but with a looser default than the other primitives:
    <?= e(api_token('--space-6')['value'] ?? '1.5rem') ?>, because two columns of
    content need more separation than two items in a row.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/04-layout.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--space-4', '--space-6', '--space-8']); ?>
  <p class="text-muted">
    <code>--rail</code> is a local property with a <code>20rem</code> fallback rather
    than a global token, because a rail width is a per-layout decision and not part of
    the design system.
  </p>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Markup order is reading order and tab order.</strong> Put the main content
      first. Everything on this page follows from that: the rail is the second child even
      when <code>.split-rail-start</code> draws it on the left.
    </li>
    <li>
      <strong><code>.split-rail-start</code> separates visual and DOM order on
      purpose</strong>, which is the one thing this page asks you to think about. It is
      fine for supplementary navigation; it is wrong if a sighted reader would encounter
      something in the rail before the content and a keyboard user would not.
    </li>
    <li>
      <strong>The rail should usually be an <code>&lt;aside&gt;</code></strong>, which
      is a complementary landmark a screen reader can jump to. A bare
      <code>&lt;div&gt;</code> is fine when the rail is decoration rather than a
      distinct region.
    </li>
    <li>
      <strong>Below 64rem the rail follows the content.</strong> On a phone the sidebar
      is at the bottom of the page. That is correct for a summary and wrong for filters
      the reader needs before they scroll — in which case put the filters in a
      <code>.drawer</code> rather than a rail.
    </li>
    <li>
      <strong><code>min-inline-size: 0</code> is an accessibility fix as much as a
      layout one.</strong> Horizontal scrolling at 320px or 400% zoom fails WCAG's
      reflow criterion, and an overflowing grid track is the usual cause.
    </li>
    <li>
      <strong>Two columns can double the tab distance.</strong> A reader tabbing through
      the main column reaches the rail only after everything in the content. If the rail
      holds a frequently used control, that is a long way; a skip link or putting the
      control in the content is the answer.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Grid columns follow the inline direction, so under <code>dir="rtl"</code> the main
    column is on the right and the rail on the left, with no extra rules.
    <code>.split-rail-start</code> means the <em>starting</em> edge, not the left one, so
    it also mirrors — which is exactly why it is not called
    <code>.split-rail-left</code>.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="split" style="--rail:12rem">' . "\n" .
      '  <div class="stack-2"><h3>المحتوى الرئيسي</h3><p class="text-muted">العمود المرن.</p></div>' . "\n" .
      '  <aside class="panel"><p class="text-muted">الشريط الجانبي</p></aside>' . "\n" .
      '</div>',
      'Main on the right, rail on the left, from the same markup',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing animates. Collapsing to one column at <code>64rem</code> happens at layout
    time and is not a transition.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> sets <code>.split { display: block }</code>. Two
    columns on paper means a reader finishing column one at the bottom of page four and
    going back to page one for column two, and a grid track cannot break across pages
    anyway. The rail therefore prints after the content, in markup order — which is
    another reason for the main content to be first.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One split, no layer needed */
<div class="split" style="--rail:16rem;--gap:var(--space-8)">

@layer app.components {
  /* Split sooner than 64rem */
  @media (min-width: 52rem) {
    .split { grid-template-columns: minmax(0, 1fr) var(--rail, 20rem); }
  }

  /* A rail that keeps a share of the width rather than a fixed size */
  .split-fluid { --rail: minmax(14rem, 24rem); }
}') ?></code></pre>
  <p class="text-muted">
    <code>--rail</code> is dropped into <code>grid-template-columns</code> verbatim, so
    it accepts any track size — <code>minmax()</code>, <code>fit-content()</code>, a
    percentage — not only a length.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for two equal columns.</strong> A split has one flexible column and one
      fixed rail. Two things of equal weight are <code>.grid-fixed-2</code>, or
      <code>.grid</code> if they should collapse.
    </li>
    <li>
      <strong>Not for the app frame.</strong> A persistent navigation sidebar with a
      header and a footer is <code>.app-shell</code> plus <code>.sidebar</code>, which
      handle the full height and the scroll behaviour. A split is a content layout.
    </li>
    <li>
      <strong>Not inside a modal or a drawer.</strong> Its breakpoint asks about the
      viewport, and inside a narrow container the viewport is the wrong question. Use a
      container query.
    </li>
    <li>
      <strong>Not for a rail the reader needs first on a phone.</strong> Below
      <code>64rem</code> the rail is at the bottom. Filters, a summary the reader acts
      on, or a call to action belong in the content column or in a
      <code>.drawer</code>.
    </li>
    <li>
      <strong>Not three columns.</strong> Nesting a split inside a split gives you two
      breakpoints fighting over the same viewport. Use <code>.grid</code> with explicit
      tracks, or reconsider whether the third column is a rail at all.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
