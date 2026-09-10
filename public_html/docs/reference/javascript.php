<?php
declare(strict_types=1);

/**
 * The JavaScript API.
 *
 * Hand-written. There is no docblock convention in src/js/*.js consistent
 * enough to extract from, and a generated signature list would not answer the
 * question that actually costs people time — what a method does when the thing
 * it depends on is not there. Every entry below says.
 *
 * The method list is authored as data and rendered through one function so the
 * shape cannot drift between entries. The prose in it is written, not derived.
 */

$page = [
    'path' => 'reference/javascript.php',
    'title' => 'JavaScript API',
    'level' => 'Intermediate',
    'description' => "Every public method on the Deck global, with signature, parameters, return value, an example, and what each one does when the API it needs is missing.",
];

require __DIR__ . '/../_layout.php';

/**
 * One method entry. `absent` is the field that earns this page: every method
 * here can be called in a browser missing the thing it wants, and silence is a
 * worse answer than a sentence.
 */
function js_method(array $m): void
{
    ?>
    <article class="stack-3" id="<?= e($m['id']) ?>">
      <h3><code><?= e($m['signature']) ?></code></h3>
      <p><?= $m['summary'] ?></p>

      <?php if (!empty($m['params'])): ?>
        <div class="table-wrap">
          <table class="table table-stack">
            <caption class="sr-only">Parameters for <?= e($m['signature']) ?></caption>
            <thead>
              <tr><th scope="col">Parameter</th><th scope="col">Type</th><th scope="col">Meaning</th></tr>
            </thead>
            <tbody>
              <?php foreach ($m['params'] as [$pname, $ptype, $pdesc]): ?>
                <tr>
                  <th scope="row" data-label="Parameter"><code><?= e($pname) ?></code></th>
                  <td data-label="Type"><code class="dx-dim"><?= e($ptype) ?></code></td>
                  <td data-label="Meaning"><?= $pdesc ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

      <dl class="stack-2">
        <dt><strong>Returns</strong></dt>
        <dd><?= $m['returns'] ?></dd>
        <dt><strong>When what it needs is missing</strong></dt>
        <dd><?= $m['absent'] ?></dd>
      </dl>

      <pre class="dx-code"><code><?= e($m['example']) ?></code></pre>
      <p class="text-sm text-muted">Defined in <code><?= e($m['source']) ?></code>.</p>
    </article>
    <?php
}

/* ---------------------------------------------------------------------------
   Core — dist/deck.js
   ------------------------------------------------------------------------ */

