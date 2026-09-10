<?php
declare(strict_types=1);

$page = [
    'path' => 'components/datepicker.php',
    'title' => 'Date picker',
    'level' => 'Advanced',
    'description' => 'Deck\'s .datepicker is a popover calendar anchored to a .datefield: single dates or ranges, presets, month and year jump, and eight day states that compose into a range selection.',
    'documents' => [
        'datepicker', 'datepicker-day', 'datepicker-dow', 'datepicker-foot', 'datepicker-grid',
        'datepicker-head', 'datepicker-jump', 'datepicker-layout', 'datepicker-main', 'datepicker-month',
        'datepicker-months', 'datepicker-nav', 'datepicker-preset', 'datepicker-presets', 'datepicker-readout',
        'datepicker-time', 'datepicker-title', 'is-blocked', 'is-in-range', 'is-outside',
        'is-preview', 'is-range-end', 'is-range-start', 'is-selected', 'is-today',
        'datefield', 'datefield-clear', 'has-value',
    ],

    'component' => 'datepicker',
    'accounts' => [
        '10-datepicker.css' => 'documented: the trigger field, the popover panel, the calendar grid, all eight day states, presets and the month jump',
        '25-anchor.css'     => 'documented: the panel is anchored to its field with the same fallback chain as a menu — the Positioning section',
        '99-print.css'      => 'documented: the picker never prints — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Date picker</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Date picker</h1>
  <p class="lede">
    <code>.datefield</code> is the trigger — an <a href="input.php">input</a> with a
    calendar icon and a clear button. <code>.datepicker</code> is the popover panel it
    opens. The behaviour is a class in <code>deck.js</code>; the stylesheet is the skin,
    and its interesting part is the eight day states that compose into a range.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it when the format matters, when you need a range, or when the reader benefits
    from seeing a month at once. <code>&lt;input type="date"&gt;</code> is otherwise the
    better answer — it is free, it is accessible, and it gives a phone the platform's own
    picker.
  </p>
  <p>
    The trade is explicit: the native control renders differently in every browser and
    cannot be styled, and it has no range mode. If neither matters to you, use it.
  </p>
  <p>
    Here is a working one. <strong>Click the field</strong> — or focus it and press Enter,
    Space or Down — and the calendar opens.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:22rem">' . "\n" .
      '  <label class="label" for="dx-date-live">Ship date</label>' . "\n" .
      '  <div class="datefield" data-deck-datepicker data-format="iso">' . "\n" .
      '    <input class="input" id="dx-date-live" name="ship_date">' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'A live picker. That is the whole markup — the icon and the clear button are added for you.',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    That really is all of it. <code>DatePicker.build()</code> inserts the calendar icon
    and the <code>.datefield-clear</code> button if they are not already there, sets
    <code>readonly</code> and <code>cursor: pointer</code> on the input, creates the
    popover panel and appends it to the body. Write the icon yourself only if you want a
    different one — the script skips anything already present.
  </p>
  <p>The attributes, all optional:</p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Date picker attributes</caption>
      <thead><tr><th scope="col">Attribute</th><th scope="col">Default</th><th scope="col">What it does</th></tr></thead>
      <tbody>
        <tr><th scope="row" data-label="Attribute"><code>data-mode</code></th><td data-label="Default"><code>single</code></td><td data-label="What it does"><code>single</code> or <code>range</code></td></tr>
        <tr><th scope="row" data-label="Attribute"><code>data-format</code></th><td data-label="Default"><code>mdy</code></td><td data-label="What it does"><code>mdy</code>, <code>dmy</code>, <code>ymd</code> or <code>iso</code></td></tr>
        <tr><th scope="row" data-label="Attribute"><code>data-min</code> / <code>data-max</code></th><td data-label="Default">unbounded</td><td data-label="What it does">ISO dates bounding the selectable range</td></tr>
        <tr><th scope="row" data-label="Attribute"><code>data-presets</code></th><td data-label="Default">off</td><td data-label="What it does">Shows the shortcut rail</td></tr>
        <tr><th scope="row" data-label="Attribute"><code>data-months</code></th><td data-label="Default">1, or 2 in range mode</td><td data-label="What it does">How many months to show at once</td></tr>
        <tr><th scope="row" data-label="Attribute"><code>data-locale</code></th><td data-label="Default">the document's</td><td data-label="What it does">Month and day names, and the default week start</td></tr>
        <tr><th scope="row" data-label="Attribute"><code>data-week-start</code></th><td data-label="Default">from the locale</td><td data-label="What it does">0 for Sunday, 1 for Monday</td></tr>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-3">
  <h2 id="field">The trigger field</h2>
  <p>
    <code>.datefield</code> positions an icon inside the input's starting edge — the same
    technique as <a href="input.php#search"><code>.search</code></a>, with
    <code>pointer-events: none</code> so clicking the icon focuses the field. The input is
    <code>readonly</code> and <code>cursor: pointer</code>, because the value comes from
    the calendar rather than the keyboard.
  </p>
  <p>
    <code>.datefield-clear</code> is hidden until the field has a value:
    <code>.datefield:not(.has-value) .datefield-clear { display: none }</code>.
    <code>.has-value</code> is set by <code>deck.js</code> and is the one state here an
    author never writes.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:22rem">' . "\n" .
      '  <label class="label" for="dx-date">Ship date</label>' . "\n" .
      '  <div class="datefield has-value">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#calendar"></use></svg>' . "\n" .
      '    <input class="input" id="dx-date" value="2026-03-11" readonly>' . "\n" .
      '    <button class="datefield-clear" aria-label="Clear date">' . "\n" .
      '      <svg class="icon icon-sm" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#x"></use></svg>' . "\n" .
      '    </button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Static markup, not wired — this one does not open. The clear button shows because .has-value is set by hand.',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="opening">Opening and closing</h2>
  <p>
    The panel is a native popover, so most of its behaviour is the platform's. What
    <code>deck.js</code> adds is the ways in:
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">How the calendar opens and closes</caption>
      <thead><tr><th scope="col">Action</th><th scope="col">Result</th></tr></thead>
      <tbody>
        <tr><th scope="row" data-label="Action">Click the field</th><td data-label="Result">Opens</td></tr>
        <tr><th scope="row" data-label="Action">Enter, Space or Down on the field</th><td data-label="Result">Opens</td></tr>
        <tr><th scope="row" data-label="Action">Escape</th><td data-label="Result">Closes — the popover's own behaviour</td></tr>
        <tr><th scope="row" data-label="Action">Click outside</th><td data-label="Result">Closes — light dismiss, also the popover's</td></tr>
        <tr><th scope="row" data-label="Action">Click the field again</th><td data-label="Result"><strong>Nothing.</strong> It stays open.</td></tr>
      </tbody>
    </table>
  </div>
  <p class="dx-note text-muted">
    That last row is a wart rather than a bug. The handler calls
    <code>showPopover()</code> rather than <code>togglePopover()</code>, so the field
    opens the panel and never closes it. Escape and an outside click both work, so nothing
    is unreachable — but a reader who clicks the field expecting it to close will be
    surprised. Recorded in <code>FINDINGS.md</code>.
  </p>
  <p class="text-muted">
    <code>deck.js</code> also sets <code>popovertarget</code> on the input, which does
    nothing: the attribute is only honoured on buttons. The click handler is what actually
    opens the panel. <code>aria-haspopup="dialog"</code>, set alongside it, <em>is</em>
    meaningful and is what tells a screen reader the field opens something.
  </p>
</section>

<section class="stack-3">
  <h2 id="states">Eight day states</h2>
  <p>
    <code>.datepicker-day</code> is a 38px square — 42px on a coarse pointer, so it clears
    the touch target on a phone. Eight classes compose on it, and the way they compose is
    what makes a range selection read correctly.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Day states</caption>
      <thead><tr><th scope="col">State</th><th scope="col">What it means</th></tr></thead>
      <tbody>
        <tr><th scope="row" data-label="State"><code>.is-today</code></th><td data-label="What it means">A dot beneath the number, drawn with <code>::after</code></td></tr>
        <tr><th scope="row" data-label="State"><code>.is-selected</code></th><td data-label="What it means">The chosen day: brand fill, and the today dot inverts to stay visible</td></tr>
        <tr><th scope="row" data-label="State"><code>.is-outside</code></th><td data-label="What it means">A day from the previous or next month, faded</td></tr>
        <tr><th scope="row" data-label="State"><code>.is-blocked</code></th><td data-label="What it means">Outside <code>data-min</code>/<code>data-max</code>, or otherwise unavailable</td></tr>
        <tr><th scope="row" data-label="State"><code>.is-in-range</code></th><td data-label="What it means">Between the two ends of a range: a tinted band</td></tr>
        <tr><th scope="row" data-label="State"><code>.is-range-start</code></th><td data-label="What it means">The first day, rounded on the starting side only</td></tr>
        <tr><th scope="row" data-label="State"><code>.is-range-end</code></th><td data-label="What it means">The last day, rounded on the ending side only</td></tr>
        <tr><th scope="row" data-label="State"><code>.is-preview</code></th><td data-label="What it means">The range the pointer is currently hovering toward</td></tr>
      </tbody>
    </table>
  </div>
  <p>
    The band is the fiddly part. A range that spans a week boundary has to look continuous
    within each row and rounded where it stops, so the source uses
    <code>:has()</code> to round the first and last cell of every row:
  </p>
  <pre class="dx-code"><code><?= e('.datepicker-grid:has(.is-in-range) .datepicker-day.is-in-range:first-child { … }
.datepicker-grid:has(.is-in-range) .datepicker-day.is-in-range:nth-child(7n) { … }') ?></code></pre>
  <p class="text-muted">
    <code>.is-range-start.is-range-end</code> — a single-day range — gets the full radius
    back, so selecting one day twice does not leave a half-rounded pill.
  </p>
  <p class="dx-note text-muted">
    All eight are set by <code>deck.js</code> at runtime and are
    <strong>internal</strong>. They are documented because you may want to restyle them,
    not because you should write them.
  </p>
</section>

<section class="stack-3">
  <h2 id="positioning">Positioning</h2>
  <p>
    The panel is a native popover — light dismiss, Escape and the top layer from the
    platform — and <code>src/25-anchor.css</code> anchors it to its field with the same
    <code>position-area</code> and fallback chain as a
    <a href="menu.php#positioning"><code>.menu</code></a>. Near the bottom of a window it
    opens upward, and <code>position-try-order: most-block-size</code> picks the direction
    with the most room rather than the first that fits.
  </p>
  <p class="text-muted">
    That matters more for a calendar than for a menu: a picker that opens downward with
    four rows visible and the rest cut off is unusable, whereas a menu merely scrolls.
  </p>
</section>

<section class="stack-3">
  <h2 id="presets">Presets and the month jump</h2>
  <p>
    <code>.datepicker-presets</code> is a rail of shortcuts — "Last 7 days", "This month".
    Below <code>40rem</code> it is a horizontal scroller above the calendar; from
    <code>40rem</code> up, <code>.datepicker-layout:has(.datepicker-presets)</code> turns
    the whole panel into a row and the rail becomes a column beside it.
  </p>
  <p>
    <code>.datepicker-jump</code> opens <code>.datepicker-months</code>, a grid of twelve
    months, so a reader choosing a date two years out does not press the next-month arrow
    twenty-four times.
  </p>
  <p class="dx-note text-muted">
    A picker without a jump is the most common date-picker complaint there is. If you are
    accepting dates more than a few months away, turn it on.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/10-datepicker.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--surface-hover', '--line', '--text', '--text-faint', '--brand', '--brand-soft', '--brand-600', '--text-on-brand', '--r-md', '--r-sm', '--r-full', '--shadow-4', '--space-2', '--space-3', '--space-6', '--space-10', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Consider <code>&lt;input type="date"&gt;</code> first.</strong> It is fully
      accessible, gives a phone the platform picker, and needs nothing from you. A custom
      picker is a large accessibility surface to take on, and it should be taken on for a
      reason — a range, a format, a preset rail.
    </li>
    <li>
      <strong>The input is <code>readonly</code>, not <code>disabled</code></strong>, so
      it stays focusable and its value is still submitted. That is the right choice, but
      it also means <strong>a reader cannot type a date</strong> — which is faster than
      clicking for anyone who knows the date they want. Consider allowing typed input for
      single-date fields.
    </li>
    <li>
      <strong>Days are 38px, below the
      <?= e(api_token('--tap')['value'] ?? '44px') ?> target</strong> — but
      <code>@media (pointer: coarse)</code> raises them to 42px, which is close. This is
      the only place in Deck where a target is adjusted by pointer type rather than left
      at one value.
    </li>
    <li>
      <strong>A calendar grid needs keyboard navigation.</strong> Arrow keys between days,
      Page Up and Down between months, Home and End for the week. The behaviour is in
      <code>deck.js</code>; if you build your own panel with these classes, the keys are
      your responsibility.
    </li>
    <li>
      <strong>State is conveyed by more than colour.</strong> Today is a dot, selected is
      a filled square, blocked is faded <em>and</em> gets the
      <code>disabled</code> attribute. A range is a band, which is colour alone — so the
      selected ends carry the fill as well.
    </li>
    <li>
      <strong>Announce the chosen value.</strong> <code>.datepicker-readout</code> shows
      the current selection in the panel; put it in an <code>aria-live</code> region so a
      range being built is announced as it changes.
    </li>
    <li>
      <strong>The clear button needs a label.</strong> It is icon-only —
      <code>aria-label="Clear date"</code>, as in the example above.
    </li>
    <li>
      <strong><code>.is-preview</code> is hover-only</strong> and therefore invisible to
      keyboard and touch. It is a nicety for pointer users, not a state anyone else
      relies on.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The field's icon and clear button use <code>inset-inline-start</code> and
    <code>inset-inline-end</code>, and the range ends use
    <code>border-start-start-radius</code> and friends — logical corners — so a range
    rounds on the correct side in either direction.
  </p>
  <p class="text-muted">
    The calendar grid itself is a CSS grid, so the days of the week fill from the right
    under <code>dir="rtl"</code>. The week still starts where the locale says it does,
    which is the picker's job rather than the stylesheet's.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The panel fades and rises six pixels over
    <?= e(api_token('--dur-2')['value'] ?? '200ms') ?>, with <code>overlay</code> and
    <code>display</code> in the transition so it stays rendered while it closes. The
    global reset collapses it to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>. Nothing inside the calendar animates.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> lists <code>.datepicker</code> among the chrome that
    never prints. The panel is a control for choosing a value; what prints is the value in
    the field, which is an ordinary <a href="input.php">input</a> and gets the standard
    print treatment.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Larger days everywhere, not only on touch */
  .datepicker-day { inline-size: 44px; block-size: 44px; }

  /* A weekend tint */
  .datepicker-day:nth-child(7n), .datepicker-day:nth-child(7n-6) {
    color: var(--text-muted);
  }

  /* Keep the preset rail beside the calendar at every width */
  .datepicker-layout:has(.datepicker-presets) { flex-direction: row; }
}') ?></code></pre>
  <p class="text-muted">
    The <code>:nth-child(7n)</code> selectors assume a Sunday-first week. Which day starts
    the week is a locale question, so a weekend tint that is correct everywhere needs the
    picker to mark the cells rather than the stylesheet to count them.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not when <code>&lt;input type="date"&gt;</code> would do.</strong> A single
      date with no format requirement is a solved problem, and the native control is
      better at it than any custom panel.
    </li>
    <li>
      <strong>Not for a date the reader knows.</strong> A birth date is faster typed than
      navigated. If your picker is <code>readonly</code>, a reader entering
      1974 has to press the year jump and scroll.
    </li>
    <li>
      <strong>Not for a time.</strong> <code>.datepicker-time</code> exists for a time row
      inside the panel, but a standalone time is
      <code>&lt;input type="time"&gt;</code>.
    </li>
    <li>
      <strong>Not for a month or a quarter.</strong> Use a
      <a href="select.php">select</a> — a calendar grid for twelve options is a lot of
      panel for a short list.
    </li>
    <li>
      <strong>Not without <code>data-min</code> and <code>data-max</code></strong> when
      the valid range is known. <code>.is-blocked</code> exists so the picker can say no
      before the server does.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
