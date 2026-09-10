<?php
declare(strict_types=1);

$page = [
    'path' => 'components/kanban.php',
    'title' => 'Kanban',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .kanban is a horizontal scroller of sunken columns, each with its own scrolling body. Drag-and-drop is styled but not implemented, and there is no keyboard path — the honest state of the component.',
    'documents' => [
        'kanban', 'kanban-body', 'kanban-card', 'kanban-card-meta', 'kanban-card-title',
        'kanban-col', 'kanban-count', 'kanban-empty', 'kanban-head',
    ],

    'component' => 'kanban',
    'accounts' => [
        '26-perf.css' => 'documented: the board, its columns, heads, bodies, cards and the empty state — plus the drag handle it shares with lists and grids',
    ],
];

require __DIR__ . '/../_layout.php';

function demo_card(string $title, string $meta): string
{
    return '      <article class="kanban-card">' . "\n"
        . '        <h4 class="kanban-card-title">' . $title . '</h4>' . "\n"
        . '        <div class="kanban-card-meta">' . $meta . '</div>' . "\n"
        . '      </article>' . "\n";
}
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Kanban</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Kanban</h1>
  <p class="lede">
    <code>.kanban</code> is a horizontally scrolling row of columns, each a sunken panel
    with its own scrolling body. It is twelve rules — the board is mostly two nested
    scroll containers, and getting those to behave is the whole component.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it when items move between a small number of named states and seeing the whole
    pipeline at once is the point: a task board, a hiring pipeline, an order queue. If the
    states are not meaningful, a <a href="grid.php"><code>.grid</code></a> or a
    <a href="list.php"><code>.list</code></a> is easier to scan.
  </p>
  <?php
  docs_example(
      '<div class="kanban" style="--kanban-height:22rem">' . "\n" .
      '  <section class="kanban-col" aria-labelledby="dx-k1">' . "\n" .
      '    <div class="kanban-head"><h3 id="dx-k1">To do</h3><span class="kanban-count">2</span></div>' . "\n" .
      '    <div class="kanban-body">' . "\n" .
      demo_card('Rewrite the export job', 'Due Thursday · Ada') .
      demo_card('Add a webhook retry', 'Unassigned') .
      '    </div>' . "\n" .
      '  </section>' . "\n" .
      '  <section class="kanban-col" aria-labelledby="dx-k2">' . "\n" .
      '    <div class="kanban-head"><h3 id="dx-k2">In progress</h3><span class="kanban-count">1</span></div>' . "\n" .
      '    <div class="kanban-body">' . "\n" .
      demo_card('Freeze the public API', 'Due today · Marco') .
      '    </div>' . "\n" .
      '  </section>' . "\n" .
      '  <section class="kanban-col" aria-labelledby="dx-k3">' . "\n" .
      '    <div class="kanban-head"><h3 id="dx-k3">Done</h3><span class="kanban-count">0</span></div>' . "\n" .
      '    <div class="kanban-body">' . "\n" .
      '      <p class="kanban-empty">Nothing here yet</p>' . "\n" .
      '    </div>' . "\n" .
      '  </section>' . "\n" .
      '</div>',
      'Scroll the board sideways; scroll a column on its own',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="scrolling">Two scroll containers</h2>
  <p>
    The board scrolls on the inline axis and each column scrolls on the block axis. Both
    set <code>overscroll-behavior: contain</code>, which is what stops a scroll that
    reaches the end of a column from chaining outward — first to the board, then to the
    page.
  </p>
  <p>
    Columns are <code>flex: 0 0 min(19rem, 82vw)</code>: a fixed width so they do not
    squash, with a viewport cap so a phone still shows a sliver of the next one. The board
    uses <code>scroll-snap-type: x proximity</code>, so it settles on a column when the
    reader stops near one and leaves them alone otherwise — the same reasoning as
    <a href="scroller.php#h-snap"><code>.scroller</code></a>.
  </p>
  <p class="text-muted">
    <code>align-items: flex-start</code> on the board means columns are as tall as their
    content up to <code>--kanban-height</code>, rather than all stretching to match the
    longest. A board where an empty column is as tall as a full one wastes most of the
    screen.
  </p>
</section>

<section class="stack-3">
  <h2 id="parts">Head, body, card</h2>
  <p>
    <code>.kanban-head</code> is pinned outside the scrolling area, so a column's name and
    count stay visible while its cards scroll. <code>.kanban-count</code> is the pill
    beside the title. <code>.kanban-body</code> is the scrolling region, with a
    <code>5rem</code> minimum so an empty column is still a target you can drop onto.
  </p>
  <p>
    <code>.kanban-empty</code> shows in an empty column and is hidden by
    <code>.kanban-col:has(.kanban-card) .kanban-empty</code> as soon as one arrives — a
    parent selector rather than a class that something has to remember to toggle.
  </p>
</section>

<section class="stack-3">
  <h2 id="dragging">Dragging</h2>
  <p>
    <code>src/26-perf.css</code> styles the drag states this board shares with
    <a href="list.php#reorder">lists</a> and the
    <a href="datagrid.php">data grid</a>: a <code>.drag-handle</code> that appears on
    hover or focus-within, and the lifted appearance of a card in flight.
    <code>deck-adapters.js</code> drives them with SortableJS when it is present, or the
    native drag-and-drop API when it is not.
  </p>
  <p class="dx-note text-muted">
    <strong>There is no keyboard path.</strong> A board that can only be reordered by
    dragging cannot be reordered at all by a keyboard-only user, and a kanban board's
    entire purpose is moving cards. This is the most serious accessibility gap in Deck —
    more so here than in a list, because in a list the order is usually cosmetic and here
    it is the data. Recorded in <code>FINDINGS.md</code>.
  </p>
  <p>
    The mitigation is a move control on each card — a <a href="menu.php">menu</a> with
    "Move to In progress" — which is also faster than dragging for anyone using a large
    board.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/26-perf.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    <code>--kanban-height</code> caps a column and defaults to <code>70dvh</code>. It is
    local to the component rather than a global token.
  </p>
  <?php docs_token_table(['--bg-sunken', '--surface', '--line', '--text-sm', '--text-xs', '--text-faint', '--r-md', '--r-sm', '--shadow-2', '--space-1', '--space-3', '--space-4']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Drag-and-drop with no keyboard equivalent is the headline problem.</strong>
      See <a href="#dragging">above</a>. Ship a move control as well; a board without one
      is unusable without a pointer.
    </li>
    <li>
      <strong>Columns should be labelled regions.</strong>
      <code>&lt;section aria-labelledby&gt;</code> pointing at the column's heading, as
      the example does — so a screen-reader user can navigate between columns rather than
      falling through every card.
    </li>
    <li>
      <strong>The count should be in the heading's accessible name.</strong> "To do" and
      a separate "2" are announced as two things. Consider
      <code>&lt;h3 id&gt;To do &lt;span class="sr-only"&gt;— 2 items&lt;/span&gt;&lt;/h3&gt;</code>.
    </li>
    <li>
      <strong>Both scroll containers need keyboard reach.</strong> The board and each
      column are <code>overflow: auto</code> with no <code>tabindex</code>. Cards are
      usually focusable, which drags the container along — but an empty or non-interactive
      column cannot be scrolled from the keyboard. Same defect as
      <a href="table.php#accessibility"><code>.table-wrap</code></a>.
    </li>
    <li>
      <strong>A card needs a real heading or a link.</strong>
      <code>.kanban-card-title</code> is styling only. If a card opens something, make the
      title a link and let the card be the target with
      <a href="card.php#link">the stretched-link pattern</a>.
    </li>
    <li>
      <strong>Moving a card should be announced.</strong> Nothing about the drag reaches a
      screen reader. An <code>aria-live</code> region saying "Moved to In progress" is the
      minimum, whether the move came from a drag or a menu.
    </li>
    <li>
      <strong><code>.drag-handle</code> appears on hover <em>or focus-within</em></strong>,
      which is the right pairing — a handle that only appears on hover is invisible to a
      keyboard user even before the missing key handling.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The board is a flex row that reverses under <code>dir="rtl"</code>, columns use
    <code>scroll-snap-align: start</code> — the starting edge, not the left one — and
    <code>overscroll-behavior-inline</code> is already logical. Nothing needs a rule.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="kanban" style="--kanban-height:16rem">' . "\n" .
      '  <section class="kanban-col" aria-labelledby="dx-kr1">' . "\n" .
      '    <div class="kanban-head"><h3 id="dx-kr1">قيد الانتظار</h3><span class="kanban-count">١</span></div>' . "\n" .
      '    <div class="kanban-body">' . "\n" .
      '      <article class="kanban-card"><h4 class="kanban-card-title">إعادة كتابة مهمة التصدير</h4></article>' . "\n" .
      '    </div>' . "\n" .
      '  </section>' . "\n" .
      '  <section class="kanban-col" aria-labelledby="dx-kr2">' . "\n" .
      '    <div class="kanban-head"><h3 id="dx-kr2">قيد التنفيذ</h3><span class="kanban-count">٠</span></div>' . "\n" .
      '    <div class="kanban-body"><p class="kanban-empty">لا شيء هنا</p></div>' . "\n" .
      '  </section>' . "\n" .
      '</div>',
      'The first column is the rightmost one',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The only transition is a card's shadow on hover, collapsed to <code>.01ms</code> by
    the global reset. Scroll snapping is positional rather than animated and is
    unaffected — a reader who wants less motion still wants the columns to line up.
  </p>
  <p class="text-muted">
    A drag follows the pointer, so it is a response to the reader's own gesture rather
    than motion they did not ask for.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    No print rule. A printed board shows the columns that fit on the page and clips the
    rest, and each column shows only the cards that were scrolled into view — the same
    gap as <a href="scroller.php#print"><code>.scroller</code></a> and
    <a href="carousel.php#print"><code>.carousel</code></a>, and worse here because there
    are two nested scrollers.
  </p>
  <p class="text-muted">
    A board is a working surface rather than a document, so this matters less than the
    <a href="accordion.php#print">accordion's print gap</a> — but a printed board that
    silently omits half the work is still misleading.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One board, no layer needed */
<div class="kanban" style="--kanban-height:60vh">

@layer app.components {
  /* Wider columns */
  .kanban-col { flex-basis: min(24rem, 86vw); }

  /* Columns that fill the height rather than sizing to content */
  .kanban { align-items: stretch; }

  /* A per-column accent */
  .kanban-col[data-state="blocked"] { border-color: var(--bad-500); }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not without a non-drag way to move cards.</strong> The board's purpose is
      moving things, and dragging is the one interaction Deck does not make accessible.
    </li>
    <li>
      <strong>Not for many columns.</strong> Six states means six columns and a lot of
      horizontal scrolling; past four the board stops showing the pipeline at a glance,
      which was the reason to use one.
    </li>
    <li>
      <strong>Not for many cards.</strong> A column with two hundred cards is a scroll
      container inside a scroll container. Filter first, or use a
      <a href="datagrid.php">data grid</a> with a status column.
    </li>
    <li>
      <strong>Not on a phone as the primary view.</strong> One column at a time with the
      rest off-screen is worse than a filtered list. Consider swapping to a
      <a href="list.php"><code>.list</code></a> grouped by status below a breakpoint.
    </li>
    <li>
      <strong>Not when the states are not real.</strong> If items do not actually move
      between the columns, the columns are categories and a
      <a href="grid.php">grid</a> with headings says so more plainly.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
