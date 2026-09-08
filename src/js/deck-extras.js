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
