<?php
require('connection.php');
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($email, $v_code)
{
    require("PHPMailer/PHPMailer.php");
    require("PHPMailer/SMTP.php");
    require("PHPMailer/Exception.php");

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'dattq.ba11-021@st.usth.edu.vn';        //SMTP username
        $mail->Password   = 'lufmmgpbeaovdvlt';                     //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        $mail->setFrom('dattq.ba11-021@st.usth.edu.vn', 'PETCARE');
        $mail->addAddress($email);

        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Email Verification from PETCARE';
        $mail->Body    = "Thank for Registration! 
        Click  the link  below to verify the email adderess
        <a href='http://code3.test/verify.php?email=$email&v_code=$v_code'>Verify</a>";  //tên miền 

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}




#for registration
// Kiểm tra nếu biểu mẫu đăng ký đã được gửi đi
if (isset($_POST['register'])) {
    // Kiểm tra xem tất cả các trường đã được điền vào không
    if (empty($_POST['fullname']) || empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['phone'])) {
        echo "
            <script>
                alert('Please fill in all fields.');
            </script>
        ";
    } else {
        // Nếu tất cả các trường đều đã được điền, tiến hành xử lý dữ liệu
        $user_exist_query = "SELECT * FROM `registered_users` WHERE `username` = '$_POST[username]' OR `email` = '$_POST[email]' OR `phone` = '$_POST[phone]'";
        $result = mysqli_query($con, $user_exist_query);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $result_fetch = mysqli_fetch_assoc($result);
                if ($result_fetch['username'] == $_POST['username']) {
                    echo "
                        <script>
                            alert('$result_fetch[username] - Username already taken');
                        </script>
                    ";
                } elseif ($result_fetch['email'] == $_POST['email']) {
                    echo "
                        <script>
                            alert('Email already registered');
                        </script>
                    ";
                } elseif ($result_fetch['phone'] == $_POST['phone']) {
                    echo "
                        <script>
                            alert('Phone number already registered');
                        </script>
                    ";
                }
            } else {
                // Dữ liệu hợp lệ, thực hiện đăng ký và gửi email xác nhận
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $v_code = bin2hex(random_bytes(16));
                $query = "INSERT INTO `registered_users`(`full_name`, `username`, `email`, `password`, `phone`, `verification_code`, `is_verified`) 
                VALUES ('$_POST[fullname]','$_POST[username]','$_POST[email]','$password','$_POST[phone]','$v_code','0')";
      
      
                if (mysqli_query($con, $query) && sendMail($_POST['email'], $v_code)) {
                    echo "
                        <script>
                            alert('Registration successful');
                            window.location.href='Login.php';
                        </script>
                    ";
                } else {
                    echo "
                        <script>
                            alert('Server Down');
                        </script>
                    ";
                }
            }
        } else {
            echo "
                <script>
                    alert('Cannot run query');
                </script>
            ";
        }
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

<body class="bg-light">
    <div class="container">
        <div class="row mt-5"> <!-- cho be cai o lai -->
            <div class="col-lg-4 bg-white m-auto rounded-top wrapper">
                <h2 class="text-center pt-3" name="register">Signup Now</h2>
                <p class="text-center text-muted lead ">It free and take a minute</p>
                <!-- form start -->
                <form action="register.php" class="py-3" method="post" autocomplete="off">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class='bx bx-user'></i></span>
                        <input type="text" name="fullname" class="form-control" placeholder="Full Name">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class='bx bx-user'></i></span>
                        <input type="text" name="username" class="form-control" placeholder="User Name">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                        <input type="email" name="email" class="form-control" placeholder="E-mail">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class='bx bx-phone'></i></span>
                        <input type="tel" name="phone" class="form-control" placeholder="Phone Number">
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                    <!-- <div class="input-group mb-3">
                        <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                        <input type="password" name="confirmpassword" class="form-control"  placeholder="Confirmpassword">
                    </div> -->

                    <div class="d-grid">
                        <button type="submit" name="register" class="btn btn-success">Signup Now</button>
                        <p class="text-center text-muted mt-2">
                            When you Register by Clicking Signup Button, You Agree to our
                            <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>
                        </p>
                        <p class="text-center">
                            Already Have an Account ? <a href="Login.php"> Login Here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>