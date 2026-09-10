<?php
declare(strict_types=1);

$page = [
    'path' => 'components/video.php',
    'title' => 'Video',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .video is a ratio-locked frame for a native video or a third-party embed, with an optional poster overlay for click-to-load. The click handler is yours — Deck ships no video JavaScript.',
    'documents' => [
        'video', 'video-cover', 'video-duration', 'video-meta', 'video-play',
        'video-portrait', 'video-poster', 'video-square', 'video-wide',
    ],

    'component' => 'video',
    'accounts' => [
        '24-media.css' => 'documented: the ratio-locked frame, the three ratio modifiers and the cover variant, the native control tweaks, the poster overlay with its scrim and play button, and the meta and duration overlays',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Video</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Video</h1>
  <p class="lede">
    A frame that reserves the right amount of space before anything loads, holds a
    <code>&lt;video&gt;</code> or an <code>&lt;iframe&gt;</code> at a fixed ratio, and
    optionally covers it with a poster until someone asks for it. The last part is markup
    only — Deck ships no video behaviour at all.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Any time you put a video on a page. The frame's job is to stop the layout jumping when
    the player initialises, and that applies equally to a self-hosted file and to a YouTube
    embed.
  </p>
  <?php
  docs_example(
      '<div class="video" style="max-inline-size:32rem">' . "\n" .
      '  <video controls preload="metadata" poster="/assets/deck/docs/sample-3.jpg">' . "\n" .
      '    <source src="/assets/deck/docs/sample.mp4" type="video/mp4">' . "\n" .
      '    <track kind="captions" src="/assets/deck/docs/sample.vtt" srclang="en" label="English" default>' . "\n" .
      '    Your browser cannot play this video.' . "\n" .
      '  </video>' . "\n" .
      '</div>',
      'The frame holds 16:9 whether or not the file loads',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    That example deliberately points at a file that is not there, so you can see what the
    frame does on its own: it reserves the space, stays 16:9, and shows the poster. This is
    the failure mode you want — a placeholder of the right size rather than a collapsed
    element and a page that reflows when the real file arrives.
  </p>
</section>

<section class="stack-3">
  <h2 id="ratios">Ratios and fit</h2>
  <p>
    The frame is <code>aspect-ratio: var(--ratio, 16 / 9)</code>. Three modifiers change it,
    and <code>--ratio</code> takes anything else directly.
  </p>
  <ul class="stack-2">
    <li><code>.video-square</code> — 1:1, for a social embed.</li>
    <li><code>.video-portrait</code> — 9:16, for vertical video.</li>
    <li><code>.video-wide</code> — 21:9, for anamorphic footage.</li>
  </ul>
  <p>
    Inside the frame, the media is <code>object-fit: contain</code> by default: the whole
    picture is visible and the mismatch shows as letterboxing against the frame's dark
    background. <code>.video-cover</code> switches a
    <code>&lt;video&gt;</code> to <code>cover</code> instead, filling the frame and cropping
    the overflow.
  </p>
  <?php
  docs_example(
      '<div class="cluster" style="align-items:flex-start">' . "\n" .
      '  <div class="video video-square" style="inline-size:11rem">' . "\n" .
      '    <img src="/assets/deck/docs/sample-3.jpg" alt="A tiled roofline">' . "\n" .
      '  </div>' . "\n" .
      '  <div class="video video-portrait" style="inline-size:8rem">' . "\n" .
      '    <img src="/assets/deck/docs/sample-2.jpg" alt="Frost on a window">' . "\n" .
      '  </div>' . "\n" .
      '  <div class="video video-wide" style="inline-size:16rem">' . "\n" .
      '    <img src="/assets/deck/docs/sample-1.jpg" alt="A pier at dusk">' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Three ratios. Note the letterboxing where the source does not match',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <strong>An <code>&lt;img&gt;</code> is not covered by the fill rule.</strong> The
    selector is <code>.video &gt; :is(video, iframe, embed, object)</code> — no
    <code>img</code>. The examples above use images to show the ratios without loading five
    video files, and they are sized by the frame rather than positioned by it. For a real
    poster image use <a href="#poster"><code>.video-poster</code></a>, which does handle it.
  </p>
</section>

<section class="stack-3">
  <h2 id="poster">The poster overlay</h2>
  <p>
    A third-party embed loads a player, a tracker and several hundred kilobytes the moment
    the page renders, whether or not anyone watches. <code>.video-poster</code> is the
    façade that avoids it: a full-bleed image, a gradient scrim, a play button, and no
    embed until it is clicked.
  </p>
  <p>
    <strong>Deck supplies the appearance and none of the behaviour.</strong> There is no
    script watching for a click on <code>.video-poster</code>. Swapping the poster for the
    real player is a handful of lines you write:
  </p>
  <pre class="dx-code"><code><?= e('document.querySelectorAll(\'.video-poster\').forEach(btn => {
  btn.addEventListener(\'click\', () => {
    const frame = btn.closest(\'.video\');
    const iframe = document.createElement(\'iframe\');
    iframe.src = btn.dataset.src + \'?autoplay=1\';
    iframe.allow = \'autoplay; fullscreen; picture-in-picture\';
    iframe.title = btn.dataset.title;
    frame.append(iframe);
    btn.remove();
  });
});') ?></code></pre>
  <?php
  docs_example(
      '<div class="video" style="max-inline-size:30rem">' . "\n" .
      '  <button type="button" class="video-poster"' . "\n" .
      '          aria-label="Play: Building a component library in an afternoon">' . "\n" .
      '    <img src="/assets/deck/docs/sample-1.jpg" alt="">' . "\n" .
      '    <span class="video-play">' . "\n" .
      '      <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>' . "\n" .
      '    </span>' . "\n" .
      '  </button>' . "\n" .
      '  <span class="video-meta">' . "\n" .
      '    <span class="video-duration">12:04</span>' . "\n" .
      '  </span>' . "\n" .
      '</div>',
      'A real button with a real label — the image alt is empty because the button is named',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <strong>Deck's sprite has no transport icons.</strong> There is no play, pause, stop,
    mute or fullscreen symbol in <code>deck-icons.svg</code> — the 77 icons are interface
    and status glyphs, and media controls are not among them. The examples here use an
    inline triangle. That is not a workaround so much as the honest answer: the native
    <code>controls</code> attribute draws its own, and a poster façade needs exactly one
    custom glyph.
  </p>
  <p class="text-muted">
    <code>.video-play</code> is a 64px disc using <code>backdrop-filter: blur(8px)</code>
    over a 90%-opaque surface colour. Where <code>backdrop-filter</code> is unsupported the
    blur is skipped and the disc is still nearly opaque, so it stays legible — the effect
    degrades rather than failing.
  </p>
  <p class="text-muted">
    <code>.video-meta</code> is a bottom-start overlay for a duration or a channel name, and
    <code>.video-duration</code> is the dark pill inside it. Both sit above the poster's
    scrim, which is what makes white text readable over an arbitrary image.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/24-media.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--ink-950', '--surface', '--text', '--brand-500', '--shadow-3', '--r-md', '--r-full', '--r-xs', '--text-xs', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Captions are not optional, and Deck cannot provide them.</strong> A
      <code>&lt;track kind="captions"&gt;</code> on a self-hosted video, or captions enabled
      on the hosted one. Everything else on this list is smaller than this.
    </li>
    <li>
      <strong>Use <code>controls</code>.</strong> The native control set is keyboard
      operable, screen-reader labelled and familiar. Deck restyles only the WebKit panel
      background and the accent colour, precisely so the rest keeps working.
    </li>
    <li>
      <strong>The poster must be a real <code>&lt;button&gt;</code>.</strong>
      <code>.video-poster</code> is styled as one — border and padding reset — but the class
      does not make it focusable. A <code>&lt;div&gt;</code> here is unreachable by keyboard.
    </li>
    <li>
      <strong>Name the button with the video's title.</strong>
      <code>aria-label="Play: …"</code>. If the poster image is decorative alongside that
      label, give it <code>alt=""</code> so it is not announced twice.
    </li>
    <li>
      <strong>An <code>&lt;iframe&gt;</code> needs a <code>title</code>.</strong> Without
      one it is announced as an unlabelled frame. The snippet above sets it when the embed
      is created.
    </li>
    <li>
      <strong>Do not autoplay with sound.</strong> If a video must autoplay it has to be
      muted and it has to be stoppable. Motion that starts on its own is covered by
      <a href="#motion">Reduced motion</a>, and it is also simply rude.
    </li>
    <li>
      <strong><code>.video-cover</code> crops.</strong> If the subject is near the edge of
      the frame it will be cut off, and there is no focal-point control. Prefer
      <code>contain</code> unless you know the footage.
    </li>
    <li>
      <strong>The duration overlay is real text and is announced.</strong> "12:04" read
      aloud in the middle of a link name is confusing, so keep it outside the button — as
      the example does — or hide it and put the duration in the button's label.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The frame itself has no direction. <code>.video-meta</code> is placed with
    <code>inset-inline-start</code>, so the duration moves to the right-hand corner under
    <code>dir="rtl"</code> without a rule in <code>src/19-logical.css</code>.
  </p>
  <p>
    The play triangle keeps pointing right, and that is deliberate.
    <code>src/19-logical.css</code> auto-mirrors ten icons from Deck's sprite by name —
    the chevrons, the arrows, <code>#external</code>, <code>#log-out</code>,
    <code>#send</code> and the two trend icons — and <code>#play</code> is not one of them.
    A media transport is a timeline rather than a sentence, and it runs left to right in
    every locale, so a mirrored play button would be pointing at the wrong end of the
    video.
  </p>
  <p class="text-muted">
    If you do want a directional icon of your own to flip, that is
    <a href="icon.php#rtl"><code>.mirror-rtl</code></a>; <code>.no-flip</code> is the escape
    hatch in the other direction, for an icon inside an ancestor you have already mirrored.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="video" style="max-inline-size:24rem">' . "\n" .
      '  <button type="button" class="video-poster" aria-label="تشغيل: بناء مكتبة مكوّنات">' . "\n" .
      '    <img src="/assets/deck/docs/sample-4.jpg" alt="">' . "\n" .
      '    <span class="video-play">' . "\n" .
      '      <svg class="icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>' . "\n" .
      '    </span>' . "\n" .
      '  </button>' . "\n" .
      '  <span class="video-meta"><span class="video-duration">١٢:٠٤</span></span>' . "\n" .
      '</div>',
      'The duration pill moves to the right corner',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Deck's own motion here is one transition: <code>.video-play</code> scales to 1.08 on
    hover over <?= e(api_token('--dur-2')['value'] ?? '180ms') ?>, collapsed by the global
    reset under <code>prefers-reduced-motion: reduce</code>.
  </p>
  <p>
    The video itself is the real question and the stylesheet cannot answer it. A
    <code>prefers-reduced-motion</code> setting is a request not to be shown unexpected
    movement, and an autoplaying video is the largest possible violation of it. If you
    autoplay anything — a background loop, a silent demo — gate it in script on
    <code>matchMedia('(prefers-reduced-motion: reduce)')</code> and show the poster instead.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.video</code> does not print. It is not in <code>src/99-print.css</code> — but it
    is in a second <code>@layer deck.print</code> block at the end of <code>src/24-media.css</code>, in the same
    <code>display: none !important</code> list as <code>.drawer</code> and
    <code>.mega</code>. Frame, poster, play button and duration all go.
  </p>
  <p>
    That is a defensible default, since a video is the one thing paper genuinely cannot
    reproduce. It also means a printed article silently loses its illustrations, which is
    not always what you want — a tutorial whose screenshots are video posters prints with
    gaps in the argument.
  </p>
  <pre class="dx-code"><code><?= e('@media print {
  /* Print the poster, drop the parts that imply interaction */
  .video { display: block !important; }
  .video-play, .video-poster::after, .video-meta { display: none; }
}') ?></code></pre>
  <p class="text-muted">
    <code>display: block</code> rather than <code>revert</code>, because the rule you are
    overriding carries <code>!important</code> and you need the same weight to beat it.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One frame, no layer needed */
<div class="video" style="--ratio:4/3"> … </div>

@layer app.components {
  /* A lighter frame for a light-only site */
  .video { background: var(--ink-200); }

  /* Bigger play affordance on touch */
  @media (pointer: coarse) {
    .video-play { inline-size: 76px; block-size: 76px; }
  }

  /* Let an <img> fill the frame like the media does */
  .video > img { position: absolute; inset: 0; inline-size: 100%; block-size: 100%; object-fit: cover; }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for an animated GIF or a decorative loop.</strong> Those are images with
      no controls; wrap them in a plain figure and respect reduced motion.
    </li>
    <li>
      <strong>Not for audio.</strong> An <code>&lt;audio&gt;</code> element has no picture,
      so a ratio-locked frame reserves space for nothing.
    </li>
    <li>
      <strong>Not without captions.</strong> See <a href="#accessibility">Accessibility</a>.
      This is the one that makes a video usable or not.
    </li>
    <li>
      <strong>Not with a poster you have not wired up.</strong> A play button that does
      nothing is worse than no façade at all — Deck supplies no click handler.
    </li>
    <li>
      <strong>Not as a background.</strong> Video behind text is a contrast problem that
      changes every frame, and it is motion the reader did not ask for.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
