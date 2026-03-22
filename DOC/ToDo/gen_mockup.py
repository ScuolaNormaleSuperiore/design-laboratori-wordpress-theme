#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Generate SVG mockups for the DLI dashboard."""

import os

OUT_DIR = os.path.dirname(os.path.abspath(__file__))

# ─── data ─────────────────────────────────────────────────────────────────────
SECTIONS = [
    {
        "name": "Persone e struttura",
        "color": "#003D8F",
        "bg": "#E3EBF6",
        "cards": [
            {"title": "Persone",
             "contenuti": ["Elenco persone", "Aggiungi persona", "Tipologia persone"],
             "tassonomie": ["Tag", "Struttura"]},
            {"title": "Tipologie persone",
             "contenuti": ["Elenco tipologie", "Aggiungi tipologia"],
             "tassonomie": []},
        ],
    },
    {
        "name": "Attivita scientifica",
        "color": "#005D2E",
        "bg": "#E1F0EA",
        "cards": [
            {"title": "Indirizzi di ricerca",
             "contenuti": ["Elenco indirizzi", "Aggiungi indirizzo"],
             "tassonomie": ["Tag"]},
            {"title": "Progetti",
             "contenuti": ["Elenco progetti", "Aggiungi progetto"],
             "tassonomie": ["Tag"]},
            {"title": "Pubblicazioni",
             "contenuti": ["Elenco pubblicazioni", "Aggiungi pubblicazione"],
             "tassonomie": ["Tag", "Tipo pubblicazione"]},
        ],
    },
    {
        "name": "Comunicazione",
        "color": "#A93226",
        "bg": "#FDEEEC",
        "cards": [
            {"title": "Notizie",
             "contenuti": ["Elenco notizie", "Aggiungi notizia"],
             "tassonomie": ["Categorie", "Tag"]},
            {"title": "Eventi",
             "contenuti": ["Elenco eventi", "Aggiungi evento"],
             "tassonomie": ["Categorie", "Tag"]},
            {"title": "Banner",
             "contenuti": ["Elenco banner", "Aggiungi banner"],
             "tassonomie": []},
        ],
    },
    {
        "name": "Ricerca",
        "color": "#4A1777",
        "bg": "#F0E8F8",
        "cards": [
            {"title": "Brevetti",
             "contenuti": ["Elenco brevetti", "Aggiungi brevetto"],
             "tassonomie": ["Categorie", "Tag", "Area tematica"]},
            {"title": "Risorse tecniche",
             "contenuti": ["Elenco risorse", "Aggiungi risorsa"],
             "tassonomie": ["Categorie", "Tag", "Tipo risorsa"]},
            {"title": "Spin-off",
             "contenuti": ["Elenco spin-off", "Aggiungi spin-off"],
             "tassonomie": ["Categorie", "Tag", "Settore attivita"]},
            {"title": "Sponsor",
             "contenuti": ["Elenco sponsor", "Aggiungi sponsor"],
             "tassonomie": ["Categorie", "Tag"]},
        ],
    },
    {
        "name": "Impostazioni",
        "color": "#37474F",
        "bg": "#ECEFF1",
        "cards": [
            {"title": "Configurazione",
             "contenuti": ["Configurazione"],
             "tassonomie": []},
            {"title": "Luoghi",
             "contenuti": ["Elenco luoghi", "Aggiungi luogo"],
             "tassonomie": ["Tipologia luogo"]},
        ],
    },
]

# ─── layout constants ─────────────────────────────────────────────────────────
PAGE_W       = 1200
PAGE_PAD     = 20
CARD_GAP     = 14
CARD_W       = 264
CARD_H       = 185
HDR_H        = 38
SECTION_BAR  = 30
SEC_GAP_TOP  = 12
SEC_GAP_BOT  = 20
HEADER_H     = 125
BODY_PAD     = 10
LINE_H       = 15
LABEL_H      = 13
RX           = 5
COLS         = 4

