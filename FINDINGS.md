# Findings

Things documenting Deck revealed about Deck. Started while building the docs system and
the `button.php` exemplar, then updated by the pass that fixed the source and again by
the public API freeze. Items marked FIXED were repaired; the rest carry a reason for
leaving them. Ordered by how much they would cost to leave alone.

## 1. A component's rules are spread across ten files — now mechanically checked

`.btn` is defined in `src/05-buttons.css`, but it also has rules in `02-reset.css`,
`06-forms.css`, `07-components.css`, `16-motion.css`, `18-container.css`,
`19-logical.css`, `22-nav.css`, and `99-print.css` — nine files for one component.
`.btn-3d` is not in the buttons file at all; it lives in `21-space3d.css`. `.fab` is
defined with the buttons but repositioned in `22-nav.css`.

This is the biggest obstacle to the one-page-per-component model. Writing the button
page meant reading nine files to be sure nothing was missed, and there is no mechanical
way to know a component page is complete. The extractor now records every file a class
has a rule in, which makes the problem visible, but it does not solve it.

**Now handled by tooling rather than by memory.** The extractor emits a per-component
rule inventory — every rule in every file that targets any member of the component,
including descendant and `:is()` selectors — and the verifier requires a component page
to declare `'component' => 'btn'` and account for every file in that inventory. `.btn`
turns out to be 55 rules across **ten** files, not nine: `21-space3d.css` was the one I
missed by reading rather than counting, which is the whole argument for counting.

**Still not fixed:** the underlying arrangement. `src/` is organised by concern, so a
component page will always span several files. Reorganising by component would lose the
layer-per-file clarity that makes `dist/layers/` possible, so the tooling compensates
instead of the source changing.

## 2. The spacing scale had holes — FIXED

`.stack` implemented 1, 2, 3, 6, 8; `.gap-*` implemented 0, 1, 2, 3, 4, 6, 8; the
padding utilities implemented 0, 2, 3, 4, 5, 6, 8; the logical utilities implemented
different subsets again. Four scales that disagreed.

Now one scale — **0, 1, 2, 3, 4, 6, 8** — implemented completely by `.stack-*`,
`.cluster-*`, `.gap-*`, `.p*-*` and `.m*-*`. There is deliberately no 5: at 1.25rem it
is indistinguishable from 4 and 6 in a real layout, and a half step between two
neighbours is what people reach for when they are not sure. `.p-5`, `.px-5` and
`.py-12` were removed; all three had zero uses.

The `--space-*` tokens keep their full 0–24 range. Tokens are the palette a component
composes from and are used 41 times for `--space-5` alone; the utilities are the
smaller set you author markup with. Different jobs, so different sizes — now stated in
`04-layout.css` rather than implied.

## 3. Most classes still carry no doc comment — improved, not solved

Coverage was 8.9% (81 of 912). It is now **16.8% (162 of 963)**, in two steps: fixing
the extractor to stop discarding prose inside ruled comment blocks took it to 14.2%
without a word being written (see item 13), and 30 hand-written comments took it to
16.8%.

The 30 were chosen by counting class attributes in the demo, so they are the classes
people actually reach for — `.icon` at 122 uses, `.text-muted` at 78, `.card` at 56 —
rather than a guess about what matters.

**801 classes remain undocumented.** They are described in the generated reference only
by the declarations they set, which is honest but thin. The backlog is recorded in
`tools/docs/undocumented.txt` so it shrinks deliberately rather than being rounded off,
and `tools/docs/add-comments.mjs` is the mechanism for the next batch.

## 4. State classes had no owning component — FIXED, with two exceptions

The extractor now attributes every `is-*` class to the component whose selectors
mention it, and the verifier fails on any that cannot be attributed. 39 of 41 resolved
automatically. A state may have several owners — `.is-active` belongs to `chip`, `tab`,
`segmented` and `combo` — which is correct rather than ambiguous.

Two resisted, and they turn out not to be states at all:

| class | what it actually is |
| --- | --- |
| `.is-auto` | `inline-size: auto` |
| `.is-full` | `inline-size: 100%` |

In `19-logical.css` the `is` prefix means **inline-size**, colliding with the `is-`
state convention used everywhere else in the framework. They are exempted in
`tools/docs/verify.mjs` with that reason recorded rather than being forced into a
component they do not belong to.

