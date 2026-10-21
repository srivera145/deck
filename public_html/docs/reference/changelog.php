<?php
declare(strict_types=1);

/**
 * The changelog, rendered from CHANGELOG.md at the repository root.
 *
 * CHANGELOG.md is the one copy. This page reads it on each request rather than
 * keeping a second one, so a release that updates the file updates the page.
 * The file uses headings, paragraphs and one-line bullets, with **bold**,
 * *emphasis* and `code` inside them, and the renderer below handles exactly
 * that, so the page needs no Markdown library. A line it does not recognise is
 * printed as a paragraph of plain text, never as markup.
 *
 * The file has to be deployed beside public_html/, like dist/ and package.json.
 * Where it is not, the page says so and links to the copy on GitHub.
 */

$page = [
    'path' => 'reference/changelog.php',
    'title' => 'Changelog',
    'level' => 'Beginner',
    'description' => 'What changed in each Deck release: breaking changes first, then fixes, additions and changes, each with what to do if a page relied on the old behaviour.',
];

require __DIR__ . '/../_layout.php';

$changelogFile = $REPO . '/CHANGELOG.md';
$changelogLines = is_readable($changelogFile)
    ? file($changelogFile, FILE_IGNORE_NEW_LINES) ?: []
    : null;

/* Inline Markdown: code spans are set aside first, so a `*` inside one is
   never read as emphasis, and everything else is escaped before any tag is
   added. */
$inline = static function (string $text): string {
    $codes = [];
    $text = (string) preg_replace_callback('/`([^`]+)`/', static function (array $m) use (&$codes): string {
        $codes[] = '<code>' . e($m[1]) . '</code>';
        return "\x01" . (count($codes) - 1) . "\x01";
    }, $text);
    $text = e($text);
    $text = (string) preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = (string) preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);
    return (string) preg_replace_callback('/\x01(\d+)\x01/', static fn(array $m): string => $codes[(int) $m[1]], $text);
};

/* "0.2.0 (2026-10-21)" -> "v0-2-0", so a release can be linked to. */
$slug = static fn(string $heading): string =>
    'v' . trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower(strtok($heading, ' ') ?: $heading)), '-');
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">Changelog</li>
  </ol>
</nav>

<header class="stack stack-3">
  <h1>Changelog</h1>
  <p class="lede">
    Deck <?= e($SITE['version']) ?> is the current release. In 0.x a breaking change moves
    the minor version, so a CDN URL pinned to <code>@0.1</code> or a <code>^0.1</code>
    range stays on 0.1.x until you move it.
  </p>
</header>

<?php if ($changelogLines === null): ?>
  <div class="alert alert-warn">
    <svg class="icon"><use href="<?= e($assets) ?>/deck/deck-icons.svg#info"></use></svg>
    <div>
      <div class="alert-title">CHANGELOG.md is not on this server</div>
      <p class="alert-body">
        Read it in the repository instead:
        <a href="https://github.com/srivera145/deck/blob/main/CHANGELOG.md">CHANGELOG.md on GitHub</a>.
      </p>
    </div>
  </div>
<?php else: ?>
  <?php
  $open = null;       // 'ul' or 'p' while one is being written
  $section = null;    // the release slug, so h3 ids stay unique per release
  $close = static function () use (&$open): void {
      if ($open === 'ul') {
          echo "</ul>\n";
      } elseif ($open === 'p') {
          echo "</p>\n";
      }
      $open = null;
  };
  foreach ($changelogLines as $line) {
      if (trim($line) === '') {
          $close();
          continue;
      }
      if (preg_match('/^(#{1,3})\s+(.*)$/', $line, $h)) {
          $close();
          $level = strlen($h[1]);
          if ($level === 1) {
              continue;  // "# Changelog" is the page's own <h1>.
          }
          if ($level === 2) {
              if ($section !== null) {
                  echo "</section>\n";
              }
              $section = $slug($h[2]);
              echo '<section class="stack stack-4">' . "\n";
              echo '<h2 id="' . e($section) . '">' . $inline($h[2]) . "</h2>\n";
          } else {
              $id = ($section ?? 'v') . '-' . trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($h[2])), '-');
              echo '<h3 id="' . e($id) . '">' . $inline($h[2]) . "</h3>\n";
          }
          continue;
      }
      if (str_starts_with($line, '- ')) {
          if ($open !== 'ul') {
              $close();
              echo '<ul class="stack stack-2">' . "\n";
              $open = 'ul';
          }
          echo '<li>' . $inline(substr($line, 2)) . "</li>\n";
          continue;
      }
      if ($open === 'p') {
          echo ' ' . $inline(trim($line));
          continue;
      }
      $close();
      echo '<p>' . $inline(trim($line));
      $open = 'p';
  }
  $close();
  if ($section !== null) {
      echo "</section>\n";
  }
  ?>
  <p class="text-sm text-muted">
    Rendered from <code>CHANGELOG.md</code> on each request.
    <a href="https://github.com/srivera145/deck/releases">Releases on GitHub</a> carry the
    editor extension's <code>.vsix</code>.
  </p>
<?php endif; ?>

<?php docs_footer(); ?>
