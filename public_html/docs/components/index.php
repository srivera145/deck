<?php
declare(strict_types=1);

/**
 * The component index: every public class Deck ships, under the page that
 * documents it.
 *
 * Nothing on this page is typed. docs_component_index() builds it on every
 * request from the pages' own `documents` claims, which tools/docs/verify.mjs
 * checks against dist/api.json on every build, and from dist/api.json for
 * whatever no page claims. So a public class cannot be in src/ and missing
 * here: it is under its page, or under "No page yet" with its source line. A
 * new page appears the moment it claims a class.
 *
 * It exists because a page title hides what the page covers. The date picker
 * had its own page and a gap report still said Deck had none, and a dashboard
 * was built without .sparkline, .donut and .ring, which live on pages called
 * Charts and Progress. ?format=json returns the same index with absolute
 * links, for a script or an AI assistant deciding whether a component exists.
 */

$page = [
    'path' => 'components/index.php',
    'title' => 'All components',
    'level' => 'Beginner',
    'description' => 'Every public class Deck ships, listed under the page that documents it and generated from the stylesheet inventory. Filter it, or fetch it as JSON.',
];

require __DIR__ . '/../_docs.php';

$index = docs_component_index();

if (($_GET['format'] ?? '') === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'about' => 'Every public Deck class, under the docs page that documents it. Generated on each request from dist/api.json and the docs pages\' documents claims, so a class missing from here does not exist in Deck. "unclaimed" is public classes no page covers yet.',
        'version' => $SITE['version'],
        'generated' => $api['generated'],
        'counts' => [
            'public' => $index['public'],
            'pages' => count($index['pages']),
            'unclaimed' => array_sum(array_map(static fn(array $u): int => count($u['classes']), $index['unclaimed'])),
        ],
        'pages' => array_map(static fn(array $p): array => [
            'title' => $p['title'],
            'url' => $DOCS_BASE . '/' . $p['path'],
            'section' => $p['section'],
            'description' => $p['description'],
            'classes' => array_column($p['classes'], 'name'),
        ], $index['pages']),
        'unclaimed' => array_map(static fn(array $u): array => [
            'classes' => $u['classes'],
            'source' => 'src/' . $u['file'] . ':' . $u['line'],
            'description' => $u['doc'] ?? $u['summary'],
            'url' => $DOCS_BASE . '/components/index.php#' . $u['anchor'],
        ], $index['unclaimed']),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

require __DIR__ . '/../_layout.php';

$q = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$pages = docs_pages();

/* The rows, each with the terms the filter matches. The same terms go to the
   script, so typing and submitting keep the same rows. */
$groups = [];
foreach ($index['pages'] as $p) {
    $terms = docs_terms(
        $p['title'],
        basename($p['path'], '.php'),
        $p['description'],
        ...array_column($pages[$p['path']]['headings'], 'text'),
        ...array_column($p['classes'], 'name'),
    );
    $groups[$p['section']][] = ['page' => $p, 'terms' => $terms, 'shown' => docs_terms_match($terms, $q)];
}
$strays = [];
foreach ($index['unclaimed'] as $u) {
    $terms = docs_terms((string) $u['doc'], ...$u['classes']);
    $strays[] = ['entry' => $u, 'terms' => $terms, 'shown' => docs_terms_match($terms, $q)];
}
$headings = ['components' => 'Components', 'reference' => 'Utilities and reference'];
$shownIn = static fn(array $rows): int => count(array_filter($rows, static fn(array $r): bool => $r['shown']));
$shown = $shownIn($strays) + array_sum(array_map($shownIn, $groups));
$total = count($index['pages']) + count($strays);
$strayCount = array_sum(array_map(static fn(array $u): int => count($u['classes']), $index['unclaimed']));
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">All components</li>
  </ol>
</nav>

<header class="stack stack-3">
  <h1>All components</h1>
  <p class="lede">
    All <?= (int) $index['public'] ?> public classes Deck ships, each listed under the
    page that documents it, so <code>.donut</code> and <code>.sparkline</code> appear
    under Charts and <code>.ring</code> under Progress instead of hiding behind a page
    name. The page is generated from <code>dist/api.json</code> and from the pages
    themselves each time it loads. Nothing on it is typed by hand, and a class with no
    page yet is listed at the end rather than left out.
  </p>
</header>

<form method="get" class="stack stack-3" action="index.php" role="search">
  <div class="field">
    <label class="label" for="dx-filter">Filter by name or purpose</label>
    <div class="search">
      <svg class="icon"><use href="../../assets/deck/deck-icons.svg#search"></use></svg>
      <input class="input" type="search" id="dx-filter" name="q" value="<?= e($q) ?>"
             placeholder="date picker, donut, upload" autocomplete="off">
    </div>
    <p class="help">
      Matches page titles, headings and descriptions, and every class name. “date
      picker”, “datepicker” and “.datepicker” are the same filter.
    </p>
  </div>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Filter</button>
    <?php if ($q !== ''): ?>
      <a class="btn btn-ghost" href="index.php">Clear</a>
    <?php endif; ?>
  </div>
</form>

<p class="text-muted" id="dx-count" role="status">
  Showing <?= (int) $shown ?> of <?= (int) $total ?> entries<?= $q !== '' ? ' matching “' . e($q) . '”' : '' ?>.
</p>

<?php foreach ($groups as $section => $rows): ?>
  <?php $id = $section !== '' ? $section : 'other'; ?>
  <section class="stack stack-3" aria-labelledby="<?= e($id) ?>" data-dx-group<?= $shownIn($rows) > 0 ? '' : ' hidden' ?>>
    <h2 id="<?= e($id) ?>"><?= e($headings[$section] ?? ucfirst($section ?: 'Other pages')) ?></h2>
    <div class="table-wrap">
      <table class="table table-stack">
        <caption class="sr-only">Pages in <?= e($headings[$section] ?? $section) ?>, with the public classes each documents</caption>
        <thead>
          <tr><th scope="col">Page</th><th scope="col">Public classes</th></tr>
        </thead>
        <tbody>
          <?php foreach ($rows as ['page' => $p, 'terms' => $terms, 'shown' => $on]): ?>
            <tr id="<?= e(str_replace(['/', '.php'], ['-', ''], $p['path'])) ?>" data-terms="<?= e($terms) ?>"<?= $on ? '' : ' hidden' ?>>
              <th scope="row" data-label="Page">
                <a href="<?= e($up . $p['path']) ?>"><?= e($p['title']) ?></a>
                <div class="text-sm text-muted fw-normal"><?= e($p['description']) ?></div>
              </th>
              <td data-label="Public classes">
                <span class="cluster cluster-tight">
                  <?php foreach ($p['classes'] as $c): ?>
                    <a href="<?= e($up . $c['href']) ?>"><code><?= e('.' . $c['name']) ?></code></a>
                  <?php endforeach; ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php endforeach; ?>

<section class="stack stack-3" aria-labelledby="no-page" data-dx-group<?= $shownIn($strays) > 0 ? '' : ' hidden' ?>>
  <h2 id="no-page">No page yet</h2>
  <p>
    <?= (int) $strayCount ?> public classes no page claims, grouped by the component
    <code>dist/api.json</code> puts them in. They are as supported as the rest; they
    just have no prose yet, and <code>tools/docs/undocumented.txt</code> records them as
    the backlog. The source line is the documentation until a page claims them.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Public classes with no documentation page yet</caption>
      <thead>
        <tr><th scope="col">Classes</th><th scope="col">What it does</th><th scope="col">Source</th></tr>
      </thead>
      <tbody>
        <?php foreach ($strays as ['entry' => $u, 'terms' => $terms, 'shown' => $on]): ?>
          <tr id="<?= e($u['anchor']) ?>" data-terms="<?= e($terms) ?>"<?= $on ? '' : ' hidden' ?>>
            <th scope="row" data-label="Classes">
              <span class="cluster cluster-tight">
                <?php foreach ($u['classes'] as $name): ?>
                  <code><?= e('.' . $name) ?></code>
                <?php endforeach; ?>
              </span>
            </th>
            <td data-label="What it does">
              <?php if ($u['doc']): ?>
                <?= e($u['doc']) ?>
              <?php else: ?>
                <span class="dx-dim"><?= e($u['summary']) ?></span>
              <?php endif; ?>
              <?php if ($u['documentedIn']): ?>
                <div class="text-sm text-muted">
                  Part of <code><?= e('.' . $u['family']) ?></code>, which
                  <a href="<?= e($up . $u['documentedIn']['path']) ?>"><?= e($u['documentedIn']['title']) ?></a> documents.
                </div>
              <?php endif; ?>
            </td>
            <td data-label="Source"><code class="dx-dim"><?= e($u['file'] . ':' . $u['line']) ?></code></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="stack stack-3" aria-labelledby="json">
  <h2 id="json">As JSON</h2>
  <p>
    The same index, with absolute links, for a script or an AI assistant:
    <a href="index.php?format=json"><code>components/index.php?format=json</code></a>.
    It answers “does Deck have one?” without reading every page. For every class,
    internal ones included, with layers, declarations and the tokens each reads, the
    <a href="../reference/classes.php">class reference</a> is generated from the same
    file.
  </p>
</section>

<script>
  /* Enhancement only. The form filters on the server; this does the same as you
     type. It is docs_terms_match() from _docs.php line for line, so the rows it
     keeps are the rows a submit would. */
  (function () {
    var input = document.getElementById('dx-filter');
    var count = document.getElementById('dx-count');
    if (!input || !count) return;
    var rows = Array.prototype.slice.call(document.querySelectorAll('tr[data-terms]'));
    var groups = Array.prototype.slice.call(document.querySelectorAll('[data-dx-group]'));
    var stop = <?= json_encode(DOCS_STOPWORDS) ?>;
    var total = <?= (int) $total ?>;
    var timer = null;

    function stem(w) {
      return w.length > 3 && w.slice(-1) === 's' && w.slice(-2, -1) !== 's' ? w.slice(0, -1) : w;
    }
    function words(s, dropStop) {
      return (s.toLowerCase().match(/[a-z0-9]+/g) || [])
        .filter(function (w) { return !dropStop || stop.indexOf(w) === -1; })
        .map(stem);
    }
    function matches(terms, q) {
      var all = words(q, false);
      var core = words(q, true);
      if (!core.length) core = all;
      if (!all.length) return true;
      if (terms.indexOf(' ' + all.join('')) !== -1 || terms.indexOf(' ' + core.join('')) !== -1) return true;
      return core.every(function (w) { return terms.indexOf(' ' + w) !== -1; });
    }
    function apply() {
      var q = input.value.trim();
      var shown = 0;
      rows.forEach(function (r) {
        var hit = matches(r.dataset.terms, q);
        r.hidden = !hit;
        if (hit) shown++;
      });
      groups.forEach(function (g) {
        g.hidden = !g.querySelector('tr[data-terms]:not([hidden])');
      });
      count.textContent = 'Showing ' + shown + ' of ' + total + ' entries'
        + (q ? ' matching “' + q + '”' : '') + '.';
    }

    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(apply, 90);
    });
  })();
</script>

<?php docs_footer(); ?>
