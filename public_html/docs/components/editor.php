<?php
declare(strict_types=1);

$page = [
    'path' => 'components/editor.php',
    'title' => 'Editor',
    'level' => 'Advanced',
    'description' => 'Deck\'s .editor is a contenteditable region with a toolbar, driven by document.execCommand and mirrored into a hidden input so an ordinary form post carries the HTML.',
    'documents' => [
        'editor', 'editor-content', 'editor-count', 'editor-footer', 'editor-select',
        'editor-sep', 'editor-tool', 'editor-tool-wide', 'editor-toolbar', 'is-over',
    ],

    'component' => 'editor',
    'accounts' => [
        '23-inputs.css' => 'documented: the frame, the toolbar and its tools, the editable region and the character count',
        '24-media.css'  => 'documented: images and figures pasted into the content — the Content styling section',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Editor</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Editor</h1>
  <p class="lede">
    <code>.editor</code> is a small rich-text field: a toolbar over a
    <code>contenteditable</code> region, with the resulting HTML mirrored into a hidden
    input so an ordinary form post carries it. It is the most cautious component in Deck,
    and the caution is warranted — read
    <a href="#execcommand">the note on execCommand</a> before choosing it.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it where a reader needs bold, a list and a link in a short piece of text — a
    comment, a description, a note — and a full editing surface would be too much. For
    plain text use <a href="textarea.php"><code>.textarea</code></a>, which is simpler in
    every way.
  </p>
  <?php
  docs_example(
      '<div class="editor" data-target="#dx-editor-value" data-limit="280">' . "\n" .
      '  <div class="editor-toolbar">' . "\n" .
      '    <button class="editor-tool" data-cmd="bold" aria-pressed="false" aria-label="Bold"><strong>B</strong></button>' . "\n" .
      '    <button class="editor-tool" data-cmd="italic" aria-pressed="false" aria-label="Italic"><em>I</em></button>' . "\n" .
      '    <div class="editor-sep"></div>' . "\n" .
      '    <button class="editor-tool" data-cmd="insertUnorderedList" aria-pressed="false" aria-label="Bulleted list">&bull;</button>' . "\n" .
      '    <button class="editor-tool editor-tool-wide" data-cmd="createLink" aria-label="Add a link">Link</button>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="editor-content" data-placeholder="Write a note"></div>' . "\n" .
      '  <div class="editor-footer">' . "\n" .
      '    <span class="editor-count">0 / 280</span>' . "\n" .
      '  </div>' . "\n" .
      '</div>' . "\n" .
      '<input type="hidden" id="dx-editor-value" name="note">',
      'Type in it. The count updates and turns red past the limit.',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>data-target</code> is the selector of the hidden input to mirror into, and
    <code>data-limit</code> the character count. Both are optional; without a target the
    editor still works and you read <code>innerHTML</code> yourself, or listen for
    <code>deck:change</code>.
  </p>
</section>

<section class="stack-3">
  <h2 id="toolbar">The toolbar</h2>
  <p>
    <code>.editor-tool</code> is a square button carrying <code>data-cmd</code>;
    <code>.editor-tool-wide</code> is the same with room for a word.
    <code>.editor-sep</code> is a one-pixel divider, and <code>.editor-select</code> a
    dropdown for block format.
  </p>
  <p>
    The pressed state is <code>[aria-pressed="true"]</code> — the accessible state doing
    the styling, as with <a href="tabs.php"><code>.tab</code></a>. After every command and
    every selection change, <code>deck-extras.js</code> reads
    <code>document.queryCommandState</code> for each tool and writes the attribute back,
    so the toolbar reflects the caret rather than the last button pressed.
  </p>
</section>

<section class="stack-6">
  <h2 id="execcommand">The execCommand problem</h2>
  <p>
    <code>document.execCommand</code> is how this component applies formatting, and it is
    <strong>deprecated</strong>. It is worth being plain about what that means, because
    the component is useful and the caveat is real.
  </p>
  <ul class="stack-2">
    <li>
      <strong>It is not going away soon.</strong> Every browser implements it and the web
      depends on it too heavily to remove. Deprecated here means unmaintained, not
      scheduled for deletion.
    </li>
    <li>
      <strong>The HTML it produces varies by engine.</strong> Bold might be
      <code>&lt;b&gt;</code>, <code>&lt;strong&gt;</code> or a
      <code>&lt;span style&gt;</code> depending on the browser and what was already
      applied. If you store the output, you are storing something inconsistent.
    </li>
    <li>
      <strong>There is no replacement.</strong> The standards work that would have
      replaced it stalled. Real editors — ProseMirror, Lexical, TipTap — reimplement
      editing from scratch over a document model, which is tens of kilobytes and a
      different kind of component.
    </li>
  </ul>
  <p class="dx-note text-muted">
    <strong>Sanitise the output on the server.</strong> Always, without exception. A
    <code>contenteditable</code> region accepts pasted HTML — Deck intercepts
    <code>paste</code> and inserts plain text, which handles the common case — but the
    field is still user-controlled markup arriving at your server, and the browser is not
    a validator.
  </p>
</section>

<section class="stack-3">
  <h2 id="content">Content styling</h2>
  <p>
    <code>.editor-content</code> styles what is inside it the way
    <code>.prose</code> does: <code>&gt; * + *</code> for the vertical rhythm, more space
    before headings, list padding, and a blockquote treatment.
  </p>
  <p>
    <code>.editor-content:empty::before</code> takes its text from
    <code>data-placeholder</code> — the same trick as the CSS-only
    <a href="tooltip.php#css-tooltip">tooltip</a>, and the reason a placeholder needs no
    extra element and no script to hide it.
  </p>
  <p class="text-muted">
    <code>src/24-media.css</code> adds the rules for an image or a figure pasted into the
    region, so a screenshot dropped into a comment does not break out of the frame.
  </p>
</section>

<section class="stack-3">
  <h2 id="count">The character count</h2>
  <p>
    <code>.editor-count</code> shows the length, and <code>.is-over</code> turns it red
    when <code>data-limit</code> is exceeded. It is a <strong>soft</strong> limit —
    nothing stops the reader typing past it, which is deliberate: silently refusing
    keystrokes reads as a broken keyboard.
  </p>
  <p class="dx-note text-muted">
    Because it is soft, the server has to enforce it. And because the count is not in a
    live region, a screen-reader user is not told when they pass the limit — put
    <code>role="status"</code> on <code>.editor-count</code> if the limit matters.
  </p>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/23-inputs.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--surface-2', '--surface-hover', '--line', '--text', '--text-muted', '--focus', '--ring', '--brand-soft', '--brand-soft-text', '--bad-700', '--r-sm', '--space-1', '--space-2', '--space-3', '--space-5', '--space-6']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The region is given a role by the script.</strong>
      <code>deck-extras.js</code> sets <code>role="textbox"</code> and
      <code>aria-multiline="true"</code> on <code>.editor-content</code>, so it is
      announced as a text field rather than as a block of prose.
    </li>
    <li>
      <strong>It still needs a label.</strong> The role gives it a type, not a name. Add
      <code>aria-labelledby</code> pointing at a visible label, or
      <code>aria-label</code>.
    </li>
    <li>
      <strong>Toolbar buttons need labels too.</strong> "B" is announced as the letter B.
      Every example here carries <code>aria-label</code>, and the visible glyph is
      decorative.
    </li>
    <li>
      <strong><code>aria-pressed</code> is kept in sync with the caret</strong>, which is
      the part most hand-built toolbars get wrong — a bold button that stays pressed after
      the caret leaves bold text is actively misleading.
    </li>
    <li>
      <strong>The toolbar is not a <code>role="toolbar"</code></strong>, so it is a series
      of tab stops rather than one with arrow keys. For a toolbar of four that is
      acceptable; for a dozen it is a lot of tabbing to reach the content.
    </li>
    <li>
      <strong>Formatting shortcuts are the browser's.</strong> Ctrl/Cmd+B and +I work
      inside a <code>contenteditable</code> without Deck doing anything — worth knowing,
      because it means keyboard users are not dependent on the toolbar.
    </li>
    <li>
      <strong>The link command uses <code>prompt()</code>.</strong> A browser dialog is
      focus-trapping and accessible, but it is also jarring and cannot be styled. For
      anything beyond a prototype, replace it with a <a href="modal.php">modal</a>.
    </li>
    <li>
      <strong>The placeholder is <code>::before</code> content</strong>, so like every
      generated-content placeholder it is not a reliable accessible name. It is a hint;
      the label carries the name.
    </li>
    <li>
      <strong>The count is not announced.</strong> See <a href="#count">above</a>.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The toolbar is a flex row, the separator uses <code>margin-inline</code>, and the
    content's list indentation is <code>padding-inline-start</code> — so the whole
    component mirrors with no rule of its own, and a list inside it indents from the
    correct edge.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="editor">' . "\n" .
      '  <div class="editor-toolbar">' . "\n" .
      '    <button class="editor-tool" data-cmd="bold" aria-pressed="false" aria-label="عريض"><strong>B</strong></button>' . "\n" .
      '    <div class="editor-sep"></div>' . "\n" .
      '    <button class="editor-tool" data-cmd="insertUnorderedList" aria-pressed="false" aria-label="قائمة">&bull;</button>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="editor-content" data-placeholder="اكتب ملاحظة"></div>' . "\n" .
      '</div>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Tools transition their background on hover, and the frame transitions its border and
    ring on focus. The global reset collapses both to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>. Nothing else moves.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    There is no <code>.editor</code> rule in <code>src/99-print.css</code>, so the frame,
    the toolbar and the footer all print — including a row of formatting buttons that
    cannot be pressed on paper.
  </p>
  <p class="dx-note text-muted">
    <code>.editor-toolbar</code> and <code>.editor-footer</code> belong in the never-print
    list beside <code>.dg-toolbar</code>. Recorded in <code>FINDINGS.md</code>; the
    content region itself should print as prose, which it already would.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* A taller editing area */
  .editor-content { min-block-size: 12rem; }

  /* A toolbar that scrolls rather than wrapping */
  .editor-toolbar { flex-wrap: nowrap; overflow-x: auto; }

  /* Announce the character count */
  /* markup: <span class="editor-count" role="status">…</span> */
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for plain text.</strong> Use
      <a href="textarea.php"><code>.textarea</code></a>. It is a real form control with
      real validation, and it needs no script.
    </li>
    <li>
      <strong>Not for a document.</strong> Tables, images with captions, undo across a
      long session, collaborative editing — none of that is here. Use a real editor
      library.
    </li>
    <li>
      <strong>Not for Markdown.</strong> If the stored format is Markdown, a
      <code>.textarea</code> with a preview is simpler and round-trips exactly;
      <code>execCommand</code> output would have to be converted, inconsistently.
    </li>
    <li>
      <strong>Not for code.</strong> No monospace, no indentation handling, no syntax
      highlighting — and <code>contenteditable</code> will happily insert
      <code>&lt;div&gt;</code>s into what you thought was a code block.
    </li>
    <li>
      <strong>Not without server-side sanitising.</strong> Not a style preference. The
      field produces HTML from user input, and only the server can decide what is safe.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
