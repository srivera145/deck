<?php
declare(strict_types=1);

/**
 * Your first page — the second tutorial.
 *
 * One real artefact, built in order, with every line typed by the reader. The
 * finished file was written and run before this page existed, and every example
 * below is a slice of that file rather than a paraphrase of it.
 *
 * Deliberately not a tour. A tutorial that shows one of everything teaches the
 * catalogue; a tutorial that builds one thing teaches the shape of the work.
 */

$page = [
    'path' => 'start/first-page.php',
    'title' => 'Your first page',
    'level' => 'Beginner',
    'description' => "Build a complete account settings page with Deck in one sitting: a nav bar, a rail layout, real form fields, a confirm dialog and a sticky save bar.",
];

require __DIR__ . '/../_layout.php';
?>

<nav class="breadcrumb" aria-label="Breadcrumb">
  <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
    <li><a href="../index.php">Docs</a></li>
    <li>Start</li>
    <li aria-current="page">Your first page</li>
  </ol>
</nav>

<header class="stack-3">
  <h1>Your first page</h1>
  <p class="lede">
    You are going to build an account settings page: a nav bar with a working theme
    switch, a rail layout, a profile form, choice cards, switches, a confirm dialog and a
    save bar that sticks to the bottom of the screen. It is about 130 lines of HTML, you
    will type all of them, and at the end it is a real page rather than a demo with the
    interesting parts left out.
  </p>
</header>

<section class="stack-3">
  <h2 id="before">Before you start</h2>
  <p>
    You need <a href="install.php">Deck installed</a> and a folder you can serve. Make a
    file called <code>settings.html</code> beside your <code>assets</code> folder. Every
    step below adds to that one file, in order, and each one ends with something you can
    look at.
  </p>
  <p>
    Nothing here needs a server language, a bundler or a framework. If you are following
    along in PHP, Rails or anything else, the markup is the same — only the file
    extension changes.
  </p>
</section>

<section class="stack-4">
  <h2 id="step1">1. The shell</h2>
  <p>
    Start with the same head you finished the install with, and an empty
    <code>&lt;main&gt;</code>. Two classes are doing the work.
    <code>.container</code> centres the content and applies the page gutter, which is
    fluid — it tightens on a phone and opens up on a monitor without a media query
    deciding where the line falls. <code>.section</code> applies the vertical rhythm the
    same way.
  </p>
  <pre class="dx-code"><code>&lt;!doctype html&gt;
&lt;html lang="en"&gt;
&lt;head&gt;
&lt;meta charset="utf-8"&gt;
&lt;meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"&gt;
&lt;title&gt;Account settings — Ledgerly&lt;/title&gt;
&lt;link rel="stylesheet" href="assets/deck/deck.css"&gt;
&lt;script src="assets/deck/deck.js" defer&gt;&lt;/script&gt;
&lt;script src="assets/deck/deck-extras.js" defer&gt;&lt;/script&gt;
&lt;/head&gt;
&lt;body&gt;

&lt;a class="skip-link" href="#main"&gt;Skip to content&lt;/a&gt;

&lt;main id="main" class="container section"&gt;
  &lt;div class="stack-2 mb-8"&gt;
    &lt;h1&gt;Account settings&lt;/h1&gt;
    &lt;p class="lede"&gt;Changes are saved to your account and apply everywhere you are signed in.&lt;/p&gt;
  &lt;/div&gt;
&lt;/main&gt;

&lt;/body&gt;
&lt;/html&gt;</code></pre>
  <p>
    <code>.skip-link</code> is off-screen until it takes focus, and then it is the first
    thing a keyboard user reaches. It costs one line and it is the difference between a
    page someone can navigate and one they have to tab through forty times. Put it in
    first so you never have to remember to add it.
  </p>
  <p class="dx-note">
    <code>.stack-2</code> on the wrapper sets the gap between the heading and the lede.
    Notice there is no margin on either of them. That is the whole layout idea in Deck
    and it is worth reading <a href="../guides/layout.php">the layout guide</a> once you
    have finished here.
  </p>
</section>

