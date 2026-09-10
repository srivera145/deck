<?php
declare(strict_types=1);

/**
 * Layout — the page that changes how people use Deck.
 *
 * An argument, not a catalogue. The component pages document each primitive;
 * this one is here to talk someone out of the habit they arrived with, which
 * is spacing things by putting a margin on them.
 *
 * The counts in the "Deck barely uses margin" section were produced by parsing
 * src/*.css rather than estimated, because a claim like that is worth nothing
 * if the reader greps and finds it is off by a factor of three.
 */

$page = [
    'path' => 'guides/layout.php',
    'title' => 'Stop writing margins',
    'level' => 'Beginner',
    'description' => "Spacing belongs to the container, not the item. How to lay out a page with eight Deck primitives and no margins at all, and the four bugs that removes.",
];

require __DIR__ . '/../_layout.php';

$PRIMITIVES = [
    ['stack', 'A column with a gap', 'Anything stacked vertically. The default.'],
    ['cluster', 'A wrapping row with a gap', 'Buttons, tags, anything horizontal that must survive narrow screens.'],
    ['grid', 'Auto-fitting columns', 'Cards. Chooses its own column count from a minimum width.'],
    ['split', 'Two columns, one on a phone', 'A sidebar and content, a form and a preview.'],
    ['bar', 'A row that does not wrap', 'Toolbars and headers, where wrapping would be wrong.'],
    ['center', 'A centred column', 'A hero, an empty state, a sign-in box.'],
    ['container', 'Centred, capped, gutter applied', 'The page shell. Almost every page starts with one.'],
    ['section', 'Vertical page rhythm', 'The band of air between major sections.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Guides</li>
    <li aria-current="page">Stop writing margins</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Stop writing margins</h1>
  <p class="lede">
    You are about to put <code>margin-bottom</code> on something. This page is an argument
    that you should not, and that the eight classes below will do the same job with fewer
    bugs in it. It is the shortest route to using Deck well rather than using Deck like
    Bootstrap.
  </p>
</header>

<section class="stack-4">
  <h2 id="the-move">The move</h2>
  <p>
    Spacing is a property of a <em>group</em>, not of the things in it. So it goes on the
    parent, once, as a <code>gap</code> — not on each child as a margin.
  </p>
  <div class="split">
    <div class="stack-2">
      <p class="text-sm fw-semi text-bad">The habit</p>
      <pre class="dx-code"><code>&lt;div&gt;
  &lt;h2 class="mb-2"&gt;Title&lt;/h2&gt;
  &lt;p class="mb-4"&gt;Body copy.&lt;/p&gt;
  &lt;button class="btn"&gt;Action&lt;/button&gt;
&lt;/div&gt;</code></pre>
    </div>
    <div class="stack-2">
      <p class="text-sm fw-semi text-good">The move</p>
      <pre class="dx-code"><code>&lt;div class="stack-3"&gt;
  &lt;h2&gt;Title&lt;/h2&gt;
  &lt;p&gt;Body copy.&lt;/p&gt;
  &lt;button class="btn"&gt;Action&lt;/button&gt;
&lt;/div&gt;</code></pre>
    </div>
  </div>
  <p>
    One class instead of two, and the spacing is stated once instead of twice. That is the
    smaller half of the win. The larger half is the four bugs that are now impossible.
  </p>
</section>

<section class="stack-4">
  <h2 id="bugs">Four bugs you just deleted</h2>
  <ol class="stack-4">
    <li>
      <strong>The trailing space.</strong> <code>.mb-4</code> on every child puts a margin
      under the last one too, so the group ends with a gap that is not between anything. In
      a card, that is 1rem of dead space above the border that nobody asked for. The usual
      fix is a <code>:last-child</code> reset — a rule you now have to write, name and
      remember in every component that stacks.
    </li>
    <li>
      <strong>Collapsing.</strong> Adjacent block margins merge into the larger of the two,
      so <code>.mb-4</code> above and <code>.mt-2</code> below give you 1rem rather than
      1.5rem. That is correct CSS and it is almost never what anyone meant. It also stops
      collapsing the moment a parent gets <code>display: flex</code> or a border, so the
      same markup spaces differently depending on a property set somewhere else entirely.
      <code>gap</code> has no collapsing rules at all.
    </li>
    <li>
      <strong>Spacing that travels with the component.</strong> A margin on a child is a
      claim about its surroundings, made from inside it. Put that component somewhere else
      and it brings the claim along, so the same card has different spacing in a grid than
      in a sidebar and you fix it with an override. A <code>gap</code> is set by whatever
      is doing the arranging, which is the only thing that actually knows.
    </li>
    <li>
      <strong>Reordering.</strong> Wrap a row, reverse it, hide a child conditionally,
      insert one from a template. With gaps the spacing stays right because there is one
      value and the layout applies it. With margins, every one of those changes needs the
      spacing re-reasoned — and a conditionally hidden child leaves its margin behind
      unless you remembered <code>:last-child</code> was now a different element.
    </li>
  </ol>
  <p class="dx-note">
    Point four is the one that bites in production rather than in review. An empty state
    that renders no rows, a permissions check that hides the last button, a list whose
    final item is filtered out — all of them silently leave a margin behind, and none of
    them show up until the data does.
  </p>
</section>

<section class="stack-4">
  <h2 id="deck-itself">Deck barely uses margin either</h2>
  <p>
    This is not advice the framework gives and does not follow. The reset opens with
    <code>* { margin: 0 }</code> — every margin in the document is gone before anything
    else happens, so a margin exists only where something deliberately adds one back.
  </p>
  <p>
    Across 26 stylesheets and 1,766 rules there are 114 margin declarations. 34 of them are
    the opt-in <code>.mt-*</code> and <code>.mis-*</code> utilities, 21 are
    <code>auto</code> for centring or pushing, and most of the rest are
    <code>margin: 0</code> resets or one- and two-pixel optical nudges. The number that are
    actually spacing one thing away from another is <strong>29</strong>, and the majority of
    those are the heading rules in <code>03-type.css</code> — because a heading's space
    above it genuinely does belong to the heading.
  </p>
  <p>
    Everything else is a gap. The whole component library is built the way this page is
    asking you to build.
  </p>
</section>

<section class="stack-4">
  <h2 id="primitives">The eight classes</h2>
  <p>
    You lay out with about eight classes, not forty. Each one is a container, each one sets
    a gap, and each one is documented in full on its own page.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The layout primitives and what each is for</caption>
      <thead>
        <tr><th scope="col">Class</th><th scope="col">Is</th><th scope="col">Reach for it when</th></tr>
      </thead>
      <tbody>
        <?php foreach ($PRIMITIVES as [$name, $is, $when]): ?>
          <tr>
            <?php /* Link only where the component page exists. .center is real
                     and has no page yet, and a table that links to a 404 is
                     worse than one that does not link. */ ?>
            <?php $hasPage = is_file(__DIR__ . '/../components/' . $name . '.php'); ?>
            <th scope="row" data-label="Class">
              <?php if ($hasPage): ?>
                <a href="../components/<?= e($name) ?>.php"><code><?= e('.' . $name) ?></code></a>
              <?php else: ?>
                <code><?= e('.' . $name) ?></code>
                <span class="dx-dim">no page yet</span>
              <?php endif; ?>
            </th>
            <td data-label="Is"><?= e($is) ?></td>
            <td data-label="Reach for it when"><?= e($when) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h3 id="gap-variants">Changing the gap</h3>
  <p>
    Every primitive reads the same custom property, so the size variants are all the same
    seven steps and they behave identically across the eight classes.
  </p>
  <pre class="dx-code"><code>.stack   { display: flex; flex-direction: column; gap: var(--gap, var(--space-4)); }
.stack-2 { --gap: var(--space-2); }</code></pre>
  <p>
    Which means two ways to change it, and both are legitimate. Use the named step when it
    exists, and set the property directly when you want a value the scale does not carry:
  </p>
  <pre class="dx-code"><code>&lt;div class="stack-2"&gt;…&lt;/div&gt;                      &lt;!-- named step --&gt;
&lt;div class="stack" style="--gap: var(--space-10)"&gt;…&lt;/div&gt;  &lt;!-- any token --&gt;</code></pre>
  <?php docs_example(
      '<div class="stack-1 bg-sunken p-3 r-md">' . "\n" .
      '  <div class="bg-surface p-2 r-sm">stack-1</div>' . "\n" .
      '  <div class="bg-surface p-2 r-sm">4px apart</div>' . "\n" .
      '</div>' . "\n" .
      '<div class="stack-4 bg-sunken p-3 r-md">' . "\n" .
      '  <div class="bg-surface p-2 r-sm">stack-4</div>' . "\n" .
      '  <div class="bg-surface p-2 r-sm">16px apart</div>' . "\n" .
      '</div>' . "\n" .
      '<div class="stack-8 bg-sunken p-3 r-md">' . "\n" .
      '  <div class="bg-surface p-2 r-sm">stack-8</div>' . "\n" .
      '  <div class="bg-surface p-2 r-sm">32px apart</div>' . "\n" .
      '</div>',
      'The same class, three steps. No margins involved',
      'stack'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="composing">Composing them</h2>
  <p>
    The primitives nest, and nesting is how you get real layouts rather than by adding
    more classes. A page is usually a container, a section, a split, and stacks inside
    that.
  </p>
  <pre class="dx-code"><code>&lt;main class="container section"&gt;
  &lt;div class="stack-8"&gt;

    &lt;div class="stack-2"&gt;
      &lt;h1&gt;Invoices&lt;/h1&gt;
      &lt;p class="lede"&gt;Everything issued in the last 90 days.&lt;/p&gt;
    &lt;/div&gt;

    &lt;div class="bar"&gt;
      &lt;input class="input" type="search" placeholder="Search"&gt;
      &lt;button class="btn btn-primary push"&gt;New invoice&lt;/button&gt;
    &lt;/div&gt;

    &lt;div class="grid"&gt;
      &lt;article class="card"&gt;&lt;div class="card-body stack-2"&gt;…&lt;/div&gt;&lt;/article&gt;
      &lt;article class="card"&gt;&lt;div class="card-body stack-2"&gt;…&lt;/div&gt;&lt;/article&gt;
    &lt;/div&gt;

  &lt;/div&gt;
&lt;/main&gt;</code></pre>
  <?php docs_example(
      '<div class="stack-6">' . "\n" .
      '  <div class="stack-2">' . "\n" .
      '    <h3>Invoices</h3>' . "\n" .
      '    <p class="lede">Everything issued in the last 90 days.</p>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="bar">' . "\n" .
      '    <input class="input" type="search" placeholder="Search">' . "\n" .
      '    <button class="btn btn-primary push">New invoice</button>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="grid">' . "\n" .
      '    <article class="card"><div class="card-body stack-2"><h4 class="card-title">INV-2291</h4><span class="badge badge-good">Paid</span></div></article>' . "\n" .
      '    <article class="card"><div class="card-body stack-2"><h4 class="card-title">INV-2290</h4><span class="badge badge-warn">Pending</span></div></article>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Five nested containers, zero margins, and it is already responsive',
      'stack'
  ); ?>
  <p>
    Count the responsive code in that: none. <code>.grid</code> chooses its own column
    count from a minimum column width, so it is three across on a monitor and one on a
    phone without a breakpoint being named. <code>.bar</code> keeps the toolbar on one
    line. <code>.container</code> applies a gutter that is fluid between 1rem and 2rem.
  </p>
</section>

<section class="stack-4">
  <h2 id="keep">Where a margin is still right</h2>
  <p>
    This is not a prohibition. Three cases where a margin is the correct tool and a gap is
    not:
  </p>
  <ul class="stack-3">
    <li>
      <strong>One element, one neighbour.</strong> A back link that needs to sit further
      from the heading below it than the stack's rhythm allows. That is genuinely about
      those two elements and not about a group. <code>.mt-6</code> and move on.
    </li>
    <li>
      <strong>Centring and pushing.</strong> <code>.mx-auto</code> centres a block;
      <code>.mis-auto</code> pushes an item to the end of a flex row. These are
      <code>auto</code> margins, which are a layout mechanism rather than spacing, and
      there is no gap equivalent.
    </li>
    <li>
      <strong>Space above a heading in flowing prose.</strong> Deck does this itself. In a
      long article the gap before an <code>&lt;h2&gt;</code> should be larger than the gap
      after it, and a single <code>gap</code> cannot express an asymmetry.
    </li>
  </ul>
  <p class="dx-note">
    The tell for a wrong margin is whether removing it from one element would make the
    layout wrong for its <em>siblings</em>. If yes, it was the container's job.
  </p>
</section>

<section class="stack-4">
  <h2 id="gotchas">Two things that will look broken</h2>
  <dl class="stack-3">
    <dt><strong><code>.gap-3</code> on a plain <code>&lt;div&gt;</code> does nothing</strong></dt>
    <dd>
      <code>gap</code> only applies to flex, grid and multi-column containers. On anything
      else it is inert, silently. Check the parent is really a flex or grid container —
      usually you wanted <code>.stack-3</code>, which sets both.
    </dd>
    <dt><strong>A long word or a wide table pushes the page sideways</strong></dt>
    <dd>
      A flex item will not shrink below its content, and a grid track's minimum is
      <code>auto</code>. So one unbreakable string inside a flex row drags the whole layout
      past the viewport and you get a horizontal scrollbar on the body. The fix is
      <code>.grow</code> — which sets <code>min-inline-size: 0</code> along with the flex —
      or <code>.min-is-0</code> on a grid child. This is the most common layout bug in any
      flex-based system and it is worth recognising on sight.
    </dd>
  </dl>
</section>

<section class="stack-3">
  <h2 id="next">Next</h2>
  <p>
    The <a href="../reference/spacing.php">spacing reference</a> has the scale itself and
    why the utilities stop at seven steps. Each primitive has a page of its own —
    <a href="../components/stack.php">stack</a>,
    <a href="../components/cluster.php">cluster</a>,
    <a href="../components/grid.php">grid</a> and
    <a href="../components/split.php">split</a> are the four you will use most, and reading
    those four is about ten minutes well spent.
  </p>
</section>

<?php docs_footer(); ?>
