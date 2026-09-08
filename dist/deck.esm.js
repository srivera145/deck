/* Deck v0.1.0 — ES module entry.
   The scripts attach Deck to globalThis; this re-exports it so that
   `import Deck from '@echodial/deck'` behaves the way you expect. */
/*! ==========================================================================
 *  deck.js v0.1 — behaviour for Deck
 *  No dependencies. No build step. Drop it in and it wires itself up.
 *
 *    <script src="/assets/deck/deck.js" defer></script>
 *
 *  deck-icons.svg is expected to sit beside this file.
 *
 *  Anything with a data-deck-* attribute is initialised on DOMContentLoaded
 *  and again whenever you call Deck.init(container) after injecting HTML.
 *  ======================================================================== */

const Deck = (() => {
  'use strict';

  /* deck-icons.svg ships next to deck.js, so resolve it against this script's
     own URL and the sprite follows the bundle wherever it is installed. Read
     it here, at script-execution time, while document.currentScript is still
     set. Override per page with data-deck-icons on the script tag; setting
     Deck.iconSprite later only affects icons rendered after that point. */
  const spriteURL = (() => {
    const tag = typeof document !== 'undefined' ? document.currentScript : null;
    if (tag && tag.dataset.deckIcons) return tag.dataset.deckIcons;
    if (tag && tag.src) {
      try { return new URL('deck-icons.svg', tag.src).href; } catch (e) { }
    }
    return '/assets/deck-icons.svg';
  })();

  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const el = (tag, cls, html) => {
    const n = document.createElement(tag);
    if (cls) n.className = cls;
    if (html != null) n.innerHTML = html;
    return n;
  };
  const icon = (name, cls = 'icon') =>
    `<svg class="${cls}" aria-hidden="true"><use href="${Deck.iconSprite}#${name}"></use></svg>`;

  /* ======================================================================
     Date picker
     <div class="datefield" data-deck-datepicker
          data-mode="single|range" data-format="mdy|dmy|iso"
          data-min="2024-01-01" data-max="2026-12-31" data-presets>
       <input class="input" name="claim_date" readonly>
     </div>
     ====================================================================== */

  const DAY = 86400000;
  const iso = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
  const parseISO = s => {
    if (!s) return null;
    const [y, m, d] = s.split('-').map(Number);
    return (y && m && d) ? new Date(y, m - 1, d) : null;
  };
  const sameDay = (a, b) => a && b && a.toDateString() === b.toDateString();
  const startOfDay = d => new Date(d.getFullYear(), d.getMonth(), d.getDate());
  const addMonths = (d, n) => new Date(d.getFullYear(), d.getMonth() + n, 1);

  /* Month and weekday names come from Intl rather than a hardcoded English
     array or a date library. It is built into every browser, it is correct in
     every locale, and it costs nothing to ship. Set data-locale on the field
     to override the document language. */
  const nameCache = new Map();
  function names(locale) {
    const key = locale || 'auto';
    if (nameCache.has(key)) return nameCache.get(key);
    const loc = locale || document.documentElement.lang || navigator.language || 'en';
    const month = new Intl.DateTimeFormat(loc, { month: 'long' });
    const monthShort = new Intl.DateTimeFormat(loc, { month: 'short' });
    const day = new Intl.DateTimeFormat(loc, { weekday: 'short' });
    const set = {
      locale: loc,
      months: Array.from({ length: 12 }, (_, i) => month.format(new Date(2021, i, 1))),
      monthsShort: Array.from({ length: 12 }, (_, i) => monthShort.format(new Date(2021, i, 1))),
      // 2021-08-01 was a Sunday, so this walks Sunday through Saturday
      days: Array.from({ length: 7 }, (_, i) => day.format(new Date(2021, 7, 1 + i))),
      // First day of the week per locale: Sunday in the US, Monday in the EU
      weekStart: (() => {
        try {
          const info = new Intl.Locale(loc).getWeekInfo?.() ||
                       new Intl.Locale(loc).weekInfo;
          return info ? (info.firstDay === 7 ? 0 : info.firstDay) : 0;
        } catch (e) { return 0; }
      })(),
      long: new Intl.DateTimeFormat(loc, { dateStyle: 'medium' }),
      full: new Intl.DateTimeFormat(loc, { dateStyle: 'full' })
    };
    nameCache.set(key, set);
    return set;
  }

  function formatDate(d, fmt, locale) {
    if (!d) return '';
    if (fmt === 'iso') return iso(d);
    if (fmt === 'long') return names(locale).long.format(d);
    const y = d.getFullYear(), m = d.getMonth() + 1, day = d.getDate();
    const p = n => String(n).padStart(2, '0');
    if (fmt === 'dmy') return `${p(day)}/${p(m)}/${y}`;
    if (fmt === 'ymd') return `${y}-${p(m)}-${p(day)}`;
    return `${p(m)}/${p(day)}/${y}`;
  }

  class DatePicker {
    constructor(root) {
      this.root = root;
      this.input = $('input', root);
      this.mode = root.dataset.mode || 'single';
      this.format = root.dataset.format || 'mdy';
      this.min = parseISO(root.dataset.min);
      this.max = parseISO(root.dataset.max);
      this.showPresets = root.hasAttribute('data-presets');
      this.locale = root.dataset.locale || null;
      this.i18n = names(this.locale);
      this.weekStart = root.dataset.weekStart != null
        ? Number(root.dataset.weekStart)
        : this.i18n.weekStart;
      this.months = Number(root.dataset.months || (this.mode === 'range' ? 2 : 1));

      this.start = parseISO(this.input.value) || null;
      this.end = null;
      this.view = startOfDay(this.start || new Date());
      this.view.setDate(1);
      this.jump = null;

      this.build();
      this.bind();
      this.render();
    }

    build() {
      const r = this.root;
      if (!$('.icon', r)) r.insertAdjacentHTML('afterbegin', icon('calendar'));
      if (!$('.datefield-clear', r)) {
        r.insertAdjacentHTML('beforeend',
          `<button type="button" class="datefield-clear" aria-label="Clear date">${icon('x', 'icon icon-sm')}</button>`);
      }
      this.input.readOnly = true;
      this.input.setAttribute('aria-haspopup', 'dialog');

      this.id = 'dp-' + Math.random().toString(36).slice(2, 8);
      this.panel = el('div', 'datepicker');
      this.panel.id = this.id;
      this.panel.setAttribute('popover', '');
      document.body.append(this.panel);
      this.input.setAttribute('popovertarget', this.id);
      this.input.style.cursor = 'pointer';
    }

    bind() {
      this.input.addEventListener('click', () => this.panel.showPopover());
      this.input.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
          e.preventDefault();
          this.panel.showPopover();
        }
      });
      $('.datefield-clear', this.root).addEventListener('click', e => {
        e.stopPropagation();
        this.start = this.end = null;
        this.commit();
        this.render();
      });
      this.panel.addEventListener('toggle', e => {
        if (e.newState === 'open') this.position();
      });
      this.panel.addEventListener('click', e => this.onClick(e));
      this.panel.addEventListener('mouseover', e => {
        const day = e.target.closest('.datepicker-day');
        if (this.mode !== 'range' || !this.start || this.end || !day) return;
        this.hover = parseISO(day.dataset.date);
        this.paintRange();
      });
      window.addEventListener('resize', () => {
        if (this.panel.matches(':popover-open')) this.position();
      });
    }

    position() {
      if (window.innerWidth <= 480) return; // becomes a sheet in CSS
      const r = this.root.getBoundingClientRect();
      const p = this.panel;
      p.style.insetInlineStart = Math.max(8, Math.min(r.left, window.innerWidth - p.offsetWidth - 8)) + 'px';
      const below = window.innerHeight - r.bottom;
      p.style.insetBlockStart = (below > p.offsetHeight + 12 || below > r.top)
        ? (r.bottom + 6) + 'px'
        : Math.max(8, r.top - p.offsetHeight - 6) + 'px';
    }

    blocked(d) {
      return (this.min && d < this.min) || (this.max && d > this.max);
    }

    onClick(e) {
      const day = e.target.closest('.datepicker-day');
      if (day) return this.pick(parseISO(day.dataset.date));

      const nav = e.target.closest('[data-nav]');
      if (nav) {
        this.view = addMonths(this.view, Number(nav.dataset.nav));
        return this.render();
      }
      const title = e.target.closest('.datepicker-title');
      if (title) { this.jump = this.jump ? null : 'month'; return this.render(); }

      const jm = e.target.closest('[data-month]');
      if (jm) {
        this.view = new Date(this.view.getFullYear(), Number(jm.dataset.month), 1);
        this.jump = null;
        return this.render();
      }
      const preset = e.target.closest('[data-preset]');
      if (preset) {
        const n = Number(preset.dataset.preset);
        const today = startOfDay(new Date());
        if (this.mode === 'range') {
          this.end = today;
          this.start = n === 0 ? today : new Date(today - (n - 1) * DAY);
        } else {
          this.start = new Date(today - n * DAY);
          this.end = null;
        }
        this.commit();
        this.render();
        if (this.mode !== 'range') this.panel.hidePopover();
        return;
      }
      if (e.target.closest('[data-apply]')) { this.commit(); this.panel.hidePopover(); }
      if (e.target.closest('[data-cancel]')) this.panel.hidePopover();
    }

    pick(d) {
      if (!d || this.blocked(d)) return;
      if (this.mode === 'range') {
        if (!this.start || this.end) { this.start = d; this.end = null; }
        else if (d < this.start) { this.end = this.start; this.start = d; }
        else this.end = d;
      } else {
        this.start = d;
        this.end = null;
      }
      this.commit();
      this.render();
      if (this.mode !== 'range' || this.end) {
        setTimeout(() => this.panel.hidePopover(), 120);
      }
    }

    commit() {
      const f = d => formatDate(d, this.format, this.locale);
      this.input.value = this.mode === 'range'
        ? (this.start ? f(this.start) + (this.end ? ' – ' + f(this.end) : '') : '')
        : f(this.start);
      this.root.classList.toggle('has-value', !!this.input.value);
      this.input.dispatchEvent(new CustomEvent('deck:change', {
        bubbles: true,
        detail: { start: this.start ? iso(this.start) : null, end: this.end ? iso(this.end) : null }
      }));
    }

    monthHTML(base) {
      const first = new Date(base.getFullYear(), base.getMonth(), 1);
      let lead = (first.getDay() - this.weekStart + 7) % 7;
      const cells = [];
      const cursor = new Date(first);
      cursor.setDate(1 - lead);
      for (let i = 0; i < 42; i++) {
        cells.push(new Date(cursor));
        cursor.setDate(cursor.getDate() + 1);
        if (i >= 34 && cursor.getMonth() !== base.getMonth() && cursor.getDay() === this.weekStart) break;
      }
      const all = this.i18n.days;
      const dow = all.slice(this.weekStart).concat(all.slice(0, this.weekStart));
      const today = startOfDay(new Date());

      return `<div class="datepicker-month">
        <div class="datepicker-grid">${dow.map(d => `<div class="datepicker-dow">${d}</div>`).join('')}</div>
        <div class="datepicker-grid">${cells.map(d => {
          const cls = ['datepicker-day'];
          if (d.getMonth() !== base.getMonth()) cls.push('is-outside');
          if (sameDay(d, today)) cls.push('is-today');
          if (this.blocked(d)) cls.push('is-blocked');
          return `<button type="button" class="${cls.join(' ')}" data-date="${iso(d)}"
                    ${this.blocked(d) ? 'disabled' : ''}
                    aria-label="${this.i18n.full.format(d)}">${d.getDate()}</button>`;
        }).join('')}</div>
      </div>`;
    }

    render() {
      const presets = this.showPresets ? `
        <div class="datepicker-presets">
          ${(this.mode === 'range'
            ? [['Today', 0], ['Last 7 days', 7], ['Last 30 days', 30], ['Last 90 days', 90]]
            : [['Today', 0], ['Yesterday', 1], ['A week ago', 7], ['A month ago', 30]]
          ).map(([label, n]) =>
            `<button type="button" class="datepicker-preset" data-preset="${n}">${label}</button>`).join('')}
        </div>` : '';

      const body = this.jump
        ? `<div class="datepicker-jump">${this.i18n.monthsShort.map((m, i) =>
             `<button type="button" data-month="${i}" aria-selected="${i === this.view.getMonth()}">${m}</button>`).join('')}</div>`
        : `<div class="datepicker-months">${Array.from({ length: this.months },
             (_, i) => this.monthHTML(addMonths(this.view, i))).join('')}</div>`;

      const label = this.months > 1 && !this.jump
        ? `${this.i18n.monthsShort[this.view.getMonth()]} – ${this.i18n.monthsShort[addMonths(this.view, this.months - 1).getMonth()]} ${this.view.getFullYear()}`
        : `${this.i18n.months[this.view.getMonth()]} ${this.view.getFullYear()}`;

      const f = d => formatDate(d, this.format, this.locale);
      const readout = this.mode === 'range'
        ? (this.start ? `${f(this.start)} – ${this.end ? f(this.end) : '…'}` : 'Pick a start date')
        : (this.start ? f(this.start) : 'No date selected');

      this.panel.innerHTML = `
        <div class="datepicker-layout">
          ${presets}
          <div class="datepicker-main">
            <div class="datepicker-head">
              <button type="button" class="datepicker-nav" data-nav="-1" aria-label="Previous month">${icon('chevron-left', 'icon icon-sm')}</button>
              <button type="button" class="datepicker-title">${label} ${icon('chevron-down', 'icon icon-sm')}</button>
              <button type="button" class="datepicker-nav" data-nav="1" aria-label="Next month">${icon('chevron-right', 'icon icon-sm')}</button>
            </div>
            ${body}
          </div>
        </div>
        <div class="datepicker-foot">
          <span class="datepicker-readout">${readout}</span>
          <button type="button" class="btn btn-sm" data-cancel>Cancel</button>
          <button type="button" class="btn btn-sm btn-primary" data-apply>Apply</button>
        </div>`;

      this.paintRange();
      this.root.classList.toggle('has-value', !!this.input.value);
    }

    paintRange() {
      $$('.datepicker-day', this.panel).forEach(node => {
        const d = parseISO(node.dataset.date);
        const end = this.end || (this.mode === 'range' && this.start && this.hover > this.start ? this.hover : null);
        node.classList.toggle('is-selected', sameDay(d, this.start) || sameDay(d, this.end));
        node.classList.toggle('is-range-start', !!end && sameDay(d, this.start));
        node.classList.toggle('is-range-end', !!end && sameDay(d, end));
        node.classList.toggle('is-in-range', !!(end && this.start && d > this.start && d < end));
      });
    }
  }

  /* ======================================================================
     Combobox / autocomplete
     <div class="combo" data-deck-combo data-multi data-create
          data-placeholder="Search VINs">
       <select hidden multiple>… your real options …</select>
     </div>
     The <select> stays in the DOM and stays in sync, so normal form posts
     work with no extra server-side handling.
     ====================================================================== */

  class Combo {
    constructor(root) {
      this.root = root;
      this.select = $('select', root);
      this.multi = root.hasAttribute('data-multi') || (this.select && this.select.multiple);
      this.allowCreate = root.hasAttribute('data-create');
      this.placeholder = root.dataset.placeholder || 'Search';
      this.remote = root.dataset.url || null;
      this.minChars = Number(root.dataset.minChars || 0);
      this.open = false;
      this.activeIndex = -1;
      this.query = '';

      this.options = this.select
        ? $$('option', this.select).map(o => ({
            value: o.value,
            label: o.textContent.trim(),
            sub: o.dataset.sub || '',
            group: o.dataset.group || (o.parentElement.tagName === 'OPTGROUP' ? o.parentElement.label : ''),
            disabled: o.disabled,
            selected: o.selected
          }))
        : [];

      if (this.select) this.select.hidden = true;
      this.build();
      this.bind();
      this.renderTokens();
    }

    get selected() { return this.options.filter(o => o.selected); }

    build() {
      this.control = el('div', 'combo-control');
      this.control.innerHTML = `
        <span class="combo-tokens"></span>
        <input class="combo-input" type="text" role="combobox" autocomplete="off"
               aria-expanded="false" aria-autocomplete="list" placeholder="${this.placeholder}">
        <button type="button" class="combo-toggle" tabindex="-1" aria-label="Toggle list">${icon('chevron-down', 'icon icon-sm')}</button>`;
      this.list = el('div', 'combo-list');
      this.list.setAttribute('role', 'listbox');
      this.root.append(this.control, this.list);
      this.input = $('.combo-input', this.control);
      this.tokens = $('.combo-tokens', this.control);
    }

    bind() {
      this.control.addEventListener('mousedown', e => {
        if (e.target.closest('.combo-token > button')) return;
        if (e.target !== this.input) { e.preventDefault(); this.input.focus(); }
        if (e.target.closest('.combo-toggle')) this.toggle();
        else if (!this.open) this.show();
      });

      this.input.addEventListener('input', () => {
        this.query = this.input.value;
        this.activeIndex = 0;
        if (this.remote) this.fetch();
        else this.render();
        this.show();
      });

      this.input.addEventListener('keydown', e => this.onKey(e));
      this.input.addEventListener('blur', () => setTimeout(() => this.hide(), 130));

      this.list.addEventListener('mousedown', e => {
        const opt = e.target.closest('.combo-option');
        if (!opt) return;
        e.preventDefault();
        if (opt.dataset.create != null) this.create(this.query);
        else this.choose(opt.dataset.value);
      });
      this.list.addEventListener('mousemove', e => {
        const opt = e.target.closest('.combo-option');
        if (!opt) return;
        this.activeIndex = Number(opt.dataset.index);
        this.paintActive();
      });

      this.tokens.addEventListener('click', e => {
        const btn = e.target.closest('button');
        if (!btn) return;
        this.choose(btn.dataset.value, true);
      });
    }

    matches() {
      const q = this.query.trim().toLowerCase();
      if (q.length < this.minChars) return [];
      if (!q) return this.options;
      return this.options.filter(o =>
        o.label.toLowerCase().includes(q) || o.sub.toLowerCase().includes(q) || o.value.toLowerCase().includes(q));
    }

    render() {
      const list = this.matches();
      this.visible = list;

      if (!list.length) {
        this.list.innerHTML = this.allowCreate && this.query.trim()
          ? `<button type="button" class="combo-option" data-create data-index="0">${icon('plus', 'icon icon-sm')}
               <span class="combo-option-main"><span>Add “${escapeHTML(this.query.trim())}”</span></span></button>`
          : `<div class="combo-empty">${icon('search', 'icon icon-sm')} No matches for “${escapeHTML(this.query)}”</div>`;
        return;
      }

      let html = '';
      let group = null;
      list.forEach((o, i) => {
        if (o.group && o.group !== group) {
          group = o.group;
          html += `<div class="combo-group">${escapeHTML(group)}</div>`;
        }
        html += `<button type="button" class="combo-option" role="option" data-index="${i}"
                   data-value="${escapeHTML(o.value)}"
                   aria-selected="${o.selected}" ${o.disabled ? 'aria-disabled="true"' : ''}>
            <span class="combo-option-main">
              <span>${highlight(o.label, this.query)}</span>
              ${o.sub ? `<span class="combo-option-sub">${highlight(o.sub, this.query)}</span>` : ''}
            </span>
            ${this.multi ? `<span class="combo-check">${icon('check', 'icon icon-sm')}</span>` : ''}
          </button>`;
      });

      if (this.allowCreate && this.query.trim() && !list.some(o => o.label.toLowerCase() === this.query.trim().toLowerCase())) {
        html += `<div class="combo-create"><button type="button" class="combo-option" data-create data-index="${list.length}">
          ${icon('plus', 'icon icon-sm')}<span class="combo-option-main"><span>Add “${escapeHTML(this.query.trim())}”</span></span></button></div>`;
      }

      html += `<div class="combo-hint"><span><kbd>↑</kbd><kbd>↓</kbd> move</span><span><kbd>enter</kbd> select</span><span><kbd>esc</kbd> close</span></div>`;
      this.list.innerHTML = html;
      this.paintActive();
    }

    paintActive() {
      const nodes = $$('.combo-option', this.list);
      nodes.forEach(n => n.classList.remove('is-active'));
      const node = nodes[this.activeIndex];
      if (node) {
        node.classList.add('is-active');
        node.scrollIntoView({ block: 'nearest' });
      }
    }

    onKey(e) {
      const nodes = $$('.combo-option', this.list);
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        if (!this.open) this.show();
        const dir = e.key === 'ArrowDown' ? 1 : -1;
        this.activeIndex = (this.activeIndex + dir + nodes.length) % (nodes.length || 1);
        this.paintActive();
      } else if (e.key === 'Enter') {
        if (!this.open) return;
        e.preventDefault();
        const node = nodes[this.activeIndex];
        if (!node) return;
        if (node.dataset.create != null) this.create(this.query);
        else this.choose(node.dataset.value);
      } else if (e.key === 'Escape') {
        if (this.open) { e.stopPropagation(); this.hide(); }
      } else if (e.key === 'Backspace' && !this.input.value && this.multi) {
        const last = this.selected.at(-1);
        if (last) this.choose(last.value, true);
      }
    }

    choose(value, forceOff) {
      const opt = this.options.find(o => o.value === value);
      if (!opt || opt.disabled) return;
      if (this.multi) {
        opt.selected = forceOff ? false : !opt.selected;
      } else {
        this.options.forEach(o => { o.selected = false; });
        opt.selected = true;
        this.input.value = opt.label;
        this.query = '';
        this.hide();
      }
      if (this.multi) { this.input.value = ''; this.query = ''; }
      this.sync();
      this.renderTokens();
      this.render();
    }

    create(text) {
      const label = text.trim();
      if (!label) return;
      const o = { value: label, label, sub: '', group: '', disabled: false, selected: false };
      this.options.push(o);
      if (this.select) this.select.append(new Option(label, label));
      this.choose(label);
      this.root.dispatchEvent(new CustomEvent('deck:create', { bubbles: true, detail: { value: label } }));
    }

    renderTokens() {
      if (!this.multi) {
        const one = this.selected[0];
        if (one && document.activeElement !== this.input) this.input.value = one.label;
        this.tokens.innerHTML = '';
        return;
      }
      const max = Number(this.root.dataset.maxTokens || 4);
      const sel = this.selected;
      this.tokens.innerHTML = sel.slice(0, max).map(o =>
        `<span class="combo-token"><span>${escapeHTML(o.label)}</span>
          <button type="button" data-value="${escapeHTML(o.value)}" aria-label="Remove ${escapeHTML(o.label)}">${icon('x', 'icon icon-sm')}</button>
        </span>`).join('')
        + (sel.length > max ? `<span class="combo-count">+${sel.length - max}</span>` : '');
      this.input.placeholder = sel.length ? '' : this.placeholder;
    }

    sync() {
      if (!this.select) return;
      $$('option', this.select).forEach(o => {
        const match = this.options.find(x => x.value === o.value);
        o.selected = !!(match && match.selected);
      });
      this.select.dispatchEvent(new Event('change', { bubbles: true }));
      this.root.dispatchEvent(new CustomEvent('deck:change', {
        bubbles: true,
        detail: { values: this.selected.map(o => o.value) }
      }));
    }

    async fetch() {
      const q = this.query.trim();
      if (q.length < this.minChars) return;
      this.list.innerHTML = `<div class="combo-loading"><span class="spinner"></span> Searching</div>`;
      clearTimeout(this._t);
      this._t = setTimeout(async () => {
        try {
          const res = await window.fetch(this.remote + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
          const rows = await res.json();
          const keep = this.selected;
          this.options = rows.map(r => ({
            value: String(r.value ?? r.id),
            label: String(r.label ?? r.name),
            sub: r.sub || '',
            group: r.group || '',
            disabled: !!r.disabled,
            selected: keep.some(k => k.value === String(r.value ?? r.id))
          }));
          this.render();
        } catch (err) {
          this.list.innerHTML = `<div class="combo-empty">Could not load results. Try again.</div>`;
        }
      }, Number(this.root.dataset.debounce || 220));
    }

    show() {
      if (this.open) return;
      this.open = true;
      this.root.classList.add('is-open');
      this.input.setAttribute('aria-expanded', 'true');
      this.render();
      const r = this.control.getBoundingClientRect();
      this.list.classList.toggle('is-above',
        window.innerWidth > 480 && window.innerHeight - r.bottom < 240 && r.top > 240);
    }

    toggle() { this.open ? this.hide() : this.show(); }

    hide() {
      if (!this.open) return;
      this.open = false;
      this.root.classList.remove('is-open');
      this.input.setAttribute('aria-expanded', 'false');
      if (!this.multi) {
        const one = this.selected[0];
        this.input.value = one ? one.label : '';
      } else {
        this.input.value = '';
      }
      this.query = '';
    }
  }

  function escapeHTML(s) {
    return String(s).replace(/[&<>"']/g, c =>
      ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }
  function highlight(text, query) {
    const q = query.trim();
    const safe = escapeHTML(text);
    if (!q) return safe;
    const i = text.toLowerCase().indexOf(q.toLowerCase());
    if (i < 0) return safe;
    return escapeHTML(text.slice(0, i)) + '<mark>' + escapeHTML(text.slice(i, i + q.length)) + '</mark>' + escapeHTML(text.slice(i + q.length));
  }

  /* ======================================================================
     Data grid: pin shadows, sort, select-all, column resize
     <div class="dg-wrap" data-deck-grid> <table class="dg"> … </table> </div>
     Sortable header: <th data-sort="num|text|date">
     ====================================================================== */

  class Grid {
    constructor(wrap) {
      this.wrap = wrap;
      this.table = $('.dg', wrap);
      if (!this.table) return;
      this.measurePins();
      this.shadows();
      this.wireSort();
      this.wireSelect();
      this.wireResize();
      wrap.addEventListener('scroll', () => this.shadows(), { passive: true });
      new ResizeObserver(() => this.measurePins()).observe(this.table);
    }

    measurePins() {
      const first = $('thead .dg-pin-start', this.table);
      if (first) this.wrap.style.setProperty('--dg-pin-start', first.offsetWidth + 'px');
    }

    shadows() {
      const w = this.wrap;
      w.classList.toggle('is-scrolled-x', w.scrollLeft > 0);
      w.classList.toggle('is-scrolled-end', w.scrollLeft + w.clientWidth < w.scrollWidth - 1);
    }

    wireSort() {
      $$('th[data-sort]', this.table).forEach((th, col) => {
        if ($('.dg-sort', th)) return;
        const label = th.textContent.trim();
        th.innerHTML = `<button type="button" class="dg-sort">${escapeHTML(label)}
          <svg class="icon dg-sort-icon" aria-hidden="true"><use href="${Deck.iconSprite}#chevron-up"></use></svg></button>`;
        $('.dg-sort', th).addEventListener('click', () => this.sort(th));
      });
    }

    sort(th) {
      const head = $$('th', this.table.tHead.rows[0]);
      const index = head.indexOf(th);
      const kind = th.dataset.sort;
      const dir = th.getAttribute('aria-sort') === 'ascending' ? 'descending' : 'ascending';
      head.forEach(h => h.removeAttribute('aria-sort'));
      th.setAttribute('aria-sort', dir);

      const body = this.table.tBodies[0];
      const rows = Array.from(body.rows);
      const key = row => {
        const cell = row.cells[index];
        const raw = cell?.dataset.value ?? cell?.textContent.trim() ?? '';
        if (kind === 'num') return parseFloat(raw.replace(/[^0-9.\-]/g, '')) || 0;
        if (kind === 'date') return new Date(raw).getTime() || 0;
        return raw.toLowerCase();
      };
      rows.sort((a, b) => {
        const x = key(a), y = key(b);
        return (x < y ? -1 : x > y ? 1 : 0) * (dir === 'ascending' ? 1 : -1);
      });
      rows.forEach(r => body.append(r));
      this.wrap.dispatchEvent(new CustomEvent('deck:sort', { bubbles: true, detail: { index, dir, kind } }));
    }

    wireSelect() {
      const all = $('thead .dg-check input', this.table);
      const boxes = () => $$('tbody .dg-check input', this.table);
      const update = () => {
        const list = boxes();
        const on = list.filter(b => b.checked);
        list.forEach(b => b.closest('tr').setAttribute('aria-selected', b.checked));
        if (all) {
          all.checked = on.length > 0 && on.length === list.length;
          all.indeterminate = on.length > 0 && on.length < list.length;
        }
        const bar = $('[data-dg-selection]', this.wrap.parentElement || document);
        if (bar) {
          bar.hidden = on.length === 0;
          const count = $('[data-dg-count]', bar);
          if (count) count.textContent = on.length;
        }
        this.wrap.dispatchEvent(new CustomEvent('deck:select', { bubbles: true, detail: { count: on.length } }));
      };
      if (all) all.addEventListener('change', () => {
        boxes().forEach(b => { b.checked = all.checked; });
        update();
      });
      this.table.addEventListener('change', e => {
        if (e.target.closest('.dg-check')) update();
      });
      update();
    }

    wireResize() {
      $$('th[data-resize]', this.table).forEach(th => {
        if ($('.dg-resize', th)) return;
        th.style.position = 'sticky';
        const grip = el('div', 'dg-resize');
        th.append(grip);
        let x0 = 0, w0 = 0;
        const move = e => {
          const x = (e.touches ? e.touches[0].clientX : e.clientX);
          th.style.width = Math.max(64, w0 + (x - x0)) + 'px';
          this.measurePins();
        };
        const stop = () => {
          this.wrap.classList.remove('is-resizing');
          document.removeEventListener('pointermove', move);
          document.removeEventListener('pointerup', stop);
        };
        grip.addEventListener('pointerdown', e => {
          e.preventDefault();
          x0 = e.clientX;
          w0 = th.offsetWidth;
          this.wrap.classList.add('is-resizing');
          document.addEventListener('pointermove', move);
          document.addEventListener('pointerup', stop);
        });
      });
    }
  }

  /* ======================================================================
     Toast queue
       Deck.toast('Claim 88214 approved')
       Deck.toast({ title: 'Claim withdrawn', text: 'You can undo this.',
                    kind: 'warn', duration: 8000,
                    actions: [{ label: 'Undo', onClick: fn }] })
       const t = Deck.toast({ kind: 'loading', title: 'Submitting…', duration: 0 });
       t.update({ kind: 'good', title: 'Submitted', duration: 4000 });
     ====================================================================== */

  const Toaster = {
    region: null,
    items: [],
    max: 3,
    stacked: true,

    mount() {
      if (this.region) return this.region;
      this.region = $('.toast-region') || el('div', 'toast-region');
      if (this.stacked) this.region.classList.add('toast-region-stacked');
      this.region.setAttribute('role', 'region');
      this.region.setAttribute('aria-label', 'Notifications');
      if (!this.region.isConnected) document.body.append(this.region);
      return this.region;
    },

    icons: { good: 'check-circle', bad: 'x-circle', warn: 'alert-triangle', info: 'info', loading: null },

    push(opts) {
      if (typeof opts === 'string') opts = { title: opts };
      const o = Object.assign({ kind: '', duration: 5000, actions: [], dismissible: true }, opts);
      const region = this.mount();

      const node = el('div', 'toast' + (o.kind ? ' toast-' + o.kind : ''));
      node.setAttribute('role', o.kind === 'bad' ? 'alert' : 'status');
      node.style.setProperty('--toast-duration', o.duration + 'ms');

      const item = {
        node,
        opts: o,
        dismiss: () => this.dismiss(item),
        update: patch => this.update(item, patch)
      };

      this.paintBody(item);
      this.wireSwipe(item);

      region.prepend(node);
      this.items.unshift(item);
      requestAnimationFrame(() => {
        node.style.setProperty('--toast-h', node.offsetHeight + 'px');
        this.reflow();
      });

      if (o.duration > 0) this.arm(item);
      while (this.items.length > 8) this.dismiss(this.items.at(-1));
      return item;
    },

    paintBody(item) {
      const o = item.opts;
      const glyph = o.kind === 'loading'
        ? '<span class="spinner toast-icon"></span>'
        : (this.icons[o.kind] ? icon(this.icons[o.kind], 'icon toast-icon') : '');
      item.node.innerHTML = `
        ${glyph}
        <div class="toast-main">
          ${o.title ? `<div class="toast-title">${escapeHTML(o.title)}</div>` : ''}
          ${o.text ? `<div class="toast-text">${escapeHTML(o.text)}</div>` : ''}
          ${o.actions.length ? `<div class="toast-actions">${o.actions.map((a, i) =>
            `<button type="button" class="toast-action" data-action="${i}">${escapeHTML(a.label)}</button>`).join('')}</div>` : ''}
        </div>
        ${o.dismissible ? `<button type="button" class="toast-close" aria-label="Dismiss">${icon('x', 'icon icon-sm')}</button>` : ''}
        ${o.duration > 0 ? '<span class="toast-timer"></span>' : ''}`;

      $('.toast-close', item.node)?.addEventListener('click', () => this.dismiss(item));
      $$('.toast-action', item.node).forEach(btn => {
        btn.addEventListener('click', () => {
          const a = o.actions[Number(btn.dataset.action)];
          a?.onClick?.(item);
          if (a?.close !== false) this.dismiss(item);
        });
      });
    },

    update(item, patch) {
      Object.assign(item.opts, patch);
      item.node.className = 'toast' + (item.opts.kind ? ' toast-' + item.opts.kind : '');
      item.node.style.setProperty('--toast-duration', item.opts.duration + 'ms');
      this.paintBody(item);
      this.wireSwipe(item);
      clearTimeout(item.timer);
      if (item.opts.duration > 0) this.arm(item);
      return item;
    },

    arm(item) {
      clearTimeout(item.timer);
      item.timer = setTimeout(() => this.dismiss(item), item.opts.duration);
      const region = this.region;
      const pause = () => clearTimeout(item.timer);
      const resume = () => { if (item.opts.duration > 0) this.arm(item); };
      region.addEventListener('mouseenter', pause, { once: true });
      region.addEventListener('mouseleave', resume, { once: true });
    },

    dismiss(item) {
      if (!item || item.leaving) return;
      item.leaving = true;
      clearTimeout(item.timer);
      item.node.classList.add('is-leaving');
      item.node.addEventListener('transitionend', () => item.node.remove(), { once: true });
      setTimeout(() => item.node.remove(), 500);
      this.items = this.items.filter(i => i !== item);
      this.reflow();
      item.opts.onDismiss?.(item);
    },

    clear() { [...this.items].forEach(i => this.dismiss(i)); },

    reflow() {
      this.items.forEach((item, i) => {
        item.node.style.setProperty('--i', i);
        item.node.setAttribute('aria-hidden', i >= this.max ? 'true' : 'false');
      });
    },

    wireSwipe(item) {
      const node = item.node;
      let x0 = null, dx = 0;
      node.addEventListener('pointerdown', e => {
        if (e.target.closest('button')) return;
        x0 = e.clientX;
        node.classList.add('is-dragging');
        node.setPointerCapture(e.pointerId);
      });
      node.addEventListener('pointermove', e => {
        if (x0 === null) return;
        dx = e.clientX - x0;
        node.style.translate = `${dx}px 0`;
        node.style.opacity = String(Math.max(0, 1 - Math.abs(dx) / 220));
      });
      const end = () => {
        if (x0 === null) return;
        node.classList.remove('is-dragging');
        if (Math.abs(dx) > 90) {
          node.style.setProperty('--toast-exit-x', (dx > 0 ? 400 : -400) + 'px');
          this.dismiss(item);
        } else {
          node.style.translate = '';
          node.style.opacity = '';
        }
        x0 = null; dx = 0;
      };
      node.addEventListener('pointerup', end);
      node.addEventListener('pointercancel', end);
    }
  };

  /* ======================================================================
     Motion
     Ripples, a reveal fallback for browsers without scroll-driven animation,
     stagger indexes past the twelfth child, and view-transition helpers.
     ====================================================================== */

  const reduced = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function wireRipple(root) {
    $$('.ripple', root).forEach(node => {
      if (node.dataset.deckWired) return;
      node.dataset.deckWired = '1';
      node.addEventListener('pointerdown', e => {
        if (reduced()) return;
        const r = node.getBoundingClientRect();
        const ink = el('span', 'ripple-ink');
        ink.style.setProperty('--rx', (e.clientX - r.left) + 'px');
        ink.style.setProperty('--ry', (e.clientY - r.top) + 'px');
        node.append(ink);
        ink.addEventListener('animationend', () => ink.remove(), { once: true });
        setTimeout(() => ink.remove(), 900);
      });
    });
  }

  function wireReveal(root) {
    if (CSS.supports('animation-timeline: view()')) return;   // CSS handles it
    const targets = $$('.reveal, .reveal-fade, .reveal-pop', root)
      .filter(n => !n.dataset.deckWired);
    if (!targets.length) return;
    if (reduced()) return targets.forEach(n => { n.style.opacity = '1'; });

    const io = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-revealed');
        io.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });

    targets.forEach(n => { n.dataset.deckWired = '1'; io.observe(n); });
  }

  function wireStagger(root) {
    $$('.stagger', root).forEach(group => {
      const kids = Array.from(group.children);
      if (kids.length <= 12) return;             // pure CSS covers the first twelve
      kids.forEach((kid, i) => kid.style.setProperty('--n', i));
    });
  }

  function wireValidationShake(root) {
    $$('.field', root).forEach(field => {
      if (field.dataset.deckWired) return;
      field.dataset.deckWired = '1';
      field.addEventListener('animationend', () => field.classList.add('was-shaken'));
      $$('input, textarea, select', field).forEach(input => {
        input.addEventListener('input', () => {
          if (input.checkValidity()) {
            field.classList.remove('is-invalid', 'was-shaken');
          }
        });
      });
    });
  }

  function wireViewTransitions() {
    if (!('startViewTransition' in document)) return;
    // Give the browser a direction hint so back navigations slide the other way
    window.addEventListener('popstate', () => {
      document.documentElement.dataset.vt = 'back';
    });
    window.addEventListener('pageswap', () => {
      if (!performance.getEntriesByType('navigation')[0]) return;
      delete document.documentElement.dataset.vt;
    });
  }

  function wireTick(root) {
    $$('[data-deck-tick]', root).forEach(node => {
      if (node.dataset.deckWired) return;
      node.dataset.deckWired = '1';
      node.classList.add('tick');
      let last = parseFloat(node.textContent.replace(/[^0-9.\-]/g, '')) || 0;
      new MutationObserver(() => {
        const now = parseFloat(node.textContent.replace(/[^0-9.\-]/g, '')) || 0;
        if (now === last) return;
        node.classList.remove('is-up', 'is-down');
        void node.offsetWidth;                    // restart the animation
        node.classList.add(now > last ? 'is-up' : 'is-down');
        last = now;
      }).observe(node, { childList: true, characterData: true, subtree: true });
    });
  }

  /* ======================================================================
     Space: tilt, flip, coverflow
     ====================================================================== */

  function wireTilt(root) {
    if (!window.matchMedia('(hover: hover)').matches) return;
    $$('.tilt', root).forEach(node => {
      if (node.dataset.deckWired) return;
      node.dataset.deckWired = '1';
      const max = Number(node.dataset.tilt || 9);

      const move = e => {
        if (reduced()) return;
        const r = node.getBoundingClientRect();
        const px = (e.clientX - r.left) / r.width - 0.5;
        const py = (e.clientY - r.top) / r.height - 0.5;
        // one rotation about a computed axis, so it composes as a single value
        const amount = Math.hypot(px, py) * max * 2;
        node.style.setProperty('--tilt-axis', `${py.toFixed(3)} ${(-px).toFixed(3)} 0`);
        node.style.setProperty('--tilt-amount', amount.toFixed(2) + 'deg');
        node.style.setProperty('--gx', ((px + 0.5) * 100).toFixed(1) + '%');
        node.style.setProperty('--gy', ((py + 0.5) * 100).toFixed(1) + '%');
      };
      const rest = () => {
        node.classList.remove('is-tilting');
        node.style.setProperty('--tilt-amount', '0deg');
      };
      node.addEventListener('pointerenter', () => node.classList.add('is-tilting'));
      node.addEventListener('pointermove', move);
      node.addEventListener('pointerleave', rest);
      node.addEventListener('blur', rest);
    });
  }

  function wireFlip(root) {
    $$('[data-deck-flip]', root).forEach(trigger => {
      if (trigger.dataset.deckWired) return;
      trigger.dataset.deckWired = '1';
      const target = trigger.dataset.deckFlip
        ? $(trigger.dataset.deckFlip)
        : trigger.closest('.flip, .flip-x');
      if (!target) return;
      trigger.addEventListener('click', () => {
        const on = target.classList.toggle('is-flipped');
        trigger.setAttribute('aria-pressed', String(on));
        // the hidden face should not be reachable by keyboard
        $$('.flip-front, .flip-back', target).forEach((face, i) => {
          face.inert = on ? i === 0 : i === 1;
        });
      });
    });
  }

  function wireCoverflow(root) {
    $$('.coverflow', root).forEach(strip => {
      if (strip.dataset.deckWired) return;
      strip.dataset.deckWired = '1';
      const items = Array.from(strip.children);
      const paint = () => {
        const mid = strip.scrollLeft + strip.clientWidth / 2;
        const rtl = getComputedStyle(strip).direction === 'rtl';
        items.forEach(item => {
          const center = item.offsetLeft + item.offsetWidth / 2;
          const d = center - mid;
          item.classList.toggle('is-front', Math.abs(d) < item.offsetWidth / 2);
          item.classList.toggle('is-behind-start', d < -item.offsetWidth / 2 !== rtl && Math.abs(d) >= item.offsetWidth / 2);
          item.classList.toggle('is-behind-end', d > item.offsetWidth / 2 !== rtl && Math.abs(d) >= item.offsetWidth / 2);
        });
      };
      strip.addEventListener('scroll', () => requestAnimationFrame(paint), { passive: true });
      new ResizeObserver(paint).observe(strip);
      paint();
    });
  }

  function wireStack(root) {
    $$('.stack-depth', root).forEach(stack => {
      if (stack.dataset.deckWired) return;
      stack.dataset.deckWired = '1';
      const index = () => Array.from(stack.children)
        .filter(c => !c.classList.contains('is-dismissed'))
        .forEach((c, i) => c.style.setProperty('--i', i));
      index();
      stack.addEventListener('deck:advance', () => {
        const top = stack.querySelector(':scope > *:not(.is-dismissed)');
        if (!top) return;
        top.classList.add('is-dismissed');
        setTimeout(index, 40);
      });
    });
  }

  /* ======================================================================
     Small conveniences that pair with the CSS
     ====================================================================== */

  function wireGroups(root) {
    $$('.segmented, .tabs', root).forEach(group => {
      if (group.dataset.deckWired) return;
      group.dataset.deckWired = '1';
      group.addEventListener('click', e => {
        const item = e.target.closest('[aria-selected]');
        if (!item) return;
        $$('[aria-selected]', group).forEach(b => b.setAttribute('aria-selected', 'false'));
        item.setAttribute('aria-selected', 'true');
        group.dispatchEvent(new CustomEvent('deck:change', { bubbles: true, detail: { value: item.dataset.value ?? item.textContent.trim() } }));
      });
    });
  }

  /* Anchor positioning needs a unique anchor-name per pair. Rather than make
     the author invent one, pair up every popovertarget with its panel and
     generate it. Where the browser lacks anchor positioning this is inert and
     the Floating UI adapter or Deck's own placement takes over. */
  let anchorSeq = 0;
  function wireAnchors(root) {
    if (!CSS.supports('anchor-name: --a')) return;
    $$('[popovertarget]', root).forEach(trigger => {
      const panel = document.getElementById(trigger.getAttribute('popovertarget'));
      if (!panel || trigger.dataset.deckAnchored) return;
      trigger.dataset.deckAnchored = '1';
      const name = panel.style.getPropertyValue('--anchor') || `--dk-a${++anchorSeq}`;
      trigger.classList.add('anchor');
      trigger.style.setProperty('--anchor', name);
      panel.style.setProperty('--anchor', name);
      trigger.setAttribute('data-deck-anchor', '#' + panel.id);
    });
  }

  function wireTheme(root) {
    $$('[data-deck-theme]', root).forEach(btn => {
      if (btn.dataset.deckWired) return;
      btn.dataset.deckWired = '1';
      btn.addEventListener('click', () => {
        const dark = document.documentElement.getAttribute('data-theme') === 'dark';
        Deck.theme(dark ? 'light' : 'dark');
      });
    });
  }

  /* Fallback for browsers with no scroll-driven animations. The CSS above
     handles show and hide everywhere else, so this only runs where
     animation-timeline is missing -- and never as a scroll handler. A sentinel
     the height of the reveal threshold sits at the top of the document; when it
     leaves the viewport, we are past it. */
  let bttObserver = null;

  function wireBackToTop(root) {
    if (CSS.supports('animation-timeline: scroll()')) return;   // CSS handles it
    if (reduced()) return;             // CSS leaves the link visible; nothing to toggle
    const links = $$('.back-to-top', root).filter(n => !n.dataset.deckBtt);
    if (!links.length) return;
    /* data-deck-btt is both the once-guard and the CSS hook: without it the
       link stays visible, so a browser that runs no JS still gets a working
       one rather than an invisible one. */
    links.forEach(n => { n.dataset.deckBtt = '1'; });

    let sentinel = document.getElementById('dk-btt-sentinel');
    if (!sentinel) {
      sentinel = document.createElement('div');
      sentinel.id = 'dk-btt-sentinel';
      sentinel.setAttribute('aria-hidden', 'true');
      sentinel.style.cssText =
        'position:absolute;inset-block-start:0;inset-inline-start:0;' +
        'inline-size:1px;block-size:400px;visibility:hidden;pointer-events:none;';
      document.body.prepend(sentinel);
    }

    if (bttObserver) bttObserver.disconnect();
    bttObserver = new IntersectionObserver(entries => {
      const past = !entries[entries.length - 1].isIntersecting;
      $$('.back-to-top[data-deck-btt]').forEach(n => n.classList.toggle('is-visible', past));
    });
    bttObserver.observe(sentinel);
  }

  /* ====================================================================== */

  const Deck = {
    iconSprite: spriteURL,
    version: '0.1',

    init(root = document) {
      $$('[data-deck-datepicker]', root).forEach(n => { if (!n._deck) n._deck = new DatePicker(n); });
      $$('[data-deck-combo]', root).forEach(n => { if (!n._deck) n._deck = new Combo(n); });
      $$('[data-deck-grid]', root).forEach(n => { if (!n._deck) n._deck = new Grid(n); });
      wireGroups(root);
      wireAnchors(root);
      wireTheme(root);
      wireBackToTop(root);
      wireRipple(root);
      wireReveal(root);
      wireStagger(root);
      wireValidationShake(root);
      wireTick(root);
      wireTilt(root);
      wireFlip(root);
      wireCoverflow(root);
      wireStack(root);
      return Deck;
    },

    toast(opts) { return Toaster.push(opts); },
    toasts: Toaster,

    theme(mode) {
      if (!mode) return document.documentElement.getAttribute('data-theme') || 'auto';
      document.documentElement.setAttribute('data-theme', mode);
      try { localStorage.setItem('deck-theme', mode); } catch (e) {}
      return mode;
    },

    hue(n) { document.documentElement.style.setProperty('--hue-brand', n); },

    /* Locale-aware month, weekday, and first-day-of-week data from Intl. */
    locale(tag) { return names(tag); },

    /* Wrap a DOM change so the browser tweens between before and after.
       Falls back to running the change immediately where unsupported or where
       the person asked for reduced motion.
         Deck.transition(() => row.remove());
         Deck.transition(() => list.prepend(newRow), { name: 'row-42' });   */
    transition(update, opts = {}) {
      if (reduced() || !document.startViewTransition) {
        const out = update();
        return { finished: Promise.resolve(out), ready: Promise.resolve() };
      }
      if (opts.direction) document.documentElement.dataset.vt = opts.direction;
      const vt = document.startViewTransition(update);
      vt.finished.finally(() => { delete document.documentElement.dataset.vt; });
      return vt;
    },

    /* Play a one-shot animation class and clean up after itself.
         Deck.play(field, 'shake');
         await Deck.play(card, 'flash-good');                                */
    play(node, className) {
      if (!node) return Promise.resolve();
      node.classList.remove(className);
      void node.offsetWidth;
      node.classList.add(className, 'is-animating');
      return new Promise(resolve => {
        const done = () => {
          node.classList.remove(className, 'is-animating');
          resolve(node);
        };
        node.addEventListener('animationend', done, { once: true });
        setTimeout(done, 2000);
      });
    },

    /* Height:auto expand/collapse with no measuring.
         Deck.toggle(panel);                                                 */
    toggle(node, force) {
      const open = force ?? !node.classList.contains('is-open');
      node.classList.add('expand');
      node.classList.toggle('is-open', open);
      return open;
    },

    /* Read or set text direction. Deck is written in logical properties, so
       this is all that a full RTL flip requires.
         Deck.dir('rtl');                                                    */
    dir(value) {
      if (!value) return document.documentElement.getAttribute('dir') || 'ltr';
      document.documentElement.setAttribute('dir', value);
      try { localStorage.setItem('deck-dir', value); } catch (e) {}
      return value;
    },

    /* Flip a card, advance a depth stack, rotate a cube.                    */
    flip(node, force) {
      const on = node.classList.toggle('is-flipped', force);
      return on;
    },
    advance(stack) { stack.dispatchEvent(new CustomEvent('deck:advance')); },
    face(cube, name) { cube.dataset.face = name; return name; },

    reduced
  };

  wireViewTransitions();

  document.addEventListener('DOMContentLoaded', () => {
    try {
      const saved = localStorage.getItem('deck-theme');
      if (saved) document.documentElement.setAttribute('data-theme', saved);
      const dir = localStorage.getItem('deck-dir');
      if (dir) document.documentElement.setAttribute('dir', dir);
    } catch (e) {}
    Deck.init();
  });

  return Deck;
})();

