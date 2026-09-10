<?php
declare(strict_types=1);

$page = [
    'path' => 'components/input.php',
    'title' => 'Input',
    'level' => 'Beginner',
    'description' => 'Deck\'s .input, .textarea and .select share one rule: 16px on touch so iOS never zooms, validation that waits for :user-invalid, a self-sizing textarea, and .input-group for prefixes and attached buttons.',
    'documents' => [
        'input', 'input-group', 'is-invalid',
    ],

    /* 19-logical.css is deliberately absent. It mirrors .select and .search
       under dir="rtl", and the Right to left section covers that — but neither
       selector touches .input, so the verifier is right to reject it here.
       The accounts map is what styles THIS component, not what the page talks
       about. */
    'component' => 'input',
    'accounts' => [
        '06-forms.css'      => 'documented: the shared control rule, every state, the textarea and select variants, .input-group and .search',
        '23-inputs.css'     => 'documented: .float and .float-outline float the label over the control — the Floating labels section',
        '10-datepicker.css' => 'documented: an .input inside .datefield and inside the picker\'s time row — the In other components section',
        '16-motion.css'     => 'documented: an invalid field shakes once, and only with motion allowed — the Reduced motion section',
        '99-print.css'      => 'documented: controls print as underlined values rather than boxes — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Input</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Input</h1>
  <p class="lede">
    <code>.input</code>, <code>.textarea</code> and <code>.select</code> are declared in
    one rule and differ only where they have to. They are
    <?= e(api_token('--control-h')['value'] ?? '42px') ?> tall, full width, and
    <strong>16px on any coarse pointer</strong> — because Safari on iOS zooms the whole
    page when a control smaller than 16px receives focus, and no amount of viewport
    configuration prevents it.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Put <code>.input</code> on a real <code>&lt;input&gt;</code>,
    <code>.textarea</code> on a <code>&lt;textarea&gt;</code>, and
    <code>.select</code> on a <code>&lt;select&gt;</code>. The class is a look; the
    element is the behaviour, and the element is what decides the keyboard, the mobile
    keypad and what a screen reader announces.
  </p>
  <p>
    Almost always wrap the control in a <a href="field.php"><code>.field</code></a>,
    which supplies the label, the help text and the error slot with the right gaps. A
    bare <code>.input</code> with no label is the most common form defect there is.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:22rem">' . "\n" .
      '  <label class="label" for="ex-email">Email</label>' . "\n" .
      '  <input class="input" type="email" id="ex-email" placeholder="you@example.com">' . "\n" .
      '</div>',
      'A control is a label plus an input. Neither is optional.',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="types">The three controls</h2>
  <p>
    One shared rule sets the size, border, radius, shadow and transition. Each control
    then adds only what makes it itself.
  </p>

  <div class="stack-2">
    <h3 id="t-input">Input</h3>
    <p class="text-muted">
      The shared rule, unmodified. Every <code>type</code> works — the class does not
      care, and the type is what gives a phone the right keypad.
    </p>
    <?php
    docs_example(
        '<input class="input" type="text" placeholder="Text" aria-label="Text">' . "\n" .
        '<input class="input" type="email" placeholder="you@example.com" aria-label="Email">' . "\n" .
        '<input class="input" type="tel" placeholder="+1 555 0100" aria-label="Telephone">' . "\n" .
        '<input class="input" type="date" aria-label="Date">',
        '',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="t-textarea">Textarea</h3>
    <p class="text-muted">
      <code>field-sizing: content</code> makes it grow with what is typed, from two rows
      up to a <?= e('22rem') ?> ceiling, so a comment box is never a four-row window
      onto a long paragraph. <code>resize: vertical</code> stays available for readers
      who want more.
    </p>
    <?php
    docs_example(
        '<textarea class="textarea" placeholder="Type a few lines and watch it grow" aria-label="Notes"></textarea>',
        'It grows as you type — no JavaScript involved',
        'stack'
    );
    ?>
    <p class="text-muted">
      <code>field-sizing</code> is not in every browser yet. Where it is missing the
      textarea is simply two rows tall and scrolls, which is the ordinary behaviour — the
      feature degrades to the default rather than breaking.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="t-select">Select</h3>
    <p class="text-muted">
      <code>appearance: none</code> plus two CSS gradients draw the chevron, so there is
      no background image to load and the arrow takes
      <code>currentColor</code> — it follows the text colour into dark mode on its own.
    </p>
    <?php
    docs_example(
        '<select class="select" aria-label="Plan">' . "\n" .
        '  <option>Starter</option>' . "\n" .
        '  <option>Team</option>' . "\n" .
        '  <option>Enterprise</option>' . "\n" .
        '</select>',
        '',
        'stack'
    );
    ?>
    <p class="dx-note text-muted">
      <code>background-position</code> is one of the few properties with no logical
      equivalent, so the arrow cannot mirror itself.
      <code>src/19-logical.css</code> redeclares it for <code>dir="rtl"</code> —
      including swapping the two gradient angles, because a chevron reflected without
      swapping them points the wrong way. See <a href="#rtl">Right to left</a>.
    </p>
  </div>
</section>

<section class="stack-6">
  <h2 id="states">States</h2>
  <p>
    Hover darkens the border. Focus swaps it for <code>--focus</code> and adds
    <code>--ring</code>, replacing the browser outline rather than sitting beside it.
    Disabled sinks the background and drops the shadow; readonly keeps the border but
    tints the fill, so the two are distinguishable at a glance.
  </p>
  <?php
  docs_example(
      '<input class="input" value="Ordinary" aria-label="Ordinary">' . "\n" .
      '<input class="input" value="Readonly" readonly aria-label="Readonly">' . "\n" .
      '<input class="input" value="Disabled" disabled aria-label="Disabled">',
      'Readonly is still selectable and focusable; disabled is neither',
      'stack'
  );
  ?>

  <h3 id="validation">Validation waits for the reader</h3>
  <p>
    This is the part of Deck's form handling worth understanding.
    <code>:invalid</code> matches an empty required field <em>immediately</em>, so a
    stylesheet that uses it paints a form red before the reader has typed anything. Deck
    uses <code>:user-invalid</code>, which only matches after the reader has interacted
    with the control and left it.
  </p>
  <p>
    <code>.is-invalid</code> on the enclosing <code>.field</code> is the escape hatch
    for validation your server did: it produces the same border and focus ring without
    needing the browser to agree that the value is wrong.
  </p>
  <?php
  docs_example(
      '<div class="field is-invalid" style="max-inline-size:22rem">' . "\n" .
      '  <label class="label" for="ex-vat">VAT number <span class="required">*</span></label>' . "\n" .
      '  <input class="input" id="ex-vat" value="GB-nope" aria-describedby="ex-vat-err">' . "\n" .
      '  <p class="error" id="ex-vat-err">That is not a VAT number we recognise.</p>' . "\n" .
      '</div>',
      '.is-invalid on the field, .error beneath, aria-describedby joining them',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.is-invalid</code> is one of the nine states an author writes by hand. The
    rest are set by Deck's JavaScript at runtime.
  </p>
</section>

<section class="stack-3">
  <h2 id="group">Input groups</h2>
  <p>
    <code>.input-group</code> is a flex row that takes over the border, radius, shadow
    and <code>overflow: hidden</code> from the control inside it. The
    <code>.input</code> then strips its own border and background, so the seam between a
    prefix and the field does not double up. <code>:focus-within</code> on the group
    draws the focus ring around the whole assembly, which is why focusing the input
    lights up the prefix too.
  </p>
  <?php
  docs_example(
      '<div class="input-group" style="max-inline-size:24rem">' . "\n" .
      '  <span class="addon">https://</span>' . "\n" .
      '  <input class="input" value="example.com" aria-label="Domain">' . "\n" .
      '</div>' . "\n" .
      '<div class="input-group" style="max-inline-size:24rem">' . "\n" .
      '  <input class="input" placeholder="Coupon code" aria-label="Coupon code">' . "\n" .
      '  <button class="btn">Apply</button>' . "\n" .
      '</div>' . "\n" .
      '<div class="input-group" style="max-inline-size:24rem">' . "\n" .
      '  <span class="addon">$</span>' . "\n" .
      '  <input class="input" type="text" inputmode="decimal" value="1240.00" aria-label="Amount">' . "\n" .
      '  <span class="addon">USD</span>' . "\n" .
      '</div>',
      'Prefix, attached button, and both at once',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.addon</code> borders are logical:
    <code>border-inline-end</code> normally, swapped to
    <code>border-inline-start</code> on <code>:last-child</code>. So a leading and a
    trailing addon each get their divider on the correct side, in both writing
    directions, from two rules.
  </p>
</section>

<section class="stack-3">
  <h2 id="search">Search</h2>
  <p>
    <code>.search</code> positions an icon inside the control with
    <code>inset-inline-start</code> and adds
    <?= e(api_token('--space-10')['value'] ?? '2.5rem') ?> of leading padding to the
    input. <code>pointer-events: none</code> on the icon means clicking it focuses the
    field underneath rather than doing nothing.
  </p>
  <?php
  docs_example(
      '<div class="search" style="max-inline-size:22rem">' . "\n" .
      '  <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#search"></use></svg>' . "\n" .
      '  <input class="input" type="search" placeholder="Search invoices" aria-label="Search invoices">' . "\n" .
      '</div>',
      'The icon is inside the field and not clickable',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="float">Floating labels</h2>
  <p>
    <code>.float</code> from <code>src/23-inputs.css</code> overlays the label on the
    control and lifts it once the field has content, using
    <code>:not(:placeholder-shown)</code> — no JavaScript and no state class. The
    control needs a <code>placeholder</code> for the selector to have anything to test,
    and the label must come <em>after</em> the input in the markup so the sibling
    combinator can reach it.
  </p>
  <?php
  docs_example(
      '<div class="float" style="max-inline-size:22rem">' . "\n" .
      '  <input class="input" id="ex-float" placeholder=" ">' . "\n" .
      '  <label for="ex-float">Company name</label>' . "\n" .
      '</div>',
      'Type in it: the label lifts. Empty it: the label drops back.',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>placeholder=" "</code> — a single space — is the trick. It makes
    <code>:placeholder-shown</code> match while the field is empty without showing any
    placeholder text that would collide with the label.
  </p>
</section>

<section class="stack-3">
  <h2 id="other">In other components</h2>
  <p>
    <code>.input</code> appears inside components that are documented elsewhere, and
    each adjusts it rather than restyling it:
  </p>
  <ul class="stack-2">
    <li>
      <code>.datefield &gt; .input</code> in <code>src/10-datepicker.css</code> makes
      room for the calendar trigger.
    </li>
    <li>
      <code>.datepicker-time .input</code> sizes the hour and minute boxes inside the
      picker's footer.
    </li>
    <li>
      <code>.combo-control</code> shares the invalid-shake rule, so a combobox and an
      input misbehave identically.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    Generated from the stylesheet source. <code>.textarea</code>,
    <code>.select</code>, <code>.addon</code> and <code>.search</code> are documented on
    this page in prose but belong to no component root, so they are not in the table —
    the table is the <code>input</code> component exactly as the extractor reports it.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    <code>--control-h</code> is shared with <code>.btn</code>, which is why a button and
    an input in the same row line up without either being adjusted.
  </p>
  <?php docs_token_table(['--control-h', '--surface', '--surface-2', '--bg-sunken', '--line', '--line-strong', '--ink-400', '--focus', '--ring', '--r-sm', '--r-full', '--shadow-1', '--text-base', '--text-sm', '--text', '--text-muted', '--text-faint', '--bad-500', '--space-2', '--space-3', '--space-10', '--dur-1']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Every control needs a label.</strong> A <code>&lt;label
      class="label" for="id"&gt;</code> is the right answer.
      <code>aria-label</code> is the fallback used by the short examples on this page
      and is worse: it is invisible, so it drifts from the placeholder beside it and
      cannot be translated by the page.
    </li>
    <li>
      <strong>A placeholder is not a label.</strong> It disappears when the reader
      types, has no programmatic association, and is <code>--text-faint</code>, which is
      below 4.5:1 on purpose — placeholders are hints, and Deck styles them as hints.
    </li>
    <li>
      <strong>Focus replaces the outline rather than adding to it.</strong>
      <code>outline: none</code> with <code>box-shadow: var(--ring)</code> is only safe
      because the ring is always drawn; the border colour changes too, so there are two
      cues.
    </li>
    <li>
      <strong>16px on touch is an accessibility fix, not a style choice.</strong>
      <code>@media (pointer: coarse)</code> raises the font size so iOS Safari does not
      zoom on focus. A page that zooms unexpectedly loses a low-vision reader's place
      entirely.
    </li>
    <li>
      <strong>Errors need <code>aria-describedby</code>.</strong> A
      <code>.error</code> paragraph next to an input is invisible to a screen reader
      unless the input points at it. Deck cannot wire that up from CSS; the example in
      <a href="#validation">Validation</a> shows the markup.
    </li>
    <li>
      <strong><code>:user-invalid</code> is the accessible choice.</strong> Marking a
      field wrong before the reader has touched it is noise, and a screen-reader user
      hears "invalid entry" on a field they have not reached.
    </li>
    <li>
      <strong>Disabled controls are removed from the tab order</strong> by the
      <code>disabled</code> attribute, not by the class. <code>.input[disabled]</code>
      only styles what the attribute already did — there is no
      <code>.is-disabled</code> for inputs, on purpose.
    </li>
    <li>
      <strong>The select's chevron is decorative.</strong> It is drawn with
      <code>background-image</code>, so it is never announced and never selectable. That
      is correct — the element already announces itself as a combobox.
    </li>
    <li>
      <strong>Contrast.</strong> <code>--line-strong</code> against
      <code>--surface</code> clears 3:1, the non-text minimum for a control boundary, in
      both themes.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Padding, the addon dividers and the search icon are all logical, so they mirror with
    no extra rules at all. Two things cannot, and
    <code>src/19-logical.css</code> handles both by hand:
  </p>
  <ul class="stack-2">
    <li>
      <strong>The select's chevron.</strong> It is drawn with
      <code>background-position</code>, which has no logical form, so the rule
      redeclares the positions from the other edge <em>and</em> swaps the two gradient
      angles — a mirrored chevron that keeps its original angles points the wrong way.
      The padding is redeclared with it, because the arrow and the text-side padding
      have to move together.
    </li>
    <li>
      <strong>The search icon</strong>, which is positioned with
      <code>inset-inline-start</code> and therefore already correct — the rule beside it
      exists for the icon's own asymmetry rather than its position.
    </li>
  </ul>
  <p class="text-muted">
    This is the general shape of Deck's RTL story: logical properties handle almost
    everything, and the handful of properties that have no logical form are collected in
    one file rather than scattered as <code>[dir="rtl"]</code> overrides beside each
    component.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="stack-3" style="max-inline-size:22rem">' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="ex-rtl">البريد الإلكتروني</label>' . "\n" .
      '    <input class="input" id="ex-rtl" placeholder="you@example.com">' . "\n" .
      '  </div>' . "\n" .
      '  <div class="input-group">' . "\n" .
      '    <span class="addon">https://</span>' . "\n" .
      '    <input class="input" value="example.com" aria-label="النطاق">' . "\n" .
      '  </div>' . "\n" .
      '  <select class="select" aria-label="الخطة">' . "\n" .
      '    <option>أساسية</option>' . "\n" .
      '    <option>فريق</option>' . "\n" .
      '  </select>' . "\n" .
      '</div>',
      'All three mirror — the select needs one hand-written rule to do it',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Controls transition <code>border-color</code>, <code>box-shadow</code> and
    <code>background-color</code> over
    <?= e(api_token('--dur-1')['value'] ?? '120ms') ?>. The global reset collapses all
    three to <code>.01ms</code> under <code>prefers-reduced-motion: reduce</code>.
  </p>
  <p>
    <code>src/16-motion.css</code> also shakes an invalid field once —
    <code>.field.is-invalid:not(.was-shaken)</code> — and that rule lives inside
    <code>@media (prefers-reduced-motion: no-preference)</code>, so it is never declared
    for a reader who has asked for less motion. The <code>:not(.was-shaken)</code> guard
    is what stops it repeating every time the page re-renders.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> turns <code>.input</code> into an underlined value: the
    box, the shadow and the fill go, and what remains is the text the reader entered with
    a rule beneath it. A printed form should read as a filled-in document, not as a
    screenshot of a web form.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  .input, .textarea, .select {
    border-radius: var(--r-md);
    box-shadow: none;
  }
}

/* The whole control size, globally, from one token */
:root { --control-h: 48px; }') ?></code></pre>
  <p>
    Prefer changing <code>--control-h</code> over setting a height on
    <code>.input</code>: buttons, selects and the datepicker's trigger all read the same
    token, so the token keeps them aligned and a local height does not.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a number the reader will step.</strong> Use
      <code>.number</code>, which has real increment and decrement buttons.
      <code>&lt;input type="number"&gt;</code> with <code>.input</code> gives you the
      browser's tiny spinners, which differ per browser and are hard to hit.
    </li>
    <li>
      <strong>Not for a searchable list of options.</strong> A <code>.select</code> with
      two hundred <code>&lt;option&gt;</code>s is unusable. Use
      <code>.combo</code>, which filters as the reader types.
    </li>
    <li>
      <strong>Not for one-of-three.</strong> A select with three options costs two
      clicks to reveal a choice that <code>.segmented</code> or a group of
      <code>.check</code> radios shows immediately.
    </li>
    <li>
      <strong>Not for a date, if the format matters.</strong>
      <code>&lt;input type="date"&gt;</code> renders differently in every browser and
      cannot be styled. Use <code>.datefield</code> with the datepicker, which controls
      the format and the calendar.
    </li>
    <li>
      <strong>Not on a <code>&lt;div contenteditable&gt;</code>.</strong> The class will
      style it and nothing else will work: no <code>name</code>, no form submission, no
      validation, no mobile keypad. Use <code>.editor</code> if you need rich text.
    </li>
    <li>
      <strong><code>.input-group</code> is not for two separate fields.</strong> It
      removes the inner borders so the assembly reads as one control. Two inputs that
      the reader fills in independently need two <code>.field</code>s in a
      <code>.field-row</code>.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