$CORE = [
    [
        'id' => 'init',
        'signature' => 'Deck.init(root = document)',
        'summary' => 'Finds every element inside <code>root</code> carrying a <code>data-deck-*</code>'
            . ' attribute and wires its behaviour. Runs itself once on'
            . ' <code>DOMContentLoaded</code>; call it again after you inject markup.',
        'params' => [
            ['root', 'Element | Document', 'The subtree to scan. Defaults to the whole document.'],
        ],
        'returns' => 'The <code>Deck</code> object, so calls chain.',
        'absent' => 'Nothing observes the DOM — there is no MutationObserver — so HTML added'
            . ' after load is inert until you call this. Calling it twice is safe: every'
            . ' wiring sets a once-guard on the element (<code>data-deck-wired</code>,'
            . ' <code>data-deck-x</code>, <code>data-deck-a</code>, or a <code>_deck</code>'
            . ' property) and skips anything already wired, so re-running it over a whole'
            . ' page only costs the query.',
        'example' => "const res = await fetch('/rows?page=2');\n"
            . "list.insertAdjacentHTML('beforeend', await res.text());\n"
            . "Deck.init(list);   // only the new rows get wired",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'toast',
        'signature' => 'Deck.toast(opts)',
        'summary' => 'Pushes a notification into the toast region and returns a handle to it.'
            . ' A string is shorthand for <code>{ title: string }</code>.',
        'params' => [
            ['opts', 'string | object', 'The message, or the options below.'],
            ['opts.title', 'string', 'The bold first line. Escaped before insertion.'],
            ['opts.text', 'string', 'A second line of detail. Escaped.'],
            ['opts.kind', 'string', 'One of <code>good</code>, <code>bad</code>, <code>warn</code>,'
                . ' <code>info</code>, <code>loading</code>, or omitted for neutral. Chooses the'
                . ' icon, the tint, and whether the element is announced as an alert.'],
            ['opts.duration', 'number', 'Milliseconds before it dismisses itself. Default'
                . ' <code>5000</code>. <code>0</code> means it stays until something dismisses it.'],
            ['opts.actions', 'Array', 'Buttons, as <code>{ label, onClick, close }</code>.'
                . ' <code>onClick</code> receives the handle. The toast closes after the action'
                . ' unless <code>close</code> is <code>false</code>.'],
            ['opts.dismissible', 'boolean', 'Whether to render the close button. Default <code>true</code>.'],
            ['opts.onDismiss', 'Function', 'Called with the handle once it has been dismissed,'
                . ' however that happened.'],
        ],
        'returns' => 'A <strong>handle</strong>: <code>{ node, opts, dismiss(), update(patch) }</code>.'
            . ' <code>node</code> is the live element, <code>opts</code> is the merged options,'
            . ' <code>dismiss()</code> removes it now, and <code>update(patch)</code> merges new'
            . ' options in, repaints the body and restarts the timer — which is what turns a'
            . ' loading toast into a result rather than stacking a second toast on top of it.',
        'absent' => 'No markup is required. If the page has no <code>.toast-region</code>, one is'
            . ' created and appended to <code>&lt;body&gt;</code> on the first call. The element'
            . ' is given <code>role="alert"</code> when <code>kind</code> is <code>bad</code> and'
            . ' <code>role="status"</code> otherwise, so it is announced without any ARIA on your'
            . ' side. Three are visible at once; the rest are kept in the DOM with'
            . ' <code>aria-hidden</code>, and pushing a ninth dismisses the oldest.',
        'example' => "Deck.toast('Order #1042 shipped');\n\n"
            . "Deck.toast({\n"
            . "  kind: 'warn',\n"
            . "  title: 'Project archived',\n"
            . "  text: 'You can still restore it from the archive.',\n"
            . "  duration: 8000,\n"
            . "  actions: [{ label: 'Undo', onClick: restore }]\n"
            . "});\n\n"
            . "// The handle is the point: one toast, two states\n"
            . "const t = Deck.toast({ kind: 'loading', title: 'Submitting…', duration: 0 });\n"
            . "try {\n"
            . "  await save();\n"
            . "  t.update({ kind: 'good', title: 'Submitted', duration: 4000 });\n"
            . "} catch (err) {\n"
            . "  t.update({ kind: 'bad', title: 'Could not submit', text: err.message, duration: 0 });\n"
            . "}",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'toasts',
        'signature' => 'Deck.toasts',
        'summary' => 'The queue behind <code>Deck.toast()</code>, exposed so you can act on all of'
            . ' them at once. <code>.items</code> is the live array, newest first;'
            . ' <code>.clear()</code> dismisses every one; <code>.max</code> is how many are'
            . ' visible at a time and <code>.stacked</code> whether they pile up or list out.',
        'params' => [],
        'returns' => 'The queue object. <code>Deck.toasts.push(opts)</code> is what'
            . ' <code>Deck.toast()</code> calls.',
        'absent' => 'Setting <code>.max</code> or <code>.stacked</code> after toasts are already'
            . ' on screen affects the next reflow rather than repainting immediately. Change them'
            . ' at startup.',
        'example' => "Deck.toasts.max = 5;\n"
            . "window.addEventListener('beforeunload', () => Deck.toasts.clear());",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'theme',
        'signature' => 'Deck.theme(mode?)',
        'summary' => 'Reads or sets the colour theme by writing <code>data-theme</code> on the'
            . ' <code>&lt;html&gt;</code> element.',
        'params' => [
            ['mode', 'string', "<code>'light'</code> or <code>'dark'</code>. Omit to read."],
        ],
        'returns' => "With no argument, the current theme, or <code>'auto'</code> when no"
            . ' attribute is set and the operating system decides. With an argument, the mode you'
            . ' passed.',
        'absent' => 'The choice is persisted to <code>localStorage</code> under'
            . ' <code>deck-theme</code> inside a try/catch, so private browsing or blocked'
            . ' storage costs you the persistence and nothing else — the attribute is still set'
            . ' and the page still changes. On the next load the saved value is applied before'
            . ' <code>Deck.init()</code> runs, so there is no flash of the wrong theme.',
        'example' => "Deck.theme();          // 'auto', 'light' or 'dark'\n"
            . "Deck.theme('dark');\n\n"
            . "// A button with data-deck-theme does this for you, with no code at all",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'hue',
        'signature' => 'Deck.hue(n)',
        'summary' => 'Sets <code>--hue-brand</code> as an inline style on'
            . ' <code>&lt;html&gt;</code>. Every colour derived from the brand hue — fills,'
            . ' borders, focus rings, shadows, chart series — follows immediately.',
        'params' => [
            ['n', 'number | string', 'An OKLCH hue angle, 0 to 360.'],
        ],
        'returns' => 'Nothing.',
        'absent' => 'Not persisted and not validated. A value outside 0–360 wraps the way OKLCH'
            . ' does rather than erroring. For per-tenant theming set the property server-side'
            . ' instead, so the first paint is already correct — <code>Deck::theme()</code> in the'
            . ' PHP helper emits exactly that.',
        'example' => "// Live preview from a slider\n"
            . "range.addEventListener('input', () => Deck.hue(range.value));",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'dir',
        'signature' => 'Deck.dir(value?)',
        'summary' => 'Reads or sets the text direction on <code>&lt;html&gt;</code>. Deck is'
            . ' written entirely in logical properties, so this is the whole of a right-to-left'
            . ' flip — there is no second stylesheet to load.',
        'params' => [
            ['value', 'string', "<code>'ltr'</code> or <code>'rtl'</code>. Omit to read."],
        ],
        'returns' => "The current direction, defaulting to <code>'ltr'</code> when the attribute"
            . ' is absent.',
        'absent' => 'Persisted to <code>localStorage</code> under <code>deck-dir</code> in a'
            . ' try/catch, and restored before init on the next load. Note that this sets the'
            . ' direction only — it does not translate anything, and a right-to-left page in'
            . ' English is a demo rather than a localisation.',
        'example' => "Deck.dir('rtl');\n"
            . "document.querySelectorAll('.mirror-rtl').length;  // glyphs that flip with it",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'locale',
        'signature' => 'Deck.locale(tag?)',
        'summary' => 'Month and weekday names, and the first day of the week, from'
            . ' <code>Intl</code>. This is what the date picker reads, and it is public because'
            . ' the same data is usually wanted beside it.',
        'params' => [
            ['tag', 'string', "A BCP 47 tag such as <code>'de-AT'</code>. Omit to use the page."],
        ],
        'returns' => '<code>{ locale, months, monthsShort, days, weekStart, long, full }</code>.'
            . ' <code>days</code> runs Sunday to Saturday; <code>weekStart</code> is the index the'
            . ' locale actually begins on — 0 in the US, 1 across most of Europe.'
            . ' <code>long</code> and <code>full</code> are <code>Intl.DateTimeFormat</code>'
            . ' instances, ready to <code>.format()</code>.',
        'absent' => 'With no tag it falls back through <code>&lt;html lang&gt;</code>,'
            . ' <code>navigator.language</code>, then <code>en</code>. Where'
            . ' <code>Intl.Locale.getWeekInfo()</code> is unsupported — Firefox, at the time of'
            . ' writing — <code>weekStart</code> comes back as <code>0</code> rather than'
            . ' throwing, so a European page there starts its weeks on Sunday until that lands.'
            . ' Results are cached per tag.',
        'example' => "const { months, weekStart, long } = Deck.locale('fr-FR');\n"
            . "months[0];              // 'janvier'\n"
            . "weekStart;              // 1\n"
            . "long.format(new Date());",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'transition',
        'signature' => 'Deck.transition(update, opts = {})',
        'summary' => 'Wraps a DOM change so the browser tweens between the before and after'
            . ' states, using the View Transition API.',
        'params' => [
            ['update', 'Function', 'The mutation to run. It may be async; the transition waits'
                . ' for the returned promise.'],
            ['opts.direction', 'string', 'Written to <code>data-vt</code> on'
                . ' <code>&lt;html&gt;</code> for the duration, so your CSS can animate forward'
                . ' and back differently.'],
            ['opts.name', 'string', 'A label for the transition, for your own bookkeeping.'],
        ],
        'returns' => 'The <code>ViewTransition</code>, with <code>.ready</code> and'
            . ' <code>.finished</code> promises.',
        'absent' => 'This is the one to know. Where <code>document.startViewTransition</code> does'
            . ' not exist, <em>or</em> the reader has asked for reduced motion, the update runs'
            . ' immediately and the return value is a stub with the same shape —'
            . ' <code>{ finished, ready }</code>, both already resolved. So'
            . ' <code>await Deck.transition(fn).finished</code> is safe to write unconditionally,'
            . ' and you never need to feature-detect at the call site.',
        'example' => "Deck.transition(() => row.remove());\n\n"
            . "await Deck.transition(() => {\n"
            . "  list.prepend(newRow);\n"
            . "}, { direction: 'forward' }).finished;\n"
            . "newRow.focus();",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'play',
        'signature' => 'Deck.play(node, className)',
        'summary' => 'Adds a one-shot animation class, waits for it, then cleans up after itself.'
            . ' It also removes the class and forces a reflow first, so playing the same'
            . ' animation twice in a row actually plays twice.',
        'params' => [
            ['node', 'Element', 'The element to animate.'],
            ['className', 'string', 'An animation class such as <code>shake</code>,'
                . ' <code>flash-good</code> or <code>pop</code>.'],
        ],
        'returns' => 'A <code>Promise</code> resolving to the node when the animation ends.'
            . ' <code>is-animating</code> is on the element for the duration, which is what the'
            . ' motion stylesheet keys its will-change hints off.',
        'absent' => 'If <code>node</code> is null or undefined it resolves immediately rather than'
            . ' throwing, so an optional target does not need a guard. There is also a 2000ms'
            . ' safety timeout: a class that triggers no animation at all — a typo, or a rule'
            . ' switched off under reduced motion — still resolves and still cleans up, rather'
            . ' than leaving a promise pending forever.',
        'example' => "await Deck.play(field, 'shake');\n"
            . "field.focus();\n\n"
            . "Deck.play(card, 'flash-good');",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'toggle',
        'signature' => 'Deck.toggle(node, force?)',
        'summary' => 'Opens or closes a panel with an animated height, with no measuring. It adds'
            . ' <code>.expand</code> and toggles <code>.is-open</code>; the animation comes from'
            . ' <code>interpolate-size</code> in the stylesheet.',
        'params' => [
            ['node', 'Element', 'The panel.'],
            ['force', 'boolean', 'Open or close explicitly instead of toggling.'],
        ],
        'returns' => 'The resulting open state, as a boolean.',
        'absent' => 'The height animation needs <code>interpolate-size: allow-keywords</code>,'
            . ' which Deck opts into on <code>:root</code>. Where the browser does not support it'
            . ' the panel still opens and closes correctly — it simply snaps instead of sliding.'
            . ' Nothing is measured and nothing is read back, so this cannot desynchronise from'
            . ' the content the way a JavaScript height animation does.',
        'example' => "trigger.addEventListener('click', () => {\n"
            . "  const open = Deck.toggle(panel);\n"
            . "  trigger.setAttribute('aria-expanded', String(open));\n"
            . "});",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'flip',
        'signature' => 'Deck.flip(node, force?)',
        'summary' => 'Toggles <code>.is-flipped</code>, which turns a flip card over.',
        'params' => [
            ['node', 'Element', 'The card.'],
            ['force', 'boolean', 'Set the state explicitly rather than toggling.'],
        ],
        'returns' => 'The resulting state, as a boolean.',
        'absent' => 'Purely a class toggle; the rotation is CSS. Under reduced motion the'
            . ' stylesheet cross-fades the faces instead of rotating them, so the method behaves'
            . ' the same and the appearance changes. An element with <code>data-deck-flip</code>'
            . ' does this on click without any code.',
        'example' => "Deck.flip(card);\n"
            . "Deck.flip(card, false);   // back to the front, whatever it was showing",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'advance',
        'signature' => 'Deck.advance(stack)',
        'summary' => 'Advances a depth stack — the swipeable pile of cards — by one, by'
            . ' dispatching a <code>deck:advance</code> event on it.',
        'params' => [
            ['stack', 'Element', 'The <code>.stack-depth</code> element.'],
        ],
        'returns' => 'Nothing.',
        'absent' => 'It dispatches an event rather than manipulating the pile, so it works whether'
            . ' the stack was wired by <code>Deck.init()</code> or by you. If nothing is listening'
            . ' the event goes nowhere and no error is raised.',
        'example' => "Deck.advance(document.querySelector('.stack-depth'));",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'face',
        'signature' => 'Deck.face(cube, name)',
        'summary' => 'Rotates a 3D cube to a named face by setting <code>data-face</code>.',
        'params' => [
            ['cube', 'Element', 'The <code>.cube</code> element.'],
            ['name', 'string', 'One of <code>front</code>, <code>back</code>, <code>left</code>,'
                . ' <code>right</code>, <code>top</code>, <code>bottom</code>.'],
        ],
        'returns' => 'The name you passed.',
        'absent' => 'An unrecognised name is written to the attribute and no rule matches it, so'
            . ' the cube stays where it was. The rotation is a CSS transition on the attribute'
            . ' selector, so this is inert without the stylesheet and silent under reduced motion.',
        'example' => "Deck.face(cube, 'right');",
        'source' => 'src/js/deck.js',
    ],
    [
        'id' => 'reduced',
        'signature' => 'Deck.reduced()',
        'summary' => 'Whether the reader has asked their system to reduce motion. Deck checks this'
            . ' itself everywhere it animates; it is public so your own code can make the same'
            . ' decision from the same place.',
        'params' => [],
        'returns' => '<code>true</code> when <code>prefers-reduced-motion: reduce</code> matches.',
        'absent' => 'Evaluated on each call rather than cached, so it follows a preference changed'
            . ' mid-session. A browser with no matchMedia support would throw, but every browser'
            . ' Deck targets has it.',
        'example' => "if (!Deck.reduced()) {\n"
            . "  confetti();\n"
            . "}",
        'source' => 'src/js/deck.js',
    ],
];

