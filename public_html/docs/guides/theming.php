<?php
declare(strict_types=1);

/**
 * Theming — the differentiator, so it gets the depth.
 *
 * The claims on this page were tested in a browser rather than reasoned about,
 * because they are the kind of thing that is obviously true and turns out not
 * to be: that Deck.hue() actually repaints (it does), that setting --hue-brand
 * on a subtree rethemes it (on its own it does not; with data-theme-scope it
 * does), and how fast the slider is (it was "sixty times a second", which a
 * slow phone does not manage). FINDINGS 114 and 117 have the measurements.
 */

$page = [
    'path' => 'guides/theming.php',
    'title' => 'Change your brand colour',
    'level' => 'Beginner',
    'description' => "Retheme an entire Deck app from one number. How the OKLCH ramps are generated, how to do it per tenant from PHP, at runtime from JavaScript, for one region of a page, and what not to override.",
];

require __DIR__ . '/../_layout.php';

$SWATCHES = ['--brand-50', '--brand-100', '--brand-200', '--brand-300', '--brand-400',
             '--brand-500', '--brand-600', '--brand-700', '--brand-800', '--brand-900', '--brand-950'];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Guides</li>
    <li aria-current="page">Change your brand colour</li>
  </ol>
</nav>

<header class="stack stack-3">
  <h1>Change your brand colour</h1>
  <p class="lede">
    You want the buttons, links, focus rings, charts and shadows to be your colour instead
    of Deck's. That is one number, set in one place, with no rebuild, no config file and
    no second stylesheet. This page is that number, and then everything that follows from
    it being a number rather than a hex code.
  </p>
</header>

<section class="stack stack-4">
  <h2 id="answer">The short answer</h2>
  <p>
    Put a hue angle on the <code>&lt;html&gt;</code> tag. That is the whole thing.
  </p>
  <pre class="dx-code"><code>&lt;html style="--hue-brand: 42"&gt;   &lt;!-- rust --&gt;
&lt;html style="--hue-brand: 265"&gt;  &lt;!-- violet --&gt;
&lt;html style="--hue-brand: 152"&gt;  &lt;!-- green --&gt;</code></pre>
  <p>
    A hue is an angle around the colour wheel, from 0 to 360. 0 is pink-red, 30 is orange,
    90 is olive, 150 is green, 200 is cyan, 265 is violet, 330 is magenta. Deck ships at
    196, which is a harbour teal.
  </p>
  <?php docs_example(
      '<div class="cluster">' . "\n" .
      '  <button class="btn btn-primary">Primary</button>' . "\n" .
      '  <button class="btn btn-soft">Soft</button>' . "\n" .
      '  <button class="btn btn-outline">Outline</button>' . "\n" .
      '  <span class="badge badge-brand">Badge</span>' . "\n" .
      '  <a href="#answer">A link</a>' . "\n" .
      '</div>',
      'Deck at its shipped hue, 196'
  ); ?>
  <p>
    Press the button below to move this whole page. It calls
    <code>Deck.hue()</code>, which sets the same custom property at runtime — every
    surface, border, focus ring, shadow tint and chart series on the page follows,
    including the documentation chrome around it.
  </p>
  <?php docs_example(
      '<div class="cluster">' . "\n" .
      '  <button class="btn btn-sm" onclick="Deck.hue(42)">Rust, 42</button>' . "\n" .
      '  <button class="btn btn-sm" onclick="Deck.hue(152)">Green, 152</button>' . "\n" .
      '  <button class="btn btn-sm" onclick="Deck.hue(265)">Violet, 265</button>' . "\n" .
      '  <button class="btn btn-sm" onclick="Deck.hue(330)">Magenta, 330</button>' . "\n" .
      '  <button class="btn btn-sm btn-ghost" onclick="Deck.hue(196)">Back to 196</button>' . "\n" .
      '</div>',
      'Live. This changes the page you are reading'
  ); ?>
</section>

<section class="stack stack-4">
  <h2 id="why-number">Why a number and not a hex code</h2>
  <p>
    Most frameworks ask for <code>#0f766e</code> and then ask for the ten shades around it
    as well, because a brand colour on its own is not a palette. You need a light tint for
    a selected row, a dark step for text on white, a mid step for a border. Picking eleven
    hex codes that look like a family is a design job, and doing it again for every tenant
    is a design job you do not want.
  </p>
  <p>
    Deck asks for the hue and generates the rest in OKLCH, where the first number is
    <em>perceptual</em> lightness. That matters more than it sounds. In HSL, yellow at 50%
    lightness and blue at 50% lightness look nothing like the same lightness — yellow is
    glaring and blue is nearly black. In OKLCH they match, because the space is built
    around how eyes actually work. So the same ramp recipe produces a usable palette at
    every hue, which is what makes one number enough.
  </p>
  <pre class="dx-code"><code>--brand-500: oklch(60.8% var(--chroma-brand) var(--hue-brand));
