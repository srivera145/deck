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

**Wider than one class.** Writing the scroller page in batch 2 found the same hole in
`.scroller`, and the same reasoning applies to any `overflow: auto` container Deck ships.
`.scroller` is the milder case, because its children are usually links or cards that take
focus and drag the row along with them — but a scroller of plain content is unreachable
by keyboard in Chromium exactly as a wide table is. Both pages now document it; the
general fix is a convention, not a rule: any scroll container whose contents are not
focusable needs `tabindex="0"` and a labelled `role="region"`.

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

## 27. `.file` draws a drop zone that Deck does not implement — FIXED

`.file` is a dashed border, a hover state and a `:focus-within` state — the visual
vocabulary of a drag-and-drop target. Deck ships no `drop` handler, so dragging a file
onto it does nothing at all. Clicking works, because the label wraps a real file input.

An affordance that does not do what it looks like it does is worse than no affordance.
Documented on the field page; the honest fixes are either to ship the handler or to stop
drawing a drop zone.

**Fixed by shipping the handler**, in `src/js/deck-extras.js`. Dragging files over a
zone sets `.is-over` (counted across the zone's children, so crossing the icon does not
flicker it off), which `src/06-forms.css` now styles as a solid border with the focus
ring. A drop goes into the zone's input as a pick would: filtered by `accept`
(extensions, `type/*` and exact types — the picker enforces `accept` and a drop bypasses
it), cut to one file unless the input is `multiple`, then `input` and `change`, bubbling.
`.is-over` was reused rather than adding a class because `file.php` had already told
authors to style `.file.is-over` from a handler of their own. For the same reason the
listeners are on the document: they run after any handler on the zone, and a drop that
handler has already taken (`preventDefault`) is left alone, so a page that followed the
old advice does not get two `change` events. Delegation also means a zone inserted later
needs no `Deck.init()`. Tests cover the state, the filter, `multiple` and the hand-off.

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


<!-- Items 31 onward were found while writing the batch 2 layout pages:
     stack, cluster, grid, split, container, scroller. -->

## 31. `.grid-2` and `.grid-wide` are the same grid, and one of them does not compose

`.grid` sizes its columns with `minmax(min(var(--min, 17rem), 100%), 1fr)`, so
`.grid-wide` — which sets `--min: 24rem` and nothing else — produces
`minmax(min(24rem, 100%), 1fr)`.

`.grid-2` writes that same value straight into `grid-template-columns`. The two are
identical in effect, and it is not obvious from the names that they are: `.grid-2` reads
as "two columns" and is not — it is a 24rem floor, which gives two columns at some widths
and three at others.

The difference that matters is that `.grid-wide` composes and `.grid-2` does not. Setting
`--min` afterwards adjusts `.grid-wide` and has no effect on `.grid-2`, because the
latter has already resolved the property away.

**Not fixed.** Removing `.grid-2` is a deletion after the freeze, and the freeze pass
happened one task ago; it should have been caught then and was not, because nothing
compares two classes for equivalent computed output. Documented on the grid page with a
recommendation to prefer `.grid-wide`, and a source comment now says so above the rule.
A check that flags two public classes with identical effective declarations would have
found this and is worth writing.

## 32. `.scroller` has no print rule, so a printed scroller loses most of its content

Every other layout primitive is accounted for in `src/99-print.css`: `.grid` and
`.split` are unwound to `display: block`, `.container` loses its cap and gutter,
`.section` loses its top padding. `.scroller` is not mentioned, which means it prints as
an overflow container — showing whatever happened to be scrolled into view and silently
dropping the rest.

A row of eight date tiles prints as three. Nothing warns anybody, on screen or on paper.

**Not fixed**, because this pass was not allowed to change rendering and a print rule is
a rendering change. The fix is small and belongs with the next `src/` pass:

```css
@media print {
  .scroller { display: block; overflow: visible; margin-inline: 0; padding-inline: 0; }
  .scroller > * + * { margin-block-start: 4mm; }
}
```

Documented on the scroller page as a gap rather than described as working.

## 33. Hiding the scrollbar on `.scroller` trades an affordance for an appearance

`.scroller` sets `scrollbar-width: none` and hides the WebKit scrollbar. A scrollbar is
the only persistent, platform-standard signal that a region scrolls; with it hidden, the
affordance is a partially-visible item at the edge — which works when the items are wide
and disappears entirely when they happen to fit the container exactly.

This is an aesthetic decision overriding a usability one. It is defensible — a native
scrollbar under a row of cards does look like a mistake on desktop, and the pattern is
near-universal — but it is a decision, and until now it was not written down anywhere.

**Not fixed**, deliberately. Recorded so that it is a choice rather than an accident, and
documented on the scroller page under Accessibility with the override that gives the
scrollbar back.

## 34. `.split`'s breakpoint is the right answer to the wrong question inside a container

`.split` carries a `@media (min-width: 64rem)`, one of very few media queries left in
Deck. That is correct when the split is the outermost layout on a page, which is what it
is designed for.

Inside a modal, a drawer or another rail it is wrong in the way container queries exist to
fix: the viewport says there is room for two columns and the actual container has room
for one. Deck already ships the container-query answer for the equivalent form problem —
`.field-row-cq`, whose source comment calls it "the one that keeps biting people" — but
there is no `.split-cq`.

**Not fixed.** Adding one is a new public class after the freeze, which is a bigger
decision than a documentation pass should make. Documented on the split page under both
*The one breakpoint* and *When not to use it*.


<!-- Items 35 onward were found while splitting six topics out of their host
     pages into their own: bar, section, textarea, select, switch, file,
     fieldset. -->

## 35. Folding a component into a host page made it unfindable — FIXED

`.bar`, `.section`, `.textarea`, `.switch`, `.file` and `.fieldset` were all documented,
all correct, and none of them were in the sidebar. Each had been folded into the page for
a larger neighbour on the grounds that a reader choosing between a cluster and a bar wants
both explanations in one place.

That reasoning is fine for the reader who is already on the cluster page. It is no help
at all to the reader who knows the word "switch" and is looking for it, which is the more
common way somebody arrives at reference documentation. A topic that is not in the
navigation does not exist.

Fixed by giving each of the six its own page, moving the `documents` claim with it, and
leaving a short cross-reference where the section used to be — so the comparison is still
on the page a reader was reading, and the topic is still findable on its own. The
Components navigation is now sorted alphabetically for the same reason.

**The rule this suggests:** if a class is worth a section heading, it is worth a page. The
cost of a short page is much lower than the cost of a reader not finding it.

## 36. Four classes were documented in prose but claimed by no page — FIXED

`.textarea`, `.select`, `.addon` and `.search` were all explained at length on the input
page, and none of them appeared in any page's `documents` array. They were therefore still
counted as undocumented in the coverage figures, and `verify.mjs` had nothing to check
them against — a rename would not have been caught.

This was a reporting error on my part in batch 1: I said they were "documented in prose
but belong to no component root, so they are not in the table", which was a decision about
the generated *table* that I let become a decision about the *claim*. The two are separate,
and only the claim is load-bearing.

Fixed: `.textarea` and `.select` now have their own pages, and `.addon` and `.search` are
claimed by the input page with a comment saying why they are there.

## 37. `.bar` is the only layout primitive that ignores `--gap` — NOT FIXED

`.stack`, `.cluster`, `.grid`, `.split`, `.scroller` and `.center` all declare
`gap: var(--gap, …)`, which is what lets the spacing scale classes work on any of them.
`.bar` declares `gap: var(--space-3)` with no custom property, so `.stack-2` on a bar does
nothing and the gap can only be changed by setting `gap` directly.

Nothing about `.bar` makes it different in kind; it looks like an omission rather than a
decision. The fix is one word — wrap the value in `var(--gap, …)` — and it is
backward-compatible, since the fallback keeps the current default.

**Not fixed** because it is a rendering change on a public class and this pass was
documentation. Documented on the bar page under *Gap*.

## 38. `.select[multiple]` paints a chevron with nothing to drop down — NOT FIXED

`.select` sets `appearance: none` and paints a chevron in the top-right with two
gradients. With `multiple` or `size="4"` the element renders as a list box rather than a
dropdown, and the chevron is painted over the first option — pointing at a dropdown that
does not exist, and overlapping the text.

One rule fixes it:

```css
.select[multiple], .select[size]:not([size="1"]) {
  background-image: none;
  padding-inline-end: var(--space-3);
}
```

**Not fixed** for the same reason as item 37. Documented on the select page, alongside the
larger point that a multi-select is usually the wrong control anyway.

## 39. `.switch` has three numbers that must agree and nothing that keeps them agreeing

The track is `46px × 28px`, the knob is `22px`, its inset is `3px`, and the checked state
translates it `18px`. Those numbers are related — `18 = 46 − 22 − 3 − 3` — and every one
of them is written separately. Changing the switch's size means getting four
declarations right by hand, and a wrong one shows up as a knob that stops short of the
end or slides past it.

A single `--switch-size` with the rest derived by `calc()` would make the component
resizable the way `.avatar` is resizable by `--size`.

**Not fixed**, and recorded because it is the kind of thing that is obvious while writing
the override example and invisible otherwise. Documented on the switch page under
*Overriding it*.

## 40. A disabled `<fieldset>` does not look disabled

`<fieldset disabled>` is a genuinely useful HTML feature: it disables every control
inside, including ones added later, and removes them from the tab order properly. Deck
styles the controls — they get their own disabled treatment — but nothing styles the
group. The border and the legend look exactly as they did, so the reader sees a normal
box full of greyed-out controls rather than a group that is off.

`.fieldset[disabled] { opacity: .6; }` is the whole fix.

**Not fixed**, same reason. Documented on the fieldset page under *Disabling a whole
group*.

## 41. Three print sections described behaviour the print stylesheet does not have — FIXED

Writing the textarea page, I asserted that `.textarea` was missing from
`src/99-print.css` and that the fix was to clear `max-block-size`. Checking before
publishing the claim found the opposite: `.textarea` is in the shared control rule *and*
has a dedicated line doing exactly that.

Checking that turned up two more of my own errors from the same wrong assumption:

- The input page said `.input` prints as "an underlined value" with the box removed. It
  does not; it gets a `#999` border on white with `min-block-size: auto`.
- The field page said `.form-actions-sticky` is un-stickied "so the buttons print where
  the form ends". They do not print at all — `.btn, .form-actions { display: none }`,
  with `.btn.print-keep` as the escape hatch.

All three corrected. The lesson is narrow and worth stating: **a print rule is the
easiest thing in a stylesheet to describe from memory and the least likely to be checked
by a reader**, because almost nobody prints a page to verify the documentation. Print
claims need reading the source every time, not recalling it.


<!-- Items 42 onward were found while writing the batch 3 interactive pages:
     modal, drawer, sheet, menu, tooltip, popover, tabs, accordion, segmented,
     toast. -->

## 42. Three components ship an ARIA role's appearance without its behaviour

`.tabs` and `.segmented` are both styled from `[aria-selected="true"]`, which is the
right way round — the visual state and the accessible state are one attribute and cannot
drift. But `aria-selected` only means anything inside a `role="tablist"`, and a tablist
carries obligations Deck does not meet:

- one tab stop for the whole strip, via a roving `tabindex`
- arrow keys between tabs, following the writing direction
- Home and End
- `aria-controls` and `role="tabpanel"` wiring

Deck ships none of it, and no JavaScript for any of it. A page that uses the roles
without adding the keys has a control that *announces* itself as a tablist and then
behaves like a row of separate buttons — which is worse than no role at all, because a
screen-reader user is told to expect arrow keys that do nothing.

**Not fixed.** Both pages now document the full keyboard pattern with working code, and
both say plainly that the alternative — links with `aria-current`, or radio inputs — is
often the better choice because the platform supplies the behaviour. But a framework that
styles `[aria-selected]` is inviting the roles, and it should either ship the script or
not style the attribute.

The same shape appears a third time in `.menu`, which deliberately does *not* use
`role="menu"` for exactly this reason. That is the right call, and it is worth noting the
inconsistency: the menu page refuses the role it cannot support, while tabs and segmented
style for a role they cannot support either.

## 43. `.segmented` does not style its own no-JavaScript version

A segmented control built from radio inputs is strictly better than one built from
buttons: arrow keys, group labelling from a `<legend>`, and form submission all come free
from the platform. The source comment calls it "iOS-style, good on a phone", and the
markup is obvious.

It does not work, because the selected-state rule keys off `[aria-selected="true"]` and
`.is-active` only. A radio version renders with every option looking unselected.

```css
/* the missing rule */
.segmented > label:has(:checked) {
  background: var(--surface);
  color: var(--text);
  box-shadow: var(--shadow-1);
}
```

**Not fixed** — a rendering change on a public class, in a documentation pass. Documented
on the segmented page with the rule to paste, and flagged as the version to prefer
despite the gap.

## 44. `.sheet-grip` promises a gesture that does not exist

The grip is the platform convention for "drag this panel down to dismiss". Deck draws it
and implements no drag: swiping a sheet down does nothing, and `touch-action` is not set
on the sheet either, so there is nothing to build one on.

Same shape as item 27, the file drop zone. Milder, because a grip also reads as a
decorative handle and a close button is usually present — but a reader who tries the
gesture gets no feedback at all.

**Not fixed.** Documented on the sheet page, which also says what implementing it would
take.

## 45. `.accordion` content that is closed does not print

A closed `<details>` renders nothing, so it prints nothing. For a page whose accordions
*are* the content — an FAQ, a policy, a terms page — most of the page is missing from the
printed copy, and nothing indicates that anything was omitted.

`src/99-print.css` has no `.accordion` rule. The closest fix is forcing the content
region open for print:

```css
@media print {
  .accordion details::details-content {
    block-size: auto !important;
    content-visibility: visible !important;
  }
}
```

That reveals the content but leaves every chevron pointing "closed", so the chevron should
be hidden in the same block.

**Not fixed**, and worth prioritising over the other print gaps: unlike a scroller, an
accordion routinely holds the substance of a page rather than a row of tiles.

## 46. Four components ship touch targets under 44px

Documenting the interactive set turned this into a pattern rather than a series of
one-offs:

| Component | Target | Why |
| --- | ---: | --- |
| `.menu-item` | 40px | A menu of eight items would otherwise be very tall |
| `.segmented > *` | 36px | Full-width control; the horizontal target is generous |
| `.dg .dg-check .check` | ~34px | A dense grid cannot have 44px checkboxes |
| `.toast-close` | 26px | Mitigated by the whole toast being swipeable |

Each is defensible on its own and each is now documented on its page. What was not
visible before is that there are four of them, which makes it a house style rather than
four exceptions. Whether that is the right house style is a question worth answering
deliberately — the alternative is a `--tap-dense` token that names the smaller value, so
the decision is made once and visible.

**Not fixed.** Recorded so the pattern is at least legible.

## 47. `.tip` is a tooltip that opens on click — FIXED in the documentation only

`.tip` is a popover, and a popover opens on click. A tooltip that requires a click is not
a tooltip; the pattern readers expect is hover and focus.

Deck ships no script to open it on `pointerenter`, so out of the box `.tip` behaves as a
small popover. The CSS-only `.tooltip` has the opposite problem — it opens on hover and
focus for free, and cannot flip, escape an overflow, or appear on touch at all.

Neither is wrong; between them they cover the cases. But a reader arriving at "tooltip"
needs to be told which one they are getting, and until now nothing said so.

**Documented rather than fixed:** the tooltip page now leads with the comparison and a
table of what each can do. A few lines of JavaScript would give `.tip` hover-and-focus
behaviour and make it the better default for both.

## 48. `translate` and `transform-origin` have no logical form, and three components pay for it

Deck's RTL story is that logical properties handle almost everything and the exceptions
are collected in `src/19-logical.css`. Batch 3 found how many of those exceptions are the
same two properties:

- `.drawer` slides with `translate: -100% 0`; `.drawer-end` declares `translate: 100% 0`
  separately so the pair is correct by construction.
- `.switch` moves its knob with `translate: 18px 0`, negated for RTL in `19-logical.css`.
- `.toast-timer` drains from `transform-origin: left center`, flipped for RTL in the same
  file alongside chart bars and progress fills.

The switch is the one that bites: the travel distance is a magic number written **twice**,
once in the component and once negated. Change the switch's size and forget the RTL copy,
and it breaks only for readers of Arabic and Hebrew — the least likely case to be tested.

**Not fixed.** Both pages now say so explicitly, and the switch's override example shows
all four numbers including the RTL one. A `--switch-size` with derived values would remove
the magic numbers; a linter rule that flags a `translate` on the inline axis without a
matching `[dir="rtl"]` rule would catch the class of bug.


<!-- Items 49 onward were found while writing the batch 4 pages: datagrid,
     datepicker, combobox, carousel, kanban, editor, chat, stepper, charts. -->

## 49. Drag-to-reorder is the only interaction in Deck with no keyboard path — and kanban makes it critical

Item 25 recorded this for `.list`, where the order is usually cosmetic. On a kanban board
it is not cosmetic: moving a card between columns **is the data**, and it is the entire
purpose of the component. A keyboard-only user cannot use a Deck kanban board at all.

`.kanban`, `.list` and `.dg` all share the drag styling in `src/26-perf.css` and all are
driven by `deck-adapters.js` through SortableJS or the native drag-and-drop API. Both are
pointer-only.

**Not fixed.** The kanban page documents it as the component's headline problem and
recommends a per-card move menu — which is also faster than dragging on a large board.
The general fix is keyboard handlers in `deck-adapters.js`: pick up with Space, move with
arrows, drop with Space, cancel with Escape, and announce each move in a live region.

Of everything found across four batches, this is the item I would fix first.

## 50. Six scroll containers ship without a keyboard path

Item 24 started with `.table-wrap` and item 34's note widened it to `.scroller`. Batch 4
makes it six:

| Container | Consequence |
| --- | --- |
| `.table-wrap` | A wide table cannot be scrolled by keyboard in Chromium |
| `.scroller` | Same, when the items are not focusable |
| `.dg-wrap` | Same, and it is the component most likely to be wide |
| `.kanban` | The board axis |
| `.kanban-body` | Each column |
| `.stepper` | A strip of more than about five steps |
| `.tabs` | Mitigated: tabs are focusable, so focus drags the strip |

Firefox gives scroll containers a tab stop automatically; Chromium does not. Every one of
these needs `tabindex="0"` and a labelled `role="region"` when its contents are not
focusable — and Deck cannot add it from CSS.

**Not fixed**, but it has crossed from "a defect in one component" to "a convention Deck
is missing". The right answer is probably a documented `.scroll-region` utility that pairs
the overflow with the guidance, so the decision is made once rather than seven times.

## 51. `.editor` uses `document.execCommand`, which is deprecated

Formatting in the rich-text editor is applied with `document.execCommand`. It is
deprecated, unmaintained, and produces different markup per engine — bold may be `<b>`,
`<strong>` or a `<span style>` depending on the browser and what was already applied.

There is no replacement: the standards work that would have superseded it stalled, and
real editors reimplement editing over their own document model instead.

**Not a defect to fix so much as a limit to state**, and until now it was not stated
anywhere. The editor page now leads with it, says plainly that the stored HTML is
inconsistent, and requires server-side sanitising rather than suggesting it. Anyone
choosing the component should choose it knowing that.

## 52. ~~`.editor`'s toolbar and footer print~~ — WITHDRAWN, this was wrong

**This finding was incorrect and is retracted.** `.editor-toolbar` and `.editor-footer` are
already dropped when printing, and `.editor-content` already has its height cap and
overflow removed so a long document prints in full:

```css
/* src/24-media.css, at the end of the file */
@layer deck.print {
  @media print {
    .editor-toolbar, .editor-footer { display: none !important; }
    .editor-content { max-block-size: none !important; overflow: visible !important; }
  }
}
```

**How the mistake was made:** I searched `src/99-print.css` for `.editor`, found nothing,
and concluded there was no print handling. Deck has **two** `@layer deck.print` blocks —
the main sheet, and a second one at the end of `src/24-media.css` that keeps the media and
messaging components' print rules beside the components themselves. Grepping one file
answers the question for some components and not others.

**The real lesson, which is worth more than the finding was:** "there is no rule for X"
is a claim about the whole stylesheet, and it cannot be checked by reading one file. Four
component pages — editor, chat, drawer and video — carried print claims built on the same
incomplete search, and all four were wrong in the same direction: they described Deck as
having neglected something it had actually handled deliberately. All are now corrected.

## 53. `.carousel` and `.kanban` print only what was scrolled into view

Item 32 recorded this for `.scroller`. It is the same rule and the same fix for
`.carousel` — a printed carousel shows one slide — and worse for `.kanban`, which nests
two scroll containers, so a printed board loses both the columns that were off-screen and
the cards that were scrolled past inside the visible ones.

```css
@media print {
  .scroller, .carousel-track, .kanban, .kanban-body {
    display: block; overflow: visible; margin-inline: 0;
  }
  .carousel-slide + .carousel-slide, .kanban-col + .kanban-col { margin-block-start: 4mm; }
}
```

**Not fixed.** Recorded together because one rule covers all three, and because the
pattern — "an overflow container silently truncates on paper" — is now the single most
common print defect in the framework.

## 54. The chart palette is hue-rotated, which is the axis colour-blind readers lose

