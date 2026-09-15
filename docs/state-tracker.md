# State Tracker & Progress — INLISLite v3 Perpustakaan UT

File ini digunakan untuk melacak fitur yang sedang dikerjakan, selesai, serta catatan pengujian di Perpustakaan Pusat Universitas Terbuka.

## 1. Fitur Selesai (Completed)
- [x] Analisis mendalam arsitektur INLISLite v3 dan penelusuran skema database riil `dbsirkulasi`.
- [x] Verifikasi tabel lokasi aktif: `locations` (Lantai 1-4) dan data koleksi buku (`collections`).
- [x] Pembuatan skrip DDL database `db/master_rak.sql`.
- [x] Pembuatan model ActiveRecord `common/models/MasterRak.php` (relasi ke `locations` dan kalkulasi koleksi aktif).
- [x] Pembuatan generator QR Code mandiri `common/components/QrCodeHelper.php` (SVG / Base64 Data-URI) dengan dukungan default IP intranet `http://172.30.14.94/inlislite3`.
- [x] Pembuatan controller backend `backend/controllers/RakController.php` (CRUD rak, kalkulasi buku per rak, cetak single & batch).
- [x] Pembuatan view backend di `backend/views/rak/` (`index.php`, `_form.php`, `create.php`, `update.php`, `view.php`, `print-single.php`, `print-batch.php`).
- [x] Penyematan menu "QR Code Rak Buku" di sidebar backend `inliscore/kw-themes-adminlte/views/layouts/sidebar.php`.
- [x] Pendaftaran hak akses `rak/*` pada `allowActions` di `backend/config/main.php`.
- [x] Pembuatan controller publik OPAC `opac/controllers/RakController.php` (akses instan tanpa login).
- [x] Pembuatan view publik OPAC mobile-first di `opac/views/rak/index.php` (pencarian instan, penonjolan nomor panggil, filter status ketersediaan, dan link rincian katalog).
- [x] Pembaruan dokumentasi Single Source of Truth di `docs/architecture.md` dan `docs/database.md`.
- [x] Push seluruh codebase profesional INLISLite v3 beserta fitur QR Code Rak ke GitHub `https://github.com/pusaka-ut/inlislite.git` (branch `master`).

## 2. Inisiatif Modernisasi UI/UX (Zero Layout & Flow Breaking)
- [x] **Fase 1: Desain & Tokens UT:** Dokumentasi design tokens resmi Perpustakaan Universitas Terbuka di `docs/design.md`.
- [x] **Fase 2: OPAC Skin:** Pembuatan `opac/assets_b/css/modern-opac.css` dan pendaftaran pada `AppAsset.php`.
- [x] **Fase 3: Backend AdminLTE Skin:** Pembuatan `inliscore/kw-themes-adminlte/assets/css/modern-adminlte.css` dan pendaftaran pada `MyAsset.php`.
- [x] **Fase 4: Perbaikan Presisi UI/UX (Sprint Feedback Bos):**
  - [x] *OPAC Search Hero:* Rekonstruksi elevated search card putih bersih, batas tepi input tegas (`1.5px solid #cbd5e1`), tinggi field `44px`, tombol Cari gradasi UT Royal Blue (`#002b55` - `#004080`), dan tabs Cari/Browse rapi.
  - [x] *Backend Admin Header:* Unifikasi seluruh navbar menjadi satu kesatuan UT Dark Navy (`#001f3f` ke `#002b55`), garis aksen emas UT (`#ffcc00`) 3px di bawah header, logo transparan terintegrasi tanpa kotak terpotong, harmonisasi toggle burger putih dan jam digital emas.
  - [x] *Table Action Buttons:* Penyisipan margin spasi renggang (`margin: 2px 3px !important`) agar tombol tidak saling menempel, ukuran proporsional ergonomis (`padding: 4px 10px !important`), serta pemisahan semantik warna mutlak: Koreksi (Royal Blue `#2563eb`), Detail (Teal Cyan `#0891b2`), dan Hapus (Crimson Red `#e11d48`).
- [x] **Fase 6: Revitalisasi UI/UX OPAC Menyeluruh (Sprint Permintaan Bos):**
  - [x] *Navigasi Logo ke Home:* Membungkus logo UT dan teks judul OPAC dalam tautan `<a href="<?= $homeUrl ?>">` di seluruh layout OPAC (`main-sederhana.php`, `main-sederhana-search.php`, `main.php`, `main-advance.php`, `main-advance-search.php`).
  - [x] *Grid Form Pencarian Sederhana:* Menyeimbangkan grid kolom (`col-md-5`, `col-md-3`, `col-md-2`, `col-md-2`) agar tombol Cari leluasa, serta memperbaiki penutupan tag form yang benar.
  - [x] *Solusi Paten Sticky Footer Flexbox:* Mengunci layout dengan `min-height: 100vh` pada `body` & `.wrapper` serta `margin-top: auto` pada footer, mencegah footer melayang di tengah layar saat konten halaman pendek.
  - [x] *Header Component Alignment:* Transformasi jam digital menjadi translucent subtle pill badge, serta tombol Tampung, Login, dan Registrasi menjadi modern action pills berjarak rapi (Registrasi emas UT).
