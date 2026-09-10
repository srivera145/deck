<?php
declare(strict_types=1);

$page = [
    'path' => 'components/range.php',
    'title' => 'Range',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .range styles a real slider through the vendor track and thumb pseudo-elements, and .range-pair stacks two of them into a two-handle filter that deck-extras.js keeps in step, clamped and read out.',
    'documents' => [
        'range', 'range-pair', 'range-readout', 'range-ticks',
    ],

    'component' => 'range',
    'accounts' => [
        '06-forms.css'  => 'documented: the slider itself — the track and thumb pseudo-elements for both engines',
        '23-inputs.css' => 'documented: .range-pair, .range-readout and .range-ticks — the Two handles and Readout and ticks sections, including what src/js/deck-extras.js does to them at runtime',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Range</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Range</h1>
  <p class="lede">
    <code>.range</code> styles a real <code>&lt;input type="range"&gt;</code>. A slider
    is the one control CSS cannot style with ordinary properties: the track and the
    thumb are vendor pseudo-elements, and the two engines do not agree on their names or
    their box model. Deck writes both, and the differences between them are the reason
    this page exists.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a slider when the reader is choosing approximately — a price ceiling, a volume,
    an opacity — and the exact number matters less than the position. If the number
    matters, pair it with a readout, or use <code>.number</code> instead.
  </p>
  <p>
    The input is <code>block-size: var(--tap)</code> —
    <?= e(api_token('--tap')['value'] ?? '44px') ?> — with a 6px track drawn inside it,
    so the grab area is comfortable on a phone while the slider still looks thin.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:24rem">' . "\n" .
      '  <label class="label" for="ex-vol">Volume</label>' . "\n" .
      '  <input class="range" type="range" id="ex-vol" min="0" max="100" value="60">' . "\n" .
      '</div>',
      'A real range input, styled',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="engines">Two engines, two sets of rules</h2>
  <p>
    Every declaration is written twice:
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Vendor pseudo-elements for a range input</caption>
      <thead>
        <tr><th scope="col">Part</th><th scope="col">Chromium and WebKit</th><th scope="col">Firefox</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Part">Track</th>
          <td data-label="Chromium and WebKit"><code>::-webkit-slider-runnable-track</code></td>
          <td data-label="Firefox"><code>::-moz-range-track</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="Part">Thumb</th>
          <td data-label="Chromium and WebKit"><code>::-webkit-slider-thumb</code></td>
          <td data-label="Firefox"><code>::-moz-range-thumb</code></td>
        </tr>
      </tbody>
    </table>
  </div>
  <p>
    They cannot be combined into one selector: a rule with an unrecognised vendor
    pseudo-element is dropped entirely by the other engine, taking the valid half with
    it. That is why the source repeats itself here and nowhere else.
  </p>
  <p class="dx-note text-muted">
    The two also differ in box model. WebKit's thumb needs
    <code>margin-block-start: -8px</code> to centre on the track; Firefox centres it
    automatically, so the Firefox rule has no margin. A thumb that looks correct in one
    browser and sits low in the other is almost always this line.
  </p>
</section>

<section class="stack-3">
  <h2 id="pair">Two handles</h2>
  <p>
    <code>.range-pair</code> is the interesting one. It stacks two real
    <code>&lt;input type="range"&gt;</code> elements in the same grid cell, hides both
    tracks, and draws its own: a grey <code>::before</code> for the full width and a
    brand-coloured <code>::after</code> positioned from
    <code>--lo</code> and <code>--hi</code>.
  </p>
  <p>
    The inputs get <code>pointer-events: none</code> and only their thumbs get it back,
    so a click between the handles does not jump one of them. Both inputs stay real, so
    both submit with the form and both are reachable by keyboard.
  </p>
  <p>
    <code>src/js/deck-extras.js</code> wires the rest: on every
    <code>input</code> event it writes <code>--lo</code> and <code>--hi</code> as
    percentages, refuses to let the handles cross, fills any
    <code>.range-readout</code> beside the pair, and fires a
    <code>deck:change</code> event carrying <code>{ min, max }</code>. The CSS draws;
    the script keeps the drawing honest.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:26rem">' . "\n" .
      '  <span class="label" id="ex-price-label">Price range</span>' . "\n" .
      '  <div class="range-pair" data-gap="5" style="--lo:20;--hi:70">' . "\n" .
      '    <input type="range" min="0" max="100" value="20" data-prefix="$" aria-label="Minimum price">' . "\n" .
      '    <input type="range" min="0" max="100" value="70" data-prefix="$" aria-label="Maximum price">' . "\n" .
      '  </div>' . "\n" .
      '  <div class="range-readout"><span>$20</span><span>$70</span></div>' . "\n" .
      '</div>',
      'Drag either handle: the fill, the readout and both values keep up',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <code>--lo</code> and <code>--hi</code> are percentages of the track written as bare
    numbers. Setting them in the markup, as the example does, is only the starting
    position — from the first interaction onwards <code>deck-extras.js</code> owns them.
    If you are not loading Deck's JavaScript, the fill is whatever you last wrote and
    the two handles will drift out of step with it.
  </p>
  <p>
    <code>data-gap</code> on the pair is the minimum distance the script keeps between
    the handles. With <code>data-gap="10"</code> on a 0-100 scale the two can never come
    within ten of each other, and whichever handle the reader is holding is the one that
    gets pushed back — so dragging never yanks the handle out from under the pointer.
  </p>
</section>

<section class="stack-3">
  <h2 id="readout">Readout and ticks</h2>
  <p>
    <code>.range-readout</code> is a tabular-figure display for the current value.
    Beside a <code>.range-pair</code> it is filled in for you: the script writes the low
    value into its first child and the high value into its second, so it needs exactly
    two children and it has to be a sibling of the pair. <code>data-prefix</code> on
    either input is prepended — <code>data-prefix="$"</code> gives "$20" rather than
    "20".
  </p>
  <p>
    Beside a single <code>.range</code> it is styling only, and an
    <code>&lt;output&gt;</code> you update yourself.
    <code>.range-ticks</code> is a row of labels beneath the track and is never
    populated by anything.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:26rem">' . "\n" .
      '  <label class="label" for="ex-budget">Monthly budget</label>' . "\n" .
      '  <input class="range" type="range" id="ex-budget" min="0" max="500" step="50" value="250">' . "\n" .
      '  <output class="range-readout" for="ex-budget">$250</output>' . "\n" .
      '  <div class="range-ticks">' . "\n" .
      '    <span>$0</span>' . "\n" .
      '    <span>$250</span>' . "\n" .
      '    <span>$500</span>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The readout is an <output>, which is announced when it changes',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>&lt;output&gt;</code> is an implicit live region, so a screen reader announces
    the new value as the slider moves — without any <code>aria-live</code> of your own.
    That is why the example uses it rather than a <code>&lt;span&gt;</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from the stylesheet source by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--tap', '--ink-0', '--ink-200', '--brand-500', '--brand-600', '--r-full', '--shadow-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The input is real, so the keyboard works.</strong> Arrow keys step by
      <code>step</code>, Page Up and Page Down jump, Home and End go to the ends. None of
      that is Deck's doing and none of it can be lost by styling.
    </li>
    <li>
      <strong>Focus is the global ring.</strong> There is no
      <code>:focus-visible</code> rule on <code>.range</code> itself, so the browser's
      default focus indication applies to the input as a whole. <strong>The thumb does
      not get its own ring</strong>, which makes focus less obvious than on other Deck
      controls. Worth adding in <code>app.components</code> if sliders are important on
      your page.
    </li>
    <li>
      <strong>A slider needs a visible label and a visible value.</strong> The position
      of a handle is not readable to anyone who cannot see it precisely, and
      <code>aria-valuetext</code> is what makes "250" into "$250 per month". Use
      <code>&lt;output&gt;</code> so the value is announced as it changes.
    </li>
    <li>
      <strong><code>.range-pair</code> needs two labels.</strong> Each input is a
      separate control and each needs its own name — "Minimum price" and "Maximum
      price". A single label on the wrapper leaves both handles unnamed.
    </li>
    <li>
      <strong>The handles cannot cross, but only with the JavaScript loaded.</strong>
      <code>deck-extras.js</code> clamps them on every <code>input</code> event, which
      is early enough to stay ahead of the drag. Without the script there is nothing in
      the CSS to stop the maximum being dragged below the minimum, and the fill inverts.
    </li>
    <li>
      <strong>Two handles in one place are hard to separate.</strong> When both sit at
      the same value the reader cannot tell which one they are about to grab, and a
      screen-reader user has two identically-positioned controls. <code>data-gap</code>
      prevents the overlap; distinct <code>aria-label</code>s are what make them
      distinguishable.
    </li>
    <li>
      <strong>Touch target.</strong> The input is
      <?= e(api_token('--tap')['value'] ?? '44px') ?> tall and the thumb is 22px, so the
      grab area is the full height of the control rather than the size of the visible
      handle.
    </li>
    <li>
      <strong><code>.range-ticks</code> labels are decorative.</strong> They are not
      associated with the slider's values in any way a screen reader can use. They help
      a sighted reader read the scale and do nothing for anyone else.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The browser mirrors a range input under <code>dir="rtl"</code> on its own — the
    minimum moves to the right and arrow keys follow the writing direction — and Deck
    adds nothing, because the track and thumb rules are symmetrical.
  </p>
  <p>
    <code>.range-pair</code>'s fill uses <code>margin-inline-start</code>, so it is
    measured from the correct edge in both directions.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="field" style="max-inline-size:24rem">' . "\n" .
      '  <label class="label" for="ex-rtl-range">مستوى الصوت</label>' . "\n" .
      '  <input class="range" type="range" id="ex-rtl-range" min="0" max="100" value="60">' . "\n" .
      '</div>',
      'The browser mirrors it; Deck stays out of the way',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The only movement is <code>scale: 1.08</code> on the WebKit thumb while it is being
    dragged, which is a response to the reader's own gesture rather than an animation
    they did not ask for. It is not transitioned, so
    <code>prefers-reduced-motion</code> has nothing to collapse.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Deck's print stylesheet has no rule for <code>.range</code>. A slider prints as
    whatever the browser draws for it, which is usually the track and thumb without the
    brand colour. If a printed form needs the value, put it in a
    <code>&lt;output&gt;</code> beside the slider — the readout prints as text.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <p>
    Remember to write both engines. Overriding only the WebKit pseudo-element leaves
    Firefox on Deck's default, and the mismatch is easy to miss.
  </p>
  <pre class="dx-code"><code><?= e('@layer app.components {
  .range::-webkit-slider-runnable-track { block-size: 10px; }
  .range::-moz-range-track { block-size: 10px; }

  /* WebKit needs the offset recomputed when the track height changes */
  .range::-webkit-slider-thumb { margin-block-start: -6px; }

  /* A focus ring on the thumb, which Deck does not ship */
  .range:focus-visible::-webkit-slider-thumb { box-shadow: var(--ring); }
  .range:focus-visible::-moz-range-thumb { box-shadow: var(--ring); }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not when the exact value matters.</strong> Hitting 37 on a 0–100 slider is
      hard with a mouse and harder with a thumb. Use <code>.number</code>, or pair the
      slider with a number input that edits the same value.
    </li>
    <li>
      <strong>Not for a small set of choices.</strong> A slider with
      <code>step</code> and four positions is a <code>.segmented</code> control with
      extra imprecision.
    </li>
    <li>
      <strong>Not for an on/off setting.</strong> That is <code>.switch</code>.
    </li>
    <li>
      <strong>Not for a value with no natural range.</strong> A slider needs a floor and
      a ceiling that mean something to the reader. "Between 0 and 1,000,000" is not a
      scale anyone can aim at.
    </li>
    <li>
      <strong><code>.range-pair</code> is not a substitute for two fields.</strong> If
      the reader knows the numbers they want — a date range, a price they typed off an
      invoice — two inputs are faster and exact. The pair is for exploring, not for
      entering.
    </li>
    <li>
      <strong>Not without a visible value.</strong> A bare slider tells the reader
      nothing about what they have chosen. That is the most common slider defect and it
      is a markup problem, not a styling one.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
