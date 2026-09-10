<?php
/**
 * Shared shell for the documentation site.
 *
 * Every page sets $page before requiring this file:
 *
 *     $page = [
 *         'title'       => 'Button',              // <60 chars with the suffix
 *         'description' => '…',                   // 140-160, unique per page
 *         'section'     => 'components',
 *         'documents'   => ['btn', 'btn-primary'] // classes this page owns
 *     ];
 *     require __DIR__ . '/../_layout.php';
 *
 * Then it echoes its body and calls docs_footer().
 *
 * The docs are built with Deck itself. Classes that belong to the docs
 * chrome rather than to Deck are prefixed `dx-`; tools/docs/verify.mjs treats
 * every other class on the page as a claim that it exists in src/, which is
 * what catches a typo in an example.
 */

declare(strict_types=1);

/* --------------------------------------------------------------------------
   Data
   -------------------------------------------------------------------------- */

$DOCS_ROOT = __DIR__;
$REPO = dirname(__DIR__, 2);

$apiFile = $REPO . '/dist/api.json';
if (!is_readable($apiFile)) {
    http_response_code(500);
    exit('dist/api.json is missing. Run `node tools/docs/extract.mjs`.');
}
$api = json_decode(file_get_contents($apiFile), true, 512, JSON_THROW_ON_ERROR);

$sizesFile = $REPO . '/dist/sizes.json';
$sizes = is_readable($sizesFile)
    ? json_decode(file_get_contents($sizesFile), true, 512, JSON_THROW_ON_ERROR)
    : null;

$DOCS_BASE = rtrim(getenv('DECK_SITE_BASE') ?: 'https://get-keel.dev/deck', '/') . '/docs';

/* --------------------------------------------------------------------------
   Helpers
   -------------------------------------------------------------------------- */

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** Look a class up in the generated inventory. */
function api_class(string $name): ?array
{
    global $api;
    static $index = null;
    if ($index === null) {
        $index = [];
        foreach ($api['classes'] as $c) {
            $index[$c['name']] = $c;
        }
    }
    return $index[$name] ?? null;
}

function api_token(string $name): ?array
{
    global $api;
    static $index = null;
    if ($index === null) {
        $index = [];
        foreach ($api['tokens'] as $t) {
            $index[$t['name']] = $t;
        }
    }
    return $index[$name] ?? null;
}

/**
 * The freeze bucket for a class — public, internal, deprecated — from
 * dist/api-buckets.json. The class reference filters on it, because "does this
 * class exist" and "am I allowed to use it" are different questions and only
 * the second one decides whether you should type it.
 */
function api_bucket(string $name): string
{
    static $index = null;
    if ($index === null) {
        $index = [];
        $file = dirname(__DIR__, 2) . '/dist/api-buckets.json';
        if (is_readable($file)) {
            $bk = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            foreach ($bk['buckets'] as $bucket => $names) {
                foreach ($names as $n) {
                    $index[$n] = $bucket;
                }
            }
        }
    }
    return $index[$name] ?? 'unclassified';
}

/**
 * Which page documents which class, read from the pages' own `documents`
 * claims.
 *
 * This is deliberately the same list tools/docs/verify.mjs reads to decide
 * whether a class is documented. Keeping one source means the class reference
 * cannot link to a page that does not really cover the class, and cannot miss
 * a page that does: the moment a page adds a class to `documents`, the link
 * appears here, and the moment it drops one, the link goes.
 *
 * @return array<string, array{path: string, title: string}>
 */
