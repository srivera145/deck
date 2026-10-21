<?php
declare(strict_types=1);

/**
 * Search: the pages that answer a query, then the class and token reference.
 *
 * Filtering happens on the server, from a plain GET form. That is the whole
 * fallback: with JavaScript off this page still searches, still paginates, and
 * still links. The progressive enhancement at the bottom filters the rendered
 * rows as you type so a match is instant, and it degrades to the form when the
 * script does not run. A docs site for a CSS framework that needs JavaScript to
 * list its own classes would be a poor advertisement.
 *
 * Pages come first because a page is what people are looking for. This used to
 * be the class table alone, matched on raw substrings, with a file and line in
 * place of a link: "date picker" found one row and not .datepicker, "upload"
 * found nothing, and a hit for .donut did not say it was on the Charts page.
 * docs_find() in _docs.php ranks pages by their titles, headings, descriptions
 * and the classes they claim instead.
 *
 * The navbar's box sends go=1. When one page is clearly the answer
 * (docs_sure()), the search opens it, at the heading or class row that
 * matched, and the page says so and links back here. Every other search, and
 * every search from this page's own form, shows the results.
 */

$page = [
    'path' => 'search.php',
    'title' => 'Search the docs',
    'level' => 'Beginner',
    'description' => 'Search the Deck docs: pages ranked by title, heading and the classes they cover, then every class and design token by name, layer or source file.',
];

require __DIR__ . '/_docs.php';

$q = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$layer = is_string($_GET['layer'] ?? null) ? trim($_GET['layer']) : '';
$kind = is_string($_GET['kind'] ?? null) ? trim($_GET['kind']) : 'classes';
$needle = mb_strtolower($q);

$hits = docs_find($q);
$sure = isset($_GET['go']) ? docs_sure($hits) : null;
if ($sure !== null) {
    $to = $sure['path'] . '?found=' . rawurlencode($q) . ($sure['fragment'] !== null ? '#' . $sure['fragment'] : '');
    header('Location: ' . $to, true, 302);
    exit;
}

require __DIR__ . '/_layout.php';

$classes = $api['classes'];
$tokens = $api['tokens'];

if ($layer !== '') {
    $classes = array_values(array_filter($classes, static fn(array $c): bool => ($c['layer'] ?? '') === $layer));
}
if ($needle !== '') {
    /* By name first, as a key, so "date picker" finds .datepicker and an
       exact .ring comes before the twenty comments with "during" in them. */
    $key = docs_query($q)['full'];
    $rank = static function (array $c) use ($key, $needle): ?int {
        $name = docs_key($c['name']);
        if ($key !== '' && $name === $key) {
            return 0;
        }
        if ($key !== '' && str_starts_with($name, $key)) {
            return 1;
        }
        if (($key !== '' && str_contains($name, $key)) || str_contains(mb_strtolower($c['name']), $needle)) {
            return 2;
        }
        if (str_contains(mb_strtolower((string) $c['doc']), $needle) || str_contains(mb_strtolower($c['file']), $needle)) {
            return 3;
        }
        return null;
    };
    $ranked = [];
    foreach ($classes as $c) {
        $r = $rank($c);
        if ($r !== null) {
            $ranked[] = [$r, $c];
        }
    }
    usort($ranked, static fn(array $a, array $b): int => $a[0] <=> $b[0]);
    $classes = array_column($ranked, 1);
    $tokens = array_values(array_filter($tokens, static fn(array $t): bool =>
        str_contains(mb_strtolower($t['name']), $needle)
        || str_contains(mb_strtolower((string) $t['value']), $needle)));
}

$showing = $kind === 'tokens' ? count($tokens) : count($classes);

/** How a hit matched, as a reader would put it. */
$how = static function (array $hit): string {
    return match (true) {
        $hit['via'] === 'title' => 'Title',
        $hit['via'] === 'name' => 'Page name',
        $hit['via'] === 'description' => 'Description',
        str_starts_with($hit['via'], '.') => 'Class <code>' . e($hit['via']) . '</code>',
        default => 'Heading “' . e($hit['via']) . '”',
    };
};
?>

