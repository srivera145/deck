<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Deck — the CSS framework for Keel</title>

<!-- Brand marks. These are the static ones: an <img> or a <link> gets no colour
     context, so currentColor would resolve to black. Everything on the page
     that should follow --hue-brand uses <use> against the sprite instead.
     Published from src/brand/ by npm run demo. -->
<link rel="icon" href="assets/images/deck-mark.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="assets/images/deck-apple-touch-icon.png">

<meta name="description" content="Deck is one stylesheet: a full component set, an icon system, and a palette that recolors from a single number. No build step, no config file, no dependencies.">
<meta property="og:type" content="website">
<meta property="og:title" content="Deck — the CSS framework for Keel">
<meta property="og:description" content="One stylesheet. Retheme the entire app from one number.">
<meta property="og:image" content="assets/images/deck-og.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="The Deck logo, white on the brand teal.">
<meta name="twitter:card" content="summary_large_image">

<link rel="stylesheet" href="assets/deck/deck.css">
<script src="assets/deck/deck.js" defer></script>
<script src="assets/deck/deck-extras.js" defer></script>
<script src="assets/deck/deck-adapters.js" defer></script>
<style>
  /* Page-specific styles only. Everything else comes from Deck. */
  .hero { padding-block: var(--space-12) var(--space-10); }
  .hero .display { max-inline-size: 15ch; }
  .swatch { block-size: 44px; border-radius: var(--r-sm); border: 1px solid var(--line); }
  .theme-dock {
    position: sticky;
    inset-block-start: 0;
    z-index: var(--z-sticky);
    background: color-mix(in oklab, var(--bg) 84%, transparent);
    backdrop-filter: saturate(1.6) blur(14px);
    -webkit-backdrop-filter: saturate(1.6) blur(14px);
    border-block-end: 1px solid var(--line);
  }
  .ramp { display: grid; grid-template-columns: repeat(11, 1fr); gap: 2px; }
  .ramp > div { block-size: 40px; border-radius: 3px; }
  .demo-label { font-size: var(--text-xs); font-weight: 620; color: var(--text-faint); }
</style>
</head>
<body>

<span id="top" tabindex="-1"></span>
<a class="skip-link" href="#main">Skip to content</a>
<div class="banner" data-dismiss-key="demo">
  <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#sparkle-sm"></use></svg>
  <span>Deck v0.1 — one stylesheet, no build step, no dependencies.</span>
  <button class="banner-close" aria-label="Dismiss"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#x-sm"></use></svg></button>
</div>
<div class="scroll-progress" aria-hidden="true"></div>

<!-- ===================== Theme dock ===================== -->
<div class="theme-dock">
  <div class="container">
    <div class="navbar" style="border:0">
      <a class="navbar-brand" href="#">
        <svg class="icon icon-lg icon-fill" style="color:var(--brand)"><use href="assets/deck/deck-icons.svg#deck-mark"></use></svg>
        Deck
      </a>
      <nav class="navbar-links">
        <a class="nav-link" aria-current="page" href="#main">Components</a>
        <a class="nav-link" href="#forms">Forms</a>
        <a class="nav-link" href="#grid">Grid</a>
        <a class="nav-link" href="#charts">Charts</a>
        <a class="nav-link" href="#motion">Motion</a>
        <a class="nav-link" href="#gradients">Gradients</a>
        <a class="nav-link" href="#space">3D</a>
        <a class="nav-link" href="#more">More</a>
        <a class="nav-link" href="#gallery">Gallery</a>
        <a class="nav-link" href="#sidebar">Sidebar</a>
        <a class="nav-link" href="#tooltips">Tooltips</a>
        <a class="nav-link" href="#libs">Libraries</a>
      </nav>
      <div class="push cluster cluster-tight">
        <label class="sr-only" for="hue">Brand hue</label>
        <input id="hue" class="range" type="range" min="0" max="360" value="196"
               style="inline-size:104px" oninput="document.documentElement.style.setProperty('--hue-brand', this.value)">
        <button class="btn btn-icon btn-ghost" id="themeBtn" aria-label="Switch theme">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#moon"></use></svg>
        </button>
      </div>
    </div>
  </div>
</div>