`.chart` computes five of its six series colours by rotating `--hue-brand` and holding
lightness and chroma roughly steady. That is a genuinely good decision for coherence: the
palette rethemes with the brand and every series obviously belongs to the same family.

It is also close to the worst possible choice for discriminability. Holding lightness
constant and varying only hue produces colours that a reader with deuteranopia or
protanopia may not be able to tell apart at all — and a six-series chart is exactly where
telling them apart matters.

The source is already candid about a related limit, in the `contrast-color()` comment:
*"Series colours are chosen for discriminability and some sit in the 55-65% band, so this
raises the floor rather than guaranteeing AA."*

**Not fixed**, and arguably not a bug — the trade is real and coherence has value. But it
should be a stated trade rather than an implicit one. The charts page now says so, and
recommends labelling marks directly or using a hand-picked categorical palette when a
chart has more than three series. A shipped `.chart-categorical` with varied lightness
would make the accessible choice the easy one.

## 55. A CSS chart is invisible to a screen reader, and nothing in Deck says so — FIXED in documentation

Every chart in `src/14-charts.css` is `<div>`s and custom properties. There is no role, no
label, no table, and no text alternative. A screen-reader user gets *nothing at all* from a
Deck chart unless the author supplies it.

That is not unusual — it is true of most CSS charting techniques — but it was undocumented,
and a framework that ships forty-eight chart classes without mentioning it is inviting an
inaccessible dashboard.

**Documented rather than fixed**, because the fix is markup the author has to write. The
charts page now leads its accessibility section with it and gives the hierarchy: a
`sr-only` table beside the chart is best, `role="img"` with a real summary is acceptable
for a single-value chart, and nothing is not an option.


## 56. The date field opens the calendar but never closes it, and carries an inert attribute

Two small things in `DatePicker`, both found by a reader asking whether clicking the date
was supposed to open the calendar. It is — and the docs page only showed static markup, so
there was no way to tell.

**The field is open-only.** `bind()` does:

```js
this.input.addEventListener('click', () => this.panel.showPopover());
```

`showPopover()`, not `togglePopover()`. So clicking the field opens the panel and clicking
it again does nothing. Escape and an outside click both close it, so nothing is
unreachable — but a reader who clicks the field to dismiss it is left wondering. Measured:
first click opens, second click leaves it open, Escape closes.

**`popovertarget` on the input is inert.** `build()` sets it, but the attribute is only
honoured on `<button>` and button-type inputs — on a text input the browser ignores it.
It is dead markup that misleads anyone reading the DOM, and it is why the click handler
exists at all. (`aria-haspopup="dialog"`, set beside it, *is* meaningful and should stay.)

**Not fixed** — a behaviour change in a documentation pass. One word fixes the first
(`togglePopover`) and one deletion fixes the second. Both documented on the datepicker
page, which now also carries a live picker rather than only static markup.

## 57. A page can show only static markup and nobody notices

The datepicker page had one example, and it was deliberately static — written that way so
the clear button would be visible without JavaScript setting `.has-value`. Every automated
check passed: the example rendered, matched its printed source, used real classes, and the
component's completeness was satisfied.

None of that could tell that the page never demonstrated the component *working*. A reader
clicked the field, nothing happened, and reasonably concluded the component was broken.

The checks verify that an example is **live** in the sense of being rendered rather than
screenshotted. They cannot verify it is **wired**. For components whose behaviour comes
from `deck.js`, a page needs at least one example carrying the `data-deck-*` attribute
that switches the behaviour on — and that is now a rule worth applying to every page in
batch 4, where nearly every component has a script behind it.

**Partly fixed:** the datepicker page now leads with a live picker and labels the static
one as not wired. A check that flags a page documenting a `data-deck-*` component without
using that attribute in any example would catch the rest.


## 58. JavaScript placement fought CSS anchor positioning and double-offset two panels — FIXED

`src/25-anchor.css` gives `.datepicker` and `.mega` a `position-anchor` and a
`position-area`, which changes what their inset properties mean: they are resolved
against the anchored region rather than against the viewport.

Both components also computed viewport coordinates in JavaScript and wrote them as inline
insets — `DatePicker.position()` and `megaMenus.place()`. The region then applied that
offset a second time. Measured on a field at `y=454`: the panel landed at `y=512` instead
of `y=462`, and the further down the page the field sat, the further away the panel
drifted. On a long page it ended up in the opposite corner.

The stylesheet already tried to prevent this — it sets `inset: auto` on those selectors
for exactly this reason — but an inline style beats a stylesheet, so the guard has to be
in the script.

**Fixed.** Both now bail out where the platform does the work:

```js
if (CSS.supports('anchor-name: --a')) return;
```

That is the pattern `Grid` already used for scroll-state queries, where the JavaScript
fallback only runs if the CSS feature is missing. Two of the three progressive-enhancement
pairs in Deck had the guard; these two did not.

Verified: no inline insets are written, and a field at `x=50, bottom=454` now anchors its
panel at `x=50, y=462` — edge-aligned, eight pixels below, and flipping above when there
is no room, which is the `position-try-fallbacks` chain working as documented.

**What made it hard to see:** every automated check passed. The panel opened, the
component was complete, the example was live and wired. Nothing measures *where* a
floating element lands relative to its trigger, and that is the one thing a positioning
component exists to get right.

## 59. A pseudo-element inside `:is()` is silently dropped, and one RTL rule relies on it

`src/19-logical.css` flips three things that grow from the start edge:

```css
[dir="rtl"] :is(.chart-bar-fill, .progress::-webkit-progress-value, .toast-timer) {
  transform-origin: right center;
}
```

A pseudo-element is not a valid argument to `:is()`, and `:is()` uses forgiving selector
parsing — an invalid item is dropped rather than invalidating the rule. So the browser
keeps the other two and throws the progress fill away. Read back through `cssRules` in
Chrome, the rule serialises as:

```
[dir="rtl"] :is(.chart-bar-fill, .toast-timer)
```

**Nothing is broken by it.** The fill is animated with `inline-size`, not a transform, so
`transform-origin` would have been a no-op even if it had applied; and the engine mirrors
`<progress>` under `dir="rtl"` on its own. Screenshotted at `value="30"` with the dropped
rule and with a correctly-written `[dir="rtl"] .progress::-webkit-progress-value` rule
beside it: the two fill from the right identically.

The danger is not this line, it is the shape of it. Forgiving parsing means a mistake
inside `:is()` never announces itself — no console warning, no failed rule, no visual
change. The next person who adds a pseudo-element to a grouped selector will get the same
silence with a component that *does* need the declaration.

**Recommended:** delete `.progress::-webkit-progress-value` from the list. It is dead
weight that reads as coverage. If a vendor pseudo-element ever does need a logical flip,
it has to be its own rule — as `.progress` and `.range` already do for every other
declaration, for the same reason.

**How it was found:** writing the "Right to left" section of `progress.php` and checking
the claim against the source instead of restating the comment above the rule.

## 60. `.ring` styles a child that its own mask guarantees nobody will see

`.ring` in `src/07-components.css` sets four declarations for a label in the middle:

```css
display: grid;
place-items: center;
font-size: var(--text-xs);
font-weight: 620;
font-variant-numeric: tabular-nums;
```

and then, four lines later:

```css
mask: radial-gradient(farthest-side, transparent calc(100% - var(--thickness)), #000 calc(100% - var(--thickness) + 1px));
```

A mask applies to an element **and its entire subtree**. The middle of the gradient is
transparent, so a `<span>` centred inside a `.ring` is masked out along with the hole — it
renders, it takes up space, it is in the accessibility tree, and it is completely
invisible. Screenshotted side by side: `<div class="ring">68%</div>` shows an empty ring.

This is already known in the source, because the fix is sitting right below it:

```css
.ring-wrap { position: relative; display: inline-grid; place-items: center; }
.ring-wrap > .ring-label { position: absolute; font-size: var(--text-xs); font-weight: 620; font-variant-numeric: tabular-nums; }
```

`.ring-label` repeats all three typography declarations, which is the tell — the same
three, on an unmasked sibling, because on the ring they never took effect.

**Recommended:** drop `place-items`, `font-size`, `font-weight` and
`font-variant-numeric` from `.ring`. They cost nothing at runtime and cost a reader real
time: they are an invitation to put a number inside the ring, which is the one thing that
cannot work. `display: grid` can stay or go — with no visible children it does nothing
either way, but it is the least misleading of the five.

**How it was found:** writing the ring section of `progress.php`, noticing that `.ring`
was dressed for a label it could not show, and screenshotting both approaches rather than
assuming which one worked.

## 61. `.is-invalid` means two different things depending on the wrapper

`src/06-forms.css` reaches into the control:

```css
.input:user-invalid, .textarea:user-invalid, .select:user-invalid,
.field.is-invalid :is(.input, .textarea, .select) { /* red border, red ring */ }
```

`src/23-inputs.css` does not:

```css
.float.is-invalid > label,
.float > :user-invalid ~ label { color: var(--bad-700); }
```

So `.field.is-invalid` reddens the border *and* whatever error text you supply, while
`.float.is-invalid` colours the label and leaves the control looking untouched. Same class
name, same position on the wrapper, different amount of work — and the weaker one is on the
component where the label is the *only* thing that would have carried the signal, because a
floating label sits inside the field.

`:user-invalid` is not affected: it matches the control directly through the
`06-forms.css` rule, so a genuinely-invalid `.float` does get its border. The gap is only
in the manual override, which is exactly the path used for server-side errors — the errors
the browser cannot detect and the reader most needs pointed out.

**Recommended:** add the missing rule.

```css
.float.is-invalid :is(.input, .textarea, .select) { /* same treatment as .field */ }
```

Better still, write one rule that covers both wrappers, so the next wrapper Deck adds gets
it for free rather than needing to remember.

**How it was found:** writing the invalid section of `float.php` and checking what
`.is-invalid` actually selects, rather than assuming the two wrappers behaved alike because
they share a class name.

## 62. `.phone` silently drops a digit when you edit the middle of a number

`phones()` in `src/js/deck-extras.js` reformats on every `input` event by stripping the
value to digits and re-laying them into the mask:

```js
const digits = input.value.replace(/\D/g, '');
let out = '', d = 0;
for (const ch of mask) {
  if (d >= digits.length) break;
  out += ch === '#' ? digits[d++] : ch;
}
input.value = out;
```

Two things follow, and the second is the serious one.

**The caret moves.** Assigning `input.value` collapses the selection to the end of the
field. Measured in Chrome: caret at index 8 of `(202) 555-0147`, type `9`, caret ends at
14.

**A digit is destroyed.** The loop stops at the end of the *mask*, not the end of the
digits. Inserting a digit mid-number shifts every later digit one place along, and the
eleventh digit has nowhere to go:

```
(202) 555-0147   caret between the second and third 5, type "9"
(202) 559-5014   the 7 is gone
```

No warning, no visual cue that anything was lost, and the field still reports itself
`.is-valid` because it still holds ten digits. Someone correcting a single typo in a stored
number will save a different number than the one they think they typed.

**Recommended:** count digits before the caret, rebuild, then restore the caret to the same
digit offset — the standard approach for a masked input. And do not discard overflow
silently: either refuse the keystroke when the mask is full, or let the value exceed the
mask and mark it invalid. Dropping data quietly is the one option that should not be on the
table.

**How it was found:** writing the accessibility section of `phone.php`. The caret jump was
predictable from reading the code; the dropped digit was not, and only showed up because
the claim was checked in a browser rather than reasoned about.

## 63. The copy button's first press is announced to nobody

`copiers()` in `src/js/deck-extras.js`:

```js
btn.classList.toggle('is-copied', ok);
btn.setAttribute('aria-live', 'polite');
```

The class toggle is what swaps `.copy-idle` for `.copy-done` inside the button. The
`aria-live` attribute is added *after* that has already happened.

A live region has to be present in the accessibility tree **before** its content changes
for the change to be announced — adding the attribute and mutating the subtree in the same
task gives assistive technology nothing to compare against. So:

- **First press:** the label swaps, `aria-live` is added, nothing is announced.
- **Every press after:** the attribute is already there from last time, so it announces.

Inconsistent is worse than absent here. A reader who presses once and hears nothing learns
the control is silent; the announcement then arrives unexpectedly on the second press.

There is a second, smaller oddity: `aria-live` is being put on a `<button>`, so the
button's own accessible name becomes a live region. That works, but the conventional place
for this is a separate status element.

**Recommended:** declare the live region in the markup rather than adding it at press time
— either a visually hidden `<span role="status">` next to the button that the script writes
into, or `aria-live="polite"` rendered on the button from the start. Note that the toast
path is already correct: where no `.copy-done` exists, Deck raises a toast, and the toast
region is a properly-declared live region that announces on the first press like any other.

**How it was found:** writing the accessibility section of `copy.php` and reading the
order of the two statements rather than the fact that `aria-live` appeared somewhere in the
function.

## 64. A collapsed `.sidebar-link` has no accessible name

`src/18-container.css` collapses the rail to icons below 15rem:

```css
@container shell (max-width: 15rem) {
  .sidebar-link span { display: none; }
  .sidebar-link { justify-content: center; }
  .sidebar-group { text-align: center; font-size: 0; }
}
```

`display: none` removes the label from the accessibility tree, not merely from the screen.
The icon inside the link is `aria-hidden="true"` by Deck's own convention, so a collapsed
`.sidebar-link` contains nothing that contributes a name. A screen reader announces "link"
and stops — for every destination in the application's primary navigation.

The neighbouring rule shows the author knew the difference. `.sidebar-group` uses
`font-size: 0`, which hides the heading visually and **keeps** it in the tree, so the
grouping survives the collapse. The link rule reaches for `display: none` instead.

**Recommended:** hide the label the way the group heading is hidden, or with Deck's own
`.sr-only` treatment, so the name survives:

```css
@container shell (max-width: 15rem) {
  .sidebar-link span {
    position: absolute;
    inline-size: 1px; block-size: 1px;
    overflow: hidden; clip-path: inset(50%);
    white-space: nowrap;
  }
}
```

The documentation currently tells authors to put an `aria-label` on every `.sidebar-link`
duplicating its visible text, which works and should keep working — but it is a workaround
for a stylesheet defect, and every author has to know about it independently.

**How it was found:** writing the collapse section of `sidebar.php` and asking what a
collapsed link is actually called, rather than only what it looks like.

## 65. A link in footer prose is indistinguishable from the text around it

`src/22-nav.css`:

```css
.footer { color: var(--text-muted); font-size: var(--text-sm); }
.footer a { color: var(--text-muted); text-decoration: none; }
.footer a:hover { color: var(--brand); text-decoration: underline; }
```

The link colour and the body colour are **the same token**, and the underline is removed.
So a link inside a sentence in the footer has no colour difference, no weight difference,
no underline and no other mark. There is nothing to find it by.

Hover reveals it, which does not help a keyboard user, a touch user, or anyone who does not
happen to move the pointer over that exact word.

This is WCAG 1.4.1 (Use of Colour) failed in the strongest possible way — normally that
criterion is about relying on colour *alone*; here there is not even a colour.

**Where it is fine:** a `.footer-col`, which is a stack of links and nothing else. A list
where every item is a link needs no per-item marker, and this is the usual justification
for unstyled footer links. The rule is only wrong for prose, and Deck's own footer markup
puts a copyright sentence in `.footer-bottom` where prose is likely.

**Recommended:** scope the underline removal to the places where it is justified rather than
to every descendant.

```css
.footer a { color: var(--text-muted); }
.footer :is(.footer-col, .footer-social, .footer-bottom) > a { text-decoration: none; }
```

That keeps the clean look for link columns and the bottom bar's own link row, and leaves a
link inside a `<p>` underlined like any other.

**How it was found:** writing the link section of `footer.php` and noticing that the
declared link colour and the declared container colour were the same variable.

## 66. `.is-error` is written by the script and styled by nothing — FIXED

`lazies()` in `src/js/deck-extras.js` handles a failed image:

```js
img.addEventListener('error', () => img.closest('.lazy')?.classList.add('is-error'), { once: true });
```

There is no `.is-error` rule anywhere in `src/`. Grepping the whole stylesheet returns
nothing, and the class does not appear in `dist/api.json`.

So the class is added, and the frame carries on exactly as before: `.lazy::after` is still
painting its shimmer, the image is still `opacity: 0` because `.is-loaded` never arrived,
and the result is a frame that shimmers forever. **A broken image is visually identical to
one that is still loading**, with no time limit on the illusion.

For a sighted reader that is a page that never finishes. For a screen reader user the
`alt` text is still announced, so they are arguably better served than the sighted reader —
an unusual inversion.

**Recommended:** style the state, even minimally. Stopping the shimmer is the important
half, because that is what removes the false "still working" signal:

```css
.lazy.is-error::after { content: none; }
.lazy.is-error {
  display: grid;
  place-items: center;
  border: 1px dashed var(--line);
  color: var(--text-faint);
}
```

Deck classifies `.is-error` nowhere because the extractor only sees CSS, and there is no CSS
to see. A class the JavaScript writes and the stylesheet ignores is invisible to every check
in the build — worth noting as a category, not just an instance.

**How it was found:** writing the lazy-loading section of `gallery.php` and reading the
error handler, then grepping for the class it sets.

