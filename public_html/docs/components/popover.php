<?php
declare(strict_types=1);

$page = [
    'path' => 'components/popover.php',
    'title' => 'Popover',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .pop is a card the reader can interact with: a title, body copy and actions, anchored to its trigger with CSS anchor positioning. Plus .anchor, the class that names an anchor pair.',
    'documents' => [
        'pop', 'pop-actions', 'pop-body', 'pop-title', 'anchor',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Popover</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Popover</h1>
  <p class="lede">
    <code>.pop</code> is what a <a href="tooltip.php">tooltip</a> becomes when it needs a
    heading, a paragraph and a button. It is a native popover — light dismiss, Escape and
    the top layer from the platform — anchored to its trigger by CSS, and unlike a tooltip
    the reader can move into it and click things.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for an explanation the reader might act on: what a field means and a link to
    the documentation, why a build failed and a button to retry, a definition with a
    "don't show again" control. The test is whether there is anything to interact with —
    if there is, a tooltip cannot hold it.
  </p>
  <?php
  docs_example(
      '<button class="btn" popovertarget="dx-pop">Why did this build fail?</button>' . "\n" .
      '<div class="pop" popover id="dx-pop" aria-labelledby="dx-pop-t">' . "\n" .
      '  <p class="pop-title" id="dx-pop-t">Missing environment variable</p>' . "\n" .
      '  <p class="pop-body">The build could not find <code>DATABASE_URL</code>. Add it in Settings and run the build again.</p>' . "\n" .
      '  <div class="pop-actions">' . "\n" .
      '    <button class="btn btn-sm btn-primary">Open settings</button>' . "\n" .
      '    <button class="btn btn-sm" popovertarget="dx-pop" popovertargetaction="hide">Dismiss</button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Click outside to dismiss, or use the button — which needs no JavaScript either',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>popovertargetaction="hide"</code> on a button inside the panel closes it. That
    is the declarative close, and it means a popover with a dismiss button needs no script
    at all.
  </p>
</section>

<section class="stack-3">
  <h2 id="anchor">.anchor and the naming problem</h2>
  <p>
    CSS anchor positioning connects two elements by name: the trigger declares
    <code>anchor-name</code>, the panel declares <code>position-anchor</code>, and the
    names must match. Every pair on a page needs a unique one.
  </p>
  <p>
    <code>.anchor</code> is the one-line class that does the first half:
  </p>
  <pre class="dx-code"><code><?= e('.anchor { anchor-name: var(--anchor); }') ?></code></pre>
  <p>
    So the pairing is <code>--anchor</code> set to the same value on both elements. You
    can write it yourself:
  </p>
  <pre class="dx-code"><code><?= e('<button class="btn anchor" style="--anchor:--pop-build" popovertarget="p1">Why?</button>
<div class="pop" popover id="p1" style="--anchor:--pop-build">…</div>') ?></code></pre>
  <p>
    Or let <code>deck.js</code> do it. It walks every <code>[popovertarget]</code>,
    generates a name, adds <code>.anchor</code> to the trigger and sets
    <code>--anchor</code> on both — skipping any panel that already has one, so a
    hand-written name always wins. That is why the example above needs neither the class
    nor the property.
  </p>
  <p class="dx-note text-muted">
    Generating the name server-side is the other good option, and the source comment
    recommends it for Keel: <code>style="--anchor:--pop-&lt;?= $id ?&gt;"</code>. It
    removes the JavaScript from the path entirely and guarantees uniqueness from data you
    already have.
  </p>
</section>

<section class="stack-3">
  <h2 id="positioning">Positioning</h2>
  <pre class="dx-code"><code><?= e('position-area: block-end;
position-try-fallbacks: flip-block, flip-inline, block-start span-inline-start;
justify-self: anchor-center;') ?></code></pre>
  <p>
    Below the trigger by preference, centred on it. If there is no room below it flips
    above; if it would overflow an edge it flips inline; and the last fallback pins it
    above and toward the starting edge, which is the option that fits in the tightest
    corner.
  </p>
  <p>
    Width is <code>min(20rem, calc(100vw - <?= e('1.5rem') ?>))</code> — capped for
    readability, and never wider than the viewport, so it does not need a mobile variant.
  </p>
  <p class="text-muted">
    All of this is inside <code>@supports (anchor-name: --a)</code>. Where the browser
    lacks anchor positioning the panel still opens as a popover in the top layer; it is
    simply centred by the browser's default popover placement instead of being attached to
    its trigger. That degrades to something usable rather than to something broken.
  </p>
</section>

<section class="stack-3">
  <h2 id="parts">Title, body and actions</h2>
  <p>
    Three parts, all optional, all plain: <code>.pop-title</code> is a weight change,
    <code>.pop-body</code> is smaller muted text, and <code>.pop-actions</code> is a flex
    row with a gap and a top margin. There is no header border and no footer band — a
    popover is small enough that regions would be more structure than content.
  </p>
  <?php
  docs_example(
      '<button class="btn btn-sm" popovertarget="dx-pop-min">What is a labor code?</button>' . "\n" .
      '<div class="pop" popover id="dx-pop-min" aria-labelledby="dx-pop-min-t">' . "\n" .
      '  <p class="pop-title" id="dx-pop-min-t">Labor code</p>' . "\n" .
      '  <p class="pop-body">The three-digit code a technician enters against a repair. It sets the hours billed.</p>' . "\n" .
      '</div>',
      'Title and body, no actions',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    These live in <code>src/25-anchor.css</code> and are not a component root — the
    extractor finds no <code>.pop</code> selector outside the
    <code>@supports</code> block to define it from — so there is no completeness check on
    this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--line', '--text', '--text-muted', '--text-sm', '--r-md', '--shadow-4', '--space-1', '--space-2', '--space-3', '--space-4', '--space-6', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The platform supplies the behaviour.</strong> Light dismiss, Escape,
      closing when another popover opens, and <code>aria-expanded</code> on the trigger
      are the browser's. The panel is in the top layer, so it escapes clipping ancestors.
    </li>
    <li>
      <strong>Focus is not moved into it.</strong> A popover does not trap or move focus.
      For a panel with actions in it, that means a keyboard user opens it and then has to
      tab forward to reach the buttons — which works, because the panel follows the
      trigger in the DOM, but only if you put it there. Keep the panel immediately after
      its trigger in the markup.
    </li>
    <li>
      <strong>Name it.</strong> <code>aria-labelledby</code> pointing at
      <code>.pop-title</code>, as every example here does.
    </li>
    <li>
      <strong>Do not give it <code>role="tooltip"</code>.</strong> A tooltip role tells a
      screen reader the content is a label and not interactive. Anything with a button in
      it is a dialog-like panel; leave the implicit grouping, or use
      <code>role="dialog"</code> if it genuinely blocks.
    </li>
    <li>
      <strong>It works on touch</strong>, unlike <a href="tooltip.php#css-tooltip">the CSS
      tooltip</a>, because it opens on click rather than hover. That alone is often the
      reason to choose it.
    </li>
    <li>
      <strong>Light dismiss can be surprising.</strong> Any outside click closes the
      panel, including a click on a form field the reader meant to fill in first. For a
      panel holding a small form, a <a href="modal.php">modal</a> is the safer shape.
    </li>
    <li>
      <strong>Contrast.</strong> Unlike a tooltip, a popover is
      <code>--surface</code> and <code>--text</code> — an ordinary page surface, so it
      follows the theme and inherits the page's contrast guarantees.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>position-area</code>, <code>justify-self: anchor-center</code> and the fallback
    keywords are all logical, and the panel's padding is symmetrical. The
    <code>block-start span-inline-start</code> fallback resolves to the correct corner in
    either direction without a second rule.
  </p>
  <?php
  docs_example(
      '<div dir="rtl">' . "\n" .
      '  <button class="btn" popovertarget="dx-pop-rtl">لماذا فشل البناء؟</button>' . "\n" .
      '  <div class="pop" popover id="dx-pop-rtl" aria-labelledby="dx-pop-rtl-t">' . "\n" .
      '    <p class="pop-title" id="dx-pop-rtl-t">متغير بيئة مفقود</p>' . "\n" .
      '    <p class="pop-body">لم يتم العثور على <code>DATABASE_URL</code>.</p>' . "\n" .
      '    <div class="pop-actions">' . "\n" .
      '      <button class="btn btn-sm btn-primary">الإعدادات</button>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The panel fades and rises four pixels over
    <?= e(api_token('--dur-2')['value'] ?? '200ms') ?>, with
    <code>overlay</code> and <code>display</code> in the transition so it stays rendered
    while it fades out. The global reset collapses it to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.pop</code> has no print rule and is not in the never-print list, so an open
    popover prints where it falls — over whatever it is anchored above. That is unlikely
    to come up, since printing with a popover open is rare, but it is worth knowing that
    Deck does not handle it.
  </p>
  <p class="text-muted">
    If an explanation matters on paper it belongs in the page, not in a panel that only
    exists while a reader is holding it open.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One popover, no layer needed */
<div class="pop" popover style="--anchor:--pop-x;inline-size:min(28rem, 92vw)">

@layer app.components {
  /* Prefer opening to the inline end rather than below */
  .pop-side {
    position-area: inline-end;
    position-try-fallbacks: flip-inline, block-end;
    align-self: anchor-center;
    justify-self: auto;
  }
}') ?></code></pre>
  <p class="text-muted">
    If you change the axis, change the centring with it:
    <code>justify-self: anchor-center</code> centres across the inline axis and
    <code>align-self</code> does the block axis. Leaving both set fights itself.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a label.</strong> A few words naming a control is
      <a href="tooltip.php"><code>.tooltip</code></a> or <code>.tip</code>. A popover is
      a card, and a card for two words is heavy.
    </li>
    <li>
      <strong>Not for a decision the reader must make.</strong> Light dismiss means an
      outside click discards it silently. Use <a href="modal.php"><code>.modal</code></a>,
      which blocks and has to be answered.
    </li>
    <li>
      <strong>Not for a form of any size.</strong> A stray click loses the input. A
      popover is for reading with an optional action, not for entering data.
    </li>
    <li>
      <strong>Not for a list of actions.</strong> Use <a href="menu.php"><code>.menu</code></a>,
      which is built for rows of items and sizes itself to its trigger.
    </li>
    <li>
      <strong>Not on hover.</strong> A popover opens on click. Wiring it to
      <code>pointerenter</code> gives you a panel that appears when the pointer passes
      over the trigger on the way somewhere else, and light dismiss will fight the
      reader trying to move into it.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
