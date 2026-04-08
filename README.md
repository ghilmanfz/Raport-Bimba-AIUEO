# 📚 E-Rapor BiMBA AIUEO

Aplikasi **E-Rapor Digital** untuk lembaga pendidikan anak **BiMBA AIUEO** — sistem pencatatan dan pelaporan perkembangan belajar murid berbasis web.

---

## ✨ Fitur Utama

- 📊 **Dashboard Admin** — Statistik murid, guru, dan aktivitas terkini
- 👨‍🎓 **Manajemen Data Murid** — CRUD data murid dengan status aktif/cuti
- 👩‍🏫 **Manajemen Data Guru** — Data motivator/guru beserta spesialisasi
- 📝 **Pengolahan Nilai Progres** — Input nilai per aspek (Baca, Tulis, Hitung) dengan skala K/B/P/T
- 📈 **Grafik Perkembangan** — Visualisasi tren kemajuan level siswa per periode
- 🖨️ **Cetak Rapor Digital** — Generate dan print laporan perkembangan murid
- 👪 **Portal Wali Murid** — Pantau progres dan rapor anak secara online
- ⚙️ **Pengaturan Sistem** — Konfigurasi institusi, penilaian, role, dan keamanan

---

## 🏗️ Teknologi

| Teknologi | Keterangan |
|-----------|-----------|
| **Laravel 13** | PHP Framework (Backend) |
| **Blade** | Template engine Laravel |
| **TailwindCSS** | CSS Framework via CDN |
| **Iconify (Lucide)** | Icon library |
| **Google Fonts** | Inter, Roboto, Poppins |
| **Laragon** | Local development server |

---

## 👥 Role Pengguna

| Role | Akses |
|------|-------|
| **Admin** | Dashboard, Data Murid, Data Guru, Pengaturan |
| **Guru / Motivator** | Dashboard, Pengolahan Nilai, Grafik, Cetak Rapor |
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
