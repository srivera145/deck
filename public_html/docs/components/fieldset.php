<?php
declare(strict_types=1);

$page = [
    'path' => 'components/fieldset.php',
    'title' => 'Fieldset',
    'level' => 'Beginner',
    'description' => 'Deck\'s .fieldset is a real <fieldset> with a border and a flex column inside, and its <legend> is the only way to give a group of radios an accessible name.',
    'documents' => [
        'fieldset',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Fieldset</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Fieldset</h1>
  <p class="lede">
    <code>.fieldset</code> goes on a real <code>&lt;fieldset&gt;</code>: a border, a
    radius, and a flex column with a
    <?= e(api_token('--space-4')['value'] ?? '1rem') ?> gap inside. It exists for one
    reason that no amount of styling can substitute for — its
    <code>&lt;legend&gt;</code> is the accessible name of the group, and a group of
    radios has no other way to get one.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it whenever several controls answer <em>one</em> question. A radio group is the
    clearest case: each option has its own label, but nothing says what is being chosen
    until a legend does.
  </p>
  <?php
  docs_example(
      '<fieldset class="fieldset" style="max-inline-size:26rem">' . "\n" .
      '  <legend>Billing period</legend>' . "\n" .
      '  <label class="check">' . "\n" .
      '    <input type="radio" name="fs-period" checked>' . "\n" .
      '    <span>Monthly</span>' . "\n" .
      '  </label>' . "\n" .
      '  <label class="check">' . "\n" .
      '    <input type="radio" name="fs-period">' . "\n" .
      '    <span>Annual — two months free</span>' . "\n" .
      '  </label>' . "\n" .
      '</fieldset>',
      'Without the legend, a screen reader announces "Monthly, radio button" and nothing else',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="legend">Why the legend cannot be a heading instead</h2>
  <p>
    A <code>&lt;h3&gt;</code> above a group of radios looks identical and does nothing.
    Headings label a <em>region</em> of a document; a legend labels a <em>group of form
    controls</em>, and screen readers use it differently — the legend is announced again
    with each option, so a reader arrowing through the group hears "Billing period,
    Monthly" and "Billing period, Annual" rather than losing the context after the first.
  </p>
  <p>
    That repetition is the point, and it is the thing a heading cannot do. It is also why
    a legend should be short: it is read many times.
  </p>
  <p class="dx-note text-muted">
    <code>.label</code> above a radio group is the same mistake in a different hat. It
    looks like a group label and is a label for nothing — <code>for</code> can only point
    at one control, and a group is not a control.
  </p>
</section>

<section class="stack-3">
  <h2 id="styling">Styling a legend</h2>
  <p>
    <code>&lt;legend&gt;</code> is one of the last elements in HTML that resists CSS. It
    is positioned by the browser, breaks the fieldset's border where it sits, and ignores
    most layout properties. Deck does very little to it deliberately —
    <code>padding-inline</code>, a small size, a muted colour and a heavier weight:
  </p>
  <pre class="dx-code"><code><?= e('.fieldset > legend {
  padding-inline: var(--space-2);
  font-size: var(--text-sm);
  font-weight: 600;
  color: var(--text-muted);
}') ?></code></pre>
  <p>
    The <code>padding-inline</code> is what stops the border running into the text on
    either side. Everything else is typography.
  </p>
  <p class="text-muted">
    If you need a legend that behaves like an ordinary block — to put a control beside it,
    or to lay it out with flex — the usual approach is
    <code>&lt;fieldset&gt;</code> with a visually hidden legend for the accessible name,
    plus a <code>&lt;div&gt;</code> that carries the visible heading. Two elements, but
    both do a job they are capable of.
  </p>
</section>

<section class="stack-3">
  <h2 id="nested">Fields inside a fieldset</h2>
  <p>
    A fieldset is a flex column, so <a href="field.php"><code>.field</code></a> children
    space themselves without margins — the same arrangement as a
    <a href="stack.php"><code>.stack</code></a>, with a
    <?= e(api_token('--space-4')['value'] ?? '1rem') ?> gap rather than the stack's
    default.
  </p>
  <?php
  docs_example(
      '<fieldset class="fieldset" style="max-inline-size:26rem">' . "\n" .
      '  <legend>Billing address</legend>' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="fs-street">Street</label>' . "\n" .
      '    <input class="input" id="fs-street">' . "\n" .
      '  </div>' . "\n" .
      '  <div class="field-row">' . "\n" .
      '    <div class="field">' . "\n" .
      '      <label class="label" for="fs-city">City</label>' . "\n" .
      '      <input class="input" id="fs-city">' . "\n" .
      '    </div>' . "\n" .
      '    <div class="field">' . "\n" .
      '      <label class="label" for="fs-post">Postcode</label>' . "\n" .
      '      <input class="input" id="fs-post">' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '</fieldset>',
      'A .field-row inside a fieldset behaves exactly as it would anywhere else',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>min-inline-size: 0</code> is set on the fieldset for the same reason it is set
    on <code>.field</code> and <code>.split &gt; *</code>: a flex or grid child's minimum
    is its content, and a wide child would otherwise push the fieldset past its
    container.
  </p>
</section>

<section class="stack-3">
  <h2 id="disabled">Disabling a whole group</h2>
  <p>
    <code>&lt;fieldset disabled&gt;</code> disables every control inside it, including
    ones added later. That is a genuinely useful HTML feature with no CSS equivalent —
    and it is the browser doing it, so the controls are removed from the tab order
    properly rather than just looking grey.
  </p>
  <?php
  docs_example(
      '<fieldset class="fieldset" disabled style="max-inline-size:26rem">' . "\n" .
      '  <legend>Shipping — unavailable for digital orders</legend>' . "\n" .
      '  <div class="field">' . "\n" .
      '    <label class="label" for="fs-addr">Address</label>' . "\n" .
      '    <input class="input" id="fs-addr" value="Not required">' . "\n" .
      '  </div>' . "\n" .
      '  <label class="check">' . "\n" .
      '    <input type="checkbox">' . "\n" .
      '    <span>Signature on delivery</span>' . "\n" .
      '  </label>' . "\n" .
      '</fieldset>',
      'One attribute, every control inside it disabled',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Deck does not style <code>.fieldset[disabled]</code> — the controls inside get their
    own disabled treatment, but the border and legend look unchanged. Adding
    <code>opacity</code> to the group would make the whole thing clearer, and it is
    recorded in <code>FINDINGS.md</code>. Note that a disabled fieldset's controls do not
    submit, which is often exactly what you want.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.fieldset</code> has no <code>.fieldset-*</code> variants, so the extractor
    does not treat it as a component root and there is no completeness check on this
    page. Its second rule, <code>.fieldset &gt; legend</code>, is an element selector.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--line', '--r-md', '--space-2', '--space-4', '--space-5', '--text-sm', '--text-muted']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>This is an accessibility component first.</strong> Everything else about it
      is a border. A radio group without a legend is the most common serious form defect
      there is, and no class can fix it — the element has to be a
      <code>&lt;fieldset&gt;</code> and it has to contain a
      <code>&lt;legend&gt;</code>.
    </li>
    <li>
      <strong>The legend must be the first child.</strong> HTML requires it, and browsers
      that tolerate it elsewhere will not associate it. Deck's selector is
      <code>.fieldset &gt; legend</code>, so a legend nested inside a wrapper is not
      styled either — which is a useful early warning.
    </li>
    <li>
      <strong>Keep it short.</strong> The legend is announced with every control in the
      group, so a sentence becomes a sentence repeated five times.
    </li>
    <li>
      <strong>Do not use one for every field.</strong> A fieldset around a single input
      gives that input two names — the legend and its label — and a screen reader reads
      both. Fieldsets are for groups.
    </li>
    <li>
      <strong><code>disabled</code> is real and thorough.</strong> It disables descendants
      the browser adds later too, and removes them from the tab order. The one exception
      is a control inside the first <code>&lt;legend&gt;</code>, which stays enabled by
      specification.
    </li>
    <li>
      <strong>A hidden legend still works.</strong> If the visible design has no room for
      one, <code>.sr-only</code> on the legend keeps the accessible name while removing
      it from the screen. That is far better than omitting it.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The legend's <code>padding-inline</code> is logical and the border is symmetrical, so
    the legend moves to the right edge under <code>dir="rtl"</code> with no extra rules —
    the browser positions it at the start of the block, and the start follows the writing
    direction.
  </p>
  <?php
  docs_example(
      '<fieldset dir="rtl" class="fieldset" style="max-inline-size:26rem">' . "\n" .
      '  <legend>فترة الفوترة</legend>' . "\n" .
      '  <label class="check"><input type="radio" name="fs-rtl" checked><span>شهري</span></label>' . "\n" .
      '  <label class="check"><input type="radio" name="fs-rtl"><span>سنوي</span></label>' . "\n" .
      '</fieldset>',
      'The legend follows the writing direction',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing about a fieldset animates or transitions, so
    <code>prefers-reduced-motion</code> changes nothing here. The controls inside it have
    their own behaviour — see <a href="check.php#motion">checkbox and radio</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> lists <code>.fieldset</code> with the other surfaces —
    a <code>#bbb</code> hairline, no radius, a white background, and
    <code>break-inside: avoid</code>. That last one matters most here: a group of
    controls split across two sheets loses the connection between its legend and half its
    options, which is the printed version of the defect this component exists to prevent.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* No box, just the grouping and the legend */
  .fieldset-bare { border: 0; padding: 0; }
  .fieldset-bare > legend { padding-inline: 0; }

  /* Make a disabled group visibly disabled */
  .fieldset[disabled] { opacity: .6; }
}') ?></code></pre>
  <p class="text-muted">
    <code>.fieldset-bare</code> is worth having: the accessibility benefit is entirely in
    the element and the legend, so you can drop the border and keep all of it. Deck ships
    the bordered version because a visible group is usually the point of grouping.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not around a single control.</strong> Two names for one input. Use
      <a href="field.php"><code>.field</code></a> with a
      <code>&lt;label&gt;</code>.
    </li>
    <li>
      <strong>Not as a generic bordered box.</strong> Use
      <a href="card.php"><code>.card</code></a> or <code>.panel</code>. A fieldset
      around non-form content is invalid and adds a group semantic that means nothing.
    </li>
    <li>
      <strong>Not for visual sectioning of a long form.</strong> If the grouping is about
      reading order rather than about one question, use headings and
      <a href="stack.php"><code>.stack</code></a>. A page of six bordered boxes is harder
      to scan than a page with six headings.
    </li>
    <li>
      <strong>Not with a legend you need to lay out.</strong> The element resists CSS. Use
      a hidden legend plus a visible heading rather than fighting it.
    </li>
    <li>
      <strong>Not nested more than one deep.</strong> Nested fieldsets are legal and
      announced as nested groups, which gets confusing quickly — a reader hears both
      legends before every option.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
