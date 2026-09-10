<?php
declare(strict_types=1);

$page = [
    'path' => 'components/textarea.php',
    'title' => 'Textarea',
    'level' => 'Beginner',
    'description' => 'Deck\'s .textarea shares the control rule with .input and adds three declarations: field-sizing: content so it grows as you type, a two-row floor and a 22rem ceiling.',
    'documents' => [
        'textarea',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Textarea</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Textarea</h1>
  <p class="lede">
    <code>.textarea</code> is declared in the same rule as
    <a href="input.php"><code>.input</code></a> and <a href="select.php"><code>.select</code></a>
    — same border, radius, shadow, focus ring and 16px-on-touch — and then adds three
    declarations of its own. The interesting one is
    <code>field-sizing: content</code>, which makes it grow as the reader types, with no
    JavaScript.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Put it on a real <code>&lt;textarea&gt;</code>, wrapped in a
    <a href="field.php"><code>.field</code></a> with a label. Use it whenever the answer
    might run to more than one line: a note, a description, a message, an address.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:26rem">' . "\n" .
      '  <label class="label" for="ta-note">Note</label>' . "\n" .
      '  <textarea class="textarea" id="ta-note" placeholder="Type a few lines and watch it grow"></textarea>' . "\n" .
      '  <p class="help">Visible to everyone on the project.</p>' . "\n" .
      '</div>',
      'Type into it — the box grows to fit',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="sizing">The three declarations</h2>

  <div class="stack-2">
    <h3 id="s-field-sizing">field-sizing: content</h3>
    <p>
      The browser sizes the control to its content instead of to the
      <code>rows</code> attribute. Every implementation of "auto-growing textarea" before
      this was JavaScript measuring a hidden clone on every keystroke; this is one
      declaration and it costs nothing.
    </p>
    <p class="text-muted">
      It is not in every browser yet. Where it is missing the textarea is simply its
      minimum height and scrolls — the ordinary behaviour. The feature degrades to the
      default rather than breaking, which is why Deck ships it unguarded rather than
      behind an <code>@supports</code>.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="s-floor">A two-row floor</h3>
    <p>
      <code>min-block-size: calc(var(--control-h) * 2)</code> — twice the height of an
      input. An empty auto-sizing textarea would otherwise be one row tall and
      indistinguishable from a text input, which is the one thing a textarea has to
      communicate before it is used.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="s-ceiling">A 22rem ceiling</h3>
    <p>
      <code>max-block-size: 22rem</code>. Without it, a textarea with a thousand words in
      it grows to a thousand words tall and the reader loses both the rest of the form
      and the submit button. At the ceiling it starts scrolling, which is the ordinary
      behaviour again.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="s-resize">Vertical resize stays available</h3>
    <p>
      <code>resize: vertical</code> rather than <code>none</code> or <code>both</code>.
      Vertical because a reader who wants more room should have it — the ceiling is
      Deck's guess, not theirs. Not horizontal, because a textarea wider than its
      <a href="field.php"><code>.field</code></a> breaks the form layout around it.
    </p>
    <?php
    docs_example(
        '<div class="field" style="max-inline-size:26rem">' . "\n" .
        '  <label class="label" for="ta-long">Release notes</label>' . "\n" .
        '  <textarea class="textarea" id="ta-long">Deck 0.1.0

The first tagged build. One stylesheet, no build step, no runtime dependencies.

Paste enough text in here and the box stops growing at 22rem and starts scrolling. Drag the corner if you want more room than that.</textarea>' . "\n" .
        '</div>',
        'Past the ceiling it scrolls; the corner still resizes',
        'stack'
    );
    ?>
  </div>
</section>

<section class="stack-3">
  <h2 id="states">States</h2>
  <p>
    Every state comes from the shared rule, so a textarea hovers, focuses, disables and
    validates exactly as an input does. <code>:user-invalid</code> rather than
    <code>:invalid</code> means an empty required textarea is not marked wrong before the
    reader has touched it — the reasoning is on the
    <a href="input.php#validation">input page</a>.
  </p>
  <?php
  docs_example(
      '<div class="stack-3" style="max-inline-size:26rem">' . "\n" .
      '  <textarea class="textarea" aria-label="Ordinary">Ordinary</textarea>' . "\n" .
      '  <textarea class="textarea" readonly aria-label="Readonly">Readonly</textarea>' . "\n" .
      '  <textarea class="textarea" disabled aria-label="Disabled">Disabled</textarea>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="counter">Counting characters</h2>
  <p>
    Deck ships no character counter for <code>.textarea</code>. The
    <code>.editor</code> component has one — <code>.editor-count</code>, with an
    <code>.is-over</code> state driven by <code>data-limit</code> — but that is a rich
    text editor rather than a plain control.
  </p>
  <p>
    For a plain textarea, an <code>&lt;output&gt;</code> beside it is the accessible
    version, because <code>&lt;output&gt;</code> is an implicit live region and the
    remaining count is announced as it changes.
  </p>
  <pre class="dx-code"><code><?= e('<div class="field">
  <label class="label" for="bio">Bio</label>
  <textarea class="textarea" id="bio" maxlength="280"></textarea>
  <output class="help" for="bio">280 left</output>
</div>') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.textarea</code> is declared alongside <code>.input</code> and
    <code>.select</code> in one shared rule, so the extractor does not treat it as a
    component root and there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    <code>--control-h</code> is doing double duty here: the same token that sets an
    input's height sets a textarea's minimum, so the two line up in a form.
  </p>
  <?php docs_token_table(['--control-h', '--surface', '--surface-2', '--bg-sunken', '--line-strong', '--focus', '--ring', '--r-sm', '--shadow-1', '--text-base', '--text-faint', '--bad-500', '--space-2', '--space-3', '--dur-1']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>It needs a label like any other control.</strong> A
      <code>&lt;label class="label" for="id"&gt;</code> is the right answer;
      <code>aria-label</code> is the fallback the short examples on this page use.
    </li>
    <li>
      <strong>Growing as you type is announced as nothing</strong>, which is correct — it
      is a visual convenience. But it does move everything below the textarea down the
      page while the reader is typing, and for a reader using a screen magnifier that
      can be disorienting. The <?= e('22rem') ?> ceiling bounds how bad that gets.
    </li>
    <li>
      <strong>16px on touch is the same iOS fix as on an input.</strong> Below 16px,
      Safari zooms the page when the control takes focus, and a page that zooms
      unexpectedly loses a low-vision reader's place.
    </li>
    <li>
      <strong><code>resize: vertical</code> is a genuine accessibility feature.</strong>
      Removing the resize handle takes away a reader's ability to see more of their own
      text at once. Deck constrains the axis rather than removing the affordance.
    </li>
    <li>
      <strong>A <code>maxlength</code> with no visible counter is a trap.</strong> The
      control silently stops accepting input, which reads as a broken keyboard. If you
      set a limit, show the remaining count — and put it in an
      <code>&lt;output&gt;</code> so it is announced.
    </li>
    <li>
      <strong>Placeholder text is not a label</strong> and is deliberately
      <code>--text-faint</code>, which is below 4.5:1. It is a hint, and Deck styles it
      as one.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Padding is logical and the browser handles the text direction itself, so a textarea
    mirrors with no extra rules. The resize handle moves to the opposite corner on its
    own, because the browser draws it at the end edge.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="field" style="max-inline-size:26rem">' . "\n" .
      '  <label class="label" for="ta-rtl">ملاحظة</label>' . "\n" .
      '  <textarea class="textarea" id="ta-rtl" placeholder="اكتب هنا"></textarea>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The border, shadow and background transition over
    <?= e(api_token('--dur-1')['value'] ?? '120ms') ?>, and the global reset collapses
    all three to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>.
  </p>
  <p class="text-muted">
    The growth from <code>field-sizing</code> is not a transition — the box jumps to its
    new height on each new line. That is deliberate: an animated height on every
    keystroke would be motion nobody asked for, and it would lag the caret.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> gives every control a plain <code>#999</code> border
    on white and drops the minimum height, so a printed form reads as a filled-in
    document rather than a screenshot of a web page.
  </p>
  <p>
    <code>.textarea</code> then gets a line of its own:
  </p>
  <pre class="dx-code"><code><?= e('.textarea { max-block-size: none !important; block-size: auto !important; }') ?></code></pre>
  <p>
    Both halves matter. Clearing <code>max-block-size</code> undoes the
    <?= e('22rem') ?> ceiling, and <code>block-size: auto</code> lets the box take its
    content's height — so a long note prints in full instead of printing the first
    <?= e('22rem') ?> of itself with no indication that anything was cut.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One textarea, no layer needed */
<textarea class="textarea" style="max-block-size:40rem"></textarea>

@layer app.components {
  /* A taller floor for a control that is mostly used for long text */
  .textarea { min-block-size: calc(var(--control-h) * 4); }

  /* Turn off the growth and go back to rows */
  .textarea-fixed { field-sizing: fixed; }
}') ?></code></pre>
  <p class="text-muted">
    <code>field-sizing: fixed</code> is the explicit way back to the old behaviour, and
    it makes the <code>rows</code> attribute meaningful again.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a single line.</strong> Use <a href="input.php"><code>.input</code></a>.
      A textarea accepts newlines, which means a name field can contain one and your
      validation now has to care.
    </li>
    <li>
      <strong>Not for formatted text.</strong> A textarea holds plain text. If the reader
      needs bold, lists or links, use <code>.editor</code>, which is a
      <code>contenteditable</code> region with a toolbar and a hidden input that carries
      the HTML.
    </li>
    <li>
      <strong>Not for code.</strong> A textarea has no monospace font, no tab handling
      and no line numbers. Add <code>.mono</code> at minimum, and consider whether an
      editor component is what you actually need.
    </li>
    <li>
      <strong>Not for a value with a known shape.</strong> A date, a phone number or a
      quantity has a control of its own — <code>.datefield</code>,
      <code>.phone</code>, <code>.number</code> — each of which gives a phone the right
      keypad and validates without you writing anything.
    </li>
    <li>
      <strong>Not without a ceiling, if you raise it.</strong> Removing
      <code>max-block-size</code> lets a long value push the submit button below the
      fold indefinitely. If you raise it, raise it to a number rather than to
      <code>none</code>.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
