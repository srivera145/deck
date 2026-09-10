<?php
declare(strict_types=1);

$page = [
    'path' => 'components/container.php',
    'title' => 'Container',
    'level' => 'Beginner',
    'description' => 'Deck\'s .container centres content, caps it at a max width and applies the responsive page gutter. Plus .app-shell for a sticky-footer frame and .sticky-top for a header that earns its shadow.',
    'documents' => [
        'container', 'container-full', 'container-lg', 'container-md', 'container-sm',
        'container-xl', 'app-shell', 'sticky-top',
    ],

    'component' => 'container',
    'accounts' => [
        '04-layout.css' => 'documented: the container, its five widths, and .app-shell and .sticky-top alongside it. .section is in the same file and has its own page.',
        '99-print.css'  => 'documented: the cap and the gutter are removed for print, and sticky things go static — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';

$widths = [
    ['container-sm', '40rem', 'A single column of prose. About 70 characters at the body size.'],
    ['container-md', '56rem', 'An article with a rail, or a form.'],
    ['container', '76rem', 'The default. A page of cards or a dashboard.'],
    ['container-lg', '76rem', 'The default, named. Use it when you want to be explicit.'],
    ['container-xl', '90rem', 'A wide dashboard or a data grid.'],
    ['container-full', 'none', 'No cap. Keeps the gutter, drops the max width.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Container</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Container</h1>
  <p class="lede">
    <code>.container</code> does three things: caps the content at
    <code>--container</code>, centres it with <code>margin-inline: auto</code>, and
    applies the page gutter. The gutter is
    <code><?= e(api_token('--space-gutter')['value'] ?? 'clamp(1rem, .55rem + 2.2vw, 2rem)') ?></code>
    — a clamp, so the space at the edge of the page grows with the viewport instead of
    stepping at breakpoints.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Wrap the content of a page section in one. Almost every page is a
    <code>&lt;header&gt;</code>, some <code>&lt;section&gt;</code>s and a
    <code>&lt;footer&gt;</code>, each with a container inside it — which is what lets a
    full-width background sit behind content that is still capped and centred.
  </p>
  <?php
  docs_example(
      '<section class="section" style="background:var(--surface-2)">' . "\n" .
      '  <div class="container">' . "\n" .
      '    <div class="stack-2">' . "\n" .
      '      <h3>Full-width band, capped content</h3>' . "\n" .
      '      <p class="text-muted">The background reaches the edges; the text does not.</p>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '</section>',
      'The section carries the background, the container carries the width',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    The order matters. Put the background on the outer element and the container inside
    it. A container with a background gives you a coloured band that stops short of the
    edges, which is almost never what anyone wants.
  </p>
</section>

<section class="stack-3">
  <h2 id="widths">Widths</h2>
  <p>
    Each variant sets <code>--container</code> and nothing else, so switching width never
    changes the gutter or the centring.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Container widths</caption>
      <thead>
        <tr><th scope="col">Class</th><th scope="col">--container</th><th scope="col">What it is for</th></tr>
      </thead>
      <tbody>
        <?php foreach ($widths as [$cls, $w, $use]): ?>
          <tr>
            <th scope="row" data-label="Class"><code><?= e('.' . $cls) ?></code></th>
            <td data-label="--container"><code class="dx-dim"><?= e($w) ?></code></td>
            <td data-label="What it is for"><?= e($use) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="text-muted">
    <code>.container-lg</code> and the bare <code>.container</code> are the same
    <code>76rem</code>. The named one exists so a page can say which width it means
    rather than relying on the default, which is worth having when the default might
    change.
  </p>
  <?php
  docs_example(
      '<div class="stack-3">' . "\n" .
      '  <div class="container-sm" style="background:var(--surface-2);padding-block:var(--space-3)">' . "\n" .
      '    <p class="text-muted">container-sm — 40rem</p>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="container-md" style="background:var(--surface-2);padding-block:var(--space-3)">' . "\n" .
      '    <p class="text-muted">container-md — 56rem</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Both centred, both gutter-padded, different caps',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Those two examples put a background <em>on</em> the container to make the width
    visible, which is the thing the note above says not to do. It is a diagram, not a
    pattern.
  </p>
</section>

<section class="stack-3">
  <h2 id="nesting">Do not nest containers</h2>
  <p>
    A container inside a container applies the gutter twice, so the inner content sits
    two gutters in from the edge and no longer lines up with anything else on the page.
    If you need a narrower column inside a container, use
    <code>.container-full</code> — which keeps the gutter and drops the cap — or set
    <code>max-inline-size</code> on the child directly.
  </p>
  <?php
  docs_example(
      '<div class="container-full" style="background:var(--surface-2);padding-block:var(--space-3)">' . "\n" .
      '  <p class="text-muted">Gutter, no cap — safe inside another container.</p>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="section">Vertical page rhythm</h2>
  <p>
    The container handles the horizontal axis. The vertical counterpart is
    <a href="section.php"><code>.section</code></a>, whose padding is a
    <code>clamp()</code> that grows with the viewport in the same way the gutter does.
    The two are almost always used together — a <code>&lt;section
    class="section"&gt;</code> carrying a background, with a
    <code>.container</code> inside it — and <code>.section</code> has
    <a href="section.php">its own page</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="app-shell">.app-shell — header, content, footer</h2>
  <p>
    <code>.app-shell</code> is a grid with
    <code>min-block-size: 100dvh</code> and <code>grid-template-rows: auto 1fr
    auto</code>. The middle row takes all the remaining height, which is the modern
    answer to the sticky-footer problem: on a short page the footer sits at the bottom of
    the screen rather than halfway up it, and on a long page it is where the content
    ends.
  </p>
  <pre class="dx-code"><code><?= e('<body class="app-shell">
  <header>…</header>
  <main>…</main>
  <footer>…</footer>
</body>') ?></code></pre>
  <p class="text-muted">
    <code>100dvh</code> rather than <code>100vh</code> is the detail that matters on a
    phone: <code>vh</code> is measured against the largest viewport, so with the browser
    chrome visible a <code>100vh</code> shell is taller than the screen and the page
    scrolls a little for no reason. <code>dvh</code> tracks the viewport as the chrome
    shows and hides.
  </p>
  <p class="dx-note text-muted">
    It expects exactly three children. A fourth gets an <code>auto</code> row from
    <code>grid-auto-rows</code> and lands after the footer, which is rarely what was
    intended.
  </p>
</section>

<section class="stack-3">
  <h2 id="sticky-top">.sticky-top</h2>
  <p>
    A sticky header. What is interesting is what it does <em>not</em> do while it is at
    rest: where the browser supports <code>container-type: scroll-state</code>, the blur
    and the shadow are moved onto an <code>::after</code> that is transparent until the
    header actually sticks. Sitting in flow it looks like part of the page; the moment it
    pins, it earns a shadow.
  </p>
  <pre class="dx-code"><code><?= e('@supports (container-type: scroll-state) {
  .sticky-top { container-type: scroll-state; }
  .sticky-top::after { opacity: 0; /* blur + shadow live here */ }
  @container scroll-state(stuck: block-start) {
    .sticky-top::after { opacity: 1; box-shadow: var(--shadow-2); }
  }
}') ?></code></pre>
  <p>
    The treatment rides on a pseudo-element because a scroll-state container cannot style
    itself — only its descendants — and <code>::after</code> is a descendant. Without
    support, the fallback is the unconditional blurred background, which is what the
    header on this page is using if your browser is older.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/04-layout.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    Both page-level spacings are clamps rather than breakpoint steps, which is why a Deck
    page has no width at which the margins suddenly jump.
  </p>
  <?php docs_token_table(['--space-gutter', '--space-section', '--z-sticky', '--shadow-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>A container is a <code>&lt;div&gt;</code> with no role.</strong> Landmarks
      come from the elements around it — <code>&lt;header&gt;</code>,
      <code>&lt;main&gt;</code>, <code>&lt;nav&gt;</code>,
      <code>&lt;footer&gt;</code>. Putting <code>.container</code> on the landmark itself
      is fine and often tidier.
    </li>
    <li>
      <strong>The line length cap is a readability feature.</strong>
      <code>.container-sm</code> at <code>40rem</code> is about 70 characters at the
      body size, which is inside the 80-character maximum WCAG recommends for blocks of
      text. A full-width paragraph on a wide monitor is genuinely harder to read, not
      just less pretty.
    </li>
    <li>
      <strong>The gutter survives zoom.</strong> Because it is a
      <code>clamp()</code> in <code>rem</code> and <code>vw</code>, text never reaches
      the edge of the screen at 200% zoom — which is where a fixed
      <code>padding: 16px</code> starts to look cramped.
    </li>
    <li>
      <strong><code>.sticky-top</code> eats vertical space.</strong> At 400% zoom a
      sticky header can take a third of the screen. Consider
      <code>@media (max-height: 30rem) { .sticky-top { position: static } }</code> if
      your header is tall.
    </li>
    <li>
      <strong>A sticky header hides anchor targets.</strong> Following an in-page link
      scrolls the target to the top of the viewport, which is underneath the header. Add
      <code>scroll-margin-block-start</code> to headings equal to the header's height —
      Deck does not do this for you because it does not know how tall your header is.
    </li>
    <li>
      <strong><code>.app-shell</code> and skip links.</strong> A three-row shell makes
      <code>&lt;main&gt;</code> easy to identify; give it an <code>id</code> and point a
      <code>.skip-link</code> at it, as this documentation site does.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>margin-inline: auto</code> and <code>padding-inline</code> are both
    direction-agnostic, and <code>.section</code>'s <code>padding-block</code> is
    unaffected by direction. Nothing here needs an RTL rule — a centred, capped column is
    the same shape in every writing direction.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.sticky-top::after</code> transitions its opacity over
    <?= e(api_token('--dur-2')['value'] ?? '200ms') ?> as the header sticks and unsticks.
    Under <code>prefers-reduced-motion: reduce</code> the global reset collapses it to
    <code>.01ms</code>, so the shadow appears immediately instead of fading in. Nothing
    else on this page moves.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> removes the cap and the gutter from every container
    variant — paper has its own margins, and a <code>76rem</code> cap inside them would
    waste an inch on both sides. <code>.section</code> loses its top padding and keeps
    <code>8mm</code> at the bottom, so sections stay separated without a band of white at
    the top of every page. <code>.sticky-top</code> is forced to
    <code>position: static</code>, or the header would be stamped across every sheet.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One container, no layer needed */
<div class="container" style="--container:64rem">

@layer app.components {
  /* A different default width for the whole site */
  .container { --container: 68rem; }

  /* A wider gutter on large screens only */
  :root { --space-gutter: clamp(1rem, .55rem + 3vw, 3rem); }
}') ?></code></pre>
  <p>
    Changing <code>--space-gutter</code> on <code>:root</code> moves every container,
    every <code>.scroller</code>'s bleed and every <code>.panel</code>'s edge together,
    because all three read the same token. That is the intended way to change a page's
    horizontal rhythm.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not inside another container.</strong> Two gutters. Use
      <code>.container-full</code>, or set a <code>max-inline-size</code> on the child.
    </li>
    <li>
      <strong>Not for a component's own width.</strong> A card that caps itself at
      <code>76rem</code> is a card that behaves differently depending on how deeply it is
      nested. Components should fill what they are given; the container is the page's
      job.
    </li>
    <li>
      <strong>Not for centring one element.</strong> <code>.mx-auto</code> with a
      <code>max-inline-size</code> does that without adding a gutter you did not ask
      for.
    </li>
    <li>
      <strong><code>.section</code> is not for spacing inside a component.</strong> Its
      padding reaches <?= e('6rem') ?> on a wide screen. Inside a card that is absurd; use
      <code>.stack-6</code> or <code>.stack-8</code>.
    </li>
    <li>
      <strong><code>.app-shell</code> is not for a panel.</strong>
      <code>min-block-size: 100dvh</code> makes it the height of the screen wherever you
      put it, so a shell inside a modal is a modal as tall as the viewport.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
