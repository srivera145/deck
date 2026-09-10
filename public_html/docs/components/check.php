<?php
declare(strict_types=1);

$page = [
    'path' => 'components/check.php',
    'title' => 'Checkbox and radio',
    'level' => 'Beginner',
    'description' => 'Deck\'s .check styles a real checkbox or radio with appearance: none, draws the tick with clip-path so it needs no icon, handles the indeterminate state, and keeps the whole row a 44px label.',
    'documents' => [
        'check', 'check-card', 'check-note', 'check-text',
    ],

    'component' => 'check',
    'accounts' => [
        '06-forms.css'    => 'documented: the row, the box, the tick and dot, the indeterminate state, .check-text, .check-note and .check-card',
        '99-print.css'    => 'documented: boxes print as outlines and a ticked box prints filled — the Printing section',
        '12-datagrid.css' => 'documented: the data grid\'s row-selection cell reuses .check — the In a data grid section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Checkbox and radio</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Checkbox and radio</h1>
  <p class="lede">
    <code>.check</code> goes on a <code>&lt;label&gt;</code> that wraps a real
    <code>&lt;input type="checkbox"&gt;</code> or <code>&lt;input
    type="radio"&gt;</code>. The input keeps every behaviour the browser gives it —
    space to toggle, arrow keys to move within a radio group, form submission — and
    <code>appearance: none</code> only replaces how it looks.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Checkboxes for any number of independent choices; radios for exactly one of a small
    set. The row is <code>min-block-size: var(--tap)</code> —
    <?= e(api_token('--tap')['value'] ?? '44px') ?> — and the label wraps the input, so
    the whole line is the target rather than the 20px box.
  </p>
  <p>
    Wrapping rather than using <code>for</code>/<code>id</code> is deliberate: it cannot
    drift. A <code>for</code> that points at a missing <code>id</code> silently produces
    an unlabelled control, and nothing warns you.
  </p>
  <?php
  docs_example(
      '<label class="check">' . "\n" .
      '  <input type="checkbox" checked>' . "\n" .
      '  <span>Email me when an export finishes</span>' . "\n" .
      '</label>' . "\n" .
      '<label class="check">' . "\n" .
      '  <input type="checkbox">' . "\n" .
      '  <span>Email me about product news</span>' . "\n" .
      '</label>',
      'The label is the row; the whole row is clickable',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="drawn">How the tick is drawn</h2>
  <p>
    There is no icon and no background image. The <code>::before</code> is a solid block
    coloured by <code>box-shadow: inset 1em 1em var(--text-on-brand)</code> and then cut
    into a tick by a six-point <code>clip-path</code>. It scales from
    <code>0</code> to <code>1</code> on <code>:checked</code>, which is why the tick
    springs in rather than appearing.
  </p>
  <p>
    The radio uses the same trick with a circle instead of a polygon, and the
    indeterminate state reuses the checkbox's <code>::before</code> with a rectangular
    clip — one dash, no third asset.
  </p>
  <?php
  docs_example(
      '<label class="check">' . "\n" .
      '  <input type="checkbox">' . "\n" .
      '  <span>Unchecked</span>' . "\n" .
      '</label>' . "\n" .
      '<label class="check">' . "\n" .
      '  <input type="checkbox" checked>' . "\n" .
      '  <span>Checked</span>' . "\n" .
      '</label>' . "\n" .
      '<label class="check">' . "\n" .
      '  <input type="checkbox" disabled>' . "\n" .
      '  <span>Disabled</span>' . "\n" .
      '</label>',
      'Three states, no images',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="radio">Radios</h2>
  <p>
    Same class. The only difference in the stylesheet is
    <code>border-radius: var(--r-full)</code> on the box and a circle instead of a tick.
    Give every radio in a group the same <code>name</code> — that is what makes them
    mutually exclusive and what makes the arrow keys move between them.
  </p>
  <?php
  docs_example(
      '<label class="check">' . "\n" .
      '  <input type="radio" name="ex-plan" checked>' . "\n" .
      '  <span>Monthly</span>' . "\n" .
      '</label>' . "\n" .
      '<label class="check">' . "\n" .
      '  <input type="radio" name="ex-plan">' . "\n" .
      '  <span>Annual</span>' . "\n" .
      '</label>',
      'Arrow keys move between them because they share a name',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    A radio group needs a group label as well as per-option labels. Wrap it in a
    <code>&lt;fieldset class="fieldset"&gt;</code> with a
    <code>&lt;legend&gt;</code>, or the reader hears "Monthly, radio button" with no
    indication of what is being chosen.
  </p>
</section>

<section class="stack-3">
  <h2 id="indeterminate">Indeterminate</h2>
  <p>
    <code>:indeterminate</code> is a real CSS pseudo-class and Deck styles it: filled
    box, horizontal dash. It cannot be set in HTML — only
    <code>el.indeterminate = true</code> in JavaScript — which is why there is no
    attribute in the example below and why a "select all" checkbox needs a line of
    script to show a partial selection.
  </p>
  <pre class="dx-code"><code><?= e('<label class="check">
  <input type="checkbox" id="all">
  <span>Select all</span>
</label>

<script>
  document.getElementById(\'all\').indeterminate = true;
</script>') ?></code></pre>
  <p class="text-muted">
    Indeterminate is a display state only. The checkbox still submits as checked or
    unchecked, so the server never sees "partial".
  </p>
</section>

<section class="stack-3">
  <h2 id="text">Two-line labels</h2>
  <p>
    <code>.check-text</code> makes the text a column so a
    <code>.check-note</code> can sit under the label as a second, muted line. The row
    uses <code>align-items: flex-start</code> and the box gets
    <code>margin-block-start: 1px</code>, so the box aligns with the first line of text
    rather than centring against a two-line block.
  </p>
  <?php
  docs_example(
      '<label class="check">' . "\n" .
      '  <input type="checkbox" checked>' . "\n" .
      '  <span class="check-text">' . "\n" .
      '    <span>Nightly backups</span>' . "\n" .
      '    <span class="check-note">Kept for 30 days. Adds $4 per month.</span>' . "\n" .
      '  </span>' . "\n" .
      '</label>',
      'The box stays on the first line',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="card">Choice cards</h2>
  <p>
    <code>.check-card</code> adds a border, padding and a surface to the same label, and
    uses <code>:has(input:checked)</code> to light up the whole card when the control
    inside it is chosen. No JavaScript and no state class — the parent selector does it.
  </p>
  <?php
  docs_example(
      '<div class="grid grid-tight">' . "\n" .
      '  <label class="check check-card">' . "\n" .
      '    <input type="radio" name="ex-tier" checked>' . "\n" .
      '    <span class="check-text">' . "\n" .
      '      <span>Team</span>' . "\n" .
      '      <span class="check-note">Up to 20 people, shared billing.</span>' . "\n" .
      '    </span>' . "\n" .
      '  </label>' . "\n" .
      '  <label class="check check-card">' . "\n" .
      '    <input type="radio" name="ex-tier">' . "\n" .
      '    <span class="check-text">' . "\n" .
      '      <span>Enterprise</span>' . "\n" .
      '      <span class="check-note">SSO, audit log, and a contract.</span>' . "\n" .
      '    </span>' . "\n" .
      '  </label>' . "\n" .
      '</div>',
      'Select one: the card border follows',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>:has()</code> is in every browser Deck targets. In one that lacks it the card
    still works — it just does not highlight, and the radio inside it still shows which
    option is chosen.
  </p>
</section>

<section class="stack-3">
  <h2 id="switch">Switch</h2>
  <p>
    <code>.switch</code> is the same pattern — a <code>&lt;label&gt;</code> wrapping a
    real <code>&lt;input type="checkbox"&gt;</code> — drawn as a 46&times;28 track with a
    22px knob in an <code>::after</code>. The knob moves with
    <code>translate: 18px 0</code> on <code>:checked</code>, and its offset is
    <code>inset-inline-start</code>, so under <code>dir="rtl"</code> it starts on the
    right and travels the other way without a second rule.
  </p>
  <?php
  docs_example(
      '<label class="switch">' . "\n" .
      '  <input type="checkbox" checked>' . "\n" .
      '  <span>Two-factor authentication</span>' . "\n" .
      '</label>' . "\n" .
      '<label class="switch">' . "\n" .
      '  <input type="checkbox">' . "\n" .
      '  <span>Weekly digest</span>' . "\n" .
      '</label>',
      'A checkbox underneath; a switch on the surface',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    A switch is announced as a checkbox, because it is one. If it reads better as
    "on"/"off" than "checked"/"unchecked", add
    <code>role="switch"</code> to the input — the browser then exposes it as a switch and
    reports its state accordingly. Deck does not add the role for you, because a switch
    inside a form that is saved with a button really is a checkbox and should stay one.
  </p>
</section>

<section class="stack-3">
  <h2 id="datagrid">In a data grid</h2>
  <p>
    <code>src/12-datagrid.css</code> reuses <code>.check</code> for the row-selection
    column, stripping the row's <code>min-block-size</code> so a selection checkbox does
    not force every grid row to
    <?= e(api_token('--tap')['value'] ?? '44px') ?>. That is the one place Deck
    deliberately drops the touch target, and it is a trade the data grid makes
    knowingly: a dense grid of 34px rows cannot also have 44px checkboxes.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/06-forms.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--tap', '--surface', '--line', '--line-strong', '--brand-400', '--brand-500', '--brand-600', '--brand-soft', '--text-on-brand', '--focus', '--r-xs', '--r-full', '--r-md', '--text-sm', '--text-muted', '--space-2', '--space-3', '--space-4', '--dur-1', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The input is real.</strong> <code>appearance: none</code> changes painting
      only. Space toggles, arrows move within a radio group, the control is announced as
      a checkbox or radio button, and it submits with the form. Nothing here is a
      <code>&lt;div&gt;</code> with a click handler.
    </li>
    <li>
      <strong>Wrapping is the labelling.</strong> The <code>&lt;label
      class="check"&gt;</code> contains the input, so the association cannot break. The
      whole row is clickable as a consequence, not as a separate feature.
    </li>
    <li>
      <strong>Focus is a real outline.</strong>
      <code>:focus-visible</code> draws <code>2px solid var(--focus)</code> with a 2px
      offset on the box itself, so the ring is on the control rather than around the
      whole row.
    </li>
    <li>
      <strong>Touch target.</strong> The box is 20px; the row is
      <?= e(api_token('--tap')['value'] ?? '44px') ?>. Because the label wraps the
      input, the target is the row. The exception is inside a data grid — see
      <a href="#datagrid">In a data grid</a>.
    </li>
    <li>
      <strong>A radio group needs a <code>&lt;fieldset&gt;</code> and a
      <code>&lt;legend&gt;</code>.</strong> Without one the options are announced
      individually with no question attached. This is the most common radio defect and
      no class can fix it.
    </li>
    <li>
      <strong><code>.check-note</code> is not announced as part of the label.</strong>
      It is inside the <code>&lt;label&gt;</code>, so it <em>is</em> read as part of the
      accessible name — which makes for a long name like "Nightly backups Kept for 30
      days Adds $4 per month". That is usually acceptable and occasionally too much; move
      the note outside the label and use <code>aria-describedby</code> if it is.
    </li>
    <li>
      <strong>Indeterminate is announced as "mixed"</strong> by most screen readers, but
      only because the browser exposes the property. Setting a class instead of the
      property styles it without announcing it.
    </li>
    <li>
      <strong>Contrast.</strong> The unchecked box is
      <code>--line-strong</code> on <code>--surface</code>, clearing the 3:1 minimum for
      a control boundary. The tick is <code>--text-on-brand</code> on
      <code>--brand-600</code>, computed rather than hard-coded, so it survives a change
      of brand hue.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The row is a flex line with <code>gap</code>, so the box moves to the right of the
    text under <code>dir="rtl"</code> on its own. Nothing about the tick, the dot or the
    card is directional — a clip-path tick is symmetrical enough that mirroring it would
    be indistinguishable.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="stack-2">' . "\n" .
      '  <label class="check">' . "\n" .
      '    <input type="checkbox" checked>' . "\n" .
      '    <span>النسخ الاحتياطي الليلي</span>' . "\n" .
      '  </label>' . "\n" .
      '  <label class="check">' . "\n" .
      '    <input type="radio" name="ex-rtl-plan" checked>' . "\n" .
      '    <span>شهري</span>' . "\n" .
      '  </label>' . "\n" .
      '</div>',
      'The box follows the writing direction',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The tick and the dot scale in over
    <?= e(api_token('--dur-2')['value'] ?? '200ms') ?> with a spring easing, and the box
    transitions its background and border. Under
    <code>prefers-reduced-motion: reduce</code> the global reset collapses all of them to
    <code>.01ms</code>, so the tick appears instantly. It still appears — the state is
    never hidden from a reader who has asked for less motion, only the travel is removed.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> gives the box a visible outline on paper and fills a
    checked one, with <code>print-color-adjust: exact</code>. Without those three rules a
    printed form would show every option as an identical empty square, which is the worst
    possible outcome for a form somebody is meant to read back.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A larger box without touching the row */
  .check input[type="checkbox"], .check input[type="radio"] {
    inline-size: 24px;
    block-size: 24px;
  }

  /* A denser list of options */
  .check { min-block-size: 0; padding-block: var(--space-1); }
}') ?></code></pre>
  <p class="text-muted">
    Shrinking the row below <code>--tap</code> is the one override to think twice about:
    it is the whole reason a checkbox is comfortable on a phone.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong><code>.check</code> versus <code>.switch</code>.</strong> A checkbox
      collects a value that is submitted with a form. A switch turns something on or off
      immediately. If the reader has to press Save afterwards, it is a checkbox.
    </li>
    <li>
      <strong>Not for one-of-three shown inline.</strong> Two or three radios are fine;
      for a compact toggle between mutually exclusive views use
      <code>.segmented</code>, which reads as a control rather than as a question.
    </li>
    <li>
      <strong>Not for more than about seven options.</strong> A long radio list is a
      <code>.select</code>, or a <code>.combo</code> if it is long enough to need
      filtering.
    </li>
    <li>
      <strong>Not as a fake button.</strong> A <code>.check-card</code> with the input
      visually hidden and a click handler is a button that announces itself as a
      checkbox. If it performs an action, use <code>.btn</code>.
    </li>
    <li>
      <strong>Not for a filter chip row.</strong> Use <code>.chip</code>, which is sized
      for a horizontal row and has a removal affordance. A row of
      <code>.check</code> labels is
      <?= e(api_token('--tap')['value'] ?? '44px') ?> tall each and wraps badly.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
