# Architecture & Tech Stack — INLISLite v3 Perpustakaan UT

Dokumentasi ini memetakan arsitektur sistem, topologi jaringan, dan modul pada aplikasi INLISLite Versi 3 di Perpustakaan Pusat Universitas Terbuka.

## 1. Tech Stack
- **Framework:** PHP Yii 2.0.7 (Yii2 Advanced Application Template)
- **Backend Admin UI:** AdminLTE Theme (`inliscore/kw-themes-adminlte`)
- **Public OPAC UI:** Responsive Mobile-First HTML5 & Bootstrap 3
- **Database:** MySQL / MariaDB (`dbsirkulasi` pada host 172.30.13.81)
- **Metadata Standard:** MARC21 (Machine-Readable Cataloging) & ISO-2709
- **QR Code Engine:** Standalone Vector SVG & Base64 Data-URI (`common\components\QrCodeHelper`)
- **Network Environment:** Jaringan Intranet UT (`http://172.30.14.94/inlislite3`) dengan dukungan dynamic host override

## 2. Struktur Direktori Utama
```
inlislite3/
├── backend/                  # Portal pustakawan / pengelola perpustakaan
│   ├── controllers/          # Termasuk RakController.php (Manajemen & Cetak Stiker)
│   ├── modules/              # Akuisisi, Pengkatalogan, Sirkulasi, Keanggotaan, dll.
│   └── views/rak/            # View manajemen rak & template cetak stiker (single & batch A4)
├── common/                   # Komponen dan model bersama seluruh aplikasi
│   ├── components/           # Termasuk QrCodeHelper.php, MarcHelpers, SirkulasiComponent
│   ├── config/               # Konfigurasi database dbsirkulasi dan parameter aplikasi
│   └── models/               # Termasuk MasterRak.php, Locations.php, Collections.php, Catalogs.php
├── db/                       # Skrip DDL database (master_rak.sql)
├── docs/                     # Kitab suci dokumentasi teknis project
├── inliscore/                # Ekstensi tema AdminLTE (navigasi sidebar.php)
└── opac/                     # Portal publik pemustaka
    ├── controllers/          # Termasuk RakController.php (Mobile-first view tanpa login)
    └── views/rak/            # Tampilan kartu buku, pencarian instan dalam rak, filter ketersediaan
```

## 3. Arsitektur Navigasi Rak & QR Code
1. **Pustakawan (Backend):**
   - Mengelola master rak (`master_rak`) yang mengikat lantai (`locations`) dengan rentang nomor klasifikasi DDC / Call Number.
   - Mencetak label stiker siap tempel dalam ukuran single (signage rak) atau batch (lembaran kertas stiker A4).
2. **Pemustaka (OPAC Mobile):**
   - Memindai QR code fisik di rak menggunakan kamera ponsel pintar.
   - Browser ponsel langsung membuka `/opac/rak?id=<id>` tanpa perlu autentikasi.
   - Menyajikan kartu buku dengan penonjolan nomor panggil (*call number*), status ketersediaan (*tersedia di tempat / dipinjam*), pencarian teks instan di dalam rak, dan link ke detail katalog OPAC lengkap.

## 4. Arsitektur Mesin Pencarian OPAC (Dual-Engine Read-Only & Guardrail 2 Karakter)
1. **Guardrail Input:**
   - Pembatasan panjang kata kunci minimal 2 karakter (`minlength="2"` di frontend dan `mb_strlen(trim($Keyword)) < 2` di controller).
   - Mencegah *denial-of-service* akibat full table scan wildcard satu huruf pada database koleksi ratusan ribu buku, namun tetap mengizinkan singkatan penting ("AI", "UT", "IT", "UU").
2. **Dual-Engine Search Query:**
   - Mencari secara paralel pada kolom teks utama tabel `catalogs` (`Title`, `Author`, `Publisher`, `Subject`, `CallNumber`, `ISBN`) dan subquery MARC tags pada `catalog_ruas`.
   - Menjamin seluruh buku katalog ditemukan 100% tanpa kehilangan metadata bibliografis.
3. **Stateless Paginasi Read-Only:**
   - Menghilangkan ketergantungan pada stored procedure `insertTempSederhanaOpac` dan `insertTempSederhanaOpac0` yang memicu error 1637 InnoDB (`HA_ERR_TOO_MANY_CONCURRENT_TRXS` pada `ibtmp1`).
   - Menggunakan kalkulasi limit-offset matematis `LIMIT :offset, :limit` murni read-only yang menjamin halaman 2, 3, dst. selalu menyajikan data buku secara konsisten dan cepat.