<main id="main">

  <!-- ===================== Hero ===================== -->
  <section class="container hero stack-6">
    <div class="cluster cluster-tight">
      <span class="badge badge-brand badge-dot">v0.1</span>
      <span class="badge">No build step</span>
      <span class="badge">One file</span>
    </div>
    <h1 class="display mb-3">Drag the slider. Watch the whole page change its mind.</h1>
    <p class="lede mb-2">
      Deck is one stylesheet. Drop it in a Keel view and you have a full component
      set, an icon system, and a palette that recolors from a single number — no
      config file, no purge step, no rebuild to change a brand color.
    </p>
    <div class="cluster">
      <a class="btn btn-primary btn-lg" href="#main">
        Get started
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#arrow-right-sm"></use></svg>
      </a>
      <button class="btn btn-lg" popovertarget="installMenu">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#copy-sm"></use></svg>
        Copy the link tag
      </button>
      <div class="menu" id="installMenu" popover style="position:fixed;inset-block-start:auto">
        <div class="menu-label">Add to a Keel layout</div>
        <code style="display:block;padding:var(--space-3);white-space:pre-wrap;font-size:var(--text-xs)">&lt;link rel="stylesheet" href="/assets/deck/deck.css"&gt;</code>
      </div>
    </div>

    <div class="ramp mt-4" aria-hidden="true">
      <div style="background:var(--brand-50)"></div>
      <div style="background:var(--brand-100)"></div>
      <div style="background:var(--brand-200)"></div>
      <div style="background:var(--brand-300)"></div>
      <div style="background:var(--brand-400)"></div>
      <div style="background:var(--brand-500)"></div>
      <div style="background:var(--brand-600)"></div>
      <div style="background:var(--brand-700)"></div>
      <div style="background:var(--brand-800)"></div>
      <div style="background:var(--brand-900)"></div>
      <div style="background:var(--brand-950)"></div>
    </div>
  </section>

  <hr>

  <!-- ===================== Buttons ===================== -->
  <section class="container section stack-6">
    <div class="stack-4">
      <h2>Buttons</h2>
      <p class="text-muted mb-1">Seven variants, three sizes, groups, and a loading state.</p>
    </div>

    <div class="cluster mb-2">
      <button class="btn btn-primary">Save changes</button>
      <button class="btn">Cancel</button>
      <button class="btn btn-soft">Duplicate</button>
      <button class="btn btn-outline">Preview</button>
      <button class="btn btn-ghost">Skip</button>
      <button class="btn btn-accent">Upgrade</button>
      <button class="btn btn-danger">Delete claim</button>
      <button class="btn" disabled>Unavailable</button>
      <button class="btn btn-primary is-loading">Submitting</button>
    </div>

    <div class="cluster">
      <button class="btn btn-sm">Small</button>
      <button class="btn">Default</button>
      <button class="btn btn-lg">Large</button>
      <button class="btn btn-icon" aria-label="Settings">
        <svg class="icon"><use href="assets/deck/deck-icons.svg#settings"></use></svg>
      </button>
      <button class="btn btn-icon btn-round btn-primary" aria-label="Add">
        <svg class="icon"><use href="assets/deck/deck-icons.svg#plus"></use></svg>
      </button>
      <div class="btn-group">
        <button class="btn btn-sm">Day</button>
        <button class="btn btn-sm">Week</button>
        <button class="btn btn-sm">Month</button>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Cards + stats ===================== -->
  <section class="container section stack-6">
    <h2 class="mb-2">Cards and stats</h2>

    <div class="grid mb-3">
      <article class="card">
        <div class="card-body">
          <div class="stat">
            <span class="stat-label">Claims submitted</span>
            <span class="stat-value">1,284</span>
            <span class="stat-delta stat-delta-up">
              <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#trend-up-sm"></use></svg>
              12.4% vs last month
            </span>
          </div>
        </div>
      </article>

      <article class="card">
        <div class="card-body">
          <div class="stat">
            <span class="stat-label">Average approval time</span>
            <span class="stat-value">3.2 days</span>
            <span class="stat-delta stat-delta-down">
              <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#trend-down-sm"></use></svg>
              0.6 days faster
            </span>
          </div>
        </div>
      </article>

      <article class="card card-link">
        <div class="card-body">
          <div class="cluster cluster-tight">
            <span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#wrench"></use></svg></span>
            <div class="grow">
              <h3 class="card-title"><a class="link-quiet stretch" href="#main">Open repair orders</a></h3>
              <p class="text-sm text-muted">17 waiting on parts</p>
            </div>
            <svg class="icon icon-muted"><use href="assets/deck/deck-icons.svg#chevron-right"></use></svg>
          </div>
        </div>
      </article>
    </div>

    <div class="grid grid-wide">
      <article class="card">
        <header class="card-header">
          <span class="avatar">RM</span>
          <div class="grow">
            <div class="fw-semi">Rissa Molina</div>
            <div class="text-sm text-muted">Warranty administrator</div>
          </div>
          <button class="btn btn-icon btn-ghost btn-sm" aria-label="More">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#more-horizontal"></use></svg>
          </button>
        </header>
        <div class="card-body">
          <p class="text-muted">Submitted eleven claims this week. Two came back needing
            additional cause and correction detail.</p>
          <div class="cluster cluster-tight">
            <span class="badge badge-good">9 approved</span>
            <span class="badge badge-warn">2 returned</span>
          </div>
        </div>
        <footer class="card-footer">
          <button class="btn btn-sm">Message</button>
          <button class="btn btn-sm btn-primary">Review queue</button>
        </footer>
      </article>

      <div class="stack-4">
        <div class="alert alert-info mb-2">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#info"></use></svg>
          <div>
            <div class="alert-title">Policy update</div>
            <p class="alert-body">Labor time allowances changed for 2024 model year Broncos.</p>
          </div>
        </div>
        <div class="alert alert-good mb-2">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#check-circle"></use></svg>
          <div>
            <div class="alert-title">Claim 88214 approved</div>
            <p class="alert-body">Paid at $842.16 on the next statement.</p>
          </div>
        </div>
        <div class="alert alert-warn mb-2">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#alert-triangle"></use></svg>
          <div>
            <div class="alert-title">Missing documentation</div>
            <p class="alert-body">Three claims need a technician story before Friday.</p>
          </div>
        </div>
        <div class="alert alert-bad mb-2">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#x-circle"></use></svg>
          <div>
            <div class="alert-title">Claim 88109 denied</div>
            <p class="alert-body">Outside the coverage window by 412 miles.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Forms ===================== -->
  <section class="container section stack-6" id="forms">
    <div class="stack-2">
      <h2>Forms</h2>
      <p class="text-muted">Inputs are 16px on touch devices, so iOS never zooms when a
        field takes focus. Every control clears a 44px target.</p>
    </div>

    <div class="split">
      <form class="stack-5">
        <div class="field-row">
          <div class="field">
            <label class="label" for="vin">VIN <span class="required">*</span></label>
            <input class="input" id="vin" placeholder="1FMCU9J91LUA12345" required>
            <span class="help">17 characters, no spaces.</span>
          </div>
          <div class="field">
            <label class="label" for="ro">RO number <span class="optional">optional</span></label>
            <input class="input" id="ro" placeholder="482910">
          </div>
        </div>

        <div class="field">
          <label class="label" for="email">Email</label>
          <input class="input" id="email" type="email" value="not-an-email" required>
          <span class="error">
            <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#alert-circle-sm"></use></svg>
            Enter an address in the form name@shop.com
          </span>
        </div>

        <div class="field">
          <label class="label" for="type">Claim type</label>
          <select class="select" id="type">
            <option>Base warranty</option>
            <option>Extended service plan</option>
            <option>Field service action</option>
            <option>Goodwill</option>
          </select>
        </div>

        <div class="field">
          <label class="label" for="labor">Labor</label>
          <div class="input-group">
            <span class="addon">$</span>
            <input class="input" id="labor" inputmode="decimal" placeholder="0.00">
            <span class="addon">per hour</span>
          </div>
        </div>

        <div class="field">
          <label class="label" for="story">Technician story</label>
          <textarea class="textarea" id="story" placeholder="Cause, correction, and what was verified."></textarea>
          <span class="help">This box grows as you type.</span>
        </div>

        <div class="field">
          <span class="label">Notify</span>
          <label class="check">
            <input type="checkbox" checked>
            <span class="check-text">
              <span>Email me on approval</span>
              <span class="check-note">Sent within a minute of the status change.</span>
            </span>
          </label>
          <label class="check">
            <input type="checkbox">
            <span class="check-text"><span>Text me on denial</span></span>
          </label>
          <label class="check">
            <input type="checkbox" id="indet">
            <span class="check-text"><span>Weekly summary</span></span>
          </label>
        </div>

        <label class="switch">
          <input type="checkbox" checked>
          <span>Auto-submit claims that pass validation</span>
        </label>

        <div class="stack-3">
          <span class="label">Priority</span>
          <div class="grid grid-tight">
            <label class="check check-card">
              <input type="radio" name="pri" checked>
              <span class="check-text">
                <span class="fw-semi">Standard</span>
                <span class="check-note">Reviewed in order received</span>
              </span>
            </label>
            <label class="check check-card">
              <input type="radio" name="pri">
              <span class="check-text">
                <span class="fw-semi">Expedite</span>
                <span class="check-note">Customer is waiting</span>
              </span>
            </label>
          </div>
        </div>

        <label class="file">
          <input type="file">
          <svg class="icon icon-xl icon-muted"><use href="assets/deck/deck-icons.svg#upload"></use></svg>
          <span class="fw-semi text-inherit">Add photos or the repair order</span>
          <span class="text-sm">PDF, JPG, or PNG up to 20 MB</span>
        </label>

        <div class="form-actions">
          <button class="btn btn-primary" type="button">Submit claim</button>
          <button class="btn" type="button">Save draft</button>
        </div>
      </form>

      <aside class="stack-4">
        <div class="search">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#search"></use></svg>
          <input class="input" type="search" placeholder="Search claims">
        </div>

        <div class="segmented" role="tablist">
          <button role="tab" aria-selected="true">Open</button>
          <button role="tab" aria-selected="false">Paid</button>
          <button role="tab" aria-selected="false">Denied</button>
        </div>

        <div class="list">
          <div class="list-header">Today</div>
          <a class="list-row" href="#forms">
            <span class="icon-tile icon-tile-good"><svg class="icon"><use href="assets/deck/deck-icons.svg#check"></use></svg></span>
            <span class="list-main">
              <span class="list-title">Claim 88214</span>
              <span class="list-sub truncate">2021 F-150 · transmission cooler line</span>
            </span>
            <span class="list-trail nums">$842.16</span>
          </a>
          <a class="list-row" href="#forms">
            <span class="icon-tile icon-tile-warn"><svg class="icon"><use href="assets/deck/deck-icons.svg#clock"></use></svg></span>
            <span class="list-main">
              <span class="list-title">Claim 88220</span>
              <span class="list-sub truncate">2023 Explorer · waiting on parts</span>
            </span>
            <span class="list-trail nums">$318.00</span>
          </a>
          <a class="list-row" href="#forms">
            <span class="icon-tile icon-tile-bad"><svg class="icon"><use href="assets/deck/deck-icons.svg#x"></use></svg></span>
            <span class="list-main">
              <span class="list-title">Claim 88109</span>
              <span class="list-sub truncate">2020 Escape · out of coverage</span>
            </span>
            <span class="list-trail nums">$0.00</span>
          </a>
        </div>

        <div class="stack-2">
          <span class="demo-label">Progress</span>
          <progress class="progress" value="68" max="100"></progress>
          <div class="cluster">
            <div class="ring-wrap">
              <div class="ring" style="--value:68"></div>
              <span class="ring-label">68%</span>
            </div>
            <div class="spinner"></div>
            <span class="text-sm text-muted">Syncing with OASIS</span>
          </div>
        </div>

        <div class="stack-2">
          <span class="demo-label">Loading</span>
          <div class="card"><div class="card-body">
            <div class="cluster cluster-tight">
              <div class="skeleton skeleton-circle" style="inline-size:40px;block-size:40px"></div>
              <div class="grow stack-1">
                <div class="skeleton skeleton-text" style="inline-size:60%"></div>
                <div class="skeleton skeleton-text" style="inline-size:40%"></div>
              </div>
            </div>
          </div></div>
        </div>
      </aside>
    </div>
  </section>

  <hr>

  <!-- ===================== Table ===================== -->
  <section class="container section stack-6">
    <div class="bar">
      <h2>Tables</h2>
      <div class="push cluster cluster-tight">
        <button class="btn btn-sm">
          <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#filter-sm"></use></svg>
          Filter
        </button>
        <button class="btn btn-sm">
          <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#download-sm"></use></svg>
          Export
        </button>
      </div>
    </div>
    <p class="text-muted">Below 640px this table restacks into labelled rows instead of
      forcing a sideways scroll. Resize the window to see it.</p>

    <div class="table-wrap">
      <table class="table table-stack">
        <thead>
          <tr><th>Claim</th><th>Vehicle</th><th>Status</th><th class="num">Amount</th><th class="num">Age</th></tr>
        </thead>
        <tbody>
          <tr>
            <td data-label="Claim"><a href="#main">88214</a></td>
            <td data-label="Vehicle">2021 F-150</td>
            <td data-label="Status"><span class="badge badge-good">Approved</span></td>
            <td data-label="Amount" class="num nums">$842.16</td>
            <td data-label="Age" class="num nums">2d</td>
          </tr>
          <tr>
            <td data-label="Claim"><a href="#main">88220</a></td>
            <td data-label="Vehicle">2023 Explorer</td>
            <td data-label="Status"><span class="badge badge-warn">Pending</span></td>
            <td data-label="Amount" class="num nums">$318.00</td>
            <td data-label="Age" class="num nums">5d</td>
          </tr>
          <tr>
            <td data-label="Claim"><a href="#main">88109</a></td>
            <td data-label="Vehicle">2020 Escape</td>
            <td data-label="Status"><span class="badge badge-bad">Denied</span></td>
            <td data-label="Amount" class="num nums">$0.00</td>
            <td data-label="Age" class="num nums">11d</td>
          </tr>
        </tbody>
      </table>
    </div>

    <nav class="pagination" aria-label="Pages">
      <a href="#main" aria-label="Previous"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#chevron-left-sm"></use></svg></a>
      <a href="#main" aria-current="page">1</a>
      <a href="#main">2</a>
      <a href="#main">3</a>
      <span>…</span>
      <a href="#main">18</a>
      <a href="#main" aria-label="Next"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#chevron-right-sm"></use></svg></a>
    </nav>
  </section>

  <hr>

  <!-- ===================== Navigation + disclosure ===================== -->
  <section class="container section stack-6">
    <h2>Navigation and disclosure</h2>

    <nav class="breadcrumb" aria-label="Breadcrumb">
      <ol class="cluster cluster-tight" style="display:flex;list-style:none;padding:0;margin:0">
        <li><a href="#main">Dashboard</a></li>
        <li><a href="#main">Claims</a></li>
        <li aria-current="page">88214</li>
      </ol>
    </nav>

    <div class="tabs" role="tablist">
      <button class="tab" role="tab" aria-selected="true">Summary</button>
      <button class="tab" role="tab" aria-selected="false">Parts</button>
      <button class="tab" role="tab" aria-selected="false">Labor</button>
      <button class="tab" role="tab" aria-selected="false">Attachments</button>
      <button class="tab" role="tab" aria-selected="false">History</button>
    </div>

    <div class="split">
      <div class="accordion">
        <details open>
          <summary>
            <svg class="icon icon-muted"><use href="assets/deck/deck-icons.svg#help"></use></svg>
            Why was this claim returned?
          </summary>
          <div class="accordion-body">The technician story did not state what was verified
            before the part was replaced. Add the diagnostic steps and resubmit.</div>
        </details>
        <details>
          <summary>
            <svg class="icon icon-muted"><use href="assets/deck/deck-icons.svg#help"></use></svg>
            How long does review take?
          </summary>
          <div class="accordion-body">Most claims clear in two to four business days.
            Expedited claims are reviewed the same day when submitted before noon.</div>
        </details>
        <details>
          <summary>
            <svg class="icon icon-muted"><use href="assets/deck/deck-icons.svg#help"></use></svg>
            Can I edit a submitted claim?
          </summary>
          <div class="accordion-body">Until it enters review. After that, withdraw it and
            submit a corrected version.</div>
        </details>
      </div>

      <div class="timeline">
        <div class="timeline-item is-done">
          <span class="timeline-dot"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-sm"></use></svg></span>
          <div><div class="fw-semi">Submitted</div><div class="text-sm text-muted">Mar 3, 8:14 AM</div></div>
        </div>
        <div class="timeline-item is-done">
          <span class="timeline-dot"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-sm"></use></svg></span>
          <div><div class="fw-semi">In review</div><div class="text-sm text-muted">Mar 3, 2:40 PM</div></div>
        </div>
        <div class="timeline-item">
          <span class="timeline-dot"></span>
          <div><div class="fw-semi">Payment</div><div class="text-sm text-muted">Expected Mar 12</div></div>
        </div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Icons + emoji ===================== -->
  <section class="container section stack-6" id="mobile">
    <div class="stack-2">
      <h2>Icons and emoji</h2>
      <p class="text-muted">Icons come from one sprite file and inherit color and font
        size, so they sit on the text baseline without nudging. Emoji get a pinned font
        stack so they render the same on Windows, iOS, and Android.</p>
    </div>

    <div class="scroller">
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#car"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#wrench"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#clipboard"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#receipt"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#truck"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#gauge"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#shield"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#sparkle"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#map-pin"></use></svg></span>
      <span class="icon-tile icon-tile-lg"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#send"></use></svg></span>
    </div>

    <div class="cluster">
      <span class="emoji-tile">🔧</span>
      <span class="emoji-tile emoji-tile-round">🚙</span>
      <span class="emoji-tile">📋</span>
      <button class="reaction" aria-pressed="true"><span class="emoji">👍</span> 4</button>
      <button class="reaction"><span class="emoji">🎉</span> 2</button>
      <button class="reaction"><span class="emoji">🔥</span> 1</button>
      <span class="text-muted">Inline <span class="emoji">✅</span> emoji stay on the baseline.</span>
    </div>

    <div class="card"><div class="card-body">
      <span class="demo-label">Emoji picker</span>
      <div class="emoji-grid">
        <button>😀</button><button>😄</button><button>🙂</button><button>😉</button>
        <button>😊</button><button>🤝</button><button>👍</button><button>👏</button>
        <button>🙌</button><button>💪</button><button>🔥</button><button>✨</button>
        <button>🎉</button><button>✅</button><button>⚠️</button><button>🚗</button>
        <button>🔧</button><button>🧰</button><button>📋</button><button>💵</button>
      </div>
    </div></div>
  </section>

  <hr>

  <!-- ===================== Overlays ===================== -->
  <section class="container section stack-6">
    <div class="stack-2">
      <h2>Overlays</h2>
      <p class="text-muted">Built on native <code>&lt;dialog&gt;</code> and the popover
        attribute, so focus trapping, escape-to-close, and the top layer are handled by
        the browser. The sheet slides up from the bottom on a phone and centers on a
        desktop.</p>
    </div>

    <div class="cluster">
      <button class="btn btn-primary" onclick="document.getElementById('m1').showModal()">Open modal</button>
      <button class="btn" onclick="document.getElementById('s1').showModal()">Open bottom sheet</button>
      <button class="btn" popovertarget="menu1">Open menu</button>
      <button class="btn" onclick="toast('Claim 88214 approved', 'good')">Fire a toast</button>
      <span class="btn btn-ghost tooltip" data-tip="Tooltips hide themselves on touch devices, where they never worked anyway.">
        <svg class="icon"><use href="assets/deck/deck-icons.svg#info"></use></svg>
        Hover me
      </span>
    </div>

    <div class="menu" id="menu1" popover>
      <div class="menu-label">Claim actions</div>
      <button class="menu-item"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#edit-sm"></use></svg> Edit</button>
      <button class="menu-item"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#copy-sm"></use></svg> Duplicate <kbd class="push">⌘D</kbd></button>
      <button class="menu-item"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#download-sm"></use></svg> Export PDF</button>
      <div class="menu-sep"></div>
      <button class="menu-item menu-item-danger"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#trash-sm"></use></svg> Withdraw claim</button>
    </div>

    <dialog class="modal" id="m1">
      <div class="modal-header">
        <span class="icon-tile icon-tile-bad"><svg class="icon"><use href="assets/deck/deck-icons.svg#alert-triangle"></use></svg></span>
        <div class="grow">
          <h3 class="modal-title">Withdraw claim 88214?</h3>
          <p class="text-sm text-muted mt-1">The claim leaves the review queue. You can
            submit a corrected version afterward.</p>
        </div>
      </div>
      <div class="modal-body">
        <div class="field">
          <label class="label" for="reason">Reason</label>
          <select class="select" id="reason">
            <option>Incorrect labor operation</option>
            <option>Wrong VIN</option>
            <option>Duplicate submission</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn" onclick="this.closest('dialog').close()">Keep it open</button>
        <button class="btn btn-danger" onclick="this.closest('dialog').close(); toast('Claim withdrawn','bad')">Withdraw claim</button>
      </div>
    </dialog>

    <dialog class="sheet" id="s1">
      <div class="sheet-grip"></div>
      <div class="sheet-header">
        <h3 class="sheet-title grow">Filter claims</h3>
        <button class="btn btn-icon btn-ghost btn-sm" aria-label="Close" onclick="this.closest('dialog').close()">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#x"></use></svg>
        </button>
      </div>
      <div class="sheet-body">
        <div class="segmented">
          <button aria-selected="true">All</button>
          <button aria-selected="false">Mine</button>
          <button aria-selected="false">Flagged</button>
        </div>
        <div class="field">
          <span class="label">Status</span>
          <label class="check"><input type="checkbox" checked><span class="check-text"><span>Approved</span></span></label>
          <label class="check"><input type="checkbox" checked><span class="check-text"><span>Pending</span></span></label>
          <label class="check"><input type="checkbox"><span class="check-text"><span>Denied</span></span></label>
        </div>
        <div class="field">
          <label class="label" for="amt">Minimum amount</label>
          <input class="range" id="amt" type="range" min="0" max="2000" value="250">
        </div>
        <button class="btn btn-primary btn-block btn-lg" onclick="this.closest('dialog').close()">Show 42 claims</button>
      </div>
    </dialog>

    <div class="card">
      <div class="empty">
        <span class="empty-art"><span class="emoji">📭</span></span>
        <span class="empty-title">No claims match those filters</span>
        <p>Clear a filter or widen the date range to see more.</p>
        <button class="btn btn-soft">Clear filters</button>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Date picker ===================== -->
  <section class="container section stack-6" id="dates">
    <div class="stack-2">
      <h2>Date picker</h2>
      <p class="text-muted">Single date, range with two months, and presets. It's a
        popover on a desktop and slides up as a sheet under 480px.</p>
    </div>

    <div class="grid grid-tight">
      <div class="field">
        <label class="label" for="d1">Repair date</label>
        <div class="datefield" data-deck-datepicker data-format="mdy">
          <input class="input" id="d1" placeholder="Pick a date">
        </div>
      </div>

      <div class="field">
        <label class="label" for="d2">Claim period</label>
        <div class="datefield" data-deck-datepicker data-mode="range" data-months="2" data-presets>
          <input class="input" id="d2" placeholder="Start – end">
        </div>
        <span class="help">Try "Last 30 days" in the rail.</span>
      </div>

      <div class="field">
        <label class="label" for="d3">Within warranty window only</label>
        <div class="datefield" data-deck-datepicker data-min="2026-08-15" data-max="2026-10-15">
          <input class="input" id="d3" placeholder="Aug 15 – Oct 15">
        </div>
        <span class="help">Dates outside the range are struck through and unclickable.</span>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Combobox ===================== -->
  <section class="container section stack-6" id="combo">
    <div class="stack-2">
      <h2>Combobox</h2>
      <p class="text-muted">Type to filter, arrows to move, enter to pick. The real
        <code>&lt;select&gt;</code> stays in the DOM and stays in sync, so a plain PHP
        form post works with nothing extra on the server.</p>
    </div>

    <div class="grid grid-tight">
      <div class="field">
        <label class="label" for="c1">Labor operation</label>
        <div class="combo" data-deck-combo data-placeholder="Search operations">
          <select id="c1" hidden>
            <option value="0110" data-sub="Engine · 1.4 hrs" data-group="Powertrain">Cylinder head gasket</option>
            <option value="0219" data-sub="Engine · 0.6 hrs" data-group="Powertrain">Oil pan reseal</option>
            <option value="0331" data-sub="Transmission · 2.8 hrs" data-group="Powertrain">Cooler line replacement</option>
            <option value="1402" data-sub="Electrical · 0.4 hrs" data-group="Electrical">Battery cable</option>
            <option value="1490" data-sub="Electrical · 1.1 hrs" data-group="Electrical">BCM reprogram</option>
            <option value="2201" data-sub="Body · 0.9 hrs" data-group="Body">Door latch actuator</option>
            <option value="2277" data-sub="Body · 1.6 hrs" data-group="Body">Liftgate strut</option>
          </select>
        </div>
        <span class="help">Grouped, with a second line of detail per option.</span>
      </div>

      <div class="field">
        <label class="label" for="c2">Assign to</label>
        <div class="combo" data-deck-combo data-multi data-create data-placeholder="Add people">
          <select id="c2" multiple hidden>
            <option value="rissa" selected>Rissa Molina</option>
            <option value="ken">Ken Spence</option>
            <option value="dana">Dana Whitfield</option>
            <option value="marco">Marco Reyes</option>
            <option value="tina">Tina Okafor</option>
          </select>
        </div>
        <span class="help">Multi-select with tokens. Type a new name to add it.</span>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Data grid ===================== -->
  <section class="container section stack-4" id="grid">
    <div class="stack-2">
      <h2>Data grid</h2>
      <p class="text-muted">Frozen header, pinned first and last columns, sortable
        headers, resizable columns, and a totals row that sticks to the bottom. Scroll
        the grid sideways — the shadow only appears once you do.</p>
    </div>

    <div class="dg-toolbar">
      <div class="search">
        <svg class="icon"><use href="assets/deck/deck-icons.svg#search"></use></svg>
        <input class="input" type="search" placeholder="Filter claims">
      </div>
      <div class="segmented" style="inline-size:auto">
        <button aria-selected="true">Comfortable</button>
        <button aria-selected="false">Compact</button>
      </div>
      <button class="btn btn-sm">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#download-sm"></use></svg>
        Export
      </button>
    </div>

    <div class="dg-selection" data-dg-selection hidden>
      <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-circle-sm"></use></svg>
      <span><span data-dg-count>0</span> selected</span>
      <button class="btn btn-sm push">Reassign</button>
      <button class="btn btn-sm btn-danger">Withdraw</button>
    </div>

    <div class="dg-wrap dg-cards-wrap" data-deck-grid style="--dg-height:360px">
      <table class="dg dg-zebra dg-cards">
        <thead>
          <tr>
            <th class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select all"></label></th>
            <th class="dg-pin-start-2" data-sort="text" data-resize>Claim</th>
            <th data-sort="text" data-resize>VIN</th>
            <th data-sort="text">Vehicle</th>
            <th data-sort="text">Operation</th>
            <th data-sort="text">Advisor</th>
            <th data-sort="text">Status</th>
            <th class="dg-num" data-sort="num">Labor</th>
            <th class="dg-num" data-sort="num">Parts</th>
            <th class="dg-num" data-sort="num">Total</th>
            <th class="dg-num" data-sort="date">Submitted</th>
            <th class="dg-actions dg-pin-end"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select claim 88214"></label></td>
            <td class="dg-pin-start-2" data-label="Claim"><a href="#grid">88214</a></td>
            <td data-label="VIN" class="mono">1FTFW1E85MFA12345</td>
            <td data-label="Vehicle">2021 F-150 XLT</td>
            <td data-label="Operation">Cooler line replacement</td>
            <td data-label="Advisor">Rissa Molina</td>
            <td data-label="Status"><span class="badge badge-good">Approved</span></td>
            <td data-label="Labor" class="dg-num">$392.00</td>
            <td data-label="Parts" class="dg-num">$450.16</td>
            <td data-label="Total" class="dg-num">$842.16</td>
            <td data-label="Submitted" class="dg-num">2026-09-03</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select claim 88220"></label></td>
            <td class="dg-pin-start-2" data-label="Claim"><a href="#grid">88220</a></td>
            <td data-label="VIN" class="mono">1FMSK8DH2PGA98721</td>
            <td data-label="Vehicle">2023 Explorer ST</td>
            <td data-label="Operation">BCM reprogram</td>
            <td data-label="Advisor">Ken Spence</td>
            <td data-label="Status"><span class="badge badge-warn">Pending</span></td>
            <td data-label="Labor" class="dg-num">$154.00</td>
            <td data-label="Parts" class="dg-num">$164.00</td>
            <td data-label="Total" class="dg-num">$318.00</td>
            <td data-label="Submitted" class="dg-num">2026-08-31</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select claim 88109"></label></td>
            <td class="dg-pin-start-2" data-label="Claim"><a href="#grid">88109</a></td>
            <td data-label="VIN" class="mono">1FMCU9J91LUA55310</td>
            <td data-label="Vehicle">2020 Escape SE</td>
            <td data-label="Operation">Door latch actuator</td>
            <td data-label="Advisor">Dana Whitfield</td>
            <td data-label="Status"><span class="badge badge-bad">Denied</span></td>
            <td data-label="Labor" class="dg-num">$0.00</td>
            <td data-label="Parts" class="dg-num">$0.00</td>
            <td data-label="Total" class="dg-num">$0.00</td>
            <td data-label="Submitted" class="dg-num">2026-08-27</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select claim 88301"></label></td>
            <td class="dg-pin-start-2" data-label="Claim"><a href="#grid">88301</a></td>
            <td data-label="VIN" class="mono">1FTBR1C86PKA44029</td>
            <td data-label="Vehicle">2023 Transit 250</td>
            <td data-label="Operation">Liftgate strut</td>
            <td data-label="Advisor">Marco Reyes</td>
            <td data-label="Status"><span class="badge badge-good">Approved</span></td>
            <td data-label="Labor" class="dg-num">$224.00</td>
            <td data-label="Parts" class="dg-num">$97.40</td>
            <td data-label="Total" class="dg-num">$321.40</td>
            <td data-label="Submitted" class="dg-num">2026-09-05</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
          <tr>
            <td class="dg-check dg-pin-start"><label class="check"><input type="checkbox" aria-label="Select claim 88344"></label></td>
            <td class="dg-pin-start-2" data-label="Claim"><a href="#grid">88344</a></td>
            <td data-label="VIN" class="mono">3FMTK3SU9NMA10388</td>
            <td data-label="Vehicle">2022 Mustang Mach-E</td>
            <td data-label="Operation">Battery cable</td>
            <td data-label="Advisor">Tina Okafor</td>
            <td data-label="Status"><span class="badge badge-warn">Pending</span></td>
            <td data-label="Labor" class="dg-num">$88.00</td>
            <td data-label="Parts" class="dg-num">$212.75</td>
            <td data-label="Total" class="dg-num">$300.75</td>
            <td data-label="Submitted" class="dg-num">2026-09-06</td>
            <td class="dg-actions dg-pin-end">
              <button class="btn btn-icon btn-ghost btn-sm" aria-label="Actions"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-horizontal-sm"></use></svg></button>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td class="dg-pin-start"></td>
            <td class="dg-pin-start-2">5 claims</td>
            <td colspan="5"></td>
            <td class="dg-num">$858.00</td>
            <td class="dg-num">$924.31</td>
            <td class="dg-num">$1,782.31</td>
            <td></td>
            <td class="dg-pin-end"></td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="dg-statusbar">
      <span>Showing 5 of 412</span>
      <span class="push">Rows per page</span>
      <select class="select" style="inline-size:auto"><option>25</option><option>50</option><option>100</option></select>
    </div>
  </section>

  <hr>

  <!-- ===================== Toasts ===================== -->
  <section class="container section stack-6" id="toasts-demo">
    <div class="stack-2">
      <h2>Toast queue</h2>
      <p class="text-muted">Toasts stack instead of stringing down the screen. Hover the
        stack to fan it out and pause every timer. Swipe or drag one sideways to
        dismiss it. Fire several in a row to see the queue work.</p>
    </div>

    <div class="cluster">
      <button class="btn" onclick="Deck.toast({kind:'good', title:'Claim 88214 approved', text:'Paid at $842.16 on the next statement.'})">Success</button>
      <button class="btn" onclick="Deck.toast({kind:'warn', title:'Missing documentation', text:'Three claims need a technician story.'})">Warning</button>
      <button class="btn" onclick="Deck.toast({kind:'bad', title:'Claim 88109 denied', text:'Outside the coverage window by 412 miles.'})">Error</button>
      <button class="btn" onclick="Deck.toast({kind:'info', title:'Policy update', text:'Labor allowances changed for 2024 Broncos.'})">Info</button>
      <button class="btn btn-soft" onclick="Deck.toast({kind:'', title:'Claim withdrawn', text:'It has left the review queue.', duration:9000, actions:[{label:'Undo', onClick:()=>Deck.toast({kind:'good',title:'Restored'})}]})">With an action</button>
      <button class="btn btn-soft" onclick="demoProgress()">Loading, then done</button>
      <button class="btn btn-ghost" onclick="Deck.toasts.clear()">Clear all</button>
    </div>
  </section>

  <hr>

  <!-- ===================== Charts ===================== -->
  <section class="container section stack-6" id="charts">
    <div class="stack-2">
      <h2>Charts</h2>
      <p class="text-muted">No charting library. Bars and donuts are CSS driven by a
        <code>--value</code> property; lines are inline SVG you style with classes.
        Every series reads the brand palette, so the hue slider retunes these too.</p>
    </div>

    <div class="grid grid-wide">
      <div class="card"><div class="card-body">
        <div class="chart">
          <div class="chart-head">
            <span class="chart-title">Claims submitted by month</span>
            <span class="chart-note push">2026</span>
          </div>
          <div class="chart-columns">
            <div class="chart-col s1" style="--value:42" data-label="Mar" data-value="84"></div>
            <div class="chart-col s1" style="--value:58" data-label="Apr" data-value="116"></div>
            <div class="chart-col s1" style="--value:51" data-label="May" data-value="102"></div>
            <div class="chart-col s1" style="--value:73" data-label="Jun" data-value="146"></div>
            <div class="chart-col s1" style="--value:66" data-label="Jul" data-value="132"></div>
            <div class="chart-col s1" style="--value:88" data-label="Aug" data-value="176"></div>
            <div class="chart-col s2" style="--value:100" data-label="Sep" data-value="200"></div>
          </div>
        </div>
      </div></div>

      <div class="card"><div class="card-body">
        <div class="chart">
          <div class="chart-head"><span class="chart-title">Approval rate</span></div>
          <svg class="chart-svg" viewBox="0 0 300 120" preserveAspectRatio="none" role="img" aria-label="Approval rate trending up">
            <line class="chart-gridline" x1="0" y1="30" x2="300" y2="30"/>
            <line class="chart-gridline" x1="0" y1="60" x2="300" y2="60"/>
            <line class="chart-gridline" x1="0" y1="90" x2="300" y2="90"/>
            <path class="chart-area s1" d="M0,86 L50,74 L100,80 L150,52 L200,58 L250,34 L300,26 L300,120 L0,120 Z"/>
            <path class="chart-line s1" d="M0,86 L50,74 L100,80 L150,52 L200,58 L250,34 L300,26"/>
            <path class="chart-line chart-line-dashed s-muted" d="M0,96 L50,92 L100,94 L150,84 L200,88 L250,78 L300,74"/>
            <line class="chart-baseline" x1="0" y1="120" x2="300" y2="120"/>
          </svg>
          <div class="chart-x"><span>Mar</span><span>May</span><span>Jul</span><span>Sep</span></div>
          <div class="chart-legend">
            <span class="s1">This dealer</span>
            <span class="s-muted">Region average</span>
          </div>
        </div>
      </div></div>
    </div>

    <div class="grid">
      <div class="card"><div class="card-body">
        <span class="chart-title">Claims by status</span>
        <div class="cluster" style="justify-content:center;padding-block:var(--space-2)">
          <div class="donut-wrap">
            <div class="donut" style="--stops: var(--c1) 0 62%, var(--c3) 62% 84%, var(--c4) 84% 100%"></div>
            <div class="donut-center">
              <span class="donut-value">412</span>
              <span class="donut-label">total</span>
            </div>
          </div>
        </div>
        <div class="chart-legend" style="justify-content:center">
          <span class="s1">Approved 62%</span>
          <span class="s3">Pending 22%</span>
          <span class="s4">Denied 16%</span>
        </div>
      </div></div>

      <div class="card"><div class="card-body">
        <span class="chart-title mb-3">Top labor operations</span>
        <div class="chart chart-bars">
          <div class="chart-bar">
            <span class="chart-bar-label">Cooler line</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s1" style="--value:92"></span></span>
            <span class="chart-bar-value">92</span>
          </div>
          <div class="chart-bar">
            <span class="chart-bar-label">BCM reprogram</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s2" style="--value:71"></span></span>
            <span class="chart-bar-value">71</span>
          </div>
          <div class="chart-bar">
            <span class="chart-bar-label">Door latch</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s3" style="--value:54"></span></span>
            <span class="chart-bar-value">54</span>
          </div>
          <div class="chart-bar">
            <span class="chart-bar-label">Liftgate strut</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s4" style="--value:38"></span></span>
            <span class="chart-bar-value">38</span>
          </div>
          <div class="chart-bar">
            <span class="chart-bar-label">Battery cable</span>
            <span class="chart-bar-track"><span class="chart-bar-fill s5" style="--value:21"></span></span>
            <span class="chart-bar-value">21</span>
          </div>
        </div>
      </div></div>

      <div class="card"><div class="card-body stack-4">
        <div class="stat">
          <span class="stat-label">Reimbursement this week</span>
          <div class="cluster cluster-tight">
            <span class="stat-value">$18,402</span>
            <svg class="sparkline sparkline-good" viewBox="0 0 88 28" preserveAspectRatio="none">
              <path class="chart-line" d="M0,22 L11,19 L22,24 L33,14 L44,17 L55,9 L66,12 L77,5 L88,3"/>
            </svg>
          </div>
          <span class="stat-delta stat-delta-up">
            <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#trend-up-sm"></use></svg>
            8.1% vs last week
          </span>
        </div>
        <div class="stack-2">
          <span class="chart-note">Queue mix</span>
          <div class="chart-meter">
            <span class="s1" style="--value:62"></span>
            <span class="s3" style="--value:22"></span>
            <span class="s4" style="--value:16"></span>
          </div>
        </div>
        <div class="stack-2">
          <span class="chart-note">Submissions, last five weeks</span>
          <div class="chart-heat" style="--cols:7">
            <div style="--value:10"></div><div style="--value:35"></div><div style="--value:80"></div>
            <div style="--value:55"></div><div style="--value:95"></div><div style="--value:20"></div>
            <div style="--value:5"></div><div style="--value:15"></div><div style="--value:60"></div>
            <div style="--value:100"></div><div style="--value:70"></div><div style="--value:45"></div>
            <div style="--value:25"></div><div style="--value:8"></div><div style="--value:30"></div>
            <div style="--value:85"></div><div style="--value:50"></div><div style="--value:90"></div>
            <div style="--value:40"></div><div style="--value:12"></div><div style="--value:3"></div>
          </div>
        </div>
      </div></div>
    </div>
  </section>

  <hr>

  <hr>

  <!-- ===================== Motion ===================== -->
  <section class="container section stack-6" id="motion">
    <div class="stack-2">
      <h2>Transitions and animations</h2>
      <p class="text-muted">Motion answers an action or shows what changed. Nothing here
        runs unless you ask for it by class, and everything is off for anyone whose OS
        asks for reduced motion — except loading indicators, which keep turning slowly,
        because a frozen spinner reads as broken.</p>
    </div>

    <div class="stack-3">
      <span class="demo-label">Entrances, staggered</span>
      <div class="grid grid-tight stagger" id="entranceDemo">
        <div class="card enter-rise"><div class="card-body"><span class="fw-semi">Submitted</span><span class="text-sm text-muted">42 claims</span></div></div>
        <div class="card enter-rise"><div class="card-body"><span class="fw-semi">In review</span><span class="text-sm text-muted">18 claims</span></div></div>
        <div class="card enter-rise"><div class="card-body"><span class="fw-semi">Approved</span><span class="text-sm text-muted">311 claims</span></div></div>
        <div class="card enter-rise"><div class="card-body"><span class="fw-semi">Denied</span><span class="text-sm text-muted">41 claims</span></div></div>
      </div>
      <button class="btn btn-sm" onclick="replayEntrances()">Replay</button>
    </div>

    <div class="stack-3">
      <span class="demo-label">Scroll reveal</span>
      <p class="text-muted text-sm">These use <code>animation-timeline: view()</code> —
        tied to scroll position with no IntersectionObserver. deck.js falls back to an
        observer where the browser doesn't support it.</p>
      <div class="grid grid-tight">
        <div class="card reveal"><div class="card-body"><span class="fw-semi">Reveal</span></div></div>
        <div class="card reveal-fade"><div class="card-body"><span class="fw-semi">Fade</span></div></div>
        <div class="card reveal-pop"><div class="card-body"><span class="fw-semi">Pop</span></div></div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Micro-interactions</span>
      <div class="cluster">
        <button class="btn lift">Lift on hover</button>
        <button class="btn press">Press</button>
        <button class="btn btn-primary ripple">Ripple</button>
        <button class="btn btn-soft">
          Continue
          <svg class="icon icon-sm icon-follow"><use href="assets/deck/deck-icons.svg#arrow-right-sm"></use></svg>
        </button>
        <a class="sweep" href="#motion">Underline sweep</a>
        <span class="badge badge-good"><span class="ping" style="display:inline-block;inline-size:6px;block-size:6px;border-radius:99px;background:currentColor"></span> Live</span>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Attention and feedback</span>
      <div class="cluster">
        <button class="btn" onclick="Deck.play(this, 'shake')">Shake</button>
        <button class="btn" onclick="Deck.play(this.closest('.cluster'), 'flash')">Flash</button>
        <button class="btn" onclick="Deck.play(document.getElementById('flashRow'), 'flash-good')">Flash a row</button>
        <button class="btn" onclick="bumpTotal()">Change a number</button>
        <span class="badge badge-warn pulse">Pulsing</span>
      </div>
      <div class="list">
        <div class="list-row" id="flashRow">
          <span class="icon-tile icon-tile-good"><svg class="icon"><use href="assets/deck/deck-icons.svg#check"></use></svg></span>
          <span class="list-main">
            <span class="list-title">Claim 88214</span>
            <span class="list-sub">Reimbursement total</span>
          </span>
          <span class="list-trail nums fw-semi" data-deck-tick id="total">$842.16</span>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Expand and collapse</span>
      <p class="text-muted text-sm">A real <code>height: auto</code> transition using
        <code>interpolate-size</code>. No measuring in JavaScript, no max-height guess.</p>
      <button class="btn btn-sm" onclick="Deck.toggle(document.getElementById('exp'))">Toggle details</button>
      <div class="expand" id="exp">
        <div class="panel">
          <p class="text-muted">Cause: transmission fluid seeping at the cooler line
            fitting. Correction: replaced the line assembly, refilled, road tested
            twelve miles with no recurrence. Verified with a lift inspection.</p>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Ticker</span>
      <div class="marquee card" style="padding-block:var(--space-3)">
        <div class="marquee-track text-sm text-muted">
          <span>FSA 24B12 open on 2,140 units</span>
          <span>Parts backorder: cooler line assembly, ETA Sep 19</span>
          <span>Labor time allowance updated for operation 0331</span>
          <span>Statement posts Friday</span>
        </div>
        <div class="marquee-track text-sm text-muted" aria-hidden="true">
          <span>FSA 24B12 open on 2,140 units</span>
          <span>Parts backorder: cooler line assembly, ETA Sep 19</span>
          <span>Labor time allowance updated for operation 0331</span>
          <span>Statement posts Friday</span>
        </div>
      </div>
    </div>

    <div class="alert alert-info">
      <svg class="icon"><use href="assets/deck/deck-icons.svg#sparkle"></use></svg>
      <div>
        <div class="alert-title">Page transitions in a plain PHP app</div>
        <p class="alert-body">Add <code>@view-transition { navigation: auto; }</code> to
          your app CSS and full page loads in Keel cross-fade like a single page app —
          no router, no JavaScript. Deck styles what the browser generates, including
          holding the header and tab bar still while the content changes.</p>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Cascade layers ===================== -->
  <section class="container section stack-6" id="layers">
    <div class="stack-2">
      <h2>Cascade layers</h2>
      <p class="text-muted">Deck declares the whole cascade contract in
        <code>00-layers.css</code> before a single rule exists. Order is decided there —
        not by file order, not by specificity, and never by <code>!important</code>.
        Four empty <code>app.*</code> layers are reserved for you.</p>
    </div>

    <pre><code>@layer
  deck.reset, deck.tokens, deck.type, deck.layout,
  deck.components, deck.mobile, deck.motion, deck.effects,
  deck.utilities, deck.rtl, deck.print,

  app.base, app.components, app.pages, app.overrides;</code></pre>

    <div class="grid grid-tight">
      <div class="card"><div class="card-body">
        <span class="card-title">Write in a slot</span>
        <p class="text-sm text-muted">A rule in <code>app.pages</code> beats every Deck
          rule with a single class selector. No specificity war, no stacked
          <code>.page .card .btn</code> chains.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <span class="card-title">Unlayered always wins</span>
        <p class="text-sm text-muted">Anything outside a layer beats all layers. A one-off
          rule in a Keel view template overrides Deck with nothing special.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <span class="card-title">Third-party CSS</span>
        <p class="text-sm text-muted">Wrap a vendor stylesheet in its own layer with
          <code>@import url(x.css) layer(vendor)</code> and it stops fighting you.</p>
      </div></div>
    </div>
  </section>

  <hr>

  <!-- ===================== Container queries ===================== -->
  <section class="container section stack-6" id="containers">
    <div class="stack-2">
      <h2>Container queries</h2>
      <p class="text-muted">The same card markup, twice. The one in the wide column goes
        horizontal; the one in the rail stays stacked. Neither knows where it was
        placed — they respond to the space they were given, not the window.</p>
    </div>

    <div class="split">
      <div class="cq">
        <article class="card card-flex">
          <figure class="card-media"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt=""></figure>
          <div class="card-body">
            <h3 class="card-title">Cooler line replacement</h3>
            <p class="text-sm text-muted">Operation 0331 · 2.8 hours · 2021 F-150</p>
            <div class="actions-cq">
              <button class="btn btn-sm btn-primary">Open claim</button>
              <button class="btn btn-sm">History</button>
            </div>
          </div>
        </article>
      </div>

      <aside class="cq">
        <article class="card card-flex">
          <figure class="card-media"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt=""></figure>
          <div class="card-body">
            <h3 class="card-title">Cooler line replacement</h3>
            <p class="text-sm text-muted">Operation 0331 · 2.8 hours · 2021 F-150</p>
            <div class="actions-cq">
              <button class="btn btn-sm btn-primary">Open claim</button>
              <button class="btn btn-sm">History</button>
            </div>
          </div>
        </article>
      </aside>
    </div>

    <div class="stack-3">
      <span class="demo-label">Style queries — set <code>--tone</code> on the container, children follow</span>
      <div class="grid grid-tight">
        <div class="cq-tone" style="--tone: clear">
          <div class="card tone-surface"><div class="card-body cluster cluster-tight">
            <svg class="icon icon-lg tone-icon"><use href="assets/deck/deck-icons.svg#check-circle"></use></svg>
            <span class="tone-text fw-semi">Within coverage</span>
          </div></div>
        </div>
        <div class="cq-tone" style="--tone: caution">
          <div class="card tone-surface"><div class="card-body cluster cluster-tight">
            <svg class="icon icon-lg tone-icon"><use href="assets/deck/deck-icons.svg#alert-triangle"></use></svg>
            <span class="tone-text fw-semi">Documentation needed</span>
          </div></div>
        </div>
        <div class="cq-tone" style="--tone: critical">
          <div class="card tone-surface"><div class="card-body cluster cluster-tight">
            <svg class="icon icon-lg tone-icon"><use href="assets/deck/deck-icons.svg#x-circle"></use></svg>
            <span class="tone-text fw-semi">Out of coverage</span>
          </div></div>
        </div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Logical properties ===================== -->
  <section class="container section stack-6" id="logical">
    <div class="stack-2">
      <h2>Logical properties</h2>
      <p class="text-muted">Deck is written in logical properties end to end, so a full
        right-to-left flip needs nothing but <code>dir="rtl"</code>. Try the switch — the
        layout, the switch knob, the chart fills, the sidebar, and the pointing icons all
        mirror. The VIN does not, because a VIN reads left to right in every language.</p>
    </div>

    <div class="cluster">
      <button class="btn" onclick="Deck.dir(Deck.dir() === 'rtl' ? 'ltr' : 'rtl')">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#refresh-sm"></use></svg>
        Flip the whole page
      </button>
      <span class="badge">current: <span id="dirLabel">ltr</span></span>
    </div>

    <div class="grid grid-tight">
      <div class="card"><div class="card-body stack-3">
        <span class="card-title">Mirrors</span>
        <div class="cluster cluster-tight">
          <button class="btn btn-sm">Next <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#arrow-right-sm"></use></svg></button>
          <label class="switch"><input type="checkbox" checked><span class="text-sm">Auto-submit</span></label>
        </div>
        <div class="chart-bar">
          <span class="chart-bar-label">Approved</span>
          <span class="chart-bar-track"><span class="chart-bar-fill s1" style="--value:74"></span></span>
          <span class="chart-bar-value">74</span>
        </div>
      </div></div>

      <div class="card"><div class="card-body stack-3">
        <span class="card-title">Does not mirror</span>
        <p class="text-sm text-muted">Identifiers stay in their own direction and stay
          isolated from the text around them.</p>
        <div class="stack-2">
          <span class="mono vin">1FTFW1E85MFA12345</span>
          <span class="cluster cluster-tight">
            <svg class="icon no-flip"><use href="assets/deck/deck-icons.svg#wrench"></use></svg>
            <svg class="icon no-flip"><use href="assets/deck/deck-icons.svg#clock"></use></svg>
            <svg class="icon no-flip"><use href="assets/deck/deck-icons.svg#check"></use></svg>
            <span class="text-sm text-muted">glyphs with no direction</span>
          </span>
        </div>
      </div></div>

      <div class="card"><div class="card-body">
        <span class="card-title mbe-2">Writing modes</span>
        <div class="cluster">
          <span class="writing-vertical text-sm text-muted" style="block-size:6rem">Vertical column header</span>
          <span class="writing-vertical writing-upright text-sm text-muted" style="block-size:6rem">UPRIGHT</span>
        </div>
      </div></div>
    </div>
  </section>

  <hr>

  <!-- ===================== Gradients ===================== -->
  <section class="container section stack-6" id="gradients">
    <div class="stack-2">
      <h2>Gradients</h2>
      <p class="text-muted">All built from the brand hue and interpolated in oklab, which
        avoids the grey dead zone you get blending two saturated colors in sRGB. Drag the
        hue slider and every one of these retunes.</p>
    </div>

    <div class="card g-mesh g-mesh-drift" style="min-block-size:200px">
      <div class="card-body center" style="justify-content:center;block-size:100%">
        <h3 class="display-cq g-text" style="font-size:var(--text-2xl)">Warranty, handled</h3>
        <p class="text-muted">Mesh background, gradient text, no images</p>
      </div>
    </div>

    <div class="grid grid-tight">
      <div class="card g-border"><div class="card-body">
        <span class="fw-semi">Gradient border</span>
        <p class="text-sm text-muted">Two clip boxes, works with any radius.</p>
      </div></div>
      <div class="card g-border g-border-spin"><div class="card-body">
        <span class="fw-semi">Animated border</span>
        <p class="text-sm text-muted">The angle is a registered <code>@property</code>, so it interpolates.</p>
      </div></div>
      <div class="card"><div class="card-body">
        <span class="fw-semi g-text-shine">Shine sweep on text</span>
        <p class="text-sm text-muted">Clipped to the glyphs, with a real color underneath.</p>
      </div></div>
    </div>

    <div class="cluster">
      <button class="btn btn-primary g-sheen">Hover for sheen</button>
      <span class="badge g-good">Approved</span>
      <span class="badge g-warn">Pending</span>
      <span class="badge g-bad">Denied</span>
      <span class="g-ring g-ring-spin" style="inline-size:28px;block-size:28px;display:inline-block"></span>
    </div>

    <div class="grid grid-tight">
      <div class="card g-grid-lines" style="min-block-size:110px"><div class="card-body"><span class="text-sm text-muted">Grid lines</span></div></div>
      <div class="card g-dots" style="min-block-size:110px"><div class="card-body"><span class="text-sm text-muted">Dots</span></div></div>
      <div class="card g-hatch" style="min-block-size:110px"><div class="card-body"><span class="text-sm text-muted">Hatch — unavailable slot</span></div></div>
      <div class="card g-scrim" style="min-block-size:110px;background:var(--ink-400)">
        <div class="card-body" style="justify-content:flex-end;block-size:100%">
          <span class="fw-semi" style="color:#fff">Scrim over media</span>
        </div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== 3D ===================== -->
  <section class="container section stack-6" id="space">
    <div class="stack-2">
      <h2>3D transforms</h2>
      <p class="text-muted">Depth earns its place when it carries meaning: a card with two
        sides, a pile you are working down through, a control that physically depresses.
        Everything uses <code>rotate</code> and <code>translate</code> as individual
        properties, so two effects compose instead of overwriting each other.</p>
    </div>

    <div class="grid grid-tight">
      <div class="scene">
        <div class="flip card" id="flipCard" style="min-block-size:170px">
          <div class="flip-front card-body stack-2">
            <span class="card-title">Claim 88214</span>
            <p class="text-sm text-muted">2021 F-150 · cooler line</p>
            <button class="btn btn-sm push" data-deck-flip>See the breakdown</button>
          </div>
          <div class="flip-back card-body stack-2 g-brand-soft">
            <span class="card-title">Breakdown</span>
            <p class="text-sm">Labor $392.00 · Parts $450.16</p>
            <button class="btn btn-sm" data-deck-flip>Back</button>
          </div>
        </div>
      </div>

      <div class="scene scene-near">
        <div class="card tilt" data-tilt="10" style="min-block-size:170px">
          <div class="card-body space">
            <span class="card-title tilt-lift">Tilt</span>
            <p class="text-sm text-muted tilt-lift-sm">Move the pointer across this card.
              The title floats above the face on the Z axis.</p>
          </div>
        </div>
      </div>

      <div class="stack-3">
        <div class="stack-depth" id="pile" style="min-block-size:150px">
          <div class="card"><div class="card-body"><span class="fw-semi">Claim 88301</span><span class="text-sm text-muted">Awaiting review</span></div></div>
          <div class="card"><div class="card-body"><span class="fw-semi">Claim 88344</span><span class="text-sm text-muted">Awaiting review</span></div></div>
          <div class="card"><div class="card-body"><span class="fw-semi">Claim 88350</span><span class="text-sm text-muted">Awaiting review</span></div></div>
          <div class="card"><div class="card-body"><span class="fw-semi">Claim 88361</span><span class="text-sm text-muted">Awaiting review</span></div></div>
        </div>
        <div class="cluster cluster-tight">
          <button class="btn btn-sm" onclick="document.getElementById('pile').classList.toggle('is-fanned')">Fan out</button>
          <button class="btn btn-sm" onclick="Deck.advance(document.getElementById('pile'))">Next card</button>
        </div>
      </div>
    </div>

    <div class="cluster">
      <button class="btn btn-primary btn-3d">Press me</button>
      <div class="scene">
        <div class="cube cube-spin" style="--size:88px">
          <div class="face-front"><span class="emoji">🔧</span></div>
          <div class="face-back"><span class="emoji">📋</span></div>
          <div class="face-end"><span class="emoji">🚙</span></div>
          <div class="face-start"><span class="emoji">💵</span></div>
          <div class="face-top"><span class="emoji">✅</span></div>
          <div class="face-bottom"><span class="emoji">🧰</span></div>
        </div>
      </div>
    </div>

    <div class="stack-2">
      <span class="demo-label">Coverflow — scroll it sideways</span>
      <div class="coverflow">
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">March</span><span class="text-sm text-muted">84 claims</span></div></div>
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">April</span><span class="text-sm text-muted">116 claims</span></div></div>
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">May</span><span class="text-sm text-muted">102 claims</span></div></div>
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">June</span><span class="text-sm text-muted">146 claims</span></div></div>
        <div class="card" style="inline-size:190px"><div class="card-body"><span class="fw-semi">July</span><span class="text-sm text-muted">132 claims</span></div></div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Added components ===================== -->
  <section class="container section stack-8" id="more">
    <div class="stack-2">
      <h2>Carousel, drawer, mega menu, speed dial</h2>
      <p class="text-muted">The carousel is scroll snap underneath, so it swipes correctly
        with JavaScript off. Arrows and dots are enhancement.</p>
    </div>

    <div class="carousel carousel-peek" data-deck-carousel tabindex="0">
      <button class="carousel-arrow carousel-prev" aria-label="Previous"><svg class="icon"><use href="assets/deck/deck-icons.svg#chevron-left"></use></svg></button>
      <div class="carousel-track">
        <div class="carousel-slide card"><div class="card-body"><span class="fw-semi">Open claims</span><span class="stat-value">42</span></div></div>
        <div class="carousel-slide card"><div class="card-body"><span class="fw-semi">Approved this week</span><span class="stat-value">311</span></div></div>
        <div class="carousel-slide card"><div class="card-body"><span class="fw-semi">Average payout</span><span class="stat-value">$614</span></div></div>
        <div class="carousel-slide card"><div class="card-body"><span class="fw-semi">Waiting on parts</span><span class="stat-value">17</span></div></div>
      </div>
      <button class="carousel-arrow carousel-next" aria-label="Next"><svg class="icon"><use href="assets/deck/deck-icons.svg#chevron-right"></use></svg></button>
      <div class="carousel-dots"></div>
    </div>

    <div class="cluster">
      <button class="btn" data-deck-drawer="#drawer1">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#filter-sm"></use></svg> Open drawer
      </button>
      <button class="btn" popovertarget="mega1" data-deck-mega="#mega1">Products
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#chevron-down-sm"></use></svg>
      </button>
      <span class="with-indicator">
        <button class="btn btn-icon" aria-label="Notifications"><svg class="icon"><use href="assets/deck/deck-icons.svg#bell"></use></svg></button>
        <span class="indicator-badge">7</span>
      </span>
      <span class="status-line"><span class="indicator indicator-good ping"></span> All systems normal</span>
      <span class="status-line"><span class="indicator indicator-warn"></span> Degraded</span>
    </div>

    <dialog class="drawer" id="drawer1">
      <div class="drawer-header">
        <h3 class="drawer-title grow">Filter claims</h3>
        <button class="btn btn-icon btn-ghost btn-sm" data-drawer-close aria-label="Close"><svg class="icon"><use href="assets/deck/deck-icons.svg#x"></use></svg></button>
      </div>
      <div class="drawer-body">
        <div class="field">
          <span class="label">Status</span>
          <label class="check"><input type="checkbox" checked><span class="check-text"><span>Approved</span></span></label>
          <label class="check"><input type="checkbox" checked><span class="check-text"><span>Pending</span></span></label>
          <label class="check"><input type="checkbox"><span class="check-text"><span>Denied</span></span></label>
        </div>
        <div class="field">
          <span class="label">Payout range</span>
          <div class="range-pair" data-gap="5">
            <input type="range" min="0" max="2000" value="250" data-prefix="$" aria-label="Minimum">
            <input type="range" min="0" max="2000" value="1400" data-prefix="$" aria-label="Maximum">
          </div>
          <div class="range-readout"><span>$250</span><span>$1400</span></div>
        </div>
      </div>
      <div class="drawer-footer">
        <button class="btn" data-drawer-close>Reset</button>
        <button class="btn btn-primary" data-drawer-close>Apply</button>
      </div>
    </dialog>

    <div class="mega" id="mega1" popover>
      <div class="mega-grid">
        <div class="mega-col">
          <span class="mega-heading">Claims</span>
          <a class="mega-item" href="#more"><span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#clipboard"></use></svg></span><span><span class="mega-item-title">Submit a claim</span><span class="mega-item-note">Start from a VIN or an RO</span></span></a>
          <a class="mega-item" href="#more"><span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#search"></use></svg></span><span><span class="mega-item-title">Look up coverage</span><span class="mega-item-note">Per VIN, in seconds</span></span></a>
        </div>
        <div class="mega-col">
          <span class="mega-heading">Reporting</span>
          <a class="mega-item" href="#more"><span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#chart"></use></svg></span><span><span class="mega-item-title">Dashboards</span><span class="mega-item-note">Approval rate and aging</span></span></a>
          <a class="mega-item" href="#more"><span class="icon-tile"><svg class="icon"><use href="assets/deck/deck-icons.svg#receipt"></use></svg></span><span><span class="mega-item-title">Statements</span><span class="mega-item-note">Reconcile the payout</span></span></a>
        </div>
        <div class="mega-feature">
          <span class="fw-semi">The Warranty Desk</span>
          <p class="text-sm mt-1">Self-serve answers to general and per-VIN warranty questions.</p>
        </div>
      </div>
      <div class="mega-footer"><a href="#more">Documentation</a><a href="#more">Release notes</a><a href="#more">Contact support</a></div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Stepper</span>
      <div class="stepper stepper-auto">
        <div class="step is-done"><div class="step-marker"></div><span class="step-label">Vehicle</span><span class="step-note">VIN verified</span></div>
        <div class="step is-done"><div class="step-marker"></div><span class="step-label">Repair</span><span class="step-note">Operation 0331</span></div>
        <div class="step is-current"><div class="step-marker"></div><span class="step-label">Documentation</span><span class="step-note">Story and photos</span></div>
        <div class="step"><div class="step-marker"></div><span class="step-label">Review</span><span class="step-note">Two to four days</span></div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Inputs</span>
      <div class="grid grid-tight">
        <div class="float">
          <input class="input" id="f1" placeholder=" ">
          <label for="f1">Repair order number</label>
        </div>
        <div class="float float-outline">
          <input class="input" id="f2" placeholder=" ">
          <label for="f2">Customer name</label>
        </div>
        <div class="field">
          <label class="label" for="n1">Labor hours</label>
          <div class="number">
            <button type="button" data-step="-1" aria-label="Decrease"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#minus-sm"></use></svg></button>
            <input id="n1" type="number" value="2.8" step="0.1" min="0" max="24">
            <button type="button" data-step="1" aria-label="Increase"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#plus-sm"></use></svg></button>
            <span class="number-unit">hrs</span>
          </div>
        </div>
        <div class="field">
          <label class="label" for="p1">Phone</label>
          <div class="phone">
            <span class="phone-country">
              <span class="phone-flag">🇺🇸</span>
              <span class="phone-code">+1</span>
              <select aria-label="Country">
                <option data-code="+1" data-flag="🇺🇸" data-mask="(###) ###-####" selected>US</option>
                <option data-code="+1" data-flag="🇨🇦" data-mask="(###) ###-####">CA</option>
                <option data-code="+52" data-flag="🇲🇽" data-mask="## #### ####">MX</option>
              </select>
            </span>
            <input id="p1" type="tel" placeholder="(000) 000-0000">
            <span class="phone-status"><svg class="icon icon-sm text-good"><use href="assets/deck/deck-icons.svg#check-sm"></use></svg></span>
          </div>
        </div>
        <div class="field">
          <span class="label">Rate this repair</span>
          <div class="rating">
            <input type="radio" name="rate" id="r5" value="5"><label for="r5"><svg class="icon"><use href="assets/deck/deck-icons.svg#star"></use></svg></label>
            <input type="radio" name="rate" id="r4" value="4"><label for="r4"><svg class="icon"><use href="assets/deck/deck-icons.svg#star"></use></svg></label>
            <input type="radio" name="rate" id="r3" value="3" checked><label for="r3"><svg class="icon"><use href="assets/deck/deck-icons.svg#star"></use></svg></label>
            <input type="radio" name="rate" id="r2" value="2"><label for="r2"><svg class="icon"><use href="assets/deck/deck-icons.svg#star"></use></svg></label>
            <input type="radio" name="rate" id="r1" value="1"><label for="r1"><svg class="icon"><use href="assets/deck/deck-icons.svg#star"></use></svg></label>
          </div>
        </div>
        <div class="field">
          <span class="label">Claim number</span>
          <div class="copy">
            <code class="copy-value">88214-2026-0331</code>
            <button class="copy-btn" data-deck-copy>
              <span class="copy-idle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#copy-sm"></use></svg> Copy</span>
              <span class="copy-done"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-sm"></use></svg> Copied</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Editor</span>
      <div class="editor" data-limit="600">
        <div class="editor-toolbar">
          <select class="editor-select" aria-label="Block format">
            <option value="p">Paragraph</option>
            <option value="h2">Heading</option>
            <option value="h3">Subheading</option>
          </select>
          <span class="editor-sep"></span>
          <button class="editor-tool" data-cmd="bold" aria-label="Bold">B</button>
          <button class="editor-tool" data-cmd="italic" aria-label="Italic" style="font-style:italic">I</button>
          <button class="editor-tool" data-cmd="underline" aria-label="Underline" style="text-decoration:underline">U</button>
          <span class="editor-sep"></span>
          <button class="editor-tool" data-cmd="insertUnorderedList" aria-label="Bullet list"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#list-sm"></use></svg></button>
          <button class="editor-tool" data-cmd="createLink" aria-label="Link"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#link-sm"></use></svg></button>
          <span class="editor-sep"></span>
          <button class="editor-tool" data-cmd="undo" aria-label="Undo"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#refresh-sm"></use></svg></button>
        </div>
        <div class="editor-content" data-placeholder="Cause, correction, and what was verified."></div>
        <div class="editor-footer"><span>Pastes arrive as plain text</span><span class="editor-count">0 / 600</span></div>
      </div>
    </div>

    <div class="grid grid-wide">
      <div class="stack-3">
        <span class="demo-label">Chat</span>
        <div class="card">
          <div class="chat" style="max-block-size:280px">
            <span class="chat-day">Today</span>
            <div class="msg">
              <span class="avatar avatar-xs">RM</span>
              <div><div class="bubble"><span class="bubble-name">Rissa</span>The story on 88214 is missing the diagnostic steps.</div></div>
            </div>
            <div class="msg">
              <span class="avatar avatar-xs">RM</span>
              <div><div class="bubble">Can you add what the tech verified before the line was replaced?<div class="bubble-meta">8:14 AM</div></div></div>
            </div>
            <div class="msg msg-out">
              <div><div class="bubble">On it. He pressure tested the cooler circuit first — I'll write it up.<div class="bubble-meta">8:16 AM <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#check-double-sm"></use></svg></div></div></div>
            </div>
            <div class="msg">
              <span class="avatar avatar-xs">RM</span>
              <div><div class="bubble bubble-typing"><span></span><span></span><span></span></div></div>
            </div>
          </div>
          <div class="chat-composer">
            <textarea class="textarea" placeholder="Write a message" rows="1"></textarea>
            <button class="btn btn-icon btn-primary btn-round" aria-label="Send"><svg class="icon"><use href="assets/deck/deck-icons.svg#send"></use></svg></button>
          </div>
        </div>
      </div>

      <div class="stack-3">
        <span class="demo-label">QR code — encoded in the browser, no library and no network call</span>
        <div class="cluster">
          <div class="qr" data-deck-qr="https://claim-iq.io/c/88214" data-ecl="M"></div>
          <div class="qr qr-sm" data-deck-qr="1FTFW1E85MFA12345" data-ecl="H"></div>
        </div>
        <span class="demo-label">Video</span>
        <div class="video">
          <button class="video-poster">
            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 360'%3E%3Crect width='640' height='360' fill='%2378909c'/%3E%3C/svg%3E" alt="">
            <span class="video-play"><svg class="icon icon-xl"><use href="assets/deck/deck-icons.svg#chevron-right"></use></svg></span>
          </button>
          <span class="video-meta"><span class="video-duration">4:12</span></span>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Masonry gallery with lazy loading</span>
      <div class="masonry masonry-3">
        <div class="lazy" style="--ratio:3/4"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 400'%3E%3Crect width='300' height='400' fill='%2390a4ae'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:4/3"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 300'%3E%3Crect width='400' height='300' fill='%23b0bec5'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:1"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:3/5"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 500'%3E%3Crect width='300' height='500' fill='%2378909c'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:16/9"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 640 360'%3E%3Crect width='640' height='360' fill='%23b0bec5'/%3E%3C/svg%3E" alt=""></div>
        <div class="lazy" style="--ratio:4/5"><img data-src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 500'%3E%3Crect width='400' height='500' fill='%2390a4ae'/%3E%3C/svg%3E" alt=""></div>
      </div>
    </div>

    <div class="jumbotron jumbotron-center g-mesh-subtle">
      <span class="badge badge-brand">Jumbotron</span>
      <h2>Every claim, answered the first time</h2>
      <p class="lede">Coverage checks, labor allowances, and submission validation before
        anything reaches the review queue.</p>
      <div class="jumbotron-actions">
        <button class="btn btn-primary btn-lg">Start a claim</button>
        <button class="btn btn-lg">See a sample</button>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Gallery ===================== -->
  <section class="container section stack-6" id="gallery">
    <div class="stack-2">
      <h2>Gallery</h2>
      <p class="text-muted">Equal tiles for claim evidence — the damage photos, the RO,
        the part tag. <code>.gallery</code> is a grid of square cells that reflows on its
        own, so an advisor uploading nine photos and one uploading three both get a tidy
        block. <code>.span-2</code> promotes a tile to a 2&times;2 feature and
        <code>.span-wide</code> makes it a 2&times;1 banner, which is how you lead with
        the shot the reviewer actually needs to see.</p>
    </div>

    <div class="stack-3">
      <span class="demo-label">Claim WC-88214 &middot; 2021 F-150 &middot; VIN 1FTFW1E85MFA12345</span>
      <div class="gallery">
        <a class="span-2" href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect width='400' height='400' fill='%2378909c'/%3E%3C/svg%3E" alt="Transmission cooler line at the failure point"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%2390a4ae'/%3E%3C/svg%3E" alt="VIN plate"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23b0bec5'/%3E%3C/svg%3E" alt="Odometer at 41,203 miles"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt="Part tag, number BL3Z-7R081-B"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%2390a4ae'/%3E%3C/svg%3E" alt="Fluid pooled on the crossmember"></a>
        <a class="span-wide" href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 300'%3E%3Crect width='600' height='300' fill='%2378909c'/%3E%3C/svg%3E" alt="Repair order 44190, signed by the customer"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23b0bec5'/%3E%3C/svg%3E" alt="Replacement line installed"></a>
        <a href="#gallery"><img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23cfd8dc'/%3E%3C/svg%3E" alt="Post-repair road test printout"></a>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Sidebar ===================== -->
  <section class="container section stack-6" id="sidebar">
    <div class="stack-2">
      <h2>Sidebar</h2>
      <p class="text-muted"><code>.sidebar</code> is a bare nav list — <code>.sidebar-group</code>
        for a heading, <code>.sidebar-link</code> for a row, <code>.push</code> to shove a
        count to the far end. It brings no width and no chrome of its own, so it goes in
        whatever rail your shell already has. Give that rail <code>.cq-shell</code> and the
        links collapse to icons below 15rem — a container query, not a media query, so it
        keys off the rail and not the window. <strong>Drag the rail by its bottom
        corner</strong> and watch the labels drop out while the page width never
        changes.</p>
    </div>

    <div class="split" style="--rail: 17rem">
      <div class="stack-4">
        <div class="card"><div class="card-body stack-2">
          <h3 class="card-title">Open claims</h3>
          <p class="text-sm text-muted">Forty-two claims are waiting on something. Thirty-one
            of them are waiting on the dealer, not on the review queue.</p>
          <div class="cluster cluster-tight">
            <span class="badge badge-brand">42 open</span>
            <span class="badge">17 awaiting parts</span>
            <span class="badge">6 need photos</span>
          </div>
        </div></div>
        <p class="text-sm text-muted">The rail beside this column is the same markup at every
          width. Nothing inside the sidebar knows how wide it is — the container does.</p>
      </div>

      <aside class="cq-shell" style="resize: horizontal; overflow: auto; min-inline-size: 7rem; max-inline-size: 22rem">
        <nav class="panel sidebar" aria-label="Warranty desk">
          <span class="sidebar-group">Claims</span>
          <a class="sidebar-link" aria-current="page" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#clipboard"></use></svg>
            <span>Open claims</span><span class="badge push">42</span>
          </a>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#check-circle"></use></svg>
            <span>Approved</span><span class="badge push">311</span>
          </a>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#alert-triangle"></use></svg>
            <span>Needs documentation</span><span class="badge push">6</span>
          </a>
          <span class="sidebar-group">Vehicles</span>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#search"></use></svg>
            <span>Look up a VIN</span>
          </a>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#car"></use></svg>
            <span>Coverage lookup</span>
          </a>
          <span class="sidebar-group">Desk</span>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#users"></use></svg>
            <span>Advisors</span>
          </a>
          <a class="sidebar-link" href="#sidebar">
            <svg class="icon"><use href="assets/deck/deck-icons.svg#receipt"></use></svg>
            <span>Statements</span>
          </a>
        </nav>
      </aside>
    </div>
  </section>

  <hr>

  <!-- ===================== Tooltips ===================== -->
  <section class="container section stack-6" id="tooltips">
    <div class="stack-2">
      <h2>Tooltips</h2>
      <p class="text-muted">There are two of them and the difference matters.
        <code>.tooltip</code> is a <code>::after</code> on the trigger with the text in
        <code>data-tip</code> — no extra markup, no JavaScript, nothing to keep in sync.
        But it is pinned above the trigger and <em>cannot flip</em>, so near the top of a
        scrollport it runs off the edge, and it hides itself on coarse pointers because a
        hover tip never worked on a phone anyway. <code>.tip</code> is a real popover
        placed with CSS anchor positioning, so it <em>can</em> flip, and it carries an
        arrow that stays pointed at its anchor. Reach for <code>.tooltip</code> for a
        short label on an icon button in the middle of a page; reach for <code>.tip</code>
        when the text is longer, has to survive an edge, or should open on click.</p>
    </div>

    <div class="stack-3">
      <span class="demo-label">.tooltip &mdash; CSS only, hover or focus, never flips, gone on touch</span>
      <div class="cluster">
        <button class="btn btn-icon tooltip" data-tip="Recheck coverage for this VIN" aria-label="Recheck coverage">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#refresh"></use></svg>
        </button>
        <button class="btn btn-icon tooltip" data-tip="Attach repair order 44190" aria-label="Attach the repair order">
          <svg class="icon"><use href="assets/deck/deck-icons.svg#upload"></use></svg>
        </button>
        <span class="btn btn-ghost tooltip" data-tip="Labor allowance for operation 0331 is 2.8 hours" tabindex="0">
          Operation 0331 <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#help-sm"></use></svg>
        </span>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">.tip &mdash; anchored popover, flips at an edge, carries an arrow</span>
      <div class="cluster">
        <button class="btn" popovertarget="tipVin">Why did this VIN fail?</button>
        <div class="tip" id="tipVin" popover>Coverage ended at 36,000 miles. The odometer read
          41,203 on the repair order, so this one needs a goodwill authorisation before it can
          be submitted.<span class="tip-arrow"></span></div>

        <button class="btn" popovertarget="tipPayout">Payout breakdown</button>
        <div class="tip" id="tipPayout">Parts $318.40, labor 2.8&nbsp;hours at $142, less the
          $100 deductible. Advisor D. Okafor.<span class="tip-arrow"></span></div>
      </div>
    </div>
  </section>

  <hr>

  <!-- ===================== Libraries and performance ===================== -->
  <section class="container section stack-8" id="libs">
    <div class="stack-2">
      <h2>Optional libraries</h2>
      <p class="text-muted">Deck's core stays zero-dependency. <code>deck-adapters.js</code>
        detects a library if you've loaded it and hands that job over, keeping Deck's
        markup and styling unchanged. Load none and everything still works.</p>
    </div>

    <div class="table-wrap">
      <table class="table table-stack">
        <thead><tr><th>Job</th><th>Deck alone</th><th>With a library</th><th>Verdict</th></tr></thead>
        <tbody>
          <tr>
            <td data-label="Job">Placement</td>
            <td data-label="Deck alone">CSS anchor positioning</td>
            <td data-label="With a library">Floating UI, 9 KB</td>
            <td data-label="Verdict">Library only where anchor positioning is missing</td>
          </tr>
          <tr>
            <td data-label="Job">Rich text</td>
            <td data-label="Deck alone"><code>execCommand</code>, deprecated</td>
            <td data-label="With a library">Tiptap or Quill</td>
            <td data-label="Verdict">Use the library</td>
          </tr>
          <tr>
            <td data-label="Job">Charts</td>
            <td data-label="Deck alone">CSS charts that retheme</td>
            <td data-label="With a library">Chart.js, themed by Deck</td>
            <td data-label="Verdict">CSS for tiles, Chart.js for real axes</td>
          </tr>
          <tr>
            <td data-label="Job">Drag and drop</td>
            <td data-label="Deck alone">Native HTML DnD, poor on touch</td>
            <td data-label="With a library">SortableJS</td>
            <td data-label="Verdict">Use the library</td>
          </tr>
          <tr>
            <td data-label="Job">Icons</td>
            <td data-label="Deck alone">74-icon sprite</td>
            <td data-label="With a library">Lucide, 1500 icons</td>
            <td data-label="Verdict">Sprite is enough for Deck; Lucide for the rest</td>
          </tr>
          <tr>
            <td data-label="Job">Long lists</td>
            <td data-label="Deck alone"><code>content-visibility</code></td>
            <td data-label="With a library">A virtualizer, 5 KB</td>
            <td data-label="Verdict">Keep the browser; it doesn't break find-in-page</td>
          </tr>
          <tr>
            <td data-label="Job">Dates and locales</td>
            <td data-label="Deck alone"><code>Intl</code>, built in</td>
            <td data-label="With a library">date-fns or similar</td>
            <td data-label="Verdict">Keep <code>Intl</code></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="alert alert-info">
      <svg class="icon"><use href="assets/deck/deck-icons.svg#info"></use></svg>
      <div>
        <div class="alert-title">What is running right now</div>
        <p class="alert-body">Call <code>Deck.adapters.report()</code> in the console. It
          names what is actually doing each job on this page, which is what you want when
          a component behaves differently between two environments.</p>
        <button class="btn btn-sm mt-2" onclick="showReport()">Show the report</button>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Anchored tooltips and popovers — these flip and shift on their own</span>
      <div class="cluster">
        <button class="btn" popovertarget="tip1">Hover-free tooltip</button>
        <div class="tip" id="tip1" popover>Placement is native. Scroll the page to the edge and it flips instead of running off.<span class="tip-arrow"></span></div>
        <button class="btn" popovertarget="pop1">Explain operation 0331</button>
        <div class="pop" id="pop1" popover>
          <div class="pop-title">Operation 0331</div>
          <p class="pop-body">Transmission cooler line replacement. Allowance 2.8 hours,
            includes fluid fill and a road test.</p>
          <div class="pop-actions">
            <button class="btn btn-sm btn-primary" popovertarget="pop1" popovertargetaction="hide">Got it</button>
            <button class="btn btn-sm">Open the manual</button>
          </div>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Kanban with drag reordering</span>
      <div class="kanban">
        <div class="kanban-col">
          <div class="kanban-head">Submitted <span class="kanban-count">3</span></div>
          <div class="kanban-body" data-deck-sortable="claims" data-handle=".drag-handle">
            <div class="kanban-card" data-id="88214">
              <div class="bar"><span class="kanban-card-title grow">88214 · Cooler line</span><span class="drag-handle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-vertical-sm"></use></svg></span></div>
              <div class="kanban-card-meta"><span class="indicator indicator-warn"></span> 2021 F-150 · $842.16</div>
            </div>
            <div class="kanban-card" data-id="88301">
              <div class="bar"><span class="kanban-card-title grow">88301 · Liftgate strut</span><span class="drag-handle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-vertical-sm"></use></svg></span></div>
              <div class="kanban-card-meta"><span class="indicator indicator-warn"></span> 2023 Transit · $321.40</div>
            </div>
            <div class="kanban-card" data-id="88344">
              <div class="bar"><span class="kanban-card-title grow">88344 · Battery cable</span><span class="drag-handle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-vertical-sm"></use></svg></span></div>
              <div class="kanban-card-meta"><span class="indicator indicator-warn"></span> 2022 Mach-E · $300.75</div>
            </div>
            <div class="kanban-empty">Drop a claim here</div>
          </div>
        </div>
        <div class="kanban-col">
          <div class="kanban-head">In review <span class="kanban-count">1</span></div>
          <div class="kanban-body" data-deck-sortable="claims" data-handle=".drag-handle">
            <div class="kanban-card" data-id="88220">
              <div class="bar"><span class="kanban-card-title grow">88220 · BCM reprogram</span><span class="drag-handle"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#more-vertical-sm"></use></svg></span></div>
              <div class="kanban-card-meta"><span class="indicator indicator-brand"></span> 2023 Explorer · $318.00</div>
            </div>
            <div class="kanban-empty">Drop a claim here</div>
          </div>
        </div>
        <div class="kanban-col">
          <div class="kanban-head">Paid <span class="kanban-count">0</span></div>
          <div class="kanban-body" data-deck-sortable="claims" data-handle=".drag-handle">
            <div class="kanban-empty">Drop a claim here</div>
          </div>
        </div>
      </div>
    </div>

    <div class="stack-3">
      <span class="demo-label">Virtualized grid — 5,000 rows, no virtualization library</span>
      <p class="text-muted text-sm">One line of CSS. Off-screen rows are skipped during
        layout, style, and paint, but they stay in the DOM — so Ctrl+F still finds them
        and the print stylesheet still prints them, which a JavaScript virtualizer breaks.</p>
      <div class="dg-wrap" data-deck-grid style="--dg-height:320px">
        <table class="dg dg-virtual dg-compact dg-zebra">
          <thead><tr>
            <th class="dg-pin-start" data-sort="text">Claim</th>
            <th>VIN</th><th>Vehicle</th><th>Advisor</th><th>Status</th>
            <th class="dg-num" data-sort="num">Total</th>
          </tr></thead>
          <tbody id="bigRows"></tbody>
        </table>
      </div>
      <span class="text-sm text-muted" id="rowStat"></span>
    </div>

    <div class="stack-3">
      <span class="demo-label">Datepicker localization — <code>Intl</code>, not a date library</span>
      <div class="grid grid-tight">
        <div class="field">
          <label class="label" for="dl1">English (US)</label>
          <div class="datefield" data-deck-datepicker data-locale="en-US">
            <input class="input" id="dl1" placeholder="Pick a date">
          </div>
        </div>
        <div class="field">
          <label class="label" for="dl2">Español (México)</label>
          <div class="datefield" data-deck-datepicker data-locale="es-MX" data-format="dmy">
            <input class="input" id="dl2" placeholder="Elige una fecha">
          </div>
        </div>
        <div class="field">
          <label class="label" for="dl3">Deutsch — week starts Monday</label>
          <div class="datefield" data-deck-datepicker data-locale="de-DE" data-format="dmy">
            <input class="input" id="dl3" placeholder="Datum wählen">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== Print ===================== -->
  <section class="container section stack-6" id="print">
    <div class="stack-2">
      <h2>Print</h2>
      <p class="text-muted">Dealership work ends up on paper. Print this page and the
        nav, tab bar, buttons, toasts, and theme dock drop out; the grid unfreezes and
        prints every column; the mobile card fallbacks revert to real tables; dark mode
        is forced back to light.</p>
    </div>

    <div class="cluster">
      <button class="btn btn-primary" onclick="window.print()">
        <svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#file-sm"></use></svg>
        Print preview
      </button>
      <span class="badge">.page-break</span>
      <span class="badge">.keep-together</span>
      <span class="badge">.no-print</span>
      <span class="badge">.print-only</span>
      <span class="badge">.print-keep</span>
    </div>

    <div class="alert alert-info">
      <svg class="icon"><use href="assets/deck/deck-icons.svg#info"></use></svg>
      <div>
        <div class="alert-title">This line only exists on paper</div>
        <p class="alert-body">The block below is hidden on screen and appears in the
          print preview.</p>
      </div>
    </div>

    <div class="print-only panel">
      <h3>Claim summary — 88214</h3>
      <p>Printed from ClaimIQ on <span class="mono">2026-09-07</span>.</p>
    </div>
  </section>


  <!-- ===================== Footer ===================== -->
  <footer class="container section stack-4 text-muted">
    <hr>
    <div class="cluster cluster-between">
      <div class="cluster cluster-tight">
        <svg class="icon icon-fill" style="color:var(--brand)"><use href="assets/deck/deck-icons.svg#deck-mark"></use></svg>
        <span class="fw-semi text-inherit">Deck</span>
        <span class="text-sm">for Keel and Helm</span>
      </div>
      <div class="cluster cluster-tight text-sm">
        <span class="badge" id="lineCount">CSS + optional JS</span>
        <span class="badge">0 dependencies</span>
      </div>
    </div>
  </footer>
