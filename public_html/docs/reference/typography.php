<?php
declare(strict_types=1);

/**
 * The typography reference.
 *
 * An intro and a set of generated tables. Type is looked up, not learned: the
 * question a reader arrives with is "what size is .text-sm" and the answer is a
 * number. The argument for the scale itself belongs on the theming guide.
 */

$page = [
    'path' => 'reference/typography.php',
    'title' => 'Typography',
    'level' => 'Beginner',
    'description' => "Deck type classes in one table: the heading and size scales, weights, alignment, casing, truncation, and the writing-mode and bidi utilities for vertical and mixed-direction text.",
    'documents' => [
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'display', 'lede', 'prose', 'link-quiet',
        'text-2xs', 'text-xs', 'text-sm', 'text-base', 'text-md', 'text-lg',
        'text-xl', 'text-2xl', 'text-cq', 'display-cq',
        'fw-normal', 'fw-medium', 'fw-semi', 'fw-bold',
        'text-start', 'text-center', 'text-end',
        'mono', 'nums', 'uppercase', 'capitalize', 'no-underline',
        'truncate', 'clamp-2', 'clamp-3',
        'writing-vertical', 'writing-vertical-lr', 'writing-normal',
        'writing-sideways', 'writing-upright', 'th-vertical',
        'bidi-isolate', 'bidi-plaintext', 'dir-ltr', 'dir-rtl', 'code-ltr',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">Typography</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Typography</h1>
  <p class="lede">
    Deck styles the heading elements directly, so a page written in plain HTML is
    already typeset. The classes on this page are for the cases where the element and
    the appearance need to come apart — a <code>&lt;h3&gt;</code> that has to look
    like an <code>&lt;h1&gt;</code>, a paragraph that has to be small — plus the
    casing, alignment and writing-mode utilities.
  </p>
</header>

<section class="stack-4">
  <h2 id="headings">Headings</h2>
  <p>
    <code>.h1</code> through <code>.h6</code> apply a heading's appearance to any
    element. Use them to keep the document outline honest: the heading level should
    come from where the section sits in the page, and the size from how it should
    look. A card title that is the third level of the document is an
    <code>&lt;h3&gt;</code> with <code>.h5</code> on it, not an
    <code>&lt;h5&gt;</code>.
  </p>
  <p>
    <code>.display</code> is a size above <code>.h1</code>, for the one line at the
    top of a landing page. <code>.lede</code> is the paragraph directly under a
    heading: larger, looser, muted, and capped at the measure.
  </p>
  <?php docs_example(
      '<h3 class="h5">A third-level heading that looks like a fifth</h3>' . "\n" .
      '<p class="lede">The lede sets its own measure, so it stays readable however wide the column gets.</p>',
      'The element carries the outline; the class carries the size',
      'stack'
  ); ?>
  <?php docs_utility_table(
      ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'display', 'lede', 'prose', 'link-quiet'],
      'Heading and prose classes'
  ); ?>
  <p class="dx-note">
    <code>.prose</code> caps inline size at <code>--measure</code>
    (<code><?= e(api_token('--measure')['value'] ?? '') ?></code>), which is the line
    length research keeps landing on for continuous reading. It is the one class here
    that is about the block rather than the glyphs.
  </p>
</section>

<section class="stack-4">
  <h2 id="size">Size</h2>
  <p>
    Eight steps, each reading a <code>--text-*</code> token. The scale is a ratio
    rather than a series of round numbers, so the steps stay distinguishable at both
    ends. <code>.text-cq</code> and <code>.display-cq</code> size against the nearest
    <code>.cq</code> container instead of a fixed value, which is how a heading in a
    sidebar stays small on a wide monitor.
  </p>
  <?php docs_utility_table([
      'text-2xs', 'text-xs', 'text-sm', 'text-base', 'text-md', 'text-lg',
      'text-xl', 'text-2xl', 'text-cq', 'display-cq',
  ], 'Font size utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="weight">Weight</h2>
  <p>
    Four weights, and two of them are not round numbers. Deck's default face is
    variable, so 540 and 620 are available and are better choices than 500 and 600:
    at text sizes 500 is nearly indistinguishable from 400, and 600 is heavy enough
    to read as bold when it is meant to read as emphasis.
  </p>
  <?php docs_utility_table(['fw-normal', 'fw-medium', 'fw-semi', 'fw-bold'], 'Font weight utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="alignment">Alignment and casing</h2>
  <p>
    <code>.text-start</code> and <code>.text-end</code> are logical: they follow the
    text direction, so a right-to-left page mirrors without a second rule. There is
    no <code>.text-left</code> or <code>.text-right</code>, for the same reason there
    is no <code>.ml-4</code>.
  </p>
  <p>
    <code>.uppercase</code> adds a small amount of letter-spacing along with the
    transform, because capitals set at the same tracking as lower case read as
    cramped. <code>.nums</code> switches on tabular figures, which is what you want in
    any column of numbers that has to line up.
  </p>
  <?php docs_utility_table([
      'text-start', 'text-center', 'text-end',
      'uppercase', 'capitalize', 'mono', 'nums', 'no-underline',
  ], 'Alignment, casing and numeral utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="overflow">Truncation</h2>
  <p>
    <code>.truncate</code> is one line with an ellipsis; <code>.clamp-2</code> and
    <code>.clamp-3</code> are two and three. All three hide text, so none of them
    should be the only place a value appears — put the full string in a
    <code>title</code> attribute or somewhere it can be read.
  </p>
  <p>
    <code>.truncate</code> also sets <code>min-inline-size: 0</code>, without which it
    silently does nothing inside a flex row: a flex item will not shrink below its
    content by default, so the ellipsis never gets a chance to appear.
  </p>
  <?php docs_utility_table(['truncate', 'clamp-2', 'clamp-3'], 'Truncation utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="writing">Writing modes</h2>
  <p>
    Six classes for vertical text — Japanese and Mongolian setting, and the rotated
    column header in a dense table, which is what <code>.th-vertical</code> is for.
    <code>.writing-upright</code> keeps Latin characters upright inside a vertical
    run instead of rotating them, and adds tracking, because upright Latin in a
    vertical column needs more air than horizontal Latin does.
  </p>
  <?php docs_utility_table([
      'writing-vertical', 'writing-vertical-lr', 'writing-normal',
      'writing-sideways', 'writing-upright', 'th-vertical',
  ], 'Writing mode utilities'); ?>
</section>

<section class="stack-4">
  <h2 id="bidi">Direction and bidi</h2>
  <p>
    These are for text whose direction differs from the page around it: an English
    product name inside an Arabic sentence, a file path in a Hebrew paragraph.
    Without isolation the bidi algorithm reorders punctuation at the boundary and a
    path ending in a slash comes out with the slash on the wrong end.
  </p>
  <p>
    <code>.bidi-isolate</code> is the one to reach for when you know the direction of
    the run. <code>.bidi-plaintext</code> is for user-supplied strings whose direction
    you do not know — it takes the direction from the first strong character in the
    string, which is what a chat message or a comment field needs.
    <code>.code-ltr</code> is the specific case of a code span or path, which is
    always left to right whatever the page is.
  </p>
  <?php docs_utility_table([
      'dir-ltr', 'dir-rtl', 'bidi-isolate', 'bidi-plaintext', 'code-ltr',
  ], 'Direction and bidi utilities'); ?>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use these</h2>
  <ul class="stack-3">
    <li>
      <strong>Not instead of a heading element.</strong> <code>.h2</code> on a
      <code>&lt;div&gt;</code> looks like a heading and is not one. Screen reader
      users navigate by heading, and a div is not in that list.
    </li>
    <li>
      <strong>Not to shrink text below <code>.text-xs</code> for body copy.</strong>
      <code>.text-2xs</code> exists for a timestamp or a unit label beside something
      larger. A paragraph at that size is a paragraph nobody reads.
    </li>
    <li>
      <strong>Not <code>.truncate</code> on something with no other home.</strong> A
      truncated string with no <code>title</code> and no detail view is a value the
      reader cannot get at.
    </li>
    <li>
      <strong>Not <code>.uppercase</code> on a long run.</strong> Capitals lose the
      word shapes that make reading fast. It is for a label of one or two words.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
