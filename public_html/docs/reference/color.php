<?php
declare(strict_types=1);

/**
 * The colour and surface reference: background, text colour, border, radius,
 * shadow, and the style-query tone classes.
 *
 * Grouped together because they are the same decision — what this box looks
 * like — and because every one of them reads a token rather than a literal,
 * which is what makes the whole set follow --hue-brand.
 */

$page = [
    'path' => 'reference/color.php',
    'title' => 'Colour and surface',
    'level' => 'Beginner',
    'description' => "Background, text colour, border, radius and shadow utilities, all reading design tokens so they follow the theme, plus the style-query tone classes.",
    'documents' => [
        'bg-surface', 'bg-sunken', 'bg-soft', 'bg-brand', 'bg-none',
        'text-brand', 'text-muted', 'text-faint', 'text-good', 'text-warn',
        'text-bad', 'text-inherit',
        'border', 'border-0', 'border-t', 'border-b', 'border-brand', 'bis', 'bie',
        'r-xs', 'r-sm', 'r-md', 'r-lg', 'r-full', 'r-start', 'r-end',
        'shadow-0', 'shadow-1', 'shadow-2', 'shadow-3', 'shadow-4',
        'tone-surface', 'tone-text', 'tone-icon',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">Colour and surface</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Colour and surface</h1>
  <p class="lede">
    Everything that decides what a box looks like: its background, its text colour,
    its border, its corners and its shadow. Not one of these classes contains a
    literal colour — every one reads a token — which is why changing
    <code>--hue-brand</code> retints the whole page and why all of them already work
    in dark mode.
  </p>
</header>

<section class="stack-4">
  <h2 id="background">Background</h2>
  <p>
    The three neutral backgrounds are a depth order, not a palette:
    <code>.bg-sunken</code> is behind the page, <code>--bg</code> is the page, and
    <code>.bg-surface</code> is a card lifted off it. In dark mode the order inverts in
    lightness and stays the same in meaning, which is the point of naming them by role.
  </p>
  <p>
    <code>.bg-brand</code> and <code>.bg-soft</code> also set a foreground colour,
    because a background without a matching text colour is how you get grey-on-brand
    that nobody can read. <code>.bg-soft</code> is the tinted-but-quiet one — the fill
    behind a selected row or an informational callout.
  </p>
  <?php docs_utility_table(
      ['bg-surface', 'bg-sunken', 'bg-soft', 'bg-brand', 'bg-none'],
      'Background utilities'
  ); ?>
  <?php docs_token_table(['--bg', '--bg-sunken', '--surface', '--surface-2', '--surface-hover', '--brand-soft']); ?>
</section>

<section class="stack-4">
  <h2 id="text">Text colour</h2>
  <p>
    Three neutrals and four status colours. The neutrals are a hierarchy —
    <code>--text</code> for the content, <code>.text-muted</code> for a caption or a
    label, <code>.text-faint</code> for metadata that should be findable and not
    read — and the gap between them is deliberately large enough to see and small
    enough to keep the faint one above the contrast floor.
  </p>
  <p>
    The status four are the 700 step of each hue rather than the 500, so they carry
    enough contrast on a light surface to be used as text rather than only as a fill.
    <code>.text-inherit</code> is the escape hatch: it puts a link or an icon back to
    whatever colour its parent is, which is what you want for a link inside a coloured
    alert.
  </p>
  <?php docs_utility_table([
      'text-brand', 'text-muted', 'text-faint',
      'text-good', 'text-warn', 'text-bad', 'text-inherit',
  ], 'Text colour utilities'); ?>
  <?php docs_token_table(['--text', '--text-muted', '--text-faint', '--brand', '--good-700', '--warn-700', '--bad-700']); ?>
  <p class="dx-note">
    Colour is never the only signal in Deck. <code>.alert-bad</code> has an icon,
    <code>.badge-good</code> has a word, and a chart series has a shape as well as a
    hue — because roughly one man in twelve cannot separate the good and bad ends of a
    red-green pair. If you use <code>.text-bad</code> to mean "this failed", something
    else on the row has to say so too.
  </p>
</section>

<section class="stack-4">
  <h2 id="border">Border</h2>
  <p>
    One hairline, in <code>--line</code>, which is a low-contrast neutral that reads as
    a division rather than as a drawn box. <code>.border-t</code> and
    <code>.border-b</code> set the block edges and <code>.bis</code> and
    <code>.bie</code> the inline ones — the inline pair is named logically because those
    are the two that flip.
  </p>
  <?php docs_utility_table(
      ['border', 'border-0', 'border-t', 'border-b', 'bis', 'bie', 'border-brand'],
      'Border utilities'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="radius">Radius</h2>
  <p>
    Five steps and two logical halves. The steps are in <code>px</code> rather than
    <code>rem</code> on purpose: a corner radius is a physical property of the shape
    and should not grow when someone raises their base font size, or a 4px chip corner
    becomes a 5px one for no reason anybody asked for.
  </p>
  <p>
    <code>.r-start</code> and <code>.r-end</code> round one inline side, which is how a
    segmented control or a button group gets rounded ends and square joins that survive
    a direction flip.
  </p>
  <?php docs_utility_table(
      ['r-xs', 'r-sm', 'r-md', 'r-lg', 'r-full', 'r-start', 'r-end'],
      'Border radius utilities'
  ); ?>
  <?php docs_example(
      '<div class="cluster cluster-tight">' . "\n" .
      '  <span class="badge r-xs">r-xs</span>' . "\n" .
      '  <span class="badge r-sm">r-sm</span>' . "\n" .
      '  <span class="badge r-md">r-md</span>' . "\n" .
      '  <span class="badge r-lg">r-lg</span>' . "\n" .
      '  <span class="badge r-full">r-full</span>' . "\n" .
      '</div>',
      'The five steps, on the same element'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="shadow">Shadow</h2>
  <p>
    Four elevations and a reset. The shadow colour is a token,
    <code>--shadow-color</code>, rather than a flat black at low alpha — a black shadow
    over a tinted surface goes muddy, and tinting the shadow toward the surface keeps
    it reading as depth.
  </p>
  <p>
    Elevation should track how far something is from the page, so a card is
    <code>.shadow-1</code>, a popover is <code>.shadow-2</code>, and
    <code>.shadow-4</code> is for something being dragged. Using a large shadow on a
    resting element spends the whole range at once and leaves nothing to say "this is
    lifted".
  </p>
  <?php docs_utility_table(
      ['shadow-0', 'shadow-1', 'shadow-2', 'shadow-3', 'shadow-4'],
      'Box shadow utilities'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="tone">Tone</h2>
  <p>
    Three classes driven by a style query rather than by their own name. Set
    <code>--tone</code> to <code>critical</code>, <code>caution</code> or
    <code>clear</code> on an element carrying <code>.cq-tone</code>, and every
    <code>.tone-surface</code>, <code>.tone-text</code> and <code>.tone-icon</code>
    inside it takes the matching colour — with no class changes on the children at all.
  </p>
  <pre class="dx-code"><code>&lt;article class="card cq-tone" style="--tone: caution"&gt;
  &lt;div class="card-body tone-surface"&gt;
    &lt;h3 class="tone-text"&gt;Payment overdue&lt;/h3&gt;
  &lt;/div&gt;
&lt;/article&gt;</code></pre>
  <p>
    That matters when the tone comes from data. A server rendering a row does not have
    to map a status onto three class names; it writes one custom property and the
    stylesheet does the rest. Style queries are supported in current Chrome and Safari;
    where they are not, the fallback is simply the untinted appearance, which is why
    the rows below show a condition rather than a plain declaration.
  </p>
  <?php docs_utility_table(['tone-surface', 'tone-text', 'tone-icon'], 'Style-query tone utilities'); ?>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use these</h2>
  <ul class="stack-3">
    <li>
      <strong>Not to build a component that already exists.</strong>
      <code>.bg-surface .border .r-md .shadow-1</code> is a <code>.card</code>. The
      card also handles its header, its media slot and its print appearance.
    </li>
    <li>
      <strong>Not with a literal colour beside them.</strong> Mixing
      <code>.text-muted</code> with an inline <code>#666</code> gives you an element
      that half-follows the theme, which is more confusing than one that does not
      follow it at all.
    </li>
    <li>
      <strong>Not as the only signal for a state.</strong> Pair a status colour with
      an icon or a word.
    </li>
    <li>
      <strong>Not <code>.r-full</code> on a rectangle.</strong> It is 999px, so on a
      wide element the ends go to semicircles. It is for a pill or a circle.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