## 5. Arsitektur Agregasi Facet Read-Only (Sidebar 'Lebih Spesifik')
1. **Direct Aggregation Query:**
   - Menghilangkan ketergantungan pada tabel temporary `tempCariOpac` yang menyebabkan Error 1637 InnoDB.
   - Mengambil agregasi Top N (`Faced*Max`) untuk 6 kategori bibliografis (`Author`, `Publisher`, `PublishLocation`, `PublishYear`, `Subject`, `Languages`) secara langsung dari tabel `catalogs` menggunakan query `GROUP BY ... ORDER BY jml DESC LIMIT N` yang cepat (~25ms per kategori).
2. **Sanitasi & Delimiter Parsing:**
   - Hasil raw SQL diproses via `OpacHelpers::facedGenerator` untuk membersihkan delimiter titik koma (`;`), tanda hubung, dan duplikasi.
3. **Session Caching & Paginasi Seamless:**
   - Hasil facet disimpan pada sesi pengguna (`$_SESSION['dataFaced*']`) sehingga perpindahan ke halaman 2, 3, dst. tidak perlu mengulang query agregasi facet.
4. **Adaptive View Rendering:**
   - Kotak kategori facet pada `resultListOpac.php` hanya dirender jika memiliki data atau terdapat filter aktif, mencegah munculnya kotak kosong tak berfungsi. Jika seluruh facet kosong, kolom hasil pencarian otomatis meluas menjadi lebar penuh (`col-sm-12`).

## 6. Arsitektur Pencarian Lanjut Read-Only & Penanganan Error 1637 InnoDB
1. **Eliminasi Error 1637 InnoDB:**
   - Stored procedure legacy `insertTempLanjutOpac` dan `insertTempLanjutOpac0` mengeksekusi DDL pembuatan temporary table yang memicu kehabisan rollback segment (`SQLSTATE[HY000]: General error: 1637 Too many active concurrent transactions`).
   - Sistem kini mengimplementasikan arsitektur dual-mode: `try-catch` pada halaman 1 dengan auto-fallback direct query, dan direct read-only query murni (`getDirectSearchDataLanjut`, `getDirectSearchCountLanjut`, `getDirectSearchFacetsLanjut`) pada paginasi (halaman 2, 3, dst.).
2. **Pembersihan String Waktu Eksekusi (Detik):**
   - Menghapus string runtime benchmarking internal `(0.xxxx detik)` pada view hasil pencarian sederhana dan pencarian lanjut sehingga teks ringkasan tampil bersih dan profesional: `Menampilkan X - Y dari Z hasil`.
3. **Koreksi Paginasi & Penomoran Awal:**
   - Standarisasi formula nomor awal `$awal = ($totalCountResult == 0) ? 0 : (($page - 1) * $limit) + 1` pada seluruh view hasil pencarian.

## 7. Eliminasi Total Error 1637 & Unifikasi Direct Read-Only Engine (Fase 25)
1. **Penyebab Sistemik MySQL Error 1637:**
   - Terjadi akibat `CALL insertTemp*` yang mengeksekusi `CREATE TEMPORARY TABLE tempCariOpac` secara transaksional di InnoDB engine. Alokasi slot rollback segment pada tablespace temporary shared (`ibtmp1`) terkuras saat banyak pengguna mengeksekusi pencarian secara bersamaan, memicu `SQLSTATE[HY000]: General error: 1637 Too many active concurrent transactions`.
2. **Unifikasi 100% Direct Read-Only Query Engine:**
   - **OPAC Telusur (`BrowseController.php`):** Sepenuhnya decoupled dari `insertTempTelusurOpac`. Menggunakan direct read-only query helper `getDirectBrowseData()`, `getDirectBrowseCount()`, dan `getDirectBrowseFacets()`.
   - **OPAC Pencarian Sederhana (`PencarianSederhanaController.php`):** Eliminasi pemanggilan `insertTempSederhanaOpac` pada halaman 1. Seluruh halaman sekarang 100% dialirkan via `getDirectSearchData()`.
   - **OPAC Pencarian Lanjut (`PencarianLanjutController.php`):** Eliminasi pemanggilan `insertTempLanjutOpac` pada halaman 1. Seluruh halaman 100% direct via `getDirectSearchDataLanjut()`.
   - **Multi-Portal Defensiveness (`digitalcollection` & `article`):** Isolasi logger dan stored procedure dalam blok `try-catch` defensif untuk mencegah crash 500 error.
