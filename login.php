<?php
require('connection.php');
session_start();

// Xóa các session liên quan đến admin khi đăng nhập user
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']);
unset($_SESSION['user_id']);

#for login
if (isset($_POST['login'])) {
    $query = "SELECT * FROM `registered_users` WHERE `email` = '$_POST[email_username]' OR `username` = '$_POST[email_username]'";
    $result = mysqli_query($con, $query);

    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $result_fetch = mysqli_fetch_assoc($result);
            if ($result_fetch['is_verified'] == 1) {
                if (password_verify($_POST['password'], $result_fetch['password'])) {
                    #if password match
                    $_SESSION['user_logged_in'] = true;  // Đổi biến session
                    $_SESSION['user_username'] = $result_fetch['username'];  // Đổi biến session
                    $_SESSION['user_id'] = $result_fetch['id'];
        
                    if ($result_fetch['role'] == 1) {
                        // Thay header() bằng script JS để hiện thông báo và chuyển hướng
                        echo "
                        <script>
                            alert('Login successful!');
                            window.location.href = 'index.php'; // Chuyển trang sau khi hiện thông báo
                        </script>
                        ";
                        exit;
                    } else {
                        // Nếu vai trò không phải là 1, thông báo lỗi
                        echo "
                        <script>
                            alert('You Are Not User');
                        </script>
                        ";
                    }
                } else {
                    #if incorrect password
                    echo "
                    <script>
                        alert('Incorrect password');
                    </script>
                    ";
                }
            } else {
                echo "
                <script>
                    alert('Email or Username Not Verified');
                </script>
                ";
            }
        } else {
            echo "
            <script>
                alert('Email or Username Not Registered');
            </script>
            ";
        }
    } else {
        echo "
        <script>
            alert('Cannot Run Query');
        </script>
        ";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/boxicons.min.css">
    <link rel="stylesheet" href="./css/owl.carousel.min.css">
    <link rel="stylesheet" href="./css/owl.theme.default.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="shortcut icon" href="./image/h3.png">
    <title>CarePaws</title>

</head>

<section class="login-form" style="padding-top: 100px;">
<body class="bg-light ">
    <div class="container">
        <div class="row mt-5 ">  <!-- cho be cai o lai -->
            <div class="col-lg-4 bg-white m-auto rounded-top wrapper">
                <h2 class="text-center pt-3">Login Now</h2>

                <!-- form start -->
                <!-- autocomplete de lam gi -->
                <form action="#" class="py-3" method="post" autocomplete="off">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                        <input type="text" name="email_username" class="form-control" require value="" placeholder="E-mail or user name"> 
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                        <input type="password" name="password" class="form-control" require value="" placeholder="Password"> 
                    </div>
                    <p><a href="forgotpassword.php" style="margin-bottom: 15px; display: block; text-align: right;">Forgot Password?</a></p>

                    <div class="d-grid">
                        <button type="submit" name="login" class="btn btn-success">Signup Now</button>
                        <p class="text-center mt-3">
                           Register Now for Free by clicking <a href="register.php"> Register Now</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

</body>

</html>