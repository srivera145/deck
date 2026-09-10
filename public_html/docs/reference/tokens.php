<?php
declare(strict_types=1);

/**
 * The complete token reference.
 *
 * Grouped by family, and the grouping is checked rather than trusted: anything
 * that matches no group falls into an "Ungrouped" section at the bottom, so a
 * token added to src/ shows up here whether or not anybody remembered to widen
 * a pattern. That section being empty is the only reason to believe this page
 * is complete.
 *
 * Every preview is the token applied to something — a background, a width, a
 * box-shadow, an animation — so a value that changes in the stylesheet changes
 * the cell. There are no colour chips typed in by hand anywhere on this page.
 */

$page = [
    'path' => 'reference/tokens.php',
    'title' => 'All tokens',
    'level' => 'Beginner',
    'description' => "Every design token Deck declares, grouped by family with a live preview of each: the palette, the roles, spacing, radius, shadow, type, motion and stacking.",
];

require __DIR__ . '/../_layout.php';

/* Each group is a label, a note, and a matcher. Order matters: the first group
   whose matcher accepts a token takes it, so the specific patterns come first
   and the broad ones last. */
$GROUPS = [
    [
        'id' => 'hue',
        'title' => 'Hue and chroma',
        'note' => 'The five numbers the whole palette is generated from. Change one and every'
            . ' shade, border, focus ring and shadow that derives from it follows, with no'
            . ' rebuild and no second stylesheet. This is the retheming story in ten tokens.',
        'match' => static fn(string $n): bool => str_starts_with($n, '--hue-') || str_starts_with($n, '--chroma-'),
    ],
    [
        'id' => 'brand',
        'title' => 'Brand ramp',
        'note' => 'Eleven steps in OKLCH, computed from --hue-brand and --chroma-brand. The'
            . ' numbers are perceptual lightness, so 500 is the same apparent lightness at'
            . ' every hue — which is what stops a yellow brand from looking washed out where'
            . ' a blue one looks solid.',
        'match' => static fn(string $n): bool => (bool) preg_match('/^--brand-\d+$/', $n),
    ],
    [
        'id' => 'ink',
        'title' => 'Neutral ramp',
        'note' => 'Fifteen steps of near-grey, tinted toward --hue-neutral rather than pure'
            . ' grey. A truly neutral grey beside a saturated brand reads as dirty; a grey'
            . ' carrying a little of the brand hue reads as deliberate.',
        'match' => static fn(string $n): bool => (bool) preg_match('/^--ink-\d+$/', $n),
    ],
    [
        'id' => 'status',
        'title' => 'Status ramps',
        'note' => 'Good, warn, bad and accent, at the steps that actually get used: 100 for a'
            . ' fill, 500 for an icon or a border, 600 and 700 for text. Deliberately not a'
            . ' full eleven-step ramp each — the missing steps are ones nothing needed.',
        'match' => static fn(string $n): bool => (bool) preg_match('/^--(good|warn|bad|accent)-\d+$/', $n),
    ],
    [
        'id' => 'roles',
        'title' => 'Semantic roles',
        'note' => 'The tokens you should actually be reaching for. Each one names a job rather'
            . ' than a colour, and each resolves through light-dark() — which is why the'
            . ' entire dark theme is one attribute rather than a second set of rules.'
            . ' Reference --surface, not --ink-0, and dark mode comes free.',
        'match' => static fn(string $n): bool => in_array($n, [
            '--bg', '--bg-sunken', '--surface', '--surface-2', '--surface-hover',
            '--line', '--line-strong', '--text', '--text-muted', '--text-faint',
            '--text-on-brand', '--text-on-accent', '--text-on-good', '--text-on-warn',
            '--text-on-bad', '--brand', '--brand-hover', '--brand-soft',
            '--brand-soft-text', '--focus', '--scrim',
        ], true),
    ],
    [
        'id' => 'space',
        'title' => 'Space',
        'note' => 'A 4px base in rem, plus two fluid steps for page rhythm. Seven of these have'
            . ' utilities; the rest are for your own rules. The scale is explained in full on'
            . ' the spacing reference.',
        'match' => static fn(string $n): bool => str_starts_with($n, '--space'),
    ],
    [
        'id' => 'radius',
        'title' => 'Radius',
        'note' => 'Five steps in px rather than rem, because a corner is a property of the'
            . ' shape and should not grow when a reader raises their base font size.',
        'match' => static fn(string $n): bool => str_starts_with($n, '--r-'),
    ],
    [
        'id' => 'shadow',
        'title' => 'Shadow and ring',
        'note' => 'Four elevations, an inset highlight, and the focus ring. The shadow colour'
            . ' is tinted toward the neutral hue rather than being black at low alpha, which'
            . ' is what keeps it from going muddy over a coloured surface.',
        'match' => static fn(string $n): bool => str_starts_with($n, '--shadow') || $n === '--ring',
    ],
    [
        'id' => 'type',
        'title' => 'Type',
        'note' => 'Three families, ten sizes and four line heights. The top five sizes are'
            . ' clamp() expressions, so display type scales with the viewport between a floor'
            . ' and a ceiling instead of stepping at a breakpoint.',
        'match' => static fn(string $n): bool => str_starts_with($n, '--font-')
            || str_starts_with($n, '--text-') || str_starts_with($n, '--leading-')
            || $n === '--measure',
    ],
    [
        'id' => 'motion',
        'title' => 'Motion',
        'note' => 'Seven durations and ten easing curves, three of which are linear() springs —'
            . ' a real spring feel with no JavaScript. Thirteen of these live in'
            . ' src/16-motion.css rather than the token sheet, which is why they were missing'
            . ' from this reference until the extractor stopped selecting tokens by filename.',
        'match' => static fn(string $n): bool => str_starts_with($n, '--dur')
            || str_starts_with($n, '--ease') || $n === '--stagger-step' || $n === '--travel',
    ],
    [
        'id' => 'controls',
        'title' => 'Controls',
        'note' => 'The three control heights and the minimum tap target. 44px is the floor a'
            . ' finger needs; every interactive thing in Deck is at least that tall on a'
            . ' touch device even where the visible box looks smaller.',
        'match' => static fn(string $n): bool => $n === '--tap' || str_starts_with($n, '--control-h'),
    ],
    [
        'id' => 'z',
        'title' => 'Stacking',
        'note' => 'Five named layers, a hundred apart. Deck has exactly one z-index utility and'
            . ' five tokens, on the theory that stacking bugs come from competing numbers and'
            . ' the cure is fewer of them, not more.',
        'match' => static fn(string $n): bool => str_starts_with($n, '--z-'),
    ],
];

