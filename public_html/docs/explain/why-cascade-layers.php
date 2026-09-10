<?php
declare(strict_types=1);

/**
 * Why Deck uses cascade layers — an essay.
 *
 * Every behaviour described here was measured in Chrome on a page loading the
 * published deck.min.css, not reasoned from the specification: the override
 * that wins or loses depending on load order, unlayered CSS beating a far more
 * specific layered selector, !important inverting layer order on Deck's real
 * reduced-motion rule, a one-class utility beating a two-class component
 * selector, and :where() losing to a bare element selector. The !important
 * counts come from a parser that tracks the at-rules enclosing each
 * declaration in src/*.css. Browser versions are caniuse; the Baseline date
 * is MDN.
 *
 * Paragraphs stand alone. Each names its subject instead of leaning on the
 * paragraph before it.
 */

$page = [
    'path' => 'explain/why-cascade-layers.php',
    'title' => 'Why Deck uses cascade layers',
    'level' => 'Intermediate',
    'description' => "Cascade layers let application CSS override Deck without !important, but only once two rules about load order are understood. The cost, measured, and the case for it.",
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Explanation</li>
    <li aria-current="page">Why cascade layers</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Why Deck uses cascade layers</h1>
  <p class="lede">
    Deck places every one of its rules inside a cascade layer, so overriding Deck with
    confidence requires understanding how cascade layers are ordered. Two rules catch
    people. A layer's position is fixed where its name is first declared, so CSS that loads
    before Deck loses to Deck. And CSS written outside any layer outranks every layer,
    however specific the layered selector is. Deck accepts that learning cost because, once
    the two rules are known, every override of Deck is a one-class selector with no
    <code>!important</code>.
  </p>
</header>

<section class="stack-4">
  <h2 id="cost">What the model costs</h2>
  <p>
    Load order decides whether an override of Deck works. Deck's stylesheet begins by
    declaring fifteen cascade layers in a fixed order: eleven for Deck, from
    <code>deck.reset</code> to <code>deck.print</code>, followed by four for application
    code, named <code>app.base</code>, <code>app.components</code>,
    <code>app.pages</code> and <code>app.overrides</code>. On a page that loads Deck first
    and then writes <code>.btn { border-radius: 0 }</code> inside
    <code>@layer app.pages</code>, the button's corners measure 0px in Chrome. When the
    identical override sits in a style block before Deck's stylesheet, the corners measure
    7px, which is Deck's value, because that first mention of <code>app.pages</code>
    created the layer ahead of every Deck layer.
  </p>
  <pre class="dx-code"><code>&lt;!-- Deck wins: app.pages is created before Deck's layers exist --&gt;
&lt;style&gt;@layer app.pages { .btn { border-radius: 0; } }&lt;/style&gt;
&lt;link rel="stylesheet" href="/assets/deck/deck.min.css"&gt;

&lt;!-- The override wins: Deck has already placed app.pages last --&gt;
&lt;link rel="stylesheet" href="/assets/deck/deck.min.css"&gt;
&lt;style&gt;@layer app.pages { .btn { border-radius: 0; } }&lt;/style&gt;</code></pre>
  <p>
    CSS written outside any cascade layer outranks all layered CSS, regardless of
    specificity. On a page with Deck loaded, an unlayered rule as weak as
    <code>button { border-radius: 3px }</code> beats a rule inside
    <code>@layer app.overrides</code> whose selector is
    <code>main &gt; button.btn.btn-primary#probe</code>, which contains an ID and three
    classes, and the button measures 3px in Chrome. A developer who expects specificity to
    settle that conflict will be wrong, and the only explanation the browser offers is the
    layer label that developer tools print beside each rule.
  </p>
  <p>
    Importance reverses cascade layer order: among declarations marked
    <code>!important</code>, the earlier layer wins. Deck's reset layer sets
    <code>transition-duration: .01ms !important</code> for readers whose operating system
    is set to reduce motion. With that preference on, an application rule of
    <code>transition-duration: 5s !important</code> inside
    <code>@layer app.overrides</code> loses, and Chrome reports a duration of 0.01ms; with
    the preference off, the application rule applies and the duration is 5s. The inversion
    is correct behaviour and it protects the reader's accessibility setting, but it defeats
    anyone who reaches for <code>!important</code> to force an override of Deck.
  </p>
  <p>
    The need to explain overriding shows in Deck's own documentation. Of the 87 component,
    reference, tutorial and guide pages on Deck's documentation site, 76 contain an example
    written inside an <code>@layer app</code> block, because nearly every page about a
    component has to show how to change that component safely.
  </p>
</section>

<section class="stack-4">
  <h2 id="replace">What layers replace</h2>
  <p>
    Before cascade layers, a CSS framework and the application using it competed on
    specificity. An application overriding a framework selector such as
    <code>.card .card-header .card-title</code> needed a selector at least as specific, then
    <code>!important</code> when that was not enough, and a framework that also used
    <code>!important</code> left nothing further to escalate to. Which rule won depended on
    how selectors happened to be written rather than on which code was meant to have the
    final say.
  </p>
  <p>
    Deck's source uses <code>!important</code> 101 times, and the uses are concentrated
    rather than scattered. 84 are inside <code>@media print</code>, where paper styling must
    replace screen styling. 7 are inside <code>prefers-reduced-motion</code>, where a
    reader's accessibility setting must replace animation. Of the remaining 10, six reset
    positioning, shadows and clipping on table cells when Deck's data grid restacks into
    cards on a narrow screen, two implement the <code>.no-motion</code> opt-out, and two
    style an item being dragged and the gap it leaves behind.
  </p>
  <p>
    Another way for a framework to yield to application CSS is to wrap its selectors in
    <code>:where()</code>, which reduces their specificity to zero so that any application
    selector wins. Zero specificity also loses to bare element selectors: in Chrome, a rule
    of <code>:where(.note) { color: blue }</code> is overridden by
    <code>span { color: red }</code> on the same element, and the text renders red. Under
    that approach, an application's ordinary element defaults, such as a style for every
    button or every link, silently override a framework's component styling.
  </p>
  <p>
    Cascade layers let Deck keep normal specificity inside itself while yielding to
    application code as a single block, and they also rank Deck's own parts against each
    other. In Chrome, an input carrying Deck's <code>.border</code> utility inside an
    <code>.input-group</code> shows a 1px solid border, even though the rule
    <code>.input-group &gt; .input</code> removes borders with a selector of two classes
    against the utility's one. Deck's utilities layer comes after its components layer, so
    the one-class utility wins, which is the result a developer adding
    <code>.border</code> intends.
  </p>
</section>

<section class="stack-4">
  <h2 id="buys">What the order buys</h2>
  <p>
    Deck declares the four application layers inside its own stylesheet, after all eleven
    of its own, so an application never has to create those layers or put them in order.
    Once Deck's stylesheet has loaded, a one-class selector inside an application layer
    beats every Deck rule without <code>!important</code>.
  </p>
  <pre class="dx-code"><code>@layer app.components {
  .card { border-radius: 0; }
}</code></pre>
  <p>
    Deck's four application layers also order an application's CSS against itself. Rules in
    <code>app.base</code>, such as element defaults and token overrides, lose to
    <code>app.components</code>; rules in <code>app.components</code> lose to
    <code>app.pages</code>; and <code>app.overrides</code> beats all three. An override that
    must win immediately can go in <code>app.overrides</code> and still be found later by
    searching for that layer name, which cannot be said of an <code>!important</code>
    scattered through a codebase.
  </p>
  <p>
    Cascade layers add no browser-support cost to Deck. They have been Baseline widely
    available since March 2022, having first shipped in Chrome and Edge 99, Firefox 97 and
    Safari 15.4 according to caniuse. The <code>light-dark()</code> function that Deck's
    colour system depends on arrived later in every one of those browsers, so cascade
    layers never determine Deck's minimum browser versions.
  </p>
</section>

<section class="stack-4">
  <h2 id="not-for">Who this model does not suit</h2>
  <p>
    Deck's cascade layer model suits applications that control the order in which their
    stylesheets load. A site where a plugin, a tag manager or an embedded widget can insert
    a stylesheet into the page head before Deck's link tag cannot guarantee that Deck loads
    first, and any layer such a stylesheet names early will be positioned ahead of Deck's
    layers.
  </p>
  <p>
    A codebase with a large amount of unlayered legacy CSS will find that the legacy rules
    beat Deck wherever the two overlap, because CSS outside a cascade layer outranks every
    layer. Adopting Deck in that codebase means moving the legacy CSS into an application
    layer first, or accepting that the old rules will keep winning.
  </p>
  <p>
    A team that settles styling conflicts with <code>!important</code> will find the habit
    works against Deck. Because importance inverts layer order, an
    <code>!important</code> declaration in <code>app.overrides</code> loses to an
    <code>!important</code> declaration in any Deck layer, including the reduced-motion
    rules in Deck's reset layer.
  </p>
</section>

<section class="stack-4">
  <h2 id="case">The case in one paragraph</h2>
  <p>
    Deck's argument for cascade layers is that two rules about load order are easier to
    learn than an unbounded contest of selector specificity. Load Deck's stylesheet before
    any application CSS and keep that CSS inside the four application layers, and every
    override of Deck becomes a one-class selector without <code>!important</code>. The rules
    have to be learned once, and Deck's documentation repeats them on 76 pages because
    nearly every component page has to show how that component is overridden.
  </p>
  <p class="text-sm text-muted">
    Related: <a href="why-no-build-step.php">why Deck has no build step</a>,
    <a href="why-one-hue.php">why Deck themes from one hue</a>, and the task-oriented
    <a href="../guides/layers.php">guide to overriding Deck without !important</a>.
  </p>
</section>

<?php docs_footer(); ?>
