<?php
declare(strict_types=1);

/**
 * The complete class index.
 *
 * Every filter on this page is a query string, applied on the server. That is
 * not a nicety: a CSS framework whose documentation cannot list its own classes
 * without JavaScript is arguing against itself, and 931 rows with no way to
 * narrow them is not a reference, it is a wall.
 *
 * The type-ahead at the bottom is enhancement over the top of a form that
 * already works. It filters the rows the server sent, so it can only ever
 * narrow the current result set — which is the honest behaviour, because the
 * count it prints then matches what is on screen.
 *
 * The "Documented in" column is generated from the pages' own `documents`
 * claims via docs_claims(), so it is the same data tools/docs/verify.mjs uses
 * to decide whether a class is documented. A row that says "not yet" is the
 * backlog admitting itself in the place a reader will actually look.
 */

$page = [
    'path' => 'reference/classes.php',
    'title' => 'All classes',
    'level' => 'Beginner',
    'description' => "Every class Deck defines, filterable by cascade layer, API bucket, name and documentation status, with a link to the page that covers each one.",
];

require __DIR__ . '/../_layout.php';

$q       = trim((string) ($_GET['q'] ?? ''));
$layer   = trim((string) ($_GET['layer'] ?? ''));
$bucket  = trim((string) ($_GET['bucket'] ?? ''));
$state   = trim((string) ($_GET['state'] ?? ''));
$needle  = mb_strtolower($q);

$claims  = docs_claims();
$buckets = ['public', 'internal', 'deprecated'];

/* The filter chain, in the order that discards the most first. Nothing here is
   clever; it runs over 931 rows once. */
$rows = $api['classes'];

if ($layer !== '') {
    $rows = array_filter($rows, static fn(array $c): bool => ($c['layer'] ?? '') === $layer);
}
if ($bucket !== '') {
    $rows = array_filter($rows, static fn(array $c): bool => api_bucket($c['name']) === $bucket);
}
if ($state === 'documented') {
    $rows = array_filter($rows, static fn(array $c): bool => isset($claims[$c['name']]));
} elseif ($state === 'undocumented') {
    $rows = array_filter($rows, static fn(array $c): bool => !isset($claims[$c['name']]));
}
if ($needle !== '') {
    $rows = array_filter($rows, static fn(array $c): bool =>
        str_contains(mb_strtolower($c['name']), $needle)
        || str_contains(mb_strtolower((string) $c['doc']), $needle)
        || str_contains(mb_strtolower($c['file']), $needle));
}
$rows = array_values($rows);

