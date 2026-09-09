<?php
declare(strict_types=1);

$page = [
    'path' => 'components/button.php',
    'title' => 'Button',
    'level' => 'Beginner',
    'description' => 'Deck\'s .btn component: seven variants, three sizes, icon-only, block, groups, loading and disabled states, with the touch target, focus ring and RTL behaviour explained.',
    'documents' => [
        'btn', 'btn-primary', 'btn-accent', 'btn-danger', 'btn-soft', 'btn-ghost',
        'btn-outline', 'btn-link', 'btn-sm', 'btn-lg', 'btn-block', 'btn-icon',
        'btn-round', 'btn-group', 'btn-3d', 'fab', 'is-loading', 'is-disabled',
    ],

    /* This page owns the `btn` component. tools/docs/verify.mjs cross-checks
       that claim against dist/api.json: every class and state in the component
       must appear in `documents`, and every source file with a rule affecting
       it must be accounted for below. Ten files style a button; this is how a
       page proves it looked at all ten instead of remembering nine. */
    'component' => 'btn',
    'accounts' => [
        '05-buttons.css' => 'documented: the component itself, its variants, sizes, group and states',
        '21-space3d.css' => 'documented: .btn-3d, in the Pressable section',
        '16-motion.css'  => 'documented: reduced-motion behaviour, in the Reduced motion section',
        '99-print.css'   => 'documented: buttons are dropped from print, in Reduced motion and Accessibility',
        '02-reset.css'   => 'internal: the global reduced-motion reset that keeps .is-loading spinning',
        '19-logical.css' => 'internal: buttons inherit the global logical-property and RTL rules',
        '06-forms.css'   => 'internal: layout of a .btn placed inside .field-row and .input-group',
        '07-components.css' => 'internal: spacing of a .btn inside .card-footer and .alert',
        '18-container.css'  => 'internal: .actions-cq reflows buttons on container width',
        '22-nav.css'     => 'internal: .fab offset above the tab bar, and buttons inside .navbar',
    ],
];

require __DIR__ . '/../_layout.php';

