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