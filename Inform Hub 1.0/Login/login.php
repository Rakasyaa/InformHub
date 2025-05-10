<?php
session_start();
include '../database/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sanitize inputs to prevent SQL injection
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Check if email exists
    $sql_email = "SELECT * FROM users WHERE email = '$email'";
    $result_email = mysqli_query($conn, $sql_email);

    if (mysqli_num_rows($result_email) == 1) {
        // Email found, check password
        $user = mysqli_fetch_assoc($result_email);

        if ($user['password'] === $password) {
            // Password matches → login successful
            $_SESSION['email'] = $email;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to home page
            header("Location: ../index.php");
            exit();
        } else {
            // Wrong password
            echo "<script>alert('Login failed: Wrong password');window.location.href='Login.html';</script>";
        }
    } else {
        // Email not found
        echo "<script>alert('Login failed: Email not found');window.location.href='Login.html';</script>";
    }
}
?>
