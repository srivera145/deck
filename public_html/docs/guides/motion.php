<?php
declare(strict_types=1);

/**
 * Motion.
 *
 * The reduced-motion section is not a footnote here. Deck wraps essentially
 * every animation in a no-preference query, which is an unusual default and the
 * main reason this guide can be short.
 */

$page = [
    'path' => 'guides/motion.php',
    'title' => 'Animate without a JavaScript library',
    'level' => 'Intermediate',
    'description' => "Entrances, scroll reveals, one-shot animations and page transitions in Deck, all in CSS, all respecting reduced motion without you writing the query.",
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Guides</li>
    <li aria-current="page">Animate without a JavaScript library</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Animate without a JavaScript library</h1>
  <p class="lede">
    You want things to move a little — a panel that slides, a row that fades in, a button
    that acknowledges a press — and you do not want 40 KB of animation library to get it.
    Deck's motion is CSS, it reads the same duration and easing tokens everywhere, and it
    stops for anyone who has asked their system to calm things down.
  </p>
</header>

<section class="stack-4">
  <h2 id="answer">The short answer</h2>
  <p>Four families, and you can guess which you need from the name.</p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The four kinds of motion in Deck</caption>
      <thead>
        <tr><th scope="col">You want</th><th scope="col">Use</th><th scope="col">Fires when</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="You want">A property to change smoothly</th>
          <td data-label="Use"><code>.transition</code>, <code>.transition-colors</code></td>
          <td data-label="Fires when">Whenever the property changes</td>
        </tr>
        <tr>
          <th scope="row" data-label="You want">Something to arrive</th>
          <td data-label="Use"><code>.enter</code>, <code>.enter-rise</code>, <code>.enter-pop</code></td>
          <td data-label="Fires when">On load, once</td>
        </tr>
        <tr>
          <th scope="row" data-label="You want">Something to arrive on scroll</th>
          <td data-label="Use"><code>.reveal</code>, <code>.reveal-fade</code>, <code>.reveal-pop</code></td>
          <td data-label="Fires when">When it scrolls into view</td>
        </tr>
        <tr>
          <th scope="row" data-label="You want">A one-off reaction</th>
          <td data-label="Use"><code>Deck.play(node, 'shake')</code></td>
          <td data-label="Fires when">When you say so</td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php docs_example(
      '<div class="cluster">' . "\n" .
      '  <button class="btn" onclick="Deck.play(this, \'shake\')">Shake</button>' . "\n" .
      '  <button class="btn" onclick="Deck.play(this, \'flash-good\')">Flash</button>' . "\n" .
      '  <button class="btn" onclick="Deck.play(this, \'nudge\')">Nudge</button>' . "\n" .
      '  <button class="btn btn-primary lift">Hover me</button>' . "\n" .
      '</div>',
      'Press them. The first three are one-shots; the last is a hover transition'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="reduced">Reduced motion is the default, not an afterthought</h2>
  <p>
    Almost everything that moves in Deck is inside
    <code>@media (prefers-reduced-motion: no-preference)</code>. The animation is not
    written and then switched off for some people; it is only written for people who have
    not asked for less. So a reader with the setting on gets the end state instantly —
    the panel is open, the row is visible, the card has arrived — rather than a broken
    page with the entrance missing.
  </p>
  <p>
    There is one deliberate exception: loading indicators keep moving. A frozen spinner
    reads as a hung request, which is worse than the motion it avoided.
  </p>
  <p>
    You get all of this by using the classes. If you write your own animation, use the same
    guard:
  </p>
  <pre class="dx-code"><code>@layer app.components {
  @media (prefers-reduced-motion: no-preference) {
    .invoice-row { transition: background-color var(--dur-2) var(--ease-out); }
  }
}</code></pre>
  <p>
    From JavaScript, <code>Deck.reduced()</code> answers the same question, evaluated fresh
    on each call so it follows a preference changed mid-session:
  </p>
  <pre class="dx-code"><code>if (!Deck.reduced()) confetti();</code></pre>
  <p>
    <code>.no-motion</code> switches motion off for one subtree regardless of the system
    setting — useful around a third-party embed that animates more than you would like.
  </p>
</section>

<section class="stack-4">
  <h2 id="tokens">Durations and easings</h2>
  <p>
    Seven durations and ten easing curves, and the point of using the tokens rather than
    typing <code>200ms ease-out</code> is that changing one value later changes the whole
    product's feel rather than one rule.
  </p>
  <?php docs_token_table(['--dur-1', '--dur-2', '--dur-3', '--dur-4', '--dur-5', '--ease-out', '--ease-spring', '--ease-bounce']); ?>
  <p>
    The rough rule: <code>--dur-1</code> for a colour change, <code>--dur-2</code> for
    something moving a short distance, <code>--dur-3</code> for a panel opening, and the
    longer two for something crossing the screen. If an interaction feels sluggish it is
    usually one step too long, not the easing.
  </p>
  <p>
    Three of the easings are <code>linear()</code> springs rather than cubic béziers —
    <code>--ease-spring</code>, <code>--ease-bounce</code> and
    <code>--ease-overshoot</code>. They give the overshoot-and-settle feel that normally
    needs a physics library, as a plain CSS value with no JavaScript and no per-frame cost.
  </p>
  <?php docs_example(
      '<div class="cluster">' . "\n" .
      '  <span class="badge enter-pop">enter-pop</span>' . "\n" .
      '  <span class="badge enter-rise">enter-rise</span>' . "\n" .
      '  <span class="badge enter-blur">enter-blur</span>' . "\n" .
      '  <span class="badge enter-drop">enter-drop</span>' . "\n" .
      '</div>',
      'The entrance set, played on load'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="stagger">Lists that arrive in sequence</h2>
  <p>
    Put <code>.stagger</code> on the container and the entrance classes on the children,
    and each child starts a little after the one before it. The step is
    <code>--stagger-step</code>, 45ms by default.
  </p>
  <pre class="dx-code"><code>&lt;ul class="stack-2 stagger"&gt;
  &lt;li class="enter-rise"&gt;First&lt;/li&gt;
  &lt;li class="enter-rise"&gt;Second&lt;/li&gt;
  &lt;li class="enter-rise"&gt;Third&lt;/li&gt;
&lt;/ul&gt;</code></pre>
  <p class="dx-note">
    Keep it under about eight items. A stagger across thirty rows means the last one
    arrives more than a second after the first, and by then it reads as the page being
    slow rather than the page being considered. <code>.stagger-fast</code> and
    <code>.stagger-slow</code> change the step.
  </p>
</section>

<section class="stack-4">
  <h2 id="reveal">Revealing on scroll</h2>
  <p>
    <code>.reveal</code>, <code>.reveal-fade</code> and <code>.reveal-pop</code> animate
    when the element scrolls into view. An <code>IntersectionObserver</code> in
    <code>deck.js</code> adds <code>.is-revealed</code> once and then stops watching, so
    the cost is one observer for the page rather than a scroll handler.
  </p>
  <pre class="dx-code"><code>&lt;section class="reveal"&gt;…&lt;/section&gt;
&lt;section class="reveal-fade reveal-late"&gt;…&lt;/section&gt;</code></pre>
  <p>
    Two warnings. Do not put a reveal on anything above the fold — it will animate while
    the reader is already looking at it. And do not reveal content that matters: if
    JavaScript fails the observer never runs, so anything hidden pending reveal stays
    hidden. Use it for polish on a marketing page, never for the contents of an app.
  </p>
</section>

<section class="stack-4">
  <h2 id="transitions">Animating between two states of the page</h2>
  <p>
    <code>Deck.transition()</code> wraps a DOM change so the browser tweens between before
    and after — a row leaving a table, a card expanding into a detail view — using the View
    Transition API.
  </p>
  <pre class="dx-code"><code>Deck.transition(() =&gt; row.remove());

await Deck.transition(() =&gt; list.prepend(newRow), { direction: 'forward' }).finished;
newRow.focus();</code></pre>
  <p>
    The useful part is the fallback. Where the API is missing, <em>or</em> the reader has
    asked for reduced motion, the update runs immediately and the return value is a stub
    with the same shape — <code>{ finished, ready }</code>, both already resolved. So
    <code>await …finished</code> is safe to write unconditionally and you never
    feature-detect at the call site.
  </p>
  <p>
    For cross-document transitions, the four <code>.vt-*</code> classes name elements that
    should be treated as the same thing across two pages — a header that stays put while
    the content changes. See <a href="../components/motion.php">the motion component
    page</a> for the full set.
  </p>
</section>

<section class="stack-4">
  <h2 id="oneshot">One-shot reactions</h2>
  <p>
    <code>Deck.play()</code> adds an animation class, waits for it, and cleans up after
    itself. It also removes the class and forces a reflow first, so playing the same
    animation twice in a row actually plays twice — the thing that catches people writing
    this by hand.
  </p>
  <pre class="dx-code"><code>await Deck.play(field, 'shake');
field.focus();

Deck.play(card, 'flash-good');</code></pre>
  <p>
    It returns a promise resolving to the node, resolves immediately if the node is null,
    and has a two-second safety timeout so a class that triggers no animation — a typo, or
    a rule switched off under reduced motion — still resolves rather than leaving a promise
    pending forever.
  </p>
</section>

<section class="stack-3">
  <h2 id="taste">Two rules of taste</h2>
  <ul class="stack-3">
    <li>
      <strong>Motion should explain, not decorate.</strong> A panel that slides down from
      its trigger tells you where it came from. A card that spins on hover tells you
      nothing and costs attention. If you cannot say what a movement communicates, remove
      it.
    </li>
    <li>
      <strong>Animate <code>transform</code> and <code>opacity</code>.</strong> Those two
      run on the compositor. Animating <code>width</code>, <code>top</code> or
      <code>margin</code> forces layout on every frame, which is how a smooth animation on
      your laptop becomes a stuttering one on a three-year-old phone. Deck's own animations
      use <code>translate</code>, <code>scale</code> and <code>opacity</code> for exactly
      this reason.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