<section class="stack-4">
  <h2 id="step2">2. A nav bar that already does something</h2>
  <p>
    Above the <code>&lt;main&gt;</code>, add a header. <code>.sticky-top</code> pins it
    with a translucent backdrop and the right stacking token, so you do not pick a
    <code>z-index</code>. <code>.push</code> on the button takes the leftover space,
    which is how it lands on the far end of the row.
  </p>
  <pre class="dx-code"><code>&lt;header class="sticky-top"&gt;
  &lt;div class="container"&gt;
    &lt;nav class="navbar" aria-label="Main"&gt;
      &lt;a class="navbar-brand" href="#"&gt;
        &lt;svg class="icon icon-lg" style="color:var(--brand)"&gt;
          &lt;use href="assets/deck/deck-icons.svg#grid"&gt;&lt;/use&gt;
        &lt;/svg&gt;
        Ledgerly
      &lt;/a&gt;
      &lt;button class="btn btn-icon btn-ghost push" data-deck-theme aria-label="Switch theme"&gt;
        &lt;svg class="icon"&gt;&lt;use href="assets/deck/deck-icons.svg#moon"&gt;&lt;/use&gt;&lt;/svg&gt;
      &lt;/button&gt;
    &lt;/nav&gt;
  &lt;/div&gt;
&lt;/header&gt;</code></pre>
  <?php docs_example(
      '<nav class="navbar" aria-label="Example">' . "\n" .
      '  <a class="navbar-brand" href="#step2">' . "\n" .
      '    <svg class="icon icon-lg" style="color:var(--brand)"><use href="../../assets/deck/deck-icons.svg#grid"></use></svg>' . "\n" .
      '    Ledgerly' . "\n" .
      '  </a>' . "\n" .
      '  <button class="btn btn-icon btn-ghost push" data-deck-theme aria-label="Switch theme">' . "\n" .
      '    <svg class="icon"><use href="../../assets/deck/deck-icons.svg#moon"></use></svg>' . "\n" .
      '  </button>' . "\n" .
      '</nav>',
      'Press the moon — it works here too, because it is the same attribute',
      'stack'
  ); ?>
  <p>
    Reload and press the moon. The page switches between light and dark and remembers
    your choice on the next load. You did not write a line of JavaScript:
    <code>data-deck-theme</code> is one of
    <a href="../reference/javascript.php#attributes">fifteen attributes</a> that wire a
    behaviour with no code.
  </p>
</section>

<section class="stack-4">
  <h2 id="step3">3. The rail layout</h2>
  <p>
    Settings pages are a narrow rail of section links beside a wide column of controls.
    Inside <code>&lt;main&gt;</code>, after the heading block, add this.
  </p>
  <pre class="dx-code"><code>  &lt;div class="split split-rail-start"&gt;
    &lt;aside&gt;
      &lt;nav class="panel sidebar" aria-label="Settings sections"&gt;
        &lt;span class="sidebar-group"&gt;Settings&lt;/span&gt;
        &lt;a class="sidebar-link" href="#profile" aria-current="page"&gt;&lt;span&gt;Profile&lt;/span&gt;&lt;/a&gt;
        &lt;a class="sidebar-link" href="#preferences"&gt;&lt;span&gt;Preferences&lt;/span&gt;&lt;/a&gt;
        &lt;a class="sidebar-link" href="#danger"&gt;&lt;span&gt;Danger zone&lt;/span&gt;&lt;/a&gt;
      &lt;/nav&gt;
    &lt;/aside&gt;

    &lt;form class="stack-8" id="settings"&gt;
      &lt;!-- the next three steps go here --&gt;
    &lt;/form&gt;
  &lt;/div&gt;</code></pre>
  <p>
    <code>.split</code> is a single column by default and becomes two at 48rem;
    <code>.split-rail-start</code> makes the first of those two a fixed rail rather than
    an equal half. So the phone layout is the one you get for free and the desktop
    layout is the variant — which is the right way round, because the phone is the harder
    case and the one people forget to check.
  </p>
  <p>
    The rail is <code>20rem</code> and you change it with <code>--rail</code>, not by
    editing Deck:
  </p>
  <pre class="dx-code"><code>&lt;div class="split split-rail-start" style="--rail: 14rem"&gt;</code></pre>
  <?php docs_example(
      '<nav class="panel sidebar" aria-label="Example sections" style="max-inline-size:18rem">' . "\n" .
      '  <span class="sidebar-group">Settings</span>' . "\n" .
      '  <a class="sidebar-link" href="#step3" aria-current="page"><span>Profile</span></a>' . "\n" .
      '  <a class="sidebar-link" href="#step4"><span>Preferences</span></a>' . "\n" .
      '  <a class="sidebar-link" href="#step5"><span>Danger zone</span></a>' . "\n" .
      '</nav>',
      'The rail. aria-current marks the section you are on',
      'stack'
  ); ?>
