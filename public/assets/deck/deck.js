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
       <input class="input" name="ship_date" readonly>
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
          data-placeholder="Search repositories">
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
      this.wireSort();
      this.wireSelect();
      this.wireResize();
      /* @container scroll-state() paints the pin shadows on its own; this
         handler is only here for browsers that do not have it yet. */
      if (!CSS.supports('container-type: scroll-state')) {
        this.shadows();
        wrap.addEventListener('scroll', () => this.shadows(), { passive: true });
      }
      new ResizeObserver(() => this.measurePins()).observe(this.table);
    }

    measurePins() {
      const first = $('thead .dg-pin-start', this.table);
      if (first) this.wrap.style.setProperty('--dg-pin-start', first.offsetWidth + 'px');
    }

    shadows() {
      const w = this.wrap;
      const x = Math.abs(w.scrollLeft);          // an RTL scroller counts down from 0
      w.classList.toggle('is-scrolled-x', x > 0);
      w.classList.toggle('is-scrolled-end', x + w.clientWidth < w.scrollWidth - 1);
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
       Deck.toast('Order #1042 shipped')
       Deck.toast({ title: 'Project archived', text: 'You can undo this.',
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
      this.items.forEach((item, i) =>
        item.node.setAttribute('aria-hidden', i >= this.max ? 'true' : 'false'));
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
    $$('.pile', root).forEach(stack => {
      if (stack.dataset.deckWired) return;
      stack.dataset.deckWired = '1';
      stack.addEventListener('deck:advance', () => {
        const top = stack.querySelector(':scope > *:not(.is-dismissed)');
        if (!top) return;
        top.classList.add('is-dismissed');
        /* Once it has flown off, move it to the end of the pile. It stays in
           the DOM, but out of the way of everyone else's sibling-index(). */
        setTimeout(() => stack.append(top), 400);
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
