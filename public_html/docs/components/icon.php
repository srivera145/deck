<?php
declare(strict_types=1);

$page = [
    'path' => 'components/icon.php',
    'title' => 'Icon',
    'level' => 'Beginner',
    'description' => 'Deck\'s .icon sizes an SVG in em so it scales with the text beside it, strokes it in currentColor, and mirrors the directional glyphs under dir="rtl" by matching on the sprite id.',
    'documents' => [
        'icon', 'icon-fill', 'icon-follow', 'icon-lg', 'icon-muted',
        'icon-sm', 'icon-spin', 'icon-tile', 'icon-tile-bad', 'icon-tile-good',
        'icon-tile-lg', 'icon-tile-warn', 'icon-xl', 'is-open',
    ],

    'component' => 'icon',
    'accounts' => [
        '08-mobile.css'   => 'documented: the icon itself, its sizes, the fill and muted variants, and the icon tiles',
        '19-logical.css'  => 'documented: directional glyphs are mirrored by matching on the sprite id — the Right to left section',
        '16-motion.css'   => 'documented: .icon-follow steps forward on hover — the Reduced motion section',
        '11-combobox.css' => 'internal: the combobox chevron rotates when its list opens',
        '02-reset.css'    => 'internal: the reduced-motion reset keeps .icon-spin turning rather than freezing it',
        '22-nav.css'      => 'internal: icon sizing inside the tab bar and the carousel arrows',
        '07-components.css' => 'internal: icon alignment inside an alert, a menu item and a toast',
        '06-forms.css'    => 'internal: the icon inside .search and .datefield',
        '10-datepicker.css' => 'internal: the calendar glyph in the date field',
        '99-print.css'    => 'documented: an icon tile keeps its tint on paper — the Printing section',
        '23-inputs.css'   => 'internal: icons inside the editor toolbar and the copy button',
    ],
];

require __DIR__ . '/../_layout.php';

