<?php
declare(strict_types=1);

$page = [
    'path' => 'components/gradients.php',
    'title' => 'Gradients',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .g-* classes: every gradient built from the brand hue and interpolated in oklab, covering scrims, fade masks, patterns and surface washes — plus an honest account of the ones that do not earn their place.',
    'documents' => [
        'g-surface', 'g-sunken', 'g-brand', 'g-brand-soft', 'g-dark',
        'g-mesh', 'g-mesh-drift', 'g-mesh-subtle', 'g-mesh-warm',
        'g-text', 'g-text-shine',
        'g-border', 'g-border-soft', 'g-border-spin', 'g-hairline',
        'g-scrim', 'g-scrim-top',
        'g-fade-inline', 'g-fade-end', 'g-fade-block', 'g-fade-more',
        'g-sheen', 'g-good', 'g-warn', 'g-bad',
        'g-conic', 'g-ring', 'g-ring-spin',
        'g-grid-lines', 'g-dots', 'g-stripes', 'g-hatch',
        'g-shimmer', 'g-bar-fill',
    ],
];

require __DIR__ . '/../_layout.php';

/* The verdict on every class in the file, worked out by trying to write a case
   for each. Kept as data so the summary below cannot drift from the table. */
$verdicts = [
    ['g-scrim',       'keep', 'Text over a photo. Eased rather than a flat wash, so it darkens only where the text sits. Shared with .carousel-caption through the --scrim token.'],
    ['g-scrim-top',   'keep', 'The same from the block start, for a caption above an image.'],
    ['g-fade-inline', 'keep', 'Fades content at both inline edges instead of cutting it. This is what a .scroller should have at its edges.'],
    ['g-fade-end',    'keep', 'Fades at the end edge only — a row that continues off-screen in one direction.'],
    ['g-fade-block',  'keep', 'Fades the bottom of a truncated article, under a "read more".'],
    ['g-grid-lines',  'keep', 'A grid pattern with a --cell size. A gradient standing in for an image: nothing to download, and it retints with --line.'],
    ['g-dots',        'keep', 'A dot pattern, same idea, same argument.'],
    ['g-hatch',       'keep', 'Diagonal hatching in the warn colour, for an unavailable block in a schedule. A genuine job with no other component for it.'],
    ['g-stripes',     'keep', 'Neutral stripes, for a zone that is inactive rather than unavailable.'],
    ['g-hairline',    'keep', 'A glowing one-pixel edge that fades at both ends — a nav or modal top edge. Used nowhere yet, which is a gap in the demo rather than a fault in the class.'],
    ['g-border',      'keep', 'A gradient border, via the padding-box/border-box trick. Genuinely awkward to write from memory, which is a good reason for it to be a class.'],
    ['g-brand',       'keep', 'A brand-filled panel with --text-on-brand applied, so the text contrast is handled.'],
    ['g-brand-soft',  'keep', 'The tinted version, for a highlighted card.'],
    ['g-dark',        'keep', 'A dark panel — a terminal block, a dark hero band.'],
    ['g-surface',     'keep', 'A quiet wash behind a section. Designed not to fight content.'],
    ['g-sunken',      'keep', 'The inverse, for a footer or a well.'],
    ['g-good',        'keep', 'Status fill with --text-on-good. The computed text colour is the reason to have it rather than write a gradient inline.'],
    ['g-warn',        'keep', 'As above.'],
    ['g-bad',         'keep', 'As above.'],
    ['g-mesh',        'keep', 'Three soft radial blooms for a marketing hero. No image, no SVG filter, no blur — blur over a large area is expensive and this reads the same.'],
    ['g-text',        'keep', 'Gradient headline text via background-clip. Needs care — see Accessibility — but it is a real, common request.'],
    ['chart-area-g',  'keep', 'The gradient fill under an area chart line. Documented on the charts page.'],

    ['g-mesh-subtle', 'weak', 'A second copy of the mesh with lower alpha and one fewer bloom. Defensible — the full mesh is too strong behind text — but it is a variant written out longhand rather than a modifier. A --mesh-alpha would cover both.'],
    ['g-fade-more',   'weak', 'A stronger .g-fade-block. A variant of a variant; the mask distance could be a custom property.'],
    ['g-border-soft', 'weak', 'A softer .g-border. Same objection.'],
    ['g-ring',        'weak', 'A conic ring with a mask. Plausible as a progress ring or an avatar ring, but nothing in Deck uses it that way and .donut already draws a ring from a value.'],
    ['g-conic',       'weak', 'A full-hue conic wheel. I cannot name what it is for. A colour picker, maybe — Deck does not ship one.'],

    ['g-mesh-warm',   'demote', 'Sets --hue-brand: 34 and nothing else. It is not a gradient; it is one arbitrary hue with a name. style="--hue-brand:34" does the same thing and says what it does. Used nowhere.'],
    ['g-shimmer',     'demote', 'Runs the same dk-shimmer keyframes as .skeleton, at 1.6s instead of 1.4s. A second name for a loading shimmer that .skeleton already provides.'],
    ['g-bar-fill',    'demote', 'A vertical gradient on --series. .chart-bar-fill and .progress already fill bars; this is a third way to do it.'],
    ['g-text-shine',  'demote', 'An animated sweep across gradient text. Decoration with no stated job, on top of text that is already hard to make accessible.'],
    ['g-sheen',       'demote', 'A highlight that sweeps on hover. Decoration. Pleasant, and it belongs in a page\'s own stylesheet rather than in a frozen framework API.'],
    ['g-border-spin', 'demote', 'A rotating gradient border. Decoration.'],
    ['g-ring-spin',   'demote', 'A spinning conic ring at 1.6s. Reads as a loading indicator, which .spinner already is.'],
    ['g-mesh-drift',  'demote', 'Drifts the mesh over 22 seconds. Reduced-motion guarded, so it is not harmful — but "the background moves slowly" is not a job, and a hero that never holds still is a cost the reader pays for nothing.'],
];
$counts = array_count_values(array_column($verdicts, 1));
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Gradients</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Gradients</h1>
  <p class="lede">
    Thirty-four <code>.g-*</code> classes, every one built from the brand hue and
    interpolated in <code>oklab</code> or <code>oklch</code> — which is what avoids the
    grey dead zone you get when sRGB blends two saturated colours. Change
    <code>--hue-brand</code> and all of them retune together.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use them</h2>
  <p>
    The useful ones fall into three groups, and they are worth separating because they
    are not the same kind of thing at all:
  </p>
  <ul class="stack-2">
    <li>
      <strong>Gradients doing a job</strong> — a scrim making text legible over a photo, a
      fade telling the reader content continues past an edge, a pattern standing in for a
      background image. These earn their place.
    </li>
    <li>
      <strong>Gradients as a surface</strong> — a brand-filled panel that also sets the
      right text colour. The gradient is incidental; the paired text colour is the point.
    </li>
    <li>
      <strong>Gradients as decoration</strong> — a sweep, a shine, a slow drift. Pleasant,
      and the part of this file that does not survive being asked what it is for.
    </li>
  </ul>
  <p class="dx-note text-muted">
    That third group is the subject of <a href="#verdict">the audit below</a>, which is
    the most useful thing on this page.
  </p>
