<?php
declare(strict_types=1);

$page = [
    'path' => 'components/sheet.php',
    'title' => 'Sheet',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .sheet is a bottom sheet on a phone and a centred modal from 40rem up — one dialog, two shapes, two different animations, chosen by a media query rather than by the markup.',
    'documents' => [
        'sheet', 'sheet-body', 'sheet-grip', 'sheet-header', 'sheet-title',
    ],

    'component' => 'sheet',
    'accounts' => [
        '08-mobile.css' => 'documented: the bottom sheet, its grip, header and body, the backdrop, and the switch to a centred modal above 40rem',
        '99-print.css'  => 'documented: .sheet-grip is dropped for print — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Sheet</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Sheet</h1>
  <p class="lede">
    <code>.sheet</code> is one <code>&lt;dialog&gt;</code> that is two components. Below
    <code>40rem</code> it is a bottom sheet — full width, rounded at the top, rising from
    the bottom edge. Above it, it is a centred modal that fades and scales. Same markup,
    same <code>showModal()</code>; the media query decides.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for the things a phone reader reaches for with a thumb: a share menu, a set of
    filters, a short form, a picker. The bottom of the screen is the easiest place to
    reach on a phone and the hardest on a monitor, which is exactly why the component
    changes shape rather than staying a sheet everywhere.
  </p>
  <?php
  docs_example(
      '<button class="btn btn-primary" onclick="document.getElementById(\'dx-sheet\').showModal()">Share</button>' . "\n" .
      '<dialog class="sheet" id="dx-sheet" aria-labelledby="dx-sheet-t">' . "\n" .
      '  <div class="sheet-grip"></div>' . "\n" .
      '  <div class="sheet-header">' . "\n" .
      '    <h3 class="sheet-title" id="dx-sheet-t">Share this export</h3>' . "\n" .
      '    <button class="btn btn-sm btn-ghost btn-icon push" aria-label="Close" onclick="this.closest(\'dialog\').close()">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#x"></use></svg>' . "\n" .
      '    </button>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="sheet-body">' . "\n" .
      '    <button class="btn btn-block">Copy link</button>' . "\n" .
      '    <button class="btn btn-block">Email a copy</button>' . "\n" .
      '    <button class="btn btn-block">Download CSV</button>' . "\n" .
      '  </div>' . "\n" .
      '</dialog>',
      'Narrow the window below 40rem and open it again — it arrives from the bottom instead',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="two-shapes">One dialog, two shapes</h2>
  <p>
    The two states are worth reading side by side, because the difference is not only
    position — the animation changes too, and it changes for a reason.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">How the sheet differs above and below 40rem</caption>
      <thead>
        <tr><th scope="col"></th><th scope="col">Below 40rem</th><th scope="col">40rem and up</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="">Width</th>
          <td data-label="Below 40rem">100%</td>
          <td data-label="40rem and up"><code>min(30rem, 100vw − <?= e('2rem') ?>)</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="">Position</th>
          <td data-label="Below 40rem"><code>margin-block-start: auto</code> — pinned to the bottom</td>
          <td data-label="40rem and up"><code>margin: auto</code> — centred</td>
        </tr>
        <tr>
          <th scope="row" data-label="">Corners</th>
          <td data-label="Below 40rem">Rounded at the top only</td>
          <td data-label="40rem and up">Rounded all round</td>
        </tr>
        <tr>
          <th scope="row" data-label="">Animation</th>
          <td data-label="Below 40rem"><code>translate: 0 100%</code> — slides up</td>
          <td data-label="40rem and up">Fades and scales from <code>.97</code></td>
        </tr>
      </tbody>
    </table>
  </div>
  <p>
    The animation changes because the two shapes imply different things. A panel attached
    to an edge should arrive <em>from</em> that edge — the movement explains where it came
    from and where it will go. A centred dialog is attached to nothing, so sliding it up
    from the bottom of the screen would be travel with no story; fading and scaling reads
    as appearing in place.
  </p>
  <p class="text-muted">
    The mobile transition is <?= e(api_token('--dur-3')['value'] ?? '320ms') ?> and the
    desktop one is <?= e(api_token('--dur-2')['value'] ?? '200ms') ?>, for the same
    reason a drawer is slower than a modal: distance travelled needs time to read as
    travel.
  </p>
</section>

<section class="stack-3">
  <h2 id="grip">The grip</h2>
  <p>
    <code>.sheet-grip</code> is a 38&times;4 pill centred at the top of the sheet. It is
    the platform convention that says "this panel can be dragged down to dismiss", and it
    is worth knowing that <strong>Deck draws it and does not implement the drag</strong>.
    Swiping the sheet down does nothing.
  </p>
  <p class="dx-note text-muted">
    That makes it the same shape of problem as the
    <a href="file.php#dropping">file drop zone</a>: an affordance that promises a gesture
    the page does not handle. It is milder here — the grip also reads as a decorative
    handle, and a close button is usually present — but it is a promise nonetheless.
    Recorded in <code>FINDINGS.md</code>.
  </p>
  <p>
    <code>touch-action: pan-y</code> is not set on the sheet, so there is nothing to build
    a drag on either. If you add one, the pieces are a <code>pointerdown</code> on the
    grip, a translate that follows the pointer, and a threshold past which you call
    <code>close()</code> instead of springing back.
  </p>
</section>

<section class="stack-3">
  <h2 id="regions">Header and body</h2>
  <p>
    <code>.sheet-body</code> scrolls, with <code>overscroll-behavior: contain</code> so
    reaching its end does not start scrolling the page behind it. The sheet caps at
    <code>88dvh</code>, leaving a strip of backdrop visible at the top — which is what
    tells the reader there is a page behind, and gives them somewhere to tap if you add
    light dismiss.
  </p>
  <p>
    The body's bottom padding is
    <code>calc(var(--space-6) + env(safe-area-inset-bottom))</code>, so the last control
    clears the home indicator on a phone rather than sitting underneath it.
  </p>
  <?php
  docs_example(
      '<button class="btn" onclick="document.getElementById(\'dx-sheet-long\').showModal()">A long sheet</button>' . "\n" .
      '<dialog class="sheet" id="dx-sheet-long" aria-labelledby="dx-sheet-long-t">' . "\n" .
      '  <div class="sheet-grip"></div>' . "\n" .
      '  <div class="sheet-header"><h3 class="sheet-title" id="dx-sheet-long-t">Pick a category</h3></div>' . "\n" .
      '  <div class="sheet-body">' . "\n" .
      '    <label class="check"><input type="radio" name="dx-cat" checked><span>Exports</span></label>' . "\n" .
      '    <label class="check"><input type="radio" name="dx-cat"><span>Invoices</span></label>' . "\n" .
      '    <label class="check"><input type="radio" name="dx-cat"><span>Members</span></label>' . "\n" .
      '    <label class="check"><input type="radio" name="dx-cat"><span>Webhooks</span></label>' . "\n" .
      '    <label class="check"><input type="radio" name="dx-cat"><span>Audit log</span></label>' . "\n" .
      '    <label class="check"><input type="radio" name="dx-cat"><span>Domains</span></label>' . "\n" .
      '    <button class="btn btn-primary btn-block" onclick="this.closest(\'dialog\').close()">Done</button>' . "\n" .
      '  </div>' . "\n" .
      '</dialog>',
      'The body scrolls; the header stays',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/08-mobile.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--ink-300', '--text', '--text-md', '--r-lg', '--r-full', '--shadow-4', '--space-1', '--space-2', '--space-3', '--space-5', '--space-6', '--space-8', '--dur-2', '--dur-3', '--ease-spring', '--hue-neutral']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>It is a dialog, so it behaves like one</strong> — focus trapped, Escape
      closes, background inert, focus returned to the trigger. All from
      <code>showModal()</code>.
    </li>
    <li>
      <strong>Name it with <code>aria-labelledby</code></strong> pointing at
      <code>.sheet-title</code>, as the examples do.
    </li>
    <li>
      <strong>The grip is decorative and unlabelled</strong>, which is correct — it is an
      empty <code>&lt;div&gt;</code> and a screen reader ignores it. It is also, per
      <a href="#grip">above</a>, a gesture affordance with no gesture behind it. Always
      ship a real close button as well.
    </li>
    <li>
      <strong>A close button is not optional on a phone.</strong> There is no Escape key
      on a touch keyboard, and Deck adds no light dismiss. Without a button the only exits
      are the browser's back gesture and a hardware key.
    </li>
    <li>
      <strong>Safe-area insets are handled at the bottom, not the top.</strong>
      <code>.sheet-body</code> pads for the home indicator; the sheet does not pad for a
      notch, because it never reaches the top of the screen — the
      <code>88dvh</code> cap keeps it clear.
    </li>
    <li>
      <strong>Two shapes, one focus order.</strong> The media query changes only
      presentation, so the tab order, the reading order and the announcement are identical
      at every width. This is what makes a responsive component swap safe.
    </li>
    <li>
      <strong>Full-width buttons in the body are deliberate.</strong> A
      <code>.btn-block</code> in a bottom sheet is a large, easy target at the bottom of
      a phone screen, which is the whole reason for choosing this shape.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The mobile sheet's radii are <code>border-start-start-radius</code> and
    <code>border-start-end-radius</code> — logical corners — so the rounding stays on the
    top two corners in any writing direction. The animation is on the block axis, which
    does not mirror. There is nothing direction-specific to get wrong here, which is
    unusual for a component this size.
  </p>
  <?php
  docs_example(
      '<button class="btn" onclick="document.getElementById(\'dx-sheet-rtl\').showModal()">مشاركة</button>' . "\n" .
      '<dialog dir="rtl" class="sheet" id="dx-sheet-rtl" aria-labelledby="dx-sheet-rtl-t">' . "\n" .
      '  <div class="sheet-grip"></div>' . "\n" .
      '  <div class="sheet-header"><h3 class="sheet-title" id="dx-sheet-rtl-t">مشاركة التصدير</h3></div>' . "\n" .
      '  <div class="sheet-body">' . "\n" .
      '    <button class="btn btn-block">نسخ الرابط</button>' . "\n" .
      '    <button class="btn btn-block" onclick="this.closest(\'dialog\').close()">إغلاق</button>' . "\n" .
      '  </div>' . "\n" .
      '</dialog>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Both animations — the slide below <code>40rem</code>, the fade-and-scale above — are
    transitions, so the global reset collapses them to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>. The sheet appears and disappears in
    place.
  </p>
  <p class="text-muted">
    A slide of the sheet's full height is the largest movement in Deck's interactive set,
    so this is one of the components where the reduced-motion path matters most. As with
    <a href="modal.php#motion">modal</a>, <code>display</code> and <code>overlay</code>
    remain in the transition with <code>allow-discrete</code>, so closing still completes.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Two things happen. <code>dialog::backdrop { display: none }</code> stops an open
    sheet printing a grey wash over the page, and <code>.sheet-grip</code> is listed
    among the chrome that never prints — it is a gesture affordance, and paper has no
    gestures.
  </p>
  <p class="text-muted">
    The sheet itself is not otherwise adjusted, because it holds controls rather than
    content. If what is inside one needs to be printable, it belongs in the page.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A taller sheet on a phone */
  .sheet { max-block-size: 94dvh; }

  /* Keep it a bottom sheet at every width */
  @media (min-width: 40rem) {
    .sheet {
      inline-size: 100%;
      max-inline-size: 100%;
      margin: 0;
      margin-block-start: auto;
      border-radius: var(--r-lg) var(--r-lg) 0 0;
    }
  }
}') ?></code></pre>
  <p class="text-muted">
    Undoing the desktop shape means undoing four declarations, because the media query
    changes four things. If you find yourself doing that, you probably want a component
    that is always a bottom sheet rather than an override of one that is not.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a confirmation.</strong> Use <a href="modal.php"><code>.modal</code></a>.
      A sheet is a surface for choosing; a confirmation is a question, and it should be
      centred and small at every width.
    </li>
    <li>
      <strong>Not for a long form.</strong> <code>88dvh</code> minus a header leaves very
      little on a phone once the keyboard is up. Give it a page.
    </li>
    <li>
      <strong>Not on desktop-only interfaces.</strong> If nobody will see the phone shape,
      you are using a component with two behaviours to get one of them. Use a modal.
    </li>
    <li>
      <strong>Not with "swipe down to close" in the copy</strong> — until the drag is
      implemented, that is an instruction the page cannot follow.
    </li>
    <li>
      <strong>Not for a menu of actions on desktop.</strong> Use
      <a href="menu.php"><code>.menu</code></a>, which is anchored to its trigger and
      dismisses on an outside click. A sheet blocks the page.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
