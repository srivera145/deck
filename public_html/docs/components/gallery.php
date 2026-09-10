<?php
declare(strict_types=1);

$page = [
    'path' => 'components/gallery.php',
    'title' => 'Gallery',
    'level' => 'Intermediate',
    'description' => 'Deck has two image layouts — .gallery for equal square tiles and .masonry for ragged heights — plus .lazy, a frame that holds its space and fades an image in once it decodes.',
    'documents' => [
        'gallery', 'span-2', 'span-wide',
        'masonry', 'masonry-2', 'masonry-3',
        'lazy', 'lazy-blur', 'lazy-block', 'is-loaded',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Gallery</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Gallery</h1>
  <p class="lede">
    Two ways to lay out a set of images, and one way to load them. Which layout you want
    comes down to a single question: <strong>are the images the same shape?</strong> If they
    are, use tiles. If they are not, use masonry and stop fighting it.
  </p>
</header>

<section class="stack-3">
  <h2 id="gallery">.gallery — equal tiles</h2>
  <p>
    An auto-filling grid of squares. Every child gets
    <code>aspect-ratio: 1</code> and its image is <code>object-fit: cover</code>, so the
    tiles line up regardless of what shape the source files are — at the cost of cropping
    them.
  </p>
  <?php
  docs_example(
      '<div class="gallery" style="max-inline-size:34rem">' . "\n" .
      '  <a href="#"><img src="/assets/deck/docs/sample-1.jpg" alt="A pier at dusk"></a>' . "\n" .
      '  <a href="#"><img src="/assets/deck/docs/sample-2.jpg" alt="Frost on a window"></a>' . "\n" .
      '  <a href="#"><img src="/assets/deck/docs/sample-3.jpg" alt="A tiled roofline"></a>' . "\n" .
      '  <a href="#"><img src="/assets/deck/docs/sample-4.jpg" alt="Reeds in shallow water"></a>' . "\n" .
      '  <a href="#"><img src="/assets/deck/docs/sample-5.jpg" alt="A stairwell from below"></a>' . "\n" .
      '</div>',
      'Tiles are at least 9rem; the grid fits as many as the width allows',
      'stack'
  );
  ?>
  <p>
    Two children can break the grid: <code>.span-2</code> takes two columns and two rows,
    <code>.span-wide</code> takes two columns and stays half the height. Both are useful for
    giving one image prominence without a second layout.
  </p>
  <?php
  docs_example(
      '<div class="gallery" style="max-inline-size:34rem">' . "\n" .
      '  <a class="span-2" href="#"><img src="/assets/deck/docs/sample-1.jpg" alt="A pier at dusk"></a>' . "\n" .
      '  <a href="#"><img src="/assets/deck/docs/sample-2.jpg" alt="Frost on a window"></a>' . "\n" .
      '  <a href="#"><img src="/assets/deck/docs/sample-3.jpg" alt="A tiled roofline"></a>' . "\n" .
      '  <a class="span-wide" href="#"><img src="/assets/deck/docs/sample-4.jpg" alt="Reeds in shallow water"></a>' . "\n" .
      '</div>',
      '.span-2 on the first tile, .span-wide on the last',
      'stack'
  );
  ?>
  <p class="text-muted">
    Images inside a link scale to 1.05 on hover. The tile itself is
    <code>overflow: clip</code>, so the image grows inside a fixed frame rather than pushing
    its neighbours around.
  </p>
</section>

<section class="stack-3">
  <h2 id="masonry">.masonry — ragged heights</h2>
  <p>
    For photographs of mixed shapes, where cropping everything square would throw away the
    composition. <code>.masonry</code> ships <strong>two implementations</strong> and picks
    between them at runtime.
  </p>
  <ul class="stack-2">
    <li>
      <strong>CSS columns</strong> is the baseline and works everywhere. Its weakness is
      reading order: content flows down column one, then down column two, so item 2 is
      underneath item 1 rather than beside it.
    </li>
    <li>
      <strong><code>grid-template-rows: masonry</code></strong> is the real thing and
      preserves row order. It is used automatically wherever it is supported, through a
      <code>@supports</code> block that switches <code>columns</code> back to
      <code>auto</code> and rebuilds the layout as a grid.
    </li>
  </ul>
  <p>
    That means <strong>the reading order of your gallery changes depending on the
    browser</strong>. For photographs it rarely matters. If the order carries meaning —
    a sequence, a ranking — masonry is the wrong layout, because in half the engines the
    order on screen is not the order in the markup.
  </p>
  <?php
  docs_example(
      '<div class="masonry masonry-3" style="max-inline-size:34rem">' . "\n" .
      '  <img src="/assets/deck/docs/sample-1.jpg" alt="A pier at dusk">' . "\n" .
      '  <img src="/assets/deck/docs/sample-2.jpg" alt="Frost on a window">' . "\n" .
      '  <img src="/assets/deck/docs/sample-3.jpg" alt="A tiled roofline">' . "\n" .
      '  <img src="/assets/deck/docs/sample-4.jpg" alt="Reeds in shallow water">' . "\n" .
      '  <img src="/assets/deck/docs/sample-5.jpg" alt="A stairwell from below">' . "\n" .
      '</div>',
      'Heights follow the images; .masonry-3 pins it to three columns',
      'stack'
  );
  ?>
  <p class="text-muted">
    Without a modifier the column count is responsive on its own — one column, then two at
    30rem, three at 48rem, four at 72rem. <code>.masonry-2</code> and
    <code>.masonry-3</code> fix it instead. Both the count and the gap are custom properties
    (<code>--cols</code> and <code>--gap</code>), so an inline style handles a one-off.
  </p>
</section>

<section class="stack-3">
  <h2 id="lazy">.lazy — loading without the jump</h2>
  <p>
    <code>.lazy</code> is a frame with an <code>aspect-ratio</code> and a shimmer. It holds
    the space before a byte of the image arrives, so nothing on the page moves when it lands
    — the shimmer stops and the image fades up in place.
  </p>
  <p>
    <code>deck-extras.js</code> observes each image with an
    <code>IntersectionObserver</code> at a 200px margin, swaps <code>data-src</code> into
    <code>src</code>, and adds <code>.is-loaded</code> once it decodes. Where
    <code>IntersectionObserver</code> is missing, every image is loaded immediately — a
    degradation to "not lazy" rather than to "broken".
  </p>
  <?php
  docs_example(
      '<div class="gallery" style="max-inline-size:26rem">' . "\n" .
      '  <span class="lazy" style="--ratio:1">' . "\n" .
      '    <img data-src="/assets/deck/docs/sample-2.jpg" alt="Frost on a window">' . "\n" .
      '  </span>' . "\n" .
      '  <span class="lazy" style="--ratio:1">' . "\n" .
      '    <img data-src="/assets/deck/docs/sample-3.jpg" alt="A tiled roofline">' . "\n" .
      '  </span>' . "\n" .
      '</div>',
      'data-src rather than src — the frame is what you see until it loads',
      'stack'
  );
  ?>
  <p>
    <code>.lazy-blur</code> adds a blurred backdrop from the element's own
    <code>background-image</code>, for a low-resolution preview you already have; it fades
    out when the real image arrives. <code>.lazy-block</code> is unrelated to images — a
    dashed placeholder box for a region that is still streaming in.
  </p>
  <p class="dx-note text-muted">
    <strong>A failed image shimmers forever.</strong> On an <code>error</code> event the
    script adds <code>.is-error</code> to the <code>.lazy</code> wrapper — and nothing in
    Deck styles that class. There is no rule for it anywhere in <code>src/</code>, so a
    broken image is visually identical to one still loading, indefinitely. Style it yourself
    (see <a href="#overriding">Overriding it</a>). Recorded in <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    These are three separate layouts in <code>src/24-media.css</code> rather than one
    component root, so there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--space-2', '--space-3', '--r-sm', '--r-md', '--line', '--text-faint', '--ink-300', '--bg-sunken', '--dur-3']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Every image needs <code>alt</code>.</strong> Describe what is in the picture,
      not what it is called. A gallery of twelve images alt-texted "photo" is twelve
      identical announcements.
    </li>
    <li>
      <strong>A purely decorative gallery takes <code>alt=""</code>.</strong> Empty, not
      missing — an empty alt removes the image from the tree, while an absent one makes the
      screen reader fall back to reading the filename.
    </li>
    <li>
      <strong>A linked tile needs a name that is not the image.</strong> If the
      <code>&lt;a&gt;</code> contains only an image, the alt becomes the link's name — so
      write it as the destination ("Open: a pier at dusk"), not as a caption.
    </li>
    <li>
      <strong>Masonry's reading order is not stable.</strong> See
      <a href="#masonry">above</a>. Under CSS columns, the DOM order and the visual order
      disagree, so a sighted reader and a screen reader user encounter the images in
      different sequences. It is only acceptable where the order does not matter.
    </li>
    <li>
      <strong>The shimmer is animation.</strong> It runs at 1.6s and is exempt from the
      reduced-motion freeze — see <a href="#motion">Reduced motion</a>. A screenful of
      loading frames is a screenful of movement.
    </li>
    <li>
      <strong>A broken image says nothing.</strong> With <code>.is-error</code> unstyled,
      there is no visual failure state and no announcement. If images can fail, handle it —
      the alt text is what a screen reader falls back to, and a sighted reader gets nothing
      at all.
    </li>
    <li>
      <strong><code>.gallery</code> crops.</strong> <code>object-fit: cover</code> on a
      square tile will cut the top and bottom off a portrait. Where the whole image matters
      — a diagram, a document scan — use masonry or a plain figure.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>.gallery</code> is a grid and fills from the correct edge under
    <code>dir="rtl"</code> with no rule. <code>.masonry</code> in its grid form does the
    same.
  </p>
  <p>
    <code>.masonry</code> in its <em>columns</em> form also reverses — CSS columns follow the
    inline direction — so the first item appears in the rightmost column. Both
    implementations mirror; they simply disagree with each other about order in the same way
    they do in a left-to-right document.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Two things move. The tile hover scale is a normal transition and the global reset in
    <code>src/02-reset.css</code> collapses it.
  </p>
  <p>
    The <code>.lazy</code> shimmer is different: it runs the same
    <code>dk-shimmer</code> keyframes as <a href="skeleton.php#motion">the skeleton</a>, and
    although <code>.lazy</code> is <em>not</em> in the reset's exemption list, the shimmer is
    painted by <code>.lazy::after</code> — the reset targets
    <code>.skeleton</code> and six other selectors by name, so this one is neither exempted
    nor explicitly slowed. It is caught by the blanket
    <code>animation-duration: .01ms</code> rule, which effectively freezes it. A frozen
    shimmer on a loading frame reads as a stalled image, which is the exact failure the
    skeleton exemption exists to prevent.
  </p>
  <p class="text-muted">
    So the two components that do the same job under the same keyframes behave differently
    under reduced motion. Recorded in <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    None of these are in <code>src/99-print.css</code>, but two of them are handled in a second <code>@layer deck.print</code> block at the end of <code>src/24-media.css</code>:
  </p>
  <pre class="dx-code"><code><?= e('.masonry { columns: 2; }
.lazy::after { display: none !important; }') ?></code></pre>
  <p>
    So masonry drops to two columns on paper — four columns at print width is unreadably
    small — and the lazy shimmer is switched off, which stops an unloaded frame printing a
    grey smear over nothing.
  </p>
  <p>
    <code>.gallery</code> has no print rule and keeps its auto-filling grid, which at print
    width usually means the same three or four columns it had on screen.
  </p>
  <p>
    The remaining problem is one no stylesheet can solve: an image below the fold was never
    fetched, because browsers do not run an <code>IntersectionObserver</code> pass before
    printing. The shimmer is gone, so it prints as an empty frame rather than a smear — but
    it is still empty.
  </p>
  <pre class="dx-code"><code><?= e('@media print {
  /* Two columns for tiles as well, matching what .masonry already does */
  .gallery { grid-template-columns: repeat(2, 1fr); }
}') ?></code></pre>
  <p class="text-muted">
    If a page is meant to be printed with its images, load them eagerly rather than lazily.
    There is no CSS fix for a file that was never fetched.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One gallery, no layer needed */
<div class="masonry" style="--cols:2;--gap:1rem"> … </div>

@layer app.components {
  /* Larger tiles */
  .gallery { grid-template-columns: repeat(auto-fill, minmax(min(14rem, 100%), 1fr)); }

  /* A 3:2 gallery instead of squares */
  .gallery > * { aspect-ratio: 3 / 2; }

  /* Give the failed state something to say */
  .lazy.is-error::after { display: none; }
  .lazy.is-error {
    border: 1px dashed var(--line);
    display: grid;
    place-items: center;
  }
  .lazy.is-error::before {
    content: "Image unavailable";
    color: var(--text-faint);
    font-size: var(--text-sm);
  }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not <code>.gallery</code> where cropping loses the point.</strong> Diagrams,
      screenshots and scans need to be seen whole.
    </li>
    <li>
      <strong>Not <code>.masonry</code> where order matters.</strong> The two
      implementations disagree about reading order — see <a href="#masonry">above</a>.
    </li>
    <li>
      <strong>Not <code>.lazy</code> above the fold.</strong> The hero image should be
      eager, and preloaded. Lazy-loading what the reader is already looking at makes the
      page slower, not faster.
    </li>
    <li>
      <strong>Not <code>.lazy</code> on a page that will be printed.</strong> Unloaded
      images print blank.
    </li>
    <li>
      <strong>Not for a carousel.</strong> If the reader is meant to move through the images
      one at a time, that is a <a href="carousel.php">carousel</a>.
    </li>
    <li>
      <strong>Not for two images.</strong> A grid of two is a
      <a href="cluster.php">cluster</a>, and the auto-fill machinery earns nothing.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
