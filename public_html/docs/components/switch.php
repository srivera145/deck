<?php
declare(strict_types=1);

$page = [
    'path' => 'components/switch.php',
    'title' => 'Switch',
    'level' => 'Beginner',
    'description' => 'Deck\'s .switch draws a real checkbox as a 46x28 track with a sliding knob, using inset-inline-start so it mirrors under dir="rtl". When it is a switch and when it is a checkbox.',
    'documents' => [
        'switch',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Switch</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Switch</h1>
  <p class="lede">
    <code>.switch</code> goes on a <code>&lt;label&gt;</code> wrapping a real
    <code>&lt;input type="checkbox"&gt;</code>, drawn as a 46&times;28 track with a 22px
    knob. Underneath it is a checkbox, with everything a checkbox does — space to toggle,
    form submission, the <code>:checked</code> pseudo-class. Only the painting changes.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use a switch when the change <strong>takes effect immediately</strong>: a setting
    that saves as it is flipped, a feature toggled on, a filter applied at once. The
    reader flips it and it is done.
  </p>
  <p>
    If the change is collected and applied later — when they press Save — that is a
    checkbox, not a switch. The affordance is a promise about when something happens, and
    a switch that needs a Save button afterwards is breaking it.
  </p>
  <?php
  docs_example(
      '<div class="stack-2" style="max-inline-size:26rem">' . "\n" .
      '  <label class="switch">' . "\n" .
      '    <input type="checkbox" checked>' . "\n" .
      '    <span>Two-factor authentication</span>' . "\n" .
      '  </label>' . "\n" .
      '  <label class="switch">' . "\n" .
      '    <input type="checkbox">' . "\n" .
      '    <span>Weekly digest</span>' . "\n" .
      '  </label>' . "\n" .
      '</div>',
      'Each row is a label, so the whole line toggles it',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="how">How it is drawn</h2>
  <p>
    <code>appearance: none</code> on the input, then a
    <code>--r-full</code> radius to make the track a pill, and an
    <code>::after</code> for the knob. The knob is positioned with
    <code>inset-block-start: 3px</code> and <code>inset-inline-start: 3px</code>, and
    moves with <code>translate: 18px 0</code> on <code>:checked</code>.
  </p>
  <p>
    <code>translate</code> rather than changing <code>inset-inline-start</code> is
    deliberate: a transform is composited, so the knob slides without triggering layout
    on every toggle. It is also why the movement can be transitioned smoothly.
  </p>
  <p class="dx-note text-muted">
    <code>inset-inline-start</code> is the logical property doing real work here. Under
    <code>dir="rtl"</code> the knob starts on the right, and the same
    <code>translate: 18px 0</code> moves it toward the centre and across — see
    <a href="#rtl">Right to left</a>.
  </p>
  <?php
  docs_example(
      '<div class="cluster">' . "\n" .
      '  <label class="switch"><input type="checkbox"><span>Off</span></label>' . "\n" .
      '  <label class="switch"><input type="checkbox" checked><span>On</span></label>' . "\n" .
      '  <label class="switch"><input type="checkbox" disabled><span>Disabled</span></label>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="rows">In a settings list</h2>
  <p>
    The most common home for a switch. The row is
    <code>min-block-size: var(--tap)</code> —
    <?= e(api_token('--tap')['value'] ?? '44px') ?> — and the label wraps the input, so
    the whole line is the target.
  </p>
  <?php
  docs_example(
      '<div class="list" style="max-inline-size:30rem">' . "\n" .
      '  <div class="list-row">' . "\n" .
      '    <div class="list-main">' . "\n" .
      '      <span class="list-title">Email notifications</span>' . "\n" .
      '      <span class="list-sub">A summary when an export finishes.</span>' . "\n" .
      '    </div>' . "\n" .
      '    <label class="switch"><input type="checkbox" checked><span class="sr-only">Email notifications</span></label>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="list-row">' . "\n" .
      '    <div class="list-main">' . "\n" .
      '      <span class="list-title">Desktop alerts</span>' . "\n" .
      '      <span class="list-sub">Requires browser permission.</span>' . "\n" .
      '    </div>' . "\n" .
      '    <label class="switch"><input type="checkbox"><span class="sr-only">Desktop alerts</span></label>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The switch keeps a name even when the visible text is in the row',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Note the <code>.sr-only</code> text inside each switch. Without it the control has no
    accessible name — the row's title is a sibling, not a label — and a screen reader
    announces "checkbox, not checked" with no idea what for. This is the most common
    defect in a settings list, and it is invisible on screen.
  </p>
</section>

<section class="stack-3">
  <h2 id="role">role="switch"</h2>
  <p>
    Deck does not add <code>role="switch"</code> for you. Added to the input, it changes
    what a screen reader announces from "checked"/"unchecked" to "on"/"off", which reads
    better for a setting.
  </p>
  <p>
    It is left to you because the role is a claim about behaviour. A switch inside a form
    that is saved with a button really is a checkbox — it collects a value — and
    labelling it a switch would be a lie about when the change takes effect. Add the role
    when the toggle acts immediately.
  </p>
  <pre class="dx-code"><code><?= e('<label class="switch">
  <input type="checkbox" role="switch" checked>
  <span>Two-factor authentication</span>
</label>') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.switch</code> has no <code>.switch-*</code> variants, so the extractor does
    not treat it as a component root and there is no completeness check on this page. Its
    other rules — <code>.switch input</code>, <code>.switch input::after</code> — are
    element selectors rather than classes.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--tap', '--ink-0', '--ink-300', '--brand-600', '--focus', '--r-full', '--shadow-2', '--space-3', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>It is a real checkbox.</strong> Space toggles it, it is announced as a
      checkbox (or a switch, with the role), it submits with the form, and
      <code>:checked</code> is the browser's own state rather than a class.
    </li>
    <li>
      <strong>Wrapping is the labelling.</strong> The <code>&lt;label&gt;</code> contains
      the input, so the association cannot break and the whole row is the target as a
      consequence.
    </li>
    <li>
      <strong>A switch with no visible text still needs a name.</strong> In a settings
      list the description is usually beside the control, not inside the label. Add
      <code>.sr-only</code> text inside the label, or point the input at the title with
      <code>aria-labelledby</code>.
    </li>
    <li>
      <strong>The state is not conveyed by colour alone</strong> — the knob moves, which
      is a positional cue that survives any colour vision. That is worth keeping in mind
      if you restyle it: a switch that only changes colour is not accessible.
    </li>
    <li>
      <strong>Focus is a real outline.</strong> <code>:focus-visible</code> draws
      <code>2px solid var(--focus)</code> with a 2px offset on the track itself.
    </li>
    <li>
      <strong>Touch target.</strong> The track is 46&times;28, below the
      <?= e(api_token('--tap')['value'] ?? '44px') ?> minimum on its own — but the row is
      <code>min-block-size: var(--tap)</code> and the label wraps the input, so the
      target is the row. A switch used outside a label would not clear it.
    </li>
    <li>
      <strong>Immediate effect needs feedback.</strong> If flipping a switch saves to a
      server, say so — a <code>.toast</code>, or a status line in an
      <code>aria-live</code> region. A switch that silently fails is worse than a
      checkbox with a Save button.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The row is a flex line with a gap, so the track moves to the right of the text on its
    own. The knob's offset is <code>inset-inline-start</code>, so it starts at the right
    end of the track and <code>translate: 18px 0</code> carries it across — the same
    declaration, mirrored by the property rather than by a second rule.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="stack-2">' . "\n" .
      '  <label class="switch"><input type="checkbox" checked><span>المصادقة الثنائية</span></label>' . "\n" .
      '  <label class="switch"><input type="checkbox"><span>الملخص الأسبوعي</span></label>' . "\n" .
      '</div>',
      'The knob starts on the right and travels the other way',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The track transitions its background and the knob transitions its
    <code>translate</code> over
    <?= e(api_token('--dur-2')['value'] ?? '200ms') ?> with a spring easing. Under
    <code>prefers-reduced-motion: reduce</code> the global reset collapses both to
    <code>.01ms</code>, so the knob jumps rather than slides.
  </p>
  <p class="text-muted">
    The state is never hidden from a reader who has asked for less motion — only the
    travel is removed. The knob still ends up at the other end, which is the part that
    carries the meaning.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> gives <code>.switch input</code> a
    <code>1px solid #000</code> border so the track is visible on paper. The knob's
    position is what shows the state, and it prints where it is — so an on switch and an
    off switch are still distinguishable.
  </p>
  <p class="text-muted">
    It is a thinner treatment than checkboxes get, which are given an explicit filled
    style for the checked state. A printed page of switches relies on the reader noticing
    which side each knob is on.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A larger switch — three numbers have to move together */
  .switch input { inline-size: 58px; block-size: 34px; }
  .switch input::after { inline-size: 28px; block-size: 28px; }
  .switch input:checked::after { translate: 24px 0; }
}') ?></code></pre>
  <p class="dx-note text-muted">
    That is the one awkward thing about this component: the track size, the knob size and
    the travel distance are three separate numbers that have to agree, and nothing checks
    that they do. A version driven by a single <code>--switch-size</code> would be
    better, and it is recorded in <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not in a form with a Save button.</strong> That is a
      <a href="check.php"><code>.check</code></a>. The switch affordance says "this is
      already done", and it should not be used when it is not.
    </li>
    <li>
      <strong>Not for one of several options.</strong> A switch is on or off. One choice
      from three is a radio group or <code>.segmented</code>.
    </li>
    <li>
      <strong>Not for a destructive setting without confirmation.</strong> A switch is
      one tap and immediate. "Delete all exports after 30 days" needs a
      <code>.modal</code> in front of it, or it needs to be a checkbox that is saved
      deliberately.
    </li>
    <li>
      <strong>Not for something that might fail.</strong> If the change goes to a server
      that can reject it, the switch will show the new state before the truth is known.
      Either revert it visibly on failure, or use a control with an explicit Save.
    </li>
    <li>
      <strong>Not for agreeing to terms.</strong> Consent needs an unambiguous,
      deliberate act, and a checkbox is the convention readers and regulators both expect.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