</section>

<section class="stack-4">
  <h2 id="step4">4. The profile fields</h2>
  <p>
    Inside the <code>&lt;form&gt;</code>. This is the part worth slowing down on, because
    it is the part every application has and the part most frameworks make you assemble
    yourself.
  </p>
  <pre class="dx-code"><code>      &lt;section class="stack-4" id="profile"&gt;
        &lt;h2&gt;Profile&lt;/h2&gt;

        &lt;div class="cluster"&gt;
          &lt;span class="avatar avatar-xl"&gt;AC&lt;/span&gt;
          &lt;div class="stack-2"&gt;
            &lt;button class="btn btn-sm" type="button"&gt;Upload a photo&lt;/button&gt;
            &lt;p class="help"&gt;JPG or PNG, up to 2 MB.&lt;/p&gt;
          &lt;/div&gt;
        &lt;/div&gt;

        &lt;div class="field-row"&gt;
          &lt;div class="field"&gt;
            &lt;label class="label" for="name"&gt;Full name&lt;/label&gt;
            &lt;input class="input" id="name" name="name" value="Ada Chen" required&gt;
          &lt;/div&gt;
          &lt;div class="field"&gt;
            &lt;label class="label" for="email"&gt;Email&lt;/label&gt;
            &lt;input class="input" id="email" name="email" type="email" value="ada@ledgerly.io" required&gt;
            &lt;p class="help"&gt;Invoices and receipts go here.&lt;/p&gt;
          &lt;/div&gt;
        &lt;/div&gt;

        &lt;div class="field"&gt;
          &lt;label class="label" for="bio"&gt;Bio &lt;span class="optional"&gt;optional&lt;/span&gt;&lt;/label&gt;
          &lt;textarea class="textarea" id="bio" name="bio" rows="3"&gt;Finance lead.&lt;/textarea&gt;
        &lt;/div&gt;
      &lt;/section&gt;</code></pre>
  <?php docs_example(
      '<div class="stack-4" style="max-inline-size:34rem">' . "\n" .
      '  <div class="cluster">' . "\n" .
      '    <span class="avatar avatar-xl">AC</span>' . "\n" .
      '    <div class="stack-2">' . "\n" .
      '      <button class="btn btn-sm" type="button">Upload a photo</button>' . "\n" .
      '      <p class="help">JPG or PNG, up to 2 MB.</p>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '  <div class="field-row">' . "\n" .
      '    <div class="field">' . "\n" .
      '      <label class="label" for="t-name">Full name</label>' . "\n" .
      '      <input class="input" id="t-name" value="Ada Chen">' . "\n" .
      '    </div>' . "\n" .
      '    <div class="field">' . "\n" .
      '      <label class="label" for="t-email">Email</label>' . "\n" .
      '      <input class="input" id="t-email" type="email" value="ada@ledgerly.io">' . "\n" .
      '      <p class="help">Invoices and receipts go here.</p>' . "\n" .
      '    </div>' . "\n" .
      '  </div>' . "\n" .
      '</div>',
      'field-row is one column on a phone and two side by side from 40rem',
      'stack'
  ); ?>
  <p>
    Three things you did not have to do. <code>.field</code> is a stack, so the gap
    between the label, the control and the help text comes from the container rather than
    from margins on each piece. <code>.field-row</code> is one column on a phone and two
    from 40rem, so the responsive behaviour is in the class rather than in a media query
    you write. And the <code>for</code> and <code>id</code> pairs are what make the label
    clickable and the control announced — Deck styles them, it cannot supply them.
  </p>
  <p class="dx-note">
    <code>required</code> on the input is the real HTML attribute, not a class. Deck
    styles <code>:user-invalid</code>, so the field turns red after someone has actually
    interacted with it and left it wrong — not while they are still typing. See the
    <a href="../guides/forms.php">forms guide</a> for why that distinction matters.
  </p>
