<?php
declare(strict_types=1);

$page = [
    'path' => 'components/print.php',
    'title' => 'Printing',
    'level' => 'Intermediate',
    'description' => 'Deck treats print as a real target: a forced light palette, page-break helpers, repeating table headers, form fields that stay legible, and chrome that is removed. Plus the utilities you drive it with.',
    'documents' => [
        'page-break', 'page-break-after', 'keep-together', 'print-only',
        'print-header', 'print-footer', 'print-keep', 'no-print', 'no-print-url',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Printing</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Printing</h1>
  <p class="lede">
    Deck's own summary is the right framing: <em>real work still gets printed. Invoices,
    packing slips, purchase orders and reports all end up on paper or in a PDF, so this is a
    real stylesheet and not an afterthought.</em> Most of it happens without you doing
    anything; this page is what it does, and the nine classes you use to steer it.
  </p>
</header>

<section class="stack-3">
  <h2 id="where">Where the print rules live</h2>
  <p>
    Two places, and knowing both saves an afternoon. The main sheet is
    <code>src/99-print.css</code>. There is a second
    <code>@layer deck.print</code> block at the end of
    <code>src/24-media.css</code>, holding the print rules for the media and messaging
    components so they sit beside the components themselves.
  </p>
  <p>
    So "is there a print rule for X" is a question about the whole stylesheet, not about one
    file. <code>.video</code>, <code>.chat</code>, <code>.drawer</code>,
    <code>.editor-toolbar</code>, <code>.masonry</code>, <code>.lazy</code>,
    <code>.qr</code>, <code>.mega</code>, <code>.banner</code>,
    <code>.speed-dial</code> and the carousel controls are all handled in the second file.
  </p>
</section>

<section class="stack-3">
  <h2 id="automatic">What happens without you asking</h2>
  <ul class="stack-2">
    <li>
      <strong>The light palette is forced.</strong> <code>color-scheme: light only</code>
      and every surface token overridden to white, text to black, lines to grey. Dark mode
      is a screen idea, and printing a dark theme wastes toner and produces grey text.
    </li>
    <li>
      <strong>Shadows, blurs and animation are removed</strong> from everything, with a
      universal selector. They cost ink and convey nothing on paper.
    </li>
    <li>
      <strong>Deliberate fills are kept.</strong> Badges, chips, chart series, donuts, icon
      tiles and avatars get <code>print-color-adjust: exact</code>, because for those the
      colour <em>is</em> the content. Everything else loses its background the way the
      browser intends.
    </li>
    <li>
      <strong>Chrome is dropped.</strong> The tab bar, floating action button, back-to-top,
      toast region, skip link, theme dock, nav links, sidebar, date picker, combo list,
      menus, data-grid toolbar, sheet grip, pagination, segmented controls and tabs all
      disappear — along with anything you have marked <code>.no-print</code>.
    </li>
    <li>
      <strong>Link targets are printed.</strong> A link whose <code>href</code> starts with
      <code>http</code> gets its address appended in brackets. In-page anchors,
      <code>javascript:</code> links, <code>.btn</code> and anything marked
      <code>.no-print-url</code> are exempt.
    </li>
    <li>
      <strong>Type is resized for paper.</strong> The body drops to 10.5pt with a 1.4 line
      height, and the heading scale is re-expressed in points rather than
      <code>rem</code>.
    </li>
    <li>
      <strong>Headings do not end a page.</strong> <code>break-after: avoid</code> on every
      heading level, and <code>break-inside: avoid</code> on paragraphs, list items,
      figures, <code>.stat</code> and <code>.timeline-item</code>.
    </li>
    <li>
      <strong>Dialogs print only if open</strong>, and then as a block in the flow rather
      than as an overlay.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="tables">Tables get the most work</h2>
  <p>
    Which is right, because a table is what people actually print. Three things happen that
    are worth knowing about:
  </p>
  <ul class="stack-2">
    <li>
      <strong>The header repeats on every page.</strong>
      <code>display: table-header-group</code> on <code>thead</code> — so a fifty-row
      invoice keeps its column headings on page three. The footer gets
      <code>table-footer-group</code> to match.
    </li>
    <li>
      <strong>Rows do not split.</strong> <code>break-inside: avoid</code> on every
      <code>tr</code>.
    </li>
    <li>
      <strong>The mobile card fallback is undone.</strong> This is the clever one.
      <code>.table-stack</code> and <code>.dg-cards</code> turn a table into a stack of
      cards on a narrow screen — and a print sheet is a narrow viewport, so without
      intervention every printed table would come out as cards. The print rules put the
      table back: <code>display: table</code>, real header groups, and
      <code>td::before { content: none }</code> to remove the injected labels.
    </li>
  </ul>
  <p class="text-muted">
    The data grid gets the same treatment plus a few of its own — sticky cells become
    <code>position: static</code>, the scroll wrapper loses its height cap, and cells are
    allowed to wrap rather than being clipped. A printed data grid is a table again.
  </p>
</section>

<section class="stack-3">
  <h2 id="forms">Forms print as forms</h2>
  <p>
    A form on paper is usually something to be filled in by hand, and Deck treats it that
    way. Inputs, textareas and selects keep a visible grey border on white; the textarea
    loses its height cap so a long value prints in full; the select loses its dropdown
    arrow.
  </p>
  <p>
    Buttons and <code>.form-actions</code> are removed — you cannot press a button on
    paper — with one exception: <code>.btn.print-keep</code> survives, bordered, for a
    button whose <em>label</em> is part of the document.
  </p>
  <p>
    Checkboxes and radios are given exact colour rendering, so an unchecked box prints as an
    empty box and a checked one prints filled black rather than both vanishing into white.
  </p>
  <?php
  docs_example(
      '<div class="stack-3" style="max-inline-size:24rem">' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="dx-pr-name">Name</label>' . "\n" .
      '    <input class="input" id="dx-pr-name">' . "\n" .
      '  </div>' . "\n" .
      '  <label class="check">' . "\n" .
      '    <input type="checkbox" checked>' . "\n" .
      '    <span>I have read the terms</span>' . "\n" .
      '  </label>' . "\n" .
      '  <div class="form-actions">' . "\n" .
      '    <button type="button" class="btn btn-primary">Submit</button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Print this page and the button row disappears; the field and the tick do not',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">The nine classes</h2>
  <ul class="stack-2">
    <li><strong><code>.page-break</code></strong> — <code>break-before: page</code>. Start
      this element on a new sheet.</li>
    <li><strong><code>.page-break-after</code></strong> — the same, after.</li>
    <li><strong><code>.keep-together</code></strong> — <code>break-inside: avoid</code>.
      Keep a block whole, for a signature panel or a total.</li>
    <li><strong><code>.no-print</code></strong> — hide on paper.</li>
    <li><strong><code>.print-only</code></strong> — the inverse:
      <code>display: none</code> on screen, revealed when printing. For a signature line, a
      reference number, or terms that would be clutter on screen.</li>
    <li><strong><code>.no-print-url</code></strong> — suppress the appended address on one
      link. It is <code>.no-print-url::after</code>, so it goes on the
      <code>&lt;a&gt;</code> itself.</li>
    <li><strong><code>.print-keep</code></strong> — on a <code>.btn</code>, keep it
      visible.</li>
    <li><strong><code>.print-header</code> and <code>.print-footer</code></strong> —
      <code>position: fixed</code> blocks that repeat on every printed page.</li>
  </ul>
  <?php
  docs_example(
      '<div class="card stack-3" style="max-inline-size:26rem">' . "\n" .
      '  <p>Visible on screen and on paper.</p>' . "\n" .
      '  <p class="no-print text-muted">Screen only — a hint, a control, a nudge.</p>' . "\n" .
      '  <p class="print-only">Paper only — reference ORD-4417-QK, printed from example.com.</p>' . "\n" .
      '  <div class="keep-together stack-1">' . "\n" .
      '    <strong>Signature</strong>' . "\n" .
      '    <p class="text-muted">This block will not be split across two pages.</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Use your browser\'s print preview to see the other half of this example',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <code>.print-only</code> is the one to reach for more often than people do. A printed
    invoice usually wants a reference, a date printed and a "page 1 of 3" that would be
    noise on screen — and putting them in the markup is simpler than generating a separate
    print view.
  </p>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    Printing does not use Deck's tokens so much as <strong>replace</strong> them. The
    <code>:root</code> block inside <code>@media print</code> overrides fifteen of them with
    literal values — white surfaces, black text, grey lines, no shadows — so every component
    that reads <code>var(--surface)</code> or <code>var(--text)</code> becomes
    print-appropriate without any component needing a print rule of its own.
  </p>
  <?php docs_token_table(['--bg', '--surface', '--surface-2', '--text', '--text-muted', '--text-faint', '--line', '--line-strong', '--shadow-1']); ?>
  <p class="text-muted">
    The values above are the screen values. On paper each of these becomes a literal:
    <code>#fff</code>, <code>#000</code>, <code>#333</code>, <code>#555</code>,
    <code>#bbb</code>, <code>#888</code> and <code>none</code>. That token-override
    approach is why so little per-component print CSS is needed.
  </p>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Paper has no assistive technology.</strong> A printed page is read by eye or
      not at all, which makes the ordinary rules sharper rather than looser: anything
      carried by colour, hover, or an <code>aria-label</code> is gone.
    </li>
    <li>
      <strong>Backgrounds are dropped by default</strong>, and most readers never turn
      "background graphics" on. Any status shown only as a coloured fill prints as nothing.
      This is the single most common way a printed page loses information — see the
      <a href="indicator.php#print">indicator</a> and
      <a href="timeline.php#print">timeline</a> pages for what that looks like in practice.
    </li>
    <li>
      <strong>Print in black and white before you ship it.</strong> Deck's status colours
      are distinguishable in colour and much less so in greyscale. A "paid" and an
      "overdue" badge that print as two similar greys are worse than no badge.
    </li>
    <li>
      <strong>10.5pt is small for some readers.</strong> It is a sensible default for a
      dense report, and it is not a sensible default for something an older reader will keep
      by the phone. Override it per document.
    </li>
    <li>
      <strong>Appended URLs are long and unreadable.</strong> They use
      <code>word-break: break-all</code>, which produces addresses broken mid-word across
      lines. Useful on a reference document, noise on everything else —
      <code>.no-print-url</code> per link, or suppress them wholesale.
    </li>
    <li>
      <strong>Do not hide content from the screen that only exists on paper.</strong>
      <code>.print-only</code> is for restating things — a reference, a date — not for
      information a screen reader would then never encounter.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>@page</code> margins are set with the physical
    <code>margin: 16mm 14mm</code> shorthand, which is symmetrical, so nothing is wrong
    under <code>dir="rtl"</code>. <code>.print-header</code> and
    <code>.print-footer</code> use <code>inset-inline</code>, so they span correctly in
    either direction.
  </p>
  <p>
    The page size is the thing to change: <code>@page { size: letter }</code> is a US
    default, and most of the world prints A4. That is not an RTL issue but it lands in the
    same place — a document typeset for letter and printed on A4 loses about 18mm off the
    bottom.
  </p>
  <pre class="dx-code"><code><?= e('@page { size: A4; }') ?></code></pre>
  <p class="text-muted">
    One line in your own CSS. There is no cascade layer question here — <code>@page</code>
    is not subject to layers in the way ordinary rules are, and a later declaration wins.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Not applicable, and handled anyway: the universal selector inside
    <code>@media print</code> sets <code>animation: none !important</code> and
    <code>transition: none !important</code> on everything. Browsers do not run animations
    when printing regardless, so this is belt and braces — but it also guarantees an element
    prints in its final state rather than wherever an animation had reached.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing this page</h2>
  <p>
    The section every other page has is this whole page, so instead: the honest way to check
    print CSS is your browser's print preview, with <strong>background graphics off</strong>,
    at A4 and at letter, in greyscale. Deck's print sheet is good, and it cannot know which
    of your colours carried meaning.
  </p>
  <p class="text-muted">
    Chrome's preview updates live as you edit, which makes it a usable development surface
    rather than a final check.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* A4 rather than letter */
@page { size: A4; margin: 18mm 16mm; }

@layer app.overrides {
  @media print {
    /* Larger type for a document people keep */
    html, body { font-size: 12pt; }

    /* Drop the appended addresses everywhere */
    a[href^="http"]::after { content: none; }

    /* Start every invoice on its own sheet */
    .invoice + .invoice { break-before: page; }
  }
}') ?></code></pre>
  <p class="text-muted">
    <code>app.overrides</code> is the last layer in Deck's cascade, which is what you want
    here — the print sheet uses <code>!important</code> liberally, and matching it from a
    later layer is cleaner than escalating.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not <code>.print-only</code> for anything essential.</strong> Screen readers
      never reach it.
    </li>
    <li>
      <strong>Not <code>.no-print</code> as a general hide.</strong> It is
      <code>display: none</code> in a print context only; on screen the element is still
      there. <code>.hidden</code> or <code>[hidden]</code> is what you want otherwise.
    </li>
    <li>
      <strong>Not <code>.print-header</code> for a long header.</strong> It is
      <code>position: fixed</code> and repeats on every page, so anything tall eats the
      whole document.
    </li>
    <li>
      <strong>Not a screen layout for a document people file.</strong> If a printed invoice
      is a real deliverable, it deserves its own template — the print sheet makes a screen
      page acceptable on paper, not optimal.
    </li>
    <li>
      <strong>Not relying on background colour.</strong> It is off by default in every
      browser's print dialogue, and most people never change it.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
