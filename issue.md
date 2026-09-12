# Project: Ibnu Wedding Invitation Landing Page

## Deskripsi Singkat
Buat sebuah website landing page untuk undangan pernikahan dengan desain modern dan elegan. Landing page ini harus memiliki fitur auto-scroll (scroll otomatis secara perlahan) ke bagian form "Kehadiran Tamu" saat halaman dibuka atau saat tamu menekan tombol awal.

## Spesifikasi & Fitur Utama
1. **Hero Section (Bagian Utama)**
   - Tampilkan nama kedua mempelai. (**Wajib: Gunakan nama fiktif/random untuk sementara waktu**).
   - Tampilkan foto kedua mempelai. (**Wajib: Gunakan foto random/placeholder bertema pernikahan**).
   - Sediakan elemen pemicu (contoh: Tombol "Buka Undangan").

2. **Fitur Auto-Scroll (Perlahan) ke Form Kehadiran**
   - Implementasikan fungsi JavaScript untuk melakukan *smooth scroll* dengan perlahan (slow scroll) dari atas langsung menuju ke bagian bawah tempat form "Kehadiran Tamu" (RSVP) berada.
   - Kecepatan scroll harus diatur agar tidak terlalu cepat, memberikan kesan elegan.

3. **Form Kehadiran Tamu (RSVP Section)**
   - Form sederhana yang setidaknya menanyakan:
     - Nama Tamu
     - Jumlah Kehadiran
     - Konfirmasi Kehadiran (Hadir / Tidak Hadir)
     - Ucapan & Doa

4. **Desain & UI/UX**
   - Desain harus **Responsif (Mobile Friendly)** karena tamu umumnya membuka lewat smartphone.
   - Gunakan warna elegan, pastel, atau tema pernikahan standar.
   - Pastikan layout rapi.

## Tahapan Pengerjaan (Task Breakdown)
*Dokumen ini disusun agar mudah dieksekusi oleh Junior Programmer atau AI Model.*

### Tahap 1: Setup Proyek
- [ ] Buat struktur proyek dasar HTML, CSS (Vanilla atau Tailwind CSS), dan JavaScript.
- [ ] Siapkan file `index.html`, `style.css`, dan `script.js`.
- [ ] Cari dan pasang foto dummy/placeholder dari Unsplash (atau sumber sejenis) dengan keyword "wedding".

### Tahap 2: Struktur HTML (Markup)
- [ ] Buat kerangka halaman (Hero section, sedikit jarak/konten dummy di tengah, dan Form RSVP di paling bawah).
- [ ] Letakkan **nama dummy mempelai** (misal: "Romeo & Juliet") dan **foto dummy** di bagian atas (Hero).
- [ ] Tambahkan tombol "Buka Undangan" di Hero section yang nanti akan men-trigger efek scroll.
- [ ] Buat bagian form RSVP di akhir halaman dengan ID tertentu, misal: `id="rsvp-form"`.

### Tahap 3: Styling (CSS)
- [ ] Buat tampilan menarik menggunakan desain *mobile-first*.
- [ ] Gunakan tipografi yang cocok untuk undangan pernikahan (seperti font latin / handwriting untuk nama, dan font sans-serif untuk teks).
- [ ] Percantik form agar terlihat menyatu dengan tema.

### Tahap 4: Logika JavaScript (Slow Scroll)
- [ ] Buat event listener ketika tombol "Buka Undangan" diklik (atau trigger otomatis saat halaman siap).
- [ ] Implementasikan *custom smooth scroll* ke elemen `id="rsvp-form"`. 
- [ ] **Penting**: Gunakan fungsi `requestAnimationFrame` jika `behavior: 'smooth'` bawaan browser dianggap terlalu cepat, agar scroll benar-benar berjalan "perlahan".

### Tahap 5: Pengujian
- [ ] Lakukan tes di mode mobile dan desktop.
- [ ] Pastikan fungsi scroll mengarah tepat ke lokasi form (tidak terpotong).
- [ ] Biarkan dummy content (nama dan foto) karena akan diganti secara manual nanti setelah desain dan fitur disetujui.

## Catatan Penting
- Jangan habiskan waktu mencari nama/foto asli sekarang, cukup pakai variabel dummy dan beri komentar pada kodenya agar mudah diubah.
- Fokus utama pada estetika dan fungsi "slow scroll".