/* Expose on the global so the optional scripts and page code can find Deck
   whether it was loaded as a classic script, a module, or from a bundler. */
if (typeof window !== 'undefined') window.Deck = Deck;
if (typeof globalThis !== 'undefined') globalThis.Deck = Deck;
if (typeof module !== 'undefined' && module.exports) module.exports = Deck;

/*! ==========================================================================
 *  deck-extras.js v0.1 — behaviour for the extended component set
 *  Loads after deck.js and registers itself with Deck.init.
 *  No dependencies, including the QR encoder.
 *  ======================================================================== */

(function (Deck) {
  'use strict';
  if (!Deck) { console.warn('deck-extras.js: load deck.js first'); return; }

  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const once = node => { if (node.dataset.deckX) return false; node.dataset.deckX = '1'; return true; };
  const reduced = () => matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ======================================================================
     Carousel
     <div class="carousel" data-deck-carousel data-autoplay="6000">
     ====================================================================== */

  function carousels(root) {
    $$('[data-deck-carousel]', root).forEach(car => {
      if (!once(car)) return;
      const track = $('.carousel-track', car);
      if (!track) return;
      const slides = $$('.carousel-slide', track);
      const dots = $('.carousel-dots', car);
      const prev = $('.carousel-prev', car);
      const next = $('.carousel-next', car);
      const rtl = () => getComputedStyle(track).direction === 'rtl';

      if (dots && !dots.children.length) {
        dots.innerHTML = slides.map((s, i) =>
          `<button type="button" class="carousel-dot" data-go="${i}" aria-label="Slide ${i + 1}"></button>`).join('');
      }

      const current = () => {
        const mid = track.scrollLeft + track.clientWidth / 2;
        let best = 0, bestD = Infinity;
        slides.forEach((s, i) => {
          const d = Math.abs(s.offsetLeft + s.offsetWidth / 2 - mid);
          if (d < bestD) { bestD = d; best = i; }
        });
        return best;
      };

      const go = i => {
        const s = slides[Math.max(0, Math.min(slides.length - 1, i))];
        if (!s) return;
        track.scrollTo({
          left: s.offsetLeft - (track.clientWidth - s.offsetWidth) / 2,
          behavior: reduced() ? 'auto' : 'smooth'
        });
      };

      const paint = () => {
        const i = current();
        if (dots) $$('.carousel-dot', dots).forEach((d, n) =>
          d.setAttribute('aria-current', String(n === i)));
        if (prev) prev.disabled = i === 0;
        if (next) next.disabled = i === slides.length - 1;
        car.dispatchEvent(new CustomEvent('deck:slide', { bubbles: true, detail: { index: i } }));
      };

      track.addEventListener('scroll', () => requestAnimationFrame(paint), { passive: true });
      prev?.addEventListener('click', () => go(current() - 1));
      next?.addEventListener('click', () => go(current() + 1));
      dots?.addEventListener('click', e => {
        const b = e.target.closest('[data-go]');
        if (b) go(Number(b.dataset.go));
      });

      car.addEventListener('keydown', e => {
        const back = rtl() ? 'ArrowRight' : 'ArrowLeft';
        const fwd = rtl() ? 'ArrowLeft' : 'ArrowRight';
        if (e.key === back) { e.preventDefault(); go(current() - 1); }
        if (e.key === fwd) { e.preventDefault(); go(current() + 1); }
      });

      const ms = Number(car.dataset.autoplay || 0);
      if (ms > 0 && !reduced()) {
        let timer = setInterval(tick, ms);
        function tick() {
          const i = current();
          go(i >= slides.length - 1 ? 0 : i + 1);
        }
        const stop = () => clearInterval(timer);
        const start = () => { stop(); timer = setInterval(tick, ms); };
        car.addEventListener('pointerenter', stop);
        car.addEventListener('pointerleave', start);
        car.addEventListener('focusin', stop);
        document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
      }

      paint();
      car.carousel = { go, current, paint };
    });
  }

  /* ======================================================================
     Drawer, mega menu, speed dial, banner
     ====================================================================== */

  function drawers(root) {
    $$('[data-deck-drawer]', root).forEach(trigger => {
      if (!once(trigger)) return;
      const target = $(trigger.dataset.deckDrawer);
      if (!target) return;
      trigger.addEventListener('click', () => target.showModal());
      // Click on the backdrop closes it; the dialog element itself does not
      target.addEventListener('click', e => {
        if (e.target === target) target.close();
      });
      $$('[data-drawer-close]', target).forEach(b =>
        b.addEventListener('click', () => target.close()));
    });
  }

  function megaMenus(root) {
    $$('[data-deck-mega]', root).forEach(trigger => {
      if (!once(trigger)) return;
      const panel = $(trigger.dataset.deckMega);
      if (!panel) return;
      const place = () => {
        const r = trigger.getBoundingClientRect();
        panel.style.insetBlockStart = (r.bottom + 8) + 'px';
        const w = panel.offsetWidth;
        const start = Math.min(Math.max(8, r.left + r.width / 2 - w / 2), innerWidth - w - 8);
        panel.style.insetInlineStart = start + 'px';
      };
      panel.addEventListener('toggle', e => { if (e.newState === 'open') place(); });
      addEventListener('resize', () => { if (panel.matches(':popover-open')) place(); });
      // Hover intent on pointer devices, click everywhere
      if (matchMedia('(hover: hover)').matches) {
        let t;
        const open = () => { clearTimeout(t); t = setTimeout(() => panel.showPopover(), 90); };
        const close = () => { clearTimeout(t); t = setTimeout(() => panel.hidePopover(), 180); };
        [trigger, panel].forEach(n => {
          n.addEventListener('pointerenter', open);
          n.addEventListener('pointerleave', close);
        });
      }
    });
  }

  function speedDials(root) {
    $$('.speed-dial', root).forEach(dial => {
      if (!once(dial)) return;
      const fab = $('.fab', dial);
      $$('.speed-dial-action', dial).forEach((a, i) => a.style.setProperty('--i', i));
      const set = on => {
        dial.classList.toggle('is-open', on);
        fab?.setAttribute('aria-expanded', String(on));
      };
      fab?.addEventListener('click', () => set(!dial.classList.contains('is-open')));
      document.addEventListener('click', e => {
        if (!dial.contains(e.target)) set(false);
      });
      dial.addEventListener('keydown', e => { if (e.key === 'Escape') { set(false); fab?.focus(); } });
    });
  }

  function banners(root) {
    $$('.banner', root).forEach(banner => {
      if (!once(banner)) return;
      const key = banner.dataset.dismissKey;
      if (key) {
        try { if (localStorage.getItem('deck-banner-' + key)) { banner.hidden = true; return; } } catch (e) {}
      }
      $('.banner-close', banner)?.addEventListener('click', () => {
        banner.hidden = true;
        if (key) { try { localStorage.setItem('deck-banner-' + key, '1'); } catch (e) {} }
      });
    });
  }

  /* ======================================================================
     Number, phone, range pair
     ====================================================================== */

  function numbers(root) {
    $$('.number', root).forEach(box => {
      if (!once(box)) return;
      const input = $('input', box);
      const step = Number(input.step) || 1;
      const min = input.min === '' ? -Infinity : Number(input.min);
      const max = input.max === '' ? Infinity : Number(input.max);
      const decimals = (String(step).split('.')[1] || '').length;

      const nudge = dir => {
        const now = Number(input.value) || 0;
        const next = Math.min(max, Math.max(min, now + dir * step));
        input.value = next.toFixed(decimals);
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
        paint();
      };
      const paint = () => {
        const now = Number(input.value);
        $$('button', box).forEach(b => {
          const d = Number(b.dataset.step || (b === box.firstElementChild ? -1 : 1));
          b.disabled = d < 0 ? now <= min : now >= max;
        });
      };

      $$('button', box).forEach(b => {
        const dir = Number(b.dataset.step || (b === box.firstElementChild ? -1 : 1));
        let hold, repeat;
        const start = e => {
          e.preventDefault();
          nudge(dir);
          hold = setTimeout(() => { repeat = setInterval(() => nudge(dir), 70); }, 420);
        };
        const stop = () => { clearTimeout(hold); clearInterval(repeat); };
        b.addEventListener('pointerdown', start);
        ['pointerup', 'pointerleave', 'pointercancel'].forEach(ev => b.addEventListener(ev, stop));
      });
      input.addEventListener('input', paint);
      paint();
    });
  }

  function phones(root) {
    $$('.phone', root).forEach(box => {
      if (!once(box)) return;
      const input = $('input', box);
      const select = $('select', box);
      const codeEl = $('.phone-code', box);
      const flagEl = $('.phone-flag', box);

      const sync = () => {
        const opt = select?.selectedOptions[0];
        if (!opt) return;
        if (codeEl) codeEl.textContent = opt.dataset.code || '';
        if (flagEl) flagEl.textContent = opt.dataset.flag || '';
        input.placeholder = opt.dataset.mask
          ? opt.dataset.mask.replace(/#/g, '0')
          : input.placeholder;
      };
      select?.addEventListener('change', () => { sync(); format(); });

      const format = () => {
        const mask = select?.selectedOptions[0]?.dataset.mask;
        const digits = input.value.replace(/\D/g, '');
        if (!mask) { input.value = digits; return; }
        let out = '', d = 0;
        for (const ch of mask) {
          if (d >= digits.length) break;
          out += ch === '#' ? digits[d++] : ch;
        }
        input.value = out;
        const full = (mask.match(/#/g) || []).length;
        box.classList.toggle('is-valid', digits.length === full);
      };
      input.addEventListener('input', format);
      input.addEventListener('blur', () => {
        box.dispatchEvent(new CustomEvent('deck:change', {
          bubbles: true,
          detail: {
            code: select?.selectedOptions[0]?.dataset.code || '',
            number: input.value.replace(/\D/g, ''),
            e164: (select?.selectedOptions[0]?.dataset.code || '') + input.value.replace(/\D/g, '')
          }
        }));
      });
      sync();
    });
  }

  function rangePairs(root) {
    $$('.range-pair', root).forEach(pair => {
      if (!once(pair)) return;
      const [lo, hi] = $$('input[type="range"]', pair);
      if (!lo || !hi) return;
      const min = Number(lo.min || 0), max = Number(lo.max || 100);
      const pct = v => ((v - min) / (max - min)) * 100;
      const gap = Number(pair.dataset.gap || 0);

      const paint = () => {
        if (Number(lo.value) > Number(hi.value) - gap) {
          // whichever handle moved last gets pushed back
          if (document.activeElement === lo) lo.value = String(Number(hi.value) - gap);
          else hi.value = String(Number(lo.value) + gap);
        }
        pair.style.setProperty('--lo', pct(Number(lo.value)).toFixed(2));
        pair.style.setProperty('--hi', pct(Number(hi.value)).toFixed(2));
        const readout = pair.parentElement?.querySelector('.range-readout');
        if (readout) {
          const [a, b] = readout.children;
          if (a) a.textContent = lo.dataset.prefix ? lo.dataset.prefix + lo.value : lo.value;
          if (b) b.textContent = hi.dataset.prefix ? hi.dataset.prefix + hi.value : hi.value;
        }
        pair.dispatchEvent(new CustomEvent('deck:change', {
          bubbles: true, detail: { min: Number(lo.value), max: Number(hi.value) }
        }));
      };
      [lo, hi].forEach(i => i.addEventListener('input', paint));
      paint();
    });
  }

  /* ======================================================================
     Copy to clipboard
     ====================================================================== */

  async function writeClipboard(text) {
    try {
      await navigator.clipboard.writeText(text);
      return true;
    } catch (e) {
      // http origins and older engines
      const ta = document.createElement('textarea');
      ta.value = text;
      ta.setAttribute('readonly', '');
      ta.style.cssText = 'position:fixed;opacity:0;pointer-events:none';
      document.body.append(ta);
      ta.select();
      let ok = false;
      try { ok = document.execCommand('copy'); } catch (err) {}
      ta.remove();
      return ok;
    }
  }

  function copiers(root) {
    $$('[data-deck-copy]', root).forEach(btn => {
      if (!once(btn)) return;
      btn.addEventListener('click', async () => {
        const sel = btn.dataset.deckCopy;
        const text = sel && sel !== 'true'
          ? ($(sel)?.value ?? $(sel)?.textContent ?? sel)
          : (btn.closest('.copy')?.querySelector('code, .copy-value')?.textContent
             ?? btn.previousElementSibling?.textContent ?? '');
        const ok = await writeClipboard(String(text).trim());
        btn.classList.toggle('is-copied', ok);
        btn.setAttribute('aria-live', 'polite');
        if (ok && !btn.classList.contains('copy-inline') && !$('.copy-done', btn)) {
          Deck.toast({ kind: 'good', title: 'Copied', duration: 2000 });
        }
        setTimeout(() => btn.classList.remove('is-copied'), 1800);
      });
    });
  }

  /* ======================================================================
     WYSIWYG editor
     ====================================================================== */

  function editors(root) {
    $$('.editor', root).forEach(ed => {
      if (!once(ed)) return;
      const content = $('.editor-content', ed);
      const target = ed.dataset.target ? $(ed.dataset.target) : null;
      const counter = $('.editor-count', ed);
      const limit = Number(ed.dataset.limit || 0);
      if (!content) return;
      content.contentEditable = 'true';
      content.setAttribute('role', 'textbox');
      content.setAttribute('aria-multiline', 'true');

      const run = (cmd, value = null) => {
        content.focus();
        document.execCommand(cmd, false, value);
        paint();
        sync();
      };

      const paint = () => {
        $$('[data-cmd]', ed).forEach(b => {
          let on = false;
          try { on = document.queryCommandState(b.dataset.cmd); } catch (e) {}
          b.setAttribute('aria-pressed', String(on));
        });
        if (counter) {
          const n = content.textContent.trim().length;
          counter.textContent = limit ? `${n} / ${limit}` : `${n} characters`;
          counter.classList.toggle('is-over', limit > 0 && n > limit);
        }
      };

      const sync = () => {
        if (target) target.value = content.innerHTML;
        ed.dispatchEvent(new CustomEvent('deck:change', {
          bubbles: true, detail: { html: content.innerHTML, text: content.textContent }
        }));
      };

      ed.addEventListener('click', e => {
        const b = e.target.closest('[data-cmd]');
        if (!b) return;
        e.preventDefault();
        const cmd = b.dataset.cmd;
        if (cmd === 'createLink') {
          const url = prompt('Link address');
          if (url) run('createLink', url);
          return;
        }
        run(cmd, b.dataset.value || null);
      });

      $$('.editor-select', ed).forEach(sel => {
        sel.addEventListener('change', () => run('formatBlock', sel.value));
      });

      // Paste as plain text, so a paste from Word does not import its styling
      content.addEventListener('paste', e => {
        e.preventDefault();
        const text = (e.clipboardData || window.clipboardData).getData('text/plain');
        document.execCommand('insertText', false, text);
      });

      content.addEventListener('input', () => { paint(); sync(); });
      content.addEventListener('keyup', paint);
      content.addEventListener('mouseup', paint);
      document.addEventListener('selectionchange', () => {
        if (content.contains(document.getSelection()?.anchorNode)) paint();
      });

      if (target && target.value) content.innerHTML = target.value;
      paint();
    });
  }

  /* ======================================================================
     Lazy images
     ====================================================================== */

  function lazies(root) {
    const imgs = $$('.lazy img, img[data-src]', root).filter(i => !i.dataset.deckX);
    if (!imgs.length) return;
    const load = img => {
      img.dataset.deckX = '1';
      if (img.dataset.src) img.src = img.dataset.src;
      if (img.dataset.srcset) img.srcset = img.dataset.srcset;
      const done = () => img.classList.add('is-loaded');
      if (img.complete && img.naturalWidth) done();
      else img.addEventListener('load', done, { once: true });
      img.addEventListener('error', () => img.closest('.lazy')?.classList.add('is-error'), { once: true });
    };
    if (!('IntersectionObserver' in window)) return imgs.forEach(load);
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        load(e.target);
        io.unobserve(e.target);
      });
    }, { rootMargin: '200px' });
    imgs.forEach(i => io.observe(i));
  }

  /* ======================================================================
     QR code
     A complete encoder: byte mode, versions 1 to 10, error correction
     L / M / Q / H, all eight masks scored by the standard penalty rules.
     No dependency, no network call, no canvas — it emits SVG.
     ====================================================================== */

  const QR = (() => {
    // Galois field GF(256) with the QR primitive polynomial 0x11D
    const EXP = new Uint8Array(512), LOG = new Uint8Array(256);
    for (let i = 0, x = 1; i < 255; i++) {
      EXP[i] = x; LOG[x] = i;
      x <<= 1; if (x & 0x100) x ^= 0x11d;
    }
    for (let i = 255; i < 512; i++) EXP[i] = EXP[i - 255];
    const mul = (a, b) => (a && b) ? EXP[LOG[a] + LOG[b]] : 0;

    // [ecPerBlock, group1Blocks, group1Data, group2Blocks, group2Data] by version, then L M Q H
    const RS = {
      1:  [[7,1,19,0,0],[10,1,16,0,0],[13,1,13,0,0],[17,1,9,0,0]],
      2:  [[10,1,34,0,0],[16,1,28,0,0],[22,1,22,0,0],[28,1,16,0,0]],
      3:  [[15,1,55,0,0],[26,1,44,0,0],[18,2,17,0,0],[22,2,13,0,0]],
      4:  [[20,1,80,0,0],[18,2,32,0,0],[26,2,24,0,0],[16,4,9,0,0]],
      5:  [[26,1,108,0,0],[24,2,43,0,0],[18,2,15,2,16],[22,2,11,2,12]],
      6:  [[18,2,68,0,0],[16,4,27,0,0],[24,4,19,0,0],[28,4,15,0,0]],
      7:  [[20,2,78,0,0],[18,4,31,0,0],[18,2,14,4,15],[26,4,13,1,14]],
      8:  [[24,2,97,0,0],[22,2,38,2,39],[22,4,18,2,19],[26,4,14,2,15]],
      9:  [[30,2,116,0,0],[22,3,36,2,37],[20,4,16,4,17],[24,4,12,4,13]],
      10: [[18,2,68,2,69],[26,4,43,1,44],[24,6,19,2,20],[28,6,15,2,16]]
    };
    const ALIGN = {
      1: [], 2: [6,18], 3: [6,22], 4: [6,26], 5: [6,30],
      6: [6,34], 7: [6,22,38], 8: [6,24,42], 9: [6,26,46], 10: [6,28,50]
    };
    const ECL = { L: 0, M: 1, Q: 2, H: 3 };
    const ECL_BITS = { L: 1, M: 0, Q: 3, H: 2 };

    function generator(n) {
      let poly = [1];
      for (let i = 0; i < n; i++) {
        const next = new Array(poly.length + 1).fill(0);
        for (let j = 0; j < poly.length; j++) {
          next[j] ^= mul(poly[j], EXP[i]);
          next[j + 1] ^= poly[j];
        }
        poly = next;
      }
      return poly;
    }

    function ecBytes(data, n) {
      const gen = generator(n);
      const rem = new Array(n).fill(0);
      for (const byte of data) {
        const factor = byte ^ rem[0];
        rem.shift();
        rem.push(0);
        for (let i = 0; i < n; i++) rem[i] ^= mul(gen[i + 1], factor);
      }
      return rem;
    }

    function formatBits(ecl, mask) {
      const data = (ECL_BITS[ecl] << 3) | mask;
      let rem = data;
      for (let i = 0; i < 10; i++) rem = (rem << 1) ^ ((rem >> 9) * 0x537);
      return ((data << 10) | rem) ^ 0x5412;
    }

    function versionBits(v) {
      let rem = v;
      for (let i = 0; i < 12; i++) rem = (rem << 1) ^ ((rem >> 11) * 0x1f25);
      return (v << 12) | rem;
    }

    function pickVersion(len, ecl) {
      for (let v = 1; v <= 10; v++) {
        const [ec, b1, d1, b2, d2] = RS[v][ECL[ecl]];
        const total = b1 * d1 + b2 * d2;
        const countBits = v < 10 ? 8 : 16;
        if (len <= Math.floor((total * 8 - 4 - countBits) / 8)) return v;
      }
      throw new Error('QR: content too long for versions 1 to 10. Shorten it or link to it.');
    }

    function encode(text, ecl = 'M') {
      const bytes = Array.from(new TextEncoder().encode(text));
      const version = pickVersion(bytes.length, ecl);
      const [ecPer, b1, d1, b2, d2] = RS[version][ECL[ecl]];
      const totalData = b1 * d1 + b2 * d2;
      const countBits = version < 10 ? 8 : 16;

      // Bit stream: mode 0100, character count, payload, terminator, padding
      const bits = [];
      const push = (val, n) => { for (let i = n - 1; i >= 0; i--) bits.push((val >> i) & 1); };
      push(4, 4);
      push(bytes.length, countBits);
      bytes.forEach(b => push(b, 8));
      for (let i = 0; i < 4 && bits.length < totalData * 8; i++) bits.push(0);
      while (bits.length % 8) bits.push(0);
      const data = [];
      for (let i = 0; i < bits.length; i += 8) {
        data.push(parseInt(bits.slice(i, i + 8).join(''), 2));
      }
      const PAD = [0xec, 0x11];
      for (let i = 0; data.length < totalData; i++) data.push(PAD[i % 2]);

      // Split into blocks, compute error correction, then interleave
      const blocks = [], ecs = [];
      let at = 0;
      for (let i = 0; i < b1 + b2; i++) {
        const size = i < b1 ? d1 : d2;
        const block = data.slice(at, at + size);
        at += size;
        blocks.push(block);
        ecs.push(ecBytes(block, ecPer));
      }
      const out = [];
      const maxData = Math.max(d1, d2);
      for (let i = 0; i < maxData; i++)
        blocks.forEach(b => { if (i < b.length) out.push(b[i]); });
      for (let i = 0; i < ecPer; i++)
        ecs.forEach(b => out.push(b[i]));

      return { version, ecl, codewords: out };
    }

    function build(text, ecl = 'M') {
      const { version, codewords } = encode(text, ecl);
      const size = version * 4 + 17;
      const grid = Array.from({ length: size }, () => new Array(size).fill(null));
      const reserved = Array.from({ length: size }, () => new Array(size).fill(false));

      const put = (x, y, v, res = true) => {
        if (x < 0 || y < 0 || x >= size || y >= size) return;
        grid[y][x] = v;
        if (res) reserved[y][x] = true;
      };

      // Finder patterns and separators
      [[0, 0], [size - 7, 0], [0, size - 7]].forEach(([ox, oy]) => {
        for (let y = -1; y <= 7; y++) for (let x = -1; x <= 7; x++) {
          const on = (x >= 0 && x <= 6 && (y === 0 || y === 6)) ||
                     (y >= 0 && y <= 6 && (x === 0 || x === 6)) ||
                     (x >= 2 && x <= 4 && y >= 2 && y <= 4);
          put(ox + x, oy + y, on ? 1 : 0);
        }
      });

      // Timing patterns
      for (let i = 8; i < size - 8; i++) {
        put(i, 6, i % 2 === 0 ? 1 : 0);
        put(6, i, i % 2 === 0 ? 1 : 0);
      }

      // Alignment patterns, skipping the ones that collide with finders
      const centers = ALIGN[version];
      centers.forEach(cy => centers.forEach(cx => {
        if ((cx === 6 && cy === 6) ||
            (cx === 6 && cy === size - 7) ||
            (cx === size - 7 && cy === 6)) return;
        for (let y = -2; y <= 2; y++) for (let x = -2; x <= 2; x++) {
          const on = Math.max(Math.abs(x), Math.abs(y)) !== 1;
          put(cx + x, cy + y, on ? 1 : 0);
        }
      }));

      // Dark module, then reserve the two format strips so data skips them
      put(8, size - 8, 1);
      for (let i = 0; i < 9; i++) { reserved[8][i] = true; reserved[i][8] = true; }
      for (let i = 0; i < 8; i++) {
        reserved[8][size - 1 - i] = true;
        reserved[size - 1 - i][8] = true;
      }

      // Version information blocks, versions 7 and up
      if (version >= 7) {
        const vb = versionBits(version);
        for (let i = 0; i < 18; i++) {
          const b = (vb >> i) & 1;
          const a = Math.floor(i / 3), c = i % 3;
          grid[a][size - 11 + c] = b; reserved[a][size - 11 + c] = true;
          grid[size - 11 + c][a] = b; reserved[size - 11 + c][a] = true;
        }
      }

      // Place data in the zig-zag, skipping the timing column
      let bit = 0;
      const stream = [];
      codewords.forEach(cw => { for (let i = 7; i >= 0; i--) stream.push((cw >> i) & 1); });

      let up = true;
      for (let right = size - 1; right > 0; right -= 2) {
        if (right === 6) right = 5;
        for (let step = 0; step < size; step++) {
          const y = up ? size - 1 - step : step;
          for (let c = 0; c < 2; c++) {
            const x = right - c;
            if (reserved[y][x]) continue;
            grid[y][x] = bit < stream.length ? stream[bit++] : 0;
          }
        }
        up = !up;
      }

      // Try every mask, keep the one with the lowest penalty
      const maskFn = [
        (x, y) => (x + y) % 2 === 0,
        (x, y) => y % 2 === 0,
        (x, y) => x % 3 === 0,
        (x, y) => (x + y) % 3 === 0,
        (x, y) => (Math.floor(y / 2) + Math.floor(x / 3)) % 2 === 0,
        (x, y) => ((x * y) % 2) + ((x * y) % 3) === 0,
        (x, y) => (((x * y) % 2) + ((x * y) % 3)) % 2 === 0,
        (x, y) => (((x + y) % 2) + ((x * y) % 3)) % 2 === 0
      ];

      let best = null, bestScore = Infinity;
      for (let m = 0; m < 8; m++) {
        const test = grid.map(r => r.slice());
        for (let y = 0; y < size; y++) for (let x = 0; x < size; x++) {
          if (!reserved[y][x] && maskFn[m](x, y)) test[y][x] ^= 1;
        }
        const fb = formatBits(ecl, m);
        const fbit = i => (fb >> i) & 1;
        // grid is indexed [row][column]
        // First copy: down column 8, then left along row 8
        for (let i = 0; i <= 5; i++) test[i][8] = fbit(i);
        test[7][8] = fbit(6);
        test[8][8] = fbit(7);
        test[8][7] = fbit(8);
        for (let i = 9; i <= 14; i++) test[8][14 - i] = fbit(i);
        // Second copy: along row 8 beside the top-right finder,
        // then down column 8 beside the bottom-left finder
        for (let i = 0; i <= 7; i++) test[8][size - 1 - i] = fbit(i);
        for (let i = 8; i <= 14; i++) test[size - 15 + i][8] = fbit(i);
        test[size - 8][8] = 1;
        const score = penalty(test, size);
        if (score < bestScore) { bestScore = score; best = test; }
      }
      return { modules: best, size, version };
    }

    function penalty(m, size) {
      let score = 0;
      // Rule 1: runs of five or more of the same colour
      const run = line => {
        let total = 0, count = 1;
        for (let i = 1; i < size; i++) {
          if (line[i] === line[i - 1]) count++;
          else { if (count >= 5) total += count - 2; count = 1; }
        }
        if (count >= 5) total += count - 2;
        return total;
      };
      for (let y = 0; y < size; y++) score += run(m[y]);
      for (let x = 0; x < size; x++) score += run(m.map(r => r[x]));
      // Rule 2: two by two blocks
      for (let y = 0; y < size - 1; y++) for (let x = 0; x < size - 1; x++) {
        const v = m[y][x];
        if (v === m[y][x + 1] && v === m[y + 1][x] && v === m[y + 1][x + 1]) score += 3;
      }
      // Rule 3: the finder-like 1011101 pattern with four light modules beside it
      const pat1 = [1,0,1,1,1,0,1,0,0,0,0];
      const pat2 = [0,0,0,0,1,0,1,1,1,0,1];
      const scan = line => {
        let s = 0;
        for (let i = 0; i <= size - 11; i++) {
          const slice = line.slice(i, i + 11).join('');
          if (slice === pat1.join('') || slice === pat2.join('')) s += 40;
        }
        return s;
      };
      for (let y = 0; y < size; y++) score += scan(m[y]);
      for (let x = 0; x < size; x++) score += scan(m.map(r => r[x]));
      // Rule 4: deviation from an even split of dark and light
      let dark = 0;
      for (let y = 0; y < size; y++) for (let x = 0; x < size; x++) dark += m[y][x];
      const ratio = Math.abs((dark * 100) / (size * size) - 50);
      score += Math.floor(ratio / 5) * 10;
      return score;
    }

    function svg(text, ecl = 'M') {
      const { modules, size } = build(text, ecl);
      // One path-free approach: merge horizontal runs into rects so the SVG
      // stays small even at version 10
      let rects = '';
      for (let y = 0; y < size; y++) {
        let x = 0;
        while (x < size) {
          if (!modules[y][x]) { x++; continue; }
          let w = 1;
          while (x + w < size && modules[y][x + w]) w++;
          rects += `<rect x="${x}" y="${y}" width="${w}" height="1"/>`;
          x += w;
        }
      }
      return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${size} ${size}" role="img"><g>${rects}</g></svg>`;
    }

    return { svg, build, encode };
  })();

  function qrCodes(root) {
    $$('[data-deck-qr]', root).forEach(node => {
      if (!once(node)) return;
      const value = node.dataset.deckQr || node.textContent.trim();
      const level = (node.dataset.ecl || 'M').toUpperCase();
      try {
        node.innerHTML = QR.svg(value, level) + (node.dataset.caption
          ? `<span class="qr-caption">${node.dataset.caption}</span>` : '');
      } catch (err) {
        node.innerHTML = `<span class="text-sm text-muted">${err.message}</span>`;
      }
    });
  }

  /* ======================================================================
     Register
     ====================================================================== */

  const baseInit = Deck.init.bind(Deck);
  Deck.init = function (root = document) {
    baseInit(root);
    carousels(root);
    drawers(root);
    megaMenus(root);
    speedDials(root);
    banners(root);
    numbers(root);
    phones(root);
    rangePairs(root);
    copiers(root);
    editors(root);
    lazies(root);
    qrCodes(root);
    return Deck;
  };

  Deck.qr = QR;
  Deck.copy = writeClipboard;

  if (document.readyState !== 'loading') Deck.init();
  else document.addEventListener('DOMContentLoaded', () => Deck.init());

})(typeof window !== 'undefined' && window.Deck ? window.Deck
   : typeof Deck !== 'undefined' ? Deck : null);

