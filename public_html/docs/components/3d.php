<?php
declare(strict_types=1);

$page = [
    'path' => 'components/3d.php',
    'title' => '3D space',
    'level' => 'Advanced',
    'description' => 'Deck\'s 3D layer: flip cards, pointer tilt, a card pile, coverflow, a cube, depressible buttons and compositor-driven parallax. Every effect is guarded for reduced motion.',
    'documents' => [
        'scene', 'scene-near', 'scene-far', 'space', 'flat', 'backface-hidden',
        'flip', 'flip-front', 'flip-back', 'flip-x', 'flip-hover', 'is-flipped',
        'tilt', 'tilt-lift', 'tilt-lift-sm', 'is-tilting',
        'pile', 'is-fanned', 'is-dismissed',
        'coverflow', 'is-front', 'is-behind-start', 'is-behind-end',
        'cube', 'cube-spin', 'face-front', 'face-back', 'face-start', 'face-end',
        'face-top', 'face-bottom',
        'btn-3d', 'parallax', 'parallax-layer', 'parallax-back', 'parallax-mid',
        'parallax-front', 'turn-in', 'turn-out',
        'z-lift-1', 'z-lift-2', 'z-lift-3', 'z-sink',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">3D space</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>3D space</h1>
  <p class="lede">
    The file opens with its own argument, and it is the right one to start from:
    <em>3D earns its place when depth carries meaning — a card that has two sides, a stack
    you are working down through, a control that physically depresses. It does not earn its
    place as decoration on a landing page.</em>
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    When the thing on screen genuinely has a third dimension. A card with a front and a
    back. A pile of records you deal off the top. A button that goes down when pressed.
  </p>
  <p>
    In those cases the depth is not an effect, it is the model — and using it means the
    reader does not have to be told what will happen next. Everywhere else, 3D costs GPU
    memory, breaks in reduced motion, and makes text softer.
  </p>
  <p class="dx-note text-muted">
    <strong>Everything in this layer uses <code>rotate</code>, <code>scale</code> and
    <code>translate</code> as individual properties</strong> rather than the
    <code>transform</code> shorthand. That is deliberate: two effects on the same element
    compose instead of overwriting each other, so a <code>.tilt</code> inside a
    <code>.pile</code> does not cancel the pile's offset. If you add your own, follow the
    same rule.
  </p>
</section>

<section class="stack-3">
  <h2 id="scene">Scene and space</h2>
  <p>
    Nothing looks three-dimensional without a vanishing point.
    <code>.scene</code> establishes one; <code>.space</code> tells the children to stay in
    it rather than being flattened.
  </p>
  <ul class="stack-2">
    <li><code>.scene</code> — <code>perspective: var(--perspective, 1000px)</code>, with the
      vanishing point at <code>--vanish</code>, defaulting to the centre.</li>
    <li><code>.scene-near</code> — 560px. Stronger perspective, more dramatic.</li>
    <li><code>.scene-far</code> — 2000px. Nearly isometric.</li>
    <li><code>.space</code> — <code>transform-style: preserve-3d</code> on a wrapper whose
      children need to keep their own depth.</li>
    <li><code>.flat</code> — the escape hatch, forcing <code>transform-style: flat</code>
      back on for a subtree that should not.</li>
    <li><code>.backface-hidden</code> — hides an element's reverse side.</li>
  </ul>
  <p class="text-muted">
    A smaller <code>perspective</code> means the viewer is closer, so rotation is more
    extreme. If a flip looks violent, <code>.scene-far</code> is usually the fix rather than
    a smaller rotation.
  </p>
</section>

<section class="stack-3">
  <h2 id="flip">Flip card</h2>
  <p>
    Two faces in one grid cell, so the card is exactly as tall as its taller side and
    nothing reflows when it turns. <code>.is-flipped</code> rotates it;
    <code>.flip-x</code> turns it about the horizontal axis instead.
  </p>
  <?php
  docs_example(
      '<div class="scene" style="max-inline-size:18rem">' . "\n" .
      '  <button type="button" class="flip flip-hover card" aria-label="Card, hover to see the back">' . "\n" .
      '    <span class="flip-front stack-1">' . "\n" .
      '      <strong>Standard</strong>' . "\n" .
      '      <span class="text-muted">£12 a month</span>' . "\n" .
      '    </span>' . "\n" .
      '    <span class="flip-back stack-1">' . "\n" .
      '      <strong>Includes</strong>' . "\n" .
      '      <span class="text-muted">5 seats, 20GB, email support</span>' . "\n" .
      '    </span>' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'A real button, so it is reachable — but see Accessibility before shipping this',
      'stack'
  );
  ?>
  <p>
    <code>.flip-hover</code> flips on hover, and it is wrapped in
    <code>@media (hover: hover) and (prefers-reduced-motion: no-preference)</code> — so on a
    touchscreen it does nothing at all, because a hover flip on touch would need a tap that
    the card cannot distinguish from a press. On touch, toggle
    <code>.is-flipped</code> from your own click handler.
  </p>
</section>

<section class="stack-3">
  <h2 id="tilt">Tilt</h2>
  <p>
    <code>.tilt</code> leans toward the pointer. <code>deck.js</code> writes
    <code>--tilt-axis</code> and <code>--tilt-amount</code> from the cursor position, and
    both are registered with <code>@property</code> as angles, so they interpolate smoothly
    back to rest instead of jumping.
  </p>
  <p>
    <code>.tilt-lift</code> on a child floats it above the card face at
    <code>--depth</code> (28px by default); <code>.tilt-lift-sm</code> is 12px. Both only
    apply where hover exists and motion is allowed.
  </p>
  <?php
  docs_example(
      '<div class="scene scene-near" style="max-inline-size:20rem">' . "\n" .
      '  <div class="tilt card stack-2" data-tilt="12">' . "\n" .
      '    <strong class="tilt-lift">Deploy</strong>' . "\n" .
      '    <p class="text-muted tilt-lift-sm">Ships to production in about 40 seconds.</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'data-tilt sets the maximum angle; 9 degrees is the default',
      'stack'
  );
  ?>
  <p class="text-muted">
    The script bails out entirely where <code>(hover: hover)</code> does not match, so no
    pointer listeners are attached on a phone. It also re-checks reduced motion on every
    move rather than only at startup, so changing the system setting takes effect without a
    reload.
  </p>
</section>

<section class="stack-3">
  <h2 id="pile">Pile</h2>
  <p>
    A stack of records you work down through. Each child's <code>--i</code> is its position
    from the top, and everything follows from it: offset, scale, z-index and brightness are
    all <code>calc()</code> on that one number.
  </p>
  <p>
    Where <code>sibling-index()</code> is supported, <code>--i</code> comes from the browser
    and the pile is any length. Where it is not, a <code>@supports not</code> block hard-codes
    <code>:nth-child</code> for the first eight — so <strong>a pile of more than eight
    cards has its ninth and later members all sitting at
    <code>--i: 0</code></strong>, stacked exactly on top of the first. That is a real limit,
    not a rounding error.
  </p>
  <?php
  docs_example(
      '<div class="pile" style="max-inline-size:20rem">' . "\n" .
      '  <div class="card stack-1"><strong>Invoice 4417</strong><span class="text-muted">£1,240 · due Friday</span></div>' . "\n" .
      '  <div class="card stack-1"><strong>Invoice 4418</strong><span class="text-muted">£860 · due Monday</span></div>' . "\n" .
      '  <div class="card stack-1"><strong>Invoice 4419</strong><span class="text-muted">£3,100 · due next week</span></div>' . "\n" .
      '</div>',
      'Only the top three are visible — the fourth onward are opacity 0 until fanned',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.is-fanned</code> on the pile spreads it out and brings the hidden cards back;
    <code>.is-dismissed</code> on a child throws it off to the side and fades it, which is
    what <code>deck.js</code> adds when a card is dealt away.
  </p>
</section>

<section class="stack-3">
  <h2 id="coverflow">Coverflow</h2>
  <p>
    A horizontal scroller where off-centre items rotate away. The mechanics are scroll snap
    — real scrolling, real momentum, real keyboard scrolling — and the 3D is only the
    appearance.
  </p>
  <p>
    <code>deck.js</code> marks the centred item <code>.is-front</code> and its neighbours
    <code>.is-behind-start</code> / <code>.is-behind-end</code>. Without the script nothing
    is marked, so <strong>every item simply sits flat</strong> and the component degrades to
    a plain snapping scroller — which is the correct failure and is worth copying.
  </p>
  <p class="dx-note text-muted">
    The scrollbar is hidden (<code>scrollbar-width: none</code>), and the container is not
    focusable. That is the same keyboard gap recorded for Deck's other scroll containers in
    <code>FINDINGS.md</code>: a keyboard-only user cannot scroll it without a focusable
    child to tab to. Make the items focusable.
  </p>
</section>

<section class="stack-3">
  <h2 id="cube">Cube</h2>
  <p>
    Six faces, positioned by <code>translateZ</code> at half the cube's size.
    <code>--size</code> drives everything, so one value resizes the whole thing.
  </p>
  <p>
    Which face is showing is an attribute, not a class:
    <code>data-face="front"</code>, <code>"back"</code>, <code>"start"</code>,
    <code>"end"</code>, <code>"top"</code> or <code>"bottom"</code>. Note
    <em>start</em> and <em>end</em> rather than left and right — the naming is logical even
    though the transforms underneath cannot be.
  </p>
  <?php
  docs_example(
      '<div class="scene" style="padding:2rem">' . "\n" .
      '  <div class="cube" data-face="front" style="--size:90px">' . "\n" .
      '    <span class="face-front">1</span>' . "\n" .
      '    <span class="face-end">2</span>' . "\n" .
      '    <span class="face-back">3</span>' . "\n" .
      '    <span class="face-start">4</span>' . "\n" .
      '    <span class="face-top">5</span>' . "\n" .
      '    <span class="face-bottom">6</span>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Change data-face and it rotates to that side',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.cube-spin</code> rotates it continuously over 14 seconds. The whole rule —
    animation and keyframes together — lives inside
    <code>@media (prefers-reduced-motion: no-preference)</code>, so under a reduced-motion
    setting it does not merely slow down, it never starts.
  </p>
</section>

<section class="stack-3">
  <h2 id="btn-3d">Depressible buttons</h2>
  <p>
    <code>.btn-3d</code> gives a <a href="button.php">button</a> real thickness with a solid
    offset shadow. On <code>:active</code> the face moves down by
    <code>--lift</code> and the shadow collapses to nothing, so the button
    <em>arrives</em> at the surface rather than changing colour.
  </p>
  <?php
  docs_example(
      '<span class="cluster">' . "\n" .
      '  <button type="button" class="btn btn-primary btn-3d">Deploy</button>' . "\n" .
      '  <button type="button" class="btn btn-primary btn-3d" style="--lift:6px">Deeper</button>' . "\n" .
      '</span>',
      'Press and hold to see the face meet the shadow',
      'stack'
  );
  ?>
  <p class="text-muted">
    This is the clearest case for 3D in the whole layer: the depth <em>is</em> the feedback,
    it lasts 70ms, and it happens only in response to something the reader did.
  </p>
</section>

<section class="stack-3">
  <h2 id="parallax">Parallax, and page turns</h2>
  <p>
    <code>.parallax</code> is genuine 3D parallax rather than a scroll handler: layers sit
    at different Z depths inside a container with an 8px perspective, and the compositor
    does the arithmetic. There is no <code>scroll</code> listener, so there is nothing to
    jank.
  </p>
  <pre class="dx-code"><code><?= e('<div class="parallax">
  <div class="parallax-layer parallax-back">…</div>
  <div class="parallax-layer parallax-mid">…</div>
  <div class="parallax-layer parallax-front">…</div>
</div>') ?></code></pre>
  <p>
    The scale on each layer compensates for its depth, which is why
    <code>.parallax-back</code> is <code>scale(1.75)</code> — without it a layer pushed 6px
    away would render smaller.
  </p>
  <p>
    <code>.turn-out</code> and <code>.turn-in</code> are a page-turn pair for stepping
    through a wizard, meant to be used with <code>Deck.transition()</code> so the DOM swap
    and the motion happen together. Both set <code>transform-origin: left center</code> and
    both are flipped to <code>right center</code> under <code>dir="rtl"</code> in the same
    file — one of the few places a physical value is corrected in place rather than in
    <code>src/19-logical.css</code>.
  </p>
  <p class="text-muted">
    <code>.z-lift-1</code>, <code>-2</code>, <code>-3</code> and <code>.z-sink</code> move
    an element 8, 20, 40 or −16 pixels toward or away from the viewer inside a scene. They
    are for real depth in a 3D context, not a substitute for a shadow — outside a
    <code>.scene</code> they do nothing visible.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    These are nine separate effects in <code>src/21-space3d.css</code> rather than one
    component root, so there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--line', '--surface', '--brand-600', '--ink-900', '--space-4', '--space-6', '--dur-3', '--ease-spring']); ?>
  <p class="dx-note text-muted">
    <strong>Two tokens this layer uses are missing from that table.</strong> The flip is
    <code>--dur-4</code> (420ms) and the cube is <code>--dur-5</code> (620ms), and neither
    appears above because both are declared in <code>src/16-motion.css</code> rather than
    <code>src/01-tokens.css</code> — and <code>tools/docs/extract.mjs</code> only reads the
    latter. They are real tokens, they work, and the documentation cannot see them.
    Recorded in <code>FINDINGS.md</code>.
  </p>
  <p class="text-muted">
    <code>--perspective</code>, <code>--vanish</code>, <code>--size</code>,
    <code>--depth</code>, <code>--lift</code>, <code>--tilt-axis</code> and
    <code>--tilt-amount</code> are component-level properties rather than tokens. The last
    two are registered with <code>@property</code> in <code>src/00-layers.css</code>, which
    is what makes them animatable.
  </p>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>A flip card hides half its content from everybody.</strong> Both faces are in
      the DOM and both are in the accessibility tree, so a screen reader reads the front and
      the back one after the other with no indication that they are two sides of one thing.
      A sighted reader sees one. Neither gets the whole picture. If the back holds
      information rather than a flourish, do not hide it behind a rotation.
    </li>
    <li>
      <strong>If it flips, it must be a control.</strong> A <code>&lt;div&gt;</code> with a
      click handler cannot be reached or operated by keyboard. Use a
      <code>&lt;button&gt;</code>, and give it a name that says what flipping does.
    </li>
    <li>
      <strong><code>.flip-hover</code> is unreachable by keyboard.</strong> It is a
      <code>:hover</code> rule and there is no <code>:focus-visible</code> alongside it. A
      keyboard user tabbing to the card sees nothing happen. Pair it with your own
      <code>.is-flipped</code> toggle if the back matters.
    </li>
    <li>
      <strong>Rotated text is harder to read.</strong> A tilted card renders its text
      through a 3D transform, which softens the rasterisation. At small sizes and low
      contrast that is a real legibility cost for a decorative gain.
    </li>
    <li>
      <strong>A pile hides everything below the third card.</strong>
      <code>.pile &gt; :nth-child(n+4)</code> is <code>opacity: 0</code> and
      <code>pointer-events: none</code> — but <strong>not</strong>
      <code>visibility: hidden</code> or <code>inert</code>, so those cards are still in the
      tab order and still announced. A keyboard user can tab into a card nobody can see. Add
      <code>inert</code> to the hidden ones yourself.
    </li>
    <li>
      <strong>Coverflow cannot be scrolled by keyboard alone.</strong> Hidden scrollbar,
      non-focusable container. Make the items focusable.
    </li>
    <li>
      <strong>3D motion is the kind that causes nausea.</strong> Rotation and depth change
      are worse for vestibular disorders than fades or slides. This layer is unusually well
      guarded — see below — and the guards only work if you do not add unguarded motion
      beside them.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Mostly handled, with one deliberate exception and one unavoidable one.
  </p>
  <p>
    The cube's faces are named <code>.face-start</code> and <code>.face-end</code> rather
    than left and right, which keeps the API logical — but the transforms behind them are
    <code>rotateY(90deg)</code> and <code>rotateY(-90deg)</code>, which are physical and do
    not mirror. So <code>data-face="start"</code> shows the same physical face in both
    directions. The name is logical, the geometry is not.
  </p>
  <p>
    The page turn <em>is</em> corrected: <code>[dir="rtl"] :is(.turn-out, .turn-in)</code>
    switches <code>transform-origin</code> to <code>right center</code>, so pages turn from
    the correct edge — a book in Arabic opens the other way, and the animation follows.
  </p>
  <p>
    Everything else — flip, tilt, pile, coverflow — is symmetrical about its own centre, so
    there is nothing to mirror. The one thing to check is <code>.pile &gt; .is-dismissed</code>,
    which throws a card to <code>translate: 120%</code>, a physical direction. A dismissed
    card flies right in both directions.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    This is the best-guarded file in Deck, and it is worth reading as a model rather than
    just a caveat. There are <strong>five</strong> separate provisions:
  </p>
  <ul class="stack-2">
    <li>
      <strong>Hover effects are gated at the source.</strong>
      <code>.flip-hover</code>, <code>.tilt:hover</code>, <code>.tilt-lift</code> and
      <code>.tilt.is-tilting</code> live inside
      <code>@media (hover: hover) and (prefers-reduced-motion: no-preference)</code> — so
      they are not overridden later, they are never defined.
    </li>
    <li>
      <strong><code>.cube-spin</code> and the page turns are the same</strong>: the
      animation and its keyframes are both inside a
      <code>no-preference</code> block, so nothing is declared to be switched off.
    </li>
    <li>
      <strong>The script checks too.</strong> <code>wireTilt</code> calls
      <code>reduced()</code> on every pointer move, not once at startup — so the setting can
      change mid-session.
    </li>
    <li>
      <strong>Parallax is dismantled rather than paused.</strong> Under
      <code>reduce</code>, <code>.parallax</code> loses its perspective and the layers become
      <code>position: relative</code> with no transform — the effect becomes an ordinary
      stacked document instead of a frozen 3D scene.
    </li>
    <li>
      <strong>The remaining transitions drop to 1ms and lose their rotation</strong>, except
      the flip's end state, which is kept: <code>.flip.is-flipped</code> still rotates 180°.
      That is the right choice — the card still shows its other side, it just gets there
      instantly instead of turning.
    </li>
  </ul>
  <p class="dx-note text-muted">
    The performance guard is worth noting alongside it: <code>will-change</code> is set only
    on elements that are <em>currently</em> moving —
    <code>.tilt.is-tilting</code>, <code>.flip.is-flipped</code> — and explicitly returned to
    <code>auto</code> when they stop. 3D forces GPU layers, and a page that promotes
    everything permanently will exhaust video memory on a modest device.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Nothing in <code>src/21-space3d.css</code> has a print rule, in either print layer. What
    reaches paper is whatever each element's final transform left on screen: a flipped card
    prints its back face mirrored, a tilted card prints tilted, and a cube prints as one
    face with the others folded behind it.
  </p>
  <p>
    A <code>.pile</code> is the worst case — three cards printed on top of each other, with
    the rest at <code>opacity: 0</code> and therefore invisible but still occupying the
    page.
  </p>
  <pre class="dx-code"><code><?= e('@media print {
  /* Flatten everything and show what was hidden */
  .flip, .flip-x, .tilt, .cube, .pile > *, .coverflow > *, .parallax-layer {
    rotate: none !important;
    scale: none !important;
    translate: none !important;
    transform: none !important;
    opacity: 1 !important;
  }
  .pile { display: block; }
  .flip > .flip-back { display: none; }
}') ?></code></pre>
  <p class="text-muted">
    <code>.pile { display: block }</code> is the important line — it takes the cards out of
    the single grid cell they share and lets them print one after another.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One scene, no layer needed */
<div class="scene" style="--perspective:700px;--vanish:30% 40%"> … </div>

@layer app.components {
  /* A gentler tilt across the app */
  .tilt { --depth: 16px; }

  /* Focus parity for the hover flip */
  @media (hover: hover) and (prefers-reduced-motion: no-preference) {
    .flip-hover:focus-visible { rotate: y 180deg; }
  }

  /* Make the cards a pile hides genuinely unreachable */
  .pile > :nth-child(n+4) { visibility: hidden; }
  .pile.is-fanned > :nth-child(n+4) { visibility: visible; }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for decoration.</strong> The file says so in its first paragraph, and it
      is right. A tilting card on a marketing page costs GPU memory and legibility to say
      nothing.
    </li>
    <li>
      <strong>Not to hide content you need read.</strong> A flip card's back is invisible to
      sighted readers and announced out of context to everyone else. See
      <a href="#accessibility">Accessibility</a>.
    </li>
    <li>
      <strong>Not a pile of more than eight.</strong> Beyond eight, cards without
      <code>sibling-index()</code> support all stack at position zero.
    </li>
    <li>
      <strong>Not coverflow for a list people need to read.</strong> Rotated, scaled-down
      items are harder to scan than a <a href="list.php">list</a>, and the container is not
      keyboard-scrollable.
    </li>
    <li>
      <strong>Not parallax on a page with a lot of text.</strong> Depth-scaled layers
      rasterise text softly, and the effect competes with reading.
    </li>
    <li>
      <strong>Not <code>.z-lift-*</code> as a shadow.</strong> Outside a
      <code>.scene</code> they do nothing. Use Deck's
      <a href="card.php">elevation tokens</a>.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
