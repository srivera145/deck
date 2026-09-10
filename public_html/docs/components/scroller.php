<?php
declare(strict_types=1);

$page = [
    'path' => 'components/scroller.php',
    'title' => 'Scroller',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .scroller keeps a row on one line and scrolls it sideways, with scroll snapping, a hidden scrollbar and a negative-margin bleed so cards run to the edge of the screen on a phone.',
    'documents' => [
        'scroller',
    ],
];

require __DIR__ . '/../_layout.php';

function demo_tiles(int $n = 8): string
{
    $names = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'Next week'];
    $out = '';
    for ($i = 0; $i < $n; $i++) {
        $out .= '  <div class="card" style="inline-size:12rem">' . "\n"
            . '    <div class="card-body stack-1">' . "\n"
            . '      <h3 class="card-title">' . $names[$i % count($names)] . '</h3>' . "\n"
            . '      <p class="text-muted text-sm">' . (3 + $i) . ' bookings</p>' . "\n"
            . '    </div>' . "\n"
            . '  </div>' . "\n";
    }
    return $out;
}
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Scroller</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Scroller</h1>
  <p class="lede">
    <code>.scroller</code> is the third answer to "what happens when a row runs out of
    room". A <a href="cluster.php">cluster</a> wraps, a <code>.bar</code> squashes, and a
    scroller keeps everything on one line and lets the reader scroll sideways — with
    snapping, a hidden scrollbar, and a bleed that runs the row to the edge of the
    screen.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it when there are more items than fit and they are all peers: a row of date
    tiles, category chips, recent files, a carousel of cards. It works best when the
    items are wide enough that a partial one is visible at the edge, because that
    sliver is the only thing telling the reader there is more.
  </p>
  <?php
  docs_example('<div class="scroller">' . "\n" . demo_tiles(8) . '</div>',
      'Drag or shift-scroll. Notice the item that is half-visible at the end.', 'stack');
  ?>
</section>

