<?php
/**
 * Deck's PHP helper, demonstrated.
 *
 * This page uses no Composer autoloader on purpose, so it runs in a bare
 * Helm or XAMPP site with nothing installed. In a real project you would
 * `composer require echodial/deck` and let the autoloader find the class.
 */

declare(strict_types=1);

require __DIR__ . '/../php/Deck.php';

use EchoDial\Deck\Deck;

Deck::configure([
    'base'     => '/assets/deck',
    'root'     => __DIR__,
    'extras'   => true,
    'adapters' => false,
]);

// Pretend these came from the database
$tenant = ['name' => 'Orchid Isle Ford', 'hue' => 196];
$user   = ['theme' => null];   // null follows the operating system

$claims = [
    ['id' => 88214, 'vehicle' => '2021 F-150 XLT',   'status' => 'good', 'label' => 'Approved', 'total' => 842.16],
    ['id' => 88220, 'vehicle' => '2023 Explorer ST', 'status' => 'warn', 'label' => 'Pending',  'total' => 318.00],
    ['id' => 88109, 'vehicle' => '2020 Escape SE',   'status' => 'bad',  'label' => 'Denied',   'total' => 0.00],
];
?>
<!doctype html>
<html <?= Deck::htmlAttributes(lang: 'en') ?> <?= Deck::theme(hue: $tenant['hue'], mode: $user['theme']) ?>>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($tenant['name']) ?> — claims</title>
<?= Deck::head(viewport: true) ?>
</head>
<body>

<header class="sticky-top">
  <div class="container">
    <nav class="navbar">
      <a class="navbar-brand" href="/">
        <?= Deck::icon('gauge', 'icon icon-lg') ?>
        <?= htmlspecialchars($tenant['name']) ?>
      </a>
      <button class="btn btn-icon btn-ghost push" data-deck-theme aria-label="Switch theme">
        <?= Deck::icon('moon') ?>
      </button>
    </nav>
  </div>
</header>

<main class="container section stack-6">

  <div class="stack-2">
    <h1>Open claims</h1>
    <p class="lede">Rendered by <code>php/Deck.php</code>. The brand hue comes from the
      tenant record, applied as one inline style on the <code>&lt;html&gt;</code> tag —
      no rebuild and no second stylesheet.</p>
  </div>

  <div class="list">
    <?php foreach ($claims as $claim): ?>
      <a class="list-row" href="#claim-<?= $claim['id'] ?>">
        <span class="icon-tile icon-tile-<?= $claim['status'] ?>">
          <?= Deck::icon(match ($claim['status']) {
                'good' => 'check',
                'warn' => 'clock',
                default => 'x',
            }) ?>
        </span>
        <span class="list-main">
          <span class="list-title">Claim <?= $claim['id'] ?></span>
          <span class="list-sub"><?= htmlspecialchars($claim['vehicle']) ?></span>
        </span>
        <span class="badge badge-<?= $claim['status'] ?>"><?= htmlspecialchars($claim['label']) ?></span>
        <span class="list-trail nums">$<?= number_format($claim['total'], 2) ?></span>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <div class="card-body stack-3">
      <h2 class="card-title">What the helper emitted</h2>
      <div class="copy">
        <code class="copy-value"><?= htmlspecialchars(Deck::css()) ?></code>
        <button class="copy-btn" data-deck-copy>
          <span class="copy-idle"><?= Deck::icon('copy', 'icon icon-sm') ?> Copy</span>
          <span class="copy-done"><?= Deck::icon('check', 'icon icon-sm') ?> Copied</span>
        </button>
      </div>
      <p class="text-sm text-muted">The <code>?v=</code> is the stylesheet's mtime, so a
        deploy busts the browser cache and nothing else does.</p>
      <p class="text-sm text-muted">
        Stylesheet in use: <code><?= htmlspecialchars(Deck::stylesheet()) ?></code> ·
        Deck <code><?= Deck::VERSION ?></code>
      </p>
    </div>
  </div>

  <a class="btn" href="/index.php">
    <?= Deck::icon('arrow-left', 'icon icon-sm') ?>
    Back to the component demo
  </a>

</main>

</body>
</html>
