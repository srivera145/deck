<?php
declare(strict_types=1);

$page = [
    'path' => 'components/chat.php',
    'title' => 'Chat',
    'level' => 'Intermediate',
    'description' => 'Deck\'s .chat is a message thread: bubbles that group with logical corner radii, an avatar that hides on repeated messages, and a day separator — with the semantics left to you.',
    'documents' => [
        'chat', 'chat-composer', 'chat-day',
        'msg', 'msg-out', 'bubble', 'bubble-attachment', 'bubble-meta', 'bubble-name',
        'bubble-system', 'bubble-typing',
    ],

    'component' => 'chat',
    'accounts' => [
        '24-media.css' => 'documented: the thread, the day separator and the composer — plus .msg and .bubble, which this page also covers',
    ],
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Components</li>
    <li aria-current="page">Chat</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Chat</h1>
  <p class="lede">
    <code>.chat</code> is a scrolling thread of <code>.msg</code> rows, each holding a
    <code>.bubble</code>. The details worth knowing are the ones that make a run of
    messages read as a run: corner radii that flatten between consecutive bubbles from the
    same sender, and an avatar that is hidden rather than removed so the alignment holds.
  </p>
</header>

<section class="stack-3">
  <h2 id="when">When to use it</h2>
  <p>
    Use it for a conversation between two or more participants where the order is
    chronological and authorship matters: support threads, comments on a record, a
    messaging panel. For a list of notes with no back-and-forth, a
    <a href="list.php"><code>.list</code></a> is simpler.
  </p>
  <?php
  docs_example(
      '<div class="chat" style="max-block-size:22rem">' . "\n" .
      '  <div class="chat-day">Today</div>' . "\n" .
      '  <div class="msg">' . "\n" .
      '    <span class="avatar">AC</span>' . "\n" .
      '    <div class="bubble">' . "\n" .
      '      <div class="bubble-name">Ada Chen</div>' . "\n" .
      '      Did the nightly export finish?' . "\n" .
      '      <div class="bubble-meta">09:12</div>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="msg">' . "\n" .
      '    <span class="avatar">AC</span>' . "\n" .
      '    <div class="bubble">I can rerun it if not.<div class="bubble-meta">09:12</div></div>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="msg msg-out">' . "\n" .
      '    <div class="bubble">Finished at 04:12. 2,481 rows.<div class="bubble-meta">09:14</div></div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Two from Ada, one from you — note the second avatar and the corner radii',
      'stack'
  );
  ?>
</section>

<section class="stack-6">
  <h2 id="grouping">How a run of messages groups</h2>

  <div class="stack-2">
    <h3 id="g-radii">Flattened corners</h3>
    <p>
      A second message from the same sender flattens the corner nearest the previous
      bubble, so the two read as one block rather than two separate statements:
    </p>
    <pre class="dx-code"><code><?= e('.msg:not(:last-of-type) + .msg .bubble { border-start-start-radius: var(--r-xs); }
.msg-out:not(:last-of-type) + .msg-out .bubble { border-start-end-radius: var(--r-xs); }') ?></code></pre>
    <p>
      Two rules, both logical corners — <code>start-start</code> for an incoming bubble,
      <code>start-end</code> for an outgoing one — so the flattening happens on the side
      facing the sender's own edge, in either writing direction.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="g-avatar">The avatar that is hidden, not removed</h3>
    <p>
      <code>src/24-media.css</code> shrinks the avatar inside a message to 28px, aligns it
      to <code>flex-end</code> so it sits beside the <em>last</em> line of the bubble, and
      then:
    </p>
    <pre class="dx-code"><code><?= e('.msg:not(:last-of-type) + .msg .avatar { visibility: hidden; }') ?></code></pre>
    <p>
      <code>visibility: hidden</code> rather than <code>display: none</code>. The space is
      kept, so every bubble in the run stays aligned with the first — remove the element
      and the second message would slide 28px toward the edge. It is a one-word difference
      with a very visible consequence.
    </p>
  </div>

  <div class="stack-2">
    <h3 id="g-direction">Incoming and outgoing</h3>
    <p>
      <code>.msg</code> is <code>align-self: flex-start</code>; <code>.msg-out</code>
      switches to <code>flex-end</code> and <code>row-reverse</code>, so the avatar moves
      to the other side of the bubble. Both are capped at
      <code>min(80%, 34rem)</code> so a long message does not run the full width of a wide
      thread.
    </p>
  </div>
</section>

<section class="stack-3">
  <h2 id="parts">Bubble parts</h2>
  <ul class="stack-2">
    <li><code>.bubble-name</code> — the sender, in the brand colour on incoming messages and inherited on outgoing ones.</li>
    <li><code>.bubble-meta</code> — the timestamp and read state, aligned to the sender's edge.</li>
    <li><code>.bubble-attachment</code> — a full-bleed image or file inside the bubble.</li>
    <li><code>.bubble-system</code> — a centred, unattributed notice: "Ada joined the conversation".</li>
    <li><code>.bubble-typing</code> — the three-dot indicator.</li>
    <li><code>.chat-day</code> — a centred date pill separating one day from the next.</li>
  </ul>
  <?php
  docs_example(
      '<div class="chat" style="max-block-size:20rem">' . "\n" .
      '  <div class="chat-day">Yesterday</div>' . "\n" .
      '  <div class="bubble bubble-system">Marco joined the conversation</div>' . "\n" .
      '  <div class="msg">' . "\n" .
      '    <span class="avatar">MS</span>' . "\n" .
      '    <div class="bubble">' . "\n" .
      '      <div class="bubble-name">Marco Silva</div>' . "\n" .
      '      Here is the failing run.' . "\n" .
      '      <figure class="bubble-attachment">' . "\n" .
      '        <img alt="A screenshot of the failed run" src="data:image/svg+xml;utf8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22480%22 height=%22200%22%3E%3Crect width=%22480%22 height=%22200%22 fill=%22%23c7d6d4%22/%3E%3C/svg%3E">' . "\n" .
      '      </figure>' . "\n" .
      '      <div class="bubble-meta">17:03</div>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="msg">' . "\n" .
      '    <span class="avatar">MS</span>' . "\n" .
      '    <div class="bubble bubble-typing"><span></span><span></span><span></span></div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'A system notice, an attachment and a typing indicator',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="composer">The composer</h2>
  <p>
    <code>.chat-composer</code> is the row at the bottom of a thread. Deck styles it and
    supplies nothing else — no send handling, no auto-grow beyond what
    <a href="textarea.php"><code>.textarea</code></a> already does, no draft persistence.
  </p>
  <?php
  docs_example(
      '<div class="chat-composer">' . "\n" .
      '  <label class="sr-only" for="dx-chat-in">Message</label>' . "\n" .
      '  <textarea class="textarea" id="dx-chat-in" placeholder="Write a message"></textarea>' . "\n" .
      '  <button class="btn btn-primary btn-icon" aria-label="Send">' . "\n" .
      '    <svg class="icon mirror-rtl" aria-hidden="true"><use href="/assets/deck/deck-icons.svg#send"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '</div>',
      'The textarea grows as you type — that is field-sizing, not the composer',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="classes">Classes</h2>
  <p>
    The <code>chat</code> component proper is three classes; <code>.msg</code> and
    <code>.bubble</code> are separate roots in the same file, documented here because a
    thread is all three together.
  </p>
  <?php docs_class_table($page['documents']); ?>
</section>

<section class="stack-3">
  <h2 id="tokens">Tokens</h2>
  <?php docs_token_table(['--surface', '--surface-2', '--brand', '--brand-600', '--text-on-brand', '--text-faint', '--text-xs', '--r-full', '--r-xs', '--space-1', '--space-2', '--space-3', '--space-4']); ?>
</section>

<section class="stack-3">
  <h2 id="accessibility">Accessibility</h2>
  <ul class="stack-2">
    <li>
      <strong>Deck supplies no semantics, and a thread needs some.</strong> The classes
      style <code>&lt;div&gt;</code>s. Use a <code>&lt;ul&gt;</code> of
      <code>&lt;li class="msg"&gt;</code>, or an <code>&lt;article&gt;</code> per message,
      so the thread has structure rather than being a wall of text.
    </li>
    <li>
      <strong>The hidden avatar is correctly hidden.</strong>
      <code>visibility: hidden</code> removes it from the accessibility tree as well as
      the screen, so a run of three messages is not announced as three identical avatars.
    </li>
    <li>
      <strong>But the sender is then lost.</strong> Only the first bubble in a run carries
      <code>.bubble-name</code>. A screen-reader user reading the second and third
      messages has no attribution. Add <code>.sr-only</code> sender text to every message,
      or accept that the grouping is visual only.
    </li>
    <li>
      <strong>Incoming and outgoing look different and sound identical.</strong> Position
      and colour carry the distinction, and neither reaches a screen reader. This is the
      most important thing to fix in a real thread: each message needs its sender named
      in text somewhere.
    </li>
    <li>
      <strong>New messages need announcing.</strong> A live thread should have
      <code>aria-live="polite"</code> on the container — but on the container only, and
      not on a thread that loads a hundred messages at once, or every one is read aloud.
    </li>
    <li>
      <strong>The typing indicator is decorative</strong> — three empty spans. It says
      nothing to a screen reader, which is arguably right, and means an
      <code>.sr-only</code> "Marco is typing" is needed if that information matters.
    </li>
    <li>
      <strong><code>.chat-day</code> is styling, not structure.</strong> Same as
      <a href="list.php"><code>.list-header</code></a>: it looks like a heading and is
      announced as ordinary text.
    </li>
    <li>
      <strong>The composer's send button is icon-only</strong> and needs
      <code>aria-label</code>. Also consider that Enter-to-send in a
      <code>&lt;textarea&gt;</code> takes away the ability to write a second line — offer
      both, and say which is which.
    </li>
  </ul>
</section>

<section class="stack-3">
  <h2 id="rtl">Right to left</h2>
  <p>
    <code>align-self: flex-start</code> and <code>flex-end</code> follow the writing
    direction, so incoming messages sit on the right and outgoing on the left in an Arabic
    thread — which is correct, and needs no rule. The flattened corners are logical
    (<code>border-start-start-radius</code>), so they flatten on the correct side too.
  </p>
  <p class="text-muted">
    The send icon is directional and needs <code>.mirror-rtl</code>, as in the composer
    example. Deck's icon set mirrors arrows, chevrons and <code>#send</code>, and leaves
    checkmarks and clocks alone.
  </p>
  <?php
  docs_example(
      '<div dir="rtl" class="chat" style="max-block-size:16rem">' . "\n" .
      '  <div class="msg">' . "\n" .
      '    <span class="avatar">ا</span>' . "\n" .
      '    <div class="bubble"><div class="bubble-name">آدا تشين</div>هل اكتمل التصدير؟<div class="bubble-meta">٠٩:١٢</div></div>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="msg msg-out">' . "\n" .
      '    <div class="bubble">نعم، في الساعة ٠٤:١٢.<div class="bubble-meta">٠٩:١٤</div></div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'Incoming on the right, outgoing on the left',
      'stack'
  );
  ?>
</section>

<section class="stack-3">
  <h2 id="motion">Reduced motion</h2>
  <p>
    The typing indicator's three dots animate. Under
    <code>prefers-reduced-motion: reduce</code> the global reset collapses animations to a
    single <code>.01ms</code> iteration, so the dots stop — and since the indicator is
    decorative and unannounced, a reader with reduced motion is left with three static
    dots that mean nothing.
  </p>
  <p class="dx-note text-muted">
    That is a real edge: the animation <em>is</em> the message. If a typing state matters,
    pair the dots with <code>.sr-only</code> text, which works regardless of motion
    preference.
  </p>
</section>

<section class="stack-3">
  <h2 id="print">Printing</h2>
  <p>
    A thread prints in full, and more care has gone into this than into most of Deck's
    print handling. A second <code>@layer deck.print</code> block at the end of <code>src/24-media.css</code> carries five rules for it:
  </p>
  <pre class="dx-code"><code><?= e('.chat { overflow: visible; max-block-size: none; }
.chat-composer { display: none !important; }
.msg { break-inside: avoid; }
.bubble { border: 1px solid #bbb !important; background: #fff !important; color: #000 !important; }
.msg-out .bubble { background: #f2f2f2 !important; }') ?></code></pre>
  <p>
    The first line is the important one: without it a printed thread would show only what
    was scrolled into view. The composer is dropped because you cannot type on paper, no
    message is split across a page boundary, and the bubbles are given explicit black-on-white
    borders — because backgrounds are dropped when printing, and a bubble with no background
    and no border is not a bubble at all.
  </p>
  <p class="text-muted">
    The one thing left to you is the direction of the conversation: outgoing messages print
    on a light grey rather than in the brand colour, so who said what is carried by the
    alignment and the tail rather than by hue. On a long transcript, a name beside each run
    is worth more than either.
  </p>
</section>

<section class="stack-3">
  <h2 id="overriding">Overriding it</h2>
  <pre class="dx-code"><code><?= e('@layer app.components {
  /* Wider bubbles on a big screen */
  .msg { max-inline-size: min(70%, 44rem); }

  /* A quieter outgoing bubble */
  .msg-out .bubble { background: var(--surface-2); color: var(--text); }

  /* Sender name on every message rather than the first of a run */
  .msg:not(:last-of-type) + .msg .bubble-name { display: block; }
}') ?></code></pre>
</section>

<section class="stack-3">
  <h2 id="when-not">When not to use it</h2>
  <ul class="stack-3">
    <li>
      <strong>Not for a comment thread with replies.</strong> Bubbles imply a linear
      conversation. Nested replies need indentation and a tree, which this is not — use a
      <a href="list.php"><code>.list</code></a> or cards.
    </li>
    <li>
      <strong>Not for a log.</strong> A stream of system events is a
      <a href="table.php">table</a> or a list with timestamps. Bubbles imply authorship,
      and a log has none.
    </li>
    <li>
      <strong>Not for two long messages.</strong> The bubble shape suits short turns. A
      thread of paragraphs is easier to read as plain blocks with a name above each.
    </li>
    <li>
      <strong>Not without naming senders in text.</strong> The visual left/right
      distinction is invisible to a screen reader, and the grouping removes the name from
      all but the first message of a run.
    </li>
    <li>
      <strong>Not as the archive.</strong> The panel scrolls and prints partially. If the
      conversation is a record, it needs a page of its own.
    </li>
  </ul>
</section>

<?php docs_footer(); ?>