<section class="stack-6">
  <h2 id="how">What the eight declarations do</h2>

  <div class="stack-2">
    <h3 id="h-flex">A flex row whose items do not shrink</h3>
    <p>
      <code>.scroller &gt; *</code> gets <code>flex: 0 0 auto</code>. Without it the
      items would compress to fit rather than overflow, and there would be nothing to
      scroll — which is the mistake that makes a "horizontal scroller" quietly behave
      like a squashed bar.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="h-snap">Snapping, proximity rather than mandatory</h3>
    <p>
      <code>scroll-snap-type: x proximity</code> with
      <code>scroll-snap-align: start</code> on each child. Proximity means the row
      settles onto an item when the reader stops near one, and leaves them alone
      otherwise. <code>mandatory</code> would force a snap on every scroll, which fights
      a reader who is trying to move a small distance and can trap them if an item is
      taller than the viewport.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="h-contain">The page does not scroll with it</h3>
    <p>
      <code>overscroll-behavior-inline: contain</code> stops a horizontal scroll that
      reaches the end of the row from turning into a browser back-navigation or a scroll
      of the page behind it. On a trackpad, without this, flicking past the last card
      navigates away from the page.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="h-bleed">The bleed</h3>
    <pre class="dx-code"><code><?= e('margin-inline: calc(var(--space-gutter) * -1);
padding-inline: var(--space-gutter);') ?></code></pre>
    <p>
      A negative margin the width of the page gutter, cancelled by an equal padding. The
      row's scrolling area therefore reaches the edge of the screen while its content
      still lines up with everything else in the container. On a phone that is the
      difference between cards that look cut off and cards that look like they continue
      past the edge.
    </p>
    <p class="text-muted">
      It only works inside something that has the gutter — a <code>.container</code>. In
      a container with different padding the row will bleed by the wrong amount, and in
      an element with <code>overflow: hidden</code> it will be clipped.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="h-scrollbar">The hidden scrollbar</h3>
    <p>
      <code>scrollbar-width: none</code> and a
      <code>::-webkit-scrollbar { display: none }</code>. This is the most
      questionable line in the component, and it is discussed properly under
      <a href="#accessibility">Accessibility</a> — hiding the scrollbar removes the only
      persistent signal that the row scrolls at all.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="gap">Gap</h2>
  <p>
    Same <code>--gap</code> as the other primitives, defaulting to
    <?= e(api_token('--space-3')['value'] ?? '.75rem') ?>, so the
    <a href="stack.php#scale">spacing scale</a> works here too.
  </p>
  <?php
  docs_example('<div class="scroller stack-6">' . "\n" . demo_tiles(6) . '</div>',
      '.stack-6 on a scroller — the step classes only set --gap', 'stack');
  ?>
  <p class="text-muted">
    <code>padding-block-end: <?= e(api_token('--space-2')['value'] ?? '.5rem') ?></code>
    is set as well, leaving room for a scrollbar on platforms that show one over the
    content rather than beside it.
  </p>
</section>

<section class="stack-3">
  <h2 id="chips">A row of chips</h2>
  <p>
    The other common use. Here the items are small, so the sliver at the edge is less
    obvious and the case for a visible affordance is stronger.
  </p>
  <?php
  docs_example(
      '<div class="scroller cluster-tight">' . "\n" .
      '  <span class="chip">All</span>' . "\n" .
      '  <span class="chip">Overdue</span>' . "\n" .
      '  <span class="chip">This week</span>' . "\n" .
      '  <span class="chip">Unassigned</span>' . "\n" .
      '  <span class="chip">Archived</span>' . "\n" .
      '  <span class="chip">Drafts</span>' . "\n" .
      '  <span class="chip">Shared with me</span>' . "\n" .
      '</div>',
      'Filters that stay on one line',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <code>.cluster-tight</code> works on a scroller because both read
    <code>--gap</code>. It is also a good illustration of the naming muddle: the class is
    called <em>cluster</em>-tight and there is no cluster here.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.scroller</code> is a single class rather than a component root — the
    extractor finds no <code>.scroller-*</code> members — so there is no completeness
    check on this page. The rules that shape it are
    <code>.scroller &gt; *</code> and <code>.scroller::-webkit-scrollbar</code>, neither
    of which is a class of its own.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--space-2', '--space-3', '--space-gutter']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The hidden scrollbar is a real cost.</strong> A scrollbar is the only
      persistent, universal indication that a region scrolls. Hiding it means the
      affordance is now the partially-visible item at the edge, and if your items happen
      to fit the width exactly there is no affordance at all. Deck hides it because a
      native scrollbar under a row of cards looks like a mistake on desktop; that is an
      aesthetic decision overriding a usability one, and it should be made knowingly.
    </li>
    <li>
      <strong>A scroll container needs to be focusable to be keyboard-scrollable.</strong>
      <code>.scroller</code> sets no <code>tabindex</code>, so in Chromium a keyboard-only
      user cannot scroll it unless it contains something focusable. If your items are
      links or buttons, focus moves into them and the row scrolls to follow — that case
      works. If the items are not interactive, <strong>add
      <code>tabindex="0"</code></strong> and a <code>role="region"</code> with an
      <code>aria-label</code>. This is the same defect as
      <a href="table.php#accessibility"><code>.table-wrap</code></a>, and it is recorded
      in <code>FINDINGS.md</code>.
    </li>
    <li>
      <strong>Do not add <code>aria-hidden</code> to the off-screen items.</strong> They
      are scrolled, not hidden. Everything in the row is in the accessibility tree and in
      the tab order, which is correct.
    </li>
    <li>
      <strong>Snapping and reduced motion.</strong> Snapping is positional rather than
      animated, so it is unaffected by <code>prefers-reduced-motion</code> — but
      <code>scroll-behavior: smooth</code>, if you add it, is affected, and the global
      reset turns it off.
    </li>
    <li>
      <strong>Proximity snapping is the accessible choice.</strong>
      <code>mandatory</code> can prevent a reader from resting between items, and with a
      zoomed page an item wider than the viewport can become impossible to scroll past.
    </li>
    <li>
      <strong>The bleed does not break reflow.</strong> The negative margin is cancelled
      by an equal padding, so the row never makes the page wider than the viewport — the
      overflow is inside the scroller, which is the point.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>margin-inline</code>, <code>padding-inline</code> and
    <code>overscroll-behavior-inline</code> are all logical, and the browser reverses the
    scroll direction of an RTL container itself, so the row scrolls right-to-left and
    starts at the right edge with no extra rules.
  </p>
  <p class="text-muted">
    <code>scroll-snap-align: start</code> means the starting edge, not the left one, so
    it also follows the direction. This is the reason it is <code>start</code> and not
    <code>left</code>.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="scroller">' . "\n" .
      '  <div class="card" style="inline-size:10rem"><div class="card-body"><h3 class="card-title">الأحد</h3></div></div>' . "\n" .
      '  <div class="card" style="inline-size:10rem"><div class="card-body"><h3 class="card-title">الاثنين</h3></div></div>' . "\n" .
      '  <div class="card" style="inline-size:10rem"><div class="card-body"><h3 class="card-title">الثلاثاء</h3></div></div>' . "\n" .
      '  <div class="card" style="inline-size:10rem"><div class="card-body"><h3 class="card-title">الأربعاء</h3></div></div>' . "\n" .
      '  <div class="card" style="inline-size:10rem"><div class="card-body"><h3 class="card-title">الخميس</h3></div></div>' . "\n" .
      '</div>',
      'Starts at the right and scrolls the other way',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing in <code>.scroller</code> animates. Scroll snapping is a positioning rule
    rather than a transition, so it is not something
    <code>prefers-reduced-motion</code> switches off — and it should not be, since a
    reader who wants less motion still wants the row to line up.
  </p>
  <p class="text-muted">
    If you add <code>scroll-behavior: smooth</code> or drive the scroller from
    JavaScript, that <em>is</em> motion. Deck's carousel checks
    <code>matchMedia('(prefers-reduced-motion: reduce)')</code> and uses
    <code>behavior: 'auto'</code> when it matches; do the same.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Deck's print stylesheet has no rule for <code>.scroller</code>, which means a printed
    scroller shows only what was on screen and silently drops the rest. <strong>That is a
    gap</strong>, not a decision — the fix is
    <code>@media print { .scroller { display: block; overflow: visible; margin-inline: 0
    } }</code> so the items stack and all of them print. Recorded in
    <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Give the scrollbar back */
  .scroller { scrollbar-width: thin; }
  .scroller::-webkit-scrollbar { display: block; block-size: 8px; }

  /* Snap to the middle rather than the start */
  .scroller > * { scroll-snap-align: center; }

  /* No bleed, for a scroller that is not inside a .container */
  .scroller-flush { margin-inline: 0; padding-inline: 0; }
}') ?></code></pre>
  <p>
    <code>scroll-snap-align: center</code> is worth knowing about for a carousel of full
    -width items, where centring reads better than aligning to the starting edge.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not when the reader must see everything.</strong> Horizontal scrolling
      hides content behind a gesture many readers never make. If all the items matter
      equally, use <code>.grid</code> and let them wrap onto several rows.
    </li>
    <li>
      <strong>Not for primary navigation.</strong> A scrolling row of nav links puts
      destinations off-screen with no indication they exist. Use a
      <code>.bar</code> with an overflow menu, or a <code>.drawer</code>.
    </li>
    <li>
      <strong>Not for a handful of items.</strong> Four chips that fit on one line gain
      nothing from a scroller and lose the wrapping they would have had at 400% zoom. Use
      <code>.cluster</code>.
    </li>
    <li>
      <strong>Not for a real carousel.</strong> If you need previous and next buttons,
      dot indicators, autoplay or a slide-changed event, use
      <code>.carousel</code>, which has all of them and handles the keyboard. A scroller
      is the layout underneath, not the component.
    </li>
    <li>
      <strong>Not outside a <code>.container</code></strong>, unless you override the
      bleed. The negative margin assumes the page gutter is its parent's padding, and
      without that it pulls the row out of alignment or gets clipped.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