/* ---------------------------------------------------------------------------
   Extras and adapters
   ------------------------------------------------------------------------ */

$EXTRAS = [
    [
        'id' => 'copy',
        'signature' => 'Deck.copy(text)',
        'summary' => 'Writes a string to the clipboard.',
        'params' => [
            ['text', 'string', 'What to copy.'],
        ],
        'returns' => 'A <code>Promise&lt;boolean&gt;</code> — whether the copy succeeded.',
        'absent' => 'The async clipboard API is only available on secure origins, so on plain'
            . ' <code>http</code> and in older engines this falls back to a hidden textarea and'
            . ' <code>document.execCommand("copy")</code>. That fallback is deprecated and may'
            . ' eventually stop working, which is why the return value is a boolean worth'
            . ' checking rather than something to ignore. It never throws.',
        'example' => "const ok = await Deck.copy(input.value);\n"
            . "Deck.toast(ok ? 'Copied' : { kind: 'bad', title: 'Could not copy' });",
        'source' => 'src/js/deck-extras.js',
    ],
    [
        'id' => 'qr',
        'signature' => 'Deck.qr.svg(text, ecl = "M")',
        'summary' => 'Encodes a string as a QR code and returns it as SVG markup. The encoder is'
            . ' Deck\'s own — there is no library behind it and no network call, so this works'
            . ' offline and leaks nothing.',
        'params' => [
            ['text', 'string', 'What to encode.'],
            ['ecl', 'string', 'Error correction level: <code>L</code>, <code>M</code>,'
                . ' <code>Q</code> or <code>H</code>. Higher levels survive more damage and hold'
                . ' less data.'],
        ],
        'returns' => 'An SVG string, sized in module units with a <code>viewBox</code>, so it'
            . ' scales to whatever box you put it in. Horizontal runs are merged into single'
            . ' rects, which keeps the markup small.',
        'absent' => 'Throws where the text is too long for the largest version the encoder'
            . ' supports at that error correction level — the message says so. An element with'
            . ' <code>data-deck-qr</code> catches that and renders the message in place instead,'
            . ' so a too-long value degrades to readable text rather than a blank box.',
        'example' => "node.innerHTML = Deck.qr.svg('https://example.com/ticket/1042', 'H');",
        'source' => 'src/js/deck-extras.js',
    ],
    [
        'id' => 'adapters',
        'signature' => 'Deck.adapters',
        'summary' => 'What optional libraries are on the page, and what is actually doing the'
            . ' work. <code>.available</code> is a getter returning a boolean per library;'
            . ' <code>.report()</code> answers the more useful question.',
        'params' => [],
        'returns' => '<code>.available</code> gives'
            . ' <code>{ floating, quill, tiptap, chart, sortable, lucide }</code>.'
            . ' <code>.report()</code> gives <code>{ placement, editor, charts, dragDrop, icons,'
            . ' virtualization }</code>, each a sentence naming the implementation in use.',
        'absent' => 'This is the point of it: every adapter has a built-in fallback, so nothing'
            . ' here being present is a supported configuration. Placement falls back from CSS'
            . ' anchor positioning to Floating UI to Deck\'s own; the editor from Tiptap to Quill'
            . ' to <code>execCommand</code>; charts from Chart.js to Deck\'s CSS charts; drag and'
            . ' drop from SortableJS to the native HTML API. Paste'
            . ' <code>Deck.adapters.report()</code> into a console when a component behaves'
            . ' differently on two pages — it is usually this.',
        'example' => "Deck.adapters.available.chart;   // false\n"
            . "Deck.adapters.report();\n"
            . "// { placement: 'CSS anchor positioning',\n"
            . "//   editor: 'Deck built-in (execCommand)',\n"
            . "//   charts: 'Deck CSS charts', … }",
        'source' => 'src/js/deck-adapters.js',
    ],
    [
        'id' => 'chart',
        'signature' => 'Deck.chart(canvas, config)',
        'summary' => 'Creates a Chart.js chart with Deck\'s series palette already applied, so the'
            . ' chart matches the theme and follows <code>--hue-brand</code> like everything else.',
        'params' => [
            ['canvas', 'HTMLCanvasElement', 'The canvas to draw into.'],
            ['config', 'object', 'A Chart.js config. Datasets that already set'
                . ' <code>borderColor</code> or <code>backgroundColor</code> are left alone.'],
        ],
        'returns' => 'The <code>Chart</code> instance.',
        'absent' => '<strong>This method does not exist unless Chart.js is loaded.</strong> It is'
            . ' defined inside the adapter\'s Chart.js branch, so on a page without the library'
            . ' <code>Deck.chart</code> is <code>undefined</code> and calling it throws. Guard'
            . ' with <code>Deck.adapters.available.chart</code>, or use Deck\'s CSS charts, which'
            . ' need no JavaScript at all. The same applies to <code>Deck.chartTheme</code>,'
            . ' which exposes <code>{ apply, repaint, series }</code> and repaints every chart'
            . ' when the theme or the hue changes.',
        'example' => "if (Deck.adapters.available.chart) {\n"
            . "  Deck.chart(canvas, { type: 'line', data });\n"
            . "}",
        'source' => 'src/js/deck-adapters.js',
    ],
];