</section>

<section class="stack-4">
  <h2 id="step5">5. Preferences: choice cards and switches</h2>
  <p>
    Still inside the form, after the profile section. An <code>&lt;hr&gt;</code> between
    sections is enough of a divider; you do not need a card around each one.
  </p>
  <pre class="dx-code"><code>      &lt;hr&gt;

      &lt;section class="stack-4" id="preferences"&gt;
        &lt;h2&gt;Preferences&lt;/h2&gt;

        &lt;div class="field"&gt;
          &lt;label class="label" for="tz"&gt;Time zone&lt;/label&gt;
          &lt;select class="select" id="tz" name="tz"&gt;
            &lt;option&gt;Europe/London&lt;/option&gt;
            &lt;option selected&gt;Europe/Berlin&lt;/option&gt;
            &lt;option&gt;America/New_York&lt;/option&gt;
          &lt;/select&gt;
        &lt;/div&gt;

        &lt;fieldset class="fieldset"&gt;
          &lt;legend class="label"&gt;Invoice reminders&lt;/legend&gt;
          &lt;div class="grid grid-tight"&gt;
            &lt;label class="check check-card"&gt;
              &lt;input type="radio" name="remind" value="off"&gt;
              &lt;span class="check-text"&gt;
                &lt;span&gt;Never&lt;/span&gt;
                &lt;span class="check-note"&gt;No reminders at all.&lt;/span&gt;
              &lt;/span&gt;
            &lt;/label&gt;
            &lt;label class="check check-card"&gt;
              &lt;input type="radio" name="remind" value="due" checked&gt;
              &lt;span class="check-text"&gt;
                &lt;span&gt;On the due date&lt;/span&gt;
                &lt;span class="check-note"&gt;One email, the morning it is due.&lt;/span&gt;
              &lt;/span&gt;
            &lt;/label&gt;
            &lt;label class="check check-card"&gt;
              &lt;input type="radio" name="remind" value="chase"&gt;
              &lt;span class="check-text"&gt;
                &lt;span&gt;Until it is paid&lt;/span&gt;
                &lt;span class="check-note"&gt;Every three days after the due date.&lt;/span&gt;
              &lt;/span&gt;
            &lt;/label&gt;
          &lt;/div&gt;
        &lt;/fieldset&gt;

        &lt;div class="stack-2"&gt;
          &lt;label class="switch"&gt;
            &lt;input type="checkbox" name="digest" checked&gt;
            &lt;span&gt;Weekly summary email&lt;/span&gt;
          &lt;/label&gt;
          &lt;label class="switch"&gt;
            &lt;input type="checkbox" name="twofa"&gt;
            &lt;span&gt;Require two-factor authentication&lt;/span&gt;
          &lt;/label&gt;
        &lt;/div&gt;
      &lt;/section&gt;</code></pre>
  <?php docs_example(
      '<div class="stack-4">' . "\n" .
      '  <div class="grid grid-tight">' . "\n" .
      '    <label class="check check-card">' . "\n" .
      '      <input type="radio" name="t-remind" value="off">' . "\n" .
      '      <span class="check-text"><span>Never</span><span class="check-note">No reminders at all.</span></span>' . "\n" .
      '    </label>' . "\n" .
      '    <label class="check check-card">' . "\n" .
      '      <input type="radio" name="t-remind" value="due" checked>' . "\n" .
      '      <span class="check-text"><span>On the due date</span><span class="check-note">One email, the morning it is due.</span></span>' . "\n" .
      '    </label>' . "\n" .
      '  </div>' . "\n" .
      '  <label class="switch"><input type="checkbox" checked><span>Weekly summary email</span></label>' . "\n" .
      '</div>',
      'Click anywhere on a card. The whole thing is the label',
      'stack'
  ); ?>
  <p>
    Both of these are one real form control wearing a costume. The choice card is a
    <code>&lt;label&gt;</code> around a radio, and the card lights up through
    <code>:has(input:checked)</code> — no state class, no JavaScript, and the value posts
    with the form because it is a radio. The switch is the same trick around a checkbox.
    Keyboard, screen readers, form serialisation and the browser's own validation all
    work because nothing was reimplemented.
  </p>
</section>

