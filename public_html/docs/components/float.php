<?php
declare(strict_types=1);

$page = [
    'path' => 'components/float.php',
    'title' => 'Floating label',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .float lifts a label out of the field when it has focus or content, with no JavaScript — :placeholder-shown does the work. The label has to come after the control.',
    'documents' => [
        'float', 'float-outline', 'is-invalid',
    ],

    'component' => 'float',
    'accounts' => [
        '23-inputs.css' => 'documented: the wrapper, the resting and lifted label positions, the invalid colour, and the outlined variant with its notch',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Floating label</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Floating label</h1>
  <p class="lede">
    A label that sits inside the field when it is empty and lifts above the text when it is
    not. There is no JavaScript: <code>:placeholder-shown</code> tells the stylesheet
    whether the field has content, so the label can never get out of sync with the value the
    way a scripted version can.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it where vertical space is genuinely tight and the labels are short — a dense
    settings panel, a payment form on a phone. It buys you roughly one label's height per
    field.
  </p>
  <p>
    That is the whole benefit, and it is worth being honest that it is not large. A
    <a href="field.php"><code>.field</code></a> with a label above it is easier to read,
    easier to translate, and survives a long label without breaking. Reach for
    <code>.float</code> when you have measured that you need the space, not by default.
  </p>
</section>

<section class="stack-3">
  <h2 id="markup">The markup, and the order that matters</h2>
  <p>
    Two rules, and both are easy to get wrong because neither fails loudly:
  </p>
  <ul class="stack-2">
    <li>
      <strong>The label comes <em>after</em> the control.</strong> Every selector that
      lifts the label is a sibling combinator — <code>.float &gt; .input:not(:placeholder-shown) ~ label</code>
      — and <code>~</code> only looks forward. Put the label first and it simply never
      moves.
    </li>
    <li>
      <strong>The control needs <code>placeholder=" "</code></strong> — a single space.
      Without a placeholder attribute <code>:placeholder-shown</code> never matches, so the
      label stays lifted over an empty field. The space is what keeps the placeholder
      invisible while still existing.
    </li>
  </ul>
  <?php
  docs_example(
      '<div class="float" style="max-inline-size:22rem">' . "\n" .
      '  <input class="input" id="dx-fl-email" type="email" placeholder=" ">' . "\n" .
      '  <label for="dx-fl-email">Email address</label>' . "\n" .
      '</div>',
      'Control first, label second — the reverse of every other Deck form pattern',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    The label is still a real <code>&lt;label for&gt;</code>, so it names the control for a
    screen reader whether it is lifted or resting. The visual trick is entirely in
    <code>translate</code> and <code>scale</code>; nothing about the semantics changes.
  </p>
</section>

<section class="stack-3">
  <h2 id="controls">What it works with</h2>
  <p>
    <code>.input</code>, <code>.textarea</code> and <code>.select</code>. The wrapper adds
    the top padding the lifted label needs, and the textarea gets a little more because its
    text starts at the top rather than being centred.
  </p>
  <?php
  docs_example(
      '<div class="stack-3" style="max-inline-size:22rem">' . "\n" .
      '  <div class="float">' . "\n" .
      '    <input class="input" id="dx-fl-name" placeholder=" " value="Ada Lovelace">' . "\n" .
      '    <label for="dx-fl-name">Full name</label>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="float">' . "\n" .
      '    <select class="select" id="dx-fl-plan">' . "\n" .
      '      <option>Starter</option>' . "\n" .
      '      <option>Team</option>' . "\n" .
      '    </select>' . "\n" .
      '    <label for="dx-fl-plan">Plan</label>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="float">' . "\n" .
      '    <textarea class="textarea" id="dx-fl-note" placeholder=" " rows="3"></textarea>' . "\n" .
      '    <label for="dx-fl-note">Note</label>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'A filled input, a select, and an empty textarea',
      'stack'
  );
  ?>
  <p class="text-muted">
    A <code>&lt;select&gt;</code> has no placeholder and always shows something, so its
    label is lifted permanently — <code>.float &gt; .select ~ label</code> carries no
    <code>:placeholder-shown</code> test. That is correct, but it means a select in a
    <code>.float</code> gets none of the space saving, only the appearance.
  </p>
</section>

<section class="stack-3">
  <h2 id="outline">The outlined variant</h2>
  <p>
    <code>.float-outline</code> parks the lifted label on the border itself, in a notch cut
    by giving it the surface colour as a background. It is the Material-style treatment, and
    it works with Deck's own bordered controls without any extra markup.
  </p>
  <?php
  docs_example(
      '<div class="stack-3" style="max-inline-size:22rem">' . "\n" .
      '  <div class="float float-outline">' . "\n" .
      '    <input class="input" id="dx-fo-a" placeholder=" ">' . "\n" .
      '    <label for="dx-fo-a">Company</label>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="float float-outline">' . "\n" .
      '    <input class="input" id="dx-fo-b" placeholder=" " value="ACME Corp">' . "\n" .
      '    <label for="dx-fo-b">Trading name</label>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Empty and filled — the notch only appears once the label lifts',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    The notch is <code>background: var(--surface)</code> on the label, not a real gap in the
    border. On any other background — a tinted <a href="card.php">card</a>, a
    <a href="gradients.php">gradient</a> — the label paints a surface-coloured rectangle
    over it, and the effect breaks. Override <code>background</code> on the label to match
    whatever it actually sits on.
  </p>
</section>

<section class="stack-3">
  <h2 id="invalid">Invalid</h2>
  <p>
    Two ways in, and they do the same thing. <code>:user-invalid</code> is the browser's
    own — it applies after the reader has interacted and the value fails validation, which
    is the right moment rather than on page load. <code>.is-invalid</code> on the wrapper is
    the manual override for server-side errors the browser cannot know about.
  </p>
  <?php
  docs_example(
      '<div class="stack-2" style="max-inline-size:22rem">' . "\n" .
      '  <div class="float is-invalid">' . "\n" .
      '    <input class="input" id="dx-fl-bad" placeholder=" " value="not-an-email" aria-describedby="dx-fl-bad-e">' . "\n" .
      '    <label for="dx-fl-bad">Email address</label>' . "\n" .
      '  </div>' . "\n" .
      '  <p class="error" id="dx-fl-bad-e">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#alert-circle"></use></svg>' . "\n" .
      '    <span>That address is not registered.</span>' . "\n" .
      '  </p>' . "\n" .
      '</div>',
      'The colour is the signal; the message underneath is the information',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <strong>It behaves differently from <code>.field</code>.</strong>
    <code>.field.is-invalid</code> reaches into the control and reddens its border —
    <code>src/06-forms.css</code> has a rule for exactly that. <code>.float.is-invalid</code>
    has no such rule: it colours the label and stops. So the same class name on the two
    wrappers does two different amounts of work, and a <code>.float</code> marked invalid
    shows a red label above a normal-looking field. Use <code>:user-invalid</code> where you
    can, since that reaches the control through
    <a href="input.php#validation"><code>.input</code></a>'s own rule, and add the border
    yourself where you cannot. Recorded in <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/23-inputs.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--control-h-lg', '--text-faint', '--text-muted', '--brand', '--bad-700', '--surface', '--text-base', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The label is real, and that is the reason to use this over a placeholder.</strong>
      A placeholder-as-label disappears the moment someone types and is announced
      inconsistently. A <code>.float</code> label is a <code>&lt;label for&gt;</code> that
      stays on screen and stays in the accessibility tree. This pattern is a fix for that
      problem, not another instance of it.
    </li>
    <li>
      <strong>The resting label is low-contrast.</strong> It is
      <code><?= e(api_token('--text-faint')['value'] ?? '--text-faint') ?></code>, which is
      chosen to read as a hint rather than as content. On an empty form that is the only
      thing naming the field, so check it against your surface — this is the same trade-off
      every floating-label implementation makes and it is the weakest point of the pattern.
    </li>
    <li>
      <strong>The lifted label is 78% size.</strong> On top of a small base size that can
      land under 11px. <code>scale</code> does not respond to the reader's font settings the
      way a <code>font-size</code> would, so it shrinks below whatever minimum they have
      set.
    </li>
    <li>
      <strong>Clicking the label works, by accident of stacking.</strong> The label is
      <code>pointer-events: none</code>, so the click passes through to the control
      underneath and focuses it. The result is right; it is worth knowing it is not the
      <code>for</code> attribute doing it, so a label moved outside the wrapper stops
      responding to clicks.
    </li>
    <li>
      <strong>A long label has nowhere to go.</strong> The label is absolutely positioned
      and does not wrap. In a language where your six-character label becomes twenty-four —
      German, Finnish — it runs out of the field. Test with your longest translation, not
      your English.
    </li>
    <li>
      <strong>The error colour is not the error message.</strong> A red label says something
      is wrong and not what. Pair it with <code>.error</code> and
      <code>aria-describedby</code>, as the example does.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The label is placed with <code>inset-inline-start</code> and its
    <code>transform-origin</code> is <code>inline-start center</code> — both logical, so the
    label starts on the correct side and shrinks toward it rather than away. Nothing in
    <code>src/19-logical.css</code> is needed for this component.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="float" style="max-inline-size:22rem">' . "\n" .
      '  <input class="input" id="dx-fl-rtl" placeholder=" " value="آدا لوفلايس">' . "\n" .
      '  <label for="dx-fl-rtl">الاسم الكامل</label>' . "\n" .
      '</div>',
      'The label lifts toward the right edge, which is where it started',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The lift is a <?= e(api_token('--dur-2')['value'] ?? '180ms') ?> transition on
    <code>translate</code>, <code>scale</code> and <code>color</code>, and the global reset
    in <code>src/02-reset.css</code> collapses it to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>. The label still moves — it has to, or the
    text would sit under it — but it arrives instantly rather than sliding.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.float</code> is not in <code>src/99-print.css</code>. A filled field prints with
    its label lifted and legible. An <em>empty</em> field prints with the label resting
    inside it, which on paper is indistinguishable from a field someone has already filled
    in with that word.
  </p>
  <p class="text-muted">
    If the form is meant to be printed and completed by hand, use
    <a href="field.php"><code>.field</code></a> instead. This is the one context where the
    pattern actively misleads.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* The outlined notch on a tinted card rather than the page surface */
  .card .float-outline > label { background: var(--surface-2); }

  /* A gentler lift for a form with long labels */
  .float:focus-within > label,
  .float > .input:not(:placeholder-shown) ~ label { scale: .88; }

  /* Give .float.is-invalid the reach .field.is-invalid already has */
  .float.is-invalid :is(.input, .textarea, .select) {
    border-color: var(--bad-500);
  }
}') ?></code></pre>
  <p class="text-muted">
    If you change the <code>scale</code>, check the wrapper's top padding still clears the
    label — the two are set independently and nothing warns you when they disagree.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not as a default.</strong> A <a href="field.php"><code>.field</code></a> with
      the label above is more legible and more robust. Floating labels are a space
      optimisation, and space is rarely the binding constraint on a form.
    </li>
    <li>
      <strong>Not with help text.</strong> The pattern saves a line and then you add one
      back underneath. If a field needs an explanation, use a normal label and put the
      explanation where it belongs.
    </li>
    <li>
      <strong>Not for a form you will translate.</strong> The label cannot wrap. English
      labels fit; the same labels in German frequently do not.
    </li>
    <li>
      <strong>Not on a form to be printed and filled in.</strong> See
      <a href="#print">Printing</a> — the resting label reads as an answer.
    </li>
    <li>
      <strong>Not for a <a href="check.php">checkbox or radio</a>.</strong> There is nothing
      to float out of; the label belongs beside the control.
    </li>
    <li>
      <strong>Not mixed with plain fields in the same form.</strong> Two label conventions
      in one column reads as a mistake, and the reader has to learn both.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
