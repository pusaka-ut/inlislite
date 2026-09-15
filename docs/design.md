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

## 4. Komponen Kunci OPAC & Admin

- **Header / Navbar:** Gradasi Navy dengan aksen emas UT di border bawah. Logo institusi dibungkus tautan aktif ke halaman beranda (`brand-link`) dengan kolom teks vertikal: Judul Putih Bold di atas dan Subtitle Emas UT di bawahnya.
- **Header Components Alignment:** Jam digital berwujud subtle translucent glass pill badge, tombol Tampung, Login, dan Registrasi berwujud modern action pills (Registrasi berlatar emas UT). Keranjang kosong (`a.bookmarkShow:empty`) otomatis disembunyikan.
- **Sticky Footer Flexbox Lock:** Kunci layout `min-height: 100vh` pada `body` & `.wrapper`, dengan `margin-top: auto` pada footer untuk mencegah footer melayang pada konten pendek.
- **Ambient Canvas Background:** Kanvas latar belakang diberikan ambient lighting berlapis memadukan `#f8fafc` dengan radial gradient lembut (`radial-gradient(1200px 500px at 50% -50px, rgba(0, 43, 85, 0.06), rgba(0, 64, 128, 0.02), transparent)`).
- **Search Hero & Advance Search Card:**
  - Search Hero Sederhana: Input field dengan border fisik (`1.5px solid #cbd5e1`), tombol Cari proporsional (`col-md-2`), dan sublinks teratur dengan divider halus.
  - Advance Search Card: Form dibungkus dalam elevated card putih `border-radius: 16px` dengan border atas biru royal UT, field input/dropdown setinggi `42px`, dan tombol Cari gradasi UT Navy Deep. Sembunyikan kontainer kosong (`.box-default:empty`).
- **Book Cards & Carousel:**
  - Kartu buku hasil pencarian: Elevated ambient cards dengan border-radius `14px`, soft ambient multi-layer shadow, hover lift, dan sampul ber-radius `8px`.
  - Carousel koleksi (Unggulan & Terbaru): Kartu buku ambient ber-radius `14px` dengan 2-line title clamp, safe padding `42px` pada kontainer slider, dan tombol navigasi sirkular sempurna (`44px x 44px`, `border-radius: 50%`, `bottom: auto`) putih ber-backdrop blur (*anti-numpul*).
  - Section Header: Aksen bar emas vertikal UT (`border-left: 4px solid #ffcc00`) dengan tipografi tebal UT Deep Navy.
- **Favicon Standar:**
  - Lokasi Utama: `uploaded_files/aplikasi/favicon.png`
  - Lokasi Fallback: `opac/favicon.ico` dan `opac/favicon.png`
- **Badges:** Pill badge halus dengan border transparan lembut.
- **Backend Admin Tables, QueryBuilder & Action Toolbar (Fase 18):**
  - Static Asset Pipeline: Melayani `modern-adminlte.css` langsung dari direktori statis publik `backend/assets_b/css/` dan didaftarkan pada `backend/assets_b/AppAsset.php` serta tautan langsung ber-timestamp cache-busting `?v=...`, menjamin kebal 100% dari masalah hash caching Yii AssetManager.
  - QueryBuilder Card: Menggantikan latar krem usang dengan Elevated White Card (`#ffffff`), border halus (`#e2e8f0`), shadow ambient lembut, garis cabang slate (`#cbd5e1`), dan tombol `[Dan] [Atau]` berupa Segmented Pill Switch modern (Active: UT Deep Navy `#002b55`, Inactive: Slate `#f1f5f9`). Tombol Cari dan Ulangi berjarak lega (`gap: 8px`).
  - Batch Action Toolbar Card: Dibungkus ke dalam kartu putih elegan ber-radius `10px`, flex horizontal terpusat, penguncian penyembunyian tombol Download ekstra saat opsi reguler aktif, dan spasi antar tombol (`margin: 0 4px; gap: 8px`).
  - Table Panel Header: Mengadopsi Navy Gradient (`#002b55` ke `#004080`) dengan aksen garis emas UT `2px`, header kolom slate abu-abu bersih (`#f8fafc`), dan hover state baris tabel (`#f0f7ff`).
  - Micro-Pill Badges: Badge RDA/AACR berupa kapsul modern (`#ecfdf5` mint green / `#eff6ff` royal blue) dan BIBID berupa code pill badge monospace (`#f1f5f9`).
  - Data Table Links: Seluruh tautan data konten (`.table td a:not(.btn)`, `.catalog-title-link`) wajib menggunakan warna kontras tinggi UT Deep Navy (`#002b55`), bobot tebal (`font-weight: 700`), latar transparan murni, dan garis bawah saat di-hover (`#1d4ed8`) memenuhi standar aksesibilitas WCAG AAA.
  - Table Action Buttons: Tombol aksi murni dispesifikasikan via `.table td a.btn` sehingga tidak merembet ke teks konten data.
  - Form Repeater Buttons: Tombol `.input-group-btn > .btn` dikunci pada tinggi presisi `36px` sejajar penuh dengan field input.
