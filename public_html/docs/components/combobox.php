<?php
declare(strict_types=1);

$page = [
    'path' => 'components/combobox.php',
    'title' => 'Combobox',
    'level' => 'Advanced',
    'description' => 'Deck\'s .combo wraps a real hidden <select> that stays in sync, so a filtering autocomplete with tokens and grouping still posts with an ordinary form and needs nothing on the server.',
    'documents' => [
        'combo', 'combo-check', 'combo-clear', 'combo-control', 'combo-count',
        'combo-create', 'combo-empty', 'combo-group', 'combo-hint', 'combo-input',
        'combo-list', 'combo-loading', 'combo-option', 'combo-option-main', 'combo-option-sub',
        'combo-option-trail', 'combo-sep', 'combo-toggle', 'combo-token', 'combo-tokens',
        'is-above', 'is-active', 'is-disabled', 'is-invalid', 'is-open',
    ],

    'component' => 'combo',
    'accounts' => [
        '11-combobox.css' => 'documented: the control, the token row, the list, option rows, the match highlight, groups and every state',
        '25-anchor.css'   => 'documented: the list escapes to the top layer where anchor positioning exists — the Escaping the modal section',
        '99-print.css'    => 'documented: the list never prints and the control prints as a value — the Printing section',
        '16-motion.css'   => 'documented: an invalid combo shakes once, sharing the rule with .input — the Reduced motion section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Combobox</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Combobox</h1>
  <p class="lede">
    <code>.combo</code> is a filtering select: type to narrow a long list, pick one or
    several, with tokens for multiple selection and grouped options. The design decision
    that matters is underneath — it wraps a <strong>real
    <code>&lt;select&gt;</code></strong> that stays in the DOM and stays in sync, so a
    form post works with no server-side handling of its own.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it when a <a href="select.php">select</a> would be unusable: more than about
    twenty options, options the reader knows the name of but not the position of, or
    multiple selection. Below that, a native select is better — it needs no JavaScript and
    gets the platform's own picker on a phone.
  </p>
  <pre class="dx-code"><code><?= e('<div class="combo" data-deck-combo data-multi data-create
     data-placeholder="Search repositories">
  <select hidden multiple name="repos">
    <option value="deck">deck</option>
    <option value="keel">keel</option>
    <option value="helm">helm</option>
  </select>
</div>') ?></code></pre>
  <p class="text-muted">
    <code>data-multi</code> turns on tokens (or take it from the select's own
    <code>multiple</code>), <code>data-create</code> allows values that are not in the
    list, and <code>data-placeholder</code> sets the input's placeholder.
  </p>
</section>

<section class="stack-3">
  <h2 id="select">The hidden select is the point</h2>
  <p>
    Most combobox components replace the native control entirely and then need a hidden
    input, a serialiser and something on the server to read it. Deck keeps the
    <code>&lt;select&gt;</code> — hidden, but present, with its
    <code>name</code> and its options — and mirrors every change back into it.
  </p>
  <p>
    Three consequences follow, and they are the reason to prefer this shape:
  </p>
  <ul class="stack-2">
    <li>
      <strong>An ordinary form post works.</strong> No JSON, no hidden field, no special
      case in the handler. The server sees a select.
    </li>
    <li>
      <strong>Server-side rendering is trivial.</strong> Render the
      <code>&lt;select&gt;</code> with the right options selected and the combobox comes
      up correct — the initial state is in the markup rather than in a script.
    </li>
    <li>
      <strong>It degrades.</strong> With JavaScript off the reader gets a plain select.
      Not the filtering, but a working control with the real options in it.
    </li>
  </ul>
  <p class="dx-note text-muted">
    That last one only holds if you leave the <code>hidden</code> attribute off and let
    Deck hide the select itself. As written above — <code>&lt;select hidden&gt;</code> —
    a reader without JavaScript gets nothing. Which is right depends on whether you
    support that case; the source comment shows <code>hidden</code>, so the default is
    the enhanced-only version.
  </p>
</section>

<section class="stack-3">
  <h2 id="control">The control</h2>
  <p>
    <code>.combo-control</code> is the box, styled to match
    <a href="input.php"><code>.input</code></a> — same border, radius, focus ring — so a
    combobox in a form does not look like a different species.
    <code>.combo-input</code> is the text field inside it, and
    <code>.combo-toggle</code> the chevron, which rotates 180 degrees on
    <code>.is-open</code>.
  </p>
  <p>
    Focus is handled twice: <code>.combo:has(.combo-input:focus) .combo-control</code>
    and <code>.combo-control:focus-within</code>. The first covers the input, the second
    anything else focusable inside — a token's remove button, the clear control.
  </p>
  <?php
  docs_example(
      '<div class="field" style="max-inline-size:26rem">' . "\n" .
      '  <label class="label" for="dx-combo">Repositories</label>' . "\n" .
      '  <div class="combo">' . "\n" .
      '    <div class="combo-control">' . "\n" .
      '      <span class="combo-token"><span>deck</span><button aria-label="Remove deck">×</button></span>' . "\n" .
      '      <span class="combo-token"><span>keel</span><button aria-label="Remove keel">×</button></span>' . "\n" .
      '      <input class="combo-input" id="dx-combo" role="combobox" aria-expanded="false" placeholder="Search repositories">' . "\n" .
      '      <button class="combo-toggle" aria-label="Show options">' . "\n" .
      '        <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#chevron-down"></use></svg>' . "\n" .
      '      </button>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Static markup, so the tokens hold still. Normally deck.js builds all of this.',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>.combo-tokens</code> is <code>display: contents</code>, so the tokens are laid
    out by the control's own flex row rather than forming a nested box. That is what lets
    a token and the input share a line and wrap together.
  </p>
</section>

<section class="stack-3">
  <h2 id="options">Option rows</h2>
  <p>
    <code>.combo-option</code> has the same three-part shape as a
    <a href="list.php"><code>.list-row</code></a>: <code>.combo-option-main</code>
    takes the space and is allowed to shrink, <code>.combo-option-sub</code> is a second
    muted line, <code>.combo-option-trail</code> sits at the end and never shrinks.
    <code>.combo-check</code> is the tick, pushed to the end and revealed by
    <code>[aria-selected="true"]</code>.
  </p>
  <?php
  docs_example(
      '<div class="combo is-open" style="max-inline-size:26rem">' . "\n" .
      '  <div class="combo-list" style="position:static;display:block">' . "\n" .
      '    <div class="combo-group">Recent</div>' . "\n" .
      '    <button class="combo-option is-active" role="option" aria-selected="true">' . "\n" .
      '      <span class="combo-option-main">' . "\n" .
      '        <span><mark>de</mark>ck</span>' . "\n" .
      '        <span class="combo-option-sub">One stylesheet, no build step</span>' . "\n" .
      '      </span>' . "\n" .
      '      <span class="combo-option-trail">CSS</span>' . "\n" .
      '      <svg class="icon combo-check" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check"></use></svg>' . "\n" .
      '    </button>' . "\n" .
      '    <button class="combo-option" role="option" aria-selected="false">' . "\n" .
      '      <span class="combo-option-main"><span><mark>de</mark>ck-icons</span></span>' . "\n" .
      '      <span class="combo-option-trail">SVG</span>' . "\n" .
      '    </button>' . "\n" .
      '    <div class="combo-sep"></div>' . "\n" .
      '    <button class="combo-option" role="option" aria-disabled="true">' . "\n" .
      '      <span class="combo-option-main"><span>archived-repo</span></span>' . "\n" .
      '    </button>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The list shown inline rather than floating, so it can be inspected',
      'stack'
  );
  ?>
  <p>
    The matched substring is wrapped in <code>&lt;mark&gt;</code> by
    <code>deck.js</code>, and Deck restyles it: no yellow background, just a heavier
    weight and a tinted underline. A highlight that inverts the colours of half a word is
    harder to read than the word was.
  </p>
</section>

<section class="stack-3">
  <h2 id="two-states">Two highlight states, deliberately different</h2>
  <p>
    An option can be <em>active</em> — where the keyboard is — and
    <em>selected</em> — chosen. They are different things and they need different looks,
    or a reader arrowing through a list cannot tell where they are.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Option states</caption>
      <thead><tr><th scope="col">State</th><th scope="col">Look</th></tr></thead>
      <tbody>
        <tr><th scope="row" data-label="State"><code>.is-active</code></th><td data-label="Look">Hover background — the keyboard cursor</td></tr>
        <tr><th scope="row" data-label="State"><code>[aria-selected="true"]</code></th><td data-label="Look">Brand tint, heavier weight, tick visible</td></tr>
        <tr><th scope="row" data-label="State">Both</th><td data-label="Look">A blend of the two, so the cursor is visible on a selected row</td></tr>
        <tr><th scope="row" data-label="State"><code>[aria-disabled="true"]</code></th><td data-label="Look">Faded, <code>pointer-events: none</code></td></tr>
      </tbody>
    </table>
  </div>
  <p class="text-muted">
    <code>.is-active</code> is used for both mouse hover and keyboard position, which
    keeps the two from fighting: moving the mouse over the list moves the same highlight
    the arrow keys move.
  </p>
</section>

<section class="stack-3">
  <h2 id="anchor">Escaping the modal</h2>
  <p>
    By default <code>.combo-list</code> is a positioned sibling, and
    <code>.is-above</code> flips it when there is no room below. That works until the
    combobox is inside something with <code>overflow: hidden</code> — a
    <a href="modal.php">modal</a>, a <a href="card.php">card</a>, a scroll container —
    at which point the list is clipped. It is the bug that bites every combobox
    eventually.
  </p>
  <p>
    <code>src/25-anchor.css</code> fixes it where the browser has anchor positioning: the
    list goes in the top layer, sized to the control with
    <code>anchor-size(inline)</code> and flipped with
    <code>position-try-fallbacks: flip-block</code>. No clipping ancestor can reach it.
  </p>
  <p class="text-muted">
    Where anchor positioning is missing, the sibling positioning and
    <code>.is-above</code> remain — so the component works everywhere and works
    <em>better</em> where the platform allows.
  </p>
</section>

<section class="stack-3">
  <h2 id="extras">Create, count, loading, empty</h2>
  <ul class="stack-2">
    <li><code>.combo-create</code> — the "Add …" row for a value not in the list, shown with <code>data-create</code>.</li>
    <li><code>.combo-count</code> — a "+3 more" pill when tokens overflow the control.</li>
    <li><code>.combo-loading</code> — a placeholder row while options are fetched.</li>
    <li><code>.combo-empty</code> — "No matches", which should say what was searched for.</li>
    <li><code>.combo-hint</code> — a line under the control, for a format note or a shortcut.</li>
    <li><code>.combo-clear</code> — removes every selection at once.</li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/11-combobox.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--surface-hover', '--line', '--line-strong', '--ink-400', '--focus', '--ring', '--brand', '--brand-soft', '--brand-soft-text', '--brand-300', '--bad-500', '--text-faint', '--text-muted', '--text-xs', '--text-2xs', '--r-sm', '--space-1', '--space-2', '--space-3', '--dur-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The ARIA is built by the script, not by you.</strong>
      <code>deck.js</code> gives the input <code>role="combobox"</code>,
      <code>aria-autocomplete="list"</code> and an <code>aria-expanded</code> it keeps in
      step, and each option <code>role="option"</code> with
      <code>aria-selected</code>. That is the right division: the roles promise behaviour,
      and the thing that implements the behaviour should own them.
    </li>
    <li>
      <strong>Focus stays in the input.</strong> Arrow keys move
      <code>.is-active</code> rather than focus, which is what the combobox pattern
      requires — the reader keeps typing while the highlight moves. The link between them
      is <code>aria-activedescendant</code>.
    </li>
    <li>
      <strong>The control needs a label</strong> like any other. A
      <code>&lt;label for&gt;</code> pointing at <code>.combo-input</code>, or
      <code>aria-labelledby</code>. The placeholder is not one.
    </li>
    <li>
      <strong>Token remove buttons need names.</strong> "×" is announced as "times" or
      skipped. <code>aria-label="Remove deck"</code>, naming the token — as the example
      above does.
    </li>
    <li>
      <strong>Announce the result count.</strong> Filtering a list of two hundred down to
      three is silent unless you put "3 results" in an <code>aria-live</code> region.
      Deck does not do this for you, and it is the most commonly missed part of the
      pattern.
    </li>
    <li>
      <strong><code>.combo-empty</code> should name the search.</strong> "No matches" is
      less useful than "No repositories match 'deckk'" — and it tells a reader who
      mistyped what they actually typed.
    </li>
    <li>
      <strong>The match highlight is not announced</strong>, and does not need to be —
      <code>&lt;mark&gt;</code> is presentational here. It is styled without a background
      change specifically so it does not reduce contrast.
    </li>
    <li>
      <strong>Selected and active must look different.</strong> They do, and the combined
      state has a third look. If you restyle these, keep all three distinguishable, or
      keyboard users lose their place.
    </li>
    <li>
      <strong>Options are 40px-ish rows.</strong> Comfortable enough on touch, and the
      list scrolls rather than the page — but a long list on a phone is still a lot of
      scrolling inside a small box. Consider a <a href="sheet.php">sheet</a> for the
      mobile case.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The tick is pushed with <code>margin-inline-start: auto</code>, groups and options use
    <code>padding-inline</code>, and the anchored list uses logical
    <code>position-area</code> keywords. Everything mirrors without a rule of its own.
  </p>
  <p class="text-muted">
    The chevron's <code>rotate: 180deg</code> on open is symmetrical about the vertical
    axis, so it does not need mirroring either — unlike the
    <a href="select.php#rtl">select's gradient chevron</a>, which does.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The list animates in with <code>dk-combo-in</code>, tokens animate as they are added,
    and the chevron rotates. All are collapsed to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>.
  </p>
  <p>
    <code>src/16-motion.css</code> also shakes an invalid combobox once — the selector is
    <code>.field.is-invalid:not(.was-shaken) :is(.input, .textarea, .select,
    .combo-control)</code>, so a combobox and an input misbehave identically. It is
    declared inside <code>@media (prefers-reduced-motion: no-preference)</code>, so it is
    never declared rather than declared and cancelled.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.combo-list</code> is in the never-print list, and
    <code>.combo-control</code> is in the shared form-control rule — a plain
    <code>#999</code> border on white, with <code>min-block-size: auto</code>. So a
    printed combobox shows the chosen tokens in a box and no dropdown, which is the right
    reading of a filled-in form.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A taller list */
  .combo-list { max-block-size: 24rem; }

  /* Bring back a conventional match highlight */
  .combo-option mark {
    background: var(--warn-100);
    text-decoration: none;
  }

  /* Tokens that wrap onto their own line rather than sharing with the input */
  .combo-tokens { display: flex; flex-wrap: wrap; inline-size: 100%; }
}') ?></code></pre>
  <p class="text-muted">
    That last one changes <code>display: contents</code> to a real box, which is the one
    override here with a structural effect — the tokens stop participating in the
    control's flex row and become a block above the input.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a short list.</strong> Under about twenty options a native
      <a href="select.php">select</a> is better: no script, platform picker on a phone, and
      nothing to get wrong.
    </li>
    <li>
      <strong>Not for one of three.</strong> Use
      <a href="segmented.php"><code>.segmented</code></a> or radios — showing all the
      options costs nothing and saves a click.
    </li>
    <li>
      <strong>Not as a search box.</strong> A combobox picks from a known set. Searching a
      corpus is a <a href="input.php#search"><code>.search</code></a> field and a results
      page.
    </li>
    <li>
      <strong>Not for free text with suggestions.</strong> That is
      <code>&lt;input list&gt;</code> with a <code>&lt;datalist&gt;</code>, which is
      native and needs nothing. Reach for <code>data-create</code> only when the new value
      has to join the real option set.
    </li>
    <li>
      <strong>Not without announcing the filtered count.</strong> The most important part
      of the interaction — how many results are left — is the part Deck does not provide,
      and a combobox without it is silent for a screen-reader user.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
