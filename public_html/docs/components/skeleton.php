<?php
declare(strict_types=1);

$page = [
    'path' => 'components/skeleton.php',
    'title' => 'Skeleton',
    'level' => 'Beginner',
    'description' => 'Deck\'s .skeleton is a shimmering placeholder block for content that has not arrived. It is decoration: it says nothing to a screen reader, so the loading state has to be announced separately.',
    'documents' => [
        'skeleton', 'skeleton-circle', 'skeleton-text',
    ],

    'component' => 'skeleton',
    'accounts' => [
        '07-components.css' => 'documented: the shimmering block, the text line and the circle',
        '02-reset.css'      => 'documented: the reduced-motion exemption that slows the shimmer to 2.4s rather than freezing it — see Reduced motion',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Skeleton</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Skeleton</h1>
  <p class="lede">
    A grey block where content will be, shimmering to say it is on its way. It works because
    it holds the shape of the thing that is loading, so the page does not jump when the real
    content replaces it — which is the entire justification for the pattern.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    When you know the shape of what is coming and it will take long enough to notice —
    roughly a third of a second and up. A list of rows, a card, an avatar and a name.
  </p>
  <p>
    When you do <em>not</em> know the shape, a skeleton is a lie about the layout, and the
    page will still jump. Use a <a href="progress.php#spinner">spinner</a> and accept the
    honest answer.
  </p>
  <?php
  docs_example(
      '<div class="card" style="max-inline-size:24rem" aria-hidden="true">' . "\n" .
      '  <div class="cluster">' . "\n" .
      '    <span class="skeleton skeleton-circle" style="inline-size:40px;block-size:40px"></span>' . "\n" .
      '    <span class="stack-1" style="flex:1">' . "\n" .
      '      <span class="skeleton skeleton-text" style="inline-size:40%"></span>' . "\n" .
      '      <span class="skeleton skeleton-text" style="inline-size:60%"></span>' . "\n" .
      '    </span>' . "\n" .
      '  </div>' . "\n" .
      '  <p class="skeleton skeleton-text"></p>' . "\n" .
      '  <p class="skeleton skeleton-text" style="inline-size:80%"></p>' . "\n" .
      '</div>',
      'The shape of a card that has not loaded — note aria-hidden on the whole block',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="sizing">Sizing it</h2>
  <p>
    <code>.skeleton</code> is <code>block-size: 1em</code> and full width by default, so it
    takes the size of the text it stands in for. <code>.skeleton-text</code> shortens it to
    <code>.8em</code> and adds vertical margins so a stack of lines has the rhythm of a
    paragraph. <code>.skeleton-circle</code> is the same block with a full radius, for an
    <a href="avatar.php">avatar</a>.
  </p>
  <p>
    Width is yours. Varying it line by line is what stops the block reading as a table, and
    a last line at 60–80% is what makes it read as a paragraph.
  </p>
  <?php
  docs_example(
      '<div class="stack-3" style="max-inline-size:26rem" aria-hidden="true">' . "\n" .
      '  <span class="skeleton" style="block-size:1.6em;inline-size:55%"></span>' . "\n" .
      '  <div class="stack-1">' . "\n" .
      '    <span class="skeleton skeleton-text"></span>' . "\n" .
      '    <span class="skeleton skeleton-text"></span>' . "\n" .
      '    <span class="skeleton skeleton-text" style="inline-size:72%"></span>' . "\n" .
      '  </div>' . "\n" .
      '  <span class="skeleton" style="block-size:8rem"></span>' . "\n" .
      '</div>',
      'A heading, a paragraph and an image well',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <code>.skeleton</code> also sets <code>color: transparent</code>, which is there so you
    can wrap real placeholder text and get its natural width without seeing it. That is
    useful and it is a trap — see <a href="#accessibility">Accessibility</a>, because
    transparent text is still read aloud.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/07-components.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--ink-100', '--ink-200', '--r-xs', '--r-full', '--ease-in-out']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>A skeleton announces nothing.</strong> It is an empty element with a
      background. A screen reader user gets silence where a sighted user gets a clear "this
      is loading" signal. Everything below follows from that.
    </li>
    <li>
      <strong>Hide the skeleton and announce the state.</strong>
      <code>aria-hidden="true"</code> on the placeholder block, and
      <code>aria-busy="true"</code> on the region that is loading. Where the wait is long,
      add a live region that says "Loading results" and, when it finishes, how many arrived.
    </li>
    <li>
      <strong>Transparent text is still announced.</strong> <code>color: transparent</code>
      hides text visually and does nothing to the accessibility tree. Wrap real words in a
      <code>.skeleton</code> without hiding it and a screen reader reads out your dummy copy
      as though it were the content. This is the most likely way to get this component
      wrong.
    </li>
    <li>
      <strong>Do not let the skeleton be focusable.</strong> If you are replacing a list of
      links, the placeholders must not be links. An <code>aria-hidden</code> element
      containing a tab stop is a trap — the focus lands somewhere a screen reader cannot
      describe.
    </li>
    <li>
      <strong>Move focus deliberately when content arrives.</strong> If the reader was
      focused inside the region that has just been replaced, focus falls back to the body
      silently. Decide where it should go before the swap.
    </li>
    <li>
      <strong>It is animation in the periphery.</strong> A page of shimmering blocks is a
      lot of movement for anyone with a vestibular disorder or an attention difficulty. Deck
      slows it rather than stopping it — see below — and if the wait is long, fewer
      skeletons is kinder than more.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Nothing here is directional in a way that needs a rule, and nothing in
    <code>src/19-logical.css</code> touches it. Widths are set with
    <code>inline-size</code>, so a partial line shortens from the correct end on its own.
  </p>
  <p>
    The shimmer itself sweeps the same way in both directions — the gradient is at a fixed
    <code>100deg</code> and the animation moves the background position, neither of which is
    mirrored. It is a texture rather than a direction of travel, so this is defensible; it is
    worth knowing rather than assuming it flips.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="stack-1" style="max-inline-size:22rem" aria-hidden="true">' . "\n" .
      '  <span class="skeleton skeleton-text"></span>' . "\n" .
      '  <span class="skeleton skeleton-text" style="inline-size:65%"></span>' . "\n" .
      '</div>',
      'The short line ends on the correct side',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.skeleton</code> is one of seven things <code>src/02-reset.css</code> exempts from
    the reduced-motion freeze. Instead of stopping, the shimmer slows from 1.4 seconds to
    2.4 and keeps running:
  </p>
  <pre class="dx-code"><code><?= e('.spinner, .icon-spin, .skeleton, .marquee-track, .btn.is-loading::after,
.dg-wrap.is-loading::after, .ping::after {
  animation-duration: 2.4s !important;
  animation-iteration-count: infinite !important;
}') ?></code></pre>
  <p>
    The reasoning in the source is that loading feedback is information rather than
    decoration, and a frozen skeleton is indistinguishable from a page that has given up.
    That is a real trade-off rather than an oversight: a reader who asked for no motion still
    gets some. If your application would rather respect the request completely, override it —
    but replace the shimmer with something static and obviously "waiting", not with nothing.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.skeleton</code> is not in <code>src/99-print.css</code> and is not in the list of
    elements given <code>print-color-adjust: exact</code>, so its background is dropped and
    it prints as blank space the size of the content that never arrived.
  </p>
  <p class="text-muted">
    Which is roughly the right outcome, since a printed page of skeletons has no information
    on it whatever it looks like. If a page is likely to be printed mid-load, print a line of
    text saying so rather than leaving a gap the reader has to interpret.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One block, no layer needed */
<span class="skeleton" style="block-size:3rem;inline-size:70%"></span>

@layer app.components {
  /* A slower, flatter shimmer */
  .skeleton { animation-duration: 2.2s; }

  /* No shimmer at all — a plain tinted block */
  .skeleton { animation: none; background: var(--ink-100); }
}') ?></code></pre>
  <p class="text-muted">
    Note that removing the animation in your own layer does not remove the reduced-motion
    rule, which carries <code>!important</code>. It has nothing to act on once
    <code>animation</code> is <code>none</code>, so the result is what you asked for.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not when you do not know the shape.</strong> A skeleton that does not match
      what arrives causes the layout shift it was supposed to prevent, and costs a shimmer
      on the way.
    </li>
    <li>
      <strong>Not for a wait under about 300ms.</strong> A flash of placeholder and then
      content is worse than a brief nothing. Delay showing the skeleton, or do not show one.
    </li>
    <li>
      <strong>Not for an indeterminate wait.</strong> Skeletons imply "nearly there". For a
      job that may take a minute, use a <a href="progress.php">progress bar</a> and say what
      is happening.
    </li>
    <li>
      <strong>Not for a whole page.</strong> A full screen of shimmer is a lot of motion and
      no information. Show the chrome that is already known — the header, the nav — and put
      skeletons only where data is genuinely pending.
    </li>
    <li>
      <strong>Not as the only loading signal.</strong> It is invisible to assistive
      technology. <code>aria-busy</code> and a live region are not optional extras here.
    </li>
    <li>
      <strong>Not around real text without hiding it.</strong> See
      <a href="#accessibility">Accessibility</a> — transparent is not hidden.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