</main>

<!-- ===================== Mobile tab bar ===================== -->
<div class="speed-dial">
  <div class="speed-dial-actions">
    <div class="speed-dial-action"><span class="speed-dial-label">New claim</span><button class="speed-dial-button" aria-label="New claim"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#clipboard-sm"></use></svg></button></div>
    <div class="speed-dial-action"><span class="speed-dial-label">Look up VIN</span><button class="speed-dial-button" aria-label="Look up VIN"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#search-sm"></use></svg></button></div>
    <div class="speed-dial-action"><span class="speed-dial-label">Upload photos</span><button class="speed-dial-button" aria-label="Upload photos"><svg class="icon icon-sm"><use href="assets/deck/deck-icons.svg#camera-sm"></use></svg></button></div>
  </div>
  <button class="fab" aria-label="Quick actions" aria-expanded="false"><svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#plus"></use></svg></button>
</div>

<a class="back-to-top" href="#top" aria-label="Back to top">
  <svg class="icon"><use href="assets/deck/deck-icons.svg#chevron-up"></use></svg>
</a>

<nav class="tabbar" aria-label="Main">
  <a class="tabbar-item" aria-current="page" href="#main">
    <svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#home"></use></svg> Home
  </a>
  <a class="tabbar-item" href="#forms">
    <svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#clipboard"></use></svg> Claims
  </a>
  <a class="tabbar-item" href="#mobile">
    <svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#chart"></use></svg> Reports
  </a>
  <a class="tabbar-item" href="#main">
    <svg class="icon icon-lg"><use href="assets/deck/deck-icons.svg#user"></use></svg> Account
  </a>
