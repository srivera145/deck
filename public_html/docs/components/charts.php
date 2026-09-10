<?php
declare(strict_types=1);

$page = [
    'path' => 'components/charts.php',
    'title' => 'Charts',
    'level' => 'Advanced',
    'description' => 'Deck has no charting library. Bars, columns, donuts and heatmaps are plain CSS driven by a --value custom property; lines and areas are inline SVG you style with classes. Everything reads the brand palette.',
    'documents' => [
        'chart', 'chart-area', 'chart-area-g', 'chart-bar', 'chart-bar-fill',
        'chart-bar-label', 'chart-bar-track', 'chart-bar-value', 'chart-bars', 'chart-baseline',
        'chart-col', 'chart-col-stack', 'chart-columns', 'chart-dot', 'chart-dot-active',
        'chart-gridline', 'chart-group', 'chart-head', 'chart-heat', 'chart-legend',
        'chart-lg', 'chart-line', 'chart-line-dashed', 'chart-meter', 'chart-note',
        'chart-plot', 'chart-sm', 'chart-svg', 'chart-title', 'chart-x',
        'chart-y', 'donut', 'donut-center', 'donut-label', 'donut-value', 'donut-wrap',
        'sparkline', 'sparkline-bad', 'sparkline-good',
        's1', 's2', 's3', 's4', 's5', 's6', 's-good', 's-warn', 's-bad', 's-muted',
    ],

    'component' => 'chart',
    'accounts' => [
        '14-charts.css'    => 'documented: the frame, the palette, columns, bars, lines, areas, donuts, heatmaps, the legend and the series classes',
        '99-print.css'     => 'documented: chart fills are kept on paper — the Printing section',
        '19-logical.css'   => 'documented: bar fills grow from the starting edge under dir="rtl" — the Right to left section',
        '20-gradients.css' => 'documented: .chart-area-g fills an area with a gradient — the Lines and areas section',
        '26-perf.css'      => 'internal: .chart is given contain: layout style, so a chart relaying out cannot reflow the page',
    ],
];

require __DIR__ . '/../_layout.php';

function demo_columns(): string
{
    $data = [['Jan', 42], ['Feb', 58], ['Mar', 68], ['Apr', 51], ['May', 74], ['Jun', 63]];
    $out = '';
    foreach ($data as [$label, $v]) {
        $out .= '    <div class="chart-col" style="--value:' . $v . '" data-label="' . $label . '" data-value="' . $v . '"></div>' . "\n";
    }
    return $out;
}
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Charts</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Charts</h1>
  <p class="lede">
    There is no charting library. Bars, columns, donuts and heatmaps are plain CSS driven
    by a <code>--value</code> custom property; lines and areas are inline SVG you write
    and Deck styles. Everything reads the brand palette, so a chart rethemes with the rest
    of the application rather than carrying its own colours.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use these for the charts an application actually needs: a bar per month, a donut of
    three segments, a sparkline in a table cell. They are markup you render on the server,
    which means no client-side data layer, no library to load, and a chart that appears
    with the page rather than after it.
  </p>
  <p>
    For interactive analysis — zooming, brushing, tooltips on a thousand points, axes
    computed from data — use a real charting library. That is a different job, and Deck
    does not pretend to do it.
  </p>
</section>

