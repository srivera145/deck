<?php
declare(strict_types=1);

/**
 * Migrating from Tailwind.
 *
 * Every claim about Tailwind on this page was checked against tailwindcss.com
 * at v4.3 while it was being written, and the things that could not be verified
 * are named at the bottom rather than smoothed over. A comparison a reader
 * catches lying is worth less than none, and this is the page most likely to be
 * read by somebody who knows the other framework better than we do.
 */

$page = [
    'path' => 'guides/migrating-from-tailwind.php',
    'title' => 'Move a page from Tailwind',
    'level' => 'Intermediate',
    'description' => "The same components written in Tailwind v4 and in Deck, side by side, with an honest account of what you gain, what you lose, and when not to switch.",
];

require __DIR__ . '/../_layout.php';

/* verify:foreign

   Tailwind's markup, kept together in one place and fenced so that
   tools/docs/verify.mjs does not read a Tailwind utility name here as a Deck
   class that has gone missing. The fence is a region rather than a file-level
   opt-out, so every Deck snippet further down this page is still checked
   against the inventory like any other page's. */
$TW = [
    'card' =>
        '<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm' . "\n" .
        '            dark:border-gray-700 dark:bg-gray-800">' . "\n" .
        '  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">' . "\n" .
        '    Invoice INV-2291' . "\n" .
        '  </h3>' . "\n" .
        '  <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">' . "\n" .
        '    Northwind Traders' . "\n" .
        '  </p>' . "\n" .
        '</div>',

    'button' =>
        '<button class="inline-flex items-center gap-2 rounded-md bg-indigo-600' . "\n" .
        '               px-4 py-2 text-sm font-semibold text-white shadow-sm' . "\n" .
        '               hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2' . "\n" .
        '               focus-visible:outline-offset-2 focus-visible:outline-indigo-600' . "\n" .
        '               disabled:opacity-50 disabled:cursor-not-allowed">' . "\n" .
        '  Save changes' . "\n" .
        '</button>',

    'field' =>
        '<div>' . "\n" .
        '  <label for="email"' . "\n" .
        '         class="block text-sm font-medium text-gray-900 dark:text-white">' . "\n" .
        '    Email' . "\n" .
        '  </label>' . "\n" .
        '  <input type="email" id="email" required' . "\n" .
        '         class="mt-2 block w-full rounded-md border-0 py-1.5 px-3' . "\n" .
        '                text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300' . "\n" .
        '                focus:ring-2 focus:ring-inset focus:ring-indigo-600' . "\n" .
        '                dark:bg-white/5 dark:text-white dark:ring-white/10">' . "\n" .
        '  <p class="mt-2 text-sm text-gray-500">Invoices go here.</p>' . "\n" .
        '</div>',

    'dark' =>
        '<div class="bg-white text-gray-900' . "\n" .
        '            dark:bg-gray-900 dark:text-gray-100">' . "\n" .
        '  <!-- and dark: on every coloured element below -->' . "\n" .
        '</div>',

    'theme' =>
        '/* app.css */' . "\n" .
        '@import "tailwindcss";' . "\n" .
        '@theme {' . "\n" .
        '  --color-brand-500: oklch(0.62 0.14 265);' . "\n" .
        '  --color-brand-600: oklch(0.52 0.14 265);' . "\n" .
        '  /* …and the other nine steps */' . "\n" .
        '}' . "\n" .
        '/* then rebuild, and update every bg-indigo-600 in the markup */',

    'rtl' =>
        '<!-- use the logical utilities throughout -->' . "\n" .
        '<div class="ms-4 me-2 ps-3 text-start">…</div>' . "\n" .
        '<!-- and audit for every ml-, mr-, pl-, pr-, text-left -->',
];
/* verify:/foreign */