</nav>

<div class="toast-region" id="toasts"></div>

<script>
  /* deck.js is deferred, so Deck only exists once parsing finishes. This
     page code runs on DOMContentLoaded, which is after every deferred script.
     Note the library has already self-initialised by then, so this is too late
     to configure it -- deck.js resolves the icon sprite from its own URL, and
     data-deck-icons on the script tag overrides it. This init() is just a
     rescan; it is idempotent, guarded by data-deck-wired. */
  document.addEventListener('DOMContentLoaded', function () {
    Deck.init();

    // Theme switch
    const btn = document.getElementById('themeBtn');
    btn.addEventListener('click', () => {
      const dark = Deck.theme() === 'dark';
      Deck.theme(dark ? 'light' : 'dark');
      btn.querySelector('use').setAttribute('href', 'assets/deck/deck-icons.svg#' + (dark ? 'moon' : 'sun'));
    });

    // Indeterminate checkbox demo
    document.getElementById('indet').indeterminate = true;

    // Older buttons in this page call toast(); route them through the queue
    window.toast = function toast(message, kind) { Deck.toast({ title: message, kind: kind || '' }); };

    // Loading toast that resolves into a success toast
    window.demoProgress = function demoProgress() {
      const t = Deck.toast({ kind: 'loading', title: 'Submitting claim 88410', duration: 0, dismissible: false });
      setTimeout(() => t.update({ kind: 'good', title: 'Claim 88410 submitted', text: 'Review usually takes two to four days.', duration: 5000, dismissible: true }), 2200);
    };

    // Density toggle on the grid demo
    document.querySelector('#grid .segmented').addEventListener('deck:change', e => {
      document.querySelector('#grid .dg').classList.toggle('dg-compact', e.detail.value === 'Compact');
    });

    // Motion demos
    window.replayEntrances = function replayEntrances() {
      document.querySelectorAll('#entranceDemo > .card').forEach(c => {
        c.classList.remove('enter-rise');
        void c.offsetWidth;
        c.classList.add('enter-rise');
      });
    };
    window.bumpTotal = function bumpTotal() {
      const el = document.getElementById('total');
      const now = parseFloat(el.textContent.replace(/[^0-9.]/g, ''));
      const next = now + (Math.random() > 0.5 ? 1 : -1) * (40 + Math.random() * 300);
      el.textContent = '$' + next.toFixed(2);
    };

    // Direction label
    const dirLabel = document.getElementById('dirLabel');
    if (dirLabel) {
      new MutationObserver(() => { dirLabel.textContent = Deck.dir(); })
        .observe(document.documentElement, { attributes: true, attributeFilter: ['dir'] });
      dirLabel.textContent = Deck.dir();
    }

    // Adapter report
    window.showReport = function showReport() {
      const r = Deck.adapters.report();
      Deck.toast({
        kind: 'info',
        title: 'Doing the work right now',
        text: Object.entries(r).map(([k, v]) => k + ': ' + v).join(' · '),
        duration: 12000
      });
    };

    // 5,000 rows, rendered as plain DOM and skipped by content-visibility
    (function () {
      const body = document.getElementById('bigRows');
      if (!body) return;
      const t0 = performance.now();
      const vehicles = ['2021 F-150 XLT','2023 Explorer ST','2020 Escape SE','2023 Transit 250','2022 Mustang Mach-E'];
      const advisors = ['Rissa Molina','Ken Spence','Dana Whitfield','Marco Reyes','Tina Okafor'];
      const statuses = [['good','Approved'],['warn','Pending'],['bad','Denied']];
      const frag = document.createDocumentFragment();
      for (let i = 0; i < 5000; i++) {
        const [kind, label] = statuses[i % 3];
        const tr = document.createElement('tr');
        tr.innerHTML =
          '<td class="dg-pin-start"><a href="#libs">' + (88000 + i) + '</a></td>' +
          '<td class="mono">1FTFW1E85MFA' + String(10000 + i).slice(-5) + '</td>' +
          '<td>' + vehicles[i % 5] + '</td>' +
          '<td>' + advisors[i % 5] + '</td>' +
          '<td><span class="badge badge-' + kind + '">' + label + '</span></td>' +
          '<td class="dg-num">$' + ((i * 37) % 1900 + 100).toFixed(2) + '</td>';
        frag.append(tr);
      }
      body.append(frag);
      const ms = (performance.now() - t0).toFixed(0);
      document.getElementById('rowStat').textContent =
        '5,000 rows built and inserted in ' + ms + ' ms. Try Ctrl+F for a claim number near the bottom.';
    })();

    // Reactions
    document.querySelectorAll('.reaction').forEach(r => {
      r.addEventListener('click', () => {
        r.setAttribute('aria-pressed', r.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
      });
    });
  });
</script>
</body>
</html>