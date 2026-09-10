<?php
declare(strict_types=1);

/**
 * Dark mode.
 *
 * The three caveats near the bottom were measured in a browser, not guessed:
 * eight status surfaces do not follow the theme, and the shadow tokens follow
 * the OS preference rather than the attribute. Both are real and both will be
 * noticed by anyone who ships a theme switch, so they are on the page rather
 * than left to be discovered.
 */

$page = [
    'path' => 'guides/dark-mode.php',
    'title' => 'Add a dark mode switch',
    'level' => 'Beginner',
    'description' => "Dark mode already works in Deck with no configuration. Here is how to add a switch a person can press, make the choice stick, and what still needs your attention.",
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Guides</li>
    <li aria-current="page">Add a dark mode switch</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Add a dark mode switch</h1>
  <p class="lede">
    Dark mode is already on. Deck follows the operating system with no configuration, so
    the job on this page is narrower than you expected: add a control a person can press
    to override their OS, make that choice survive a reload, and know the three places
    where the automatic answer is not quite right yet.
  </p>
</header>

<section class="stack-4">
  <h2 id="switch">The switch</h2>
  <p>
    One attribute on a button. No handler, no state, no code.
  </p>
  <pre class="dx-code"><code>&lt;button class="btn btn-icon btn-ghost" data-deck-theme aria-label="Switch theme"&gt;
  &lt;svg class="icon"&gt;&lt;use href="/assets/deck/deck-icons.svg#moon"&gt;&lt;/use&gt;&lt;/svg&gt;
&lt;/button&gt;</code></pre>
  <?php docs_example(
      '<button class="btn btn-icon btn-ghost" data-deck-theme aria-label="Switch theme">' . "\n" .
      '  <svg class="icon"><use href="../../assets/deck/deck-icons.svg#moon"></use></svg>' . "\n" .
      '</button>' . "\n" .
      '<button class="btn btn-sm" data-deck-theme>With a label instead</button>',
      'Both of these switch this page. Press one'
  ); ?>
  <p>
    That writes <code>data-theme="dark"</code> or <code>data-theme="light"</code> on the
    <code>&lt;html&gt;</code> element and saves it to <code>localStorage</code> under
    <code>deck-theme</code>. On the next load the saved value is applied before
    <code>Deck.init()</code> runs, so there is no flash of the wrong theme.
  </p>
  <p>
    From your own code, <code>Deck.theme()</code> is the same thing:
  </p>
  <pre class="dx-code"><code>Deck.theme();          // 'auto', 'light' or 'dark'
Deck.theme('dark');    // set it
Deck.theme('light');</code></pre>
  <p>
    Three states, not two. <code>'auto'</code> means no attribute is set and the OS is
    deciding — which is the state a page starts in and the one a good switch should be
    able to return to. A two-way toggle quietly takes that option away from someone whose
    machine already switches at sunset.
  </p>
  <?php docs_example(
      '<div class="segmented" role="group" aria-label="Colour theme">' . "\n" .
      '  <button class="btn btn-sm" onclick="document.documentElement.removeAttribute(\'data-theme\')">Auto</button>' . "\n" .
      '  <button class="btn btn-sm" onclick="Deck.theme(\'light\')">Light</button>' . "\n" .
      '  <button class="btn btn-sm" onclick="Deck.theme(\'dark\')">Dark</button>' . "\n" .
      '</div>',
      'Three states. Auto is the one people actually want'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="how">Why it works without a second stylesheet</h2>
  <p>
    Deck declares its semantic tokens with <code>light-dark()</code>, so each one carries
    both values and the browser picks based on <code>color-scheme</code>:
  </p>
  <pre class="dx-code"><code>:root { color-scheme: light dark; }

--bg:      light-dark(var(--ink-25),  var(--ink-1000));
--surface: light-dark(var(--ink-0),   var(--ink-950));
--text:    light-dark(var(--ink-900), var(--ink-100));
--brand:   light-dark(var(--brand-600), var(--brand-400));

[data-theme="light"] { color-scheme: light; }
[data-theme="dark"]  { color-scheme: dark; }</code></pre>
  <p>
    There is no <code>@media (prefers-color-scheme: dark)</code> block redefining forty
    colours, and no <code>.dark</code> class threaded through every rule. There are 16 role
    tokens, each holding a pair, and switching <code>color-scheme</code> switches all of
    them at once.
  </p>
  <p>
    <code>color-scheme</code> also tells the browser to render its own furniture darkly:
    form controls, scrollbars, the spellcheck underline and the space outside the page.
    That is the part hand-rolled dark modes usually miss, and it is why a page with dark
    CSS and light scrollbars looks wrong in a way that is hard to place.
  </p>
</section>

<section class="stack-4">
  <h2 id="your-css">Making your own CSS follow</h2>
  <p>
    One rule: <strong>reference the roles, not the ramps.</strong>
  </p>
  <div class="split">
    <div class="stack-2">
      <p class="text-sm fw-semi text-bad">Hard-codes white into the page</p>
      <pre class="dx-code"><code>@layer app.components {
  .invoice {
    background: var(--ink-0);
    color: var(--ink-900);
    border: 1px solid var(--ink-200);
  }
}</code></pre>
    </div>
    <div class="stack-2">
      <p class="text-sm fw-semi text-good">Follows the theme for free</p>
      <pre class="dx-code"><code>@layer app.components {
  .invoice {
    background: var(--surface);
    color: var(--text);
    border: 1px solid var(--line);
  }
}</code></pre>
    </div>
  </div>
  <p>
    The ramps — <code>--ink-*</code>, <code>--brand-*</code>, <code>--good-*</code> — are
    fixed colours and are the same in both themes by design. They are the palette the roles
    are mixed from. The 16 roles are listed on the
    <a href="../reference/tokens.php#roles">token reference</a>; if you only learn four,
    learn <code>--bg</code>, <code>--surface</code>, <code>--text</code> and
    <code>--line</code>.
  </p>
  <p>
    For a value with no matching role, use <code>light-dark()</code> yourself. It is a
    plain CSS function and it works in your layer exactly as it does in Deck's:
  </p>
  <pre class="dx-code"><code>.chart-gridline { stroke: light-dark(oklch(90% .01 232), oklch(30% .01 232)); }</code></pre>
</section>

<section class="stack-4">
  <h2 id="server">Rendering the choice server-side</h2>
  <p>
    <code>localStorage</code> is read by a script, which means it is read after the HTML
    has arrived. Deck applies it before first paint so you will not usually see a flash,
    but the robust version is to store the preference on the user and render it:
  </p>
  <pre class="dx-code"><code>&lt;html &lt;?= Deck::htmlAttributes(lang: 'en') ?&gt; &lt;?= Deck::theme(mode: $user-&gt;theme) ?&gt;&gt;
&lt;!-- &lt;html lang="en" data-theme="dark"&gt; --&gt;</code></pre>
  <p>
    Pass <code>null</code> for a user who has not chosen, and no attribute is emitted — so
    the OS decides, which is the correct default. Never emit
    <code>data-theme="light"</code> as a default: that overrides the preference of everyone
    who set their machine to dark, which is the opposite of what a default should do.
  </p>
</section>

<section class="stack-4">
  <h2 id="caveats">Three things that are not automatic yet</h2>
  <p>
    Measured on a real page rather than assumed. None of these will stop you shipping, and
    all three are the kind of thing you would otherwise find at the wrong moment.
  </p>

  <div class="alert alert-warn">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#alert-triangle"></use></svg>
    <div>
      <div class="alert-title">1. Eight status surfaces stay light</div>
      <p class="alert-body">
        <code>.alert-good</code>, <code>.alert-warn</code>, <code>.alert-bad</code>,
        <code>.badge-good</code>, <code>.badge-warn</code>, <code>.badge-bad</code>,
        <code>.icon-tile-good</code> and <code>.icon-tile-bad</code> all fill with the
        <code>100</code> step of their ramp, which is a fixed pale tint with no
        <code>light-dark()</code> around it. On a dark page they read as light stickers.
        They are still legible — the text is the fixed dark <code>700</code> step, so
        contrast measures between 5.3:1 and 6.9:1 — but they do not match the page.
        <code>.alert-info</code> and <code>.badge-brand</code> are fine, because those use
        <code>--brand-soft</code>, which is a role.
      </p>
    </div>
  </div>
  <p>Until that is fixed in the framework, this is the patch:</p>
  <pre class="dx-code"><code>@layer app.base {
  :root {
    /* the fill */
    --good-100: light-dark(oklch(94%   .04 var(--hue-good)), oklch(26% .05 var(--hue-good)));
    --warn-100: light-dark(oklch(95%   .04 var(--hue-warn)), oklch(26% .05 var(--hue-warn)));
    --bad-100:  light-dark(oklch(94.5% .04 var(--hue-bad)),  oklch(26% .05 var(--hue-bad)));

    /* and the text on it, or you get dark on dark */
    --good-700: light-dark(oklch(43% .12 var(--hue-good)), oklch(86% .10 var(--hue-good)));
    --warn-700: light-dark(oklch(43% .12 var(--hue-warn)), oklch(86% .10 var(--hue-warn)));
    --bad-700:  light-dark(oklch(43% .12 var(--hue-bad)),  oklch(86% .10 var(--hue-bad)));
  }
}</code></pre>
  <p class="text-sm text-muted">
    Override the fill and its text together, or you get dark text on a dark fill —
    readable in neither theme. Keeping <code>var(--hue-*)</code> in there means the patch
    still follows <a href="theming.php">your brand hues</a> rather than pinning the status
    colours to Deck's.
  </p>

  <div class="alert alert-warn">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#alert-triangle"></use></svg>
    <div>
      <div class="alert-title">2. Shadows follow the OS, not your switch</div>
      <p class="alert-body">
        Dark mode wants heavier shadows, because a soft grey shadow is invisible on a near
        black surface. Deck adjusts <code>--shadow-2</code> through <code>--shadow-4</code>
        for that — but it does it inside
        <code>@media (prefers-color-scheme: dark)</code>, which only responds to the
        operating system. Force dark with the attribute on a light machine and you get a
        dark page with light-mode shadows; force light on a dark machine and you get heavy
        shadows on white.
      </p>
    </div>
  </div>
  <pre class="dx-code"><code>@layer app.base {
  [data-theme="dark"] {
    --shadow-2: 0 2px 4px oklch(0% 0 0 / .3), 0 4px 12px oklch(0% 0 0 / .34);
    --shadow-3: 0 4px 10px oklch(0% 0 0 / .34), 0 14px 30px oklch(0% 0 0 / .4);
    --shadow-4: 0 10px 20px oklch(0% 0 0 / .4), 0 28px 60px oklch(0% 0 0 / .5);
    --shadow-inset: inset 0 1px 0 oklch(100% 0 0 / .06);
  }
}</code></pre>
  <p class="text-sm text-muted">
    If your switch only ever offers "follow the system", neither of these applies to you.
    That is a legitimate product decision and it is the cheapest correct dark mode there
    is.
  </p>

  <div class="alert alert-info">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#info"></use></svg>
    <div>
      <div class="alert-title">3. Images and embeds do not have a dark version</div>
      <p class="alert-body">
        A screenshot with a white background, a logo drawn in black, a third-party iframe.
        None of these know about your theme. Deck cannot help — but
        <code>&lt;picture&gt;</code> can, and it needs no JavaScript.
      </p>
    </div>
  </div>
  <pre class="dx-code"><code>&lt;picture&gt;
  &lt;source srcset="/logo-dark.svg" media="(prefers-color-scheme: dark)"&gt;
  &lt;img src="/logo-light.svg" alt="Ledgerly"&gt;
&lt;/picture&gt;</code></pre>
</section>

<section class="stack-4">
  <h2 id="testing">Testing it</h2>
  <ul class="stack-2">
    <li>
      In Chrome dev tools: the three-dot menu, More tools, Rendering, then
      <em>Emulate prefers-color-scheme</em>. Firefox has a light/dark toggle directly in
      the Inspector.
    </li>
    <li>
      Test <strong>both</strong> paths. The OS preference and the forced attribute take
      different code paths through Deck, as caveat two shows, so a page that is right one
      way can be wrong the other.
    </li>
    <li>
      Look for anything that stayed the same. A surface that did not move between the two
      screenshots is a hard-coded colour, and it is almost always a ramp token used where a
      role belonged.
    </li>
    <li>
      Check your own <code>box-shadow</code>s and any <code>rgba(0,0,0,…)</code> you have
      written. Black at 8% opacity is a shadow on white and nothing at all on black.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="next">Next</h2>
  <p>
    <a href="theming.php">Changing your brand colour</a> uses the same token system and the
    two compose — a purple tenant gets a correct dark mode without anyone choosing a second
    purple. The <a href="../reference/tokens.php">token reference</a> lists all 144 with a
    live preview of each.
  </p>
</section>

<?php docs_footer(); ?>
