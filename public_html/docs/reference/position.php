<?php
declare(strict_types=1);

/**
 * The positioning reference. Ten classes and one paragraph.
 */

$page = [
    'path' => 'reference/position.php',
    'title' => 'Position',
    'level' => 'Beginner',
    'description' => "The ten positioning utilities in Deck: relative, absolute, fixed and sticky, the logical inset classes for each edge, and the single z-index step.",
    'documents' => [
        'relative', 'absolute', 'fixed', 'sticky',
        'inset-0', 'inset-is-0', 'inset-ie-0', 'inset-bs-0', 'inset-be-0',
        'z-1',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">Position</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Position</h1>
  <p class="lede">
    Ten classes: the four <code>position</code> values, the five insets, and one
    <code>z-index</code> step. Deliberately thin. Everything in Deck that genuinely
    needs to be positioned — the modal, the toast region, the sticky header, the
    popover — positions itself, and reads a named <code>--z-*</code> token to do it.
  </p>
</header>

<section class="stack-4">
  <h2 id="position">The four values</h2>
  <p>
    The pairing you will use most is <code>.relative</code> on a parent and
    <code>.absolute</code> on a child, which is how you put a badge on the corner of
    an avatar or an overlay across an image. <code>position: absolute</code> resolves
    against the nearest positioned ancestor, so forgetting the <code>.relative</code>
    means the child jumps to the viewport instead — usually to the top left of the
    page, which is the tell.
  </p>
  <p>
    <code>.sticky</code> needs three things to work, and the third is the one that
    catches people: a <code>position: sticky</code>, an inset on the axis you are
    sticking to, and no ancestor with <code>overflow: hidden</code>, <code>auto</code>
    or <code>scroll</code> between it and the scroll container. If a sticky element
    simply does not stick, an ancestor's overflow is nearly always why —
    <code>.overflow-clip</code> is the fix, since it clips without creating a scroll
    container.
  </p>
  <?php docs_utility_table(['relative', 'absolute', 'fixed', 'sticky'], 'Position utilities'); ?>
  <p class="dx-note">
    For a header that sticks to the top of the page, use
    <a href="../components/bar.php"><code>.sticky-top</code></a> rather than
    <code>.sticky</code>. It sets the inset, the <code>--z-sticky</code> token and the
    translucent backdrop in one class.
  </p>
</section>

<section class="stack-4">
  <h2 id="inset">Insets</h2>
  <p>
    <code>.inset-0</code> pins all four edges, which with <code>.absolute</code> is the
    full-cover overlay. The four single-edge classes are logical, so
    <code>.inset-is-0</code> is the left edge in an English page and the right edge in
    an Arabic one, and a corner badge stays in the correct corner when the page flips.
  </p>
  <?php docs_utility_table(
      ['inset-0', 'inset-is-0', 'inset-ie-0', 'inset-bs-0', 'inset-be-0'],
      'Logical inset utilities'
  ); ?>
  <?php docs_example(
      '<div class="relative" style="inline-size:8rem">' . "\n" .
      '  <div class="square bg-soft r-md"></div>' . "\n" .
      '  <span class="badge badge-solid absolute inset-be-0 inset-ie-0 mie-2 mb-2">New</span>' . "\n" .
      '</div>',
      'A corner badge that stays in the corner when the page flips',
      'stack'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="z">Stacking</h2>
  <p>
    There is exactly one <code>z-index</code> utility, and it is
    <code>z-index: 1</code>. That is not an omission. Stacking bugs come from
    competing numbers, and the fix is a small set of named layers rather than a
    bigger set of numbers — so Deck's overlays read tokens
    (<code>--z-sticky</code>, <code>--z-nav</code>, <code>--z-sheet</code>,
    <code>--z-modal</code>, <code>--z-toast</code>, in that order) and everything else
    is expected to sort itself out in document order.
  </p>
  <p>
    <code>.z-1</code> exists for the one honest case: lifting a positioned element
    above a sibling that comes after it in the source. If you find yourself wanting
    <code>.z-50</code>, the element probably belongs to one of the named layers, and
    the token is the thing to reach for.
  </p>
  <?php docs_utility_table(['z-1'], 'Stacking utilities'); ?>
  <?php docs_token_table(['--z-sticky', '--z-nav', '--z-sheet', '--z-modal', '--z-toast']); ?>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use these</h2>
  <ul class="stack-3">
    <li>
      <strong>Not to build an overlay.</strong> <code>.modal</code>,
      <code>.drawer</code> and <code>.sheet</code> use the top layer, so they sit above
      everything with no <code>z-index</code> involved at all and get the focus trap
      and the backdrop with them.
    </li>
    <li>
      <strong>Not to pin a toolbar to the bottom of a phone screen.</strong> That is
      <code>.tabbar</code>, which also handles the home indicator inset.
    </li>
    <li>
      <strong>Not <code>.fixed</code> inside a transformed ancestor.</strong> A
      <code>transform</code>, <code>filter</code> or <code>perspective</code> anywhere
      above it makes that ancestor the containing block, and the element stops being
      fixed to the viewport. This is not a bug you can fix from the fixed element.
    </li>
    <li>
      <strong>Not <code>.absolute</code> for a layout.</strong> Absolutely positioned
      children are out of flow, so the parent does not grow around them. If the box
      needs to make room, it is a grid or a flex row.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
