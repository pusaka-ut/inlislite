# Design System & UI Guideline — Perpustakaan Universitas Terbuka

Dokumentasi ini adalah Single Source of Truth untuk standar desain, warna, tipografi, dan komponen visual INLISLite v3 dan OPAC di Perpustakaan Pusat Universitas Terbuka.

## 1. Palet Warna Resmi (Universitas Terbuka Design Tokens)

- **UT Deep Navy (Primary Brand):** `#002b55` (Header utama, sidebar dark, navbar portal)
- **UT Dark Navy (Background Contrast):** `#001f3f` (Sidebar background, footer dark)
- **UT Royal Blue (Interactive Accent):** `#004080` (Tombol primer, link hover, focus ring)
- **UT Gold Accent (Prestige Highlight):** `#ffcc00` (Garis aksen navbar, badge penanda, status highlight)
- **Neutral Background (Canvas):** `#f8fafc` (Slate 50 — bersih, elegan, tidak menyilaukan)
- **Card Background:** `#ffffff` (Pure white dengan border tipis dan soft shadow)
- **Slate Border:** `#e2e8f0` (Slate 200 — garis pemisah tipis dan halus)
- **Text Primary:** `#1e293b` (Slate 800 — teks utama, kontras tinggi dan mudah dibaca)
- **Text Secondary:** `#64748b` (Slate 500 — metadata, caption, tanggal)
- **Text Muted:** `#94a3b8` (Slate 400 — placeholder teks)
- **Success Mint:** `#10b981` (Status "Tersedia di Tempat", background `#ecfdf5`)
- **Danger Rose:** `#ef4444` (Status "Sedang Dipinjam", background `#fef2f2`)

## 2. Tipografi

- **Primary Font Family:** `'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`
- **Monospace Font Family (Nomor Panggil & Barcode):** `'JetBrains Mono', Consolas, Monaco, monospace`
- **Heading Weight:** 700 (Bold) / 800 (ExtraBold)
- **Body Weight:** 400 (Regular) / 500 (Medium) / 600 (SemiBold)

## 3. Spacing, Elevasi & Radius

- **Border Radius Standar:**
  - Small Elements (Buttons, Badges): `6px`
  - Medium Elements (Cards, Tables, Input): `10px`
  - Large Elements (Hero Search Container): `16px`
  - Pills / Chips: `9999px`
- **Box Shadows (Soft Modern Elevation):**
  - Card Shadow: `0 4px 12px -2px rgba(0, 43, 85, 0.08)`
  - Elevated Shadow: `0 10px 25px -5px rgba(0, 43, 85, 0.12)`
  - Focus Ring: `0 0 0 3px rgba(0, 64, 128, 0.15)`

## 4. Komponen Kunci

- **Header / Navbar:** Gradasi Navy dengan aksen emas UT di border bawah.
- **Search Hero:** Input besar dengan soft focus ring dan tombol pencarian royal blue.
- **Book Cards:** Kartu terangkat dengan efek hover lift (`translateY(-2px)`), Call Number kontras tinggi.
- **Badges:** Pill badge halus dengan border transparan lembut.