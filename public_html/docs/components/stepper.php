<?php
declare(strict_types=1);

$page = [
    'path' => 'components/stepper.php',
    'title' => 'Stepper',
    'level' => 'Beginner',
    'description' => 'Deck\'s .stepper shows where a reader is in a multi-step process: numbered markers joined by a connector, with done, current and upcoming states, and a vertical mode that switches on the container.',
    'documents' => [
        'stepper', 'stepper-auto', 'stepper-vertical',
        'step', 'step-marker', 'step-label', 'step-note',
        'is-done', 'is-current',
    ],

    'component' => 'stepper',
    'accounts' => [
        '22-nav.css' => 'documented: the strip, the step, the marker and its connector, the done and current states, and both vertical modes',
    ],
];

require __DIR__ . '/../_layout.php';

function demo_steps(string $extra = ''): string
{
    return '<ol class="stepper' . ($extra ? ' ' . $extra : '') . '">' . "\n"
        . '  <li class="step is-done">' . "\n"
        . '    <span class="step-marker"></span>' . "\n"
        . '    <span class="step-label">Details</span>' . "\n"
        . '    <span class="step-note">Ada Chen</span>' . "\n"
        . '  </li>' . "\n"
        . '  <li class="step is-current" aria-current="step">' . "\n"
        . '    <span class="step-marker"></span>' . "\n"
        . '    <span class="step-label">Billing</span>' . "\n"
        . '    <span class="step-note">Card ending 4242</span>' . "\n"
        . '  </li>' . "\n"
        . '  <li class="step">' . "\n"
        . '    <span class="step-marker"></span>' . "\n"
        . '    <span class="step-label">Confirm</span>' . "\n"
        . '  </li>' . "\n"
        . '</ol>';
}
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Stepper</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Stepper</h1>
  <p class="lede">
    <code>.stepper</code> answers one question: where am I, and how much is left. Each
    <code>.step</code> has a marker, a label and an optional note, and the markers are
    joined by a connector drawn from the marker's own
    <code>::after</code> — so the line stops at the last step without a
    <code>:last-child</code> exception in the markup.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for a process with a fixed, known number of steps that the reader moves through
    in order: checkout, onboarding, a multi-page form. It is a progress indicator, not
    navigation — though the steps behind the current one can be links back.
  </p>
  <?php docs_example(demo_steps(), 'Done, current, upcoming', 'stack'); ?>
</section>

<section class="stack-3">
  <h2 id="states">Three states</h2>
  <p>
    Everything is drawn from two classes on the step:
  </p>
  <ul class="stack-2">
    <li>
      <strong><code>.is-done</code></strong> — the marker fills with the brand colour and
      shows a tick, the connector after it turns brand, and the label goes from muted to
      full text colour. The filled connector is what makes progress readable at a glance:
      the coloured part of the line is how far you have come.
    </li>
    <li>
      <strong><code>.is-current</code></strong> — the marker gets a brand ring, and the
      label goes bold and full colour.
    </li>
    <li>
      <strong>Neither</strong> — upcoming. Muted label, hollow marker, grey connector.
    </li>
  </ul>
  <p class="dx-note text-muted">
    <code>.is-done</code> and <code>.is-current</code> are two of the nine states an
    author writes by hand. Every other <code>is-</code> class in Deck is set by
    JavaScript at runtime; these two describe where the server thinks the reader is, so
    they belong in the markup.
  </p>
</section>