$variants = [
    ['btn', 'Default', 'Neutral action. The one to reach for when nothing else applies.'],
    ['btn-primary', 'Primary', 'The single most important action in a view. One per screen.'],
    ['btn-accent', 'Accent', 'A secondary emphasis colour, for upsells and highlights.'],
    ['btn-danger', 'Danger', 'Destructive and irreversible. Pair it with a confirmation.'],
    ['btn-soft', 'Soft', 'Tinted, low weight. Good for a repeated action inside a list.'],
    ['btn-ghost', 'Ghost', 'No chrome until hover. For toolbars and dense layouts.'],
    ['btn-outline', 'Outline', 'Brand-coloured border, transparent fill.'],
    ['btn-link', 'Link', 'Looks like a link, behaves like a button. See the caveat below.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Button</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Button</h1>
  <p class="lede">
    <code>.btn</code> is Deck's action element: an inline-flex box with a
    <?= e($tap = api_token('--tap')['value'] ?? '44px') ?> touch target, a focus ring, and
    a colour set driven by four custom properties. It works on
    <code>&lt;button&gt;</code>, <code>&lt;a&gt;</code>, and
    <code>&lt;input type="submit"&gt;</code> without changing class.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use <code>.btn</code> when the element performs an action: submitting a form,
    opening a dialog, running a command. Use a plain <code>&lt;a&gt;</code> when the
    element navigates somewhere, even if you want it to look like a button — the
    element decides what a screen reader announces and what the middle mouse button
    does, and a class cannot override either.
  </p>
  <p class="dx-note text-muted">
    A button that navigates is the single most common accessibility defect in a
    component library. If the thing has a destination, it is a link.
  </p>
</section>

<section class="stack-6">
  <h2 id="variants">Variants</h2>
  <p>
    Eight looks, including the bare <code>.btn</code>. Each sets the four
    <code>--btn-*</code> properties and inherits everything else, so a variant is a
    colour change and never a layout change.
  </p>

  <?php foreach ($variants as [$cls, $label, $blurb]): ?>
    <div class="stack-2">
      <h3 id="v-<?= e($cls) ?>"><?= e($label) ?></h3>
      <p class="text-muted"><?= e($blurb) ?></p>
      <?php
      $c = $cls === 'btn' ? 'btn' : "btn {$cls}";
      docs_example(
          '<button class="' . $c . '">Save changes</button>' . "\n" .
          '<button class="' . $c . '" disabled>Disabled</button>' . "\n" .
          '<a class="' . $c . '" href="#variants">As a link</a>'
      );
      ?>
    </div>
  <?php endforeach; ?>
</section>

<section class="stack-3">
  <h2 id="sizes">Sizes</h2>
  <p>
    Three heights, set from <code>--control-h-sm</code>, <code>--control-h</code>, and
    <code>--control-h-lg</code>. The small size is
    <?= e(api_token('--control-h-sm')['value'] ?? '34px') ?> tall but still clears the
    <?= e($tap) ?> touch target, because the target is a pseudo-element rather than the
    box itself.
  </p>
  <?php
  docs_example(
      '<button class="btn btn-sm">Small</button>' . "\n" .
      '<button class="btn">Default</button>' . "\n" .
      '<button class="btn btn-lg">Large</button>'
  );
  ?>

  <h3 id="sizes-matrix">Every variant at every size</h3>
  <p class="text-muted">Eight variants across three sizes, so the whole matrix is visible at once.</p>
  <?php
  $rows = '';
  foreach ($variants as [$cls, $label]) {
      $c = $cls === 'btn' ? 'btn' : "btn {$cls}";
      $rows .= '<div class="cluster cluster-tight">' . "\n"
          . '  <button class="' . $c . ' btn-sm">' . $label . '</button>' . "\n"
          . '  <button class="' . $c . '">' . $label . '</button>' . "\n"
          . '  <button class="' . $c . ' btn-lg">' . $label . '</button>' . "\n"
          . '</div>' . "\n";
  }
  docs_example(trim($rows), '', 'stack');
  ?>
</section>

<section class="stack-3">
  <h2 id="shapes">Icon-only, round, and block</h2>
  <p>
    <code>.btn-icon</code> makes the button square by dropping the inline padding, so it
    needs an <code>aria-label</code> — there is no text for a screen reader to announce.
    <code>.btn-round</code> only changes the radius. <code>.btn-block</code> fills its
    container.
  </p>
  <?php
  docs_example(
      '<button class="btn btn-icon" aria-label="Settings">' . "\n" .
      '  <svg class="icon"><use href="../../assets/deck/deck-icons.svg#settings"></use></svg>' . "\n" .
      '</button>' . "\n" .
      '<button class="btn btn-icon btn-round btn-primary" aria-label="Add">' . "\n" .
      '  <svg class="icon"><use href="../../assets/deck/deck-icons.svg#plus"></use></svg>' . "\n" .
      '</button>' . "\n" .
      '<button class="btn btn-icon btn-sm btn-ghost" aria-label="More">' . "\n" .
      '  <svg class="icon icon-sm"><use href="../../assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg>' . "\n" .
      '</button>'
  );
  docs_example('<button class="btn btn-primary btn-block">Block, fills its container</button>', '', 'stack');
  ?>

  <h3 id="btn-3d">Pressable</h3>
  <p>
    <code>.btn-3d</code> lives in <code>src/21-space3d.css</code> rather than with the
    other button classes, because it is an effect rather than a variant. It replaces the
    border with a stacked shadow and translates the button down by
    <code>--lift</code> on <code>:active</code>, so the press is physical. It is purely
    decorative, so it is removed entirely under reduced motion rather than shortened.
  </p>
  <?php docs_example('<button class="btn btn-primary btn-3d">Press me</button>'); ?>
</section>

<section class="stack-3">
  <h2 id="group">Groups</h2>
  <p>
    <code>.btn-group</code> joins adjacent buttons into a segmented control: it squares
    the inner corners, collapses the shared border with a negative margin, and raises the
    hovered button so its outline is not clipped. It uses logical radius properties, so
    the rounded ends swap under <code>dir="rtl"</code>.
  </p>
  <?php
  docs_example(
      '<div class="btn-group">' . "\n" .
      '  <button class="btn btn-sm">Day</button>' . "\n" .
      '  <button class="btn btn-sm">Week</button>' . "\n" .
      '  <button class="btn btn-sm">Month</button>' . "\n" .
      '</div>'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="states">States</h2>
  <p>
    <code>.is-loading</code> hides the label and draws a spinner in its place, and sets
    <code>pointer-events: none</code> so the click cannot repeat. Disabled is a real
    attribute; <code>.is-disabled</code> exists for elements that cannot take one, such
    as an anchor, and must be paired with <code>aria-disabled</code>.
  </p>
  <?php
  docs_example(
      '<button class="btn btn-primary is-loading">Submitting</button>' . "\n" .
      '<button class="btn" disabled>Disabled</button>' . "\n" .
      '<a class="btn is-disabled" aria-disabled="true" href="#states">Unavailable link</a>'
  );
  ?>
  <p class="text-muted">
    The spinner keeps turning under <code>prefers-reduced-motion: reduce</code>, slowed
    to a 2.4 second rotation rather than stopped. A frozen spinner reads as a hung page,
    and progress is information rather than decoration.
  </p>
</section>

<section class="stack-3">
  <h2 id="fab">Floating action button</h2>
  <p>
    <code>.fab</code> is a fixed, circular action parked above the tab bar and clear of
    the home indicator. It positions itself with <code>env(safe-area-inset-bottom)</code>
    and lifts further when the page has a <code>.tabbar</code>, so it does not need a
    media query or a magic number.
  </p>
  <p class="text-muted">
    It is fixed to the viewport, so it is described rather than rendered here — see it
    in the bottom corner of the <a href="../../index.php">component demo</a>.
  </p>
  <pre class="dx-code"><code><?= e('<button class="fab" aria-label="New item">
  <svg class="icon icon-lg"><use href="/assets/deck/deck-icons.svg#plus"></use></svg>
</button>') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    Generated from <code>src/05-buttons.css</code> by
    <code>tools/docs/extract.mjs</code>. If a class is added or removed in the source and
    this table is not updated, <code>tools/docs/verify.mjs</code> fails the build.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    <code>.btn</code> sets four local properties from global tokens, then every variant
    overrides those four. Restyling all buttons is a matter of changing
    <code>--btn-bg</code> and friends rather than re-declaring the component.
  </p>
  <?php docs_token_table(['--btn-bg', '--btn-text', '--btn-border', '--btn-shadow']); ?>
  <p class="text-muted">Those four read from these globals:</p>
  <?php docs_token_table(['--surface', '--text', '--line-strong', '--shadow-1', '--control-h', '--control-h-sm', '--control-h-lg', '--tap', '--r-sm', '--focus']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Touch target.</strong> <code>.btn</code> is
      <?= e(api_token('--control-h')['value'] ?? '42px') ?> tall, below the
      <?= e($tap) ?> minimum. A <code>::before</code> pseudo-element centred on the
      button carries <code>min-inline-size</code> and <code>min-block-size</code> of
      <code>--tap</code>, so the hit area clears the minimum without the button
      looking oversized. <code>.btn-sm</code> gets the same treatment.
    </li>
    <li>
      <strong>Focus.</strong> A two-pixel <code>--focus</code> outline with a 2px offset,
      on <code>:focus-visible</code> only, so a pointer click does not leave a ring but
      keyboard navigation always does.
    </li>
    <li>
      <strong>Icon-only buttons need a label.</strong> <code>.btn-icon</code> removes the
      text; add <code>aria-label</code>. The icon itself is decorative.
    </li>
    <li>
      <strong>Disabled anchors.</strong> <code>.is-disabled</code> styles the element but
      cannot stop a click the way <code>disabled</code> does. Add
      <code>aria-disabled="true"</code>, and remove the <code>href</code> if it must not
      be followed.
    </li>
    <li>
      <strong>Loading.</strong> <code>.is-loading</code> hides the label visually. Add
      <code>aria-busy="true"</code> so the state is announced, and keep the accessible
      name on the button.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>.btn</code> is written in logical properties, so <code>dir="rtl"</code> mirrors
    it with no extra stylesheet: <code>padding-inline</code>, the group's
    <code>border-start-start-radius</code>, and the negative
    <code>margin-inline-start</code> all follow the writing direction. An icon inside a
    button mirrors only if it points — Deck mirrors arrows, chevrons, and
    <code>#send</code>, and leaves checkmarks and clocks alone.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="cluster">' . "\n" .
      '  <button class="btn btn-primary">حفظ التغييرات</button>' . "\n" .
      '  <div class="btn-group">' . "\n" .
      '    <button class="btn btn-sm">يوم</button>' . "\n" .
      '    <button class="btn btn-sm">أسبوع</button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The same markup with dir="rtl" on the wrapper',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.btn</code> transitions background, border, colour, shadow, and a one-pixel
    press. Under <code>prefers-reduced-motion: reduce</code> the reset collapses every
    transition to <code>.01ms</code>, so the press becomes instant rather than animated.
    The loading spinner is deliberately exempt and slows to 2.4 seconds instead of
    stopping.
  </p>
  <p class="text-muted">
    <code>.btn-3d</code>, from <code>src/21-space3d.css</code>, is the one button effect
    that is purely decorative — its shadow-and-translate press is removed entirely rather
    than slowed.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <p>
    Deck ships inside cascade layers and reserves four empty <code>app.*</code> layers
    for application CSS. A rule in <code>app.components</code> beats every Deck rule
    regardless of specificity, so overriding a button needs a single class selector and
    no <code>!important</code>.
  </p>
  <pre class="dx-code"><code><?= e('@layer app.components {
  .btn {
    --btn-bg: var(--brand-50);
    border-radius: var(--r-full);
  }
}') ?></code></pre>
  <p>
    Prefer changing the four <code>--btn-*</code> properties over re-declaring
    <code>background</code> and <code>color</code>: the variants set those properties, so
    a token override survives <code>.btn-primary</code> being added later while a flat
    <code>background</code> override does not.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong><code>.btn-link</code> versus a plain anchor.</strong> Reach for
      <code>.btn-link</code> only when the element is genuinely a
      <code>&lt;button&gt;</code> that has to sit in a row of links — cancelling a
      dialog, toggling a disclosure. If it navigates, use a bare <code>&lt;a&gt;</code>
      and let it look like a link. <code>.btn-link</code> on an anchor adds a 44px
      target, a focus ring offset, and <code>white-space: nowrap</code> to something that
      wanted none of them, and it will not wrap mid-sentence.
    </li>
    <li>
      <strong>Not for navigation bars.</strong> Use <code>.nav-link</code> or
      <code>.sidebar-link</code>. They are sized for a row of destinations and carry the
      current-page state; a row of <code>.btn</code> elements reads as a row of commands.
    </li>
    <li>
      <strong>Not for a toggle with two states.</strong> Use <code>.switch</code> for on
      and off, or <code>.segmented</code> for one-of-three. A button that changes its own
      label is harder to announce and harder to script.
    </li>
    <li>
      <strong>Not for a whole clickable card.</strong> Use <code>.card-link</code> with a
      <code>.stretch</code> anchor, which keeps the text selectable and gives the card a
      single accessible name.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