--brand-600: oklch(51.6% var(--chroma-brand) var(--hue-brand));
--brand-700: oklch(43.4% calc(var(--chroma-brand) * .92) var(--hue-brand));</code></pre>
  <p>
    The lightness is fixed per step, the hue comes from your number, and the chroma tapers
    at the ends — because a very light or very dark colour at full chroma looks artificial,
    so the tints and shades pull their saturation back.
  </p>
  <p>Here is the ramp as it stands on this page right now:</p>
  <?php docs_token_table($SWATCHES); ?>
</section>

<section class="stack stack-4">
  <h2 id="chroma">Turning the saturation down</h2>
  <p>
    The second knob is <code>--chroma-brand</code>: how colourful, independent of which
    colour. Deck ships at <code>.118</code>, which is a confident but not loud brand.
    Lower it for something institutional, raise it for something consumer.
  </p>
  <pre class="dx-code"><code>&lt;html style="--hue-brand: 265; --chroma-brand: .04"&gt;   &lt;!-- nearly grey --&gt;
&lt;html style="--hue-brand: 265; --chroma-brand: .16"&gt;   &lt;!-- vivid --&gt;</code></pre>
  <p>
    Above about <code>.2</code> you leave the sRGB gamut at some hues, and the browser
    clamps — so a wide-gamut screen shows something a normal screen cannot, and the two
    stop matching. If a colour looks flat on one monitor and electric on another, chroma
    is why.
  </p>
  <?php docs_example(
      '<div class="cluster">' . "\n" .
      '  <button class="btn btn-sm" onclick="document.documentElement.style.setProperty(\'--chroma-brand\', \'.04\')">Muted</button>' . "\n" .
      '  <button class="btn btn-sm" onclick="document.documentElement.style.setProperty(\'--chroma-brand\', \'.118\')">Default</button>' . "\n" .
      '  <button class="btn btn-sm" onclick="document.documentElement.style.setProperty(\'--chroma-brand\', \'.17\')">Vivid</button>' . "\n" .
      '</div>',
      'Same hue, three chromas'
  ); ?>
</section>

<section class="stack stack-4">
  <h2 id="tenant">One theme per tenant, from PHP</h2>
  <p>
    This is the case Deck was built for. The tenant's colour is a column in your database,
    and it has to be right in the first paint — not applied by a script after the page has
    already flashed the wrong brand.
  </p>
  <pre class="dx-code"><code>&lt;?php use EchoDial\Deck\Deck; ?&gt;
&lt;html &lt;?= Deck::htmlAttributes(lang: 'en') ?&gt; &lt;?= Deck::theme(
    hue: $tenant-&gt;hue,          // 265
    chroma: $tenant-&gt;chroma,    // .12, or null for the default
    mode: $user-&gt;theme,         // 'light', 'dark', or null to follow the OS
) ?&gt;&gt;

&lt;!-- renders as --&gt;
&lt;html lang="en" style="--hue-brand:265;--chroma-brand:.12" data-theme="dark"&gt;</code></pre>
  <p>
    Every argument is optional and a null one is left out entirely, so a tenant with no
    brand colour and a user with no preference produce an empty attribute and the defaults
    apply. There is no per-tenant CSS file, no build per customer, and no cache to
    invalidate: the stylesheet is byte-identical for everybody and the theme is two
    declarations in the HTML.
  </p>
  <p>
    The full signature is on the <a href="../reference/php.php#theme">PHP helper
    page</a>. If you are not using PHP, the point stands — emit
    <code>style="--hue-brand:265"</code> on the <code>&lt;html&gt;</code> tag from whatever
    renders it.
  </p>
  <p>
    Two tenants on one page, such as an admin list of customers or a preview of a
    customer's brand, is the same call on the region's element with
    <code>scope: true</code>. That adds the <code>data-theme-scope</code> attribute,
    which is <a href="#scope">what makes a region retheme</a>.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="runtime">Letting a person choose, at runtime</h2>
  <p>
    <code>Deck.hue(n)</code> sets the same property from JavaScript. It is not persisted
    and not validated — it is a live preview, and saving the choice is your job.
  </p>
  <pre class="dx-code"><code>&lt;label class="label" for="hue"&gt;Brand hue&lt;/label&gt;
