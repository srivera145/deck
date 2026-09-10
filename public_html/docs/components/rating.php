<?php
declare(strict_types=1);

$page = [
    'path' => 'components/rating.php',
    'title' => 'Rating',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .rating is five radio inputs under five stars, so it posts a real value and works with the keyboard. .rating-static shows a fractional average as a clipped overlay.',
    'documents' => [
        'rating', 'rating-fill', 'rating-lg', 'rating-sm', 'rating-static',
    ],

    'component' => 'rating',
    'accounts' => [
        '23-inputs.css' => 'documented: the reversed row, the hidden radios, the star labels and their fill rules, the two sizes and the read-only fractional display',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Rating</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Rating</h1>
  <p class="lede">
    Five stars over five radio inputs. The radios are the component — they carry the value,
    post with the form, and give you arrow-key navigation and a group role for free. The
    stars are labels, and the fill is a CSS selector rather than a script.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    For collecting a rating on a small fixed scale where the reader understands the units
    without being told — a review, a satisfaction check after a support ticket. Stars are
    one of the few controls people can use without a label.
  </p>
  <p>
    They are also imprecise. If you need to know <em>why</em> a rating is what it is, the
    stars are the easy half and the text field underneath is the part that matters.
  </p>
</section>

<section class="stack-3">
  <h2 id="markup">The markup, and why it is backwards</h2>
  <p>
    <code>.rating</code> is <code>flex-direction: row-reverse</code>, so the first star in
    the DOM is drawn at the right-hand end. That means the inputs are written
    <strong>highest value first</strong> — 5, 4, 3, 2, 1 — and appear on screen in the
    order 1 to 5.
  </p>
  <p>
    The reversal is what makes the fill work with no JavaScript. To light up a star and
    everything below it, the stylesheet needs a selector that reaches <em>forward</em> from
    the chosen one, because CSS has no previous-sibling combinator. Reversing the row turns
    "everything below" into "everything after":
  </p>
  <pre class="dx-code"><code><?= e('.rating > input:checked ~ label,
.rating > label:hover,
.rating > label:hover ~ label { color: var(--accent-500); }') ?></code></pre>
  <?php
  docs_example(
      '<fieldset class="rating" style="border:0;padding:0;margin:0">' . "\n" .
      '  <input type="radio" name="dx-rate" id="dx-rate-5" value="5">' . "\n" .
      '  <label for="dx-rate-5" title="5 stars"><svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg></label>' . "\n" .
      '  <input type="radio" name="dx-rate" id="dx-rate-4" value="4">' . "\n" .
      '  <label for="dx-rate-4" title="4 stars"><svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg></label>' . "\n" .
      '  <input type="radio" name="dx-rate" id="dx-rate-3" value="3">' . "\n" .
      '  <label for="dx-rate-3" title="3 stars"><svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg></label>' . "\n" .
      '  <input type="radio" name="dx-rate" id="dx-rate-2" value="2">' . "\n" .
      '  <label for="dx-rate-2" title="2 stars"><svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg></label>' . "\n" .
      '  <input type="radio" name="dx-rate" id="dx-rate-1" value="1">' . "\n" .
      '  <label for="dx-rate-1" title="1 star"><svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg></label>' . "\n" .
      '</fieldset>',
      'Written 5 to 1, drawn 1 to 5 — hover or use the arrow keys',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Get the order wrong and it fails in a specific, recognisable way: the stars fill from
    the right instead of the left, and the value posted is the mirror of the one clicked.
    If you see that, the DOM order is ascending and it needs to be descending.
  </p>
</section>

<section class="stack-3">
  <h2 id="sizes">Sizes</h2>
  <p>
    <code>.rating-sm</code> and <code>.rating-lg</code> set <code>--star</code> to 18px and
    36px against a 26px default. It is one custom property, so a size in between is an
    inline style rather than a class you have to add.
  </p>
  <?php
  docs_example(
      '<div class="stack-2">' . "\n" .
      '  <span class="rating-static rating-sm" style="--value:100" role="img" aria-label="5 out of 5">' . "\n" .
      '    <span class="rating-fill">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '  </span>' . "\n" .
      '  <span class="rating-static" style="--value:64;--star:26px" role="img" aria-label="3.2 out of 5">' . "\n" .
      '    <span class="rating-fill">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '  </span>' . "\n" .
      '</div>',
      'Small at 5 stars, default at 3.2 — the second star row is the fill',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="static">Showing an average</h2>
  <p>
    An average is rarely a whole number, and radios cannot express 3.2. <code>.rating-static</code>
    solves it differently: two identical rows of stars, one grey and one coloured, with the
    coloured row absolutely positioned on top and clipped to
    <code>calc(var(--value) * 1%)</code>. A 64% width cuts the third star part-way across.
  </p>
  <p>
    So <code>--value</code> is a <strong>percentage, not a star count</strong>. Three and a
    fifth stars out of five is <code>--value: 64</code>. Multiply your average by 20.
  </p>
  <p class="text-muted">
    The clipping is <code>overflow: hidden</code> with <code>white-space: nowrap</code>, so
    the fill row must contain the same number of stars as the row underneath or the
    proportions will not line up.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/23-inputs.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--accent-500', '--ink-300', '--focus', '--r-xs', '--dur-1']); ?>
  <p class="text-muted">
    <code>--star</code> is a component-level custom property with a 26px fallback, not a
    Deck token — it does not exist until <code>.rating-sm</code>, <code>.rating-lg</code> or
    you set it.
  </p>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Real radios are the whole reason this works.</strong> Arrow keys move between
      options, the group is announced as a radio group, the current value is announced, and
      the form posts a number. None of that had to be built, and none of it can be matched
      by a row of <code>&lt;div&gt;</code> elements with click handlers.
    </li>
    <li>
      <strong>The group needs a name.</strong> A <code>&lt;fieldset&gt;</code> with a
      <code>&lt;legend&gt;</code>, or <code>role="radiogroup"</code> with
      <code>aria-label</code>. Without it a screen reader announces five options and no
      question.
    </li>
    <li>
      <strong>Every star needs its own text.</strong> The label contains an icon and nothing
      else, so its accessible name is empty. The examples use <code>title</code> for
      brevity; a visually hidden <code>&lt;span&gt;</code> reading "4 stars" inside each
      label is the more reliable choice, since <code>title</code> is inconsistently
      announced and never shown on touch.
    </li>
    <li>
      <strong>Focus is visible, on the label rather than the input.</strong>
      <code>.rating &gt; input:focus-visible + label</code> draws the outline, because the
      input itself is a 1px transparent box. It uses the adjacent-sibling combinator, so
      each label must come immediately after its own input — another reason the DOM order
      is not free-form.
    </li>
    <li>
      <strong>The hover preview is pointer-only.</strong> A keyboard user sees the fill
      change as they arrow through, which is equivalent. A touch user gets no preview at
      all — the first tap commits. That is a real difference in what the three input methods
      show, and it argues for a text value beside the stars.
    </li>
    <li>
      <strong><code>.rating-static</code> is a picture and must say so.</strong> Two rows of
      identical stars announce as ten graphics. Give the wrapper
      <code>role="img"</code> and an <code>aria-label</code> — "3.2 out of 5" — and hide
      every icon inside, as the examples do.
    </li>
    <li>
      <strong>Colour carries the value.</strong> Filled and empty stars differ only in
      colour — both are the same solid glyph. In greyscale, or for a reader who cannot
      separate the amber from the grey, the rating is unreadable. Writing the number beside
      it is the fix, and it is worth doing everywhere.
    </li>
    <li>
      <strong>There is no way to clear a rating.</strong> Radios cannot be unchecked by
      clicking. If someone can rate by accident, supply a "clear" control that resets the
      group.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>row-reverse</code> is resolved against the writing direction, so under
    <code>dir="rtl"</code> the row reverses again and the stars run right to left — one star
    at the right, five at the left. That is correct: the scale should read from the
    reader's starting edge.
  </p>
  <p>
    <code>.rating-static</code> follows too, because its fill is anchored with
    <code>inset-inline-start</code> rather than <code>left</code>. The partial star is cut
    from the correct side without a rule in <code>src/19-logical.css</code>.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="stack-2">' . "\n" .
      '  <span class="rating-static" style="--value:64" role="img" aria-label="٣٫٢ من ٥">' . "\n" .
      '    <span class="rating-fill">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#star-fill"></use></svg>' . "\n" .
      '  </span>' . "\n" .
      '</div>',
      'The fill starts at the right, which is where the scale starts',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    A star scales to 1.14 on hover over
    <?= e(api_token('--dur-1')['value'] ?? '110ms') ?>, with the colour change on the same
    transition. The global reset in <code>src/02-reset.css</code> collapses both under
    <code>prefers-reduced-motion: reduce</code> — the star still grows, it just arrives
    instantly. Nothing else on the component moves.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.rating</code> is not in <code>src/99-print.css</code>. The stars are inline SVG
    filled with <code>currentColor</code>, so they print as shapes rather than disappearing
    the way a background would — but filled and empty stars print as the same shape in two
    tones of grey, and on a monochrome printer they may become indistinguishable.
  </p>
  <p class="text-muted">
    This is the same argument as <a href="#accessibility">colour carrying the value</a>, and
    it has the same answer: print the number.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* One control, no layer needed */
<span class="rating-static" style="--value:64;--star:22px"> … </span>

@layer app.components {
  /* Outline stars for the empty half, filled for the rated half */
  .rating > label .icon { fill: none; stroke: currentColor; }
  .rating > input:checked ~ label .icon { fill: currentColor; }

  /* A different scale colour */
  .rating > input:checked ~ label,
  .rating > label:hover,
  .rating > label:hover ~ label { color: var(--warn-500); }
}') ?></code></pre>
  <p class="text-muted">
    If you re-colour the fill, change all three selectors together — they are one rule in
    the source and splitting them gives you a hover state that disagrees with the checked
    state.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a scale people cannot interpret.</strong> Stars mean "how good"; they
      do not mean likelihood, frequency or agreement. Use labelled
      <a href="check.php">radios</a> and write the ends of the scale down.
    </li>
    <li>
      <strong>Not for more than about seven points.</strong> Ten stars is a row of targets
      nobody can hit accurately. Use a <a href="range.php"><code>.range</code></a> or a
      <a href="select.php">select</a>.
    </li>
    <li>
      <strong>Not for a yes-or-no.</strong> Thumbs up and down is two
      <a href="button.php">buttons</a>, and pretending it is a scale invites a three-star
      shrug that tells you nothing.
    </li>
    <li>
      <strong>Not as the only feedback you collect.</strong> A number without a reason is
      not actionable. The <a href="textarea.php">textarea</a> underneath is the part you
      will read.
    </li>
    <li>
      <strong>Not <code>.rating-static</code> for anything interactive.</strong> It is two
      rows of icons with no inputs. It cannot be focused, changed or posted — if someone
      needs to set a value, use the radio version.
    </li>
    <li>
      <strong>Not where a rating can be given by accident.</strong> There is no way to
      unset one — see <a href="#accessibility">Accessibility</a>.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
