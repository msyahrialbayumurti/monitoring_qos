# NETMONITOR

**Sistem Monitoring Quality of Service (QoS)**

NETMONITOR adalah aplikasi berbasis Laravel untuk membantu pemantauan dan analisis Quality of Service (QoS) jaringan.

## Fitur

- Dashboard monitoring.
- Pemantauan dan pengukuran QoS jaringan.
- Riwayat hasil pengukuran.
- Analisis QoS.
- Rekomendasi berdasarkan hasil pemantauan.
- Pengelolaan data sekolah dan pengaturan aplikasi.

## Teknologi

- PHP
- Laravel
- MySQL atau database lain yang didukung oleh konfigurasi aplikasi
- HTML, CSS, dan JavaScript

## Persyaratan

Pastikan perangkat pengembangan sudah memiliki PHP, Composer, database yang sesuai, serta Node.js dan npm jika diperlukan untuk membangun aset frontend.

## Instalasi

1. Clone repository:

   ```bash
   git clone https://github.com/ferdikuliah7/monitoring-qos.git
   cd monitoring-qos
   ```

2. Pasang dependensi PHP:

   ```bash
   composer install
   ```

3. Buat file konfigurasi lingkungan:

   ```bash
   cp .env.example .env
   ```

   Di Windows, kamu juga bisa menyalin `.env.example` menjadi `.env` melalui File Explorer.

4. Buat application key:

   ```bash
   php artisan key:generate
   ```

5. Sesuaikan konfigurasi database pada `.env`, lalu jalankan migrasi:

   ```bash
   php artisan migrate
   ```

6. Jalankan aplikasi secara lokal:

   ```bash
   php artisan serve
   ```

Buka alamat lokal yang ditampilkan oleh Laravel di terminal.

## Kontribusi

Saran, laporan masalah, dan kontribusi pengembangan dipersilakan melalui GitHub.

## Lisensi

Lisensi proyek belum ditentukan.
