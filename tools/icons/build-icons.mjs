#!/usr/bin/env node
/**
 * Deck icon sprite generator.
 *
 *     npm run icons
 *
 * Reads the icon names listed in tools/icons/icons.txt out of the Material
 * Symbols Rounded variable font and writes src/deck-icons.svg.
 *
 * This is a MAINTAINER task, deliberately not part of `npm run build`. The
 * font lives under tools/, which is not published to npm and not shipped to a
 * browser: an install from npm has src/ but no tools/, so a build that needed
 * the font would fail for everyone downstream. The generated sprite is
 * committed instead.
 *
 * Two cuts are emitted for every entry:
 *
 *     #name      wght 400   the default, for .icon / .icon-lg / .icon-xl
 *     #name-sm   wght 500   the heavier cut, for .icon-sm at 16px
 *
 * Symbols not present in the font (the brand marks) are carried through from
 * the existing sprite verbatim; see HAND_AUTHORED handling below.
 *
 * Material Symbols is licensed under the Apache License, Version 2.0.
 * See tools/icons/LICENSE.txt.
 */

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';
import zlib from 'node:zlib';
import opentype from 'opentype.js';

const HERE = path.dirname(url.fileURLToPath(import.meta.url));
const ROOT = path.resolve(HERE, '..', '..');

const FONT = path.join(
  HERE,
  'Material_Symbols_Rounded',
  'MaterialSymbolsRounded-VariableFont_FILL,GRAD,opsz,wght.ttf'
);
const LIST = path.join(HERE, 'icons.txt');
const OUT = path.join(ROOT, 'src', 'deck-icons.svg');

/* The sprite grid. Material Symbols is drawn on a 960 unit em that divides
   cleanly into 24, so one design unit is exactly 40 font units. */
const UPM = 960;
const VIEWBOX = 24;
const SCALE = VIEWBOX / UPM;

/* Coordinate precision. Two decimals on a 24 unit grid is 1/100 of a design
   unit, well under half a device pixel even at .icon-xl on a 3x display. */
const DECIMALS = 2;
const QUANTUM = 10 ** -DECIMALS;

/* The two cuts. FILL 0 keeps the outlined drawing, GRAD 0 the nominal weight
   grade, opsz 24 the optical size Deck renders at (1.25em of 16px = 20px for
   .icon, 16px for .icon-sm). */
const CUTS = [
  { suffix: '', axes: { FILL: 0, GRAD: 0, opsz: 24, wght: 400 } },
  { suffix: '-sm', axes: { FILL: 0, GRAD: 0, opsz: 24, wght: 500 } },
];

const die = (msg) => {
  console.error(`\n  icons: ${msg}\n`);
  process.exit(1);
};

/* ---------------------------------------------------------------------------
   1. The icon list
   ------------------------------------------------------------------------ */