<header class="stack stack-3">
  <h1>Search</h1>
  <p class="lede">
    Pages first, matched on their titles, headings, descriptions and the classes they
    cover. Then every class and token Deck defines, read straight from
    <code>src/*.css</code>: <?= (int) $api['counts']['classes'] ?> classes,
    <?= (int) $api['counts']['tokens'] ?> tokens,
    <?= (int) $api['stylesheets'] ?> stylesheets. To browse instead, the
    <a href="components/index.php">component index</a> lists every public class under
    the page that covers it.
  </p>
</header>

<form method="get" class="stack stack-3" action="search.php">
  <div class="field">
    <label class="label" for="q">Search pages, classes and tokens</label>
    <div class="search">
      <svg class="icon"><use href="../assets/deck/deck-icons.svg#search"></use></svg>
      <input class="input" type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="date picker, btn, --space, forms.css">
    </div>
  </div>

  <div class="field-row">
    <div class="field">
      <label class="label" for="layer">Layer</label>
      <select class="select" id="layer" name="layer">
        <option value="">Every layer</option>
        <?php foreach ($api['layers'] as $l): ?>
          <option value="<?= e($l) ?>"<?= $l === $layer ? ' selected' : '' ?>><?= e($l) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label class="label" for="kind">Show</label>
      <select class="select" id="kind" name="kind">
        <option value="classes"<?= $kind === 'classes' ? ' selected' : '' ?>>Classes</option>
        <option value="tokens"<?= $kind === 'tokens' ? ' selected' : '' ?>>Tokens</option>
      </select>
    </div>
  </div>

  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Search</button>
    <a class="btn" href="search.php">Clear</a>
  </div>
</form>

<?php if ($q !== ''): ?>
  <!-- Ranked on the server for the query that was sent. Typing a new one hides
       it until the form is sent, rather than leaving stale pages showing. -->
  <section class="stack stack-3" id="dx-pages" data-q="<?= e($q) ?>" aria-labelledby="pages">
    <h2 id="pages">Pages</h2>
    <?php if ($hits === []): ?>
      <p class="text-muted">
        No page matches “<?= e($q) ?>”. The table below also searches source comments
        and file names.
      </p>
    <?php else: ?>
      <div class="table-wrap">
        <table class="table table-stack">
          <caption class="sr-only">Pages matching the search, best first</caption>
          <thead>
            <tr><th scope="col">Page</th><th scope="col">Matched on</th><th scope="col">What it covers</th></tr>
          </thead>
          <tbody>
            <?php foreach (array_slice($hits, 0, 10) as $hit): ?>
              <tr>
                <th scope="row" data-label="Page">
                  <a href="<?= e($hit['href']) ?>"><?= e($hit['title']) ?></a>
                  <?php if ($hit['section'] !== ''): ?><span class="dx-dim"><?= e($hit['section']) ?></span><?php endif; ?>
                </th>
                <td data-label="Matched on"><?= $how($hit) ?></td>
                <td data-label="What it covers"><?= e($hit['description']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php if (count($hits) > 10): ?>
        <p class="text-sm text-muted"><?= count($hits) - 10 ?> weaker matches not shown.</p>
      <?php endif; ?>
    <?php endif; ?>
  </section>
<?php endif; ?>

<p class="text-muted" id="dx-count" role="status">
  Showing <?= (int) $showing ?> <?= $kind === 'tokens' ? 'tokens' : 'classes' ?><?= $q !== '' ? ' matching “' . e($q) . '”' : '' ?>.
</p>

<?php if ($kind === 'tokens'): ?>
  <div class="table-wrap">
    <table class="table table-stack" id="dx-table">
      <caption class="sr-only">Design tokens</caption>
      <thead>
        <tr><th scope="col">Token</th><th scope="col">Live</th><th scope="col">Value</th><th scope="col">Source</th></tr>
      </thead>
      <tbody>
        <?php foreach ($tokens as $t): ?>
          <tr data-name="<?= e($t['name'] . ' ' . $t['value']) ?>">
            <th scope="row" data-label="Token"><code><?= e($t['name']) ?></code></th>
            <td data-label="Live">
              <?php if ($t['kind'] === 'color'): ?>
                <span class="dx-swatch" style="background: var(<?= e($t['name']) ?>)"></span>
              <?php else: ?>
                <span class="dx-dim"><?= e($t['kind']) ?></span>
              <?php endif; ?>
            </td>
            <td data-label="Value"><code class="dx-dim"><?= e($t['value']) ?></code></td>
            <td data-label="Source"><code class="dx-dim"><?= e($t['file'] . ':' . $t['line']) ?></code></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="table-wrap">
    <table class="table table-stack" id="dx-table">
      <caption class="sr-only">Classes Deck defines, with the page that documents each</caption>
      <thead>
        <tr><th scope="col">Class</th><th scope="col">Layer</th><th scope="col">What it does</th><th scope="col">Documented in</th></tr>
      </thead>
      <tbody>
        <?php foreach ($classes as $c): ?>
          <tr data-name="<?= e($c['name'] . ' ' . docs_key($c['name']) . ' ' . $c['file'] . ' ' . (string) $c['doc']) ?>">
            <th scope="row" data-label="Class"><code><?= e('.' . $c['name']) ?></code></th>
            <td data-label="Layer"><code class="dx-dim"><?= e($c['layer'] ?? '—') ?></code></td>
            <td data-label="What it does">
              <?php if ($c['doc']): ?><?= e($c['doc']) ?>
              <?php else: ?><span class="dx-dim"><?= e(docs_summarise($c)) ?></span><?php endif; ?>
            </td>
            <td data-label="Documented in"><?php docs_home_cell($c, ''); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<script>
  /* Enhancement only. The form above already works; this filters the rows that
     are on the page so a match appears without a round trip. Each row carries
     its name as a key too, so "date picker" keeps the .datepicker rows here as
     it does on the server. */
  (function () {
    var input = document.getElementById('q');
    var table = document.getElementById('dx-table');
    var count = document.getElementById('dx-count');
    var pages = document.getElementById('dx-pages');
    if (!input || !table) return;
    var rows = Array.prototype.slice.call(table.tBodies[0].rows);
    var noun = <?= json_encode($kind === 'tokens' ? 'tokens' : 'classes') ?>;
    input.addEventListener('input', function () {
      var q = input.value.trim().toLowerCase();
      var key = q.replace(/[^a-z0-9]+/g, '');
      var shown = 0;
      rows.forEach(function (r) {
        var name = r.dataset.name.toLowerCase();
        var hit = !q || name.indexOf(q) !== -1 || (key !== '' && name.indexOf(key) !== -1);
        r.hidden = !hit;
        if (hit) shown++;
      });
      if (pages) pages.hidden = input.value.trim() !== pages.dataset.q;
      count.textContent = 'Showing ' + shown + ' ' + noun + (q ? ' matching “' + input.value.trim() + '”' : '') + '.';
    });
  })();
</script>

<?php docs_footer(); ?>
