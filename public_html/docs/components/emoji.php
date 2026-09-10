<?php
declare(strict_types=1);

$page = [
    'path' => 'components/emoji.php',
    'title' => 'Emoji',
    'level' => 'Beginner',
    'description' => 'Deck\'s .emoji names the colour emoji font stack explicitly and fixes the baseline, so an emoji sits with its text instead of floating. Plus tiles and a picker grid.',
    'documents' => [
        'emoji', 'emoji-grid', 'emoji-hero', 'emoji-lg', 'emoji-tile',
        'emoji-tile-round', 'emoji-xl',
    ],

    'component' => 'emoji',
    'accounts' => [
        '08-mobile.css' => 'documented: the font stack and baseline fix, the three sizes, the tiles and the picker grid',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Emoji</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Emoji</h1>
  <p class="lede">
    An emoji is a character, and browsers pick the font for it inconsistently — sometimes
    a colour font, sometimes a monochrome fallback, sometimes the body font's own glyph.
    <code>.emoji</code> names the colour stack explicitly and fixes the size and baseline,
    so the character sits <em>with</em> its text rather than above or below it.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Wrap an emoji in a <code>&lt;span class="emoji"&gt;</code> whenever it appears beside
    text you care about. Without it the character inherits the body font, which on some
    platforms produces a black-and-white outline and on others shifts the line height of
    the whole paragraph.
  </p>
  <?php
  docs_example(
      '<p>Deployed <span class="emoji">🚀</span> in 42 seconds</p>' . "\n" .
      '<p>Nightly export finished <span class="emoji">✅</span> with 2,481 rows</p>',
      'The character sits on the text baseline',
      'stack'
  );
  ?>
  <p class="text-muted">
    <code>font-style: normal</code> and <code>font-weight: 400</code> are set as well, so
    an emoji inside a <code>&lt;strong&gt;</code> or an <code>&lt;em&gt;</code> is not
    asked to synthesise a bold or italic it does not have.
  </p>
</section>

<section class="stack-3">
  <h2 id="sizes">Sizes</h2>
  <p>
    Three steps, each pairing a size with the <code>vertical-align</code> that keeps it on
    the baseline — the alignment is the part that has to change with the size, and getting
    it wrong is what makes a large emoji look like it is falling off the line.
  </p>
  <?php
  docs_example(
      '<span class="emoji">🎉</span>' . "\n" .
      '<span class="emoji emoji-lg">🎉</span>' . "\n" .
      '<span class="emoji emoji-xl">🎉</span>' . "\n" .
      '<span class="emoji emoji-hero">🎉</span>',
      'Default, lg, xl, hero',
      'cluster'
  );
  ?>
  <p class="text-muted">
    <code>.emoji-hero</code> is the odd one: it is sized in <code>rem</code> rather than
    <code>em</code>, because at that scale it is a graphic in its own right rather than
    something sitting beside text.
  </p>
</section>

<section class="stack-3">
  <h2 id="tiles">Tiles</h2>
  <p>
    <code>.emoji-tile</code> puts the character in a tinted rounded square — the emoji
    counterpart of <a href="icon.php#tiles"><code>.icon-tile</code></a>, for an empty
    state or a feature row. <code>.emoji-tile-round</code> makes it a circle.
  </p>
  <?php
  docs_example(
      '<span class="emoji-tile"><span class="emoji emoji-lg">📦</span></span>' . "\n" .
      '<span class="emoji-tile"><span class="emoji emoji-lg">🔔</span></span>' . "\n" .
      '<span class="emoji-tile emoji-tile-round"><span class="emoji emoji-lg">🎯</span></span>',
      '',
      'cluster'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="grid">Picker grid</h2>
  <p>
    <code>.emoji-grid</code> is an auto-fitting grid of buttons that scale slightly on
    hover — a reaction picker or a quick-select. Deck styles the grid and supplies no
    picker: the characters, the categories and the search are yours.
  </p>
  <?php
  docs_example(
      '<div class="emoji-grid" style="max-inline-size:20rem">' . "\n" .
      '  <button type="button" aria-label="Thumbs up"><span class="emoji emoji-lg">👍</span></button>' . "\n" .
      '  <button type="button" aria-label="Celebrate"><span class="emoji emoji-lg">🎉</span></button>' . "\n" .
      '  <button type="button" aria-label="Eyes"><span class="emoji emoji-lg">👀</span></button>' . "\n" .
      '  <button type="button" aria-label="Rocket"><span class="emoji emoji-lg">🚀</span></button>' . "\n" .
      '  <button type="button" aria-label="Heart"><span class="emoji emoji-lg">❤️</span></button>' . "\n" .
      '  <button type="button" aria-label="Thinking"><span class="emoji emoji-lg">🤔</span></button>' . "\n" .
      '</div>',
      'Every button needs a label — see Accessibility',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/08-mobile.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface-2', '--surface-hover', '--r-md', '--r-full', '--space-2']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>An emoji is read aloud, and the reading is often long.</strong>
      "🎉" is announced as "party popper". In a sentence that is usually fine; in a row of
      six it is noise.
    </li>
    <li>
      <strong>Decorative emoji should be hidden.</strong>
      <code>aria-hidden="true"</code> on the span when the character is ornament beside
      text that already says everything.
    </li>
    <li>
      <strong>An emoji carrying meaning needs words too.</strong> A ✅ standing alone in a
      table cell is announced as "check mark button", which is a description of a picture
      rather than a status. Put the status in text and hide the emoji.
    </li>
    <li>
      <strong>Picker buttons need real labels.</strong> A grid of unlabelled emoji
      buttons is announced as a list of glyph names with no indication of what pressing
      one does. Every button in the example above carries
      <code>aria-label</code>.
    </li>
    <li>
      <strong>Emoji rendering is not universal.</strong> The font stack lists five
      families and falls back to <code>sans-serif</code>; on a system with none of them a
      character may render as a box. Never let an emoji be the only carrier of meaning.
    </li>
    <li>
      <strong>Skin-tone and ZWJ sequences vary by platform.</strong> A family emoji may
      render as one glyph or as four separate ones depending on the font, which changes
      both the width and what is announced.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    Emoji are neutral characters in the bidirectional algorithm, so they take the
    direction of the surrounding text and need nothing from Deck. The one thing worth
    knowing is that a directional emoji — an arrow, a pointing hand — does
    <strong>not</strong> mirror, unlike Deck's
    <a href="icon.php#rtl">directional icons</a>. If direction matters, use an icon.
  </p>
  <?php
  docs_example(
      '<p dir="rtl">تم النشر <span class="emoji">🚀</span> خلال ٤٢ ثانية</p>',
      '',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    Only the picker grid moves — its buttons scale to 1.16 on hover — and the global reset
    collapses that transition to <code>.01ms</code> under
    <code>prefers-reduced-motion: reduce</code>. Nothing else animates.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    Emoji print as characters, in colour, on a colour printer. There is no print rule and
    none is needed — though a colour emoji printed in greyscale can become an
    indistinguishable dark blob, which is the same argument for not letting one carry
    meaning alone.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Prefer a bundled emoji font you ship yourself */
  .emoji { font-family: "Noto Color Emoji", sans-serif; }

  /* A tighter picker */
  .emoji-grid { grid-template-columns: repeat(auto-fit, minmax(2rem, 1fr)); }
}') ?></code></pre>
  <p class="text-muted">
    Shipping your own emoji font is the only way to make rendering identical everywhere.
    It is also several megabytes, which is why Deck names a stack instead.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not instead of an icon.</strong> An <a href="icon.php">icon</a> takes
      <code>currentColor</code>, mirrors under RTL, prints as line art and looks the same
      on every platform. An emoji does none of those.
    </li>
    <li>
      <strong>Not for status.</strong> ✅ and ❌ are pictures whose announced names
      describe the picture, not the state.
    </li>
    <li>
      <strong>Not in a heading you will translate.</strong> Emoji meaning is cultural, and
      a gesture that is friendly in one locale is not in another.
    </li>
    <li>
      <strong>Not as a bullet.</strong> A list marked with emoji is announced with the
      glyph name before every item.
    </li>
    <li>
      <strong>Not in a value you store and compare.</strong> The same emoji has multiple
      byte sequences depending on variation selectors, so equality checks surprise people.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
