<?php
declare(strict_types=1);

/**
 * The class and token reference, filterable.
 *
 * Filtering happens on the server, from a plain GET form. That is the whole
 * fallback: with JavaScript off this page still searches, still paginates, and
 * still links. The progressive enhancement at the bottom filters the rendered
 * rows as you type so a match is instant, and it degrades to the form when the
 * script does not run. A docs site for a CSS framework that needs JavaScript to
 * list its own classes would be a poor advertisement.
 */

$page = [
    'path' => 'search.php',
    'title' => 'Search the reference',
    'level' => 'Beginner',
    'description' => 'Search every class and design token Deck defines, by name, cascade layer, or source file. Server-rendered, works without JavaScript, and generated from the stylesheet source.',
];

require __DIR__ . '/_layout.php';

$q = trim((string) ($_GET['q'] ?? ''));
$layer = trim((string) ($_GET['layer'] ?? ''));
$kind = trim((string) ($_GET['kind'] ?? 'classes'));
$needle = mb_strtolower($q);

$classes = $api['classes'];
$tokens = $api['tokens'];

if ($layer !== '') {
    $classes = array_values(array_filter($classes, static fn(array $c): bool => ($c['layer'] ?? '') === $layer));
}
if ($needle !== '') {
    $classes = array_values(array_filter($classes, static fn(array $c): bool =>
        str_contains(mb_strtolower($c['name']), $needle)
        || str_contains(mb_strtolower((string) $c['doc']), $needle)
        || str_contains(mb_strtolower($c['file']), $needle)));
    $tokens = array_values(array_filter($tokens, static fn(array $t): bool =>
        str_contains(mb_strtolower($t['name']), $needle)
        || str_contains(mb_strtolower((string) $t['value']), $needle)));
}

$showing = $kind === 'tokens' ? count($tokens) : count($classes);
?>

<header class="stack-3">
  <h1>Reference</h1>
  <p class="lede">
    Every class and token Deck defines, read straight from
    <code>src/*.css</code>. <?= (int) $api['counts']['classes'] ?> classes,
    <?= (int) $api['counts']['tokens'] ?> tokens,
    <?= (int) $api['stylesheets'] ?> stylesheets.
  </p>
</header>

<form method="get" class="stack-3" action="search.php">
  <div class="field">
    <label class="label" for="q">Filter by name, description, or file</label>
    <div class="search">
      <svg class="icon"><use href="../assets/deck/deck-icons.svg#search"></use></svg>
      <input class="input" type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="btn, --space, forms.css">
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
    <button class="btn btn-primary" type="submit">Filter</button>
    <a class="btn" href="search.php">Clear</a>
  </div>
</form>

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
      <caption class="sr-only">Classes Deck defines</caption>
      <thead>
        <tr><th scope="col">Class</th><th scope="col">Layer</th><th scope="col">What it does</th><th scope="col">Source</th></tr>
      </thead>
      <tbody>
        <?php foreach ($classes as $c): ?>
          <tr data-name="<?= e($c['name'] . ' ' . $c['file'] . ' ' . (string) $c['doc']) ?>">
            <th scope="row" data-label="Class"><code><?= e('.' . $c['name']) ?></code></th>
            <td data-label="Layer"><code class="dx-dim"><?= e($c['layer'] ?? '—') ?></code></td>
            <td data-label="What it does">
              <?php if ($c['doc']): ?><?= e($c['doc']) ?>
              <?php else: ?><span class="dx-dim"><?= e(docs_summarise($c)) ?></span><?php endif; ?>
            </td>
            <td data-label="Source"><code class="dx-dim"><?= e($c['file'] . ':' . $c['line']) ?></code></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<script>
  /* Enhancement only. The form above already works; this filters the rows that
     are on the page so a match appears without a round trip. */
  (function () {
    var input = document.getElementById('q');
    var table = document.getElementById('dx-table');
    var count = document.getElementById('dx-count');
    if (!input || !table) return;
    var rows = Array.prototype.slice.call(table.tBodies[0].rows);
    var noun = <?= json_encode($kind === 'tokens' ? 'tokens' : 'classes') ?>;
    input.form.addEventListener('submit', function () { /* still allowed */ });
    input.addEventListener('input', function () {
      var q = input.value.trim().toLowerCase();
      var shown = 0;
      rows.forEach(function (r) {
        var hit = !q || r.dataset.name.toLowerCase().indexOf(q) !== -1;
        r.hidden = !hit;
        if (hit) shown++;
      });
      count.textContent = 'Showing ' + shown + ' ' + noun + (q ? ' matching “' + input.value.trim() + '”' : '') + '.';
    });
  })();
</script>

<?php docs_footer(); ?>
