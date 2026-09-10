<?php
declare(strict_types=1);

$page = [
    'path' => 'components/badge.php',
    'title' => 'Badge',
    'level' => 'Beginner',
    'description' => 'Deck\'s .badge: a small status pill sized in em so it tracks the text beside it, with four tones, a solid variant, a leading dot, and the print rule that keeps its fill.',
    'documents' => [
        'badge', 'badge-bad', 'badge-brand', 'badge-dot', 'badge-good',
        'badge-solid', 'badge-warn',
    ],

    'component' => 'badge',
    'accounts' => [
        '07-components.css' => 'documented: the badge, its five tone variants and the leading dot',
        '99-print.css'      => 'documented: badges keep their fill on paper — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';

$tones = [
    ['badge', 'Neutral', 'The default. Grey fill, muted text, hairline border.'],
    ['badge-brand', 'Brand', 'Brand-tinted. For a label that is a category rather than a status.'],
    ['badge-good', 'Good', 'Paid, shipped, passing, active.'],
    ['badge-warn', 'Warn', 'Pending, expiring, needs attention but nothing is broken.'],
    ['badge-bad', 'Bad', 'Failed, overdue, rejected.'],
    ['badge-solid', 'Solid', 'Filled with the brand colour. Highest emphasis; use it once.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Badge</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Badge</h1>
  <p class="lede">
    <code>.badge</code> is a small status pill. Its padding is
    <code>.18em .55em</code> and its font size is
    <?= e(api_token('--text-xs')['value'] ?? '.8125rem') ?>, so it scales with whatever
    text it sits beside instead of needing a size variant for every context. Put one in
    a heading and it grows; put one in a table cell and it shrinks.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a badge for a short, categorical value attached to something else: a status, a
    count, a label. The word inside it should be one or two words that a reader can take
    in without reading — "Paid", "Draft", "3 open".
  </p>
  <p>
    <code>white-space: nowrap</code> is set deliberately. A badge that wraps to two lines
    has stopped being a badge, so if the text is long enough to wrap, the text is wrong
    rather than the badge.
  </p>
  <?php
  docs_example(
      '<h3 class="cluster cluster-tight">Invoice INV-2291 <span class="badge badge-good">Paid</span></h3>' . "\n" .
      '<p class="text-sm cluster cluster-tight">Same badge in small text <span class="badge badge-good">Paid</span></p>',
      'One class, two sizes — the em-based padding does it',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="tones">Tones</h2>
  <p>
    Six looks. Each sets <code>background</code>, <code>color</code> and
    <code>border-color</code> and changes nothing else, so a tone never resizes a badge
    or moves what is around it.
  </p>
  <?php foreach ($tones as [$cls, $label, $blurb]): ?>
    <div class="stack-2">
      <h3 id="t-<?= e($cls) ?>"><?= e($label) ?></h3>
      <p class="text-muted"><?= e($blurb) ?></p>
      <?php
      $c = $cls === 'badge' ? 'badge' : "badge {$cls}";
      docs_example('<span class="' . $c . '">' . $label . '</span>');
      ?>
    </div>
  <?php endforeach; ?>
</section>

<section class="stack-3">
  <h2 id="dot">Leading dot</h2>
  <p>
    <code>.badge-dot</code> adds a <code>::before</code> circle in
    <code>currentColor</code>, which means it takes the tone's text colour automatically
    and no extra markup is involved. It reads as a status light rather than a label.
  </p>
  <?php
  docs_example(
      '<span class="badge badge-dot badge-good">Live</span>' . "\n" .
      '<span class="badge badge-dot badge-warn">Degraded</span>' . "\n" .
      '<span class="badge badge-dot badge-bad">Down</span>' . "\n" .
      '<span class="badge badge-dot">Unknown</span>'
  );
  ?>
  <p class="dx-note text-muted">
    The dot is decorative and carries no meaning of its own. Colour alone is not a
    status — the word beside it is what a colour-blind reader and a screen reader both
    rely on. Never ship <code>.badge-dot</code> with no text in it.
  </p>
</section>

<section class="stack-3">
  <h2 id="in-context">In context</h2>
  <p>
    Badges are almost always inside something else. The two places they earn their keep
    are the end of a table row and beside a title.
  </p>
  <?php
  docs_example(
      '<div class="table-wrap">' . "\n" .
      '  <table class="table">' . "\n" .
      '    <thead><tr><th scope="col">Invoice</th><th scope="col">Status</th></tr></thead>' . "\n" .
      '    <tbody>' . "\n" .
      '      <tr><th scope="row">INV-2291</th><td><span class="badge badge-good">Paid</span></td></tr>' . "\n" .
      '      <tr><th scope="row">INV-2292</th><td><span class="badge badge-warn">Pending</span></td></tr>' . "\n" .
      '      <tr><th scope="row">INV-2293</th><td><span class="badge badge-bad">Overdue</span></td></tr>' . "\n" .
      '    </tbody>' . "\n" .
      '  </table>' . "\n" .
      '</div>',
      'A status column is the badge\'s natural home',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    Generated from <code>src/07-components.css</code> by
    <code>tools/docs/extract.mjs</code>.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    The badge reads the status ramps directly. Each tone uses the <code>100</code> step
    for its fill and the <code>700</code> step for its text, which is the pairing Deck
    uses everywhere a tint needs readable text on it.
  </p>
  <?php docs_token_table(['--surface-2', '--text-muted', '--line', '--r-xs', '--text-xs', '--brand-soft', '--brand-soft-text', '--good-100', '--good-700', '--warn-100', '--warn-700', '--bad-100', '--bad-700', '--brand-600', '--text-on-brand']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>A badge is a <code>&lt;span&gt;</code> with no role.</strong> A screen
      reader reads the text and nothing more. That is usually right — "Paid" in a status
      column reads as "Paid" — but it also means the badge does not announce
      <em>what</em> it is a status of. Give the column a header, or add
      <code>.sr-only</code> text.
    </li>
    <li>
      <strong>Colour is never the message.</strong> The tone is a second signal on top of
      the word. A badge whose only content is a colour has no accessible name at all.
    </li>
    <li>
      <strong>Not a live region.</strong> Changing a badge's text with JavaScript
      announces nothing. If a status change needs to be heard, put it in a
      <code>.toast</code> or an element with <code>aria-live</code>.
    </li>
    <li>
      <strong>Contrast.</strong> The <code>100</code>/<code>700</code> pairing clears
      4.5:1 in both themes. <code>.badge-solid</code> uses
      <code>--text-on-brand</code>, which is computed against the brand rather than
      hard-coded, so it stays readable if the brand hue is changed.
    </li>
    <li>
      <strong>It is not interactive.</strong> There is no focus style, because there is
      nothing to focus. A badge that needs to be clicked is a <code>.chip</code> or a
      <code>.btn-sm</code>.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The badge uses <code>gap</code> and <code>padding</code> shorthands with no
    side-specific values, so <code>dir="rtl"</code> needs nothing. The dot moves to the
    right-hand side on its own because it is a flex child rather than a positioned
    element.
  </p>
  <?php
  docs_example(
      '<span dir="rtl" class="badge badge-dot badge-good">مدفوعة</span>' . "\n" .
      '<span dir="rtl" class="badge badge-warn">قيد الانتظار</span>'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing about a badge animates or transitions, so
    <code>prefers-reduced-motion</code> changes nothing here. This section exists because
    every component page has it; for this component there is genuinely nothing to say.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Print stylesheets usually strip backgrounds, which would turn every badge into plain
    text and lose the status. <code>src/99-print.css</code> lists <code>.badge</code>
    among the elements that keep their fill, with
    <code>print-color-adjust: exact</code>, so a printed invoice still shows Paid as
    green.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <p>
    The badge has no local custom properties; the tones set
    <code>background</code> and <code>color</code> directly. Override in
    <code>app.components</code>, which beats every Deck layer regardless of specificity.
  </p>
  <pre class="dx-code"><code><?= e('@layer app.components {
  .badge {
    border-radius: var(--r-full);
    text-transform: uppercase;
    letter-spacing: .04em;
  }
}') ?></code></pre>
  <p>
    Adding a tone of your own is four declarations and needs no Deck class:
    <code>background</code>, <code>color</code>, <code>border-color: transparent</code>,
    and nothing else.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for something clickable.</strong> Use <code>.chip</code>, which has a
      hover state, a focus ring and a 44px target, or a <code>.btn-sm</code>. A badge
      with a click handler is an unfocusable control.
    </li>
    <li>
      <strong>Not for a removable filter.</strong> That is <code>.chip</code> with a
      remove button. Badges have no affordance for being dismissed.
    </li>
    <li>
      <strong>Not for a count on an icon.</strong> Use <code>.indicator</code>, which
      positions itself over its parent. A badge is inline and will sit beside the icon
      rather than on it.
    </li>
    <li>
      <strong>Not for a sentence.</strong> <code>white-space: nowrap</code> means a long
      badge pushes its container wide instead of wrapping, and on a phone that is a
      horizontal scrollbar. If it is a sentence, it is an <code>.alert</code>.
    </li>
    <li>
      <strong>Not as a heading-level label.</strong> A badge in an
      <code>&lt;h1&gt;</code> inherits the heading's size through <code>em</code> and
      becomes very large. That is the em sizing working correctly; it is still usually
      not what you want. Wrap it in a <code>&lt;small&gt;</code> or use
      <code>.text-sm</code> on the badge's parent.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