&lt;input class="range" id="hue" type="range" min="0" max="360" value="196"
       oninput="Deck.hue(this.value)"&gt;</code></pre>
  <?php docs_example(
      '<div class="field" style="max-inline-size:26rem">' . "\n" .
      '  <label class="label" for="g-hue">Brand hue</label>' . "\n" .
      '  <input class="range" id="g-hue" type="range" min="0" max="360" value="196" oninput="Deck.hue(this.value)">' . "\n" .
      '</div>',
      'Drag it. The whole page follows',
      'stack'
  ); ?>
  <p>
    Each call restyles every element on the page, because every element inherits the hue.
    Measured in Edge on a page of 2,000 elements, that was about 12ms of style
    recalculation per call on a desktop, which fits in a 60fps frame with little to spare,
    and about 97ms with the processor slowed four times to stand in for a low-end phone,
    which is closer to ten updates a second. On a big page that has to feel smooth on slow
    devices, preview the hue on one <a href="#scope">scoped region</a>, which restyles only
    that region, and call <code>Deck.hue()</code> on <code>change</code> rather than
    <code>input</code>, so the whole page restyles once, on release.
  </p>
  <p>
    To make it stick, save the number and render it server-side next time. Doing it the
    other way round — reading <code>localStorage</code> in a script and calling
    <code>Deck.hue()</code> on load — means the page paints in the default colour first and
    then jumps, which is the flash of unthemed content and it is very visible on a slow
    connection.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="scope">One region, its own brand: <code>data-theme-scope</code></h2>
  <p>
    A hue on the root themes the page. To give one region its own brand, such as a second
    tenant's card on an admin page or the preview pane of a theme editor, set the hue on
    that element <em>and</em> mark the element with <code>data-theme-scope</code>:
  </p>
  <pre class="dx-code"><code>&lt;section class="card" data-theme-scope style="--hue-brand: 24"&gt;
  &lt;div class="card-body"&gt;&lt;button class="btn btn-primary"&gt;Rust&lt;/button&gt;&lt;/div&gt;
&lt;/section&gt;

