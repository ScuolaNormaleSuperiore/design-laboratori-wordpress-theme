const fs = require('fs');
const path = require('path');

const OUT_DIR = __dirname;

const SECTIONS = [
  {
    name: 'Persone e struttura', color: '#003D8F', bg: '#E3EBF6',
    cards: [
      { title: 'Persone',
        contenuti: ['Elenco persone', 'Aggiungi persona', 'Tipologia persone'],
        tassonomie: ['Tag', 'Struttura'] },
      { title: 'Tipologie persone',
        contenuti: ['Elenco tipologie', 'Aggiungi tipologia'],
        tassonomie: [] },
    ],
  },
  {
    name: 'Attivit\u00e0 scientifica', color: '#005D2E', bg: '#E1F0EA',
    cards: [
      { title: 'Indirizzi di ricerca',
        contenuti: ['Elenco indirizzi', 'Aggiungi indirizzo'],
        tassonomie: ['Tag'] },
      { title: 'Progetti',
        contenuti: ['Elenco progetti', 'Aggiungi progetto'],
        tassonomie: ['Tag'] },
      { title: 'Pubblicazioni',
        contenuti: ['Elenco pubblicazioni', 'Aggiungi pubblicazione'],
        tassonomie: ['Tag', 'Tipo pubblicazione'] },
    ],
  },
  {
    name: 'Comunicazione', color: '#A93226', bg: '#FDEEEC',
    cards: [
      { title: 'Notizie',
        contenuti: ['Elenco notizie', 'Aggiungi notizia'],
        tassonomie: ['Categorie', 'Tag'] },
      { title: 'Eventi',
        contenuti: ['Elenco eventi', 'Aggiungi evento'],
        tassonomie: ['Categorie', 'Tag'] },
      { title: 'Banner',
        contenuti: ['Elenco banner', 'Aggiungi banner'],
        tassonomie: [] },
    ],
  },
  {
    name: 'Ricerca', color: '#4A1777', bg: '#F0E8F8',
    cards: [
      { title: 'Brevetti',
        contenuti: ['Elenco brevetti', 'Aggiungi brevetto'],
        tassonomie: ['Categorie', 'Tag', 'Area tematica'] },
      { title: 'Risorse tecniche',
        contenuti: ['Elenco risorse', 'Aggiungi risorsa'],
        tassonomie: ['Categorie', 'Tag', 'Tipo risorsa'] },
      { title: 'Spin-off',
        contenuti: ['Elenco spin-off', 'Aggiungi spin-off'],
        tassonomie: ['Categorie', 'Tag', 'Settore attivit\u00e0'] },
      { title: 'Sponsor',
        contenuti: ['Elenco sponsor', 'Aggiungi sponsor'],
        tassonomie: ['Categorie', 'Tag'] },
    ],
  },
  {
    name: 'Impostazioni', color: '#37474F', bg: '#ECEFF1',
    cards: [
      { title: 'Configurazione',
        contenuti: ['Configurazione'],
        tassonomie: [] },
      { title: 'Luoghi',
        contenuti: ['Elenco luoghi', 'Aggiungi luogo'],
        tassonomie: ['Tipologia luogo'] },
    ],
  },
];

const PAGE_W      = 1200;
const PAGE_PAD    = 20;
const CARD_GAP    = 14;
const CARD_W      = 264;
const CARD_H      = 185;
const HDR_H       = 38;
const SECTION_BAR = 30;
const SEC_GAP_TOP = 12;
const SEC_GAP_BOT = 20;
const HEADER_H    = 125;
const BODY_PAD    = 10;
const LINE_H      = 15;
const LABEL_H     = 13;
const RX          = 5;
const COLS        = 4;
const FF          = 'Titillium Web, sans-serif';

function hdrPath(cx, cy, w = CARD_W, h = HDR_H, r = RX) {
  return `M ${cx+r},${cy} H ${cx+w-r} Q ${cx+w},${cy} ${cx+w},${cy+r} ` +
         `V ${cy+h} H ${cx} V ${cy+r} Q ${cx},${cy} ${cx+r},${cy} Z`;
}

function cardSvg(cx, cy, card, color) {
  const t = [];
  t.push(`<rect x="${cx+2}" y="${cy+3}" width="${CARD_W}" height="${CARD_H}" rx="${RX}" fill="#00000018"/>`);
  t.push(`<rect x="${cx}" y="${cy}" width="${CARD_W}" height="${CARD_H}" rx="${RX}" fill="white" stroke="#dddddd" stroke-width="1"/>`);
  t.push(`<path d="${hdrPath(cx, cy)}" fill="${color}"/>`);
  t.push(`<text x="${cx+12}" y="${cy + Math.floor(HDR_H/2) + 5}" font-family="${FF}" font-size="12" font-weight="bold" fill="white">${card.title}</text>`);

  let y = cy + HDR_H + BODY_PAD + LABEL_H;
  t.push(`<text x="${cx+BODY_PAD}" y="${y}" font-family="${FF}" font-size="9" fill="#888888" font-weight="bold" letter-spacing="0.8">CONTENUTI</text>`);
  y += 4;
  for (const lnk of card.contenuti) {
    y += LINE_H;
    t.push(`<text x="${cx+BODY_PAD+4}" y="${y}" font-family="${FF}" font-size="11" fill="#0066CC">&#8594; ${lnk}</text>`);
  }

  if (card.tassonomie.length > 0) {
    y += 8;
    t.push(`<line x1="${cx+BODY_PAD}" y1="${y}" x2="${cx+CARD_W-BODY_PAD}" y2="${y}" stroke="#eeeeee" stroke-width="1"/>`);
    y += LABEL_H;
    t.push(`<text x="${cx+BODY_PAD}" y="${y}" font-family="${FF}" font-size="9" fill="#888888" font-weight="bold" letter-spacing="0.8">TASSONOMIE</text>`);
    y += 4;
    for (const tax of card.tassonomie) {
      y += LINE_H;
      t.push(`<text x="${cx+BODY_PAD+4}" y="${y}" font-family="${FF}" font-size="11" fill="#0066CC">&#8594; ${tax}</text>`);
    }
  }

  return t.join('\n');
}