3. **Perbaikan View Telusur (`browse/resultListOpac.php`):**
   - Eliminasi teks detik `(0.xxxx detik)`.
   - Penomoran `$awal` yang akurat.
   - Koreksi URL facet link yang sebelumnya memakai parameter keliru `katakunci` menjadi parameter telusur valid (`tag`, `findBy`, `query`, `query2`).
   - Adaptive layout grid (`col-sm-9` vs `col-sm-12`) dan proteksi anti-kotak kosong.

## 8. Arsitektur Ekspor Backend & Penanganan Error 400 Bad Request CSRF (Fase 26)
1. **Akar Masalah HTTP 400 (`Bad Request: Tidak dapat mem-verifikasi pengiriman data Anda`):**
   - Widget tabel `kartik\grid\GridView` mengumpulkan baris HTML tabel di sisi klien melalui JavaScript (`kv-grid-export.js`), membuat tag form tersembunyi ber-target popup window (`kvDownloadDialog`), lalu melakukan POST ke route `/gridview/export/download`.
   - Secara default, controller vendor `kartik\grid\controllers\ExportController` mewarisi `yii\web\Controller` dengan validasi CSRF aktif (`$enableCsrfValidation = true`). Pada navigasi tabel berbasis PJAX atau form yang di-submit lewat jendela popup terpisah, token CSRF tidak sinkron dengan session aktif di server sehingga request ditolak oleh `Request::validateCsrfToken()`.
2. **Implementasi Zero-Touch Vendor via Controller Map:**
   - Tanpa mengubah berkas pada direktori `vendor/`, sistem membuat controller kustom `backend\controllers\ExportController` yang meng-extends `kartik\grid\controllers\ExportController` dengan menyetel `public $enableCsrfValidation = false;`.
   - Controller tersebut didaftarkan pada modul `gridview` di `backend/config/modules.php` via konfigurasi `controllerMap => ['export' => 'backend\controllers\ExportController']`.
3. **Penyelarasan Otorisasi RBAC:**
   - Route `gridview/*` didaftarkan ke dalam `allowActions` pada `backend/config/main.php` di bawah filter `as access` (`mdm\admin\components\AccessControl`), menjamin seluruh staf/operator perpustakaan dapat mengunduh berkas ekspor tanpa terhadang error otorisasi HTTP 403.

## 9. Modernisasi Terpadu Pengalaman Ekspor GridView: Direct Download & In-Page Toast (Fase 27)
1. **Akomodasi Manfaat Desain Lama ke Standar Modern:**
   - Desain bawaan lama menggunakan jendela popup (`_popup`) dan alert konfirmasi untuk mencegah hilangnya *state* kerja dan memberi tahu proses pembuatan berkas.
   - Sistem memodernisasi arsitektur ini dengan beralih ke **Direct Download Native (`target = '_self'`)** dan **Tanpa Alert Konfirmasi Kaku (`showConfirmAlert = false`)**. Berkat header HTTP `Content-Disposition: attachment`, browser modern langsung mengalirkan berkas ke pengelola unduhan tanpa me-reload halaman, mempertahankan centang checkbox, filter, dan posisi scroll 100% utuh.
2. **Penerapan Sistemik 100% Global via Dependency Injection Container:**
   - Menghindari modifikasi manual pada 100+ view tabel, konfigurasi default widget dideklarasikan secara global pada `backend/config/bootstrap.php` menggunakan `\Yii::$container->set('kartik\grid\GridView', ...)`.
3. **Umpan Balik Visual Modern (In-Page Floating Toast UI):**
   - Menggantikan jendela popup lama 350x120px dengan kartu notifikasi melayang di pojok kanan atas berdesain resmi Universitas Terbuka (Navy `#002b55` dan Gold `#ffcc00`) yang dilengkapi spinner animasi, perlindungan klik ganda, transisi sukses otomatis, dan *auto-dismiss* halus.

## 10. Arsitektur Modernisasi Header, Sidebar Gradasi Bergerak, & Dashboard Components (Fase 28)
1. **Pembersihan Markup Semantik & Eliminasi Hack Inline Styles:**
   - Menghapus seluruh hardcoded inline styles peninggalan lawas pada widget `inliscore\adminlte\widgets\NavBar` (`padding-top: 59px` pada toggle button, `height: 70px` inline pada logo, `padding-top: 15px` pada brand link, dan `margin-top: 5px` pada `#clocktime`), serta inline styles pada view layout `heading.php` dan `heading-member.php` (`height: 89px`, `background: #369`, dan `padding-top: 38px`).
   - Kontrol estetika dialirkan 100% ke pipeline CSS terpadu (`backend/assets_b/css/modern-adminlte.css`), menjaga kode PHP tetap modular, bersih, dan mematuhi Zero-Comments Rule.