/* The attributes an author writes, and the ones Deck writes for itself. Read
   off src/js/*.js; the internal four are listed because they turn up in dev
   tools and look like something you were supposed to set. */
$ATTRS = [
    ['data-deck-icons', 'On the <code>&lt;script&gt;</code> tag', 'Overrides where the icon sprite is loaded from. Without it the sprite is resolved against the script\'s own URL, so it follows the bundle wherever it is installed.', 'deck.js'],
    ['data-deck-datepicker', 'A <code>.datefield</code>', 'Turns it into a date picker. Takes <code>data-mode</code>, <code>data-format</code>, <code>data-min</code>, <code>data-max</code>, <code>data-presets</code>, <code>data-locale</code>.', 'deck.js'],
    ['data-deck-combo', 'A <code>.combo</code>', 'Turns it into a combobox. Takes <code>data-multi</code> and <code>data-create</code>.', 'deck.js'],
    ['data-deck-grid', 'A <code>.dg-wrap</code>', 'Wires the data grid inside it: sorting, selection, column resizing.', 'deck.js'],
    ['data-deck-theme', 'A <code>&lt;button&gt;</code>', 'Makes it a light and dark toggle. No code, no handler, and the choice persists.', 'deck.js'],
    ['data-deck-tick', 'Any element with a number in it', 'Counts the number up when it scrolls into view.', 'deck.js'],
    ['data-deck-flip', 'A trigger', 'Flips a card on click. The value is a selector for the card; without one it flips the nearest.', 'deck.js'],
    ['data-deck-anchor', 'A popover trigger', 'Anchors the popover to the trigger. Set by Deck on popover triggers it wires, and read by the Floating UI adapter where CSS anchor positioning is missing.', 'deck.js, deck-adapters.js'],
    ['data-deck-btt', 'A <code>.back-to-top</code>', 'Marks the link as wired and is the hook the visibility rule keys off. Deck adds it during init, so you rarely write it yourself.', 'deck.js'],
    ['data-deck-carousel', 'A <code>.carousel</code>', 'Wires the slides, arrows and dots. Takes <code>data-autoplay</code> in milliseconds.', 'deck-extras.js'],
    ['data-deck-drawer', 'A trigger', 'Opens the drawer named by its value.', 'deck-extras.js'],
    ['data-deck-mega', 'A trigger', 'Opens the mega menu named by its value.', 'deck-extras.js'],
    ['data-deck-copy', 'A <code>.copy-btn</code>', 'Copies on click. The value is a selector for what to copy; with no value it takes the text of the <code>.copy-value</code> beside it.', 'deck-extras.js'],
    ['data-deck-qr', 'Any element', 'Replaces its content with a QR code. The value is what to encode, falling back to the element\'s own text. Takes <code>data-ecl</code> and <code>data-caption</code>.', 'deck-extras.js'],
    ['data-deck-sortable', 'A list', 'Makes its children drag-reorderable. The value is a group name, for dragging between lists.', 'deck-adapters.js'],
];

