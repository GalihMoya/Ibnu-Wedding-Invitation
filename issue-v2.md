# Project Planning: Fitur Sesi Admin (RSVP Management) & Galeri Slideshow
**Project:** Ibnu Wedding Invitation
**Target:** Junior Programmer / AI Model

## Deskripsi Singkat
Dokumen ini berisi detail perencanaan untuk mengembangkan fitur baru pada website undangan pernikahan yang sudah ada. Terdapat dua objektif utama:
1. **Sistem Sesi (Guest & Admin)**: Pemisahan hak akses antara Tamu (hanya bisa mengisi form) dan Admin (bisa login, melihat data RSVP tamu, dan mencentang/kehadiran fisik tamu di lokasi acara).
2. **Slideshow Galeri Perlahan**: Mengubah tampilan galeri foto dari yang sebelumnya *grid* banyak foto menjadi *carousel* yang hanya menampilkan 1 foto dalam satu waktu dan bergeser perlahan secara otomatis.

---

## 1. Fitur Sesi Admin dan Manajemen Buku Tamu (RSVP)

Karena website di-*host* di server lokal (XAMPP/htdocs), sangat direkomendasikan untuk menggunakan **PHP murni (Native)** dan **MySQL** (atau file JSON/SQLite sebagai alternatif paling sederhana) untuk mengelola data dan sesi.

### Tahap 1.1: Persiapan Database & Backend Sederhana
- [ ] Buat database MySQL baru (contoh: `db_wedding`).
- [ ] Buat tabel `guests` dengan kolom minimal: 
  - `id` (INT, Primary Key, Auto Increment)
  - `name` (VARCHAR)
  - `status_rsvp` (ENUM: 'Hadir', 'Ragu', 'Tidak Hadir')
  - `pax` (INT)
  - `message` (TEXT)
  - `is_checked_in` (BOOLEAN/TINYINT, default 0) - *untuk checklist kehadiran di lokasi*.
  - `created_at` (TIMESTAMP)
- [ ] Ubah file `index.html` menjadi `index.php` agar dapat dipadukan dengan backend jika perlu.
- [ ] Ubah logika Javascript form RSVP (`script.js`) yang tadinya menggunakan *LocalStorage*, menjadi fungsi `fetch()` atau *AJAX* yang mengirim data POST ke endpoint backend (misal: `api/save_rsvp.php`).

### Tahap 1.2: Halaman Tamu (Non-User Sesi)
- [ ] **Akses:** Tamu hanya dapat mengakses `index.php`.
- [ ] **Fungsi:** Form RSVP berfungsi normal mengirim data ke database. Menampilkan daftar ucapan tamu *(Wishes Wall)* dengan mengambil data dari backend (`api/get_wishes.php`). 

### Tahap 1.3: Halaman Login & Dashboard Admin (User Sesi)
- [ ] Buat file `login.php` yang berisi form login sederhana (Username & Password).
- [ ] Buat file `auth.php` untuk memvalidasi login dan membuat *Session* PHP (`$_SESSION['admin_logged_in'] = true`). Gunakan kredensial *hardcoded* saja jika ingin cepat (misal admin / admin123).
- [ ] Buat file `admin.php`. Di awal file ini, cek apakah session admin aktif. Jika tidak, *redirect* paksa kembali ke `login.php`.
- [ ] Di dalam `admin.php`, ambil seluruh data dari tabel `guests` dan tampilkan dalam bentuk tabel (*DataTables* disarankan).
- [ ] Tambahkan kolom **"Hadir di Lokasi?"** yang berisi elemen *Checkbox*. 
- [ ] Beri *event listener* pada setiap checkbox. Jika admin mencentang kotak tersebut, jalankan AJAX ke `api/checkin.php?id={id_tamu}&status=1` agar mengubah nilai `is_checked_in` di database secara *realtime* tanpa perlu reload halaman.

---

## 2. Fitur Slideshow Galeri Otomatis Perlahan

Misi ini adalah menyederhanakan *section* galeri menjadi tampak lebih eksklusif dan fokus.

### Tahap 2.1: Perubahan Markup (HTML)
- [ ] Temukan elemen `<section id="gallery">` di `index.php` (sebelumnya `index.html`).
- [ ] Hapus struktur `grid` yang lama.
- [ ] Buat struktur HTML *Carousel/Slideshow* dasar:
  ```html
  <div class="gallery-slideshow-container">
     <div class="slide-item active"><img src="foto1.jpg"></div>
     <div class="slide-item"><img src="foto2.jpg"></div>
     <div class="slide-item"><img src="foto3.jpg"></div>
  </div>
  ```

### Tahap 2.2: Perubahan Gaya (CSS)
- [ ] Di `style.css`, buat `.gallery-slideshow-container` dengan properti: 
  - `position: relative`
  - `width: 100%`, `max-width: 800px`
  - `height: 500px` (atau sesuaikan aspect ratio)
  - `overflow: hidden; margin: 0 auto;`
- [ ] Buat `.slide-item` menjadi `position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 2s ease-in-out;` (Gunakan transisi durasi panjang seperti `2s` agar efek perpindahannya terasa "perlahan" dan elegan).
- [ ] Buat modifier `.slide-item.active` dengan `opacity: 1;`.
- [ ] *(Opsional untuk efek geser)*: Jika ingin benar-benar bergeser dan bukan sekadar *fade*, gunakan `transform: translateX(100%)` pada slide tidak aktif, lalu ubah menjadi `translateX(0)` pada `.active` dengan transisi sangat lambat.

### Tahap 2.3: Perubahan Logika Animasi (JavaScript)
- [ ] Hapus logika *Lightbox* (modal preview galeri) pada `script.js` jika sudah tidak dibutuhkan.
- [ ] Buat fungsi auto-slide dengan `setInterval`:
  - Kumpulkan semua elemen `.slide-item` ke dalam array.
  - Tentukan index gambar saat ini (dimulai dari 0).
  - Setiap 4 atau 5 detik (misal `4000ms`), hilangkan kelas `active` dari gambar saat ini, tingkatkan index (atau reset ke 0 jika di akhir), dan tambahkan kelas `active` ke gambar berikutnya.

## Catatan Tambahan untuk Eksekutor
- Pastikan perubahan fitur RSVP dan Galeri ini **TIDAK merusak** fitur *Slow Auto-Scroll* yang sudah berjalan dari sebelumnya. Form RSVP tetap menjadi target dari pergerakan auto-scroll.
- Desain halaman `admin.php` bisa dibuat minimalis menggunakan framework bantuan kecil seperti Bootstrap atau Tailwind CDN agar cepat selesai.
