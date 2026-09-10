<?php
declare(strict_types=1);

$page = [
    'path' => 'components/table.php',
    'title' => 'Table',
    'level' => 'Beginner',
    'description' => 'Deck\'s .table: a scroll wrapper, a sticky header, tabular figures for numeric columns, and .table-stack, which restacks into labelled rows on a phone and unstacks again for print.',
    'documents' => [
        'table', 'table-compact', 'table-stack', 'table-wrap',
    ],

    'component' => 'table',
    'accounts' => [
        '07-components.css' => 'documented: the wrapper, the sticky header, the numeric cell, the compact variant and the phone restack',
        '99-print.css'      => 'documented: print restores the real table, repeats the header per page and avoids splitting rows — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';

$rows = [
    ['INV-2291', 'Ada Chen', '3 Mar', '1,240.00', 'good', 'Paid'],
    ['INV-2292', 'Marco Silva', '7 Mar', '86.50', 'warn', 'Pending'],
    ['INV-2293', 'Priya Raman', '11 Mar', '12,900.00', 'bad', 'Overdue'],
];

/* Built once and used by three examples, so the markup that is shown is the
   markup that ran. */
function demo_table(string $classes, array $rows, bool $labels = true): string
{
    $out = '<div class="table-wrap">' . "\n" . '  <table class="' . $classes . '">' . "\n"
        . '    <thead>' . "\n" . '      <tr>' . "\n"
        . '        <th scope="col">Invoice</th>' . "\n"
        . '        <th scope="col">Client</th>' . "\n"
        . '        <th scope="col">Due</th>' . "\n"
        . '        <th scope="col" class="num">Amount</th>' . "\n"
        . '        <th scope="col">Status</th>' . "\n"
        . '      </tr>' . "\n" . '    </thead>' . "\n" . '    <tbody>' . "\n";
    foreach ($rows as [$id, $who, $due, $amt, $tone, $status]) {
        $l = static fn(string $n): string => $labels ? ' data-label="' . $n . '"' : '';
        $out .= '      <tr>' . "\n"
            . '        <th scope="row"' . $l('Invoice') . '>' . $id . '</th>' . "\n"
            . '        <td' . $l('Client') . '>' . $who . '</td>' . "\n"
            . '        <td' . $l('Due') . '>' . $due . '</td>' . "\n"
            . '        <td class="num"' . $l('Amount') . '>' . $amt . '</td>' . "\n"
            . '        <td' . $l('Status') . '><span class="badge badge-' . $tone . '">' . $status . '</span></td>' . "\n"
            . '      </tr>' . "\n";
    }
    return $out . '    </tbody>' . "\n" . '  </table>' . "\n" . '</div>';
}
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Table</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Table</h1>
  <p class="lede">
    <code>.table</code> styles a real <code>&lt;table&gt;</code>. It stays a table at
    every width — the markup is never rewritten, the header row is never turned into
    <code>&lt;div&gt;</code>s — because a table's value is that values in a column can
    be compared, and that only survives if the columns survive.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a table when the reader needs to compare values <em>down</em> a column: amounts,
    dates, counts, statuses. If they only ever read one row at a time, a
    <a href="list.php"><code>.list</code></a> is easier to scan and much easier on a
    phone.
  </p>
  <p>
    <code>.table-wrap</code> is not optional. It carries the border, the radius, the
    background <em>and</em> <code>overflow-x: auto</code>. Without it a wide table pushes
    the whole page sideways, which is the single most common table bug there is.
  </p>
  <?php docs_example(demo_table('table', $rows, false), 'The wrapper carries the border and the horizontal scroll', 'stack'); ?>
</section>

<section class="stack-3">
  <h2 id="header">The sticky header</h2>
  <p>
    <code>.table thead th</code> is <code>position: sticky</code> with
    <code>inset-block-start: 0</code> and <code>z-index: 1</code>. In a tall table
    inside a scrolling page the header stays visible, so a reader forty rows down still
    knows which column is which. It needs no JavaScript and no fixed height.
  </p>
  <p class="text-muted">
    It sticks to the nearest scrolling ancestor. Inside a
    <code>.table-wrap</code> that only scrolls horizontally, that is the page — which is
    what you want. If you give the wrapper a <code>max-block-size</code> and vertical
    overflow, the header sticks to the wrapper instead, which is also what you want.
  </p>
</section>

<section class="stack-3">
  <h2 id="num">Numeric columns</h2>
  <p>
    <code>.num</code> on a cell sets <code>text-align: end</code> and
    <code>font-variant-numeric: tabular-nums</code>. Tabular figures give every digit
    the same advance width, so <code>1,240.00</code> and <code>12,900.00</code> line up
    on the decimal point without any padding tricks. Put it on the
    <code>&lt;th&gt;</code> as well as the cells, or the header sits over the wrong edge.
  </p>
  <p class="dx-note text-muted">
    <code>text-align: end</code>, not <code>right</code>. Under
    <code>dir="rtl"</code> the numbers move to the left edge along with the rest of the
    table, which is correct — the digits themselves still read left to right.
  </p>
</section>

<section class="stack-3">
  <h2 id="compact">Compact</h2>
  <p>
    <code>.table-compact</code> drops cell padding from
    <?= e(api_token('--space-3')['value'] ?? '.75rem') ?> /
    <?= e(api_token('--space-4')['value'] ?? '1rem') ?> to
    <?= e(api_token('--space-2')['value'] ?? '.5rem') ?> /
    <?= e(api_token('--space-3')['value'] ?? '.75rem') ?>. Use it when the table is a
    reference the reader scans rather than reads, and rows-on-screen matters more than
    comfort.
  </p>
  <?php docs_example(demo_table('table table-compact', $rows, false), '', 'stack'); ?>
</section>

<section class="stack-3">
  <h2 id="stack">Restacking on a phone</h2>
  <p>
    Below <code>40rem</code>, <code>.table-stack</code> turns each row into a small
    bordered block and each cell into a label/value line. The label comes from
    <code>data-label</code> on the cell via a <code>::before</code>, so no text is
    duplicated in the markup — and the <code>&lt;thead&gt;</code> is hidden visually but
    left in the accessibility tree rather than removed.
  </p>
  <p>
    Every cell needs its <code>data-label</code>. A cell without one renders as a value
    with no label, which is worse than not restacking at all.
  </p>
  <?php docs_example(demo_table('table table-stack', $rows), 'Narrow the window below 40rem to see it restack', 'stack'); ?>
  <p class="text-muted">
    This is the one place Deck changes <code>display</code> on table elements, and it is
    a real trade: the columns stop being comparable. Reach for it when the alternative
    is a horizontal scrollbar the reader will not find.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    Generated from <code>src/07-components.css</code> by
    <code>tools/docs/extract.mjs</code>. <code>.num</code> is not in this table: it is
    not part of the <code>table</code> component, because it works on any element that
    holds a figure.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--surface-2', '--surface-hover', '--line', '--r-md', '--r-sm', '--text-sm', '--text-xs', '--text-muted', '--space-2', '--space-3', '--space-4']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong><code>scope</code> is on you.</strong> Deck styles
      <code>&lt;th&gt;</code> but cannot know whether it heads a column or a row. Every
      header needs <code>scope="col"</code> or <code>scope="row"</code>, or a screen
      reader cannot tell a user which column the cell they are on belongs to.
    </li>
    <li>
      <strong>The first cell of a row should be a <code>&lt;th
      scope="row"&gt;</code>.</strong> Every example here does that. It gives each row a
      name, so navigating cell to cell announces "INV-2291, Amount, 1,240.00" rather
      than a bare number.
    </li>
    <li>
      <strong>Add a <code>&lt;caption&gt;</code>.</strong> It is the table's accessible
      name. <code>.sr-only</code> on it keeps it out of the visual design while leaving
      it in the accessibility tree — that is what the generated class tables on this
      site do.
    </li>
    <li>
      <strong>Keyboard scrolling of the wrapper.</strong> <code>.table-wrap</code>
      scrolls horizontally but is not focusable, so a keyboard-only user cannot scroll
      it without a pointer. <strong>This is a real defect.</strong> Add
      <code>tabindex="0"</code> and <code>role="region"</code> with an
      <code>aria-label</code> to a wrapper whose table overflows. Deck does not add
      <code>tabindex</code> for you, because putting a tab stop on every table wrapper
      whether or not it overflows is its own problem.
    </li>
    <li>
      <strong>The restack keeps the header in the tree.</strong>
      <code>.table-stack thead</code> is hidden with the clip-path technique rather than
      <code>display: none</code>, so header/cell association survives on a phone.
    </li>
    <li>
      <strong>The sticky header can cover a focused cell.</strong> With a link in the
      first visible row, keyboard focus can land underneath the sticky
      <code>&lt;th&gt;</code>. There is no <code>scroll-margin</code> on table cells to
      prevent it; add <code>scroll-margin-block-start</code> to the row if the table has
      focusable content.
    </li>
    <li>
      <strong>Hover is not focus.</strong> <code>.table tbody tr:hover</code> has no
      <code>:focus-within</code> counterpart, so a keyboard user gets no row
      highlight. Add one in <code>app.components</code> if rows contain controls.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Cells use <code>text-align: start</code> and <code>.num</code> uses
    <code>end</code>, so the whole table mirrors on <code>dir="rtl"</code> with no extra
    rules: the first column moves to the right, and amounts align to the left edge of
    their column. Row borders are <code>border-block-start</code>, which is unaffected.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="table-wrap">' . "\n" .
      '  <table class="table">' . "\n" .
      '    <thead><tr><th scope="col">العميل</th><th scope="col" class="num">المبلغ</th></tr></thead>' . "\n" .
      '    <tbody>' . "\n" .
      '      <tr><th scope="row">آدا تشين</th><td class="num">1,240.00</td></tr>' . "\n" .
      '      <tr><th scope="row">ماركو سيلفا</th><td class="num">86.50</td></tr>' . "\n" .
      '    </tbody>' . "\n" .
      '  </table>' . "\n" .
      '</div>',
      'start and end do the mirroring; the digits stay left to right',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The only animated property is the row hover background, which is not transitioned at
    all — it changes immediately. There is nothing for
    <code>prefers-reduced-motion</code> to reduce.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Print is where the table's design pays off, and
    <code>src/99-print.css</code> does more work here than anywhere else — twelve rules:
  </p>
  <ul class="stack-2">
    <li>
      <code>display: table-header-group</code> on <code>&lt;thead&gt;</code>, so the
      header repeats at the top of every printed page.
    </li>
    <li>
      <code>break-inside: avoid</code> on every row, so a row never splits across two
      sheets.
    </li>
    <li>
      <code>.table-stack</code> is <strong>undone</strong> — <code>display:
      table</code>, <code>table-row</code>, <code>table-cell</code> restored and the
      <code>::before</code> labels set to <code>content: none</code>. Paper is never
      narrow, so the phone restack would only waste it.
    </li>
    <li>
      <code>font-size: 9.5pt</code> and full width, with the header keeping a grey fill
      through <code>print-color-adjust: exact</code>.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <p>
    No local custom properties. Override in <code>app.components</code>, which beats
    every Deck layer regardless of specificity.
  </p>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Zebra striping, which Deck deliberately does not ship */
  .table tbody tr:nth-child(even) { background: var(--surface-2); }

  /* A row highlight for keyboard users, which Deck is missing */
  .table tbody tr:focus-within { background: var(--surface-hover); }
}') ?></code></pre>
  <p>
    Zebra striping is left out on purpose: with a hover background and a one-pixel row
    border already present, a third background makes three competing signals. It is two
    lines to add when a table is wide enough to need it.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong><code>.table</code> versus <code>.dg</code>.</strong> The data grid brings
      sorting, column pinning, selection, virtual scrolling and a card mode. If you need
      any of those, start there. <code>.table</code> is for data you present;
      <code>.dg</code> is for data the reader operates on.
    </li>
    <li>
      <strong>Not for layout.</strong> Obvious, still worth saying. Use
      <code>.grid</code> or <code>.split</code>.
    </li>
    <li>
      <strong>Not for a list of one-line items.</strong> A two-column table of label and
      value is a description list. Use <code>&lt;dl&gt;</code>, or
      <code>.list</code> with <code>.list-title</code> and <code>.list-trail</code>.
    </li>
    <li>
      <strong>Not when only one row is ever read.</strong> If the reader never compares
      values down a column, the table's structure costs them scrolling and gains them
      nothing. That is what <code>.list</code> is for.
    </li>
    <li>
      <strong><code>.table-stack</code> is not free.</strong> It destroys column
      comparison at exactly the width where a reader is most likely to be checking one
      number against another. Consider letting the table scroll horizontally instead —
      with <code>tabindex="0"</code> on the wrapper so the keyboard can reach it.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
