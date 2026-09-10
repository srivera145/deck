<?php
declare(strict_types=1);

$page = [
    'path' => 'components/toast.php',
    'title' => 'Toast',
    'level' => 'Intermediate',
    'description' => 'Deck\'s toasts are a stack rather than a list: collapsed behind the newest, fanning out on hover, with timers that pause when the reader looks at them. Driven by Deck.toast() rather than written by hand.',
    'documents' => [
        'toast', 'toast-action', 'toast-actions', 'toast-bad', 'toast-close',
        'toast-good', 'toast-icon', 'toast-info', 'toast-loading', 'toast-main',
        'toast-region', 'toast-region-center', 'toast-region-stacked', 'toast-region-start',
        'toast-region-top', 'toast-text', 'toast-timer', 'toast-title', 'toast-undo',
        'toast-warn', 'is-dragging', 'is-leaving',
    ],

    'component' => 'toast',
    'accounts' => [
        '13-toasts.css' => 'documented: the region and its placements, the toast, its kinds, the timer, the stack and the leaving state',
        '99-print.css'  => 'documented: the toast region never prints — the Printing section',
        '19-logical.css' => 'documented: the timer bar\'s transform-origin is flipped under dir="rtl" — the Right to left section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Toast</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Toast</h1>
  <p class="lede">
    A toast reports something that has already happened. Deck's are a
    <strong>stack</strong> rather than a list: new ones arrive at the front, older ones
    collapse behind, and hovering the stack fans them out and pauses every timer in it.
    You almost never write the markup — <code>Deck.toast()</code> builds it.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a toast for the result of something the reader just did, when they do not need to
    act on it: saved, sent, copied, failed. It is transient and it does not block, which
    also means it is the wrong place for anything they must read.
  </p>
  <pre class="dx-code"><code><?= e('Deck.toast(\'Order #1042 shipped\');

Deck.toast({
  title: \'Project archived\',
  text: \'You can undo this.\',
  kind: \'warn\',
  duration: 8000,
  actions: [{ label: \'Undo\', onClick: () => restore() }],
});') ?></code></pre>
  <p class="text-muted">
    A string is shorthand for <code>{ title }</code>. The defaults are
    <code>duration: 5000</code>, <code>dismissible: true</code>, no kind and no actions.
  </p>
</section>

<section class="stack-3">
  <h2 id="anatomy">Anatomy</h2>
  <p>
    The generated markup, so you can recognise the parts and restyle them. This is
    rendered here as static markup rather than through the API, so that it holds still to
    be looked at.
  </p>
  <?php
  docs_example(
      '<div class="toast toast-good" role="status" style="max-inline-size:23rem">' . "\n" .
      '  <svg class="icon toast-icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check-circle"></use></svg>' . "\n" .
      '  <div class="toast-main">' . "\n" .
      '    <div class="toast-title">Export finished</div>' . "\n" .
      '    <div class="toast-text">2,481 rows written to invoices-2026-03.csv.</div>' . "\n" .
      '    <div class="toast-actions">' . "\n" .
      '      <button class="toast-action">Download</button>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '  <button class="toast-close" aria-label="Dismiss">' . "\n" .
      '    <svg class="icon icon-sm" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#x"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'Icon, main, actions, close — and a timer bar when duration is not 0',
      'stack'
  );
  ?>
  <p class="text-muted">
    The accent colour is one custom property. <code>--toast-accent</code> paints the
    three-pixel edge on the starting side, the icon and the timer bar, so a kind class is
    a single declaration.
  </p>
</section>

<section class="stack-3">
  <h2 id="kinds">Kinds</h2>
  <?php
  docs_example(
      '<div class="stack-2" style="max-inline-size:23rem">' . "\n" .
      '  <div class="toast toast-good" role="status"><div class="toast-main"><div class="toast-title">Good</div></div></div>' . "\n" .
      '  <div class="toast toast-warn" role="status"><div class="toast-main"><div class="toast-title">Warn</div></div></div>' . "\n" .
      '  <div class="toast toast-bad" role="alert"><div class="toast-main"><div class="toast-title">Bad</div></div></div>' . "\n" .
      '  <div class="toast toast-info" role="status"><div class="toast-main"><div class="toast-title">Info</div></div></div>' . "\n" .
      '  <div class="toast toast-undo" role="status"><div class="toast-main"><div class="toast-title">Undo</div></div>' . "\n" .
      '    <div class="toast-actions"><button class="toast-action">Undo</button></div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Five accents. Note the role changes with the kind.',
      'stack'
  );
  ?>
  <p>
    <code>.toast-loading</code> is the sixth: the same brand accent, but
    <code>Deck.toast</code> renders a <code>.spinner</code> in place of an icon. Pair it
    with <code>duration: 0</code> so it stays until you replace it.
  </p>
  <pre class="dx-code"><code><?= e('const t = Deck.toast({ kind: \'loading\', title: \'Submitting…\', duration: 0 });
await save();
t.update({ kind: \'good\', title: \'Submitted\', duration: 4000 });') ?></code></pre>
  <p class="text-muted">
    <code>.toast-undo</code> is a presentational variant rather than a kind: a neutral
    accent with the action in the full text colour, for the "archived — undo?" pattern
    where the action matters more than the status.
  </p>
</section>

<section class="stack-3">
  <h2 id="stack">The stack</h2>
  <p>
    <code>.toast-region-stacked</code> collapses older toasts behind the newest one,
    scaled down and offset, so five notifications do not become five cards down the side
    of the screen. Hovering or focusing the region fans them back out.
  </p>
  <p>
    The index each toast needs for its offset comes from
    <code>sibling-index()</code> where the browser has it, with an
    <code>:nth-child</code> ladder as the fallback — which is why the source has ten
    nearly identical lines after the modern one.
  </p>
  <p class="dx-note text-muted">
    The whole region is <code>pointer-events: none</code> and each toast turns it back on
    with <code>pointer-events: auto</code>. Without that, the invisible region would sit
    over the bottom corner of the page and swallow clicks on whatever is underneath it.
  </p>
</section>

<section class="stack-3">
  <h2 id="timer">Timers that pause</h2>
  <p>
    <code>.toast-timer</code> is a two-pixel bar that scales from full to zero over
    <code>--toast-duration</code>, set from the <code>duration</code> option. It is a CSS
    animation rather than a JavaScript countdown, which buys one thing that matters:
  </p>
  <pre class="dx-code"><code><?= e('.toast-region:is(:hover, :focus-within) .toast-timer { animation-play-state: paused; }') ?></code></pre>
  <p>
    Hovering the stack — or tabbing into it — pauses every timer in it. A reader reaching
    for the Undo button does not have it disappear as they arrive. That is one
    declaration, and it would be a scheduler with pause and resume in JavaScript.
  </p>
</section>

<section class="stack-3">
  <h2 id="placement">Where the region sits</h2>
  <p>
    Bottom end by default, which on a phone is where the thumb is. Four modifiers move it,
    and <code>.toast-region-top</code> also reverses the flex direction so the stack still
    grows away from its edge.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Toast region placements</caption>
      <thead><tr><th scope="col">Class</th><th scope="col">Position</th></tr></thead>
      <tbody>
        <tr><th scope="row" data-label="Class"><code>.toast-region</code></th><td data-label="Position">Bottom, inline end</td></tr>
        <tr><th scope="row" data-label="Class"><code>.toast-region-start</code></th><td data-label="Position">Bottom, inline start</td></tr>
        <tr><th scope="row" data-label="Class"><code>.toast-region-center</code></th><td data-label="Position">Bottom, centred</td></tr>
        <tr><th scope="row" data-label="Class"><code>.toast-region-top</code></th><td data-label="Position">Top, growing downward</td></tr>
        <tr><th scope="row" data-label="Class"><code>.toast-region-stacked</code></th><td data-label="Position">Any of the above, collapsed into a stack</td></tr>
      </tbody>
    </table>
  </div>
  <p class="text-muted">
    Two details handled for you: <code>env(safe-area-inset-bottom)</code> keeps the stack
    clear of a phone's home indicator, and
    <code>body:has(.tabbar) .toast-region</code> lifts it by
    <?= e(api_token('--tap')['value'] ?? '44px') ?> when the page has a bottom tab bar —
    so a toast never covers the navigation. Above <code>48rem</code>, where the tab bar
    is not shown, that offset is removed again.
  </p>
</section>

<section class="stack-3">
  <h2 id="states">is-leaving and is-dragging</h2>
  <p>
    Both are set by <code>deck.js</code> at runtime and are <strong>internal</strong> —
    style them if you like, but do not write them. <code>.is-leaving</code> plays the exit
    animation and collapses the toast's margin so the stack closes up;
    <code>.is-dragging</code> disables the transition while a swipe follows the pointer,
    so the toast tracks the finger exactly rather than lagging behind it.
  </p>
  <p class="text-muted">
    <code>touch-action: pan-y</code> on the toast is what lets a horizontal swipe be
    captured for dismissal while a vertical drag still scrolls the page.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/13-toasts.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--surface-hover', '--line', '--text', '--text-muted', '--text-faint', '--text-sm', '--brand', '--brand-500', '--good-500', '--warn-500', '--bad-500', '--ink-400', '--ink-500', '--r-md', '--r-full', '--shadow-4', '--tap', '--space-1', '--space-2', '--space-3', '--space-4', '--space-6', '--z-toast', '--dur-2', '--dur-3']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The region is a labelled landmark.</strong> <code>Deck.toast</code> sets
      <code>role="region"</code> and <code>aria-label="Notifications"</code> on it when it
      mounts, so a screen-reader user can navigate to the stack deliberately.
    </li>
    <li>
      <strong>The role depends on the kind.</strong> <code>kind: 'bad'</code> gets
      <code>role="alert"</code>, which interrupts; everything else gets
      <code>role="status"</code>, which waits for a pause. That is the right split — a
      failure should interrupt, "Copied" should not.
    </li>
    <li>
      <strong>Auto-dismiss is a genuine accessibility problem.</strong> WCAG asks that
      content which disappears on a timer can be paused, stopped or extended. Deck's
      pause-on-hover-or-focus satisfies that for a reader who can reach the stack — but a
      screen-reader user who has not focused it still has five seconds. <strong>For
      anything with an action in it, pass <code>duration: 0</code></strong> and let the
      reader dismiss it.
    </li>
    <li>
      <strong>Toasts are far from the focus point.</strong> A message in the bottom corner
      is easy to miss if the reader is looking at the top of a form. For a result they must
      see, put an <a href="alert.php"><code>.alert</code></a> in the page as well.
    </li>
    <li>
      <strong>Swipe-to-dismiss is pointer-only</strong>, but the close button and the
      timer both work without it, so nothing is unreachable — unlike
      <a href="list.php#reorder">drag-to-reorder</a>, where the gesture is the only path.
    </li>
    <li>
      <strong>The close button has a label.</strong>
      <code>aria-label="Dismiss"</code>, set by the generator. At 26px it is under the
      touch minimum, which is the fourth such place in Deck — mitigated here by the whole
      toast being swipeable.
    </li>
    <li>
      <strong>The queue caps at eight.</strong> Older toasts are dismissed automatically
      past that, so a burst of activity cannot fill the screen or the accessibility tree.
    </li>
    <li>
      <strong>Text is escaped.</strong> <code>Deck.toast</code> runs
      <code>escapeHTML</code> over the title and text, so a message built from user input
      cannot inject markup.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The region's placement uses <code>inset-inline</code>, the accent edge is
    <code>inset-inline-start</code>, and the close button's negative offset is
    <code>margin-inline-end</code> — so the default bottom-end region moves to the bottom
    left under <code>dir="rtl"</code> and the accent stripe moves to the right side of
    each toast. All of that is free.
  </p>
  <p>
    One thing is not. The timer bar shrinks with
    <code>transform-origin: left center</code>, and <code>transform-origin</code> takes
    physical keywords with no logical equivalent — so a bar that should drain toward the
    starting edge would drain the wrong way. <code>src/19-logical.css</code> corrects it,
    alongside the chart bars and progress fills that have the same problem:
  </p>
  <pre class="dx-code"><code><?= e('[dir="rtl"] :is(.chart-bar-fill, .progress::-webkit-progress-value, .toast-timer) {
  transform-origin: right center;
}') ?></code></pre>
  <p class="text-muted">
    This is the third property in Deck with no logical form, after
    <a href="select.php#rtl"><code>background-position</code></a> and the
    <a href="drawer.php#rtl">drawer's translate</a>. They are all collected in one file
    rather than scattered as overrides beside each component.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="toast toast-good" role="status" style="max-inline-size:23rem">' . "\n" .
      '  <svg class="icon toast-icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check-circle"></use></svg>' . "\n" .
      '  <div class="toast-main">' . "\n" .
      '    <div class="toast-title">اكتمل التصدير</div>' . "\n" .
      '    <div class="toast-text">تمت كتابة ٢٤٨١ صفًا.</div>' . "\n" .
      '  </div>' . "\n" .
      '  <button class="toast-close" aria-label="إغلاق">' . "\n" .
      '    <svg class="icon icon-sm" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#x"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'The accent edge follows the writing direction',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Toasts animate in, animate out, and move as the stack reflows. Under
    <code>prefers-reduced-motion: reduce</code> the global reset collapses every
    transition to <code>.01ms</code> and every animation to a single iteration, so they
    appear and disappear in place.
  </p>
  <p class="dx-note text-muted">
    The timer bar is the interesting case. It is an <em>animation</em> rather than a
    transition, and the reset sets <code>animation-duration: .01ms</code> — which would
    make the bar vanish immediately. It still works because the bar is decoration: the
    dismissal is a JavaScript timer, not the animation's end. A reader with reduced motion
    loses the visual countdown and keeps the behaviour, which is the right way round.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> lists <code>.toast-region</code> among the chrome that
    never prints, with the tab bar and the skip link. A toast reports something transient;
    printing one would be printing a moment.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* Choose the placement once, before the first toast */
Deck.toasts.stacked = true;

@layer app.components {
  /* A wider stack on desktop */
  @media (min-width: 64rem) {
    .toast-region { inline-size: min(28rem, calc(100vw - var(--space-6))); }
  }

  /* A kind of your own — one declaration */
  .toast-brand { --toast-accent: var(--brand-600); }
}') ?></code></pre>
  <p class="text-muted">
    A custom kind needs the class on the toast, which means passing
    <code>kind: 'brand'</code> — the generator builds the class name as
    <code>toast-</code> plus the kind, so any suffix works as long as you have styled it.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for anything the reader must act on.</strong> It disappears. Use an
      <a href="alert.php"><code>.alert</code></a> in the page, or a
      <a href="modal.php"><code>.modal</code></a> if it blocks.
    </li>
    <li>
      <strong>Not for form validation.</strong> The error belongs beside the field, in an
      <code>.error</code> with <code>aria-describedby</code>. A toast tells the reader
      something is wrong and then takes away the only description of it.
    </li>
    <li>
      <strong>Not for a question.</strong> "Are you sure?" needs an answer, and a toast
      can be missed entirely. That is a modal.
    </li>
    <li>
      <strong>Not for progress that lasts.</strong> A loading toast is right for a few
      seconds. For a job that runs for minutes, put a
      <code>.progress</code> in the page where the reader can find it again.
    </li>
    <li>
      <strong>Not several at once, deliberately.</strong> The stack handles a burst
      gracefully, but three toasts for one action is three interruptions. Summarise
      instead.
    </li>
    <li>
      <strong>Not with an action and a short duration.</strong> If there is an Undo
      button, use <code>duration: 0</code>. A five-second window to notice, read and reach
      a button is not a window at all for many readers.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
