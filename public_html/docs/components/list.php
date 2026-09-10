<?php
declare(strict_types=1);

$page = [
    'path' => 'components/list.php',
    'title' => 'List',
    'level' => 'Beginner',
    'description' => 'Deck\'s .list and .list-row: a bordered stack of rows that clear the 44px touch target, with a main/title/sub/trail structure, a virtual-scrolling variant, and reorder states.',
    'documents' => [
        'list', 'list-header', 'list-main', 'list-row', 'list-sub',
        'list-title', 'list-trail', 'list-virtual', 'is-lifted',
    ],

    'component' => 'list',
    'accounts' => [
        '07-components.css' => 'documented: the list, its rows, the main/title/sub/trail structure and the group header',
        '26-perf.css'       => 'documented: .list-virtual skips offscreen rows, .is-lifted styles a dragged row, and .list gets layout containment — the Long lists and Reordering sections',
        '99-print.css'      => 'documented: lists keep a hairline border and the group header keeps its fill — the Printing section',
        '16-motion.css'     => 'documented: .icon-follow steps forward on row hover — the Reduced motion section',
        '18-container.css'  => 'documented: .list.cq makes a list a query container — the Responds to its own width section',
        '19-logical.css'    => 'internal: mirrors the hover arrow under dir="rtl" so it moves toward the text end',
    ],
];

require __DIR__ . '/../_layout.php';

