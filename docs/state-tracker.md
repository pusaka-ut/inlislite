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
- [x] **Fase 2: OPAC Skin:** Pembuatan `opac/assets_b/css/modern-opac.css` dan pendaftaran pada `AppAsset.php` (telah di-push ke GitHub).
- [x] **Fase 3: Backend AdminLTE Skin:** Pembuatan `inliscore/kw-themes-adminlte/assets/css/modern-adminlte.css` dan pendaftaran pada `MyAsset.php` (sidebar navy dark `#001f3f`, gold accent `#ffcc00`, clean white navbar, soft elevation card).
- [ ] **Fase 4: Deployment & Verifikasi Server:** Eksekusi sinkronisasi ke server intranet UT `172.30.14.94`.

## 3. Current Sprint / Fokus Pengujian Saat Ini
- [x] Eksekusi DDL `master_rak.sql` di database server UT (IP: `172.30.13.81` / `dbsirkulasi`).
- [x] Pengujian tambah 1 rak uji coba di Admin (Lantai 2, DDC 330 - 339).
- [ ] Pengujian cetak stiker rak dan scan melalui kamera ponsel di jaringan `172.30.14.94`.

## 4. Catatan Konfigurasi Lingkungan
- **Alamat Server Web Intranet:** `http://172.30.14.94/inlislite3`
- **Rute Backend Admin:** `http://172.30.14.94/inlislite3/backend/rak/index`
- **Rute Publik OPAC:** `http://172.30.14.94/inlislite3/opac/rak?id=<id_rak>`