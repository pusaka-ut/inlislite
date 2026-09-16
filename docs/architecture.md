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