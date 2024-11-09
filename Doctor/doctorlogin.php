<?php
require('../connection.php');
session_start();

// Check if the doctor is already logged in
if (isset($_SESSION['doctor_logged_in'])) {
    header("location:doctor_profile.php");
    exit(); // Ensure no further code is executed after redirection
}

// Handle login form submission
if (isset($_POST['login'])) {
    $email_or_username = mysqli_real_escape_string($con, $_POST['email_username']);
    $password = $_POST['password'];

    // Query to check the doctor table for the given username or email
    $query = "SELECT * FROM `doctor` WHERE `username` = '$email_or_username' OR `email_doctor` = '$email_or_username'";
    $result = mysqli_query($con, $query);

    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $result_fetch = mysqli_fetch_assoc($result);

            if (password_verify($password, $result_fetch['password'])) {
                // If password matches
                $_SESSION['doctor_logged_in'] = true;
                $_SESSION['doctor_username'] = $result_fetch['username'];
                $_SESSION['doctor_id'] = $result_fetch['id_doctor']; // Store doctor ID in session

                // Redirect to doctor dashboard
                echo "
                <script>
                    alert('Login successful!');
                    window.location.href = 'doctor_profile.php'; // Chuyển trang sau khi hiện thông báo
                </script>
                ";
            } else {
                // Incorrect password
                echo "<script>alert('Incorrect password');</script>";
            }
        } else {
            // Username or Email not registered
            echo "<script>alert('Username or Email Not Registered');</script>";
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
    <title>CarePaws - Doctor Login</title>
</head>
<body class="bg-light">
    <section class="login-form" style="padding-top: 100px;">
        <div class="container">
            <div class="row mt-5">
                <div class="col-lg-4 bg-white m-auto rounded-top wrapper">
                    <h2 class="text-center pt-3">Doctor Login</h2>

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
</body>
</html>
