<?php

declare(strict_types=1);

namespace EchoDial\Deck;

/**
 * Deck — the PHP side.
 *
 * Deck is a stylesheet, not a PHP library, so this class does exactly three
 * things: tell you where the assets are, emit the tags, and version the URLs
 * so a browser picks up a new build. Nothing here is required. If you would
 * rather write the link tag yourself, write the link tag yourself.
 *
 *   use EchoDial\Deck\Deck;
 *
 *   Deck::configure(['base' => '/assets/deck']);
 *
 *   <head>
 *     <?= Deck::head() ?>
 *   </head>
 *
 * Framework-agnostic on purpose: no container, no service provider, no
 * facade. It works in Keel, in Laravel, in Symfony, in WordPress, and in a
 * single index.php.
 */
final class Deck
{
    public const VERSION = '0.1.4';

    /** @var array<string, mixed> */
    private static array $config = [
        'base'      => '/assets/deck',
        'minify'    => null,   // null decides from the file that exists
        'bust'      => true,   // append ?v= from the file mtime
        'extras'    => true,   // datepicker, combobox, grid, toasts, QR
        'adapters'  => false,  // optional library integrations
        'bundle'    => false,  // one request instead of three
        'hue'       => null,   // e.g. 265 to retheme at render time
        'theme'     => null,   // 'light' | 'dark' | null for the OS setting
        'root'      => null,   // filesystem path, for mtime and integrity
        'defer'     => true,
    ];

    /**
     * @param array<string, mixed> $options
     */
    public static function configure(array $options): void
    {
        self::$config = array_merge(self::$config, $options);
    }

    public static function config(string $key, mixed $default = null): mixed
    {
        return self::$config[$key] ?? $default;
    }

    /** Absolute filesystem path to Deck's own dist directory. */
    public static function distPath(): string
    {
        return dirname(__DIR__) . '/dist';
    }

    /** Public URL for one asset, with a cache-busting query when possible. */
    public static function asset(string $file): string
    {
        $base = rtrim((string) self::$config['base'], '/');
        $url  = $base . '/' . ltrim($file, '/');

        if (! self::$config['bust']) {
            return $url;
        }

        $root = self::$config['root'] ?? self::guessDocumentRoot();
        $disk = $root ? $root . '/' . ltrim($base, '/') . '/' . ltrim($file, '/') : null;

        if ($disk && is_file($disk)) {
            return $url . '?v=' . filemtime($disk);
        }

        return $url . '?v=' . self::VERSION;
    }

    /** The stylesheet name Deck should link, minified when one is present. */
    public static function stylesheet(): string
    {
        $minify = self::$config['minify'];

        if ($minify === null) {
            $root = self::$config['root'] ?? self::guessDocumentRoot();
            $base = trim((string) self::$config['base'], '/');
            $minify = $root !== null && is_file($root . '/' . $base . '/deck.min.css');
        }

        return $minify ? 'deck.min.css' : 'deck.css';
    }

    /**
     * The <link> tag. Pass the request's CSP nonce when style-src allows
     * stylesheets by nonce.
     */
    public static function css(?string $nonce = null): string
    {
        return sprintf(
            '<link rel="stylesheet" href="%s"%s>',
            htmlspecialchars(self::asset(self::stylesheet()), ENT_QUOTES),
            self::nonceAttribute($nonce)
        );
    }

    /**
     * The <script> tags, in the right order, each with the nonce when one is
     * given. The first carries data-deck-icons, which deck.js reads to find
     * the sprite, so the sprite path needs no inline script.
     */
    public static function js(?string $nonce = null): string
    {
        $defer = self::$config['defer'] ? ' defer' : '';
        $nonceAttr = self::nonceAttribute($nonce);
        $sprite = sprintf(' data-deck-icons="%s"', htmlspecialchars(self::asset('deck-icons.svg'), ENT_QUOTES));
        $tag = static fn (string $f, string $extra = ''): string => sprintf(
            '<script src="%s"%s%s%s></script>',
            htmlspecialchars(self::asset($f), ENT_QUOTES),
            $extra,
            $defer,
            $nonceAttr
        );

        if (self::$config['bundle']) {
            $root = self::$config['root'] ?? self::guessDocumentRoot();
            $base = trim((string) self::$config['base'], '/');
            $min  = $root !== null && is_file($root . '/' . $base . '/deck.bundle.min.js');

            return $tag($min ? 'deck.bundle.min.js' : 'deck.bundle.js', $sprite);
        }

        $out = [$tag('deck.js', $sprite)];

        if (self::$config['extras']) {
            $out[] = $tag('deck-extras.js');
        }

        if (self::$config['adapters']) {
            $out[] = $tag('deck-adapters.js');
        }

        return implode("\n", $out);
    }

