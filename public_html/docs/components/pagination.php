<?php
declare(strict_types=1);

$page = [
    'path' => 'components/pagination.php',
    'title' => 'Pagination',
    'level' => 'Beginner',
    'description' => 'Deck\'s .pagination styles a row of page links from one rule on its children, so it works with links, buttons or spans. The current page is keyed to aria-current, not a class.',
    'documents' => [
        'pagination',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Pagination</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Pagination</h1>
  <p class="lede">
    A row of page numbers. It is a single class styling whatever is inside it, which makes
    it unusually flexible and means the semantics — which element, which attributes — are
    entirely up to you. That is where the care is needed.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    For a result set with a known size that people need to move around inside rather than
    read straight through — search results, an archive, a table of records. Page numbers let
    someone jump, come back, and link to where they were.
  </p>
  <p>
    For a feed that is read in one direction and has no meaningful page boundaries, infinite
    scroll or a "load more" <a href="button.php">button</a> is a better fit. Pagination
    exists to give people co-ordinates; if there is nothing to co-ordinate, it is overhead.
  </p>
  <?php
  docs_example(
      '<nav class="pagination" aria-label="Search results">' . "\n" .
      '  <a href="#" aria-label="Previous page">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-left"></use></svg>' . "\n" .
      '  </a>' . "\n" .
      '  <a href="#">1</a>' . "\n" .
      '  <a href="#">2</a>' . "\n" .
      '  <a href="#" aria-current="page">3</a>' . "\n" .
      '  <a href="#">4</a>' . "\n" .
      '  <span aria-hidden="true">…</span>' . "\n" .
      '  <a href="#">17</a>' . "\n" .
      '  <a href="#" aria-label="Next page">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-right"></use></svg>' . "\n" .
      '  </a>' . "\n" .
      '</nav>',
      'The current page is marked with aria-current, which is also what colours it',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="children">One rule, any child</h2>
  <p>
    The whole component is three declarations on the container and one on
    <code>.pagination &gt; *</code>. Every direct child — link, button, span, whatever — gets
    the same square target, the same radius and tabular figures.
  </p>
  <pre class="dx-code"><code><?= e('.pagination > * {
  min-inline-size: var(--control-h-sm);
  min-block-size: var(--control-h-sm);
  /* … centred, rounded, muted, tabular-nums … */
}
.pagination > a:hover { background: var(--surface-hover); color: var(--text); }
.pagination > [aria-current="page"] { background: var(--brand-600); … }') ?></code></pre>
  <p>
    Two consequences worth naming. The hover state applies only to
    <code>&lt;a&gt;</code> — a <code>&lt;button&gt;</code> gets the shape but no hover, and
    you will need to add it. And the current-page style is keyed to
    <code>[aria-current="page"]</code> exactly, so <code>aria-current="true"</code> gets the
    layout and not the colour. That is stricter than
    <a href="nav.php#link"><code>.nav-link</code></a>, which matches the bare attribute —
    an inconsistency between the two components rather than a bug in either.
  </p>
  <p class="dx-note text-muted">
    <code>font-variant-numeric: tabular-nums</code> is why the numbers do not shuffle
    sideways as you page from 9 to 10. It is a small thing that is very visible when it is
    missing.
  </p>
</section>

<section class="stack-3">
  <h2 id="markup">Which elements to use</h2>
  <p>
    Deck styles children and takes no view. The version that behaves best:
  </p>
  <ul class="stack-2">
    <li>
      <strong>A <code>&lt;nav&gt;</code> with <code>aria-label</code>.</strong> Pagination
      is navigation, and a page often has two sets of it.
    </li>
    <li>
      <strong>Links, not buttons</strong> — each page should have a URL someone can
      bookmark, share and open in a new tab.
    </li>
    <li>
      <strong>The current page as a <code>&lt;span&gt;</code> rather than a link.</strong>
      A link to where you already are is a tab stop that does nothing. Keep
      <code>aria-current="page"</code> on it so it still looks and announces right.
    </li>
    <li>
      <strong>Previous and next as real words</strong> in the accessible name.
      A chevron alone announces as nothing.
    </li>
    <li>
      <strong>The ellipsis hidden.</strong> "…" is announced as "horizontal ellipsis" or as
      a pause, neither of which helps. <code>aria-hidden="true"</code>.
    </li>
  </ul>
  <?php
  docs_example(
      '<nav class="pagination" aria-label="Invoices">' . "\n" .
      '  <span aria-hidden="true">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-left"></use></svg>' . "\n" .
      '  </span>' . "\n" .
      '  <span aria-current="page">1</span>' . "\n" .
      '  <a href="#">2</a>' . "\n" .
      '  <a href="#">3</a>' . "\n" .
      '  <a href="#" aria-label="Next page">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-right"></use></svg>' . "\n" .
      '  </a>' . "\n" .
      '</nav>',
      'On page one: previous is an inert span, not a disabled link',
      'stack'
  );
  ?>
  <p class="text-muted">
    There is no disabled state in the component. A <code>&lt;span&gt;</code> is the honest
    version of "you cannot go back from here" — it is not focusable, so nobody tabs to a
    dead control, and it needs no <code>aria-disabled</code> to explain itself.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.pagination</code> is a single class with no members — the styling is all on
    <code>.pagination &gt; *</code> and attribute selectors — so there is no completeness
    check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--control-h-sm', '--space-1', '--space-2', '--text-sm', '--text-muted', '--surface-hover', '--brand-600', '--text-on-brand', '--r-xs']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Label the region.</strong> <code>&lt;nav aria-label="Search results"&gt;</code>.
      Where pagination appears above and below the same list, either label them differently
      or mark the second <code>aria-hidden</code> and leave it out of the tab order — two
      identical navigation regions is a common and avoidable annoyance.
    </li>
    <li>
      <strong><code>aria-current="page"</code>, exactly.</strong> It is both the
      announcement and the styling hook, so they cannot disagree — but note the selector
      wants that precise value.
    </li>
    <li>
      <strong>The targets are 34px, not 44.</strong> <code>--control-h-sm</code> is below
      the <code>--tap</code> size Deck uses elsewhere. On a touch device, a row of 34px
      targets separated by a 4px gap is a genuinely difficult thing to hit. Raise it for
      touch, as in <a href="#overriding">Overriding it</a>.
    </li>
    <li>
      <strong>Say where the reader is in text.</strong> "Page 3 of 17" above the row helps
      everybody and is the only version that survives on paper, where the whole component is
      removed.
    </li>
    <li>
      <strong>Announce that the content changed.</strong> If pages load without a full
      navigation, the reader gets no signal. Move focus to the top of the results, or
      announce the change in a live region — the pagination cannot do it.
    </li>
    <li>
      <strong>Numbers alone are poor names.</strong> "3" as a link name is thin out of
      context. <code>aria-label="Page 3"</code> costs one attribute and makes a links list
      readable.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The row is flex, so it mirrors with the document and page 1 appears on the right —
    correct, since that is where reading starts. Nothing in
    <code>src/19-logical.css</code> is needed.
  </p>
  <p>
    The chevrons are a different matter: they are directional icons, and they mirror through
    <a href="icon.php#rtl">the icon rules</a> rather than anything in this component. If you
    use a glyph or a text arrow instead of a Deck icon, it will not flip and "previous" will
    point the wrong way.
  </p>
  <?php
  docs_example(
      '<nav dir="rtl" class="pagination" aria-label="النتائج">' . "\n" .
      '  <a href="#" aria-label="الصفحة السابقة">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-left"></use></svg>' . "\n" .
      '  </a>' . "\n" .
      '  <a href="#">١</a>' . "\n" .
      '  <a href="#" aria-current="page">٢</a>' . "\n" .
      '  <a href="#">٣</a>' . "\n" .
      '  <a href="#" aria-label="الصفحة التالية">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-right"></use></svg>' . "\n" .
      '  </a>' . "\n" .
      '</nav>',
      'Page one on the right, chevrons flipped by the icon rules',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing here is transitioned. The hover change on a link is instant by design, and there
    are no transforms or keyframes anywhere in the component — so
    <code>prefers-reduced-motion</code> has nothing to switch off.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.pagination</code> is in the "chrome that should never print" list in
    <code>src/99-print.css</code> and is removed entirely. Page numbers you cannot click are
    noise, and the printed page has its own numbering.
  </p>
  <p class="text-muted">
    This is the reason to write "Page 3 of 17" as text above the results rather than leaving
    the reader to infer it from the row. The text prints; the row does not.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Full-size targets on touch devices */
  @media (pointer: coarse) {
    .pagination > * { min-inline-size: var(--tap); min-block-size: var(--tap); }
  }

  /* Give buttons the hover state only links get */
  .pagination > button:hover { background: var(--surface-hover); color: var(--text); }

  /* Outline the current page instead of filling it */
  .pagination > [aria-current="page"] {
    background: none;
    color: var(--brand);
    box-shadow: inset 0 0 0 1px var(--brand);
  }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a feed.</strong> A stream with no stable page boundaries gives page
      numbers that mean something different every time they are loaded.
    </li>
    <li>
      <strong>Not for three pages.</strong> Previous and next, or just showing everything,
      is less machinery than a numbered row.
    </li>
    <li>
      <strong>Not for hundreds of pages.</strong> Nobody navigates to page 340 by clicking.
      If the set is that large, the reader needs filters and search, not co-ordinates.
    </li>
    <li>
      <strong>Not without URLs.</strong> If pages do not have addresses, the reader cannot
      link to a result or use the back button, and pagination's main advantage is gone.
    </li>
    <li>
      <strong>Not at small size on touch.</strong> 34px targets in a row are hard to hit —
      see <a href="#accessibility">Accessibility</a>.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