/** A side-by-side pair. Tailwind on the left because that is what you have. */
function versus(string $title, string $tw, string $deck, string $note = ''): void
{
    ?>
    <div class="stack-3">
      <h3><?= e($title) ?></h3>
      <div class="split">
        <div class="stack-2">
          <p class="text-sm fw-semi text-muted">Tailwind</p>
          <pre class="dx-code"><code><?= e($tw) ?></code></pre>
        </div>
        <div class="stack-2">
          <p class="text-sm fw-semi text-brand">Deck</p>
          <pre class="dx-code"><code><?= e($deck) ?></code></pre>
        </div>
      </div>
      <?php if ($note !== ''): ?><p><?= $note ?></p><?php endif; ?>
    </div>
    <?php
}
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Guides</li>
    <li aria-current="page">Move a page from Tailwind</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Move a page from Tailwind</h1>
  <p class="lede">
    You have a Tailwind project and you are wondering what a page looks like in Deck. This
    is that, in markup rather than adjectives — and then an honest list of what you give up,
    because there is a real list and you will find it on day three whether or not it is on
    this page.
  </p>
</header>

<section class="stack-4">
  <h2 id="difference">The one difference everything else follows from</h2>
  <p>
    Tailwind gives you <strong>utilities</strong> and expects you to build the components.
    Deck gives you <strong>components</strong> and a smaller set of utilities for the gaps.
    That is the whole thing, and every trade below is a consequence of it.
  </p>
  <p>
    Tailwind's own documentation is explicit about this — if you need to reuse styles, it
    tells you to make a component in your template language or your framework. That is
    correct advice for a utility framework, and it means the button in your project is a
    button you designed, maintain and debug. In Deck the button is
    <code>.btn</code>, and its focus ring, disabled state, loading state, icon alignment,
    44px touch target and print appearance are somebody else's problem.
  </p>
  <p>
    So: if your product's value is in a distinctive interface, Tailwind's flexibility is
    the point and you should keep it. If your product's value is somewhere else and the
    interface needs to be good rather than novel, that flexibility is a bill you pay every
    sprint.
  </p>
</section>

