<p align="center">
{{-- Sesuaikan path ini jika logo Anda ada di 'public/img/Logo(1).png' --}}
<img src="public/vendor/adminlte/dist/img/Logo.png" alt="Buleleng Creative Hub Logo" width="150">
</p>

<h1 align="center">Buleleng Creative Hub</h1>

<p align="center">
Platform Digital Terpadu untuk ekosistem ekonomi kreatif di Buleleng.
</p>

<p align="center">
<a href="##🚀-tentang-proyek-ini">Tentang Proyek</a> •
<a href="##✨-fitur-fitur">Fitur</a> •
<a href="##🛠️-teknologi-yang-digunakan">Teknologi</a> •
<a href="##🖥️-instalasi">Instalasi</a>
</p>

## Tentan Proyek ini

Buleleng Creative Hub (BCH) adalah sebuah aplikasi web yang dirancang untuk menjadi pusat digital bagi para pelaku ekonomi kreatif di Buleleng. Tujuan utamanya adalah untuk menghubungkan talenta kreatif (Peserta/User) dengan sumber daya yang mereka butuhkan untuk berkembang.

Aplikasi ini memiliki dua peran utama:

-   Admin: Mengelola seluruh data master platform (Mentor, Event, User, Produk, dll).

-   User (Peserta): Melengkapi profil, mendapatkan rekomendasi, mendaftar mentor, dan mengakses konten.

## ✨ Fitur-Fitur

Platform ini dibagi menjadi tiga bagian utama: Landing Page Publik, Panel Admin, dan Portal Pengguna.

1. Landing Page (Publik)

Desain modern terinspirasi "Unbrew" (menggunakan Tailwind CSS).

Menampilkan hero section, carousel produk unggulan, daftar fitur, statistik real-time (jumlah mentor, user, dll.), galeri mentor, dan galeri venue.

Tombol Login dan Register yang jelas.

2. Panel Admin (Admin Dashboard)

Dashboard Statistik: Menampilkan ringkasan jumlah peserta, mentor, event, dan media pembelajaran. Termasuk tabel peserta terbaru dan status mentor mereka.

Manajemen Multi-Peran: Menggunakan Gate Laravel (is_admin, is_user) untuk menampilkan sidebar dinamis yang aman untuk di-cache.

CRUD AJAX: Sebagian besar manajemen data menggunakan modal AJAX (tanpa reload halaman) untuk:

Manajemen Admin: Tambah/Edit/Hapus admin (dengan toggle lihat password).

Manajemen Mentor: CRUD data mentor, keahlian (sub sektor), dan layanan (user needs).

Manajemen Event: Modal multi-langkah (Detail Acara -> Pilih Sub Sektor) dengan logika dropdown "Venue Lainnya" yang dinamis.

Manajemen LMS: CRUD media pembelajaran dengan pilihan sumber (URL eksternal atau Upload File).

Manajemen Produk: CRUD etalase produk dengan upload foto.

Manajemen Venue: CRUD data venue (lokasi) dengan upload foto.

Manajemen Berita: CRUD untuk berita/artikel.

Manajemen Peserta: Melihat daftar semua peserta (role "user") dan detail profil mereka (termasuk No. HP dan mentor).

3. Portal Pengguna (User Dashboard)

Alur Registrasi & Profiling: Pengguna baru mendaftar (halaman registrasi kustom) dan langsung diarahkan ke modal 2 langkah untuk melengkapi profil (bio, foto, portofolio) dan memilih minat (Sub Sektor & Kebutuhan Layanan).

Dashboard Dinamis: Tampilan dashboard yang dipersonalisasi:

Menampilkan carousel Produk Etalase dan daftar Berita Terbaru.

Menampilkan grid card Event Mendatang dan Media Pembelajaran (LMS) yang difilter berdasarkan sub-sektor yang diminati pengguna.

Sistem Rekomendasi Mentor:

Menampilkan daftar mentor yang relevan berdasarkan sub-sektor pengguna.

Pengguna dapat Memilih Mentor (dengan konfirmasi SweetAlert).

Jika sudah punya mentor, menampilkan detail mentor saat ini dan tombol Ganti Mentor (dengan konfirmasi SweetAlert).

Halaman Eksplorasi (User-Facing):

Halaman user.products.index, user.venues.index, user.events.index, user.lms.index, user.news.index dengan tampilan grid card yang rapi.

Halaman user.lms.index dan user.events.index memiliki filter chips (badges) untuk memfilter konten berdasarkan Tipe (LMS) atau Status (Event).

Detail Produk dan Venue ditampilkan dalam Modal AJAX untuk experience yang cepat.

Halaman Profil: Tampilan detail profil pengguna dengan modal edit 3-tab (Akun, Profil, Minat) untuk memperbarui data via AJAX.

## 🛠️ Teknologi yang Digunakan

-   Database: MySQL

-   Template Admin/Dashboard: AdminLTE v3 (diimplementasikan melalui paket jeroennoten/laravel-adminlte)

-   Frontend (Dashboard): Bootstrap 4 (dari AdminLTE), jQuery, AJAX (untuk semua operasi CRUD modal)

-   Frontend (Landing Page): Tailwind CSS (via CDN)

-   Library Tambahan:

    1. SweetAlert2: (Versi 8, via CDN) untuk notifikasi modal yang interaktif.

    2. Chart.js: (Opsional, untuk dashboard admin)

    3. Font Awesome 5/6: (via AdminLTE) untuk ikonografi.

    4. Autentikasi: Laravel UI (Bootstrap) dengan view yang dikustomisasi agar sesuai layout AdminLTE.

    5. Otorisasi: Laravel Gates (is_admin, is_user) untuk memisahkan logika peran di backend dan sidebar.