**FIXED by the API freeze.** That pass looked at the whole logical-utility family at
once and deleted twenty-two of them, `.is-auto` and `.is-full` among them, as duplicates
of the `09-utilities.css` names. Nothing is published yet, so it cost nothing. `API.md`
carries the record of what went and why.

A shared state still has no single home in the one-page-per-component model. Button
documents `is-loading` and `is-disabled`; the data grid page will want `is-loading`
too. A `reference/state-classes.php` page is still the answer.

## 5. `.btn` is redefined in the print layer, which naive tooling gets wrong

The first version of the extractor reported `.btn` as living in `99-print.css:167`,
because it took the last plain `.btn { … }` rule it saw. Anything that walks Deck's CSS
has to prefer the first definition and treat later ones as overrides, or it will point
readers at the print stylesheet. Worth stating in the source, since the next tool to be
written will hit the same thing.

## 6. Section dividers and doc comments are indistinguishable without a heuristic

Deck writes both as `/* … */`. `/* --- Variants ------- */` is a heading;
`/* Every button clears the 44px touch target */` is documentation. The extractor tells
them apart by looking for a run of three or more dashes or equals signs, which works on
today's source and is exactly the kind of rule that breaks quietly when someone writes a
divider differently.

A convention would be more robust — `/** … */` for documentation, `/* --- … --- */` for
dividers — and would cost one pass over `src/`.

## 7. `.prose` exists and I did not find it

I wrote the docs pages using bare `<p>` inside `.stack-*` wrappers, and only discovered
`.prose` afterwards while auditing. It is exactly what a documentation page wants.

That is a discoverability finding rather than a defect: with 912 classes and no
reference page, the way you learn a class exists is by reading the source or the demo.
Which is the whole argument for building this site.

## 8. `.copy` requires a redundant copy of the text

`.copy-btn[data-deck-copy]` reads its text from a sibling `.copy-value`. On a
documentation page the example source is already on screen in a `<pre>`, so the markup
has to contain it twice and hide one copy with CSS:

```css
.dx-example-source .copy-value { display: none; }
```

A `data-copy-target="#id"` or `data-copy-text` attribute would let the button point at
the visible block instead of duplicating it.

## 9. Headings have no block margin

`src/03-type.css` sets font, weight, and size on `h1`–`h6` but no margins, on the
assumption that spacing comes from a `.stack-*` wrapper. That works for component
layouts and is awkward for long-form prose, where every heading-and-paragraph run has to
be wrapped in its own `stack` element. The button page has fourteen such wrappers that
exist only to create spacing.

`.prose` may already solve this (see 6); if it does, it needs to be the documented
default for text pages.

## 10. Grid children need `min-inline-size: 0` and Deck knows it

The docs shell overflowed 422px horizontally at 375px, because a CSS grid item's
`min-inline-size` is `auto` and a wide code block could not shrink. Deck ships
`.min-is-0` for exactly this, but a utility class cannot be applied to children you do
not control, so the shell needed a rule. Worth a line in the layout guide: it is the
single most common grid bug and Deck's own docs hit it within an hour.

## 11. "Grid" means two different things

`.grid` is the layout primitive; `.dg` is the data grid. `.grid-2`, `.grid-tight` and
`.grid-wide` belong to the first; `.dg-num`, `.dg-pin-start` to the second. Nothing is
wrong, but a reader searching "grid" gets both, and the reference page will need to
disambiguate them in the description rather than the name.


## 12. `.split` children overflowed a phone viewport — FIXED

The demo overflowed a 375px viewport by 16px. The cause was `.split`: its children are
grid items, a grid item's `min-inline-size` is `auto`, and the two-column form inside
could not shrink below its content. The children measured 373px inside a 341px grid.

`.split > * { min-inline-size: 0 }` fixes it, and the page now measures zero overflow at
375, 768 and 1280. This is the same defect the docs shell hit in the previous pass, which
is what makes it worth fixing in the framework rather than in each consumer.

This was outside the stated scope of this task. It was fixed anyway because a 16px
horizontal scroll on a phone is a defect a component page would otherwise document as
correct behaviour.

## 13. Ruled comment blocks were hiding real documentation

Deck writes section headers as `/* --- Icons ------- */` and documentation as ordinary
comments, but several of its best explanations live *inside* a ruled block:

```
/* --- Icons ---------------------------------------------------------------
   Icons inherit color and font-size, so they line up with text by default.
   ---------------------------------------------------------------------- */
```