<section class="stack-4">
  <h2 id="step6">6. The danger zone and a confirm dialog</h2>
  <p>
    The last section inside the form, then the dialog itself just before
    <code>&lt;/body&gt;</code>.
  </p>
  <pre class="dx-code"><code>      &lt;hr&gt;

      &lt;section class="stack-4" id="danger"&gt;
        &lt;h2&gt;Danger zone&lt;/h2&gt;
        &lt;div class="alert alert-bad"&gt;
          &lt;svg class="icon"&gt;&lt;use href="assets/deck/deck-icons.svg#alert-triangle"&gt;&lt;/use&gt;&lt;/svg&gt;
          &lt;div&gt;
            &lt;div class="alert-title"&gt;Closing the account is permanent&lt;/div&gt;
            &lt;p class="alert-body"&gt;Seven years of invoices, every export and the audit
              log go with it. We cannot restore them afterwards.&lt;/p&gt;
          &lt;/div&gt;
        &lt;/div&gt;
        &lt;div&gt;
          &lt;button class="btn btn-outline btn-danger" type="button"
                  onclick="document.getElementById('close-account').showModal()"&gt;
            Close this account
          &lt;/button&gt;
        &lt;/div&gt;
      &lt;/section&gt;</code></pre>

  <pre class="dx-code"><code>&lt;dialog class="modal" id="close-account"&gt;
  &lt;form method="dialog" class="stack-0"&gt;
    &lt;div class="modal-header"&gt;
      &lt;h3 class="modal-title"&gt;Close this account?&lt;/h3&gt;
    &lt;/div&gt;
    &lt;div class="modal-body stack-3"&gt;
      &lt;p&gt;This deletes 2,481 invoices and cannot be undone.&lt;/p&gt;
      &lt;div class="field"&gt;
        &lt;label class="label" for="confirm"&gt;Type &lt;code&gt;ledgerly&lt;/code&gt; to confirm&lt;/label&gt;
        &lt;input class="input" id="confirm" autocomplete="off"&gt;
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="modal-footer"&gt;
      &lt;button class="btn" value="cancel"&gt;Keep my account&lt;/button&gt;
      &lt;button class="btn btn-danger" value="close"&gt;Close account&lt;/button&gt;
    &lt;/div&gt;
  &lt;/form&gt;
&lt;/dialog&gt;</code></pre>
  <p>
    That is a real <code>&lt;dialog&gt;</code>. The browser gives you the focus trap, the
    backdrop, dismissal on <kbd>Esc</kbd>, the top layer so no
    <code>z-index</code> can ever cover it, and <code>method="dialog"</code> closes it on
    submit and tells you which button was pressed. Deck styles it. None of that behaviour
    is Deck's, which is exactly why it is reliable.
  </p>
  <p class="dx-note">
    The confirm-by-typing field is not decoration either. A destructive action with a
    single <em>Are you sure?</em> gets clicked through; one that asks you to type the name
    makes you read what you are about to lose.
  </p>
</section>

<section class="stack-4">
  <h2 id="step7">7. The save bar</h2>
  <p>
    The last thing in the form, after the danger section.
  </p>
  <pre class="dx-code"><code>      &lt;div class="form-actions form-actions-sticky"&gt;
        &lt;button class="btn btn-primary" type="submit"&gt;Save changes&lt;/button&gt;
        &lt;button class="btn btn-ghost" type="reset"&gt;Discard&lt;/button&gt;
      &lt;/div&gt;</code></pre>
  <p>
    <code>.form-actions-sticky</code> pins the row to the bottom of the viewport with a
    blur behind it, and pads itself past the home indicator on a phone. On a settings page
    of any length that is the difference between a save button people find and one they
    scroll past.
  </p>
  <p>
    Now make it do something. Add an <code>onsubmit</code> to the form and a script before
    <code>&lt;/body&gt;</code>:
  </p>
  <pre class="dx-code"><code>&lt;form class="stack-8" id="settings" onsubmit="save(event)"&gt;</code></pre>
  <pre class="dx-code"><code>&lt;script&gt;
  function save(event) {
    event.preventDefault();
    const t = Deck.toast({ kind: 'loading', title: 'Saving…', duration: 0 });
    setTimeout(() =&gt; t.update({ kind: 'good', title: 'Settings saved', duration: 4000 }), 900);
  }
