<?php
declare(strict_types=1);

/**
 * Why Deck themes from one hue — an essay.
 *
 * The numbers here were measured rather than estimated. The 18 brand tokens
 * and 198 declarations come from a transitive walk of the token values in
 * dist/api.json and of src/*.css. The two-colour recipe was generated from
 * those tokens and checked in Chrome, where every probed colour changed. The
 * hex matching uses the published OKLCH-to-sRGB conversion against Deck's
 * lightness steps. The contrast bands were calculated with the WCAG 2
 * relative-luminance formula at Deck's shipped hues. Browser versions are
 * caniuse; Baseline dates and the contrast-color() description are MDN.
 *
 * Paragraphs stand alone. Each names its subject instead of leaning on the
 * paragraph before it.
 */

$page = [
    'path' => 'explain/why-one-hue.php',
    'title' => 'Why Deck themes from one hue',
    'level' => 'Intermediate',
    'description' => "Deck generates its brand palette from one hue angle, so a two-colour brand or an exact brand hex takes extra work. The measured cost, and why runtime theming repays it.",
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Explanation</li>
    <li aria-current="page">Why one hue</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Why Deck themes from one hue</h1>
  <p class="lede">
    Deck builds its whole brand palette from one number, a hue angle between 0 and 360, so it
    cannot express a brand with two primary colours, or reproduce an exact brand hex code,
    without extra work. Deck accepts that limitation because a palette computed from one
    number can be stored per customer, rendered into the first byte of HTML, and paired with
    a matching dark mode without anyone choosing a second set of colours.
  </p>
</header>

<section class="stack-4">
  <h2 id="cost">What one hue cannot do</h2>
  <p>
    A brand with two equally important colours has nowhere in Deck to put the second one.
    Deck defines a second hue token, <code>--hue-accent</code>, but only five rules in its
    source read it: the highlight behind the <code>&lt;mark&gt;</code> element, the
    <code>.btn-accent</code> button and its hover state, and two rules that colour rating
    stars. Links, focus rings, soft tinted fills and every other brand-coloured surface in
    Deck read from <code>--hue-brand</code> alone.
  </p>
  <p>
    Giving one region of a page a second brand colour takes 19 declarations in Deck.
    Setting <code>--hue-brand</code> on a container is not enough, because Deck's 18 brand
    tokens are declared on the root element and resolve there, so everything inside the
    container inherits colours already computed from the root's hue. Measured in Chrome, a
    container given only <code>--hue-brand: 24</code> still renders its primary button in
    the page's original colour, while a container that redeclares all 18 tokens alongside
    the hue renders its buttons, links, badges and focus colour in the new one.
  </p>
  <pre class="dx-code"><code>.brand-secondary {
  --hue-brand: 24;
  --brand-50: oklch(97.4% calc(var(--chroma-brand) * .16) var(--hue-brand));
  --brand-100: oklch(94.2% calc(var(--chroma-brand) * .30) var(--hue-brand));
  --brand-200: oklch(88.6% calc(var(--chroma-brand) * .48) var(--hue-brand));
  --brand-300: oklch(80.4% calc(var(--chroma-brand) * .70) var(--hue-brand));
  --brand-400: oklch(70.2% calc(var(--chroma-brand) * .90) var(--hue-brand));
  --brand-500: oklch(60.8% var(--chroma-brand) var(--hue-brand));
  --brand-600: oklch(51.6% var(--chroma-brand) var(--hue-brand));
  --brand-700: oklch(43.4% calc(var(--chroma-brand) * .92) var(--hue-brand));
  --brand-800: oklch(35.6% calc(var(--chroma-brand) * .78) var(--hue-brand));
  --brand-900: oklch(28.2% calc(var(--chroma-brand) * .62) var(--hue-brand));
  --brand-950: oklch(20.4% calc(var(--chroma-brand) * .46) var(--hue-brand));
  --text-on-brand: oklch(99% .01 var(--hue-brand));
  --brand: light-dark(var(--brand-600), var(--brand-400));
  --brand-hover: light-dark(var(--brand-700), var(--brand-300));
  --brand-soft: light-dark(var(--brand-50), oklch(26% .05 var(--hue-brand)));
  --brand-soft-text: light-dark(var(--brand-800), var(--brand-200));
  --focus: light-dark(var(--brand-500), var(--brand-300));
  --ring: 0 0 0 3px light-dark(oklch(from var(--focus) l c h / .32), oklch(from var(--focus) l c h / .42));
}</code></pre>
  <p>
    An exact brand colour is usually not one of the eleven shades Deck generates. Each Deck
    shade has a fixed perceptual lightness, from 97.4% for the lightest to 20.4% for the
    darkest, so a brand colour lands on a shade only when its own lightness matches one. The
    orange <code>#ff5a1f</code> has a lightness of 68.2%, between Deck's 70.2% and 60.8%
    shades; at Deck's default saturation the nearest generated shade is
    <code>#d8876e</code>, and with the saturation raised to match the orange it is
    <code>#fd6b3b</code>. The navy <code>#1b2a6b</code> has a lightness of 31.6%, between
    the 35.6% and 28.2% shades, and no saturation setting brings its nearest shade closer
    than <code>#29386c</code>.
  </p>
  <p>
    Deck's supported answer for an exact brand colour is to override its role tokens rather
    than its hue. Setting <code>--brand</code> and <code>--brand-hover</code> to the brand's
    own values inside an application layer puts the exact colour on every button and link
    that reads those roles, while tints, borders and the rest of the palette continue to
    follow <code>--hue-brand</code>. The application then owns the text colour on that fill,
    <code>--text-on-brand</code>, because Deck computed that value for a colour the
    application has replaced, and it gives up the lighter dark-mode shade Deck would
    otherwise have paired with the brand colour.
  </p>
  <pre class="dx-code"><code>@layer app.base {
  :root {
    --brand: #ff5a1f;
    --brand-hover: #e14a12;
    --text-on-brand: #1a0a04;
  }
}</code></pre>
</section>

<section class="stack-4">
  <h2 id="why">Why one number is enough</h2>
  <p>
    One number can drive a whole palette because Deck generates the palette in OKLCH, a
    colour space in which the lightness value tracks how light a colour looks. In HSL, pure
    yellow and pure blue share a lightness of 50% yet have WCAG relative luminances of 0.93
    and 0.07, so a recipe written in HSL produces shades of very different visual weight at
    different hues. In OKLCH, one recipe of lightness and saturation per shade yields a
    comparable ramp at any hue. The <code>oklch()</code> function has been Baseline widely
    available since May 2023, having first shipped in Chrome and Edge 111, Firefox 113 and
    Safari 15.4.
  </p>
  <p>
    A single custom property controls a large share of Deck's appearance. Changing
    <code>--hue-brand</code> recalculates 18 brand tokens, and 198 declarations spread
    across 20 of Deck's 26 stylesheets read those tokens. Of Deck's 60 colour tokens, 59 are
    computed from a hue or from another computed token; the only fixed colour token is pure
    white.
  </p>
  <p>
    Deck's source contains almost no literal colours that could drift away from its palette.
    Outside its print rules Deck has 25 hex values, and none of them sets a visible interface
    colour: 22 are <code>#000</code> inside CSS mask gradients, where only opacity is read,
    and three are the <code>#fff</code> and <code>#000</code> defaults that keep a QR code
    scannable.
  </p>
</section>

<section class="stack-4">
  <h2 id="buys">What one number buys</h2>
  <p>
    Theming from one number makes per-customer branding a value in a database instead of a
    separate build. Deck's PHP helper, <code>Deck::theme()</code>, turns a tenant's stored
    hue into one inline style on the <code>&lt;html&gt;</code> element, so the correct
    colours are present in the first byte of HTML and no script has to repaint the page
    after it loads. Every tenant shares the same stylesheet file, so adding a customer adds
    no CSS file, no build and no extra cache entry.
  </p>
  <pre class="dx-code"><code>&lt;html &lt;?= Deck::theme(hue: $tenant-&gt;hue) ?&gt;&gt;
&lt;!-- renders as: &lt;html style="--hue-brand:265"&gt; --&gt;</code></pre>
  <p>
    Deck's dark mode follows the brand hue without anyone choosing a second palette. Deck
    declares its <code>--brand</code> role as
    <code>light-dark(var(--brand-600), var(--brand-400))</code>, which uses the
    51.6%-lightness shade on light pages and the 70.2%-lightness shade on dark pages, and
    both shades move whenever the hue changes. A tenant who picks purple gets a dark-mode
    purple produced by the same recipe as the light one.
  </p>
  <p>
    Computing colours from a number creates one hard problem: choosing a readable colour for
    text that sits on a brand-coloured fill. Where the browser supports it, Deck uses
    <code>contrast-color()</code>, which MDN describes as returning only white or black and
    as commonly meeting the WCAG AA contrast ratio of 4.5:1; MDN lists
    <code>contrast-color()</code> as Baseline newly available since April 2026. Where the
    browser lacks it, Deck falls back to two fixed text colours: a near-white at 99%
    lightness and a near-black at 24% lightness.
  </p>
  <p>
    Deck's two fixed text colours reach 4.5:1 only on fills that avoid a middle band of
    lightness, and Deck places its text-bearing fills outside that band. Calculated with the
    WCAG 2 formula at Deck's brand hue, neither fallback reaches 4.5:1 on a fill between
    about 54% and 60% lightness, and at Deck's warning and error hues the band runs from
    about 58% to 62%. The five fills in Deck that carry text sit at 51.6%, 52%, 52%, 74% and
    76% lightness. Pure black or pure white would reach at least about 4.58:1 on any fill,
    but MDN warns that meeting that ratio on a mid-tone background does not guarantee
    readable text.
  </p>
</section>

<section class="stack-4">
  <h2 id="not-for">Who this model does not suit</h2>
  <p>
    Deck's one-hue model does not suit a brand defined by two co-equal primary colours that
    both need to appear across buttons, links and focus states. Deck can express that brand
    only by redeclaring its 18 brand tokens in every region that uses the second colour, and
    an interface that alternates between the two colours throughout will repeat that work in
    every such region.
  </p>
  <p>
    Deck's one-hue model also does not suit a design system specified as an exact palette of
    hex values from a brand book, where every shade must match a published swatch. Deck's
    shades are computed from fixed lightness steps, so matching a prescribed palette means
    overriding Deck's generated shades one by one, at which point the generation that makes
    Deck's theming cheap is no longer doing any work.
  </p>
  <p>
    A product with one fixed brand, a single site, no tenants and no runtime theme switching
    gains the least from Deck's approach. That product could choose its eleven shades once,
    by hand or with a palette tool, and Deck's ability to recompute the palette from a
    database value would go unused.
  </p>
</section>

<section class="stack-4">
  <h2 id="case">The case in one paragraph</h2>
  <p>
    Deck's argument for theming from one hue is that, for multi-tenant and white-labelled
    applications, a readable palette in each customer's own colour matters more than a
    second brand colour or an exact swatch match. One number produces eleven shades, a
    dark-mode pairing and 198 declarations of brand styling, can be stored per customer and
    rendered into the first byte of HTML, and needs no build per tenant. The price is 19
    declarations for each region that needs a second brand colour, and three role overrides
    for an exact brand hex.
  </p>
  <p class="text-sm text-muted">
    Related: <a href="why-no-build-step.php">why Deck has no build step</a>,
    <a href="why-cascade-layers.php">why Deck uses cascade layers</a>, and the task-oriented
    <a href="../guides/theming.php">guide to changing your brand colour</a>.
  </p>
</section>

<?php docs_footer(); ?>