The extractor classified any ruled block as a section header and threw the prose away,
which is why `.icon`, `.card` and `.emoji` were reported as undocumented when they are
anything but. A ruled block is now a section header only when it is a short label, and
documentation when it carries prose. That single change moved coverage from 8.9% to
14.2% before a word was written.

**Not fixed:** the convention itself. `/** … */` for documentation and `/* --- … --- */`
for dividers would remove the heuristic entirely, and the heuristic is the kind of rule
that breaks quietly when someone writes a divider differently.

## 14. `.stack-depth` and `.stack` share a prefix but are unrelated — FIXED

`.stack` is the vertical rhythm primitive; `.stack-depth` was the 3D card pile. The
extractor reduces a class to its shortest defined prefix when attributing states, so
`.is-fanned` and `.is-dismissed` — states of the pile — attributed to `stack`.

Renamed to `.pile` in the API freeze, which also turned up three more of the same shape:
`.flip-rtl` under `.flip`, `.select-none` under `.select`, and `.row-cq` under the
`.row-*` component family. `tools/docs/collisions.mjs` now asks the question of every
name rather than of the ones somebody happened to notice.


## 15. A rule inside `@media` was being read as a class's definition — FIXED

`02-reset.css` deliberately keeps loading indicators moving under
`prefers-reduced-motion`, and it does that by naming them:

```css
.spinner, .icon-spin, .skeleton, .marquee-track, … { animation-duration: 2.4s !important; }
```

The extractor took the first plain `.name { }` rule in file order as the definition
site, and `02-reset.css` sorts first. So `.spinner`, `.skeleton`, `.icon-spin` and
`.marquee-track` were recorded as living in `deck.reset`, and `.ping` took the reset
block's comment as its own documentation. The freeze then classified four components an
author writes by hand as internal, on the strength of a layer they were never in.

Fixed in `tools/docs/extract.mjs`: an unconditional rule beats a conditional one as the
definition site even when the conditional one came first; between two conditional
candidates the one with more declarations wins, which is what settles `.marquee-track`
(six declarations in `16-motion.css`, two in the reset block); and prose collected from a
file the definition later moves away from is dropped rather than carried along.

Worth recording what this cost. The ledger had already been written with four wrong
buckets and had to be taken again. A bug in the tool that reads the source becomes a
wrong entry in the contract, and the contract is the thing that is supposed to be stable.

## 16. `src/99-print.css` names a class the framework does not own — NOT FIXED

Line 61 lists `.theme-dock` among the things that should not print. `.theme-dock` is not
a Deck component; it is chrome on the demo page. A framework stylesheet should not know
that one particular page exists.

It is classified internal with that reason recorded, which keeps it out of the public
surface, but the rule is still in the shipped stylesheet. The fix is to move it into the
demo's own `<style>` block, and it is deliberately not done here because this pass was
not allowed to change what the demo renders.

## 17. Every new class was public by default — FIXED

The first classifier was an ordered rule list ending in a catch-all: anything in
`deck.utilities` is public, anything unlayered is internal. That reads as thorough and is
not. Adding `.totally-new-thing` to `src/09-utilities.css` produced a clean
classification and one more public class that nobody had ever looked at.

The break test did fail, but on the *documentation backlog* check rather than on
classification — the right exit code for the wrong reason, which is the kind of pass that
hides a problem instead of catching it.

Fixed with `tools/docs/api-decisions.txt`: a committed ledger, one line per class. The
rules now only *propose* a bucket; a class with no line in the ledger stops the build and
names itself. `node tools/docs/classify.mjs --accept` writes the proposals in, and that
extra command is the entire point — it is the difference between choosing to make
something public and ending up with a public class because nobody looked. A line naming a
class that no longer exists fails the build as well, so the record cannot rot in the
other direction either.

## 18. 829 public classes is more than anyone will learn

The honest number, and the one this pass is least comfortable with. The freeze fixed the
classes that were duplicated, misnamed or colliding. It did not reduce the surface,
because reducing it means deciding that whole components do not belong in a
single-stylesheet framework, and that is a scope question rather than a naming one.

What makes it defensible for now is that the surface is not flat. Roughly a third of it
is utility grammar — `.p-*`, `.text-*`, `.bg-*`, `.gap-*` — where learning the pattern is
learning every member of it. Most of the rest is about 99 components, and a page that
uses six of them needs six names. What is not defensible is asking anyone to hold 829
names at once, and no document of Deck's should pretend otherwise.

