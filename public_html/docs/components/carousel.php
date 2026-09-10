<?php
declare(strict_types=1);

$page = [
    'path' => 'components/carousel.php',
    'title' => 'Carousel',
    'level' => 'Advanced',
    'description' => 'Deck\'s .carousel is a scroll-snap track with dots and arrows — and where ::scroll-marker exists the browser builds both, so the JavaScript steps aside and the markup does not change.',
    'documents' => [
        'carousel', 'carousel-arrow', 'carousel-caption', 'carousel-dot', 'carousel-dots',
        'carousel-multi', 'carousel-next', 'carousel-peek', 'carousel-prev', 'carousel-slide',
        'carousel-track',
    ],

    'component' => 'carousel',
    'accounts' => [
        '22-nav.css'   => 'documented: the track, slides, arrows, dots, the peek and multi variants, and the native ::scroll-marker path',
        '24-media.css' => 'documented: the arrows and dots are dropped from print — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';

function demo_slides(int $n = 4): string
{
    $tints = ['%23c7d6d4', '%23d8cfc4', '%23c4cdd8', '%23d3d8c4'];
    $out = '';
    for ($i = 0; $i < $n; $i++) {
        $out .= '    <div class="carousel-slide">' . "\n"
            . '      <img alt="Slide ' . ($i + 1) . '" src="data:image/svg+xml;utf8,'
            . '%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22640%22 height=%22360%22%3E'
            . '%3Crect width=%22640%22 height=%22360%22 fill=%22' . $tints[$i % 4] . '%22/%3E%3C/svg%3E">' . "\n"
            . '    </div>' . "\n";
    }
    return $out;
}
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Carousel</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Carousel</h1>
  <p class="lede">
    <code>.carousel</code> is a scroll-snap track with dots and arrows over it. What makes
    it interesting is that it has two implementations: a JavaScript one, and a native one
    built from <code>::scroll-marker</code> and <code>::scroll-button</code> — and where
    the browser has the native pseudo-elements, <strong>the markup does not change</strong>
    and the script steps aside.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for a set of peer items where seeing them all at once is not necessary and
    space is short: product photos, testimonials, a gallery. Be honest about the cost —
    carousels hide most of their content behind a gesture, and most readers never move
    past the first slide.
  </p>
  <?php
  docs_example(
      '<div class="carousel" data-deck-carousel>' . "\n" .
      '  <div class="carousel-track">' . "\n" . demo_slides(4) .
      '  </div>' . "\n" .
      '  <button class="carousel-arrow carousel-prev" aria-label="Previous slide">' . "\n" .
      '    <svg class="icon mirror-rtl" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-left"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '  <button class="carousel-arrow carousel-next" aria-label="Next slide">' . "\n" .
      '    <svg class="icon mirror-rtl" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-right"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '  <div class="carousel-dots"></div>' . "\n" .
      '</div>',
      'Drag it, or use the arrows. The dots are built for you.',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.carousel-dots</code> is left empty in the markup —
    <code>deck-extras.js</code> fills it with one button per slide. On a browser with
    <code>::scroll-marker</code> it stays empty and hidden, because the browser draws the
    dots instead.
  </p>
</section>

<section class="stack-6">
  <h2 id="native">Two implementations, one markup</h2>
  <p>
    This is the most interesting thing in the component, and it is worth reading the
    source comment, which says it plainly:
  </p>
  <blockquote class="dx-note text-muted">
    <code>::scroll-marker</code> gives every slide its own dot and
    <code>::scroll-button</code> gives the track its arrows, with the active state, the
    disabled state, the click handling and the arrow-key focus order all done by the
    browser.
  </blockquote>

  <div class="stack-2">
    <h3 id="n-native">Where the browser has them</h3>
    <pre class="dx-code"><code><?= e('@supports selector(::scroll-marker) {
  .carousel-dots, .carousel-arrow { display: none; }
  .carousel-track { scroll-marker-group: after; }
  .carousel-slide::scroll-marker { content: ""; … }
}') ?></code></pre>
    <p>
      Deck's own dots and arrows are hidden, the track grows a marker group, and every
      slide contributes a dot. The active dot, the disabled arrow at each end, the click
      handling and the arrow-key focus order are all the browser's — nothing is wired,
      nothing is measured, and there is no scroll listener.
    </p>
    <p class="text-muted">
      The scroll buttons take <strong>logical</strong> keywords —
      <code>inline-start</code> and <code>inline-end</code> rather than left and right —
      so an RTL carousel flips them with no rule in <code>deck.rtl</code> at all.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="n-js">Where it does not</h3>
    <p>
      <code>deck-extras.js</code> builds the dots, wires the arrows, tracks the current
      slide by measuring which one is nearest the centre, sets
      <code>aria-current</code> on the dots and <code>disabled</code> on the end arrows,
      and fires a <code>deck:slide</code> event. It also checks
      <code>matchMedia('(prefers-reduced-motion: reduce)')</code> and scrolls with
      <code>behavior: 'auto'</code> when it matches.
    </p>
    <p>
      The point is that the author writes the same markup either way. There is no
      <code>@supports</code> in the HTML, no feature detection in the page, and no
      difference in what a reader sees.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="variants">Peek and multi</h2>
  <p>
    Slides are <code>flex-basis: 100%</code> by default — one at a time.
    <code>.carousel-peek</code> makes them 86%, so the next slide is partly visible, which
    is the cue that tells a reader there is more. <code>.carousel-multi</code> shows three
    at a time above <code>40rem</code> and 78% below it.
  </p>
  <?php
  docs_example(
      '<div class="carousel carousel-peek" data-deck-carousel>' . "\n" .
      '  <div class="carousel-track">' . "\n" . demo_slides(4) .
      '  </div>' . "\n" .
      '  <div class="carousel-dots"></div>' . "\n" .
      '</div>',
      '.carousel-peek — the sliver at the edge is the affordance',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Peek is worth preferring over a plain carousel for exactly the reason
    <a href="scroller.php#accessibility">the scroller page</a> gives: the scrollbar is
    hidden, so the partially visible slide is the only persistent signal that the track
    scrolls at all.
  </p>
</section>

<section class="stack-3">
  <h2 id="captions">Captions</h2>
  <p>
    <code>.carousel-caption</code> overlays text on a slide. It reads the shared
    <code>--scrim</code> token — an eased gradient rather than a flat wash, so it only
    darkens where the text sits instead of dulling the whole image — and the slide is its
    containing block, so each caption stays on its own slide.
  </p>
  <?php
  docs_example(
      '<div class="carousel" data-deck-carousel>' . "\n" .
      '  <div class="carousel-track">' . "\n" .
      '    <div class="carousel-slide">' . "\n" .
      '      <img alt="A quiet street" src="data:image/svg+xml;utf8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22640%22 height=%22360%22%3E%3Crect width=%22640%22 height=%22360%22 fill=%22%238fa8a4%22/%3E%3C/svg%3E">' . "\n" .
      '      <div class="carousel-caption">Nightly export, 04:12 UTC</div>' . "\n" .
      '    </div>' . "\n" .
      '    <div class="carousel-slide">' . "\n" .
      '      <img alt="A second view" src="data:image/svg+xml;utf8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22640%22 height=%22360%22%3E%3Crect width=%22640%22 height=%22360%22 fill=%22%23a48f9a%22/%3E%3C/svg%3E">' . "\n" .
      '      <div class="carousel-caption">Retried twice before succeeding</div>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="carousel-dots"></div>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="autoplay">Autoplay</h2>
  <p>
    <code>data-autoplay="5000"</code> advances the carousel every five seconds. It pauses
    on <code>pointerenter</code>, on <code>focusin</code>, and when the tab is hidden —
    and it does not start at all if <code>prefers-reduced-motion: reduce</code> matches.
  </p>
  <p class="dx-note text-muted">
    Those four conditions are the minimum for autoplay to be defensible, and even then it
    is worth asking whether it should exist. Content that moves on its own is content a
    reader has to chase, and WCAG asks that anything auto-updating for more than five
    seconds can be paused. Deck's pause triggers are hover and focus — a reader who is
    simply reading, with the pointer elsewhere, has no way to stop it.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/22-nav.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--brand-600', '--ink-300', '--r-md', '--r-full', '--shadow-2', '--space-2', '--space-3', '--space-4', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The native path is more accessible than the scripted one</strong>, which is
      the argument for it. <code>::scroll-marker</code> dots are real focusable controls
      with the browser's own arrow-key handling and focus order — behaviour that a
      hand-built dot row has to reimplement and usually gets partly wrong.
    </li>
    <li>
      <strong>Arrows are hidden on touch.</strong>
      <code>@media (pointer: coarse) { .carousel-arrow { display: none } }</code> — a
      phone reader swipes instead. That is right for a pointer, and it means the dots are
      the only control left, so do not hide those too.
    </li>
    <li>
      <strong>Dots have a bigger target than they look.</strong>
      <code>.carousel-dot::before</code> is a 32px invisible square centred on a 7px dot.
      Still under <?= e(api_token('--tap')['value'] ?? '44px') ?>, but far better than the
      visible size — and the technique is worth copying anywhere a control is deliberately
      small.
    </li>
    <li>
      <strong>The current slide is announced through <code>aria-current</code></strong> on
      the dot, which is also what styles it — the same attribute doing both jobs, as with
      <a href="tabs.php">tabs</a>.
    </li>
    <li>
      <strong>Slides are not hidden when off-screen</strong>, and should not be. They are
      scrolled, not hidden: everything is in the accessibility tree and the tab order,
      which is what lets a keyboard user reach slide four without pressing an arrow.
    </li>
    <li>
      <strong>Give the carousel a label.</strong> <code>aria-label</code> on the
      <code>.carousel</code>, and consider
      <code>aria-roledescription="carousel"</code> — though be aware that the roledescription
      is announced on every interaction and can become noise.
    </li>
    <li>
      <strong>Autoplay needs a pause control</strong> to satisfy WCAG properly. Hover and
      focus are not enough for a reader who is not touching anything. If you use
      autoplay, add a visible pause button.
    </li>
    <li>
      <strong>Images need real alt text.</strong> A carousel of decorative images is
      decoration; a carousel of product photos is content, and each one needs describing.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The track is a flex row that the browser reverses under <code>dir="rtl"</code>, and
    the arrows are positioned with <code>inset-inline-start</code> and
    <code>inset-inline-end</code>, so they swap sides on their own. The chevron glyphs
    need <code>.mirror-rtl</code> — a chevron is directional, and mirroring it is the one
    thing the layout cannot do for you.
  </p>
  <p>
    <code>deck-extras.js</code> checks
    <code>getComputedStyle(track).direction === 'rtl'</code> and swaps which arrow key
    moves which way, for the same reason
    <a href="tabs.php#keyboard">a tablist has to</a>.
  </p>
  <p class="text-muted">
    On the native path none of that applies: <code>::scroll-button(inline-start)</code>
    resolves to the correct edge by itself, which is why the source comment calls it out.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Scrolling between slides is smooth by default and <code>auto</code> when
    <code>prefers-reduced-motion: reduce</code> matches — checked in JavaScript, because
    the scroll is initiated there. Autoplay does not start at all under the same
    condition.
  </p>
  <p>
    The arrows also scale slightly on hover; that transition is collapsed to
    <code>.01ms</code> by the global reset.
  </p>
  <p class="dx-note text-muted">
    A carousel is one of the few components where reduced motion changes
    <em>behaviour</em> rather than only duration. Autoplay is motion the reader did not
    ask for, so switching it off entirely is the correct response — not merely making it
    faster.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/24-media.css</code> drops <code>.carousel-arrow</code> and
    <code>.carousel-dots</code> from print — controls that cannot be operated on paper.
    The track itself is not unwound, so a printed carousel shows the slide that happened
    to be in view and drops the rest, the same gap as
    <a href="scroller.php#print"><code>.scroller</code></a> and with the same fix.
  </p>
  <p class="text-muted">
    Recorded in <code>FINDINGS.md</code> alongside the scroller, since it is one rule
    covering both.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Two slides at a time rather than three */
  .carousel-multi .carousel-slide { flex-basis: calc((100% - var(--space-4)) / 2); }

  /* Keep the arrows on touch */
  @media (pointer: coarse) { .carousel-arrow { display: flex; } }

  /* Style the native dots, where they exist */
  .carousel-slide::scroll-marker:target-current {
    background: var(--brand-600);
    inline-size: 22px;
  }
}') ?></code></pre>
  <p class="text-muted">
    <code>:target-current</code> is the native equivalent of
    <code>[aria-current="true"]</code> on Deck's own dots. If you restyle one, restyle
    both, or the carousel looks different depending on the browser.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for anything important.</strong> Readers do not move past the first
      slide. If all the content matters, use a <a href="grid.php"><code>.grid</code></a>
      and let it wrap.
    </li>
    <li>
      <strong>Not as a homepage hero rotator.</strong> The most-studied carousel pattern
      there is, and the finding is consistent: almost nobody sees slide two.
    </li>
    <li>
      <strong>Not for a row of small items.</strong> Use
      <a href="scroller.php"><code>.scroller</code></a>, which is the same scroll-snap
      idea without dots, arrows or a script.
    </li>
    <li>
      <strong>Not with autoplay, unless you also add a pause button.</strong> Hover and
      focus do not cover a reader who is simply reading.
    </li>
    <li>
      <strong>Not for sequential content.</strong> Steps in a process are a
      <a href="stepper.php"><code>.stepper</code></a>; a carousel implies its items are
      peers in no particular order.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
