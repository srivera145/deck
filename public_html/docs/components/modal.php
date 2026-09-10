<?php
declare(strict_types=1);

$page = [
    'path' => 'components/modal.php',
    'title' => 'Modal',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .modal styles a native <dialog>, so the focus trap, the escape key, the backdrop and the top layer are the browser\'s. What Deck adds is an animated open and close using @starting-style and allow-discrete.',
    'documents' => [
        'modal', 'modal-body', 'modal-footer', 'modal-header', 'modal-title',
    ],

    'component' => 'modal',
    'accounts' => [
        '07-components.css' => 'documented: the dialog, its backdrop, the open and close animation, and the header, body and footer regions',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Modal</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Modal</h1>
  <p class="lede">
    <code>.modal</code> goes on a native <code>&lt;dialog&gt;</code>. That single
    decision is most of the component: the focus trap, the escape key, the inertness of
    the page behind, the backdrop and the top layer all come from the element. Deck
    supplies the look and an open-and-close animation, and implements none of the
    behaviour.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a modal when the reader must deal with something before continuing: confirming a
    destructive action, completing a short form that would lose its place inline, reading
    something that blocks the task. Modality is an interruption, and it should be
    justified by the interruption being necessary.
  </p>
  <p>
    Open it with <code>showModal()</code>, never <code>show()</code> and never
    <code>open</code> in the markup. Only <code>showModal()</code> puts the dialog in the
    top layer, renders <code>::backdrop</code>, traps focus and makes the rest of the page
    inert — the other two give you a styled box with none of it.
  </p>
  <?php
  docs_example(
      '<button class="btn btn-primary" onclick="document.getElementById(\'dx-modal\').showModal()">Delete project</button>' . "\n" .
      '<dialog class="modal" id="dx-modal">' . "\n" .
      '  <form method="dialog" class="stack-0">' . "\n" .
      '    <div class="modal-header">' . "\n" .
      '      <h3 class="modal-title">Delete this project?</h3>' . "\n" .
      '    </div>' . "\n" .
      '    <div class="modal-body">' . "\n" .
      '      <p>Everything in it goes with it: 2,481 exports, 14 webhooks and the audit log.</p>' . "\n" .
      '      <p class="text-muted">This cannot be undone.</p>' . "\n" .
      '    </div>' . "\n" .
      '    <div class="modal-footer">' . "\n" .
      '      <button class="btn" value="cancel">Cancel</button>' . "\n" .
      '      <button class="btn btn-danger" value="delete">Delete project</button>' . "\n" .
      '    </div>' . "\n" .
      '  </form>' . "\n" .
      '</dialog>',
      'Open it, then press Escape — nothing here handles that key',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>&lt;form method="dialog"&gt;</code> is the detail worth copying. Any button
    inside it closes the dialog and sets <code>returnValue</code> to that button's
    <code>value</code>, so a confirm dialog needs no click handlers at all — read
    <code>dialog.returnValue</code> on the <code>close</code> event.
  </p>
</section>

<section class="stack-6">
  <h2 id="animation">Animating a dialog</h2>
  <p>
    A dialog goes from <code>display: none</code> to <code>display: block</code>, and
    historically that made it impossible to animate in: the element had no rendered
    starting state to transition from, and on close it disappeared before any transition
    could run. Deck's modal animates both directions with three modern pieces and no
    JavaScript.
  </p>

  <div class="stack-2">
    <h3 id="a-starting">@starting-style — the state to animate from</h3>
    <p>
      A <code>@starting-style</code> block gives an element the styles it should have for
      the first frame it is rendered. Without it there is nothing to interpolate from and
      the modal simply appears.
    </p>
    <pre class="dx-code"><code><?= e('.modal { opacity: 0; scale: .97; translate: 0 8px; }
.modal[open] { opacity: 1; scale: 1; translate: 0; }
@starting-style { .modal[open] { opacity: 0; scale: .97; translate: 0 8px; } }') ?></code></pre>
  </div>

  <div class="stack-2">
    <h3 id="a-discrete">transition-behavior: allow-discrete — staying visible to close</h3>
    <p>
      <code>display</code> and <code>overlay</code> are discrete properties: they flip
      instantly and cannot be interpolated. Listing them in the transition with
      <code>allow-discrete</code> makes the browser defer the flip to the
      <em>end</em> of the transition, so the modal stays rendered — and stays in the top
      layer — long enough to animate out.
    </p>
    <pre class="dx-code"><code><?= e('transition: opacity var(--dur-2) var(--ease-out),
            scale var(--dur-2) var(--ease-spring),
            translate var(--dur-2) var(--ease-spring),
            overlay var(--dur-2) allow-discrete,
            display var(--dur-2) allow-discrete;') ?></code></pre>
    <p class="dx-note text-muted">
      <code>overlay</code> is the one people miss. Without it the dialog leaves the top
      layer immediately on close, so it drops behind everything else and the fade-out
      happens somewhere the reader cannot see.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="a-backdrop">The backdrop animates separately</h3>
    <p>
      <code>::backdrop</code> is its own pseudo-element with its own transition, so it
      needs its own <code>@starting-style</code> too. Deck fades it from transparent and
      blurs what is behind it by 3px.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="regions">Header, body, footer</h2>
  <p>
    The same three-region pattern as <a href="card.php">card</a>, with one addition that
    matters: <code>.modal-body</code> is the scrolling region.
    <code>.modal</code> caps itself at <code>min(85dvh, 48rem)</code> and clips, so a long
    body scrolls while the header and footer stay put — which means the actions are never
    below the fold.
  </p>
  <?php
  docs_example(
      '<button class="btn" onclick="document.getElementById(\'dx-modal-long\').showModal()">Open a long modal</button>' . "\n" .
      '<dialog class="modal" id="dx-modal-long">' . "\n" .
      '  <form method="dialog" class="stack-0">' . "\n" .
      '    <div class="modal-header"><h3 class="modal-title">Terms</h3></div>' . "\n" .
      '    <div class="modal-body">' . "\n" .
      '      <p>The header and footer stay; this region scrolls.</p>' . "\n" .
      '      <p>One.</p><p>Two.</p><p>Three.</p><p>Four.</p><p>Five.</p><p>Six.</p>' . "\n" .
      '      <p>Seven.</p><p>Eight.</p><p>Nine.</p><p>Ten.</p><p>Eleven.</p><p>Twelve.</p>' . "\n" .
      '    </div>' . "\n" .
      '    <div class="modal-footer"><button class="btn btn-primary">Accept</button></div>' . "\n" .
      '  </form>' . "\n" .
      '</dialog>',
      'The footer is pinned; only the body moves',
      'stack'
  );
  ?>
  <p class="text-muted">
    Below <code>32rem</code>, <code>.modal-footer</code> becomes
    <code>column-reverse</code> with full-width buttons — so the primary action, which is
    last in the markup, is on top where a thumb reaches it, while the DOM order that
    screen readers and keyboards follow is unchanged.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/07-components.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--surface-2', '--line', '--text', '--text-lg', '--r-lg', '--shadow-4', '--space-3', '--space-4', '--space-5', '--space-8', '--dur-2', '--ease-spring', '--hue-neutral']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The element does the hard parts.</strong> <code>showModal()</code> traps
      focus inside the dialog, makes everything else inert, closes on Escape and exposes
      the dialog as <code>role="dialog"</code> with <code>aria-modal</code>. None of that
      is Deck's, and none of it can be broken by styling.
    </li>
    <li>
      <strong>Give it an accessible name.</strong> Put an <code>id</code> on
      <code>.modal-title</code> and point at it with <code>aria-labelledby</code> on the
      dialog. Without one, a screen reader announces "dialog" and nothing else.
    </li>
    <li>
      <strong>Focus lands on the first focusable element</strong> unless you say
      otherwise. For a destructive confirmation that is usually wrong — put
      <code>autofocus</code> on Cancel so an accidental Enter does not delete anything.
    </li>
    <li>
      <strong>Focus returns to the trigger on close</strong>, handled by the browser.
      This is the part hand-rolled modals most often get wrong, and it is free here.
    </li>
    <li>
      <strong>Escape cannot be disabled, and should not be.</strong> If closing needs
      confirmation, listen for <code>cancel</code> and call
      <code>preventDefault()</code> — but be certain, because a reader who cannot get out
      of a dialog is trapped.
    </li>
    <li>
      <strong>Clicking the backdrop does nothing by default.</strong> Deck adds no
      light-dismiss handler, which is deliberate for a confirmation. If you want it,
      compare <code>event.target</code> to the dialog itself — the backdrop counts as the
      dialog for click purposes.
    </li>
    <li>
      <strong>The page behind still scrolls</strong> in some browsers.
      <code>&lt;dialog&gt;</code> makes the background inert but does not universally lock
      scrolling. If that matters, set <code>overflow: hidden</code> on
      <code>&lt;html&gt;</code> while it is open — Deck does not, because doing it in CSS
      alone is not possible.
    </li>
    <li>
      <strong>Touch targets in the footer.</strong> Buttons are <code>.btn</code>, which
      clears <?= e(api_token('--tap')['value'] ?? '44px') ?>, and below
      <code>32rem</code> they go full width.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The dialog is centred by the browser and its regions use logical padding and borders,
    so the whole component mirrors with no extra rules. The footer's
    <code>justify-content: flex-end</code> follows the writing direction, putting the
    primary action on the left in an RTL layout — which is where a reader of Arabic
    expects it.
  </p>
  <?php
  docs_example(
      '<button class="btn" onclick="document.getElementById(\'dx-modal-rtl\').showModal()">افتح النافذة</button>' . "\n" .
      '<dialog dir="rtl" class="modal" id="dx-modal-rtl">' . "\n" .
      '  <form method="dialog" class="stack-0">' . "\n" .
      '    <div class="modal-header"><h3 class="modal-title">حذف المشروع؟</h3></div>' . "\n" .
      '    <div class="modal-body"><p>لا يمكن التراجع عن هذا الإجراء.</p></div>' . "\n" .
      '    <div class="modal-footer">' . "\n" .
      '      <button class="btn">إلغاء</button>' . "\n" .
      '      <button class="btn btn-danger">حذف</button>' . "\n" .
      '    </div>' . "\n" .
      '  </form>' . "\n" .
      '</dialog>',
      'The footer actions move to the left edge',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The modal scales, translates and fades over
    <?= e(api_token('--dur-2')['value'] ?? '200ms') ?>, and the backdrop fades with it.
    Under <code>prefers-reduced-motion: reduce</code> the global reset collapses every
    transition to <code>.01ms</code>, so the dialog appears and disappears instantly.
  </p>
  <p class="dx-note text-muted">
    That is the correct outcome and it depends on something subtle: because
    <code>display</code> and <code>overlay</code> are still in the transition list, they
    still flip at the end — of a <code>.01ms</code> transition. The close still works. A
    modal that animated with JavaScript timers instead would need its own
    reduced-motion branch to avoid a dialog that never closes.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> sets <code>dialog::backdrop { display: none }</code>, so
    a dialog that happens to be open when the page is printed does not put a grey wash
    over the sheet. The dialog itself prints where it falls.
  </p>
  <p class="text-muted">
    Printing with a modal open is unusual enough that Deck does not try to do more. If a
    modal's contents are meant to be printable — a receipt, a ticket — render them into
    the page as well rather than relying on the dialog.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One modal, no layer needed */
<dialog class="modal" style="inline-size:min(48rem, calc(100vw - 2rem))">

@layer app.components {
  /* A wider default */
  .modal { inline-size: min(42rem, calc(100vw - var(--space-8))); }

  /* No blur behind, for a page with a busy background */
  .modal::backdrop { backdrop-filter: none; }
}') ?></code></pre>
  <p class="text-muted">
    If you replace the transition, keep <code>overlay</code> and <code>display</code> in
    the list with <code>allow-discrete</code>. Dropping them is what turns a smooth close
    into an instant disappearance, and it is not obvious from the symptom.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a message.</strong> A modal demands a response. Something the
      reader only needs to know is an <a href="alert.php"><code>.alert</code></a> in the
      page, or a <a href="toast.php"><code>.toast</code></a> if it reports something that
      just happened.
    </li>
    <li>
      <strong>Not for a long form.</strong> A form that scrolls inside a modal loses the
      reader's place and cannot be linked to or refreshed. Give it a page.
    </li>
    <li>
      <strong>Not for filters or settings on a phone.</strong> Use
      <a href="sheet.php"><code>.sheet</code></a>, which comes from the bottom and is
      reachable by thumb, or <a href="drawer.php"><code>.drawer</code></a> from the side.
    </li>
    <li>
      <strong>Not for a menu.</strong> Use <a href="menu.php"><code>.menu</code></a>,
      which is a popover: light-dismiss, no focus trap, no backdrop. A modal for a list
      of five actions is a blocking interruption for something that is not one.
    </li>
    <li>
      <strong>Not nested.</strong> A dialog opened from a dialog stacks two focus traps
      and two backdrops, and the reader has to escape twice. If a modal needs a modal, the
      first one should have been a page.
    </li>
    <li>
      <strong>Not with <code>open</code> in the markup.</strong>
      <code>&lt;dialog open&gt;</code> renders the dialog inline, outside the top layer,
      with no backdrop, no focus trap and no inertness — a box that looks like a modal and
      behaves like a <code>&lt;div&gt;</code>.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