&lt;!-- or from PHP --&gt;
&lt;section class="card" &lt;?= Deck::theme(hue: $other-&gt;hue, scope: true) ?&gt;&gt;
&lt;!-- &lt;section class="card" style="--hue-brand:24" data-theme-scope&gt; --&gt;</code></pre>
  <?php docs_example(
      '<div class="grid grid-fixed-2">' . "\n" .
      '  <div class="card" data-theme-scope style="--hue-brand: 24">' . "\n" .
      '    <div class="card-body stack">' . "\n" .
      '      <div class="cluster"><button class="btn btn-primary">Rust</button><button class="btn btn-soft">Soft</button><span class="badge badge-brand">24</span><a href="#scope">A link</a></div>' . "\n" .
      '      <div class="chart"><div class="chart-bars">' . "\n" .
      '        <div class="chart-bar s1"><span class="chart-bar-label">Series 1</span><span class="chart-bar-track"><span class="chart-bar-fill" style="--value:70"></span></span><span>70</span></div>' . "\n" .
      '        <div class="chart-bar s2"><span class="chart-bar-label">Series 2</span><span class="chart-bar-track"><span class="chart-bar-fill" style="--value:45"></span></span><span>45</span></div>' . "\n" .
      '      </div></div>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="card" data-theme-scope style="--hue-brand: 265">' . "\n" .
      '    <div class="card-body stack">' . "\n" .
      '      <div class="cluster"><button class="btn btn-primary">Violet</button><button class="btn btn-soft">Soft</button><span class="badge badge-brand">265</span><a href="#scope">A link</a></div>' . "\n" .
      '      <div class="chart"><div class="chart-bars">' . "\n" .
      '        <div class="chart-bar s1"><span class="chart-bar-label">Series 1</span><span class="chart-bar-track"><span class="chart-bar-fill" style="--value:70"></span></span><span>70</span></div>' . "\n" .
      '        <div class="chart-bar s2"><span class="chart-bar-label">Series 2</span><span class="chart-bar-track"><span class="chart-bar-fill" style="--value:45"></span></span><span>45</span></div>' . "\n" .
      '      </div></div>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Two brands on one page. The rest of this page keeps its own hue'
  ); ?>
  <div class="alert alert-warn">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#alert-triangle"></use></svg>
    <div>
      <div class="alert-title">Without <code>data-theme-scope</code>, a hue on an element recolours almost nothing</div>
      <p class="alert-body">
        No error and no warning: the buttons, links and badges inside keep the page's
        colour. This is the most likely way to lose an hour with Deck's theming, so it is
        worth understanding rather than memorising.
      </p>
    </div>
  </div>
  <p>
    The reason is how custom properties resolve. <code>--brand-600</code> is declared as
    <code>oklch(51.6% var(--chroma-brand) var(--hue-brand))</code>, and that
    <code>var()</code> is substituted where the property is <em>declared</em>, not where it
    is used. So <code>--brand-600</code> computes to a finished colour on the element that
    declares it, and everything below inherits that finished colour. Deck declares the 18
    tokens that read the hue on <code>:root</code> and again on
    <code>[data-theme-scope]</code>, so a marked element works the ramp out again from its
    own hue. On an unmarked element, <code>--hue-brand: 42</code> is inherited correctly
    and read only by the few rules that use the hue directly: <code>.g-mesh</code> and the
    series colours of a <code>.chart</code>. That is how
    <code>&lt;div class="g-mesh" style="--hue-brand: 34"&gt;</code> tints a hero's
    gradient without touching the buttons in it.
  </p>
  <p>
    Where the hue comes from does not matter, only the attribute: an inline style, a
    class in your stylesheet on an element that also carries the attribute, or
    <code>element.style.setProperty('--hue-brand', n)</code> at runtime.
    <code>--chroma-brand</code> works the same way, and scopes nest, each using its own
    hue.
  </p>
  <p>
    A <code>--brand</code> you set on <code>:root</code>, the exact-colour override
    <a href="#beyond">below</a>, stops at a scope. That is intended: the scope works
    <code>--brand</code> out again from its own hue, which is what it is for. To give a
    scoped region an exact colour, set <code>--brand</code> on the scope element itself.
  </p>
  <p>
    Light or dark for one region is <code>data-theme</code> on the element, which
    <code>Deck::theme(mode: 'dark')</code> emits. The region's text takes the region's
    ink, so a dark card on a light page reads as one. The two combine:
    <code>&lt;section data-theme-scope data-theme="dark" style="--hue-brand: 24"&gt;</code>.
  </p>
  <p>
    Measured in Edge on a page of 2,000 elements, the attribute costs nothing until an
    element carries it. Ten scoped cards cost the same as ten cards with an inline hue and
    no attribute, because most of the cost is having ten different hues on the page at
    all.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="dark">Dark mode is already done</h2>
  <p>
    You do not theme twice. Deck's semantic tokens are declared with
    <code>light-dark()</code>, so each one holds both values and the browser picks:
  </p>
  <pre class="dx-code"><code>--surface: light-dark(var(--ink-0),   var(--ink-950));
--text:    light-dark(var(--ink-900), var(--ink-100));
--brand:   light-dark(var(--brand-600), var(--brand-400));</code></pre>
  <p>
    Note the last one. In light mode the brand is the 600 step, and in dark it is the 400 —
    lighter, because the same colour that reads as solid on white reads as muddy on near
    black. Links and <code>.btn-primary</code> both follow it, so a primary button is the
    lighter step in dark mode, with dark text. Change the hue and both steps move together,
    so a purple tenant gets a correct dark mode without anyone choosing a second purple.
  </p>
  <p>
    That is why the rule is <strong>reference the roles, not the ramps</strong>. Write
    <code>var(--surface)</code> and dark mode is free; write <code>var(--ink-0)</code> and
    you have hard-coded white into a page that will one day be dark. There are 16 role
    tokens and they are listed on the <a href="../reference/tokens.php#roles">token
    reference</a>. Adding a switch a person can press is
    <a href="dark-mode.php">its own short guide</a>.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="contrast">Text on a coloured surface, and where contrast-color() fits</h2>
  <p>
    Generating a palette from one number has one genuinely hard problem in it: what colour
    is the text on top of the brand fill? White works on a dark blue and is unreadable on a
    bright yellow, and you do not know which you are getting until the tenant picks.
  </p>
  <p>
    Deck ships static answers for the five saturated surfaces — they are correct for the
    shipped hues and they are guesses about yours:
  </p>
  <pre class="dx-code"><code>--text-on-brand:  oklch(99% .01 var(--hue-brand));   /* near white */
