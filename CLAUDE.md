## Deck src/ fix work

These tasks change the framework itself, not a template. Line numbers below
were verified against 0.1.2 and may have shifted in 0.1.3 — confirm before
editing.

AFTER ANY src/ CHANGE
- npm run build must pass. It regenerates dist/, dist/api.json,
  dist/api-buckets.json, dist/sizes.json and sitemap.xml.
- If you add, rename or remove a class, update tools/docs/api-decisions.txt
  with its bucket and reason, or the classifier will not know where to put it.
- Regenerate the editor data too: cd tools/vscode && node generate.mjs &&
  node test.mjs. Otherwise the IntelliSense extension reports new classes as
  typos.
- Add an entry to FINDINGS.md for anything you fix or discover.
- Class and token counts are stamped into public_html/index.php, README.md
  and llms.txt. tools/sync-sizes.mjs fails the build if they drift — let it
  tell you rather than editing them by hand first.

DO NOT
- Do not add a dependency, a build step or a config file.
- Do not change a public class name without recording the old one in the
  removed list with a replacement, the way the existing 31 removed classes do.

## Deck template work

Target file: public_html/templates/dashboard.html — one standalone HTML file,
no build step, no framework, no JS libraries.

Assets load from ../assets/deck/deck.css and ../assets/deck/deck.js.
Icons are ../assets/deck/deck-icons.svg#name and must stay same-origin.

HARD RULES
- Use only classes that exist in src/. `npm run build` fails on any class
  that doesn't, and that check is the acceptance test.
- Before using a class, confirm it in dist/api.json or src/. Do not invent one.
- Page-local CSS goes in a single <style> block. Never edit src/ from a
  template task.

KNOWN TRAPS, verified in source
- .segmented shows its active child via [aria-selected="true"] or .is-active.
  NOT aria-pressed — .chip and .reaction use that one. Wrong attribute = no
  visible state and no error. aria-selected needs role="tablist"/role="tab".
- --ink-100 and --ink-200 are FIXED light values (95.4% / 91.2% lightness,
  no light-dark()). Never use them for a surface or you get a white block in
  dark mode. This is why .progress and .skeleton look wrong in dark mode today.
- Icons `inbox` and `arrow-down-right` do NOT exist. Use `file` and
  `arrow-right` (rotate with a page-local transform if a down-right arrow is
  needed).
- .list-row is a centred flex row. Stacked content inside it needs its own
  wrapper element.