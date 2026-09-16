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