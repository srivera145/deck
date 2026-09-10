<?php
declare(strict_types=1);

/**
 * Forms.
 *
 * The problem this starts from is not "how do I style an input" — it is that
 * the reader has a form which turns red while people are still typing in it,
 * and they are about to write JavaScript to fix a problem CSS already solved.
 */

$page = [
    'path' => 'guides/forms.php',
    'title' => 'Build a form that validates itself',
    'level' => 'Beginner',
    'description' => "Stop a form turning red while people are still typing. How :user-invalid, real HTML validation and Deck's field anatomy give you error states with no JavaScript.",
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Guides</li>
    <li aria-current="page">Build a form that validates itself</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Build a form that validates itself</h1>
  <p class="lede">
    Your form goes red on the first keystroke, because an empty required field is invalid
    the moment the page loads and <code>:invalid</code> says so. That is the problem worth
    solving, and it does not need JavaScript — it needs a different pseudo-class. This page
    is that, plus the anatomy of a Deck field and the four accessibility details that are
    yours rather than the framework's.
  </p>
</header>

<section class="stack-4">
  <h2 id="answer">The short answer</h2>
  <p>
    Use real HTML validation attributes and let Deck style
    <code>:user-invalid</code>, which it already does. Write no CSS and no script.
  </p>
  <pre class="dx-code"><code>&lt;div class="field"&gt;
  &lt;label class="label" for="email"&gt;Email&lt;/label&gt;
  &lt;input class="input" id="email" type="email" required&gt;
&lt;/div&gt;</code></pre>
  <p>
    <code>:invalid</code> matches from the moment the page loads, so a required field is
    red before anyone has touched it. <code>:user-invalid</code> only matches after the
    person has interacted with the control and left it in a bad state. Same styling, and
    the difference is the whole complaint people have about native form validation.
  </p>
  <?php docs_example(
      '<div class="stack-4" style="max-inline-size:26rem">' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="g-email">Email <span class="required">*</span></label>' . "\n" .
      '    <input class="input" id="g-email" type="email" required placeholder="you@example.com">' . "\n" .
      '    <p class="help">Type something that is not an email, then click away.</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Not red now. Red after you have typed in it and left',
      'stack'
  ); ?>
  <p>
    Try it. It stays neutral while you type, and turns red when you leave the field — which
    is when the feedback is useful and not before.
  </p>
</section>

<section class="stack-4">
  <h2 id="anatomy">The anatomy of a field</h2>
  <p>
    <code>.field</code> is a stack. The gap between the label, the control and the help
    text comes from the container, so none of the three carries a margin and you can add or
    remove any of them without the spacing changing.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The classes that make up a form field</caption>
      <thead>
        <tr><th scope="col">Class</th><th scope="col">On</th><th scope="col">Does</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="Class"><code>.field</code></th><td data-label="On">The wrapper</td><td data-label="Does">Stacks label, control and help with one gap</td></tr>
        <tr><th scope="row" data-label="Class"><code>.label</code></th><td data-label="On">The <code>&lt;label&gt;</code></td><td data-label="Does">A flex row, so a marker sits beside the text</td></tr>
        <tr><th scope="row" data-label="Class"><code>.required</code></th><td data-label="On">A span in the label</td><td data-label="Does">The asterisk. Visual only</td></tr>
        <tr><th scope="row" data-label="Class"><code>.optional</code></th><td data-label="On">A span in the label</td><td data-label="Does">A muted "optional" marker</td></tr>
        <tr><th scope="row" data-label="Class"><code>.help</code></th><td data-label="On">A <code>&lt;p&gt;</code> after the control</td><td data-label="Does">Small, muted supporting text</td></tr>
        <tr><th scope="row" data-label="Class"><code>.error</code></th><td data-label="On">A <code>&lt;p&gt;</code> after the control</td><td data-label="Does">The error message</td></tr>
        <tr><th scope="row" data-label="Class"><code>.is-invalid</code></th><td data-label="On">The <code>.field</code></td><td data-label="Does">Forces the invalid look from your own validation</td></tr>
        <tr><th scope="row" data-label="Class"><code>.field-row</code></th><td data-label="On">A wrapper round several fields</td><td data-label="Does">One column on a phone, two from 40rem</td></tr>
      </tbody>
    </table>
  </div>
  <p class="dx-note">
    Mark the <em>minority</em>. If most of your fields are required, mark the optional ones
    and say so once at the top; if most are optional, mark the required ones. Fifteen
    asterisks down a form is noise that stops being read by the third field.
  </p>
</section>

<section class="stack-4">
  <h2 id="server">Errors that come back from the server</h2>
  <p>
    The browser cannot know an email is already taken. For those, add
    <code>.is-invalid</code> to the field yourself — it produces the same border and ring
    as <code>:user-invalid</code> without the browser having to agree.
  </p>
  <pre class="dx-code"><code>&lt;div class="field is-invalid"&gt;
  &lt;label class="label" for="email"&gt;Email&lt;/label&gt;
  &lt;input class="input" id="email" type="email" value="ada@ledgerly.io"
         aria-invalid="true" aria-describedby="email-error"&gt;
  &lt;p class="error" id="email-error"&gt;That address is already registered.&lt;/p&gt;
&lt;/div&gt;</code></pre>
  <?php docs_example(
      '<div class="field is-invalid" style="max-inline-size:26rem">' . "\n" .
      '  <label class="label" for="g-taken">Email</label>' . "\n" .
      '  <input class="input" id="g-taken" type="email" value="ada@ledgerly.io" aria-invalid="true" aria-describedby="g-taken-err">' . "\n" .
      '  <p class="error" id="g-taken-err">That address is already registered.</p>' . "\n" .
      '</div>',
      'A server-side error, styled the same as a browser one',
      'stack'
  ); ?>
  <p>
    <code>.is-invalid</code> is the visual half and <code>aria-invalid</code> is the
    announced half. Deck can only do the first. Both, or a screen reader user gets a field
    that looks wrong to everyone but them.
  </p>
</section>

<section class="stack-4">
  <h2 id="yours">The four things Deck cannot do for you</h2>
  <p>
    Every one of these is markup rather than styling, so no framework can supply them. They
    are also the four that most often go missing.
  </p>
  <ol class="stack-3">
    <li>
      <strong>A <code>for</code> and an <code>id</code> on every label and control.</strong>
      This is what makes the label clickable, what enlarges the tap target to the whole row,
      and what makes a screen reader announce the field's name. A <code>.label</code> with
      no <code>for</code> is decoration.
    </li>
    <li>
      <strong><code>aria-describedby</code> pointing at the help or error text.</strong>
      Without it, help text is visible and unannounced — so the person who most needs the
      hint about password rules is the one who does not get it.
    </li>
    <li>
      <strong>A real <code>type</code>.</strong> <code>type="email"</code>,
      <code>type="tel"</code>, <code>type="url"</code>, <code>type="number"</code>. This
      picks the phone keyboard, enables the browser's own validation, and turns on
      autofill. <code>type="text"</code> for an email address is three features thrown
      away.
    </li>
    <li>
      <strong>An <code>autocomplete</code> value.</strong> <code>autocomplete="email"</code>,
      <code>"street-address"</code>, <code>"cc-number"</code>, <code>"one-time-code"</code>.
      It is the single highest-leverage attribute on a checkout form and it costs one word.
    </li>
  </ol>
  <pre class="dx-code"><code>&lt;div class="field"&gt;
  &lt;label class="label" for="tel"&gt;Mobile&lt;/label&gt;
  &lt;input class="input" id="tel" name="tel" type="tel"
         autocomplete="tel" aria-describedby="tel-help"&gt;
  &lt;p class="help" id="tel-help"&gt;We only text you about deliveries.&lt;/p&gt;
&lt;/div&gt;</code></pre>
</section>

<section class="stack-4">
  <h2 id="controls">Controls that are really controls</h2>
  <p>
    The switch and the choice card are one real input wearing a costume — a
    <code>&lt;label&gt;</code> wrapped round a checkbox or radio, lit up with
    <code>:has(input:checked)</code>. No state class, no JavaScript, and the value posts
    with the form because it is a checkbox.
  </p>
  <?php docs_example(
      '<div class="stack-3" style="max-inline-size:28rem">' . "\n" .
      '  <label class="switch"><input type="checkbox" checked><span>Weekly summary email</span></label>' . "\n" .
      '  <label class="check"><input type="checkbox"><span>I agree to the terms</span></label>' . "\n" .
      '  <label class="check check-card">' . "\n" .
      '    <input type="radio" name="g-plan" checked>' . "\n" .
      '    <span class="check-text"><span>Team</span><span class="check-note">Up to 20 people, shared billing.</span></span>' . "\n" .
      '  </label>' . "\n" .
      '</div>',
      'Three real inputs. Tab to them, press space',
      'stack'
  ); ?>
  <p>
    That matters more than it looks. Keyboard support, screen reader announcement, form
    serialisation, <code>required</code> on a checkbox, the browser's own validation
    bubble, and autofill all work because nothing was reimplemented with a
    <code>&lt;div&gt;</code> and a click handler. A custom switch has to rebuild every one
    of those, and usually rebuilds three.
  </p>
  <p>
    Each row is at least 44px tall, which is the minimum a finger can reliably hit. The
    visible box is smaller; the label makes up the difference.
  </p>
</section>

<section class="stack-4">
  <h2 id="submit">Submitting</h2>
  <p>
    Let the browser block the submit when the form is invalid — that is what
    <code>required</code> is for. Then use one toast for the whole round trip rather than
    stacking a spinner and a result:
  </p>
  <pre class="dx-code"><code>async function save(event) {
  event.preventDefault();
  const t = Deck.toast({ kind: 'loading', title: 'Saving…', duration: 0 });
  try {
    const res = await fetch('/api/settings', { method: 'POST', body: new FormData(event.target) });
    if (!res.ok) throw new Error(await res.text());
    t.update({ kind: 'good', title: 'Saved', duration: 4000 });
  } catch (err) {
    t.update({ kind: 'bad', title: 'Could not save', text: err.message, duration: 0 });
  }
}</code></pre>
  <p>
    <code>duration: 0</code> on the failure branch is deliberate. An error that dismisses
    itself after five seconds is an error nobody read.
  </p>
  <p>
    On a long form, <code>.form-actions-sticky</code> pins the buttons to the bottom of the
    viewport with a blur behind them and padding past the home indicator, so the save
    button is reachable without scrolling to the end.
  </p>
</section>

<section class="stack-3">
  <h2 id="dont">Things not to do</h2>
  <ul class="stack-3">
    <li>
      <strong>Do not validate on every keystroke.</strong> Telling someone their email is
      invalid while they are on the fourth character is technically true and practically
      hostile. <code>:user-invalid</code> waits, and so should your JavaScript.
    </li>
    <li>
      <strong>Do not use placeholder text as the label.</strong> It disappears the moment
      someone types, so the field has no name exactly when they are checking their answer,
      and its contrast is deliberately low. If you want the look,
      <a href="../components/float.php">the floating label</a> keeps a real label.
    </li>
    <li>
      <strong>Do not disable the submit button until the form is valid.</strong> A disabled
      button gives no reason and cannot be focused, so the person is stuck with no way to
      find out why. Let them submit and tell them what is wrong.
    </li>
    <li>
      <strong>Do not put a <code>maxlength</code> on a phone number or a name.</strong>
      Names are longer than you think, and numbers have country codes.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="next">Next</h2>
  <p>
    Every control has its own page with the full class table:
    <a href="../components/input.php">input</a>,
    <a href="../components/select.php">select</a>,
    <a href="../components/textarea.php">textarea</a>,
    <a href="../components/check.php">checkbox and radio</a>,
    <a href="../components/switch.php">switch</a>,
    <a href="../components/range.php">range</a>,
    <a href="../components/file.php">file</a> and
    <a href="../components/fieldset.php">fieldset</a>. For the composite controls, see
    <a href="../components/datepicker.php">the date picker</a> and
    <a href="../components/combobox.php">the combobox</a>.
  </p>
</section>

<?php docs_footer(); ?>