$INTERNAL_ATTRS = [
    ['data-deck-wired', 'The once-guard in deck.js. If it is on an element, that element has already been wired and init will skip it.'],
    ['data-deck-x', 'The same guard in deck-extras.js.'],
    ['data-deck-a', 'The same guard in deck-adapters.js.'],
    ['data-deck-anchored', 'Marks a popover trigger whose anchor name has already been assigned.'],
];
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Reference</li>
    <li aria-current="page">JavaScript API</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>JavaScript API</h1>
  <p class="lede">
    Deck is a stylesheet first. The JavaScript adds behaviour that CSS cannot express —
    a date picker, a toast queue, a data grid — and it is optional: a page with no
    scripts at all still gets every component that HTML and CSS can carry on their own.
    Most people will never call a method on this page, because
    <a href="#attributes">the attributes</a> do it for them.
  </p>
</header>

<section class="stack-4">
  <h2 id="loading">Loading it</h2>
  <p>
    Three files, in order, and only the first is required.
    <code>deck.js</code> defines the global and the core components.
    <code>deck-extras.js</code> adds the carousel, drawer, mega menu, copy button, QR
    encoder and the rest. <code>deck-adapters.js</code> is the bridge to optional
    third-party libraries and does nothing at all when none of them are present.
  </p>
  <pre class="dx-code"><code>&lt;script src="/assets/deck/deck.js" defer&gt;&lt;/script&gt;
