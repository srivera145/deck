<?php
declare(strict_types=1);

$page = [
    'path' => 'components/segmented.php',
    'title' => 'Segmented',
    'level' => 'Beginner',
    'description' => 'Deck\'s .segmented is an iOS-style one-of-N control: equal-width children in a sunken track, with the selected one raised. Styled from aria-selected, so the state is real rather than decorative.',
    'documents' => [
        'segmented',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Segmented</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Segmented</h1>
  <p class="lede">
    <code>.segmented</code> is a one-of-N control drawn as a sunken track with the
    selected option raised out of it. Its children get
    <code>flex: 1 1 0</code>, so every option is exactly the same width whatever its
    label — which is what makes the row read as a single control rather than as a row of
    buttons.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for two to four mutually exclusive options that the reader should be able to
    see all of at once: a date range, a view mode, a filter with a small fixed set of
    values. It is the compact alternative to a radio group, and it is particularly good on
    a phone, where a <a href="select.php">select</a> would hide the choices behind a tap.
  </p>
  <?php
  docs_example(
      '<div class="segmented" role="tablist" aria-label="Date range" style="max-inline-size:22rem">' . "\n" .
      '  <button class="btn btn-ghost" role="tab" aria-selected="true">Day</button>' . "\n" .
      '  <button class="btn btn-ghost" role="tab" aria-selected="false" tabindex="-1">Week</button>' . "\n" .
      '  <button class="btn btn-ghost" role="tab" aria-selected="false" tabindex="-1">Month</button>' . "\n" .
      '</div>',
      'Three equal thirds, whatever the labels say',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.segmented &gt; *</code> styles the children directly, so they need no class of
    their own. The <code>.btn.btn-ghost</code> above is there to give them a focus ring and
    a hover state; a bare <code>&lt;button&gt;</code> works too and is what the styling
    assumes.
  </p>
</section>

<section class="stack-3">
  <h2 id="state">The selected state</h2>
  <p>
    The raised look comes from <code>[aria-selected="true"]</code> — a surface background,
    the full text colour and a one-pixel shadow, against the sunken
    <code>--bg-sunken</code> track. Nothing moves: the unselected children have the same
    padding and radius, so selecting one is a colour and shadow change only.
  </p>
  <pre class="dx-code"><code><?= e('.segmented > [aria-selected="true"], .segmented > .is-active {
  background: var(--surface);
  color: var(--text);
  box-shadow: var(--shadow-1);
}') ?></code></pre>
  <p>
    As with <a href="tabs.php">tabs</a>, styling from the ARIA attribute means the visual
    state cannot drift from the accessible one. <code>.is-active</code> is accepted for
    cases where the ARIA attribute would be wrong — a set of links, for instance.
  </p>
</section>

<section class="stack-3">
  <h2 id="radios">The version with no JavaScript</h2>
  <p>
    A segmented control is a radio group wearing different clothes, and it can be built as
    one — real inputs, real names, real form submission, no script at all. Hide the input
    and drive the appearance from <code>:has(:checked)</code>.
  </p>
  <?php
  docs_example(
      '<fieldset class="fieldset" style="max-inline-size:24rem;border:0;padding:0">' . "\n" .
      '  <legend class="label">View</legend>' . "\n" .
      '  <div class="segmented">' . "\n" .
      '    <label class="btn btn-ghost">' . "\n" .
      '      <input class="sr-only" type="radio" name="dx-seg" checked>' . "\n" .
      '      <span>List</span>' . "\n" .
      '    </label>' . "\n" .
      '    <label class="btn btn-ghost">' . "\n" .
      '      <input class="sr-only" type="radio" name="dx-seg">' . "\n" .
      '      <span>Board</span>' . "\n" .
      '    </label>' . "\n" .
      '    <label class="btn btn-ghost">' . "\n" .
      '      <input class="sr-only" type="radio" name="dx-seg">' . "\n" .
      '      <span>Calendar</span>' . "\n" .
      '    </label>' . "\n" .
      '  </div>' . "\n" .
      '</fieldset>',
      'Real radios: arrow keys work, and it submits with the form',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <strong>Deck does not style the checked state of this version.</strong> The rule keys
    off <code>[aria-selected]</code>, not <code>:has(:checked)</code>, so the labels above
    look identical whichever radio is chosen. It is a real gap, recorded in
    <code>FINDINGS.md</code>; one rule closes it:
    <code>.segmented &gt; label:has(:checked) { background: var(--surface); color:
    var(--text); box-shadow: var(--shadow-1); }</code>
  </p>
  <p>
    Until then, the radio version needs that rule in your own layer — but it is still
    worth preferring, because arrow-key navigation, form submission and the group label
    all come free from the elements.
  </p>
</section>

<section class="stack-3">
  <h2 id="width">Width</h2>
  <p>
    <code>inline-size: 100%</code> is set on the control, so a segmented control fills its
    container by default. That suits a phone, where it usually spans the screen. Cap it
    with a <code>max-inline-size</code> or put it in a narrower parent when it should not.
  </p>
  <?php
  docs_example(
      '<div class="stack-3">' . "\n" .
      '  <div class="segmented" style="max-inline-size:16rem">' . "\n" .
      '    <button class="btn btn-ghost" aria-selected="true">On</button>' . "\n" .
      '    <button class="btn btn-ghost" aria-selected="false">Off</button>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="segmented">' . "\n" .
      '    <button class="btn btn-ghost" aria-selected="true">Full width</button>' . "\n" .
      '    <button class="btn btn-ghost" aria-selected="false">By default</button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    One class. The children are styled by <code>.segmented &gt; *</code>, so there are no
    <code>.segmented-*</code> members and no component root — hence no completeness check
    on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--bg-sunken', '--surface', '--line', '--text', '--text-muted', '--text-sm', '--r-sm', '--shadow-1', '--space-2', '--space-3', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Pick a pattern and commit to it.</strong> A segmented control can be a
      tablist, a radio group, or a group of buttons — and each has different obligations.
      Mixing them, or using <code>role="tablist"</code> with no arrow keys, is worse than
      using none.
    </li>
    <li>
      <strong>As a tablist</strong>, it needs everything
      <a href="tabs.php#keyboard">the tabs page</a> describes: roving
      <code>tabindex</code>, arrow keys, Home and End. Deck ships none of it.
    </li>
    <li>
      <strong>As a radio group</strong>, the browser gives you arrow keys, the group label
      from a <code>&lt;legend&gt;</code>, and form submission — for free. This is the
      version to prefer, and the one Deck does not fully style.
    </li>
    <li>
      <strong>The children are 36px tall</strong>, below the
      <?= e(api_token('--tap')['value'] ?? '44px') ?> touch target. That is a deliberate
      density choice for a control that is often full width — the horizontal target is
      generous even when the vertical one is not — but it is a third place Deck ships
      under the minimum, after the menu item and the data grid checkbox.
    </li>
    <li>
      <strong>The selected state is not colour alone.</strong> It has a background, a
      shadow and a text-colour change, so it survives without colour vision.
    </li>
    <li>
      <strong><code>.sr-only</code> on the radio, not
      <code>display: none</code>.</strong> A hidden-with-display input is not focusable
      and not in the accessibility tree, which would take the arrow keys away — the same
      trap as the <a href="file.php#hiding">file input</a>.
    </li>
    <li>
      <strong>Label the group.</strong> <code>aria-label</code> on the tablist version, a
      <code>&lt;legend&gt;</code> on the radio version. Without one the options are
      announced with no indication of what is being chosen.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    A flex row with a gap and <code>padding-inline</code> on the children, so it fills
    from the right under <code>dir="rtl"</code> with no extra rules. The first option in
    the markup is the first from the right, which is what a reader of Arabic expects.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="segmented" aria-label="النطاق الزمني" style="max-inline-size:22rem">' . "\n" .
      '  <button class="btn btn-ghost" aria-selected="true">يوم</button>' . "\n" .
      '  <button class="btn btn-ghost" aria-selected="false">أسبوع</button>' . "\n" .
      '  <button class="btn btn-ghost" aria-selected="false">شهر</button>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Children transition their background and colour over
    <?= e(api_token('--dur-2')['value'] ?? '200ms') ?>; the global reset collapses both to
    <code>.01ms</code> under <code>prefers-reduced-motion: reduce</code>. There is no
    sliding pill behind the selection, which would need JavaScript to measure — the
    background simply moves from one child to another.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> lists <code>.segmented</code> among the chrome that
    never prints, alongside <code>.tabs</code> and <code>.pagination</code>. It is a
    control for choosing what to look at, and on paper the choice has already been made.
  </p>
  <p class="dx-note text-muted">
    The same caveat as tabs: the printed page then carries no record of which option was
    selected. If the reader needs to know they were looking at Week rather than Month,
    that has to appear in the content as well as in the control.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Style the radio version, which Deck does not */
  .segmented > label:has(:checked) {
    background: var(--surface);
    color: var(--text);
    box-shadow: var(--shadow-1);
  }

  /* Natural widths rather than equal thirds */
  .segmented-auto > * { flex: 0 1 auto; }

  /* Full touch targets */
  .segmented > * { min-block-size: var(--tap); }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for more than about four options.</strong> Equal widths mean five
      options are five narrow columns, and the labels start truncating. Use
      <a href="select.php">a select</a>, or a radio group in a
      <a href="fieldset.php">fieldset</a>.
    </li>
    <li>
      <strong>Not for long labels.</strong> <code>white-space: nowrap</code> is set, so a
      long option pushes the control wider rather than wrapping. One or two words each.
    </li>
    <li>
      <strong>Not for actions.</strong> A segmented control shows a persistent choice.
      Three buttons that each do something are <a href="cluster.php">a cluster</a> of
      <a href="button.php">buttons</a> or a <a href="bar.php">bar</a>.
    </li>
    <li>
      <strong>Not for an on/off setting.</strong> Two options where one is "off" is a
      <a href="switch.php"><code>.switch</code></a>, which is smaller and reads faster.
    </li>
    <li>
      <strong>Not as a tablist without the keyboard work.</strong> Use the radio version
      instead — you get the arrow keys from the platform and the only thing missing is one
      CSS rule.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
