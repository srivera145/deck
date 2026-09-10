<?php
declare(strict_types=1);

/**
 * The spacing reference.
 *
 * The only utility page on this site with an argument in it. Every other one is
 * an intro paragraph and a generated table, because a reader looking up
 * `.text-sm` wants the value and nothing else. Spacing is different: the
 * mistakes people make with it are conceptual rather than factual — reaching for
 * a margin where a gap belongs, or inventing a ninth step because the eighth
 * looked wrong — and neither of those is fixed by a table of values.
 */

$page = [
    'path' => 'reference/spacing.php',
    'title' => 'Spacing',
    'level' => 'Beginner',
    'description' => "The 4px scale behind every gap, pad and margin in Deck: fourteen tokens, seven utility steps, the logical per-side family, and why gap beats margin.",
    'documents' => [
        'p-0', 'p-1', 'p-2', 'p-3', 'p-4', 'p-6', 'p-8',
        'px-0', 'px-1', 'px-2', 'px-3', 'px-4', 'px-6', 'px-8',
        'py-0', 'py-1', 'py-2', 'py-3', 'py-4', 'py-6', 'py-8',
        'pis-0', 'pis-1', 'pis-2', 'pis-3', 'pis-4', 'pis-6', 'pis-8',
        'pie-0', 'pie-1', 'pie-2', 'pie-3', 'pie-4', 'pie-6', 'pie-8',
        'pbs-0', 'pbs-1', 'pbs-2', 'pbs-3', 'pbs-4', 'pbs-6', 'pbs-8',
        'pbe-0', 'pbe-1', 'pbe-2', 'pbe-3', 'pbe-4', 'pbe-6', 'pbe-8',
        'pad-cq',
        'm-0', 'mx-auto',
        'mt-0', 'mt-1', 'mt-2', 'mt-3', 'mt-4', 'mt-6', 'mt-8',
        'mb-0', 'mb-1', 'mb-2', 'mb-3', 'mb-4', 'mb-6', 'mb-8',
        'mis-0', 'mis-1', 'mis-2', 'mis-3', 'mis-4', 'mis-6', 'mis-8', 'mis-auto',
        'mie-0', 'mie-1', 'mie-2', 'mie-3', 'mie-4', 'mie-6', 'mie-8', 'mie-auto',
        'gap-0', 'gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-6', 'gap-8', 'gap-cq',
        'safe-top', 'safe-bottom', 'safe-x',
    ],
];

require __DIR__ . '/../_layout.php';

/* The steps of the scale, in order. Which of them have a utility is looked up
   from the inventory rather than typed, so if someone adds .p-5 this table
   grows a row instead of quietly lying about the set. */
