<?php
require("connection.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



function sendMail($email,$reset_token)
{
 require('PHPMailer/PHPMailer.php');
 require('PHPMailer/SMTP.php');
 require('PHPMailer/Exception.php');

 $mail = new PHPMailer(true);

 try {
    //Server settings
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'dattq.ba11-021@st.usth.edu.vn';        //SMTP username
    $mail->Password   = 'gnlqowwwpmcuiutv';                     //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('dattq.ba11-021@st.usth.edu.vn', 'PETCARE');
    $mail->addAddress($email);     //Add a recipient


    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Password Reset Link From PETCARE';
    $mail->Body    = "we got a request from you to reset password
                    <br> Click the link below: <br>
                    <a href='http://code3.test/updatepassword.php?email=$email&reset_token=$reset_token'>
                    Reset Password  
                    </a>
    ";

    $mail->send();
   return true;
} 
catch (Exception $e) {
   return false;
}

}

if(isset($_POST['send-reset-link']))
{
    $query="SELECT * FROM `registered_users` WHERE `email`='$_POST[email]'";
    $result=mysqli_query($con,$query);
    if($result)
    {
        if(mysqli_num_rows($result)==1)
        {
            // email found
            $reset_token=bin2hex(random_bytes(16));
            date_default_timezone_set('Asia/kolkata');
            $date=date("Y-m-d");
            $query="UPDATE `registered_users` SET `resettoken`='$reset_token',`resettokenexpired`='$date' WHERE `email`='$_POST[email]'";
            if(mysqli_query($con,$query) && sendMail($_POST['email'],$reset_token))
            {
                echo"
                <script>
                alert('Password reset Link Sent to Email');
                window.location.href='index.php';
                </script>
                ";
            }
            else
            {
                echo"
                <script>
                alert('Enter your email!');
                </script>
                ";
            }
        }
        else
        {
            echo"
            <script>
            alert('Email Not found');
            window.location.href='index.php';
            </script>
            ";
        }

    }
    else
    {
        echo"
        <script>
        alert('cannot run query');
        window.location.href='index.php';
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

<body>

<section class="login-form" style="padding-top: 100px;">
<body class="bg-light ">
    <div class="container">
        <div class="row mt-5 ">  <!-- cho be cai o lai -->
            <div class="col-lg-4 bg-white m-auto rounded-top wrapper">
                <h2 class="text-center pt-3">Reset Password</h2>

                <!-- form start -->
                <!-- autocomplete de lam gi -->
                <form action="#" class="py-3" method="post" autocomplete="off">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                        <input type="email" name="email" class="form-control" require value="" placeholder="E-mail "> 
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="send-reset-link" class="btn btn-success">Send Reset Link</button>
                        <p class="text-center mt-3">
                           Back to! <a href="Login.php"> Login</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


</body>

</body>

</html>