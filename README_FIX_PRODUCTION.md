# Panduan Perbaikan Masalah Produksi & Lokal

Saya telah memperbaiki masalah link index.html yang tidak bisa dibuka di lokal, serta masalah 403 Forbidden di server produksi. Berikut langkah-langkah yang perlu Anda lakukan:

## 1. Perbaikan Link Lokal (Selesai)
File `index.html` sudah saya update. Sekarang, file ini bisa dibuka langsung (klik ganda file) maupun lewat server (localhost/domain). Link ke ERP dan Recruitment akan menyesuaikan otomatis.

## 2. Perbaikan Error 403 Forbidden di Produksi
Masalah ini terjadi karena server tidak mengizinkan akses (permission denied) atau konfigurasi .htaccess tidak terbaca.

### Opsi A: Menggunakan Script Otomatis (Paling Mudah)
1. Upload file `fix_permissions.php` ke folder root public_html di hosting/server Anda.
2. Buka browser dan kunjungi: `http://domain-anda.com/fix_permissions.php`
3. Tunggu sampai muncul pesan "Done!".
4. **PENTING**: Hapus file `fix_permissions.php` dari server setelah selesai demi keamanan.

### Opsi B: Menggunakan SSH (Untuk Advanced User)
Jika Anda punya akses terminal/SSH:
1. Upload `fix_htaccess.sh` ke root folder.
2. Jalankan perintah:
   ```bash
   chmod +x fix_htaccess.sh
   ./fix_htaccess.sh
   ```

## 3. Update File Lain
Saya juga telah memperbarui file sistem agar lebih kompatibel dengan berbagai hosting:
- `recruitment/public/index.php`: Deteksi URL otomatis (tidak perlu setting manual).
- `erp/index.php`: Deteksi URL otomatis.
- `recruitment/index.php`: File baru sebagai backup jika .htaccess gagal, agar Recruitment tetap bisa dibuka.

## Langkah Terakhir
Upload ulang file-file yang telah diubah ke server produksi Anda:
1. `index.html`
2. `fix_permissions.php`
3. `fix_htaccess.sh`
4. `recruitment/index.php`
5. `recruitment/public/index.php`
6. `erp/index.php`
