<?php
require('../connection.php');
session_start();

// Check if the user is already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header("location:dashboard.php");
    exit(); // Ensure no further code is executed after redirection
}

// Handle login form submission
if (isset($_POST['login'])) {
    $email_or_username = mysqli_real_escape_string($con, $_POST['email_username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM `registered_users` WHERE `email` = '$email_or_username' OR `username` = '$email_or_username'";
    $result = mysqli_query($con, $query);

    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $result_fetch = mysqli_fetch_assoc($result);

            if ($result_fetch['is_verified'] == 1) {
                if (password_verify($password, $result_fetch['password'])) {
                    // If password matches
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $result_fetch['username'];
                    $_SESSION['role'] = $result_fetch['role']; // Store user role in session

                    // Redirect based on user role
                    if ($result_fetch['role'] == 0) {
                        echo "
                        <script>
                            alert('Login successful!');
                            window.location.href = 'dashboard.php'; // Chuyển trang sau khi hiện thông báo
                        </script>
                        ";
                        exit;
                    } else {
                        // Role is not 0, display alert or redirect as needed
                        echo "<script>alert('You Are Not Authorized');</script>";
                    }
                } else {
                    // Incorrect password
                    echo "<script>alert('Incorrect password');</script>";
                }
            } else {
                // User is not verified
                echo "<script>alert('Your account is not verified');</script>";
            }
        } else {
            // Email or Username not registered
            echo "<script>alert('Email or Username Not Registered');</script>";
        }
    } else {
        // Query failed
        echo "<script>alert('Database query failed');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/boxicons.min.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../css/styleadmin.css">
    <link rel="shortcut icon" href="../image/h3.png">
    <title>CarePaws</title>
</head>
<body class="bg-light">
    <section class="login-form" style="padding-top: 100px;">
        <div class="container">
            <div class="row mt-5">
                <div class="col-lg-4 bg-white m-auto rounded-top wrapper">
                    <h2 class="text-center pt-3">Admin Login</h2>

                    <!-- Login form -->
                    <form action="#" class="py-3" method="post" autocomplete="off">
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                            <input type="text" name="email_username" class="form-control" required placeholder="E-mail or Username"> 
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                            <input type="password" name="password" class="form-control" required placeholder="Password"> 
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-success">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php require('footeradmin.php') ?>