/* Assign every token to the first group that will take it. */
$assigned = [];
$taken = [];
foreach ($GROUPS as $g) {
    $assigned[$g['id']] = [];
}
foreach ($api['tokens'] as $t) {
    foreach ($GROUPS as $g) {
        if ($g['match']($t['name'])) {
            $assigned[$g['id']][] = $t['name'];
            $taken[$t['name']] = true;
            continue 2;
        }
    }
}
$ungrouped = array_values(array_filter(
    array_column($api['tokens'], 'name'),
    static fn(string $n): bool => !isset($taken[$n])
));

$byFile = [];
foreach ($api['tokens'] as $t) {
    $byFile[$t['file']] = ($byFile[$t['file']] ?? 0) + 1;
}
ksort($byFile);
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">All tokens</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>All tokens</h1>
  <p class="lede">
    All <?= (int) count($api['tokens']) ?> custom properties Deck declares on
    <code>:root</code>, grouped by family. Every preview on this page is the token
    applied to something — a background, a width, a shadow, an animation — so what you
    are looking at is the value in effect rather than a picture of it.
  </p>
</header>

<section class="stack-3">
  <h2 id="using">Using a token</h2>
  <p>
    Tokens are plain CSS custom properties. Read one with <code>var()</code> in your own
    rules, and set one anywhere in the tree to change it from that element down.
  </p>
  <pre class="dx-code"><code>@layer app.pages {
  .invoice-total { font-size: var(--text-xl); color: var(--text); }
}

&lt;!-- one tenant, one number, no rebuild --&gt;
&lt;html style="--hue-brand: 265"&gt;

&lt;!-- or scoped to a subtree --&gt;
&lt;section class="card" style="--brand-soft: var(--good-100)"&gt;</code></pre>
  <p>
    Reach for the <a href="#roles">semantic roles</a> before the ramps.
    <code>--surface</code> resolves to a different step of the neutral ramp in light and
    dark; <code>--ink-0</code> is the same colour in both, and using it directly is how
    a component ends up unreadable in dark mode.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Where the tokens are declared</caption>
      <thead>
        <tr><th scope="col">Declared in</th><th scope="col">Tokens</th></tr>
      </thead>
      <tbody>
        <?php foreach ($byFile as $file => $n): ?>
          <tr>
            <th scope="row" data-label="Declared in"><code><?= e('src/' . $file) ?></code></th>
            <td data-label="Tokens"><?= (int) $n ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php foreach ($GROUPS as $g): ?>
  <?php if ($assigned[$g['id']] === []) { continue; } ?>
  <section class="stack-4">
    <h2 id="<?= e($g['id']) ?>"><?= e($g['title']) ?></h2>
    <p><?= e($g['note']) ?></p>
    <?php docs_token_table($assigned[$g['id']], true); ?>
  </section>
<?php endforeach; ?>

<section class="stack-4">
  <h2 id="ungrouped">Ungrouped</h2>
  <?php if ($ungrouped === []): ?>
    <div class="alert alert-good">
      <svg class="icon"><use href="../../assets/deck/deck-icons.svg#check-circle"></use></svg>
      <div>
        <div class="alert-title">Every token above is accounted for</div>
        <p class="alert-body">
          All <?= (int) count($api['tokens']) ?> tokens matched a group. This section is
          here so that a token added to <code>src/</code> that matches none of them
          appears rather than disappearing, which is the only way this page can be
          trusted to be complete.
        </p>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warn">
      <svg class="icon"><use href="../../assets/deck/deck-icons.svg#alert-triangle"></use></svg>
      <div>
        <div class="alert-title"><?= (int) count($ungrouped) ?> tokens match no group</div>
        <p class="alert-body">
          They are listed below rather than dropped. Widen a pattern at the top of this
          page to file them.
        </p>
      </div>
    </div>
    <?php docs_token_table($ungrouped, true); ?>
  <?php endif; ?>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding a token</h2>
  <p>
    Custom properties inherit, so where you set one decides how far the change reaches.
    On <code>:root</code> or the <code>&lt;html&gt;</code> element it is the whole
    document; on a component it is that subtree.
  </p>
  <p>
    Specificity still applies to the declaration that sets the property, so a token set
    in your own <code>@layer app</code> beats Deck's <code>:root</code> without
    <code>!important</code> — the same rule that makes every other override in Deck
    work. What custom properties do <em>not</em> do is participate in the cascade at the
    point of use: <code>var(--surface)</code> resolves to whatever
    <code>--surface</code> is on that element, whichever layer set it.
  </p>
  <p class="dx-note">
    A token that resolves to nothing makes its declaration invalid at computed-value
    time, which usually shows up as an inherited or initial value rather than as an
    error. If a colour comes out black or a size comes out zero, check the token name
    for a typo first — <code>var()</code> will not tell you.
  </p>
</section>

<?php docs_footer(); ?>
