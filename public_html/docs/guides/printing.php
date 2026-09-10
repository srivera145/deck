<?php
declare(strict_types=1);

/**
 * Printing.
 *
 * The framing here is invoices and reports, because that is who prints. The
 * page leads with what Deck already does, since the honest answer to "how do I
 * make this print" is usually "press print and see, it is probably fine".
 */

$page = [
    'path' => 'guides/printing.php',
    'title' => 'Make a page print properly',
    'level' => 'Intermediate',
    'description' => "Invoices, reports and receipts that come out of a printer looking right. What Deck already handles, and the five classes for the decisions only you can make.",
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Guides</li>
    <li aria-current="page">Make a page print properly</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Make a page print properly</h1>
  <p class="lede">
    Somebody needs a paper copy of an invoice, and the browser's print preview shows a
    dark background, a navigation bar, a cookie banner and a table cut in half across two
    pages. Deck ships 64 rules for exactly this, so most of that is already handled. This
    page is what it does for you and the five classes for the decisions it cannot make.
  </p>
</header>

<section class="stack-4">
  <h2 id="already">Try printing first</h2>
  <p>
    Press <kbd>Ctrl</kbd>+<kbd>P</kbd> on a Deck page before changing anything. Everything
    in this list already happened:
  </p>
  <ul class="stack-2">
    <li>
      <strong>The palette is forced to light.</strong> Dark mode is a screen idea. Paper is
      white, ink is black, and the whole token set is redeclared inside
      <code>@media print</code> so a dark page does not print as a black rectangle.
    </li>
    <li>
      <strong>Shadows, blurs, animations and transitions are gone.</strong> A shadow on
      paper is a grey smudge that costs toner.
    </li>
    <li>
      <strong>Chrome is removed.</strong> Nav bars, tab bars, drawers, mega menus, speed
      dials, banners and the back-to-top button are all hidden.
    </li>
    <li>
      <strong>Type is resized in points.</strong> Body text becomes 10.5pt and headings
      scale down with it, because a 16px heading is not a paper heading.
    </li>
    <li>
      <strong>Headings do not end a page.</strong> <code>break-after: avoid</code> on every
      heading level, and <code>break-inside: avoid</code> on paragraphs, list items,
      figures and stats.
    </li>
    <li>
      <strong>Table headers repeat.</strong> <code>thead</code> becomes
      <code>table-header-group</code>, so a long table carries its column names onto every
      page. Rows are kept whole.
    </li>
    <li>
      <strong>Link destinations are printed.</strong> An external link gets its URL in
      small grey text after it, because a printed link is otherwise a dead end.
    </li>
    <li>
      <strong>Deliberate fills survive.</strong> Badges, chips, avatars, icon tiles and
      chart series keep their colour through
      <code>print-color-adjust: exact</code> — a status badge that prints white is a status
      badge that means nothing.
    </li>
    <li>
      <strong>Mobile card fallbacks are switched off.</strong> A narrow print width would
      otherwise trigger the phone layout that restacks a table into cards, which is a
      screen behaviour and wrong on A4.
    </li>
  </ul>
</section>

<section class="stack-4">
  <h2 id="classes">The five classes</h2>
  <p>
    These are the choices Deck cannot guess: what is chrome on <em>your</em> page, and
    where a page break belongs in <em>your</em> document.
  </p>
  <?php docs_class_table(['no-print', 'print-only', 'page-break', 'keep-together', 'no-print-url']); ?>
  <dl class="stack-3">
    <dt><strong><code>.no-print</code></strong></dt>
    <dd>
      Anything on screen that is not on paper. Your own filter bar, a "back to list" link,
      a live chat widget, the action buttons above a report.
    </dd>
    <dt><strong><code>.print-only</code></strong></dt>
    <dd>
      The reverse, and the one people forget. A letterhead, a signature line, a page of
      terms, the "this is a copy" watermark — things that belong on the paper and would be
      clutter on the screen.
    </dd>
    <dt><strong><code>.page-break</code></strong></dt>
    <dd>
      Force a new sheet before this element. One per major section of a long report; not
      between every card.
    </dd>
    <dt><strong><code>.keep-together</code></strong></dt>
    <dd>
      Never split this across two pages. A signature block, an address, a total, a small
      table. Use it on things that are meaningless in halves.
    </dd>
    <dt><strong><code>.no-print-url</code></strong></dt>
    <dd>
      Suppress the printed URL after a link. For a link whose text already is the address,
      or a link into your own app that a reader cannot use from paper. Buttons and in-page
      anchors are already exempt.
    </dd>
  </dl>
  <pre class="dx-code"><code>&lt;div class="print-header print-only"&gt;
  &lt;img src="/letterhead.svg" alt="Ledgerly"&gt;
&lt;/div&gt;

&lt;div class="bar no-print"&gt;
  &lt;button class="btn"&gt;Export&lt;/button&gt;
  &lt;button class="btn btn-primary" onclick="print()"&gt;Print&lt;/button&gt;
&lt;/div&gt;

&lt;table class="table"&gt;…&lt;/table&gt;

&lt;div class="keep-together stack-2"&gt;
  &lt;p class="text-sm"&gt;Total due&lt;/p&gt;
  &lt;p class="h2"&gt;£4,281.16&lt;/p&gt;
&lt;/div&gt;

&lt;section class="page-break"&gt;
  &lt;h2&gt;Terms&lt;/h2&gt;
&lt;/section&gt;</code></pre>
  <p>
    <code>.print-header</code> and <code>.print-footer</code> exist too, for a block that
    should sit at the top or bottom of the printed document. Pair either with
    <code>.print-only</code> — on their own they are visible on screen as well.
  </p>
</section>

<section class="stack-4">
  <h2 id="margins">Page size and margins</h2>
  <p>
    Deck does not set <code>@page</code>, because the right margin depends on whether you
    are printing onto letterhead. It is one rule in your own layer:
  </p>
  <pre class="dx-code"><code>@layer app.base {
  @page {
    size: A4;
    margin: 18mm 16mm;
  }
  /* wider top margin on the first page, for a pre-printed letterhead */
  @page :first {
    margin-block-start: 45mm;
  }
}</code></pre>
  <p>
    <code>@page</code> takes <code>size</code>, <code>margin</code> and the
    <code>:first</code>, <code>:left</code> and <code>:right</code> selectors. It does not
    take arbitrary CSS, so a header on every page is not something you can do here —
    browsers put their own header and footer there, and the reader controls it in the print
    dialogue.
  </p>
  <p class="dx-note">
    Use physical units for print. <code>mm</code>, <code>cm</code> and <code>pt</code> mean
    something on paper; <code>rem</code> and <code>vw</code> do not, and
    <code>vh</code> is meaningless when the viewport is a sheet.
  </p>
</section>

<section class="stack-4">
  <h2 id="testing">Testing it</h2>
  <p>
    The print preview dialogue is the real test, but it is slow to iterate in. In Chrome
    dev tools you can hold the page in print mode while you edit: three-dot menu, More
    tools, Rendering, then <em>Emulate CSS media type: print</em>. Firefox has a printer
    icon in the Inspector's toolbar that does the same.
  </p>
  <ul class="stack-2">
    <li>Check page two. Most print bugs are on the second sheet: a repeated header that is not repeating, a table row split in half, a heading stranded at the bottom of page one.</li>
    <li>Print to PDF rather than paper while iterating, then check one real print before you ship. Printers disagree with PDF renderers about margins.</li>
    <li>Look at the printed URLs. If a table of internal links has turned into a wall of grey text, that is what <code>.no-print-url</code> is for.</li>
    <li>Try it in dark mode. The palette is forced light, and this is the quickest way to confirm nothing in your own CSS is fighting that.</li>
    <li>Count the pages. A report that was three sheets and is now nine usually has a <code>.page-break</code> on a repeated element.</li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="next">Next</h2>
  <p>
    <a href="../components/print.php">The printing component page</a> has the full class
    table and the complete list of what is hidden. If you are building a document that is
    mostly meant for paper, <a href="../components/table.php">table</a> and
    <a href="../components/datagrid.php">data grid</a> both document their own print
    behaviour, which is where the fiddly parts are.
  </p>
</section>

<?php docs_footer(); ?>
