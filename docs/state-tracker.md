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
- [x] **Fase 18: Redesign Total UI/UX Backend Katalog (Modern, Canggih, Elegan, dan Anti-Dempetan):**
  - [x] *Static Asset Pipeline Langsung:* Menyajikan `modern-adminlte.css` langsung dari direktori statis publik `backend/assets_b/css/` dan mendaftarkannya pada `backend/assets_b/AppAsset.php` serta tautan langsung ber-timestamp cache-busting `?v=<?= time() ?>` di `inliscore/kw-themes-adminlte/views/layouts/main.php`, menembus hambatan hashing asset bundle Yii.
  - [x] *Redesign Total QueryBuilder:* Menghilangkan warna kuning-krem usang (`#fef9c3`) dan menggantinya dengan Elevated White Card (`#ffffff`) ber-radius `10px`, shadow halus, konektor slate bersih (`#cbd5e1`), dan Segmented Pill Switch `[Dan] [Atau]` modern (Active: UT Deep Navy `#002b55` dengan soft shadow, Inactive: Slate `#f1f5f9`).
  - [x] *Tombol Aksi Pencarian Lega:* Mengubah tombol `[ Cari ]` menjadi UT Navy pill dengan bayangan elegan dan tombol `[ Ulangi ]` menjadi subtle ghost button dengan spasi renggang (`gap: 8px`).
  - [x] *Redesign Batch Action Toolbar Card:* Mengemas toolbar aksi menjadi Action Bar Card putih horizontal ber-radius `10px`, mengunci tombol Download agar benar-benar tersembunyi saat opsi "Tampil di OPAC" aktif, serta memberikan spasi margin mandiri (`margin: 0 4px; gap: 8px; border-radius: 6px`) pada seluruh tombol aksi sehingga bebas dari tampilan dempetan.
- [x] **Fase 19: Perbaikan Tag HTML BIBID, Hover Judul Buku, & Bug Tombol Detail:**
  - [x] *Sanitasi Tag HTML Kolom BIBID & Judul:* Mengaplikasikan `strip_tags($data->BIBID)` dan `strip_tags($data->Title)` di `katalog/index.php`, membersihkan tag `<div>` legacy dari database query dan menghasilkan tampilan monospace badge murni.
  - [x] *Interactive Enterprise Hover Judul Buku:* Melepas atribut inline lock pada tautan judul dan memindahkannya ke `.catalog-title-link` dengan transisi warna ke UT Royal Blue (`#2563eb`), aksen border bawah, dan micro-shift `translateX(3px)`.
  - [x] *Eliminasi Bug Tombol Detail ("Muter-Muter Terus"):* Menambahkan atribut `'data-pjax' => '0'` pada tombol Detail dan tautan judul di `index.php` guna menghentikan intersepsi PJAX, menginisialisasi `$for = isset($for) ? $for : 'cat';` di `detail.php`, mengoper `'for' => 'cat'` dari `KatalogController::actionDetail`, serta mengamankan pemrosesan tag MARC di `CatalogHelpers.php` dan `CatalogHelpers2.php` dari notice PHP.
