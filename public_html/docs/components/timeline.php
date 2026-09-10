<?php
declare(strict_types=1);

$page = [
    'path' => 'components/timeline.php',
    'title' => 'Timeline',
    'level' => 'Beginner',
    'description' => 'Deck\'s .timeline is a vertical list of events joined by a line, with a dot per entry. .is-done fills the dot in the brand colour — which is colour alone, so the state needs words too.',
    'documents' => [
        'timeline', 'timeline-dot', 'timeline-item', 'is-done',
    ],

    'component' => 'timeline',
    'accounts' => [
        '07-components.css' => 'documented: the column, the two-track item grid, the connector line drawn on every item but the last, the dot and its completed state',
        '99-print.css'      => 'documented: .timeline-item is given break-inside: avoid so an event is not split across pages — see Printing',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Timeline</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Timeline</h1>
  <p class="lede">
    A vertical run of events, each with a dot, joined by a line. It suits anything with a
    real order and a real time: an audit trail, a delivery's progress, the history of a
    ticket.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    When the sequence is the point. A timeline says "this, then this, then this" more
    directly than a <a href="list.php">list</a>, and it does it in a shape people already
    read as chronological.
  </p>
  <p>
    When the sequence is a process the reader is currently moving through — checkout, an
    onboarding wizard — that is a <a href="stepper.php">stepper</a>, which is horizontal,
    numbered, and about what happens next rather than what already happened.
  </p>
  <?php
  docs_example(
      '<ol class="timeline">' . "\n" .
      '  <li class="timeline-item is-done">' . "\n" .
      '    <span class="timeline-dot">' . "\n" .
      '      <svg class="icon icon-sm" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '    <div class="stack-1">' . "\n" .
      '      <strong>Order placed</strong>' . "\n" .
      '      <p class="text-muted"><time datetime="2026-09-04T09:12">4 September, 09:12</time> — payment authorised</p>' . "\n" .
      '    </div>' . "\n" .
      '  </li>' . "\n" .
      '  <li class="timeline-item is-done">' . "\n" .
      '    <span class="timeline-dot">' . "\n" .
      '      <svg class="icon icon-sm" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '    <div class="stack-1">' . "\n" .
      '      <strong>Dispatched</strong>' . "\n" .
      '      <p class="text-muted"><time datetime="2026-09-05T16:40">5 September, 16:40</time> — left the Bristol depot</p>' . "\n" .
      '    </div>' . "\n" .
      '  </li>' . "\n" .
      '  <li class="timeline-item">' . "\n" .
      '    <span class="timeline-dot"></span>' . "\n" .
      '    <div class="stack-1">' . "\n" .
      '      <strong>Out for delivery</strong>' . "\n" .
      '      <p class="text-muted">Expected <time datetime="2026-09-06">6 September</time></p>' . "\n" .
      '    </div>' . "\n" .
      '  </li>' . "\n" .
      '</ol>',
      'An ordered list, because the order is the meaning',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>&lt;ol&gt;</code> with a class already has its markers and padding removed by
    <code>src/02-reset.css</code>, so no list reset is needed. The
    <code>&lt;time datetime&gt;</code> is not Deck's doing and is worth the keystrokes —
    it is what makes the date machine-readable.
  </p>
</section>

<section class="stack-3">
  <h2 id="structure">How it is drawn</h2>
  <p>
    <code>.timeline-item</code> is a two-column grid: a fixed 22px track for the dot and
    everything else for the content. That fixed track is what keeps the dots in a straight
    line however tall or short the entries are.
  </p>
  <p>
    The connector is a pseudo-element on the item, not a border on the column:
  </p>
  <pre class="dx-code"><code><?= e('.timeline-item:not(:last-child)::before {
  content: "";
  position: absolute;
  inset-block: 22px 0;
  inset-inline-start: 10px;
  inline-size: 2px;
  background: var(--line);
}') ?></code></pre>
  <p>
    Two things follow. It starts at <code>22px</code> — below the dot — so the line runs
    between dots rather than through them. And <code>:not(:last-child)</code> means the
    final entry has no trailing line, which is what stops the timeline looking like it
    continues past the last thing that happened.
  </p>
  <p class="dx-note text-muted">
    Both numbers are hard-coded: the 22px track, the 22px offset and the 10px inset are
    written out rather than derived from a custom property. Resizing the dot means changing
    four values in three rules, so it is not a one-line override — see
    <a href="#overriding">Overriding it</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="done">Completed entries</h2>
  <p>
    <code>.is-done</code> on the <em>item</em> — not the dot — fills its dot with the brand
    colour and sets the icon inside it to the on-brand text colour. Everything else about
    the entry is unchanged.
  </p>
  <?php
  docs_example(
      '<ol class="timeline">' . "\n" .
      '  <li class="timeline-item is-done">' . "\n" .
      '    <span class="timeline-dot">' . "\n" .
      '      <svg class="icon icon-sm" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '    <div><strong>Done</strong> <span class="badge badge-good">Complete</span></div>' . "\n" .
      '  </li>' . "\n" .
      '  <li class="timeline-item">' . "\n" .
      '    <span class="timeline-dot"></span>' . "\n" .
      '    <div><strong>Not done</strong> <span class="badge">Pending</span></div>' . "\n" .
      '  </li>' . "\n" .
      '</ol>',
      'The badge is doing the work the fill colour cannot',
      'stack'
  );
  ?>
  <p class="text-muted">
    The dot is a plain <code>&lt;span&gt;</code> with a background — it says nothing. Note
    what the example does: the state is also written as a
    <a href="badge.php">badge</a>, because that is the version a screen reader and a
    monochrome printer can both read.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/07-components.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--line', '--line-strong', '--surface', '--brand-600', '--text-on-brand', '--r-full', '--space-3', '--space-5']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Use an <code>&lt;ol&gt;</code>.</strong> The order is the information. An
      ordered list is announced with its position — "3 of 5" — which is exactly what the
      shape is conveying visually.
    </li>
    <li>
      <strong>The dot and the line are decoration.</strong> One is an empty element with a
      background, the other a pseudo-element. Neither is announced, and neither should be:
      the content of the entry has to stand on its own when read aloud.
    </li>
    <li>
      <strong><code>.is-done</code> is colour alone.</strong> A filled dot and an empty dot
      differ only in fill. Put the state in the entry — a
      <a href="badge.php">badge</a>, or simply the word — as the example does.
    </li>
    <li>
      <strong>An icon inside the dot needs hiding.</strong> A tick in a
      <code>.timeline-dot</code> is repeating what the text should already say. Give it
      <code>aria-hidden="true"</code>, or it announces alongside the entry as a second,
      contentless item.
    </li>
    <li>
      <strong>Give each entry a real date.</strong> <code>&lt;time datetime&gt;</code>
      rather than "2 days ago" alone. Relative times are friendly and ambiguous, and they go
      stale on a printed or cached page.
    </li>
    <li>
      <strong>A long timeline is a long list.</strong> Twenty entries is twenty things to
      pass through. Put a heading above it so it can be skipped, and consider paginating or
      collapsing older entries.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The dot track moves to the right and the connector follows, because the item is a grid
    and the line is placed with <code>inset-inline-start</code>. No rule in
    <code>src/19-logical.css</code> is involved — the component was written with logical
    properties from the start, so it mirrors on its own.
  </p>
  <?php
  docs_example(
      '<ol dir="rtl" class="timeline">' . "\n" .
      '  <li class="timeline-item is-done">' . "\n" .
      '    <span class="timeline-dot">' . "\n" .
      '      <svg class="icon icon-sm" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '    <div><strong>تم استلام الطلب</strong></div>' . "\n" .
      '  </li>' . "\n" .
      '  <li class="timeline-item">' . "\n" .
      '    <span class="timeline-dot"></span>' . "\n" .
      '    <div><strong>قيد التوصيل</strong></div>' . "\n" .
      '  </li>' . "\n" .
      '</ol>',
      'Dots on the right, content flowing left',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing on the timeline animates — no transitions, no keyframes, no transforms. There is
    nothing for <code>prefers-reduced-motion</code> to act on, and no guard is needed.
  </p>
  <p class="text-muted">
    If you animate entries appearing as they load, that motion is yours and needs its own
    guard.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.timeline-item</code> is one of the few things <code>src/99-print.css</code>
    protects with <code>break-inside: avoid</code>, alongside paragraphs, list items and
    <code>.stat</code>. An event will not be split across a page boundary.
  </p>
  <p>
    The dots and the connector are backgrounds, and are not in the list of elements given
    <code>print-color-adjust: exact</code> — so both are dropped and the timeline prints as
    an indented list of events with a gap on the left where the track was. The content
    survives; the shape does not, and neither does the completed state.
  </p>
  <p class="text-muted">
    Which is the same argument as everywhere else on this page: if
    <code>.is-done</code> matters, it has to be a word.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A larger dot. Four values have to move together. */
  .timeline-item { grid-template-columns: 32px 1fr; }
  .timeline-dot { inline-size: 32px; block-size: 32px; }
  .timeline-item:not(:last-child)::before {
    inset-block-start: 32px;   /* clear the dot */
    inset-inline-start: 15px;  /* half the dot, minus half the 2px line */
  }

  /* A dashed connector for a projected future */
  .timeline-item.is-planned::before {
    background: repeating-linear-gradient(
      to bottom, var(--line) 0 4px, transparent 4px 8px);
  }
}') ?></code></pre>
  <p class="text-muted">
    The line's <code>inset-inline-start</code> is half the dot width minus half the line
    width. Get it wrong by a pixel and the connector visibly misses the dot, which is the
    main hazard in resizing this component.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a process in progress.</strong> Checkout, onboarding and any "you are
      here" sequence is a <a href="stepper.php">stepper</a>.
    </li>
    <li>
      <strong>Not for unordered items.</strong> If the order does not matter, the line is
      claiming something untrue. Use a <a href="list.php">list</a>.
    </li>
    <li>
      <strong>Not for data you would rather compare.</strong> Events with durations and
      overlaps are a chart, not a column. Deck's <a href="charts.php">charts</a> are the
      nearer tool.
    </li>
    <li>
      <strong>Not for hundreds of entries.</strong> The connector implies you can see the
      whole sequence. Past a screenful or two that stops being true and the shape stops
      paying for itself.
    </li>
    <li>
      <strong>Not with <code>.is-done</code> as the only signal.</strong> It is a colour,
      and it is gone on paper.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
