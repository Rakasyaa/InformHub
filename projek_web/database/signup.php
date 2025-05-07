<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Validasi password dan konfirmasi
    if ($password !== $confirm) {
        echo "<script>alert('Password dan konfirmasi tidak cocok');window.location.href='../Login/Signup.html';</script>";
        exit();
    }

    // Cek apakah email sudah ada
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Email sudah terdaftar');window.location.href='../Login/Signup.html';</script>";
        exit();
    }

    // Masukkan data ke database
    $sql = "INSERT INTO users (email, password) VALUES ('$email', '$password')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Signup berhasil! Silakan login');window.location.href='../Login/Login.html';</script>";
    } else {
        echo "Gagal signup: " . mysqli_error($conn);
    }
}
?>