<section class="stack-3">
  <h2 id="value">--value is the whole idea</h2>
  <p>
    A column's height is <code>calc(var(--value) * 1%)</code>. A bar's width is the same
    on the inline axis. A donut segment and a heatmap cell read the same property. So
    rendering a chart is writing numbers into style attributes, which any template
    language does:
  </p>
  <pre class="dx-code"><code><?= e('<div class="chart-col" style="--value:68" data-label="Mar" data-value="68"></div>') ?></code></pre>
  <p>
    <code>data-label</code> and <code>data-value</code> are drawn as
    <code>::after</code> and <code>::before</code> content, so the axis label and the
    value on top of the bar come from attributes rather than from extra elements.
  </p>
  <?php
  docs_example(
      '<figure class="chart">' . "\n" .
      '  <figcaption class="chart-head">' . "\n" .
      '    <span class="chart-title">Exports per month</span>' . "\n" .
      '    <span class="chart-note">2026</span>' . "\n" .
      '  </figcaption>' . "\n" .
      '  <div class="chart-columns">' . "\n" . demo_columns() .
      '  </div>' . "\n" .
      '</figure>',
      'Six numbers in six style attributes',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="palette">The palette</h2>
  <p>
    <code>.chart</code> declares six series colours as custom properties, and five of them
    are computed from the brand hue:
  </p>
  <pre class="dx-code"><code><?= e('--c1: var(--brand-500);
--c2: oklch(64% .13 calc(var(--hue-brand) + 48));
--c3: oklch(70% .14 calc(var(--hue-brand) + 96));
--c4: oklch(62% .13 calc(var(--hue-brand) - 52));
--c5: oklch(74% .12 calc(var(--hue-brand) + 148));
--c6: oklch(56% .10 calc(var(--hue-brand) + 200));') ?></code></pre>
  <p>
    Rotating the hue and holding lightness and chroma roughly steady is what makes six
    colours that are distinguishable from each other <em>and</em> obviously related. Change
    <code>--hue-brand</code> and the whole palette rotates with it — the charts stay in the
    same family as the buttons.
  </p>
  <p>
    <code>.s1</code> through <code>.s6</code> set <code>--series</code> to one of them, and
    <code>.s-good</code>, <code>.s-warn</code>, <code>.s-bad</code> and
    <code>.s-muted</code> set it to a status colour. Put the class on a mark and its fill,
    its stroke and its legend swatch all follow.
  </p>
  <?php
  docs_example(
      '<figure class="chart chart-sm">' . "\n" .
      '  <div class="chart-legend">' . "\n" .
      '    <span class="s1">Shipped</span>' . "\n" .
      '    <span class="s2">Pending</span>' . "\n" .
      '    <span class="s-bad">Failed</span>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="chart-columns">' . "\n" .
      '    <div class="chart-col s1" style="--value:72" data-label="Jan"></div>' . "\n" .
      '    <div class="chart-col s2" style="--value:48" data-label="Feb"></div>' . "\n" .
      '    <div class="chart-col s-bad" style="--value:22" data-label="Mar"></div>' . "\n" .
      '  </div>' . "\n" .
      '</figure>',
      'One class per series, on the mark and the legend alike',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="contrast">Text on a series colour</h2>
  <p>
    A value printed inside a bar has to be readable against whatever colour that series
    is, and the series colours are chosen for discriminability rather than for contrast.
    Where the browser supports it, Deck uses <code>contrast-color()</code>:
  </p>
  <pre class="dx-code"><code><?= e('@supports (color: contrast-color(red)) {
  .chart-col, .chart-bar-fill, .chart-meter > span {
    color: contrast-color(var(--series, var(--c1)));
  }
}') ?></code></pre>
  <p class="dx-note text-muted">
    The source comment is unusually candid about the limits, and it is worth repeating:
    some series colours sit in the 55–65% lightness band where neither black nor white
    clears AA comfortably, so this <strong>raises the floor rather than guaranteeing
    it</strong>. If a value must be readable, put it outside the bar with
    <code>.chart-bar-value</code> rather than on top of it.
  </p>
</section>

<section class="stack-6">
  <h2 id="types">The chart types</h2>

  <div class="stack-2">
    <h3 id="t-bars">Horizontal bars</h3>
    <p>
      <code>.chart-bars</code> holds rows of <code>.chart-bar</code>, each a
      <code>.chart-bar-label</code>, a <code>.chart-bar-track</code> containing a
      <code>.chart-bar-fill</code>, and a <code>.chart-bar-value</code>. Better than
      columns when the labels are words rather than dates.
    </p>
    <?php
    docs_example(
        '<figure class="chart">' . "\n" .
        '  <div class="chart-bars">' . "\n" .
        '    <div class="chart-bar">' . "\n" .
        '      <span class="chart-bar-label">Chrome</span>' . "\n" .
        '      <span class="chart-bar-track"><span class="chart-bar-fill s1" style="--value:64"></span></span>' . "\n" .
        '      <span class="chart-bar-value">64%</span>' . "\n" .
        '    </div>' . "\n" .
        '    <div class="chart-bar">' . "\n" .
        '      <span class="chart-bar-label">Safari</span>' . "\n" .
        '      <span class="chart-bar-track"><span class="chart-bar-fill s2" style="--value:21"></span></span>' . "\n" .
        '      <span class="chart-bar-value">21%</span>' . "\n" .
        '    </div>' . "\n" .
        '    <div class="chart-bar">' . "\n" .
        '      <span class="chart-bar-label">Firefox</span>' . "\n" .
        '      <span class="chart-bar-track"><span class="chart-bar-fill s3" style="--value:9"></span></span>' . "\n" .
        '      <span class="chart-bar-value">9%</span>' . "\n" .
        '    </div>' . "\n" .
        '  </div>' . "\n" .
        '</figure>',
        'The value sits outside the bar, where it is always readable',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="t-donut">Donut</h3>
    <p>
      <code>.donut</code> is a conic gradient driven by <code>--value</code>, with
      <code>.donut-center</code> holding a figure in the hole. One element, no SVG.
    </p>
    <?php
    docs_example(
        '<div class="donut-wrap">' . "\n" .
        '  <div class="donut" style="--value:68" role="img" aria-label="68 per cent of quota used">' . "\n" .
        '    <div class="donut-center">' . "\n" .
        '      <span class="donut-value">68%</span>' . "\n" .
        '      <span class="donut-label">of quota</span>' . "\n" .
        '    </div>' . "\n" .
        '  </div>' . "\n" .
        '</div>',
        'role="img" with a label, because the shape carries the meaning',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="t-heat">Heatmap</h3>
    <p>
      <code>.chart-heat</code> is a grid of <code>--cols</code> columns whose cells mix the
      series colour with the sunken background by <code>--value</code> — so a value of 0
      is the background and 100 is the full colour, with everything between interpolated
      in <code>oklab</code>.
    </p>
    <?php
    docs_example(
        '<div class="chart-heat" style="--cols:7;max-inline-size:16rem">' . "\n" .
        '  <span style="--value:10"></span><span style="--value:35"></span><span style="--value:80"></span>' . "\n" .
        '  <span style="--value:55"></span><span style="--value:5"></span><span style="--value:95"></span>' . "\n" .
        '  <span style="--value:45"></span><span style="--value:70"></span><span style="--value:20"></span>' . "\n" .
        '  <span style="--value:60"></span><span style="--value:88"></span><span style="--value:30"></span>' . "\n" .
        '  <span style="--value:15"></span><span style="--value:50"></span>' . "\n" .
        '</div>',
        '',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="t-svg">Lines and areas</h3>
    <p>
      These are the one place you write SVG. <code>.chart-svg</code> is the element,
      <code>.chart-line</code> the stroke, <code>.chart-area</code> the fill beneath it,
      <code>.chart-dot</code> a point and <code>.chart-gridline</code> the grid. All take
      <code>--series</code>, so <code>.s2</code> on a path colours it like everything
      else.
    </p>
    <p class="text-muted">
      <code>.chart-area-g</code> from <code>src/20-gradients.css</code> fills the area with
      a fading gradient rather than a flat tint, which reads better under a line.
      <code>.chart-line-dashed</code> is the convention for a forecast or a target.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="t-spark">Sparklines and meters</h3>
    <p>
      <code>.sparkline</code> is a tiny inline chart for a table cell or a stat, with
      <code>.sparkline-good</code> and <code>.sparkline-bad</code> for direction.
      <code>.chart-meter</code> is a single horizontal bar — a quota, a score — and is the
      simplest thing in the file.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/14-charts.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    The six series colours and <code>--chart-h</code> are local to <code>.chart</code>
    rather than global tokens, because they are a chart's palette rather than the
    application's. <code>--hue-brand</code> is what they are all computed from.
  </p>
  <?php docs_token_table(['--hue-brand', '--brand-500', '--good-500', '--warn-500', '--bad-500', '--line', '--bg-sunken', '--text-muted', '--text-xs', '--space-2', '--space-3']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>A chart made of <code>&lt;div&gt;</code>s is invisible.</strong> None of
      these classes carry semantics. A screen-reader user gets nothing at all unless you
      supply it — this is the single most important thing on the page.
    </li>
    <li>
      <strong>The best answer is usually a table.</strong> Put the figures in a
      <code>&lt;table class="sr-only"&gt;</code> beside the chart, or make the chart the
      visual layer over a real one. Then the data is available, sortable and printable, and
      the chart is decoration on top.
    </li>
    <li>
      <strong>The next best is <code>role="img"</code> with a summary.</strong> A donut
      showing one number is well served by
      <code>aria-label="68 per cent of quota used"</code>, as in the example. A six-series
      line chart is not — no label summarises it usefully.
    </li>
    <li>
      <strong>Never rely on colour alone to distinguish series.</strong> Six hues at
      similar lightness are hard to tell apart for a reader with colour vision deficiency —
      and the palette is deliberately hue-rotated, which is precisely the axis that is
      hardest to distinguish. Label the marks directly, or use
      <code>.chart-line-dashed</code> and other pattern differences.
    </li>
    <li>
      <strong>Values printed inside bars may not clear AA.</strong> See
      <a href="#contrast">above</a> — <code>contrast-color()</code> improves it and does
      not guarantee it. <code>.chart-bar-value</code> outside the bar always does.
    </li>
    <li>
      <strong><code>data-label</code> and <code>data-value</code> are generated
      content.</strong> Like every <code>::before</code> text in Deck, they are not
      reliably announced. They are for sighted readers; the accessible version has to exist
      separately.
    </li>
    <li>
      <strong>The legend can be interactive.</strong>
      <code>.chart-legend [aria-pressed="false"]</code> fades a toggled-off series, so a
      legend of real <code>&lt;button&gt;</code>s gets a working state for free — with the
      accessible state driving the styling, as elsewhere in Deck.
    </li>
    <li>
      <strong>Hover-only detail is lost on touch and keyboard.</strong>
      <code>.chart-col:hover</code> brightens and <code>.chart-dot-active</code> marks a
      point; neither is reachable without a pointer.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Bars grow from the starting edge, which means <code>transform-origin</code> has to be
    flipped for RTL — and <code>transform-origin</code> has no logical form.
    <code>src/19-logical.css</code> handles it in one rule shared with the toast timer and
    the progress fill:
  </p>
  <pre class="dx-code"><code><?= e('[dir="rtl"] :is(.chart-bar-fill, .progress::-webkit-progress-value, .toast-timer) {
  transform-origin: right center;
}') ?></code></pre>
  <p class="text-muted">
    Columns grow on the block axis and are unaffected. A donut's conic gradient does not
    mirror either, which is correct — a clock face reads the same way in every writing
    direction.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Bars, columns, lines, areas and donuts all animate in when they first render. Under
    <code>prefers-reduced-motion: reduce</code>, <code>src/14-charts.css</code> sets
    <code>animation: none</code> on all of them explicitly rather than relying on the
    global reset:
  </p>
  <pre class="dx-code"><code><?= e('.chart-col, .chart-col-stack, .chart-bar-fill, .chart-line, .chart-area, .donut { animation: none; }') ?></code></pre>
  <p>
    That is the right treatment. The global reset shortens animations to
    <code>.01ms</code>, which for a chart that animates <em>from zero</em> would risk a
    frame showing an empty chart. Setting <code>none</code> means the chart is simply drawn
    at its final values.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Ten rules. Chart fills are in the <code>print-color-adjust: exact</code> list —
    <code>.chart-col</code>, <code>.chart-col-stack &gt; span</code>,
    <code>.chart-bar-fill</code>, <code>.chart-meter &gt; span</code>,
    <code>.donut</code> and <code>.chart-heat &gt; *</code> — because a chart whose
    backgrounds are stripped is a set of empty rectangles.
  </p>
  <p class="text-muted">
    This is one of the places print support genuinely pays: reports go to PDF, and a
    printed dashboard with no bars in it is worse than no dashboard.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One chart, no layer needed */
<figure class="chart" style="--chart-h:180px;--c1:var(--good-500)">

@layer app.components {
  /* A categorical palette that is not hue-rotated from the brand */
  .chart-categorical {
    --c1: #4e79a7; --c2: #f28e2b; --c3: #e15759;
    --c4: #76b7b2; --c5: #59a14f; --c6: #edc948;
  }

  /* Thicker lines */
  .chart-line { stroke-width: 3; }
}') ?></code></pre>
  <p class="text-muted">
    A hue-rotated palette is coherent and hard to tell apart; a hand-picked categorical one
    is the opposite. If a chart has more than three series and the reader must distinguish
    them, the second is the better trade.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not without an accessible alternative.</strong> A table beside the chart, or
      <code>role="img"</code> with a real summary. Without one the chart does not exist for
      a portion of your readers.
    </li>
    <li>
      <strong>Not for interactive analysis.</strong> Zoom, brush, crosshairs, tooltips on
      a thousand points — use a charting library. These classes render a picture of data
      you already decided to show.
    </li>
    <li>
      <strong>Not for many points.</strong> One element per column means a hundred columns
      is a hundred elements. Past a few dozen, use SVG with a single
      <code>&lt;path&gt;</code>.
    </li>
    <li>
      <strong>Not for two numbers.</strong> A donut showing 68% is a picture of a figure
      that would be clearer written down. Charts earn their space by showing shape or
      comparison.
    </li>
    <li>
      <strong>Not with six series and colour as the only distinction.</strong> The palette
      is hue-rotated, and hue is the axis colour-blind readers lose. Label the marks
      directly.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
