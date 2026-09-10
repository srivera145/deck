<?php
declare(strict_types=1);

$page = [
    'path' => 'components/number.php',
    'title' => 'Number input',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .number replaces the browser\'s tiny native spinners with two real buttons that clear the touch target, wrapped around a real <input type="number">.',
    'documents' => [
        'number', 'number-sm', 'number-unit',
    ],

    'component' => 'number',
    'accounts' => [
        '23-inputs.css' => 'documented: the welded row, the centred input with native spinners removed, the stepper buttons and their states, the unit slot and the small size',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Number input</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Number input</h1>
  <p class="lede">
    The browser's own spinner arrows are about eight pixels tall, sit on top of each other,
    look different in every engine and are invisible on touch. <code>.number</code> hides
    them and puts two real buttons either side of the field — each a full control height, so
    they are actually hittable.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    For a quantity someone adjusts by small amounts and can see the whole range of: items in
    a basket, seats on a plan, a retry count. The buttons are worth their space when
    stepping is the common action.
  </p>
  <p>
    For a number that is typed rather than nudged — a price, a year, an invoice number — a
    plain <a href="input.php"><code>.input</code></a> is better. Two buttons nobody presses
    are two buttons in the way.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:14rem">' . "\n" .
      '  <label class="label" for="dx-num-a">Seats</label>' . "\n" .
      '  <div class="number">' . "\n" .
      '    <button type="button" aria-label="One fewer seat">−</button>' . "\n" .
      '    <input id="dx-num-a" type="number" value="3" min="1" max="20">' . "\n" .
      '    <button type="button" aria-label="One more seat">+</button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Press and hold either button to repeat',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <strong>The first child decrements and the last increments</strong> —
    <code>deck-extras.js</code> works that out from position. Put them the other way round
    in the DOM and the minus button adds. If your markup cannot follow that order, set
    <code>data-step="-1"</code> and <code>data-step="1"</code> explicitly and position is
    ignored.
  </p>
</section>

<section class="stack-3">
  <h2 id="behaviour">What the script does</h2>
  <ul class="stack-2">
    <li>
      <strong>Reads <code>step</code>, <code>min</code> and <code>max</code> off the
      input.</strong> They are ordinary attributes, so the same values constrain typing,
      constrain the buttons and are enforced by the browser on submit.
    </li>
    <li>
      <strong>Keeps the decimals of the step.</strong> A <code>step="0.25"</code> produces
      <code>3.25</code>, not <code>3.2500000000000004</code> — the value is written with
      <code>toFixed()</code> at the step's own precision.
    </li>
    <li>
      <strong>Disables the button at the limit.</strong> At <code>min</code> the minus
      button goes <code>disabled</code>, and it is recomputed whenever the value changes,
      including when it is typed.
    </li>
    <li>
      <strong>Repeats on hold.</strong> 420ms to start, then a step every 70ms. Long enough
      that a normal press is a single step.
    </li>
    <li>
      <strong>Fires <code>input</code> and <code>change</code>.</strong> Both bubble, so
      anything already listening to the field — a total, a validity check, a framework
      binding — sees a button press exactly as if the value had been typed.
    </li>
  </ul>
  <?php
  docs_example(
      '<div class="cluster">' . "\n" .
      '  <div class="number number-sm">' . "\n" .
      '    <button type="button" aria-label="Decrease">−</button>' . "\n" .
      '    <input type="number" value="1.00" step="0.25" min="0" max="5" aria-label="Rate">' . "\n" .
      '    <button type="button" aria-label="Increase">+</button>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="number">' . "\n" .
      '    <button type="button" aria-label="Decrease">−</button>' . "\n" .
      '    <input type="number" value="0" min="0" max="3" aria-label="Extras">' . "\n" .
      '    <button type="button" aria-label="Increase">+</button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Left: a fractional step. Right: already at its minimum, so minus is disabled',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="unit">Units and size</h2>
  <p>
    <code>.number-unit</code> is a tinted cell welded to the end of the row for the unit the
    figure is in. <code>.number-sm</code> shrinks the control and narrows the box.
  </p>
  <?php
  docs_example(
      '<div class="cluster">' . "\n" .
      '  <div class="number">' . "\n" .
      '    <button type="button" aria-label="Decrease">−</button>' . "\n" .
      '    <input type="number" value="30" step="5" min="0" aria-label="Timeout in seconds">' . "\n" .
      '    <button type="button" aria-label="Increase">+</button>' . "\n" .
      '    <span class="number-unit">sec</span>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="number number-sm">' . "\n" .
      '    <button type="button" aria-label="Decrease">−</button>' . "\n" .
      '    <input type="number" value="2" min="1" aria-label="Copies">' . "\n" .
      '    <button type="button" aria-label="Increase">+</button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The unit is in the input\'s own label as well, because the cell is decoration',
      'stack'
  );
  ?>
  <p class="text-muted">
    Width comes from <code>--number-width</code>, which defaults to <code>11rem</code> and
    is what <code>.number-sm</code> changes. It is a component knob rather than a global
    token, so set it inline or in your own layer for a one-off.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/23-inputs.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--control-h', '--control-h-sm', '--line', '--line-strong', '--surface', '--surface-2', '--surface-hover', '--brand-soft', '--focus', '--ring', '--r-sm', '--dur-1']); ?>
  <p class="text-muted">
    <code>--number-width</code> is a component-level custom property with an
    <code>11rem</code> fallback, not one of Deck's tokens — it is not in the table above
    because it does not exist until you or <code>.number-sm</code> set it.
  </p>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The input is a real <code>&lt;input type="number"&gt;</code>.</strong> It
      keeps its role, its value, its <code>min</code> and <code>max</code> in the
      accessibility tree, and its native keyboard behaviour. Up and down arrows step the
      value without any script at all — the buttons are for pointers, not a replacement for
      the keyboard.
    </li>
    <li>
      <strong>Both buttons need <code>type="button"</code>.</strong> A
      <code>&lt;button&gt;</code> with no type submits the form it is in. Every example on
      this page sets it, and this is the single most common way to break this component.
    </li>
    <li>
      <strong>Both buttons need a label.</strong> "−" and "+" are announced as "minus" and
      "plus" with no object. <code>aria-label="One more seat"</code> is the difference
      between a control and a mystery.
    </li>
    <li>
      <strong>The unit cell is decoration.</strong> <code>.number-unit</code> is a
      <code>&lt;span&gt;</code> beside the field, not part of its accessible name. Put the
      unit in the label — "Timeout in seconds" — or the reader hears a bare number.
    </li>
    <li>
      <strong>A disabled button disappears from the tab order.</strong> When the value hits
      <code>min</code>, the minus button is <code>disabled</code> and a keyboard user tabbing
      through loses it with no announcement. The value itself is still reachable and the
      arrow keys still work, so nothing is lost — but do not build a flow that depends on
      that button being focusable.
    </li>
    <li>
      <strong>Nothing announces the new value.</strong> Pressing plus changes the field
      silently unless the reader is focused in it. Where the number drives something visible
      — a total, a price — that total needs <code>aria-live</code>; the stepper cannot do
      it.
    </li>
    <li>
      <strong>16px on coarse pointers.</strong> <code>@media (pointer: coarse)</code> forces
      the input to 16px, because iOS Safari zooms the whole page when it focuses a field
      smaller than that. It is a deliberate override of your type scale.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="js-off">With JavaScript off</h2>
  <p>
    This is the one component on this page with a real gap, and it is worth stating plainly.
    The stylesheet removes the native spinners with
    <code>appearance: textfield</code>; the replacement buttons are wired by
    <code>deck-extras.js</code>. With the script blocked you get neither — the buttons are
    inert and the native arrows are gone.
  </p>
  <p>
    What survives is the field itself: it is still a real number input, so typing works,
    validation works, the form posts, and the up and down arrow keys still step the value
    because that is the browser's behaviour rather than Deck's. So the control degrades to a
    plain number field with two dead buttons beside it. Usable, and untidy.
  </p>
  <p class="text-muted">
    If a page has to work without script, use a plain
    <a href="input.php"><code>.input</code></a> with <code>type="number"</code> and let the
    browser draw its own spinners. Recorded in <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The row is a flex container, so it follows the document direction and the whole control
    mirrors: the button that is first in the DOM — the one that decrements — is drawn on the
    right. The separators between the cells are
    <code>border-inline-start</code> and <code>border-inline-end</code>, so they follow too.
    Nothing in <code>src/19-logical.css</code> is needed.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="number">' . "\n" .
      '  <button type="button" aria-label="إنقاص">−</button>' . "\n" .
      '  <input type="number" value="٣" min="1" max="20" aria-label="المقاعد">' . "\n" .
      '  <button type="button" aria-label="زيادة">+</button>' . "\n" .
      '  <span class="number-unit">مقعد</span>' . "\n" .
      '</div>',
      'Minus on the right, unit on the left — the control mirrors as a whole',
      'stack'
  );
  ?>
  <p class="text-muted">
    Digits are left-to-right in the bidirectional algorithm whatever the surrounding
    direction, so the number itself reads normally without the <code>direction: ltr</code>
    that <a href="phone.php#rtl">the phone field</a> needs.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Only the buttons animate — <code>background-color</code> and <code>color</code> over
    <?= e(api_token('--dur-1')['value'] ?? '110ms') ?> on hover — and the global reset in
    <code>src/02-reset.css</code> collapses that. The press-and-hold repeat is not motion and
    is not affected: it is the control doing what it was asked to.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.number</code> is not in <code>src/99-print.css</code>. The row prints with both
    buttons — a minus and a plus beside a figure, which on paper is meaningless furniture
    and, worse, reads as part of the value.
  </p>
  <pre class="dx-code"><code><?= e('@media print {
  .number > button, .number-unit { display: none; }
  .number { border: 0; box-shadow: none; }
}') ?></code></pre>
  <p class="text-muted">
    Keep the unit if the figure is meaningless without it — in that case hide only the
    buttons.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One control, no layer needed */
<div class="number" style="--number-width:7rem"> … </div>

@layer app.components {
  /* Left-align the figure rather than centring it */
  .number > input { text-align: start; padding-inline: var(--space-3); }

  /* A wider default across the app */
  .number { --number-width: 13rem; }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a number people type.</strong> A year, a price, an account number —
      nobody steps to 1987. Use an <a href="input.php"><code>.input</code></a>.
    </li>
    <li>
      <strong>Not for a wide range.</strong> Stepping from 0 to 900 is not a control, it is
      a punishment. Use a <a href="range.php"><code>.range</code></a>, or let them type.
    </li>
    <li>
      <strong>Not for a value with meaningful text.</strong> A phone number, a card number
      and a postcode are digits but not quantities;
      <code>type="number"</code> strips leading zeros and offers a spinner for something
      that cannot be incremented. Use <a href="phone.php"><code>.phone</code></a> or a text
      input with <code>inputmode</code>.
    </li>
    <li>
      <strong>Not where script may not run.</strong> See
      <a href="#js-off">With JavaScript off</a>.
    </li>
    <li>
      <strong>Not for a choice from a short list.</strong> One to four guests is a
      <a href="segmented.php">segmented control</a> — one press instead of three.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
