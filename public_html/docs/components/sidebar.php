<?php
declare(strict_types=1);

$page = [
    'path' => 'components/sidebar.php',
    'title' => 'Sidebar',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .sidebar is a column of grouped destinations that collapses to icons when its container is narrow — a container query, not a media query, so it responds to the rail rather than the window.',
    'documents' => [
        'sidebar', 'sidebar-group', 'sidebar-link',
    ],

    'component' => 'sidebar',
    'accounts' => [
        '07-components.css' => 'documented: the column, the group heading, the link and its hover and current states, and the .push slot for a trailing count',
        '18-container.css'  => 'documented: the @container shell rule that collapses links to icons below 15rem — see Collapsing',
        '99-print.css'      => 'documented: .sidebar is hidden when printing — see Printing',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Sidebar</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Sidebar</h1>
  <p class="lede">
    A column of destinations, optionally under group headings, for an application with more
    places to go than a <a href="nav.php">nav bar</a> can hold. It collapses to icons when
    its container gets narrow — keyed to the rail's own width rather than the window's,
    which is the interesting part.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    For the primary navigation of an application: a dashboard, an admin area, a settings
    screen. It suits eight to twenty destinations in two or three groups, which is exactly
    the range a horizontal bar handles badly.
  </p>
  <p>
    For a marketing site or anything with five links, the <a href="nav.php">nav bar</a> is
    less structure for the same job.
  </p>
  <?php
  docs_example(
      '<nav class="sidebar card" aria-label="Sections" style="max-inline-size:16rem">' . "\n" .
      '  <a class="sidebar-link" href="#" aria-current="page">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#home"></use></svg>' . "\n" .
      '    <span>Overview</span>' . "\n" .
      '  </a>' . "\n" .
      '  <a class="sidebar-link" href="#">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#mail"></use></svg>' . "\n" .
      '    <span>Inbox</span>' . "\n" .
      '    <span class="badge push">12</span>' . "\n" .
      '  </a>' . "\n" .
      '  <p class="sidebar-group">Workspace</p>' . "\n" .
      '  <a class="sidebar-link" href="#">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#users"></use></svg>' . "\n" .
      '    <span>Members</span>' . "\n" .
      '  </a>' . "\n" .
      '  <a class="sidebar-link" href="#">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#settings"></use></svg>' . "\n" .
      '    <span>Settings</span>' . "\n" .
      '  </a>' . "\n" .
      '</nav>',
      'A .push inside a link shoves the count to the far end',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.sidebar</code> paints nothing — no background, no border. It is a padded flex
    column, so pair it with a <a href="card.php"><code>.card</code></a> or your own surface
    if you want it to read as a panel.
  </p>
</section>

<section class="stack-3">
  <h2 id="collapsing">Collapsing to icons</h2>
  <p>
    Below <code>15rem</code> the labels are hidden, the icons centre, and the group headings
    shrink to nothing. What triggers it is a <strong>container query</strong>, not a media
    query:
  </p>
  <pre class="dx-code"><code><?= e('@container shell (max-width: 15rem) {
  .sidebar-link span { display: none; }
  .sidebar-link { justify-content: center; }
  .sidebar-group { text-align: center; font-size: 0; }
}') ?></code></pre>
  <p>
    That means it responds to how wide <em>the rail</em> is, not how wide the window is — so
    a sidebar you have dragged narrow collapses on a large monitor, and a sidebar in a wide
    layout stays expanded on a small one. It is the correct behaviour for a resizable rail
    and it is why this is not a <code>@media</code> rule.
  </p>
  <p class="dx-note text-muted">
    <strong>It does nothing until you name the container.</strong> The query is against a
    container called <code>shell</code>, and Deck does not put one on the sidebar for you.
    Add <a href="container.php"><code>.cq-shell</code></a> to the element whose width should
    drive the collapse — the wrapper around the rail, usually, not the
    <code>.sidebar</code> itself. Without it, nothing collapses at any width and there is no
    error to tell you why.
  </p>
  <?php
  docs_example(
      '<div class="cq-shell" style="inline-size:4.5rem">' . "\n" .
      '  <nav class="sidebar card" aria-label="Collapsed example">' . "\n" .
      '    <a class="sidebar-link" href="#" aria-current="page" aria-label="Overview">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#home"></use></svg>' . "\n" .
      '      <span>Overview</span>' . "\n" .
      '    </a>' . "\n" .
      '    <a class="sidebar-link" href="#" aria-label="Inbox">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#mail"></use></svg>' . "\n" .
      '      <span>Inbox</span>' . "\n" .
      '    </a>' . "\n" .
      '    <a class="sidebar-link" href="#" aria-label="Settings">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#settings"></use></svg>' . "\n" .
      '      <span>Settings</span>' . "\n" .
      '    </a>' . "\n" .
      '  </nav>' . "\n" .
      '</div>',
      'A 4.5rem .cq-shell — note the aria-label on every link, for the reason below',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="collapsed-names">The collapsed state loses the link names</h2>
  <p>
    This deserves its own heading because it is a real defect rather than a caveat.
    <code>display: none</code> removes the label from the accessibility tree, not just from
    the screen. A collapsed <code>.sidebar-link</code> contains an
    <code>aria-hidden</code> icon and nothing else, so <strong>its accessible name is
    empty</strong> — a screen reader announces "link" with no destination.
  </p>
  <p>
    <code>.sidebar-group</code> is different and better: <code>font-size: 0</code> hides the
    heading visually but leaves it in the tree, so the grouping still reads correctly.
  </p>
  <p>
    Until the stylesheet handles it, put an <code>aria-label</code> on every
    <code>.sidebar-link</code> that duplicates its visible text. It costs nothing when
    expanded — an <code>aria-label</code> simply wins over the content — and it is the only
    thing standing between a collapsed rail and unusable navigation. Recorded in
    <code>FINDINGS.md</code>.
  </p>
  <pre class="dx-code"><code><?= e('<!-- The label appears twice on purpose -->
<a class="sidebar-link" href="/inbox" aria-label="Inbox">
  <svg class="icon" aria-hidden="true"><use href="…#inbox"></use></svg>
  <span>Inbox</span>
</a>') ?></code></pre>
  <p class="text-muted">
    A tooltip is not a substitute. It appears on hover and on keyboard focus, but it is not
    the element's accessible name, and it does nothing for a reader who never triggers it.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/07-components.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--text-xs', '--text-sm', '--text-faint', '--text-muted', '--surface-hover', '--brand-soft', '--brand-soft-text', '--r-sm', '--dur-1']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Wrap it in <code>&lt;nav&gt;</code> with a label.</strong> Most applications
      have at least two navigation regions; unlabelled they are announced identically.
    </li>
    <li>
      <strong>Label every link, always.</strong> See
      <a href="#collapsed-names">above</a>. This is the one non-negotiable thing on this
      page.
    </li>
    <li>
      <strong>Mark the current destination.</strong> <code>aria-current="page"</code> drives
      the styling as well as the announcement, so there is no way to have one without the
      other.
    </li>
    <li>
      <strong>A group heading is not a real heading.</strong> <code>.sidebar-group</code>
      is styling on whatever element you use. If the groups matter structurally, use nested
      <code>&lt;ul&gt;</code> elements with <code>aria-labelledby</code>, or real headings —
      a <code>&lt;p&gt;</code> in small caps groups things visually and not otherwise.
    </li>
    <li>
      <strong>The count in a <code>.push</code> is separate from the link name.</strong>
      "Inbox" and "12" are announced as two things; whether that reads as "Inbox, 12" or as
      two disconnected items varies. Where the number matters, put it in the
      <code>aria-label</code>: <code>aria-label="Inbox, 12 unread"</code>.
    </li>
    <li>
      <strong>Links are 40px tall, not 44.</strong> <code>min-block-size: 40px</code> is
      below the 44px target size Deck uses elsewhere via <code>--tap</code>. In a dense
      desktop rail that is a defensible trade; on a touch device it is a small target next
      to other small targets.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The column mirrors correctly with no rule in <code>src/19-logical.css</code>: padding
    and the gap are symmetrical, and <code>.push</code> uses
    <code>margin-inline-start: auto</code>, so a trailing count moves to the correct end.
    An icon that has direction — a chevron — mirrors through
    <a href="icon.php#rtl">the icon rules</a> rather than anything here.
  </p>
  <?php
  docs_example(
      '<nav dir="rtl" class="sidebar card" aria-label="الأقسام" style="max-inline-size:16rem">' . "\n" .
      '  <a class="sidebar-link" href="#" aria-current="page" aria-label="نظرة عامة">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#home"></use></svg>' . "\n" .
      '    <span>نظرة عامة</span>' . "\n" .
      '  </a>' . "\n" .
      '  <a class="sidebar-link" href="#" aria-label="الوارد، ١٢ غير مقروءة">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#mail"></use></svg>' . "\n" .
      '    <span>الوارد</span>' . "\n" .
      '    <span class="badge push">١٢</span>' . "\n" .
      '  </a>' . "\n" .
      '</nav>',
      'The count moves to the left, which is the end of the line',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.sidebar-link</code> transitions its background and colour over
    <?= e(api_token('--dur-1')['value'] ?? '110ms') ?>, collapsed by the global reset under
    <code>prefers-reduced-motion: reduce</code>.
  </p>
  <p class="text-muted">
    The collapse to icons is not animated at all — <code>display: none</code> cannot be
    transitioned, and the width change belongs to whatever is driving your
    <code>.cq-shell</code>. If you animate that width, that is your motion to guard.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.sidebar</code> is in the "chrome that should never print" list in
    <code>src/99-print.css</code> and is dropped entirely. That is right for navigation: a
    printed page of destinations nobody can follow is wasted paper, and removing the rail
    lets the main column use the full width.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Give the rail a surface without wrapping it in a card */
  .sidebar {
    background: var(--surface-2);
    border-inline-end: 1px solid var(--line);
    block-size: 100%;
  }

  /* Bigger touch targets on a rail that is used on tablets */
  .sidebar-link { min-block-size: var(--tap); }

  /* Collapse earlier than 15rem */
  @container shell (max-width: 12rem) {
    .sidebar-link span { display: none; }
  }
}') ?></code></pre>
  <p class="text-muted">
    Widening the collapse threshold is additive — Deck's 15rem rule still applies below
    15rem. To collapse <em>later</em> you have to undo the original, which is a case for
    changing the container width instead.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for five destinations.</strong> A <a href="nav.php">nav bar</a> costs less
      space and less structure.
    </li>
    <li>
      <strong>Not without <code>.cq-shell</code> if you want it to collapse.</strong> The
      container query has nothing to match and fails silently.
    </li>
    <li>
      <strong>Not collapsed without labels.</strong> See
      <a href="#collapsed-names">the collapsed state</a> — the links lose their names.
    </li>
    <li>
      <strong>Not for navigation inside a page.</strong> Sections of the current document
      are <a href="tabs.php">tabs</a> or a table of contents.
    </li>
    <li>
      <strong>Not on a phone.</strong> A rail takes a third of a narrow screen. Put the same
      list in a <a href="drawer.php">drawer</a> and open it from the bar.
    </li>
    <li>
      <strong>Not for a list of records.</strong> Conversations, files and search results
      are a <a href="list.php">list</a> — they are content, not navigation, and they belong
      in the main column.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
