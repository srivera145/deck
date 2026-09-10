<?php
declare(strict_types=1);

$page = [
    'path' => 'components/datagrid.php',
    'title' => 'Data grid',
    'level' => 'Advanced',
    'description' => 'Deck\'s .dg is a real table with a frozen header, pinned columns whose shadows come from a scroll-state container query, sorting, selection, column resizing and a card mode for phones.',
    'documents' => [
        'dg', 'dg-actions', 'dg-cards', 'dg-cards-wrap', 'dg-center',
        'dg-check', 'dg-comfy', 'dg-compact', 'dg-cq', 'dg-empty',
        'dg-num', 'dg-pin-end', 'dg-pin-start', 'dg-pin-start-2', 'dg-resize',
        'dg-selection', 'dg-sort', 'dg-sort-icon', 'dg-statusbar', 'dg-tight',
        'dg-toolbar', 'dg-virtual', 'dg-wrap', 'dg-wrap-text', 'dg-zebra',
        'is-loading', 'is-new', 'is-resizing', 'is-scrolled-end', 'is-scrolled-x',
    ],

    'component' => 'dg',
    'accounts' => [
        '12-datagrid.css'  => 'documented: the wrapper, the frozen header, pinned columns, rows, density, sorting, resizing, selection, loading and the card mode',
        '99-print.css'     => 'documented: the grid becomes a plain table, repeats its header per page and unwinds the card mode — the Printing section',
        '18-container.css' => 'documented: .dg-cq switches to cards on the container\'s width rather than the viewport\'s — the Cards on a phone section',
        '26-perf.css'      => 'documented: .dg-virtual skips offscreen rows, with a per-density intrinsic height — the Long grids section',
        '02-reset.css'     => 'internal: the global reduced-motion reset, which keeps the loading bar moving rather than freezing it',
        '19-logical.css'   => 'documented: the pin shadow geometry is mirrored once for RTL — the Right to left section',
    ],
];

require __DIR__ . '/../_layout.php';

/* One grid, reused so the examples differ only by the class under discussion. */
function demo_grid(string $wrapClasses = 'dg-wrap', string $gridClasses = 'dg', bool $labels = false): string
{
    $rows = [
        ['INV-2291', 'Ada Chen', 'Paid', '1,240.00'],
        ['INV-2292', 'Marco Silva', 'Pending', '86.50'],
        ['INV-2293', 'Priya Raman', 'Overdue', '12,900.00'],
    ];
    $l = static fn(string $n): string => $labels ? ' data-label="' . $n . '"' : '';
    $out = '<div class="' . $wrapClasses . '">' . "\n" . '  <table class="' . $gridClasses . '">' . "\n"
        . '    <thead><tr>' . "\n"
        . '      <th scope="col">Invoice</th>' . "\n"
        . '      <th scope="col">Client</th>' . "\n"
        . '      <th scope="col">Status</th>' . "\n"
        . '      <th scope="col" class="dg-num">Amount</th>' . "\n"
        . '    </tr></thead>' . "\n" . '    <tbody>' . "\n";
    foreach ($rows as [$id, $who, $status, $amt]) {
        $out .= '      <tr>' . "\n"
            . '        <th scope="row"' . $l('Invoice') . '>' . $id . '</th>' . "\n"
            . '        <td' . $l('Client') . '>' . $who . '</td>' . "\n"
            . '        <td' . $l('Status') . '>' . $status . '</td>' . "\n"
            . '        <td class="dg-num"' . $l('Amount') . '>' . $amt . '</td>' . "\n"
            . '      </tr>' . "\n";
    }
    return $out . '    </tbody>' . "\n" . '  </table>' . "\n" . '</div>';
}
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Data grid</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Data grid</h1>
  <p class="lede">
    <code>.dg</code> is a real <code>&lt;table&gt;</code> with a frozen header, columns
    that can be pinned to either edge, sorting, selection and column resizing. It is the
    heaviest component in Deck — 106 rules across six files — and almost all of that
    weight is in making a scrolling table behave.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it when the reader <em>operates</em> on data rather than reading it: sorting,
    selecting rows, scanning a hundred records for one. For data you present rather than
    manipulate, <a href="table.php"><code>.table</code></a> is lighter and does not need
    JavaScript.
  </p>
  <p>
    <code>.dg-wrap</code> is not optional — it carries the scroll container, the height
    cap, the border and the custom properties every other rule reads. The
    <code>&lt;table&gt;</code> goes inside it.
  </p>
  <?php docs_example(demo_grid(), 'The wrapper scrolls; the header stays', 'stack'); ?>