<section class="stack-6">
  <h2 id="side-by-side">The same things, both ways</h2>

  <?php versus(
      'A card',
      $TW['card'],
      '<div class="card">' . "\n" .
      '  <div class="card-body stack-2">' . "\n" .
      '    <h3 class="card-title">Invoice INV-2291</h3>' . "\n" .
      '    <p class="text-sm text-muted">Northwind Traders</p>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Twelve utility classes against four component classes. The interesting part is not
       the count — it is that the Tailwind version names <code>gray-200</code>,
       <code>gray-700</code>, <code>gray-800</code>, <code>gray-900</code>,
       <code>gray-400</code> and <code>white</code>, so six colour decisions are now in
       this file and will need finding again if the palette moves.'
  ); ?>

  <?php versus(
      'A button',
      $TW['button'],
      '<button class="btn btn-primary">Save changes</button>',
      'The hover, focus and disabled states are in the Tailwind markup because they have to
       be somewhere. Deck puts them in the class. Both are legitimate; the difference shows
       up when the focus ring needs to change and there are 340 buttons.'
  ); ?>

  <?php versus(
      'A form field',
      $TW['field'],
      '<div class="field">' . "\n" .
      '  <label class="label" for="email">Email</label>' . "\n" .
      '  <input class="input" id="email" type="email" required>' . "\n" .
      '  <p class="help">Invoices go here.</p>' . "\n" .
      '</div>',
      'Deck also styles <code>:user-invalid</code> here, so the field turns red after
       someone has interacted with it and left it wrong — not on page load, which is what
       <code>:invalid</code> does. Getting that behaviour in Tailwind means a variant or a
       little JavaScript. See <a href="forms.php">the forms guide</a>.'
  ); ?>

  <?php versus(
      'Dark mode',
      $TW['dark'],
      '<div class="bg-surface">' . "\n" .
      '  <!-- nothing else. The tokens hold both values -->' . "\n" .
      '</div>',
      'Tailwind\'s <code>dark:</code> variant is per-utility, so a dark mode is a second
       colour decision on every element that has a first one. Deck resolves it in the token
       with <code>light-dark()</code>, so there is one declaration. Deck\'s way is less
       flexible — you cannot make one card invert independently — and for the common case of
       "the whole app has a dark mode" it is far less markup. Both approaches have three
       states worth supporting: light, dark, and following the system.'
  ); ?>

  <?php versus(
      'Changing the brand colour',
      $TW['theme'],
      '<html style="--hue-brand: 265">',
      'This is the widest gap between the two. Tailwind v4 moved theming into CSS with
       <code>@theme</code>, which is a real improvement over the old JavaScript config — but
       you still supply the ramp and you still rebuild. Deck generates eleven OKLCH steps
       from one hue angle at runtime, which is what makes per-tenant theming a database
       column rather than a build. See <a href="theming.php">the theming guide</a>.'
  ); ?>

  <?php versus(
      'Right to left',
      $TW['rtl'],
      '<html dir="rtl">' . "\n" .
      '<!-- Deck has no physical utilities to audit for -->',
      'Tailwind has the logical utilities — <code>ms-</code>, <code>me-</code>,
       <code>ps-</code>, <code>pe-</code>, <code>start-</code>, <code>end-</code> — and they
       work well. The difference is that it also has <code>ml-</code> and
       <code>mr-</code>, so an RTL migration is an audit. Deck ships no physical spacing
       utility at all, which removes the audit by removing the option.'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="translate">A translation table</h2>
  <p>
    Two things transfer directly, which makes the first hour easier than you expect.
  </p>
  <div class="alert alert-good">
    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#check-circle"></use></svg>
    <div>
      <div class="alert-title">The spacing scale and the breakpoints are the same numbers</div>
      <p class="alert-body">
        Tailwind's default spacing unit is <code>0.25rem</code>, so <code>p-4</code> is
        1rem. Deck's <code>--space-4</code> is also 1rem, so <code>.p-4</code> is the same
        1rem. And Tailwind's <code>sm</code>, <code>md</code> and <code>lg</code> are 40rem,
        48rem and 64rem — which are exactly Deck's three breakpoints. Your muscle memory
        for both is correct.
      </p>
    </div>
  </div>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Common Tailwind utilities and their Deck equivalents</caption>
      <thead>
        <tr><th scope="col">Tailwind</th><th scope="col">Deck</th><th scope="col">Notes</th></tr>
      </thead>
      <tbody>
        <tr><th scope="row" data-label="Tailwind"><code>p-4</code>, <code>px-4</code>, <code>py-4</code></th><td data-label="Deck"><code>.p-4</code>, <code>.px-4</code>, <code>.py-4</code></td><td data-label="Notes">Same value. Deck has only 0,1,2,3,4,6,8</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>ml-4</code>, <code>mr-4</code></th><td data-label="Deck"><code>.mis-4</code>, <code>.mie-4</code></td><td data-label="Notes">Logical only. No physical version exists</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>space-y-4</code></th><td data-label="Deck"><code>.stack-4</code></td><td data-label="Notes">A real <code>gap</code>, not a sibling selector</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>flex items-center gap-3</code></th><td data-label="Deck"><code>.cluster</code></td><td data-label="Notes">Also wraps by default</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>grid grid-cols-3 gap-4</code></th><td data-label="Deck"><code>.grid</code></td><td data-label="Notes">Auto-fits from a min width; no column count</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>text-sm</code>, <code>text-lg</code></th><td data-label="Deck"><code>.text-sm</code>, <code>.text-lg</code></td><td data-label="Notes">Same names, different values</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>font-semibold</code></th><td data-label="Deck"><code>.fw-semi</code></td><td data-label="Notes">620, not 600 — the face is variable</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>text-gray-500</code></th><td data-label="Deck"><code>.text-muted</code></td><td data-label="Notes">A role, so it follows dark mode</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>bg-white</code></th><td data-label="Deck"><code>.bg-surface</code></td><td data-label="Notes">As above</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>rounded-md</code>, <code>shadow-sm</code></th><td data-label="Deck"><code>.r-md</code>, <code>.shadow-1</code></td><td data-label="Notes">Five radius steps, five shadow steps</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>md:flex</code>, <code>lg:hidden</code></th><td data-label="Deck"><code>md:flex</code>, <code>lg:hidden</code></td><td data-label="Notes">Same widths. Deck's set is fixed, not generated</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>sr-only</code></th><td data-label="Deck"><code>.sr-only</code></td><td data-label="Notes">Identical</td></tr>
        <tr><th scope="row" data-label="Tailwind"><code>p-[13px]</code></th><td data-label="Deck">—</td><td data-label="Notes">No arbitrary values. Write a rule in your layer</td></tr>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-4">
  <h2 id="lose">What you give up</h2>
  <p>
    All of these are real, none of them are going away soon, and any one of them can be the
    reason not to switch.
  </p>
  <ol class="stack-4">
    <li>
      <strong>Editor autocomplete.</strong> This is the one people feel first and it is the
      biggest single loss. Tailwind ships an official IntelliSense extension for VS Code
      with completion, linting and hover previews of the generated CSS; Zed and the
      JetBrains IDEs have equivalent support built in; and there is an official Prettier
      plugin that sorts classes for you. Deck has <strong>none of that</strong> — no
      extension, no language server, no class sorting. You will be reading
      <a href="../reference/classes.php">the class reference</a> in another tab for the
      first week.
    </li>
    <li>
      <strong>Class names you cannot derive.</strong> Tailwind's are mechanical: knowing
      <code>p</code>, <code>t</code> and the scale gets you <code>pt-7</code> without
      looking it up, and the same grammar generalises to properties you have never used.
      Deck has 931 classes, 828 of them public, and a good proportion are names rather than
      formulas — <code>.check-note</code>, <code>.list-trail</code>,
      <code>.dg-cards-wrap</code>, <code>.speed-dial-label</code>. You cannot guess those;
      you learn them or you look them up. It is a genuinely larger memory cost and the
      reference exists because of it.
    </li>
    <li>
      <strong>The ecosystem.</strong> Tailwind has first-party plugins for typography and
      forms, a large community of component libraries, templates, UI kits and Stack Overflow
      answers, and any AI assistant you use has seen enormous amounts of Tailwind and almost
      no Deck. Deck has this documentation site and the source. If you get stuck at
      2am, that difference is the whole difference.
    </li>
    <li>
      <strong>Arbitrary values.</strong> <code>p-[13px]</code>,
      <code>grid-cols-[24rem_1fr]</code>, <code>bg-[#316ff6]</code> — Tailwind's escape
      hatch for the one-off. Deck has no equivalent syntax. The answer is a rule in
      <code>@layer app.pages</code> or a <code>style</code> attribute, which is more
      keystrokes and puts the value somewhere other than where you are working.
    </li>
    <li>
      <strong>A fixed 26 KB, whether you use it or not.</strong> Tailwind generates only
      the classes you actually used, so a small site ships a very small stylesheet. Deck's
      <code>deck.min.css</code> is 26 KB brotli — 169 KB raw — for a landing page with three
      buttons and for a whole application, because there is no build step and therefore
      nothing to purge. Above a certain app size Deck comes out ahead and it never grows;
      below it, Tailwind is smaller.
    </li>
    <li>
      <strong>A newer browser floor.</strong> Tailwind v4 targets Safari 16.4+, Chrome 111+
      and Firefox 128+. Deck's package declares Chrome and Edge 117+, Safari 17.4+ and
      Firefox 128+, and it leans on <code>light-dark()</code>, <code>@container</code> style
      queries and <code>:has()</code>. If you have to support Safari 16, Deck is not an
      option.
    </li>
    <li>
      <strong>Maturity.</strong> Tailwind is at v4.3 with years of production use behind it.
      Deck is v0.1 and is not on npm or Packagist yet — you install it
      <a href="../start/install.php">from the repository</a>. Its public API is frozen and
      checked on every build, which is the right start, but it is a start.
    </li>
  </ol>
</section>

<section class="stack-4">
  <h2 id="gain">What you get</h2>
  <ol class="stack-3">
    <li>
      <strong>No build step, at all.</strong> Not a faster one — none. No PostCSS, no Vite
      plugin, no scan of your source files for class names, no watcher, no CI step that regenerates CSS. Tailwind's own
      docs are clear that the Play CDN is "designed for development purposes only, and is
      not intended for production", so a build is not optional there. Deck is a
      <code>&lt;link&gt;</code> tag in any stack that can serve a file.
    </li>
    <li>
      <strong>Components you did not have to build.</strong> A data grid, a date picker, a
      combobox, charts, toasts, a kanban board, a rich text editor and a QR encoder. In
      Tailwind each of those is a decision, a dependency, or a week.
    </li>
    <li>
      <strong>Retheming from one number.</strong> Covered above. If you sell to
      businesses who want their own colours, this is a feature you would otherwise build.
    </li>
    <li>
      <strong>Markup you can read.</strong> A card is <code>.card</code>. Six months later
      a diff on a template shows what changed rather than a reflowed wall of utilities.
    </li>
    <li>
      <strong>Print, RTL and reduced motion, already done.</strong> 64 print rules, a
      logical-property codebase, and animations that are only written for people who have
      not asked for less motion. All three are things teams intend to do and do not.
    </li>
  </ol>
</section>

<section class="stack-4">
  <h2 id="how">Migrating in practice</h2>
  <p>
    Do not convert a codebase. Run both, convert at the page boundary, and stop when it
    stops paying.
  </p>
  <ol class="stack-3">
    <li>
      <strong>Load Deck first, then Tailwind.</strong> Tailwind's output is unlayered, and
      unlayered CSS beats every cascade layer — so Tailwind utilities will win over Deck
      components wherever both apply, which is what you want during a migration. If you
      want the reverse, import Tailwind into a layer that sits before Deck's.
    </li>
    <li>
      <strong>Convert one page, not one component.</strong> Deck's value is in the
      primitives composing, so a <code>.card</code> dropped into a Tailwind flex column
      shows you the smallest part of the benefit and all of the friction.
    </li>
    <li>
      <strong>Start with a form.</strong> It is where the ratio is best — the field, label,
      help, error and validation styling is a lot of Tailwind and very little Deck — and it
      is the fastest way to find out whether the trade suits you.
    </li>
    <li>
      <strong>Delete the <code>dark:</code> variants as you go.</strong> Once an element
      uses Deck's role tokens it has a dark mode, and the leftover
      <code>dark:</code> classes will fight it.
    </li>
    <li>
      <strong>Keep Tailwind for your bespoke screens.</strong> There is no prize for
      removing it. A marketing page with a custom layout is exactly what utilities are good
      at.
    </li>
  </ol>
</section>

<section class="stack-4">
  <h2 id="dont">When not to switch</h2>
  <ul class="stack-3">
    <li><strong>Your interface is the product.</strong> A design tool, a game, anything with a distinctive visual language. Components will be in your way.</li>
    <li><strong>You need Safari 16.</strong> Not negotiable.</li>
    <li><strong>Your team is fast in Tailwind.</strong> Fluency is worth more than either framework's feature list, and you would be trading a known speed for an unknown one.</li>
    <li><strong>You ship a small marketing site.</strong> Purged Tailwind will be smaller than 26 KB and you will not use the components.</li>
    <li><strong>You depend on a Tailwind component library.</strong> Replacing a UI kit is a much bigger project than replacing a stylesheet.</li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="checked">What was checked, and what was not</h2>
  <p>
    Every claim about Tailwind here was read off <code>tailwindcss.com</code> at
    <strong>v4.3</strong> while this page was written: the build-step requirement and the
    Play CDN's production warning, the browser floor, the <code>sm</code>/<code>md</code>/<code>lg</code>
    widths, the <code>0.25rem</code> spacing unit, <code>@theme</code>, the
    <code>dark:</code> variant and <code>@custom-variant</code>, the logical-property
    utilities, container queries in core, arbitrary values, and the editor tooling.
  </p>
  <p>Three things are <em>not</em> verified and are marked as such:</p>
  <ul class="stack-2">
    <li>
      <strong>Relative bundle sizes.</strong> Deck's 26 KB brotli is measured, from
      <code>dist/sizes.json</code>. Tailwind's output size depends entirely on your
      markup, so "above a certain app size" is a judgement and no number is given for it.
    </li>
    <li>
      <strong>Ecosystem size.</strong> Stated as a direction, not a count. Any specific
      number would be stale within a month.
    </li>
    <li>
      <strong>Deck's browser floor.</strong> Chrome and Edge 117, Safari 17.4, Firefox 128
      is what <code>package.json</code> declares. It has not been tested feature by feature
      against those exact versions, and some of what Deck uses —
      <code>light-dark()</code> in particular — may in practice need a slightly newer
      Safari than 17.4. Treat the declared floor as the intent and test if you are near it.
    </li>
  </ul>
  <p class="dx-note">
    Tailwind moves quickly. If something here has gone out of date, their docs are right
    and this page is wrong — please
    <a href="https://github.com/srivera145/deck/issues">say so</a>.
  </p>
</section>

<?php docs_footer(); ?>
