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

      // Deck's own handler already owns this node; take it back cleanly
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