$sizes = [
    ['icon-sm', '1em', 'Beside small text. Uses the heavier cut of the sprite.'],
    ['icon', '1.25em', 'The default. Lines up with body text.'],
    ['icon-lg', '1.5em', 'A button that is mostly icon.'],
    ['icon-xl', '2em', 'An empty state, a feature row.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Icon</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Icon</h1>
  <p class="lede">
    <code>.icon</code> is sized in <code>em</code>, not pixels — so it scales with the
    text it sits beside instead of needing a size for every context — and it is stroked in
    <code>currentColor</code>, so it takes the colour of whatever it is inside. Those two
    decisions are why an icon in a button, a muted caption and a danger alert all look
    right without a variant for each.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Put it on an <code>&lt;svg&gt;</code> containing a <code>&lt;use&gt;</code> that
    references the sprite. That is the whole pattern — there is no icon font, no
    per-icon class, and no build step that inlines anything.
  </p>
  <?php
  docs_example(
      '<p class="cluster cluster-tight">' . "\n" .
      '  <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check-circle"></use></svg>' . "\n" .
      '  Sits on the baseline of the text beside it' . "\n" .
      '</p>' . "\n" .
      '<button class="btn btn-primary">' . "\n" .
      '  <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#download"></use></svg>' . "\n" .
      '  Download' . "\n" .
      '</button>',
      'One class, and it takes the colour and size of its surroundings',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>vertical-align: -.22em</code> is the number that makes it land on the baseline
    rather than floating above it. <code>flex: 0 0 auto</code> stops it shrinking when the
    text beside it is long.
  </p>
</section>

<section class="stack-3">
  <h2 id="sizes">Sizes</h2>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Icon sizes</caption>
      <thead><tr><th scope="col">Class</th><th scope="col">Size</th><th scope="col">Use</th></tr></thead>
      <tbody>
        <?php foreach ($sizes as [$c, $s, $u]): ?>
          <tr>
            <th scope="row" data-label="Class"><code><?= e('.' . $c) ?></code></th>
            <td data-label="Size"><code class="dx-dim"><?= e($s) ?></code></td>
            <td data-label="Use"><?= e($u) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php
  docs_example(
      '<svg class="icon icon-sm" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-sm"></use></svg>' . "\n" .
      '<svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star"></use></svg>' . "\n" .
      '<svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star"></use></svg>' . "\n" .
      '<svg class="icon icon-xl" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star"></use></svg>',
      'Note the first one references #star-sm, not #star',
      'cluster'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="two-weights">Why .icon-sm needs a different symbol</h2>
  <p>
    The sprite's symbols are <strong>filled outlines</strong> rather than strokes, so
    <code>stroke-width</code> does nothing to them — an icon shrunk to
    <code>1em</code> would get visually lighter with no way to compensate.
  </p>
  <p>
    So every icon ships twice: <code>#name</code> at weight 400 and
    <code>#name-sm</code> at weight 500, generated from the variable font by
    <code>tools/icons/build-icons.mjs</code>. <code>.icon-sm</code> is meant to be paired
    with the <code>-sm</code> symbol.
  </p>
  <p class="dx-note text-muted">
    CSS cannot rewrite a <code>&lt;use href&gt;</code>, so <strong>the pairing is on
    you</strong>. <code>.icon-sm</code> with <code>#star</code> renders the light cut at a
    small size and simply looks thin — nothing errors, and nothing warns. This is the one
    part of Deck's icon system that a reader has to remember rather than being handed.
  </p>
</section>

<section class="stack-3">
  <h2 id="variants">Fill and muted</h2>
  <p>
    <code>.icon-fill</code> swaps stroke for fill, for the solid cut of a glyph —
    a filled star for a rating, a filled circle for a status.
    <code>.icon-muted</code> drops the colour to <code>--text-faint</code>.
  </p>
  <?php
  docs_example(
      '<svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star"></use></svg>' . "\n" .
      '<svg class="icon icon-lg icon-fill" style="color:var(--warn-500)" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '<svg class="icon icon-lg icon-muted" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star"></use></svg>',
      'Outline, filled, muted',
      'cluster'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="tiles">Icon tiles</h2>
  <p>
    <code>.icon-tile</code> is a rounded square with a tinted background, for a feature
    row or an empty state — the icon equivalent of an
    <a href="avatar.php">avatar</a>. Three status variants and a large size.
  </p>
  <?php
  docs_example(
      '<span class="icon-tile"><svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#folder"></use></svg></span>' . "\n" .
      '<span class="icon-tile icon-tile-good"><svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check"></use></svg></span>' . "\n" .
      '<span class="icon-tile icon-tile-warn"><svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#alert-triangle"></use></svg></span>' . "\n" .
      '<span class="icon-tile icon-tile-bad"><svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#x"></use></svg></span>' . "\n" .
      '<span class="icon-tile icon-tile-lg"><svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#upload"></use></svg></span>',
      '',
      'cluster'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion-classes">.icon-spin and .icon-follow</h2>
  <p>
    <code>.icon-spin</code> rotates continuously — a loading state on an icon that is not
    a <code>.spinner</code>. <code>.icon-follow</code> steps three pixels forward when its
    parent is hovered, which is what makes the chevron in a
    <a href="list.php#rows-as-links">list row</a> or a
    <a href="card.php#link">card link</a> feel like it is going somewhere.
  </p>
  <?php
  docs_example(
      '<div class="list" style="max-inline-size:24rem">' . "\n" .
      '  <a class="list-row" href="#motion-classes">' . "\n" .
      '    <div class="list-main"><span class="list-title">Hover this row</span></div>' . "\n" .
      '    <svg class="icon icon-follow list-trail" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-right"></use></svg>' . "\n" .
      '  </a>' . "\n" .
      '</div>' . "\n" .
      '<span class="cluster cluster-tight">' . "\n" .
      '  <svg class="icon icon-spin" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#refresh"></use></svg>' . "\n" .
      '  Working' . "\n" .
      '</span>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    Eleven files style an icon, which is what happens to a primitive used everywhere. Only
    two of them are about the icon itself; the rest position it inside another component.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    An icon reads almost nothing. It inherits <code>color</code> and
    <code>font-size</code> from its parent, which is the entire design.
  </p>
  <?php docs_token_table(['--text-faint', '--brand-soft', '--brand-soft-text', '--good-100', '--warn-100', '--bad-100', '--r-md']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Nearly every icon should be <code>aria-hidden="true"</code>.</strong> An
      icon beside a label is decoration — the label is the name. Every example on this
      page hides its icons for that reason.
    </li>
    <li>
      <strong>An icon-only control needs a label on the control.</strong>
      <code>aria-label</code> on the <code>&lt;button&gt;</code>, not on the SVG. The
      button is what gets focus and what is announced.
    </li>
    <li>
      <strong>An icon that carries meaning on its own needs
      <code>role="img"</code></strong> and a title — a status glyph in a table cell with
      no text beside it, for instance. That is rare, and usually a sign the cell should
      have text.
    </li>
    <li>
      <strong>Colour is not a status.</strong> A green tick and a red cross are the same
      shape to a screen reader and similar to some readers with colour vision deficiency.
      The <code>.icon-tile-*</code> variants are a tint on top of a glyph, not a
      substitute for words.
    </li>
    <li>
      <strong><code>currentColor</code> means contrast is inherited.</strong> An icon is
      as readable as the text around it, automatically — which is one of the quiet
      benefits of stroking rather than filling with a fixed colour.
    </li>
    <li>
      <strong>Icons scale with zoom</strong> because they are sized in <code>em</code>. A
      pixel-sized icon stays put while the text around it grows, which at 200% looks
      broken.
    </li>
    <li>
      <strong><code>.icon-spin</code> is exempt from the reduced-motion reset.</strong>
      <code>src/02-reset.css</code> keeps it turning at 2.4 seconds rather than freezing
      it, on the grounds that a frozen loading indicator reads as a hung page.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    This is the most interesting rule in the component.
    <code>src/19-logical.css</code> mirrors directional glyphs by matching on the sprite
    id itself:
  </p>
  <pre class="dx-code"><code><?= e('[dir="rtl"] :is(
  .icon:has(use[href$="#chevron-left"]),
  .icon:has(use[href$="#arrow-right"]),
  .icon:has(use[href$="#send"]),
  …
) { scale: -1 1; }') ?></code></pre>
  <p>
    So an arrow, a chevron and the send glyph flip automatically, while a checkmark, a
    clock and a magnifier — which would look wrong mirrored — are left alone. It is a
    list rather than a rule, which means <strong>a directional icon you add yourself is
    not in it</strong>; reach for <code>.mirror-rtl</code> in that case.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="cluster">' . "\n" .
      '  <svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-right"></use></svg>' . "\n" .
      '  <svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#send"></use></svg>' . "\n" .
      '  <svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check"></use></svg>' . "\n" .
      '  <svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#clock"></use></svg>' . "\n" .
      '</div>',
      'The chevron and the send glyph flip; the tick and the clock do not',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.icon-follow</code> is declared inside
    <code>@media (prefers-reduced-motion: no-preference)</code>, so a reader who has asked
    for less motion never receives the rule at all. <code>.icon-spin</code> is the
    opposite case: it is in the reset's exemption list and keeps moving, slowly, because a
    frozen spinner reads as broken.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Icons print. They are strokes in <code>currentColor</code>, so they come out as line
    art at whatever the text colour prints as — which is usually correct.
  </p>
  <p>
    <code>.icon-tile</code> <em>is</em> in the
    <code>print-color-adjust: exact</code> list in <code>src/99-print.css</code>,
    alongside badges, avatars and chart fills — so a tile keeps its tint on paper rather
    than printing as a glyph floating in white space. That is the difference between a
    status tile that still reads as a status and one that does not.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One icon, no layer needed */
<svg class="icon" style="inline-size:2.5em;block-size:2.5em">

@layer app.components {
  /* A lighter stroke throughout */
  .icon { stroke-width: 1.5; }

  /* A size Deck does not ship */
  .icon-2xl { inline-size: 3em; block-size: 3em; }
}') ?></code></pre>
  <p class="text-muted">
    Changing <code>stroke-width</code> affects only the stroked symbols. The sprite's
    glyphs are filled outlines, so a heavier stroke does not make them heavier — that is
    what the two weights are for.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not as the only label.</strong> An icon-only button is fine with
      <code>aria-label</code>, and a row of unlabelled glyphs is a guessing game for
      everyone.
    </li>
    <li>
      <strong>Not for a logo or a brand mark.</strong> Those are hand-authored symbols in
      the sprite — <code>#deck-mark</code> and <code>#deck-wordmark</code> — and they are
      filled rather than stroked, so <code>.icon</code>'s stroke settings do nothing
      useful for them.
    </li>
    <li>
      <strong>Not for an illustration.</strong> A 24-unit grid at
      <code>1.25em</code> is not a picture. Use an <code>&lt;img&gt;</code>.
    </li>
    <li>
      <strong>Not for an emoji.</strong> Use <a href="emoji.php"><code>.emoji</code></a>,
      which sizes and aligns a character rather than an SVG.
    </li>
    <li>
      <strong>Not with a fixed pixel size.</strong> The <code>em</code> sizing is the
      component. Overriding it with pixels breaks the alignment with text and the
      behaviour under zoom.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
