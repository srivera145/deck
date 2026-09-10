<?php
declare(strict_types=1);

$page = [
    'path' => 'components/accordion.php',
    'title' => 'Accordion',
    'level' => 'Beginner',
    'description' => 'Deck\'s .accordion styles native <details> elements and animates them open with ::details-content and interpolate-size — the first time a disclosure could animate to auto height without JavaScript.',
    'documents' => [
        'accordion', 'accordion-body',
    ],

    'component' => 'accordion',
    'accounts' => [
        '07-components.css' => 'documented: the container, the summary row, the chevron, the body and the open animation',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Accordion</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Accordion</h1>
  <p class="lede">
    <code>.accordion</code> is a bordered container for native
    <code>&lt;details&gt;</code> elements. The open and close behaviour, the keyboard
    handling and the accessible state are all the element's; Deck restyles the summary
    row, replaces the disclosure triangle with a chevron, and animates the opening — which
    until recently was not possible in CSS at all.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for a set of sections a reader opens one at a time and mostly leaves closed:
    frequently asked questions, advanced settings, per-item detail in a long list. Unlike
    <a href="tabs.php">tabs</a>, more than one can be open at once and it needs no
    JavaScript at all.
  </p>
  <?php
  docs_example(
      '<div class="accordion" style="max-inline-size:34rem">' . "\n" .
      '  <details open>' . "\n" .
      '    <summary>How is the export generated?</summary>' . "\n" .
      '    <div class="accordion-body">A nightly job reads the project and writes a CSV to your storage bucket.</div>' . "\n" .
      '  </details>' . "\n" .
      '  <details>' . "\n" .
      '    <summary>Can I change the schedule?</summary>' . "\n" .
      '    <div class="accordion-body">Yes — in Settings, under Exports. Times are UTC.</div>' . "\n" .
      '  </details>' . "\n" .
      '  <details>' . "\n" .
      '    <summary>What happens if it fails?</summary>' . "\n" .
      '    <div class="accordion-body">We retry twice, then email the project owner.</div>' . "\n" .
      '  </details>' . "\n" .
      '</div>',
      'No script. Click, or tab to a summary and press Enter.',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="animating">Animating to auto height</h2>
  <p>
    Animating a disclosure open has been a JavaScript problem for twenty years, for one
    reason: you cannot transition to <code>height: auto</code>, and the height is not
    known until the content is laid out. Every solution measured the panel with
    <code>scrollHeight</code> and animated to a pixel value.
  </p>
  <p>
    Two new pieces remove the need. Deck uses both.
  </p>

  <div class="stack-2">
    <h3 id="a-details-content">::details-content</h3>
    <p>
      A pseudo-element wrapping everything after the <code>&lt;summary&gt;</code>. It can
      be styled and transitioned, which the content of a
      <code>&lt;details&gt;</code> previously could not be as a unit.
    </p>
    <pre class="dx-code"><code><?= e('.accordion details::details-content {
  block-size: 0;
  overflow: hidden;
  transition: block-size var(--dur-3) var(--ease-out),
              content-visibility var(--dur-3) allow-discrete;
}
.accordion details[open]::details-content { block-size: auto; }') ?></code></pre>
  </div>

  <div class="stack-2">
    <h3 id="a-interpolate">interpolate-size: allow-keywords</h3>
    <p>
      The transition above animates to <code>block-size: auto</code>, which normally is
      not interpolable. <code>interpolate-size: allow-keywords</code> — set once on
      <code>:root</code> in <code>src/02-reset.css</code>, inside an
      <code>@supports</code> guard — makes the browser interpolate to and from
      intrinsic size keywords. That single declaration is what makes the rule above work.
    </p>
    <p class="text-muted">
      <code>content-visibility</code> is in the transition list with
      <code>allow-discrete</code> for the same reason <code>display</code> is on a
      <a href="modal.php#animation">modal</a>: it is a discrete property, and deferring
      its flip to the end keeps the content rendered while the height animates.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="a-fallback">Where it is unsupported</h3>
    <p>
      A browser that does not know <code>::details-content</code> ignores the rule, and
      the element opens and closes instantly — exactly as a <code>&lt;details&gt;</code>
      always has. Nothing is broken and nothing needs a fallback, which is why Deck ships
      it unguarded.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="chevron">The chevron</h2>
  <p>
    <code>list-style: none</code> and
    <code>::-webkit-details-marker { display: none }</code> remove the platform triangle —
    both are needed, because the two engines hide it differently. The replacement is a
    <code>::after</code>: an 8px square with two borders, rotated 45 degrees, so it reads
    as a chevron. Open rotates it to 225 degrees.
  </p>
  <p>
    <code>margin-inline-start: auto</code> pushes it to the end of the summary row, which
    is the same mechanism as <a href="bar.php#push"><code>.push</code></a> and mirrors
    under <code>dir="rtl"</code> for free.
  </p>
</section>

<section class="stack-3">
  <h2 id="single">One at a time</h2>
  <p>
    Give every <code>&lt;details&gt;</code> in a group the same <code>name</code>
    attribute and the browser closes the others when one opens — an exclusive accordion
    with no script.
  </p>
  <?php
  docs_example(
      '<div class="accordion" style="max-inline-size:34rem">' . "\n" .
      '  <details name="dx-acc">' . "\n" .
      '    <summary>Starter</summary>' . "\n" .
      '    <div class="accordion-body">One project, one collaborator.</div>' . "\n" .
      '  </details>' . "\n" .
      '  <details name="dx-acc">' . "\n" .
      '    <summary>Team</summary>' . "\n" .
      '    <div class="accordion-body">Unlimited projects, shared billing, audit log.</div>' . "\n" .
      '  </details>' . "\n" .
      '  <details name="dx-acc">' . "\n" .
      '    <summary>Enterprise</summary>' . "\n" .
      '    <div class="accordion-body">SSO, a contract, and a support channel.</div>' . "\n" .
      '  </details>' . "\n" .
      '</div>',
      'Open one and the others close — the name attribute does it',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    Exclusive accordions are worth thinking about before using. They stop a reader
    comparing two sections, and closing something the reader opened is a small surprise
    every time. Use them when the sections are alternatives, not when they are a list.
  </p>
</section>

<section class="stack-3">
  <h2 id="disclosure">A single disclosure</h2>
  <p>
    Every rule on this page is written as <code>.accordion …, .disclosure …</code>, so a
    standalone <code>&lt;details class="disclosure"&gt;</code> gets the same summary row,
    chevron and animation without the container's border and dividers. Use it for one
    "show more" in the flow of a page.
  </p>
  <p class="text-muted">
    <code>.disclosure</code> and <code>.disclosure-body</code> are documented on their own
    page rather than here — this page's completeness check covers the
    <code>accordion</code> component only.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/07-components.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--surface-hover', '--line', '--text-faint', '--text-muted', '--text-sm', '--tap', '--r-md', '--space-3', '--space-4', '--dur-1', '--dur-2', '--dur-3']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The element does everything.</strong> <code>&lt;summary&gt;</code> is
      focusable, operable with Enter and Space, and exposed with an expanded state that
      the browser keeps in step with the <code>open</code> attribute. There is no ARIA to
      add and none to get wrong.
    </li>
    <li>
      <strong>Put a heading inside the summary, not around it.</strong>
      <code>&lt;summary&gt;&lt;h3&gt;…&lt;/h3&gt;&lt;/summary&gt;</code> gives the section
      a place in the document outline while keeping the summary as the control. A heading
      wrapping the <code>&lt;details&gt;</code> does not.
    </li>
    <li>
      <strong>Content in a closed section is genuinely hidden</strong> — not in the
      accessibility tree, not in the tab order, and not found by the browser's find-in-page
      in most engines. That is correct behaviour, and it is worth knowing if you expect
      readers to search the page for something inside a closed panel.
    </li>
    <li>
      <strong>Deep links need opening.</strong> A link to an <code>id</code> inside a
      closed <code>&lt;details&gt;</code> will not scroll to it in every browser. Some
      engines now open the ancestor automatically; do not rely on it.
    </li>
    <li>
      <strong>Touch target.</strong> The summary is
      <code>min-block-size: var(--tap)</code> —
      <?= e(api_token('--tap')['value'] ?? '44px') ?> — and full width, so the whole row
      is the target.
    </li>
    <li>
      <strong>The chevron is decorative</strong> and drawn with borders, so it is never
      announced. The expanded state is carried by the element itself, which is where a
      screen reader looks for it.
    </li>
    <li>
      <strong>Hover has no focus counterpart.</strong>
      <code>.accordion summary:hover</code> changes the background;
      <code>:focus-visible</code> gets the global focus ring but not the background. The
      ring is enough, but it is a difference from
      <a href="menu.php"><code>.menu-item</code></a>, which styles both.
    </li>
    <li>
      <strong>Exclusive accordions move focus nowhere</strong>, but they do close a
      section the reader may have been reading. Consider whether that surprise is worth
      the tidiness.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The summary is a flex row with a gap and logical padding, and the chevron is pushed
    with <code>margin-inline-start: auto</code>, so it moves to the left edge under
    <code>dir="rtl"</code> with no extra rules. The chevron's rotation is symmetrical
    about the vertical axis — it points down when open and toward the block start when
    closed — so it does not need mirroring either.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="accordion" style="max-inline-size:34rem">' . "\n" .
      '  <details open>' . "\n" .
      '    <summary>كيف يتم إنشاء التصدير؟</summary>' . "\n" .
      '    <div class="accordion-body">مهمة ليلية تقرأ المشروع وتكتب ملف CSV.</div>' . "\n" .
      '  </details>' . "\n" .
      '  <details>' . "\n" .
      '    <summary>هل يمكنني تغيير الجدول الزمني؟</summary>' . "\n" .
      '    <div class="accordion-body">نعم، من الإعدادات.</div>' . "\n" .
      '  </details>' . "\n" .
      '</div>',
      'The chevron follows the writing direction',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The height animates over <?= e(api_token('--dur-3')['value'] ?? '320ms') ?>, the
    chevron rotates over <?= e(api_token('--dur-2')['value'] ?? '200ms') ?>, and the
    summary background transitions over
    <?= e(api_token('--dur-1')['value'] ?? '120ms') ?>. The global reset collapses all
    three to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>, which returns the element to its native
    instant open — the behaviour it had before the animation existed.
  </p>
  <p class="text-muted">
    This is a good example of an enhancement degrading to the platform default rather than
    to something broken. Nothing has to be conditionally disabled, because the thing
    underneath already worked.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    There is no <code>.accordion</code> rule in <code>src/99-print.css</code>, so a
    printed accordion shows open sections and hides closed ones — the closed content is
    genuinely not rendered, so it cannot print.
  </p>
  <p class="dx-note text-muted">
    For a page whose accordions hold the actual content — an FAQ, a policy — that means
    most of the page does not print. One rule fixes it, and it is recorded in
    <code>FINDINGS.md</code>:
    <code>@media print { .accordion details { open: true } }</code> is not possible in
    CSS, but <code>.accordion details::details-content { block-size: auto !important;
    content-visibility: visible !important }</code> comes close.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A chevron that rotates the other way */
  .accordion summary::after { rotate: -45deg; }
  .accordion details[open] summary::after { rotate: 45deg; }

  /* No animation, for a very long list */
  .accordion-instant details::details-content { transition: none; }

  /* A plus/minus instead of a chevron */
  .accordion summary::after { content: "+"; border: 0; rotate: none; }
  .accordion details[open] summary::after { content: "−"; }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for content most readers need.</strong> Hiding something behind a click
      means most people never see it — and it will not print or be found by a page search.
      An accordion is for the minority case, not the majority one.
    </li>
    <li>
      <strong>Not for one of several views.</strong> Use <a href="tabs.php">tabs</a>, or
      the exclusive <code>name</code> attribute if you want the accordion shape with
      one-at-a-time behaviour.
    </li>
    <li>
      <strong>Not for a navigation menu.</strong> Use
      <a href="menu.php"><code>.menu</code></a> or a
      <a href="drawer.php"><code>.drawer</code></a>. A details element in a nav does not
      close when the reader clicks elsewhere.
    </li>
    <li>
      <strong>Not to make a long page look short.</strong> The page is the same length;
      the reader now has to click eight times to read it. If the content is genuinely long,
      split it across pages.
    </li>
    <li>
      <strong>Not with a form inside a closed section.</strong> Fields that are not
      rendered do not validate and are not focusable, so a required field inside a closed
      panel produces a submit that fails with nothing visible to fix.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
