<?php
declare(strict_types=1);

$page = [
    'path' => 'components/nav.php',
    'title' => 'Nav bar',
    'level' => 'Beginner',
    'description' => 'Deck\'s .navbar is a horizontal bar holding a brand and a row of .nav-link destinations. The link row is hidden below 48rem and Deck supplies no replacement — that is yours.',
    'documents' => [
        'navbar', 'navbar-brand', 'navbar-links', 'nav-link',
    ],

    'component' => 'navbar',
    'accounts' => [
        '07-components.css' => 'documented: the bar, the brand, the link row and the breakpoint that reveals it. .nav-link is defined in the same file and is covered here too',
        '99-print.css'      => 'documented: .navbar-links is hidden when printing — see Printing',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Nav bar</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Nav bar</h1>
  <p class="lede">
    A brand on one side, destinations on the other, a hairline underneath. It is four
    classes and no behaviour — which is deliberate, and which means the one thing this page
    has to be clear about is what happens on a narrow screen.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    For top-level navigation on a page with a handful of destinations. Above about six it
    stops fitting and starts needing a <a href="menu.php">menu</a>; below three it is
    probably just a brand.
  </p>
  <p>
    For navigation down the side of an application, use the
    <a href="sidebar.php">sidebar</a> instead. The two are not variants of each other — they
    have different classes and different collapse behaviour.
  </p>
  <?php
  docs_example(
      '<nav class="navbar" aria-label="Main">' . "\n" .
      '  <a class="navbar-brand" href="#">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#grid"></use></svg>' . "\n" .
      '    Deck' . "\n" .
      '  </a>' . "\n" .
      '  <span class="navbar-links">' . "\n" .
      '    <a class="nav-link" href="#" aria-current="page">Docs</a>' . "\n" .
      '    <a class="nav-link" href="#">Components</a>' . "\n" .
      '    <a class="nav-link" href="#">Tokens</a>' . "\n" .
      '  </span>' . "\n" .
      '  <span class="push"></span>' . "\n" .
      '  <button type="button" class="btn btn-sm">Sign in</button>' . "\n" .
      '</nav>',
      'Resize the frame below 768px and the link row disappears',
      'stack'
  );
  ?>
  <p class="text-muted">
    The bar is a plain flex row with a gap, so ordering and spacing are yours.
    <a href="cluster.php#push"><code>.push</code></a> is the usual way to shove everything
    after it to the far end.
  </p>
</section>

<section class="stack-3">
  <h2 id="mobile">What happens on a narrow screen</h2>
  <p>
    <code>.navbar-links</code> is <code>display: none</code> and only becomes a flex row at
    <code>48rem</code>. Below that the destinations are gone — not collapsed, not stacked,
    not behind a button. <strong>Deck does not supply the replacement.</strong>
  </p>
  <pre class="dx-code"><code><?= e('.navbar-links { display: none; gap: var(--space-1); align-items: center; }
@media (min-width: 48rem) { .navbar-links { display: flex; } }') ?></code></pre>
  <p>
    That is a reasonable division of labour — there are three or four good answers and the
    right one depends on the application — but it means a <code>.navbar</code> on its own is
    an unfinished component on a phone. The three answers Deck already has parts for:
  </p>
  <ul class="stack-2">
    <li>
      <strong>A <a href="drawer.php">drawer</a></strong> opened by a button in the bar. The
      usual choice for a site with more than a few destinations.
    </li>
    <li>
      <strong>A <a href="sheet.php">sheet</a></strong> from the bottom, which is easier to
      reach one-handed on a tall phone.
    </li>
    <li>
      <strong>A tab bar</strong> pinned to the bottom for an application with three to five
      top-level areas.
    </li>
  </ul>
  <?php
  docs_example(
      '<nav class="navbar" aria-label="Main">' . "\n" .
      '  <a class="navbar-brand" href="#">Deck</a>' . "\n" .
      '  <span class="navbar-links">' . "\n" .
      '    <a class="nav-link" href="#" aria-current="page">Docs</a>' . "\n" .
      '    <a class="nav-link" href="#">Components</a>' . "\n" .
      '  </span>' . "\n" .
      '  <span class="push"></span>' . "\n" .
      '  <button type="button" class="btn btn-icon btn-ghost md:hidden" aria-label="Open navigation"' . "\n" .
      '          popovertarget="dx-nav-menu">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#menu"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '  <div class="menu" id="dx-nav-menu" popover>' . "\n" .
      '    <a class="menu-item" href="#">Docs</a>' . "\n" .
      '    <a class="menu-item" href="#">Components</a>' . "\n" .
      '  </div>' . "\n" .
      '</nav>',
      'The simplest version: a button that opens a popover menu, hidden above the breakpoint',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Whatever you choose, the destinations must exist twice — once in
    <code>.navbar-links</code> for wide screens and once in the disclosure for narrow ones —
    or be moved between them. Two copies of the same list is a maintenance cost worth
    knowing about before you build it.
  </p>
</section>

<section class="stack-3">
  <h2 id="link">.nav-link, and when not to use a button</h2>
  <p>
    <code>.nav-link</code> is one destination. It is muted until hovered, and
    <code>[aria-current]</code> gives it the brand colour on a soft brand background — note
    the bare attribute selector, so <code>aria-current="page"</code>,
    <code>"true"</code> or any other value all match.
  </p>
  <p>
    The source carries an instruction worth repeating: <strong>use
    <a href="button.php"><code>.btn</code></a> only for actions, never for navigation.</strong>
    A link goes somewhere and can be opened in a new tab, copied, or followed with the
    keyboard as a link. A button does something. Styling one as the other takes that away
    from the reader for no gain.
  </p>
  <?php
  docs_example(
      '<span class="cluster cluster-tight">' . "\n" .
      '  <a class="nav-link" href="#" aria-current="page">Overview</a>' . "\n" .
      '  <a class="nav-link" href="#">Activity</a>' . "\n" .
      '  <a class="nav-link" href="#">Settings</a>' . "\n" .
      '</span>',
      'The current page is coloured and, more importantly, carries aria-current',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/07-components.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--control-h-lg', '--line', '--text-md', '--text-sm', '--text-muted', '--surface-hover', '--brand', '--brand-soft', '--r-sm', '--dur-1']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Put it in a <code>&lt;nav&gt;</code> with a label.</strong>
      <code>.navbar</code> is a class, not an element — it confers no role. A page with more
      than one navigation region needs each labelled, or they are announced as identical.
    </li>
    <li>
      <strong>Mark the current page.</strong> <code>aria-current="page"</code> is what tells
      a screen reader where the reader is. The colour is the sighted half of the same
      signal, and Deck keys its styling off the attribute so the two cannot drift apart.
    </li>
    <li>
      <strong>Below 48rem the links leave the accessibility tree.</strong>
      <code>display: none</code> removes them for everybody — this is not a visual-only
      hide. A screen reader user on a phone has no navigation at all unless you have built
      the disclosure described above. It is the most consequential thing on this page.
    </li>
    <li>
      <strong>The brand should be a link to the home page</strong> and should say so. A logo
      with no text is announced as nothing; give the <code>&lt;a&gt;</code> an accessible
      name even where the mark is enough for a sighted reader.
    </li>
    <li>
      <strong>Links are links.</strong> Do not attach click handlers to
      <code>&lt;span&gt;</code> elements and style them <code>.nav-link</code>. Keyboard
      users navigate by link, and a fake one is invisible to that.
    </li>
    <li>
      <strong>The bar does not stick by itself.</strong> If you make it
      <code>position: sticky</code>, remember it then covers content that anchors scroll to
      — add <code>scroll-margin-block-start</code> to your headings, or a skipped-to heading
      lands underneath the bar.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The bar is a flex row and the link padding is symmetrical, so the whole thing mirrors
    with the document — brand on the right, destinations running leftward — with nothing in
    <code>src/19-logical.css</code> required. The one thing to check is your own
    <a href="cluster.php#push"><code>.push</code></a>, which uses
    <code>margin-inline-start: auto</code> and so also follows direction correctly.
  </p>
  <?php
  docs_example(
      '<nav dir="rtl" class="navbar" aria-label="التنقل الرئيسي">' . "\n" .
      '  <a class="navbar-brand" href="#">ديك</a>' . "\n" .
      '  <span class="navbar-links">' . "\n" .
      '    <a class="nav-link" href="#" aria-current="page">المستندات</a>' . "\n" .
      '    <a class="nav-link" href="#">المكوّنات</a>' . "\n" .
      '  </span>' . "\n" .
      '</nav>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.nav-link</code> transitions its background and text colour over
    <?= e(api_token('--dur-1')['value'] ?? '110ms') ?> on hover, and the global reset in
    <code>src/02-reset.css</code> collapses that to <code>.01ms</code>. Nothing else on the
    bar moves. If you add a sticky bar that hides on scroll, that motion is yours to guard.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.navbar-links</code> is in the "chrome that should never print" list in
    <code>src/99-print.css</code>, so the destinations are dropped. The bar itself and the
    brand remain, which is the right split: the brand identifies the document, and a list of
    places you cannot click is noise.
  </p>
  <p class="text-muted">
    <code>.nav-link</code> is not in that list, so a <code>.nav-link</code> used outside
    <code>.navbar-links</code> will still print. If you have a secondary row of them, add it
    to your own print rule or give it <code>.no-print</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Show the links earlier than 48rem */
  @media (min-width: 40rem) { .navbar-links { display: flex; } }

  /* A sticky bar — remember the scroll-margin on your headings */
  .navbar {
    position: sticky;
    inset-block-start: 0;
    background: var(--surface);
    z-index: 10;
  }
  :target, h2 { scroll-margin-block-start: 4rem; }

  /* An underline for the current page instead of a filled pill */
  .nav-link[aria-current] {
    background: none;
    box-shadow: inset 0 -2px 0 var(--brand);
    border-radius: 0;
  }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not on a phone without a disclosure.</strong> The link row is hidden and
      nothing takes its place. This is the failure mode of this component.
    </li>
    <li>
      <strong>Not for more than about six destinations.</strong> They will not fit, and the
      row will not wrap gracefully. Group them behind a <a href="menu.php">menu</a>.
    </li>
    <li>
      <strong>Not for navigation within a page.</strong> Sections of the current document
      are <a href="tabs.php">tabs</a> or a table of contents, not top-level navigation.
    </li>
    <li>
      <strong>Not for an application shell.</strong> A left rail with grouped destinations
      is a <a href="sidebar.php">sidebar</a>; it collapses gracefully and this does not.
    </li>
    <li>
      <strong>Not with <code>.btn</code> for the destinations.</strong> See
      <a href="#link">.nav-link</a>. Buttons for actions, links for places.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
