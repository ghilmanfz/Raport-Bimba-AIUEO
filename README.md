# 📚 E-Rapor BiMBA AIUEO

Aplikasi **E-Rapor Digital** untuk lembaga pendidikan anak **BiMBA AIUEO** — sistem pencatatan dan pelaporan perkembangan belajar murid berbasis web.

---

## ✨ Fitur Utama

- 📊 **Dashboard Admin** — Statistik murid, guru, dan aktivitas terkini
- 👨‍🎓 **Manajemen Data Murid** — CRUD data murid dengan status aktif/lulus/pindah
- 👩‍🏫 **Manajemen Data Guru** — Data motivator/guru, status aktif/nonaktif/cuti, dan plotting siswa bimbingan
- 👪 **Manajemen Data Wali Murid** — Daftar wali murid, relasi anak, dan status otomatis berbasis data siswa
- 📝 **Pengolahan Nilai Progres** — Input nilai per aspek (Baca, Tulis, Hitung) dengan skala K/B/P/T
- 📈 **Grafik Perkembangan** — Visualisasi tren kemajuan level siswa per periode
- 🖨️ **Cetak Rapor Digital** — Generate dan print laporan perkembangan murid
- 👪 **Portal Wali Murid** — Pantau progres dan rapor anak secara online
- ⚙️ **Pengaturan Sistem** — Konfigurasi institusi, penilaian, role, dan keamanan

---

## 🏗️ Teknologi

| Teknologi | Keterangan |
|-----------|-----------|
| **Laravel 11** | PHP Framework (Backend) |
| **Blade** | Template engine Laravel |
| **TailwindCSS 4** | CSS Framework — di-*compile* lokal via Vite (tanpa CDN) |
| **Iconify (Lucide)** | Icon library — data ikon di-*bundle* offline |
| **@fontsource** | Font self-hosted: Inter, Roboto, Poppins (tanpa Google Fonts) |
| **Chart.js / Alpine.js** | Grafik & interaktivitas — di-*bundle* lokal via Vite |
| **DomPDF + simple-qrcode** | Cetak rapor PDF & QR code (server-side, offline) |
| **Laragon** | Server lokal (PHP + MySQL) |

---

## 🔌 Mode Offline (Tanpa Internet)

Aplikasi ini dirancang berjalan **100% offline** — tidak ada CDN, Google Fonts, maupun API eksternal. Semua aset (CSS, JS, font, ikon) di-*compile* lokal oleh Vite ke `public/build/`, sedangkan PDF rapor dan QR code dibuat sepenuhnya di sisi server.

**Cara menjalankan tanpa internet** — lihat panduan lengkap di **[PANDUAN-OFFLINE.md](PANDUAN-OFFLINE.md)**. Ringkasnya:

1. Pastikan **Laragon** (Apache/Nginx + MySQL) menyala.
2. Buka `http://raportbimba.test/` atau jalankan `php artisan serve`.

> ⚠️ Saat memindahkan ke komputer lain yang offline, **salin seluruh folder apa adanya** (termasuk `vendor/` dan `public/build/`). Jangan `git clone`, karena kedua folder itu tidak ikut ke repository dan mengisinya kembali (`composer install` / `npm install`) membutuhkan internet.

---

## 👥 Role Pengguna

| Role | Akses |
|------|-------|
| **Admin** | Dashboard, Data Guru, Data Murid, Data Wali Murid |
| **Guru / Motivator** | Dashboard, Daftar Murid Bimbingan, Pengolahan Nilai, Grafik, Cetak Rapor |
| **Wali Murid** | Dashboard anak, Laporan Rapor |

---

## 📁 Struktur Views

```
resources/views/
├── layouts/
│   ├── admin.blade.php
│   ├── guru.blade.php
│   └── wali.blade.php
├── auth/
│   └── login.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── murid.blade.php
│   ├── guru.blade.php
│   └── pengaturan.blade.php
├── guru/
│   ├── dashboard.blade.php
│   ├── nilai.blade.php
│   ├── grafik.blade.php
│   └── rapor.blade.php
├── wali/
│   ├── dashboard.blade.php
│   └── rapor.blade.php
└── welcome.blade.php
```

---

## 🚀 Instalasi

```bash
git clone https://github.com/ghilmanfz/Raport-Bimba-AIUEO.git
cd Raport-Bimba-AIUEO
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# opsional jika baru clone dan ingin data awal
# php artisan db:seed
```

Akses: `http://raportbimba.test/`

---

## 📊 Skala Penilaian BiMBA

| Kode | Label | Deskripsi |
|------|-------|-----------|
| **K** | Kenal | Baru mengenal, belum menunjukkan minat |
| **B** | Belum | Mulai belajar, tahap pengenalan |
| **P** | Paham | Materi dikuasai, mengerti konsep dasar |
| **T** | Terampil | Mahir, dapat mengaplikasikan secara mandiri |

---

## 📌 Status Pengembangan

- [x] Front-end slicing 15 halaman
- [x] Layout Blade (3 role)
- [x] Routing dasar
- [ ] Autentikasi dan Middleware role
- [ ] Database migrations dan Models
- [ ] Controllers dan CRUD

---

## 👨‍💻 Developer

**Ghilman Faza** — [@ghilmanfz](https://github.com/ghilmanfz)

---

> *"Tujuan utama bukan hanya bisa baca, tapi MINAT baca yang tumbuh sepanjang hayat."* — BiMBA AIUEO
