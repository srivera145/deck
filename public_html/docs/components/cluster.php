<?php
declare(strict_types=1);

$page = [
    'path' => 'components/cluster.php',
    'title' => 'Cluster',
    'level' => 'Beginner',
    'description' => 'Deck\'s .cluster is a flex row that wraps instead of overflowing, with four alignment variants, and how it differs from a bar that never wraps and a scroller that scrolls.',
    'documents' => [
        'cluster', 'cluster-between', 'cluster-center', 'cluster-end', 'cluster-tight',
    ],

    'component' => 'cluster',
    'accounts' => [
        '04-layout.css' => 'documented: the cluster and its four alignment variants. .bar sits in the same file and has its own page.',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Cluster</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Cluster</h1>
  <p class="lede">
    <code>.cluster</code> is a flex row that <strong>wraps</strong>. That one word is the
    component: a row of buttons, chips or filters that runs out of room drops onto a
    second line instead of pushing the page sideways. Its default gap is
    <?= e(api_token('--space-3')['value'] ?? '.75rem') ?>, tighter than a stack's,
    because things side by side need less air than things stacked.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for any horizontal group whose length you do not control: action buttons,
    tags, a breadcrumb, a toolbar. <code>align-items: center</code> is set, so items of
    different heights — a button beside a badge beside a line of text — line up on their
    middles without any help.
  </p>
  <?php
  docs_example(
      '<div class="cluster">' . "\n" .
      '  <button class="btn btn-primary">Save</button>' . "\n" .
      '  <button class="btn">Duplicate</button>' . "\n" .
      '  <button class="btn btn-ghost">Cancel</button>' . "\n" .
      '  <span class="badge badge-good">Draft saved</span>' . "\n" .
      '</div>',
      'Narrow the window: it wraps rather than overflowing',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Wrapping is why a cluster is the safe default on a phone. It is also why
    <code>.nav-link</code> sets <code>white-space: nowrap</code>: a cluster will happily
    break a two-word label onto two lines, which is right for a paragraph and wrong for
    a navigation item.
  </p>
</section>

<section class="stack-6">
  <h2 id="alignment">Alignment</h2>
  <p>
    Three variants, each setting <code>justify-content</code> and nothing else. They
    change where the items sit along the row; they never change the gap or the wrapping.
  </p>

  <div class="stack-2">
    <h3 id="a-default">Default — packed to the start</h3>
    <?php
    docs_example(
        '<div class="cluster" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
        '  <button class="btn btn-sm">One</button>' . "\n" .
        '  <button class="btn btn-sm">Two</button>' . "\n" .
        '  <button class="btn btn-sm">Three</button>' . "\n" .
        '</div>',
        '',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="a-end">Packed to the end</h3>
    <p class="text-muted">
      <code>flex-end</code>, which follows the writing direction — so it is the right
      edge in English and the left edge in Arabic.
    </p>
    <?php
    docs_example(
        '<div class="cluster cluster-end" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
        '  <button class="btn btn-sm">Cancel</button>' . "\n" .
        '  <button class="btn btn-sm btn-primary">Save</button>' . "\n" .
        '</div>',
        '',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="a-center">Centred</h3>
    <?php
    docs_example(
        '<div class="cluster cluster-center" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
        '  <button class="btn btn-sm">One</button>' . "\n" .
        '  <button class="btn btn-sm">Two</button>' . "\n" .
        '</div>',
        '',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="a-between">Space between</h3>
    <p class="text-muted">
      Pushes the first item to one end and the last to the other. With exactly two
      children this is the classic title-and-action row.
    </p>
    <?php
    docs_example(
        '<div class="cluster cluster-between" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
        '  <h3>Team members</h3>' . "\n" .
        '  <button class="btn btn-sm btn-primary">Invite</button>' . "\n" .
        '</div>',
        '',
        'stack'
    );
    ?>
    <p class="dx-note text-muted">
      <code>space-between</code> and wrapping interact badly. When the row wraps, each
      line is justified separately, so a trailing item alone on line two jumps to the far
      end and looks stranded. If the row can wrap and you want an end-aligned action,
      reach for <code>.bar</code> and <code>.push</code> instead.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="gap">Gap</h2>
  <p>
    <code>.cluster</code> reads the same <code>--gap</code> as every other layout
    primitive, so the whole <a href="stack.php#scale">spacing scale</a> works on it:
    <code>.stack-2</code> on a cluster sets a
    <?= e(api_token('--space-2')['value'] ?? '.5rem') ?> gap exactly as it would on a
    stack.
  </p>
  <p>
    <code>.cluster-tight</code> exists as a readable name for step 2, which is what most
    clusters want. It is the only place in Deck where a gap has a word instead of a
    number, and that inconsistency is deliberate but not defensible — it is recorded in
    <code>FINDINGS.md</code>.
  </p>
  <?php
  docs_example(
      '<div class="stack-3">' . "\n" .
      '  <div class="cluster cluster-tight">' . "\n" .
      '    <span class="chip">Overdue</span><span class="chip">This week</span><span class="chip">Unassigned</span>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="cluster stack-6">' . "\n" .
      '    <span class="chip">Overdue</span><span class="chip">This week</span><span class="chip">Unassigned</span>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Tight, then step 6 — the step classes work on a cluster unchanged',
      'stack'
  );
  ?>
  <p class="text-muted">
    Both <code>row-gap</code> and <code>column-gap</code> come from the same value, so a
    wrapped cluster has the same space between its lines as between its items. That is
    usually right and occasionally too much; set
    <code>row-gap</code> separately if a wrapped toolbar looks loose.
  </p>
</section>

<section class="stack-3">
  <h2 id="bar">The row that does not wrap</h2>
  <p>
    <a href="bar.php"><code>.bar</code></a> is the same idea with the wrapping removed: a
    flex row, <code>align-items: center</code>, a fixed
    <?= e(api_token('--space-3')['value'] ?? '.75rem') ?> gap. Use it when the row is a
    single line by definition — a card header, a toolbar with an overflow menu — and see
    <a href="bar.php">its own page</a> for <code>.push</code> and for what happens when
    a bar runs out of room.
  </p>
  <?php
  docs_example(
      '<div class="bar" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
      '  <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#folder"></use></svg>' . "\n" .
      '  <strong>Q1 exports</strong>' . "\n" .
      '  <span class="badge">12 files</span>' . "\n" .
      '  <button class="btn btn-sm push">Share</button>' . "\n" .
      '  <button class="btn btn-sm btn-ghost btn-icon" aria-label="More">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#more-horizontal"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'Three items, then .push, then two more — the split is where you put it',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="overflow">What happens when it does not fit</h2>
  <p>
    This is the difference that matters when you are choosing between the three
    horizontal primitives:
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">How each row primitive handles running out of space</caption>
      <thead>
        <tr><th scope="col">Class</th><th scope="col">Out of room</th><th scope="col">Reach for it when</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Class"><code>.cluster</code></th>
          <td data-label="Out of room">Wraps to the next line</td>
          <td data-label="Reach for it when">The number of items varies, or is set by a user</td>
        </tr>
        <tr>
          <th scope="row" data-label="Class"><code>.bar</code></th>
          <td data-label="Out of room">Squashes, then overflows</td>
          <td data-label="Reach for it when">The row is one line by definition</td>
        </tr>
        <tr>
          <th scope="row" data-label="Class"><code>.scroller</code></th>
          <td data-label="Out of room">Scrolls sideways, snapping</td>
          <td data-label="Reach for it when">The items are cards and there are many</td>
        </tr>
      </tbody>
    </table>
  </div>
  <p class="dx-note text-muted">
    <code>.bar</code> squashing is the one to watch, and it is covered on
    <a href="bar.php">its own page</a>: a flex item's minimum size is its content, so a
    long unbroken string does not squash — it overflows, and takes the page with it.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    Generated from <code>src/04-layout.css</code>. <code>.push</code> is listed because
    this page documents it; the extractor puts it in <code>deck.utilities</code>, not in
    the <code>cluster</code> component.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--space-2', '--space-3', '--space-4', '--space-6']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>No semantics, as with every layout primitive.</strong> A cluster of links
      that is navigation needs a <code>&lt;nav&gt;</code> with a label; a cluster of tags
      that is a list needs <code>&lt;ul&gt;</code>.
    </li>
    <li>
      <strong>Visual order is DOM order, and must stay that way.</strong>
      <code>.cluster-end</code> and <code>.cluster-between</code> move the whole group;
      they never reorder items. Do not reach for <code>order</code> to put the primary
      button last visually — the tab order would then not match, and a keyboard user
      would tab to Cancel first.
    </li>
    <li>
      <strong><code>.push</code> is safe in this respect</strong> because it moves items
      without reordering them: everything after it moves as a block, in markup order.
    </li>
    <li>
      <strong>Wrapping is an accessibility feature.</strong> At 200% zoom or a 320px
      viewport, a row that wraps stays usable and a row that overflows hides its last
      item off-screen. WCAG's reflow criterion is about exactly this.
    </li>
    <li>
      <strong>The gap is the only thing separating adjacent controls.</strong> Two
      buttons
      <?= e(api_token('--space-3')['value'] ?? '.75rem') ?> apart are distinguishable;
      <code>.cluster-tight</code> at
      <?= e(api_token('--space-2')['value'] ?? '.5rem') ?> is close to the point where
      an imprecise tap hits the wrong one. Keep <code>.cluster-tight</code> for chips
      and badges rather than for destructive actions.
    </li>
    <li>
      <strong>A cluster of icon-only buttons needs labels.</strong> Nothing about the
      layout supplies a name; each <code>.btn-icon</code> needs its own
      <code>aria-label</code>.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>justify-content: flex-end</code> follows the writing direction, and
    <code>.push</code> is <code>margin-inline-start</code>, so both mirror with no extra
    rules. A cluster is one of the components where writing logically costs nothing and
    buys the whole behaviour.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="stack-3">' . "\n" .
      '  <div class="cluster cluster-end" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
      '    <button class="btn btn-sm">إلغاء</button>' . "\n" .
      '    <button class="btn btn-sm btn-primary">حفظ</button>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="bar" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
      '    <strong>تصدير الربع الأول</strong>' . "\n" .
      '    <button class="btn btn-sm push">مشاركة</button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'End alignment and .push both follow the direction',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing here animates or transitions. Wrapping happens at layout time and is not a
    transition, so a cluster reflowing as the window narrows is not something
    <code>prefers-reduced-motion</code> has any say over.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    No print rule. A wrapping flex row prints as a wrapping flex row, which is correct —
    unlike <code>.grid</code> and <code>.split</code>, which are unwound to
    <code>display: block</code> so their content can break across pages.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One cluster, no layer needed */
<div class="cluster" style="--gap:var(--space-1)">

@layer app.components {
  /* Tighter lines than items when a toolbar wraps */
  .cluster { row-gap: var(--space-2); }

  /* Align a cluster on its first line of text rather than its middle */
  .cluster-baseline { align-items: baseline; }
}') ?></code></pre>
  <p>
    <code>align-items: baseline</code> is the override worth knowing about: for a row
    that is mostly text at different sizes — a heading beside a timestamp — baseline
    alignment reads better than centring, and Deck does not ship it because centring is
    right for the far more common case of a control beside a label.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not when wrapping would be wrong.</strong> A navigation bar whose links
      drop onto a second line looks broken. Use <code>.bar</code>, and let the
      overflowing items go into a menu.
    </li>
    <li>
      <strong>Not for many items.</strong> Twenty chips in a cluster is five wrapped
      lines. Use <code>.scroller</code>, which keeps them on one line and scrolls.
    </li>
    <li>
      <strong>Not for a grid of cards.</strong> A cluster wraps but its items keep their
      natural widths, so the second line does not line up with the first. Use
      <code>.grid</code>, which gives every item an equal track.
    </li>
    <li>
      <strong>Not for a form's field pairs.</strong> Use <code>.field-row</code>, which
      is a grid with the form's gap and a column floor that stacks on a phone. A cluster
      of fields wraps at unpredictable places.
    </li>
    <li>
      <strong><code>.cluster-between</code> is not a layout for three or more
      items</strong> that can wrap. Once it wraps, each line justifies on its own and the
      result looks accidental. <code>.bar</code> with <code>.push</code> is the
      predictable version.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
