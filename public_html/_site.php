<?php
/**
 * The site's base URL and version, read from one place.
 *
 * Every absolute URL the site prints starts with 'base': the canonical links,
 * og:url and og:image, and the JSON-LD, on the demo page and on every docs page.
 * sitemap.xml, the Sitemap line in robots.txt and the site links in llms.txt
 * are static files, so build.mjs writes the same base into them
 * (tools/site.mjs), resolved the same way in the same order:
 *
 *   1. DECK_SITE_BASE from the environment, for a local vhost or a staging copy
 *   2. otherwise "homepage" in package.json, which is also what npm shows
 *
 * A server that sets DECK_SITE_BASE needs the build run with it set too, or
 * sitemap.xml will name a different host from the one serving it.
 *
 *     $meta = require __DIR__ . '/_site.php';   // ['base' => ..., 'version' => ...]
 */

declare(strict_types=1);

return (static function (): array {
    $file = dirname(__DIR__) . '/package.json';
    if (!is_readable($file)) {
        http_response_code(500);
        exit('package.json is missing. The site reads its base URL and version from it.');
    }
    $pkg = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

    return [
        'base'    => rtrim(getenv('DECK_SITE_BASE') ?: $pkg['homepage'], '/'),
        'version' => $pkg['version'],
    ];
})();