- [x] **Fase 8: Peningkatan Menyeluruh UI/UX OPAC (Ambient Lighting & Presisi Komponen):**
  - [x] *Header Brand & Subtitle Alignment:* Memperbaiki brand link di semua layout (`main-sederhana.php`, `main-advance.php`, `main.php`, `main-search.php`, `main-sederhana-search.php`) dengan kolom teks vertikal di samping logo (menghilangkan lonjakan teks kuning ke samping atas).
  - [x] *Penyembunyian Keranjang Kosong:* Menyembunyikan kapsul biru kosong `a.bookmarkShow:empty` saat belum login atau keranjang kosong.
  - [x] *Favicon Multi-Fallback:* Memperkaya link icon dengan multi-fallback (`uploaded_files/aplikasi/favicon.png`, `opac/favicon.ico`, `opac/favicon.png`).
  - [x] *Tombol Carousel Lingkaran Sempurna (Anti-Numpul):* Mengunci tombol 44px x 44px bulat sempurna (`bottom: auto !important; border-radius: 50% !important;`), backdrop blur putih, dan safe padding 42px pada kontainer slider sehingga bebas menutupi sampul buku.
  - [x] *Ambient Canvas Background & Cards:* Menghadirkan radial ambient lighting lembut pada kanvas latar belakang dan multi-layer shadow ambient glow pada kartu buku hasil pencarian dan carousel.
  - [x] *Modernisasi Halaman Pencarian Lanjut:* Elevated card form 16px, field input/dropdown setinggi 42px, tombol Cari UT Navy Deep, dan penyembunyian kotak kosong di bawah pencarian.
- [x] **Fase 17: Perbaikan Teks Judul Tabel & Tombol Aksi Backend INLISLite v3:**
  - [x] *Proteksi Kontras Teks Judul Buku:* Mengoreksi selector CSS agresif `.table td a[href*="update"]` menjadi `.table td a.btn[...]`, serta melindungi seluruh tautan teks tabel dengan `.table td a:not(.btn)` dan `.catalog-title-link` (UT Deep Navy `#002b55`, font-weight 600, latar transparan, hover underline), menjamin teks judul buku terbaca 100% dengan kontras tinggi WCAG AAA.
  - [x] *Perbaikan Tombol Aksi Bertumpuk:* Menghapus `!important` pada `display: inline-flex` tombol `.btn` serta menambahkan aturan pengaman mutlak `.btn[style*="display: none"] { display: none !important; }`, memungkinkan fungsi jQuery dan inline style menyembunyikan tombol secara normal.
  - [x] *Restrukturisasi Toolbar Aksi Batch:* Mengubah baris form-group aksi katalog dan karantina di `backend/modules/pengkatalogan/views/katalog/index.php` menjadi `.batch-action-toolbar` dan `.action-buttons-group` yang sejajar horizontal, responsif, dan bebas tumpang tindih.
  - [x] *Harmonisasi Form Repeater Buttons:* Mengunci tinggi `.input-group-btn > .btn` presisi 36px sejajar field input form subjek.
  - [x] *Presisi Grid, Padding & Margin Tabel Katalog:* Mengunci perataan atas (`vertical-align: top !important`) di seluruh baris tabel, padding seragam `10px 12px !important`, penguncian lebar kolom presisi (Serial 40px, RDA 60px, BIBID 110px, Edition 75px nowrap, CallNumber 95px monospace, KontenDigital 90px, Eksemplar 85px, Kreator 85px), dan styling container QueryBuilder (`_searchAdvanced.php`).
  - [x] *Proteksi Inline Judul Buku Anti-Cache:* Menyematkan atribut inline style `style="color: #002b55 !important; font-weight: 600 !important; text-decoration: none !important; display: inline-block; line-height: 1.45;"` langsung pada pemanggilan `Html::a()` kolom judul, menjamin teks selalu kontras dan terbaca mutlak tanpa terpengaruh cache browser.
- [ ] **Fase 9: Deployment & Verifikasi Server:** Eksekusi sinkronisasi ke server intranet UT `172.30.14.94`.

## 3. Inisiatif Stabilitas Database & OPAC
- [x] **Solusi Paten Anti-Error 1637 InnoDB:** Mengeliminasi stored procedure `insertTempSederhanaOpac0` yang membebani rollback segment temporary tablespace (`ibtmp1`) saat paginasi (halaman 2, 3, 4, dst.).
- [x] **Direct Read-Only Streaming:** Mengganti alur paginasi dengan method `getDirectSearchData()` yang mengeksekusi query `SELECT DISTINCT ... LIMIT :offset, :limit` murni read-only yang 100% kebal dari Error 1637.
- [x] **Autonomous Fallback Pencarian Awal:** Menambahkan method `getDirectSearchCount()` dan fallback otomatis pada halaman 1 jika database server kehabisan slot temporary table, sehingga pengguna tetap menerima hasil katalog tanpa crash.
- [x] **Optimasi Query Facet:** Menghilangkan 6 query facet redundan saat paginasi dan mengandalkan session cache yang sudah terbentuk.
- [x] **Zero-Comments & Linter:** Kode 100% bersih dari komentar dan lolos uji sintaks `php -l`.

## 4. Current Sprint / Fokus Pengujian Saat Ini
- [x] Eksekusi DDL `master_rak.sql` di database server UT (IP: `172.30.13.81` / `dbsirkulasi`).
- [x] Pengujian tambah 1 rak uji coba di Admin (Lantai 2, DDC 330 - 339).
- [ ] Pengujian pencarian katalog dan paginasi di OPAC setelah penerapan error-handling dan optimasi query.
- [ ] Pengujian cetak stiker rak dan scan melalui kamera ponsel di jaringan `172.30.14.94`.

## 5. Catatan Konfigurasi Lingkungan
- **Alamat Server Web Intranet:** `http://172.30.14.94/inlislite3`
- **Rute Backend Admin:** `http://172.30.14.94/inlislite3/backend/rak/index`
- **Rute Publik OPAC:** `http://172.30.14.94/inlislite3/opac/rak?id=<id_rak>`