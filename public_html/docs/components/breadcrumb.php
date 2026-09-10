<?php
declare(strict_types=1);

$page = [
    'path' => 'components/breadcrumb.php',
    'title' => 'Breadcrumb',
    'level' => 'Beginner',
    'description' => 'Deck\'s .breadcrumb is a trail of ancestor links with a generated slash between them. The separator is CSS content, which is the one thing worth knowing about it.',
    'documents' => [
        'breadcrumb',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Breadcrumb</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Breadcrumb</h1>
  <p class="lede">
    Where the current page sits in the hierarchy, and a way back up it. Four rules and a
    generated separator — the smallest component in Deck, and one where the markup matters
    more than the styling.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    On a site with real depth, where a page has ancestors someone might want to go back to:
    documentation, a catalogue, a file browser. The trail answers "where am I" and "how do I
    go up one".
  </p>
  <p>
    On a flat site it is decoration — a breadcrumb with one ancestor tells the reader
    nothing they did not know from the nav bar.
  </p>
  <?php
  docs_example(
      '<nav aria-label="Breadcrumb">' . "\n" .
      '  <ol class="breadcrumb">' . "\n" .
      '    <li><a href="#">Docs</a></li>' . "\n" .
      '    <li><a href="#">Components</a></li>' . "\n" .
      '    <li aria-current="page">Breadcrumb</li>' . "\n" .
      '  </ol>' . "\n" .
      '</nav>',
      'The class goes on the list; the nav carries the label',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>&lt;ol&gt;</code>, because the order is the hierarchy. An
    <code>&lt;ol&gt;</code> with a class already has its markers and padding removed by
    <code>src/02-reset.css</code> — the <code>ul[class], ol[class]</code> rule — so no list
    reset is needed alongside <code>.breadcrumb</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="separator">The separator is generated content</h2>
  <p>
    The slash between items is not in your markup. It is a pseudo-element on every item
    after the first:
  </p>
  <pre class="dx-code"><code><?= e('.breadcrumb li + li::before {
  content: "/";
  color: var(--text-faint);
  margin-inline-end: var(--space-2);
}') ?></code></pre>
  <p>
    That is the right way to do it — a separator is presentation, and putting it in the HTML
    means it gets copied, translated and read aloud as content. Two things follow from
    generated content, though, and both are worth knowing:
  </p>
  <ul class="stack-2">
    <li>
      <strong>Some screen readers announce it.</strong> CSS <code>content</code> is exposed
      by most engines, so the trail may be read as "Docs slash Components slash Breadcrumb".
      That is tolerable — it is short and it does convey separation — but it is not silent,
      and you cannot <code>aria-hidden</code> a pseudo-element.
    </li>
    <li>
      <strong>It does not survive a copy-paste</strong> in most browsers, which is usually a
      benefit: the reader gets the words rather than the punctuation.
    </li>
  </ul>
  <p class="dx-note text-muted">
    <code>margin-inline-end</code> puts the gap after the slash, and
    <code>.breadcrumb</code>'s own <code>gap</code> supplies the space before it. Both are
    logical, so the spacing holds up in either direction.
  </p>
</section>

<section class="stack-3">
  <h2 id="current">The last item</h2>
  <p>
    The current page belongs in the trail — it is the "you are here" — but it should not be
    a link. <code>[aria-current]</code> gives it full-strength text and a heavier weight,
    and the bare attribute selector means any value matches.
  </p>
  <?php
  docs_example(
      '<div class="stack-3">' . "\n" .
      '  <nav aria-label="Breadcrumb">' . "\n" .
      '    <ol class="breadcrumb">' . "\n" .
      '      <li><a href="#">Catalogue</a></li>' . "\n" .
      '      <li><a href="#">Lighting</a></li>' . "\n" .
      '      <li><a href="#">Desk lamps</a></li>' . "\n" .
      '      <li aria-current="page">Anglepoise 1227</li>' . "\n" .
      '    </ol>' . "\n" .
      '  </nav>' . "\n" .
      '  <nav aria-label="Breadcrumb, truncated">' . "\n" .
      '    <ol class="breadcrumb">' . "\n" .
      '      <li><a href="#">Catalogue</a></li>' . "\n" .
      '      <li><span aria-hidden="true">…</span></li>' . "\n" .
      '      <li><a href="#">Desk lamps</a></li>' . "\n" .
      '      <li aria-current="page">Anglepoise 1227</li>' . "\n" .
      '    </ol>' . "\n" .
      '  </nav>' . "\n" .
      '</div>',
      'A full trail, and the same trail with the middle collapsed',
      'stack'
  );
  ?>
  <p class="text-muted">
    The container is <code>flex-wrap: wrap</code>, so a long trail wraps to a second line
    rather than overflowing. On a narrow screen that is usually preferable to truncation —
    but if the trail is deep, collapsing the middle as above keeps it to one line.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.breadcrumb</code> is a single class with no members — everything else is
    descendant and attribute selectors — so there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--space-2', '--text-sm', '--text', '--text-muted', '--text-faint', '--brand']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Wrap it in <code>&lt;nav aria-label="Breadcrumb"&gt;</code>.</strong>
      <code>.breadcrumb</code> is a class and confers no role. The label is what
      distinguishes it from the other navigation regions on the page.
    </li>
    <li>
      <strong>Use an ordered list.</strong> The hierarchy is an order. It also gives a
      screen reader a count, so the reader knows how deep they are before hearing every
      level.
    </li>
    <li>
      <strong>Mark the current page and do not link it.</strong>
      <code>aria-current="page"</code> on the last <code>&lt;li&gt;</code>. A link to the
      page you are on is a tab stop that goes nowhere.
    </li>
    <li>
      <strong>The separator may be announced.</strong> See
      <a href="#separator">above</a> — generated content is exposed by most engines. It
      cannot be hidden, and it is short enough not to be a problem, but it is worth knowing
      before you choose a more elaborate separator character.
    </li>
    <li>
      <strong>Hide a truncation ellipsis.</strong> <code>aria-hidden="true"</code> on the
      "…", and if the hidden levels are reachable some other way, make sure that way is
      obvious.
    </li>
    <li>
      <strong>Do not use it as the only way back.</strong> It is a supplement to the main
      navigation, not a replacement. A reader who arrives from a search engine may have no
      other route upward, and a trail of two items is not much of one.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The row is flex and every offset is logical, so the trail runs from the right and the
    separator's spacing follows. No rule in <code>src/19-logical.css</code> is needed.
  </p>
  <p>
    The slash is worth a thought: <code>/</code> is a bidirectionally neutral character, so
    it does not mirror and does not need to — it reads as a separator in either direction.
    A chevron would be a different story, and would need
    <a href="icon.php#rtl">an icon that flips</a> rather than a text glyph.
  </p>
  <?php
  docs_example(
      '<nav dir="rtl" aria-label="مسار التنقل">' . "\n" .
      '  <ol class="breadcrumb">' . "\n" .
      '    <li><a href="#">المستندات</a></li>' . "\n" .
      '    <li><a href="#">المكوّنات</a></li>' . "\n" .
      '    <li aria-current="page">مسار التنقل</li>' . "\n" .
      '  </ol>' . "\n" .
      '</nav>',
      'The trail starts on the right and the slashes space correctly',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing here animates. The hover colour change on a link is instant, and there are no
    transitions, transforms or keyframes in the component — so
    <code>prefers-reduced-motion</code> has nothing to act on.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.breadcrumb</code> is not in <code>src/99-print.css</code>, so it prints — unlike
    <a href="pagination.php#print">pagination</a> and the
    <a href="sidebar.php#print">sidebar</a>, which are both removed.
  </p>
  <p>
    That is arguably right: the trail is the document's position in a hierarchy, which is
    information about the page rather than a control. But note that the print stylesheet
    appends the target URL after every link whose <code>href</code> starts with
    <code>http</code>, so a printed breadcrumb of absolute links becomes a row of items each
    trailing a full address. Relative and in-page links are already exempt. If your trail
    uses absolute URLs, put <code>.no-print-url</code> on each
    <code>&lt;a&gt;</code> — the rule is <code>.no-print-url::after</code>, so it has to be
    on the link itself, not the list — or suppress them all at once:
  </p>
  <pre class="dx-code"><code><?= e('@media print {
  /* keep the words, drop the addresses */
  .breadcrumb a::after { content: none; }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A chevron instead of a slash */
  .breadcrumb li + li::before { content: "›"; }

  /* Truncate long titles rather than wrapping the trail */
  .breadcrumb li {
    max-inline-size: 14ch;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .breadcrumb li:last-child { max-inline-size: none; }
}') ?></code></pre>
  <p class="text-muted">
    If you truncate, leave the last item alone — the current page is the one the reader most
    needs to read in full.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not on a flat site.</strong> One ancestor is not a trail, and it competes with
      the <a href="nav.php">nav bar</a> for the same job.
    </li>
    <li>
      <strong>Not as a history of where the reader has been.</strong> A breadcrumb shows the
      site's hierarchy, not the path taken through it. Using it for the latter surprises
      people, because the back button already does that and does it correctly.
    </li>
    <li>
      <strong>Not as a substitute for a back button</strong> in a flow. In a checkout or a
      wizard, "back" means the previous step, which is a <a href="stepper.php">stepper</a>
      concern.
    </li>
    <li>
      <strong>Not with the current page as a link.</strong> See
      <a href="#current">The last item</a>.
    </li>
    <li>
      <strong>Not for more than about five levels.</strong> Past that the trail is longer
      than the content it labels. Collapse the middle.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