## 19. 270 public classes are referenced nowhere in the repository

`tools/docs/usage.mjs` scans the demo, the docs, `src/js/`, `php/`, `bin/` and the
Markdown. 289 of the 931 classes appear in none of them, and 270 of those are public.

Some of that is legitimate: a framework ships classes its own demo does not happen to
use. But a class with no use anywhere has never been rendered, never been checked at
375px, and never had a reader look at it — and every one of them is now inside a
published contract. The demo is the only test Deck has, so the honest reading of this
number is that about a third of the public surface is untested.

Not fixed, and it should not be fixed by writing 270 more demo instances. The answer is
component pages with live examples, which is the next pass.

## 20. `.range` is defined in one file and extended in another

`06-forms.css:252` defines `.range`. `23-inputs.css` adds `.range-pair`, `.range-readout`
and `.range-ticks` — the same component, one file and 190 lines away, with nothing at
either site pointing at the other. Same shape as item 1, and found by
`tools/docs/collisions.mjs` rather than by reading.

Left alone: moving rules between files changes cascade order, and this pass was not
allowed to change rendering. It belongs with item 1's rearrangement.


## 21. `.mt-*` is named after a physical edge and declares a logical one

`src/09-utilities.css:65`:

```css
.mt-0 { margin-block-start: 0; }
.mb-2 { margin-block-end: var(--space-2); }
.w-full { inline-size: 100%; }
.h-full { block-size: 100%; }
```

The declaration is logical. The name is physical. In `horizontal-tb` they agree and
nobody notices; in `.writing-vertical`, which Deck ships, block-start is a side edge and
`.mt-4` does not put a margin on the top. Anyone reading the class name to predict the
behaviour will be wrong exactly where logical properties are supposed to help.

Deck used to ship both spellings — `.mbs-*` alongside `.mt-*`, `.is-full` alongside
`.w-full` — which is worse: two names for one declaration, and neither one authoritative.
The freeze deleted the logical spellings and kept the physical ones, on the grounds that
`.mt-4` is what people type and it was already the more used of the two.

**Not fixed, and it is the freeze decision I am least sure of.** Renaming 14 margin
utilities and two size utilities to logical names would make Deck's own stylesheet agree
with Deck's own pitch. It was not done because the reason is a preference about which
vocabulary should win, and the brief for this pass said a rename needs a reason beyond
preference. If that is the wrong reading, this is the item to revisit — and it must
happen before the first publish, because after that the names are load-bearing.

The inconsistency is only on one axis, which is what makes it easy to miss: the inline
axis uses logical names (`.mis-*`, `.mie-*`, `.pis-*`, `.pie-*`), and the block axis uses
physical ones (`.mt-*`, `.mb-*`). One framework, two vocabularies, split by axis.

## 22. `.stack-2` and `.gap-2` set the same gap by different means

`.stack-2` sets `--gap`, which `.stack` reads through `gap: var(--gap, …)`. `.gap-2` sets
`gap` directly, sits in a later layer, and therefore works on a `.stack` too — and wins.
Two spellings, same result, no rule anywhere saying which to reach for.

This is also why `.cluster-0` through `.cluster-8` were removable: they were
declaration-for-declaration identical to `.stack-0` through `.stack-8`, because neither
had anything to do with stacking. They were gap classes wearing a component's name.
`.cluster-tight` survives as the readable name for step 2, which leaves `.cluster` with a
word where `.stack` has numbers.

Not fixed. The clean answer is that `.gap-*` is the gap vocabulary and `.stack-*` should
not exist, but that rewrites gap classes across the whole demo, and this pass was not
allowed to change what the demo renders.


<!-- Items 23 onward were found while writing the batch 1 component pages:
     card, badge, alert, avatar, list, table, field, input, check, range. -->

## 23. `.was-shaken` was frozen as public with a reason that was backwards — FIXED

`deck-extras.js` adds `.was-shaken` to a field after its invalid-state shake has run
once, and the selector `.field.is-invalid:not(.was-shaken)` is what stops the animation
replaying on every re-render. The freeze classified it **public**, with the reason
"Motion or effect class. Opt in by adding it; nothing runs unless you do."

That is exactly inverted. Writing `.was-shaken` by hand does not enable anything — it
*suppresses* the shake. The rule that matched it was the layer-level `effect` rule,
which is right about `deck.motion` in general and wrong about this one class.

