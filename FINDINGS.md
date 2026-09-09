# Findings

Things documenting Deck revealed about Deck. Started while building the docs system and
the `button.php` exemplar, then updated by the pass that fixed the source. Items marked
FIXED were repaired; the rest carry a reason for leaving them. Ordered by how much they
would cost to leave alone.

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

**Not fixed:** renaming them. `.w-auto` and `.w-full` already exist in `09-utilities.css`
and do the same thing, so `.is-auto` and `.is-full` are duplicates as well as
misnamed — but deleting two public classes is a breaking change and belongs in a pass
that can look at the whole logical-utility family at once.

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

## 14. `.stack-depth` and `.stack` share a prefix but are unrelated

`.stack` is the vertical rhythm primitive; `.stack-depth` is the 3D card pile. The
extractor reduces a class to its shortest defined prefix when attributing states, so
`.is-fanned` and `.is-dismissed` — states of the pile — attribute to `stack`.

The extractor records `ownerClasses` alongside `owners` so the exact class is never
lost, but the reduction is still wrong for a reader. `.pile` or `.card-stack` would fix
it. Same shape as the `.grid` / `.dg` collision in item 11.
