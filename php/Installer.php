<?php

declare(strict_types=1);

namespace EchoDial\Deck;

use Composer\Script\Event;

/**
 * Publishes Deck's assets out of vendor/ and into the project's public
 * directory, because a browser cannot read vendor/.
 *
 * Composer only runs scripts defined in the root package, so none of this runs
 * in your project until your own composer.json wires it in:
 *
 *   "scripts": {
 *     "deck-publish": "EchoDial\\Deck\\Installer::publish",
 *     "post-install-cmd": ["EchoDial\\Deck\\Installer::postInstall"],
 *     "post-update-cmd": ["EchoDial\\Deck\\Installer::postInstall"]
 *   },
 *   "extra": {
 *     "deck": {
 *       "publish-to": "public/assets/deck",
 *       "auto-publish": false
 *     }
 *   }
 *
 * extra.deck is read from the root package too, which is your project. The
 * extra.deck block in Deck's own composer.json only applies inside Deck's own
 * repository, and there this class refuses to publish: running it there once
 * copied Deck into itself, and the copy was committed.
 *
 * auto-publish is off unless set to true. With it off the install and update
 * hooks print a reminder instead of writing files into a project that did not
 * ask for them.
 *
 * Publish by hand at any time:
 *
 *   composer deck-publish
 *   composer deck-publish -- public/static/deck
 *   composer deck-publish -- --link              symlink instead of copy
 */
final class Installer
{
    /** The package this class ships in. Publishing into it is never what anyone meant. */
    private const PACKAGE = 'echodial/deck';

    /** The explicit opt-in for publishing inside Deck's own repository anyway. */
    private const ALLOW_SELF = '--allow-self';

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
        if ($event->getComposer()->getPackage()->getName() === self::PACKAGE) {
            $event->getIO()->write("<comment>Deck: skipped publishing assets. This is Deck's own repository (root package echodial/deck), not a project that uses Deck.</comment>");

            return;
        }

        $extra = $event->getComposer()->getPackage()->getExtra()['deck'] ?? [];

        if (($extra['auto-publish'] ?? false) !== true) {
            $event->getIO()->write('<comment>Deck: assets not published, because extra.deck.auto-publish is off. Run `composer deck-publish`, or set auto-publish to true.</comment>');

            return;
        }

        self::publish($event);
    }

    /**
     * Returns false when it refuses or fails. For `composer deck-publish`,
     * Composer turns that into a non-zero exit code, so a script calling it
     * can tell the difference.
     */
    public static function publish(Event $event): bool
    {
        $io      = $event->getIO();
        $package = $event->getComposer()->getPackage();
        $args    = $event->getArguments();

        if ($package->getName() === self::PACKAGE) {
            if (! in_array(self::ALLOW_SELF, $args, true)) {
                $io->writeError("<error>Deck: refusing to publish. This is Deck's own repository (root package echodial/deck); pass -- " . self::ALLOW_SELF . ' to publish into it anyway.</error>');

                return false;
            }

            $io->write("<warning>Deck: publishing into Deck's own repository, because " . self::ALLOW_SELF . ' was passed.</warning>');
        }

        $extra   = $package->getExtra()['deck'] ?? [];
        $link    = in_array('--link', $args, true);
        $args    = array_values(array_filter(
            $args,
            static fn ($a) => $a !== '--link' && $a !== self::ALLOW_SELF
        ));

        $target  = $args[0] ?? $extra['publish-to'] ?? self::DEFAULT_TARGET;
        $files   = $extra['files'] ?? self::DEFAULT_FILES;

        $projectRoot = dirname($event->getComposer()->getConfig()->get('vendor-dir'));
        $source      = dirname(__DIR__) . '/dist';
        $destination = $projectRoot . '/' . trim((string) $target, '/');

        if (! is_dir($source)) {
            $io->writeError('<error>Deck: dist/ is missing. Run `node build.mjs` in the package first.</error>');

            return false;
        }

        if (! is_dir($destination) && ! @mkdir($destination, 0o755, true) && ! is_dir($destination)) {
            $io->writeError(sprintf('<error>Deck: could not create %s</error>', $destination));

            return false;
        }

        $copied  = 0;
        $skipped = 0;
        $failed  = 0;

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
                $failed++;
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

        // A URL, not a path. The first directory of the target is usually the
        // document root, which is not part of the URL: public/assets/deck is
        // served as /assets/deck. The npx CLI strips the same names.
        $url = '/' . preg_replace('#^(public_html|public|web|htdocs|httpdocs|www|wwwroot|dist|static)/#', '', trim((string) $target, '/'));

        $io->write('  Add to your layout:');
        $io->write(sprintf('    <link rel="stylesheet" href="%s/deck.css">', $url));
        $io->write(sprintf('    <script src="%s/deck.js" defer></script>', $url));

        return $failed === 0;
    }
}
