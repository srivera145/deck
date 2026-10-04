# Deck IntelliSense

Class and token completion, hover documentation, and typo detection for
[Deck](https://get-deck.dev), the mobile-first CSS framework.

Everything the extension knows is generated from Deck's own API extract, so it
cannot drift from the stylesheet it describes. For Deck 0.1.2 that is 828 public
classes, 103 internal ones, 31 removed ones, 144 tokens and 152 icon symbols.

## Why this exists

Autocomplete is the smaller half. The reason to install it is the diagnostics.

Deck's spacing scales are `0, 1, 2, 3, 4, 6, 8` — they skip 5 and 7. So `.stack-5`
is not a class. But `.stack` on its own *is*, and it carries a default gap, which
means `.stack-5` does not fail. The browser applies the default, the page looks
nearly right, and nothing tells you. That bug shipped once in Deck's own history,
which is the whole argument for catching it in the editor:

```html
<div class="card stack-5 shadow">
                  ~~~~~~~
```

> `.stack-5` is not a Deck class — the stack scale is 0, 1, 2, 3, 4, 6, 8.
> `.stack` on its own is valid, so this silently falls back to the default
> instead of failing.
>
> **Quick fix:** Replace with `.stack-4` · Replace with `.stack-6`

Both neighbours are offered, because 5 sits exactly between them and guessing
which one you wanted would be a coin toss.

## What it reports

| Case | Severity | Example |
| --- | --- | --- |
| A number off a real scale | Error, with fixes | `.gap-5`, `.p-7`, `.stack-5` |
| A class removed from Deck | Error, with the replacement | `.is-full` → `.w-full` |
| One character off a real class | Error, with a fix | `.btn-primery` → `.btn-primary` |
| A class internal to Deck | Warning | `.carousel-dot` |
| An `is-*` state class written by hand | Hint | `.is-copied` |

### What it deliberately does not report

Your own classes. An extension that underlines correct markup gets switched off
within a day, and then it catches nothing at all — so the default setting reports
a class only when it looks like a Deck class gone wrong.

In practice that means `.my-app-header`, `.site-wrap`, `.hero` and `.dx-toc` stay
silent, and so does `.row` — one edit from Deck's `.grow`, but far more likely to
be Bootstrap markup you are migrating. Generic typo matching needs a token of at
least five characters for the same reason: below that, almost any string is one
edit from something.

If your project uses Deck classes exclusively, set
`deck.diagnostics.unknownClasses` to `all` and it will report every class Deck
does not define. Set it to `off` to report nothing on that basis.

## The rest of it

**Completion** inside `class` and `className` attributes, with the layer shown as
the detail and the real declarations in the documentation — for the 541 classes
with no prose, `position: absolute` says everything `.absolute` does.

**Tokens** inside `var(`, with a colour swatch on the 76 colour tokens and the
resolved value on all 144. A token that dark mode redefines says so.

**Icons** after `deck-icons.svg#`, all 152 symbols, each with the markup to paste
and a reminder that the sprite has to be served from your own origin — a
cross-origin `<use href>` renders nothing at all.

**Hover** on any class or token: what it declares, which cascade layer owns it,
its siblings in the family, and the source file and line.

**Go to definition** opens the real rule in Deck's `src/`. It looks in
`node_modules/@echodial/deck/src` and `vendor/echodial/deck/src` in each
workspace folder; set `deck.sourceRoot` if yours is elsewhere.

## Languages

HTML, PHP, Blade, Twig, Jinja, Django, ERB, Handlebars, Razor, Astro, Vue,
Svelte, JavaScript, TypeScript, JSX and TSX, plus CSS, SCSS and Less for tokens.
Add others with `deck.languages`.

Template expressions are blanked before anything is checked, so PHP short echoes,
Blade braces and JS interpolation are never mistaken for class names:

```php
<a class="btn btn-primary <?= $active ? 'is-active' : '' ?>">
```

Only `btn` and `btn-primary` are checked here. Classes inside the PHP expression
are skipped rather than guessed at — under-reporting is the right failure
direction for a linter that runs on every keystroke.

## Settings

| Setting | Default | Does |
| --- | --- | --- |
| `deck.enable` | `true` | Master switch |
| `deck.diagnostics.enable` | `true` | Report problems |
| `deck.diagnostics.unknownClasses` | `likely` | `off` · `likely` · `all` |
| `deck.diagnostics.internalClasses` | `true` | Warn on internal classes |
| `deck.diagnostics.stateClasses` | `true` | Hint on hand-written `is-*` |
| `deck.completion.classes` | `true` | Class completion |
| `deck.completion.tokens` | `true` | Token completion |
| `deck.completion.icons` | `true` | Icon completion |
| `deck.languages` | `[]` | Extra language IDs |
| `deck.sourceRoot` | `""` | Path to Deck's `src/` |

## Building it

The extension carries no hand-maintained list. `data/deck-data.json` is
generated from files the main build already produces:

```
dist/api.json          classes, tokens, docs, declarations, source lines
dist/api-buckets.json  the public / internal / removed classification
dist/deck-icons.svg    the sprite, for its symbol ids
```

From this folder, after `npm run build` in the repo root:

```bash
node generate.mjs        # rewrite data/deck-data.json
node test.mjs            # 28 assertions, headless, no VS Code needed
npm run package          # regenerate, then build the .vsix
```

`generate.mjs` exits non-zero rather than writing a partial file. An extension
shipping half a class list would flag real classes as typos, which is worse than
having no diagnostics at all.

`test.mjs` stubs enough of the VS Code API to run `extension.js` headless. It
asserts both directions: that the bugs are caught, and that ordinary app classes
and Bootstrap leftovers are left alone. Run it before every release — the
false-positive assertions are the ones that matter, because that is the failure
that makes people uninstall.

## Releasing

Regenerate the data whenever Deck's classes change, or the extension will
describe a version nobody is running:

```bash
cd tools/vscode
node generate.mjs && node test.mjs && npm run package
```

The `.vsix` installs locally with **Extensions: Install from VSIX…** in the
command palette. Publishing to the marketplace needs a publisher named
`echodial` and `npx @vscode/vsce login echodial`.

MIT, same as Deck.
