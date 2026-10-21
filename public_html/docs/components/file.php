<?php
declare(strict_types=1);

$page = [
    'path' => 'components/file.php',
    'title' => 'File upload',
    'level' => 'Beginner',
    'description' => 'Deck\'s .file is a dashed drop zone wrapping a real file input that is 1px rather than display:none, so it keeps its tab stop, and deck-extras.js puts a dropped file into the input.',
    /* .is-over is shared with the editor's character counter, which claims it
       too; here it is the drag-over state. */
    'documents' => [
        'file', 'is-over',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">File upload</li>
  </ol>
</nav>

<header class="stack stack-3">
  <h1>File upload</h1>
  <p class="lede">
    <code>.file</code> is a dashed, centred drop zone that goes on a
    <code>&lt;label&gt;</code> wrapping a real <code>&lt;input type="file"&gt;</code>.
    The input is hidden, but not with <code>display: none</code> — that would take it out
    of the tab order and make the control unreachable by keyboard.
  </p>
</header>

<section class="stack stack-3">
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
      '  <span>Choose a CSV, or drop it here</span>' . "\n" .
      '  <span class="text-sm text-muted">Up to 10 MB</span>' . "\n" .
      '  <input type="file" accept=".csv">' . "\n" .
      '</label>',
      'Click it, or tab to it and press Enter',
      'stack'
  );
  ?>
</section>

<section class="stack stack-6">
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
  <ul class="stack stack-2">
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

<section class="stack stack-3">
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
      '<div class="stack stack-3" style="max-inline-size:26rem">' . "\n" .
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

<section class="stack stack-3">
  <h2 id="dropping">Dropping a file</h2>
  <p>
    With <code>deck-extras.js</code> on the page, a file dragged onto the zone lights it up
    — <code>.is-over</code> turns the dashed border solid and adds the focus ring — and a
    file dropped on it goes into the input exactly as if it had been chosen in the picker:
  </p>
  <ul class="stack stack-2">
    <li>
      <strong>Only what <code>accept</code> allows.</strong> The picker filters by
      <code>accept</code>; a drop does not, so Deck applies it: extensions
      (<code>.csv</code>), wildcards (<code>image/*</code>) and exact types. A file that
      does not match is ignored, and a drop with no matching file changes nothing.
    </li>
    <li>
      <strong>One file unless the input is <code>multiple</code>.</strong> Dropping five
      files on a single-file input keeps the first that matches.
    </li>
    <li>
      <strong><code>input</code> and <code>change</code> fire</strong> on the input,
      bubbling, so a form's own listeners — a preview, an upload, a selected-file list —
      run the same way for a drop as for a pick.
    </li>
  </ul>
  <?php
  docs_example(
      '<label class="file" style="max-inline-size:26rem">' . "\n" .
      '  <svg class="icon icon-lg" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#upload"></use></svg>' . "\n" .
      '  <span>Choose a poster, or drop one here</span>' . "\n" .
      '  <span class="text-sm text-muted">JPEG or PNG</span>' . "\n" .
      '  <input type="file" accept=".jpg,.jpeg,.png">' . "\n" .
      '</label>',
      'Drag an image from your desktop onto it',
      'stack'
  );
  ?>
  <p class="text-muted">
    Deck listens on the document, after any handler of your own on the zone. If your
    handler calls <code>preventDefault()</code> on the drop, Deck leaves it alone, so a
    page that already wired the zone itself is not handled twice. A zone added to the page
    later works without a call to <code>Deck.init()</code>. A file dropped anywhere
    <em>outside</em> a zone is the browser's business, which usually means opening it.
  </p>
</section>

<section class="stack stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    <code>.file</code> has no <code>.file-*</code> variants, so the extractor does not
    treat it as a component root and there is no completeness check on this page.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--line-strong', '--brand-500', '--brand-soft', '--surface-2', '--text-muted', '--r-md', '--space-2', '--space-6', '--dur-1']); ?>
</section>

<section class="stack stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack stack-2">
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
      <strong>Dragging is pointer-only and always will be.</strong> Even with Deck's drop
      handler, drag-and-drop has no keyboard equivalent. The click path is not a fallback,
      it is the primary path — which is why the label should lead with "choose" and
      mention dropping second.
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
      file dialog, and Deck applies it to a drop by name and declared type, but both can be
      bypassed. Check the type on the server.
    </li>
    <li>
      <strong>Touch target.</strong> The zone is
      <?= e(api_token('--space-6')['value'] ?? '1.5rem') ?> of padding around centred
      content, so it clears the minimum comfortably — the one part of this component with
      nothing to worry about.
    </li>
  </ul>
</section>

<section class="stack stack-3">
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

<section class="stack stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The border and background transition over
    <?= e(api_token('--dur-1')['value'] ?? '120ms') ?> on hover and focus; the global
    reset collapses both to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>. The states still happen, immediately.
  </p>
</section>

<section class="stack stack-3">
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

<section class="stack stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A compact zone for a sidebar */
  .file { padding: var(--space-4); }

  /* Restyle the drag-over state deck-extras.js sets */
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

<section class="stack stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack stack-3">
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
      <strong>Not with "Drop" as the only instruction.</strong> Dragging is impossible on
      most phones and with a keyboard. Say "choose a file" first, and "or drop it here"
      after.
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
