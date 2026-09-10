<?php
declare(strict_types=1);

$page = [
    'path' => 'components/qr.php',
    'title' => 'QR code',
    'level' => 'Intermediate',
    'description' => 'Deck encodes QR codes in the browser with no dependency and renders them as SVG. The frame stays white in dark mode on purpose, because a scanner is not reading your theme.',
    'documents' => [
        'qr', 'qr-caption', 'qr-lg', 'qr-logo', 'qr-logo-mark', 'qr-sm',
    ],

    'component' => 'qr',
    'accounts' => [
        '24-media.css' => 'documented: the frame and its quiet zone, the SVG sizing and module fill, the two sizes, the punched-out logo and the caption — plus the print rule in the same file that drops the border and keeps the code on one page',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">QR code</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>QR code</h1>
  <p class="lede">
    A working QR encoder, in the browser, with no dependency — Galois field arithmetic,
    Reed–Solomon error correction and all. It renders as SVG, so it stays sharp at any size
    and prints at the printer's resolution rather than the screen's.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    To move something from a screen to a phone, or from paper to a phone: a ticket, a
    Wi-Fi network, a link to a page someone is standing in front of, a two-factor
    enrolment.
  </p>
  <p>
    The test is whether the reader has a second device. A QR code on a phone screen, to be
    scanned by that same phone, is a puzzle rather than a shortcut — use a link.
  </p>
  <?php
  docs_example(
      '<span class="qr" data-deck-qr="https://example.com/tickets/9fQ2xR"' . "\n" .
      '      role="img" aria-label="QR code for your ticket"></span>',
      'The value is the attribute; deck-extras.js renders the modules on load',
      'stack'
  );
  ?>
  <p class="text-muted">
    An empty <code>data-deck-qr</code> falls back to the element's own text content, which
    is occasionally handy — the value stays visible and selectable if the script never
    runs.
  </p>
</section>

<section class="stack-3">
  <h2 id="options">Options</h2>
  <ul class="stack-2">
    <li>
      <strong><code>data-deck-qr</code></strong> — the value to encode. Anything: a URL, a
      <code>WIFI:</code> string, an <code>otpauth://</code> URI.
    </li>
    <li>
      <strong><code>data-ecl</code></strong> — error-correction level,
      <code>L</code>, <code>M</code>, <code>Q</code> or <code>H</code>, defaulting to
      <code>M</code>. Higher levels survive more damage and hold less data.
    </li>
    <li>
      <strong><code>data-caption</code></strong> — renders a
      <code>.qr-caption</code> underneath, capped to the code's own width and set to
      <code>overflow-wrap: anywhere</code> so a long URL breaks rather than stretching the
      layout.
    </li>
  </ul>
  <?php
  docs_example(
      '<div class="cluster" style="align-items:flex-start">' . "\n" .
      '  <span class="qr qr-sm" data-deck-qr="https://example.com/a"' . "\n" .
      '        role="img" aria-label="QR code, small"></span>' . "\n" .
      '  <span class="qr" data-deck-qr="https://example.com/b" data-ecl="Q"' . "\n" .
      '        data-caption="example.com/b"' . "\n" .
      '        role="img" aria-label="QR code for example.com/b"></span>' . "\n" .
      '  <span class="qr qr-lg" data-deck-qr="https://example.com/c" data-ecl="H"' . "\n" .
      '        role="img" aria-label="QR code, large"></span>' . "\n" .
      '</div>',
      '104px, 160px and 240px — set by --qr-size, which the size classes change',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <strong>Versions 1 to 10 only.</strong> The encoder covers the smaller symbol versions,
    which is roughly 170 alphanumeric characters at level M and considerably fewer at H.
    Past that it throws, and the component catches it and renders the error message in
    place of the code — so a value that is too long shows you a sentence rather than
    failing silently. Shorten it, or link to it.
  </p>
</section>

<section class="stack-3">
  <h2 id="logo">A logo in the middle</h2>
  <p>
    <code>.qr-logo</code> on the frame and <code>.qr-logo-mark</code> on a child punches a
    mark out of the centre — a white square at 22% of the code's width, with your logo
    inside it.
  </p>
  <p>
    This works because QR codes carry error correction, and it works only within that
    budget. The source is explicit about the limit: <strong>only safe at error correction Q
    or H</strong>. At the default <code>M</code> you are spending redundancy you may need
    for a crease in the paper or a reflection on a screen.
  </p>
  <?php
  docs_example(
      '<span class="qr qr-lg qr-logo" data-deck-qr="https://example.com/deck" data-ecl="H"' . "\n" .
      '      role="img" aria-label="QR code for example.com/deck">' . "\n" .
      '  <span class="qr-logo-mark">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#deck-mark"></use></svg>' . "\n" .
      '  </span>' . "\n" .
      '</span>',
      'data-ecl="H" is not optional here',
      'stack'
  );
  ?>
  <p class="text-muted">
    Test the result by scanning it, on a real phone, from the size it will actually be
    printed at. A code that scans on a 240px screen may not scan at 20mm on a receipt.
  </p>
</section>

<section class="stack-3">
  <h2 id="colour">Why it stays white in dark mode</h2>
  <p>
    <code>.qr</code> sets <code>background: var(--qr-bg, #fff)</code> and fills its modules
    with <code>var(--qr-fg, #000)</code>. Both defaults are literal black and white rather
    than Deck tokens, so the code does <strong>not</strong> follow
    <code>light-dark()</code> and does not invert in dark mode. In a dark interface you get
    a white card.
  </p>
  <p>
    That is deliberate and it is the right call. A scanner is not reading your theme: it is
    looking for high contrast between light and dark modules in the orientation the spec
    describes. Inverted codes are read by some scanners and not others, and low-contrast
    codes by almost none. The white card is slightly ugly and always works.
  </p>
  <p class="text-muted">
    The two custom properties are there if you have a real reason — a brand-coloured
    foreground on white can scan fine if the contrast is high enough. Change the foreground,
    keep the background light, and test it before shipping.
  </p>
  <p class="dx-note text-muted">
    <code>--qr-quiet</code> is the padding, and it defaults to 12px. That white margin is
    part of the specification, not decoration: scanners use it to find the symbol's edges.
    Setting it to zero produces a code that looks right and frequently will not scan.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/24-media.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--line', '--r-sm', '--r-xs', '--text-xs', '--text-muted', '--space-2']); ?>
  <p class="text-muted">
    <code>--qr-size</code>, <code>--qr-quiet</code>, <code>--qr-bg</code> and
    <code>--qr-fg</code> are component-level custom properties with literal fallbacks, not
    Deck tokens — which is the whole point of <a href="#colour">the section above</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>A QR code is an image and announces as nothing.</strong> The rendered SVG is a
      grid of unlabelled rectangles. Give the frame <code>role="img"</code> and an
      <code>aria-label</code> saying what scanning it does — every example here does.
    </li>
    <li>
      <strong>Never make it the only route.</strong> This is the important one. A QR code is
      unusable to a blind reader, to anyone without a second device, and to anyone reading a
      printout at a desk. The same destination must be available as a link or as text you
      can type — <code>data-caption</code> exists partly for this.
    </li>
    <li>
      <strong>Scanning needs steady hands and a working camera.</strong> Motor impairment,
      low vision and an old phone all make it harder. It is a convenience, and treating it
      as a primary interface excludes people quietly.
    </li>
    <li>
      <strong>The caption should be readable, not just present.</strong>
      <code>.qr-caption</code> is <code>--text-xs</code> and muted, which is Deck's smallest
      text. If it carries the fallback URL, consider making it larger — the whole point is
      that someone can read and type it.
    </li>
    <li>
      <strong>Do not encode something the reader cannot verify.</strong> A code is opaque
      until it is scanned, so a reader has no way to know where it goes. Print the
      destination beside it, which is both an accessibility measure and a phishing one.
    </li>
    <li>
      <strong>Size for the distance.</strong> A poster code scanned from two metres needs to
      be much larger than <code>.qr-lg</code>. The usual rule is roughly one tenth of the
      scanning distance.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    A QR code has a fixed orientation defined by the specification and does not mirror in
    any direction — a flipped code does not scan. Nothing in the component is directional
    and nothing in <code>src/19-logical.css</code> touches it, which is correct.
  </p>
  <p>
    The caption is ordinary text and follows the document direction, so an Arabic caption
    reads right to left under a code that has not moved. A URL inside it stays left-to-right
    on its own, through the bidirectional algorithm rather than through any rule of Deck's.
  </p>
  <?php
  docs_example(
      '<span dir="rtl" class="qr" data-deck-qr="https://example.com/tickets/9fQ2xR"' . "\n" .
      '      data-caption="امسح للحصول على تذكرتك"' . "\n" .
      '      role="img" aria-label="رمز الاستجابة السريعة للتذكرة"></span>',
      'The code is unchanged; the caption reads right to left',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing here animates — no transitions, no keyframes, no transforms. The code appears
    when the script has finished encoding it, which is not motion. There is nothing for
    <code>prefers-reduced-motion</code> to act on.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    This is the one component in Deck that is <em>designed</em> for print, and it shows. The
    print block at the end of <code>src/24-media.css</code> reads:
  </p>
  <pre class="dx-code"><code><?= e('/* A QR code is often the whole point of printing the page */
.qr { border: 0; break-inside: avoid; }') ?></code></pre>
  <p>
    The border goes because on paper the code needs no frame, and
    <code>break-inside: avoid</code> stops it being split across two pages — a half-printed
    QR code is not a degraded QR code, it is nothing at all.
  </p>
  <p>
    The rest is handled on the component itself rather than in a print block:
    <code>.qr</code> carries <code>print-color-adjust: exact</code>, which is what stops the
    browser dropping its white background and black modules as "decorative" colour. Without
    that line the code would print as an empty square on most default settings.
  </p>
  <p class="text-muted">
    And because it is SVG, it prints at the printer's resolution. A raster QR code scaled up
    for print acquires soft edges, and soft edges are what a scanner fails on.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One code, no layer needed */
<span class="qr" style="--qr-size:180px" data-deck-qr="…"></span>

@layer app.components {
  /* A brand-coloured foreground. Keep the background light and test it. */
  .qr-brand { --qr-fg: oklch(30% .09 264); }

  /* A larger quiet zone for a code that will be printed small */
  .qr-print { --qr-quiet: 20px; }

  /* A readable caption */
  .qr-caption { font-size: var(--text-sm); color: var(--text); }
}') ?></code></pre>
  <p class="text-muted">
    <code>Deck.qr</code> exposes <code>svg()</code>, <code>build()</code> and
    <code>encode()</code> if you need a code somewhere the attribute cannot reach — a canvas,
    a download, a value that only exists after a fetch.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not on a phone, for that phone.</strong> If the reader is already holding the
      destination, give them a link.
    </li>
    <li>
      <strong>Not as the only way to reach something.</strong> See
      <a href="#accessibility">Accessibility</a> — this is the rule that matters most on
      this page.
    </li>
    <li>
      <strong>Not for more than about 170 characters.</strong> The encoder stops at version
      10 and will tell you so. Encode a short link, not the payload.
    </li>
    <li>
      <strong>Not with a logo at the default error-correction level.</strong>
      <code>data-ecl="Q"</code> at minimum, and test it.
    </li>
    <li>
      <strong>Not for a secret.</strong> A code on screen is readable by anyone in the room
      and by anyone in a photograph of the room. Enrolment codes should be short-lived.
    </li>
    <li>
      <strong>Not without the quiet zone.</strong> Setting <code>--qr-quiet: 0</code> to
      make it look tidier is the single most reliable way to produce a code that does not
      scan.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