</section>

<section class="stack-6">
  <h2 id="pinning">Frozen header and pinned columns</h2>
  <p>
    The header is <code>position: sticky</code> with
    <code>inset-block-start: 0</code>. Pinned columns are sticky on the inline axis, and
    the two combine: a pinned header cell needs a higher <code>z-index</code> than a
    pinned body cell, which is why the source has four <code>z-index</code> rules rather
    than one.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Pinning classes</caption>
      <thead><tr><th scope="col">Class</th><th scope="col">Pins to</th></tr></thead>
      <tbody>
        <tr><th scope="row" data-label="Class"><code>.dg-pin-start</code></th><td data-label="Pins to">The starting edge</td></tr>
        <tr><th scope="row" data-label="Class"><code>.dg-pin-start-2</code></th><td data-label="Pins to">Beside it, offset by <code>--dg-pin-start</code></td></tr>
        <tr><th scope="row" data-label="Class"><code>.dg-pin-end</code></th><td data-label="Pins to">The ending edge</td></tr>
      </tbody>
    </table>
  </div>
  <p>
    Put the class on <strong>every cell in the column</strong>, header included. A second
    pinned column needs to know how wide the first one is, and that is
    <code>--dg-pin-start</code> — measured by <code>deck.js</code> with a
    <code>ResizeObserver</code>, so it stays correct when the column is resized or the
    font changes.
  </p>

  <div class="stack-2">
    <h3 id="p-shadow">The shadow that appears only when it should</h3>
    <p>
      A pinned column should cast a shadow when content has scrolled underneath it, and
      none when the grid is at rest. Traditionally that is a scroll listener toggling a
      class. Deck asks the scroll container directly:
    </p>
    <pre class="dx-code"><code><?= e('@supports (container-type: scroll-state) {
  .dg-wrap { container-type: scroll-state; }
  @container scroll-state(scrollable: inline-start) {
    .dg :is(th, td).dg-pin-start:last-of-type { box-shadow: var(--dg-pin-shadow-start); }
  }
}') ?></code></pre>
    <p>
      No handler, no class to keep in sync, and the query is already logical so RTL needs
      nothing. Where the feature is missing, <code>Grid.shadows()</code> in
      <code>deck.js</code> toggles <code>.is-scrolled-x</code> and
      <code>.is-scrolled-end</code> and a parallel
      <code>@supports not</code> block styles them — the same appearance from two
      mechanisms, with the fallback costing nothing where it is not needed.
    </p>
    <p class="text-muted">
      The shadow geometry lives in four custom properties on <code>.dg-wrap</code> rather
      than in the rules, so <code>src/19-logical.css</code> mirrors it once for RTL
      instead of once per branch. That is the reason for the indirection.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="sorting">Sorting</h2>
  <p>
    Put <code>data-sort="num"</code>, <code>"text"</code> or <code>"date"</code> on a
    header cell and <code>data-deck-grid</code> on the wrapper.
    <code>deck.js</code> replaces the header's text with a real
    <code>&lt;button class="dg-sort"&gt;</code> and sorts on click, setting
    <code>aria-sort</code> on the <code>&lt;th&gt;</code>.
  </p>
  <p>
    The styling keys off that attribute — <code>th[aria-sort] .dg-sort { color:
    var(--brand) }</code> and a <code>rotate: 180deg</code> for descending — so the arrow
    direction and the announced sort state are the same value. There is no separate
    <code>.is-sorted</code> class to fall out of step.
  </p>
  <pre class="dx-code"><code><?= e('<div class="dg-wrap" data-deck-grid>
  <table class="dg">
    <thead><tr>
      <th scope="col" data-sort="text">Client</th>
      <th scope="col" class="dg-num" data-sort="num">Amount</th>
    </tr></thead>
    …
  </table>
</div>') ?></code></pre>
  <p class="dx-note text-muted">
    Sorting is client-side and re-orders the rows in the DOM. For a grid backed by a
    paged API that is wrong — it sorts the page rather than the data — so leave
    <code>data-sort</code> off and sort on the server, keeping
    <code>aria-sort</code> on the header yourself so the styling still works.
  </p>
</section>

<section class="stack-3">
  <h2 id="density">Density</h2>
  <p>
    <code>--dg-row-h</code> drives the row height, and two classes set it:
    <code>.dg-compact</code> at 34px with smaller text, <code>.dg-comfy</code> at 56px.
    The default is 44px, which is the touch target.
  </p>
  <?php docs_example(demo_grid('dg-wrap', 'dg dg-compact'), '.dg-compact — 34px rows', 'stack'); ?>
  <p class="text-muted">
    Cell variants are independent of density: <code>.dg-num</code> aligns to the end edge,
    <code>.dg-center</code> centres, <code>.dg-tight</code> narrows the padding,
    <code>.dg-wrap-text</code> allows wrapping with a 16rem floor, and
    <code>.dg-actions</code> shrinks a column to its content. Everything else is
    <code>white-space: nowrap</code>, which is what keeps columns aligned while the grid
    scrolls.
  </p>
</section>

<section class="stack-3">
  <h2 id="selection">Selection</h2>
  <p>
    Row selection is <code>tr[aria-selected="true"]</code> — again the accessible state
    doing the styling. <code>.dg-check</code> is the 44px checkbox column, and it is the
    one place Deck deliberately strips <code>.check</code>'s
    <code>min-block-size</code>: a dense grid of 34px rows cannot also have 44px
    checkboxes.
  </p>
  <p>
    <code>.dg-selection</code> is the bar that appears when rows are selected, and
    <code>.dg-statusbar</code> the row-count footer.
  </p>
  <?php
  docs_example(
      '<div class="dg-wrap">' . "\n" .
      '  <table class="dg">' . "\n" .
      '    <thead><tr>' . "\n" .
      '      <th scope="col" class="dg-check"><label class="check"><input type="checkbox"><span class="sr-only">Select all</span></label></th>' . "\n" .
      '      <th scope="col">Invoice</th>' . "\n" .
      '      <th scope="col" class="dg-num">Amount</th>' . "\n" .
      '    </tr></thead>' . "\n" .
      '    <tbody>' . "\n" .
      '      <tr aria-selected="true">' . "\n" .
      '        <td class="dg-check"><label class="check"><input type="checkbox" checked><span class="sr-only">Select INV-2291</span></label></td>' . "\n" .
      '        <th scope="row">INV-2291</th><td class="dg-num">1,240.00</td>' . "\n" .
      '      </tr>' . "\n" .
      '      <tr>' . "\n" .
      '        <td class="dg-check"><label class="check"><input type="checkbox"><span class="sr-only">Select INV-2292</span></label></td>' . "\n" .
      '        <th scope="row">INV-2292</th><td class="dg-num">86.50</td>' . "\n" .
      '      </tr>' . "\n" .
      '    </tbody>' . "\n" .
      '  </table>' . "\n" .
      '</div>',
      'The selected row is styled from aria-selected',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="cards">Cards on a phone</h2>
  <p>
    <code>.dg-cards</code> restacks each row into a labelled card below a breakpoint,
    reading the column name from <code>data-label</code> on each cell — the same
    technique as <a href="table.php#stack"><code>.table-stack</code></a>, and it carries
    the same cost: the columns stop being comparable.
  </p>
  <p>
    <code>.dg-cq</code> does it on the <em>container's</em> width instead, which is the
    version to prefer. A grid in a <a href="split.php">split rail</a> on a wide monitor is
    in a narrow space, and a viewport media query gets that wrong.
  </p>
  <?php docs_example(demo_grid('dg-wrap dg-cards-wrap', 'dg dg-cards', true), 'Narrow the window to see it restack', 'stack'); ?>
</section>

<section class="stack-3">
  <h2 id="long">Long grids</h2>
  <p>
    <code>.dg-virtual</code> puts <code>content-visibility: auto</code> on every row, with
    <code>contain-intrinsic-size</code> matched per density — 34px for
    <code>.dg-compact</code>, 56px for <code>.dg-comfy</code>. The browser skips layout
    and paint for offscreen rows, and the scrollbar stays honest because the placeholder
    height is right.
  </p>
  <p class="text-muted">
    That per-density pairing is the detail <a href="list.php#long"><code>.list-virtual</code></a>
    does not have: the list assumes 56px whatever its rows actually are. Here the two
    density classes each declare their own intrinsic size, so the estimate is never
    wrong by design.
  </p>
</section>

<section class="stack-3">
  <h2 id="states">Loading, empty and new</h2>
  <p>
    <code>.is-loading</code> on the wrapper dims the body, disables pointer events and
    runs an indeterminate bar across the top. <code>.dg-empty</code> is the row that holds
    an empty state. <code>.is-new</code> flashes a row green for 1.4 seconds when it
    arrives — useful for a live-updating grid, and easy to overuse.
  </p>
  <p class="dx-note text-muted">
    All five <code>is-</code> states here are set by <code>deck.js</code> at runtime and
    are <strong>internal</strong>. <code>.is-scrolled-x</code> and
    <code>.is-scrolled-end</code> only exist at all as a fallback for browsers without
    scroll-state queries.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from the stylesheet source by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    Four local properties on <code>.dg-wrap</code> shape the grid:
    <code>--dg-row-h</code>, <code>--dg-height</code>,
    <code>--dg-pin-start</code> and <code>--dg-pin-end</code>. The rest are global.
  </p>
  <?php docs_token_table(['--surface', '--surface-2', '--surface-hover', '--line', '--line-strong', '--brand', '--brand-soft', '--brand-soft-text', '--brand-200', '--brand-500', '--good-100', '--text-sm', '--text-xs', '--text-muted', '--r-md', '--r-sm', '--space-2', '--space-3', '--space-4', '--hue-neutral']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>It is a real table, so the semantics are free.</strong> Header association,
      row and column navigation, and the row count all come from the element. Every
      header still needs <code>scope</code>, and the first cell of each row should be a
      <code>&lt;th scope="row"&gt;</code>.
    </li>
    <li>
      <strong>Sorting is announced through <code>aria-sort</code></strong>, which is also
      what styles the arrow. The sort control is a real <code>&lt;button&gt;</code>
      inside the header, generated by <code>deck.js</code> — so it is focusable and
      operable without a mouse.
    </li>
    <li>
      <strong>Selection is announced through <code>aria-selected</code></strong> on the
      row. The checkbox still needs its own accessible name — the examples here use
      <code>.sr-only</code> text, because a bare checkbox in a grid is announced as
      "checkbox" with no indication of which row.
    </li>
    <li>
      <strong>The checkbox column is under the touch target.</strong>
      <code>.dg-check .check</code> drops <code>min-block-size</code>, so the target is
      the row height — 34px in compact mode. This is a deliberate density trade and one of
      four such places in Deck.
    </li>
    <li>
      <strong>The scroll container needs a tab stop.</strong> <code>.dg-wrap</code> is
      <code>overflow: auto</code> and sets no <code>tabindex</code>, so a keyboard user
      cannot scroll it horizontally unless a cell in the off-screen region is focusable.
      Add <code>tabindex="0"</code> and <code>role="region"</code> with a label. Same
      defect as <a href="table.php#accessibility"><code>.table-wrap</code></a>.
    </li>
    <li>
      <strong>Column resizing is pointer-only.</strong> <code>.dg-resize</code> is a drag
      handle with no keyboard equivalent. Column widths are a preference rather than
      content, so nothing is unreachable — but a keyboard user cannot adjust them.
    </li>
    <li>
      <strong>The loading bar is decoration.</strong> <code>.is-loading</code> dims the
      body and animates a bar; nothing is announced. Put the state in an
      <code>aria-live</code> region, or the grid silently goes quiet for a screen-reader
      user.
    </li>
    <li>
      <strong><code>.is-new</code> is colour-only</strong> and lasts 1.4 seconds. A row
      arriving is not announced either. For a live grid, an
      <code>aria-live="polite"</code> summary — "3 new rows" — carries far more than the
      flash does.
    </li>
    <li>
      <strong>Card mode keeps the header in the tree.</strong> Like
      <code>.table-stack</code>, the <code>&lt;thead&gt;</code> is clipped rather than
      removed, so header association survives the restack.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Pinning uses <code>inset-inline-start</code> and <code>inset-inline-end</code>, cells
    use <code>text-align: start</code>, and the scroll-state queries use
    <code>scrollable: inline-start</code> — all logical, all mirrored for free.
  </p>
  <p>
    Two things are not. A <code>box-shadow</code> offset is physical, and so is a
    <code>clip-path</code> inset; both live in custom properties on
    <code>.dg-wrap</code> so <code>src/19-logical.css</code> can mirror the geometry in
    one place rather than in each of the four branches that use it.
  </p>
  <p class="text-muted">
    <code>Grid.shadows()</code> also takes <code>Math.abs(scrollLeft)</code>, because an
    RTL scroller counts down from zero. That single <code>abs</code> is what makes the
    JavaScript fallback work in both directions.
  </p>
  <?php docs_example('<div dir="rtl">' . "\n" . demo_grid() . "\n" . '</div>', 'Columns fill from the right; the numeric column aligns to the left', 'stack'); ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Two animations: the <code>.is-new</code> row flash and the <code>.is-loading</code>
    bar. Under <code>prefers-reduced-motion: reduce</code> the global reset collapses
    animations to a single <code>.01ms</code> iteration — but the loading bar is in the
    reset's exemption list alongside spinners and skeletons, so it keeps moving at 2.4
    seconds rather than freezing.
  </p>
  <p class="text-muted">
    That is the right call: a frozen progress indicator reads as a hung page, which is
    worse for everyone than a slow one.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Sixteen rules, the most of any component. The grid becomes a plain table:
    <code>display: table-header-group</code> on the <code>&lt;thead&gt;</code> so the
    header repeats on every page, <code>break-inside: avoid</code> on rows,
    <code>font-size: 9.5pt</code>, and the card mode fully unwound —
    <code>display: table</code>, <code>table-row</code>, <code>table-cell</code>, with
    the <code>::before</code> labels set to <code>content: none</code>.
  </p>
  <p>
    <code>.dg-toolbar</code> and <code>.dg-resize</code> are in the never-print list, and
    the header keeps its grey fill through <code>print-color-adjust: exact</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One grid, no layer needed */
<div class="dg-wrap" style="--dg-height:40dvh;--dg-row-h:38px">

@layer app.components {
  /* A third pinned column */
  .dg :is(th, td).dg-pin-start-3 {
    position: sticky;
    inset-inline-start: calc(var(--dg-pin-start) + var(--dg-pin-start-2, 0px));
    z-index: 2;
  }

  /* A focus background for keyboard users */
  .dg tbody tr:focus-within td { background: var(--surface-hover); }
}') ?></code></pre>
  <p class="text-muted">
    A third pinned column needs its own offset property measured the way
    <code>--dg-pin-start</code> is; Deck ships two because two is where the pattern stops
    being worth the complexity.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for data you only present.</strong> Use
      <a href="table.php"><code>.table</code></a>. The grid brings a scroll container, a
      height cap, six files of CSS and a JavaScript class for behaviour you are not using.
    </li>
    <li>
      <strong>Not for a list of one-line items.</strong> Use
      <a href="list.php"><code>.list</code></a>, which has row semantics and touch targets
      without the column machinery.
    </li>
    <li>
      <strong>Not with client-side sorting over a paged API.</strong> It sorts the visible
      page and looks like it sorted the data. Sort on the server and set
      <code>aria-sort</code> yourself.
    </li>
    <li>
      <strong>Not with more than two pinned columns.</strong> Each one needs a measured
      offset, and past two the pinned region is most of a phone's screen.
    </li>
    <li>
      <strong>Not as a spreadsheet.</strong> There is no cell editing, no keyboard grid
      navigation and no formula layer. <code>role="grid"</code> would promise all three;
      Deck's is a table, and it is announced as one.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