**Fixed** in `src/24-media.css`. `.lazy.is-error` stops the shimmer and paints a
broken-image glyph (Material's `broken_image`, as a CSS mask over `currentColor`) in the
middle of the frame, in `--text-faint`, so it works in both themes; under forced colours
the glyph is `CanvasText`. The frame keeps the space it reserved. The dashed border
recommended above was not used: `.lazy-block` already means "still streaming in", and a
failure should not look like that either. The class is in the ledger as internal, because
the script sets it.

**A second gap, in the script:** `lazies()` only listened for `error`. An image whose
`src` was in the markup, rather than in `data-src`, could fail while deck-extras.js was
still loading, and then there was no event left to hear, so even a styled `.is-error`
would never have been set. `load()` now treats an image that is `complete` with no
`naturalWidth` and a `currentSrc` as already failed, unless it has just swapped
`data-src` in, when the new request has not started. A test covers both paths.

## 67. Two components share `dk-shimmer` and behave differently under reduced motion

`src/02-reset.css` exempts seven selectors from the reduced-motion freeze so that loading
feedback keeps moving slowly rather than stopping:

```css
.spinner, .icon-spin, .skeleton, .marquee-track, .btn.is-loading::after,
.dg-wrap.is-loading::after, .ping::after { animation-duration: 2.4s !important; … }
```

`.lazy::after` is not in that list. It runs the same `dk-shimmer` keyframes as `.skeleton`
— at 1.6s rather than 1.4s — for the same purpose, on the same kind of placeholder. Under
`prefers-reduced-motion: reduce` it is caught by the blanket
`*, *::before, *::after { animation-duration: .01ms !important }` rule and freezes.

The reasoning written into the source is explicit: *"A frozen spinner reads as broken, and
progress feedback is information, not decoration."* That argument applies to `.lazy`
exactly as it applies to `.skeleton`. One of them follows it and one does not.

Whichever way it is resolved, the two should match. Either both keep moving slowly, or both
stop and the comment stops claiming otherwise.

**Recommended:** add `.lazy::after` to the exemption list, and while there, reconcile the
durations — 1.4s and 1.6s for the same effect on the same page is a difference nobody chose.
(`.g-shimmer` is a third copy of this, already recommended for demotion in finding on the
gradient audit.)

**How it was found:** writing the reduced-motion section of `gallery.php` and checking
whether `.lazy` was in the exemption list rather than assuming it was, because
`.skeleton` is.

## 68. Thirteen tokens are undocumented because the extractor reads one file

`tools/docs/extract.mjs` collects tokens like this:

```js
const TOKENS_FILE = '01-tokens.css';
…
for (const rule of rules) {
  if (rule.file !== TOKENS_FILE) continue;
  …
}
```

`src/16-motion.css` declares thirteen more custom properties on `:root`, and none of them
reach `dist/api.json`, `API.md`, `docs_token_table()` or the tokens reference page:

```
--dur-0  --dur-4  --dur-5  --dur-slow  --stagger-step  --travel
--ease-in  --ease-bounce  --ease-overshoot  --ease-snap
--ease-accelerate  --ease-decelerate  --ease-emphasized
```

Eight are in active use across the stylesheet:

| token | uses |
|---|---|
| `--ease-in` | 15 |
| `--dur-4` | 9 |
| `--travel` | 6 |
| `--dur-5` | 4 |
| `--ease-bounce` | 2 |
| `--ease-overshoot` | 2 |
| `--stagger-step` | 2 |
| `--ease-emphasized` | 1 |

So `.flip` transitions over `--dur-4` and `.cube` over `--dur-5`, and an author reading the
documented token list would conclude Deck's duration scale stops at `--dur-3`. Passing
either name to `docs_token_table()` renders an empty row rather than failing, so the
documentation cannot even catch itself.

**This is the same shape of mistake as finding 52.** There, I assumed one file held all the
print rules; here, the tooling assumes one file holds all the tokens. In both cases the
assumption is invisible until something that lives in the second file is examined closely.

**Recommended, in order of preference:**

1. Collect tokens from every `:root` rule in `src/`, not from one filename. The extractor
   already has every rule parsed and already records which file each came from, so this is
   a filter to delete rather than logic to add.
2. Failing that, make `docs_token_table()` fail loudly on a name that is not in the API,
   the way `verify.mjs` already fails on a class that does not exist. A silent empty row is
   the reason this survived 60 pages.
3. Move the motion tokens into `src/01-tokens.css`. Cheapest, but it treats the symptom —
   the next file to declare a token will be invisible in the same way.

**How it was found:** writing the token table for `3d.php`, passing `--dur-5` through the
same checker used for every other page, and getting `MISSING` for a token that is plainly
in the source and plainly working.

**Resolved.** `tools/docs/extract.mjs` now selects tokens by scope rather than by filename:
a rule whose every selector targets `:root` or `html` contributes its custom properties,
wherever the file sits. That is recommendation 1. The token count went from 131 to 144 —
exactly the thirteen above, since the `:root` block inside `@media print` in
`99-print.css` only redeclares names that `01-tokens.css` already owns and the extractor
keeps the first sighting. `--dur-4`, `--travel` and the whole easing set are now in
`dist/api.json`, in `docs_token_table()`, and on `reference/tokens.php`.

Recommendation 2 is still worth doing and is not done. A name that is not in the API still
renders as a row saying "not in the inventory" rather than failing the build, which is
better than the silent empty row it used to be and is not the same as a hard error.

## 69. Six classes in the inventory do not exist, and twenty-three that do are missing

`extract.mjs` reads a class name with `/\.(-?[_a-zA-Z][\w-]*)/`, which stops at the first
character that cannot appear in an identifier. In a responsive utility that character is
the backslash escaping the colon:

```css
.sm\:hidden  { display: none; }      /* recorded as .sm  */
.cq-md\:flex { display: flex; }      /* recorded as .cq-md */
```

So `dist/api.json` contains six classes — `.sm`, `.md`, `.lg`, `.cq-sm`, `.cq-md`,
`.cq-lg` — that nobody can write, each carrying whichever declaration happened to come
first in its block, and does **not** contain the twenty-three prefixed classes that people
actually type. `API.md` lists the six as public API.

The knock-on effects are quiet rather than loud. `verify.mjs` cannot check a page that
uses `md:row`, because a class attribute containing a colon fails its "is this a plain
list of class tokens" test and is skipped — so a typo in a responsive utility is exactly
as silent today as `.stack-5` was before the verifier existed. And the six phantoms count
toward the 931, so every coverage percentage on this project is computed against a
denominator that is six too large.

`reference/display.php` documents them as what they are and says so on the page, which is
honest but is not a fix.

**Recommended:** make `classesIn()` accept escaped characters — `/\.((?:\.|[-\w])+)/` —
and unescape the result, so `.sm\:hidden` records `sm:hidden`. Then relax
`STATIC_CLASS_LIST` in `verify.mjs` to allow a colon, and the responsive utilities become
checkable like everything else. Both are one-line changes; the reason to be careful is
that the class count moves, which moves `API.md`, the buckets file and the backlog with
it, so it wants its own pass rather than being folded into a docs change.

**How it was found:** writing the breakpoint section of `reference/display.php` and
finding that `.sm` had no declarations to put in the table.

## 70. The token table decided what a token was from its name, and the name lies

`extract.mjs` labels every token with a `kind` guessed from the property name:

```js
kind: /color|hue|brand|ink|surface|text|line|good|warn|bad|accent|focus/.test(d.prop)
  ? 'color' : …
```

`--text-sm` contains "text", so it was a colour. So were `--text-2xs` through
`--text-4xl`, and so were `--hue-brand` and the five other hue angles, which are bare
numbers. `docs_token_table()` rendered each of those as
`<span style="background: var(--text-sm)">` — `background: .8125rem`, an invalid
declaration, which paints nothing and reports nothing.

Counted across the component pages, that was roughly fifty cells showing an empty box
where a swatch was meant to be. Nobody noticed because an empty swatch and a swatch of a
colour that happens to match the surface look identical.

**Fixed** in `docs_token_table()` rather than in the extractor: `docs_token_kind()` in
`_layout.php` now classifies from the *value*, which cannot lie about its own shape —
`oklch(…)` and `light-dark(…)` are colours, `.8125rem` is a length, `420ms` is a duration,
`cubic-bezier(…)` is an easing curve — and follows a one-level `var()` alias, which is what
correctly files `--shadow-color` as the hue number it actually is rather than as a shadow.
Durations and easings preview by animating with the token itself, held still under
`prefers-reduced-motion`. All 144 tokens land in one of nine kinds, and
`reference/tokens.php` prints an "Ungrouped" section that is empty only when every one of
them has been accounted for.

The generated `kind` field is left alone and is now unused by the docs. It should either be
given the same value-based treatment or dropped, because a field that is wrong and ignored
is worse than one that is absent.

**How it was found:** building the swatch column of `reference/tokens.php` and noticing
that `--text-sm` was being handed to the colour branch.

## 71. The theme switch does nothing on its first press on a dark machine

`data-deck-theme` decided which way to toggle by reading the `data-theme` attribute:

```js
const dark = document.documentElement.getAttribute('data-theme') === 'dark';
Deck.theme(dark ? 'light' : 'dark');
```

With no saved choice there is no attribute, because the operating system is deciding.
On a machine set to dark, the page is dark, `dark` is false, and the first press sets
`data-theme="dark"` on a page that is already dark. Nothing visibly happens; the second
press works. The demo page's own `#themeBtn` handler had the same logic through
`Deck.theme() === 'dark'`, and also showed a moon on a dark page until it was pressed.

Measured in Chrome with the colour-scheme preference emulated, before the fix:

| OS preference | before | first press | second press |
|---|---|---|---|
| dark  | no attribute, page dark  | `dark`, page **still dark** | `light`, page light |
| light | no attribute, page light | `dark`, page dark | `light`, page light |

It hides from anyone testing on a light machine, which is how the first tutorial's
checklist passed: that run emulated light.

**Fixed** in `src/js/deck.js` (`wireTheme`) and in the demo page's handler: both now read
the theme in effect, which is the attribute when one is set and
`matchMedia('(prefers-color-scheme: dark)')` when it is not. `Deck.theme()` is unchanged
and still returns `'auto'` when nothing is saved.

**How it was found:** adding a theme switch to the documentation site, and testing the
first press on both colour schemes before copying the pattern.

## 72. robots.txt disallowed nothing for any crawler it named

The file had a `User-agent: *` group with `Allow: /` and two `Disallow` lines for the
minified twins, then seventeen named groups — Googlebot, Bingbot, DuckDuckBot, GPTBot,
ClaudeBot and the rest — each holding only `Allow: /`. A crawler obeys the one group that
names it and reads `*` only when no group does (RFC 9309, section 2.2.1). The two
`Disallow` lines therefore reached unnamed crawlers only; every crawler the file took the
trouble to name was allowed the minified files.

While the site was planned for a subdirectory this cost nothing, because a robots.txt in a
subdirectory is never read. Moving the site to the root of `get-deck.dev` is what turns the
mistake into behaviour.

**Fixed** in `public_html/robots.txt`: every group carries the same rules, and
`tools/site.mjs` fails the build if any group's rules differ from the `*` group's. The same
step writes the `Sitemap` line from the site's base URL. All 18 groups survive, the AI
crawlers included.

**How it was found:** checking the file for a host root, where it will now be read, by
parsing it into groups the way a crawler does.

## 73. The Composer installer printed a filesystem path as the stylesheet URL

`composer deck-publish` ends with tags to paste, and built their URLs from the publish
target. Publishing to `public/assets/deck` printed `href="/public/assets/deck/deck.css"`,
which 404s in any project whose document root is `public/` — Laravel and Symfony
included — and disagrees with `Deck::head()`, whose default base is `/assets/deck`. The npx
CLI already stripped the usual document-root names and printed `/assets/deck/deck.css` for
the same folder.

Measured against 0.1.0 from Packagist, in a consumer with `publish-to` set to
`web/static/deck`: eight files arrived in `web/static/deck`, the four compared were
byte-identical to the npm package's, nothing was written to `public/`, and the printed tag
was `/web/static/deck/deck.css`.

**Fixed** in `php/Installer.php`, which strips the same names as the CLI: `web/static/deck`
now prints `/static/deck/deck.css`, and `public/assets/deck` and `public_html/assets/deck`
both print `/assets/deck/deck.css`.

Two reference pages had not caught up with the previous installer change either.
`reference/php.php` said publishing "happens automatically after composer install" and
showed `"auto-publish": true`; `reference/cli.php` said `composer require` publishes on
install and update. Neither is true until the project's own composer.json wires the scripts
in, and both pages now show that wiring.

**How it was found:** running `composer require echodial/deck` against Packagist for the
first time.

## 74. Icons cannot load from a CDN, and the CDN instructions did not say so

The README's CDN section offered jsDelivr tags for the stylesheet and the bundle. Both
load. Icons do not: browsers refuse an SVG `<use>` whose `href` is on another origin, and
`deck.js` resolves the sprite against its own script URL, so a bundle loaded from jsDelivr
points every icon it renders at jsDelivr.

Measured in headless Chrome, with the page on `127.0.0.1` and the stylesheet and bundle
from `@echodial/deck@0.1`:

| `<use href>` | Bounding box |
|---|---|
| `https://cdn.jsdelivr.net/npm/@echodial/deck@0.1/dist/deck-icons.svg#check` | 0 × 0, and an error event |
| `deck-icons.svg#check` on the page's own origin | 12.1 × 8.8 |

**Fixed** in the README, the install page and llms.txt. The CDN path saves the sprite with
one `curl` and points `data-deck-icons` at it; `deck.js` reads that attribute from
`document.currentScript`, so it works on a CDN script tag.

The same README section loaded `@floating-ui/dom` and `sortablejs` with no version, which is
`@latest` by another name, and its Floating UI tag could not have worked: the DOM build
expects `FloatingUICore` to be loaded first. On its own, `window.FloatingUIDOM` existed with
no `computePosition` and the script threw; with `@floating-ui/core@1` first it worked. All
three tags are now pinned to their major version, core first.

**How it was found:** loading the CDN tags in a browser instead of checking that the URLs
return 200.

## 75. Two absolute URLs were not quite the URLs they named

Every docs page built `og:image` as the docs base plus `/../assets/images/deck-og.png`, so
the image URL it advertised contained a `..` segment. Browsers resolve that; a social-card
scraper is not obliged to. The demo page's canonical URL was the bare base without a
trailing slash, and its JSON-LD `@id` values were built on it.

**Fixed:** `og:image` is the site base plus `/assets/images/deck-og.png`, and the home
page's canonical and `@id` values carry the trailing slash, which is the URL `sitemap.xml`
lists. Every absolute URL the pages print now comes from `public_html/_site.php`
(`DECK_SITE_BASE`, otherwise package.json `homepage`), and `tools/site.mjs` resolves the
base the same way for `sitemap.xml`, `robots.txt` and `llms.txt`. The sitemap is generated
from the pages on disk.

A check that serves the site and fetches every sitemap URL found each canonical and `og:url`
equal to its sitemap entry, all 190 JSON-LD URLs under the base, and 94 pages on disk
against 94 entries.

**How it was found:** writing that check for this pass.

## 76. Two component pages replaced the size the docs footer prints

`docs/_layout.php` loaded `dist/sizes.json` into a global `$sizes`, and `docs_footer()`
prints "Deck is N KB Brotli" from it. `components/avatar.php` and `components/icon.php` each
assign their own `$sizes`, the list of sizes they document, after requiring the layout. By
the time the footer ran, the global was that list: both pages printed three PHP warnings,
and with warnings hidden they would have said "Deck is 0.0 KB Brotli". The bug predates this
pass; an archive of HEAD served on its own shows the same three warnings on each page.

A second size was stale for a different reason. Changing the banner's URL moved
`deck.min.css` from 26.7 to 26.8 KB Brotli, and `explain/why-no-build-step.php` had 26.7 KB
typed into its opening paragraph and its closing one, the only figures on the site that
were not read from `sizes.json`.

**Fixed** in `_layout.php`: the measurements live in `$DOCS_SIZES`, a name no page uses, and
a `docs_kb()` helper formats them. The footer and the essay both call it.

**How it was found:** the URL check above treats PHP warning text on a page as a failure,
and a grep for the old figures after the rebuild.

## 77. The site was not serving Deck when this release pointed at it

`get-deck.dev` was registered at 00:31 UTC on 11 September 2026. Checked from 00:46 to
01:19 UTC, it resolved to Hostinger, answered plain HTTP with Hostinger's "Default page",
returned 404 for `/robots.txt`, `/sitemap.xml` and `/docs/index.php`, and failed every TLS
handshake with alert 80 and no certificate. `.dev` is on the HSTS preload list, so a browser
will not fall back to plain HTTP: nobody could open the site.

0.1.1's `homepage` and every canonical URL in the repository name that host, which is right
once the site is deployed with a certificate. Until then, the checks that need the live host
— robots.txt fetched from the site root, the sitemap served where it says it is — were run
against a local PHP server serving `public_html/` instead.

**Not fixed here:** deploying `public_html/`, with `dist/`, `php/` and `package.json` beside
it because the pages read them, and issuing the certificate, both happen outside the
repository.

**How it was found:** fetching the published URLs before editing anything.

## 78. Deck's Composer scripts never ran for anyone who installed Deck

Deck's `composer.json` defined `post-install-cmd`, `post-update-cmd` and `deck-publish`, all
calling `EchoDial\Deck\Installer`. Composer runs scripts from the root package only, the
project `composer` is run in, and never from a dependency. So for every project that
installed Deck, none of them existed: `composer require echodial/deck` copied nothing, and
`composer deck-publish` answered `Command "deck-publish" is not defined.` The only root
package they could fire in was Deck's own repository, which is how Deck published a copy of
itself into itself.

The 0.1.0 README, `reference/php.php` and `reference/cli.php` described that path as
working: assets published into the public directory on install, configured by an
`extra.deck` block alone, with `composer deck-publish` to run it again. None of it could
happen. The 0.1.1 docs said to add the scripts to your own `composer.json`, which works, but
showed `"auto-publish": false`, so following them `composer require` still copied nothing
and printed a reminder.

**Fixed:** Deck's `composer.json` has no `scripts`, `scripts-descriptions` or `extra.deck`.
The install page, the README and `reference/php.php` document three routes, each run from
an empty directory against Packagist: the scripts in your own `composer.json` with
`"auto-publish": true`, which publish during `composer require`; a manual
`cp -r vendor/echodial/deck/dist/* public/assets/deck/`; and `npx @echodial/deck init`.
They also say that `publish-to` defaults to `public/assets/deck` and has to change where the
document root is `public_html/`. The installer's behaviour is unchanged from 0.1.1, so all
of this is true of the release on Packagist today.

A Composer plugin would publish with no scripts at all, and it is the mechanism Composer
provides for exactly this. It was not chosen: the package type would become
`composer-plugin`, every project installing Deck would have to approve it under
`allow-plugins` before it could run, including projects that only want the PHP helper, and
to be automatic it would copy files into projects that never asked for them.

**How it was found:** `composer require echodial/deck` in a fresh project with no scripts
of its own.

## 79. `import Deck from '@echodial/deck'` did not build

`build.mjs` wrote `dist/deck.esm.js` as the bundle followed by two lines:
`export default globalThis.Deck;` and `export { globalThis as __deckGlobal };`. The second
exports a name the module never declares, which is a syntax error in any ES module. Node
says `SyntaxError: Export 'globalThis' is not defined in module`, Vite 8
`[PARSE_ERROR] Export 'globalThis' is not defined`, and webpack 5 `Module parse failed:
Export 'globalThis' is not defined`. The exports map sends `import Deck from '@echodial/deck'`
to that file, and both the README and the install page showed exactly that line, so the
documented bundler import failed in every bundler against the published 0.1.2, and the same line is in build.mjs at v0.1.0 and v0.1.1, so no release has had a working default import. The check
that the exports map worked had used `import.meta.resolve`, which finds the file and never
parses it.

**Fixed** in `build.mjs`: the named export is gone, and the rebuilt `deck.esm.js` passes
`node --check` and builds in webpack. That reaches readers only in the next release, so the
install page and the README now import `@echodial/deck/bundle` for its side effect and read
`window.Deck`. That import builds, serves and passes the install page's four-point check in
Vite 8.3 and webpack 5.110 against 0.1.2 as published.

**How it was found:** building a real page with Vite and with webpack for the install page,
instead of resolving the import.

## 80. Packagist's 0.1.2 is not the commit tagged v0.1.2, and neither one was built

Packagist lists v0.1.2 at commit `3adb4d6`, made at 12:03:50 −06:00 on 11 September 2026.
The tag `v0.1.2` now points at `939a63b`, committed seven minutes later with the same
message, and `3adb4d6` is no longer on `main`. Packagist read the tag before it moved and
kept what it read. So `composer require echodial/deck` installs `3adb4d6`, whose
`php/Deck.php` still says `VERSION = '0.1.1'`: the installer prints *Deck 0.1.1 published
to …*, and every banner in its `dist/` says v0.1.1.

The tagged commit is not right either. It bumps `php/Deck.php` and `package.json` but not
`dist/`, whose `sizes.json` and banners still say 0.1.1; the rebuilt `dist/` arrives only in
`6e12193`, after the tag. npm is the one registry with a correct 0.1.2, because
`npm publish` runs the build before it packs.

**Not fixed here.** A tag should not move once a registry has read it. The repair is a 0.1.3
cut from a commit whose `dist/` was rebuilt and committed first, and tagged once. The build
already refuses a `php/Deck.php` that disagrees with `package.json`; nothing checks that
`dist/` was rebuilt before tagging, and `node build.mjs && git diff --exit-code dist` would.
The install page shows the 0.1.1 line as printed, with a note saying why.

**How it was found:** the Composer transcript for the install page said 0.1.1 after
installing v0.1.2.

## 81. Seven components drew a light-mode surface in dark mode

`--ink-100` and `--ink-200` are fixed values, 95.4% and 91.2% lightness with no
`light-dark()`, and seven rules used them as a surface: the `.progress` track, the
`.ring` remainder, the `.spinner`'s unlit arc, the `.skeleton` gradient, both vendor
tracks of `.range`, and `.range-pair::before`. In dark mode each drew a near-white bar on a
near-black page. The status badges, alerts and `.banner-warn` had the same fault through
another ramp: they filled with `--good-100`, `--warn-100` and `--bad-100`, which are just
as fixed. Worst was a `.btn-ghost` inside `.alert-good`, `-warn` or `-bad`: `.btn` text is
`--text`, near-white in dark mode, on a near-white fill, and it measured 1.02:1 to 1.06:1.
The other `--ink-100` uses are text on a surface that is dark in both themes (`pre`,
`.g-dark`, `.banner-dark`, the Chart.js tooltip), and were left alone.

**Fixed** with seven tokens in `01-tokens.css`. Each is `light-dark()` whose light branch
is the exact step the component used before, so light mode does not change. Checked in
Edge against the build before the change: the computed colour, background, border and
shadow of all 13,582 elements and pseudo-elements across the twenty templates are
identical in light mode, and so is a screenshot of every affected component on `--bg`,
`--surface` and `--surface-2`.

- `--surface-recessed` is `--ink-200` in light and `oklch(5% .008 neutral)` in dark: the
  surface a control sits on. It is darker than what it sits on in both themes and one step
  below `--bg-sunken`, so the skeleton now runs `--bg-sunken` to `--surface-recessed`,
  which is the `--ink-100` to `--ink-200` it drew before.
- `--good-soft`, `--warn-soft` and `--bad-soft`, each with a `-text` partner, are the `100`
  and `700` steps in light. In dark the fill is `--brand-soft`'s 26% at the status hue and
  the text is 76% to 84%. Tone text now measures 7.0:1 to 9.4:1 in dark mode, and the ghost
  button inside each alert 13.4:1 to 13.9:1.

**Deliberately not recessed:** in dark mode the status fills sit at 26% lightness, above
`--surface` rather than below it. They were tried at 9% first, but sRGB holds only about
.02 chroma there: `.alert-good` and `.alert-warn` rendered as plain black boxes, and on
`--bg` the badges lost their shape and only the coloured text remained. At 26% they match
`.alert-info`, `.badge-brand` and `.banner`, which have always used `--brand-soft` there,
and the patch the dark-mode guide was already recommending.

**A limit worth knowing:** dark `--bg` renders at about rgb(5, 5, 6), so anything darker
is close to black. A recessed track on the bare page background is faint in dark mode
however it is specified; on `--surface` and `--surface-2` it is clear.

**Not fixed here:** the same fixed steps still fill `.icon-tile-good`, `-warn` and `-bad`,
`.menu-item-danger:hover`, `.copy-btn.is-copied`, `.tone-surface` in `18-container.css`,
`.drop-reject`, and the `dg-flash` keyframe in the data grid. The `700` steps are still the
text colour of `.text-good`, `.text-warn`, `.text-bad`, `.stat-delta-up`,
`.stat-delta-down`, `.menu-item-danger`, the invalid `.float` label and
`.editor-count.is-over`, which on a dark surface measures 2.0:1 to 2.8:1 for good and bad
and 2.8:1 to 3.3:1 for warn. Each is a one-line move to a soft pair. The dark-mode guide's
patch now covers the icon tiles only.

**Templates that patched this themselves:** `auto-repair.html` and `digital-marketing.html`
redefine `--good-100`, `--warn-100`, `--bad-100` and the `700` steps at `:root`, as the
guide used to recommend. In dark mode the soft pairs no longer read those steps, so their
badges now take Deck's 26% fill instead of the templates' 31% to 33%; the overrides are
redundant for badges and alerts. `app-shell.html`, `carousels.html` and `checkout.html`
restyle the classes directly in unlayered CSS, which still wins, so they render as before.
`app-shell.html` gives `.progress` a lighter `--ink-800` track in dark mode, the opposite
direction to `--surface-recessed`. Each workaround can go in a template pass.

**How it was found:** five bug reports against these components, which turned out to share
two causes rather than one. Grepping every use of `--ink-100` and `--ink-200` and sorting
each into surface or text found the two `.range` tracks, which nobody had reported.

## 82. `.dur-*`, `.ease-*` and `.delay-*` lost to every motion component

The duration, easing and delay utilities were declared at the top of `16-motion.css`, in
`deck.motion`. Every component further down that file sets the `transition` or `animation`
shorthand, at the same specificity and later in the same layer, and a shorthand resets its
longhands, so the component won. `<div class="fade-swap dur-4">` faded over `--dur-2`'s
190ms, `.fade-swap.ease-spring` kept `--ease-out`, and in `landing.html` the
`.enter-rise.delay-3` and two `.enter-pop.delay-3` elements started with no delay at all.
The same was true of the entrances, `.shake`, `.flash`, `.pulse`, `.lift`, `.press`,
`.sweep`, `.icon-follow`, `.expand` and `.tick`, and of everything in `deck.effects`, which
comes after `deck.motion` in the layer order. The utilities could only beat the layers
before them.

**Fixed** by moving the whole family to a Motion section of `09-utilities.css`, in
`deck.utilities`, in the order it had: the five `.transition` shorthands, `.dur-1` to
`.dur-5`, the six `.ease-*`, `.delay-1` to `.delay-3`, `.no-motion` and `.will-move`. All
21 are single-purpose. The components and their modifiers, `.stagger-fast` and
`.reveal-late` among them, stay in `deck.motion`. The shorthands stay above the scales, so
`.transition.dur-4` still works. The rules moved unchanged; `deck.min.css` is 22 bytes
larger, and all of that is #83's selector.

Checked in Edge against the build before the change. Across the twenty templates, the
computed transition and animation longhands and `will-change` of all 38,439 elements and
pseudo-elements are identical except on the three `landing.html` elements above, which now
wait the 260ms their markup asks for. On a test page `.fade-swap.dur-4` runs 420ms each
way instead of 190ms, and `.fade-swap.ease-spring` runs on `--ease-spring`.

**Worth knowing:** the utility now wins for the shorthands too. `.lift.transition-colors`
replaces `.lift`'s translate and shadow transition rather than losing to it, as `.card.p-0`
replaces the card's padding. `.no-motion` keeps its `!important`: the only other important
transitions are the reduced-motion reset, in an earlier layer that wins either way, and
print, which also says `none`. In `dist/api.json` 22 classes changed layer, the 21 plus
`.is-animating`, which appears only inside `.will-move`'s selector. None changed bucket or
description.

**How it was found:** `<div class="fade-swap dur-4">` animated at the default speed. The
`landing.html` delays turned up when the templates were searched for the classes being
moved.

## 83. `.fade-swap` faded in on first paint

`@starting-style` gives an element a style to transition from when it has no earlier style.
That covers an element going from `display: none` to shown, and it also covers every
element on a page's first render. `.fade-swap`'s rule had no condition, so anything with the
class that was visible on load faded in from transparent over 190ms: a boot screen or
loading overlay let the page show through before it covered it. On a test page in Edge, all
four visible `.fade-swap` elements ran an opacity transition on load.

**Fixed** with a marker, because CSS cannot tell a first render from a reveal: neither has
an earlier style. Two animation frames after `Deck.init()` runs on `DOMContentLoaded`,
deck.js sets `data-deck-ready` on `<html>`, and the `@starting-style` rule now matches only
beneath it. It takes two because the first callback runs before the first frame renders. On
the same page nothing fades in on load, and an element un-hidden afterwards still fades in
from 0. The attribute is listed with deck.js's other runtime marks in the JavaScript
reference.

**The trade:** without deck.js, `.fade-swap` still fades out but appears without fading in.
Anything shown or inserted before the marker lands, including an element page code
un-hides in its own `DOMContentLoaded` handler, also appears without fading, which is the
point: it is part of the first render.

**How it was found:** reported with #82: an overlay with `.fade-swap` flashed on load.

## 84. The combobox wrote `data-placeholder` into its markup unescaped

`Combo.build()` in deck.js builds the control with `innerHTML` and interpolated
`root.dataset.placeholder` straight into the input's `placeholder="…"`. The dataset value
is already decoded, so a server that escaped the attribute correctly did not help: a
placeholder containing a double quote closed the attribute early. In Edge,
`data-placeholder='" onfocus="alert(1)'` gave the input an `onfocus` handler that ran on
the first focus, and an ordinary `Search O'Brien's <b>files</b> & "notes"` was cut off
after the `&`, leaving a junk attribute named `notes""`. Every other
interpolation in the combobox — options, groups, tokens and `highlight()` — already went
through `escapeHTML()`.

**Fixed** by passing the placeholder through `escapeHTML()` like the rest. The markup and
the data attributes are unchanged. All three strings above now arrive as literal
placeholder text, with no extra attributes and no script. `tests/state.test.mjs` covers it.

**How it was found:** reported, with the line number; the rest of the combobox was
re-read to confirm this was the only unescaped interpolation.

## 85. `.banner-close` had no touch target

`.banner-close` set only `margin-inline-start`, `opacity`, `cursor` and `flex`, so its
hit area was the icon plus the browser's button padding: 25×22px in Edge, against the
44px every Deck control claims to clear. `.btn` meets the claim with a `::before`, but a
bare `<button class="banner-close">`, as on the home page, had nothing.

**Fixed** with a `--tap` square. `.banner-close:not(.btn)` is an `inline-grid` of
`min-inline-size` and `min-block-size: var(--tap)`, with the icon centred, so the button is
44×44 and the 13px `.icon-sm` is still 13px. A negative `--space-3` margin on the block
axis and the inline end lets the square reach into the banner's padding, so the strip does
not get taller and the icon moves 2.5px. A one-line banner is 2px *shorter*, 45px rather
than 47, because the old button's line box was the tallest thing in it; a two-line banner is
unchanged. `.btn.banner-close`, as used by `ecommerce.html`, `product.html` and
`jumbotrons.html`, is excluded and measures the same as before, because `.btn`'s `::before`
already gave it a 44px target.

**How it was found:** reported, by measuring the home page banner against the README's
touch-target claim.

## 86. Two attributes meant "on", and `.btn` answered to neither

`.chip`, `.reaction` and `.datepicker-preset` were on at `[aria-pressed="true"]`;
`.segmented` was on at `[aria-selected="true"]`; `.btn` had no on state at all. Picking the
wrong one failed silently: no error, no visible state. The templates show the cost. Five of
them put `aria-pressed` on a `.btn` and each wrote its own pressed style, and
`product.html`'s `.helpful` reinvents the brand tint by hand.

**Fixed** by picking `aria-pressed="true"` as Deck's attribute for "on", because it is
valid on a plain `<button>` with no role, and `aria-selected` is not. `aria-selected="true"`
stays as an equal alternative with the same look wherever an element could be an option or
a tab: `.chip`, `.reaction`, `.datepicker-preset` and `.segmented`'s children now take both,
and `.chip` and `.segmented` keep `.is-active`. `.btn` gains `[aria-pressed="true"]`. That is
the brand tint with a brand edge and no lift, a solid fill for `.btn-soft` (already tinted),
and a shade darker with no lift for the solid variants. `aria-pressed="false"` looks the same
as no attribute. A `.btn` inside `.segmented` is excluded, so the control's raised look is the
only one there. deck.js's `.segmented` click handler moved only `aria-selected`; it now moves
whichever of the two attributes the children carry. `.tabs` still moves only `aria-selected`,
so a toggle button inside a tab bar is not taken for a tab. The convention is in a table on
`button.php#on-attribute`, and the segmented and date picker pages link to it.

Checked in Edge against the build before the change, light and dark. The existing look of
every on state is identical: `.chip`, `.reaction` and `.datepicker-preset` at
`aria-pressed`, `.segmented` at `aria-selected` and `.is-active`, at rest and on hover,
whether the children are bare buttons, `.btn` or `.btn-ghost`. The new alternatives compute
the same paint as the originals. Every `.btn` variant now differs when pressed.

**Worth knowing:** the templates that already put `aria-pressed="true"` on a `.btn` pick up
whatever their own unlayered rules leave unset. `.helpful` now loses its lift shadow, and
`.buy-wish` gains the tint and edge when pressed. `.product-wish` and `.cr-product-fav`
change only on hover, and `landing.html`'s `.marquee-toggle` is tinted while paused. The
handler had a second bug: it found its item with `closest('[aria-selected]')` and never
checked that the item was inside the group. In a `.segmented` inside a `tr[aria-selected]`,
a click on the track's padding selected the table row. The handler now ignores anything
outside its group.

**How it was found:** reported, as a trap recorded in `CLAUDE.md`: `.segmented` built with
`aria-pressed` shows no state.

## 87. The date picker never shows which preset is active

`.datepicker-preset` has an on state, but deck.js never uses it. `render()` rebuilds the
whole panel with `innerHTML` on every interaction, and it writes each preset as a bare
`<button class="datepicker-preset" data-preset="7">` with neither attribute. So after
"Last 7 days" is chosen, nothing marks it, and an attribute set by page code is wiped on
the next click.

**Not fixed.** It needs the picker to remember which preset produced the current range,
and to drop that memory when the reader edits the range by hand. That is a behaviour change
to the date picker, outside the attribute fix in #86. The date picker page says so.

**How it was found:** while adding `aria-selected` to the preset rule in #86, by checking
what sets the attribute today.

## 88. The combobox list covered its own field, and inside a `.card` on a phone it was cut off — FIXED

Reported as a phone bug inside `.card`. Measured in Edge, it turned out to be two bugs, and
the one underneath affects every page.

**On a phone inside a `.card`.** Under 30rem `.combo-list` became a sheet:
`position: fixed; inset: auto 0 0 0; max-block-size: 70dvh`. `26-perf.css` gives `.card`
`contain: layout style`, and layout containment makes an element the containing block for
fixed-position descendants as well as absolute ones. So the sheet was laid out against the
card, not the screen. At 375×667, with the field at y=286–337 in a card spanning y=202–393,
the list ran from y=−78 to y=388: bottom-aligned to the card, 78px off the top of the screen.
Then the card's `overflow: clip` cut away everything outside the card, leaving a card-sized
window into the middle of the list, over the field. A tap in the middle of the list hit the
page behind it.

Each suspect switched off in turn, same page:

| changed | list spans | on screen | tappable |
| --- | --- | --- | --- |
| nothing | y −78 to 388, visible only inside the card | no | no |
| `.card { contain: none }` | y 196 to 663 | yes | yes |
| `.card { overflow: visible }` | y −78 to 388 | no | yes |
| anchor rules off | y −78 to 388 | no | no |

**Containment is the cause.** `overflow: clip` only did the visible damage because
containment had made the card the fixed box's containing block; without containment a fixed
box escapes the clip. The anchor rules played no part in the card failure. A z-index could
not have fixed it either, because layout containment also makes the card a stacking context.

**Under it, on every page.** With containment off, the list was on screen but still covered
the field: it started at y=196, and the field was at 286–337. Outside any card, on a desktop
page at 1024px, the list spanned y=43–363 over a field at y=39–88. `25-anchor.css` gave
the list `position-area: block-end`, but `position-area` resolves against the positioned
element's containing block. That was `.combo`, which is `position: relative` and exactly as
tall as the control. The area below the control was zero pixels tall, and so was the area
`flip-block` tried above it, so the list was pushed back into `.combo`, on top of the field.
That has been true in every Chromium browser since `25-anchor.css` shipped. The comment and
the docs said the list "goes in the top layer"; nothing put it there. On a phone outside a
card, the sheet ran 38px off the bottom of the screen.

**Fixed** by opening the list as a popover (`popover="manual"`), which puts it in the top
layer. Its containing block is the viewport, no ancestor can clip it, and `position-area`
measures real space. Each combobox gets its own `--anchor`. In the top layer every
`.combo-control` is a candidate anchor, and the shared `--combo` name would have tied every
list to the last control on the page. The phone sheet is gone: the list stays anchored at
every width, with 44px rows under 30rem. `25-anchor.css` gains a third fallback,
`--dk-combo-fit`: below the field and shrunk to the room there, for a field in the middle
of a short screen where the list fits on neither side at full height. Without anchor
positioning, the control carries `data-deck-anchor`, so the Floating UI adapter places the
list when it is loaded. Its `combo-list` branch had never been reachable, because the list
was not a popover and nothing pointed at it. With neither, deck.js places the list itself
with fixed coordinates.

Verified in Edge at 375×667 and 1024×768, with the field near the top, middle, lower part and
bottom of the screen and two comboboxes per card. In every case the list had the same edges
as its field, was clear of it, fully on screen and hit-testable: 16 of 16 with anchor
positioning, 16 of 16 with it stripped out and Deck's own placement, and 16 of 16 with it
stripped out and Floating UI 1.6 loaded from jsDelivr. `tests/combo.test.mjs` covers the card
at 375px and a plain desktop page.

**Worth knowing:** with Floating UI, the adapter's `size` middleware writes `max-height`
inline from the available space, so the list can grow past the stylesheet's
`min(20rem, 50dvh)`. That is how the adapter has always treated menus; left alone. In a
browser with no popover support, the list is still a positioned sibling and a card still
clips it. Every browser in the browserslist has popover.

**How it was found:** reported, building an app on 0.1.3. The desktop overlap turned up while
measuring the diagnosis, with containment switched off.

## 89. The combobox drew every option, so ten thousand took over a second to open — FIXED

`Combo.render()` wrote every matching option into the list with `innerHTML`, on every open
and every keystroke. Reported at 8.5 seconds for 10,500 options in a real app. Measured in
Edge on this machine, median of five, mousedown to the first painted frame:

| options | before | after |
| --- | --- | --- |
| 100 | 26.9ms | 17.1ms |
| 1,000 | 114.6ms | 17.3ms |
| 10,000 | 1,397ms | 16.4ms |

The synchronous part of the open went from 877ms to 14ms at 10,000. What is left is the wait
for the next frame. Two smaller costs grew with the list too: `sync()` looked each `<option>`
up in `this.options` with `find()`, O(n²) per pick, and `onKey()` re-queried every option
node on every key press.

**Fixed** with windowing. Only the rows near the scroll position are elements, a few
dozen at most, and padding on a wrapper stands in for the rest. Rows are not assumed to be
one height: an option, an option with a second line and a group heading each start from a
guess, the first row of each kind drawn replaces its guess with its real height, and every
row is measured as it is drawn. When a measurement moves rows above the view, the scroll
position is corrected so the view does not jump, and the browser's own scroll anchoring is
off so the two do not both correct it. A group heading drawn out of the slice is pinned as an
`aria-hidden` copy, so the sticky heading still shows while you scroll through its group.
`activeIndex` was always an index into the filtered list, so the arrow keys work over every
option. `reveal()` scrolls the active one into view, clear of the pinned heading and the
sticky Add row, before it is drawn.

The docs said the input was linked to the active option by `aria-activedescendant`.
deck.js never set it, and the options had no ids, so a screen reader heard nothing as the
highlight moved. With windowing it became essential, because the active option has to exist
to be named. deck.js now sets it, removes it when the active option is scrolled out of the
drawn slice (rather than leave it naming an element that is gone), and gives each option
`aria-posinset` and `aria-setsize`, so a drawn option is announced with its place in the whole
list. The input also gains `aria-controls`, and the list `aria-multiselectable` with
`data-multi`.

Also fixed on the way: `ArrowUp` with no active option went to the second-to-last option,
because `(−1 − 1 + n) % n` is `n − 2`. It now goes to the last. `PageUp` and
`PageDown` move a screenful and stop at the ends.

Verified with 10,000 real `ArrowDown` presses in Edge. After each one the active option was
drawn, in view and named by `aria-activedescendant`. The run ended on "Option number 9999",
position 10,000 of 10,000, with no more than 25 options in the DOM at any point. One more
press wrapped to the first option, and `ArrowUp` wrapped back.

**The trade:** the browser's find-in-page only sees drawn options. The combobox's own
filter searches all of them.

**How it was found:** reported. The missing `aria-activedescendant` turned up while checking
what a screen reader would be told once most options no longer existed.

## 90. A combobox could not be read, set or cleared from script — FIXED

The instance was reachable as `el._deck`, but it had no public methods. Setting the hidden
`<select>`'s value changed nothing on screen, and there was no way to clear the field from a
"Reset filters" button.

**Fixed** with `value()` and `clear()`. The shape follows Deck's own read-or-write
methods, where `Deck.theme()` and `Deck.dir()` read with no argument and write with one:

- `value()` returns a string for a single combobox (`''` with nothing chosen, as a select's
  `value` does), or an array with `data-multi`.
- `value(v)` takes a value, an array, or `{ value, label }` objects, and replaces the
  selection. A value the list does not have is ignored, as on a native select, unless the
  combobox is remote or takes `data-create`. Then it joins as given, and the object form
  carries the label for a remote value no search has loaded.
- `clear()` empties it.

Both update the `<select>` and fire `change` and `deck:change`, as a pick does, so code that
listens for one hears the other. `{ silent: true }` skips the events, for code that sets the
value from the same state its `deck:change` handler writes to.

**How it was found:** reported.

## 91. A remote multi-select forgot earlier choices, and never posted any — FIXED

The selection was a `selected` flag on each entry in `this.options`. `fetch()` replaced
`this.options` with each query's results and carried the flag over only for rows that
appeared in them. Choose Ada under "ad", search "gr", and Ada was gone. Measured on 0.1.3:
pick one person under each of three queries and one token is left, the last.

It was worse than reported. The form posted nothing at all, because `sync()` only marked
`<option>`s already in the select, and a remote result never was one. A token whose option
was not in the current results could not be removed, because `choose()` looked it up in
`this.options`. And two requests in flight could resolve out of order, so an old query's
results could replace a newer one's.

**Fixed** by keeping the selection in its own `Map`, keyed by value in the order chosen and
separate from the options any query returns. Rendering reads `aria-selected` from it, tokens
come from it, and `choose()` looks there first. `sync()` adds an `<option>` to the select for
any chosen value it does not have, so the form posts what the reader sees. Only the latest
request may fill the list. The same three queries now leave three tokens, and the form posts
`1, 3, 4`. Going back to the first query shows Ada as chosen. `tests/combo.test.mjs` covers
all of this, and a slow reply arriving after a newer one.

**How it was found:** reported. The empty form post turned up when the test checked what the
form would actually send.

## 92. Under reduced motion every style change is a transition, so script reads the old value

`02-reset.css` sets `transition-duration: .01ms !important` on every element when the reader
asks for reduced motion. `transition-property` defaults to `all`, so every property change on
every element starts a .01ms transition, and until the current task ends, layout and
`getComputedStyle` see the old value. A script that writes a style and reads geometry in the
same task gets stale numbers, and only for readers who asked for reduced motion.

It surfaced in #89. The windowed list moves its wrapper's padding and then measures. Near the
end of a long list, layout used the old padding, the content was briefly shorter than the
scroll position, the browser clamped `scrollTop`, and the active option fell out of the drawn
slice. It took the run of 10,000 key presses to show.

**Worked around in the combobox:** `.combo-list` and its rows wrapper have
`transition-property: none`. Nothing on them was meant to transition.

**Not fixed in general.** The .01ms is deliberate: `transitionend` and `animationend` still
fire for code that waits on them, and `transition-property: none` in the reset would stop
those events. No other script in Deck has been audited for a write-then-measure in the same
task.

**How it was found:** while verifying #89, by watching `getComputedStyle` disagree with the
inline style it had just been given, and finding `padding-top` and `padding-bottom`
transitions in `document.getAnimations()`.

## 93. `.list` cut the focus ring off its own rows — FIXED

`.list` has `overflow: clip`, which it needs for its rounded corners, and a row is the
full width of the list. The global ring sits 2px outside the element and is 2px wide, so
4px of it fell outside the list on both sides of every row, and above the first row and
below the last. What a keyboard user saw was a single line along one edge.

**Fixed** in `src/07-components.css`. `.list-row:focus-visible` has `outline-offset: -2px`,
so the ring is drawn inside the row. While focused, the first and last rows take the
list's corner radius (`inherit`), so the ring follows the rounded clip instead of being cut
at the corners. The clip stays. Item 28, hover with no focus counterpart, still stands.

**How it was found:** reported, then measured by tabbing through a three-row list and
comparing each outline's box with the list's padding box.

## 94. `.scroller` clipped focus rings and snapped its first card flush to the edge — FIXED

Three problems in one container:

- `overflow-x: auto` turns `overflow-y` to `auto` as well, so the scroller clips on both
  axes. With no top padding, every focused card lost the top 4px of its ring.
- There was no `scroll-padding-inline`. Proximity snapping settled onto the first card's
  snap position as soon as the page loaded, which put it flush against the edge of the
  screen and cut the left side of its ring. Any card scrolled into view did the same.
- The fade in `src/20-gradients.css` covers the last 8% of the row. A card resting at the
  end edge had its ring drawn at about 40% opacity.

**Fixed.** The scroller has `padding-block: var(--space-1) var(--space-2)`, which adds 4px
above the row (content below it moves down 4px), and `scroll-padding-inline:
var(--space-gutter)`, matching its own inline padding, so the first and last cards rest at
the gutter. `.scroller:has(:focus-visible)` drops the fade. The overflow is unchanged.

**Side effect in a template:** `travel.html` has a page-local
`.chip-row { padding-block-start: 2px }`, which was a workaround for the same clip. Being
unlayered, it now overrides Deck's 4px with 2px, so the top 2px of a chip's ring is still
cut on that page. Deleting that declaration fixes it.

## 95. Chromium does not scroll a half-visible item into view on focus — NOT FIXED

Found while verifying #94. Tab to a card that is partly past the end edge of a scroller,
and Chromium-based browsers leave the scroll position alone, so the card and its ring stay
cut off by the edge of the screen. A card wholly out of view is scrolled in. Tested in Edge
155, and it happens the same way in a plain `overflow-x: auto` div with no Deck CSS, so it
is the browser's focus behaviour. Whether snapping is on makes no difference.

CSS cannot change it. `scrollIntoView({ block: 'nearest', inline: 'nearest' })` on
`focusin` does work, and with #94's `scroll-padding-inline` the card comes to rest at the
gutter. That would be a `deck.js` behaviour, and it was out of scope for a CSS fix.
Documented on the scroller page.

## 96. `.card-link` gave no card-level feedback for keyboard focus — FIXED

Hover lifted the card with a deeper shadow and a stronger border, but tabbing to the
stretched link changed nothing on the card. The ring went around the link text only, so a
keyboard user moving through a grid of cards had less to go on than a mouse user.

**Fixed:** the hover rule is now `.card-link:is(:hover, :focus-within)`. The ring stays on
the anchor, which is what has focus, and the card page now describes this rather than
saying Deck does not work around it.

## 97. `.nav-link` sat its icon on the baseline, so link height depended on the icon — FIXED

`.nav-link` set no `display`, so in `.navbar-links` it was a block, and an icon inside it
was an inline box on the text baseline. Whether that pushed the line box taller depended on
the icon's size and on the font's ascent. With the default system font, `.icon` and
`.icon-sm` happened to fit, but `.icon-lg` made the link 3px taller. A bare `<svg>`,
which the reset makes `display: block`, put the icon on a line of its own and made the
link 56–60px tall. A row of mixed links went uneven, and so did their hover and current
backgrounds.

**Fixed:** `display: inline-flex; align-items: center; gap: var(--space-2)`. The icon is
centred on the label, and any icon up to the line height leaves the link at the same height
as a text-only one, whatever the font. In every template the computed heights are
unchanged. One docs page had already patched this with `inline-flex items-center gap-1`.
That still works, and its `gap-1` wins.

## 98. A `.btn` label split at a `<span>` got the gap instead of a word space — FIXED

`.btn` is a flex row with an 8px gap. In `Pay <span>$8</span>` the text and the span are
two flex items, and the space between them is dropped at the end of the first item. So the
words were held apart by the gap, which is twice a 4px word space. Templates had been
working around it by wrapping the whole label in an outer span (`settings.html`,
`product.html`).

**Fixed** in `src/05-buttons.css`:
`.btn > span:not([class], [hidden]):not(:has(*)) { display: contents; }`. A bare span's
text joins the run, and it measures the same as the unsplit label. Each exclusion is there
for a reason:

- **A span with a class keeps its box.** `.badge`, `.avatar`, `.sr-only` and spans with a
  user's own class need one, and Deck cannot tell a box class from a text class.
- **A span with children keeps its box,** so the existing outer-span workaround is left
  alone.
- **`[hidden]` is excluded** because Deck has no `[hidden]` rule. The attribute relies on
  the browser's `display: none`, which any author rule beats.

Without `:has()` the selector is invalid and the old behaviour remains.

**Still affected:** `checkout.html`'s `Pay <span class="nums">…</span>`, because the span
has a class. Wrap the label in one bare span, as `product.html` does.

## 99. `.form-actions-sticky` sat underneath `.tabbar` — FIXED

Both are pinned to the bottom of the viewport, and the tab bar has the higher z-index, so
while a long form scrolled, the tab bar covered the action row. At 375px the overlap was
the tab bar's full height.

**Fixed.** `body:has(.tabbar)` in `src/08-mobile.css` now declares its existing arithmetic
once, as `--tabbar-clearance: calc(var(--tap) + var(--space-4) +
env(safe-area-inset-bottom))`. It sets `0px` from 48rem, when the tab bar hides. The
body's padding reads the variable, and so does
`body:has(.tabbar) .form-actions-sticky { inset-block-end: … }` in `src/06-forms.css`. The
bar drops its own safe-area padding while the tab bar is showing, because the tab bar
already clears the home indicator. The tab bar in the templates is 57.6px tall against
60px of clearance, so a strip of about 2px shows between the two bars.

`.toast-region` repeats the same arithmetic in `src/13-toasts.css` and could read the
variable too. `.fab`, `.speed-dial` and `.back-to-top` add `--space-3` on top of it.

**How it was found:** reported. Items 93–99 were checked with a computed-style diff of
all 20 templates at 375px and 1280px, before and after. Apart from these rules, the only
difference was animation noise on a spinner, confirmed by a run that diffed the old build
against itself.

## 100. Templates reached for icons the sprite did not have — FIXED

Building templates hit `#inbox` and `#arrow-down-right`, neither of which existed, and a
media app needed transport and media glyphs the sprite had none of. A `<use>` that names
a missing symbol renders nothing and raises nothing, so each of these was an empty box
found by looking.

**Fixed:** `tools/icons/icons.txt` gained `inbox`, `arrow-down-right` (`south_east`, the
pair of `arrow-up-right`) and a Media section: `play`, `pause`, `skip`, `fullscreen`,
`tv`, `film`, `music` and `broadcast`. The generator writes both cuts of each, so 86
icons and 174 symbols. `film` is the film strip (`theaters`), not the clapperboard, and
`broadcast` is the on-air mark (`sensors`). There is one `skip`, pointing forward; a
previous-track button flips it with `scale: -1 1`. The docs icon page now lists the whole
set, read from the sprite when the page renders.

**Found on the way:** `menu-book` was in `src/deck-icons.svg` but not in `icons.txt`. It
had been copied into the generated file by hand after 0.1.3, so the next `npm run icons`
would have dropped it silently. It is in the manifest now. Regenerating changed no
existing symbol by a byte: the only difference is the 20 new ones.

## 101. The `-sm` cut of a pointing icon did not mirror under `dir="rtl"` — FIXED

`src/19-logical.css` mirrors the sprite's pointing icons with
`.icon:has(use[href$="#chevron-right"])` and so on. `$=` matches the end of the href, so
`#chevron-right-sm` never matched, and a small chevron or arrow kept pointing the
reading-order-wrong way. Nobody noticed because nothing rendered the `-sm` cuts with
`.icon-sm` automatically: `Deck::icon()` and deck.js's own icon helper both used the
regular cut, which is fixed later in this release. Making them pick the `-sm` cut would
have turned this latent miss into a regression in every date picker under RTL.

**Fixed:** every pointing icon is listed with its `-sm` cut, and `arrow-down-right` joined
the list. `#play` and `#skip` are deliberately not in it; a media timeline runs left to
right in every locale. Covered by a test in `tests/layout.test.mjs`.

## 102. The sprite's counts were typed into six files — FIXED where it matters

"152 symbols: 75 icons" was in README.md and llms.txt, and `tools/sync-sizes.mjs` matched
the README row with the literal `152 symbols`. The docs pages for the footer and the video
said 77, the adapters said 56 in one comment and 74 in `Deck.adapters.report()`, and the
real count was 76. Nothing failed when they drifted, because only the KB figures were
synced.

**Fixed:** `sync-sizes.mjs` counts the symbols and the `-sm` cuts in `src/deck-icons.svg`
and stamps both into README.md and llms.txt. Adding an icon no longer means editing the
tool. The two docs pages say it without a number, the icon page counts the sprite at
render time, and the remaining hand-kept figures (the comment in `public_html/index.php`,
`tools/vscode/README.md`, `deck-adapters.js`) were corrected to 86 and 174.

## 103. The brand hash check depends on how git wrote the sprite's line endings — NOT FIXED

`build.mjs` refuses to build when the sha256 of `src/deck-icons.svg` differs from the
hash in `src/brand/sources.json`. The hash is taken over the file's text as it is on disk.
This repository has no `eol` rule for the sprite, and with `core.autocrlf=true` (the Git
for Windows default) a checkout writes it with CRLF, which hashes differently (`4aa4c7…`
against the recorded `b9a909…` for the current sprite). So a fresh Windows clone, or any
`git checkout` or `git stash` that rewrites the file, fails the build with "Brand assets
are stale" when nothing about the artwork changed. The generator writes LF, which is why
the working copy here passes.

**Recommended:** hash the text with line endings normalised (`.replace(/\r\n/g, '\n')`) in
both `build.mjs` and `tools/make-brand.mjs`, or add `src/deck-icons.svg text eol=lf` and the
same for `src/brand/*.svg` to `.gitattributes`. Left alone because it is unrelated to the
icons, but the icon change is what would expose it first: the new hash was written from
an LF file, as the previous one was.

## 104. Six small layout gaps, found by building a media app — FIXED

Each of these was worked around with page-local CSS before it was added:

- **Posters had no ratio.** `.ratio-16x9`, `.ratio-4x3` and `.ratio-1x1` all crop to
  cover; a film poster is 2:3. Added `.ratio-2x3`, beside the others in the layout layer.
- **Nothing could undo the crop.** Added `.object-contain`. It is a utility on purpose:
  the utilities layer comes after the layout layer, so `.ratio-16x9.object-contain`
  letterboxes without a specificity fight.
- **Inputs had one size.** `.btn-sm` and `.btn-lg` had no input to stand beside. Added
  `.input-sm` (34px) and `.input-lg` (50px). They set height, block padding and type size
  only, so `.select` keeps its arrow padding and `.search` its icon padding, and they sit
  before the coarse-pointer rule so every control is still 16px on touch. `.input-sm` is a
  smaller target than the default, which `.btn-sm` avoids with a pseudo-element an
  `<input>` cannot have; the input page says so. `.input-lg` keeps the default block
  padding, because the first draft's 12px made it 50.6px against the button's 50.
- **A list in a card was a box in a box.** Added `.list-flush`: no border, radius or fill,
  dividers kept.
- **Stacking.** The docs say `.z-1` is the only number on purpose, and that anything else
  belongs to a named layer. That argument was kept: the three additions are `.z-sticky`,
  `.z-nav` and `.z-sheet`, each reading its token, for an author's own sticky bar, player
  bar or slide-over. There is no `.z-modal` or `.z-toast`; at those layers the answer is
  a `<dialog>` or `Deck.toast()`.
- **A long URL pushed its row wide.** The body sets `overflow-wrap: break-word`, which
  wraps a long string but leaves the element's min-content width at the string's full
  length, so a flex item or a table cell still grows to fit it. `.break-words` is
  `overflow-wrap: anywhere`, which lowers the minimum too. A test checks both halves.

**Also changed:** `tools/docs/classify.mjs --accept` rewrote `api-decisions.txt` from
scratch and dropped anything after a `#`, so a reason written beside a decision would not
have survived the next run. It now keeps the text after the `#`, writes it back, and uses
it as the decision's reason in `dist/api-buckets.json` instead of the rule's generic one.
Every class added in this pass has its reason recorded that way.

## 105. `.g-scrim` painted under a photo child, so its caption sat on the bare image — FIXED

`.g-scrim`'s `::after` has `z-index: -1` inside an isolated stacking context, and
`.g-scrim > *` lifts every child to `position: relative`. So the scrim only worked when the
photo was the element's own `background-image`. With the photo as an `<img>` child, the
usual markup, the image painted over the scrim, and the caption sat directly on the photo.
The docs example on `gradients.php` showed exactly that: white text on a light grey image,
captioned "Readable on a light image, because the scrim is underneath". `ecommerce.html`,
`consulting.html` and `jumbotrons.html` had each worked around it by giving their photo
`position: absolute; z-index: -2`.

**Fixed** in `src/20-gradients.css` by doing what the templates did:
`.g-scrim > :is(img, picture, video, canvas, svg, iframe, .lazy, .video) { z-index: -2; }`.
The media child paints before the scrim and every other child after it, as before. Moving
the scrim above the children instead was rejected, because a caption written as bare text
inside `.g-scrim` would then have gone under it. A photo wrapped in an element that is not
in that list, such as a plain `<a>`, is still lifted above the scrim. The templates' own
rules are unlayered, so they win and render as before. Found while building the
theme-aware scrim, which has the same structure.

## 106. Four components a media app wrote for itself — FIXED

- **An alert could not be dismissed.** The alert page said Deck ships no close button
  and suggested `.btn-icon.btn-ghost.btn-sm`, which hides nothing. Added `.alert-close`:
  a 44px square at the end of the row that reaches into the alert's padding, so a
  dismissible alert is exactly as tall as one without (a test measures both), and a
  delegated listener in deck.js that sets `hidden` on the alert. It listens on the
  document rather than on each alert, because alerts are the component most often
  inserted after load — a form result, a failed request — and those would otherwise need
  a `Deck.init()`. A cancellable `deck:dismiss` fires first. Deck deliberately does not
  move focus after the button disappears; the alert page says so and shows where the
  event lets you do it.
- **A short last row stretched across the page.** `.grid` uses `auto-fit`, which
  collapses empty tracks. Added `.grid-fill` (`auto-fill`). The grid page had described
  `auto-fill` as "right for a calendar and wrong for a list of cards", and its Overriding
  example built exactly this class by hand. That is true of a single row of cards and
  false of a shelf of posters or search results, where the last row should match the
  rows above it; the page now says which is which. `.grid-fill` is a grid on its own, so
  it does not repeat the `.grid-fixed-N` mistake recorded below, and print unwinds
  it as it does `.grid`.
- **No frosted surface.** Added `.glass`: `--surface` at `--glass` (76%) with
  `saturate(1.6) blur(16px)` behind, so it follows the theme. It is in `deck.effects`,
  which beats the component layer, so it works on a `.card`, a `.panel` or a sheet
  without a specificity fight. Without `backdrop-filter` it goes to 94%, and under
  `prefers-reduced-transparency: reduce` it is the opaque surface. Contrast was worked
  out rather than assumed: body text stays above 7:1 over a pure black or pure white
  backdrop, muted text falls to about 3:1 in the worst case, and the card page says so.
- **The scrim was dark in both themes.** `--scrim` is 12% lightness by design, for white
  text, so a title over a poster could not follow a light theme. Added `--scrim-auto`, the
  same eased stops in `--surface`, and `.g-scrim-auto`, which uses it and sets
  `color: var(--text)`. It is built like the fixed `.g-scrim` (#105), so a photo child
  sits under it.

## 107. `.grid-fixed-N` was not a grid unless `.grid` was beside it — FIXED

`.grid-fixed-2`, `-3` and `-4` set `grid-template-columns` and nothing else. On an
element without `.grid` that property does nothing, so `class="grid-fixed-3"` was a block
with three ignored column definitions: no error, no columns, no gap. Every example wrote
`grid grid-fixed-3`, so the dependency was learned by copying rather than stated.

**Fixed in the API rather than the docs:** each fixed grid now declares `display: grid`
and `.grid`'s `gap: var(--gap, var(--space-4))` itself. `grid grid-fixed-3` computes
exactly as before. The print sheet, which unwinds `.grid` with `display: block`, now
names the fixed grids too, so a standalone one prints as a `.grid` one did. `.grid-fill`
was written standalone from the start for the same reason.

**Still the same shape:** `.grid-2` also sets only `grid-template-columns` and so also
needs `.grid`. It was left alone because it is already recorded as a duplicate of
`.grid-wide` that ought to go, and making it standalone would give it a reason to stay.

## 108. `.split-rail-start`'s comment, and the split docs, described the opposite of what it does — FIXED

The comment said the class "puts the rail on the starting edge without touching markup
order, so the main column stays first for a screen reader". The rule only swaps the two
track widths. Grid places children in source order, so with the main column first, as the
comment advised, the main column went into the 20rem rail track and the rail stretched
across the rest. `split.php` repeated the claim, and its live example demonstrated the
broken layout under a caption saying the opposite, while its accessibility notes built an
argument about deliberately separating visual and DOM order on it.

**Fixed** by correcting the comment and the docs, not the CSS. The rule is right as a
layout: rail first in the markup, first on screen, first for the keyboard, which is what
section navigation wants and what the `first-page.php` tutorial already wrote.
Making the CSS match the old comment would have meant moving content visually away from
its reading order, which is the thing WCAG 1.3.2 warns about. The example now puts the rail
first; the accessibility notes say neither variant reorders anything. While there,
`first-page.php` said `.split` goes to two columns at 48rem; it is 64rem.

## 109. SortableJS loaded after Deck never took over, and the fallback ignored the handle — FIXED

`deck-adapters.js` called `Deck.init()` as soon as it ran if `document.readyState` was not
`loading`. With deferred scripts it is already `interactive`, so with Deck deferred and
SortableJS deferred after it (or loaded `async`), the adapter initialised before SortableJS
existed and every `[data-deck-sortable]` list went to the native fallback. The adapters'
`once()` guard then marked the list done, and the second `Deck.init()` at
`DOMContentLoaded`, by which time SortableJS was there, skipped it. So drag handles failed
on the first load of the page. The native fallback made it worse: it made every row
`draggable` and never read `data-handle`, so the row dragged from anywhere, and on touch
not at all.

**Fixed** in `src/js/deck-adapters.js`:

- `data-deck-sort` records the engine (`native` or `sortable`) in place of the once-guard.
  A list on `native` is handed to SortableJS whenever Deck initialises and finds it: its
  native listeners are on an `AbortController` and are aborted, and the `draggable`
  attributes are removed.
- deck.js already initialises again at `DOMContentLoaded`, which catches a deferred
  SortableJS. The adapter also looks once more at `load`, which catches an `async` one.
  Later than that, the docs say to call `Deck.init()`.
- The fallback honours `data-handle`: a row is draggable only while the press is on its
  handle, as SortableJS behaves. Its `dragover` used to find the row under the pointer by
  `[draggable="true"]`, which in handle mode is only the row being dragged, so that lookup
  is by containment now.

Three tests load a real page through a routed origin, with Deck deferred and a stand-in
SortableJS deferred, async and late, or absent. All three fail against the previous
`deck-adapters.js`.

## 110. `Deck::head()` could not be used under a strict Content Security Policy — FIXED

`Deck::head()` printed an inline `<script>` to set `Deck.iconSprite` on
`DOMContentLoaded`, an inline `<style>` when a hue was configured, and script and link
tags, none of which could carry a nonce. Under a nonce-based policy (`script-src
'nonce-…' 'strict-dynamic'`, the configuration CSP guidance recommends) every one of them
was blocked: no stylesheet, no Deck, four console violations. The only way out was to stop
using the helper.

**Fixed** in `php/Deck.php`. `head()`, `css()` and `js()` take an optional `$nonce`, which
goes, escaped, on the `<link>`, every `<script>` and the hue `<style>`. The inline script is
gone rather than nonced: deck.js already read a `data-deck-icons` attribute on its own
script tag (`bin/deck.mjs init` even prints it), so `js()` now puts the sprite URL there,
on `deck.js` or the bundle. The nonce is a parameter, not a `configure()` option, because
it changes every request and static configuration outlives the request on a long-running
PHP server. Checked by serving the helper's output through a routed origin under
`default-src 'self'; script-src 'nonce-n0nce' 'strict-dynamic'; style-src 'nonce-n0nce'`:
with the nonce, no violations, Deck loads, the sprite resolves and the hue applies; without
it, and with the old helper, the page is blocked as before.

**Not fixable here:** `Deck::theme()` returns a `style` attribute for the `<html>` tag, and
a nonce cannot allow an attribute; under a `style-src` without `'unsafe-inline'` it is
dropped. `php.php` now says so and points at the `hue` option with `head(nonce: …)`, which
writes the same custom property into a nonced `<style>`.

## 111. `Deck::icon()` and deck.js drew `.icon-sm` with the regular cut — FIXED

Every sprite icon has a `-sm` cut at weight 500, drawn to hold up at 16px, and the icon
page explained that CSS cannot pick it, so the pairing is on the author. Deck's own two
helpers did not make it either: `Deck::icon('x', 'icon icon-sm')` wrote `#x`, and so did
every `icon(…, 'icon icon-sm')` call in deck.js — the date picker's chevrons, the combobox
toggle and checks, the toast close button.

**Fixed:** both helpers append `-sm` when the class list has `icon-sm` and the name does
not already end in it. The PHP helper skips the two hand-authored brand marks, which have
no small cut; deck.js only ever renders font icons. This depended on #101 first: the RTL
mirror did not match `-sm` ids, so this change alone would have left the date picker's
small chevrons pointing the wrong way under `dir="rtl"`.

## 112. `.video-poster` put its play button below the frame, where it was clipped — FIXED

`.video-poster` is a grid with `place-items: center`, and its two children, the poster
`<img>` and the `.video-play` disc, were two grid items, so they took two rows. The image
row was the image's natural height (360px in a 270px frame in the docs example), and the
disc sat in the second row, below the frame, where `.video`'s `overflow: clip` hid it. Every
poster façade built from the documented markup showed no play button, the docs' own two
examples included. Found while switching those examples to the new `#play` icon and
noticing the icon never appeared.

**Fixed** in `src/24-media.css`: the poster image is `position: absolute; inset: 0`, so it
fills the frame out of flow and the grid centres the disc alone. The paint order is
unchanged: image, then the scrim (`::after`), then the disc at `z-index: 1`. Not on the list
this pass was working from, so it is its own commit. A test checks that the image fills
the frame and the disc is centred.

## 113. `.btn-primary` ignored `--brand`, so the documented exact-brand override missed it — FIXED

The theming guide and the "why one hue" page both give the same recipe for a brand colour
the ramp cannot produce: set `--brand` and `--brand-hover` on `:root` in an app layer.
Links and outline buttons follow it. `.btn-primary`, the most visible brand surface on a
page, did not: it set `--btn-bg: var(--brand-600)` and its hover `var(--brand-700)`,
ramp steps that the recipe does not touch. Measured in Edge, with
`:root { --brand: #0b5cff }` the link turned blue and the primary button stayed teal,
`oklch(51.6% .118 196)`. Found while measuring scoped theming (#114).

Honouring `--brand` and keeping the old look in both schemes cannot both be done. In dark
mode `--brand` is the lighter 400 step (for text on a dark page), while the button filled
with 600 in both schemes, and CSS cannot tell an overridden `--brand` from Deck's default.
The choice made was to honour `--brand` in both schemes.

**Fixed** in `src/05-buttons.css`: `.btn-primary` fills with `var(--brand)` and hovers to
`var(--brand-hover)`. Light mode is unchanged, value for value; a computed-style diff of
all 19 templates in both schemes changed nothing outside a `.btn-primary`. In dark mode the
default primary button is now the 400 step at rest and 300 on hover, like a link, and its
text is black: `contrast-color(var(--btn-bg))` where the browser has it, and otherwise
black or white picked from the fill's own lightness with relative colour, because white
manages about 2.7:1 on the 400 step and `--text-on-brand` stays white. The same change
shows on a light page inside an always-dark band (`data-theme="dark"` or
`color-scheme: dark`), in jumbotrons, sidebars, it-agency and pricing. With the override,
the button is `#0b5cff` in both schemes with white text (5.26:1).

`--text-on-brand` itself is unchanged, because every other consumer pairs it with a
`--brand-600` fill: `.badge-solid`, `.bg-brand`, the current page in `.pagination`, a
pressed `.btn-soft`, the date picker's selected day, and more. Those fills still ignore a
`--brand` override; they are ramp fills by design, and the docs now say which surfaces
the recipe reaches. A test covers the default in both schemes and the override.

## 114. A `--hue-brand` below the root rethemed almost nothing — FIXED with `data-theme-scope`

A custom property is computed on the element that declares it, and its children inherit
the finished value. `--brand-600: oklch(51.6% var(--chroma-brand) var(--hue-brand))` and the
other brand tokens were declared on `:root` only, so they were worked out once, from the
root's hue. `Deck.hue()` worked because it writes to `<html>`, the same element. A
`--hue-brand` on a `<section>` was inherited and read by almost nothing. Measured in Edge,
of the 110 colours on a kit of brand components that `Deck.hue()` changes, a section with
`Deck::theme(hue: 24)` on it changed 6 (`.g-mesh` and chart series 2 and 3). A scoped
`--chroma-brand` changed none of 104. It made no difference whether the hue came from an
inline style, script or a stylesheet class. `Deck::theme()` itself was only ever
documented on `<html>`, where it works; the theming guide warned that a scoped hue did
nothing, and the token reference said "set one anywhere in the tree", which is not true of
an input.

Exactly 18 tokens read `--hue-brand` or `--chroma-brand`, as 19 declarations
(`--text-on-brand` is declared twice, static and in the `@supports contrast-color()`
block): `--brand-50` to `--brand-950`, `--brand`, `--brand-hover`, `--brand-soft`,
`--brand-soft-text`, `--text-on-brand`, `--focus` and `--ring`. All ten hue and chroma
inputs together reach 72.

Three ways were measured on a 2,006-element page, 12 interleaved rounds, medians:

- **Declare the 18 on `*`.** Works for any authoring method, but every element works them
  out: `Deck.hue()` +16%, a theme toggle +25%, first style +22%, on every page, scoped or
  not. It also breaks overrides set above the element that reads them, silently: the
  exact-brand recipe (`:root { --brand: … }`) and the token reference's own
  `<section style="--brand-soft: var(--good-soft)">` example both reverted below their
  element.
- **Declare them on `:root, [style*="--hue-brand"], [style*="--chroma-brand"]`.** No
  measurable cost, but it depends on how the hue is written (an inline style works, a
  stylesheet class does not), and it would turn every `.g-mesh style="--hue-brand:34"`,
  the pattern the gradients page recommends, into a theming scope.
- **Declare them on `:root, [data-theme-scope]`.** No measurable cost, explicit, and
  independent of where the hue comes from. Chosen; a data attribute rather than a class,
  so the class API is unchanged.

**Fixed** in `src/01-tokens.css`: the 19 declarations are one rule,
`:root, [data-theme-scope]`, with the `@supports` override of `--text-on-brand` split the
same way. `tools/docs/extract.mjs` now counts a rule as declaring global tokens if any of
its selectors is the root, not every one, or the 18 would have dropped out of
`dist/api.json` (152 tokens before and after). `Deck::theme()` takes `scope: true` and then
adds `data-theme-scope` beside the custom properties; without it the output is
byte-identical to before for all 60 argument combinations tried.

Measured after the fix:

- Regions marked with the attribute follow on all 110 colours and all 18 tokens, whether
  the hue is from `Deck::theme()`, a stylesheet class or script, and nested scopes each use
  their own hue. Two regions, at 24 and 265, sit side by side correctly, chart series
  included.
- `Deck.hue()` on the root paints the same as before: 0 of 7,680 values differ, light and
  dark, at four hues. A computed-style diff of all 19 templates against the previous build
  changed nothing outside #115.
- A hue on an element without the attribute behaves as before: 8 of 110 colours follow
  now rather than 6, the extra 2 being chart series 1 (#116). A `.g-mesh` with an inline
  hue, and `.g-mesh-warm`, change exactly the values they changed in 0.1.3 unless a
  `.chart` sits inside, where series 1 now follows too.
- A `--brand` override on `:root` still works at the root and stops at a scope, which
  works its own `--brand` out; set on the scope element, it reaches inside. Intended, and
  documented.
- Cost, unthrottled: `Deck.hue()` 12.6ms before and 12.9ms after, a theme toggle 13.1 and
  13.4ms, first style 11.4 and 11.6ms, inside the ±6% noise of two identical builds. With
  ten scoped cards, `Deck.hue()` is 15.3ms against 15.2ms for the same markup on the
  previous build, where the inline hues do nothing: the extra cost of a page with ten hues
  comes from the ten hues, not from working the ramp out again.

Not fixed: the other hues (`--hue-neutral`, `--hue-good` and so on) are still root-only.
Tests cover two regions, nesting, a bare hue, and the override at a scope.

## 115. `data-theme` on a wrapper gave dark text on dark surfaces — FIXED

`data-theme="dark"` on an element switches its `color-scheme`, so every `light-dark()`
token inside it takes the dark value. Body text does not: `body { color: var(--text) }` is
worked out once, on `<body>`, and inherited as a finished colour. A dark section on a light
page therefore drew dark cards with dark text on them: 22.6% lightness text on a 16.2%
surface, 1.14:1, and 1:1 on `--surface-2`. Of 412 values a root `data-theme="dark"`
changes on a kit of components, a wrapper changed 182. `Deck::theme(mode: 'dark')` on a
wrapper did the same, since it emits the same attribute. Found while measuring #114.

**Fixed** in `src/01-tokens.css`: `:where([data-theme]:not(:root)) { color: var(--text); }`,
at zero specificity, so any colour a component sets still wins. Card text in a dark
wrapper is now 16.96:1, text on `--surface-2` 14.91:1, and the wrapper follows on 428 of
428 values. The root is untouched. In the templates, the only values that changed were
the inherited colour of four containers in the sidebars template's dark rail, which set
no colour and draw no border; their visible children set their own. The footers and
jumbotrons dark sections already set their colours. The element gets no background of its
own; the dark-mode guide now says so. A test checks the contrast.

## 116. A chart given its own hue kept series 1 in the page's colour — FIXED

`.chart` computes `--c2` to `--c6` from `--hue-brand` on the chart, but `--c1` was
`var(--brand-500)`, which is worked out on the root. A chart under a local hue, such as
inside a `.g-mesh` with an inline hue, or a `.chart` given one directly, had series 2 to 6
in the new hue and series 1 in the page's. Inside a `data-theme-scope` region (#114) this
would have been right anyway, because `--brand-500` is worked out again there.

**Fixed** in `src/14-charts.css`: `--c1: oklch(60.8% var(--chroma-brand) var(--hue-brand))`,
the `--brand-500` recipe written out. At the root's hue it is the same colour. One thing
changes: an app that overrode `--brand-500` itself, which the theming guide advises
against, no longer moves series 1.

## 117. The theming guide said `Deck.hue()` keeps up "sixty times a second" — FIXED in documentation

The slider demo's caption said the whole page follows sixty times a second. `Deck.hue()`
sets an inherited custom property on the root, so every element on the page is restyled on
every call. Measured in Edge on a 2,006-element page, that is about 12ms of style
recalculation per call on a desktop, which fits a 60fps frame with little to spare, and
about 97ms with the processor slowed four times to stand in for a low-end phone, about ten
updates a second. Nothing about the page's layout changes, so this is style alone.

**Fixed in documentation:** the caption no longer claims a rate, and the guide and the
`Deck.hue()` reference give the measured numbers. The guide suggests previewing on one
`data-theme-scope` region, which restyles only that region (about 0.5ms for a card), and
applying the hue to the root on `change` rather than `input`. Not fixed in code: the cost
is what an inherited custom property on the root costs.

## 118. `.glass` was one fixed surface, and without `backdrop-filter` it let a sharp photo through — EXTENDED

The brief for a frosted surface said the name was unused. It was not: `.glass` shipped in
#106, earlier in 0.1.4, as `--surface` at `--glass` (76%) with a fixed `blur(16px)`. 0.1.4
is unreleased, so it was extended in place rather than added beside, and its knob renamed
without an alias. What changed, in `src/20-gradients.css`:

- **Three knobs:** `--glass-blur` (16px), `--glass-tint` (72%, replacing `--glass` at
  76%) and `--glass-edge` (22%). `.glass-sm` and `.glass-lg` set only the blur (8px, 32px)
  and `.glass-solid` only the tint (90%), so `.glass-lg.glass-solid` works.
- **A top-edge highlight, weaker in dark mode,** through `light-dark()`: white at
  `--glass-edge` in light, a third of it in dark. At full strength in dark mode it read as
  a bright seam along the top. It is a 1px background layer, not an inset box-shadow:
  `box-shadow` is one property and `deck.effects` beats the components, so it would have
  wiped out `.card`'s shadow, `.card-raised`, the `.card-link` lift and any focus ring drawn
  with box-shadow. A test checks that a `.card.glass` and a `.card-raised.glass` keep their
  shadows and radius, and that a `.bar.glass` gets no radius and no border.
- **The border is `--line-strong` at 60%**, and only its colour is set. A host without a
  border gets none, for the same reason it gets no corners.
- **No `backdrop-filter`: opaque, not 94%.** The fallback is `--surface` with the
  component's own border (`border-color: revert-layer`), so a `.card.glass` is exactly a
  `.card`. With the blur removed and no fallback, muted text measured 3.1–3.3:1 over the
  test stripes, with the sharp detail showing through.
- **`prefers-reduced-transparency: reduce`:** this was already there from #106 (the brief
  thought it was the first in Deck). It now also reverts the border, the same as the
  fallback. Both are tested. The fallback is tested by rewriting the stylesheet the way an
  engine without the property would read it, and reduced transparency through CDP, because
  Playwright has no option for it.
- **Both `backdrop-filter` traps are in the source comment and on the card page:** a
  backdrop-filter is a backdrop root, so a `.glass` inside `.sticky-top`, `.tabbar` or
  another `.glass` blurs nothing; and it creates a containing block, so a `position: fixed`
  descendant is placed against the panel. The brief named `.topbar`, which is not a Deck
  class (cover.html has a page-local one).

**Measured** in Edge, worst pixel behind the text, over a real photo / pure black behind
light glass / pure white behind dark glass. `.glass` body text: 10.4:1 and 9.5:1 over the
photo (light, dark), 8.58:1, 6.98:1. `.glass` muted text: 4.05:1 and 4.19:1 over the photo,
3.33:1, 3.10:1. `.glass-solid` muted text: 5.63:1 and 6.43:1 over the photo, 5.28:1,
5.93:1. The brief's 72% is a little less legible than the 76% it replaces. At 76%, dark
muted text over the photo was 4.59:1 and dark body text over white 8.07:1, so both crossed
a line. The card page now gives these figures in place of the old "above 7:1" claim,
and points muted text at `.glass-solid`. `.glass-lg` barely helps (muted 4.17:1 over the
photo). In forced colours the surface becomes Canvas at 72%. Text was 12.88:1 over the
photo, so it needs no rule.

`deck.min.css`: +307 bytes raw, +85 gzip, +65 Brotli.

**Not changed:**
- cover.html has its own `.glass` in `@layer app.pages`, which beats `deck.effects`, so it
  overrides the framework's background, border, blur and shadow, and ignores both
  fallbacks. Its `--glass` (a colour on `:root`) also made the old knob invalid at
  computed-value time on that page. That was hidden because its own background won, and the
  rename ends the clash.
- The other blurred surfaces (`.sticky-top`, `.tabbar`, `.form-actions-sticky`, the
  carousel arrows, the video play button, `.modal::backdrop`) still hardcode their own
  values and ignore `prefers-reduced-transparency`. The `.glass` rule is the pattern to copy.

## 119. The brief asked for a theme-aware `.g-scrim`; one existed, so it was finished instead — EXTENDED

The brief said `.g-scrim` is dark in both themes and that text over a photo therefore
cannot follow a light theme. Both were true of 0.1.3. But `.g-scrim-auto` and
`--scrim-auto` had already been added for the media app (#106), and they do exactly that.
What was missing was what this brief asked for beyond the colour:

- **The dark half did not match.** `.g-scrim-auto` used `--surface` in both themes: 90/70/32%
  of `--ink-950` in dark mode, where `.g-scrim` uses 12% lightness at .82/.62/.28. So moving a
  page from one class to the other changed its dark mode. `--scrim-auto` is now
  `light-dark()` per stop, since `light-dark()` takes colours and not gradients. The light
  half is unchanged and the dark half is `--scrim`'s own stops. `.g-scrim-auto.g-scrim-top`
  does the same against `.g-scrim-top`. Measured, `.g-scrim-auto` in dark mode is
  pixel-identical to 0.1.3's `.g-scrim` and to the pre-change build, on both photos, for
  both directions.
- **No strength knob.** Added `--scrim-strength`, a multiplier on every stop's alpha, default 1,
  on all four combinations.

**Not done: flipping `.g-scrim` itself.** The brief asked for `.g-scrim` to turn light in a light
theme "so existing pages do not shift". That would shift them. 0.1.3 documents `.g-scrim` as
"dark in both themes, for white text", and every light-mode use in this repo relies on it:
the index.php demo (`color:#fff`) and the gradients.php example (`oklch(98% 0 0)`) would
both have become white text on a white floor. It stays dark. The theme-aware scrim is the
class beside it, which costs a page one class name. Flipping it is a five-line change if
that is wanted anyway, with a changelog entry saying light-mode white text needs
`.g-scrim-auto` or `data-theme="dark"`.

**How the knob works, and two designs rejected:**
- `opacity` on the `::after` would have been one line, but it can only weaken, and the
  measurements below show the default needs to go up for muted text.
- Writing the stops into the class with `var(--scrim-strength, 1)` would have cut the class
  off from its token, so a site that retuned `--scrim` on `:root` would lose it on
  `.g-scrim`. Declaring the knob inside the token does not work at all, because a custom
  property's `var()` is substituted where the property is declared, on `:root`, so a knob
  set on the element would never reach it.
- What shipped: the `::after` declares `--scrim-scaled`, the stops with
  `calc(.82 * var(--scrim-strength))` and no fallback, and paints
  `var(--scrim-scaled, var(--scrim))`. While the knob is unset, `--scrim-scaled` is invalid
  at computed-value time, so `var()` takes the token, which is the same declaration path as
  before. Once it is set, the stops scale. A test pins that a retuned `--scrim` still
  reaches `.g-scrim`, and that `--scrim-strength: 1` computes the same as unset. Opacity
  clamps at 1, so 2 makes the bottom two stops solid. `color-mix()` clamps a percentage
  above 100% as the spec says (checked in Edge 155). `.g-scrim-top` has no token, so it
  uses `var(--scrim-strength, 1)` directly.

**Measured** in Edge 155 from screenshots, as WCAG ratios against the text's own colour.
Text colours: light `--text` oklch(22.6%), `--text-muted` 47.6%; dark `--text` 95.4%,
`--text-muted` 70.8%; `.g-scrim` captions `oklch(98% 0 0)`.

*Worst case for any photo.* Pure white under the dark scrim and pure black under the light
one. This shows how far up from the bottom text still clears 4.5:1:

| | strength 1 | 1.3 | 1.5 |
|---|---|---|---|
| light floor, `--text` | 34.5% | 42.7% | 46.2% |
| light floor, `--text-muted` | 6.9% | 25.8% | 30.1% |
| dark scrim, `--text` | 24.6% | 35.0% | 39.6% |
| dark scrim, `--text-muted` | 2.6% | 22.6% | 28.8% |
| `.g-scrim`, white | 26.2% | 36.2% | 40.6% |

At the bottom edge, at 1: light `--text` 13.51:1, muted 5.24:1; dark `--text` 11.02:1,
muted 4.89:1. At 22% up: 8.03 / 3.11 and 5.14 / 2.28.

*Two real photos.* A snowfield (light at the bottom, Unsplash 1418985991508) and a night
forest under stars (dark, Unsplash 1519681393784). Same 640×360 figure and same caption in
both themes: a `--text-lg` semibold title and a `--text-sm` muted line, in
`--space-5` padding. Figures are the worst pixel behind each line:

| photo, theme | title at 1 | muted at 1 | title at 1.3 | muted at 1.3 |
|---|---|---|---|---|
| snow, light | 11.12 | 4.79 | 15.10 | 6.16 |
| snow, dark | 5.43 | **3.51** | 11.19 | 6.54 |
| night, light | 8.70 | **4.17** | 14.13 | 5.99 |
| night, dark | 10.61 | 7.20 | 14.73 | 7.71 |

So the title clears AA in both themes on both photos at the default. The muted line fails in
each theme on the photo that works against it, and 1.3 fixes both. The dark-mode failure was
already in 0.1.3 for muted text on `.g-scrim`. The default cannot be retuned without breaking
"dark mode identical", so the fix is documented: 1.3 under muted text.

**Can a light scrim carry text over an arbitrary photo?** As well as the dark one does, and a
little further up. Dark text on a light floor clears AA higher (34.5% against 24.6%), partly
because its stops are stronger and partly because WCAG luminance favours dark text on
mid-tones. Neither scrim covers a whole arbitrary photo. The limit is height, not colour.
Text in the bottom quarter is safe in both themes. Text higher up, or a lot of it, belongs on
a `.card.glass-solid` (#118), not on a scrim turned up until it hides the photo. The visible
cost is aesthetic: over a dark photo the light floor reads as a pale mist across the bottom
of the frame, which changes the picture more than a dark scrim does over a light one.

**Also checked:** 44/44 pixel comparisons identical. These are `.g-scrim` and
`.g-scrim.g-scrim-top` against 0.1.3 (photo as background) and against the pre-change build
(background and `<img>` child), light and dark, on both photos; `.g-scrim-auto` in dark
against `.g-scrim` in dark; and strength 1 against unset for all four classes in both
themes. Computed paint styles of all 20 templates, light and dark: 0 of 27,164
element/pseudo rows changed. The minified build computes the same gradients.
`deck.min.css`: +1,202 bytes raw, +153 gzip, +100 Brotli.

**Not changed:** `.carousel-caption` reads `--scrim` directly and has no knob. Templates that
paint `var(--scrim)` themselves are unaffected.

## 120. Six blurred surfaces hardcoded four radii and ignored reduced transparency — FIXED

The brief asked for blur tokens and for #118's reduced-transparency fallback on every other
blurred surface. Three of its premises had moved:

- **The line numbers.** The sites are now `04-layout.css` 210 and 237, `06-forms.css` 421,
  `07-components.css` 754, `08-mobile.css` 91, `22-nav.css` 58 and 143, and `24-media.css` 68.
- **`--glass-blur` is not a root token.** It is `.glass`'s own knob, with a 16px fallback
  (#118), so there was nothing in `01-tokens.css` to extend.
- **There are four radii, not three:** 3px (`.modal::backdrop`), 8px (`.carousel-arrow`, the
  native `::scroll-button`, and `.video-play`), 12px (both `.sticky-top` declarations and
  `.form-actions-sticky`) and 16px (`.tabbar`). Zero visual change needs four tokens.

What changed:

- **`--blur-xs` 3px, `--blur-sm` 8px, `--blur-md` 12px, `--blur-lg` 16px,** named like `--r-*`.
  All eight declaration pairs read them, `-webkit-backdrop-filter` included. The `saturate()`
  values are untouched: 1.6 on the header, 1.8 on the tab bar.
- **Reduced transparency:** each surface takes its opaque base colour and drops the blur,
  the same pattern as `.glass`. That is `--bg` for `.sticky-top` and `.form-actions-sticky`,
  and `--surface` for `.tabbar`, both carousel arrows and `.video-play`. Each block sits
  beside its rule, for two reasons:
  - `.sticky-top`'s block comes *before* the scroll-state `@supports` block, whose
    `background: none` then wins, so a header still in flow stays see-through as it does
    without the setting. Its `::after` has its own block inside `@supports`.
  - The native `::scroll-button(*)` has its own block inside `@supports selector(::scroll-marker)`.
    In a selector list with `.carousel-arrow`, an engine without `::scroll-button` would drop
    both.
- **`.modal::backdrop` goes solid,** with its own colour at full alpha. Dropping only the blur
  would leave the page showing through the 48% dim *sharp*, which is more see-through than
  before. The cost is that an open modal hides the page completely for these readers.
  `.sheet::backdrop` and `.drawer::backdrop` are unblurred 50% dims and were left alone, so
  under the setting the modal is solid and those two are not.
- `99-print.css`'s `backdrop-filter: none !important` is untouched.
- **Docs:** `reference/tokens.php` has a Backdrop blur group with a live preview. That is a
  sharp dot beside the same dot under the token's blur, because fine stripes looked the same
  at 8px as at 16px. The token is also in the token tables for carousel, container, field,
  modal and video. The modal, video, sticky header and form actions prose names it and the
  reduced-transparency behaviour.
- **A test** pins all three things. The computed values are unchanged. Each surface follows
  its own token and no other. Under reduced transparency each one is opaque and unblurred,
  while a header in flow stays transparent. Against the pre-change build it fails.

**Verified** in Edge:
- **Pixels,** before against after, over a high-detail stripe-and-text background. The scenes
  are the stuck header (scroll-state path, and the fallback with that `@supports` made false),
  the form action bar, an open modal, the tab bar at 390px, the carousel (native buttons, and
  the DIY arrows with `::scroll-marker` made false) and the video play button. 8/8 identical,
  and the before-against-before control was 8/8 identical too.
- **Computed styles** of every element and `::before`/`::after` in all 20 templates, light
  and dark, `backdrop-filter` included: 0 of 27,190 rows changed.
- **Under reduced transparency** (CDP), every surface has alpha 1 and `backdrop-filter: none`
  on both fallback paths. With the pre-change build, all were translucent and blurred.
- **Retuning,** with each token set to 40px on its own. `--blur-xs` moved the modal backdrop.
  `--blur-sm` moved both arrows and the play button. `--blur-md` moved the header on both
  paths and the action bar. `--blur-lg` moved the tab bar. Nothing else moved.
- Safari's `-webkit-` path cannot be rendered here, because Edge ignores it. Each `-webkit-`
  declaration was edited to match its unprefixed twin exactly.

**Harness traps.** `html` and `.carousel-track` scroll smoothly, so screenshots taken after
`scrollTo()` caught the scroll mid-animation and before-against-before differed. The active
`::scroll-marker` animates its width, and `*::before, *::after` does not freeze it. Under the
suite's emulated reduced motion, `.form-actions-sticky` has no transition of its own, so it
gets the reset's .01ms `all` transition. In headless Edge that took more than two frames to
settle, so the test awaits `CSSTransition.finished`.

`deck.min.css`: +1,340 bytes raw, +127 gzip, +85 Brotli. The tokens alone are +230 / +32 /
+23, because `var(--blur-md)` is longer than `12px`. A refactor like this cannot come out
negative. The eight reduced-transparency blocks are +1,110 / +95 / +62.

**Not changed:**
- `.glass` keeps its own `--glass-blur`: 16px, with 8px for `.glass-sm` and 32px for
  `.glass-lg`. So retuning `--blur-lg` does not reach it. Its 16px and 8px defaults could
  read `--blur-lg` and `--blur-sm` with no visual change. 32px has no token.
  `20-gradients.css` was outside the brief.
- There is no single master knob. Retuning every blurred surface is four changes, one per
  strength. A multiplier token would make it one.
- `.enter-blur` (16-motion.css) and `.lazy-blur` (24-media.css) blur their own content with
  `filter`. They are not see-through surfaces.

## 121. Documented components could not be found, so people reported them missing — FIXED

A gap report written while building an app on Deck said "No date picker". Separately, a
dashboard template was built without `.sparkline`, `.donut`, `.ring` or `.timeline`. All of
them had pages. Coverage was not the problem, since 889 of 948 classes are claimed by a page.
Navigation was. Before anything changed, the docs measured like this:

- **The search box led nowhere.** The navbar form on every docs page posted to
  `search.php`, a class table whose last column was a file and line. Even a perfect hit
  (`donut`, five rows) never said the class was on the Charts page.
  `reference/classes.php` had the link column, but the navbar did not go there.
- **It matched the wrong text.** It took raw substrings of the class name, source comment
  and file name, unranked, and never a page title or description:
  - `date picker` found one row, `.datefield`, because `.datepicker` has no space.
  - `upload` found nothing. The page was titled File and the class is `.file`.
  - `ring` found 28 rows in alphabetical order, with `.alert-body` first because of the
    "ring" in "during".
- **There was no component index.** The docs home's Components card said "47 pages"
  (typed by hand; there were 66) and linked to `button.php`. The sidebar lists page titles,
  and a title hides what its page covers: donuts and sparklines are on Charts, the ring is
  on Progress. Ten api.json component families with no page (`.chip`, `.stat`, `.empty`,
  `.tabbar`, `.banner` and others) appeared only as rows in a 948-row table.
- **The machine-readable routes were dead or stale.** `dist/api.json` is outside the web
  root, and no page linked a copy. llms.txt sent assistants to README#components as "the
  list of component classes". That is a hand-typed paragraph with no date picker,
  combobox, data grid, charts, kanban, QR code or carousel.

What changed:

- **`docs/_docs.php`** holds the data and helpers that used to be the top of `_layout.php`,
  with no output. That lets `search.php` redirect, and the index answer in JSON, before
  any HTML goes out. Pages still require only `_layout.php`.
- **One index, one matcher.**
  - `docs_pages()` reads every page's own `$page` block (title, description, `documents`,
    `component`) and its headings with ids. `docs_claims()` is now built on it: the same
    889 claims, now in path order rather than directory order.
  - `docs_find()` ranks pages by where the query matches:

    | Score | Matches |
    | --- | --- |
    | 100 | the title, as a key |
    | 98 | the file name |
    | 96 | a claimed class |
    | 90 | a heading |
    | 85 | every word in the title |
    | 75 | every word in a heading |
    | 65 | every word in the description |
    | below | prefixes, then fragments |

  - A key drops case, spacing and punctuation and makes plurals singular, so "Date
    picker", "date-picker", ".datepicker" and "date pickers" are one search. Question words
    are dropped first, so "does Deck have a date picker?" works too.
  - A hit lands at the heading that names the class (`charts.php#t-donut`,
    `progress.php#ring`). Otherwise it lands on the class's row in the generated table,
    which now carries `id="class-…"`. `print.php` lists its classes by hand, so its classes
    land on its Classes heading.
- **`components/index.php`** is generated on every request. It lists each page with the
  public classes it claims, then the 49 public classes no page claims, grouped by their
  api.json component, with source lines. It filters without JavaScript; the script runs the
  same matcher, line for line. `?format=json` returns the same data with absolute URLs.
- **`search.php`** lists pages first, then the class table. The table is now ranked (exact
  name, then prefix, then the rest) and has a Documented in column.
- **The navbar search can open a page directly.** It sends `go=1`. When one page is clearly
  the answer, it opens that page with `?found=`. "Clearly" is `docs_sure()`: a match on a
  name or title, strictly ahead of the next hit. The page then says "Best match for …" and
  links back to every result.
- **The hand-kept pointers no longer send people astray:**
  - The docs home card links to the index and counts its pages.
  - The sidebar's Components group starts with All components.
  - File is retitled File upload, the name the page's own first section uses.
  - In llms.txt, Component reference is replaced by the index and its JSON. `tools/site.mjs`
    writes both links.
  - README's Components paragraph says it is a summary and links to the index.
- **`tests/docs-find.test.mjs`** serves `public_html/` with `php -S` and checks five things:
  - Each term from the brief, plus "heatmap" and "btn-primary", redirects to the right page
    and fragment, and that fragment exists.
  - The landing page links back to the results.
  - An unclear query lists pages, best first.
  - Every public class is in the index's HTML and JSON, and nothing else is.
  - Every link in the index lands on an id that exists, and the server filter keeps the
    right rows.

  It is skipped when `php` is not runnable. Its first run caught `print.php`'s missing row
  ids.

**Verified:**
- **Searches, in Edge, from the navbar.** Each of the nine terms from the brief lands on
  the right page in one step:
  - date picker
  - donut, at Donut
  - sparkline, at Sparklines and meters
  - ring, at `.ring`
  - timeline
  - upload
  - kanban
  - QR
  - emoji

  A deep link settles 130px from the top, clear of the sticky header, and the row is marked
  with `:target`.
- **The filter.** The script and the server kept identical rows for 18 queries.
- **Layout.** No horizontal overflow at 390px. Checked in dark mode.
- **Coverage.** The index lists all 844 public classes: 795 under 71 pages, and 49 under "No
  page yet".
- **The build and tests.** `npm run build` verifies 120 pages with no drift, and `npm test`
  passes 55 of 55. A search takes about 85ms on the PHP dev server.
- **A clean checkout.** The test was a copy of the tracked files plus the new ones, with
  `dist/` deleted. The build regenerated `api.json`, verified the pages, then stopped:
  `sizes.json` had no icon sample block. That stop predates this work, and its message says
  to run `npm run icons` once. After that, the build and the test both pass.

**Not done:**
- **The sidebar is still a hand-kept list.** It is complete today; the index is the
  generated view of the same pages.
- **49 public classes still have no page.** Among them are `.stat` (a KPI tile with
  `.stat-value`, `.stat-delta` and `.stat-label`), `.chip`, `.empty`, `.tabbar` and
  `.banner`. They can now be found by name and in the index, but nothing explains them yet.
- **The 543 public classes with no source comment** are found by name, and by the page that
  claims them, but not by purpose. A comment above each is still the best fix.

## 122. The Charts page's donut example drew nothing, and the demo's still does not — FIXED in documentation

Found while checking where a "donut" search lands. The example had three faults:

- **There was no `.chart` around it.** `.donut` paints
  `conic-gradient(var(--stops, var(--c1) 0 100%))`, and `--c1` is defined on `.chart`.
  Outside one, the gradient is invalid at computed-value time, so `background-image`
  computes to `none`.
- **`.donut-center` was inside `.donut`.** The ring's radial mask makes the hole transparent,
  so the figure was cut out along with it. Only the strokes that crossed the ring showed.
- **The prose said a donut was driven by `--value`.** Nothing reads `--value` for a donut;
  `--stops` is its only input. The lede, the `--value` section and the meta description
  all repeated the claim.

The example now follows `digital-marketing.html`:

- inside `.chart`
- `--stops: var(--c1) 0 68%, var(--bg-sunken) 0 100%`
- the centre beside the ring in `.donut-wrap`, not inside it

The prose names `--stops`. Checked in light and dark.

**Not fixed: the demo.** The "Sessions by device" donut on `public_html/index.php` sits in
a `.card-body` with no `.chart`, so in Edge it computes `background-image: none` and draws
nothing. The template's donut, inside `.card-body.chart`, renders. The underlying problem is
that `.donut` silently depends on a `.chart` ancestor. The fix is a choice between source and
markup, so it is left open. One way is to define the palette on `:root` too, or give
`.donut` a fallback. The other is to add `chart` to the demo's card body, as the template
does, and say on the class that it needs one.

## 123. Releasing 0.2.0: the scripts never said their version, and half the npm tarball was docs data — FIXED

Found while cutting 0.2.0 (0.1.4 until `.btn-primary`'s dark fill made it a breaking release).

- **The three scripts said 0.1 for five releases.** The banners of `deck.js`, `deck-extras.js`
  and `deck-adapters.js`, and `Deck.version`, are written by hand in `src/js/`. They read
  `0.1` from 0.1.0 to 0.1.4, while every other version string came from `package.json` or was
  checked against it. `Deck.version` is documented as "the version of the script". All four
  now read `0.2.0`, and `build.mjs` refuses to build when one differs from `package.json`, as
  it already did for `Deck::VERSION`. This is the same kind of drift that let the 0.1.2
  Composer package call itself 0.1.1.
- **Two thirds of the npm tarball was docs data.** `dist/api.json` (1.46 MB), `usage.json`
  (291 KB), `api-buckets.json` (268 KB) and `collisions.json` (6 KB) were 2.03 MB of the
  4.31 MB unpacked. Nothing a page or `bin/deck.mjs` runs reads them, the `exports` map
  exposes none of them, and the editor extension bundles its own copy. `package.json`'s `files`
  now excludes them (`!dist/api.json` and so on): 90 files, 2.28 MB unpacked, 590 KB packed,
  where it was 94 files, 4.31 MB and 747 KB. Composer still ships them, which matters because
  `docs/reference/php.php` tells PHP users to read `dist/api.json`.
- **`.gitignore` had stopped ignoring `*.log`.** In 3ab5e11 the lines `*.log` and
  `tools/vscode/*.vsix` became one line, `*.logtools/vscode/*.vsix`, which matches nothing.
  That same commit committed the `.vsix` and linked to it from the docs, so the vsix is meant
  to be tracked. `*.log` is back on its own line, and the vsix stays unignored.
- **The extension could not be updated in place.** Its data changes with every Deck release
  but its version did not. `code --install-extension` refuses to reinstall an installed version
  without `--force`. It is now 0.1.1, with data for 0.2.0. Its own README, which is the
  extension's details page, said "For Deck 0.1.2" with 0.1.2's counts. It now gives 0.2.0's.
- **The editor page's download button pointed at `raw/main/tools/vscode/`.** That is the
  newest file on `main`, not the one built for the release the page describes. With the
  bump, it would have named a 0.1.1 file that `main` does not have. The vsix is now attached
  to the GitHub release, and the button links to the release named in the extension's data.
  The example output of *Show loaded API version* said "Deck 0.1.x". It now prints the
  version from the same data.

**Not done:**
- **The docs site still pins `@0.1`.** `docs/start/install.php` (its CDN and unpkg URLs and the
  npx commands), `docs/explain/why-no-build-step.php` and `llms.txt` say `@0.1`, which never
  resolves to 0.2.0. README.md now says `@0.2`. install.php's rule is to re-run a changed
  command and paste its output, which needs 0.2.0 live on npm and Packagist. Its npx and
  Composer transcripts are still from 0.1.2.
- README.md's link to the vsix names the v0.2.0 release, so it needs updating at each
  release, like the CDN pins next to it.

## 124. "Every control clears a 44px target" was false, and #46's four exceptions are nineteen — FIXED in documentation

README.md's *Mobile specifics* said "Every interactive control clears a 44px touch target",
and the demo page's Forms section said "Every control clears a 44px target." #46 recorded
four controls below 44px. Read against `src/` at 0.2.0, the list is longer, and some of it
is a different case:

| Control | Target | Source |
| --- | ---: | --- |
| `.input`, `.select`, `.textarea` | 42px tall | `--control-h`, `src/01-tokens.css`; an input cannot take a `::before` |
| `.input-sm` | 34px | `src/06-forms.css` |
| `.menu-item` | 40px | `src/07-components.css` |
| `.segmented > *` | 36px, 44px when the children are `.btn` | `src/07-components.css` |
| `.chip` | 34px | `src/07-components.css` |
| `.pagination > *` | 34px (`--control-h-sm`) | `src/07-components.css` |
| `.number-sm > button` | 34px (`--control-h-sm`) | `src/23-inputs.css` |
| `.speed-dial-button` | 42px | `src/22-nav.css` |
| `.carousel-arrow` | 40px, hidden on coarse pointers | `src/22-nav.css` |
| datepicker day / nav and preset | 38px (42px coarse) / 36px | `src/10-datepicker.css` |
| `.editor-tool`, `.editor-select` | 32px | `src/23-inputs.css` |
| `.combo-toggle` | 30px | `src/11-combobox.css` |
| `.reaction` | 28px | `src/08-mobile.css` |
| `.datefield-clear` | 28px | `src/10-datepicker.css` |
| `.toast-close` | 26px | `src/13-toasts.css` |
| `.rating` star | 26px (`--star`) | `src/23-inputs.css` |
| `.combo-token` remove | 18px | `src/11-combobox.css` |
| `.carousel-dot` | 32px (a 7px dot with a 32px `::before`) | `src/22-nav.css` |
| `.dg .dg-check .check` | ~34px | `src/12-datagrid.css`, #29 |

What does hold: `--tap: 44px` (`src/01-tokens.css`) is the minimum for `.btn` (an invisible
`::before`, so a 34px `.btn-sm` still has a 44px hit area), `.check`, `.switch`, `.range`,
`.tab`, the accordion summary, `.tabbar-item`, `.banner-close` (#85), `.alert-close` and
`.copy-btn`, and for `.combo-option` on narrow screens. "Checkboxes and tabs" is still too
broad: the data-grid checkbox is ~34px, and `.segmented` children, which carry
`role="tab"`, are 36px. The list above is what one pass found, not a proof that nothing
else is under 44px.

**Fixed:** the README bullet and the demo sentence now name the controls that have the
44px target, say that text fields are 42px, and say that dense controls are smaller on
purpose. **Not changed:** the controls. Whether each one should grow, or whether a
`--tap-dense` token should name the smaller size, is still #46's open question.

**How it was found:** checking every claim in the launch posts against `src/` before
publishing any of them, after #85 showed the README's claim had been wrong once already.

## 125. The stated browser floor was lower than `light-dark()` allows — FIXED in documentation, `package.json` NOT FIXED

README.md (twice), the demo page and llms.txt said Chrome and Edge 117 and Safari 17.4, and
the README added "older browsers still get a usable page". `--brand`, `--brand-hover`,
`--focus` and the other colour roles in `src/01-tokens.css` are `light-dark()` with no
fallback, and `light-dark()` shipped in Chrome and Edge 123 and Safari 17.5. Below that,
every one of those custom properties is invalid at computed-value time, so a page loses its
brand, surface and text colours at once. `docs/explain/why-no-build-step.php` already said
so ("whatever the package metadata says"); the README and the demo contradicted it.

**Fixed:** README.md, the demo page's browser answer and llms.txt now give Chrome/Edge 123+,
Safari 17.5+ and Firefox 128+, and say that `package.json` declares less. `light-dark()`
explains the Chrome, Edge and Safari figures only: Firefox shipped it in 120, so Firefox 128
is the declared floor rather than one `light-dark()` sets.

**Superseded by #131.** The 123 / 17.5 / 128 written here was itself taken from MDN and
`package.json` rather than from the features. Tested, the floor is Chrome and Edge 131,
Firefox 147 and Safari 26.

**Not fixed:** `package.json`'s `browserslist` and the esbuild CSS target in `build.mjs` still
say 117/17.4. Raising them is a release decision (it changes what esbuild lowers, so it can
change `dist/`). The alternative is a fallback for the colour roles, which is a `src/` change.

## 126. Two selects stay at 13px on a touch screen, so iOS can still zoom — NOT FIXED

`src/06-forms.css` sets `.input`, `.textarea` and `.select` to 16px under
`(pointer: coarse)`, after the size variants, and the combobox, `.number` and `.phone` inputs
each repeat the rule. Two form controls are not covered:

- `.phone > .phone-country select` takes `font: inherit` from `.phone-country`, which is
  `--text-sm`.
- `.editor-select`, the block-format select in the editor toolbar, is `--text-sm`.

iOS Safari zooms the page when a control under 16px takes focus. Not tested on a device.
The fix is the same one-line `(pointer: coarse)` rule for each, in `src/23-inputs.css`.

**How it was found:** listing every form control in `src/` while checking the launch posts'
"iOS never zooms" claim, instead of checking only `.input`.

## 127. A first-time visitor hit four dead ends — FIXED

Crawling the site from `/` with the PHP server (every same-origin link and `#fragment`)
found one broken anchor. Reading the pages as someone new to Deck found the rest:

- **There was no changelog page.** CHANGELOG.md was only in the repository, and not on
  GitHub's `main` either. `docs/reference/changelog.php` now renders it on each request
  (headings, paragraphs, one-line bullets, bold, emphasis and code, which is all the file
  uses), so there is one copy. It is in the Reference nav, the docs footer and the docs
  index, and the demo page's banner links to it. Where CHANGELOG.md is not deployed beside
  `public_html/`, the page says so and links to GitHub.
- **The docs never linked to the templates.** No docs page, the index included, mentioned
  them. The docs index has a Templates card (its count comes from the gallery's files) and an
  Editor extension card; until now the extension was only a phrase in the *New to Deck* card.
  The docs footer links to the templates, the editor page, the changelog, GitHub and the
  issue tracker on every page.
- **"Get started" on the demo page went to `#main`**, the top of the page it is on. It now
  goes to the install page. The *Copy the link tag* menu, which shows a path to a file the
  visitor does not have yet, links there too.
- **`docs/components/nav.php` linked `.push` to `cluster.php#push`,** an anchor that does not
  exist. `.push` is documented at `bar.php#push`.

The demo's banner and hero badge also said "v0.1"; they print the version from
package.json now. The Tailwind guide's "Deck is v0.1" does the same.

## 128. The demo page's hand-kept counts had drifted, though #102 said they were corrected — FIXED

`public_html/index.php` typed `'classes' => 927`, `'icons' => 75` and `'symbols' => 152`
into its page config, and printed them in the About section, the weight table and the icon
section. The release has 948, 86 and 174. #102 corrected the comment above them, not the
values. They are now read: the class and stylesheet counts from `dist/api.json` (the
inventory the docs footer prints), and the icon and symbol counts from the sprite the page
serves, `assets/deck/deck-icons.svg`. `icons_avail`, the size of the Material Symbols set,
is the one hand-kept number left.

## 129. 0.2.0 is released on Packagist and GitHub, but the site, npm and `main` are on 0.1.3 — NOT FIXED here

Checked on 2026-10-21 before writing anything that links to them:

- `https://get-deck.dev/llms.txt` says "Version 0.1.3" and 26.8 KB, and
  `/docs/components/index.php` is a 404. The deployed site predates 0.2.0.
- `registry.npmjs.org/@echodial/deck` has `latest: 0.1.3`. README.md's jsDelivr tags
  (`@0.2`) return 404 until 0.2.0 is published.
- GitHub's `main` is 3ab5e11, 15 commits behind `v0.2.0`, and has no CHANGELOG.md. The
  repository page a visitor lands on shows the 0.1.x README, and llms.txt's `raw/main` links
  serve 0.1.x files (75 icons, not 86).

Each of these is outside the repository: `npm publish` (blocked on an expired npm login),
deploying `public_html/` with `dist/`, `php/`, `package.json` and now `CHANGELOG.md` beside
it (#77), and pushing `main`. The install page's `@0.1` pins (#123) should move to `@0.2`
in the same pass, after npm has 0.2.0, with its transcripts re-captured.

`docs/explain/why-no-build-step.php` also still quotes 0.1.x measurements typed into its
prose (26.4 KB, a purge to 14.1 KB, 828 public and 931 total classes, a 27.1 KB sprite),
beside a stylesheet size it reads from `sizes.json`. The purge figure needs re-measuring,
not retyping, so it is left for that pass.

## 130. Three headline claims described a different thing from the one they named — FIXED in documentation, one comment NOT FIXED

Found by an independent check of the launch posts against `src/`, after #124 and #125 were
written:

- **The size is the minified file's, and the install tag is not.** "28.1 KB Brotli" is
  `deck.min.css`. README.md's opening tag, the demo page's *Copy the link tag* menu and the
  default `npx @echodial/deck init` all give `deck.css`, which keeps its comments: 343,315
  bytes, 63.0 KB Brotli and 79.9 KB gzip (measured with the build's settings: Brotli quality
  11 with a size hint). A page that follows the install instructions sends more than twice
  the size the headline gives. **Fixed** in README.md's opening, which now says "when
  minified" and links `deck.min.css`. **Not fixed:** the demo's menu and `init`'s default, and
  whether `deck.css` should be the default at all.
- **"Every colour from one number" is one number of six.** `src/01-tokens.css` declares
  `--hue-brand`, `--hue-accent`, `--hue-good`, `--hue-warn`, `--hue-bad` and
  `--hue-neutral`, and its own comment says "These six numbers are the whole palette".
  Shadows read `--shadow-color`, which is `var(--hue-neutral)`, and only `.badge-brand` and
  `.badge-solid` follow the brand hue. README.md said every badge and shadow is recomputed
  from `--hue-brand`, and so did the demo page's hero. Both now name what does follow it: the
  ramp, primary buttons, links, focus rings, brand badges and chart series. **Not fixed:** the
  comment at `src/01-tokens.css:17`, which still says shadows follow `--hue-brand`.
- **"Your CSS wins without `!important`" has exceptions.** A layered `!important` beats an
  unlayered rule, important or not. Outside `99-print.css`, `src/` has `!important` on 25
  lines, holding 29 declarations: `.no-motion` and the print rules in `24-media.css` put two
  or three on a line. A plain `grep -c '!important'` finds 29 lines, but four of those are
  the word inside comments (`00-layers.css` three times, `22-nav.css` once). The uses are
  reduced motion (`02-reset.css`, `16-motion.css`, `.no-motion`), print rules in other
  files, the stacked data grid (`12-datagrid.css`, `18-container.css`) and the drag states
  (`26-perf.css`). README.md now says an unlayered rule overrides any Deck rule that is not
  `!important`, and lists those four uses.

The same check found that README.md counted the four section sets on top of the nineteen
templates rather than among them. That is corrected too.

## 131. The browser floor was typed into `package.json`, and the real one is Chrome 131, Firefox 147, Safari 26 — FIXED in configuration and documentation

`browserslist` said Chrome and Edge 117, Safari 17.4 and Firefox 128, `build.mjs` typed the
same into esbuild's target, and #125 moved the documented floor to 123 / 17.5 / 128 from
MDN's date for `light-dark()` and from `package.json` itself. Nothing had loaded Deck in
those versions.

Tested on 2026-10-21 with a probe page built from `dist/deck.min.css` and
`dist/deck.bundle.min.js`. It reports computed styles in light and dark with transitions
switched off, so a read is not the start of a colour transition. The builds were Chrome for
Testing 122, 123, 124, 125, 126, 128, 129, 130, 131 and 132; Edge 155; and Firefox 127, 128,
131, 132, 133, 146, 147 and 158, extracted from Mozilla's signed installers and run headless.
Safari was not tested, because there is none on this machine.

| Engine | Floor | Set by | Just below it |
| --- | --- | --- | --- |
| Chrome, Edge | 131 | A relative colour taken from a `light-dark()` value (#132) | 125–130: `.btn-primary` takes the page's text colour (3.35:1 light, 2.22:1 dark) and `box-shadow: var(--ring)` is `none`. 125–128 also misplace anchored panels (#134), and 125–126 crash on a tooltip then a menu. 123–124 crash on load. 122: no colours at all |
| Firefox | 147 | Anchor positioning, because the Floating UI hand-off never runs (#133) | 133–146: tooltips, popovers and menus open where they sit in the markup. 128–132: also the button text and focus ring. 127: also `.g-text` invisible and every `.g-*` gradient gone |
| Safari | 26, not tested | Anchor positioning, as for Firefox | MDN: relative colours with `h` arithmetic need 18 (16.4–17.6 follow an older spec), so 17.5–17.6 lose the gradient classes; `light-dark()` needs 17.5 |

Features with a fallback, or with nothing depending on them, are not floors:
`@starting-style` and `allow-discrete` (Firefox 129; Firefox still cannot transition
`display`), `field-sizing`, container style queries, `contrast-color()` (behind
`@supports`), `interpolate-size`, `::details-content`, scroll-driven animations,
`::scroll-button`, `sibling-index()` and view transitions. Firefox 158 has all of them except
`interpolate-size`, scroll-driven animations and `::scroll-button` (the first two tested with
`CSS.supports`, the third from MDN).

**Fixed:** in this first pass, `browserslist` became `chrome >= 131`, `edge >= 131`,
`firefox >= 147`, `safari >= 26`; the retest below replaced it. `build.mjs` reads esbuild's
CSS target from it instead of typing a second
copy, and fails if a line is not `<browser> >= <version>`. The output is byte-identical at
the old and the new target, so `dist/` did not change. README.md, the demo page, llms.txt,
`docs/explain/why-no-build-step.php` and the Tailwind guide give the tested floor, and say
that Safari's comes from MDN.

**How it was found:** the launch posts cited `browserslist` as the proof of Firefox 128,
which is the file the number was typed into rather than anything that needs it.

**Retested the same day after #133, #135 and #136 were fixed and #132 partly fixed.** The
floor is now Chrome and Edge 131 and Firefox 133. `browserslist` says that and names no
Safari version, because Safari has not been run.

| Engine | Before | After #133 | After #132 (focus ring) | Set by, and tested in |
| --- | --- | --- | --- | --- |
| Chrome | 131 | 131 | 131 | `.btn-primary` ink and pressed fill (#132): Chrome 130 fails, 131 passes |
| Edge | 131 | 131 | 131 | same engine; only Edge 155 was run |
| Firefox | 147 | 133 with Floating UI, 147 without | 133 with Floating UI, 147 without | ink and pressed fill (#132): Firefox 132 fails, 133 passes. Placement (#133): 133 and 146 pass with Floating UI and fail without; 147 is native |
| Safari | 26, from MDN | untested | untested | not run: no Safari, iOS device or BrowserStack account here |

esbuild still gets a Safari target, `safari17.4`. Without one it strips the `-webkit-`
prefixes Deck writes for Safari, such as `-webkit-user-select`. Any Safari from 17.4 to 26
gives the same output, so the target is not a floor. `deck.min.css` is byte-identical before
and after the `browserslist` change.

Testing trap, for the next run: Firefox builds extracted from Mozilla's installers update
themselves. By the second launch, the 127, 128, 132 and 133 folders were 158.0.1, and every
result from them was really Firefox 158. They are now extracted with `updater.exe` removed and
a `distribution/policies.json` of `{"policies":{"DisableAppUpdate":true}}`. Every result is
checked against the `User-Agent` its request carried.

## 132. A relative colour from a `light-dark()` value crashes Chrome 123–124 and fails until Chrome 131 and Firefox 133 — the focus ring FIXED, the button ink NOT FIXED

`--btn-text` on `.btn-primary` is
`light-dark(var(--text-on-brand), oklch(from var(--btn-bg) clamp(0, (.565 - l) * 999, 1) 0 0))`,
and `--btn-bg` is `var(--brand)`, itself a `light-dark()`. `--ring` is
`light-dark(oklch(from var(--focus) l c h / .32), …)`, and `--focus` is a `light-dark()`
too. A pressed `.btn-primary` (`aria-pressed="true"`, `src/05-buttons.css:214`) darkens
`--btn-bg` with the same shape.

Reduced in Chrome 123: `:root { color-scheme: light dark } .x { color: oklch(from light-dark(red, blue) l c h) }`
crashes the renderer, and so does `rgb(from …)`. The same expression with `from red` does
not. Chrome 124 crashes the same way and 125 does not. Chrome 125–130 and Firefox 128–132
do not crash, but treat the value as invalid. So `.btn-primary`'s text falls back to the
inherited colour, 3.35:1 on the 600 fill in light mode and 2.22:1 on the 400 fill in dark.
And `box-shadow: var(--ring)` is `none`, which leaves a focused `.input` with only its
border colour change, because the field sets `outline: none`. Chrome 131 and Firefox 133
resolve all of it: 4.96:1 and 8.27:1. Those are the versions MDN gives for a relative colour
from `currentcolor`, which works by the same mechanism, so its Safari 18 is the likely Safari
answer.

**Fixed: the focus ring.** `--ring` is now
`light-dark(color-mix(in oklch, var(--focus) 32%, transparent), color-mix(… 42% …))`. Mixing
with transparent gives the same colour at that alpha, and it still follows an overridden
`--focus`. Where the old ring worked (Chrome 131, 132, Edge 155, Firefox 133, 146, 147, 158)
the computed `box-shadow` is the same string. It now appears in Chrome 125–130 and Firefox
127–132, where it was `none`, and focusing a text field no longer crashes Chrome 123 or 124.

**Not fixed: `.btn-primary`'s text and pressed fill.** Three formulations were measured in
real builds, against the theming guide's exact-brand example (`--brand: #0b5cff`, whose
OKLCH lightness of 0.546 is just under the 0.565 switch):

| Formulation | Works from | Exact-brand ink in dark mode |
| --- | --- | --- |
| Today's: from `var(--btn-bg)` | Chrome 131, Firefox 133 | white, 5.26:1, every run |
| From a plain step, `var(--brand-400)` | Chrome 123, Firefox 128 | black, 3.99:1, in Chrome 131–146 and Firefox 133–145; the pressed fill would also take the ramp's hue instead of the brand's, in every browser |
| From a registered `<color>` copy of `--btn-bg` | Chrome 126, Firefox 128; Chrome 123–124 still crash | black at random in Chrome 130 (1 run in 5) and 131 (3 in 5); white in Chrome 132 and every Firefox |
| `oklch(from color-mix(… var(--btn-bg) …) …)` | Chrome 131, Firefox 133 | white, but no earlier than today |

Each lowers the floor only by lowering the documented override's contrast, or not at all, so
none was kept. Chrome 123 and 124 still crash on any page with a `.btn-primary`, and Chrome
125–130 and Firefox 128–132 still give it the page's text colour. Two more traps found on the
way: `CSS.supports('color: oklch(from light-dark(red, blue) l c h)')` itself crashes Chrome
123, so no `@supports` test can guard it, and `@supports (color: rgb(from currentcolor r g b))`
is already true in Firefox 127, so it does not mark the versions that resolve this either.

## 133. The Floating UI hand-off for popover triggers never runs — FIXED

`src/25-anchor.css` says that where anchor positioning is missing, "deck-adapters.js uses
Floating UI if it is on the page and falls back to Deck's own placement if it is not", and
`docs/reference/javascript.php` says `data-deck-anchor` is "set by Deck on popover triggers
it wires, and read by the Floating UI adapter". `wireAnchors()` in `src/js/deck.js` sets it,
but its first line is `if (!CSS.supports('anchor-name: --a')) return;`, so the attribute is
written only in browsers that have anchor positioning. In those, the adapter steps aside
(`if (hasAnchor || !have.floating) return;`). In every other browser, nothing sets the
attribute and the adapter finds no trigger.

There is no "Deck's own placement" for `.tip`, `.pop` or `.menu` either; only the combobox
and date picker place themselves. With `inset: auto`, a panel opens at its static position
in the markup, which follows neither the trigger nor scrolling.

Measured with Floating UI loaded from the README's own tags:

| Browser | Markup as documented | With `data-deck-anchor` added by hand |
| --- | --- | --- |
| Chrome 122 | tooltip at (0, 900), 558px from its trigger and below the fold | 8px below, centred |
| Firefox 133 | where it sits in the markup; 836px from its trigger on the probe page | 8px below, centred |

**Fixed.** `wireAnchors()` sets `data-deck-anchor` in every browser. Where anchor
positioning is missing it does so only for a `.tip`, `.pop`, `.menu` or `.mega` opened by a
trigger outside the panel, and not for a `data-deck-mega` trigger or a
`popovertargetaction="hide"` button. The date picker, the combobox and a `data-deck-mega` menu
have placement of their own, and a `.pop`'s Dismiss button sits inside the panel it would
anchor. Where anchor positioning exists, behaviour is unchanged: positions in Edge 155 and
Firefox 158 were identical before and after. `deck.min.css` is byte-identical; only the
scripts changed. Two adapter faults surfaced once it ran, and are #136.

Verified in Firefox 133 and 146 with Floating UI loaded, at 1229×606. Each panel was measured
with its trigger 250px down the window, again after scrolling 80px, and again with the
trigger 40px from the bottom. Scrolling was instant: Deck's `scroll-behavior: smooth` made
an earlier run measure some panels against a moving page.

| Component | Opens | After scrolling | Near the bottom |
| --- | --- | --- | --- |
| `.tip` | above, 8px, centred | the same | above |
| `.pop` | below, 8px | the same | flips above |
| `.menu` | below, 8px, start-aligned | the same | flips above |
| `.menu-match` (dropdown) | below, 8px, its trigger's width | the same | flips above |
| `.mega` | below, 8px, kept inside the window | the same | flips above |
| `.mega` with `data-deck-mega` (own placement, #135) | below, 8px | the same | stays below and runs off-screen |
| date picker (own placement, #135) | below, 6px, the side with more room | the same | flips above |
| combobox list | below, 8px, the control's width | the same | flips above |

Without Floating UI, Firefox 133 and 146 still open a `.tip` 390px and a `.pop` 1347–1348px from
the trigger. A `.menu` sits at 0px, because it follows its trigger in this markup, but it
never flips. So the hand-off lowers Firefox to 133 only for a page that loads Floating UI.
Without it, `.tip` and `.pop` need Firefox 147. The comment in `src/25-anchor.css`, the
README, `docs/components/menu.php` and the `data-deck-anchor` row in
`docs/reference/javascript.php` now say so, instead of promising "Deck's own placement".

## 134. `@supports (anchor-name: --a)` admits Chrome 125–128, which lack `position-area` — NOT FIXED

Chrome shipped `anchor-name` in 125 but `position-area` in 129. It called it `inset-area`
until 130, and `position-try-fallbacks` was `position-try-options` until 128. Deck's anchor
rules are all behind `@supports (anchor-name: --a)`, and `deck.js` and the adapter use the
same test, so Chrome 125–128 take the anchor path and get none of its placement. Menus
opened at y = 1408 in a 1400px window, and tooltips at the top of it. With transitions on,
Chrome 125 and 126 also crash the tab when a tooltip opens and then a menu.

**Not fixed:** below the new floor of 131, so it no longer matters for a supported browser.
Testing `position-area` instead would send 125–128 to the JavaScript path, now that #133 is
fixed. It would not lower Chrome's floor on its own, because #132's button ink still needs
Chrome 131.

## 135. The date picker's and a mega menu's own placement ignored the page's scroll — FIXED

Where anchor positioning is missing, `DatePicker.position()` in `src/js/deck.js` and
`megaMenus()` in `src/js/deck-extras.js` place their panel from `getBoundingClientRect()`,
which is relative to the window. `.datepicker` and `.mega` are `position: absolute`, and in
the top layer the containing block of an absolute element is the document. So a panel
opened as far from its trigger as the page had been scrolled: 5,733px above the date field
and 5,015px above a `data-deck-mega` trigger on the probe page in Firefox 133.

**Fixed:** both add `scrollX` and `scrollY`, so they are document coordinates, and the panel
stays with its trigger as the page scrolls. Measured in Firefox 133 and 146: the date picker
opens 6px from its field on the side with more room, and the mega menu 8px below. The mega
menu's own placement still never flips, so near the bottom of the window it runs off-screen,
as native `.menu` does (#137). In a right-to-left page both still set `inset-inline-start`
from a left offset, which is wrong there (not tested).

## 136. Once the hand-off ran, the adapter covered wide panels' triggers and put tooltips below — FIXED

Two faults in `floating()` in `src/js/deck-adapters.js`, invisible while #133 kept it from
running:

- **Every panel defaulted to `placement: 'bottom'`.** 25-anchor.css puts a `.tip` above its
  trigger and a `.menu` below from its start edge. The adapter now defaults `.tip` to `top`
  and `.menu` to `bottom-start`, and everything else to `bottom`; `data-placement` still
  overrides it.
- **`flip()` checked the cross axis.** A `.pop` (20rem) or `.mega` (64rem) centred on a
  trigger near the window's left edge overflowed sideways, so Floating UI flipped it to a side
  placement. It then overlapped its own trigger, 147px and 101px to the side. `flip({ crossAxis:
  false })` flips only between above and below, and `shift()` keeps the panel inside.

Measured in Firefox 133 and 146; the table is in #133.

## 137. Native placement does not flip menus at the bottom of the window, and opens the date picker off-screen — NOT FIXED

The same probe, run where anchor positioning is native (Edge 155, Firefox 158), with instant
scrolling:

- `.menu`, `.menu-match` and `.mega` open below their trigger even 40px from the bottom of the
  window, and run off-screen. A `.tip` stays above, as designed, and `.pop` and the combobox
  list flip.
- The date picker opens above its field, and with the field 250px down a 768px window, its
  356px panel runs off the top. A `data-deck-mega` menu also opens above.
- In Firefox 147 a `.pop` does not stay with its trigger as the page scrolls: 71px from it,
  then 151px after scrolling 80px. Firefox 158 keeps it at 8px.

The panels that misbehave are the ones declared `position: absolute` (`.menu`, `.mega`,
`.datepicker`). In the top layer their containing block is the whole document, so
`position-try-fallbacks` and `position-try-order: most-block-size` measure the room in the
document rather than in the window. A long page always has room below and, further down it,
more room above. `.tip`, `.pop` and the combobox list are `position: fixed` and behave.

**Not fixed:** making those three `position: fixed` inside the `@supports (anchor-name: --a)`
block is the likely fix. It changes how they look in every current browser, so it needs its
own pass and a visual check, and it is not a floor question: every version with anchor
positioning does it.