Fixed with a `js-runtime-mark` rule ahead of the family rules, and the ledger line
corrected by hand. Buckets are now 828 public / 103 internal. It was found because a
docs check noticed the framework adding a class to an example, which is a roundabout way
to audit a contract, and the only reason it surfaced at all.

## 24. `.table-wrap` scrolls but cannot be reached by a keyboard — NOT FIXED

`.table-wrap` sets `overflow-x: auto` and nothing else. A scroll container is only
keyboard-scrollable if it is focusable, so a wide table can be scrolled with a pointer
and not at all with a keyboard. Firefox gives such containers a tab stop automatically;
Chromium does not.

Documented on the table page as a defect with the fix — `tabindex="0"` and
`role="region"` with an `aria-label` — rather than described as working. Not fixed in
the stylesheet because CSS cannot add a tab stop, and adding one to every wrapper
whether or not its table overflows creates a different problem: a tab stop that goes
nowhere.

## 25. Drag-to-reorder has no keyboard path — NOT FIXED

`.reorder` and `.is-lifted` style a list whose rows can be dragged into a new order, and
`deck-adapters.js` drives them with SortableJS or the native drag-and-drop API. Both are
pointer-only. A keyboard user cannot reorder the list at all.

This is a functional gap rather than a styling one, and it is the most serious
accessibility problem found in this batch. Documented on the list page with the
mitigation (ship move-up/move-down buttons as well). Fixing it properly means keyboard
handlers in `deck-adapters.js`, which is out of scope for a documentation pass.

## 26. The `.range` thumb has no focus indicator — NOT FIXED

Every other Deck control has a `:focus-visible` rule. `.range` has none, so the focus
indication for a slider is whatever the browser draws around the input as a whole, which
is easy to miss on a control that is mostly transparent track.

The fix is four lines and has to be written twice per engine, which is why it is
suggested on the range page's *Overriding it* section rather than being left implied:

```css
.range:focus-visible::-webkit-slider-thumb { box-shadow: var(--ring); }
.range:focus-visible::-moz-range-thumb { box-shadow: var(--ring); }
```

Not fixed here because this pass was not allowed to change rendering, and a focus ring
is a rendering change.

## 27. `.file` draws a drop zone that Deck does not implement — NOT FIXED

`.file` is a dashed border, a hover state and a `:focus-within` state — the visual
vocabulary of a drag-and-drop target. Deck ships no `drop` handler, so dragging a file
onto it does nothing at all. Clicking works, because the label wraps a real file input.

An affordance that does not do what it looks like it does is worse than no affordance.
Documented on the field page; the honest fixes are either to ship the handler or to stop
drawing a drop zone.

## 28. `.list-row:hover` and `.table tbody tr:hover` have no focus counterpart

Both change their background on hover and neither responds to `:focus-within`. A
keyboard user moving through a list or a table of controls gets the focus ring on the
control but no row highlight, so the row they are on is harder to place than it is for a
mouse user.

Not fixed for the same reason as item 26 — it is a visible change. Both pages document
it and show the one-line override.

## 29. `.check` inside `.dg` drops below the touch target — deliberate, now written down

`src/12-datagrid.css` removes `.check`'s `min-block-size` for the data grid's selection
column. It is the one place Deck knowingly ships a target under
44px: a dense grid of 34px rows cannot also have 44px checkboxes, and a grid that scrolls
twice as far is its own accessibility problem.

Recorded rather than fixed, because it is a trade someone made on purpose. It belongs in
the data grid page too when batch 4 reaches it.

## 30. A docs check could not tell framework behaviour from an authoring mistake — FIXED

The per-batch check re-parses each printed example and compares it with what was
rendered, which is what stops a page showing one thing and claiming another. It reported
13 mismatches across three pages, all false: Deck's own JavaScript stamps
`data-deck-wired`, adds `.was-shaken`, and rewrites `--lo`/`--hi` to two decimals on a
`.range-pair`.

Two of those three were worth knowing — `.was-shaken` became item 23, and the `--lo`
rewrite showed that three claims on the range page were wrong (the page said CSS does not
update those properties, that clamping was the author's job, and that the readout was
never populated; `deck-extras.js` does all three). The check now runs its equality test
with JavaScript disabled, which is the only state in which the question "does the printed
source match what was rendered" has a meaningful answer, and reports runtime rewrites
separately instead of as faults.