&lt;script src="/assets/deck/deck-extras.js" defer&gt;&lt;/script&gt;
&lt;script src="/assets/deck/deck-adapters.js" defer&gt;&lt;/script&gt;

&lt;!-- or all three in one request --&gt;
&lt;script src="/assets/deck/deck.bundle.min.js" defer&gt;&lt;/script&gt;</code></pre>
  <p>
    Each of the optional two extends <code>Deck.init</code> rather than replacing it, so
    load order matters and the extras must come after the core. Loading extras without
    the core logs a warning and stops rather than throwing.
    <code>Deck</code> is exposed on <code>window</code>, on <code>globalThis</code>, and
    as a CommonJS export, so it is reachable whether it was loaded as a classic script, a
    module, or through a bundler.
  </p>
  <p class="dx-note">
    <code>deck-icons.svg</code> is expected to sit beside <code>deck.js</code> and is
    found automatically, because the URL is resolved against the script tag's own
    <code>src</code> at execution time. If your build puts them in different places, set
    <code>data-deck-icons</code> on the tag.
  </p>
</section>

<section class="stack-4">
  <h2 id="attributes">The data attributes</h2>
  <p>
    This is how most people will use the JavaScript: by not writing any. Add an
    attribute in the markup and <code>Deck.init()</code> wires it on load. There are
    <?= count($ATTRS) ?> of them.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Attributes that wire a component with no code</caption>
      <thead>
        <tr>
          <th scope="col">Attribute</th>
          <th scope="col">Put it on</th>
          <th scope="col">What it does</th>
          <th scope="col">From</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ATTRS as [$attr, $where, $what, $from]): ?>
          <tr>
            <th scope="row" data-label="Attribute"><code><?= e($attr) ?></code></th>
            <td data-label="Put it on"><?= $where ?></td>
            <td data-label="What it does"><?= $what ?></td>
            <td data-label="From"><code class="dx-dim"><?= e($from) ?></code></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php docs_example(
      '<button class="btn btn-icon btn-ghost" data-deck-theme aria-label="Switch theme">' . "\n" .
      '  <svg class="icon"><use href="../../assets/deck/deck-icons.svg#moon"></use></svg>' . "\n" .
      '</button>',
      'A working theme switch, with no JavaScript written by you'
  ); ?>

  <h3 id="internal-attributes">Attributes Deck sets on itself</h3>
  <p>
    These turn up in dev tools and look like something you were meant to write. They are
    not: they are the once-guards that make <code>Deck.init()</code> safe to call
    repeatedly over the same subtree.
  </p>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Internal marker attributes</caption>
      <thead>
        <tr><th scope="col">Attribute</th><th scope="col">Meaning</th></tr>
      </thead>
      <tbody>
        <?php foreach ($INTERNAL_ATTRS as [$attr, $what]): ?>
          <tr>
            <th scope="row" data-label="Attribute"><code><?= e($attr) ?></code></th>
            <td data-label="Meaning"><?= $what ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-6">
  <h2 id="core">Core methods</h2>
  <p>
    On <code>window.Deck</code>, from <code>deck.js</code>. Available on every page that
    loads Deck at all.
  </p>
  <?php foreach ($CORE as $m) { js_method($m); } ?>
