<?php
declare(strict_types=1);

/**
 * Cascade layers — the "why doesn't my CSS work" guide.
 *
 * Task-shaped on purpose. Nobody searches for "cascade layers"; they search
 * for why their override is being ignored, and they arrive having already
 * tried !important.
 */

$page = [
    'path' => 'guides/layers.php',
    'title' => 'Override Deck without !important',
    'level' => 'Intermediate',
    'description' => "Your CSS is losing to the framework. Here is why cascade layers make your rules win regardless of specificity, and which of the four app slots to put them in.",
];

require __DIR__ . '/../_layout.php';

$LAYERS = [
    ['deck.reset', 'Normalize and element defaults', false],
    ['deck.tokens', 'Custom properties only', false],
    ['deck.type', 'Headings, prose, links', false],
    ['deck.layout', 'Containers, stack, grid', false],
    ['deck.components', 'Buttons, forms, cards, charts', false],
    ['deck.mobile', 'Sheets, tab bar, icons', false],
    ['deck.motion', 'Transitions, entrances, view transitions', false],
    ['deck.effects', 'Gradients, 3D, surface treatments', false],
    ['deck.utilities', 'Single-purpose classes', false],
    ['deck.rtl', 'Direction fixes — must beat the utilities', false],
    ['deck.print', 'Paper', false],
    ['app.base', 'Your resets and element styles', true],
    ['app.components', 'Your components, and your Deck overrides', true],
    ['app.pages', 'Per-page and per-view tweaks', true],
    ['app.overrides', 'The escape hatch, still not !important', true],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Guides</li>
    <li aria-current="page">Override Deck without !important</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Override Deck without <code>!important</code></h1>
  <p class="lede">
    You wrote a rule, it is not being applied, and the framework's rule is winning in dev
    tools. The normal fix is a longer selector, and then <code>!important</code>, and then
    <code>!important</code> on the framework's side too. Deck ends that argument before it
    starts: put your CSS in a layer and it wins, however short your selector is.
  </p>
</header>

<section class="stack-4">
  <h2 id="answer">The short answer</h2>
  <p>
    Wrap your CSS in <code>@layer app.pages</code>. That is it.
  </p>
  <pre class="dx-code"><code>@layer app.pages {
  .card { border-radius: 0; }
}</code></pre>
  <p>
    One class, no <code>!important</code>, and it beats <code>.card</code> in
    <code>deck.components</code> even though the two selectors are identical. If you only
    take one thing from this page, take that.
  </p>
  <p>
    Deck declares the order before any rule exists, in
    <code>src/00-layers.css</code>. Fifteen layers, eleven of them Deck's and four of them
    yours, and later beats earlier:
  </p>
  <pre class="dx-code"><code>@layer
  deck.reset, deck.tokens, deck.type, deck.layout, deck.components,
  deck.mobile, deck.motion, deck.effects, deck.utilities, deck.rtl, deck.print,

  app.base, app.components, app.pages, app.overrides;</code></pre>
</section>

<section class="stack-4">
  <h2 id="why">Why that works when specificity says it should not</h2>
  <p>
    The cascade decides a winner by asking a series of questions in order, and stopping at
    the first one that separates the candidates. Most people know the list as: importance,
    then specificity, then source order. Layers insert a step, and it is earlier than
    specificity:
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The order the cascade asks its questions in</caption>
      <thead>
        <tr><th scope="col">Asked</th><th scope="col">Question</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="Asked">1st</th><td data-label="Question">Origin and importance — is one of them <code>!important</code>?</td></tr>
        <tr><th scope="row" data-label="Asked">2nd</th><td data-label="Question"><strong>Which layer is it in?</strong> Later layer wins. Unlayered beats every layer.</td></tr>
        <tr><th scope="row" data-label="Asked">3rd</th><td data-label="Question">Specificity — how detailed is the selector?</td></tr>
        <tr><th scope="row" data-label="Asked">4th</th><td data-label="Question">Source order — which came last in the file?</td></tr>
      </tbody>
    </table>
  </div>
  <p>
    Specificity is question three. If question two already separated them, three is never
    asked. That is the whole mechanism: a single class in <code>app.pages</code> beats
    <code>.card .card-header .card-title</code> in <code>deck.components</code>, not
    because it is more specific — it is dramatically less — but because the argument was
    settled a step earlier.
  </p>
  <p class="dx-note">
    The counter-intuitive part, and the one that trips people who already know layers:
    <strong>unlayered CSS beats every layer</strong>. A plain <code>&lt;style&gt;</code>
    block with no <code>@layer</code> around it wins against all fifteen. That is by
    design, so a page template can always have the last word, but it means putting your CSS
    in a layer makes it <em>weaker</em> against your own unlayered CSS, not stronger. Pick
    one convention and hold it.
  </p>
</section>

<section class="stack-4">
  <h2 id="slots">Which of the four slots to use</h2>
  <p>
    All four beat all of Deck. They exist to order your own CSS against itself, which is
    the argument you will actually be having in six months.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The declared layer order, Deck's and yours</caption>
      <thead>
        <tr><th scope="col">Layer</th><th scope="col">For</th><th scope="col">Yours?</th></tr>
      </thead>
      <tbody>
        <?php foreach ($LAYERS as [$name, $what, $mine]): ?>
          <tr>
            <th scope="row" data-label="Layer"><code><?= e($name) ?></code></th>
            <td data-label="For"><?= e($what) ?></td>
            <td data-label="Yours?">
              <?php if ($mine): ?>
                <span class="badge badge-good">yours</span>
              <?php else: ?>
                <span class="dx-dim">Deck</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <dl class="stack-3">
    <dt><strong><code>app.base</code></strong></dt>
    <dd>
      Element styles and token overrides. A different body font, a custom
      <code>--brand</code>, your own <code>&lt;table&gt;</code> defaults. Things that
      should lose to your components.
    </dd>
    <dt><strong><code>app.components</code></strong></dt>
    <dd>
      Your own components, and your permanent changes to Deck's. If every card in your
      product has square corners, that rule lives here, once. This is where most of your
      CSS belongs.
    </dd>
    <dt><strong><code>app.pages</code></strong></dt>
    <dd>
      One page or one view. The hero on the marketing home page, a dashboard that needs a
      wider rail. Rules that would be wrong applied everywhere.
    </dd>
    <dt><strong><code>app.overrides</code></strong></dt>
    <dd>
      The escape hatch. Something has to win right now and you will come back to it. Keeping
      it as a named layer rather than an <code>!important</code> means it is greppable —
      you can find every one of them in a second, which is not true of importance scattered
      through a codebase.
    </dd>
  </dl>
</section>

<section class="stack-4">
  <h2 id="debug">When your rule still is not winning</h2>
  <p>
    Work down this list. It is in order of how often each one turns out to be the answer.
  </p>
  <ol class="stack-3">
    <li>
      <strong>Your stylesheet loads before Deck's.</strong> The <em>order</em> is declared
      by Deck, but a layer only exists once something declares it. If your CSS is the first
      to mention <code>app.pages</code>, it creates the layer at that point — before
      <code>deck.*</code> have been declared — and yours ends up first, which means it
      loses. Load Deck first, always. This is the single most common cause.
    </li>
    <li>
      <strong>You are competing with an unlayered rule.</strong> An inline
      <code>style</code> attribute, or a <code>&lt;style&gt;</code> block with no
      <code>@layer</code>. Both beat every layer. Dev tools show unlayered rules with no
      layer badge.
    </li>
    <li>
      <strong>The other rule is <code>!important</code>.</strong> Importance is question
      one, and it inverts the layer order — among <code>!important</code> declarations, the
      <em>earlier</em> layer wins, so importance in <code>deck.reset</code> beats importance
      in <code>app.overrides</code>. Deck's source contains 101 of them, and they are not
      scattered: 84 are inside <code>@media print</code>, 7 inside
      <code>prefers-reduced-motion</code>, and the remaining 10 are state hooks that have to
      beat inline styles written by a drag library. If one of those is beating you, you are
      printing, the reader has asked for less motion, or you are restyling a dragging
      element — and in all three cases the rule is meant to win.
    </li>
    <li>
      <strong>You are setting a property the element does not have.</strong>
      <code>gap</code> on a non-flex parent, <code>inset</code> on a static element,
      <code>width</code> on an inline one. Nothing to do with the cascade; the rule is
      winning and doing nothing.
    </li>
    <li>
      <strong>It is a custom property, and you set it too low.</strong>
      <code>--brand-600</code> resolves on <code>:root</code>, so overriding
      <code>--hue-brand</code> on a card changes nothing. See
      <a href="theming.php#scope">the theming guide</a>.
    </li>
  </ol>
  <p>
    In Chrome and Firefox dev tools, the Styles pane groups rules by layer and names the
    layer above each block. If your rule is there and struck through, the layer above it is
    the one beating you, and that is the whole diagnosis.
  </p>
</section>

<section class="stack-4">
  <h2 id="import">Loading your CSS into a layer</h2>
  <p>
    If you would rather not wrap every file, assign it at import time:
  </p>
  <pre class="dx-code"><code>@import url("components.css") layer(app.components);
@import url("dashboard.css") layer(app.pages);</code></pre>
  <p>
    Or declare the layer once at the top of your entry stylesheet and let the files fall
    into it. With a bundler, the same thing done in the right order:
  </p>
  <pre class="dx-code"><code>/* app.css — loaded after deck.css */
@layer app.base {
  :root { --hue-brand: 265; }
  body { font-family: "Inter var", var(--font-sans); }
}

@layer app.components {
  .card { border-radius: 0; }
  .btn  { text-transform: uppercase; letter-spacing: .04em; }
}</code></pre>
  <p class="dx-note">
    Deck ships each layer as a separate file under <code>dist/layers/</code> as well as the
    combined stylesheet. If you are only using the layout primitives and none of the
    components, you can load fewer of them — but load them in the declared order, or you
    are back to the problem in point one.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to reach for a layer</h2>
  <ul class="stack-3">
    <li>
      <strong>When a token would do it.</strong> Changing
      <code>--r-md</code> restyles every rounded corner in the framework at once; a rule
      overriding <code>.card { border-radius }</code> fixes cards and leaves the popover,
      the modal and the input alone. Reach for <a href="theming.php">the token</a> first.
    </li>
    <li>
      <strong>When a variant already exists.</strong> Before writing
      <code>.card { box-shadow: none }</code>, check whether that is
      <code>.card-flush</code>. Around a third of the overrides people write are a class
      Deck already ships.
    </li>
    <li>
      <strong>When it belongs on the element.</strong> A one-off width is a
      <code>style</code> attribute, and that is fine — a single inline declaration is
      easier to find and delete than a rule in a file three directories away.
    </li>
    <li>
      <strong>When you are fighting a component's structure.</strong> If the override list
      is longer than about four declarations, you do not want that component with changes;
      you want your own, and Deck's tokens make writing one cheap.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
