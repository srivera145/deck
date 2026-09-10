<?php
declare(strict_types=1);

$page = [
    'path' => 'components/drawer.php',
    'title' => 'Drawer',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .drawer is a native <dialog> pinned to an edge, sliding in with a logical translate so .drawer-end works in both writing directions. Header, scrolling body and footer, with safe-area insets.',
    'documents' => [
        'drawer', 'drawer-body', 'drawer-end', 'drawer-footer', 'drawer-header',
        'drawer-title',
    ],

    'component' => 'drawer',
    'accounts' => [
        '22-nav.css'   => 'documented: the panel, its edge variant, the slide-in, the backdrop and the three regions',
        '24-media.css' => 'internal: a drawer holding a media gallery gets its own body padding, which this page does not cover',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Drawer</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Drawer</h1>
  <p class="lede">
    <code>.drawer</code> is a native <code>&lt;dialog&gt;</code> pinned to one inline edge
    of the viewport, full height, sliding in from off-screen. Like
    <a href="modal.php">modal</a> it borrows the focus trap, the escape key and the
    backdrop from the element; what it adds is an edge, a size and a slide.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a drawer for a panel of secondary controls that the reader opens, works in, and
    closes: filters, settings, a navigation menu on a phone, a details panel beside a
    list. It is taller than it is wide, so it suits a vertical list of things.
  </p>
  <p>
    Open it with <code>showModal()</code>, for the same reasons as a modal — it is the
    only method that gives the top layer, the backdrop, the focus trap and the inert
    background.
  </p>
  <?php
  docs_example(
      '<button class="btn" onclick="document.getElementById(\'dx-drawer\').showModal()">Filters</button>' . "\n" .
      '<dialog class="drawer" id="dx-drawer" aria-labelledby="dx-drawer-t">' . "\n" .
      '  <div class="drawer-header">' . "\n" .
      '    <h3 class="drawer-title" id="dx-drawer-t">Filters</h3>' . "\n" .
      '    <button class="btn btn-sm btn-ghost btn-icon push" aria-label="Close" onclick="this.closest(\'dialog\').close()">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#x"></use></svg>' . "\n" .
      '    </button>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="drawer-body">' . "\n" .
      '    <label class="check"><input type="checkbox" checked><span>Overdue</span></label>' . "\n" .
      '    <label class="check"><input type="checkbox"><span>Unassigned</span></label>' . "\n" .
      '    <label class="check"><input type="checkbox"><span>Archived</span></label>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="drawer-footer">' . "\n" .
      '    <button class="btn push" onclick="this.closest(\'dialog\').close()">Clear</button>' . "\n" .
      '    <button class="btn btn-primary" onclick="this.closest(\'dialog\').close()">Apply</button>' . "\n" .
      '  </div>' . "\n" .
      '</dialog>',
      'Opens from the starting edge; Escape closes it',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="edge">Which edge</h2>
  <p>
    By default the drawer is pinned to the <strong>starting</strong> edge —
    <code>inset-inline-start: 0</code> — and slides in with
    <code>translate: -100% 0</code>. <code>.drawer-end</code> swaps it to the ending edge
    and reverses the translate.
  </p>
  <p>
    Both are logical, so in an RTL document the starting edge is the right and the
    animation reverses with it. There is no <code>.drawer-left</code>, and that is the
    point.
  </p>
  <?php
  docs_example(
      '<button class="btn" onclick="document.getElementById(\'dx-drawer-end\').showModal()">Open from the end edge</button>' . "\n" .
      '<dialog class="drawer drawer-end" id="dx-drawer-end" aria-labelledby="dx-drawer-end-t">' . "\n" .
      '  <div class="drawer-header">' . "\n" .
      '    <h3 class="drawer-title" id="dx-drawer-end-t">Details</h3>' . "\n" .
      '    <button class="btn btn-sm btn-ghost btn-icon push" aria-label="Close" onclick="this.closest(\'dialog\').close()">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#x"></use></svg>' . "\n" .
      '    </button>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="drawer-body"><p class="text-muted">Slides in from the other side.</p></div>' . "\n" .
      '</dialog>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="size">Size</h2>
  <p>
    <code>--drawer-size</code> defaults to <code>min(22rem, 88vw)</code>. The
    <code>88vw</code> half is what makes it usable on a phone: a fixed
    <code>22rem</code> drawer on a 360px screen would cover everything and leave no
    visible backdrop to tap.
  </p>
  <?php
  docs_example(
      '<button class="btn" onclick="document.getElementById(\'dx-drawer-wide\').showModal()">A wider drawer</button>' . "\n" .
      '<dialog class="drawer drawer-end" id="dx-drawer-wide" style="--drawer-size:min(32rem, 92vw)" aria-labelledby="dx-drawer-wide-t">' . "\n" .
      '  <div class="drawer-header"><h3 class="drawer-title" id="dx-drawer-wide-t">Wider</h3></div>' . "\n" .
      '  <div class="drawer-body">' . "\n" .
      '    <p class="text-muted">--drawer-size set inline. Keep a vw term in it.</p>' . "\n" .
      '    <button class="btn" onclick="this.closest(\'dialog\').close()">Close</button>' . "\n" .
      '  </div>' . "\n" .
      '</dialog>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="regions">Header, body, footer</h2>
  <p>
    The drawer is a flex column, so the three regions divide the full height between
    them: header and footer are <code>flex: 0 0 auto</code>, the body is
    <code>flex: 1 1 auto</code> with <code>overflow-y: auto</code>. A long list of filters
    scrolls while the Apply button stays where the reader last saw it.
  </p>
  <p>
    <code>overscroll-behavior: contain</code> on the body stops a scroll that reaches the
    end from chaining to the page behind — the effect where a drawer scrolls to its
    bottom and then the whole page starts moving underneath it.
  </p>
  <p class="text-muted">
    Both the header and the footer take safe-area insets:
    <code>padding-block-start: max(var(--space-4), env(safe-area-inset-top))</code> and
    the matching bottom. On a phone with a notch and a home indicator, the title clears
    the first and the buttons clear the second.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/22-nav.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    <code>--drawer-size</code> is local to the component rather than a global token,
    because a drawer's width is a per-instance decision.
  </p>
  <?php docs_token_table(['--surface', '--line', '--text', '--text-md', '--shadow-4', '--space-3', '--space-4', '--space-5', '--dur-3', '--ease-emphasized', '--hue-neutral']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Everything a modal gets, a drawer gets</strong> — focus trap, Escape,
      inert background, focus returned to the trigger — because it is the same element
      opened the same way.
    </li>
    <li>
      <strong>Name it.</strong> <code>aria-labelledby</code> pointing at
      <code>.drawer-title</code>, as every example on this page does. A drawer with no
      name is announced as "dialog".
    </li>
    <li>
      <strong>Include a visible close button.</strong> Escape works, but a pointer user
      on a touch device has no Escape key, and the backdrop is not a dismiss target
      unless you make it one.
    </li>
    <li>
      <strong>Focus order is markup order.</strong> The drawer is at the end of the
      document but in the top layer, so tabbing stays inside it. Put the close button
      first or last deliberately — first is easier to reach, last keeps it out of the way
      of the content.
    </li>
    <li>
      <strong>A navigation drawer is still navigation.</strong> Put a
      <code>&lt;nav&gt;</code> with a label inside it; the dialog role does not
      substitute for a landmark.
    </li>
    <li>
      <strong>Full height and small screens.</strong> <code>block-size: 100dvh</code>
      tracks a phone browser's chrome as it hides and shows, so the footer is not pushed
      under the address bar. This is the same reasoning as
      <a href="container.php#app-shell"><code>.app-shell</code></a>.
    </li>
    <li>
      <strong>The slide is long.</strong> <?= e(api_token('--dur-3')['value'] ?? '320ms') ?>
      with an emphasized easing — deliberate, because a panel travelling the width of
      itself needs time to read as travel rather than as a flash. It is collapsed under
      reduced motion.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>inset-inline-start</code>, <code>border-inline-end</code> and
    <code>.drawer-end</code>'s <code>inset-inline: auto 0</code> are all logical, so the
    default drawer opens from the right in an Arabic document and
    <code>.drawer-end</code> opens from the left.
  </p>
  <p class="dx-note text-muted">
    The one thing that is <em>not</em> logical is the translate:
    <code>translate: -100% 0</code> is a physical X offset, and it does not flip. It
    works because <code>.drawer-end</code> declares <code>translate: 100% 0</code>
    separately — so the pairing is correct in both directions by construction rather than
    by the property doing it. If you add a third edge variant, you have to remember this.
  </p>
  <?php
  docs_example(
      '<button class="btn" onclick="document.getElementById(\'dx-drawer-rtl\').showModal()">افتح اللوحة</button>' . "\n" .
      '<dialog dir="rtl" class="drawer" id="dx-drawer-rtl" aria-labelledby="dx-drawer-rtl-t">' . "\n" .
      '  <div class="drawer-header"><h3 class="drawer-title" id="dx-drawer-rtl-t">عوامل التصفية</h3></div>' . "\n" .
      '  <div class="drawer-body">' . "\n" .
      '    <label class="check"><input type="checkbox" checked><span>متأخرة</span></label>' . "\n" .
      '    <button class="btn" onclick="this.closest(\'dialog\').close()">إغلاق</button>' . "\n" .
      '  </div>' . "\n" .
      '</dialog>',
      'Opens from the right, because that is the starting edge',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The panel translates over <?= e(api_token('--dur-3')['value'] ?? '320ms') ?> and the
    backdrop fades with it. Under <code>prefers-reduced-motion: reduce</code> the global
    reset collapses both to <code>.01ms</code>, so the drawer is simply there and then
    simply gone.
  </p>
  <p class="text-muted">
    <code>display</code> and <code>overlay</code> stay in the transition list with
    <code>allow-discrete</code>, so the close still completes correctly at the shortened
    duration — the same mechanism explained on the
    <a href="modal.php#animation">modal page</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    An open drawer does not print at all. There is no <code>.drawer</code> rule in
    <code>src/99-print.css</code>, but there is a second <code>@layer deck.print</code> block at the end of <code>src/24-media.css</code>, and
    <code>.drawer</code> is in its <code>display: none !important</code> list alongside
    <code>.mega</code>, <code>.speed-dial</code> and <code>.banner</code>.
    <code>dialog::backdrop { display: none }</code> from the main print sheet removes the
    grey wash as well, so neither the panel nor its scrim reaches the page.
  </p>
  <p class="text-muted">
    That is the right default: a drawer holds controls rather than content, and a
    fixed-position panel printed over the top of the document would obscure the thing the
    reader actually wanted. If its contents need to be on paper, they belong in the page.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One drawer, no layer needed */
<dialog class="drawer" style="--drawer-size:min(28rem, 90vw)">

@layer app.components {
  /* A drawer that does not reach the full height */
  .drawer-inset {
    inset-block: var(--space-4);
    block-size: auto;
    border-radius: var(--r-lg);
    border: 1px solid var(--line);
  }

  /* Light dismiss, which Deck does not add */
  /* (needs one line of JS: close when the click target is the dialog itself) */
}') ?></code></pre>
  <p class="text-muted">
    Keep a viewport term in <code>--drawer-size</code>. A fixed width with no
    <code>vw</code> component covers the whole screen on a phone and leaves no backdrop
    to indicate there is anything behind it.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not on a phone, for most things.</strong> A side panel is a desktop shape.
      On a phone <a href="sheet.php"><code>.sheet</code></a> comes from the bottom, where
      the thumb is, and does not cover the whole screen.
    </li>
    <li>
      <strong>Not for a confirmation.</strong> Use <a href="modal.php"><code>.modal</code></a>.
      A drawer is a workspace; a confirmation is a question, and it should be centred and
      small.
    </li>
    <li>
      <strong>Not for a menu.</strong> Use <a href="menu.php"><code>.menu</code></a> — a
      popover with light dismiss and no focus trap. A drawer for five actions blocks the
      page for something that should not.
    </li>
    <li>
      <strong>Not as permanent navigation.</strong> A sidebar that is always visible is
      <code>.sidebar</code> inside an
      <a href="container.php#app-shell"><code>.app-shell</code></a>. A drawer is for
      something that opens and closes.
    </li>
    <li>
      <strong>Not for content the reader needs to compare with the page.</strong> The
      background is inert, so they cannot interact with it while the drawer is open. If
      they need both, use a <a href="split.php"><code>.split</code></a> rail.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