</section>

<section class="stack-6">
  <h2 id="extras">Extras and adapters</h2>
  <p>
    Added to the same global by <code>deck-extras.js</code> and
    <code>deck-adapters.js</code>. If you are not loading those files, these are
    <code>undefined</code>.
  </p>
  <?php foreach ($EXTRAS as $m) { js_method($m); } ?>
</section>

<section class="stack-4">
  <h2 id="properties">Properties</h2>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Properties on the Deck global</caption>
      <thead>
        <tr><th scope="col">Property</th><th scope="col">Type</th><th scope="col">Notes</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Property"><code>Deck.iconSprite</code></th>
          <td data-label="Type"><code class="dx-dim">string</code></td>
          <td data-label="Notes">
            The URL every <code>&lt;use&gt;</code> Deck generates points at. Resolved from
            the script tag at load time. Assigning to it later works, but only affects
            icons rendered after the assignment — anything already on the page keeps the
            URL it was built with.
          </td>
        </tr>
        <tr>
          <th scope="row" data-label="Property"><code>Deck.version</code></th>
          <td data-label="Type"><code class="dx-dim">string</code></td>
          <td data-label="Notes">The version of the script, as a string.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Property"><code>Deck.toasts</code></th>
          <td data-label="Type"><code class="dx-dim">object</code></td>
          <td data-label="Notes">The toast queue. See <a href="#toasts">above</a>.</td>
        </tr>
        <tr>
          <th scope="row" data-label="Property"><code>Deck.chartTheme</code></th>
          <td data-label="Type"><code class="dx-dim">object | undefined</code></td>
          <td data-label="Notes">
            <code>{ apply, repaint, series }</code>. Only defined when Chart.js was on the
            page as the adapter loaded.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section class="stack-3">
  <h2 id="without">What still works with the scripts removed</h2>
  <p>
    Worth knowing before you reach for any of the above. The modal, drawer, sheet,
    popover and tooltip are the platform's own <code>&lt;dialog&gt;</code> and popover
    APIs; the accordion is <code>&lt;details&gt;</code>; tabs, the carousel's dots and
    arrows, the segmented control and the whole of forms are CSS. Sorting a data grid,
    picking a date on the custom calendar, encoding a QR code and firing a toast are the
    things that genuinely need script.
  </p>
  <p>
    So the honest test for a page is whether the scripts are doing work the platform
    cannot. If deleting <code>deck-extras.js</code> changes nothing on a page, do not
    ship it.
  </p>
</section>

<?php docs_footer(); ?>
