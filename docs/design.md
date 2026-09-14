# Design System & UI Guideline

File ini menjadi patokan bagi AI dan developer saat membuat antarmuka. Jangan gunakan warna atau ukuran di luar dari yang terdefinisi di sini.

## 1. Palet Warna (Color Palette)

- **Primary:** `#000000` (Ganti dengan kode hex yang sesuai)
- **Secondary:** `#FFFFFF`
- **Accent:** `#0000FF`
- **Background:** `#F9FAFB`
- **Text:** `#111827` (Dark gray, hindari pure black untuk teks)

## 2. Tipografi

- **Heading Font:** [Inter / Roboto / Plus Jakarta Sans]
- **Body Font:** [Sama dengan Heading]
- **Base Size:** 16px

## 3. Spacing & Radius (Border)

- Gunakan skala kelipatan 4px (Tailwind standard: p-1, p-2, p-4, dst).
- **Border Radius:** Standar komponen menggunakan `rounded-md` atau `8px`.

## 4. Komponen Kunci (Reusable)

- **Button:** Memiliki 3 state (Default, Hover, Disabled).
- **Input Field:** Memiliki state (Default, Focus/Ring, Error).