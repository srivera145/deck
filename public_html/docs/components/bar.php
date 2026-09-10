<?php
declare(strict_types=1);

$page = [
    'path' => 'components/bar.php',
    'title' => 'Bar',
    'level' => 'Beginner',
    'description' => 'Deck\'s .bar is a flex row that never wraps, and .push is the one declaration that shoves everything after it to the far end — a better split than space-between because you choose where it happens.',
    'documents' => [
        'bar', 'push',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Bar</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Bar</h1>
  <p class="lede">
    <code>.bar</code> is a flex row with <code>align-items: center</code> and a
    <?= e(api_token('--space-3')['value'] ?? '.75rem') ?> gap, and it does
    <strong>not</strong> wrap. Three declarations. Its value is entirely in what it does
    not do: a bar stays one line, so a header does not suddenly become two rows tall when
    somebody adds a button.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it when the row is a single line by definition: a card header, a toolbar, a
    table's title row, a panel header with a control at the end. If wrapping would look
    like a bug rather than a feature, you want a bar.
  </p>
  <?php
  docs_example(
      '<div class="bar" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
      '  <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#folder"></use></svg>' . "\n" .
      '  <strong>Q1 exports</strong>' . "\n" .
      '  <span class="badge">12 files</span>' . "\n" .
      '</div>',
      'One line, whatever the width',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="push">.push</h2>
  <p>
    <code>.push</code> is <code>margin-inline-start: auto</code>. An auto margin in a
    flex row absorbs all the free space, so the element it is on — and everything after
    it — is shoved to the far end.
  </p>
  <p>
    It is the better alternative to <code>justify-content: space-between</code> for two
    reasons. It works with any number of children, and <strong>you choose where the split
    happens</strong> instead of it always falling between the first item and the rest.
  </p>
  <?php
  docs_example(
      '<div class="bar" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
      '  <strong>Q1 exports</strong>' . "\n" .
      '  <span class="badge">12 files</span>' . "\n" .
      '  <button class="btn btn-sm push">Share</button>' . "\n" .
      '  <button class="btn btn-sm btn-ghost btn-icon" aria-label="More">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#more-horizontal"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'Two items, then .push, then two more — the gap opens where you put it',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.push</code> is a standalone utility in <code>src/09-utilities.css</code>, so
    it works in any flex row, not only inside <code>.bar</code>.
    <code>.card-header</code>, <code>.menu-item</code> and <code>.sidebar-link</code>
    all rely on it, and the last two carry their own copy of the rule so the pushed
    element also picks up a muted colour.
  </p>
  <?php
  docs_example(
      '<div class="card" style="max-inline-size:26rem">' . "\n" .
      '  <div class="card-header">' . "\n" .
      '    <h3 class="card-title">Invoice INV-2291</h3>' . "\n" .
      '    <span class="badge badge-good push">Paid</span>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="card-body"><p class="text-muted">.card-header is a flex row, so .push works there too.</p></div>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="squash">What happens when it does not fit</h2>
  <p>
    A bar does not wrap, so when it runs out of room the items squash — and then, past a
    point, overflow. The point where squashing stops is the thing to know about: a flex
    item's minimum size is its content, so text with no break opportunity in it will not
    squash at all. It overflows, and takes the page's horizontal scrollbar with it.
  </p>
  <p>
    The fix is to nominate the child that should give way, with
    <code>min-inline-size: 0</code> to allow the shrink and <code>.truncate</code> to
    decide what happens to the text.
  </p>
  <?php
  docs_example(
      '<div class="bar" style="max-inline-size:20rem;border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
      '  <span class="truncate" style="min-inline-size:0">a-very-long-export-filename-2026-03-11.csv</span>' . "\n" .
      '  <span class="badge push">2.4 MB</span>' . "\n" .
      '</div>',
      'The name gives way; the badge keeps its size',
      'stack'
  );
  ?>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">How each row primitive handles running out of space</caption>
      <thead>
        <tr><th scope="col">Class</th><th scope="col">Out of room</th><th scope="col">Reach for it when</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Class"><code>.bar</code></th>
          <td data-label="Out of room">Squashes, then overflows</td>
          <td data-label="Reach for it when">The row is one line by definition</td>
        </tr>
        <tr>
          <th scope="row" data-label="Class"><code>.cluster</code></th>
          <td data-label="Out of room">Wraps to the next line</td>
          <td data-label="Reach for it when">The number of items varies</td>
        </tr>
        <tr>
          <th scope="row" data-label="Class"><code>.scroller</code></th>
          <td data-label="Out of room">Scrolls sideways, snapping</td>
          <td data-label="Reach for it when">The items are cards and there are many</td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-3">
  <h2 id="gap">Gap</h2>
  <p>
    <code>.bar</code> is the one layout primitive that does <strong>not</strong> read
    <code>--gap</code>: it hard-codes <code>gap: var(--space-3)</code>. So the
    <a href="stack.php#scale">spacing scale</a> classes have no effect on it, and
    changing a bar's gap means setting <code>gap</code> directly.
  </p>
  <p class="dx-note text-muted">
    That is an inconsistency rather than a decision — <code>.stack</code>,
    <code>.cluster</code>, <code>.grid</code>, <code>.split</code>,
    <code>.scroller</code> and <code>.center</code> all take <code>--gap</code> and
    <code>.bar</code> does not. It is recorded in <code>FINDINGS.md</code>; the one-word
    fix is to wrap the value in <code>var(--gap, …)</code> like its siblings.
  </p>
  <?php
  docs_example(
      '<div class="bar" style="gap:var(--space-6);border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
      '  <strong>Wider gap</strong>' . "\n" .
      '  <span class="badge push">set with gap, not --gap</span>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.bar</code> has one member rule — <code>.bar &gt; .push</code> — and no
    <code>.bar-*</code> variants, so the extractor does not treat it as a component root
    and there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--space-3', '--space-6']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>No semantics.</strong> A bar is a <code>&lt;div&gt;</code> with
      <code>display: flex</code>. A toolbar that is genuinely a toolbar — a group of
      controls a reader arrows between — needs <code>role="toolbar"</code> and its own
      key handling, which Deck does not provide.
    </li>
    <li>
      <strong><code>.push</code> does not reorder.</strong> It moves items without
      changing their DOM order, so the tab order still matches what is on screen. That is
      the reason to prefer it over <code>order</code>, which does not.
    </li>
    <li>
      <strong>Not wrapping is a reflow risk.</strong> At 320px or 400% zoom a bar with
      several items will squash and then overflow, and horizontal scrolling of the page
      fails WCAG's reflow criterion. Nominate a child to truncate, move items into an
      overflow menu, or use <code>.cluster</code> and let it wrap.
    </li>
    <li>
      <strong>Truncated text needs to be available somewhere.</strong>
      <code>.truncate</code> clips visually and leaves the full string in the DOM, so a
      screen reader still hears all of it — but a sighted reader cannot. Add a
      <code>title</code> attribute, or make sure the full value appears elsewhere.
    </li>
    <li>
      <strong>Icon-only buttons in a bar need labels.</strong> Nothing about the layout
      supplies an accessible name; each <code>.btn-icon</code> needs its own
      <code>aria-label</code>.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>.push</code> is <code>margin-inline-start</code>, so it pushes toward the end
    edge in whichever direction the text runs. Nothing else about a bar is directional.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="bar" style="border:1px dashed var(--line);padding:var(--space-3)">' . "\n" .
      '  <strong>تصدير الربع الأول</strong>' . "\n" .
      '  <span class="badge">١٢ ملفًا</span>' . "\n" .
      '  <button class="btn btn-sm push">مشاركة</button>' . "\n" .
      '</div>',
      'The push moves to the left, because that is the end edge here',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing here animates or transitions, so
    <code>prefers-reduced-motion</code> changes nothing.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    No print rule, and none is needed: a non-wrapping flex row prints as it appears. The
    caveat is the same as on screen — if the bar is squashed or overflowing at the
    printed width, it prints that way. A bar that has to survive print should have a
    truncating child rather than relying on the viewport being wide.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Make it read --gap like every other primitive */
  .bar { gap: var(--gap, var(--space-3)); }

  /* Align on the first line of text rather than the middle */
  .bar-baseline { align-items: baseline; }

  /* Let it wrap after all, at small sizes only */
  @media (max-width: 30rem) {
    .bar { flex-wrap: wrap; }
  }
}') ?></code></pre>
  <p class="text-muted">
    That last one is worth thinking twice about. A bar that wraps on a phone is a
    cluster; if that is what you want at every size, use
    <a href="cluster.php"><code>.cluster</code></a> and skip the media query.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not when the item count varies.</strong> A row built from user data — tags,
      filters, collaborators — will eventually be too long. Use
      <a href="cluster.php"><code>.cluster</code></a>.
    </li>
    <li>
      <strong>Not for many items.</strong> A bar with twelve chips squashes all of them.
      Use <a href="scroller.php"><code>.scroller</code></a>.
    </li>
    <li>
      <strong>Not as a navigation bar.</strong> Use <code>.navbar</code>, which has the
      brand slot, the mobile behaviour and the right height. <code>.bar</code> is a
      layout primitive, not a chrome component.
    </li>
    <li>
      <strong>Not for a form's actions.</strong> Use <code>.form-actions</code>, which
      wraps and has the sticky variant for long forms on a phone.
    </li>
    <li>
      <strong>Not with <code>.push</code> on more than one child.</strong> Two auto
      margins split the free space between them, which produces a three-way distribution
      that is hard to predict and harder to explain. If you want that, use
      <code>justify-content: space-between</code> and say so.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