function readList(file) {
  if (!fs.existsSync(file)) die(`${path.relative(ROOT, file)} not found.`);
  const entries = [];
  const hand = [];
  const seen = new Map();

  fs.readFileSync(file, 'utf8')
    .split(/\r?\n/)
    .forEach((raw, i) => {
      const line = raw.replace(/#.*$/, '').trim();
      if (!line) return;
      const at = `${path.relative(ROOT, file)}:${i + 1}`;

      const handMatch = line.match(/^@hand\s+([a-z0-9][a-z0-9-]*)$/);
      if (handMatch) {
        hand.push({ id: handMatch[1], at });
        return;
      }

      const match = line.match(/^([a-z0-9][a-z0-9-]*)\s*=\s*([a-z0-9_.]+)$/);
      if (!match) die(`${at}: cannot parse ${JSON.stringify(raw.trim())}.`);
      entries.push({ id: match[1], glyph: match[2], at });
    });

  for (const { id, at } of [...entries, ...hand]) {
    if (seen.has(id)) die(`${at}: duplicate id "${id}", first seen at ${seen.get(id)}.`);
    seen.set(id, at);
    if (id.endsWith('-sm')) die(`${at}: id "${id}" would collide with a generated -sm cut.`);
  }

  if (!entries.length) die('the icon list has no font entries.');
  return { entries, hand };
}

/* ---------------------------------------------------------------------------
   2. The hand authored symbols
   ------------------------------------------------------------------------ */

/**
 * Pull one <symbol id="..."> ... </symbol> out of the previous sprite as an
 * exact substring, so the brand marks survive byte for byte.
 */
function extractSymbol(svg, id) {
  const open = new RegExp(`<symbol\\b[^>]*\\bid="${id}"`);
  const start = svg.search(open);
  if (start < 0) return null;
  const end = svg.indexOf('</symbol>', start);
  if (end < 0) return null;
  return svg.slice(start, end + '</symbol>'.length);
}

function carryOverHandAuthored(hand, previous) {
  if (!hand.length) return [];
  if (previous === null) {
    die(
      `${path.relative(ROOT, OUT)} does not exist, so the hand authored symbols ` +
        `(${hand.map((h) => h.id).join(', ')}) cannot be carried over. ` +
        'Restore the sprite from git before regenerating.'
    );
  }
  const kept = [];
  const missing = [];
  for (const { id } of hand) {
    const markup = extractSymbol(previous, id);
    if (markup) kept.push({ id, markup });
    else missing.push(id);
  }
  if (missing.length) {
    die(
      `hand authored ${missing.length > 1 ? 'symbols' : 'symbol'} ` +
        `${missing.map((id) => `#${id}`).join(', ')} not found in the existing ` +
        `${path.relative(ROOT, OUT)}. Refusing to drop the brand marks. ` +
        'Restore the sprite from git, or remove the @hand line if it is really gone.'
    );
  }
  return kept;
}

/* ---------------------------------------------------------------------------
   3. Path extraction
   ------------------------------------------------------------------------ */

const round = (v) => {
  const r = Math.round(v / QUANTUM) * QUANTUM;
  return Object.is(r, -0) ? 0 : r;
};

/**
 * Round every coordinate to the output precision, then drop the geometry that
 * only exists to keep the FILL axis interpolatable: zero length segments and
 * whole contours that collapse to a single point in the FILL 0 master. They
 * render nothing and cost roughly a third of the file.
 */
function clean(commands) {
  const rounded = commands.map((c) => {
    const out = { type: c.type };
    for (const k of ['x', 'y', 'x1', 'y1', 'x2', 'y2']) {
      if (c[k] !== undefined) out[k] = round(c[k]);
    }
    return out;
  });

  const contours = [];
  for (const c of rounded) {
    if (c.type === 'M') contours.push([c]);
    else if (contours.length) contours.at(-1).push(c);
    else die('path data begins with something other than a moveto.');
  }

  const kept = [];
  for (const contour of contours) {
    let [{ x: cx, y: cy }] = contour;
    const survivors = [contour[0]];
    let moved = false;

    for (const c of contour.slice(1)) {
      if (c.type === 'Z') {
        survivors.push(c);
        continue;
      }
      const points = [[c.x, c.y]];
      if (c.x1 !== undefined) points.push([c.x1, c.y1]);
      if (c.x2 !== undefined) points.push([c.x2, c.y2]);
      if (points.every(([x, y]) => x === cx && y === cy)) continue;
      survivors.push(c);
      moved = true;
      cx = c.x;
      cy = c.y;
    }
    if (moved) kept.push(...survivors);
  }
  return kept;
}

/** Shortest exact spelling of a coordinate: "6.78", ".5", "-.5", "12". */
function num(v) {
  let s = v.toFixed(DECIMALS).replace(/0+$/, '').replace(/\.$/, '');
  if (s.startsWith('0.')) s = s.slice(1);
  else if (s.startsWith('-0.')) s = `-${s.slice(2)}`;
  if (s === '-0' || s === '') s = '0';
  return s;
}

/**
 * Serialise to SVG path data. Uses the shorthands that are exact rather than
 * approximate: H and V for axis aligned lines, T for the on curve points
 * TrueType implies at the midpoint of two consecutive control points (the
 * reflection T computes is that midpoint, exactly), and the SVG rule that a
 * repeated command letter may be omitted.
 */
function serialise(commands) {
  let out = '';
  let letter = '';
  let previous = '';
  let cx = 0;
  let cy = 0;
  let lastQ = null;

  const put = (n) => {
    const s = num(n);
    /* A separator is only needed when the number cannot start on its own. It
       can when it directly follows a command letter (previous is reset to ""),
       when it opens with "-", or when it opens with "." and the number before
       it already carried one: all three terminate the previous token. */
    if (previous !== '' && !s.startsWith('-') && !(s.startsWith('.') && previous.includes('.'))) {
      out += ' ';
    }
    out += s;
    previous = s;
  };
  const cmd = (c) => {
    if (c !== letter) {
      out += c;
      letter = c;
      previous = '';
    }
  };

  for (const c of commands) {
    switch (c.type) {
      case 'M':
        /* A moveto is never elided: two in a row would be read as a lineto. */
        out += 'M';
        letter = 'M';
        previous = '';
        put(c.x);
        put(c.y);
        break;
      case 'L':
        if (c.y === cy) {
          cmd('H');
          put(c.x);
        } else if (c.x === cx) {
          cmd('V');
          put(c.y);
        } else {
          cmd('L');
          put(c.x);
          put(c.y);
        }
        break;
      case 'Q':
        if (lastQ && lastQ.x1 + c.x1 === 2 * cx && lastQ.y1 + c.y1 === 2 * cy) {
          cmd('T');
          put(c.x);
          put(c.y);
        } else {
          cmd('Q');
          put(c.x1);
          put(c.y1);
          put(c.x);
          put(c.y);
        }
        break;
      case 'C':
        cmd('C');
        put(c.x1);
        put(c.y1);
        put(c.x2);
        put(c.y2);
        put(c.x);
        put(c.y);
        break;
      case 'Z':
        out += 'Z';
        letter = '';
        previous = '';
        break;
      default:
        die(`unexpected path command "${c.type}".`);
    }
    lastQ = c.type === 'Q' ? { x1: c.x1, y1: c.y1 } : null;
    if (c.type !== 'Z') {
      cx = c.x;
      cy = c.y;
    }
  }
  return out;
}

/**
 * Read the emitted path data back into absolute commands. The compaction above
 * is only safe if this reproduces exactly what went in, so every glyph is round
 * tripped before it reaches the sprite.
 */
function parsePathData(d) {
  const tokens = d.match(/[MLHVQTCZ]|-?(?:\d+\.?\d*|\.\d+)/g) || [];
  const commands = [];
  let i = 0;
  let letter = '';
  let cx = 0;
  let cy = 0;
  let sx = 0;
  let sy = 0;
  let lastQ = null;
  const n = () => {
    const t = tokens[i++];
    if (t === undefined || /[A-Z]/.test(t)) throw new Error(`expected a number, got ${t}`);
    return parseFloat(t);
  };

  while (i < tokens.length) {
    if (/[A-Z]/.test(tokens[i])) letter = tokens[i++];
    if (!letter) throw new Error('path data does not start with a command');
    if (letter === 'Z') {
      commands.push({ type: 'Z' });
      cx = sx;
      cy = sy;
      lastQ = null;
      letter = '';
      continue;
    }
    let cmd;
    if (letter === 'M') {
      cmd = { type: 'M', x: n(), y: n() };
      sx = cmd.x;
      sy = cmd.y;
      letter = 'L'; /* implicit lineto after a moveto */
    } else if (letter === 'L') cmd = { type: 'L', x: n(), y: n() };
    else if (letter === 'H') cmd = { type: 'L', x: n(), y: cy };
    else if (letter === 'V') cmd = { type: 'L', x: cx, y: n() };
    else if (letter === 'Q') cmd = { type: 'Q', x1: n(), y1: n(), x: n(), y: n() };
    else if (letter === 'T') {
      const rx = lastQ ? 2 * cx - lastQ.x1 : cx;
      const ry = lastQ ? 2 * cy - lastQ.y1 : cy;
      cmd = { type: 'Q', x1: rx, y1: ry, x: n(), y: n() };
    } else if (letter === 'C')
      cmd = { type: 'C', x1: n(), y1: n(), x2: n(), y2: n(), x: n(), y: n() };
    else throw new Error(`unsupported command ${letter}`);

    commands.push(cmd);
    lastQ = cmd.type === 'Q' ? { x1: cmd.x1, y1: cmd.y1 } : null;
    cx = cmd.x;
    cy = cmd.y;
  }
  return commands;
}

function assertRoundTrip(id, commands, d) {
  let back;
  try {
    back = parsePathData(d);
  } catch (e) {
    die(`#${id}: emitted path data does not parse (${e.message}).`);
  }
  const same =
    back.length === commands.length &&
    back.every((b, i) => {
      const a = commands[i];
      if (a.type !== b.type) return false;
      return ['x', 'y', 'x1', 'y1', 'x2', 'y2'].every(
        (k) => (a[k] === undefined && b[k] === undefined) || Math.abs(a[k] - b[k]) < QUANTUM / 2
      );
    });
  if (!same) die(`#${id}: emitted path data does not read back as the glyph it came from.`);
}

/* ---------------------------------------------------------------------------
   4. Assembly
   ------------------------------------------------------------------------ */

const CLOSE = '</svg>';

/**
 * Splice the symbol block in before the sprite's closing tag.
 *
 * lastIndexOf, not indexOf: the header comment carries a usage example that
 * contains its own </svg>, and inserting at the first match writes the whole
 * icon set inside the comment and corrupts the file.
 */
function spliceBeforeLastClose(document, block) {
  const at = document.lastIndexOf(CLOSE);
  if (at < 0) die('the sprite skeleton has no closing tag.');
  return `${document.slice(0, at)}${block}${document.slice(at)}`;
}

function header(counts) {
  /* A double hyphen is illegal inside an XML comment, so there is none in here
     and none in any wording this interpolates. */
  return `<svg xmlns="http://www.w3.org/2000/svg" style="display:none">
<!-- GENERATED FILE. Do not edit by hand: your changes will be overwritten.

     Regenerate with \`npm run icons\`, which reads the names listed in
     tools/icons/icons.txt out of the Material Symbols Rounded variable font.
     To add, remove or swap an icon, edit that list and rerun the script.

     Usage:
       <svg class="icon"><use href="deck-icons.svg#check"></use></svg>

     Every icon ships in two cuts. #name is wght 400, for .icon, .icon-lg and
     .icon-xl. #name-sm is wght 500, the heavier cut that holds up at 16px, for
     .icon-sm. Both are 24x24, filled with currentColor, so they inherit color
     and retune with the brand hue like everything else.

     ${counts.generated} generated symbols (${counts.icons} icons x 2 cuts) plus
     ${counts.hand} hand authored.

     Material Symbols by Google, licensed under the Apache License, Version 2.0.
     Full text: tools/icons/LICENSE.txt, or
     https://www.apache.org/licenses/LICENSE-2.0
     The glyph outlines below are derived from that font. The brand marks at the
     end of this file are not: those are Deck's own and are hand authored.
-->
</svg>
`;
}

/* ---------------------------------------------------------------------------
   5. Run
   ------------------------------------------------------------------------ */

function main() {
  if (!fs.existsSync(FONT)) {
    die(
      `${path.relative(ROOT, FONT)} not found.\n  The Material Symbols font is a build time ` +
        'source and is not committed. Download Material Symbols Rounded from\n  ' +
        'https://fonts.google.com/icons and unzip it into tools/icons/.'
    );
  }

  const { entries, hand } = readList(LIST);
  const previous = fs.existsSync(OUT) ? fs.readFileSync(OUT, 'utf8') : null;
  const carried = carryOverHandAuthored(hand, previous);

  const buffer = fs.readFileSync(FONT);
  const font = opentype.parse(
    buffer.buffer.slice(buffer.byteOffset, buffer.byteOffset + buffer.byteLength)
  );
  if (font.unitsPerEm !== UPM) die(`expected a ${UPM} upm font, got ${font.unitsPerEm}.`);
  if (!font.tables.fvar || !font.tables.gvar) die('that font is not a variable font.');

  const missing = [];
  const outside = [];
  const symbols = [];
  let commandCount = 0;

  for (const cut of CUTS) {
    font.variation.set(cut.axes);

    for (const { id, glyph } of entries) {
      const index = font.nameToGlyphIndex(glyph);
      if (!index || index <= 0) {
        if (!cut.suffix) missing.push({ id, glyph });
        continue;
      }

      /* getPath(x, y, fontSize): the baseline sits at y and the em scales to
         fontSize, so origin (0, 24) at size 24 maps the 0..960 design box onto
         0..24 with y flipped, which is the sprite's viewBox. The font argument
         is what applies the variation axes; without it you silently get the
         default instance. */
      const path2d = font.glyphs.get(index).getPath(0, VIEWBOX, VIEWBOX, undefined, font);
      const commands = clean(path2d.commands);
      if (!commands.length) {
        missing.push({ id: id + cut.suffix, glyph, reason: 'renders empty' });
        continue;
      }

      /* Verify where it actually landed rather than trusting the em box. */
      const box = commands.reduce(
        (b, c) => {
          for (const [x, y] of [
            [c.x, c.y],
            [c.x1, c.y1],
            [c.x2, c.y2],
          ]) {
            if (x === undefined) continue;
            b.x1 = Math.min(b.x1, x);
            b.y1 = Math.min(b.y1, y);
            b.x2 = Math.max(b.x2, x);
            b.y2 = Math.max(b.y2, y);
          }
          return b;
        },
        { x1: Infinity, y1: Infinity, x2: -Infinity, y2: -Infinity }
      );
      if (box.x1 < 0 || box.y1 < 0 || box.x2 > VIEWBOX || box.y2 > VIEWBOX) {
        outside.push({ id: id + cut.suffix, glyph, box });
      }

      const d = serialise(commands);
      assertRoundTrip(id + cut.suffix, commands, d);
      commandCount += commands.length;

      symbols.push(
        `<symbol id="${id}${cut.suffix}" viewBox="0 0 ${VIEWBOX} ${VIEWBOX}" ` +
          `fill="currentColor" stroke="none"><path d="${d}"/></symbol>`
      );
    }
  }

  if (missing.length) {
    die(
      `${missing.length} icon(s) could not be extracted:\n` +
        missing
          .map((m) => `    ${m.id} = ${m.glyph}${m.reason ? ` (${m.reason})` : ' (no such glyph)'}`)
          .join('\n') +
        '\n  Check the names against https://fonts.google.com/icons.'
    );
  }

  const counts = { icons: entries.length, generated: symbols.length, hand: carried.length };
  const block =
    `\n${symbols.join('\n')}\n` +
    (carried.length
      ? '\n<!-- Hand authored brand marks. Not from the font and not regenerated:\n' +
        '     the generator copies these across from the previous sprite verbatim.\n' +
        '     They are filled rather than stroked, so they want .icon-fill or your\n' +
        '     own fill instead of the .icon stroke defaults.\n-->\n' +
        `${carried.map((c) => c.markup).join('\n')}\n`
      : '');

  const document = spliceBeforeLastClose(header(counts), block);

  const before = previous === null ? 0 : Buffer.byteLength(previous, 'utf8');
  fs.writeFileSync(OUT, document, 'utf8');
  const after = Buffer.byteLength(document, 'utf8');
  const gzipped = zlib.gzipSync(Buffer.from(document, 'utf8')).length;

  const kb = (n) => `${(n / 1000).toFixed(1)} KB`;
  console.log(`
  icons: wrote ${path.relative(ROOT, OUT)}

    ${counts.icons} icons x ${CUTS.length} cuts = ${counts.generated} generated symbols
    ${counts.hand} hand authored symbols carried over: ${carried.map((c) => `#${c.id}`).join(', ')}
    ${counts.generated + counts.hand} symbols total, ${commandCount} path commands
    ${outside.length} glyph(s) outside the 0 0 24 24 viewBox
    size ${kb(before)} to ${kb(after)}  (${kb(gzipped)} gzipped)
`);

  if (outside.length) {
    console.log('  glyphs outside the viewBox:');
    for (const o of outside) {
      console.log(
        `    #${o.id} (${o.glyph}) x ${o.box.x1}..${o.box.x2}  y ${o.box.y1}..${o.box.y2}`
      );
    }
    console.log('');
  }
}

main();
