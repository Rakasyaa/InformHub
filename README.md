<div align="center">
    <h1>Projek - Pemrograman web</h1>
    <h1>🎓 InformHub</h1>
    <h3>Platform Forum Pembelajaran Interaktif</h3>
    <p>Sebuah platform forum online yang menghubungkan pembelajar dari berbagai latar belakang untuk berbagi pengetahuan, berdiskusi, dan berkolaborasi dalam topik-topik pembelajaran.</p>

    [![License](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
    [![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php)](https://www.php.net/)
    [![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
    [![Bootstrap](https://img.shields.io/badge/Bootstrap-5.2-7952B3?logo=bootstrap)](https://getbootstrap.com/)
</div>

## 💽 Installation

repo bersama projek web (Pliss kerjain biar cepet selesai)

## 🚀 Cara Menjalankan Proyek (dengan XAMPP)

### 1. Install XAMPP

Jika belum punya, unduh dan instal [XAMPP](https://www.apachefriends.org/index.html).

### 2. Aktifkan Apache & MySQL

- Buka **XAMPP Control Panel**
- Klik **Start** pada **Apache** dan **MySQL**

### 3. Salin Folder Proyek

1. Clone this repository to your XAMPP htdocs folder
2. Salin folder `InformHub` ke: C:\xampp\htdocs\

### 4. Import Database ke MySQL

1. Buka browser dan pergi ke: http://localhost/phpmyadmin
2. Klik database tersebut → Tab **Import**
3. Pilih file `database.sql` dari folder `database/`
4. Klik **Go**

> Databse `InformHub` dan Tabel yang ada di dalamnya akan otomatis dibuat, beserta beberapa data jika sudah disiapkan.

### 5. Jalankan Website

Buka browser dan akses: http://localhost/InformHub/index.php

### Project Structure

## 🌟 Fitur Utama

<div align="center">
  <table>
    <tr>
      <td>🔐 Autentikasi Pengguna</td>
      <td>📝 Buat & Kelola Postingan</td>
      <td>💬 Diskusi Interaktif</td>
    </tr>
    <tr>
      <td>✨ Daftar & Masuk Akun</td>
      <td>📊 Sistem Vote (Upvote/Downvote)</td>
      <td>🔍 Pencarian Topik</td>
    </tr>
    <tr>
      <td>👥 Peran Pengguna & Moderator</td>
      <td>🏷️ Kategori Topik</td>
      <td>📱 Desain Responsif</td>
    </tr>
  </table>
</div>

## 🚀 Panduan Instalasi

### Persyaratan Sistem
- PHP 7.4 atau lebih baru
- MySQL 8.0 atau lebih baru
- Web Server (XAMPP/Laragon/WAMP)
- Composer (disarankan)

### Langkah-langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/InformHub.git
   ```

2. **Salin ke Folder Web Server**
   - Salin folder `InformHub` ke dalam direktori `htdocs` (XAMPP) atau `www` (Laragon)

3. **Konfigurasi Database**
   - Buka phpMyAdmin di http://localhost/phpmyadmin
   - Buat database baru bernama `informhub`
   - Import file `database/forum_db.sql` ke database yang baru dibuat
   - Sesuaikan konfigurasi database di `config/database.php`

4. **Jalankan Aplikasi**
   - Buka browser dan akses http://localhost/InformHub
   - Gunakan akun admin default:
     - Email: admin@gmail.com
     - Username: admin
     - Password: 123

## 🛠 Teknologi yang Digunakan

### Frontend
- HTML5, CSS3, JavaScript
- Bootstrap 5.2
- Font Awesome Icons
- jQuery

### Backend
- PHP 7.4+
- MySQL 8.0+
- PDO Extension

### Tools & Lainnya
- XAMPP/Laragon
- Git
- Composer
- VS Code (disarankan)

## 📂 Struktur Proyek

```
InformHub/
├── assets/           # Aset statis (CSS, JS, gambar)
│   ├── css/          # Stylesheet
│   ├── js/           # File JavaScript
│   └── images/       # Gambar dan ikon
├── config/           # File konfigurasi
│   └── database.php  # Konfigurasi koneksi database
├── database/         # Skema database
│   └── forum_db.sql  # File SQL untuk membuat database
├── includes/         # File PHP includes
├── pages/            # Halaman template
│   ├── auth/         # Halaman autentikasi
│   ├── dashboard/    # Halaman dashboard
│   └── ...
├── uploads/          # File yang diunggah pengguna
├── .htaccess         # Konfigurasi server
├── index.php         # Halaman utama
└── README.md         # Dokumentasi ini
```

## 🤝 Berkontribusi

Kami sangat menerima kontribusi! Berikut cara Anda bisa ikut berkontribusi:

1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b fitur/namafitur`)
3. Commit perubahan Anda (`git commit -m 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin fitur/namafitur`)
5. Buat Pull Request

## 📝 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

## 📞 Kontak

- Email: tim@informhub.id
- Website: [www.informhub.id](https://www.informhub.id)
- Twitter: [@InformHubID](https://twitter.com/InformHubID)

---

<div align="center">
  Dibuat dengan ❤️ oleh Tim Pengembang InformHub
</div>
