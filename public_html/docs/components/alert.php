<?php
declare(strict_types=1);

$page = [
    'path' => 'components/alert.php',
    'title' => 'Alert',
    'level' => 'Beginner',
    'description' => 'Deck\'s .alert: a flex row with a logical start border in one of four tones, a title and body, an optional icon, and the ARIA rules that decide whether a screen reader interrupts.',
    'documents' => [
        'alert', 'alert-bad', 'alert-body', 'alert-good', 'alert-info',
        'alert-title', 'alert-warn',
    ],

    'component' => 'alert',
    'accounts' => [
        '07-components.css' => 'documented: the alert, its title and body, the four tones and the icon alignment',
        '99-print.css'      => 'documented: alerts keep a hairline border and avoid splitting across pages — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';

$tones = [
    ['alert', 'Neutral', 'No tone. A note that carries no urgency at all.'],
    ['alert-info', 'Info', 'Something the reader should know before continuing.'],
    ['alert-good', 'Good', 'Confirmation that something worked.'],
    ['alert-warn', 'Warn', 'A consequence the reader should understand first.'],
    ['alert-bad', 'Bad', 'Something failed, or is about to.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Alert</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Alert</h1>
  <p class="lede">
    <code>.alert</code> is a block of prose the reader is meant to stop and read. It is a
    flex row with a <?= e(api_token('--space-3')['value'] ?? '.75rem') ?> gap, a
    three-pixel <code>border-inline-start</code> that carries the tone, and a fill that
    is a tint of the same colour. The start border is logical, so it moves to the right
    edge under <code>dir="rtl"</code> without a second rule.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use an alert when the message is a sentence and it is <em>about the page</em> — a
    warning before a destructive action, an explanation of why a form is empty, the
    result of something the reader just did. It sits in the flow of the page and stays
    there.
  </p>
  <p>
    An alert is not a notification. If the message is transient, arrives from the
    server, or should stack with others, that is <code>.toast</code>. The difference is
    whether the message is part of the page or an event that happened to it.
  </p>
  <?php
  docs_example(
      '<div class="alert alert-warn">' . "\n" .
      '  <div>' . "\n" .
      '    <p class="alert-title">This project has no backups</p>' . "\n" .
      '    <p class="alert-body">Nightly backups start once a payment method is on file.</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Title and body, one tone',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="tones">Tones</h2>
  <p>
    Five looks. Each sets the fill, the text colour and
    <code>border-inline-start-color</code>, and sets the rest of the border to
    <code>transparent</code> so the tone reads as one coloured edge rather than a box in
    a colour.
  </p>
  <?php foreach ($tones as [$cls, $label, $blurb]): ?>
    <div class="stack-2">
      <h3 id="t-<?= e($cls) ?>"><?= e($label) ?></h3>
      <p class="text-muted"><?= e($blurb) ?></p>
      <?php
      $c = $cls === 'alert' ? 'alert' : "alert {$cls}";
      docs_example(
          '<div class="' . $c . '">' . "\n" .
          '  <div>' . "\n" .
          '    <p class="alert-title">' . $label . '</p>' . "\n" .
          '    <p class="alert-body">The border on the starting edge carries the tone.</p>' . "\n" .
          '  </div>' . "\n" .
          '</div>',
          '',
          'stack'
      );
      ?>
    </div>
  <?php endforeach; ?>
</section>

<section class="stack-3">
  <h2 id="icon">With an icon</h2>
  <p>
    <code>.alert &gt; .icon</code> gets <code>flex: 0 0 auto</code> so the icon never
    shrinks, and <code>margin-block-start: 1px</code> so it sits on the title's optical
    baseline rather than the line box's top. Nothing else is needed — the icon is a
    direct child and the flex row does the rest.
  </p>
  <?php
  docs_example(
      '<div class="alert alert-bad">' . "\n" .
      '  <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#alert-triangle"></use></svg>' . "\n" .
      '  <div>' . "\n" .
      '    <p class="alert-title">Payment failed</p>' . "\n" .
      '    <p class="alert-body">The card ending 4242 was declined. Try another card.</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The icon is a direct child, not inside the text wrapper',
      'stack'
  );
  ?>
  <p class="text-muted">
    The icon is decorative and carries <code>aria-hidden="true"</code>. It repeats what
    the tone and the title already say; a screen reader should not read "warning
    triangle, Payment failed".
  </p>
</section>

<section class="stack-3">
  <h2 id="body">Title and body</h2>
  <p>
    <code>.alert-title</code> is a weight change and two pixels of bottom margin — not a
    heading. <code>.alert-body</code> is
    <?= e(api_token('--text-sm')['value'] ?? '.9375rem') ?> and muted, with
    <code>min-inline-size: 0</code> so a long unbroken string inside it cannot push the
    alert wider than its container.
  </p>
  <p>
    <code>.alert :is(.alert-body, p)</code> resets the colour back to
    <code>inherit</code> at 90% opacity. That is why the body text inside a tone stays
    in the tone's colour family rather than reverting to the page's muted grey, which
    would look like a mistake on a coloured fill.
  </p>
  <?php
  docs_example(
      '<div class="alert alert-info">' . "\n" .
      '  <div>' . "\n" .
      '    <p class="alert-title">Scheduled maintenance</p>' . "\n" .
      '    <p class="alert-body">Exports will be unavailable on Sunday between 02:00 and 04:00 UTC. Everything else keeps working.</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The body inherits the tone rather than reverting to grey',
      'stack'
  );
  ?>
  <p class="text-muted">
    Both parts are optional. An alert with one line of text and no
    <code>.alert-title</code> is perfectly normal and is what most alerts should be.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/07-components.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    Each tone uses the <code>100</code> step of a status ramp for its fill, the
    <code>500</code> step for the edge, and the <code>700</code> step for text.
  </p>
  <?php docs_token_table(['--surface-2', '--line', '--ink-400', '--r-md', '--space-4', '--text-sm', '--brand-soft', '--brand-soft-text', '--brand-500', '--good-100', '--good-500', '--good-700', '--warn-100', '--warn-500', '--warn-700', '--bad-100', '--bad-500', '--bad-700']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Deck sets no ARIA role, and that is deliberate.</strong> An alert that is
      part of the page when it loads should have no role at all — a
      <code>role="alert"</code> on page load interrupts a screen reader before it has
      read anything else.
    </li>
    <li>
      <strong>Add <code>role="alert"</code> only when it appears in response to
      something.</strong> A validation summary that shows up after a failed submit
      should interrupt. Add the role at the moment the element is inserted, or the
      announcement will not fire.
    </li>
    <li>
      <strong>For a message that should wait its turn</strong>, use
      <code>role="status"</code> — polite rather than assertive. "Draft saved" belongs
      here; "Payment failed" belongs in <code>role="alert"</code>.
    </li>
    <li>
      <strong>The tone is not announced.</strong> Nothing about
      <code>.alert-bad</code> reaches a screen reader. The word "failed" has to be in
      the text, or the message is silent about its own severity.
    </li>
    <li>
      <strong>Colour contrast.</strong> The <code>100</code>/<code>700</code> pairing
      clears 4.5:1 in both themes. The three-pixel edge is decoration on top of that,
      not the only cue.
    </li>
    <li>
      <strong>The title is not a heading.</strong> <code>.alert-title</code> is a
      paragraph in a heavier weight. If the alert needs to appear in the document
      outline, use a real <code>&lt;h2&gt;</code>–<code>&lt;h4&gt;</code> and put
      <code>.alert-title</code> on it.
    </li>
    <li>
      <strong>Dismissible alerts need a real button.</strong> Deck ships no close
      button for <code>.alert</code>. If you add one, it needs
      <code>aria-label</code> and a 44px target — <code>.btn-icon.btn-ghost.btn-sm</code>
      gives both.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The tone edge is <code>border-inline-start</code>, so it moves to the right under
    <code>dir="rtl"</code> with no extra stylesheet. This is the clearest small example
    of why Deck is written in logical properties: the same rule that draws a left edge
    in English draws a right edge in Arabic, and there is no second file to keep in
    step.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="alert alert-bad">' . "\n" .
      '  <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#alert-triangle"></use></svg>' . "\n" .
      '  <div>' . "\n" .
      '    <p class="alert-title">فشل الدفع</p>' . "\n" .
      '    <p class="alert-body">تم رفض البطاقة المنتهية بـ ٤٢٤٢.</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The tone edge follows the writing direction',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    An alert has no transition and no animation of its own, so
    <code>prefers-reduced-motion</code> changes nothing about it. If you animate one in,
    do it in <code>@media (prefers-reduced-motion: no-preference)</code> so the
    animation is never declared for a reader who has asked for less of it, rather than
    declared and then cancelled.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> gives alerts the same treatment as cards and panels: a
    <code>#bbb</code> hairline, no radius, a white background, and
    <code>break-inside: avoid</code>. The tone fill is dropped, so a printed alert reads
    as a bordered note rather than a coloured band — the words have to carry it, which
    is the same reason the tone is never the only signal on screen.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <p>
    No local custom properties; the tones set their colours directly. Override in
    <code>app.components</code>.
  </p>
  <pre class="dx-code"><code><?= e('@layer app.components {
  .alert {
    border-inline-start-width: 0;
    border-radius: var(--r-lg);
    padding: var(--space-5);
  }
}') ?></code></pre>
  <p>
    A tone of your own is three declarations — <code>background</code>,
    <code>color</code>, <code>border-inline-start-color</code> — plus
    <code>border-color: transparent</code> for the other three edges.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong><code>.alert</code> versus <code>.toast</code>.</strong> An alert is part
      of the page and stays. A toast appears, stacks, and leaves. If the reader can
      scroll away and the message should not follow them, it is an alert; if it reports
      something that just finished, it is a toast.
    </li>
    <li>
      <strong>Not for field validation.</strong> Use <code>.error</code> beneath the
      field and <code>.is-invalid</code> on the input. An alert at the top of a form
      that says "3 fields have errors" is a summary, and it needs the per-field messages
      as well, not instead.
    </li>
    <li>
      <strong>Not for a confirmation the reader must answer.</strong> That is
      <code>.modal</code>, which takes focus and blocks. An alert can be scrolled past.
    </li>
    <li>
      <strong>Not for a permanent explanation.</strong> An alert that is on the page
      every time is not an alert; readers stop seeing it within a week. Use
      <code>.dx-note</code>-style prose, a <code>.help</code> line, or say it in the
      copy.
    </li>
    <li>
      <strong>Not for one word.</strong> "Paid" is a <code>.badge</code>. The alert's
      padding and border are sized for a sentence, and a one-word alert is a very large
      box around very little.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
