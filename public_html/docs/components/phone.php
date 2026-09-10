<?php
declare(strict_types=1);

$page = [
    'path' => 'components/phone.php',
    'title' => 'Phone input',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .phone welds a country select to a number field, formats digits against a per-country mask, and keeps the number reading left to right even in an RTL document.',
    'documents' => [
        'phone', 'phone-code', 'phone-country', 'phone-flag', 'phone-status', 'is-valid',
    ],

    'component' => 'phone',
    'accounts' => [
        '23-inputs.css' => 'documented: the welded row, the country cell and its bare select, the flag and dial code, the LTR-isolated number field, the valid border and the status slot',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Phone input</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Phone input</h1>
  <p class="lede">
    A country select welded to a number field. Choosing a country sets the dial code, the
    flag and the formatting mask; typing formats the digits against that mask as they
    arrive. The number itself always reads left to right, whatever direction the page runs
    in — because a phone number does.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Where you need a number you will actually dial or text: a delivery contact, two-factor
    setup, an account recovery number. The country cell is what makes it worth having — it
    is the difference between storing <code>07700 900123</code> and storing
    <code>+447700900123</code>.
  </p>
  <p>
    Where the number is a label rather than a destination — a reference someone reads back
    over the phone — a plain <a href="input.php"><code>.input</code></a> is less machinery
    for the same result.
  </p>
</section>

<section class="stack-3">
  <h2 id="markup">The markup</h2>
  <p>
    Three parts, and the data lives on the <code>&lt;option&gt;</code> elements rather than
    anywhere in Deck: <code>data-code</code> is the dial code, <code>data-flag</code> is the
    character shown, and <code>data-mask</code> is the format, where <code>#</code> is a
    digit and everything else is punctuation to insert.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:24rem">' . "\n" .
      '  <label class="label" for="dx-ph-num">Mobile number</label>' . "\n" .
      '  <div class="phone">' . "\n" .
      '    <span class="phone-country">' . "\n" .
      '      <span class="phone-flag" aria-hidden="true">🇬🇧</span>' . "\n" .
      '      <span class="phone-code">+44</span>' . "\n" .
      '      <select aria-label="Country">' . "\n" .
      '        <option data-code="+44" data-flag="🇬🇧" data-mask="##### ######">UK</option>' . "\n" .
      '        <option data-code="+1" data-flag="🇺🇸" data-mask="(###) ###-####">US</option>' . "\n" .
      '        <option data-code="+33" data-flag="🇫🇷" data-mask="# ## ## ## ##">FR</option>' . "\n" .
      '        <option data-code="+81" data-flag="🇯🇵" data-mask="##-####-####">JP</option>' . "\n" .
      '      </select>' . "\n" .
      '    </span>' . "\n" .
      '    <input id="dx-ph-num" type="tel" inputmode="tel" autocomplete="tel-national">' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Change the country, then type — the mask follows the selection',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    The <code>&lt;select&gt;</code> is stripped to nothing —
    <code>appearance: none</code>, no border, no background — and sits on top of the flag
    and code, which are the visible part. It is a real select, so it gets the platform's own
    picker on a phone and full keyboard support everywhere.
  </p>
</section>

<section class="stack-3">
  <h2 id="behaviour">Formatting and validity</h2>
  <ul class="stack-2">
    <li>
      <strong>Everything that is not a digit is discarded, then re-added from the
      mask.</strong> Paste <code>+44 7700 900123</code> and you get the digits laid back out
      in the selected country's shape.
    </li>
    <li>
      <strong>The placeholder comes from the mask</strong>, with every <code>#</code> shown
      as <code>0</code> — so the field shows the shape of the number it wants before anyone
      types.
    </li>
    <li>
      <strong><code>.is-valid</code> is added when the digit count matches the
      mask.</strong> Not a real validation: it counts <code>#</code> characters. A number
      with the right number of wrong digits is "valid".
    </li>
    <li>
      <strong>A <code>deck:change</code> event fires on blur</strong>, bubbling, with
      <code>detail.code</code>, <code>detail.number</code> (digits only) and
      <code>detail.e164</code> — the dial code and digits concatenated, which is what you
      store.
    </li>
    <li>
      <strong>No country data ships with Deck.</strong> The four options above are the
      example, not a library. Masks, names and ordering are yours, which is deliberate — a
      complete country list is a data problem with political edges and it does not belong in
      a stylesheet.
    </li>
  </ul>
  <?php
  docs_example(
      '<div class="stack-2" style="max-inline-size:24rem">' . "\n" .
      '  <div class="phone">' . "\n" .
      '    <span class="phone-country">' . "\n" .
      '      <span class="phone-flag" aria-hidden="true">🇺🇸</span>' . "\n" .
      '      <span class="phone-code">+1</span>' . "\n" .
      '      <select aria-label="Country">' . "\n" .
      '        <option data-code="+1" data-flag="🇺🇸" data-mask="(###) ###-####">US</option>' . "\n" .
      '        <option data-code="+44" data-flag="🇬🇧" data-mask="##### ######">UK</option>' . "\n" .
      '      </select>' . "\n" .
      '    </span>' . "\n" .
      '    <input type="tel" inputmode="tel" value="2025550147" aria-label="Phone number">' . "\n" .
      '    <span class="phone-status">' . "\n" .
      '      <svg class="icon text-good" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '  </div>' . "\n" .
      '  <p class="help">Complete numbers get a green border. The tick is yours to show and hide.</p>' . "\n" .
      '</div>',
      'A complete US number — .is-valid is set on the row by the script',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.phone-status</code> is an empty slot at the end of the row, centred and sized to
    hold an icon. Deck never puts anything in it; showing a tick when
    <code>.is-valid</code> appears is your code's job.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/23-inputs.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--line', '--line-strong', '--surface', '--surface-2', '--surface-hover', '--focus', '--ring', '--good-500', '--r-sm', '--text-sm', '--text-base']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The country select needs its own label.</strong> It is visually a flag and a
      dial code, and neither is its accessible name.
      <code>aria-label="Country"</code> on the select, as in every example here.
    </li>
    <li>
      <strong>The flag is decoration and should be hidden.</strong> A flag emoji is
      announced as a country name on some platforms, as "flag" on others and as nothing on
      the rest. <code>aria-hidden="true"</code>, and let the option text carry the country.
    </li>
    <li>
      <strong>Editing the middle of a number loses a digit.</strong> The script rebuilds the
      whole value on every keystroke and assigns it back, which collapses the caret to the
      end. Worse, the rebuild is positional: put the caret mid-number in
      <code>(202) 555-0147</code>, type <code>9</code>, and you get
      <code>(202) 559-5014</code> — the digit is inserted, everything after it shifts along,
      and the last one falls off the end of the mask with no warning. Measured, not
      theorised. Correcting a typo mid-number is effectively impossible; readers have to
      clear the field and start again. It is the sharpest edge on this component and is
      recorded in <code>FINDINGS.md</code>.
    </li>
    <li>
      <strong><code>.is-valid</code> is a colour and nothing else.</strong> A green border
      is invisible to a screen reader and to a good number of sighted readers. If
      completeness matters, say it in text near the field.
    </li>
    <li>
      <strong>Use the real attributes.</strong> <code>type="tel"</code> gets the phone
      keypad and the right autofill category; <code>autocomplete="tel"</code> or
      <code>autocomplete="tel-national"</code> lets the browser fill it. Deck styles the field and cannot
      supply these.
    </li>
    <li>
      <strong>Do not reject on the mask.</strong> The digit count is a hint, not a
      validation — numbers vary in length within a country, and people travel. Server-side
      validation is the real check, and a number the mask dislikes should still be
      submittable.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    This is the one Deck component that deliberately refuses to mirror its contents. The row
    flips — the country cell moves to the right — but the number field carries
    <code>direction: ltr</code> and <code>unicode-bidi: isolate</code>, so the digits and
    their punctuation stay in dialling order and cannot be re-ordered by the surrounding
    text.
  </p>
  <p>
    That is correct rather than an oversight. <code>+44</code> is not a quantity to be read
    in the reader's direction; it is a sequence you dial from the left. The
    <code>isolate</code> is what stops a bracket in <code>(###)</code> jumping to the wrong
    end of the string.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="field" style="max-inline-size:24rem">' . "\n" .
      '  <label class="label" for="dx-ph-rtl">رقم الهاتف</label>' . "\n" .
      '  <div class="phone">' . "\n" .
      '    <span class="phone-country">' . "\n" .
      '      <span class="phone-flag" aria-hidden="true">🇦🇪</span>' . "\n" .
      '      <span class="phone-code">+971</span>' . "\n" .
      '      <select aria-label="الدولة">' . "\n" .
      '        <option data-code="+971" data-flag="🇦🇪" data-mask="## ### ####">AE</option>' . "\n" .
      '        <option data-code="+20" data-flag="🇪🇬" data-mask="### ### ####">EG</option>' . "\n" .
      '      </select>' . "\n" .
      '    </span>' . "\n" .
      '    <input id="dx-ph-rtl" type="tel" inputmode="tel" value="501234567">' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The row mirrors; the number does not',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing on this component animates. The only transitions in the row belong to the
    country cell's hover background, which is instant enough not to be motion, and the focus
    ring, which is not transitioned at all. There is nothing for
    <code>prefers-reduced-motion</code> to switch off.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.phone</code> is not in <code>src/99-print.css</code>, and it prints acceptably:
    the flag, the dial code and the formatted number are all real text in the flow. The
    borders and the tinted country cell print as furniture, and the green
    <code>.is-valid</code> border is a background that most browsers drop.
  </p>
  <p class="text-muted">
    The flag is the one risk — a colour emoji printed in greyscale can come out as a dark
    block. The dial code beside it is what carries the information, which is why it is there
    as text rather than implied by the flag.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Room for a longer country code list */
  .phone > .phone-country select { max-inline-size: 7rem; }

  /* Show the tick only once the script marks the row complete */
  .phone-status { visibility: hidden; }
  .phone.is-valid .phone-status { visibility: visible; }

  /* Drop the flags entirely — the dial code is the useful half */
  .phone-flag { display: none; }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a single-country form.</strong> If every number is British, the
      country cell is a select with one option. Use an
      <a href="input.php"><code>.input</code></a> with <code>type="tel"</code> and put
      "+44" in the label.
    </li>
    <li>
      <strong>Not as validation.</strong> The mask counts digits. It does not know a real
      number from a made-up one of the right length, and it will reject valid numbers whose
      length it did not anticipate.
    </li>
    <li>
      <strong>Not for a number people edit rather than enter.</strong> A stored number
      someone comes back to change one digit of is the worst case for this component — see
      <a href="#accessibility">Accessibility</a>. Editing mid-number drops a digit.
    </li>
    <li>
      <strong>Not without script.</strong> With <code>deck-extras.js</code> blocked the
      country select changes nothing, no mask is applied and the flag and code stay on
      whatever was rendered. The field still posts, so the value is not lost — but it is
      unformatted and the visible dial code may be wrong.
    </li>
    <li>
      <strong>Not for any other kind of number.</strong> A card number, a postcode or a
      reference has a different shape and different privacy rules. Use
      <a href="input.php"><code>.input</code></a>, or
      <a href="number.php"><code>.number</code></a> for genuine quantities.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
