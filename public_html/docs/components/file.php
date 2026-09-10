<?php
declare(strict_types=1);

$page = [
    'path' => 'components/file.php',
    'title' => 'File',
    'level' => 'Beginner',
    'description' => 'Deck\'s .file is a dashed drop zone wrapping a real file input that is 1px rather than display:none, so it keeps its tab stop. It styles the zone; dropping needs a handler of your own.',
    'documents' => [
        'file',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">File</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>File</h1>
  <p class="lede">
    <code>.file</code> is a dashed, centred drop zone that goes on a
    <code>&lt;label&gt;</code> wrapping a real <code>&lt;input type="file"&gt;</code>.
    The input is hidden, but not with <code>display: none</code> — that would take it out
    of the tab order and make the control unreachable by keyboard.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it when uploading is a primary action on the page: an import screen, an avatar
    picker, an attachment area. The large target and the dashed border say "put something
    here" in a way a small Choose file button does not.
  </p>
  <?php
  docs_example(
      '<label class="file" style="max-inline-size:26rem">' . "\n" .
      '  <svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#upload"></use></svg>' . "\n" .
      '  <span>Drop a CSV here, or choose a file</span>' . "\n" .
      '  <span class="text-sm text-muted">Up to 10 MB</span>' . "\n" .
      '  <input type="file" accept=".csv">' . "\n" .
      '</label>',
      'Click it, or tab to it and press Enter',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="hiding">Hiding the input without losing it</h2>
  <pre class="dx-code"><code><?= e('.file input[type="file"] {
  position: absolute;
  opacity: 0;
  inline-size: 1px;
  block-size: 1px;
}') ?></code></pre>
  <p>
    A file input cannot be styled — the browser draws the button, and its appearance is
    not yours to change. Every custom upload control therefore hides the real input and
    puts something else on top. <em>How</em> it is hidden is the part that matters:
  </p>
  <ul class="stack-2">
    <li>
      <strong><code>display: none</code> removes it from the tab order.</strong> The
      control becomes mouse-only. This is the most common upload defect there is.
    </li>
    <li>
      <strong><code>visibility: hidden</code> does the same</strong> — it is removed from
      the accessibility tree as well.
    </li>
    <li>
      <strong>1px and transparent keeps everything.</strong> The input is still focusable,
      still announced, still operable with Enter or Space. It is simply not visible.
    </li>
  </ul>
  <p>
    Because the input is inside the <code>&lt;label&gt;</code>, clicking anywhere in the
    zone opens the file picker with no JavaScript at all.
  </p>
</section>

<section class="stack-3">
  <h2 id="focus">:focus-within is the focus ring</h2>
  <p>
    The input is invisible, so focusing it would show nothing. <code>.file:hover,
    .file:focus-within</code> gives the <em>zone</em> the brand border and tint, so
    tabbing to the control lights up the thing the reader can actually see.
  </p>
  <p>
    This is the pattern to copy whenever a real control is visually replaced: keep the
    control, and move its states onto the wrapper with <code>:focus-within</code>,
    <code>:has()</code> or a sibling selector.
  </p>
  <?php
  docs_example(
      '<div class="stack-3" style="max-inline-size:26rem">' . "\n" .
      '  <label class="file">' . "\n" .
      '    <span>Tab into this one</span>' . "\n" .
      '    <input type="file">' . "\n" .
      '  </label>' . "\n" .
      '  <label class="file">' . "\n" .
      '    <span>Then this one</span>' . "\n" .
      '    <input type="file">' . "\n" .
      '  </label>' . "\n" .
      '</div>',
      'Press Tab: the border follows the focus',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="dropping">Dropping is not implemented</h2>
  <p>
    <code>.file</code> draws the visual vocabulary of a drop target — a dashed border, a
    hover state, an upload icon — and Deck ships <strong>no drop handler</strong>.
    Dragging a file onto it does nothing. Clicking works, because the label wraps a real
    input; dragging does not, because dragging requires JavaScript that is not there.
  </p>
  <p class="dx-note text-muted">
    An affordance that does not do what it looks like it does is worse than no affordance:
    a reader drags a file, nothing happens, and they conclude the page is broken rather
    than that they should have clicked. This is recorded in <code>FINDINGS.md</code>. The
    honest options are to ship the handler or to stop drawing a drop zone; until one of
    them happens, the label text should not say "drop".
  </p>
  <p>
    The handler is about fifteen lines, and needs <code>dragover</code> prevented or the
    browser navigates to the file:
  </p>
  <pre class="dx-code"><code><?= e('const zone = document.querySelector(\'.file\');
const input = zone.querySelector(\'input[type="file"]\');

zone.addEventListener(\'dragover\', e => {
  e.preventDefault();               // without this the browser opens the file
  zone.classList.add(\'is-over\');
});
zone.addEventListener(\'dragleave\', () => zone.classList.remove(\'is-over\'));
zone.addEventListener(\'drop\', e => {
  e.preventDefault();
  zone.classList.remove(\'is-over\');
  input.files = e.dataTransfer.files;
  input.dispatchEvent(new Event(\'change\', { bubbles: true }));
});') ?></code></pre>
  <p class="text-muted">
    <code>.is-over</code> already exists in <code>src/23-inputs.css</code> — it is used
    by the editor's character counter — so the class is available, though nothing in
    <code>.file</code>'s own rules styles it yet.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.file</code> has no <code>.file-*</code> variants, so the extractor does not
    treat it as a component root and there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--line-strong', '--brand-500', '--brand-soft', '--surface-2', '--text-muted', '--r-md', '--space-2', '--space-6', '--dur-1']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The input keeps its tab stop</strong>, which is the whole reason it is 1px
      and transparent rather than <code>display: none</code>. Tab reaches it, Enter and
      Space open the picker, and the file dialog is the platform's own.
    </li>
    <li>
      <strong>The zone's text is the accessible name.</strong> The label wraps the input,
      so everything inside it is read as the control's name — including the "Up to 10 MB"
      hint, which makes for a long name. Move a long hint outside the label and point at
      it with <code>aria-describedby</code>.
    </li>
    <li>
      <strong>Focus is visible on the zone, not the input.</strong>
      <code>:focus-within</code> is doing that. Remove it and the control becomes
      focusable but invisibly so, which is worse than not being focusable at all.
    </li>
    <li>
      <strong>Dragging is pointer-only and always will be.</strong> Even with the handler
      above, drag-and-drop has no keyboard equivalent. The click path is not a fallback,
      it is the primary path — which is another reason not to lead the label text with
      "Drop".
    </li>
    <li>
      <strong>Selected files are not announced.</strong> The browser shows the file name
      inside its own button, which is hidden here — so after choosing a file the zone
      looks exactly as it did before. <strong>You must render the selection
      yourself</strong>, and put it in an <code>aria-live</code> region or move focus to
      it. This is a real gap in the component.
    </li>
    <li>
      <strong><code>accept</code> is a filter, not validation.</strong> It narrows the
      file dialog and can be bypassed. Check the type on the server.
    </li>
    <li>
      <strong>Touch target.</strong> The zone is
      <?= e(api_token('--space-6')['value'] ?? '1.5rem') ?> of padding around centred
      content, so it clears the minimum comfortably — the one part of this component with
      nothing to worry about.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The zone is a grid with <code>place-items: center</code> and symmetrical padding, so
    there is nothing directional to mirror. The text inside it follows the document
    direction as ordinary text does.
  </p>
  <?php
  docs_example(
      '<label dir="rtl" class="file" style="max-inline-size:26rem">' . "\n" .
      '  <svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#upload"></use></svg>' . "\n" .
      '  <span>اختر ملفًا</span>' . "\n" .
      '  <input type="file">' . "\n" .
      '</label>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The border and background transition over
    <?= e(api_token('--dur-1')['value'] ?? '120ms') ?> on hover and focus; the global
    reset collapses both to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>. The states still happen, immediately.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.file</code> is not named in <code>src/99-print.css</code>, so it prints as a
    dashed box with its prompt text inside. For a printed form that is arguably right —
    it reads as a space for an attachment — but the dashed border is a screen affordance
    and the icon is meaningless on paper.
  </p>
  <p class="text-muted">
    If the printed version matters, render the chosen file's name as text alongside the
    zone; that text prints and the zone can be hidden.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A compact zone for a sidebar */
  .file { padding: var(--space-4); }

  /* Style the drag-over state the handler sets */
  .file.is-over {
    border-color: var(--brand-600);
    background: var(--brand-soft);
    border-style: solid;
  }
}') ?></code></pre>
  <p class="text-muted">
    Do not replace the hiding technique with <code>display: none</code>, however tempting
    it looks. That single change is what breaks keyboard access, and nothing on the page
    will tell you.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not when uploading is a minor action.</strong> A drop zone is large and
      demands attention. For an optional attachment on a long form, a
      <code>.btn</code> beside the field is proportionate.
    </li>
    <li>
      <strong>Not without rendering the selection.</strong> The browser's own file name
      display is hidden, so a zone with no selected-file list leaves the reader unsure
      whether anything happened. This is the component's biggest gap.
    </li>
    <li>
      <strong>Not with "Drop" in the label, until you ship the handler.</strong> The
      dashed border already implies it; the words make a promise the page does not keep.
    </li>
    <li>
      <strong>Not for an avatar.</strong> A picture needs a preview and a crop. A drop
      zone with no preview means uploading and then looking at the result to find out
      what you got.
    </li>
    <li>
      <strong>Not as the only way to add something.</strong> If a file can also be pasted
      or linked, offer those too — dragging is impossible on many devices and awkward on
      most.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
