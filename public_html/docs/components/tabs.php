<?php
declare(strict_types=1);

$page = [
    'path' => 'components/tabs.php',
    'title' => 'Tabs',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .tabs is a scrolling row of .tab triggers styled from aria-selected, so the accessible state and the visual state are the same attribute. The keyboard pattern is yours to add.',
    'documents' => [
        'tabs', 'tab',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Tabs</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Tabs</h1>
  <p class="lede">
    <code>.tabs</code> is the strip; <code>.tab</code> is one trigger in it. The selected
    tab is styled from <code>[aria-selected="true"]</code> — the attribute a screen reader
    reads is the same one that draws the underline, so the two cannot disagree.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use tabs when one region of a page shows one of several panels and the reader switches
    between them without leaving. If switching should change the URL, use links that look
    like tabs — <code>.tab</code> works on an <code>&lt;a&gt;</code>, and
    <code>aria-current="page"</code> is then the right attribute rather than
    <code>aria-selected</code>.
  </p>
  <?php
  docs_example(
      '<div class="tabs" role="tablist" aria-label="Export views">' . "\n" .
      '  <button class="tab" role="tab" aria-selected="true" aria-controls="dx-p1" id="dx-t1">Overview</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" aria-controls="dx-p2" id="dx-t2" tabindex="-1">History</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" aria-controls="dx-p3" id="dx-t3" tabindex="-1">Settings</button>' . "\n" .
      '</div>' . "\n" .
      '<div class="stack-3" style="padding-block-start:var(--space-4)">' . "\n" .
      '  <div role="tabpanel" id="dx-p1" aria-labelledby="dx-t1">' . "\n" .
      '    <p>2,481 rows, last run at 04:12 UTC.</p>' . "\n" .
      '  </div>' . "\n" .
      '  <div role="tabpanel" id="dx-p2" aria-labelledby="dx-t2" hidden>' . "\n" .
      '    <p>Fourteen runs in the last week.</p>' . "\n" .
      '  </div>' . "\n" .
      '  <div role="tabpanel" id="dx-p3" aria-labelledby="dx-t3" hidden>' . "\n" .
      '    <p>Schedule, format and destination.</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The full ARIA shape. Deck styles it; the switching needs your JavaScript.',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Nothing in that example switches panels — Deck ships no tab script. The markup is
    complete and correct, and clicking a tab does nothing until you wire it. That is the
    honest state of this component, and the
    <a href="#keyboard">keyboard section</a> explains what wiring it involves.
  </p>
</section>

<section class="stack-3">
  <h2 id="styling">How the selected tab is drawn</h2>
  <p>
    A two-pixel <code>border-block-end</code> in the brand colour, with
    <code>margin-block-end: -1px</code> so it sits over the strip's own one-pixel border
    rather than beside it. Unselected tabs carry the same border in
    <code>transparent</code>, so selecting one changes a colour and never moves anything.
  </p>
  <pre class="dx-code"><code><?= e('.tab { border-block-end: 2px solid transparent; margin-block-end: -1px; }
.tab[aria-selected="true"], .tab.is-active { color: var(--brand); border-block-end-color: var(--brand); }') ?></code></pre>
  <p class="text-muted">
    <code>.is-active</code> is accepted as an alternative for cases where the ARIA
    attribute is not appropriate — a row of links, for instance. Prefer
    <code>aria-selected</code> when the element really is a tab, because then the state
    exists for a screen reader as well as for the eye.
  </p>
</section>

<section class="stack-3">
  <h2 id="overflow">Overflowing</h2>
  <p>
    <code>.tabs</code> is <code>overflow-x: auto</code> with the scrollbar hidden and
    <code>white-space: nowrap</code> on each tab, so a strip with more tabs than fit
    scrolls sideways rather than wrapping. Tabs that wrap to a second line stop reading as
    a strip.
  </p>
  <?php
  docs_example(
      '<div class="tabs" role="tablist" aria-label="Many views">' . "\n" .
      '  <button class="tab" role="tab" aria-selected="true">Overview</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" tabindex="-1">History</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" tabindex="-1">Settings</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" tabindex="-1">Webhooks</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" tabindex="-1">Members</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" tabindex="-1">Audit log</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" tabindex="-1">Danger zone</button>' . "\n" .
      '</div>',
      'Narrow the window and drag the strip sideways',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    The hidden scrollbar has the same cost here as on
    <a href="scroller.php#accessibility"><code>.scroller</code></a>: the only cue that
    more tabs exist is a partially visible one at the edge. With the roving
    <code>tabindex</code> pattern below, keyboard focus scrolls the strip automatically —
    but a mouse user with no visible scrollbar may not realise there is more.
  </p>
</section>

<section class="stack-6">
  <h2 id="keyboard">The keyboard pattern, which is yours</h2>
  <p>
    A correct tablist is not just markup. The ARIA authoring practices ask for four things,
    and Deck's CSS provides none of them:
  </p>
  <ul class="stack-2">
    <li>
      <strong>One tab stop for the whole strip.</strong> The selected tab has
      <code>tabindex="0"</code>, the rest <code>tabindex="-1"</code> — the roving
      tabindex. Tab enters the strip and Tab leaves it; it does not walk through seven
      tabs.
    </li>
    <li>
      <strong>Arrow keys move between tabs.</strong> Left and right, or up and down for a
      vertical strip, wrapping at the ends.
    </li>
    <li>
      <strong>Home and End</strong> jump to the first and last.
    </li>
    <li>
      <strong>Selection follows focus</strong>, for panels that are cheap to render.
      Where a panel is expensive, selection happens on Enter or Space instead, and
      <code>aria-selected</code> moves separately from focus.
    </li>
  </ul>
  <pre class="dx-code"><code><?= e('const strip = document.querySelector(\'[role="tablist"]\');
const tabs = [...strip.querySelectorAll(\'[role="tab"]\')];

const select = (i) => {
  tabs.forEach((t, n) => {
    const on = n === i;
    t.setAttribute(\'aria-selected\', String(on));
    t.tabIndex = on ? 0 : -1;
    document.getElementById(t.getAttribute(\'aria-controls\')).hidden = !on;
  });
  tabs[i].focus();
};

strip.addEventListener(\'keydown\', (e) => {
  const i = tabs.indexOf(document.activeElement);
  if (i < 0) return;
  const rtl = getComputedStyle(strip).direction === \'rtl\';
  const prev = rtl ? \'ArrowRight\' : \'ArrowLeft\';
  const next = rtl ? \'ArrowLeft\' : \'ArrowRight\';
  if (e.key === prev) { e.preventDefault(); select((i - 1 + tabs.length) % tabs.length); }
  if (e.key === next) { e.preventDefault(); select((i + 1) % tabs.length); }
  if (e.key === \'Home\') { e.preventDefault(); select(0); }
  if (e.key === \'End\') { e.preventDefault(); select(tabs.length - 1); }
});

tabs.forEach((t, i) => t.addEventListener(\'click\', () => select(i)));') ?></code></pre>
  <p class="dx-note text-muted">
    Note the direction check. Arrow keys in a tablist follow the writing direction, so in
    an RTL document Left moves to the <em>next</em> tab. Getting that wrong is the most
    common bug in a hand-written tablist, and it is invisible until somebody tests in
    Arabic.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.tabs</code> and <code>.tab</code> are two separate classes rather than a
    component root and its member — the extractor sees no shared selector — so there is no
    completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--brand', '--line', '--text', '--text-muted', '--text-sm', '--tap', '--space-1', '--space-2', '--space-3', '--space-4', '--dur-1']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The visual state and the accessible state are one attribute.</strong>
      Styling from <code>[aria-selected="true"]</code> means a tab cannot look selected
      without being selected. That is the single best thing about this component.
    </li>
    <li>
      <strong>Deck ships no keyboard behaviour.</strong> Without the script above, every
      tab is a separate tab stop and arrow keys do nothing — which is a broken tablist,
      because the role promises behaviour the page does not deliver. <strong>If you use
      <code>role="tablist"</code>, you must add the keys.</strong>
    </li>
    <li>
      <strong>Or do not use the roles at all.</strong> A row of
      <code>&lt;a class="tab"&gt;</code> links with <code>aria-current="page"</code> is a
      perfectly good navigation pattern, needs no JavaScript, and promises nothing it does
      not do. For many "tabs" that is the better answer.
    </li>
    <li>
      <strong>Each panel needs <code>role="tabpanel"</code> and
      <code>aria-labelledby</code></strong> pointing at its tab, and each tab needs
      <code>aria-controls</code> pointing back. The examples here show both directions.
    </li>
    <li>
      <strong>Hide inactive panels with <code>hidden</code></strong>, not
      <code>opacity</code> or off-screen positioning — otherwise their content stays in
      the tab order and in the accessibility tree.
    </li>
    <li>
      <strong>Touch target.</strong> <code>min-block-size: var(--tap)</code> —
      <?= e(api_token('--tap')['value'] ?? '44px') ?> — so a tab is a comfortable target
      even though it is visually just text.
    </li>
    <li>
      <strong>The strip needs a label.</strong> <code>aria-label</code> on the
      <code>role="tablist"</code> element, so a reader with several tablists on a page can
      tell them apart.
    </li>
    <li>
      <strong>Colour is not the only cue</strong> — the selected tab gains an underline as
      well as the brand colour, so it is distinguishable without colour vision.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The strip is a flex row with a gap and logical padding, so it fills from the right
    under <code>dir="rtl"</code> and scrolls the other way, both handled by the browser.
    The underline is <code>border-block-end</code>, which is unaffected by direction.
  </p>
  <p class="text-muted">
    The one thing that is not automatic is the arrow-key direction in your script — see
    the note under <a href="#keyboard">the keyboard pattern</a>.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="tabs" role="tablist" aria-label="العروض">' . "\n" .
      '  <button class="tab" role="tab" aria-selected="true">نظرة عامة</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" tabindex="-1">السجل</button>' . "\n" .
      '  <button class="tab" role="tab" aria-selected="false" tabindex="-1">الإعدادات</button>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    A tab transitions its colour and border colour over
    <?= e(api_token('--dur-1')['value'] ?? '120ms') ?>; the global reset collapses both to
    <code>.01ms</code> under <code>prefers-reduced-motion: reduce</code>. There is no
    sliding indicator, which is a deliberate simplification — an underline that travels
    between tabs needs JavaScript to measure positions, and it is motion for its own sake.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> lists <code>.tabs</code> among the chrome that never
    prints. A tab strip is a control for choosing which panel to see, and on paper the
    choice has already been made — what prints is whichever panel was open.
  </p>
  <p class="dx-note text-muted">
    That means the printed page has no indication of <em>which</em> tab it was. If that
    matters, put the panel's name in a heading inside the panel as well as in the tab.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A vertical strip for a settings page */
  .tabs-vertical {
    flex-direction: column;
    border-block-end: 0;
    border-inline-end: 1px solid var(--line);
    overflow-x: visible;
  }
  .tabs-vertical .tab {
    border-block-end: 0;
    border-inline-end: 2px solid transparent;
    margin-block-end: 0;
    margin-inline-end: -1px;
  }
  .tabs-vertical .tab[aria-selected="true"] { border-inline-end-color: var(--brand); }
}') ?></code></pre>
  <p class="text-muted">
    A vertical tablist changes the arrow keys too — Up and Down rather than Left and
    Right — and needs <code>aria-orientation="vertical"</code> on the strip.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not without the keyboard script.</strong> <code>role="tablist"</code>
      without arrow keys is a promise the page breaks. Either add the behaviour or drop
      the roles and use links.
    </li>
    <li>
      <strong>Not for navigation between pages.</strong> Use <code>.nav-link</code>, or
      <code>.tab</code> on an <code>&lt;a&gt;</code> with
      <code>aria-current="page"</code> — but not the tablist roles, which describe
      in-page panels.
    </li>
    <li>
      <strong>Not for content the reader needs to compare.</strong> Tabs hide everything
      but one panel. If two values need to be seen together, use a
      <a href="grid.php"><code>.grid</code></a> or a
      <a href="table.php"><code>.table</code></a>.
    </li>
    <li>
      <strong>Not for a long list of sections.</strong> Seven tabs already scroll. Use an
      <a href="accordion.php"><code>.accordion</code></a>, which stacks and needs no
      keyboard work, or give each section a page.
    </li>
    <li>
      <strong>Not on a phone, for primary structure.</strong> A scrolling strip hides
      options off-screen. <code>.tabbar</code> at the bottom, or an accordion, both fit a
      small screen better.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