function pageHeader() {
  const t = [];
  t.push(`<rect x="0" y="0" width="${PAGE_W}" height="${HEADER_H}" fill="white"/>`);
  t.push(`<line x1="0" y1="${HEADER_H}" x2="${PAGE_W}" y2="${HEADER_H}" stroke="#dddddd" stroke-width="1"/>`);
  t.push(`<rect x="20" y="16" width="88" height="88" rx="6" fill="#0066CC"/>`);
  const labels = ['DESIGN', 'LABORATORI', 'ITALIA'];
  labels.forEach((lbl, i) => {
    t.push(`<text x="64" y="${54 + i*16}" text-anchor="middle" font-family="${FF}" font-size="9" font-weight="bold" fill="white">${lbl}</text>`);
  });
  t.push(`<text x="124" y="44" font-family="${FF}" font-size="20" font-weight="bold" fill="#1a1a1a">SitoFederato &#8212; Pannello di gestione</text>`);
  t.push(`<text x="124" y="66" font-family="${FF}" font-size="13" fill="#555555">Tema WordPress per laboratori e centri di ricerca &#183; Scuola Normale Superiore &#183; v1.7.7</text>`);
  t.push(`<text x="124" y="86" font-family="${FF}" font-size="12" fill="#777777">Gestisci i contenuti del sito dalla dashboard.</text>`);
  return t.join('\n');
}

function svgConSezioni() {
  const parts = [];
  let y = HEADER_H + SEC_GAP_TOP;

  for (const sec of SECTIONS) {
    parts.push(`<rect x="${PAGE_PAD}" y="${y}" width="${PAGE_W - 2*PAGE_PAD}" height="${SECTION_BAR}" rx="3" fill="${sec.bg}"/>`);
    parts.push(`<rect x="${PAGE_PAD}" y="${y}" width="4" height="${SECTION_BAR}" fill="${sec.color}" rx="1"/>`);
    parts.push(`<text x="${PAGE_PAD+14}" y="${y + Math.floor(SECTION_BAR/2) + 5}" font-family="${FF}" font-size="13" font-weight="bold" fill="${sec.color}">${sec.name}</text>`);
    y += SECTION_BAR + 10;

    sec.cards.forEach((card, i) => {
      const cx = PAGE_PAD + i * (CARD_W + CARD_GAP);
      parts.push(cardSvg(cx, y, card, sec.color));
    });

    y += CARD_H + SEC_GAP_BOT;
  }

  const totalH = y + 10;
  const header = [
    '<?xml version="1.0" encoding="UTF-8"?>',
    `<svg xmlns="http://www.w3.org/2000/svg" width="${PAGE_W}" height="${totalH}" viewBox="0 0 ${PAGE_W} ${totalH}">`,
    `<rect width="${PAGE_W}" height="${totalH}" fill="#f0f0f1"/>`,
    pageHeader(),
  ];
  return header.concat(parts).concat(['</svg>']).join('\n');
}

function svgSenzaSezioni() {
  const allCards = [];
  for (const sec of SECTIONS) {
    for (const card of sec.cards) {
      allCards.push({ card, color: sec.color });
    }
  }

  const parts = [];
  const y0 = HEADER_H + 20;

  allCards.forEach(({ card, color }, idx) => {
    const col = idx % COLS;
    const row = Math.floor(idx / COLS);
    const cx = PAGE_PAD + col * (CARD_W + CARD_GAP);
    const cy = y0 + row * (CARD_H + CARD_GAP);
    parts.push(cardSvg(cx, cy, card, color));
  });

  const rows = Math.ceil(allCards.length / COLS);
  const totalH = y0 + rows * (CARD_H + CARD_GAP) - CARD_GAP + 30;

  const header = [
    '<?xml version="1.0" encoding="UTF-8"?>',
    `<svg xmlns="http://www.w3.org/2000/svg" width="${PAGE_W}" height="${totalH}" viewBox="0 0 ${PAGE_W} ${totalH}">`,
    `<rect width="${PAGE_W}" height="${totalH}" fill="#f0f0f1"/>`,
    pageHeader(),
  ];
  return header.concat(parts).concat(['</svg>']).join('\n');
}

const svg1 = svgConSezioni();
const svg2 = svgSenzaSezioni();

fs.writeFileSync(path.join(OUT_DIR, 'dashboard-con-sezioni.svg'), svg1, 'utf8');
fs.writeFileSync(path.join(OUT_DIR, 'dashboard-senza-sezioni.svg'), svg2, 'utf8');

console.log('dashboard-con-sezioni.svg    -> OK');
console.log('dashboard-senza-sezioni.svg  -> OK');
