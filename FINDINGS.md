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

## 52. `.editor`'s toolbar and footer print

`src/99-print.css` drops `.dg-toolbar`, `.tabs`, `.segmented` and the rest of the page
chrome. `.editor-toolbar` and `.editor-footer` are not in that list, so a printed page
containing an editor shows a row of formatting buttons that cannot be pressed and a
character count that means nothing.

One line adds them to the existing never-print selector. The content region should keep
printing, which it already would.

**Not fixed** — a rendering change in a documentation pass. Documented on the editor page.

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
