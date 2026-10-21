<?php
/**
 * The documentation's data and helpers, with no output.
 *
 * _layout.php requires this and then prints the page chrome. A page that has
 * to decide something before any HTML goes out requires this first on its own:
 * search.php, which can redirect straight to the page a search names, and
 * components/index.php, which can answer in JSON. _layout.php then picks up
 * the same functions through require_once.
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

/* Measured file sizes, written by build.mjs. Pages read them through
   docs_kb(). Not called $sizes: component pages use that name for their own
   lists of sizes, and assigning it after requiring this file used to replace
   the measurements before the footer printed them. */
$sizesFile = $REPO . '/dist/sizes.json';
$DOCS_SIZES = is_readable($sizesFile)
    ? json_decode(file_get_contents($sizesFile), true, 512, JSON_THROW_ON_ERROR)
    : null;

/* Base URL and version, shared with the demo page and with the sitemap that
   build.mjs writes. See public_html/_site.php. */
$SITE = require dirname(__DIR__) . '/_site.php';
$DOCS_BASE = $SITE['base'] . '/docs';

/* --------------------------------------------------------------------------
   Helpers
   -------------------------------------------------------------------------- */

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** A measured size from dist/sizes.json, as the build prints it: "26.8 KB". */
function docs_kb(string $file, string $encoding = 'brotli'): string
{
    global $DOCS_SIZES;
    $bytes = $DOCS_SIZES['files'][$file][$encoding] ?? null;

    return $bytes === null ? '?' : number_format($bytes / 1000, 1) . ' KB';
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
 * Every docs page, as its own $page block describes it, keyed by path.
 *
 * Read from the files rather than listed. A list typed by hand is how the docs
 * home came to say "47 pages" when there were 66, and how the component list
 * llms.txt points to came to leave out the date picker: a page that exists is
 * in here, with the title, description and headings it actually has.
 *
 * @return array<string, array{path: string, title: string, description: string,
 *     section: string, component: ?string, documents: list<string>,
 *     headings: list<array{id: string, text: string, key: string, words: array<string, int>}>}>
 */
function docs_pages(): array
{
    static $pages = null;
    if ($pages !== null) {
        return $pages;
    }
    $pages = [];
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($files as $file) {
        if ($file->getExtension() !== 'php' || str_starts_with($file->getBasename(), '_')) {
            continue;
        }
        $text = (string) file_get_contents($file->getPathname());
        if (!preg_match('/^\$page = \[(.*?)^\];/ms', $text, $block)) {
            continue;
        }
        $path = docs_page_field($block[1], 'path') ?? '';
        $documents = [];
        if (preg_match("/'documents'\s*=>\s*\[((?:\s*'[A-Za-z0-9_-]+'\s*,?)+)\s*\]/", $text, $claim)) {
            preg_match_all("/'([^']+)'/", $claim[1], $names);
            $documents = $names[1];
        }
        /* A heading with an id is somewhere a search can land. Its text can
           hold PHP and markup; what a reader sees is what is indexed. */
        $headings = [];
        preg_match_all('/<h([2-4])\b[^>]*\bid="([^"<]+)"[^>]*>(.*?)<\/h\1>/s', $text, $found, PREG_SET_ORDER);
        foreach ($found as $h) {
            $label = preg_replace('/<\?(?:php|=).*?\?>/s', '', $h[3]);
            $label = trim(html_entity_decode(strip_tags((string) $label), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if ($label !== '') {
                $headings[] = ['id' => $h[2], 'text' => $label, 'key' => docs_key($label), 'words' => array_flip(docs_words($label))];
            }
        }
        $pages[$path] = [
            'path' => $path,
            'title' => docs_page_field($block[1], 'title') ?? $path,
            'description' => docs_page_field($block[1], 'description') ?? '',
            'section' => str_contains($path, '/') ? strstr($path, '/', true) : '',
            'component' => docs_page_field($block[1], 'component'),
            'documents' => $documents,
            'headings' => $headings,
            /* Whether the page prints its classes through docs_class_table()
               or docs_utility_table(), whose rows carry docs_row_id(). */
            'tables' => preg_match('/\bdocs_(?:class|utility)_table\(/', $text) === 1,
        ];
    }
    ksort($pages);
    return $pages;
}

/** One string from a $page block, single- or double-quoted, unescaped. */
function docs_page_field(string $block, string $key): ?string
{
    if (!preg_match("/'" . preg_quote($key, '/') . "'\s*=>\s*(?:'((?:[^'\\\\]|\\\\.)*)'|\"((?:[^\"\\\\]|\\\\.)*)\")/s", $block, $m)) {
        return null;
    }
    return isset($m[2]) ? stripcslashes($m[2]) : strtr($m[1], ["\\'" => "'", '\\\\' => '\\']);
}

/**
 * Which page documents which class, read from the pages' own `documents`
 * claims.
 *
 * This is deliberately the same list tools/docs/verify.mjs reads to decide
 * whether a class is documented. Keeping one source means the class reference
 * cannot link to a page that does not really cover the class, and cannot miss
 * a page that does: the moment a page adds a class to `documents`, the link
 * appears here, and the moment it drops one, the link goes. A class two pages
 * claim links to the first by path.
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
    foreach (docs_pages() as $p) {
        foreach ($p['documents'] as $name) {
            $map[$name] ??= ['path' => $p['path'], 'title' => $p['title'] !== '' ? $p['title'] : $p['path']];
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
        <div class="dx-example-preview <?= $layout === 'cluster' ? 'cluster' : 'stack stack-3' ?>">
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

/**
 * The id a class's row carries in a class table, so a search or the component
 * index can link to that row: `charts.php#class-donut`. Once per page, so a
 * class listed in two tables keeps its first row and the ids stay unique.
 */
function docs_row_id(string $name): string
{
    static $seen = [];
    if (isset($seen[$name])) {
        return '';
    }
    $seen[$name] = true;
    return ' id="class-' . e($name) . '"';
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
                    <tr<?= docs_row_id($name) ?>>
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
                    <tr<?= docs_row_id($name) ?> data-name="<?= e($name . ' ' . $raw . ' ' . $resolved) ?>">
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
            /* A radius is a corner, not a distance, so draw the corner. As a
               width, --r-full is a 999px bar. Every radius token is --r-*. */
            if (str_starts_with($name, '--r-')) {
                ?><span class="dx-radius" style="border-radius: var(<?= e($name) ?>)"></span><?php
                break;
            }
            /* A blur is not a distance either: blur the right half of some
               stripes with it, the way the components use it. */
            if (str_starts_with($name, '--blur-')) {
                ?><span class="dx-blur"><span style="backdrop-filter: blur(var(<?= e($name) ?>)); -webkit-backdrop-filter: blur(var(<?= e($name) ?>))"></span></span><?php
                break;
            }
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
   Finding things. "Does Deck have a date picker?" has one answer, and the
   search box, the component index and the JSON an assistant reads all give
   it, because they share this index and this matcher.

   The old search matched raw substrings of class names, source comments and
   file names, and nothing else. So "date picker" missed .datepicker for the
   space, "upload" found nothing because the page was called File, "ring"
   listed .alert-body first for the "ring" in "during", and a hit was a file
   and line rather than a link. Pages are what people look for, so pages are
   what this ranks, by their titles, headings, descriptions and the classes
   they claim; keys and words handle the spelling, and the ranking does the
   rest.
   -------------------------------------------------------------------------- */

/** Words that phrase a question without saying what it is about: "does Deck
 *  have a date picker" is a search for "date picker". */
const DOCS_STOPWORDS = [
    'a', 'an', 'and', 'any', 'are', 'can', 'deck', 'do', 'does', 'for', 'get', 'has', 'have',
    'how', 'i', 'in', 'is', 'it', 'make', 'my', 'of', 'on', 'or', 'the', 'there', 'to', 'use',
    'what', 'where', 'which', 'with',
];

/** A word made singular enough to compare, so "donuts" finds .donut. Both
 *  sides of every comparison go through it, which is why turning "status"
 *  into "statu" does no harm. */
function docs_stem(string $w): string
{
    return strlen($w) > 3 && $w[-1] === 's' && $w[-2] !== 's' ? substr($w, 0, -1) : $w;
}

/**
 * The words in a phrase, lowercased and stemmed. With $stop, the words that
 * only phrase a question are dropped first.
 *
 * @return list<string>
 */
function docs_words(string $s, bool $stop = false): array
{
    preg_match_all('/[a-z0-9]+/', strtolower($s), $m);
    $words = $stop ? array_diff($m[0], DOCS_STOPWORDS) : $m[0];
    return array_values(array_map('docs_stem', $words));
}

/** A name with its spacing taken out, so "Date picker", "date-picker" and
 *  ".datepicker" are one key, and so are "QR codes" and "qr-code". */
function docs_key(string $s): string
{
    return implode('', docs_words($s));
}

/**
 * A query, three ways: every word as one key, the words that matter as one
 * key, and the words that matter on their own.
 *
 * @return array{full: string, core: string, words: list<string>}
 */
function docs_query(string $q): array
{
    $all = docs_words($q);
    $core = docs_words($q, true) ?: $all;
    return ['full' => implode('', $all), 'core' => implode('', $core), 'words' => $core];
}

/**
 * Every public class, under the page that documents it, then the public
 * classes no page claims yet, grouped by the component dist/api.json puts
 * them in. components/index.php renders this, and returns it as JSON.
 *
 * Built from the pages' `documents` claims, which tools/docs/verify.mjs
 * checks against dist/api.json on every build, and from dist/api.json for
 * whatever no page claims. So a public class cannot ship and be missing: it is
 * under a page or under "No page yet". Public, because that is the set Deck
 * promises; internal classes are hooks deck.js sets, and the class reference
 * lists them by bucket.
 *
 * @return array{public: int, pages: list<array{path: string, title: string,
 *     description: string, section: string, classes: list<array{name: string, href: string}>}>,
 *     unclaimed: list<array{family: string, label: string, anchor: string, classes: list<string>,
 *     file: string, line: int, doc: ?string, summary: string, documentedIn: ?array{path: string, title: string}}>}
 */
function docs_component_index(): array
{
    global $api;
    static $index = null;
    if ($index !== null) {
        return $index;
    }
    $isPublic = static fn(string $n): bool => api_bucket($n) === 'public';
    $public = array_values(array_filter(array_column($api['classes'], 'name'), $isPublic));
    $claims = docs_claims();

    $pages = [];
    foreach (docs_pages() as $p) {
        $classes = array_values(array_filter($p['documents'], $isPublic));
        if ($classes === []) {
            continue;
        }
        $pages[] = [
            'path' => $p['path'],
            'title' => $p['title'],
            'description' => $p['description'],
            'section' => $p['section'],
            'classes' => array_map(static function (string $n) use ($p): array {
                $anchor = docs_anchor($p, $n);
                return ['name' => $n, 'href' => $p['path'] . ($anchor !== null ? '#' . $anchor : '')];
            }, $classes),
        ];
    }
    /* Components first, since that is what most people come for, then the
       utility references, then anything else that claims a class. */
    $rank = static fn(array $p): array => [$p['section'] !== 'components', $p['section'], strtolower($p['title'])];
    usort($pages, static fn(array $a, array $b): int => $rank($a) <=> $rank($b));

    $familyOf = [];
    foreach ($api['components'] as $family => $c) {
        foreach ($c['members'] as $m) {
            $familyOf[$m] ??= $family;
        }
    }
    $families = [];
    foreach ($public as $name) {
        if (!isset($claims[$name])) {
            $families[$familyOf[$name] ?? $name][] = $name;
        }
    }
    ksort($families);
    $unclaimed = [];
    foreach ($families as $family => $names) {
        /* Named for the family when its root is among the strays, and for
           the first stray when the root has a page of its own. */
        $label = in_array($family, $names, true) ? $family : $names[0];
        $root = api_class($label);
        $unclaimed[] = [
            'family' => $family,
            'label' => $label,
            'anchor' => 'unclaimed-' . $label,
            'classes' => $names,
            'file' => $root['file'],
            'line' => $root['line'],
            'doc' => $root['doc'],
            'summary' => docs_summarise($root),
            'documentedIn' => $label === $family ? null : ($claims[$family] ?? null),
        ];
    }

    return $index = ['public' => count($public), 'pages' => $pages, 'unclaimed' => $unclaimed];
}

/**
 * Where on a page to land for one of its classes.
 *
 * Nowhere in particular when the page is about it: the top of the date picker
 * page is the answer to "datepicker". Otherwise at the heading that names it,
 * so "donut" opens the Charts page at Donut and "ring" opens Progress at
 * .ring. Failing both, at the class's row in the class table, or at the
 * Classes heading of a page that lists its classes by hand (print.php).
 */
function docs_anchor(array $page, string $name): ?string
{
    if ($name === ($page['component'] ?? null) || $name === ($page['documents'][0] ?? null)) {
        return null;
    }
    $key = docs_key($name);
    foreach ($page['headings'] as $h) {
        if ($h['id'] === $name || $h['key'] === $key || isset($h['words'][$key])) {
            return $h['id'];
        }
    }
    if ($page['tables']) {
        return 'class-' . $name;
    }
    return in_array('classes', array_column($page['headings'], 'id'), true) ? 'classes' : null;
}

/**
 * What the search searches: every docs page, and every family of public
 * classes no page claims yet, which lands on its entry in the component index.
 * The words and keys are worked out once per request.
 */
function docs_find_entries(): array
{
    static $entries = null;
    if ($entries !== null) {
        return $entries;
    }
    $entries = [];
    foreach (docs_pages() as $p) {
        $entries[] = $p + [
            'href' => $p['path'] === '' ? 'index.php' : $p['path'],
            'anchor' => null,
            'name' => basename($p['path'], '.php'),
        ];
    }
    foreach (docs_component_index()['unclaimed'] as $u) {
        $entries[] = [
            'href' => 'components/index.php',
            'anchor' => $u['anchor'],
            'title' => '.' . $u['label'],
            'name' => $u['label'],
            'description' => (string) $u['doc'],
            'section' => 'no page yet',
            'component' => $u['label'],
            'documents' => $u['classes'],
            'headings' => [],
        ];
    }
    foreach ($entries as &$e) {
        $e['key'] = docs_key($e['title']);
        $e['base'] = docs_key($e['name']);
        $e['titleWords'] = array_flip(docs_words($e['title']));
        $e['descWords'] = array_flip(docs_words($e['description']));
        $e['classKeys'] = [];
        foreach ($e['documents'] as $n) {
            $e['classKeys'][docs_key($n)] ??= $n;
        }
    }
    unset($e);
    return $entries;
}

/**
 * How well one entry answers a query, as [score, anchor, what matched], or
 * null for no match. Names beat words and words beat fragments:
 *
 *   100  the title, as a key              "date picker" → Date picker
 *    98  the page's file name             "qr" → qr.php
 *    96  a class it claims                "donut" → Charts
 *    90  a heading, as a key              "heatmap" → Charts, at Heatmap
 *        (each of those four scores 10 less when it matches only once the
 *        question words are dropped)
 *    85  every word in the title          "upload" → File upload
 *    75  every word in one heading        "circular" → Progress, at .ring
 *    72  the start of the title
 *    70  the start of a class it claims   "spark" → Charts
 *    65  every word in the description
 *    50  inside a class or title key      "ring" → .g-ring
 */
function docs_score(array $e, array $q): ?array
{
    $best = null;
    $offer = static function (int $score, ?string $anchor, string $via) use (&$best): void {
        if ($best === null || $score > $best[0]) {
            $best = [$score, $anchor, $via];
        }
    };
    $own = static fn(string $name): ?string => $e['anchor'] ?? docs_anchor($e, $name);

    foreach (array_values(array_unique([$q['full'], $q['core']])) as $i => $key) {
        $less = $i === 0 ? 0 : 10;
        if ($key === $e['key']) {
            $offer(100 - $less, $e['anchor'], 'title');
        }
        if ($key === $e['base']) {
            $offer(98 - $less, $e['anchor'], 'name');
        }
        if (isset($e['classKeys'][$key])) {
            $name = $e['classKeys'][$key];
            $offer(96 - $less, $own($name), '.' . $name);
        }
        foreach ($e['headings'] as $h) {
            if ($key === $h['key']) {
                $offer(90 - $less, $h['id'], $h['text']);
                break;
            }
        }
    }

    $has = static function (array $set) use ($q): bool {
        foreach ($q['words'] as $w) {
            if (!isset($set[$w])) {
                return false;
            }
        }
        return true;
    };
    if ($has($e['titleWords'])) {
        $offer(85, $e['anchor'], 'title');
    }
    foreach ($e['headings'] as $h) {
        if ($has($h['words'])) {
            $offer(75, $h['id'], $h['text']);
            break;
        }
    }
    if ($has($e['descWords'])) {
        $offer(65, $e['anchor'], 'description');
    }

    $full = $q['full'];
    if (strlen($full) >= 3) {
        if (str_starts_with($e['key'], $full)) {
            $offer(72, $e['anchor'], 'title');
        }
        foreach ($e['classKeys'] as $k => $name) {
            if (str_starts_with((string) $k, $full)) {
                $offer(70, $own($name), '.' . $name);
                break;
            }
        }
        foreach ($e['classKeys'] as $k => $name) {
            if (str_contains((string) $k, $full)) {
                $offer(50, $own($name), '.' . $name);
                break;
            }
        }
        if (str_contains($e['key'], $full)) {
            $offer(50, $e['anchor'], 'title');
        }
    }
    return $best;
}

/**
 * The pages that answer a query, best first. A tie keeps path order, which
 * puts components ahead of guides and every page ahead of "No page yet".
 *
 * @return list<array{score: int, order: int, path: string, fragment: ?string,
 *     href: string, title: string, description: string, section: string, via: string}>
 */
function docs_find(string $q): array
{
    $query = docs_query($q);
    if ($query['full'] === '') {
        return [];
    }
    $hits = [];
    foreach (docs_find_entries() as $order => $e) {
        $m = docs_score($e, $query);
        if ($m === null) {
            continue;
        }
        [$score, $fragment, $via] = $m;
        $hits[] = [
            'score' => $score,
            'order' => $order,
            'path' => $e['href'],
            'fragment' => $fragment,
            'href' => $e['href'] . ($fragment !== null ? '#' . $fragment : ''),
            'title' => $e['title'],
            'description' => $e['description'],
            'section' => $e['section'],
            'via' => $via,
        ];
    }
    usort($hits, static fn(array $a, array $b): int => [$b['score'], $a['order']] <=> [$a['score'], $b['order']]);
    return $hits;
}

/**
 * The one hit a search can go straight to, or null: a match on a name or a
 * title rather than a passing mention, and clearly ahead of the next. Anything
 * less shows the results, because sending someone to the wrong page is worse
 * than asking them to pick.
 */
function docs_sure(array $hits): ?array
{
    $top = $hits[0] ?? null;
    if ($top === null || $top['score'] < 75) {
        return null;
    }
    return isset($hits[1]) && $hits[1]['score'] >= $top['score'] ? null : $top;
}

/**
 * An entry's terms for the component index's filter, as one space-led string:
 * every word of every phrase given, and each phrase as a key.
 */
function docs_terms(string ...$phrases): string
{
    $terms = [];
    foreach ($phrases as $phrase) {
        foreach (docs_words($phrase) as $w) {
            $terms[$w] = true;
        }
        $terms[docs_key($phrase)] = true;
    }
    unset($terms['']);
    return ' ' . implode(' ', array_keys($terms));
}

/**
 * The component index's filter. The page's script repeats it word for word,
 * so typing and submitting show the same rows: the query as one key, or each
 * of its words, must start one of the entry's terms. Start, not contain, so
 * "ring" keeps .ring and .ring-wrap and drops every "during".
 */
function docs_terms_match(string $terms, string $q): bool
{
    $query = docs_query($q);
    if ($query['full'] === '') {
        return true;
    }
    if (str_contains($terms, ' ' . $query['full']) || str_contains($terms, ' ' . $query['core'])) {
        return true;
    }
    foreach ($query['words'] as $w) {
        if (!str_contains($terms, ' ' . $w)) {
            return false;
        }
    }
    return true;
}
