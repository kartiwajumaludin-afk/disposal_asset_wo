📋 PROJECT MASTER PLAN: Asset Disposal Tracker (The Clean Version)
1. Visi & Tujuan
Membangun sistem Single Source of Truth untuk tracking asset telco yang akan di-disposal/write-off. Menghilangkan ketergantungan pada tracker personal (Excel berceceran) dan menggantinya dengan WebApp terstandarisasi.

2. Arsitektur Teknis
Framework: Laravel 12 (Clean Installation).
Database: MySQL (Database Name: disposal).
Database Strategy: - No Migrations. Menggunakan tabel yang sudah ada.
Stored Procedure (SP) Driven. Logika manipulasi data utama menggunakan SP yang sudah matang di database.
AI Tools: - Mentor: Gemini (Prof) - Strategi & Arsitektur.
Executor: Cursor AI - Penulisan Kode Langsung (Tangan Gaib).

3. Struktur Data & Integrasi
Sistem mengintegrasikan 3 sumber data utama dari Customer:
Ticket: Data progress utama (1 Ticket = 1 Row).
Asset: Detail item yang didispose (1 Ticket = Multi Row).
Workinfo: Catatan aktivitas & approval (1 Ticket = Multi Row/Growing).
Logika Perkawinan Data: Menggunakan script import yang sudah teruji (Refactoring dari PHP Native).

4. Modul Utama (Roadmap)
[ ] Phase 1: Setup & Connection. Koneksi ke database disposal & setting environment.
[ ] Phase 2: Master Tracker View. Halaman utama gabungan 3 data (Ticket, Asset, Workinfo).
[ ] Phase 3: Import Engine. Modul upload Excel & sinkronisasi data via Laravel.
[ ] Phase 4: Daily Activity. Penugasan tim (Assignment) & Reporting Issue lapangan.
[ ] Phase 5: Customer Dashboard. Visualisasi progress vs Budget PO.
5. Aturan Main untuk AI (Mandatory)
Haram menghapus atau memotong kode yang sudah ada (Gunakan FULL CODE).

Jangan membuat migrasi baru tanpa seizin PM.

Selalu prioritaskan penggunaan Stored Procedure untuk operasi database kompleks.

Jika ada keraguan dalam logika bisnis, WAJIB bertanya kepada PM.