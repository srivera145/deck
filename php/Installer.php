<?php

declare(strict_types=1);

namespace EchoDial\Deck;

use Composer\Script\Event;

/**
 * Publishes Deck's assets out of vendor/ and into the project's public
 * directory, because a browser cannot read vendor/.
 *
 * Configure the destination in your own composer.json:
 *
 *   "extra": {
 *     "deck": {
 *       "publish-to": "public/assets/deck",
 *       "auto-publish": true
 *     }
 *   }
 *
 * Or run it by hand at any time:
 *
 *   composer deck-publish
 *   composer deck-publish -- public/static/deck
 *
 * If you would rather symlink than copy during development, pass --link.
 */
final class Installer
{
    private const DEFAULT_TARGET = 'public/assets/deck';

    private const DEFAULT_FILES = [
        'deck.css',
        'deck.min.css',
        'deck-icons.svg',
        'deck.js',
        'deck-extras.js',
        'deck-adapters.js',
        'deck.bundle.js',
        'deck.bundle.min.js',
    ];

    public static function postInstall(Event $event): void
    {
        $extra = $event->getComposer()->getPackage()->getExtra()['deck'] ?? [];

        if (($extra['auto-publish'] ?? true) === false) {
            $event->getIO()->write('<comment>Deck: auto-publish is off. Run `composer deck-publish` when you are ready.</comment>');

            return;
        }

        self::publish($event);
    }

    public static function publish(Event $event): void
    {
        $io      = $event->getIO();
        $extra   = $event->getComposer()->getPackage()->getExtra()['deck'] ?? [];
        $args    = $event->getArguments();
        $link    = in_array('--link', $args, true);
        $args    = array_values(array_filter($args, static fn ($a) => $a !== '--link'));

        $target  = $args[0] ?? $extra['publish-to'] ?? self::DEFAULT_TARGET;
        $files   = $extra['files'] ?? self::DEFAULT_FILES;

        $projectRoot = dirname($event->getComposer()->getConfig()->get('vendor-dir'));
        $source      = dirname(__DIR__) . '/dist';
        $destination = $projectRoot . '/' . trim((string) $target, '/');

        if (! is_dir($source)) {
            $io->writeError('<error>Deck: dist/ is missing. Run `node build.mjs` in the package first.</error>');

            return;
        }

        if (! is_dir($destination) && ! @mkdir($destination, 0o755, true) && ! is_dir($destination)) {
            $io->writeError(sprintf('<error>Deck: could not create %s</error>', $destination));

            return;
        }

        $copied  = 0;
        $skipped = 0;

        foreach ($files as $file) {
            $from = $source . '/' . $file;
            $to   = $destination . '/' . $file;

            if (! is_file($from)) {
                $skipped++;

                continue;
            }

            if ($link) {
                if (is_link($to) || is_file($to)) {
                    @unlink($to);
                }

                if (@symlink($from, $to)) {
                    $copied++;

                    continue;
                }
                // Windows without developer mode, and some containers, refuse
                // symlinks. Fall through to a copy rather than failing.
            }

            // Skip an unchanged file so a redeploy does not churn mtimes and
            // invalidate every cache-busting URL for no reason.
            if (is_file($to) && filesize($to) === filesize($from) && md5_file($to) === md5_file($from)) {
                $skipped++;

                continue;
            }

            if (@copy($from, $to)) {
                $copied++;
            } else {
                $io->writeError(sprintf('<error>Deck: could not write %s</error>', $to));
            }
        }

        $io->write(sprintf(
            '<info>Deck %s published to %s</info> (%d %s, %d unchanged)',
            Deck::VERSION,
            trim((string) $target, '/'),
            $copied,
            $link ? 'linked' : 'copied',
            $skipped
        ));

        $io->write('  Add to your layout:');
        $io->write(sprintf('    <link rel="stylesheet" href="/%s/deck.css">', trim((string) $target, '/')));
        $io->write(sprintf('    <script src="/%s/deck.js" defer></script>', trim((string) $target, '/')));
    }
}