function docs_claims(): array
{
    static $map = null;
    if ($map !== null) {
        return $map;
    }
    $map = [];
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($files as $file) {
        if ($file->getExtension() !== 'php' || str_starts_with($file->getBasename(), '_')) {
            continue;
        }
        $text = (string) file_get_contents($file->getPathname());
        if (!preg_match("/'documents'\s*=>\s*\[((?:\s*'[A-Za-z0-9_-]+'\s*,?)+)\s*\]/", $text, $claim)) {
            continue;
        }
        preg_match("/'path'\s*=>\s*'([^']*)'/", $text, $pathMatch);
        preg_match("/'title'\s*=>\s*'([^']*)'/", $text, $titleMatch);
        $path = $pathMatch[1] ?? '';
        $title = ($titleMatch[1] ?? '') !== '' ? $titleMatch[1] : $path;
        preg_match_all("/'([^']+)'/", $claim[1], $names);
        foreach ($names[1] as $name) {
            $map[$name] ??= ['path' => $path, 'title' => $title];
        }
    }
    ksort($map);
    return $map;
}

/**
 * Where a class is documented, as a cell: a link to the page that claims it,
 * or its file and line when nothing claims it yet.
 *
 * An honest "nowhere, but here is the source" beats a link to a page that does
 * not mention the class, and it makes the backlog visible in the one place a
 * reader is most likely to notice it.
 */
function docs_home_cell(array $c, string $up): void
{
    $claim = docs_claims()[$c['name']] ?? null;
    if ($claim && $claim['path'] !== '') {
        ?><a href="<?= e($up . $claim['path']) ?>"><?= e($claim['title']) ?></a><?php
        return;
    }
    ?><code class="dx-dim"><?= e($c['file'] . ':' . $c['line']) ?></code><?php
}

/**
 * Render an example once and show its source beside it.
 *
 * The markup is passed in as a string and used twice — rendered into the page
 * and printed as the source — so the two cannot disagree. That is the whole
 * point: a screenshot or a retyped snippet is a second copy that goes stale.
 */
function docs_example(string $html, string $caption = '', string $layout = 'cluster'): void
{
    static $n = 0;
    $n++;
    $id = 'dx-ex-' . $n;
    $source = trim($html);
    ?>
    <figure class="dx-example">
        <?php if ($caption !== ''): ?>
            <figcaption class="dx-example-caption"><?= e($caption) ?></figcaption>
        <?php endif; ?>
        <div class="dx-example-preview <?= $layout === 'cluster' ? 'cluster' : 'stack-3' ?>">
            <?= $source ?>
        </div>
        <div class="dx-example-source">
            <div class="copy">
                <code class="copy-value" id="<?= e($id) ?>"><?= e($source) ?></code>
                <button class="copy-btn" data-deck-copy aria-label="Copy this example">
                    <span class="copy-idle">Copy</span>
                    <span class="copy-done">Copied</span>
                </button>
            </div>
            <pre class="dx-code"><code><?= e($source) ?></code></pre>
        </div>
    </figure>
    <?php
}