$filtered = $q !== '' || $layer !== '' || $bucket !== '' || $state !== '';
$total = count($api['classes']);
$undocumented = $total - count($claims);
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">All classes</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>All classes</h1>
  <p class="lede">
    Every one of the <?= (int) $total ?> classes Deck defines, read from
    <code>src/*.css</code> by <code>tools/docs/extract.mjs</code>. Filter by cascade
    layer, by API bucket, by name, or by whether a page covers it yet. The filters are
    a plain form, so they work with JavaScript switched off.
  </p>
</header>

<form method="get" class="stack-4" action="classes.php">
  <div class="field">
    <label class="label" for="q">Name, description or source file</label>
    <div class="search">
      <svg class="icon"><use href="../../assets/deck/deck-icons.svg#search"></use></svg>
      <input class="input" type="search" id="q" name="q" value="<?= e($q) ?>"
             placeholder="btn, toast, forms.css" autocomplete="off">
    </div>
    <p class="help">Matches the class name, the comment above it in the source, and the file it lives in.</p>
  </div>

  <div class="field-row">
    <div class="field">
      <label class="label" for="layer">Cascade layer</label>
      <select class="select" id="layer" name="layer">
        <option value="">Every layer</option>
        <?php foreach ($api['layers'] as $l): ?>
          <option value="<?= e($l) ?>"<?= $l === $layer ? ' selected' : '' ?>><?= e($l) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label class="label" for="bucket">API bucket</label>
      <select class="select" id="bucket" name="bucket">
        <option value="">Every bucket</option>
        <?php foreach ($buckets as $b): ?>
          <option value="<?= e($b) ?>"<?= $b === $bucket ? ' selected' : '' ?>><?= e(ucfirst($b)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label class="label" for="state">Documentation</label>
      <select class="select" id="state" name="state">
        <option value="">Documented or not</option>
        <option value="documented"<?= $state === 'documented' ? ' selected' : '' ?>>Has a page</option>
        <option value="undocumented"<?= $state === 'undocumented' ? ' selected' : '' ?>>No page yet</option>
      </select>
    </div>
  </div>

  <div class="form-actions">
    <button class="btn btn-primary" type="submit">Filter</button>
    <?php if ($filtered): ?>
      <a class="btn btn-ghost" href="classes.php">Clear</a>
    <?php endif; ?>
  </div>
</form>

<p class="text-muted" id="dx-count" role="status">
  Showing <?= (int) count($rows) ?> of <?= (int) $total ?> classes<?= $q !== '' ? ' matching “' . e($q) . '”' : '' ?>.
</p>

<?php if ($rows === []): ?>
  <div class="empty">
    <div class="empty-title">Nothing matches those filters</div>
    <p class="text-sm text-muted">Try a shorter name, or clear the layer and bucket.</p>
    <a class="btn btn-sm" href="classes.php">Clear the filters</a>
  </div>
<?php else: ?>
  <div class="table-wrap">
    <table class="table table-stack" id="dx-table">
      <caption class="sr-only">Every class Deck defines, with its layer, bucket and documentation page</caption>
      <thead>
        <tr>
          <th scope="col">Class</th>
          <th scope="col">Layer</th>
          <th scope="col">Bucket</th>
          <th scope="col">What it does</th>
          <th scope="col">Documented in</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $c): ?>
          <?php
          $name = $c['name'];
          $bk = api_bucket($name);
          $what = $c['doc'] ?: docs_summarise($c);
          ?>
          <tr data-name="<?= e($name . ' ' . $c['file'] . ' ' . (string) $c['doc']) ?>">
            <th scope="row" data-label="Class"><code><?= e('.' . $name) ?></code></th>
            <td data-label="Layer"><code class="dx-dim"><?= e($c['layer'] ?? '—') ?></code></td>
            <td data-label="Bucket">
              <?php if ($bk === 'public'): ?>
                <span class="badge badge-good">public</span>
              <?php elseif ($bk === 'deprecated'): ?>
                <span class="badge badge-bad">deprecated</span>
              <?php else: ?>
                <span class="badge"><?= e($bk) ?></span>
              <?php endif; ?>
            </td>
            <td data-label="What it does">
              <?php if ($c['doc']): ?>
                <?= e($c['doc']) ?>
              <?php else: ?>
                <span class="dx-dim"><?= e($what) ?></span>
              <?php endif; ?>
            </td>
            <td data-label="Documented in"><?php docs_home_cell($c, '../'); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<section class="stack-3">
  <h2 id="reading">Reading the columns</h2>
  <dl class="stack-3">
    <dt><strong>Layer</strong></dt>
    <dd>
      The <code>@layer</code> the rule sits in. Layers earlier in Deck's order lose to
      layers later in it, and every one of them loses to your own
      <code>@layer app</code> — which is why overriding Deck never needs
      <code>!important</code>.
    </dd>
    <dt><strong>Bucket</strong></dt>
    <dd>
      <strong>Public</strong> is frozen: it will not change or disappear without a
      major version. <strong>Internal</strong> exists in the stylesheet and is not part
      of the promise — it is usually a hook the JavaScript sets, and it may move.
      <strong>Deprecated</strong> still works and will go.
    </dd>
    <dt><strong>What it does</strong></dt>
    <dd>
      The comment written above the rule in the source where there is one. Where there
      is not, a dimmed summary of the properties the rule sets — honest filler that
      says what the rule does without pretending to say why.
    </dd>
    <dt><strong>Documented in</strong></dt>
    <dd>
      The page that claims the class, or its file and line where none does yet.
      <?= (int) $undocumented ?> classes currently have no page; filtering the last
      select to <em>No page yet</em> lists exactly those.
    </dd>
  </dl>
</section>

<script>
  /* Enhancement only. The form above already filters on the server; this
     narrows the rows that are already here so a match appears without a round
     trip. Everything it does is reachable without it. */
  (function () {
    var input = document.getElementById('q');
    var table = document.getElementById('dx-table');
    var count = document.getElementById('dx-count');
    if (!input || !table || !count) return;

    var rows = Array.prototype.slice.call(table.tBodies[0].rows);
    var total = <?= (int) $total ?>;
    var timer = null;

    function apply() {
      var q = input.value.trim().toLowerCase();
      var shown = 0;
      for (var i = 0; i < rows.length; i++) {
        var hit = !q || rows[i].dataset.name.toLowerCase().indexOf(q) !== -1;
        rows[i].hidden = !hit;
        if (hit) shown++;
      }
      count.textContent = 'Showing ' + shown + ' of ' + total + ' classes'
        + (q ? ' matching “' + input.value.trim() + '”' : '') + '.';
    }

    /* Debounced, because filtering 931 rows on every keystroke is enough work
       to be felt on a phone. */
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(apply, 90);
    });
  })();
</script>

<?php docs_footer(); ?>
