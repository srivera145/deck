<?php
declare(strict_types=1);

$page = [
    'path' => 'components/footer.php',
    'title' => 'Footer',
    'level' => 'Beginner',
    'description' => 'Deck\'s .footer is a column grid of link lists over a bottom bar. Its links take the same colour as its body text with no underline, which is worth knowing before you put one in a sentence.',
    'documents' => [
        'footer', 'footer-bottom', 'footer-brand', 'footer-col', 'footer-grid',
        'footer-heading', 'footer-social',
    ],

    'component' => 'footer',
    'accounts' => [
        '22-nav.css' => 'documented: the region and its rule, the auto-fitting column grid, the brand block, the columns and their headings, the link treatment, the bottom bar and the social row',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Footer</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Footer</h1>
  <p class="lede">
    The bottom of a site: a few columns of links, a brand block, and a bar underneath for
    the copyright and the legal pages. Seven classes, no behaviour, and one decision about
    link styling that you should make deliberately rather than inherit.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    On a public site, where the footer is a real navigation surface — the place people go
    for the legal pages, the contact route, and the parts of the site the main nav does not
    have room for.
  </p>
  <p>
    In a logged-in application it is usually dead weight. A dashboard does not need a
    sitemap at the bottom of every screen, and the vertical space is better spent on the
    work.
  </p>
  <?php
  docs_example(
      '<footer class="footer">' . "\n" .
      '  <div class="footer-grid">' . "\n" .
      '    <div class="footer-brand">' . "\n" .
      '      <strong>Deck</strong>' . "\n" .
      '      <p>A single-stylesheet CSS framework with no build step.</p>' . "\n" .
      '    </div>' . "\n" .
      '    <nav class="footer-col" aria-labelledby="dx-f-prod">' . "\n" .
      '      <h2 class="footer-heading" id="dx-f-prod">Product</h2>' . "\n" .
      '      <a href="#">Components</a>' . "\n" .
      '      <a href="#">Tokens</a>' . "\n" .
      '      <a href="#">Changelog</a>' . "\n" .
      '    </nav>' . "\n" .
      '    <nav class="footer-col" aria-labelledby="dx-f-res">' . "\n" .
      '      <h2 class="footer-heading" id="dx-f-res">Resources</h2>' . "\n" .
      '      <a href="#">Documentation</a>' . "\n" .
      '      <a href="#">Examples</a>' . "\n" .
      '    </nav>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="footer-bottom">' . "\n" .
      '    <span>© 2026 Deck</span>' . "\n" .
      '    <a href="#">Privacy</a>' . "\n" .
      '    <a href="#">Terms</a>' . "\n" .
      '    <span class="footer-social">' . "\n" .
      '      <a href="#" aria-label="Deck on Mastodon">' . "\n" .
      '        <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#link"></use></svg>' . "\n" .
      '      </a>' . "\n" .
      '      <a href="#" aria-label="Deck on GitHub">' . "\n" .
      '        <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#external"></use></svg>' . "\n" .
      '      </a>' . "\n" .
      '    </span>' . "\n" .
      '  </div>' . "\n" .
      '</footer>',
      'Each column is its own labelled nav; the social row pushes itself to the end',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="grid">The column grid</h2>
  <p>
    <code>.footer-grid</code> is <code>repeat(auto-fit, minmax(min(11rem, 100%), 1fr))</code>
    — columns of at least 11rem, as many as fit, collapsing to one on a phone. There is no
    breakpoint and no column count to set: add a
    <code>.footer-col</code> and the grid rearranges.
  </p>
  <p>
    <code>.footer-brand</code> is capped at <code>26ch</code>, which is a measure rather
    than a width. A line of about 26 characters is short enough to read as a caption beside
    the link columns instead of competing with them.
  </p>
  <p class="dx-note text-muted">
    The <code>min(11rem, 100%)</code> is what stops the grid overflowing on a screen
    narrower than 11rem. It is the standard fix for <code>minmax</code> in an auto-fit
    track, and it is why this footer does not need a media query.
  </p>
</section>

<section class="stack-3">
  <h2 id="links">Footer links are not underlined, and take the body colour</h2>
  <p>
    This is the thing to decide about before you use the component. The footer sets its text
    to <code>--text-muted</code>, and then sets its links to the same:
  </p>
  <pre class="dx-code"><code><?= e('.footer { color: var(--text-muted); font-size: var(--text-sm); }
.footer a { color: var(--text-muted); text-decoration: none; }
.footer a:hover { color: var(--brand); text-decoration: underline; }') ?></code></pre>
  <p>
    In a column of links that is fine — everything in the list is a link, so there is
    nothing to distinguish it from. <strong>In a sentence it is a defect.</strong> A link
    inside a paragraph of footer text is the same colour and the same weight as the words
    around it, with no underline: there is nothing at all to mark it until the pointer
    reaches it, and a keyboard or touch user gets no hover.
  </p>
  <?php
  docs_example(
      '<footer class="footer">' . "\n" .
      '  <div class="footer-bottom">' . "\n" .
      '    <p>Built with Deck. Read the <a href="#">licence</a> before redistributing.</p>' . "\n" .
      '  </div>' . "\n" .
      '</footer>',
      'The word "licence" is a link. Nothing on the page says so.',
      'stack'
  );
  ?>
  <p>
    If your footer contains prose, restore the underline for links inside it. It is one
    rule, and it is the difference between a link people can find and one they cannot:
  </p>
  <pre class="dx-code"><code><?= e('@layer app.components {
  .footer p a { text-decoration: underline; }
}') ?></code></pre>
  <p class="text-muted">
    Recorded in <code>FINDINGS.md</code>. The column case is a legitimate use of the
    "links in a list need no underline" exception; the prose case is not covered by it.
  </p>
</section>

<section class="stack-3">
  <h2 id="bottom">The bottom bar</h2>
  <p>
    <code>.footer-bottom</code> is a wrapping flex row above a hairline: copyright, the legal
    links, and anything else short. <code>.footer-social</code> carries
    <code>margin-inline-start: auto</code>, so wherever you put it in the markup it pushes
    itself to the far end.
  </p>
  <p class="dx-note text-muted">
    <strong>Deck ships no brand marks.</strong> There is no Mastodon, GitHub or LinkedIn
    glyph in <code>deck-icons.svg</code> — 77 icons and not a logo among them, which is
    deliberate: brand marks have licence terms and change without warning. Supply your own
    <code>&lt;svg&gt;</code> for a social row; the examples here borrow
    <code>#link</code> and <code>#external</code> to show the layout.
  </p>
  <p class="text-muted">
    The social links are 34px squares. That is smaller than Deck's <code>--tap</code> target
    of <?= e(api_token('--tap')['value'] ?? '44px') ?> and they sit side by side with a
    <?= e(api_token('--space-1')['value'] ?? '4px') ?> gap — see
    <a href="#accessibility">Accessibility</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/22-nav.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--line', '--text-sm', '--text-xs', '--text', '--text-muted', '--brand', '--surface-hover', '--r-sm', '--space-8', '--space-10']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Use the <code>&lt;footer&gt;</code> element.</strong> At the top level of the
      document it is a <code>contentinfo</code> landmark, which is a region screen reader
      users navigate to directly. <code>.footer</code> is only a class and grants none of
      that.
    </li>
    <li>
      <strong><code>.footer-heading</code> is not a heading.</strong> It is a size, a weight
      and a colour on whatever element you choose. Use a real
      <code>&lt;h2&gt;</code> and point the column's <code>aria-labelledby</code> at it, as
      the first example does — otherwise the columns are three undifferentiated lists of
      links.
    </li>
    <li>
      <strong>Links in prose need an underline.</strong> See
      <a href="#links">above</a>. Colour alone is not enough, and here there is not even a
      colour difference.
    </li>
    <li>
      <strong>Social icons need names.</strong> An <code>&lt;a&gt;</code> containing only an
      <code>aria-hidden</code> icon has no accessible name. "Deck on GitHub" rather than
      "GitHub", so a list of links still makes sense out of context.
    </li>
    <li>
      <strong>The social targets are 34px.</strong> Below the 44px Deck uses elsewhere, and
      adjacent, so they are a hard row to hit on a touchscreen. Raise them for coarse
      pointers — see <a href="#overriding">Overriding it</a>.
    </li>
    <li>
      <strong>Footer text is small and muted by default.</strong>
      <code>--text-sm</code> at <code>--text-muted</code> is the lowest-contrast text on a
      typical page. Check it against your surface, and do not put anything important there
      that appears nowhere else.
    </li>
    <li>
      <strong>Order the markup the way it reads.</strong> The grid does not reorder
      anything, so DOM order is reading order and tab order. Keep it that way.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Everything mirrors on its own. The grid flows from the correct edge, the bottom bar's
    flex row reverses, and <code>.footer-social</code> uses
    <code>margin-inline-start: auto</code>, so it still ends up at the end of the line rather
    than pinned to a physical side. Nothing in <code>src/19-logical.css</code> is required.
  </p>
  <?php
  docs_example(
      '<footer dir="rtl" class="footer">' . "\n" .
      '  <div class="footer-grid">' . "\n" .
      '    <div class="footer-brand"><strong>ديك</strong><p>إطار عمل CSS بملف واحد.</p></div>' . "\n" .
      '    <nav class="footer-col" aria-labelledby="dx-f-rtl">' . "\n" .
      '      <h2 class="footer-heading" id="dx-f-rtl">المنتج</h2>' . "\n" .
      '      <a href="#">المكوّنات</a>' . "\n" .
      '      <a href="#">الرموز</a>' . "\n" .
      '    </nav>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="footer-bottom">' . "\n" .
      '    <span>© ٢٠٢٦ ديك</span>' . "\n" .
      '    <span class="footer-social">' . "\n" .
      '      <a href="#" aria-label="ديك على GitHub">' . "\n" .
      '        <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#external"></use></svg>' . "\n" .
      '      </a>' . "\n" .
      '    </span>' . "\n" .
      '  </div>' . "\n" .
      '</footer>',
      'The social row moves to the left, which is the end of the line',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing in the footer is transitioned or animated. The link hover and the social square's
    background change are both instant, so there is nothing for
    <code>prefers-reduced-motion</code> to switch off and no guard to add.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.footer</code> is not in <code>src/99-print.css</code>, so it prints in full. That
    is more consequential than it sounds: the print stylesheet appends the target address
    after every link whose <code>href</code> starts with <code>http</code>, so a footer of
    twenty absolute links prints twenty URLs, each on its own broken line.
  </p>
  <pre class="dx-code"><code><?= e('@media print {
  /* Usually right: the footer is navigation, and paper cannot follow it */
  .footer-grid, .footer-social { display: none; }

  /* Keep the copyright line, drop the addresses */
  .footer-bottom a::after { content: none; }
}') ?></code></pre>
  <p class="text-muted">
    The copyright and the licence line are usually worth keeping. The sitemap is not.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Underline links that sit in sentences */
  .footer p a { text-decoration: underline; }

  /* Full-size social targets on touch devices */
  @media (pointer: coarse) {
    .footer-social a { inline-size: var(--tap); block-size: var(--tap); }
  }

  /* A wider brand block */
  .footer-brand { max-inline-size: 40ch; }

  /* Narrower columns, so more of them fit */
  .footer-grid { grid-template-columns: repeat(auto-fit, minmax(min(8rem, 100%), 1fr)); }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not in an application.</strong> A dashboard does not need a sitemap under
      every screen. Put the legal links in a <a href="menu.php">menu</a> off the account
      control.
    </li>
    <li>
      <strong>Not for prose without restoring the underline.</strong> See
      <a href="#links">the link section</a> — this is the component's one real defect.
    </li>
    <li>
      <strong>Not as the only home for something important.</strong> Footer text is the
      smallest and lowest-contrast on the page, and it is the region people scroll past.
    </li>
    <li>
      <strong>Not with <code>.footer-heading</code> on a <code>&lt;div&gt;</code>.</strong>
      The columns need real headings to be navigable.
    </li>
    <li>
      <strong>Not for a single row of links.</strong> If all you have is a copyright and two
      legal pages, <code>.footer</code> with just a <code>.footer-bottom</code> is the whole
      component — the grid is optional and an empty one adds padding for nothing.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
