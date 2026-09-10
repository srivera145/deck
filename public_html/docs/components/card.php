<?php
declare(strict_types=1);

$page = [
    'path' => 'components/card.php',
    'title' => 'Card',
    'level' => 'Beginner',
    'description' => 'Deck\'s .card: a bordered surface with no padding of its own, a header, body and footer that own their spacing, a whole-card link pattern, and a container-query variant that becomes a media object.',
    'documents' => [
        'card', 'card-body', 'card-brand', 'card-flex', 'card-flush',
        'card-footer', 'card-header', 'card-link', 'card-media', 'card-raised',
        'card-title',
    ],

    'component' => 'card',
    'accounts' => [
        '07-components.css' => 'documented: the card itself, its regions, the link pattern and the three surface variants',
        '18-container.css'  => 'documented: .card.cq makes a card a query container, and .card-flex reflows on its own width — the Responds to its own width section',
        '99-print.css'      => 'documented: cards keep a hairline border and avoid breaking across pages — the Printing section',
        '16-motion.css'     => 'documented: .icon-follow steps forward on card-link hover — the Reduced motion section',
        '19-logical.css'    => 'internal: mirrors that same icon step under dir="rtl", so the arrow moves toward the text end',
        '26-perf.css'       => 'internal: .card is given contain: layout style, so one card relaying out cannot reflow the rest of the page',
    ],
];

require __DIR__ . '/../_layout.php';

