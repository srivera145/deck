<?php
declare(strict_types=1);

$page = [
    'path' => 'components/select.php',
    'title' => 'Select',
    'level' => 'Beginner',
    'description' => 'Deck\'s .select draws its chevron with two CSS gradients rather than a background image, so it follows currentColor into dark mode — and needs one hand-written rule to mirror under dir="rtl".',
    'documents' => [
        'select',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Select</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Select</h1>
  <p class="lede">
    <code>.select</code> shares the control rule with
    <a href="input.php"><code>.input</code></a> and
    <a href="textarea.php"><code>.textarea</code></a>, then replaces the browser's
    dropdown arrow with one of its own. The replacement is drawn with two CSS gradients
    rather than an image, which means it takes <code>currentColor</code> and follows the
    text into dark mode without a second asset or a second rule.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Put it on a real <code>&lt;select&gt;</code>, wrapped in a
    <a href="field.php"><code>.field</code></a> with a label. Use it for one choice from
    a short, known list where the reader does not need to see all the options at once —
    a country, a currency, a status.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:22rem">' . "\n" .
      '  <label class="label" for="sel-plan">Plan</label>' . "\n" .
      '  <select class="select" id="sel-plan">' . "\n" .
      '    <option>Starter</option>' . "\n" .
      '    <option>Team</option>' . "\n" .
      '    <option>Enterprise</option>' . "\n" .
      '  </select>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="chevron">The chevron</h2>
  <pre class="dx-code"><code><?= e('appearance: none;
padding-inline-end: var(--space-10);
background-image: linear-gradient(45deg, transparent 50%, currentColor 50%),
                  linear-gradient(135deg, currentColor 50%, transparent 50%);
background-position: calc(100% - 19px) calc(50% + 1px), calc(100% - 14px) calc(50% + 1px);
background-size: 5px 5px, 5px 5px;') ?></code></pre>
  <p>
    Two 5&times;5 squares, each half-filled on a diagonal, placed side by side so the
    filled halves form a chevron. It is a trick, and it earns its place for three
    reasons:
  </p>
  <ul class="stack-2">
    <li>
      <strong>No request.</strong> No SVG file, no data URI, nothing to 404 and nothing
      to add to the byte count beyond the declarations themselves.
    </li>
    <li>
      <strong><code>currentColor</code>.</strong> The arrow is the text colour, so it
      changes with the theme, with a disabled state, and with any colour you set on the
      control — automatically. A background image would need a second copy for dark mode.
    </li>
    <li>
      <strong>It scales with the reader.</strong> The <code>5px</code> squares are fixed,
      but the padding that makes room for them comes from
      <code>--space-10</code>, so the arrow stays clear of the text at any font size.
    </li>
  </ul>
  <p class="dx-note text-muted">
    <code>appearance: none</code> removes the platform control's own arrow. Without it
    you get two. It also removes the platform's focus ring, which is why the shared rule
    supplies <code>--ring</code> — see <a href="input.php#states">input states</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="states">States</h2>
  <p>
    All from the shared rule: hover darkens the border, focus swaps it for
    <code>--focus</code> and adds the ring, disabled sinks the background and drops the
    shadow. <code>cursor: pointer</code> is the one addition — a select is a control you
    click rather than one you type into.
  </p>
  <?php
  docs_example(
      '<div class="stack-3" style="max-inline-size:22rem">' . "\n" .
      '  <select class="select" aria-label="Ordinary"><option>Ordinary</option></select>' . "\n" .
      '  <select class="select" disabled aria-label="Disabled"><option>Disabled</option></select>' . "\n" .
      '</div>',
      'The chevron greys out with the text, because it is currentColor',
      'stack'
  );
  ?>
  <p class="text-muted">
    There is no <code>readonly</code> for a <code>&lt;select&gt;</code> — the attribute
    does not exist on it. If a value must be shown but not changed, use
    <code>disabled</code> and submit the value in a hidden input, or render it as text.
  </p>
</section>

<section class="stack-3">
  <h2 id="multiple">Multiple and size</h2>
  <p>
    <code>&lt;select multiple&gt;</code> and <code>size="n"</code> render as a list box
    rather than a dropdown. Deck's rule still applies its border and focus ring, but the
    chevron is drawn over the top-right of the list, where there is no dropdown to point
    at.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:22rem">' . "\n" .
      '  <label class="label" for="sel-multi">Regions</label>' . "\n" .
      '  <select class="select" id="sel-multi" multiple size="4">' . "\n" .
      '    <option>Europe</option>' . "\n" .
      '    <option>North America</option>' . "\n" .
      '    <option>Asia Pacific</option>' . "\n" .
      '    <option>Latin America</option>' . "\n" .
      '  </select>' . "\n" .
      '</div>',
      'The chevron is still painted, which is wrong for a list box',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    That stray chevron is a small defect, recorded in <code>FINDINGS.md</code>. One rule
    fixes it: <code>.select[multiple], .select[size]:not([size="1"]) { background-image:
    none; padding-inline-end: var(--space-3); }</code>. Multi-selects are also hard to
    operate — a reader has to know to hold a modifier key — so a group of
    <a href="check.php"><code>.check</code></a> boxes is usually the better control
    anyway.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.select</code> is declared alongside <code>.input</code> and
    <code>.textarea</code> in one shared rule, so the extractor does not treat it as a
    component root and there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--control-h', '--surface', '--bg-sunken', '--line-strong', '--ink-400', '--focus', '--ring', '--r-sm', '--shadow-1', '--text-base', '--text-faint', '--space-3', '--space-10', '--dur-1']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The element is real, so everything works.</strong>
      <code>appearance: none</code> changes painting only. The control is announced as a
      combo box, the arrow keys move through options, typing jumps to a match, and the
      native option list opens in the platform's own accessible widget.
    </li>
    <li>
      <strong>The chevron is decorative and unannounced.</strong> It is a
      <code>background-image</code>, so it is not in the accessibility tree and cannot be
      selected. That is correct — the element already tells a screen reader what it is.
    </li>
    <li>
      <strong>The option list is the platform's, and that is a feature.</strong> A
      custom-drawn dropdown has to reimplement typeahead, focus management and
      touch behaviour, and usually gets one of them wrong. Deck styles the closed control
      and leaves the open list alone.
    </li>
    <li>
      <strong>Options cannot be styled, and should not need to be.</strong>
      <code>&lt;option&gt;</code> styling is inconsistent across platforms and ignored
      on most. If the options need icons or two lines each, you need
      <code>.combo</code>, not a select.
    </li>
    <li>
      <strong>A select needs a label, not just a first option.</strong>
      <code>&lt;option&gt;Choose a plan&lt;/option&gt;</code> as a placeholder is not a
      label: it disappears on selection, and a screen reader announces it as a value.
    </li>
    <li>
      <strong>16px on touch, as with every control.</strong> Below that, iOS Safari
      zooms the page when the control takes focus.
    </li>
    <li>
      <strong>Contrast.</strong> The chevron is <code>currentColor</code>, so it inherits
      whatever contrast the text has — which means it is compliant by construction rather
      than by a separate check.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    This is the one place in Deck's forms where logical properties are not enough.
    <code>background-position</code> has no logical equivalent — there is no
    <code>background-position-inline-start</code> — so the chevron cannot mirror itself.
    <code>src/19-logical.css</code> redeclares it:
  </p>
  <pre class="dx-code"><code><?= e('[dir="rtl"] .select {
  background-position: 19px calc(50% + 1px), 14px calc(50% + 1px);
  background-image: linear-gradient(135deg, transparent 50%, currentColor 50%),
                    linear-gradient(45deg, currentColor 50%, transparent 50%);
  padding-inline: var(--space-10) var(--space-3);
}') ?></code></pre>
  <p>
    Note that the two gradient angles are swapped as well as the positions.
    <code>45deg</code> and <code>135deg</code> exchanged is what reflects the chevron;
    moving it without swapping them would put a correctly-positioned arrow pointing the
    wrong way.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="field" style="max-inline-size:22rem">' . "\n" .
      '  <label class="label" for="sel-rtl">الخطة</label>' . "\n" .
      '  <select class="select" id="sel-rtl">' . "\n" .
      '    <option>أساسية</option>' . "\n" .
      '    <option>فريق</option>' . "\n" .
      '  </select>' . "\n" .
      '</div>',
      'Arrow on the left, still pointing down, padding on the correct side',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The border, shadow and background transition over
    <?= e(api_token('--dur-1')['value'] ?? '120ms') ?>; the global reset collapses them
    to <code>.01ms</code> under <code>prefers-reduced-motion: reduce</code>. The option
    list's own opening animation belongs to the platform and is outside Deck's reach —
    which is another argument for not reimplementing it.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> gives the control a plain <code>#999</code> border on
    white, and then removes the chevron specifically:
  </p>
  <pre class="dx-code"><code><?= e('.select { background-image: none !important; padding-inline-end: var(--space-3) !important; }') ?></code></pre>
  <p>
    Both halves again. On paper there is no dropdown to open, so an arrow pointing at
    nothing is noise — and once it is gone, the padding that made room for it is a gap
    that makes the value look oddly indented, so that comes off too.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A larger chevron */
  .select {
    background-size: 7px 7px, 7px 7px;
    background-position: calc(100% - 22px) calc(50% + 1px),
                         calc(100% - 15px) calc(50% + 1px);
  }

  /* No chevron at all, for a select that is styled as plain text */
  .select-bare {
    background-image: none;
    padding-inline-end: var(--space-3);
    border-color: transparent;
    box-shadow: none;
  }
}') ?></code></pre>
  <p class="dx-note text-muted">
    If you move the chevron, remember the <code>[dir="rtl"]</code> rule exists and will
    now disagree with yours. That is the cost of a property with no logical form, and it
    is the reason this page spends a section on it.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a long list.</strong> A select with two hundred options is
      unusable — no filtering, no search, a scroll list the reader has to read. Use
      <code>.combo</code>, which filters as they type.
    </li>
    <li>
      <strong>Not for two or three options.</strong> A select hides the choices behind a
      click. <code>.segmented</code> or a group of
      <a href="check.php"><code>.check</code></a> radios shows them all immediately, and
      a reader can compare them without opening anything.
    </li>
    <li>
      <strong>Not for multiple selection.</strong> <code>&lt;select multiple&gt;</code>
      requires a modifier key most people do not know about, and on touch it is worse.
      Use checkboxes, or <code>.combo</code> with multiple selection.
    </li>
    <li>
      <strong>Not for a boolean.</strong> Yes/No in a dropdown is two clicks for
      something <a href="switch.php"><code>.switch</code></a> or a single checkbox does
      in one.
    </li>
    <li>
      <strong>Not when the options need formatting.</strong> Icons, two-line entries,
      grouped headings with descriptions — <code>&lt;option&gt;</code> cannot carry any
      of it reliably. That is what <code>.combo</code> is for.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
