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

<div align="start">
    <h2>👨 Anggota Kelompok:</h2>
    <ul style="list-style: none; padding-left: 0;">
        <li>Rakasya Yoga Surya Pratama (F1D02310022)</li>
        <li>M. Ali Abdillah Khotami (F1D02310073)</li>
        <li>R. Rafi Yudi Pramana (F1D02310132)</li>
        <li>Muhammad Ridho Fahru Rozy(F1D022076)</li>
    </ul>
    <p>repo bersama projek web </p>
</div>


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

### 💽 Langkah-langkah Instalasi

1. **Install XAMPP**

Jika belum punya, unduh dan instal [XAMPP](https://www.apachefriends.org/index.html).

2. **Clone Repository**
   ```bash
   git clone https://github.com/username/InformHub.git
   ```

3. **Aktifkan Apache & MySQL**
    - Buka **XAMPP Control Panel**
    - Klik **Start** pada **Apache** dan **MySQL**

4. **Salin ke Folder Web Server**
    - Salin folder `InformHub` ke: C:\xampp\htdocs\

5. **Konfigurasi Database**

   - Buka phpMyAdmin di http://localhost/phpmyadmin
   - Buat database baru bernama `InformHub`
   - Klik database tersebut dan pergi ke Tab **Import**
   - Pilih file `informhub.sql` dari folder `database/`
   - Klik **Go**
   - Sesuaikan konfigurasi database di `config/database.php`
> Databse `InformHub` dan Tabel yang ada di dalamnya akan otomatis dibuat, beserta beberapa data jika sudah disiapkan.

6. **Jalankan Aplikasi**
    - Buka browser dan akses http://localhost/InformHub/index.php
    - Gunakan akun admin default:
        - Email: admin@gmail.com
        - Username: admin
        - Password: 123
    - jika tidak ada, update akses admin di http://localhost/InformHub/install.php
    - Gunakan akun moderator default:
        - Email: Rakasya@gmail.com
        - Username: Rakasya
        - Password: 123
    - Gunakan akun user default:
        - Email: Yoga@gmail.com
        - Username: Yoga
        - Password: 123
        - Email: Ali@gmail.com
        - Username: Ali
        - Password: 123
        - Email: Rafi@gmail.com
        - Username: Rafi
        - Password: 123


## 🛠 Teknologi yang Digunakan

### Frontend
- HTML5, CSS3, JavaScript
- framework Bootstrap 5.2
- Font Awesome Icons

### Backend
- PHP 7.4+
- MySQL 8.0+

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
│   └── informhub.sql  # File SQL untuk membuat database
├── includes/         # File PHP includes
├── pages/            # Halaman template
│   ├── auth/         # Halaman autentikasi
│   ├── dashboard/    # Halaman dashboard
│   └── ...
├── uploads/          # File yang diunggah pengguna
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

Proyek ini dilisensikan di bawah [Tekni Infor UNRAM](LICENSE).

## 📞 Kontak

- Email: tim@informhub.id
- Website: [www.informhub.id](https://www.informhub.id)
- Twitter: [@InformHubID](https://twitter.com/InformHubID)

---

<div align="center">
  Dibuat dengan ❤️ oleh Tim Pengembang InformHub
</div>
