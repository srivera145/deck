<?php
declare(strict_types=1);

$page = [
    'path' => 'components/indicator.php',
    'title' => 'Indicator',
    'level' => 'Beginner',
    'description' => 'Deck\'s .indicator is a status dot; .with-indicator positions a count or a dot over the corner of whatever it wraps. Both are silent to a screen reader, which is the thing to know.',
    'documents' => [
        'indicator', 'indicator-bad', 'indicator-badge', 'indicator-badge-brand', 'indicator-badge-dot',
        'indicator-brand', 'indicator-good', 'indicator-lg', 'indicator-ring', 'indicator-warn',
        'with-indicator',
    ],

    'component' => 'indicator',
    'accounts' => [
        '22-nav.css' => 'documented: the dot, its four tones, the ring and large size, and the corner badge positioned by .with-indicator',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Indicator</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Indicator</h1>
  <p class="lede">
    Two related things. <code>.indicator</code> is a status dot that sits in the flow of
    text. <code>.with-indicator</code> wraps something — an icon button, an avatar — and
    positions an <code>.indicator-badge</code> over its corner. Neither says anything to a
    screen reader, which is the most important sentence on this page.
  </p>
</header>

<section class="stack-3">
  <h2 id="dot">The status dot</h2>
  <p>
    A small circle in one of four tones, sized to sit beside text. Use it where a
    <a href="badge.php"><code>.badge</code></a> would be too heavy — a row of services
    with an up-or-down state, a presence marker beside a name.
  </p>
  <?php
  docs_example(
      '<div class="stack-2">' . "\n" .
      '  <p class="cluster cluster-tight"><span class="indicator indicator-good"></span> API — operational</p>' . "\n" .
      '  <p class="cluster cluster-tight"><span class="indicator indicator-warn"></span> Exports — degraded</p>' . "\n" .
      '  <p class="cluster cluster-tight"><span class="indicator indicator-bad"></span> Webhooks — down</p>' . "\n" .
      '  <p class="cluster cluster-tight"><span class="indicator indicator-brand"></span> Beta — enrolled</p>' . "\n" .
      '</div>',
      'The word beside the dot is what carries the meaning',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.indicator-lg</code> makes it 11px, and <code>.indicator-ring</code> adds a
    two-pixel ring in the surface colour — which is what keeps a dot legible when it sits
    over an image or another colour.
  </p>
</section>

<section class="stack-3">
  <h2 id="badge">The corner badge</h2>
  <p>
    <code>.with-indicator</code> is the positioning wrapper: <code>position:
    relative</code> and <code>display: inline-flex</code>, so whatever it contains keeps
    its own size and the badge can be placed against it.
  </p>
  <p>
    Inside it, <code>.indicator-badge</code> is a count,
    <code>.indicator-badge-dot</code> is a dot with no number, and
    <code>.indicator-badge-brand</code> is the brand-filled variant.
  </p>
  <?php
  docs_example(
      '<span class="with-indicator">' . "\n" .
      '  <button class="btn btn-icon btn-ghost" aria-label="Notifications, 3 unread">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#bell"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '  <span class="indicator-badge" aria-hidden="true">3</span>' . "\n" .
      '</span>' . "\n" .
      '<span class="with-indicator">' . "\n" .
      '  <button class="btn btn-icon btn-ghost" aria-label="Messages, unread">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#mail"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '  <span class="indicator-badge indicator-badge-dot" aria-hidden="true"></span>' . "\n" .
      '</span>' . "\n" .
      '<span class="with-indicator">' . "\n" .
      '  <span class="avatar">AC</span>' . "\n" .
      '  <span class="indicator-badge indicator-badge-dot" aria-hidden="true"></span>' . "\n" .
      '</span>',
      'The count is in the button\'s label, and the badge is hidden',
      'cluster'
  );
  ?>
  <p class="dx-note text-muted">
    Look at what the examples do: the number is in the <em>button's</em>
    <code>aria-label</code> — "Notifications, 3 unread" — and the visible badge is
    <code>aria-hidden</code>. That is the pattern, and the reason for it is in
    <a href="#accessibility">Accessibility</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/22-nav.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--good-500', '--warn-500', '--bad-500', '--brand-500', '--brand-600', '--text-on-brand', '--surface', '--r-full']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Both are invisible to a screen reader.</strong> A dot is an empty
      <code>&lt;span&gt;</code> with a background; a badge is a number floating beside a
      control. Neither is part of any accessible name unless you put it there.
    </li>
    <li>
      <strong>Put the count in the control's label.</strong>
      <code>aria-label="Notifications, 3 unread"</code> on the button, and
      <code>aria-hidden="true"</code> on the badge. One thing to announce, and it stays
      correct because the label is what you update.
    </li>
    <li>
      <strong>A count that changes should be announced.</strong> Updating the label alone
      is silent. If new notifications arrive while the reader is on the page, that needs an
      <code>aria-live</code> region — the badge cannot do it.
    </li>
    <li>
      <strong>Colour is the entire signal on a dot.</strong> Green, amber and red are the
      only difference between the four tones, and they are indistinguishable to some
      readers. The examples pair every dot with a word for exactly that reason.
    </li>
    <li>
      <strong>The badge overlaps its control.</strong> It sits on the corner of the
      button, so it can cover part of the target and — for a two- or three-digit count —
      extend past it. Check that a large number does not push outside a
      <a href="bar.php">bar</a> or a <a href="table.php">table cell</a>.
    </li>
    <li>
      <strong>Contrast on a ring.</strong> <code>.indicator-ring</code> is a surface-coloured
      ring, so a dot on an unusual background gets a halo in the wrong colour — the same
      caveat as <a href="avatar.php#stack">the avatar stack</a>.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The badge is positioned with logical insets, so it moves to the opposite corner under
    <code>dir="rtl"</code> without a rule — which is correct: a notification count belongs
    on the side the reader's eye finishes on.
  </p>
  <?php
  docs_example(
      '<span dir="rtl" class="with-indicator">' . "\n" .
      '  <button class="btn btn-icon btn-ghost" aria-label="الإشعارات، ٣ غير مقروءة">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#bell"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '  <span class="indicator-badge" aria-hidden="true">٣</span>' . "\n" .
      '</span>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing here animates. If you want a new notification to pulse, that is
    <a href="motion.php"><code>.ping</code></a>, which is reduced-motion guarded — and it
    is worth remembering that a pulsing badge is motion in the corner of the reader's eye
    for as long as the count is non-zero.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Neither is in <code>src/99-print.css</code>, and both are backgrounds — so a printed
    dot disappears entirely and a printed badge prints its number with no fill. For a
    status page that means the dots vanish and only the words remain, which is survivable
    precisely because the words were required anyway.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A larger badge for two-digit counts */
  .indicator-badge { min-inline-size: 1.4em; padding-inline: .35em; }

  /* A dot tone Deck does not ship */
  .indicator-info { background: var(--ink-400); }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a status with a name.</strong> "Paid", "Overdue" — that is a
      <a href="badge.php"><code>.badge</code></a>, which shows the word.
    </li>
    <li>
      <strong>Not as the only sign of something new.</strong> A badge appearing is silent.
      If it matters, announce it.
    </li>
    <li>
      <strong>Not for a count over about 99.</strong> The badge grows past its control.
      Cap it — "99+" — as every platform does.
    </li>
    <li>
      <strong>Not inline where a word would do.</strong> A dot in a sentence is a
      decoration the reader has to decode; the word beside it is doing the work anyway.
    </li>
    <li>
      <strong>Not on a control with no accessible name.</strong> The badge cannot supply
      one, and an icon button without a label is already the defect.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