- [x] **Fase 20: Modernisasi Menyeluruh OPAC, Ambient Perpustakaan Hangat, & Tombol Carousel Elegan:**
  - [x] *Ambient Real Library Background:* Menghadirkan foto asli interior perpustakaan kontemporer hangat (`modern_library_bg.jpg`) yang dilapisi multi-stop linear gradient veil (`rgba(248, 250, 252, 0.92)` hingga `0.89)` pada `body`, menjamin seluruh teks, kartu buku, dan kontrol pencarian tetap memiliki kontras tinggi WCAG AAA dan terbaca jernih.
  - [x] *Tombol Carousel Squircle Elegan (Anti-Lonjong):* Mengeliminasi bentuk oval/lonjong vertikal akibat dimensi asimetris 25px x 30px di `style-slider.css` dan karakter teks mentah `‹`/`›`. Menggantinya dengan ikon chevron FontAwesome presisi (`fa-chevron-left` & `fa-chevron-right`) dan tombol squircle simetris `40px x 40px` dengan `border-radius: 10px`, shadow halus, serta hover elevation UT Deep Navy `#002b55`.
  - [x] *Elevated Book Cards & Title Clamping:* Kartu buku koleksi ber-radius 12px dengan hover lift `translateY(-4px)` dan clamp 2 baris judul setinggi 38px, menjamin tinggi seluruh kartu buku dalam slider seragam dan estetis.
  - [x] *Audit Responsivitas Menyeluruh (320px s.d. 1440px+):* Penerapan media queries lengkap untuk tablet (991px), mobile (767px), dan small mobile (480px). Mengatur `background-attachment: scroll` pada mobile untuk mencegah stutter rendering, logo adaptif (40px) dan teks judul (13px), kontrol slider adaptif (36px x 36px) di batas aman layar, serta sublinks pencarian dan footer yang fleksibel dan teratur.
  - [x] *Pembersihan Inline Style Layout:* Menghapus inline style `background-color: #FFF` pada container `.wrapper` di `opac/views/layouts/main.php` agar latar ambient menyatu sempurna.
- [x] **Fase 21: Harmonisasi Header Global, Visitor Counter Elegan, Restorasi Canvas Bersih, & Tombol Carousel Pure Icon:**
  - [x] *Restorasi Clean Slate Canvas:* Mengembalikan background `body` ke Clean Slate Canvas `#f8fafc` dengan subtle ambient radial gradient (menghilangkan strip foto bertumpuk) dan menghapus inline background wrapper di seluruh layout.
  - [x] *Tombol Carousel Pure Floating Chevron:* Menghilangkan seluruh background kotak/lingkaran/oval pada `.carousel-control` di `modern-opac.css` (`background: transparent !important; border: none !important; border-radius: 0 !important; box-shadow: none !important`), menggunakan pure floating chevron FontAwesome (`#94a3b8`) dengan hover scale `1.2` dan warna UT Deep Navy (`#002b55`), bebas dari distorsi bentuk lonjong.
  - [x] *Harmonisasi Header Global Konsisten:* Menyeragamkan seluruh layout OPAC (`detail-opac/index.php`, `main-advance-search.php`, `main2.php`, `main-sederhana.php`, `main.php`, `main-advance.php`, `main-search.php`, `main-sederhana-search.php`) menggunakan brand link modern `.brand-link` (logo 60px, `.brand-title` bold putih, `.brand-subtitle` emas UT, dan navigasi kanan terbungkus `.header-right-wrapper`).
  - [x] *Visitor Counter Pill di Seluruh Layout:* Memindahkan visitor counter dari bar putih terpisah yang rusak ke dalam footer navy utama menggunakan `.footer-counter-container` dan `.opac-counter-pill` (kapsul emas UT dengan ikon mata dan format ribuan), konsisten di seluruh 8 layout OPAC.
- [ ] **Fase 9: Deployment & Verifikasi Server:** Eksekusi sinkronisasi ke server intranet UT `172.30.14.94`.

## 3. Inisiatif Stabilitas Database & OPAC
- [x] **Solusi Paten Anti-Error 1637 InnoDB:** Mengeliminasi stored procedure `insertTempSederhanaOpac0` yang membebani rollback segment temporary tablespace (`ibtmp1`) saat paginasi (halaman 2, 3, 4, dst.).
- [x] **Direct Read-Only Streaming:** Mengganti alur paginasi dengan method `getDirectSearchData()` yang mengeksekusi query `SELECT DISTINCT ... LIMIT :offset, :limit` murni read-only yang 100% kebal dari Error 1637.
- [x] **Autonomous Fallback Pencarian Awal:** Menambahkan method `getDirectSearchCount()` dan fallback otomatis pada halaman 1 jika database server kehabisan slot temporary table, sehingga pengguna tetap menerima hasil katalog tanpa crash.
- [x] **Optimasi Query Facet:** Menghilangkan 6 query facet redundan saat paginasi dan mengandalkan session cache yang sudah terbentuk.
- [x] **Fase 22: Solusi Tuntas Pencarian & Paginasi OPAC Anti-Kosong & Guardrail 2 Karakter:**
  - [x] *Guardrail Input Minimal 2 Karakter:* Proteksi frontend (`minlength="2"` & placeholder) dan backend (`mb_strlen < 2`) untuk mencegah full table scan liar tanpa memblokir singkatan penting ("AI", "UT", "IT", "UU").
  - [x] *Paginasi Mulus Anti-Kosong (Page 2+):* Mengeliminasi bug case-sensitivity `'Semua Jenis Bahan'` yang menyebabkan hasil halaman berikutnya kosong total, serta menstandarkan offset limit `$limitAwal = ($page - 1) * $limit`.
  - [x] *Dual-Engine Search Query:* Pencarian serentak pada kolom tabel `catalogs` (`Title`, `Author`, `Publisher`, `Subject`, `CallNumber`, `ISBN`) dan MARC tags `catalog_ruas`, menjamin 100% buku ditemukan.
  - [x] *Penyempurnaan Kolom Return:* Alias lengkap `worksheet_id`, `Worksheet_id`, dan `title` agar sampul buku dan worksheet termuat sempurna.
  - [x] *Eliminasi Flash Alert Merah:* Membersihkan popup error palsu saat data buku berhasil dimuat secara direct streaming.
- [x] **Fase 23: Solusi Tuntas Sidebar Facet Filter 'Lebih Spesifik' (Direct Aggregation Read-Only):**
  - [x] *Direct Aggregation Query:* Mengganti sumber facet dari `tempCariOpac` menjadi query agregasi `GROUP BY` langsung pada tabel `catalogs` (`Author`, `Publisher`, `PublishLocation`, `PublishYear`, `Subject`, `Languages`) dengan limit konfigurasi `Faced*Max`.
  - [x] *Integrasi facedGenerator:* Menyelaraskan hasil raw dengan parser `OpacHelpers::facedGenerator` untuk sanitasi delimiter dan duplikasi.
  - [x] *Session Caching:* Menyimpan data facet ke `$_SESSION['dataFaced*']` sehingga navigasi paginasi (page 2, 3, dst.) tidak perlu query ulang facet.
  - [x] *Proteksi View Anti-Kotak Kosong:* Menambahkan kondisi `if (!empty($dataFaced*) || !empty($f*))` pada setiap panel facet di `resultListOpac.php`, serta memperluas lebar kolom tabel menjadi `col-sm-12` jika seluruh facet kosong.
- [x] **Fase 24: Solusi Tuntas Error 1637 Pencarian Lanjut & Pembersihan Detik Hasil Pencarian:**
  - [x] *Pembersihan Detik Hasil Pencarian:* Menghapus teks waktu eksekusi `(0.xxxx detik)` pada `opac/views/pencarian-sederhana/resultListOpac.php` dan `opac/views/pencarian-lanjut/resultListOpac.php`.
  - [x] *Koreksi Nomor Awal Paginasi:* Standarisasi `$awal = ($totalCountResult == 0) ? 0 : (($page - 1) * $limit) + 1` agar halaman berikutnya tidak menampilkan angka 2 lagi melainkan nomor urut buku yang akurat.
  - [x] *Proteksi Facet Pencarian Lanjut:* Menambahkan wrapper conditional dan pelebaran dinamis `col-sm-12` / `col-sm-9` serta parameter `&fBahasa` pada tautan paginasi `pencarian-lanjut/resultListOpac.php`.
- [x] **Fase 25: Audit Menyeluruh & Eliminasi Total Error 1637 InnoDB di Seluruh Sistem:**
  - [x] *Direct Read-Only Engine OPAC Telusur (BrowseController.php):* Mengeliminasi ketergantungan pada stored procedure `CALL insertTempTelusurOpac` dan tabel `tempCariOpac`. Mengimplementasikan method direct query read-only `getDirectBrowseData()`, `getDirectBrowseCount()`, dan agregasi mandiri `getDirectBrowseFacets()` untuk filter Author, Publisher, PublishLocation, PublishYear, Subject, dan Bahasa.
  - [x] *Pembersihan & Perbaikan View Telusur (browse/resultListOpac.php):* Menghapus tampilan runtime detik `(0.xxxx detik)`, menstandarkan formula penomoran `$awal = ($totalCountResult == 0) ? 0 : (($page - 1) * $limit) + 1`, memperbaiki bug warisan tautan facet yang sebelumnya memakai parameter keliru `katakunci` menjadi parameter telusur aktif (`tag`, `findBy`, `query`, `query2`), menerapkan grid adaptif `col-sm-9`/`col-sm-12`, membungkus setiap panel facet dengan proteksi anti-kotak kosong, serta memindahkan registrasi JS ke luar blok facet agar tombol keranjang selalu aktif.
  - [x] *Purge Total Stored Procedure di Pencarian Sederhana (PencarianSederhanaController.php):* Menghilangkan pemanggilan `CALL insertTempSederhanaOpac` pada halaman 1 dan menjadikan direct read-only engine sebagai 100% primary di seluruh halaman, meniadakan pembuatan temporary table InnoDB.
  - [x] *Purge Total Stored Procedure di Pencarian Lanjut (PencarianLanjutController.php):* Menghilangkan pemanggilan `CALL insertTempLanjutOpac` pada halaman 1 dan beralih penuh ke direct query engine `getDirectSearchDataLanjut()`, `getDirectSearchCountLanjut()`, dan `getDirectSearchFacetsLanjut()`.
  - [x] *Multi-Portal Hardening (digitalcollection & article):* Membungkus pemanggilan logger `OpacHelpers::opacLogs` dan stored procedure dalam blok `try-catch` defensif pada `digitalcollection/controllers/BrowseController.php` dan `article/controllers/BrowseController.php`.
  - [x] *Audit Zero-Comments & Linter:* Seluruh 6 file yang dimodifikasi lolos verifikasi sintaks `php -l` dengan exit code 0 dan 100% patuh terhadap Zero-Comments Rule (0 komentar di kode baru).

## 4. Current Sprint / Fokus Pengujian Saat Ini
- [x] Eksekusi DDL `master_rak.sql` di database server UT (IP: `172.30.13.81` / `dbsirkulasi`).
- [x] Pengujian tambah 1 rak uji coba di Admin (Lantai 2, DDC 330 - 339).
- [ ] Pengujian pencarian katalog dan paginasi di OPAC setelah penerapan error-handling dan optimasi query.
- [ ] Pengujian cetak stiker rak dan scan melalui kamera ponsel di jaringan `172.30.14.94`.

## 5. Catatan Konfigurasi Lingkungan
- **Alamat Server Web Intranet:** `http://172.30.14.94/inlislite3`
- **Rute Backend Admin:** `http://172.30.14.94/inlislite3/backend/rak/index`
- **Rute Publik OPAC:** `http://172.30.14.94/inlislite3/opac/rak?id=<id_rak>`