<?php
declare(strict_types=1);

$page = [
    'path' => '',
    'title' => 'Documentation',
    'level' => 'Beginner',
    'description' => 'Documentation for Deck, a CSS framework that ships as one stylesheet with no build step: install guides, component reference, cascade layers, theming, and the full class and token inventory.',
];

require __DIR__ . '/_layout.php';
?>

<header class="stack-3">
  <h1>Deck documentation</h1>
  <p class="lede">
    Deck is a CSS framework that ships as a single stylesheet. Add one
    <code>&lt;link&gt;</code> tag and you have buttons, forms, tables, a data grid,
    charts, overlays, and an icon sprite, with no build step, no config file, and no
    dependencies.
  </p>
</header>

<section class="stack-3">
  <h2>Start here</h2>
  <div class="grid">
    <article class="card card-link">
      <div class="card-body stack-2">
        <h3 class="card-title"><a class="link-quiet stretch" href="start/install.php">New to Deck</a></h3>
        <p class="text-sm text-muted">
          Install it in about a minute, then build a complete account settings page —
          nav bar, rail layout, real form controls, a confirm dialog and a save bar — in
          one sitting, typing every line.
        </p>
        <span class="badge badge-brand">Start here</span>
      </div>
    </article>
    <article class="card card-link">
      <div class="card-body stack-2">
        <h3 class="card-title"><a class="link-quiet stretch" href="components/button.php">Components</a></h3>
        <p class="text-sm text-muted">
          One page per component: every variant rendered, the class table generated from
          the stylesheet, the tokens it reads, and when not to reach for it.
        </p>
        <span class="badge">47 pages</span>
      </div>
    </article>
    <article class="card card-link">
      <div class="card-body stack-2">
        <h3 class="card-title"><a class="link-quiet stretch" href="reference/classes.php">Reference</a></h3>
        <p class="text-sm text-muted">
          All <?= (int) $api['counts']['classes'] ?> classes and
          <?= (int) $api['counts']['tokens'] ?> tokens, filterable by layer, bucket and
          name, plus the utility tables and the JavaScript, PHP and CLI APIs. Works
          without JavaScript.
        </p>
      </div>
    </article>
    <article class="card">
      <div class="card-body stack-2">
        <h3 class="card-title">The demo</h3>
        <p class="text-sm text-muted">
          Every component on one page, in context. It shows what Deck looks like; these
          pages explain how it works.
        </p>
        <a class="btn btn-sm" href="../index.php#demo">Open the demo</a>
      </div>
    </article>
  </div>
</section>

<section class="stack-3">
  <h2>How these docs are organised</h2>
  <p>
    Four modes, kept apart on purpose. Mixing reference into a tutorial is the usual way
    documentation becomes unreadable: a person learning wants a path, and a person
    looking something up wants a table.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <thead>
        <tr><th scope="col">Section</th><th scope="col">For</th><th scope="col">Looks like</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Section">Start</th>
          <td data-label="For">Learning</td>
          <td data-label="Looks like">A path from nothing to a styled page, in order.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Section">Guides</th>
          <td data-label="For">Doing a specific task</td>
          <td data-label="Looks like">Theming, dark mode, RTL, printing. Assumes you have Deck working.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Section">Components and reference</th>
          <td data-label="For">Looking something up</td>
          <td data-label="Looks like">Tables, generated from the source. No narrative.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Section">Explanation</th>
          <td data-label="For">Understanding a decision</td>
          <td data-label="Looks like">Why there is no build step, why cascade layers, why one hue.</td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-3">
  <h2>Status</h2>
  <div class="alert alert-info">
    <svg class="icon"><use href="../assets/deck/deck-icons.svg#info"></use></svg>
    <div>
      <div class="alert-title">
        <?php $covered = count(docs_claims()); ?>
        <?= (int) $covered ?> of <?= (int) $api['counts']['classes'] ?> classes have a page
      </div>
      <p class="alert-body">
        The extractor and verifier are in place, so every class is inventoried and every
        page is checked against the source on each build. The
        <?= (int) ($api['counts']['classes'] - $covered) ?> classes with no page yet are
        recorded in <code>tools/docs/undocumented.txt</code> rather than rounded off, and
        the build fails if that list grows. You can see exactly which they are by
        filtering the <a href="reference/classes.php?state=undocumented">class reference
        to “No page yet”</a>.
      </p>
    </div>
  </div>
</section>

<?php docs_footer(); ?>
