# Projek - Pemrograman web

repo bersama projek web (Pliss kerjain biar cepet selesai)Add commentMore actions

## 🚀 Cara Menjalankan Proyek (dengan XAMPP)

### 1. Install XAMPP

Jika belum punya, unduh dan instal [XAMPP](https://www.apachefriends.org/index.html).

### 2. Aktifkan Apache & MySQL

- Buka **XAMPP Control Panel**
- Klik **Start** pada **Apache** dan **MySQL**

### 3. Salin Folder Proyek

- Salin folder `projek_web` ke: C:\xampp\htdocs\

### 4. Import Database ke MySQL

1. Buka browser dan pergi ke: http://localhost/phpmyadmin
2. Klik database tersebut → Tab **Import**
3. Pilih file `database.sql` dari folder `database/`
4. Klik **Go**

> Databse dan Tabel `users` akan otomatis dibuat, beserta beberapa data jika sudah disiapkan.

### 5. Jalankan Aplikasi

Buka browser dan akses: http://localhost/forum/home.php
Add comment

# Learning Forum Web Application

A web forum application similar to Reddit/Facebook communities but focused on learning topics. Users can create posts, comment, upvote/downvote, and follow topic spaces.

## Features

- User authentication (login/register)
- User roles (regular users and moderators)
- Home page showing posts from followed topics
- Search page to find topic spaces
- Topic spaces with related posts
- Create posts with text, images, or videos
- Comment on posts with threaded replies
- Edit posts and comments
- Upvote/downvote system
- Follow/unfollow topic spaces
- Moderator capabilities (create topic spaces, moderate content)

## Technologies Used

- Frontend: HTML, CSS, JavaScript, Bootstrap 5
- Backend: PHP
- Database: MySQL

## Installation

1. Clone this repository to your XAMPP htdocs folder
2. Import the database schema from `database/forum_db.sql`
3. Configure database connection in `config/database.php`
4. Access the application through `http://localhost/forum`

## Project Structure

```
forum/
├── assets/           # CSS, JS, images
├── config/           # Configuration files
├── database/         # Database schema
├── includes/         # PHP includes
├── js/               # JavaScript files
├── pages/            # Page templates
├── uploads/          # User uploaded content
├── index.php         # Main entry point
└── README.md         # This file
```

## License

MIT
