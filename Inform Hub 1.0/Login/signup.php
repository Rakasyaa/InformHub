<?php
include '../database/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Sanitize inputs to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);
    $confirm = mysqli_real_escape_string($conn, $confirm);

    // Validate password and confirmation
    if ($password !== $confirm) {
        echo "<script>alert('Password and confirmation do not match');window.location.href='Signup.html';</script>";
        exit();
    }

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Email already registered');window.location.href='Signup.html';</script>";
        exit();
    }

    // Check if username already exists
    $check_username = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check_username) > 0) {
        echo "<script>alert('Username already taken');window.location.href='Signup.html';</script>";
        exit();
    }

    // Insert data into database
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Signup successful! Please login');window.location.href='Login.html';</script>";
    } else {
        echo "Signup failed: " . mysqli_error($conn);
    }
}
?>
