<?php
declare(strict_types=1);

$page = [
    'path' => 'components/field.php',
    'title' => 'Field',
    'level' => 'Beginner',
    'description' => 'Deck\'s .field wraps a label, a control, help text and an error into one column with no margins, and .field-row pairs fields up — by viewport width, or by their own container\'s width.',
    'documents' => [
        'field', 'field-row', 'field-row-cq', 'is-invalid',
        'label', 'help', 'error', 'required', 'optional',
        'fieldset', 'file', 'form-actions', 'form-actions-sticky',
    ],

    'component' => 'field',
    'accounts' => [
        '06-forms.css'     => 'documented: the field column, the invalid state, and .field-row — plus the label, help, error, fieldset, file and form-actions parts this page also covers',
        '18-container.css' => 'documented: .field-row-cq pairs on its own container\'s width instead of the viewport\'s — the Pairing on the container section',
        '16-motion.css'    => 'documented: an invalid field shakes once, and only when motion is allowed — the Reduced motion section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Field</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Field</h1>
  <p class="lede">
    <code>.field</code> is one control and everything attached to it: a label above, the
    control, and help or error text below. It is a flex column with a
    <?= e(api_token('--space-2')['value'] ?? '.5rem') ?> gap, which means
    <strong>a field never needs a margin</strong> — and that is the point. Margins on
    form parts are how forms end up with inconsistent spacing.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Wrap every control in one. A form is then a <code>.stack</code> of fields, and the
    only spacing decisions left are the stack's gap and the field's gap — both of which
    are already made.
  </p>
  <p>
    <code>min-inline-size: 0</code> is on the field for the same reason it is on
    <code>.list-main</code>: a grid or flex child defaults to a minimum of its content,
    so a long value in a field inside a <code>.field-row</code> would otherwise push the
    row wider than its track.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:24rem">' . "\n" .
      '  <label class="label" for="f-name">Full name</label>' . "\n" .
      '  <input class="input" id="f-name" placeholder="Ada Chen">' . "\n" .
      '  <p class="help">As it appears on the invoice.</p>' . "\n" .
      '</div>',
      'Label, control, help — one column, no margins',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="parts">The parts</h2>

  <div class="stack-2">
    <h3 id="p-label">Label</h3>
    <p>
      <code>.label</code> is a flex row, not a block, so a marker sits beside the text
      with no extra wrapper. <code>.required</code> is coloured with
      <code>--bad-500</code>; <code>.optional</code> is faint and normal weight.
    </p>
    <?php
    docs_example(
        '<div class="stack-4" style="max-inline-size:24rem">' . "\n" .
        '  <div class="field">' . "\n" .
        '    <label class="label" for="f-vat">VAT number <span class="required">*</span></label>' . "\n" .
        '    <input class="input" id="f-vat">' . "\n" .
        '  </div>' . "\n" .
        '  <div class="field">' . "\n" .
        '    <label class="label" for="f-co">Company <span class="optional">optional</span></label>' . "\n" .
        '    <input class="input" id="f-co">' . "\n" .
        '  </div>' . "\n" .
        '</div>',
        '',
        'stack'
    );
    ?>
    <p class="dx-note text-muted">
      An asterisk means nothing to a screen reader — it is announced as "star" or
      skipped. Put <code>required</code> on the input as well; that is what is actually
      announced, and the asterisk is the visual echo of it.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="p-help">Help and error</h3>
    <p>
      <code>.help</code> is muted; <code>.error</code> is a flex row in a red that is
      lightened for dark mode with <code>light-dark()</code>, so it stays readable
      against a dark surface rather than turning into a dark red on near-black.
    </p>
    <?php
    docs_example(
        '<div class="field is-invalid" style="max-inline-size:24rem">' . "\n" .
        '  <label class="label" for="f-em">Email <span class="required">*</span></label>' . "\n" .
        '  <input class="input" id="f-em" value="ada@" aria-describedby="f-em-err">' . "\n" .
        '  <p class="error" id="f-em-err">' . "\n" .
        '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#alert-circle"></use></svg>' . "\n" .
        '    <span>Enter a complete email address.</span>' . "\n" .
        '  </p>' . "\n" .
        '</div>',
        '.is-invalid on the field, aria-describedby joining input and message',
        'stack'
    );
    ?>
    <p class="text-muted">
      <code>align-items: flex-start</code> keeps the icon on the first line of a
      two-line error rather than centring it against the block.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="p-fieldset">Fieldset</h3>
    <p>
      <code>.fieldset</code> is a real <code>&lt;fieldset&gt;</code> with a border, a
      radius and a flex column inside, and its <code>&lt;legend&gt;</code> is small and
      muted. Use it wherever a group of controls answers one question — most
      importantly, around a radio group.
    </p>
    <?php
    docs_example(
        '<fieldset class="fieldset" style="max-inline-size:24rem">' . "\n" .
        '  <legend>Billing period</legend>' . "\n" .
        '  <label class="check">' . "\n" .
        '    <input type="radio" name="f-period" checked>' . "\n" .
        '    <span>Monthly</span>' . "\n" .
        '  </label>' . "\n" .
        '  <label class="check">' . "\n" .
        '    <input type="radio" name="f-period">' . "\n" .
        '    <span>Annual</span>' . "\n" .
        '  </label>' . "\n" .
        '</fieldset>',
        'The legend is the group\'s accessible name',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="p-file">File</h3>
    <p>
      <code>.file</code> is a dashed drop zone. The real
      <code>&lt;input type="file"&gt;</code> inside it is positioned off and made 1px —
      not <code>display: none</code>, which would take it out of the tab order.
      <code>:focus-within</code> on the label is what gives the zone a visible focus
      state when the hidden input is focused.
    </p>
    <?php
    docs_example(
        '<label class="file" style="max-inline-size:24rem">' . "\n" .
        '  <svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#upload"></use></svg>' . "\n" .
        '  <span>Drop a CSV here, or choose a file</span>' . "\n" .
        '  <input type="file" accept=".csv">' . "\n" .
        '</label>',
        'Tab to it: the border lights up because of :focus-within',
        'stack'
    );
    ?>
    <p class="dx-note text-muted">
      Deck styles the drop zone; it does not implement dropping. Without a
      <code>drop</code> handler the dashed border is a promise the page does not keep —
      the reader can still click to choose a file, but dragging one onto it does
      nothing.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="row">Pairing fields</h2>
  <p>
    <code>.field-row</code> is one column on a phone and an auto-fitting grid above
    <code>40rem</code>, with a <code>14rem</code> floor per column. Because it is
    <code>auto-fit</code> rather than a fixed count, two fields become two columns and
    four become four or two depending on the room — no variant per count.
  </p>
  <?php
  docs_example(
      '<div class="field-row">' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="f-first">First name</label>' . "\n" .
      '    <input class="input" id="f-first">' . "\n" .
      '  </div>' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="f-last">Last name</label>' . "\n" .
      '    <input class="input" id="f-last">' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Two up above 40rem, stacked below it',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="row-cq">Pairing on the container</h2>
  <p>
    <code>.field-row-cq</code> does the same thing on its own container's width instead
    of the viewport's. The source comment calls this "the one that keeps biting people",
    and it is right: a two-up field row inside a narrow modal or a sidebar should stay
    stacked <em>even on a desktop</em>, and a viewport media query cannot know that.
  </p>
  <?php
  docs_example(
      '<div class="cq" style="resize:horizontal;overflow:auto;min-inline-size:16rem;inline-size:24rem;max-inline-size:100%;padding-inline-end:var(--space-3)">' . "\n" .
      '  <div class="field-row-cq">' . "\n" .
      '    <div class="field">' . "\n" .
      '      <label class="label" for="f-city">City</label>' . "\n" .
      '      <input class="input" id="f-city">' . "\n" .
      '    </div>' . "\n" .
      '    <div class="field">' . "\n" .
      '      <label class="label" for="f-post">Postcode</label>' . "\n" .
      '      <input class="input" id="f-post">' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Drag it narrow: the fields stack, whatever the window is doing',
      'stack'
  );
  ?>
  <p class="text-muted">
    It needs a query container above it — <code>.cq</code> on the parent. Without one
    the <code>@container</code> rule never matches and the row stays in one column
    forever, which is a safe failure but not the one you wanted.
  </p>
</section>

<section class="stack-3">
  <h2 id="actions">Form actions</h2>
  <p>
    <code>.form-actions</code> is a wrapping flex row for the buttons at the end of a
    form. <code>.form-actions-sticky</code> pins it to the bottom of the viewport with a
    blur behind it, and adds <code>env(safe-area-inset-bottom)</code> so it clears the
    home indicator on a phone. Its buttons are full width below <code>48rem</code> and
    natural width above.
  </p>
  <?php
  docs_example(
      '<div class="form-actions">' . "\n" .
      '  <button class="btn btn-primary">Save changes</button>' . "\n" .
      '  <button class="btn btn-ghost">Cancel</button>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.form-actions-sticky</code> is not shown live here because it would pin itself
    to the bottom of this page. <code>src/99-print.css</code> forces it back to
    <code>position: static</code>, so a printed form has its buttons where the form ends
    rather than stamped across the last page.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    Generated from the stylesheet source. The <code>field</code> component proper is
    three classes and one state; the rest are the parts that live around it in
    <code>src/06-forms.css</code> and are documented here because they have nowhere
    better to go.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--space-2', '--space-3', '--space-4', '--space-5', '--line', '--line-strong', '--r-md', '--text-sm', '--text', '--text-muted', '--text-faint', '--bad-500', '--bad-700', '--brand-500', '--brand-soft', '--surface', '--surface-2', '--bg', '--z-sticky']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong><code>.field</code> has no semantics.</strong> It is a
      <code>&lt;div&gt;</code> that supplies spacing. The label/control association comes
      from <code>for</code> and <code>id</code>, and nothing about the class creates or
      checks it.
    </li>
    <li>
      <strong>Help and error text need <code>aria-describedby</code>.</strong> Visual
      proximity is not association. Give the <code>.help</code> or <code>.error</code>
      an <code>id</code> and point the control at it, or the message is invisible to a
      screen reader.
    </li>
    <li>
      <strong>An asterisk is not "required".</strong> Put the
      <code>required</code> attribute on the control. <code>.required</code> is the
      visual marker for sighted readers and is announced as punctuation or not at all.
    </li>
    <li>
      <strong>Marking the optional ones is often better.</strong> If most fields are
      required, <code>.optional</code> on the few that are not is less visual noise and
      reads more clearly than an asterisk on almost every label.
    </li>
    <li>
      <strong>A radio or checkbox group needs a <code>&lt;fieldset&gt;</code> and
      <code>&lt;legend&gt;</code>.</strong> A <code>.label</code> above a group of
      radios looks like a group label and is not one.
    </li>
    <li>
      <strong>The file input is hidden but focusable.</strong> It is 1px and
      transparent rather than <code>display: none</code>, so it keeps its place in the
      tab order. <code>:focus-within</code> on the label is what makes that focus
      visible — without it the input would be reachable but invisible.
    </li>
    <li>
      <strong><code>.error</code> is not a live region.</strong> Showing it does not
      announce it. For a message that appears after a failed submit, add
      <code>role="alert"</code> at the moment it is inserted, or move focus to the first
      invalid control.
    </li>
    <li>
      <strong><code>.form-actions-sticky</code> can cover content.</strong> It is
      <code>position: sticky</code> at the bottom, so the last field of a long form can
      end up underneath it. Add <code>padding-block-end</code> to the form equal to the
      bar's height.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Everything here is a flex column, a grid, or logical padding, so the whole assembly
    mirrors with no extra rules. The legend's <code>padding-inline</code> and the error
    row's <code>gap</code> both follow the writing direction.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="field-row">' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="f-rtl-a">الاسم الأول <span class="required">*</span></label>' . "\n" .
      '    <input class="input" id="f-rtl-a">' . "\n" .
      '  </div>' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="f-rtl-b">اسم العائلة</label>' . "\n" .
      '    <input class="input" id="f-rtl-b">' . "\n" .
      '    <p class="help">كما يظهر على الفاتورة.</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'A whole field row, mirrored, with no RTL-specific CSS',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>src/16-motion.css</code> shakes an invalid field once —
    <code>dk-shake</code>, 420ms, six pixels each way. Two things make it well behaved:
    it is declared inside <code>@media (prefers-reduced-motion: no-preference)</code>, so
    a reader who has asked for less motion never gets the rule; and the selector is
    <code>.field.is-invalid:not(.was-shaken)</code>, so adding
    <code>.was-shaken</code> after the first run stops it repeating on every re-render.
  </p>
  <p class="text-muted">
    A form that shakes every time it re-renders is worse than one that never shakes.
    <code>:not(.was-shaken)</code> is the whole reason that does not happen.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.fieldset</code> gets the same surface treatment as a card — a hairline, no
    radius, <code>break-inside: avoid</code>. <code>.form-actions-sticky</code> is forced
    back to <code>position: static</code>, so the buttons print where the form ends
    rather than floating over the last page.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A label beside the control instead of above it */
  .field-inline {
    display: grid;
    grid-template-columns: 10rem minmax(0, 1fr);
    align-items: center;
  }

  /* Pair fields sooner */
  @media (min-width: 32rem) {
    .field-row { grid-template-columns: repeat(auto-fit, minmax(min(11rem, 100%), 1fr)); }
  }
}') ?></code></pre>
  <p>
    Deck ships no inline-label variant on purpose: a label beside its control breaks down
    on a phone, needs a width guess that is wrong for long labels, and is harder to
    scan. If you need one it is four declarations, and the trade-off is then yours
    rather than the framework's.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a checkbox or radio.</strong> Those already carry their own label
      by wrapping it — see <a href="check.php">checkbox and radio</a>. A
      <code>.field</code> around one adds a second label above the row and reads as two
      questions.
    </li>
    <li>
      <strong><code>.field-row</code> versus <code>.grid</code>.</strong> The field row
      is a grid with the form gap and a sensible column floor already chosen. If you are
      laying out anything that is not a pair of fields, use <code>.grid</code> and stop
      borrowing the form's spacing.
    </li>
    <li>
      <strong><code>.field-row</code> versus <code>.field-row-cq</code>.</strong> Use
      the plain one for a form that owns the page width. Use the container one whenever
      the form could appear in a modal, a drawer or a sidebar — which is most of the
      time, and is why the source comment says it keeps biting people.
    </li>
    <li>
      <strong>Not for a whole form's spacing.</strong> A <code>.field</code> spaces one
      control's parts. The space between fields is a <code>.stack</code> on the form.
      Nesting fields to get more spacing is how a form ends up with four different gaps.
    </li>
    <li>
      <strong>Not around a submit button.</strong> Use <code>.form-actions</code>, which
      wraps and keeps buttons on one line where there is room.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