$STEPS = [
    ['--space-0', '0'], ['--space-px', 'px'], ['--space-1', '1'], ['--space-2', '2'],
    ['--space-3', '3'], ['--space-4', '4'], ['--space-5', '5'], ['--space-6', '6'],
    ['--space-8', '8'], ['--space-10', '10'], ['--space-12', '12'], ['--space-16', '16'],
    ['--space-20', '20'], ['--space-24', '24'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">Spacing</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Spacing</h1>
  <p class="lede">
    Every gap, pad and margin in Deck comes from one scale of <?= count($STEPS) ?>
    steps built on a 4px base. Seven of those steps have utilities. This page is the
    scale, the reason the utilities are a subset of it, and the reason to reach for
    <code>gap</code> before reaching for a margin.
  </p>
</header>

<section class="stack-4">
  <h2 id="scale">One scale</h2>
  <p>
    The base unit is 4px, expressed in <code>rem</code> so it follows the reader's
    font size instead of overriding it. Somebody who has set their browser to 20px
    because 16px is uncomfortable gets a page whose spacing grows with the text. A
    scale written in <code>px</code> would give them larger text crammed into the
    same gaps, which is worse than not scaling at all.
  </p>
  <p>
    The steps are not linear. They double and half-step — <code>.25 / .5 / .75 / 1 /
    1.25 / 1.5 / 2 / 2.5 / 3 / 4 / 5 / 6</code> rem — because the eye reads ratios
    rather than differences. The difference between 4px and 8px is obvious; the
    difference between 80px and 84px is invisible. A linear scale spends most of its
    steps on distinctions nobody can see.
  </p>

  <?php docs_token_table(array_column($STEPS, 0)); ?>

  <p class="dx-note">
    Two more tokens are fluid rather than fixed.
    <code>--space-section</code> is
    <code><?= e(api_token('--space-section')['value'] ?? '') ?></code> and
    <code>--space-gutter</code> is
    <code><?= e(api_token('--space-gutter')['value'] ?? '') ?></code>. They are the
    page rhythm — the band of air around a section, and the breathing room at the
    edge of the viewport — and both are things that should be tighter on a phone and
    looser on a monitor without a media query picking where the line falls.
    <code>.section</code> and <code>.container</code> consume them for you.
  </p>
</section>

<section class="stack-4">
  <h2 id="subset">The utilities are a subset, on purpose</h2>
  <p>
    Fourteen steps in the scale; seven with utilities: <strong>0, 1, 2, 3, 4, 6,
    8</strong>. There is no <code>.p-5</code>, no <code>.mt-12</code>, no
    <code>.gap-24</code>, and that is not an oversight waiting to be filled in.
  </p>
  <p>
    The two halves of the scale get used by different things. The small end — 4px to
    32px — is the distance between a label and its input, an icon and its text, a row
    and the next row. That is a nudge; it belongs to one place on one page, and
    typing it into the markup is quicker than naming it. The large end — 40px and up
    — is page rhythm: the band above a section, the inset of a hero. That is a
    decision about the whole page, it repeats, and it belongs in a rule with a name
    rather than scattered across forty elements as <code>.mt-16</code>.
  </p>
  <p>
    So the seven steps cover the nudges and stop. If you find yourself wanting
    <code>.p-12</code>, the useful question is not "why is it missing" but "what is
    this element, and should it not be a <code>.section</code>, a <code>.card</code>,
    or a rule of your own reading <code>var(--space-12)</code>?" Every step of the
    scale is a public token whether or not it has a utility, and your own layer beats
    Deck with no <code>!important</code>:
  </p>
  <pre class="dx-code"><code>@layer app.pages {
  .hero { padding-block: var(--space-12); }
}</code></pre>

  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Which steps of the scale have utilities</caption>
      <thead>
        <tr>
          <th scope="col">Step</th>
          <th scope="col">Token</th>
          <th scope="col">Value</th>
          <th scope="col">Utility</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($STEPS as [$token, $step]): ?>
          <?php $has = api_class('p-' . $step) !== null; ?>
          <tr>
            <th scope="row" data-label="Step"><code><?= e($step) ?></code></th>
            <td data-label="Token"><code class="dx-dim"><?= e($token) ?></code></td>
            <td data-label="Value"><code><?= e(api_token($token)['value'] ?? '') ?></code></td>
            <td data-label="Utility">
              <?php if ($has): ?>
                <code><?= e('.p-' . $step) ?></code>,
                <code><?= e('.gap-' . $step) ?></code>,
                <code><?= e('.mt-' . $step) ?></code> and the rest
              <?php else: ?>
                <span class="dx-dim">token only — write
                  <code><?= e('var(' . $token . ')') ?></code> in your own rule</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-4">
  <h2 id="logical">Physical names, logical properties</h2>
  <p>
    The short utilities are named the way everyone expects and implemented the way
    Deck needs. <code>.py-4</code> does not set <code>padding-top</code> and
    <code>padding-bottom</code>; it sets <code>padding-block</code>.
    <code>.mt-4</code> sets <code>margin-block-start</code>. Nothing in Deck is
    written in physical properties, so an Arabic or Hebrew page mirrors completely
    from <code>dir="rtl"</code> with no second stylesheet.
  </p>
  <p>
    That is also why there is no <code>.ml-4</code> or <code>.mr-4</code>. Left and
    right are the two directions that actually flip, so naming them would build the
    bug in. The inline pair is spelled out instead.
  </p>

  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Utility prefixes and the properties they set</caption>
      <thead>
        <tr>
          <th scope="col">Prefix</th>
          <th scope="col">Property</th>
          <th scope="col">In a left-to-right page</th>
        </tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="Prefix"><code>.p-</code></th><td data-label="Property"><code>padding</code></td><td data-label="In a left-to-right page">All four sides</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.px-</code></th><td data-label="Property"><code>padding-inline</code></td><td data-label="In a left-to-right page">Left and right</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.py-</code></th><td data-label="Property"><code>padding-block</code></td><td data-label="In a left-to-right page">Top and bottom</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.pis-</code></th><td data-label="Property"><code>padding-inline-start</code></td><td data-label="In a left-to-right page">Left</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.pie-</code></th><td data-label="Property"><code>padding-inline-end</code></td><td data-label="In a left-to-right page">Right</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.pbs-</code></th><td data-label="Property"><code>padding-block-start</code></td><td data-label="In a left-to-right page">Top</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.pbe-</code></th><td data-label="Property"><code>padding-block-end</code></td><td data-label="In a left-to-right page">Bottom</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.mt-</code></th><td data-label="Property"><code>margin-block-start</code></td><td data-label="In a left-to-right page">Top</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.mb-</code></th><td data-label="Property"><code>margin-block-end</code></td><td data-label="In a left-to-right page">Bottom</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.mis-</code></th><td data-label="Property"><code>margin-inline-start</code></td><td data-label="In a left-to-right page">Left</td></tr>
        <tr><th scope="row" data-label="Prefix"><code>.mie-</code></th><td data-label="Property"><code>margin-inline-end</code></td><td data-label="In a left-to-right page">Right</td></tr>
      </tbody>
    </table>
  </div>

  <p>
    <code>.mis-auto</code> and <code>.mie-auto</code> do the most work of the set.
    <code>.mis-auto</code> pushes an item to the far end of a flex row — it is what
    <code>.push</code> in the navbar is underneath. <code>.mx-auto</code> centres a
    block by splitting the leftover inline space evenly.
  </p>
  <?php docs_example(
      '<div class="bar"><span class="badge">Start</span><span class="badge mis-auto">Pushed to the end</span></div>',
      'mis-auto: one class on the item instead of a rule on the parent',
      'stack'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="gap">Why gap beats margin</h2>
  <p>
    When you are spacing a run of siblings, set <code>gap</code> on the parent rather
    than a margin on the children. Four reasons, in ascending order of how much time
    they cost you.
  </p>
  <ol class="stack-3">
    <li>
      <strong>The last one is wrong.</strong> <code>.mb-4</code> on every child puts a
      margin under the final child too, so the group ends with a space that is not
      between anything. The usual fix is a <code>:last-child</code> reset, which is a
      rule you now have to write, name and remember.
    </li>
    <li>
      <strong>Margins collapse; gaps do not.</strong> Adjacent block margins merge
      into the larger of the two, so <code>.mb-4</code> above and <code>.mt-2</code>
      below give you 1rem rather than 1.5rem. That is correct CSS and almost never
      what anyone intended. <code>gap</code> has no collapsing rules at all.
    </li>
    <li>
      <strong>The spacing belongs to the group, not the item.</strong> A margin on a
      child is a claim about its surroundings made from inside it, so the same
      component spaces itself differently depending on where you drop it. A
      <code>gap</code> is set once by whatever is doing the arranging, which is the
      thing that actually knows.
    </li>
    <li>
      <strong>It survives reordering.</strong> Wrap a row, reverse it, remove a child,
      insert one — the gaps stay right, because there is a single value and the layout
      applies it. Margins have to be re-reasoned every time the order changes.
    </li>
  </ol>
  <p>
    Deck's layout primitives all set <code>gap</code> from a <code>--gap</code> custom
    property, so most of the time you do not want <code>.gap-*</code> at all — you
    want <code>.stack-3</code> or <code>.cluster-tight</code>, which set the same
    thing by name. <code>.gap-*</code> is for a flex or grid container you built
    yourself.
  </p>
  <?php docs_example(
      '<div class="stack-2">' . "\n" .
      '  <div class="card"><div class="card-body">First</div></div>' . "\n" .
      '  <div class="card"><div class="card-body">Second</div></div>' . "\n" .
      '  <div class="card"><div class="card-body">Third, with no trailing space under it</div></div>' . "\n" .
      '</div>',
      'stack-2 sets --gap; nothing is spaced from the inside',
      'stack'
  ); ?>
  <p class="dx-note">
    The exception is one element that needs to sit away from one specific neighbour —
    a back link above a heading, say. That is a real margin: it is about those two
    elements and not about a group, and <code>.mt-6</code> is the right tool for it.
  </p>
</section>

<section class="stack-4">
  <h2 id="safe">Safe areas</h2>
  <p>
    Three padding utilities read <code>env(safe-area-inset-*)</code> instead of the
    scale, for the rounded corners and the home indicator on a phone. They are the
    only spacing in Deck that changes with the device rather than the design, and
    they are zero on every screen without a notch — so adding one costs nothing where
    it is not needed.
  </p>
  <p>
    <code>.safe-x</code> takes the larger of the gutter and the inset, so text never
    ends up tighter to the edge than the rest of the page. All three need
    <code>viewport-fit=cover</code> in the viewport meta tag, which the starter page
    and <code>Deck::head()</code> both emit.
  </p>
  <?php docs_utility_table(['safe-top', 'safe-bottom', 'safe-x'], 'Safe-area padding utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="container">Spacing that follows the container</h2>
  <p>
    <code>.pad-cq</code> and <code>.gap-cq</code> scale with the width of the nearest
    container rather than the viewport, using <code>cqi</code> units clamped to the
    ends of the scale. A card in a narrow sidebar takes the small value and the same
    card in a wide main column takes the large one, with no media query and no second
    class.
  </p>
  <p>
    Both only apply inside an element marked <code>.cq</code>. Outside one they do
    nothing whatsoever, which is why the rows below lead with the selector that
    carries the rule rather than with the class on its own.
  </p>
  <?php docs_utility_table(['pad-cq', 'gap-cq'], 'Container-relative spacing'); ?>
</section>

<section class="stack-4">
  <h2 id="padding">Padding</h2>
  <p>
    <code>.p-*</code> sets all four sides, <code>.px-*</code> the inline axis,
    <code>.py-*</code> the block axis.
  </p>
  <?php docs_utility_table([
      'p-0', 'p-1', 'p-2', 'p-3', 'p-4', 'p-6', 'p-8',
      'px-0', 'px-1', 'px-2', 'px-3', 'px-4', 'px-6', 'px-8',
      'py-0', 'py-1', 'py-2', 'py-3', 'py-4', 'py-6', 'py-8',
  ], 'Padding utilities on both axes'); ?>

  <h3 id="padding-sides">One side at a time</h3>
  <p>
    Four families, all logical: inline start and end, block start and end. Twenty
    eight classes that exist so you never have to write a physical
    <code>padding-left</code> into a page that might be read right to left.
  </p>
  <?php docs_utility_table([
      'pis-0', 'pis-1', 'pis-2', 'pis-3', 'pis-4', 'pis-6', 'pis-8',
      'pie-0', 'pie-1', 'pie-2', 'pie-3', 'pie-4', 'pie-6', 'pie-8',
      'pbs-0', 'pbs-1', 'pbs-2', 'pbs-3', 'pbs-4', 'pbs-6', 'pbs-8',
      'pbe-0', 'pbe-1', 'pbe-2', 'pbe-3', 'pbe-4', 'pbe-6', 'pbe-8',
  ], 'Single-side logical padding utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="margin">Margin</h2>
  <p>
    Deliberately a smaller set than the padding one. There is no <code>.m-4</code>,
    only <code>.m-0</code>, because a margin on all four sides of something is nearly
    always a padding on its parent. The block pair covers "space this away from the
    thing above it", and the inline pair is there for the alignment tricks.
  </p>
  <?php docs_utility_table([
      'm-0', 'mx-auto',
      'mt-0', 'mt-1', 'mt-2', 'mt-3', 'mt-4', 'mt-6', 'mt-8',
      'mb-0', 'mb-1', 'mb-2', 'mb-3', 'mb-4', 'mb-6', 'mb-8',
      'mis-auto', 'mis-0', 'mis-1', 'mis-2', 'mis-3', 'mis-4', 'mis-6', 'mis-8',
      'mie-auto', 'mie-0', 'mie-1', 'mie-2', 'mie-3', 'mie-4', 'mie-6', 'mie-8',
  ], 'Margin utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="gap-table">Gap</h2>
  <p>
    <code>gap</code> applies to flex, grid and multi-column containers. On anything
    else it is inert, which makes <code>.gap-3</code> on a plain
    <code>&lt;div&gt;</code> a silent no-op and the commonest way to lose ten minutes
    with these. Check that the parent is really a flex or grid container first.
  </p>
  <?php docs_utility_table([
      'gap-0', 'gap-1', 'gap-2', 'gap-3', 'gap-4', 'gap-6', 'gap-8', 'gap-cq',
  ], 'Gap utilities'); ?>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use these</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for the space between siblings.</strong> That is
      <code>.stack-*</code>, <code>.cluster</code> or <code>.grid</code>, each of
      which sets a gap for you. A column of <code>.mb-4</code> is a stack written the
      long way.
    </li>
    <li>
      <strong>Not for page rhythm.</strong> <code>.section</code> already applies
      <code>--space-section</code>, and it is fluid. Replacing it with
      <code>.py-8</code> pins the band at 2rem on a 27 inch monitor.
    </li>
    <li>
      <strong>Not for the viewport edge.</strong> <code>.container</code> applies
      <code>--space-gutter</code> on the inline axis. Adding <code>.px-4</code> on top
      of it double-pads the page.
    </li>
    <li>
      <strong>Not to fix a component.</strong> If a card needs a nudge to look right,
      the card is wrong, and every other card on the site is wrong the same way. Fix
      it once in your own layer.
    </li>
    <li>
      <strong>Not as <code>.ml-*</code> or <code>.mr-*</code>.</strong> Those do not
      exist. Use <code>.mis-*</code> and <code>.mie-*</code>, which flip correctly
      when the page does.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
