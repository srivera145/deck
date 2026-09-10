<?php
declare(strict_types=1);

$page = [
    'path' => 'components/stack.php',
    'title' => 'Stack',
    'level' => 'Beginner',
    'description' => 'Deck\'s .stack is a flex column with a gap, and the reason almost nothing in Deck needs a margin. The seven-step spacing scale, why there is no step 5, and how --gap makes one class configurable.',
    'documents' => [
        'stack', 'stack-0', 'stack-1', 'stack-2', 'stack-3',
        'stack-4', 'stack-6', 'stack-8', 'center',
    ],

    'component' => 'stack',
    'accounts' => [
        '04-layout.css' => 'documented: the stack, all seven steps, and the spacing scale they come from',
    ],
];

require __DIR__ . '/../_layout.php';

/* The scale, read from the tokens rather than typed, so this table cannot
   drift from what --space-* actually resolves to. */
$steps = [
    ['stack-0', '--space-0', 'Nothing. Two elements that must touch — a table inside its wrapper.'],
    ['stack-1', '--space-1', 'Hairline. A label and the control it names, when they read as one thing.'],
    ['stack-2', '--space-2', 'Tight. A title and its subtitle.'],
    ['stack-3', '--space-3', 'Related blocks inside a card or a section.'],
    ['stack-4', '--space-4', 'The default. What you get with no step class at all.'],
    ['stack-6', '--space-6', 'Loose. Separating parts of a page.'],
    ['stack-8', '--space-8', 'Very loose. Whole sections, when .section is too much.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Stack</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Stack</h1>
  <p class="lede">
    <code>.stack</code> is a flex column with a gap. That is the whole component — five
    declarations — and it is the most useful class in Deck, because it is the reason
    almost nothing else needs a margin.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it whenever two or more things sit one above another and need space between
    them. That is most of a page.
  </p>
  <?php
  docs_example(
      '<div class="stack">' . "\n" .
      '  <h3>Export finished</h3>' . "\n" .
      '  <p>2,481 rows written to invoices-2026-03.csv.</p>' . "\n" .
      '  <button class="btn btn-primary">Download</button>' . "\n" .
      '</div>',
      'Three unrelated elements, evenly spaced, no margins anywhere',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="why">Why gap instead of margins</h2>
  <p>
    This is the argument the rest of Deck rests on, so it is worth making properly.
    Spacing something with margins means writing a rule for every element that might
    sit next to every other element. The usual approach is the lobotomised owl —
    <code>* + * { margin-block-start: 1rem }</code> — and it works, but it has four
    problems that a gap does not.
  </p>

  <div class="stack-3">
    <h3 id="why-collapse">Margins collapse; gaps do not</h3>
    <p>
      Adjacent vertical margins merge into the larger of the two, so a
      <code>1rem</code> margin below one element and a <code>2rem</code> margin above
      the next produces <code>2rem</code> of space, not <code>3rem</code>. Sometimes
      that is what you wanted. The problem is that you cannot tell by reading the
      markup, and the answer changes when a parent gains
      <code>overflow</code>, <code>display: flow-root</code> or a border.
    </p>
    <p>
      A gap is the distance between two items. It is that distance in every
      circumstance.
    </p>
  </div>

  <div class="stack-3">
    <h3 id="why-first-last">Margins need exceptions; gaps have none</h3>
    <p>
      A margin below every child puts a margin below the <em>last</em> child too, which
      is why margin-based systems are full of <code>:last-child { margin-block-end: 0
      }</code>. A gap only exists between two items, so the first and last edges are
      clean without an exception.
    </p>
    <p>
      That difference shows up the moment something is conditional. Hide the last
      element and a margin system leaves a hole; a gap closes up.
    </p>
    <?php
    docs_example(
        '<div class="split">' . "\n" .
        '  <div class="card"><div class="card-body stack-2">' . "\n" .
        '    <span class="badge">Three items</span>' . "\n" .
        '    <p>First</p><p>Second</p><p>Third</p>' . "\n" .
        '  </div></div>' . "\n" .
        '  <div class="card"><div class="card-body stack-2">' . "\n" .
        '    <span class="badge">Last one hidden</span>' . "\n" .
        '    <p>First</p><p>Second</p><p hidden>Third</p>' . "\n" .
        '  </div></div>' . "\n" .
        '</div>',
        'The card on the right has no trailing space to trim',
        'stack'
    );
    ?>
  </div>

  <div class="stack-3">
    <h3 id="why-owner">The parent owns the spacing, not the child</h3>
    <p>
      With margins, a heading carries its spacing wherever it goes — so the same
      <code>&lt;h3&gt;</code> is wrong in a card, wrong in a modal, and right only where
      the margin was chosen. Moving it means overriding it.
    </p>
    <p>
      With a gap, the container decides. The same heading is correct in all three places
      because it brings no opinion with it. That is why Deck's components almost never
      set a margin: <code>.card-body</code>, <code>.field</code>,
      <code>.list-main</code> and <code>.alert</code> are all flex columns with a gap,
      and every one of them is a <code>.stack</code> with a different default.
    </p>
  </div>

  <div class="stack-3">
    <h3 id="why-direction">A gap is direction-agnostic</h3>
    <p>
      <code>margin-block-start</code> is fine, but the moment a stack becomes a row —
      or the writing mode changes — the property is wrong and has to be swapped.
      <code>gap</code> means "between items" in whatever direction the container is
      running, so <code>.stack</code> and <code>.cluster</code> take the same
      <code>--gap</code> and neither needs an RTL rule.
    </p>
  </div>

  <p class="dx-note text-muted">
    The honest counter-argument: <code>gap</code> needs a flex or grid container, so it
    cannot space arbitrary elements in normal flow. Deck accepts that and uses a
    container almost everywhere. Where you genuinely have flow content you do not
    control — rendered Markdown, a CMS body — use <code>.prose</code>, which is the one
    place Deck does set margins on descendants.
  </p>
</section>

<section class="stack-6">
  <h2 id="scale">The spacing scale</h2>
  <p>
    Every spacing utility in Deck uses the same seven steps. A step that exists in one
    family exists in all of them, so <code>.stack-3</code>, <code>.gap-3</code>,
    <code>.p-3</code> and <code>.mt-3</code> are all the same distance.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The seven spacing steps</caption>
      <thead>
        <tr>
          <th scope="col">Class</th>
          <th scope="col">Token</th>
          <th scope="col">Value</th>
          <th scope="col">What it is for</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($steps as [$cls, $token, $use]): ?>
          <tr>
            <th scope="row" data-label="Class"><code><?= e('.' . $cls) ?></code></th>
            <td data-label="Token"><code class="dx-dim"><?= e($token) ?></code></td>
            <td data-label="Value"><code class="dx-dim"><?= e(api_token($token)['value'] ?? '—') ?></code></td>
            <td data-label="What it is for"><?= e($use) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php
  docs_example(
      '<div class="grid grid-tight">' . "\n" .
      '  <div class="card"><div class="card-body">' . "\n" .
      '    <div class="stack-1"><span class="badge">stack-1</span><div class="skeleton" style="block-size:1.5rem"></div><div class="skeleton" style="block-size:1.5rem"></div><div class="skeleton" style="block-size:1.5rem"></div></div>' . "\n" .
      '  </div></div>' . "\n" .
      '  <div class="card"><div class="card-body">' . "\n" .
      '    <div class="stack-3"><span class="badge">stack-3</span><div class="skeleton" style="block-size:1.5rem"></div><div class="skeleton" style="block-size:1.5rem"></div><div class="skeleton" style="block-size:1.5rem"></div></div>' . "\n" .
      '  </div></div>' . "\n" .
      '  <div class="card"><div class="card-body">' . "\n" .
      '    <div class="stack-6"><span class="badge">stack-6</span><div class="skeleton" style="block-size:1.5rem"></div><div class="skeleton" style="block-size:1.5rem"></div><div class="skeleton" style="block-size:1.5rem"></div></div>' . "\n" .
      '  </div></div>' . "\n" .
      '</div>',
      'The same three bars at three steps',
      'stack'
  );
  ?>

  <div class="stack-3">
    <h3 id="no-five">Why there is no step 5</h3>
    <p>
      The steps are 0, 1, 2, 3, 4, 6, 8. Five is missing on purpose. At
      <?= e(api_token('--space-5')['value'] ?? '1.25rem') ?> it is four pixels from step
      4 and four from step 6 — a difference nobody sees in a real layout, and a choice
      nobody can make on purpose.
    </p>
    <p>
      A half step between two neighbours is what people reach for when they are not
      sure, and reaching for it is how a codebase ends up with four spacings that are
      all almost the same. It is also, concretely, how this repository ended up with
      <code>.stack-5</code> in its own demo when no such class existed: the markup
      looked right, nothing errored, and the element silently fell back to the default.
      The scale is short so that the choice stays deliberate.
    </p>
    <p class="text-muted">
      The <code>--space-*</code> tokens keep their full range, 0 through 24. Tokens are
      the palette a component composes from; the utilities are the smaller set you write
      markup with. Those are different jobs, so they are different sizes.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="gap-var">How one class is configurable</h2>
  <p>
    <code>.stack</code> declares <code>gap: var(--gap, var(--space-4))</code>, and every
    step class sets nothing but <code>--gap</code>. Three consequences follow, and they
    are what make the pattern worth copying:
  </p>
  <ul class="stack-2">
    <li>
      The step classes are one declaration each, so the whole family costs almost
      nothing in the stylesheet.
    </li>
    <li>
      A value the scale does not have is <code>style="--gap:3.5rem"</code>, with no new
      class and no <code>!important</code>.
    </li>
    <li>
      <code>--gap</code> is shared with <code>.cluster</code>, <code>.grid</code>,
      <code>.split</code>, <code>.scroller</code> and <code>.center</code> — so
      <code>.stack-6</code> on a <code>.grid</code> works, because both read the same
      property.
    </li>
  </ul>
  <?php
  docs_example(
      '<div class="stack" style="--gap:3.5rem">' . "\n" .
      '  <div class="skeleton" style="block-size:1.5rem"></div>' . "\n" .
      '  <div class="skeleton" style="block-size:1.5rem"></div>' . "\n" .
      '</div>',
      'A gap that is not on the scale, without a new class',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Because the step classes only set a custom property, <code>.stack-2</code> on a
    <code>.cluster</code> does exactly what <code>.cluster-tight</code> does. That is
    convenient and slightly untidy — see
    <a href="cluster.php">cluster</a>, where the two naming schemes meet.
  </p>
</section>

<section class="stack-3">
  <h2 id="center">Centred columns</h2>
  <p>
    <code>.center</code> is <code>.stack</code> with
    <code>align-items: center</code> and <code>text-align: center</code>. It reads the
    same <code>--gap</code>, so the step classes work on it unchanged.
  </p>
  <?php
  docs_example(
      '<div class="center stack-3">' . "\n" .
      '  <span class="avatar avatar-lg">SR</span>' . "\n" .
      '  <h3>No projects yet</h3>' . "\n" .
      '  <p class="text-muted">Create one to get started.</p>' . "\n" .
      '  <button class="btn btn-primary">New project</button>' . "\n" .
      '</div>',
      'An empty state — the usual reason to reach for .center',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>align-items: center</code> also stops children from stretching to the full
    width, which is why the button above is its natural size rather than full width. In
    a plain <code>.stack</code> it would stretch.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/04-layout.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    The seven steps the utilities use, plus the two computed spacings that the page-level
    classes read.
  </p>
  <?php docs_token_table(['--space-0', '--space-1', '--space-2', '--space-3', '--space-4', '--space-6', '--space-8', '--space-section', '--space-gutter']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>A stack has no semantics.</strong> It is a <code>&lt;div&gt;</code> with
      <code>display: flex</code> and no role. Put it on a <code>&lt;ul&gt;</code>,
      <code>&lt;section&gt;</code> or <code>&lt;form&gt;</code> when the grouping means
      something; the styles do not care what element they are on.
    </li>
    <li>
      <strong>Flex changes nothing about reading order</strong> as long as you do not
      reorder. <code>flex-direction: column</code> keeps DOM order, so what a screen
      reader hears matches what a sighted reader sees.
    </li>
    <li>
      <strong>Never use <code>order</code> or <code>row-reverse</code> to fix a
      layout.</strong> Both leave the DOM order — which is the tab order and the reading
      order — pointing one way while the visual order points the other. If the order is
      wrong, the markup is wrong.
    </li>
    <li>
      <strong><code>.stack</code> on a <code>&lt;ul&gt;</code> can silence the
      list.</strong> Some Safari and VoiceOver combinations drop list semantics when
      <code>display: flex</code> is applied. Add <code>role="list"</code> back if the
      item count matters.
    </li>
    <li>
      <strong>Spacing is not a separator.</strong> A large gap groups things visually
      and says nothing to a screen reader. If two groups are genuinely distinct, use a
      heading or a <code>&lt;section&gt;</code>, not <code>.stack-8</code>.
    </li>
    <li>
      <strong>Gap does not scale with text size</strong> unless the token does. Deck's
      steps are in <code>rem</code>, so they grow with the reader's root font size — a
      stack set in pixels would not, and a page zoomed to 200% would have text touching.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Nothing to do. <code>gap</code> is not a directional property, and a column stack has
    no start or end edge to mirror. This is one of the clearest cases for gap over
    margin: <code>margin-block-start</code> would need no RTL rule either, but
    <code>margin-left</code> on a row would, and a gap never gives you the chance to
    write the wrong one.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Nothing about a stack animates or transitions, so
    <code>prefers-reduced-motion</code> changes nothing here.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> has no rule for <code>.stack</code>, which is
    deliberate — a flex column with a gap prints correctly as it is. The layout classes
    that <em>are</em> unwound for print are <code>.grid</code> and
    <code>.split</code>, which become <code>display: block</code> so that content can
    break across pages instead of being trapped in a fixed track.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One stack, no layer needed */
<div class="stack" style="--gap:3.5rem">

@layer app.components {
  /* A step the scale does not have, if you need it repeatedly */
  .stack-12 { --gap: var(--space-12); }

  /* A different default for every stack on the site */
  .stack { gap: var(--gap, var(--space-3)); }
}') ?></code></pre>
  <p>
    Prefer setting <code>--gap</code> over setting <code>gap</code>: the custom property
    is what every step class writes to, so a <code>--gap</code> override composes with
    them and a flat <code>gap</code> override silently beats them.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a row.</strong> Use <code>.cluster</code>, which wraps, or
      <code>.bar</code>, which does not. A <code>.stack</code> with
      <code>flex-direction: row</code> overridden is a cluster you have to maintain.
    </li>
    <li>
      <strong>Not for content you did not write.</strong> Rendered Markdown, a CMS body
      or a WYSIWYG field arrives as flow content with its own margins. Use
      <code>.prose</code>, which sets the vertical rhythm on descendants — the one place
      Deck accepts margins, because there is no container to put a gap on.
    </li>
    <li>
      <strong>Not for a page's outer rhythm.</strong> Use <code>.section</code>, whose
      padding is a <code>clamp()</code> that grows with the viewport. A fixed
      <code>.stack-8</code> between page sections is too much on a phone and too little
      on a monitor.
    </li>
    <li>
      <strong>Not when the items should share a row when there is room.</strong> That is
      <code>.grid</code>, which reflows on its own width. A stack is always one column.
    </li>
    <li>
      <strong>Not to fake a separator.</strong> If the reader needs to know two groups
      are different, give them a heading or an <code>&lt;hr&gt;</code>. Extra space is
      invisible to anyone not looking at the screen.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