</section>

<section class="stack-6">
  <h2 id="jobs">The ones doing a job</h2>

  <div class="stack-2">
    <h3 id="j-scrim">Scrims</h3>
    <p>
      <code>.g-scrim</code> puts a legible floor under text laid over an image. It is
      eased rather than flat — a 50% black wash dulls the whole picture, while this only
      darkens where the text actually sits. The stops now live in the
      <code>--scrim</code> token, shared with
      <a href="carousel.php#captions"><code>.carousel-caption</code></a>.
    </p>
    <?php
    docs_example(
        '<figure class="g-scrim" style="position:relative;border-radius:var(--r-md);overflow:clip;max-inline-size:26rem">' . "\n" .
        '  <img alt="" src="data:image/svg+xml;utf8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22640%22 height=%22300%22%3E%3Crect width=%22640%22 height=%22300%22 fill=%22%23c9d4cf%22/%3E%3C/svg%3E">' . "\n" .
        '  <figcaption style="position:absolute;inset-block-end:0;inset-inline:0;padding:var(--space-4);color:oklch(98% 0 0)">' . "\n" .
        '    Readable on a light image, because the scrim is underneath' . "\n" .
        '  </figcaption>' . "\n" .
        '</figure>',
        '',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="j-fade">Fade masks</h3>
    <p>
      Content that runs off an edge should fade rather than be cut with a hard line — the
      fade is what tells a reader there is more. <code>.g-fade-inline</code> fades both
      inline edges, <code>.g-fade-end</code> only the end, and
      <code>.g-fade-block</code> the bottom of a truncated block.
    </p>
    <?php
    docs_example(
        '<div class="g-fade-block" style="max-block-size:7rem;overflow:hidden;max-inline-size:34rem">' . "\n" .
        '  <p>A fade at the bottom of a truncated block says the text continues. A hard cut says the text ended, which is a different claim and usually a false one.</p>' . "\n" .
        '  <p>This paragraph is the part the reader cannot see, and the fade is what tells them it exists.</p>' . "\n" .
        '</div>',
        '',
        'stack'
    );
    ?>
    <p class="dx-note text-muted">
      A mask is visual only. The hidden text is still in the DOM, still read by a screen
      reader, still found by a page search — so a fade must never be the only thing
      standing between the reader and content they need.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="j-patterns">Patterns</h3>
    <p>
      The source calls these "gradients standing in for an image", which is exactly right:
      they cost nothing to download and they retint with the theme.
      <code>--cell</code> sets the spacing on the two repeating ones.
    </p>
    <?php
    docs_example(
        '<div class="grid grid-tight">' . "\n" .
        '  <div class="g-grid-lines" style="block-size:6rem;border:1px solid var(--line);border-radius:var(--r-md)"></div>' . "\n" .
        '  <div class="g-dots" style="block-size:6rem;border:1px solid var(--line);border-radius:var(--r-md)"></div>' . "\n" .
        '  <div class="g-stripes" style="block-size:6rem;border:1px solid var(--line);border-radius:var(--r-md)"></div>' . "\n" .
        '  <div class="g-hatch" style="block-size:6rem;border:1px solid var(--line);border-radius:var(--r-md)"></div>' . "\n" .
        '</div>',
        'Grid lines, dots, stripes and hatch — no images involved',
        'stack'
    );
    ?>
    <p class="text-muted">
      <code>.g-hatch</code> is tinted with the warn colour and is the one with a specific
      job: an unavailable block in a schedule. <code>.g-stripes</code> is neutral, for a
      zone that is merely inactive.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="j-surfaces">Surfaces</h3>
    <p>
      The point of these is not the gradient — it is that each also sets the correct text
      colour. <code>.g-brand</code> applies <code>--text-on-brand</code>, and the status
      three apply <code>--text-on-good</code> and friends, all of which are computed
      against the fill rather than hard-coded.
    </p>
    <?php
    docs_example(
        '<div class="grid grid-tight">' . "\n" .
        '  <div class="g-brand" style="padding:var(--space-4);border-radius:var(--r-md)">g-brand</div>' . "\n" .
        '  <div class="g-good" style="padding:var(--space-4);border-radius:var(--r-md)">g-good</div>' . "\n" .
        '  <div class="g-warn" style="padding:var(--space-4);border-radius:var(--r-md)">g-warn</div>' . "\n" .
        '  <div class="g-bad" style="padding:var(--space-4);border-radius:var(--r-md)">g-bad</div>' . "\n" .
        '</div>',
        'The text colour comes with the fill',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="j-border">Gradient borders</h3>
    <p>
      <code>.g-border</code> uses the two-background trick: a flat fill clipped to
      <code>padding-box</code> over a gradient clipped to <code>border-box</code>, with a
      transparent border letting the second show through. It is awkward enough to write
      from memory that having it as a class is justified on its own.
    </p>
    <?php
    docs_example(
        '<div class="g-border" style="padding:var(--space-4);border-radius:var(--r-md);max-inline-size:22rem">' . "\n" .
        '  A gradient border, one class' . "\n" .
        '</div>',
        '',
        'stack'
    );
    ?>
  </div>

  <div class="stack-2">
    <h3 id="j-mesh">Mesh</h3>
    <p>
      <code>.g-mesh</code> is three soft radial blooms — no image, no SVG filter, and
      deliberately no <code>blur()</code>, because blurring a large area is expensive and
      overlapping radial gradients read the same.
    </p>
    <?php
    docs_example(
        '<div class="g-mesh" style="block-size:9rem;border-radius:var(--r-md);border:1px solid var(--line)"></div>',
        '',
        'stack'
    );
    ?>
  </div>
</section>

<section class="stack-6">
  <h2 id="verdict">The audit</h2>
  <p>
    The brief for this page was to justify all thirty-four one at a time, and to say so if
    some could not be. Some cannot. Here is the whole file with a verdict on each, worked
    out by trying to write a case a reader would care about and noticing when I could not.
  </p>
  <div class="cluster">
    <span class="badge badge-good"><?= (int) ($counts['keep'] ?? 0) ?> keep</span>
    <span class="badge badge-warn"><?= (int) ($counts['weak'] ?? 0) ?> weak</span>
    <span class="badge badge-bad"><?= (int) ($counts['demote'] ?? 0) ?> demote</span>
  </div>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">A verdict on every gradient class</caption>
      <thead>
        <tr><th scope="col">Class</th><th scope="col">Verdict</th><th scope="col">The case, or the absence of one</th></tr>
      </thead>
      <tbody>
        <?php foreach ($verdicts as [$name, $verdict, $why]): ?>
          <tr>
            <th scope="row" data-label="Class"><code><?= e('.' . $name) ?></code></th>
            <td data-label="Verdict">
              <span class="badge badge-<?= $verdict === 'keep' ? 'good' : ($verdict === 'weak' ? 'warn' : 'bad') ?>"><?= e($verdict) ?></span>
            </td>
            <td data-label="The case, or the absence of one"><?= e($why) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="stack-3">
    <h3 id="v-recommendation">What I would do</h3>
    <p>
      <strong>Demote the eight.</strong> They are not broken and they are not ugly — they
      are decoration, duplicates, or a preset for one arbitrary value. What makes them a
      problem is that they are in a <em>frozen public API</em>: every one is a name Deck
      has promised to keep, and a name someone has to read past to find the classes that
      do a job.
    </p>
    <p>
      <code>.g-mesh-warm</code> is the clearest case and worth stating on its own. It
      contains one declaration — <code>--hue-brand: 34</code>. It is not a gradient at
      all; it is a named preset for a single hue, and
      <code>style="--hue-brand:34"</code> does the same thing while saying what it does.
      Nothing uses it.
    </p>
    <p>
      <code>.g-shimmer</code> and <code>.g-bar-fill</code> are the other unambiguous ones:
      both are second implementations of something Deck already has —
      <code>.g-shimmer</code> literally runs <code>.skeleton</code>'s own
      <code>dk-shimmer</code> keyframes, at 1.6s instead of 1.4s.
    </p>
    <p>
      <strong>Fold the five weak ones into properties.</strong>
      <code>.g-mesh-subtle</code>, <code>.g-fade-more</code> and
      <code>.g-border-soft</code> are each a copy of their sibling with different numbers.
      A custom property on the base class — <code>--mesh-alpha</code>,
      <code>--fade-distance</code>, <code>--g-width</code> — replaces all three and matches
      how the rest of Deck is built. <code>.g-border</code> already has
      <code>--g-width</code>; the pattern exists and was simply not applied here.
    </p>
    <p class="dx-note text-muted">
      That would take the file from thirty-four public classes to twenty-one, with nothing
      lost that a page could not do for itself in three lines of its own CSS. It is
      recorded in <code>FINDINGS.md</code> and deliberately not acted on here — this pass
      documents, and removing eight frozen classes is a decision rather than a
      documentation task.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/20-gradients.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    <code>--hue-brand</code> is the one that matters: every gradient in the file is
    computed from it, so the whole set retunes when it changes.
  </p>
  <?php docs_token_table(['--hue-brand', '--scrim', '--brand-400', '--brand-500', '--brand-600', '--brand-soft', '--text-on-brand', '--good-600', '--warn-500', '--bad-600', '--line', '--line-strong', '--surface', '--bg', '--dur-5']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong><code>.g-text</code> is the one to be careful with.</strong> Gradient text
      is <code>background-clip: text</code> with a transparent fill, so its contrast varies
      along its own length — one end may pass and the other fail. Use it on large display
      text only, never on body copy, and check the lightest point rather than the average.
    </li>
    <li>
      <strong>Windows High Contrast removes background images entirely.</strong> Every
      class here is a <code>background-image</code>, so in forced-colours mode they all
      disappear. Anything carrying meaning — <code>.g-hatch</code> marking a block
      unavailable — needs a non-gradient signal as well.
    </li>
    <li>
      <strong>Text on a gradient needs its contrast checked at the worst point.</strong>
      <code>.g-brand</code> and the status three set a computed text colour, which handles
      the common case; a gradient you write yourself does not.
    </li>
    <li>
      <strong>Fade masks hide content visually only.</strong> The text behind a
      <code>.g-fade-block</code> is still announced and still searchable. That is usually
      what you want, and it means the fade is not a substitute for truncation.
    </li>
    <li>
      <strong>The animated ones are all reduced-motion guarded.</strong>
      <code>.g-mesh-drift</code>, <code>.g-text-shine</code>,
      <code>.g-border-spin</code>, <code>.g-ring-spin</code> and the
      <code>.g-sheen</code> hover are each declared inside
      <code>@media (prefers-reduced-motion: no-preference)</code>, so they are never
      declared at all rather than declared and cancelled. That is the right construction,
      and it is consistent across the file.
    </li>
    <li>
      <strong>A drifting background is still motion for everyone else.</strong> Being
      guarded does not make ambient animation free — it competes with the content for
      attention, and <code>prefers-reduced-motion</code> is a setting most people have
      never found.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Most of these are symmetrical or angle-based and need nothing.
    <code>.g-fade-inline</code> and <code>.g-fade-end</code> are the exceptions worth
    knowing about: they are built from <code>mask-image</code> with a direction, and a
    mask has no logical form — so <code>.g-fade-end</code> fades the physical end of the
    box rather than the writing direction's end.
  </p>
  <p class="text-muted">
    <code>.g-hairline</code>'s gradient runs <code>90deg</code> and is symmetrical about
    its centre, so it looks identical either way.
  </p>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Five classes animate, and every one is inside
    <code>@media (prefers-reduced-motion: no-preference)</code> rather than relying on the
    global reset. That means a reader who has asked for less motion never receives the
    declaration at all — the cleanest form of the guard, and worth copying.
  </p>
  <p class="text-muted">
    <code>.g-border-spin</code> and <code>.g-ring-spin</code> animate a registered
    <code>--g-angle</code> property, which is why the rotation interpolates smoothly
    rather than jumping. Registering a property is what makes a custom property
    animatable at all.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    None of these are in <code>src/99-print.css</code>, and browsers drop background
    images when printing unless told otherwise — so every gradient on this page vanishes
    on paper. For decoration that is correct and saves ink.
  </p>
  <p class="dx-note text-muted">
    For <code>.g-hatch</code> it is a defect: a schedule block marked unavailable by
    hatching prints as an ordinary empty block, with the meaning silently removed. Same
    class of problem as the chart fills, which <em>are</em> in the
    <code>print-color-adjust: exact</code> list. Recorded in <code>FINDINGS.md</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('/* Per-instance, no layer needed */
<div class="g-grid-lines" style="--cell:32px">
<div class="g-border" style="--g-width:3px;--g-angle:90deg">

@layer app.components {
  /* The subtle mesh as a property rather than a second class */
  .g-mesh { --mesh-alpha: .30; }
  .g-mesh-quiet { --mesh-alpha: .12; }
}') ?></code></pre>
  <p class="text-muted">
    That last one does not work today — <code>.g-mesh</code> writes its alphas inline in
    the gradient stops. Making it work is the change
    <a href="#v-recommendation">recommended above</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use them</h2>
  <ul class="stack-3">
    <li>
      <strong>Not behind body text.</strong> <code>.g-mesh</code> and the washes are
      designed to sit behind content, but a busy background under a paragraph costs
      legibility for nothing. Use <code>.g-mesh-subtle</code>, or nothing.
    </li>
    <li>
      <strong>Not for gradient body copy.</strong> <code>.g-text</code> on anything
      smaller than a display heading is a contrast problem that varies along the line.
    </li>
    <li>
      <strong>Not to carry meaning on their own.</strong> Forced-colours mode removes
      every one of them. A hatched block needs a label too.
    </li>
    <li>
      <strong>Not the decorative eight, in an application.</strong> A sweep, a shine and a
      drifting hero belong on a marketing page, and a marketing page can write them in
      three lines of its own CSS. See <a href="#verdict">the audit</a>.
    </li>
    <li>
      <strong>Not as a substitute for a component.</strong> <code>.g-ring-spin</code>
      looks like a loading indicator; <code>.spinner</code> <em>is</em> one, and carries
      the reduced-motion exemption that keeps it visible.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
