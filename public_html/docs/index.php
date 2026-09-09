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
        <h3 class="card-title"><a class="link-quiet stretch" href="components/button.php">Components</a></h3>
        <p class="text-sm text-muted">
          One page per component: every variant rendered, the class table generated from
          the stylesheet, the tokens it reads, and when not to reach for it.
        </p>
        <span class="badge badge-brand">Button is written</span>
      </div>
    </article>
    <article class="card card-link">
      <div class="card-body stack-2">
        <h3 class="card-title"><a class="link-quiet stretch" href="search.php">Search the reference</a></h3>
        <p class="text-sm text-muted">
          All <?= (int) $api['counts']['classes'] ?> classes and
          <?= (int) $api['counts']['tokens'] ?> tokens, filterable by name, layer, and
          source file. Works without JavaScript.
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
        <a class="btn btn-sm" href="../index.php">Open the demo</a>
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
      <div class="alert-title">The system is built; the pages are not</div>
      <p class="alert-body">
        <a href="components/button.php">Button</a> is the finished exemplar that sets the
        template for the rest. The extractor and verifier are in place, so
        <?= (int) $api['counts']['classes'] ?> classes are inventoried and every page is
        checked against the source on each build.
        <?= (int) $api['counts']['documented'] ?> classes are documented and
        <?= (int) $api['counts']['undocumented'] ?> are not — that number is the backlog,
        and it is recorded rather than rounded off.
      </p>
    </div>
  </div>
</section>

<?php docs_footer(); ?>