&lt;/script&gt;</code></pre>
  <p>
    <code>Deck.toast()</code> returns a handle, and <code>duration: 0</code> means it
    stays until something changes it. So one toast becomes the spinner and then the
    result, instead of a spinner toast plus a success toast stacking up. Swap the
    <code>setTimeout</code> for your real <code>fetch</code> and the shape is the same:
  </p>
  <pre class="dx-code"><code>async function save(event) {
  event.preventDefault();
  const t = Deck.toast({ kind: 'loading', title: 'Saving…', duration: 0 });
  try {
    const res = await fetch('/api/settings', { method: 'POST', body: new FormData(event.target) });
    if (!res.ok) throw new Error(await res.text());
    t.update({ kind: 'good', title: 'Settings saved', duration: 4000 });
  } catch (err) {
    t.update({ kind: 'bad', title: 'Could not save', text: err.message, duration: 0 });
  }
}</code></pre>
  <p>
    Note the failure branch uses <code>duration: 0</code>. An error that dismisses itself
    after five seconds is an error nobody read.
  </p>
</section>

<section class="stack-4">
  <h2 id="check">Check it</h2>
  <p>
    Reload and go through this list. Every one of these should already work, and if one
    does not it is worth finding out why now rather than later.
  </p>
  <ul class="stack-2">
    <li>Press <kbd>Tab</kbd> from the address bar. The first stop is <em>Skip to content</em>.</li>
    <li>Press the moon in the nav bar. The page switches theme and remembers it.</li>
    <li>Narrow the window to phone width. The rail moves above the form and the two-column field row becomes one.</li>
    <li>Click the middle of a choice card, not the radio. It selects.</li>
    <li>Clear the email field, type <code>notanemail</code>, then tab away. It turns red.</li>
    <li>Press <em>Close this account</em>. The dialog opens centred, <kbd>Esc</kbd> closes it.</li>
    <li>Press <em>Save changes</em>. One toast appears, spins, and turns green.</li>
  </ul>
  <p>
    Seven behaviours, and the only JavaScript you wrote was the six-line
    <code>save</code> function.
  </p>
</section>

<section class="stack-4">
  <h2 id="learned">What that taught you</h2>
  <div class="table-wrap">
    <table class="table table-stack">
      <caption class="sr-only">The ideas this page used and where they are explained</caption>
      <thead>
        <tr><th scope="col">Idea</th><th scope="col">Where it showed up</th><th scope="col">Read next</th></tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row" data-label="Idea">Containers own the spacing</th>
          <td data-label="Where it showed up">Every <code>.stack-*</code>, and not one margin</td>
          <td data-label="Read next"><a href="../guides/layout.php">Stop writing margins</a></td>
        </tr>
        <tr>
          <th scope="row" data-label="Idea">One real control, styled</th>
          <td data-label="Where it showed up">The switch, the choice cards, the dialog</td>
          <td data-label="Read next"><a href="../guides/forms.php">Forms that validate themselves</a></td>
        </tr>
        <tr>
          <th scope="row" data-label="Idea">Behaviour from attributes</th>
          <td data-label="Where it showed up"><code>data-deck-theme</code></td>
          <td data-label="Read next"><a href="../reference/javascript.php">JavaScript API</a></td>
        </tr>
        <tr>
          <th scope="row" data-label="Idea">Dark mode for free</th>
          <td data-label="Where it showed up">It already worked when you pressed the moon</td>
          <td data-label="Read next"><a href="../guides/dark-mode.php">Add a dark mode switch</a></td>
        </tr>
        <tr>
          <th scope="row" data-label="Idea">Mobile first, then the variant</th>
          <td data-label="Where it showed up"><code>.split</code>, <code>.field-row</code></td>
          <td data-label="Read next"><a href="../components/split.php">Split</a></td>
        </tr>
      </tbody>
    </table>
  </div>
  <p>
    The obvious next change is to make it yours. Open the page, put
    <code>style="--hue-brand: 265"</code> on the <code>&lt;html&gt;</code> tag, and reload:
    every fill, border, focus ring and shadow on the page moves to purple together. That
    is <a href="../guides/theming.php">the theming guide</a>, and it is the thing Deck is
    actually for.
  </p>
</section>

<?php docs_footer(); ?>
