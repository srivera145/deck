<?php
declare(strict_types=1);

$page = [
    'path' => 'components/menu.php',
    'title' => 'Menu',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .menu is a native popover positioned with CSS anchor positioning: light dismiss, top layer and one-at-a-time behaviour from the platform, flipping and shifting from the browser rather than a scroll listener.',
    'documents' => [
        'menu', 'menu-item', 'menu-item-danger', 'menu-label', 'menu-match',
        'menu-sep',
    ],

    'component' => 'menu',
    'accounts' => [
        '07-components.css' => 'documented: the panel, its open animation, and the item, separator and label parts',
        '25-anchor.css'     => 'documented: anchor positioning, the fallback chain and .menu-match — the Positioning section',
        '99-print.css'      => 'documented: menus are dropped from print — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Menu</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Menu</h1>
  <p class="lede">
    <code>.menu</code> is a native popover. <code>popover</code> on the panel and
    <code>popovertarget</code> on the trigger is the entire wiring — no JavaScript, no
    click-outside handler, no z-index. The platform supplies light dismiss, the top layer,
    Escape, and closing any other open popover; CSS anchor positioning supplies the
    placement.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a menu for a list of actions attached to a control: an overflow menu, a row's
    actions, a set of options too long for a row of buttons. It is not modal — the page
    behind stays live, and clicking anywhere outside closes it.
  </p>
  <?php
  docs_example(
      '<button class="btn" popovertarget="dx-menu">Actions</button>' . "\n" .
      '<div class="menu" popover id="dx-menu">' . "\n" .
      '  <div class="menu-label">This export</div>' . "\n" .
      '  <button class="menu-item">Download CSV</button>' . "\n" .
      '  <button class="menu-item">Duplicate</button>' . "\n" .
      '  <button class="menu-item">Share<span class="push">⌘S</span></button>' . "\n" .
      '  <div class="menu-sep"></div>' . "\n" .
      '  <button class="menu-item menu-item-danger">Delete</button>' . "\n" .
      '</div>',
      'Two attributes. Click outside, or press Escape.',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>popovertarget</code> takes the panel's <code>id</code>. The button needs no
    type, no handler and no <code>aria-expanded</code> — the browser sets that itself.
  </p>
</section>

<section class="stack-6">
  <h2 id="positioning">Positioning</h2>
  <p>
    Placing a floating panel used to mean <code>getBoundingClientRect</code>, a scroll
    listener, a resize listener, and logic to flip it when it hits the bottom of the
    window. That is what Floating UI exists to do. CSS anchor positioning does it
    natively, on the compositor, with no listeners at all — and
    <code>src/25-anchor.css</code> is where Deck uses it.
  </p>

  <div class="stack-2">
    <h3 id="p-naming">Naming the pair</h3>
    <p>
      An anchor needs a name, and each trigger/panel pair needs a unique one. Rather than
      make you invent them, <code>deck.js</code> walks every
      <code>[popovertarget]</code>, generates a name, and sets it on both elements:
    </p>
    <pre class="dx-code"><code><?= e('trigger.classList.add(\'anchor\');
trigger.style.setProperty(\'--anchor\', name);
panel.style.setProperty(\'--anchor\', name);') ?></code></pre>
    <p class="text-muted">
      Set <code>--anchor</code> yourself on the panel and Deck uses yours instead. The
      whole routine is skipped where <code>anchor-name</code> is unsupported, so it costs
      nothing there.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="p-fallbacks">The fallback chain</h3>
    <pre class="dx-code"><code><?= e('position-area: block-end span-inline-end;
position-try-fallbacks:
  block-end span-inline-start,
  block-start span-inline-end,
  block-start span-inline-start,
  flip-block, flip-inline;
position-try-order: most-block-size;') ?></code></pre>
    <p>
      Read it as: prefer below and running toward the end edge; if that does not fit, try
      below running the other way, then above, then above the other way. If none fits,
      flip. <code>position-try-order: most-block-size</code> picks the option with the
      most vertical room rather than the first that merely fits — so a menu near the
      bottom of a window opens upward instead of opening downward and scrolling.
    </p>
    <p class="dx-note text-muted">
      Every one of these is a logical keyword. <code>span-inline-end</code> means toward
      the end of the inline axis, so the menu aligns to the right of its trigger in
      English and the left in Arabic without a second rule.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="p-size">Sizing from the anchor</h3>
    <p>
      <code>anchor-size()</code> lets the panel read its trigger's dimensions.
      <code>.menu</code> uses it for a floor —
      <code>min-inline-size: max(12rem, anchor-size(inline))</code> — so a menu is never
      narrower than the button it belongs to.
    </p>
    <p>
      <code>.menu-match</code> goes further and makes the width <em>equal</em> to the
      anchor's, which is what a dropdown attached to a form field should do.
    </p>
    <?php
    docs_example(
        '<div class="field" style="max-inline-size:22rem">' . "\n" .
        '  <span class="label" id="dx-menu-match-l">Assign to</span>' . "\n" .
        '  <button class="btn btn-block" popovertarget="dx-menu-match" aria-labelledby="dx-menu-match-l">Choose a person</button>' . "\n" .
        '  <div class="menu menu-match" popover id="dx-menu-match">' . "\n" .
        '    <button class="menu-item">Ada Chen</button>' . "\n" .
        '    <button class="menu-item">Marco Silva</button>' . "\n" .
        '    <button class="menu-item">Priya Raman</button>' . "\n" .
        '  </div>' . "\n" .
        '</div>',
        'The panel is exactly as wide as the button',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="p-fallback-browsers">Where anchor positioning is missing</h3>
    <p>
      The whole block is inside <code>@supports (anchor-name: --a)</code>. Without
      support, <code>.menu</code> keeps its <code>position: absolute</code> from
      <code>07-components.css</code> and Deck's adapter — or Floating UI, if you have
      loaded it — places it. Neither is required, and nothing breaks: the menu appears,
      just without the flipping.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="parts">Items, separators and labels</h2>
  <p>
    <code>.menu-item</code> is a full-width flex row,
    <code>min-block-size: 40px</code>, that works equally on a
    <code>&lt;button&gt;</code> or an <code>&lt;a&gt;</code> —
    <code>color: inherit</code> and <code>text-decoration: none</code> are set so a link
    needs no reset. <code>.push</code> inside one moves a keyboard shortcut to the end and
    fades it.
  </p>
  <p>
    <code>.menu-sep</code> is a one-pixel divider, <code>.menu-label</code> a small
    uppercase heading, and <code>.menu-item-danger</code> a destructive item in the bad
    colour with a matching hover.
  </p>
  <?php
  docs_example(
      '<button class="btn" popovertarget="dx-menu-parts">All the parts</button>' . "\n" .
      '<div class="menu" popover id="dx-menu-parts">' . "\n" .
      '  <div class="menu-label">Export</div>' . "\n" .
      '  <a class="menu-item" href="#parts">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#download"></use></svg>' . "\n" .
      '    Download<span class="push">⌘D</span>' . "\n" .
      '  </a>' . "\n" .
      '  <button class="menu-item">Duplicate<span class="push">⌘D</span></button>' . "\n" .
      '  <div class="menu-sep"></div>' . "\n" .
      '  <div class="menu-label">Danger</div>' . "\n" .
      '  <button class="menu-item menu-item-danger">Delete for everyone</button>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <code>min-block-size: 40px</code> is below the
    <?= e(api_token('--tap')['value'] ?? '44px') ?> touch target. That is a deliberate
    density trade for a menu that may hold eight items, and it is one of the two places
    Deck knowingly ships a smaller target — the other is the data grid's selection
    checkbox. Recorded in <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="animation">Opening</h2>
  <p>
    Same mechanism as <a href="modal.php#animation">modal</a>:
    <code>:popover-open</code> for the open state, <code>@starting-style</code> for the
    frame to animate from, and <code>overlay</code> plus <code>display</code> in the
    transition with <code>allow-discrete</code> so it stays rendered while it fades out.
    The menu fades and rises six pixels.
  </p>
  <p class="text-muted">
    <code>.menu::backdrop</code> is set to <code>transparent</code> rather than left
    alone. A popover's backdrop exists and captures nothing, but giving it an explicit
    transparent background stops a browser default from tinting the page.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from the stylesheet source by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--surface-hover', '--line', '--text', '--text-faint', '--text-sm', '--text-xs', '--bad-100', '--bad-700', '--r-md', '--r-xs', '--shadow-4', '--space-1', '--space-2', '--space-3', '--space-8', '--dur-1', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The popover attribute does a lot for free.</strong> Light dismiss, Escape,
      closing when another popover opens, and <code>aria-expanded</code> on the trigger
      are all the browser's. It also puts the panel in the top layer, so it escapes any
      <code>overflow: hidden</code> ancestor — the bug that breaks most hand-rolled
      dropdowns inside a modal or a table.
    </li>
    <li>
      <strong>Deck adds no <code>role="menu"</code>, and that is deliberate.</strong>
      The ARIA menu pattern requires roving <code>tabindex</code>, arrow-key navigation,
      Home and End, and typeahead. A <code>role="menu"</code> with none of that is worse
      than no role: it tells a screen reader to expect behaviour that is not there. Deck's
      menu is a group of buttons and links, and it is announced as exactly that.
    </li>
    <li>
      <strong>Arrow keys therefore do not move between items.</strong> Tab does. For most
      overflow menus that is acceptable and honest. If you need the real menu pattern, add
      the roles <em>and</em> the key handling together — never one without the other.
    </li>
    <li>
      <strong>Focus is not moved into the panel automatically.</strong> A popover does not
      trap or move focus. Adding <code>autofocus</code> to the first item is usually what
      a keyboard user expects from a menu.
    </li>
    <li>
      <strong>Touch target is 40px, not
      <?= e(api_token('--tap')['value'] ?? '44px') ?>.</strong> See the note under
      <a href="#parts">Items</a>.
    </li>
    <li>
      <strong>Hover and focus share a style.</strong>
      <code>.menu-item:hover, .menu-item:focus-visible</code> — so a keyboard user sees
      the same highlight a mouse user does, which is not true of
      <a href="list.php"><code>.list-row</code></a>.
    </li>
    <li>
      <strong>A destructive item needs more than a colour.</strong>
      <code>.menu-item-danger</code> is red, and red is not announced. Say "Delete" in the
      label, and put a <a href="modal.php">confirmation</a> behind it.
    </li>
    <li>
      <strong>Keyboard shortcut hints are decorative text.</strong>
      <code>&lt;span class="push"&gt;⌘S&lt;/span&gt;</code> is read aloud as a symbol.
      Wrap it in <code>aria-hidden="true"</code> if the noise is worse than the
      information.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Everything positional here is logical: <code>position-area</code> uses
    <code>span-inline-end</code>, the item padding is <code>padding-inline</code>, and
    <code>.push</code> is <code>margin-inline-start</code>. The menu aligns to the
    opposite edge of its trigger under <code>dir="rtl"</code> with no extra rules, and the
    fallback chain flips with it.
  </p>
  <?php
  docs_example(
      '<div dir="rtl">' . "\n" .
      '  <button class="btn" popovertarget="dx-menu-rtl">إجراءات</button>' . "\n" .
      '  <div class="menu" popover id="dx-menu-rtl">' . "\n" .
      '    <div class="menu-label">هذا التصدير</div>' . "\n" .
      '    <button class="menu-item">تنزيل<span class="push">⌘D</span></button>' . "\n" .
      '    <button class="menu-item">تكرار</button>' . "\n" .
      '    <div class="menu-sep"></div>' . "\n" .
      '    <button class="menu-item menu-item-danger">حذف</button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Aligned to the other edge, shortcut hint on the other side',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The panel fades and rises over
    <?= e(api_token('--dur-2')['value'] ?? '200ms') ?>, and items transition their
    background over <?= e(api_token('--dur-1')['value'] ?? '120ms') ?>. The global reset
    collapses both to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>, so the menu appears instantly and still
    closes correctly — <code>display</code> and <code>overlay</code> remain in the
    transition list.
  </p>
  <p class="text-muted">
    Anchor positioning itself is not animation. A menu flipping above its trigger because
    there is no room below is a layout decision, and it happens the same way whatever the
    motion preference.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> drops menus from print along with the rest of the page
    chrome. A menu is a control for operating the page, so on paper it is noise — and a
    popover that happened to be open would otherwise print floating over the content.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One menu, no layer needed */
<div class="menu" popover style="--anchor:--my-menu;min-inline-size:18rem">

@layer app.components {
  /* Open above by preference rather than below */
  .menu-up { position-area: block-start span-inline-end; }

  /* Full touch targets, at the cost of density */
  .menu-item { min-block-size: var(--tap); }
}') ?></code></pre>
  <p class="text-muted">
    Setting <code>--anchor</code> yourself is the supported way to control the pairing;
    Deck's generator skips any panel that already has one.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for site navigation.</strong> Use <code>.navbar</code> with
      <code>.nav-link</code>, or <code>.mega</code> for a large panel. A menu is for
      actions on a thing, not for destinations.
    </li>
    <li>
      <strong>Not for choosing a value in a form.</strong> Use
      <a href="select.php"><code>.select</code></a>, or <code>.combo</code> if the list
      is long enough to need filtering. A menu does not submit anything and has no value.
    </li>
    <li>
      <strong>Not for a confirmation.</strong> Use <a href="modal.php"><code>.modal</code></a>.
      A menu light-dismisses on any outside click, so a destructive action one click away
      inside one is easy to trigger by accident.
    </li>
    <li>
      <strong>Not for more than about ten items.</strong> Past that a reader is scanning
      rather than choosing, and there are no arrow keys to move with. Use a
      <a href="sheet.php"><code>.sheet</code></a> on a phone, or a searchable
      <code>.combo</code>.
    </li>
    <li>
      <strong>Not as a tooltip.</strong> Use <a href="tooltip.php"><code>.tip</code></a>
      for a label and <a href="popover.php"><code>.pop</code></a> for a card with
      content. A menu opens on click and stays open, which is the wrong behaviour for an
      explanation.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