$people = [
    ['Ada Chen', 'ada@example.com', 'Owner'],
    ['Marco Silva', 'marco@example.com', 'Editor'],
    ['Priya Raman', 'priya@example.com', 'Viewer'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">List</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>List</h1>
  <p class="lede">
    <code>.list</code> is a bordered, clipped flex column; <code>.list-row</code> is one
    row inside it. The row is the component that matters: a flex line with a
    <?= e(api_token('--space-3')['value'] ?? '.75rem') ?> gap and
    <code>min-block-size: var(--tap)</code>, so every row clears the
    <?= e(api_token('--tap')['value'] ?? '44px') ?> touch target whether or not it holds
    anything tall.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a list when the reader reads one row at a time — people, files, notifications,
    settings. Each row is a self-contained thing with a name and maybe a detail. If the
    reader needs to compare a value down the column, that is a
    <a href="table.php"><code>.table</code></a> instead.
  </p>
  <p>
    Dividers come from <code>.list-row + .list-row</code>, so the first row has no top
    border and the list needs no <code>:last-child</code> exception. Rows in the markup
    are all that is needed; nothing has to be wrapped.
  </p>
  <?php
  docs_example(
      '<div class="list" style="max-inline-size:28rem">' . "\n" .
      '  <div class="list-row">First</div>' . "\n" .
      '  <div class="list-row">Second</div>' . "\n" .
      '  <div class="list-row">Third</div>' . "\n" .
      '</div>',
      'Three rows. The dividers are between them, not around them.',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="structure">Main, title, sub, trail</h2>
  <p>
    A row is usually three parts: something at the start, the text in the middle,
    something at the end.
  </p>
  <ul class="stack-2">
    <li>
      <code>.list-main</code> is <code>flex: 1 1 auto</code> with
      <code>min-inline-size: 0</code>, so it takes the free space <em>and</em> is allowed
      to shrink. That second half is what stops a long email address from pushing the
      trailing badge off the end of the row.
    </li>
    <li>
      <code>.list-title</code> and <code>.list-sub</code> are the two lines inside it,
      separated by a one-pixel gap rather than a margin.
    </li>
    <li>
      <code>.list-trail</code> is <code>flex: 0 0 auto</code> and faint — a timestamp,
      a count, a chevron. It never shrinks.
    </li>
  </ul>
  <?php
  $rows = '';
  foreach ($people as [$name, $email, $role]) {
      $rows .= '  <div class="list-row">' . "\n"
          . '    <span class="avatar avatar-sm">' . substr($name, 0, 1) . substr(strrchr($name, ' ') ?: ' ', 1, 1) . '</span>' . "\n"
          . '    <div class="list-main">' . "\n"
          . '      <span class="list-title">' . $name . '</span>' . "\n"
          . '      <span class="list-sub">' . $email . '</span>' . "\n"
          . '    </div>' . "\n"
          . '    <span class="list-trail">' . $role . '</span>' . "\n"
          . '  </div>' . "\n";
  }
  docs_example(
      '<div class="list" style="max-inline-size:30rem">' . "\n" . $rows . '</div>',
      'Avatar, main, trail — the shape most lists want',
      'stack'
  );
  ?>

  <h3 id="truncate">Why <code>min-inline-size: 0</code> is there</h3>
  <p>
    A flex item's minimum size is its content by default, so a long unbroken string
    refuses to shrink and pushes everything after it out of the row.
    <code>.list-main</code> sets <code>min-inline-size: 0</code> to allow the shrink;
    add <code>.truncate</code> to the line you want clipped.
  </p>
  <?php
  docs_example(
      '<div class="list" style="max-inline-size:22rem">' . "\n" .
      '  <div class="list-row">' . "\n" .
      '    <div class="list-main">' . "\n" .
      '      <span class="list-title truncate">a-very-long-export-filename-2026-03-11.csv</span>' . "\n" .
      '      <span class="list-sub truncate">generated by the nightly job, 04:12 UTC</span>' . "\n" .
      '    </div>' . "\n" .
      '    <span class="list-trail">2.4 MB</span>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The trail stays put; the title clips',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="rows-as-links">Rows that are links</h2>
  <p>
    <code>.list-row</code> sets <code>color: inherit</code> and
    <code>text-decoration: none</code>, so it can be an <code>&lt;a&gt;</code> directly
    with no reset of its own. That is the right way to build a navigable list: one
    element, one tab stop, a real destination, and the whole row as the target.
  </p>
  <?php
  docs_example(
      '<div class="list" style="max-inline-size:28rem">' . "\n" .
      '  <a class="list-row" href="#rows-as-links">' . "\n" .
      '    <div class="list-main"><span class="list-title">Billing</span></div>' . "\n" .
      '    <svg class="icon icon-follow list-trail" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-right"></use></svg>' . "\n" .
      '  </a>' . "\n" .
      '  <a class="list-row" href="#rows-as-links">' . "\n" .
      '    <div class="list-main"><span class="list-title">Members</span></div>' . "\n" .
      '    <svg class="icon icon-follow list-trail" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-right"></use></svg>' . "\n" .
      '  </a>' . "\n" .
      '</div>',
      'Hover a row: the chevron steps forward',
      'stack'
  );
  ?>
  <p class="text-muted">
    The chevron moves because <code>src/16-motion.css</code> translates
    <code>.icon-follow</code> by three pixels on <code>.list-row:hover</code>. It is
    declared inside <code>@media (prefers-reduced-motion: no-preference)</code>, so a
    reader who has asked for less motion never gets the rule at all.
  </p>
</section>

<section class="stack-3">
  <h2 id="header">Group headers</h2>
  <p>
    <code>.list-header</code> is a small uppercase band on
    <code>--surface-2</code>, for breaking a long list into sections. It is a row in the
    same flex column, so it needs no wrapper and it participates in the same dividers.
  </p>
  <?php
  docs_example(
      '<div class="list" style="max-inline-size:28rem">' . "\n" .
      '  <div class="list-header">Today</div>' . "\n" .
      '  <div class="list-row"><div class="list-main"><span class="list-title">Export finished</span></div><span class="list-trail">09:12</span></div>' . "\n" .
      '  <div class="list-row"><div class="list-main"><span class="list-title">Invoice paid</span></div><span class="list-trail">08:40</span></div>' . "\n" .
      '  <div class="list-header">Yesterday</div>' . "\n" .
      '  <div class="list-row"><div class="list-main"><span class="list-title">Member invited</span></div><span class="list-trail">17:03</span></div>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <code>.list-header</code> is styling, not structure. A screen reader hears the word
    "Today" as ordinary text with no indication that it heads the rows beneath. If the
    grouping is meaningful, use a real heading inside it or split into separate lists
    with headings between them.
  </p>
</section>

<section class="stack-3">
  <h2 id="cq">Responds to its own width</h2>
  <p>
    <code>.list.cq</code> makes a list a query container, so rows inside it can respond
    to the list's own width rather than the viewport's. That matters because a list is
    the component most likely to appear in a narrow sidebar <em>and</em> a wide main
    column on the same page — a viewport breakpoint gets one of those two wrong.
  </p>
  <?php
  docs_example(
      '<div class="list cq" style="resize:horizontal;overflow:auto;min-inline-size:14rem;inline-size:24rem;max-inline-size:100%">' . "\n" .
      '  <div class="list-row">' . "\n" .
      '    <div class="list-main">' . "\n" .
      '      <span class="list-title text-cq">Scales with the list</span>' . "\n" .
      '      <span class="list-sub">Not with the window</span>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Resize the list. The window stays where it is.',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="long">Long lists</h2>
  <p>
    <code>.list-virtual</code> puts <code>content-visibility: auto</code> and
    <code>contain-intrinsic-size: auto 56px</code> on every row. The browser skips
    layout, style and paint for rows that are offscreen, and uses 56px as the placeholder
    height so the scrollbar stays honest. It is one class, no JavaScript, and no
    windowing library.
  </p>
  <p>
    The 56px is a guess about your rows. If yours are taller the scrollbar will jump
    slightly as rows are rendered; override
    <code>contain-intrinsic-size</code> in <code>app.components</code> with your real
    height.
  </p>
  <pre class="dx-code"><code><?= e('<div class="list list-virtual">
  <!-- two thousand .list-row children -->
</div>') ?></code></pre>
  <p class="text-muted">
    <code>src/26-perf.css</code> disables it for print — offscreen rows have to be
    rendered when the whole list goes on paper. <code>.list</code> also gets
    <code>contain: layout style</code> there, so one list relaying out cannot reflow the
    rest of the page.
  </p>
</section>

<section class="stack-3">
  <h2 id="reorder">Reordering</h2>
  <p>
    <code>.reorder</code> on the list makes rows unselectable, and
    <code>.is-lifted</code> is what a row looks like while it is being dragged: a deep
    shadow, an opaque background and a radius, so it reads as lifted off the stack.
    Deck styles the states; <code>deck-adapters.js</code> drives them with SortableJS
    when it is present and the native drag-and-drop API when it is not.
  </p>
  <p>
    <code>.is-lifted</code> is set by JavaScript at runtime and is
    <strong>internal</strong> — style it if you like, but do not write it in markup.
  </p>
  <p class="dx-note text-muted">
    Drag-and-drop reordering has no keyboard equivalent in Deck. A list that can only be
    reordered by dragging cannot be reordered by a keyboard-only user at all. Provide
    move-up and move-down buttons as well, or make the order editable some other way.
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
    <code>--tap</code> is the one worth noting: it is the row's
    <code>min-block-size</code>, which is why a list of one-word rows is still
    comfortable on a phone.
  </p>
  <?php docs_token_table(['--surface', '--surface-2', '--surface-hover', '--line', '--r-md', '--r-sm', '--tap', '--space-3', '--space-4', '--text-sm', '--text-xs', '--text-muted', '--text-faint', '--shadow-3', '--dur-1']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong><code>.list</code> is not a list.</strong> It is a flex column of
      <code>&lt;div&gt;</code>s with no <code>role</code>, so a screen reader announces
      no item count and no "list of 12". If the count matters, use
      <code>&lt;ul&gt;</code> and <code>&lt;li class="list-row"&gt;</code> — the styles
      work unchanged — or put the count in a heading above.
    </li>
    <li>
      <strong>Touch target is handled.</strong> <code>min-block-size:
      var(--tap)</code> puts every row at
      <?= e(api_token('--tap')['value'] ?? '44px') ?> or taller, so a row that is a link
      is a legal target without any extra padding.
    </li>
    <li>
      <strong>A row that is an <code>&lt;a&gt;</code> is one tab stop with a real
      name</strong> — the row's text. That is the accessible way to build a navigable
      list, and it is why <code>.list-row</code> resets <code>color</code> and
      <code>text-decoration</code> rather than expecting a wrapper.
    </li>
    <li>
      <strong>Hover has no focus counterpart.</strong>
      <code>.list-row:hover</code> changes the background;
      <code>:focus-visible</code> gets the global focus ring but not the background. A
      keyboard user sees the ring, which is enough — but if you rely on the background
      to show position, add <code>:focus-visible</code> yourself.
    </li>
    <li>
      <strong>Buttons inside a row that is a link do not work.</strong> Nesting
      interactive elements inside an <code>&lt;a class="list-row"&gt;</code> is invalid
      HTML and the inner control will not reliably receive clicks. Make the row a
      <code>&lt;div&gt;</code> and put a stretched link in
      <code>.list-title</code> instead — the same pattern as
      <a href="card.php">card</a>.
    </li>
    <li>
      <strong><code>.list-trail</code> is faint by design.</strong>
      <code>--text-faint</code> against <code>--surface</code> is below 4.5:1. It is
      intended for a repeated timestamp or a chevron, not for information the reader
      needs. Anything load-bearing belongs in <code>.list-sub</code>, which is
      <code>--text-muted</code> and clears the ratio.
    </li>
    <li>
      <strong>Dragging is pointer-only.</strong> See
      <a href="#reorder">Reordering</a>. This is a documented gap, not an oversight.
    </li>
    <li>
      <strong><code>.list-virtual</code> and find-in-page.</strong>
      <code>content-visibility: auto</code> keeps offscreen content searchable by the
      browser, so ⌘F still works. It does change the scroll height as rows render,
      which can move the page under a reader.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Rows are flex lines with <code>gap</code> and logical padding, so the avatar moves to
    the right and the trail to the left under <code>dir="rtl"</code> with no extra
    rules. Dividers are <code>border-block-start</code>, which is unaffected by
    direction. The one directional detail is the hover chevron, which
    <code>src/19-logical.css</code> flips so it steps toward the text end rather than
    always to the right.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="list" style="max-inline-size:26rem">' . "\n" .
      '  <a class="list-row" href="#rtl">' . "\n" .
      '    <span class="avatar avatar-sm">ا</span>' . "\n" .
      '    <div class="list-main">' . "\n" .
      '      <span class="list-title">الفواتير</span>' . "\n" .
      '      <span class="list-sub">ثلاث فواتير غير مدفوعة</span>' . "\n" .
      '    </div>' . "\n" .
      '    <svg class="icon icon-follow mirror-rtl list-trail" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-right"></use></svg>' . "\n" .
      '  </a>' . "\n" .
      '</div>',
      'The chevron is mirrored by .mirror-rtl and steps the other way',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.list-row</code> transitions <code>background-color</code> over
    <?= e(api_token('--dur-1')['value'] ?? '120ms') ?>. Under
    <code>prefers-reduced-motion: reduce</code> the global reset collapses it to
    <code>.01ms</code>, so the hover state still happens instantly.
  </p>
  <p class="text-muted">
    The stepping chevron is declared only inside
    <code>@media (prefers-reduced-motion: no-preference)</code>, so it is never declared
    rather than declared and cancelled. The lifted-row shadow is a static state, not an
    animation, and is unaffected.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> gives <code>.list</code> the same surface treatment as
    a card — a <code>#bbb</code> hairline, no radius, white background,
    <code>break-inside: avoid</code> — and keeps <code>.list-header</code>'s grey fill
    with <code>print-color-adjust: exact</code> so group headers still read as headers.
    <code>src/26-perf.css</code> turns <code>content-visibility</code> off for print so
    no rows are skipped.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Taller rows, and tell the virtual list about it */
  .list-row { min-block-size: 4rem; }
  .list-virtual > .list-row { contain-intrinsic-size: auto 64px; }

  /* A focus background to match the hover one */
  .list-row:focus-visible { background: var(--surface-hover); }
}') ?></code></pre>
  <p>
    If you change the row height, change <code>contain-intrinsic-size</code> to match.
    Leaving them out of step is what makes a virtual list's scrollbar jump.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong><code>.list</code> versus <code>.table</code>.</strong> If values line up
      in columns and the reader compares them, use a table. A list of rows with four
      spans each is a table that has lost its column alignment.
    </li>
    <li>
      <strong>Not for a menu.</strong> Use <code>.menu</code> with
      <code>.menu-item</code>. It carries the roving focus, the
      <code>role="menu"</code> semantics and the dismiss behaviour that a list of links
      does not.
    </li>
    <li>
      <strong>Not for navigation.</strong> Use <code>.sidebar-link</code> or
      <code>.nav-link</code>, which have the current-page state.
      <code>aria-current</code> on a <code>.list-row</code> is styled by nothing.
    </li>
    <li>
      <strong>Not for a stack of cards.</strong> If each item needs an image, a
      paragraph and its own actions, it is a <code>.card</code> in a
      <code>.grid</code>. A <code>.list-row</code> is a single line with a start and an
      end; forcing a block of content into it fights the flex row.
    </li>
    <li>
      <strong>Not for form controls.</strong> A list of rows each holding a
      <code>.switch</code> looks right and behaves badly — the row's hover suggests the
      row is clickable when only the switch is. Use <code>.check-card</code>, which
      makes the whole block a label.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
