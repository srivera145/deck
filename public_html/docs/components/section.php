<?php
declare(strict_types=1);

$page = [
    'path' => 'components/section.php',
    'title' => 'Section',
    'level' => 'Beginner',
    'description' => 'Deck\'s .section is one declaration: block padding from a clamp that grows with the viewport. The vertical counterpart of the page gutter, and why it is padding rather than margin.',
    'documents' => [
        'section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Section</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Section</h1>
  <p class="lede">
    <code>.section</code> is one declaration —
    <code>padding-block: var(--space-section)</code> — where the token is
    <code><?= e(api_token('--space-section')['value'] ?? 'clamp(2.5rem, 1.4rem + 5.5vw, 6rem)') ?></code>.
    It is the vertical counterpart of the page gutter: breathing room between the major
    parts of a page that grows with the viewport instead of stepping at breakpoints.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it on the outermost band of each part of a page — usually a
    <code>&lt;section&gt;</code> or <code>&lt;header&gt;</code> element, with a
    <a href="container.php"><code>.container</code></a> inside it. The section owns the
    vertical rhythm and any background; the container owns the width.
  </p>
  <?php
  docs_example(
      '<div class="stack-0">' . "\n" .
      '  <section class="section" style="background:var(--surface-2)">' . "\n" .
      '    <div class="container"><h3>First section</h3></div>' . "\n" .
      '  </section>' . "\n" .
      '  <section class="section">' . "\n" .
      '    <div class="container"><h3>Second section</h3></div>' . "\n" .
      '  </section>' . "\n" .
      '</div>',
      'Two bands. Resize the window and the padding follows.',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="clamp">Why a clamp</h2>
  <p>
    <code>clamp(2.5rem, 1.4rem + 5.5vw, 6rem)</code> reads as: never less than
    <?= e('2.5rem') ?>, never more than <?= e('6rem') ?>, and in between it grows with
    the viewport at 5.5% of its width.
  </p>
  <p>
    The alternative is a media query that jumps from <?= e('2.5rem') ?> to
    <?= e('6rem') ?> at some width. The clamp is better for a reason that is easy to
    miss: there is no width at which the page suddenly changes shape. Drag a browser
    window across a breakpoint and a stepped layout visibly lurches; a clamped one
    slides. It also means there is no width that was never designed for, because every
    width gets a value on the same line.
  </p>
  <p class="text-muted">
    The same reasoning produces
    <code>--space-gutter</code>:
    <code><?= e(api_token('--space-gutter')['value'] ?? 'clamp(1rem, .55rem + 2.2vw, 2rem)') ?></code>.
    Between them, the two clamps are the whole of Deck's page-level spacing, and neither
    involves a breakpoint.
  </p>
</section>

<section class="stack-3">
  <h2 id="padding">Padding, not margin</h2>
  <p>
    <code>.section</code> uses <code>padding-block</code>. That choice does two things
    that a margin would not:
  </p>
  <ul class="stack-2">
    <li>
      <strong>Two adjacent sections do not collapse.</strong> Vertical margins merge into
      the larger of the two, so two sections with <?= e('6rem') ?> margins would be
      <?= e('6rem') ?> apart rather than <?= e('12rem') ?>. With padding, each section
      keeps its own space and the total is what it looks like.
    </li>
    <li>
      <strong>A background covers the space.</strong> Padding is inside the box, so a
      section with a background colour has the breathing room <em>within</em> the
      colour. With a margin the band would be tight against its content and the space
      would be page-coloured — which is almost never the intent.
    </li>
  </ul>
  <p>
    This is the same argument the <a href="stack.php#why">stack page</a> makes about gap
    versus margin, arriving at a different property for a different job. Deck's position
    is not that margins are wrong; it is that the container should own the spacing.
  </p>
</section>

<section class="stack-3">
  <h2 id="inside">Not for spacing inside a component</h2>
  <p>
    At the top of its range <code>.section</code> is <?= e('6rem') ?> of padding, top and
    bottom. Inside a card that is close to two inches of nothing. For spacing within a
    component, use <a href="stack.php"><code>.stack</code></a> and its steps — the
    largest, <code>.stack-8</code>, is
    <?= e(api_token('--space-8')['value'] ?? '2rem') ?>.
  </p>
  <p class="dx-note text-muted">
    The rule of thumb: if the element has a background that reaches the edge of the
    screen, <code>.section</code> is probably right. If it does not,
    <code>.stack</code> probably is.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    One class, one declaration, no variants — so there is no component root and no
    completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--space-section', '--space-gutter', '--space-8']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The class is not the element.</strong> <code>.section</code> supplies
      padding and nothing else — no role, no landmark. Put it on a real
      <code>&lt;section&gt;</code> with a heading, or on
      <code>&lt;header&gt;</code>/<code>&lt;footer&gt;</code>, if the division should
      exist for a screen reader as well as for the eye.
    </li>
    <li>
      <strong>A <code>&lt;section&gt;</code> without an accessible name is not a
      landmark.</strong> Bare, it is exposed as a generic region or not at all. Give it a
      heading and <code>aria-labelledby</code>, or accept that it is visual grouping
      only.
    </li>
    <li>
      <strong>Space is not a separator.</strong> <?= e('6rem') ?> between two blocks
      tells a sighted reader they are different things and tells a screen-reader user
      nothing. Headings are what carry that.
    </li>
    <li>
      <strong>It scales with the reader's font size.</strong> The clamp's floor and
      ceiling are in <code>rem</code>, so at 200% zoom the padding grows too and the page
      keeps its proportions rather than becoming text crammed between fixed gaps.
    </li>
    <li>
      <strong>The <code>vw</code> term does not respond to zoom</strong> in the same way
      — <code>vw</code> is a fraction of the viewport, so zooming in makes the middle
      term smaller in <code>rem</code> terms. The <code>rem</code> floor is what stops
      that mattering.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>padding-block</code> is the block axis, which is unaffected by text direction.
    Nothing to do, and nothing that could go wrong.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing animates. The clamp resizes with the viewport, which is layout rather than
    motion, so <code>prefers-reduced-motion</code> has no bearing on it.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> replaces the padding with
    <code>padding-block: 0 0 8mm</code>: no space at the top, <?= e('8mm') ?> at the
    bottom. On screen the top padding separates a section from the one above it; on
    paper a section can start at the top of a page, and <?= e('6rem') ?> of white there
    would be a wasted third of a sheet. Keeping the bottom space preserves the
    separation without the waste.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One section, no layer needed */
<section class="section" style="--space-section:3rem">

@layer app.pages {
  /* Change the page rhythm everywhere */
  :root { --space-section: clamp(2rem, 1rem + 4vw, 4.5rem); }

  /* A section that only pads the bottom */
  .section-tail { padding-block: 0 var(--space-section); }
}') ?></code></pre>
  <p>
    Overriding <code>--space-section</code> on <code>:root</code> is the intended way to
    change a page's vertical rhythm, and it moves every section together. Setting
    <code>padding-block</code> on <code>.section</code> instead works but leaves the
    token pointing at a value nothing uses.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not inside a card, a modal or a panel.</strong> Its padding is page-scale.
      Use <a href="stack.php"><code>.stack</code></a>, or the component's own padded
      region — <code>.card-body</code> already has one.
    </li>
    <li>
      <strong>Not for the space between two paragraphs.</strong> That is a
      <code>.stack</code> step, or <code>.prose</code> if the content is rendered
      Markdown.
    </li>
    <li>
      <strong>Not on the same element as <code>.container</code>.</strong> It works, but
      then the background is capped at the container's width and stops short of the
      screen edges. Section outside, container inside.
    </li>
    <li>
      <strong>Not stacked with a <code>.stack-8</code> as well.</strong> Two spacing
      systems on the same axis is how a page ends up with gaps nobody chose. Pick the one
      that matches the scale you are working at.
    </li>
    <li>
      <strong>Not as a replacement for a heading.</strong> If the reader needs to know a
      new topic has started, write one. The padding is the visual echo of the structure,
      not the structure.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