$surfaces = [
    ['card', 'Default', 'Border, radius and a one-pixel shadow. The everyday surface.'],
    ['card-raised', 'Raised', 'A deeper shadow and no visible border, for something floating above the page.'],
    ['card-flush', 'Flush', 'No border, no shadow, no background. Grouping without a box drawn around it.'],
    ['card-brand', 'Brand', 'Brand-tinted background and border. For the one card that is an offer or a callout.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Card</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Card</h1>
  <p class="lede">
    <code>.card</code> is a surface: a background, a one-pixel border, a
    <?= e(api_token('--r-md')['value'] ?? '12px') ?> radius and a shadow. It is a flex
    column that brings <strong>no padding of its own</strong>, which is the one thing
    about it worth remembering — the padding lives in <code>.card-header</code>,
    <code>.card-body</code> and <code>.card-footer</code>, so each region owns its own
    spacing and an image can still run edge to edge.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a card when a group of content is a <em>thing</em> — one order, one product, one
    person — and the reader will scan several of them side by side. The border is doing
    the work of saying where one thing ends and the next begins.
  </p>
  <p>
    If there is only ever one of them on the page, you probably want
    <code>.panel</code> or nothing at all. A single card on a white page is a border
    drawn around the whole content, which tells the reader nothing.
  </p>
  <?php
  docs_example(
      '<article class="card" style="max-inline-size:22rem">' . "\n" .
      '  <div class="card-body">' . "\n" .
      '    <h3 class="card-title">Order #4417</h3>' . "\n" .
      '    <p class="text-muted">Shipped 3 March, arriving Thursday.</p>' . "\n" .
      '  </div>' . "\n" .
      '</article>',
      'The smallest useful card: a surface and one padded region',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="regions">Header, body, footer</h2>
  <p>
    The three regions are the reason the card itself has no padding. Each one sets its
    own, and the borders between them come from the regions rather than from the card,
    so a card with only a body has no stray dividing lines.
  </p>
  <ul class="stack-2">
    <li>
      <code>.card-header</code> and <code>.card-footer</code> are both
      <code>flex</code> rows with <code>align-items: center</code>, so a title and a
      button sit on the same line without any extra wrapper. Add <code>.push</code> on a
      child to shove the rest to the far end.
    </li>
    <li>
      <code>.card-footer</code> also sets <code>margin-block-start: auto</code>. In a
      grid of cards of unequal length that pins every footer to the bottom, so a row of
      cards has its buttons aligned even when the text above them does not match.
    </li>
    <li>
      <code>.card-body</code> is a flex column with a
      <?= e(api_token('--space-3')['value'] ?? '.75rem') ?> gap, so its children space
      themselves without margins.
    </li>
  </ul>
  <?php
  docs_example(
      '<article class="card" style="max-inline-size:26rem">' . "\n" .
      '  <div class="card-header">' . "\n" .
      '    <h3 class="card-title">Invoice INV-2291</h3>' . "\n" .
      '    <span class="badge badge-good push">Paid</span>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="card-body">' . "\n" .
      '    <p>Two licences, billed annually.</p>' . "\n" .
      '    <p class="text-muted text-sm">Charged to the card ending 4242.</p>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="card-footer">' . "\n" .
      '    <button class="btn btn-sm">Download PDF</button>' . "\n" .
      '    <button class="btn btn-sm btn-ghost push">Email a copy</button>' . "\n" .
      '  </div>' . "\n" .
      '</article>',
      'Header, body and footer, with .push doing the alignment',
      'stack'
  );
  ?>

  <h3 id="equal-footers">Why the footers line up</h3>
  <p>
    The two cards below hold different amounts of text. The footer is pinned to the
    bottom of each by <code>margin-block-start: auto</code> rather than by a fixed
    height, so nothing has to be measured and nothing breaks when the text changes.
  </p>
  <?php
  docs_example(
      '<div class="grid grid-tight">' . "\n" .
      '  <article class="card">' . "\n" .
      '    <div class="card-body">' . "\n" .
      '      <h3 class="card-title">Starter</h3>' . "\n" .
      '      <p class="text-muted">One project.</p>' . "\n" .
      '    </div>' . "\n" .
      '    <div class="card-footer"><button class="btn btn-sm btn-primary">Choose</button></div>' . "\n" .
      '  </article>' . "\n" .
      '  <article class="card">' . "\n" .
      '    <div class="card-body">' . "\n" .
      '      <h3 class="card-title">Team</h3>' . "\n" .
      '      <p class="text-muted">Unlimited projects, shared billing, audit log, and priority support during business hours.</p>' . "\n" .
      '    </div>' . "\n" .
      '    <div class="card-footer"><button class="btn btn-sm btn-primary">Choose</button></div>' . "\n" .
      '  </article>' . "\n" .
      '</div>',
      'Unequal bodies, level footers',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="surfaces">Surface variants</h2>
  <p>
    Four looks. Each changes the background, border and shadow and nothing else, so a
    variant never moves anything.
  </p>
  <?php foreach ($surfaces as [$cls, $label, $blurb]): ?>
    <div class="stack-2">
      <h3 id="s-<?= e($cls) ?>"><?= e($label) ?></h3>
      <p class="text-muted"><?= e($blurb) ?></p>
      <?php
      $c = $cls === 'card' ? 'card' : "card {$cls}";
      docs_example(
          '<article class="' . $c . '" style="max-inline-size:20rem">' . "\n" .
          '  <div class="card-body">' . "\n" .
          '    <h3 class="card-title">' . $label . '</h3>' . "\n" .
          '    <p class="text-muted">Same markup, different surface.</p>' . "\n" .
          '  </div>' . "\n" .
          '</article>',
          '',
          'stack'
      );
      ?>
    </div>
  <?php endforeach; ?>
</section>

<section class="stack-3">
  <h2 id="media">Media</h2>
  <p>
    <code>.card</code> sets <code>overflow: clip</code>, so an image inside it is
    trimmed to the card's radius without any radius of its own.
    <code>.card-media</code> removes the figure's default margin and makes the image
    fill the width; the card's own <code>overflow</code> does the rounding.
  </p>
  <p class="dx-note text-muted">
    Put <code>.card-media</code> before <code>.card-body</code>, not inside it. Inside,
    it inherits the body's padding and the image no longer reaches the edges.
  </p>
  <?php
  docs_example(
      '<article class="card" style="max-inline-size:20rem">' . "\n" .
      '  <figure class="card-media">' . "\n" .
      '    <img class="ratio-16x9" alt="" src="data:image/svg+xml;utf8,' .
      '%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22640%22 height=%22360%22%3E' .
      '%3Crect width=%22640%22 height=%22360%22 fill=%22%23c7d6d4%22/%3E%3C/svg%3E">' . "\n" .
      '  </figure>' . "\n" .
      '  <div class="card-body">' . "\n" .
      '    <h3 class="card-title">Edge to edge</h3>' . "\n" .
      '    <p class="text-muted">The card clips the corners, not the image.</p>' . "\n" .
      '  </div>' . "\n" .
      '</article>',
      'The image runs to the border because the card has no padding',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="link">The whole card as a link</h2>
  <p>
    <code>.card-link</code> makes the card react to hover, and a
    <code>.stretch</code> anchor inside it grows an invisible
    <code>::after</code> over the entire card. The result has one accessible name, one
    tab stop, and text that is still selectable — which is what separates it from
    wrapping the whole card in an <code>&lt;a&gt;</code>.
  </p>
  <?php
  docs_example(
      '<article class="card card-link" style="max-inline-size:22rem">' . "\n" .
      '  <div class="card-body">' . "\n" .
      '    <h3 class="card-title"><a class="stretch" href="#link">Quarterly report</a></h3>' . "\n" .
      '    <p class="text-muted">The heading is the link. The whole card is the target.</p>' . "\n" .
      '  </div>' . "\n" .
      '</article>',
      'One anchor, stretched over the card',
      'stack'
  );
  ?>
  <p class="text-muted">
    A second link inside a stretched card needs <code>position: relative</code> to stay
    clickable, because the stretched <code>::after</code> sits above everything that is
    not positioned. Reach for <code>.relative</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="cq">Responds to its own width</h2>
  <p>
    This is the part of the card worth learning. <code>.card.cq</code> turns the card
    into a query container, and <code>.card-flex</code> then reflows on the card's
    <em>own</em> width rather than the viewport's: a column below
    <code>30rem</code>, a horizontal media object above it. The same card works in a
    wide main column and in a narrow sidebar with no breakpoint and no extra class.
  </p>
  <p>
    Drag the resizer on the example below. Nothing about the viewport changes.
  </p>
  <?php
  docs_example(
      '<div class="cq" style="resize:horizontal;overflow:auto;min-inline-size:15rem;max-inline-size:100%;inline-size:22rem;padding-inline-end:var(--space-3)">' . "\n" .
      '  <article class="card card-flex">' . "\n" .
      '    <figure class="card-media">' . "\n" .
      '      <img alt="" src="data:image/svg+xml;utf8,' .
      '%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22480%22 height=%22270%22%3E' .
      '%3Crect width=%22480%22 height=%22270%22 fill=%22%23c7d6d4%22/%3E%3C/svg%3E">' . "\n" .
      '    </figure>' . "\n" .
      '    <div class="card-body">' . "\n" .
      '      <h3 class="card-title">Reflows on its own width</h3>' . "\n" .
      '      <p class="text-muted">Wider than 30rem and the media moves beside the text.</p>' . "\n" .
      '    </div>' . "\n" .
      '  </article>' . "\n" .
      '</div>',
      'Resize the box, not the window',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.card-flex</code> needs a query container somewhere above it. <code>.cq</code>
    on the card's parent is the usual answer; <code>.card.cq</code> on the card itself
    works when the card is the one being sized.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    Generated from the stylesheet source by <code>tools/docs/extract.mjs</code>. If a
    class is added or removed in <code>src/</code> and this table is not updated,
    <code>tools/docs/verify.mjs</code> fails the build.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    The card reads global tokens directly rather than defining local
    <code>--card-*</code> properties, so restyling it means overriding the rule rather
    than setting a variable. That is a real difference from <code>.btn</code> — see
    <a href="#overriding">Overriding it</a>.
  </p>
  <?php docs_token_table(['--surface', '--surface-2', '--line', '--line-strong', '--r-md', '--shadow-1', '--shadow-3', '--brand-200', '--brand-soft', '--text-md', '--space-5']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>A card is not a landmark.</strong> <code>.card</code> is a plain box with
      no implicit role. Use <code>&lt;article&gt;</code> when the contents stand alone,
      or a <code>&lt;section&gt;</code> with a heading. A bare <code>&lt;div&gt;</code>
      is fine when the card is decoration around content that is already structured.
    </li>
    <li>
      <strong>The stretched link is one tab stop.</strong> With
      <code>.card-link</code> and a <code>.stretch</code> anchor, the keyboard reaches
      the card once and the accessible name is the anchor's text — so that text has to
      make sense on its own. "Read more" in a stretched card gives a screen-reader user
      a list of identical links.
    </li>
    <li>
      <strong>Focus is on the anchor, not the card.</strong> The focus ring is drawn
      around the link text rather than the whole card, because the anchor is what has
      focus. This is a known rough edge of the stretched-link pattern and Deck does not
      work around it; if the ring matters more than the click target, use an ordinary
      link and drop <code>.stretch</code>.
    </li>
    <li>
      <strong>Text stays selectable.</strong> Unlike wrapping the card in an anchor,
      the stretched-link pattern leaves the body text selectable with the mouse, which
      is why Deck uses it.
    </li>
    <li>
      <strong>Touch target.</strong> The card is the target, so it clears
      <?= e(api_token('--tap')['value'] ?? '44px') ?> by a wide margin. Buttons inside
      a <code>.card-footer</code> keep their own targets.
    </li>
    <li>
      <strong>Nothing here is announced as a group.</strong> A grid of cards is a grid
      of articles, not a list. If the count matters — "3 results" — put that in a
      heading above; the cards will not say it.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The card is written in logical properties throughout:
    <code>border-block-end</code> on the header, <code>margin-block-start</code> on the
    footer, <code>padding-inline</code> everywhere. Nothing about it is side-specific,
    so <code>dir="rtl"</code> needs no extra rules. The one directional thing is the
    hover arrow — <code>src/19-logical.css</code> flips
    <code>.icon-follow</code> so it steps toward the text end rather than always to the
    right.
  </p>
  <?php
  docs_example(
      '<article dir="rtl" class="card" style="max-inline-size:22rem">' . "\n" .
      '  <div class="card-header">' . "\n" .
      '    <h3 class="card-title">الفاتورة ٢٢٩١</h3>' . "\n" .
      '    <span class="badge badge-good push">مدفوعة</span>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="card-body"><p>ترخيصان، بفاتورة سنوية.</p></div>' . "\n" .
      '</article>',
      'The same markup with dir="rtl"',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.card-link</code> transitions <code>box-shadow</code> and
    <code>border-color</code>. Under <code>prefers-reduced-motion: reduce</code> the
    global reset collapses both to <code>.01ms</code>, so the hover state still happens
    — it just arrives immediately.
  </p>
  <p class="text-muted">
    The stepping arrow in <code>src/16-motion.css</code> is declared inside
    <code>@media (prefers-reduced-motion: no-preference)</code>, so it is never
    declared at all for a reader who has asked for less motion, rather than being
    declared and then overridden.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> gives cards a plain <code>#bbb</code> hairline, drops
    the radius and the shadow, forces a white background, and sets
    <code>break-inside: avoid</code> so a card does not split across two sheets.
    <code>.card-footer</code> keeps a grey fill with
    <code>print-color-adjust: exact</code>, so the region reads as a footer on paper
    rather than as more body text.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <p>
    Deck reserves four empty <code>app.*</code> layers. A rule in
    <code>app.components</code> beats every Deck rule regardless of specificity, so a
    single class selector is enough and <code>!important</code> is never needed.
  </p>
  <pre class="dx-code"><code><?= e('@layer app.components {
  .card {
    border-radius: var(--r-lg);
    box-shadow: none;
    border-color: var(--brand-200);
  }
}') ?></code></pre>
  <p>
    Unlike <code>.btn</code>, the card has no local custom properties to aim at. That is
    a deliberate difference — a card is a surface, and the surface tokens
    (<code>--surface</code>, <code>--line</code>, <code>--r-md</code>) are global on
    purpose, so changing them restyles panels, lists and tables to match instead of
    leaving the card looking like the odd one out.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a single block on a page.</strong> One card on an otherwise empty
      page is a border around the content. Use <code>.panel</code>, which goes edge to
      edge on a phone and only becomes a box once there is room, or use nothing.
    </li>
    <li>
      <strong>Not for a list of rows.</strong> A stack of full-width cards with one
      line of text in each is a list wearing borders. Use <code>.list</code> with
      <code>.list-row</code> — it has the dividers, the hover state and the row
      semantics already, and it does not draw eleven boxes.
    </li>
    <li>
      <strong>Not for tabular data.</strong> If the values line up in columns, they
      belong in <code>.table</code>. Cards break the column alignment that makes a
      table readable, and "responsive cards" is how a table stops being comparable.
    </li>
    <li>
      <strong>Not as a modal.</strong> <code>.modal</code> carries the backdrop, the
      focus trap, the escape handling and the scroll lock. A <code>.card</code> with
      <code>position: fixed</code> has none of them.
    </li>
    <li>
      <strong><code>.card-flush</code> versus no card at all.</strong>
      <code>.card-flush</code> removes the border, the shadow and the background, which
      leaves a flex column with <code>overflow: clip</code>. If you are not going to use
      the header/body/footer regions, that is <code>.stack</code> with extra steps.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