FF = "Titillium Web, sans-serif"


def hdr_path(cx, cy, w=CARD_W, h=HDR_H, r=RX):
    return (
        f"M {cx+r},{cy} H {cx+w-r} "
        f"Q {cx+w},{cy} {cx+w},{cy+r} "
        f"V {cy+h} H {cx} V {cy+r} "
        f"Q {cx},{cy} {cx+r},{cy} Z"
    )


def card_svg(cx, cy, card, color):
    t = []
    # shadow
    t.append(
        f'<rect x="{cx+2}" y="{cy+3}" width="{CARD_W}" height="{CARD_H}" '
        f'rx="{RX}" fill="#00000018"/>'
    )
    # card bg
    t.append(
        f'<rect x="{cx}" y="{cy}" width="{CARD_W}" height="{CARD_H}" '
        f'rx="{RX}" fill="white" stroke="#dddddd" stroke-width="1"/>'
    )
    # header
    t.append(f'<path d="{hdr_path(cx, cy)}" fill="{color}"/>')
    # header title
    t.append(
        f'<text x="{cx+12}" y="{cy+HDR_H//2+5}" '
        f'font-family="{FF}" font-size="12" font-weight="bold" fill="white">'
        f'{card["title"]}</text>'
    )

    y = cy + HDR_H + BODY_PAD + LABEL_H
    t.append(
        f'<text x="{cx+BODY_PAD}" y="{y}" '
        f'font-family="{FF}" font-size="9" fill="#888888" '
        f'font-weight="bold" letter-spacing="0.8">CONTENUTI</text>'
    )
    y += 4
    for lnk in card["contenuti"]:
        y += LINE_H
        t.append(
            f'<text x="{cx+BODY_PAD+4}" y="{y}" '
            f'font-family="{FF}" font-size="11" fill="#0066CC">'
            f'&#8594; {lnk}</text>'
        )

    if card["tassonomie"]:
        y += 8
        t.append(
            f'<line x1="{cx+BODY_PAD}" y1="{y}" '
            f'x2="{cx+CARD_W-BODY_PAD}" y2="{y}" '
            f'stroke="#eeeeee" stroke-width="1"/>'
        )
        y += LABEL_H
        t.append(
            f'<text x="{cx+BODY_PAD}" y="{y}" '
            f'font-family="{FF}" font-size="9" fill="#888888" '
            f'font-weight="bold" letter-spacing="0.8">TASSONOMIE</text>'
        )
        y += 4
        for tax in card["tassonomie"]:
            y += LINE_H
            t.append(
                f'<text x="{cx+BODY_PAD+4}" y="{y}" '
                f'font-family="{FF}" font-size="11" fill="#0066CC">'
                f'&#8594; {tax}</text>'
            )

    return "\n".join(t)


def page_header():
    t = []
    t.append(
        f'<rect x="0" y="0" width="{PAGE_W}" height="{HEADER_H}" fill="white"/>'
    )
    t.append(
        f'<line x1="0" y1="{HEADER_H}" x2="{PAGE_W}" y2="{HEADER_H}" '
        f'stroke="#dddddd" stroke-width="1"/>'
    )
    # logo
    t.append('<rect x="20" y="16" width="88" height="88" rx="6" fill="#0066CC"/>')
    for text, dy in [("DESIGN", 0), ("LABORATORI", 16), ("ITALIA", 32)]:
        t.append(
            f'<text x="64" y="{54+dy}" text-anchor="middle" '
            f'font-family="{FF}" font-size="9" font-weight="bold" fill="white">'
            f'{text}</text>'
        )
    t.append(
        f'<text x="124" y="44" font-family="{FF}" '
        f'font-size="20" font-weight="bold" fill="#1a1a1a">'
        f'SitoFederato &#8212; Pannello di gestione</text>'
    )
    t.append(
        f'<text x="124" y="66" font-family="{FF}" font-size="13" fill="#555555">'
        f'Tema WordPress per laboratori e centri di ricerca '
        f'&#183; Scuola Normale Superiore &#183; v1.7.7</text>'
    )
    t.append(
        f'<text x="124" y="86" font-family="{FF}" font-size="12" fill="#777777">'
        f'Gestisci i contenuti del sito dalla dashboard.</text>'
    )
    return "\n".join(t)