/*! ==========================================================================
 *  deck-adapters.js v0.1
 *
 *  Deck's core is zero-dependency on purpose. This file is the opposite: it
 *  is a set of adapters that light up ONLY if the corresponding library is
 *  already on the page. Load none of them and everything still works; load
 *  one and Deck hands that job over to a better engine and keeps providing
 *  the styling.
 *
 *  Detected, in order of how much they actually add:
 *
 *    Floating UI   window.FloatingUIDOM   collision-aware placement, for
 *                                         browsers without CSS anchor
 *                                         positioning
 *    Quill         window.Quill           replaces the deprecated
 *    Tiptap        window.Tiptap          document.execCommand editor
 *    Chart.js      window.Chart           real axes, tooltips, time scales
 *    SortableJS    window.Sortable        drag reordering and kanban
 *    Lucide        window.lucide          1500 icons instead of Deck's 56
 *
 *  Nothing here is required and nothing here is bundled. Add a script tag for
 *  the ones you want.
 *  ======================================================================== */

(function (Deck) {
  'use strict';
  if (!Deck) { console.warn('deck-adapters.js: load deck.js first'); return; }

  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
  const once = n => { if (n.dataset.deckA) return false; n.dataset.deckA = '1'; return true; };
  const css = v => getComputedStyle(document.documentElement).getPropertyValue(v).trim();
  const hasAnchor = CSS.supports('anchor-name: --a');

  const have = {
    get floating() { return !!window.FloatingUIDOM; },
    get quill() { return !!window.Quill; },
    get tiptap() { return !!(window.Tiptap && window.Tiptap.Editor); },
    get chart() { return !!window.Chart; },
    get sortable() { return !!window.Sortable; },
    get lucide() { return !!window.lucide; }
  };

  /* ======================================================================
     1. Floating UI — placement
     Only used where CSS anchor positioning is missing. Where the browser has
     anchor positioning, the CSS in 25-anchor.css already does this natively,
     on the compositor, with no listeners, so the adapter stays out of the way.
     ====================================================================== */

  function floating(root) {
    if (hasAnchor || !have.floating) return;
    const { computePosition, autoUpdate, offset, flip, shift, arrow, size } = window.FloatingUIDOM;

    $$('[data-deck-anchor]', root).forEach(trigger => {
      if (!once(trigger)) return;
      const panel = $(trigger.dataset.deckAnchor);
      if (!panel) return;
      const arrowEl = $('.tip-arrow', panel);
      const placement = panel.dataset.placement || 'bottom';
      const matchWidth = panel.classList.contains('menu-match') ||
                         panel.classList.contains('combo-list');

      const middleware = [
        offset(8),
        flip({ fallbackAxisSideDirection: 'start' }),
        shift({ padding: 8 }),
        size({
          padding: 8,
          apply({ availableHeight, rects, elements }) {
            Object.assign(elements.floating.style, {
              maxHeight: `${Math.max(120, availableHeight)}px`,
              ...(matchWidth ? { width: `${rects.reference.width}px` } : {})
            });
          }
        })
      ];
      if (arrowEl) middleware.push(arrow({ element: arrowEl, padding: 6 }));

      const place = async () => {
        const r = await computePosition(trigger, panel, { placement, strategy: 'fixed', middleware });
        Object.assign(panel.style, { left: `${r.x}px`, top: `${r.y}px`, position: 'fixed' });
        if (arrowEl && r.middlewareData.arrow) {
          const { x, y } = r.middlewareData.arrow;
          const side = { top: 'bottom', right: 'left', bottom: 'top', left: 'right' }[r.placement.split('-')[0]];
          Object.assign(arrowEl.style, {
            left: x != null ? `${x}px` : '',
            top: y != null ? `${y}px` : '',
            [side]: '-4px'
          });
        }
      };

      let stop = null;
      panel.addEventListener('toggle', e => {
        if (e.newState === 'open') { place(); stop = autoUpdate(trigger, panel, place); }
        else { stop?.(); stop = null; }
      });
    });
  }

  /* ======================================================================
     2. Editor — Quill or Tiptap
     Deck's own editor uses document.execCommand, which is deprecated and
     inconsistent. If either library is on the page, Deck hands the content
     region over and keeps its own toolbar chrome, so the markup and the CSS
     do not change.
     ====================================================================== */

  function editorAdapter(root) {
    if (!have.quill && !have.tiptap) return;

    $$('.editor', root).forEach(ed => {
      if (!once(ed)) return;
      const content = $('.editor-content', ed);
      const target = ed.dataset.target ? $(ed.dataset.target) : null;
      const counter = $('.editor-count', ed);
      const limit = Number(ed.dataset.limit || 0);
      if (!content) return;

      // Deck's own handler already claimed this node; take it back cleanly
      content.contentEditable = 'false';
      content.removeAttribute('role');

      const count = text => {
        if (!counter) return;
        const n = text.trim().length;
        counter.textContent = limit ? `${n} / ${limit}` : `${n} characters`;
        counter.classList.toggle('is-over', limit > 0 && n > limit);
      };

      if (have.tiptap) {
        const { Editor } = window.Tiptap;
        const extensions = window.Tiptap.StarterKit ? [window.Tiptap.StarterKit] : [];
        const editor = new Editor({
          element: content,
          extensions,
          content: target?.value || content.innerHTML || '',
          onUpdate: ({ editor }) => {
            if (target) target.value = editor.getHTML();
            count(editor.getText());
            paint(editor);
            ed.dispatchEvent(new CustomEvent('deck:change', {
              bubbles: true, detail: { html: editor.getHTML(), text: editor.getText() }
            }));
          }
        });

        const CMD = {
          bold: e => e.chain().focus().toggleBold().run(),
          italic: e => e.chain().focus().toggleItalic().run(),
          underline: e => e.chain().focus().toggleUnderline?.().run(),
          strikeThrough: e => e.chain().focus().toggleStrike().run(),
          insertUnorderedList: e => e.chain().focus().toggleBulletList().run(),
          insertOrderedList: e => e.chain().focus().toggleOrderedList().run(),
          formatBlock: (e, v) => v === 'p'
            ? e.chain().focus().setParagraph().run()
            : e.chain().focus().toggleHeading({ level: Number(v.replace('h', '')) }).run(),
          undo: e => e.chain().focus().undo().run(),
          redo: e => e.chain().focus().redo().run(),
          createLink: e => {
            const url = prompt('Link address');
            if (url) e.chain().focus().setLink({ href: url }).run();
          }
        };
        const STATE = { bold: 'bold', italic: 'italic', underline: 'underline',
                        strikeThrough: 'strike', insertUnorderedList: 'bulletList',
                        insertOrderedList: 'orderedList' };
        const paint = e => $$('[data-cmd]', ed).forEach(b => {
          const name = STATE[b.dataset.cmd];
          if (name) b.setAttribute('aria-pressed', String(e.isActive(name)));
        });

        ed.addEventListener('click', e => {
          const b = e.target.closest('[data-cmd]');
          if (!b) return;
          e.preventDefault();
          CMD[b.dataset.cmd]?.(editor, b.dataset.value);
          paint(editor);
        });
        $$('.editor-select', ed).forEach(sel =>
          sel.addEventListener('change', () => CMD.formatBlock(editor, sel.value)));
        ed.deckEditor = editor;
        count(editor.getText());

      } else {
        const q = new window.Quill(content, {
          theme: null,                       // Deck supplies the chrome
          placeholder: content.dataset.placeholder || '',
          modules: { toolbar: false }
        });
        if (target?.value) q.clipboard.dangerouslyPasteHTML(target.value);

        const paint = () => {
          const f = q.getFormat();
          $$('[data-cmd]', ed).forEach(b => {
            const map = { bold: 'bold', italic: 'italic', underline: 'underline',
                          strikeThrough: 'strike' };
            const key = map[b.dataset.cmd];
            if (key) b.setAttribute('aria-pressed', String(!!f[key]));
            if (b.dataset.cmd === 'insertUnorderedList')
              b.setAttribute('aria-pressed', String(f.list === 'bullet'));
            if (b.dataset.cmd === 'insertOrderedList')
              b.setAttribute('aria-pressed', String(f.list === 'ordered'));
          });
        };

        const CMD = {
          bold: () => q.format('bold', !q.getFormat().bold),
          italic: () => q.format('italic', !q.getFormat().italic),
          underline: () => q.format('underline', !q.getFormat().underline),
          strikeThrough: () => q.format('strike', !q.getFormat().strike),
          insertUnorderedList: () => q.format('list', q.getFormat().list === 'bullet' ? false : 'bullet'),
          insertOrderedList: () => q.format('list', q.getFormat().list === 'ordered' ? false : 'ordered'),
          formatBlock: v => q.format('header', v === 'p' ? false : Number(v.replace('h', ''))),
          undo: () => q.history.undo(),
          redo: () => q.history.redo(),
          createLink: () => { const u = prompt('Link address'); if (u) q.format('link', u); }
        };

        ed.addEventListener('click', e => {
          const b = e.target.closest('[data-cmd]');
          if (!b) return;
          e.preventDefault();
          CMD[b.dataset.cmd]?.(b.dataset.value);
          paint();
        });
        $$('.editor-select', ed).forEach(sel =>
          sel.addEventListener('change', () => CMD.formatBlock(sel.value)));

        q.on('text-change', () => {
          if (target) target.value = q.root.innerHTML;
          count(q.getText());
          ed.dispatchEvent(new CustomEvent('deck:change', {
            bubbles: true, detail: { html: q.root.innerHTML, text: q.getText() }
          }));
        });
        q.on('selection-change', paint);
        ed.deckEditor = q;
        count(q.getText());
      }
    });
  }

  /* ======================================================================
     3. Chart.js — themed from Deck's tokens
     Deck's CSS charts are good for dashboard tiles and they retheme with the
     hue slider. What they cannot do is a time axis, a crosshair, a zoom, or
     twenty thousand points. Where Chart.js is loaded, this makes it match
     Deck exactly and follow the theme and the hue at runtime.
     ====================================================================== */

  function chartTheme() {
    if (!have.chart) return null;
    const Chart = window.Chart;

    const series = i => css(`--c${(i % 6) + 1}`) || css('--brand-500');

    const apply = () => {
      Chart.defaults.font.family = css('--font-sans');
      Chart.defaults.font.size = 12;
      Chart.defaults.color = css('--text-muted');
      Chart.defaults.borderColor = css('--line');
      Chart.defaults.elements.line.tension = 0.3;
      Chart.defaults.elements.line.borderWidth = 2;
      Chart.defaults.elements.point.radius = 0;
      Chart.defaults.elements.point.hoverRadius = 5;
      Chart.defaults.elements.bar.borderRadius = 4;
      Chart.defaults.elements.bar.borderSkipped = 'bottom';

      Chart.defaults.plugins.legend.labels.usePointStyle = true;
      Chart.defaults.plugins.legend.labels.boxWidth = 8;
      Chart.defaults.plugins.legend.labels.boxHeight = 8;
      Chart.defaults.plugins.legend.labels.padding = 14;

      Object.assign(Chart.defaults.plugins.tooltip, {
        backgroundColor: css('--ink-900'),
        titleColor: css('--ink-50'),
        bodyColor: css('--ink-100'),
        borderColor: 'transparent',
        cornerRadius: 6,
        padding: 10,
        displayColors: true,
        usePointStyle: true,
        boxPadding: 4,
        titleFont: { weight: '620' }
      });

      Chart.defaults.scale.grid.color = css('--line');
      Chart.defaults.scale.grid.drawTicks = false;
      Chart.defaults.scale.border = { display: false };
      Chart.defaults.scale.ticks.padding = 8;
    };

    apply();

    // Follow theme and hue changes without rebuilding the chart
    const repaint = () => {
      apply();
      Object.values(Chart.instances || {}).forEach(c => {
        c.data.datasets.forEach((d, i) => {
          if (d.deckSeries !== false) {
            d.borderColor = series(i);
            d.backgroundColor = d.fill ? `color-mix(in oklab, ${series(i)} 18%, transparent)` : series(i);
          }
        });
        c.update('none');
      });
    };
    new MutationObserver(repaint).observe(document.documentElement, {
      attributes: true, attributeFilter: ['data-theme', 'style']
    });
    matchMedia('(prefers-color-scheme: dark)').addEventListener('change', repaint);

    // Convenience: Deck.chart(canvas, config) applies the series palette
    Deck.chart = (canvas, config) => {
      (config.data?.datasets || []).forEach((d, i) => {
        if (d.borderColor == null) d.borderColor = series(i);
        if (d.backgroundColor == null) {
          d.backgroundColor = d.fill
            ? `color-mix(in oklab, ${series(i)} 18%, transparent)`
            : series(i);
        }
      });
      return new Chart(canvas, config);
    };

    return { apply, repaint, series };
  }

  /* ======================================================================
     4. SortableJS — drag reordering and kanban
     Deck's CSS already uses SortableJS's default class names, so no
     configuration is needed. Without the library, the fallback below uses the
     native HTML drag and drop API, which is worse on touch but costs nothing.
     ====================================================================== */

  function sortables(root) {
    $$('[data-deck-sortable]', root).forEach(list => {
      if (!once(list)) return;
      const group = list.dataset.deckSortable || undefined;
      const handle = list.dataset.handle || null;

      const emit = detail => list.dispatchEvent(
        new CustomEvent('deck:reorder', { bubbles: true, detail }));

      if (have.sortable) {
        window.Sortable.create(list, {
          group: group && group !== 'true' ? { name: group, pull: true, put: true } : undefined,
          handle,
          animation: Deck.reduced?.() ? 0 : 180,
          easing: 'cubic-bezier(.22,1,.36,1)',
          ghostClass: 'sortable-ghost',
          chosenClass: 'sortable-chosen',
          dragClass: 'sortable-drag',
          forceFallback: false,
          fallbackOnBody: true,
          swapThreshold: 0.66,
          delay: matchMedia('(pointer: coarse)').matches ? 140 : 0,
          delayOnTouchOnly: true,
          onEnd: e => {
            counts();
            emit({
              item: e.item, from: e.from, to: e.to,
              oldIndex: e.oldIndex, newIndex: e.newIndex,
              order: Array.from(e.to.children).map(c => c.dataset.id ?? null)
            });
          }
        });
        return;
      }

      // Native fallback
      Array.from(list.children).forEach(child => { child.draggable = true; });
      let dragged = null;
      list.addEventListener('dragstart', e => {
        dragged = e.target.closest('[draggable="true"]');
        if (!dragged) return;
        dragged.classList.add('sortable-chosen');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', dragged.dataset.id || '');
      });
      list.addEventListener('dragover', e => {
        e.preventDefault();
        const over = e.target.closest('[draggable="true"]');
        if (!over || over === dragged || !dragged) return;
        const r = over.getBoundingClientRect();
        const after = (e.clientY - r.top) / r.height > 0.5;
        over.parentElement.insertBefore(dragged, after ? over.nextSibling : over);
      });
      list.addEventListener('dragend', () => {
        dragged?.classList.remove('sortable-chosen');
        counts();
        emit({ item: dragged, from: list, to: list,
               order: Array.from(list.children).map(c => c.dataset.id ?? null) });
        dragged = null;
      });
    });

    function counts() {
      $$('.kanban-col').forEach(col => {
        const n = $$('.kanban-card', col).length;
        const badge = $('.kanban-count', col);
        if (badge) badge.textContent = n;
      });
    }
    counts();
  }

  /* ======================================================================
     5. Lucide — 1500 icons instead of Deck's 56
     Deck's sprite covers the framework's own needs and the automotive set it
     was built for. For anything beyond that, write the Lucide name and this
     swaps in the path data, keeping Deck's .icon sizing and stroke rules.
     ====================================================================== */

  function lucideIcons(root) {
    if (!have.lucide) return;
    const toPascal = s => s.replace(/(^\w|-\w)/g, m => m.replace('-', '').toUpperCase());

    $$('[data-icon]', root).forEach(node => {
      if (!once(node)) return;
      const name = node.dataset.icon;
      const node_ = window.lucide.icons?.[toPascal(name)] || window.lucide[toPascal(name)];
      if (!node_) { console.warn(`deck: no Lucide icon "${name}"`); return; }
      const children = Array.isArray(node_) ? node_[2] : node_.children || [];
      const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      svg.setAttribute('viewBox', '0 0 24 24');
      svg.setAttribute('aria-hidden', 'true');
      svg.setAttribute('class', node.className || 'icon');
      children.forEach(([tag, attrs]) => {
        const el = document.createElementNS('http://www.w3.org/2000/svg', tag);
        Object.entries(attrs).forEach(([k, v]) => el.setAttribute(k, v));
        svg.append(el);
      });
      node.replaceWith(svg);
    });
  }

  /* ======================================================================
     Register
     ====================================================================== */

  const baseInit = Deck.init.bind(Deck);
  Deck.init = function (root = document) {
    baseInit(root);
    floating(root);
    editorAdapter(root);
    sortables(root);
    lucideIcons(root);
    return Deck;
  };

  Deck.adapters = {
    get available() {
      return Object.fromEntries(Object.keys(have).map(k => [k, have[k]]));
    },
    /* What is actually doing the work right now. Useful in a console when a
       component behaves differently between two pages. */
    report() {
      const a = this.available;
      return {
        placement: hasAnchor ? 'CSS anchor positioning'
                 : a.floating ? 'Floating UI'
                 : 'Deck built-in',
        editor: a.tiptap ? 'Tiptap' : a.quill ? 'Quill' : 'Deck built-in (execCommand)',
        charts: a.chart ? 'Chart.js, themed by Deck' : 'Deck CSS charts',
        dragDrop: a.sortable ? 'SortableJS' : 'native drag and drop',
        icons: a.lucide ? 'Lucide + Deck sprite' : 'Deck sprite (74 icons)',
        virtualization: CSS.supports('content-visibility: auto')
          ? 'content-visibility (engine)' : 'none'
      };
    }
  };

  if (have.chart) Deck.chartTheme = chartTheme();

  if (document.readyState !== 'loading') Deck.init();
  else document.addEventListener('DOMContentLoaded', () => Deck.init());

})(typeof window !== 'undefined' && window.Deck ? window.Deck
   : typeof Deck !== 'undefined' ? Deck : null);

export default globalThis.Deck;
export { globalThis as __deckGlobal };
