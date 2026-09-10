<?php
declare(strict_types=1);

$page = [
    'path' => 'components/avatar.php',
    'title' => 'Avatar',
    'level' => 'Beginner',
    'description' => 'Deck\'s .avatar: one --size property drives width, height and the initials\' font size, with five sizes, a square variant, an overlapping stack, and the alt-text rules that decide what a screen reader hears.',
    'documents' => [
        'avatar', 'avatar-lg', 'avatar-sm', 'avatar-square', 'avatar-stack',
        'avatar-xl', 'avatar-xs',
    ],

    'component' => 'avatar',
    'accounts' => [
        '07-components.css' => 'documented: the avatar, its five sizes, the square variant and the overlapping stack',
        '24-media.css'      => 'documented: chat bubbles shrink the avatar to 28px and hide it on repeated messages — the In a chat thread section',
        '99-print.css'      => 'documented: avatars keep their fill on paper — the Printing section',
    ],
];

require __DIR__ . '/../_layout.php';

$sizes = [
    ['avatar-xs', 'Extra small', '24px'],
    ['avatar-sm', 'Small', '32px'],
    ['avatar', 'Default', '40px'],
    ['avatar-lg', 'Large', '56px'],
    ['avatar-xl', 'Extra large', '80px'],
];

/* A neutral placeholder, so the page needs no image files and no network. */
$face = 'data:image/svg+xml;utf8,'
    . '%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22160%22 height=%22160%22%3E'
    . '%3Crect width=%22160%22 height=%22160%22 fill=%22%238fa8a4%22/%3E'
    . '%3Ccircle cx=%2280%22 cy=%2262%22 r=%2226%22 fill=%22%23dfe9e7%22/%3E'
    . '%3Ccircle cx=%2280%22 cy=%22150%22 r=%2246%22 fill=%22%23dfe9e7%22/%3E%3C/svg%3E';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Avatar</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Avatar</h1>
  <p class="lede">
    <code>.avatar</code> is a round box that holds either an image or a person's
    initials. One custom property, <code>--size</code>, drives its width, its height
    <em>and</em> the font size of the initials — <code>calc(var(--size) * .38)</code> —
    so a size variant is a single declaration and the initials never overflow.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use an avatar wherever a row, a comment or a message needs to be attributed to a
    person. It is a <code>grid</code> with <code>place-items: center</code>, so
    whatever goes inside is centred without any help, and
    <code>overflow: hidden</code> means an image is clipped to the circle rather than
    needing its own radius.
  </p>
  <p>
    <code>flex: 0 0 auto</code> is set so an avatar in a flex row never shrinks when the
    text beside it is long. That is the one declaration that stops a list of comments
    from squashing every face into an oval.
  </p>
  <?php
  docs_example(
      '<span class="avatar">SR</span>' . "\n" .
      '<span class="avatar"><img alt="" src="' . $face . '"></span>',
      'Initials and an image, same class',
      'cluster'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="sizes">Sizes</h2>
  <p>
    Five sizes, each setting <code>--size</code> and nothing else. Because the font size
    is computed from it, the initials scale with the circle rather than being set
    separately for each variant.
  </p>
  <?php
  docs_example(
      '<span class="avatar avatar-xs">SR</span>' . "\n" .
      '<span class="avatar avatar-sm">SR</span>' . "\n" .
      '<span class="avatar">SR</span>' . "\n" .
      '<span class="avatar avatar-lg">SR</span>' . "\n" .
      '<span class="avatar avatar-xl">SR</span>'
  );
  ?>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">Avatar sizes</caption>
      <thead>
        <tr><th scope="col">Class</th><th scope="col">--size</th><th scope="col">Initials</th></tr>
      </thead>
      <tbody>
        <?php foreach ($sizes as [$cls, $label, $px]): ?>
          <tr>
            <th scope="row" data-label="Class"><code><?= e('.' . $cls) ?></code></th>
            <td data-label="--size"><code class="dx-dim"><?= e($px) ?></code></td>
            <td data-label="Initials"><code class="dx-dim"><?= e(number_format((int) $px * 0.38, 1)) ?>px</code></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="text-muted">
    <code>--size</code> is a plain custom property, so a size Deck does not ship is
    <code>style="--size:120px"</code> or a class of your own. There is no need to add a
    variant to the framework for a one-off.
  </p>
</section>

<section class="stack-3">
  <h2 id="square">Square</h2>
  <p>
    <code>.avatar-square</code> swaps <code>--r-full</code> for
    <code>--r-sm</code>. Use it for anything that is not a person — an organisation, a
    project, a repository. Keeping people round and things square is a cheap signal that
    costs one class.
  </p>
  <?php
  docs_example(
      '<span class="avatar avatar-square">AC</span>' . "\n" .
      '<span class="avatar avatar-square avatar-lg"><img alt="" src="' . $face . '"></span>'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="stack">Overlapping stack</h2>
  <p>
    <code>.avatar-stack</code> is a flex row that pulls each avatar after the first back
    by <code>calc(var(--size, 40px) * -.32)</code> and gives every one a two-pixel ring
    in the page's surface colour, so the overlap reads as depth rather than as a
    collision. The offset is computed from <code>--size</code>, so it stays
    proportional at every size.
  </p>
  <?php
  docs_example(
      '<div class="avatar-stack">' . "\n" .
      '  <span class="avatar avatar-sm">SR</span>' . "\n" .
      '  <span class="avatar avatar-sm">AC</span>' . "\n" .
      '  <span class="avatar avatar-sm">MJ</span>' . "\n" .
      '  <span class="avatar avatar-sm">+4</span>' . "\n" .
      '</div>' . "\n" .
      '<div class="avatar-stack">' . "\n" .
      '  <span class="avatar avatar-lg">SR</span>' . "\n" .
      '  <span class="avatar avatar-lg">AC</span>' . "\n" .
      '  <span class="avatar avatar-lg">MJ</span>' . "\n" .
      '</div>',
      'The overlap is a fraction of --size, so it scales',
      'stack'
  );
  ?>
  <p class="dx-note text-muted">
    The ring is <code>box-shadow: 0 0 0 2px var(--surface)</code>. On a background that
    is not <code>--surface</code> — inside a <code>.card-brand</code>, say — the ring is
    the wrong colour. Set <code>--surface</code> on the stack, or accept it.
  </p>
</section>

<section class="stack-3">
  <h2 id="chat">In a chat thread</h2>
  <p>
    <code>src/24-media.css</code> overrides the avatar inside <code>.msg</code>: 28px,
    <code>align-self: flex-end</code> so it lines up with the last line of the bubble
    rather than the first, and <code>visibility: hidden</code> on every message after
    the first from the same sender. The space is kept so the bubbles stay aligned; only
    the face is dropped.
  </p>
  <?php
  docs_example(
      '<div class="chat">' . "\n" .
      '  <div class="msg">' . "\n" .
      '    <span class="avatar">AC</span>' . "\n" .
      '    <div class="bubble">Did the export finish?</div>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="msg">' . "\n" .
      '    <span class="avatar">AC</span>' . "\n" .
      '    <div class="bubble">I can rerun it if not.</div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'The second avatar is hidden, not removed — the bubbles stay aligned',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>Generated from <code>src/07-components.css</code> by <code>tools/docs/extract.mjs</code>.</p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <p>
    The avatar's fill is the brand tint, which is why a wall of initials reads as one
    palette rather than as noise. <code>--size</code> is local to the component and is
    the only thing a size variant touches.
  </p>
  <?php docs_token_table(['--brand-soft', '--brand-soft-text', '--r-full', '--r-sm', '--surface']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>An avatar beside a name needs empty alt text.</strong> If the person's
      name is already in the row, <code>alt=""</code> is correct — otherwise a screen
      reader reads the name twice. Every example on this page uses
      <code>alt=""</code> for that reason.
    </li>
    <li>
      <strong>An avatar on its own needs a real name.</strong> In an
      <code>.avatar-stack</code> with no names beside it, each image needs
      <code>alt="Ada Chen"</code>, or the stack is a row of unlabelled pictures.
    </li>
    <li>
      <strong>Initials are read as letters.</strong> <code>&lt;span
      class="avatar"&gt;SR&lt;/span&gt;</code> is announced "S R", which is rarely
      useful. Add <code>aria-hidden="true"</code> when the name is beside it, or
      <code>&lt;span class="sr-only"&gt;Santos Rivera&lt;/span&gt;</code> when it is
      not.
    </li>
    <li>
      <strong><code>user-select: none</code> is set.</strong> Initials cannot be
      selected or copied, which stops a double-click on a row from highlighting "SR".
      It also means the initials are not available to a reader who copies the row, so
      the name has to exist somewhere else in the markup.
    </li>
    <li>
      <strong>No focus style, because it is not interactive.</strong> An avatar that
      opens a profile needs to be a link or a button around the avatar, and the target
      needs to clear <?= e(api_token('--tap')['value'] ?? '44px') ?> —
      <code>.avatar-sm</code> at 32px does not on its own.
    </li>
    <li>
      <strong>The overlap hides part of each face.</strong> In an
      <code>.avatar-stack</code>, 32% of every avatar but the last is covered. That is
      fine for a decorative "who is on this" summary and wrong for a list a reader has
      to identify people from.
    </li>
    <li>
      <strong>Contrast.</strong> Initials are <code>--brand-soft-text</code> on
      <code>--brand-soft</code>, which clears 4.5:1 in both themes. An image avatar has
      no contrast requirement because it is a picture, not text.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    The stack's offset is <code>margin-inline-start</code>, so under
    <code>dir="rtl"</code> the avatars overlap from the other side and the first one in
    the markup is still the one on top. Nothing else about the component is
    directional — a circle has no start edge.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="avatar-stack">' . "\n" .
      '  <span class="avatar">ا</span>' . "\n" .
      '  <span class="avatar">ب</span>' . "\n" .
      '  <span class="avatar">ج</span>' . "\n" .
      '</div>',
      'The overlap follows the writing direction',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The avatar has no transition and no animation, so
    <code>prefers-reduced-motion</code> changes nothing about it. Nothing to report for
    this component.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    <code>src/99-print.css</code> lists <code>.avatar</code> among the elements that
    keep their fill on paper, with <code>print-color-adjust: exact</code>. Without that,
    an initials avatar would print as dark letters on white and lose the circle
    entirely.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <p>
    <code>--size</code> is the intended override and it needs no layer at all — set it
    inline or in your own class. Reach for <code>app.components</code> only to change
    the look.
  </p>
  <pre class="dx-code"><code><?= e('/* A size Deck does not ship — no layer needed */
<span class="avatar" style="--size:120px">SR</span>

@layer app.components {
  .avatar {
    background: var(--ink-100);
    color: var(--text);
    font-weight: 700;
  }
}') ?></code></pre>
  <p>
    Prefer setting <code>--size</code> over setting <code>inline-size</code> and
    <code>block-size</code>: the font size and the stack offset are both computed from
    it, so overriding the dimensions directly leaves the initials and the overlap at the
    old scale.
  </p>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a logo.</strong> An avatar clips to a circle and centres its
      contents; a wordmark comes out cropped. Use an <code>&lt;img&gt;</code> with
      <code>.ratio-1x1</code>, or <code>.icon-tile</code> for a square badge with a
      tinted background.
    </li>
    <li>
      <strong>Not for an icon.</strong> Use <code>.icon-tile</code>. It is built for a
      glyph on a tint and does not compute a font size from its own width.
    </li>
    <li>
      <strong>Not as a button on its own.</strong> <code>.avatar-sm</code> is 32px,
      below the <?= e(api_token('--tap')['value'] ?? '44px') ?> target. Wrap it in a
      <code>.btn.btn-icon</code>, which brings the target and the focus ring, rather
      than attaching a handler to the avatar.
    </li>
    <li>
      <strong>Not for a list of people you have to tell apart.</strong>
      <code>.avatar-stack</code> covers a third of each face. For a real roster use a
      <code>.list</code> with an avatar and a name in each row.
    </li>
    <li>
      <strong>Not for status.</strong> An avatar with a coloured ring to mean "online"
      is invisible to a screen reader and to anyone who cannot distinguish the colour.
      Use <code>.indicator</code>, which positions a dot and can carry
      <code>.sr-only</code> text.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
