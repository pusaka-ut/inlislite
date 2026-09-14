# Project Rules — Web Project Besar

> Catatan: File ini melengkapi Global `AGENTS.md`. Jika ada konflik, file ini (Lokal) yang menang khusus untuk project ini.

## 1. Konteks Project
Project ini adalah aplikasi web skala besar yang membutuhkan arsitektur terstruktur (frontend, backend, database). Skalabilitas dan kemudahan *maintenance* adalah prioritas utama.

## 2. Aturan Dokumentasi Mutlak (Single Source of Truth)
- Folder `docs/` adalah "kitab suci" dari project ini.
- **DILARANG KERAS** mengubah struktur database, arsitektur, atau state management di dalam kode, tanpa mengupdate file yang bersesuaian di dalam `docs/`.
- Kode dan Dokumentasi harus selalu sinkron 1:1.

## 3. Standar UI/UX Project Ini
- Wajib menggunakan design system (misal: Tailwind, Shadcn, MUI) yang didefinisikan secara global.
- Dilarang keras *hardcode* warna atau ukuran di luar dari konfigurasi tema yang sudah disepakati di `docs/design.md`.

## 4. Skill Lokal Project
Project ini memiliki skill spesifik yang tidak ada di project kecil:
- `doc-sync-guardian`: Memastikan folder `docs/` selalu *up-to-date*.
- `git-guardian`: Memastikan operasi git (commit, push, branch) aman dan mengikuti standar *Conventional Commits*.