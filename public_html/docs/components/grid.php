<?php
declare(strict_types=1);

$page = [
    'path' => 'components/grid.php',
    'title' => 'Grid',
    'level' => 'Beginner',
    'description' => 'Deck\'s .grid is one auto-fitting rule with no breakpoints: columns appear and disappear as the container changes width. How minmax(min(--min, 100%), 1fr) works, and when a fixed column count is right instead.',
    'documents' => [
        'grid', 'grid-2', 'grid-fixed-2', 'grid-fixed-3', 'grid-fixed-4',
        'grid-tight', 'grid-wide',
    ],

    'component' => 'grid',
    'accounts' => [
        '04-layout.css'    => 'documented: the auto-fit grid, its two floors and the three fixed-column variants',
        '18-container.css' => 'documented: a .cq child of a grid becomes its own query container — the Cards that respond to their track section',
        '99-print.css'     => 'documented: the grid is unwound to display: block for print — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';

/* One set of cards, reused by every example, so the only thing that varies
   between them is the class being demonstrated. */
function demo_cards(int $n = 5): string
{
    $names = ['Exports', 'Invoices', 'Members', 'Webhooks', 'Audit log', 'Domains'];
    $out = '';
    for ($i = 0; $i < $n; $i++) {
        $out .= '  <div class="card"><div class="card-body">' . "\n"
            . '    <h3 class="card-title">' . $names[$i % count($names)] . '</h3>' . "\n"
            . '    <p class="text-muted">A card in a grid track.</p>' . "\n"
            . '  </div></div>' . "\n";
    }
    return $out;
}
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Grid</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Grid</h1>
  <p class="lede">
    <code>.grid</code> is one rule with no breakpoints in it. Columns appear as the
    container gets wider and disappear as it narrows, and the number of them is never
    written down anywhere — it is a consequence of the available width and a minimum
    column size.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for a collection of things of the same kind that should line up in columns:
    cards, stats, thumbnails, a settings menu. Every item gets an equal track, so the
    second row lines up with the first — which is the difference between a grid and a
    <a href="cluster.php">cluster</a> that happens to wrap.
  </p>
  <?php
  docs_example('<div class="grid">' . "\n" . demo_cards(5) . '</div>',
      'Resize the window. Nothing here knows what a breakpoint is.', 'stack');
  ?>
</section>

<section class="stack-6">
  <h2 id="how">How the one rule works</h2>
  <pre class="dx-code"><code><?= e('grid-template-columns: repeat(auto-fit, minmax(min(var(--min, 17rem), 100%), 1fr));') ?></code></pre>
  <p>
    It is worth taking apart, because once you can read it you can write your own.
  </p>
  <ul class="stack-3">
    <li>
      <strong><code>repeat(auto-fit, …)</code></strong> — make as many columns of this
      size as will fit, and collapse the empty ones so the items stretch to fill the
      row. <code>auto-fill</code> is the alternative and leaves the empty tracks in
      place, which is right for a calendar and wrong for a list of cards.
    </li>
    <li>
      <strong><code>minmax(X, 1fr)</code></strong> — every column is at least
      <code>X</code> and shares the leftover space equally. This is what makes the
      columns equal rather than content-sized.
    </li>
    <li>
      <strong><code>min(var(--min, 17rem), 100%)</code></strong> — the important part,
      and the part most hand-written versions get wrong. A bare
      <code>minmax(17rem, 1fr)</code> overflows on any container narrower than
      <code>17rem</code>, because the minimum is a hard floor. Wrapping it in
      <code>min(…, 100%)</code> lets the floor fall to the container's own width when
      that is smaller, so the grid never pushes the page sideways on a phone.
    </li>
  </ul>
  <p class="dx-note text-muted">
    That <code>min(…, 100%)</code> is the single most useful thing on this page. A grid
    that overflows a 320px viewport is nearly always a <code>minmax()</code> with a
    fixed floor.
  </p>
</section>

<section class="stack-6">
  <h2 id="floors">Changing the floor</h2>
  <p>
    <code>--min</code> is the only knob. The variants set it and nothing else, so
    changing the column floor never changes the gap, the alignment or anything about the
    items.
  </p>

  <div class="stack-2">
    <h3 id="f-tight">.grid-tight — a 12rem floor</h3>
    <p class="text-muted">More columns before it wraps. For stats, icons, small tiles.</p>
    <?php
    docs_example('<div class="grid grid-tight">' . "\n" . demo_cards(6) . '</div>', '', 'stack');
    ?>
  </div>

  <div class="stack-2">
    <h3 id="f-wide">.grid-wide — a 24rem floor</h3>
    <p class="text-muted">Fewer, wider columns. For cards with a paragraph in them.</p>
    <?php
    docs_example('<div class="grid grid-wide">' . "\n" . demo_cards(3) . '</div>', '', 'stack');
    ?>
  </div>

  <div class="stack-2">
    <h3 id="f-custom">Any floor you like</h3>
    <p class="text-muted">
      <code>--min</code> is a plain custom property, so a floor Deck does not ship needs
      no class.
    </p>
    <?php
    docs_example('<div class="grid" style="--min:9rem">' . "\n" . demo_cards(6) . '</div>',
        'A 9rem floor, inline', 'stack');
    ?>
  </div>

  <p class="text-muted">
    <code>.grid-2</code> is the odd one out: it hard-codes a <code>24rem</code> floor in
    <code>grid-template-columns</code> rather than setting <code>--min</code>, so it is
    identical to <code>.grid-wide</code> and cannot be adjusted with
    <code>--min</code> afterwards. Two names for one behaviour, one of which composes
    and one of which does not — recorded in <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="fixed">Fixed column counts</h2>
  <p>
    <code>.grid-fixed-2</code>, <code>-3</code> and <code>-4</code> set an exact number
    of equal columns and keep it at every width. They do not collapse on a phone.
  </p>
  <?php
  docs_example('<div class="grid grid-fixed-3">' . "\n" . demo_cards(3) . '</div>',
      'Three columns, at every width, including 320px', 'stack');
  ?>
  <p class="dx-note text-muted">
    Reach for these only when the count is part of the meaning — a 2&times;2 comparison,
    a three-up pricing table where the middle one is the recommended plan. For anything
    else the auto-fit grid is better, because it is right at widths you did not think
    about. A <code>.grid-fixed-4</code> on a phone is four 70px columns.
  </p>
</section>

<section class="stack-3">
  <h2 id="gap">Gap</h2>
  <p>
    Same <code>--gap</code> as everything else, defaulting to
    <?= e(api_token('--space-4')['value'] ?? '1rem') ?>, so the
    <a href="stack.php#scale">spacing scale</a> applies unchanged.
  </p>
  <?php
  docs_example('<div class="grid grid-tight stack-1">' . "\n" . demo_cards(4) . '</div>',
      '.stack-1 on a grid: the step classes only set --gap, so they compose', 'stack');
  ?>
</section>

<section class="stack-3">
  <h2 id="cq">Cards that respond to their track</h2>
  <p>
    <code>src/18-container.css</code> makes a <code>.cq</code> child of a grid its own
    query container. That closes the loop: the grid decides how wide each track is, and
    then each item lays itself out according to the track it landed in — with no media
    query involved at either step.
  </p>
  <?php
  docs_example(
      '<div class="grid grid-tight">' . "\n" .
      '  <div class="cq">' . "\n" .
      '    <div class="card card-flex">' . "\n" .
      '      <div class="card-body"><h3 class="card-title text-cq">Sized by its track</h3></div>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="cq">' . "\n" .
      '    <div class="card card-flex">' . "\n" .
      '      <div class="card-body"><h3 class="card-title text-cq">Not by the window</h3></div>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Each item queries its own column',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/04-layout.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--space-1', '--space-3', '--space-4', '--space-6']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>A CSS grid is not a table.</strong> It has no row or column semantics, so
      a screen reader reads the items in DOM order with no notion of position. If the
      row and column a value sits in carries meaning, it belongs in a
      <code>&lt;table&gt;</code>.
    </li>
    <li>
      <strong>Reading order is DOM order</strong>, and the auto-fit grid never changes
      it. That is a real advantage of this pattern over one that places items explicitly:
      there is no <code>grid-area</code> to get out of step with the markup.
    </li>
    <li>
      <strong>Do not use <code>grid-auto-flow: dense</code> for a list of links.</strong>
      It reorders items visually to fill holes, leaving the tab order following the
      markup and the eye following the screen. It is fine for a photo wall and wrong for
      anything interactive.
    </li>
    <li>
      <strong>A grid item's minimum size is its content.</strong> A long unbroken string
      — a URL, a hash, a wide code block — makes its track wider than
      <code>1fr</code> and pushes the page sideways. Add <code>min-inline-size: 0</code>
      to the item, which is exactly what <code>.split</code> does for the same reason.
    </li>
    <li>
      <strong>Reflow.</strong> The auto-fit grid satisfies WCAG's reflow criterion for
      free: at 320px or 400% zoom it becomes one column and nothing is lost. The
      <code>.grid-fixed-*</code> variants do not, and are the one part of this page to
      use carefully.
    </li>
    <li>
      <strong>Cards in a grid need their own headings.</strong> The grid supplies no
      structure, so a page of twelve cards with no headings is twelve unlabelled regions.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Grid columns follow the inline direction, so items fill from the right under
    <code>dir="rtl"</code> with no extra rules and no change to the markup order.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="grid grid-tight">' . "\n" .
      '  <div class="card"><div class="card-body"><h3 class="card-title">الأول</h3></div></div>' . "\n" .
      '  <div class="card"><div class="card-body"><h3 class="card-title">الثاني</h3></div></div>' . "\n" .
      '  <div class="card"><div class="card-body"><h3 class="card-title">الثالث</h3></div></div>' . "\n" .
      '</div>',
      'First in the markup is first from the right',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing animates. Reflowing as the container changes width is layout, not a
    transition, so <code>prefers-reduced-motion</code> has no bearing on it.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> sets <code>.grid { display: block }</code> and gives
    every child after the first <code>margin-block-start: 4mm</code>. Grid tracks and
    paged media do not get on — an item taller than the remaining page cannot break out
    of its track — so the grid is unwound into a plain block flow and the spacing is
    restored with a margin. It is the one place in Deck where a margin is the right
    answer.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One grid, no layer needed */
<div class="grid" style="--min:9rem;--gap:var(--space-6)">

@layer app.components {
  /* Keep empty tracks — right for a calendar, wrong for cards */
  .grid-calendar {
    grid-template-columns: repeat(auto-fill, minmax(min(6rem, 100%), 1fr));
  }

  /* A first item that spans the full width */
  .grid > .span-all { grid-column: 1 / -1; }
}') ?></code></pre>
  <p>
    <code>grid-column: 1 / -1</code> on a child is the escape hatch worth remembering: it
    spans every track whatever the current column count turns out to be, so a full-width
    header inside an auto-fit grid needs no breakpoint either.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for content and a sidebar.</strong> That is <code>.split</code>, which
      has one flexible column and one fixed rail. A grid gives every column an equal
      share, which is not what a sidebar is.
    </li>
    <li>
      <strong>Not for a row of buttons.</strong> Use <code>.cluster</code>. A grid would
      give each button an equal track and stretch them all to the widest one.
    </li>
    <li>
      <strong>Not for tabular data.</strong> Use <code>.table</code>. A grid loses the
      header association and the row/column semantics that make a table readable to a
      screen reader.
    </li>
    <li>
      <strong>Not when the item count is part of the design and small.</strong> Two
      things side by side that must stay side by side is
      <code>.grid-fixed-2</code>, or a <code>.bar</code> if they are not equal.
    </li>
    <li>
      <strong><code>.grid-fixed-*</code> is not responsive.</strong> Four fixed columns
      at 320px is four 70px columns. If you find yourself adding a media query to undo a
      fixed grid, you wanted the auto-fit one with a <code>--min</code>.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
