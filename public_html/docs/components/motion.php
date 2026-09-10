<?php
declare(strict_types=1);

$page = [
    'path' => 'components/motion.php',
    'title' => 'Motion',
    'level' => 'Intermediate',
    'description' => 'Deck\'s motion layer: transition utilities, entrances, CSS-only staggers and scroll reveals, attention cues, micro-interactions and view transitions. Almost all of it is inside a no-preference block.',
    'documents' => [
        'transition', 'transition-colors', 'transition-move', 'transition-opacity', 'transition-size',
        'dur-1', 'dur-2', 'dur-3', 'dur-4', 'dur-5',
        'ease-in', 'ease-out', 'ease-linear', 'ease-spring', 'ease-bounce', 'ease-overshoot',
        'delay-1', 'delay-2', 'delay-3', 'no-motion', 'will-move', 'is-animating',
        'enter', 'enter-blur', 'enter-drop', 'enter-end', 'enter-pop', 'enter-rise', 'enter-start',
        'stagger', 'stagger-fast', 'stagger-slow',
        'reveal', 'reveal-fade', 'reveal-late', 'reveal-pop', 'is-revealed',
        'scroll-progress', 'shrink-on-scroll',
        'shake', 'was-shaken', 'flash', 'flash-good', 'pulse', 'ping', 'nudge',
        'lift', 'press', 'sweep', 'icon-follow', 'ripple', 'ripple-ink',
        'expand', 'is-open', 'fade-swap', 'marquee', 'marquee-track',
        'tick', 'is-up', 'is-down',
        'vt-hold', 'vt-header', 'vt-main', 'vt-tabbar',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Motion</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Motion</h1>
  <p class="lede">
    Sixty-odd classes for movement, and one structural decision underneath all of them:
    almost the entire file sits inside
    <code>@media (prefers-reduced-motion: no-preference)</code>. The motion is not declared
    and then switched off — for a reader who asked for calm, it is never declared at all.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Motion earns its place when it explains something: where an element came from, that a
    value changed, that a press registered, which of two things is now on top. Every class
    here is meant to answer one of those.
  </p>
  <p>
    It does not earn its place as polish. A page where six things animate on load is a page
    the reader has to wait for, and Deck's own source says so in as many words above the
    entrance block: <em>use for content that appears, not for every section.</em>
  </p>
</section>

<section class="stack-3">
  <h2 id="guard">How the guard is built</h2>
  <p>
    This matters more than any individual class, and it is unusual enough to be worth
    stating plainly. Most frameworks declare their animations and then add a
    <code>prefers-reduced-motion</code> block that overrides them. Deck inverts it: the
    animations live <em>inside</em> a <code>no-preference</code> query.
  </p>
  <pre class="dx-code"><code><?= e('@media (prefers-reduced-motion: no-preference) {
  .enter { animation: dk-fade var(--dur-3) var(--ease-out) both; }
  …everything else…
}') ?></code></pre>
  <p>
    The practical difference is that there is nothing to override and nothing to leak. A
    reader with the setting on does not get a 0.01ms version of the animation — they get an
    element with no <code>animation</code> property, in its final state, immediately. No
    <code>both</code> fill-mode holding an opacity of zero, no half-applied transform.
  </p>
  <p class="dx-note text-muted">
    <strong>The transition utilities are outside the guard</strong>, deliberately.
    <code>.transition</code>, <code>.dur-*</code>, <code>.ease-*</code> and
    <code>.delay-*</code> are declared unconditionally, because the global reset in
    <code>src/02-reset.css</code> already collapses every transition duration to
    <code>.01ms</code> under <code>reduce</code>. They are configuration rather than motion,
    and something has to remain to be configured.
  </p>
</section>

<section class="stack-3">
  <h2 id="utilities">Transitions, durations and easings</h2>
  <p>
    Five transition shorthands for the properties worth animating, plus the scales to tune
    them.
  </p>
  <ul class="stack-2">
    <li><code>.transition</code> — <code>all</code>, which is convenient and the least
      efficient. Prefer one of the specific ones.</li>
    <li><code>.transition-colors</code> — background, border, text, fill and stroke.</li>
    <li><code>.transition-move</code> — translate, scale and rotate, as individual
      properties so they compose.</li>
    <li><code>.transition-size</code> — <code>inline-size</code> and
      <code>block-size</code>, logical rather than width and height.</li>
    <li><code>.transition-opacity</code> — the cheapest thing you can animate.</li>
  </ul>
  <p>
    <code>.dur-1</code> through <code>.dur-5</code> are 110, 190, 300, 420 and 620
    milliseconds. <code>.delay-1</code> to <code>.delay-3</code> are 60, 140 and 260.
    Easings are <code>.ease-out</code>, <code>.ease-in</code>, <code>.ease-linear</code>,
    <code>.ease-spring</code>, <code>.ease-bounce</code> and
    <code>.ease-overshoot</code> — the last three are <code>linear()</code> functions with
    real overshoot rather than cubic approximations.
  </p>
  <p>
    <code>.no-motion</code> is the escape hatch: <code>transition: none !important</code>
    and <code>animation: none !important</code> on anything that must stay still.
  </p>
  <p class="dx-note text-muted">
    <strong>The duration and easing tokens behind these classes are not in Deck's
    documented token list.</strong> <code>--dur-0</code>, <code>--dur-4</code>,
    <code>--dur-5</code>, <code>--dur-slow</code>, <code>--stagger-step</code>,
    <code>--travel</code> and seven <code>--ease-*</code> values are declared on
    <code>:root</code> in <code>src/16-motion.css</code>, and
    <code>tools/docs/extract.mjs</code> only reads <code>src/01-tokens.css</code>. They are
    real, they work, and no generated table can show them. Recorded in
    <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="entrances">Entrances and stagger</h2>
  <p>
    Seven one-shot animations for content arriving: <code>.enter</code> (fade),
    <code>.enter-rise</code>, <code>.enter-drop</code>, <code>.enter-start</code>,
    <code>.enter-end</code>, <code>.enter-pop</code> and <code>.enter-blur</code>. Distance
    comes from <code>--travel</code>, 12px by default, so one property retunes all of them.
  </p>
  <p>
    <code>.enter-start</code> and <code>.enter-end</code> are the logical pair — they move
    in from the start or end edge and so reverse correctly under <code>dir="rtl"</code>
    without a rule anywhere.
  </p>
  <?php
  docs_example(
      '<div class="stagger grid" style="--cols:3;gap:var(--space-3)">' . "\n" .
      '  <div class="card enter-rise">One</div>' . "\n" .
      '  <div class="card enter-rise">Two</div>' . "\n" .
      '  <div class="card enter-rise">Three</div>' . "\n" .
      '  <div class="card enter-rise">Four</div>' . "\n" .
      '  <div class="card enter-rise">Five</div>' . "\n" .
      '  <div class="card enter-rise">Six</div>' . "\n" .
      '</div>',
      'Reload the page to see it again — entrances are one-shot',
      'stack'
  );
  ?>
  <p>
    <code>.stagger</code> on the parent delays each child by
    <code>--stagger-step</code> (45ms; <code>.stagger-fast</code> is 28,
    <code>.stagger-slow</code> is 80). Where <code>sibling-index()</code> is supported it is
    one line and has no ceiling. Where it is not, twelve <code>:nth-child</code> rules cover
    the first twelve — and the thirteenth onward hold the last step rather than snapping
    back to zero:
  </p>
  <pre class="dx-code"><code><?= e('/* Past twelve everything holds the last step rather than snapping back
   to zero. Without sibling-index() that is as far as CSS can count. */
.stagger > :nth-child(n+13) { --n: 11; }') ?></code></pre>
  <p class="text-muted">
    That one line is the difference between a long list degrading gracefully and its
    thirteenth item animating before its second. It is worth contrasting with
    <a href="3d.php#pile"><code>.pile</code></a>, which has the same
    <code>sibling-index()</code> fallback and does not have this line — so a ninth card
    stacks on the first.
  </p>
</section>

<section class="stack-3">
  <h2 id="reveal">Scroll-driven reveals</h2>
  <p>
    <code>.reveal</code>, <code>.reveal-fade</code> and <code>.reveal-pop</code> animate as
    the element enters the scrollport — with <strong>no
    <code>IntersectionObserver</code> and no scroll handler</strong>. The animation is tied
    to position with <code>animation-timeline: view()</code>, so the browser drives it on
    the compositor.
  </p>
  <p>
    <code>.reveal-late</code> shifts the range so the animation starts further into the
    viewport. Where <code>animation-timeline</code> is unsupported, a
    <code>@supports not</code> block sets the elements to <code>opacity: 0</code> and waits
    for <code>deck.js</code> to add <code>.is-revealed</code>.
  </p>
  <p class="dx-note text-muted">
    <strong>That fallback fails closed.</strong> The elements start invisible, and only
    become visible when the script marks them. If <code>deck.js</code> does not load — a
    blocked CDN, a script error earlier on the page — the content stays at
    <code>opacity: 0</code> permanently on any browser without
    <code>animation-timeline</code>. Do not put anything on <code>.reveal</code> that the
    page cannot do without.
  </p>
  <p>
    Two related classes use a scroll timeline rather than a view timeline.
    <code>.scroll-progress</code> is a 3px bar fixed to the top that fills as the document
    scrolls — genuinely useful on a long report. <code>.shrink-on-scroll</code> condenses a
    sticky header over the first 120px by animating <code>--control-h-lg</code>, which is a
    neat trick: it animates a custom property and lets everything sized from that property
    follow.
  </p>
</section>

<section class="stack-3">
  <h2 id="attention">Attention and micro-interaction</h2>
  <p>
    <code>.shake</code>, <code>.flash</code>, <code>.flash-good</code>,
    <code>.pulse</code>, <code>.ping</code> and <code>.nudge</code> are for drawing the eye.
    <code>.lift</code>, <code>.press</code>, <code>.sweep</code>,
    <code>.icon-follow</code> and <code>.ripple</code> are responses to a pointer.
  </p>
  <?php
  docs_example(
      '<span class="cluster">' . "\n" .
      '  <button type="button" class="btn press">Press me</button>' . "\n" .
      '  <a href="#" class="sweep">A sweeping underline</a>' . "\n" .
      '  <button type="button" class="btn btn-ghost">' . "\n" .
      '    Continue' . "\n" .
      '    <svg class="icon icon-follow" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#arrow-right"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '</span>' . "\n" .
      '<div class="card lift" style="max-inline-size:16rem">Hover to lift</div>',
      'All four respond to something the reader did',
      'stack'
  );
  ?>
  <p>
    The two infinite ones deserve caution. <code>.pulse</code> runs for two seconds
    repeatedly and <code>.nudge</code> for 1.6 — both forever. Motion in the corner of the
    eye that never stops is genuinely difficult for some readers, and
    <code>.ping::after</code> is one of the seven selectors exempted from the reduced-motion
    freeze in <code>src/02-reset.css</code>, so it keeps moving slowly rather than stopping.
  </p>
  <p class="text-muted">
    <code>.shake</code> has an interesting companion: <code>.was-shaken</code> is a marker
    <code>deck.js</code> writes so an invalid field shakes once rather than on every
    re-render. It is a JavaScript runtime mark, and it is classified as such in
    <code>tools/docs/api-decisions.txt</code> rather than being part of the public API.
  </p>
</section>

<section class="stack-3">
  <h2 id="vt">View transitions</h2>
  <p>
    The most consequential thing in the file, and it does nothing until you opt in. Add one
    at-rule to your own CSS:
  </p>
  <pre class="dx-code"><code><?= e('@view-transition { navigation: auto; }') ?></code></pre>
  <p>
    and full page loads in an ordinary multi-page application cross-fade — no router, no
    client-side framework, no JavaScript. Deck styles what the browser generates: the root
    transition, a direction-aware variant for back navigation
    (<code>html[data-vt="back"]</code>, which <code>deck.js</code> sets on
    <code>popstate</code>), and shorter durations for chrome that should sit still while the
    content changes.
  </p>
  <p>
    <code>.vt-header</code>, <code>.vt-main</code> and <code>.vt-tabbar</code> name the
    regions. <code>.vt-hold</code> takes its name from <code>--vt</code>, so you can give
    the same name to a row in a list and to the heading on the page it links to, and the
    browser tweens one into the other.
  </p>
  <p class="text-muted">
    The reduced-motion guard for this is separate and sits at the end of the file, because
    view-transition pseudo-elements cannot be inside the <code>no-preference</code> block
    that wraps everything else. It sets every group, old and new to
    <code>animation-duration: 1ms !important</code>, so the navigation still happens and the
    animation does not.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    These are a dozen unrelated utility groups in <code>src/16-motion.css</code> rather than
    one component root, so there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--dur-1', '--dur-2', '--dur-3', '--ease-out', '--ease-spring', '--ease-in-out', '--brand-500', '--good-500', '--z-toast']); ?>
  <p class="text-muted">
    Thirteen further tokens live on <code>:root</code> in
    <code>src/16-motion.css</code> and cannot appear here — see
    <a href="#utilities">Transitions, durations and easings</a> for the list and the reason.
  </p>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The guard is structural, and you can undo it.</strong> Everything Deck ships
      respects <code>prefers-reduced-motion</code>. Motion you add in your own layer does
      not unless you wrap it the same way — and a single unguarded animation beside a
      guarded one is what the reader will notice.
    </li>
    <li>
      <strong>Infinite motion is the hardest kind.</strong> <code>.pulse</code>,
      <code>.nudge</code>, <code>.marquee</code> and <code>.ping</code> never stop. Use them
      where something genuinely needs attention until it is dealt with, and not as texture.
    </li>
    <li>
      <strong>Motion is not an announcement.</strong> A shaken field, a flashed row and a
      ticking number are all silent. If the change matters, it needs
      <code>aria-live</code> or focus movement — the animation is the sighted half only.
    </li>
    <li>
      <strong><code>.reveal</code> hides content behind a script on older browsers.</strong>
      See <a href="#reveal">above</a>. Never on anything essential.
    </li>
    <li>
      <strong><code>.marquee</code> is a moving target.</strong> Text that slides cannot be
      read by someone who reads slowly, and it cannot be clicked reliably. It pauses on
      hover, which does not help a keyboard user. Prefer almost anything else.
    </li>
    <li>
      <strong>Entrance animations delay content.</strong> <code>both</code> fill-mode means
      the element is invisible until its animation begins. With a stagger of 45ms, the
      twentieth item appears nearly a second after the first — and a screen reader may reach
      it before it is visible.
    </li>
    <li>
      <strong>View transitions can disorient.</strong> A cross-fade between pages is gentle;
      a directional slide is not, for everyone. The 1ms guard covers the setting, and the
      setting is not universally enabled by people who would benefit from it.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Mostly correct by construction. <code>.enter-start</code> and
    <code>.enter-end</code> are named and built logically, so they enter from the correct
    edge in either direction with no rule. <code>.transition-size</code> animates
    <code>inline-size</code> and <code>block-size</code> rather than width and height.
  </p>
  <p>
    Three things are physical and are corrected in <code>src/19-logical.css</code>:
    <code>.scroll-progress</code>'s <code>transform-origin</code>, the
    <code>.icon-follow</code> direction, and the marquee keyframes — a ticker that scrolls
    left in an English page has to scroll right in an Arabic one, so the keyframes are
    flipped rather than the element.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="stagger stack-2" style="max-inline-size:20rem">' . "\n" .
      '  <div class="card enter-start">الأول</div>' . "\n" .
      '  <div class="card enter-start">الثاني</div>' . "\n" .
      '  <div class="card enter-start">الثالث</div>' . "\n" .
      '</div>',
      '.enter-start comes in from the right here, and from the left in English',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    This whole page is that topic, so the summary is short. Three mechanisms are in play:
  </p>
  <ul class="stack-2">
    <li>
      <strong>The <code>no-preference</code> wrapper</strong> around everything from
      entrances to the ticker — the motion is not declared at all.
    </li>
    <li>
      <strong>The global reset</strong> in <code>src/02-reset.css</code>, which collapses
      every remaining transition and animation to <code>.01ms</code>. This is what catches
      the transition utilities, which are outside the wrapper.
    </li>
    <li>
      <strong>Seven named exemptions</strong>, including <code>.ping::after</code> and
      <code>.marquee-track</code>, which slow to 2.4 seconds instead of stopping — on the
      grounds that a frozen loading indicator reads as a broken page.
    </li>
  </ul>
  <p class="text-muted">
    <code>.marquee-track</code> being in the exemption list is worth a second look: a ticker
    is not a loading indicator, and a reader who asked for no motion arguably should not get
    a slower ticker. It is defensible if the ticker carries live data, and not if it carries
    marketing copy.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Nothing in <code>src/16-motion.css</code> has a print rule, in either of Deck's two
    print layers. Animations do not run when printing, so most of this is moot — an element
    prints in whatever state it had reached.
  </p>
  <p>
    Two exceptions are worth planning for. <code>.scroll-progress</code> is
    <code>position: fixed</code> at the top of the viewport, so it prints as a coloured bar
    across the top of the first page. And anything with an entrance that has not run —
    <code>.reveal</code> below the fold on a browser using the JavaScript fallback — is at
    <code>opacity: 0</code> and prints blank.
  </p>
  <pre class="dx-code"><code><?= e('@media print {
  .scroll-progress, .marquee { display: none; }
  /* Anything waiting on a reveal should simply be visible on paper */
  .reveal, .reveal-fade, .reveal-pop { opacity: 1 !important; animation: none !important; }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One element, no layer needed */
<div class="enter-rise" style="--travel:24px"> … </div>

@layer app.components {
  /* A calmer stagger across the app */
  .stagger { --stagger-step: 30ms; }

  /* Your own motion, guarded the way Deck guards its own */
  @media (prefers-reduced-motion: no-preference) {
    .my-thing { animation: my-keyframes var(--dur-3) var(--ease-out) both; }
  }
}') ?></code></pre>
  <p class="text-muted">
    The second block is the pattern worth copying. Wrapping in
    <code>no-preference</code> rather than overriding in <code>reduce</code> means there is
    never a partially-applied state to reason about.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not on everything that appears.</strong> Entrances are for content the reader
      is waiting for, not for every section of a page they scrolled to.
    </li>
    <li>
      <strong>Not <code>.reveal</code> on essential content.</strong> It fails closed where
      the script does not load.
    </li>
    <li>
      <strong>Not <code>.marquee</code>, more or less ever.</strong> Moving text is hard to
      read, hard to click and impossible to pause from the keyboard.
    </li>
    <li>
      <strong>Not <code>.transition</code> where a specific one will do.</strong>
      <code>all</code> animates properties you did not intend, including ones added later.
    </li>
    <li>
      <strong>Not infinite animation for something that is not urgent.</strong>
      <code>.pulse</code> on a decorative element is movement with no message.
    </li>
    <li>
      <strong>Not as feedback on its own.</strong> A shake says "wrong" to people watching
      it. The error message says what is wrong to everybody.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