2. **Horizontal Flex Command Bar (74px) & Preservasi Konten 100%:**
   - Menyelaraskan seluruh komponen header pada garis horizontal tunggal yang presisi via CSS Flexbox `align-items: center; justify-content: space-between; min-height: 74px;`.
   - Seluruh konten inti tetap utuh: Toggle squircle kaca, logo UT, identitas perpustakaan, jam digital dinamis, dan user profile menu. Jam digital dan profil pengguna kini sejajar berdampingan secara simetris di sisi kanan.
3. **Living Dynamic Moving Gradient (Akselerasi GPU):**
   - Menerapkan keyframe animasi `@keyframes inlisHeaderFlow` (18 detik) pada header dan `@keyframes inlisSidebarFlow` (22 detik) pada sidebar menggunakan spektrum warna resmi Universitas Terbuka (`#00172e`, `#001f3f`, `#002b55`, `#004080`). Animasi menggunakan transisi `background-position` yang diakselerasi langsung oleh GPU tanpa membebani performa CPU browser.
4. **Frosted Glass Bubble & Floating Squircle Navigation (Pillio Concept):**
   - Mengemas toggle button, jam digital, dan user profile pill ke dalam kapsul kaca (*frosted glass bubble*) ber-`backdrop-filter: blur(10px–14px)` dan border semi-transparan tipis.
   - Menu navigasi sidebar bertransformasi dari balok persegi kaku menjadi kapsul melayang `border-radius: 12px` dengan margin samping dan efek cahaya aktif (*golden sheen glow*).
5. **Modernisasi Squircle Stat Cards (SmallBox):**
   - Mengubah widget `SmallBox` menjadi kartu elevated modern ber-radius `18px`, shadow ambient multi-layer halus, angka tebal 30px berbobot 800, dan bilah tombol `Detail` kaca transparan yang menyatu mulus di bagian bawah.

## 11. Arsitektur Dual Semantic Flex Header & Living Vibrant Dynamic Gradient (Fase 29)
1. **Solusi Sistemik Terhadap Isu Direct Children Flexbox:**
   - Pada `inliscore\adminlte\widgets\NavBar`, lima elemen navbar sebelumnya dirender sebagai direct children dari `<nav class="navbar">`. Sifat bawaan `justify-content: space-between` mendistribusikan spasi rata sehingga elemen terpencar ke tengah layar.
   - Solusi arsitektural: Membungkus elemen ke dalam dua kontainer semantik:
     - `.inlis-header-left`: Toggle button, logo UT, dan brand title link.
     - `.inlis-header-right`: Digital clock pill dan user profile menu container.
   - Menghasilkan pengelompokan alami: kluster identitas di sisi kiri dan kluster status/profil di sisi kanan.
2. **Living Dynamic Vibrant Gradient (Akselerasi GPU 60fps):**
   - Header dan sidebar mengadopsi spektrum multi-stop kontras tinggi (`#06152d` - `#0284c7`), menghasilkan gelombang cahaya bergerak yang jelas dan estetik tanpa membebani thread JavaScript/CPU.
3. **Bubble Glassmorphism Specular Sheen:**
   - Menerapkan specular reflection sheen fisik (`inset 0 1.5px 1.5px rgba(255, 255, 255, 0.55)`) pada seluruh kapsul kaca melayang (jam, user pill, squircle toggle).
4. **Eliminasi Legacy Absolute Positioning pada ClockZ (Fase 30):**
   - Menetralkan aturan tahun 2016 di `site.css` yang memberikan `position: absolute; top: 5px; right: 10px; width: 200px;` pada `.clockZ`.
   - Mengunci `.clockZ` dengan `position: static !important; width: auto !important;` dan `.inlis-header-right` dengan `flex-direction: row;` serta membersihkan class Bootstrap `collapse navbar-collapse` dari wrapper jam di `NavBar.php`.
   - Menjamin 100% Jam Digital dan Kapsul User duduk berdampingan secara horizontal tanpa tumpang-tindih (*zero-overlapping*).

