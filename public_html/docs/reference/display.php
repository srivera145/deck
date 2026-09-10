<?php
declare(strict_types=1);

/**
 * The display, flex, sizing and container-query reference.
 *
 * The biggest of the utility pages, and the one whose tables are most worth
 * reading rather than skimmed: several of these classes only exist inside a
 * @media or @container block, and the generated rows say so.
 */

$page = [
    'path' => 'reference/display.php',
    'title' => 'Display and sizing',
    'level' => 'Beginner',
    'description' => "Display, flex alignment, sizing, overflow, aspect ratio, visibility and containment utilities, plus the container-query classes and the breakpoint prefixes.",
    'documents' => [
        'hidden', 'block', 'inline-block', 'flex', 'inline-flex', 'contents',
        'flex-row', 'flex-col', 'wrap', 'nowrap', 'grow', 'shrink-0',
        'items-start', 'items-center', 'items-end', 'items-baseline', 'items-stretch',
        'justify-start', 'justify-center', 'justify-end', 'justify-between', 'place-center',
        'w-full', 'w-auto', 'h-full', 'min-h-screen', 'min-is-0', 'square',
        'max-w-sm', 'max-w-md', 'max-w-lg', 'max-w-prose',
        'ratio-16x9', 'ratio-4x3', 'ratio-1x1',
        'overflow-auto', 'overflow-clip', 'overflow-hidden',
        'sr-only', 'skip-link', 'touch-only', 'mouse-only',
        'cursor-pointer', 'no-select', 'pointer-none', 'opacity-60',
        'sm', 'md', 'lg',
        'cq', 'cq-size', 'cq-normal', 'cq-panel', 'cq-pane', 'cq-row', 'cq-shell', 'cq-tone',
        'cq-sm', 'cq-md', 'cq-lg',
        'actions-cq', 'metarow', 'row-meta', 'row-meta-wide', 'stat-cq',
        'mirror-rtl', 'no-flip',
        'contain', 'contain-paint', 'defer', 'virtual', 'virtual-sm', 'virtual-lg',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">Display and sizing</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Display and sizing</h1>
  <p class="lede">
    The low-level half of layout: what box an element makes, how it aligns its
    children, how big it is allowed to get, and what happens when its content does not
    fit. Most pages want <a href="../components/stack.php">stack</a>,
    <a href="../components/cluster.php">cluster</a> or
    <a href="../components/grid.php">grid</a> instead — those are the same properties
    with the decisions already made. These are for the cases the primitives do not
    cover.
  </p>
</header>

<section class="stack-4">
  <h2 id="display">Display</h2>
  <p>
    <code>.contents</code> is the interesting one. It removes the element's own box
    while keeping its children in the parent's layout, which is how you get a
    server-rendered wrapper out of the way of a grid without changing the HTML. It
    also removes the box from the accessibility tree in some engines, so do not put it
    on anything with a role or a label.
  </p>
  <?php docs_utility_table(
      ['hidden', 'block', 'inline-block', 'flex', 'inline-flex', 'contents'],
      'Display utilities'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="flex">Flex direction and alignment</h2>
  <p>
    <code>.grow</code> sets <code>flex: 1 1 auto</code> and
    <code>min-inline-size: 0</code> together. The second half is the fix for the
    commonest flex bug there is: a flex item will not shrink below its content, so a
    long unbroken string in a <code>.grow</code> item pushes the row wider than its
    container and the whole page picks up a horizontal scrollbar.
  </p>
  <?php docs_utility_table([
      'flex-row', 'flex-col', 'wrap', 'nowrap', 'grow', 'shrink-0',
      'items-start', 'items-center', 'items-end', 'items-baseline', 'items-stretch',
      'justify-start', 'justify-center', 'justify-end', 'justify-between', 'place-center',
  ], 'Flex direction and alignment utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="size">Size</h2>
  <p>
    All of these are logical: <code>.w-full</code> sets
    <code>inline-size</code> and <code>.h-full</code> sets <code>block-size</code>, so
    they follow the writing mode rather than the screen.
    <code>.min-h-screen</code> uses <code>dvh</code> rather than <code>vh</code>, which
    is what stops a full-height layout from being cropped by the address bar on a
    phone.
  </p>
  <p>
    <code>.min-is-0</code> is <code>.grow</code>'s second half on its own, for grid
    items — a grid track's minimum is <code>auto</code> too, so a wide table inside one
    drags the whole track past the viewport unless the item is allowed to shrink.
  </p>
  <?php docs_utility_table([
      'w-full', 'w-auto', 'h-full', 'min-h-screen', 'min-is-0', 'square',
      'max-w-sm', 'max-w-md', 'max-w-lg', 'max-w-prose',
  ], 'Sizing utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="ratio">Aspect ratio</h2>
  <p>
    Three ratios with <code>object-fit: cover</code> already set, for the media slot in
    a card or a gallery. Setting the ratio in CSS rather than leaving the image to size
    itself is what stops the page reflowing as images arrive.
  </p>
  <?php docs_utility_table(['ratio-16x9', 'ratio-4x3', 'ratio-1x1', 'square'], 'Aspect ratio utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="overflow">Overflow</h2>
  <p>
    <code>.overflow-clip</code> and <code>.overflow-hidden</code> both cut the content
    off; the difference is that <code>hidden</code> makes the element a scroll
    container and <code>clip</code> does not. That matters more than it sounds:
    <code>overflow: hidden</code> on an ancestor is what breaks
    <code>position: sticky</code>, and swapping it for <code>clip</code> usually fixes
    a sticky header that mysteriously does not stick.
  </p>
  <?php docs_utility_table(
      ['overflow-auto', 'overflow-clip', 'overflow-hidden'],
      'Overflow utilities'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="visibility">Visibility and input</h2>
  <p>
    <code>.hidden</code> removes an element from the page and from assistive
    technology. <code>.sr-only</code> does the opposite: it keeps the element in the
    accessibility tree and takes it off the screen, which is how you label an icon-only
    control. They are not interchangeable, and using <code>.hidden</code> for a label
    is how a button ends up announced as "button".
  </p>
  <p>
    <code>.touch-only</code> and <code>.mouse-only</code> key off
    <code>pointer: coarse</code> and <code>pointer: fine</code> rather than width, so a
    touchscreen laptop is treated by what it has rather than by how wide it is. Neither
    is a substitute for making a control work with both.
  </p>
  <?php docs_utility_table([
      'sr-only', 'skip-link', 'touch-only', 'mouse-only',
      'cursor-pointer', 'no-select', 'pointer-none', 'opacity-60',
  ], 'Visibility and pointer utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="breakpoints">Breakpoint prefixes</h2>
  <p>
    Deck has three viewport breakpoints — 40rem, 48rem and 64rem — and a small set of
    prefixed classes that turn on at each. They are mobile-first: the prefixed class
    applies at that width and above, so the unprefixed state is the phone.
  </p>
  <pre class="dx-code"><code>&lt;div class="stack-3 md:row md:items-center"&gt;
  &lt;img class="ratio-1x1" src="…" alt=""&gt;
  &lt;p&gt;Stacked on a phone, side by side from 48rem up.&lt;/p&gt;
&lt;/div&gt;</code></pre>
  <p>
    The available combinations are fixed rather than generated, so this is not a
    system where any class can take any prefix. The rows below list what exists at each
    breakpoint, taken from the source.
  </p>
  <p class="dx-note">
    The three rows below are named <code>.sm</code>, <code>.md</code> and
    <code>.lg</code> because that is how they land in the generated inventory: the
    extractor reads a class name up to the first character that cannot be in one, and
    in <code>sm\:hidden</code> that is the backslash escaping the colon. The classes
    you write are the full prefixed names. Recorded so the table is not silently
    wrong about it.
  </p>
  <?php docs_utility_table(['sm', 'md', 'lg'], 'Viewport breakpoint prefixes'); ?>

  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Which prefixed utilities exist at each viewport breakpoint</caption>
      <thead>
        <tr>
          <th scope="col">Breakpoint</th>
          <th scope="col">Applies from</th>
          <th scope="col">Classes</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Breakpoint"><code>sm:</code></th>
          <td data-label="Applies from"><code>40rem</code></td>
          <td data-label="Classes"><code>hidden block flex row w-auto text-start</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="Breakpoint"><code>md:</code></th>
          <td data-label="Applies from"><code>48rem</code></td>
          <td data-label="Classes"><code>hidden block flex grid-d row col items-center justify-between w-auto text-center p-8 gap-6</code></td>
        </tr>
        <tr>
          <th scope="row" data-label="Breakpoint"><code>lg:</code></th>
          <td data-label="Applies from"><code>64rem</code></td>
          <td data-label="Classes"><code>hidden block flex grid-d row gap-8</code></td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-4">
  <h2 id="container">Container queries</h2>
  <p>
    A viewport breakpoint asks how wide the window is. A container query asks how wide
    the element's own column is, which is nearly always the question you actually
    meant: the same card should stack in a sidebar and spread out in a main column, and
    the window has no opinion about which of those it is in.
  </p>
  <p>
    <code>.cq</code> marks an element as a container. The named variants exist so a
    deeply nested component can query a specific ancestor instead of the closest one —
    without a name, <code>@container</code> always resolves to the nearest container,
    which is rarely the one you meant once containers nest.
  </p>
  <?php docs_utility_table(
      ['cq', 'cq-size', 'cq-normal', 'cq-panel', 'cq-pane', 'cq-row', 'cq-shell', 'cq-tone'],
      'Container declaration utilities'
  ); ?>

  <h3 id="container-breakpoints">Container breakpoint prefixes</h3>
  <p>
    The same idea as <code>md:</code>, keyed to the container at 24rem, 32rem and
    44rem. These only do anything inside an element carrying <code>.cq</code>, and the
    same extractor caveat applies to their names.
  </p>
  <?php docs_utility_table(['cq-sm', 'cq-md', 'cq-lg'], 'Container breakpoint prefixes'); ?>

  <h3 id="container-patterns">Container-driven patterns</h3>
  <p>
    Five ready-made ones. <code>.metarow</code> is a list row that reveals metadata as
    space allows: <code>.row-meta</code> appears at 26rem of container width and
    <code>.row-meta-wide</code> at 38rem, so the same row is terse in a narrow pane and
    complete in a wide one with no JavaScript measuring anything.
  </p>
  <?php docs_utility_table(
      ['actions-cq', 'metarow', 'row-meta', 'row-meta-wide', 'stat-cq'],
      'Container-driven layout patterns'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="rtl">Mirroring</h2>
  <p>
    Deck is written in logical properties, so a right-to-left page mirrors on its own.
    These two are for the exceptions. <code>.mirror-rtl</code> flips an element
    horizontally when the direction is RTL — for a directional glyph in an icon set
    that does not have a mirrored variant. <code>.no-flip</code> pins something that
    would otherwise be flipped by a rule higher up: a logo, a play button, a chart
    axis.
  </p>
  <?php docs_utility_table(['mirror-rtl', 'no-flip'], 'RTL mirroring utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="containment">Containment</h2>
  <p>
    Six classes that tell the engine it may skip work. <code>.defer</code> sets
    <code>content-visibility: auto</code>, so an offscreen section is not laid out or
    painted until it approaches the viewport; <code>.virtual</code> applies the same
    idea per child, which is what makes a very long list scroll smoothly with no
    virtualisation library.
  </p>
  <p>
    The catch with <code>.defer</code> is that skipped content is skipped by in-page
    find as well in some engines, and the <code>contain-intrinsic-size</code> estimate
    is what keeps the scrollbar from jumping as sections resolve. Keep the estimate
    close to the real height.
  </p>
  <?php docs_utility_table(
      ['contain', 'contain-paint', 'defer', 'virtual', 'virtual-sm', 'virtual-lg'],
      'Containment and deferral utilities'
  ); ?>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use these</h2>
  <ul class="stack-3">
    <li>
      <strong>Not to rebuild a primitive.</strong>
      <code>.flex .flex-col .gap-4</code> is <code>.stack</code> written out.
      <code>.flex .wrap .items-center .gap-3</code> is <code>.cluster</code>. Use the
      name; it is one class and it carries the default.
    </li>
    <li>
      <strong>Not <code>.hidden</code> for a visual-only hide.</strong> It takes the
      element out of the accessibility tree too. If it should be read but not seen,
      that is <code>.sr-only</code>.
    </li>
    <li>
      <strong>Not a breakpoint prefix where a container query fits.</strong> A
      component that reflows on window width is wrong the moment somebody puts it in a
      sidebar. <code>.cq</code> plus <code>cq-md:</code> asks the right question.
    </li>
    <li>
      <strong>Not <code>.cursor-pointer</code> on a <code>&lt;div&gt;</code> to make
      it a button.</strong> The cursor is the least of what a button is. Use a
      <code>&lt;button&gt;</code> and get the focus ring, the keyboard activation and
      the role for free.
    </li>
    <li>
      <strong>Not <code>.defer</code> on everything.</strong> It costs a layout pass
      when the element comes into view. On short content it is slower than doing
      nothing.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