/** The class table for a component, generated from dist/api.json. */
function docs_class_table(array $names): void
{
    ?>
    <div class="table-wrap">
        <table class="table table-stack">
            <caption class="sr-only">Classes, generated from the stylesheet source</caption>
            <thead>
                <tr>
                    <th scope="col">Class</th>
                    <th scope="col">Layer</th>
                    <th scope="col">What it does</th>
                    <th scope="col">Source</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($names as $name): $c = api_class($name); ?>
                    <?php if (!$c) { continue; } ?>
                    <tr>
                        <th scope="row" data-label="Class"><code><?= e('.' . $c['name']) ?></code></th>
                        <td data-label="Layer"><code class="dx-dim"><?= e($c['layer'] ?? '—') ?></code></td>
                        <td data-label="What it does">
                            <?php if ($c['doc']): ?>
                                <?= e($c['doc']) ?>
                            <?php else: ?>
                                <span class="dx-dim"><?= e(docs_summarise($c)) ?></span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Source"><code class="dx-dim"><?= e($c['file'] . ':' . $c['line']) ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Substitute one level of var() from the token table.
 *
 * `.p-4` declares `padding: var(--space-4)`, and a reader looking up a spacing
 * utility wants the rem. Resolving it here rather than typing it into the page
 * means the column cannot drift from src/01-tokens.css when a token moves.
 */
function docs_resolve(string $value): string
{
    return (string) preg_replace_callback(
        '/var\(\s*(--[\w-]+)\s*(?:,[^()]*)?\)/',
        static function (array $m): string {
            $t = api_token($m[1]);
            return $t ? $t['value'] : $m[0];
        },
        $value
    );
}

/**
 * The table for a page of utilities: what the class declares, and what that
 * comes out as.
 *
 * docs_class_table() is right for a component, where the useful column is prose
 * about intent. A utility has no intent beyond its declaration, so prose there
 * is padding — the reader is looking up a value, and the honest thing to show
 * is the value.
 *
 * A class with no declarations of its own is not a mistake. `.gap-cq` and the
 * `.cq-md` variants only exist inside a @container block, so the row shows the
 * selector and the condition instead of an empty cell.
 */
function docs_utility_table(array $names, string $caption): void
{
    ?>
    <div class="table-wrap">
        <table class="table table-stack">
            <caption class="sr-only"><?= e($caption) ?></caption>
            <thead>
                <tr>
                    <th scope="col">Class</th>
                    <th scope="col">Declares</th>
                    <th scope="col">Resolves to</th>
                    <th scope="col">Source</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($names as $name): $c = api_class($name); ?>
                    <?php if (!$c) { continue; } ?>
                    <?php
                    /* A class with no rule of its own is documented from the
                       rule that does style it — `.cq :is(.gap-cq)` — with the
                       selector shown, because "where does this apply" is then
                       part of the answer. */
                    $own = $c['declarations'] !== [];
                    $decls = array_map(
                        static fn(array $d): string => $d['prop'] . ': ' . $d['value'],
                        $own ? $c['declarations'] : ($c['contextDeclarations'] ?? [])
                    );
                    $raw = implode('; ', $decls);
                    $resolved = docs_resolve($raw);
                    $context = $own ? null : ($c['contextSelector'] ?? ($c['selectors'][0]['selector'] ?? null));
                    $conditions = $own ? [] : ($c['contextConditions'] ?? []);
                    ?>
                    <tr data-name="<?= e($name . ' ' . $raw . ' ' . $resolved) ?>">
                        <th scope="row" data-label="Class"><code><?= e('.' . $name) ?></code></th>
                        <td data-label="Declares">
                            <?php if ($context !== null): ?>
                                <code class="dx-dim"><?= e($context) ?></code><br>
                            <?php endif; ?>
                            <?php if ($conditions): ?>
                                <code class="dx-dim"><?= e(implode(' ', $conditions)) ?></code><br>
                            <?php endif; ?>
                            <?php if ($raw !== ''): ?>
                                <code class="dx-dim"><?= e($raw) ?></code>
                            <?php elseif ($context === null): ?>
                                <code class="dx-dim"><?= e('.' . $name) ?></code>
                            <?php endif; ?>
                        </td>
                        <td data-label="Resolves to">
                            <?php if ($raw !== '' && $resolved !== $raw): ?>
                                <code><?= e($resolved) ?></code>
                            <?php else: ?>
                                <span class="dx-dim">—</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Source"><code class="dx-dim"><?= e($c['file'] . ':' . $c['line']) ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/** A one-line description built from the declarations, for classes with no
 *  comment in the source. Honest filler: it says what the rule sets. */
function docs_summarise(array $c): string
{
    $props = array_map(static fn(array $d): string => $d['prop'], $c['declarations']);
    $props = array_values(array_filter($props, static fn(string $p): bool => !str_starts_with($p, '--')));
    if (!$props) {
        $custom = array_map(static fn(array $d): string => $d['prop'], $c['declarations']);
        return $custom ? 'Sets ' . implode(', ', array_slice($custom, 0, 3)) . '.' : 'No declarations.';
    }
    $shown = array_slice($props, 0, 4);
    $more = count($props) - count($shown);
    return 'Sets ' . implode(', ', $shown) . ($more > 0 ? ", and {$more} more" : '') . '.';
}

/**
 * What kind of thing a token is, decided from its value rather than its name.
 *
 * The generated `kind` field guesses from the name, and the name lies: it reads
 * `--text-sm` as a colour because the word "text" is in it, so 23 component
 * pages have been rendering `background: .8125rem` — an empty box where a
 * swatch should be — and `--hue-brand` has been doing the same with a bare 196.
 * A value cannot lie about its own shape, so this asks the value.
 */
function docs_token_kind(array $t): string
{
    $name = (string) $t['name'];
    $v = trim((string) ($t['value'] ?? ''));
    if ($v === '') {
        return 'other';
    }
    /* An alias forwards to whatever it points at: --shadow-color is a hue
       number, not a shadow, whatever its name suggests. */
    if (preg_match('/^var\(\s*(--[\w-]+)\s*\)$/', $v, $m)) {
        $ref = api_token($m[1]);
        if ($ref && $ref['name'] !== $name) {
            return docs_token_kind(['name' => $name, 'value' => $ref['value']]);
        }
    }
    if ($v[0] === '#' || preg_match('/^(oklch|oklab|rgba?|hsla?|hwb|color|light-dark|color-mix)\(/i', $v)) {
        return 'colour';
    }
    if (preg_match('/^(linear|radial|conic)-gradient\(/i', $v)) {
        return 'gradient';
    }
    if (preg_match('/^(cubic-bezier|linear|steps)\(/', $v)) {
        return 'easing';
    }
    if (preg_match('/^-?[\d.]+m?s$/', $v)) {
        return 'duration';
    }
    if ($v === '0' || preg_match('/^-?[\d.]+(rem|px|ch|em|ex|vw|vh|dvh|svh|lvh|%)$/', $v)
        || preg_match('/^(clamp|calc|min|max)\(/', $v)) {
        return 'length';
    }
    if (preg_match('/^-?[\d.]+$/', $v)) {
        return str_starts_with($name, '--hue-') ? 'hue' : 'number';
    }
    if (str_starts_with($name, '--font-')) {
        return 'font';
    }
    if (str_starts_with($name, '--shadow-') || $name === '--ring') {
        return 'shadow';
    }
    return 'other';
}

/**
 * A live preview of a token: the token itself applied to something, never a
 * picture of it. If the value in the stylesheet changes, the cell changes.
 */
function docs_token_preview(array $t): void
{
    $name = $t['name'];
    switch (docs_token_kind($t)) {
        case 'colour':
        case 'gradient':
            ?><span class="dx-swatch" style="background: var(<?= e($name) ?>)"></span><?php
            break;
        case 'hue':
            /* The number is not a colour, but it is the hue of one, so build a
               swatch from it the way the palette does. */
            ?><span class="dx-swatch" style="background: oklch(62% .14 var(<?= e($name) ?>))"></span><?php
            break;
        case 'length':
            ?><span class="dx-rule" style="inline-size: var(<?= e($name) ?>)"></span><?php
            break;
        case 'shadow':
            ?><span class="dx-shadow" style="box-shadow: var(<?= e($name) ?>)"></span><?php
            break;
        case 'font':
            ?><span class="dx-font" style="font-family: var(<?= e($name) ?>)">Ag 0123</span><?php
            break;
        case 'duration':
            ?><span class="dx-motion"><span class="dx-motion-dot" style="animation-duration: var(<?= e($name) ?>)"></span></span><?php
            break;
        case 'easing':
            ?><span class="dx-motion"><span class="dx-motion-dot" style="animation-timing-function: var(<?= e($name) ?>)"></span></span><?php
            break;
        default:
            ?><span class="dx-dim">—</span><?php
    }
}

/** Token rows with a live swatch, so the value shown is the value in effect. */
function docs_token_table(array $names, bool $source = false): void
{
    ?>
    <div class="table-wrap">
        <table class="table table-stack">
            <caption class="sr-only">Design tokens this component reads</caption>
            <thead>
                <tr>
                    <th scope="col">Token</th>
                    <th scope="col">Live</th>
                    <th scope="col">Declared value</th>
                    <?php if ($source): ?><th scope="col">Declared in</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($names as $name): $t = api_token($name); ?>
                    <tr>
                        <th scope="row" data-label="Token"><code><?= e($name) ?></code></th>
                        <td data-label="Live">
                            <?php if ($t): docs_token_preview($t); else: ?>
                                <span class="dx-dim">not in the inventory</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Declared value"><code class="dx-dim"><?= e($t['value'] ?? 'inherited') ?></code></td>
                        <?php if ($source): ?>
                            <td data-label="Declared in"><code class="dx-dim"><?= e($t ? $t['file'] . ':' . $t['line'] : '—') ?></code></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/* --------------------------------------------------------------------------
   Navigation. Pages that are not written yet render as muted text rather than
   as links, so the nav is a map of the whole site without any 404s in it.
   -------------------------------------------------------------------------- */

$DOCS_NAV = [
    'Start' => [
        ['start/install.php', 'Install Deck', true],
        ['start/first-page.php', 'Your first page', true],
    ],
    /* Guides are named for the task, not the feature, because that is what a
       person types into a search engine. "Theming" is what we call it; "change
       your brand colour" is what they came to do. */
    'Guides' => [
        ['guides/theming.php', 'Change your brand colour', true],
        ['guides/layout.php', 'Stop writing margins', true],
        ['guides/layers.php', 'Override without !important', true],
        ['guides/dark-mode.php', 'Add dark mode', true],
        ['guides/forms.php', 'Forms that validate', true],
        ['guides/rtl.php', 'Right-to-left languages', true],
        ['guides/motion.php', 'Animate without a library', true],
        ['guides/printing.php', 'Print properly', true],
        ['guides/migrating-from-tailwind.php', 'Move from Tailwind', true],
    ],
    'Components' => [
        ['components/3d.php', '3D space', true],
        ['components/accordion.php', 'Accordion', true],
        ['components/alert.php', 'Alert', true],
        ['components/avatar.php', 'Avatar', true],
        ['components/badge.php', 'Badge', true],
        ['components/bar.php', 'Bar', true],
        ['components/breadcrumb.php', 'Breadcrumb', true],
        ['components/button.php', 'Button', true],
        ['components/card.php', 'Card', true],
        ['components/carousel.php', 'Carousel', true],
        ['components/charts.php', 'Charts', true],
        ['components/chat.php', 'Chat', true],
        ['components/check.php', 'Checkbox and radio', true],
        ['components/cluster.php', 'Cluster', true],
        ['components/combobox.php', 'Combobox', true],
        ['components/container.php', 'Container', true],
        ['components/copy.php', 'Copy to clipboard', true],
        ['components/datagrid.php', 'Data grid', true],
        ['components/datepicker.php', 'Date picker', true],
        ['components/drawer.php', 'Drawer', true],
        ['components/editor.php', 'Editor', true],
        ['components/emoji.php', 'Emoji', true],
        ['components/field.php', 'Field', true],
        ['components/fieldset.php', 'Fieldset', true],
        ['components/file.php', 'File', true],
        ['components/float.php', 'Floating label', true],
        ['components/footer.php', 'Footer', true],
        ['components/gallery.php', 'Gallery', true],
        ['components/gradients.php', 'Gradients', true],
        ['components/grid.php', 'Grid', true],
        ['components/icon.php', 'Icon', true],
        ['components/indicator.php', 'Indicator', true],
        ['components/input.php', 'Input', true],
        ['components/kanban.php', 'Kanban', true],
        ['components/list.php', 'List', true],
        ['components/menu.php', 'Menu', true],
        ['components/modal.php', 'Modal', true],
        ['components/motion.php', 'Motion', true],
        ['components/nav.php', 'Nav bar', true],
        ['components/number.php', 'Number input', true],
        ['components/pagination.php', 'Pagination', true],
        ['components/phone.php', 'Phone input', true],
        ['components/popover.php', 'Popover', true],
        ['components/print.php', 'Printing', true],
        ['components/progress.php', 'Progress', true],
        ['components/qr.php', 'QR code', true],
        ['components/range.php', 'Range', true],
        ['components/rating.php', 'Rating', true],
        ['components/scroller.php', 'Scroller', true],
        ['components/section.php', 'Section', true],
        ['components/segmented.php', 'Segmented', true],
        ['components/select.php', 'Select', true],
        ['components/sheet.php', 'Sheet', true],
        ['components/sidebar.php', 'Sidebar', true],
        ['components/skeleton.php', 'Skeleton', true],
        ['components/split.php', 'Split', true],
        ['components/stack.php', 'Stack', true],
        ['components/stepper.php', 'Stepper', true],
        ['components/switch.php', 'Switch', true],
        ['components/table.php', 'Table', true],
        ['components/tabs.php', 'Tabs', true],
        ['components/textarea.php', 'Textarea', true],
        ['components/timeline.php', 'Timeline', true],
        ['components/toast.php', 'Toast', true],
        ['components/tooltip.php', 'Tooltip', true],
        ['components/video.php', 'Video', true],
    ],
    'Reference' => [
        ['reference/classes.php', 'All classes', true],
        ['reference/tokens.php', 'All tokens', true],
        ['reference/spacing.php', 'Spacing', true],
        ['reference/typography.php', 'Typography', true],
        ['reference/display.php', 'Display and sizing', true],
        ['reference/position.php', 'Position', true],
        ['reference/color.php', 'Colour and surface', true],
        ['reference/javascript.php', 'JavaScript API', true],
        ['reference/php.php', 'PHP helper', true],
        ['reference/cli.php', 'CLI', true],
    ],
    'Explanation' => [
        ['explain/why-no-build-step.php', 'Why no build step', true],
        ['explain/why-cascade-layers.php', 'Why cascade layers', true],
        ['explain/why-one-hue.php', 'Why one hue', true],
    ],
];

$here = $page['path'] ?? '';
$depth = substr_count($here, '/');
$up = str_repeat('../', $depth);
$assets = $up . '../assets';

$title = ($page['title'] ?? 'Documentation') . ' — Deck';
$canonical = $DOCS_BASE . '/' . ($here ?: 'index.php');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title) ?></title>
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="description" content="<?= e($page['description'] ?? '') ?>">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
<link rel="icon" href="<?= e($assets) ?>/images/deck-mark.svg" type="image/svg+xml">

<meta property="og:type" content="article">
<meta property="og:site_name" content="Deck documentation">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($page['description'] ?? '') ?>">
<meta property="og:image" content="<?= e($DOCS_BASE) ?>/../assets/images/deck-og.png">
<meta name="twitter:card" content="summary_large_image">

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'TechArticle',
    'headline' => $page['title'] ?? 'Documentation',
    'description' => $page['description'] ?? '',
    'url' => $canonical,
    'inLanguage' => 'en',
    'isPartOf' => [
        '@type' => 'TechArticle',
        'name' => 'Deck documentation',
        'url' => $DOCS_BASE . '/',
    ],
    'about' => [
        '@type' => 'SoftwareSourceCode',
        'name' => 'Deck',
        'programmingLanguage' => ['@type' => 'ComputerLanguage', 'name' => 'CSS'],
        'codeRepository' => 'https://github.com/srivera145/deck',
    ],
    'author' => ['@type' => 'Person', 'name' => 'Santos Rivera'],
    'proficiencyLevel' => $page['level'] ?? 'Beginner',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

<script>
  /* Apply a saved theme before first paint. deck.js restores it too, but it is
     deferred, and a long page can paint before a deferred script runs. */
  try { var dxTheme = localStorage.getItem('deck-theme'); if (dxTheme) document.documentElement.setAttribute('data-theme', dxTheme); } catch (e) {}
</script>
<link rel="stylesheet" href="<?= e($assets) ?>/deck/deck.css">
<script src="<?= e($assets) ?>/deck/deck.js" defer></script>
<script src="<?= e($assets) ?>/deck/deck-extras.js" defer></script>
<style>
  /* Docs chrome only. Everything else on this page is Deck.
     These are all `dx-` prefixed so the verifier can tell them apart from
     classes that are supposed to exist in src/. */
  @layer app.pages {
    .dx-shell { display: grid; grid-template-columns: 1fr; gap: var(--space-8); }
    /* A grid item's min-inline-size is auto, so a wide code block or a long
       table would push the whole track past the viewport and drag the sidebar
       out with it. Both children have to be allowed to shrink. */
    .dx-shell > * { min-inline-size: 0; }
    @media (min-width: 60rem) {
      .dx-shell { grid-template-columns: 16rem minmax(0, 1fr); align-items: start; }
      .dx-side { position: sticky; inset-block-start: 4.5rem; max-block-size: calc(100dvh - 6rem); overflow-y: auto; }
    }
    /* On a phone the nav sits above the content, and twenty entries would push
       the first heading a screen and a half down. Bound it and let it scroll on
       its own instead. No JavaScript involved, so it survives with JS off. */
    @media (max-width: 59.99rem) {
      .dx-side { max-block-size: 40dvh; overflow-y: auto; border: 1px solid var(--line); border-radius: var(--r-md); }
    }
    .dx-side details { border: 0; }
    .dx-side summary { font-size: var(--text-xs); font-weight: 620; color: var(--text-faint); text-transform: uppercase; letter-spacing: .06em; padding-block: var(--space-2); cursor: pointer; }
    .dx-soon { color: var(--text-faint); padding: var(--space-2) var(--space-3); display: block; font-size: var(--text-sm); }
    .dx-soon::after { content: " soon"; font-size: var(--text-xs); opacity: .7; }

    .dx-example { margin: 0; border: 1px solid var(--line); border-radius: var(--r-md); overflow: hidden; }
    .dx-example-caption { padding: var(--space-3) var(--space-4); font-size: var(--text-sm); color: var(--text-muted); border-block-end: 1px solid var(--line); background: var(--surface); }
    .dx-example-preview { padding: var(--space-5); background: var(--bg); }
    .dx-example-source { border-block-start: 1px solid var(--line); background: var(--surface); }
    .dx-example-source .copy { border: 0; border-radius: 0; border-block-end: 1px solid var(--line); }
    .dx-example-source .copy-value { display: none; }
    .dx-code { margin: 0; border-radius: 0; max-block-size: 22rem; }

    .dx-swatch { display: inline-block; inline-size: 2.5rem; block-size: 1.25rem; border-radius: var(--r-xs); border: 1px solid var(--line); vertical-align: middle; }
    .dx-rule { display: inline-block; block-size: .5rem; background: var(--brand); border-radius: var(--r-full); vertical-align: middle; max-inline-size: 100%; }
    .dx-shadow { display: inline-block; inline-size: 2.5rem; block-size: 1.25rem; border-radius: var(--r-xs); background: var(--surface); vertical-align: middle; }
    .dx-font { font-size: var(--text-md); }
    /* A duration and an easing curve are only legible in motion, so the cell
       animates with the token itself rather than describing it. Held still for
       anyone who asked their system to calm things down. */
    .dx-motion { display: inline-block; inline-size: 3.5rem; block-size: .75rem; background: var(--bg-sunken); border-radius: var(--r-full); position: relative; overflow: hidden; vertical-align: middle; }
    .dx-motion-dot { position: absolute; inset-block-start: .125rem; inline-size: .5rem; block-size: .5rem; border-radius: var(--r-full); background: var(--brand); animation: dx-slide 1.2s var(--ease-out) infinite alternate; }
    @keyframes dx-slide { from { inset-inline-start: .125rem; } to { inset-inline-start: 2.875rem; } }
    @media (prefers-reduced-motion: reduce) { .dx-motion-dot { animation: none; inset-inline-start: .125rem; } }
    .dx-dim { color: var(--text-faint); font-size: var(--text-xs); }
    .dx-toc { font-size: var(--text-sm); }
    .dx-note { border-inline-start: 3px solid var(--brand); padding-inline-start: var(--space-4); }

    /* The theme switch shows where a press will take you: a moon on a light
       page, a sun on a dark one. With nothing saved there is no data-theme
       attribute and the OS decides, so the media queries cover that case and
       the icon is right before the first press. Only hiding rules, so the
       visible icon keeps whatever display .icon gives it. */
    :root[data-theme="light"] .dx-theme .dx-sun,
    :root[data-theme="dark"] .dx-theme .dx-moon { display: none; }
    @media (prefers-color-scheme: light) { :root:not([data-theme]) .dx-theme .dx-sun { display: none; } }
    @media (prefers-color-scheme: dark) { :root:not([data-theme]) .dx-theme .dx-moon { display: none; } }
  }
</style>
</head>
<body>
<a class="skip-link" href="#dx-main">Skip to content</a>

<header class="sticky-top">
  <div class="container">
    <nav class="navbar" aria-label="Documentation">
      <a class="navbar-brand" href="<?= e($up) ?>index.php">
        <svg class="icon icon-lg icon-fill" style="color:var(--brand)"><use href="<?= e($assets) ?>/deck/deck-icons.svg#deck-mark"></use></svg>
        Deck <span class="badge">docs</span>
      </a>
      <form class="push" role="search" action="<?= e($up) ?>search.php" method="get">
        <div class="search">
          <svg class="icon"><use href="<?= e($assets) ?>/deck/deck-icons.svg#search"></use></svg>
          <label class="sr-only" for="dx-q">Search the class reference</label>
          <input class="input" type="search" id="dx-q" name="q" placeholder="Search <?= (int) $api['counts']['classes'] ?> classes" value="<?= e($_GET['q'] ?? '') ?>">
        </div>
      </form>
      <a class="btn btn-sm btn-ghost" href="<?= e($up) ?>../index.php#demo">Demo</a>
      <button class="btn btn-icon btn-ghost dx-theme" type="button" data-deck-theme aria-label="Switch between light and dark theme">
        <svg class="icon dx-moon" aria-hidden="true"><use href="<?= e($assets) ?>/deck/deck-icons.svg#moon"></use></svg>
        <svg class="icon dx-sun" aria-hidden="true"><use href="<?= e($assets) ?>/deck/deck-icons.svg#sun"></use></svg>
      </button>
    </nav>
  </div>
</header>

<div class="container section">
  <div class="dx-shell">
    <aside class="dx-side" aria-label="Documentation sections">
      <nav class="panel sidebar">
        <?php foreach ($DOCS_NAV as $group => $items): ?>
          <span class="sidebar-group"><?= e($group) ?></span>
          <?php foreach ($items as [$path, $label, $written]): ?>
            <?php if ($written): ?>
              <a class="sidebar-link" href="<?= e($up . $path) ?>"<?= $path === $here ? ' aria-current="page"' : '' ?>>
                <span><?= e($label) ?></span>
              </a>
            <?php else: ?>
              <span class="dx-soon"><?= e($label) ?></span>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </nav>
    </aside>

    <main id="dx-main" class="stack-8">
<?php
/* ------------------------------------------------------------------------- */
function docs_footer(): void
{
    global $up, $api, $sizes;
    ?>
      <hr>
      <footer class="stack-3 text-muted">
        <p class="text-sm">
          Generated against <?= (int) $api['counts']['classes'] ?> classes and
          <?= (int) $api['counts']['tokens'] ?> tokens in
          <?= (int) $api['stylesheets'] ?> stylesheets.
          <?php if ($sizes): ?>
            Deck is <?= e(number_format($sizes['files']['deck.min.css']['brotli'] / 1000, 1)) ?> KB Brotli.
          <?php endif; ?>
        </p>
        <p class="text-sm">
          Every class table on this site is built from the stylesheet source by
          <code>tools/docs/extract.mjs</code>, and
          <code>tools/docs/verify.mjs</code> fails the build if a page and the
          source disagree.
        </p>
      </footer>
    </main>
  </div>
</div>
</body>
</html>
    <?php
}