    /**
     * Everything for the <head>: the meta viewport Deck's mobile-first layout
     * assumes, the stylesheet, the scripts with the icon sprite path, and any
     * theme overrides.
     *
     * Under a strict Content Security Policy, pass the request's nonce and it
     * goes on every tag this prints, the <style> for the hue included:
     *
     *   <?= Deck::head(nonce: $cspNonce) ?>
     *
     * There is no inline script to allow. The nonce is per request, so it is a
     * parameter rather than a configure() setting: static config outlives the
     * request on a long-running PHP server.
     */
    public static function head(bool $viewport = true, ?string $nonce = null): string
    {
        $out = [];

        if ($viewport) {
            $out[] = '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">';
        }

        $out[] = self::css($nonce);

        $vars = [];
        if (self::$config['hue'] !== null) {
            $vars[] = '--hue-brand:' . (int) self::$config['hue'];
        }

        if ($vars !== []) {
            $out[] = '<style' . self::nonceAttribute($nonce) . '>:root{' . implode(';', $vars) . '}</style>';
        }

        $out[] = self::js($nonce);

        return implode("\n", $out);
    }

    /** Attributes for the <html> tag: theme and direction. */
    public static function htmlAttributes(?string $lang = null, ?string $dir = null): string
    {
        $attrs = [];

        if ($lang !== null) {
            $attrs['lang'] = $lang;
        }

        if ($dir !== null) {
            $attrs['dir'] = $dir;
        }

        if (self::$config['theme'] !== null) {
            $attrs['data-theme'] = (string) self::$config['theme'];
        }

        $out = '';
        foreach ($attrs as $key => $value) {
            $out .= sprintf(' %s="%s"', $key, htmlspecialchars($value, ENT_QUOTES));
        }

        return ltrim($out);
    }

    /** The sprite's hand-authored symbols, which have no -sm cut. */
    private const NO_SMALL_CUT = ['deck-mark', 'deck-wordmark'];

    /**
     * One <svg><use> for an icon from Deck's sprite.
     *
     *   <?= Deck::icon('check-circle', 'icon icon-lg') ?>
     *
     * With .icon-sm in the class it uses the icon's heavier -sm cut, which is
     * drawn to hold up at 16px. CSS cannot change a <use href>, so the helper
     * makes the pairing a hand-written tag would have to remember.
     */
    public static function icon(string $name, string $class = 'icon', ?string $label = null): string
    {
        if (preg_match('/(^|\s)icon-sm(\s|$)/', $class) === 1
            && !str_ends_with($name, '-sm')
            && !in_array($name, self::NO_SMALL_CUT, true)) {
            $name .= '-sm';
        }

        $aria = $label === null
            ? ' aria-hidden="true"'
            : sprintf(' role="img" aria-label="%s"', htmlspecialchars($label, ENT_QUOTES));

        return sprintf(
            '<svg class="%s"%s><use href="%s#%s"></use></svg>',
            htmlspecialchars($class, ENT_QUOTES),
            $aria,
            htmlspecialchars(self::asset('deck-icons.svg'), ENT_QUOTES),
            htmlspecialchars($name, ENT_QUOTES)
        );
    }

    /**
     * The per-tenant theming Deck was built for: one inline style, no rebuild,
     * no second stylesheet.
     *
     *   <html <?= Deck::theme(hue: $tenant->hue, mode: $user->theme) ?>>
     */
    public static function theme(?int $hue = null, ?string $mode = null, ?float $chroma = null): string
    {
        $vars = [];

        if ($hue !== null) {
            $vars[] = '--hue-brand:' . $hue;
        }

        if ($chroma !== null) {
            $vars[] = '--chroma-brand:' . rtrim(rtrim(number_format($chroma, 3, '.', ''), '0'), '.');
        }

        $out = '';

        if ($vars !== []) {
            $out .= sprintf('style="%s"', htmlspecialchars(implode(';', $vars), ENT_QUOTES));
        }

        if ($mode !== null) {
            $out .= ($out === '' ? '' : ' ') . sprintf('data-theme="%s"', htmlspecialchars($mode, ENT_QUOTES));
        }

        return $out;
    }

    private static function nonceAttribute(?string $nonce): string
    {
        return $nonce === null || $nonce === ''
            ? ''
            : sprintf(' nonce="%s"', htmlspecialchars($nonce, ENT_QUOTES));
    }

    private static function guessDocumentRoot(): ?string
    {
        $root = $_SERVER['DOCUMENT_ROOT'] ?? null;

        return is_string($root) && $root !== '' ? rtrim($root, '/') : null;
    }
}