## 12. Arsitektur Pelaporan Defensif & Dual-Aliasing Subquery SQL (Fase 31)
1. **Dual-Aliasing Subquery SQL (Eliminasi SQLSTATE[42S22] Column 1054):**
   - Subquery UNION 3 cabang (`member`, `non_member`, `group_guess`) pada modul Laporan Kunjungan Periodik (`backend/modules/laporan/controllers/BukuTamuController.php`) sebelumnya meng-alias kolom perpustakaan sebagai `lokasi` dan ruang sebagai `lok_ruang`. Namun klausa filter luar mencari kolom `lokasi_perpus` dan `lokasi_ruang`.
   - Solusi arsitektural: Menerapkan dual-aliasing pada SELECT turunan dan SELECT luar:
     `lokasi`, `lokasi AS lokasi_perpus`, `lok_ruang`, `lok_ruang AS lokasi_ruang`.
   - Menjamin integritas filter luar di seluruh dialek SQL tanpa memicu pengecualian database column not found.
2. **Defensive Array Guardrails & Inisialisasi `$VALUE` (Eliminasi PHP Warning Foreach):**
   - Variabel kriteria `$VALUE` diinisialisasi secara eksplisit di awal action (`$VALUE = array();`) sebelum evaluasi `$_POST`.
   - Pengulangan kriteria dibungkus dengan guardrail defensif `if (!empty($VALUE) && is_array($VALUE))` dan nilai sub-kriteria divalidasi dengan aman sebelum fungsi string `implode()`.
   - Penambahan pengelompokan kurung boolean `AND (...)` pada klausa WHERE gabungan filter lokasi dan ruang perpustakaan untuk menjamin presisi logika SQL.
3. **Hardening View Defensif `pdf-view-kunjungan-periodik-data.php`:**
   - Loop data kunjungan diamankan dengan `if (!empty($TableLaporan) && is_array($TableLaporan))` dan verifikasi `isset($TableLaporan['count'])` guna mencegah warning notice `Undefined index: count` ketika laporan dicetak dalam kondisi tanpa data.

## 13. Arsitektur Modul Keanggotaan & Resolusi Bug Foto Anggota (Fase 32)
1. **Eliminasi Batasan Dimensi Ekstrem & Standarisasi Upload Foto (`_formFoto.php`):**
   - Menghapus aturan validasi client-side `minImageWidth => 1004` dan `minImageHeight => 638` yang sebelumnya memblokir pas foto standar (300x400, 400x600 px).
   - Memperluas dukungan format gambar ke `jpg`, `jpeg`, `png`, dan `webp` dengan ukuran maksimal 5 MB.
   - Mengaktifkan `showPreview => true` sehingga pemustaka/pustakawan dapat melihat pratinjau foto sebelum disimpan.
2. **Standardisasi Respons AJAX FileInput (`MemberController.php`):**
   - Mengubah `actionUploadFotoAnggota()` untuk mengembalikan respons JSON murni (`['success' => true]` atau `['error' => '...']`) saat dipanggil via AJAX, memenuhi protokol asynchronous widget Kartik FileInput.
   - Menghubungkan event handler `fileuploaded` di JavaScript untuk me-reload halaman secara mulus begitu unggahan berhasil.
   - Menyisipkan pembersihan berkas fisik lama (`@unlink`) saat foto anggota diganti baru, mencegah penumpukan sampah berkas di server storage.
3. **Graceful Degradation Webcam di Lingkungan Intranet HTTP (`_formFoto.php`):**
   - Melindungi API `navigator.mediaDevices.getUserMedia` dengan pengecekan ketersediaan context aman (`https://` atau `localhost`).
   - Menyajikan banner informasi ramah jika browser memblokir kamera di jaringan intranet HTTP (`http://172.30.14.94`), mengarahkan pustakawan menggunakan form Unggah Berkas Foto tanpa crash JavaScript.
4. **Perapihan Komponen UI/UX Keanggotaan & Penataan Aksi Mandiri:**
   - Menyediakan tombol aksi langsung **"Unggah / Ambil Foto"** di tab Detail Anggota (`_formEdit.php`) yang menghubungkan pengguna ke tab Foto tanpa kebingungan.
   - Membersihkan tombol fiktif "Sesuaikan Foto" yang sebelumnya memanggil class tidak eksis `\backend\libs\ProfileImage`.
   - Mengubah toolbar aksi batch di `member/index.php` dan `member/keranjang.php` dari margin negatif inline menjadi flex-container responsif.
   - Menyertakan tombol `{update}` di ActionColumn tabel `index.php` dan memperbaiki link gii fiktif di `view.php` ke rute resmi keanggotaan.
   - Menghapus baris debug `echo $memberId;` pada `MemberController::actionCreate()`.