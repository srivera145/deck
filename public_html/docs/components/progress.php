<?php
declare(strict_types=1);

$page = [
    'path' => 'components/progress.php',
    'title' => 'Progress',
    'level' => 'Beginner',
    'description' => 'Deck styles the native <progress> element, plus .ring for a circular version and .spinner for work with no known end. Which one you use depends on whether you know how much is left.',
    'documents' => [
        'progress', 'ring', 'ring-wrap', 'ring-label', 'spinner', 'spinner-lg',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Progress</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Progress</h1>
  <p class="lede">
    Three ways to show that something is happening, and the choice between them comes
    down to one question: <strong>do you know how much is left?</strong> If you do, use a
    <code>&lt;progress&gt;</code> or a ring. If you do not, use a spinner — and do not
    fake a percentage.
  </p>
</header>

<section class="stack-3">
  <h2 id="progress">.progress — the native element</h2>
  <p>
    <code>.progress</code> goes on a real <code>&lt;progress&gt;</code>. The element
    carries the value, the maximum and the accessible role, and Deck restyles the two
    vendor pseudo-elements — the same two-engines problem as
    <a href="range.php#engines"><code>.range</code></a>, so the declarations are written
    twice.
  </p>
  <?php
  docs_example(
      '<div class="stack-3" style="max-inline-size:26rem">' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="dx-prog">Export</label>' . "\n" .
      '    <progress class="progress" id="dx-prog" value="68" max="100"></progress>' . "\n" .
      '    <p class="help">68% — 1,687 of 2,481 rows</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The element announces itself; the help text says what the number means',
      'stack'
  );
  ?>
  <p class="text-muted">
    A <code>&lt;progress&gt;</code> with no <code>value</code> is indeterminate and the
    browser animates it — which is the correct way to say "working, duration unknown"
    while keeping the element's semantics.
  </p>
</section>

<section class="stack-3">
  <h2 id="ring">.ring — circular</h2>
  <p>
    A conic gradient from <code>--value</code>, with a radial
    <code>mask</code> punching out the middle. Three properties shape it:
    <code>--value</code>, <code>--size</code> and <code>--thickness</code>. Use it where
    a bar would be too wide — inside a <a href="card.php">card</a>, beside a stat.
  </p>
  <?php
  docs_example(
      '<div class="ring" style="--value:68" role="img" aria-label="68 per cent complete"></div>' . "\n" .
      '<div class="ring" style="--value:24" role="img" aria-label="24 per cent complete"></div>' . "\n" .
      '<div class="ring" style="--value:92;--size:72px;--thickness:10px" role="img" aria-label="92 per cent complete"></div>',
      'role="img" with a label, because the shape carries the meaning',
      'cluster'
  );
  ?>
  <p>
    The mask is why a figure cannot go inside the ring. A mask applies to an element
    <em>and its whole subtree</em>, so a <code>&lt;span&gt;</code> centred in a
    <code>.ring</code> is punched out along with the middle — it renders, takes up space,
    and is completely invisible. <code>.ring-wrap</code> and <code>.ring-label</code> are
    the way round it: the wrapper is an unmasked <code>inline-grid</code> that stacks the
    label over the ring as a sibling.
  </p>
  <?php
  docs_example(
      '<span class="ring-wrap">' . "\n" .
      '  <span class="ring" style="--value:68"></span>' . "\n" .
      '  <span class="ring-label">68%</span>' . "\n" .
      '</span>' . "\n" .
      '<span class="ring-wrap">' . "\n" .
      '  <span class="ring" style="--value:24"></span>' . "\n" .
      '  <span class="ring-label">24%</span>' . "\n" .
      '</span>',
      'The label is a sibling of the ring, not a child',
      'cluster'
  );
  ?>
  <p class="dx-note text-muted">
    A ring is a <code>&lt;div&gt;</code>, not a <code>&lt;progress&gt;</code>. It has no
    role and no value unless you give it one — <code>role="img"</code> with
    <code>aria-label</code>, or <code>role="progressbar"</code> with
    <code>aria-valuenow</code> if the number will change. With
    <code>.ring-wrap</code> the visible label carries it instead, which is better still.
  </p>
  <p class="text-muted">
    <code>.ring</code> itself still sets <code>place-items: center</code>,
    <code>font-size</code>, <code>font-weight</code> and
    <code>font-variant-numeric</code> — four declarations that style a child the mask
    guarantees nobody will see. They are the fossil of the approach that does not work,
    and are recorded in <code>FINDINGS.md</code> for removal.
  </p>
</section>

<section class="stack-3">
  <h2 id="meter">A bar with no element</h2>
  <p>
    Deck ships no standalone meter. Where you need a bar drawn from a value rather than
    from a <code>&lt;progress&gt;</code> — a quota inside a table cell, a fill in a series
    colour — that is <a href="charts.php"><code>.chart-meter</code></a>, documented on the
    charts page.
  </p>
  <p class="text-muted">
    Prefer the element wherever it fits. A <code>&lt;div&gt;</code> that looks like
    progress is progress nobody can hear.
  </p>
</section>

<section class="stack-3">
  <h2 id="spinner">.spinner — no known end</h2>
  <p>
    A rotating border. <code>.spinner-lg</code> is the 34px version. Use it when the
    duration is genuinely unknown; a spinner that runs for forty seconds tells the reader
    nothing about whether to wait.
  </p>
  <?php
  docs_example(
      '<span class="cluster cluster-tight"><span class="spinner"></span> Submitting</span>' . "\n" .
      '<span class="cluster cluster-tight"><span class="spinner spinner-lg"></span> Rebuilding the index</span>',
      '',
      'cluster'
  );
  ?>
  <p class="dx-note text-muted">
    <code>.spinner</code> is one of seven things exempted from the reduced-motion reset in
    <code>src/02-reset.css</code>: instead of freezing, it slows to 2.4 seconds. The
    reasoning in the source is that a frozen spinner reads as a hung page — which is worse
    for everyone than a slow one.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.progress</code>, <code>.ring</code> and <code>.spinner</code> are three
    separate classes in <code>src/07-components.css</code> rather than one component
    root, so there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--ink-200', '--brand-600', '--r-full', '--text-xs', '--dur-3', '--ease-out']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The element gives you everything.</strong>
      <code>&lt;progress&gt;</code> is announced as a progress bar with its value, updates
      are conveyed, and an omitted <code>value</code> is announced as indeterminate. Every
      other option on this page has to have that added by hand.
    </li>
    <li>
      <strong>Label it.</strong> A <code>&lt;progress&gt;</code> needs a
      <code>&lt;label for&gt;</code> or <code>aria-label</code> like any other control —
      "68%" with no idea what is at 68% is not information.
    </li>
    <li>
      <strong>Show the number in text.</strong> The bar is a picture of a figure; the
      figure is what a reader acts on. Put it in a <code>.help</code> line, as the first
      example does.
    </li>
    <li>
      <strong>A spinner announces nothing.</strong> It is a styled empty element. Pair it
      with text — the examples all do — and put that text in an
      <code>aria-live</code> region if the state changes without a page load.
    </li>
    <li>
      <strong>Do not fake determinate progress.</strong> A bar that crawls to 90% and
      waits is a lie the reader learns to distrust. If you do not know, say so.
    </li>
    <li>
      <strong>Long operations need more than an indicator.</strong> Past a few seconds a
      reader wants to know what is happening and whether they can leave. That is text, not
      a bar.
    </li>
    <li>
      <strong>Colour alone.</strong> All four are a coloured fill against a track, with no
      pattern or label. Where the exact value matters — a quota near its limit — the
      number has to be written out.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    A <code>&lt;progress&gt;</code> fills from the right under <code>dir="rtl"</code> with
    no help from Deck — the engine mirrors its own pseudo-elements. A ring does not mirror,
    which is also right: a clock face reads the same way in every direction.
  </p>
  <p class="dx-note text-muted">
    <strong>A dead line in the source.</strong> <code>src/19-logical.css</code> lists
    <code>.progress::-webkit-progress-value</code> inside an <code>:is()</code> alongside
    the <a href="toast.php#rtl">toast timer</a> and the chart bars. A pseudo-element is not
    valid inside <code>:is()</code>, and <code>:is()</code> parses forgivingly, so the
    browser drops it and keeps the rest — read the rule back through
    <code>cssRules</code> and it serialises as
    <code>[dir="rtl"] :is(.chart-bar-fill, .toast-timer)</code>. Nothing breaks, because
    the fill is animated with <code>inline-size</code> and has no transform for
    <code>transform-origin</code> to act on. It is recorded in
    <code>FINDINGS.md</code> so it is removed rather than trusted.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="field" style="max-inline-size:24rem">' . "\n" .
      '  <label class="label" for="dx-prog-rtl">التصدير</label>' . "\n" .
      '  <progress class="progress" id="dx-prog-rtl" value="68" max="100"></progress>' . "\n" .
      '</div>',
      'Fills from the right',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.progress</code>'s value transitions over
    <?= e(api_token('--dur-3')['value'] ?? '320ms') ?> and the global reset collapses it.
    <code>.spinner</code> is exempt and keeps turning slowly. An indeterminate
    <code>&lt;progress&gt;</code> is animated by the browser and is outside Deck's reach —
    which is the right place for it, since the platform knows what its own reduced-motion
    setting should do.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    None of these are in <code>src/99-print.css</code>. A <code>&lt;progress&gt;</code>
    prints as whatever the browser draws for it; the ring is a background and the spinner
    is a border mid-rotation, so both print as an empty circle.
  </p>
  <p class="text-muted">
    That is another reason to write the number beside the bar: on paper the number is all
    that survives.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One bar, no layer needed */
<progress class="progress" style="block-size:14px" value="68" max="100"></progress>

@layer app.components {
  /* Both engines, or neither */
  .progress::-webkit-progress-value { background: var(--good-500); }
  .progress::-moz-progress-bar { background: var(--good-500); }
}') ?></code></pre>
  <p class="text-muted">
    As with <a href="range.php#overriding">the range slider</a>, a vendor pseudo-element
    in a shared selector invalidates the whole rule for the other engine. Write them
    separately.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not a spinner when you know the total.</strong> "Uploading 3 of 12" is
      better than a spinner in every respect.
    </li>
    <li>
      <strong>Not a bar when you do not.</strong> See above, in reverse.
    </li>
    <li>
      <strong>Not for a rating or a score.</strong> A progress bar implies something is
      under way. Use <a href="rating.php"><code>.rating</code></a>, or a
      <a href="charts.php#t-bars">chart bar</a>.
    </li>
    <li>
      <strong>Not inside a <a href="toast.php">toast</a> for a long job.</strong> The
      toast leaves. Put a bar in the page where the reader can find it again.
    </li>
    <li>
      <strong>Not a <code>&lt;div&gt;</code> where the element would work.</strong>
      <code>.ring</code> and <code>.chart-meter</code> exist for the cases where it would
      not; those cases are rarer than they look.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