<section class="stack-3">
  <h2 id="vertical">Vertical</h2>
  <p>
    Two ways to get a vertical stepper, and the difference is when.
  </p>
  <ul class="stack-2">
    <li>
      <code>.stepper-vertical</code> — always vertical. The step becomes a row, the
      marker a column, and the connector switches from a horizontal 2px bar to a vertical
      one that stretches to fill the step's height.
    </li>
    <li>
      <code>.stepper-auto</code> — horizontal where there is room, vertical below a
      breakpoint. Use this one by default: four steps with notes do not fit across a
      phone, and a horizontal stepper that squashes is unreadable.
    </li>
  </ul>
  <?php docs_example(demo_steps('stepper-vertical'), '.stepper-vertical — the connector runs down the markers', 'stack'); ?>
  <p class="text-muted">
    Both vertical modes set <code>min-inline-size: 0</code> on the step, so a long label
    or note wraps rather than pushing the strip wide — the same defence as
    <a href="split.php#min-inline-size"><code>.split &gt; *</code></a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="overflow">When there are too many</h2>
  <p>
    <code>.stepper</code> scrolls horizontally with the scrollbar hidden, so a strip that
    does not fit does not wrap into an unreadable grid. That is the right failure mode,
    but it inherits the same problem as
    <a href="scroller.php#accessibility"><code>.scroller</code></a>: with no scrollbar,
    the only cue that there are more steps is a partially visible one at the edge.
  </p>
  <p class="dx-note text-muted">
    For more than about five steps, use <code>.stepper-auto</code> and let it go vertical,
    or stop showing every step and say "Step 3 of 8" instead. A stepper's job is telling
    the reader how much is left, and a scrolling strip does that badly.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.step</code> and its parts are separate classes rather than members of the
    <code>stepper</code> component, so the completeness check on this page covers the
    three container classes only. They are documented here because a stepper is all of
    them together.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--brand', '--brand-500', '--brand-600', '--surface', '--line', '--text', '--text-muted', '--text-faint', '--text-sm', '--text-xs', '--r-full', '--space-3', '--space-5']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Use an <code>&lt;ol&gt;</code>.</strong> The steps are ordered and the count
      matters, which is exactly what an ordered list conveys. Every example here does, and
      a <code>&lt;div&gt;</code> stepper tells a screen reader nothing about how many
      steps there are.
    </li>
    <li>
      <strong><code>aria-current="step"</code> on the current one.</strong> That is the
      attribute for this exact case — not <code>aria-current="page"</code>, and not a
      class alone. Deck styles <code>.is-current</code>, so both are needed: the class for
      the look, the attribute for the announcement.
    </li>
    <li>
      <strong>The tick and the ring are not announced.</strong> They are
      <code>::before</code> content. A screen-reader user hears the labels and
      <code>aria-current</code> and nothing about which are complete — so add
      <code>.sr-only</code> text like "completed" to each done step if that matters.
    </li>
    <li>
      <strong>State is not colour alone.</strong> Done gets a tick as well as a fill,
      current gets a ring as well as bold text. The connector's fill <em>is</em> colour
      alone, but it is a summary of information the markers already carry.
    </li>
    <li>
      <strong>If steps are links, make them real links.</strong> A completed step that
      goes back should be an <code>&lt;a&gt;</code> inside the
      <code>&lt;li&gt;</code>; an upcoming step should not be focusable at all, since it
      leads nowhere yet.
    </li>
    <li>
      <strong>The strip is a scroll container with no tab stop.</strong> Same defect as
      <a href="table.php#accessibility"><code>.table-wrap</code></a> — if the steps are
      not links, a keyboard user cannot scroll to see the rest. Another reason to prefer
      <code>.stepper-auto</code>.
    </li>
    <li>
      <strong>A stepper is not a progress bar.</strong> If you want the semantics, that is
      <code>&lt;progress&gt;</code> — but do not use both for the same thing, or the state
      is announced twice.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The strip is a flex row that reverses under <code>dir="rtl"</code>, and the vertical
    modes use <code>flex-direction: column</code>, which is unaffected. The connector is
    drawn by <code>::after</code> inside the flex flow rather than positioned, so it
    follows the direction with no rule of its own.
  </p>
  <?php
  docs_example(
      '<ol dir="rtl" class="stepper">' . "\n" .
      '  <li class="step is-done"><span class="step-marker"></span><span class="step-label">التفاصيل</span></li>' . "\n" .
      '  <li class="step is-current" aria-current="step"><span class="step-marker"></span><span class="step-label">الفوترة</span></li>' . "\n" .
      '  <li class="step"><span class="step-marker"></span><span class="step-label">التأكيد</span></li>' . "\n" .
      '</ol>',
      'Progress runs from the right',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing about a stepper animates. Moving between steps is a page change or a re-render,
    not a transition, so <code>prefers-reduced-motion</code> has nothing to act on here.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.stepper</code> is not in the never-print list, so it prints — which is
    reasonable for a checkout confirmation, where "you are at step 3 of 3" is part of the
    record. The connector and markers are backgrounds and are not in
    <code>print-color-adjust: exact</code>, so on paper the states rely on the tick and
    the label weight rather than on colour.
  </p>
  <p class="text-muted">
    Being a scroll container, a wide stepper prints clipped — the same gap as the other
    horizontal scrollers in Deck.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Numbered markers instead of ticks */
  .step .step-marker::before { content: counter(list-item); }
  .stepper { counter-reset: none; }

  /* Go vertical sooner */
  @media (max-width: 60rem) {
    .stepper-auto { flex-direction: column; gap: var(--space-5); }
  }
}') ?></code></pre>
  <p class="text-muted">
    An <code>&lt;ol&gt;</code> already increments <code>list-item</code>, so numbering the
    markers needs no counter of your own — which is another small argument for using the
    right element.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not when the number of steps is unknown.</strong> A stepper's value is
      showing how much is left. If the flow branches, it will show a path the reader may
      not take.
    </li>
    <li>
      <strong>Not for more than about five steps.</strong> Past that it scrolls, and a
      scrolling progress indicator no longer indicates progress. Use "Step 3 of 8".
    </li>
    <li>
      <strong>Not as navigation.</strong> Use <a href="tabs.php">tabs</a> or
      <code>.nav-link</code>. A stepper implies an order the reader must follow.
    </li>
    <li>
      <strong>Not for indeterminate progress.</strong> That is <code>.progress</code> or a
      <code>.spinner</code> — a stepper claims to know where the end is.
    </li>
    <li>
      <strong>Not on a <code>&lt;div&gt;</code>.</strong> It works, and it throws away the
      count and the ordering that make the component mean anything.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
