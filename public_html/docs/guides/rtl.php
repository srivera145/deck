<?php
declare(strict_types=1);

/**
 * Right-to-left.
 *
 * Short on purpose. Deck is written in logical properties throughout, so the
 * flip really is one attribute, and a long guide would be padding around a
 * one-line answer. The substance is in the second half: the things that must
 * NOT flip, which is where every RTL bug actually lives.
 */

$page = [
    'path' => 'guides/rtl.php',
    'title' => 'Support right-to-left languages',
    'level' => 'Intermediate',
    'description' => "Arabic, Hebrew, Persian and Urdu layouts in Deck are one attribute on the html tag. Here is why, which icons mirror, and what must never flip.",
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Guides</li>
    <li aria-current="page">Support right-to-left languages</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Support right-to-left languages</h1>
  <p class="lede">
    You need an Arabic, Hebrew, Persian or Urdu version of your interface and you are
    bracing for a second stylesheet. There is not one. Deck is written entirely in logical
    properties, so the whole layout mirrors from one attribute — and the work that is left
    is deciding what must <em>not</em> mirror.
  </p>
</header>

<section class="stack-4">
  <h2 id="answer">The short answer</h2>
  <pre class="dx-code"><code>&lt;html lang="ar" dir="rtl"&gt;</code></pre>
  <p>
    That is the layout done. Padding, margins, borders, radii, flex and grid alignment,
    table columns, the drawer's edge, the toast region's corner, the sidebar's side — all
    of it flips, because none of it was written in <code>left</code> and
    <code>right</code> to begin with.
  </p>
  <p>
    From JavaScript, <code>Deck.dir()</code> does the same and persists the choice:
  </p>
  <pre class="dx-code"><code>Deck.dir();        // 'ltr'
Deck.dir('rtl');   // flip, and remember it</code></pre>
  <?php docs_example(
      '<div class="cluster">' . "\n" .
      '  <button class="btn btn-sm" onclick="Deck.dir(\'rtl\')">Flip this page to RTL</button>' . "\n" .
      '  <button class="btn btn-sm btn-ghost" onclick="Deck.dir(\'ltr\')">Back to LTR</button>' . "\n" .
      '</div>',
      'Live. The sidebar, the tables and the code blocks all move'
  ); ?>
  <p class="dx-note">
    <code>dir</code> sets direction, not language. It does not translate anything, and an
    English page in RTL is a test rather than a localisation. Set <code>lang</code> too:
    it drives hyphenation, quotation marks, the font stack's script coverage and how a
    screen reader pronounces the page.
  </p>
</section>

<section class="stack-4">
  <h2 id="why">Why it costs nothing</h2>
  <p>
    Every spacing and alignment property in Deck names the writing axis rather than the
    screen. <code>inline</code> is the direction text runs; <code>block</code> is the
    direction lines stack. In English, inline-start is the left edge. In Arabic it is the
    right edge, and the same declaration means the same thing in both.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Physical properties and their logical equivalents</caption>
      <thead>
        <tr><th scope="col">Instead of</th><th scope="col">Deck writes</th><th scope="col">Which is</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="Instead of"><code>padding-left</code></th><td data-label="Deck writes"><code>padding-inline-start</code></td><td data-label="Which is">The edge text starts at</td></tr>
        <tr><th scope="row" data-label="Instead of"><code>margin-right</code></th><td data-label="Deck writes"><code>margin-inline-end</code></td><td data-label="Which is">The edge text ends at</td></tr>
        <tr><th scope="row" data-label="Instead of"><code>text-align: left</code></th><td data-label="Deck writes"><code>text-align: start</code></td><td data-label="Which is">Ragged edge follows the language</td></tr>
        <tr><th scope="row" data-label="Instead of"><code>width</code></th><td data-label="Deck writes"><code>inline-size</code></td><td data-label="Which is">Along the text axis</td></tr>
        <tr><th scope="row" data-label="Instead of"><code>top / bottom</code></th><td data-label="Deck writes"><code>inset-block-start / end</code></td><td data-label="Which is">Along the line-stacking axis</td></tr>
      </tbody>
    </table>
  </div>
  <p>
    The utilities are named the same way, which is why there is no <code>.ml-4</code> in
    Deck — only <code>.mis-4</code>. That is not pedantry: naming the left edge is how you
    build the bug in, and a class called <code>ml</code> that sets
    <code>margin-inline-start</code> would be worse, because it would lie.
    <a href="../reference/spacing.php#logical">The spacing reference</a> has the full set.
  </p>
</section>

<section class="stack-4">
  <h2 id="icons">Icons that point</h2>
  <p>
    An arrow, a chevron, a reply glyph and a back button all mean "the direction of
    reading", so they have to mirror. A checkmark, a clock, a wrench and your logo do not.
    Deck mirrors ten of its own sprite icons automatically by matching the
    <code>href</code> they use:
  </p>
  <pre class="dx-code"><code>[dir="rtl"] :is(
  .icon:has(use[href$="#chevron-left"]),
  .icon:has(use[href$="#arrow-right"]),
  .icon:has(use[href$="#send"]),
  …
) { scale: -1 1; }</code></pre>
  <p>
    For your own icons, two classes:
  </p>
  <?php docs_utility_table(['mirror-rtl', 'no-flip'], 'RTL mirroring utilities'); ?>
  <p>
    <code>.mirror-rtl</code> flips an element when the page is RTL.
    <code>.no-flip</code> pins one that would otherwise be flipped by a rule above it — a
    logo inside a mirrored header, a play button, a chart axis.
  </p>
</section>

<section class="stack-4">
  <h2 id="isolate">What must never flip</h2>
  <p>
    This is where RTL bugs actually live. Some values have one true direction regardless of
    the page around them, and if you do not isolate them the bidirectional algorithm
    reorders their punctuation at the boundary. The classic symptom is a file path whose
    trailing slash has migrated to the front, or a version number that reads
    <code>1.2.3</code> in English and <code>3.2.1</code> in Arabic.
  </p>
  <?php docs_utility_table(
      ['dir-ltr', 'dir-rtl', 'bidi-isolate', 'bidi-plaintext', 'code-ltr'],
      'Direction and bidi utilities'
  ); ?>
  <dl class="stack-3">
    <dt><strong><code>.code-ltr</code></strong></dt>
    <dd>
      A path, a URL, a command, a code span. Always left to right, always isolated, and
      aligned to the reading edge so it still sits correctly in the paragraph.
    </dd>
    <dt><strong><code>.bidi-isolate</code></strong></dt>
    <dd>
      A run whose direction you know but which differs from the page — an English product
      name inside an Arabic sentence.
    </dd>
    <dt><strong><code>.bidi-plaintext</code></strong></dt>
    <dd>
      A run whose direction you do <em>not</em> know: a chat message, a comment, a
      user-supplied display name. The direction is taken from the first strong character in
      the string, so an Arabic message and an English message in the same list each read
      correctly. This is the one to reach for on anything a person typed.
    </dd>
  </dl>
  <p class="dx-note">
    Deck already isolates <code>code</code>, <code>kbd</code>, <code>samp</code>,
    <code>.mono</code> and <code>.nums</code> for you, so identifiers and figures in the
    default elements are safe without you doing anything. The gap is user-generated text
    and anything you build out of a plain <code>&lt;span&gt;</code>.
  </p>
</section>

<section class="stack-4">
  <h2 id="motion">Motion flips too</h2>
  <p>
    The entrance animations move toward the reader, so their keyframes mirror with the
    page. <code>.enter-start</code> travels from the right on an RTL page and from the left
    on an LTR one, because "start" is a direction and not a side. You do not have to do
    anything; it is worth knowing because an animation that slides the wrong way is a
    subtle wrongness that is hard to name when you see it.
  </p>
</section>

<section class="stack-4">
  <h2 id="check">Checking it</h2>
  <ul class="stack-2">
    <li>
      Flip the whole page with the button above, or set <code>dir="rtl"</code> in dev tools
      on the <code>&lt;html&gt;</code> element. Look at the sidebar, the table headers, the
      form labels, the toast corner and the drawer edge.
    </li>
    <li>
      Look for a scrollbar. Something written in physical properties usually shows up as
      horizontal overflow on the flipped side.
    </li>
    <li>
      Find every number, path, code span and identifier and check nothing has been
      reordered. Punctuation at the ends of a run is the tell.
    </li>
    <li>
      Test with real Arabic or Hebrew text, not English in RTL. Line height, glyph height
      and the absence of letter casing all change the vertical rhythm, and Latin text
      flipped left to right hides all three.
    </li>
    <li>
      Grep your own CSS for <code>left</code>, <code>right</code>, <code>margin-left</code>
      and <code>text-align: right</code>. Deck has none; your layer is where they will be.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