- **Backend Admin Tables, Sanitasi Data & Interaktivitas Hover (Fase 19):**
  - Sanitasi Nilai Grid (BIBID & Title): Seluruh data teks kolom seperti BIBID dan Judul wajib disanitasi menggunakan `strip_tags()` sebelum diproses atau di-encode, membersihkan tag HTML lawas bawaan query database (`<div style=width:120px >`) dan menghasilkan tampilan monospace badge murni.
  - Interactive Title Hover: Tautan judul buku (`.catalog-title-link`) menerapkan transisi mikro enterprise (`transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1)`), bertransisi mulus dari UT Deep Navy ke UT Royal Blue (`#2563eb`), border bawah aksen tipis (`2px solid #2563eb`), dan pergeseran mikro responsif (`transform: translateX(3px)`). Deklarasi warna inline `!important` ditiadakan agar pseudo-class hover bekerja optimal.
  - ActionColumn Button Bypass PJAX: Seluruh tautan aksi halaman penuh (seperti tombol `[Detail]` dan link judul) wajib menyertakan `'data-pjax' => '0'` guna mencegah intersepsi PJAX yang memicu hanging spinner layar penuh.
  - Detail Button Styling: Menggunakan gradien soft sky blue (`#0284c7` ke `#0369a1`) dengan hover elevation halus (`translateY(-1px)` dan shadow `#0284c7`).
- **OPAC Ambient Real Photo Background & Squircle Carousel (Fase 20):**
  - Ambient Photo Veil: Integrasi foto asli interior perpustakaan kontemporer hangat (`modern_library_bg.jpg`) beresolusi tinggi yang dilapisi multi-stop linear gradient veil (`rgba(248, 250, 252, 0.92)` hingga `0.89)` pada `body`. Konten kartu buku, form pencarian, dan tabel tetap memiliki kontras tinggi WCAG AAA dan mengambang jernih di atasnya.
  - Squircle Carousel Controls (Anti-Lonjong): Tombol navigasi slider berupa squircle simetris `40px x 40px` dengan `border-radius: 10px`, ikon chevron FontAwesome (`fa-chevron-left` dan `fa-chevron-right`), bayangan lembut `0 4px 12px rgba(0, 43, 85, 0.12)`, serta hover elevation bertransisi ke UT Deep Navy (`#002b55`).
  - Responsive Multi-Breakpoint: Adaptif pada Tablet (991px), Mobile (767px), dan Small Mobile (480px). Mengatur `background-attachment: scroll` pada layar mobile demi performa render 60fps yang ringan tanpa stutter, kontrol slider berukuran adaptif `36px x 36px` pada batas aman layar, dan pembatasan judul buku 2-baris clamp setinggi 38px.
- **OPAC Global Brand Header, Pure Floating Chevron & Visitor Counter Pill (Fase 21):**
  - Clean Slate Canvas: Mengembalikan kanvas latar belakang ke Clean Slate `#f8fafc` dengan radial gradient aksen halus, menghilangkan strip foto bertumpuk dan memberikan konsistensi visual 100% di seluruh viewport.
  - Pure Floating Carousel Controls: Menghapus seluruh background kotak/lingkaran/oval pada navigasi slider (`background: transparent !important; border: none !important; border-radius: 0 !important; box-shadow: none !important`). Menggunakan floating chevron FontAwesome (`#94a3b8`) dengan hover state dinamis bertransisi ke UT Deep Navy (`#002b55`) dan scale `1.2`, bebas dari distorsi bentuk di berbagai breakpoint.
  - Global Brand Link Header: Menyeragamkan seluruh layout OPAC (`main-sederhana.php`, `main.php`, `main-advance.php`, `main-search.php`, `main-sederhana-search.php`, `main-advance-search.php`, `main2.php`, dan `detail-opac/index.php`) menggunakan struktur `.brand-link` modern dengan logo 60px, `.brand-title` tebal putih, `.brand-subtitle` emas UT, dan `.header-right-wrapper`. Seluruh inline style `background-color: #FFF` pada `.wrapper` dibersihkan.
  - Visitor Counter Pill: Memindahkan dan merapikan penghitung pengunjung dari bilah putih terpisah ke dalam kontainer footer navy utama menggunakan komponen `.footer-counter-container` dan badge pill emas UT `.opac-counter-pill` (`background: rgba(255, 255, 255, 0.08)`, border semi-transparan, `border-radius: 9999px`, font 11.5px, icon `<i class="fa fa-eye"></i>`, dan angka kunjungan berformat ribuan). Konsisten hadir di seluruh 8 layout OPAC.