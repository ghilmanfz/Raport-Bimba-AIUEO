# Rapor tiga bulanan dan perbaikan grafik

## Aturan yang berlaku

- Satu periode berisi tepat tiga **bulan belajar**, bukan empat potongan bulan kalender. Murid masuk 11 September memiliki rentang 11 Sep–10 Okt, 11 Okt–10 Nov, dan 11 Nov–10 Des. Rapor dibagikan 11 Desember.
- Untuk tanggal 29–31, batas bulan memakai tanggal terakhir yang tersedia. Semua batas dihitung dari tanggal masuk asli agar periode tidak bergeser atau menyisakan hari kosong.
- Absensi hanya dihitung pada rentang periode yang dipilih dan sampai tanggal laporan. Hari belum dicatat bukan Alpa. Halaman rapor menyediakan pilihan periode; QR PDF mengacu ke periode dan tanggal laporan yang sama.
- Grafik guru menghitung **penilaian modul**, bukan jumlah murid. Setiap modul pada setiap murid menyumbang satu status tertinggi yang sudah bertanggal: T, lalu P, lalu K. Tanpa tanggal berarti Belum Dinilai dan tidak masuk penyebut persentase.
- Data lama dengan status K tetapi tanpa tanggal tidak dihitung. Tidak ada penghapusan massal data lama. Jika guru mengosongkan semua tanggal pada suatu modul dan menyimpan, penilaian modul tersebut dihapus; modul yang tidak dikirim dalam formulir tetap utuh.

## Memperbarui aplikasi

1. Cadangkan database dan salin kode aplikasi yang diperbarui ke server.
2. Jalankan migrasi tambahan. Migrasi ini hanya menambahkan kunci unik opsional pada tabel notifikasi untuk mencegah pengingat ganda:

   ```sh
   php artisan migrate --force
   ```

3. Bangun aset dan segarkan cache tampilan/rute. Jika server tidak memiliki Node.js, bangun aset di komputer pengembangan kemudian salin `public/build`:

   ```sh
   npm run build
   php artisan route:clear
   php artisan view:cache
   ```

4. Periksa pendaftaran dan simulasi pengingat tanpa mengirim notifikasi:

   ```sh
   php artisan schedule:list
   php artisan rapor:remind --dry-run
   ```

   Daftar jadwal harus memuat satu `rapor:remind` pada pukul 09.00 zona `Asia/Jakarta`. Tidak perlu menjalankan seeder atau membersihkan tabel nilai.

## Menjalankan pengingat otomatis

Kode aplikasi sudah mendaftarkan jadwal. **Server tetap harus menjalankan Laravel scheduler**; sekadar membuka dashboard tidak menjalankan pengingat.

### Komputer lokal / Laragon

Jalankan perintah berikut dari folder proyek dan biarkan terminal aktif selama aplikasi digunakan:

```sh
php artisan schedule:work
```

Untuk penggunaan permanen, administrator dapat mengatur Windows Task Scheduler menjalankan `php artisan schedule:run` setiap menit, dengan folder kerja proyek dan lokasi PHP yang benar. Jangan menjalankan dua mekanisme sekaligus tanpa kebutuhan.

### Hosting Linux / cPanel

Tambahkan satu cron job setiap menit. Ganti placeholder lokasi PHP dan folder proyek dengan lokasi yang benar pada hosting; ini bukan perintah siap salin sebelum lokasinya disesuaikan:

```cron
* * * * * cd /path/ke/proyek && /path/ke/php artisan schedule:run >> /dev/null 2>&1
```

Notifikasi ditujukan kepada wali murid, guru yang terdaftar sebagai pembimbing murid, dan admin. Notifikasi berada **di dalam aplikasi**, bukan pesan WhatsApp atau pengiriman PDF otomatis. Tautan pengingat guru/wali membuka periode yang baru selesai.

Pengingat hanya mengirim untuk murid aktif yang memiliki tanggal masuk dan tanggal pembagiannya jatuh hari itu. Pastikan scheduler dan database aktif pukul 09.00 WIB. Jika terlewat tetapi masih hari yang sama, admin server dapat menjalankan `php artisan rapor:remind` setelah memeriksa `--dry-run`; pengulangan tidak membuat notifikasi duplikat. Perintah ini tidak mengirim pengingat susulan untuk tanggal yang sudah lewat.
