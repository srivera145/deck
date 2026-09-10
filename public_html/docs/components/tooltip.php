<?php
declare(strict_types=1);

$page = [
    'path' => 'components/tooltip.php',
    'title' => 'Tooltip',
    'level' => 'Intermediate',
    'description' => 'Deck has two tooltips: .tooltip, a CSS-only ::after that cannot flip and is hidden on touch, and .tip, an anchor-positioned popover that flips, shifts and carries a real arrow.',
    'documents' => [
        'tooltip', 'tip', 'tip-arrow',
    ],

    'component' => 'tip',
    'accounts' => [
        '25-anchor.css' => 'documented: .tip and .tip-arrow, their anchor positioning and the flip fallbacks',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Tooltip</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Tooltip</h1>
  <p class="lede">
    Deck ships two, and the difference is worth understanding before choosing.
    <code>.tooltip</code> is a <code>::after</code> driven by
    <code>data-tip</code> — no JavaScript, no extra element, and no ability to flip when
    it hits the top of the window. <code>.tip</code> is a real popover positioned by CSS
    anchor positioning, which can flip, shift, and carry an arrow that stays pointed at
    its trigger.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use one</h2>
  <p>
    A tooltip names something whose meaning is not obvious — an icon-only button, an
    abbreviation, a truncated value. It should be a <strong>label</strong>: a few words,
    no interaction, nothing the reader has to act on.
  </p>
  <p>
    If it holds a sentence, a link or a button, it is not a tooltip. That is
    <a href="popover.php"><code>.pop</code></a>, which the reader can move into and
    interact with.
  </p>
  <p class="dx-note text-muted">
    Both tooltips here appear on hover and keyboard focus and cannot be reached by a
    pointer — they are labels, not containers. Anything a reader needs to click is in the
    wrong component.
  </p>
</section>

<section class="stack-6">
  <h2 id="css-tooltip">.tooltip — the CSS-only one</h2>
  <p>
    Put <code>.tooltip</code> on the trigger and the text in <code>data-tip</code>. The
    <code>::after</code> takes its content from the attribute, so there is no second
    element and nothing to keep in sync.
  </p>
  <?php
  docs_example(
      '<button class="btn btn-icon tooltip" data-tip="Duplicate this export" aria-label="Duplicate this export">' . "\n" .
      '  <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#copy"></use></svg>' . "\n" .
      '</button>' . "\n" .
      '<button class="btn tooltip" data-tip="Runs the nightly job again from the beginning">Rerun</button>',
      'Hover, or tab to it',
      'cluster'
  );
  ?>

  <div class="stack-2">
    <h3 id="t-limits">What it cannot do</h3>
    <ul class="stack-2">
      <li>
        <strong>It cannot flip.</strong> It is pinned above the trigger with
        <code>inset-block-end: calc(100% + 8px)</code>. Near the top of the window it is
        clipped or scrolled off, and nothing repositions it.
      </li>
      <li>
        <strong>It cannot escape an overflow.</strong> The <code>::after</code> is a child
        of the trigger, so an ancestor with <code>overflow: hidden</code> — a table
        wrapper, a card, a scroller — cuts it off.
      </li>
      <li>
        <strong>It is hidden entirely on touch.</strong>
        <code>@media (pointer: coarse) { .tooltip::after { display: none } }</code>.
        There is no hover on a phone, and a tooltip that appears on tap and blocks the
        thing you tapped is worse than none.
      </li>
    </ul>
    <p class="dx-note text-muted">
      That last one is the rule that matters: <strong>on a phone this tooltip does not
      exist</strong>. Anything in a <code>data-tip</code> is invisible to a touch reader,
      so it must never be the only place information lives. Pair it with
      <code>aria-label</code>, as the example above does.
    </p>
  </div>
</section>

<section class="stack-6">
  <h2 id="tip">.tip — the anchor-positioned one</h2>
  <p>
    <code>.tip</code> is a popover element with <code>position-area: block-start</code>
    and <code>position-try-fallbacks: flip-block, flip-inline</code>. When there is no
    room above, it goes below; when it would overflow an edge, it flips inline. The
    browser does that on the compositor, with no scroll listener and no measurement.
  </p>
  <?php
  docs_example(
      '<button class="btn" popovertarget="dx-tip">What is a webhook?</button>' . "\n" .
      '<div class="tip" popover id="dx-tip" role="tooltip">' . "\n" .
      '  A callback URL we POST to when something happens in your project.' . "\n" .
      '  <span class="tip-arrow"></span>' . "\n" .
      '</div>',
      'Opens on click here, because a popover has no hover trigger of its own',
      'stack'
  );
  ?>
  <p>
    <code>justify-self: anchor-center</code> centres it on the trigger, and
    <code>.tip-arrow</code> uses the same declaration so the arrow stays pointed at the
    anchor whichever way the tip has flipped. The arrow is a rotated square with
    <code>background: inherit</code>, so it takes the tip's colour automatically.
  </p>
  <p class="dx-note text-muted">
    A popover opens on click, not hover. That makes <code>.tip</code> behave more like a
    small popover than a true tooltip — for hover-and-focus behaviour you need a few lines
    of JavaScript calling <code>showPopover()</code> on <code>pointerenter</code> and
    <code>focus</code>. Deck does not ship that, which is the honest gap in this
    component.
  </p>
</section>

<section class="stack-3">
  <h2 id="choosing">Choosing between them</h2>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Comparison of the two tooltip mechanisms</caption>
      <thead>
        <tr><th scope="col"></th><th scope="col">.tooltip</th><th scope="col">.tip</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="">Markup</th>
          <td data-label=".tooltip">One attribute</td>
          <td data-label=".tip">A popover element plus an id</td>
        </tr>
        <tr>
          <th scope="row" data-label="">Opens on</th>
          <td data-label=".tooltip">Hover and focus, free</td>
          <td data-label=".tip">Click, unless you add JS</td>
        </tr>
        <tr>
          <th scope="row" data-label="">Flips near an edge</th>
          <td data-label=".tooltip">No</td>
          <td data-label=".tip">Yes</td>
        </tr>
        <tr>
          <th scope="row" data-label="">Escapes overflow: hidden</th>
          <td data-label=".tooltip">No</td>
          <td data-label=".tip">Yes — it is in the top layer</td>
        </tr>
        <tr>
          <th scope="row" data-label="">On touch</th>
          <td data-label=".tooltip">Hidden entirely</td>
          <td data-label=".tip">Works — it opens on tap</td>
        </tr>
      </tbody>
    </table>
  </div>
  <p>
    In short: <code>.tooltip</code> for a short label on a control that is not near an
    edge and not inside a clipping ancestor, where the information is also available some
    other way. <code>.tip</code> when position matters or touch readers need it.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.tooltip</code> lives in <code>07-components.css</code> and is not part of the
    <code>tip</code> component; it is listed here because this page documents it.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    Both use the same near-black on near-white pairing —
    <code>--ink-900</code> and <code>--ink-50</code> — which is fixed rather than
    theme-dependent, so a tooltip is dark in light mode and dark in dark mode. That is
    deliberate: a tooltip should read as a layer above the page, not as part of it.
  </p>
  <?php docs_token_table(['--ink-900', '--ink-50', '--r-xs', '--text-xs', '--shadow-3', '--space-2', '--space-3', '--dur-2', '--z-toast']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong><code>data-tip</code> is not an accessible name.</strong> A
      <code>::after</code>'s generated content is exposed inconsistently and should never
      be relied on. An icon-only button with a tooltip needs
      <code>aria-label</code> as well — the tooltip is for sighted mouse users, the label
      is for everyone else.
    </li>
    <li>
      <strong>That means the text is written twice</strong>, in the attribute and in the
      label, with nothing keeping them in step. It is the cost of the CSS-only approach,
      and it is worth knowing before choosing it.
    </li>
    <li>
      <strong>The CSS tooltip does appear on <code>:focus-visible</code></strong>, so a
      keyboard user sees it. It does not appear on hover of a non-focusable element unless
      that element can take focus.
    </li>
    <li>
      <strong>Neither can be hovered into.</strong> <code>.tooltip::after</code> is
      <code>pointer-events: none</code>. WCAG's hoverable criterion asks that a reader be
      able to move the pointer onto a tooltip to read it — which these cannot satisfy, and
      which is another reason to keep them to a few words.
    </li>
    <li>
      <strong>Neither is dismissible with Escape</strong> in the CSS-only case. WCAG asks
      for that too. <code>.tip</code>, being a popover, does close on Escape.
    </li>
    <li>
      <strong>Nothing on a touch device sees <code>.tooltip</code>.</strong> The
      information has to exist elsewhere.
    </li>
    <li>
      <strong>Give <code>.tip</code> a role.</strong> <code>role="tooltip"</code> on the
      popover, and <code>aria-describedby</code> from the trigger, is what associates the
      two. The examples here show the role; the association needs the attribute on the
      trigger as well.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>.tooltip::after</code> is centred with
    <code>inset-inline-start: 50%</code> and <code>translate: -50%</code>, which is
    symmetrical and therefore correct in both directions.
    <code>.tip</code> uses <code>justify-self: anchor-center</code> and logical
    <code>position-area</code> keywords, so its flip fallbacks follow the writing
    direction.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="cluster">' . "\n" .
      '  <button class="btn tooltip" data-tip="أعد تشغيل المهمة الليلية" aria-label="أعد تشغيل المهمة الليلية">إعادة التشغيل</button>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    <code>.tooltip::after</code> fades and rises four pixels;
    <code>.tip</code> fades. Both are transitions, so the global reset collapses them to
    <code>.01ms</code> under <code>prefers-reduced-motion: reduce</code> and the tooltip
    simply appears.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Neither has a print rule. <code>.tooltip::after</code> is
    <code>opacity: 0</code> unless hovered, so it does not print — a tooltip is a hover
    state and paper has no hover. A <code>.tip</code> that happened to be open would print
    where it is.
  </p>
  <p class="text-muted">
    The consequence is worth stating: <strong>anything in a tooltip is absent from the
    printed page</strong>, for the same reason it is absent on touch. It is decoration on
    top of information that must exist elsewhere.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Tooltip below the trigger instead of above */
  .tooltip-below::after {
    inset-block-end: auto;
    inset-block-start: calc(100% + 8px);
  }

  /* Let it show on touch after all — think hard first */
  @media (pointer: coarse) {
    .tooltip-touch::after { display: block; }
  }
}') ?></code></pre>
  <p class="text-muted">
    Before re-enabling tooltips on touch: there is no hover, so it will only appear on tap
    — over the top of the control the reader just tapped, at the moment they are acting on
    it.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for anything essential.</strong> Hidden on touch, absent in print,
      unreliable to screen readers. If a reader needs it to complete a task, put it on the
      page — a <code>.help</code> line under a field costs nothing.
    </li>
    <li>
      <strong>Not for interactive content.</strong> Neither can be hovered into, so a link
      or a button inside one is unreachable. Use
      <a href="popover.php"><code>.pop</code></a>.
    </li>
    <li>
      <strong>Not as a replacement for a label.</strong> An icon-only button needs
      <code>aria-label</code> whether or not it has a tooltip.
    </li>
    <li>
      <strong>Not inside a table cell or a scroller</strong>, if you are using
      <code>.tooltip</code> — the clipping ancestor will cut it off. <code>.tip</code>
      escapes to the top layer and does not have this problem.
    </li>
    <li>
      <strong>Not for a paragraph.</strong> <code>max-inline-size: 16rem</code> is a
      deliberate ceiling. If the explanation does not fit in it, the explanation belongs
      somewhere a reader can keep it open while they work.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
