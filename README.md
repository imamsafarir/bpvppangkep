# 🏢 Portal Resmi BPVP Pangkep (Kementerian Ketenagakerjaan RI)

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

Dokumentasi lengkap pengembangan, instalasi, deployment (cPanel & aaPanel), pembaruan (*upgrade frontend/backend*), serta panduan pemecahan masalah (*troubleshooting*) untuk sistem portal **BPVP Pangkep**.

---

## 📑 Daftar Isi
1. [Tentang Sistem & Fitur Utama](#-tentang-sistem--fitur-utama)
2. [Spesifikasi & Kebutuhan Server](#-spesifikasi--kebutuhan-server)
3. [Panduan Instalasi di Lingkungan Lokal](#-panduan-instalasi-di-lingkungan-lokal-development)
4. [Panduan Deployment di aaPanel](#-panduan-deployment-di-aapanel)
5. [Panduan Deployment di cPanel](#-panduan-deployment-di-cpanel)
6. [Panduan Upgrade Frontend & Backend](#-panduan-upgrade-frontend--backend)
7. [Daftar Perintah Penting (Cheat Sheet)](#-daftar-perintah-penting-cheat-sheet)
8. [Panduan Troubleshooting Lengkap](#-panduan-troubleshooting-lengkap)

---

## 🌟 Tentang Sistem & Fitur Utama

Sistem portal ini dibangun menggunakan arsitektur modular di dalam `app/Modules/`:

* **🌐 Modul Website (`app/Modules/Website`)**:
  * Halaman beranda dinamis, profil balai, kejuruan, struktur organisasi, dan berita.
  * Galeri foto dan video terintegrasi.
  * Fitur Informasi Publik (Berkala, Serta Merta, Setiap Saat).
  * **Aksesibilitas Ramah Disabilitas (WCAG)**: Font ramah disleksia (*OpenDyslexic*), mode kontras tinggi, filter buta warna (*Protanopia, Deuteranopia, Tritanopia*), pembesar teks, kursor besar.
  * **Optimasi Performa & Caching**: Dukungan PWA Service Worker (`public/sw.js`), ETag 304, HTTP Cache-Control, dan Query Caching otomatis.
* **🔗 Modul Shortlink (`app/Modules/Shortlink`)**:
  * Manajemen pemendek tautan mandiri dengan kode unik kustom atau acak.
  * Halaman *Lead Capture* interaktif (judul kustom, tombol kustom, arahan kustom).
  * Pelacakan klik, pengunjung unik, perangkat, dan peramban (*analytics*).
  * Fitur Import & Export data leads (Excel/CSV).
* **📱 Modul Tim Sosmed (`app/Modules/TimSosmed`)**:
  * Manajemen alur kerja konten media sosial (Ide, Perencanaan, Pembuatan, Review, Terbit).
  * Kalender publikasi interaktif (*Content Calendar*).
  * Manajemen beban kerja anggota tim, integrasi media platform (Instagram, TikTok, YouTube, Facebook, Twitter).
  * Obrolan tim internal (*Floating Chat Livewire*).
* **🛡️ Multi-Role & Hak Akses (Spatie Permission + Filament)**:
  * Isolasi hak akses menu berdasarkan role: `admin_system`, `admin_shortlink`, `admin_medsos`, `admin_website`.

---

## 🖥️ Spesifikasi & Kebutuhan Server

Pastikan server memenuhi persyaratan berikut:
* **PHP**: Versi **8.2** atau lebih tinggi
* **Ekstensi PHP Wajib**:
  * `bcmath`, `curl`, `fileinfo`, `gd` (atau `imagick`), `intl`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
* **Database**: MySQL 8.0+ atau MariaDB 10.4+
* **Web Server**: Nginx, Apache, atau LiteSpeed
* **Node.js & NPM**: Node 18+ atau 20+ (untuk kompilasi lokal Vite/Tailwind)
* **Composer**: Composer 2.x

---

## 💻 Panduan Instalasi di Lingkungan Lokal (Development)

1. **Clone repositori**:
   ```bash
   git clone https://github.com/imamsafarir/bpvppangkep.git
   cd bpvppangkep
   ```

2. **Install Dependensi Composer & NPM**:
   ```bash
   composer install
   npm install
   ```

3. **Setup Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Sesuaikan konfigurasi database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) pada file `.env`.

4. **Jalankan Migrasi Database & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

5. **Buat Symlink Storage**:
   ```bash
   php artisan storage:link
   ```

6. **Kompilasi Aset Frontend**:
   ```bash
   # Mode pengembangan dengan hot reload:
   npm run dev

   # ATAU kompilasi build produksi:
   npm run build
   ```

7. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi di peramban pada `http://127.0.0.1:8000` (atau `http://bpvppangkep.test` jika menggunakan Laravel Herd/Valet).

---

## 🚀 Panduan Deployment di aaPanel

aaPanel sangat direkomendasikan karena mendukung penentuan subfolder `public` sebagai Document Root secara langsung tanpa perlu memindahkan file.

### Langkah-langkah:
1. **Tambahkan Website di aaPanel**:
   * Buka panel aaPanel -> Menu **Website** -> Klik **Add Site**.
   * Masukkan domain: `bpvppangkep.kemnaker.go.id`.
   * Pilih versi PHP: **PHP-8.2** atau lebih tinggi.
   * Buat Database MySQL sekaligus.
2. **Upload/Clone Proyek**:
   * Masuk ke terminal server atau File Manager aaPanel di direktori web (misal: `/www/wwwroot/bpvppangkep.kemnaker.go.id`).
   * Clone repositori:
     ```bash
     git clone https://github.com/imamsafarir/bpvppangkep.git .
     ```
3. **Konfigurasi Site Directory (Document Root) di aaPanel**:
   * Klik nama website di menu **Website** aaPanel -> Masuk ke tab **Site directory**.
   * **Site directory**: `/www/wwwroot/bpvppangkep.kemnaker.go.id`
   * **Running directory**: Ubah dari `/` menjadi **`/public`** -> Klik **Save**.
4. **Setup URL Rewrite (Nginx)**:
   * Masuk ke tab **URL rewrite**, pilih template **laravel5** (berlaku untuk semua Laravel modern), lalu klik **Save**:
     ```nginx
     location / {
         try_files $uri $uri/ /index.php?$query_string;
     }
     ```
5. **Install Dependensi & Konfigurasi**:
   Buka terminal di root proyek:
   ```bash
   composer install --no-dev --optimize-autoloader
   cp .env.example .env
   php artisan key:generate
   # Edit .env sesuai database aaPanel
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```
6. **Set Hak Akses (Permission)**:
   ```bash
   chown -R www:www /www/wwwroot/bpvppangkep.kemnaker.go.id
   chmod -R 775 storage bootstrap/cache
   ```

---

## 🌐 Panduan Deployment di cPanel

Pada cPanel, terdapat dua tipe skenario penempatan file:

### Skenario A: Document Root Mengarah Langsung ke `public` (Rekomendasi)
Jika cPanel Anda mengizinkan perubahan Document Root domain ke subfolder:
1. Ubah Document Root domain di cPanel (**Domains**) menjadi: `/home/username/bpvppangkep/public`.
2. Lakukan `git clone` atau `git pull` di `/home/username/bpvppangkep`.
3. Jalankan `composer install`, `php artisan migrate`, dan `php artisan storage:link`.

---

### Skenario B: Struktur Standar cPanel (`public_html` Terpisah)
Jika Document Root domain Anda terkunci pada `/home/username/public_html`:

1. **Letakkan Kode Proyek di Luar `public_html`**:
   Letakkan repositori pada direktori sejajar dengan `public_html` (misal: `/home/username/bpvppangkep_new`).
2. **Sinkronkan Folder `public` ke `public_html`**:
   Setelah melakukan clone/pull di folder proyek, salin seluruh aset statis ke `public_html`:
   ```bash
   cp -r /home/username/bpvppangkep_new/public/build /home/username/public_html/
   cp -r /home/username/bpvppangkep_new/public/images /home/username/public_html/
   cp /home/username/bpvppangkep_new/public/default-logo.png /home/username/public_html/
   cp /home/username/bpvppangkep_new/public/default-favicon.png /home/username/public_html/
   cp /home/username/bpvppangkep_new/public/favicon.ico /home/username/public_html/
   cp /home/username/bpvppangkep_new/public/favicon.svg /home/username/public_html/
   cp /home/username/bpvppangkep_new/public/sw.js /home/username/public_html/
   cp /home/username/bpvppangkep_new/public/manifest.json /home/username/public_html/
   ```
3. **Hubungkan `index.php` di `public_html`**:
   Pastikan file `/home/username/public_html/index.php` mengarah ke folder proyek Anda:
   ```php
   // Path autoload
   require __DIR__.'/../bpvppangkep_new/vendor/autoload.php';

   // Path bootstrap app
   $app = require_once __DIR__.'/../bpvppangkep_new/bootstrap/app.php';
   ```
4. **Symlink Storage pada cPanel**:
   ```bash
   ln -s /home/username/bpvppangkep_new/storage/app/public /home/username/public_html/storage
   ```

---

## 🔄 Panduan Upgrade Frontend & Backend

Gunakan panduan berikut setiap kali ada pembaruan kode baru:

### 1. Upgrade Backend (Logika PHP, Migrasi, Perbaikan Bug)
Jalankan perintah ini di terminal server (folder proyek):
```bash
# 1. Ambil commit terbaru dari branch utama
git pull origin main

# 2. Update dependensi PHP jika ada perubahan composer.json
composer install --no-dev --optimize-autoloader

# 3. Jalankan migrasi database jika ada tabel baru
php artisan migrate --force

# 4. Bersihkan dan segarkan cache konfigurasi serta view
php artisan optimize:clear
php artisan optimize
```

---

### 2. Upgrade Frontend (CSS Tailwind, Komponen Blade, Script Vite)
Karena server cPanel umumnya tidak memiliki Node.js/NPM atau keterbatasan RAM untuk build:
1. **Lakukan Build di Komputer Lokal**:
   ```bash
   npm run build
   git add public/build/
   git commit -m "Build assets terbaru"
   git push origin main
   ```
2. **Tarik Pembaruan di Terminal Server**:
   ```bash
   cd /home/username/bpvppangkep_new
   git pull origin main
   ```
3. **PENTING UNTUK cPanel (`public_html` terpisah)**:
   Salin file build dan aset baru ke `public_html`:
   ```bash
   cp -r public/build /home/username/public_html/
   cp -r public/images /home/username/public_html/
   cp public/sw.js /home/username/public_html/
   ```
4. **Segarkan Cache Laravel**:
   ```bash
   php artisan optimize:clear
   ```

---

## ⚡ Daftar Perintah Penting (Cheat Sheet)

| Perintah | Deskripsi & Kegunaan |
|---|---|
| `php artisan optimize:clear` | Menghapus seluruh cache (config, route, view, event). Wajib setelah deploy! |
| `php artisan optimize` | Mengkompilasi cache config dan routes agar eksekusi web lebih cepat di produksi. |
| `php artisan migrate --force` | Menjalankan migrasi database di lingkungan produksi tanpa konfirmasi interaktif. |
| `php artisan storage:link` | Membuat symlink folder publik untuk file yang diunggah pengguna. |
| `php artisan test` | Menjalankan seluruh pengujian otomatis fitur, role, dan cache. |
| `npm run dev` | Menjalankan Vite development server untuk pengerjaan frontend di lokal. |
| `npm run build` | Mengompilasi Tailwind CSS dan JavaScript menjadi aset statis produksi di `public/build`. |

---

## 🛠️ Panduan Troubleshooting Lengkap

### 🔴 1. Tampilan Website Rusak / Polos (CSS 404: `app-*.css not found`)
* **Gejala**: Tampilan web berantakan, di Inspect Console muncul error: `GET https://domain/build/assets/app-xxxx.css net::ERR_ABORTED 404`.
* **Penyebab**: Web server membaca dari folder yang berbeda (misal `public_html/build`), sedangkan file hasil build baru berada di dalam folder proyek (`bpvppangkep_new/public/build`).
* **Solusi**:
  1. Cari lokasi file lama dan baru di terminal server:
     ```bash
     find ~ -name "manifest.json"
     ```
  2. Salin folder build terbaru ke Document Root:
     ```bash
     cp -r /path/to/project/public/build /path/to/public_html/
     ```
  3. Bersihkan cache: `php artisan optimize:clear`.

---

### 🔴 2. Error 500: `The script tried to call a method on an incomplete object`
* **Gejala**: Halaman utama blank atau error 500 saat mencoba membaca data dari cache.
* **Penyebab**: Konfigurasi `config/cache.php` memiliki `'serializable_classes' => false`, sehingga objek Eloquent Collection yang di-cache di-*unserialize* sebagai `__PHP_Incomplete_Class`.
* **Solusi**:
  1. Pastikan di `config/cache.php`:
     ```php
     'serializable_classes' => null, // Izinkan serialisasi kelas Eloquent
     ```
  2. Bersihkan cache yang korup:
     ```bash
     php artisan cache:clear
     ```

---

### 🔴 3. Logo Default atau Favicon 404
* **Gejala**: Console browser mencatat 404 pada `default-logo.png` atau `default-favicon.png`.
* **Penyebab**: File fisik default belum berada di dalam folder `public/` atau `public_html/`.
* **Solusi**:
  Salin file default yang sudah disediakan:
  ```bash
  cp public/icon-192.png /path/to/public_html/default-favicon.png
  cp public/icon-192.png /path/to/public_html/default-logo.png
  ```

---

### 🔴 4. Gambar Upload Berita & Galeri Tidak Muncul (Broken Image)
* **Gejala**: Gambar berita atau galeri yang diunggah melalui admin panel tidak tampil di website publik.
* **Penyebab**: Symlink `storage` pada Document Root rusak atau belum dibuat.
* **Solusi**:
  * Jika menggunakan aaPanel atau skenario A:
    ```bash
    php artisan storage:link
    ```
  * Jika menggunakan cPanel skenario B (`public_html`):
    Buat symlink manual dari folder penyimpanan proyek ke `public_html`:
    ```bash
    ln -s /home/username/bpvppangkep_new/storage/app/public /home/username/public_html/storage
    ```

---

### 🔴 5. Error `500 Internal Server Error` saat Login atau Tulis Data (Permission Issue)
* **Gejala**: Website tidak dapat menulis log file atau session.
* **Penyebab**: Web server (user `www` atau user cPanel) tidak memiliki izin menulis pada folder `storage` dan `bootstrap/cache`.
* **Solusi**:
  Jalankan perintah izin akses:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```

---

### 🔴 6. Service Worker Caching Masih Menampilkan Versi Lama
* **Gejala**: Pengunjung lama masih melihat tampilan lama karena tersimpan di cache Service Worker browser.
* **Penyebab**: Service worker aktif meng-cache aset statis dengan versi cache sebelumnya.
* **Solusi**:
  1. Di `public/sw.js`, naikkan nomor versi cache:
     ```javascript
     const CACHE_NAME = "bpvp-client-cache-v3"; // Naikkan ke v4 jika ada perubahan besar
     ```
  2. Salin `sw.js` ke `public_html`.
  3. Lakukan **Hard Refresh** di browser: `Ctrl + Shift + R` (Windows) atau `Cmd + Shift + R` (Mac).

---

## 👥 Kontributor & Lisensi

Sistem ini dikembangkan dan dikelola untuk **Balai Pelatihan Vokasi dan Produktivitas (BPVP) Pangkep**, Kementerian Ketenagakerjaan Republik Indonesia.
Hak cipta dilindungi undang-undang.
