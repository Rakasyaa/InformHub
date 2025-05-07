<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah email ada dulu
    $sql_email = "SELECT * FROM users WHERE email = '$email'";
    $result_email = mysqli_query($conn, $sql_email);

    if (mysqli_num_rows($result_email) == 1) {
        // Email ditemukan, cek password-nya
        $user = mysqli_fetch_assoc($result_email);

        if ($user['password'] === $password) {
            // Password cocok → login berhasil
            $_SESSION['email'] = $email;
            header("Location: ../Home/index.html");
            exit();
        } else {
            // Password salah
            echo "<script>alert('Login gagal: Password salah');window.location.href='../Login/Login.html';</script>";
        }
    } else {
        // Email tidak ditemukan
        echo "<script>alert('Login gagal: Email tidak ditemukan');window.location.href='../Login/Login.html';</script>";
    }
}
?>
