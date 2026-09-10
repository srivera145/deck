<?php
declare(strict_types=1);

$page = [
    'path' => 'components/copy.php',
    'title' => 'Copy to clipboard',
    'level' => 'Beginner',
    'description' => 'Deck\'s .copy is a value in a scrollable mono row with a copy button welded to the end. The behaviour hangs off data-deck-copy, not the class — the class is only the look.',
    'documents' => [
        'copy', 'copy-btn', 'copy-done', 'copy-idle', 'copy-inline', 'copy-value', 'is-copied',
    ],

    'component' => 'copy',
    'accounts' => [
        '23-inputs.css' => 'documented: the row, the scrollable LTR-isolated value, the button and its copied state, the idle/done label swap and the inline icon-only variant',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Copy to clipboard</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Copy to clipboard</h1>
  <p class="lede">
    A value someone needs to move somewhere else — an API key, an install command, an order
    reference — with a button that puts it on the clipboard. The value stays selectable and
    readable; the button is a shortcut, not the only way in.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Where the value is long, exact, and going to be pasted: a token, a connection string, a
    shell command. Anything a reader would otherwise select by hand and probably clip a
    character off.
  </p>
  <p>
    Not for values people read rather than transfer. A price or a date gains nothing from a
    copy button, and every button on a page costs the reader a moment deciding whether it is
    for them.
  </p>
</section>

<section class="stack-3">
  <h2 id="attribute">The class is the look; the attribute is the behaviour</h2>
  <p>
    This is the one thing to get right. <code>deck-extras.js</code> binds to
    <code>[data-deck-copy]</code> — the attribute. <code>.copy-btn</code> is styling and
    nothing else, so a button with the class and no attribute looks exactly right and does
    nothing at all.
  </p>
  <?php
  docs_example(
      '<div class="copy" style="max-inline-size:30rem">' . "\n" .
      '  <code>sk_live_EXAMPLE_NOT_A_REAL_KEY</code>' . "\n" .
      '  <button type="button" class="copy-btn" data-deck-copy aria-label="Copy API key">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#copy"></use></svg>' . "\n" .
      '    <span>Copy</span>' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'A bare data-deck-copy takes the value from the .copy row it sits in',
      'stack'
  );
  ?>
  <p>
    With no value, the attribute means "look for the value yourself": the script takes the
    text of the <code>&lt;code&gt;</code> or <code>.copy-value</code> inside the surrounding
    <code>.copy</code>, and failing that the button's previous sibling. Give the attribute a
    selector and it copies that element instead — its <code>value</code> if it has one, its
    text if not.
  </p>
  <?php
  docs_example(
      '<div class="stack-3" style="max-inline-size:30rem">' . "\n" .
      '  <textarea class="textarea" id="dx-copy-src" rows="2">npm install deck-css --save-exact</textarea>' . "\n" .
      '  <button type="button" class="btn" data-deck-copy="#dx-copy-src">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#copy"></use></svg>' . "\n" .
      '    Copy the command' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'Any button anywhere can copy any element — .copy is not required',
      'stack'
  );
  ?>
  <p class="text-muted">
    The same function is exposed as <code>Deck.copy(text)</code>, which returns a promise
    resolving to <code>true</code> or <code>false</code>, for cases where the text does not
    exist on the page at all.
  </p>
</section>

<section class="stack-3">
  <h2 id="feedback">Two kinds of feedback, and how to choose</h2>
  <p>
    After a successful copy the button gets <code>.is-copied</code> for 1.8 seconds. What
    that shows depends on what is inside the button, and the switch is easy to miss:
  </p>
  <ul class="stack-2">
    <li>
      <strong>Put a <code>.copy-done</code> element inside the button</strong> and it swaps
      places with <code>.copy-idle</code> — the label changes in place, and no toast is
      raised.
    </li>
    <li>
      <strong>Leave it out</strong> and the script raises a
      <a href="toast.php">toast</a> instead: "Copied", for two seconds.
    </li>
    <li>
      <strong><code>.copy-inline</code> never toasts</strong>, whether or not it has a
      <code>.copy-done</code>. It is meant for a table cell, where a toast per row would be
      unbearable.
    </li>
  </ul>
  <?php
  docs_example(
      '<div class="copy" style="max-inline-size:30rem">' . "\n" .
      '  <span class="copy-value">https://deck.example/invite/9fQ2xR</span>' . "\n" .
      '  <button type="button" class="copy-btn" data-deck-copy aria-label="Copy invite link">' . "\n" .
      '    <span class="copy-idle">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#copy"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '    <span class="copy-done">' . "\n" .
      '      <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#check"></use></svg>' . "\n" .
      '    </span>' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'A .copy-done inside the button means the tick replaces the icon and no toast fires',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    <code>.copy-done</code> is <code>display: none</code> until <code>.is-copied</code>
    appears, at which point <code>.copy-idle</code> hides and it becomes
    <code>inline-flex</code>. Both live inside the button, so the button's width can jump if
    the two labels are different lengths — give them the same content width, or use icons of
    the same size as above.
  </p>
</section>

<section class="stack-3">
  <h2 id="inline">The inline variant</h2>
  <p>
    <code>.copy-inline</code> is a faint icon-only button for use beside a value already in
    the flow — an order number in a <a href="table.php">table</a>, an ID in a
    <a href="list.php">list</a>. It has no row, no border and no toast.
  </p>
  <?php
  docs_example(
      '<table class="table" style="max-inline-size:30rem">' . "\n" .
      '  <thead><tr><th>Order</th><th>Reference</th></tr></thead>' . "\n" .
      '  <tbody>' . "\n" .
      '    <tr><td>Tuesday</td><td><span id="dx-ref-1">ORD-4417-QK</span> ' .
      '<button type="button" class="copy-inline" data-deck-copy="#dx-ref-1" aria-label="Copy reference ORD-4417-QK">' .
      '<svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#copy"></use></svg></button></td></tr>' . "\n" .
      '    <tr><td>Wednesday</td><td><span id="dx-ref-2">ORD-4418-RM</span> ' .
      '<button type="button" class="copy-inline" data-deck-copy="#dx-ref-2" aria-label="Copy reference ORD-4418-RM">' .
      '<svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#copy"></use></svg></button></td></tr>' . "\n" .
      '  </tbody>' . "\n" .
      '</table>',
      'Each label names the value, so the two buttons are distinguishable in a list of links',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/23-inputs.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--line', '--surface', '--surface-2', '--surface-hover', '--good-100', '--good-700', '--font-mono', '--text-sm', '--r-sm', '--r-xs', '--tap', '--dur-1']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>The button needs a label that names the value.</strong> "Copy" is fine when
      there is one on the page and useless when there are six. In a table, put the value in
      the label — "Copy reference ORD-4417-QK" — as the example does.
    </li>
    <li>
      <strong>The first copy is announced to nobody.</strong> The script sets
      <code>aria-live="polite"</code> on the button <em>after</em> it has already swapped
      the label. A live region has to exist before the change for the change to be
      announced, so the first press is silent; every press after that announces, because the
      attribute is by then already there. Inconsistent rather than absent, which is harder to
      notice. Recorded in <code>FINDINGS.md</code>.
    </li>
    <li>
      <strong>The toast is the more reliable feedback.</strong> Where there is no
      <code>.copy-done</code>, Deck raises a toast, and
      <a href="toast.php#accessibility">the toast region</a> is a properly-declared live
      region. If it matters that the reader knows the copy worked, prefer that path.
    </li>
    <li>
      <strong>The value must stay selectable.</strong> It is real text in a
      <code>&lt;code&gt;</code> or a <code>&lt;span&gt;</code>, so anyone can select it by
      hand. Do not replace it with an image or a disabled input — the button is an
      accelerator and the manual route has to keep working.
    </li>
    <li>
      <strong>The row scrolls, and the scrollbar is hidden.</strong>
      <code>scrollbar-width: none</code> plus a hidden WebKit scrollbar means a long value
      can be scrolled horizontally with no visible indication that there is more. A
      keyboard user cannot reach that overflow at all unless the element is focusable. This
      is the same gap recorded for Deck's other scroll containers.
    </li>
    <li>
      <strong>Failure is silent.</strong> If the clipboard write fails,
      <code>.is-copied</code> is not added and nothing is said. The reader presses the
      button and nothing happens, which they will read as a broken page rather than a
      blocked permission.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="context">Secure contexts and the fallback</h2>
  <p>
    <code>navigator.clipboard</code> only exists in a secure context — HTTPS, or
    <code>localhost</code>. On a plain HTTP origin it is undefined, and the script falls back
    to creating an off-screen <code>&lt;textarea&gt;</code>, selecting it and calling
    <code>document.execCommand('copy')</code>.
  </p>
  <p>
    That fallback works today in every engine and is
    <strong>deprecated in all of them</strong>. It is a bridge for old browsers and
    non-secure origins, not a strategy — serve the page over HTTPS and the modern path is
    the one that runs. Deck already records the deprecation in
    <code>FINDINGS.md</code>, since <a href="editor.php">the editor</a> depends on
    <code>execCommand</code> far more heavily than this does.
  </p>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The row mirrors — the button moves to the left — but the value does not. The value cell
    carries <code>direction: ltr</code> and <code>unicode-bidi: isolate</code>, for the same
    reason as <a href="phone.php#rtl">the phone field</a>: a key, a URL or a command is a
    sequence, not a sentence, and reversing it would be wrong. The <code>isolate</code> stops
    surrounding Arabic or Hebrew text from re-ordering the punctuation inside it.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="copy" style="max-inline-size:30rem">' . "\n" .
      '  <code>sk_live_EXAMPLE_NOT_A_REAL_KEY</code>' . "\n" .
      '  <button type="button" class="copy-btn" data-deck-copy aria-label="نسخ المفتاح">' . "\n" .
      '    <svg class="icon" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#copy"></use></svg>' . "\n" .
      '    <span>نسخ</span>' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'Button on the left, key still reading left to right',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The button transitions <code>background-color</code> and <code>color</code> over
    <?= e(api_token('--dur-1')['value'] ?? '110ms') ?>, and the global reset collapses it.
    The idle-to-done swap is <code>display</code>, which has never been animated here, so
    the tick appears instantly in every setting. If a toast is raised instead, its motion is
    <a href="toast.php#motion">governed by the toast</a>.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>.copy</code> is not in <code>src/99-print.css</code>, and it needs attention if
    your page prints. The value itself prints correctly — real text in a mono face — but the
    button prints with it, and a long value that was scrolled horizontally on screen prints
    <strong>truncated at the visible width</strong>, because <code>white-space: nowrap</code>
    and <code>overflow-x</code> still apply on paper.
  </p>
  <pre class="dx-code"><code><?= e('@media print {
  .copy-btn, .copy-inline { display: none; }
  .copy { border: 0; background: none; }
  /* the important half: let the value wrap instead of being cut off */
  .copy > code, .copy > .copy-value { white-space: pre-wrap; overflow: visible; }
}') ?></code></pre>
  <p class="text-muted">
    An API key silently cut in half on a printed runbook is worse than no key at all.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Let a long value wrap on screen rather than scroll */
  .copy > code { white-space: pre-wrap; overflow: visible; }

  /* Bring the hidden scrollbar back */
  .copy > :is(code, .copy-value) { scrollbar-width: thin; }
  .copy > :is(code, .copy-value)::-webkit-scrollbar { display: block; block-size: 6px; }

  /* Stop the button jumping between its two labels */
  .copy-btn { min-inline-size: 7rem; justify-content: center; }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a value people read.</strong> A total, a date, a name. The button
      implies the value is going somewhere else.
    </li>
    <li>
      <strong>Not for a secret you should not be showing.</strong> A copy button on a
      password field displays the password to everyone in the room. If it must not be read,
      it must not be on screen — copy it from a store, with
      <code>Deck.copy()</code>, without rendering it.
    </li>
    <li>
      <strong>Not as the only way to get the value.</strong> Clipboard writes can be blocked
      by permissions, by the browser, or by a non-secure origin, and the failure is silent.
      The text has to remain selectable.
    </li>
    <li>
      <strong>Not on every row of a long table.</strong> Fifty copy buttons is fifty tab
      stops. Consider one button that copies the column.
    </li>
    <li>
      <strong>Not with <code>.copy-btn</code> alone.</strong> Without
      <code>data-deck-copy</code> it is a button that looks like it works and does not — see
      <a href="#attribute">the top of this page</a>.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