# ─── SVG con sezioni ──────────────────────────────────────────────────────────
def svg_con_sezioni():
    parts = []
    y = HEADER_H + SEC_GAP_TOP

    for sec in SECTIONS:
        # section bar
        parts.append(
            f'<rect x="{PAGE_PAD}" y="{y}" width="{PAGE_W-2*PAGE_PAD}" '
            f'height="{SECTION_BAR}" rx="3" fill="{sec["bg"]}"/>'
        )
        parts.append(
            f'<rect x="{PAGE_PAD}" y="{y}" width="4" '
            f'height="{SECTION_BAR}" fill="{sec["color"]}" rx="1"/>'
        )
        parts.append(
            f'<text x="{PAGE_PAD+14}" y="{y + SECTION_BAR//2 + 5}" '
            f'font-family="{FF}" font-size="13" font-weight="bold" '
            f'fill="{sec["color"]}">{sec["name"]}</text>'
        )
        y += SECTION_BAR + 10

        for i, card in enumerate(sec["cards"]):
            cx = PAGE_PAD + i * (CARD_W + CARD_GAP)
            parts.append(card_svg(cx, y, card, sec["color"]))

        y += CARD_H + SEC_GAP_BOT

    total_h = y + 10
    header = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        f'<svg xmlns="http://www.w3.org/2000/svg" width="{PAGE_W}" '
        f'height="{total_h}" viewBox="0 0 {PAGE_W} {total_h}">',
        f'<rect width="{PAGE_W}" height="{total_h}" fill="#f0f0f1"/>',
        page_header(),
    ]
    return "\n".join(header + parts + ["</svg>"]), total_h


# ─── SVG senza sezioni ────────────────────────────────────────────────────────
def svg_senza_sezioni():
    all_cards = []
    for sec in SECTIONS:
        for card in sec["cards"]:
            all_cards.append((card, sec["color"]))

    parts = []
    y0 = HEADER_H + 20

    for idx, (card, color) in enumerate(all_cards):
        col = idx % COLS
        row = idx // COLS
        cx = PAGE_PAD + col * (CARD_W + CARD_GAP)
        cy = y0 + row * (CARD_H + CARD_GAP)
        parts.append(card_svg(cx, cy, card, color))

    rows = (len(all_cards) + COLS - 1) // COLS
    total_h = y0 + rows * (CARD_H + CARD_GAP) - CARD_GAP + 30

    header = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        f'<svg xmlns="http://www.w3.org/2000/svg" width="{PAGE_W}" '
        f'height="{total_h}" viewBox="0 0 {PAGE_W} {total_h}">',
        f'<rect width="{PAGE_W}" height="{total_h}" fill="#f0f0f1"/>',
        page_header(),
    ]
    return "\n".join(header + parts + ["</svg>"]), total_h


svg1, h1 = svg_con_sezioni()
svg2, h2 = svg_senza_sezioni()

p1 = os.path.join(OUT_DIR, "dashboard-con-sezioni.svg")
p2 = os.path.join(OUT_DIR, "dashboard-senza-sezioni.svg")

with open(p1, "w", encoding="utf-8") as f:
    f.write(svg1)

with open(p2, "w", encoding="utf-8") as f:
    f.write(svg2)

print(f"con-sezioni:    {PAGE_W}x{h1}px  -> {p1}")
print(f"senza-sezioni:  {PAGE_W}x{h2}px  -> {p2}")
print("Done.")