--text-on-warn:   oklch(24% .06 var(--hue-warn));    /* near black */</code></pre>
  <p>
    Then it overwrites them where the browser can do better:
  </p>
  <pre class="dx-code"><code>@supports (color: contrast-color(red)) {
  :root {
    --text-on-brand: contrast-color(var(--brand-600));
    --text-on-warn:  contrast-color(var(--warn-500));
  }
}</code></pre>
  <p>
    <code>contrast-color()</code> asks the browser to pick black or white against the
    colour <em>as it actually computes</em>, so it follows your hue wherever you drag it.
    It aims at 4.5:1, which is the AA threshold for body text. Where it is missing the
    static values stand in, and the page is still readable — just not provably so at
    every hue.
  </p>
  <p>
    The ramps are built to keep that promise honest: every surface that carries text is
    kept out of a middle band of lightness, roughly 54–62% depending on the hue. In
    that band neither of Deck's static fallback text colours — the near-white and the
    near-black used where <code>contrast-color()</code> is missing — reaches 4.5:1, and
    even the better of pure black and pure white only just clears it. If you set a custom
    <code>--chroma-brand</code> above <code>.15</code> and use
    <code>.btn-primary</code> heavily, check one button with a contrast tool. It is a
    two-minute check and it is the only part of this that a number cannot guarantee.
  </p>
</section>

<section class="stack stack-4">
  <h2 id="beyond">When one hue is not enough</h2>
  <p>
    There are five more, and they work exactly the same way. Set them on the root together:
  </p>
  <?php docs_token_table(['--hue-brand', '--hue-accent', '--hue-good', '--hue-warn', '--hue-bad', '--hue-neutral']); ?>
  <p>
    <code>--hue-neutral</code> is the interesting one. The greys are not grey: they carry a
    trace of that hue at <code>--chroma-neutral: .012</code>, so they sit beside the brand
    without looking dirty. Move it toward your brand hue for a warmer page, or set
    <code>--chroma-neutral: 0</code> for true neutral grey.
  </p>
  <p>
    Beyond the hues, override a <em>role</em> rather than a ramp step. This is the
    supported way to depart from the generated palette:
  </p>
  <pre class="dx-code"><code>@layer app.base {
  :root {
    /* a specific brand colour the ramp cannot produce */
    --brand: #0b5cff;
    --brand-hover: #0847cc;
    /* everything else still follows --hue-brand */
  }
}</code></pre>
  <p>
    Your <code>@layer app.base</code> beats <code>deck.tokens</code> with no
    <code>!important</code>, because <a href="layers.php">layer order decides it</a>. Links,
    outline and ghost buttons, and <code>.btn-primary</code> read <code>--brand</code>, so
    they all take the new colour, in light and dark mode alike. A primary button works out
    its own text colour from its fill where the browser has <code>contrast-color()</code>.
    Override <code>--text-on-brand</code> as well: it is the ink on the solid fills that
    still use a ramp step, such as <code>.badge-solid</code> and the current page in
    <code>.pagination</code>, and on primary buttons in browsers without
    <code>contrast-color()</code>. The generated one is computed from a hue you have just
    stopped using. An override on <code>:root</code> stops at a
    <a href="#scope"><code>data-theme-scope</code></a> region, which works out its own
    brand; set it on the scope element to reach inside.
  </p>
</section>

<section class="stack stack-3">
  <h2 id="dont">What not to override</h2>
  <ul class="stack stack-3">
    <li>
      <strong>Not the ramp steps, one at a time.</strong> Setting
      <code>--brand-600</code> and leaving the other ten alone gives you a button that no
      longer matches its own hover state. Change the hue, or change the roles.
    </li>
    <li>
      <strong>Not <code>--ink-*</code> to get a dark page.</strong> That is
      <a href="dark-mode.php">dark mode</a>, and it already exists. Inverting the neutral
      ramp by hand gives you a dark page whose components still think they are on white.
    </li>
    <li>
      <strong>Not the lightness of a surface that carries text.</strong> The steps avoid
      the middle lightness band deliberately. Moving one into it means neither of Deck's
      static fallback text colours reaches 4.5:1 on that surface, at the hues Deck ships.
    </li>
    <li>
      <strong>Not <code>--hue-brand</code> on a component without
      <code>data-theme-scope</code>.</strong> Covered <a href="#scope">above</a>. Without
      the attribute it tints gradients and chart series and nothing else, quietly.
    </li>
    <li>
      <strong>Not with <code>!important</code>.</strong> If an override is not landing, the
      layer is wrong, not the specificity. <a href="layers.php">The layers guide</a> is
      four minutes and will save you the escalation.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
