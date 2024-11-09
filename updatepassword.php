<?php
require("head.php");
if(isset($_GET['email']) &&  isset($_GET['reset_token']))
{
    date_default_timezone_set('Asia/kolkata');
    $date=date("Y-m-d");
    $query="SELECT * from `registered_users` WHERE `email`='$_GET[email]' AND `resettoken`='$_GET[reset_token]' AND `resettokenexpired`='$date' ";
    $result=mysqli_query($con,$query);
    if($result)
    {
        if(mysqli_num_rows($result)==1)
        {
            echo"
            <section class=login-form' style=padding-top: 100px;'>
            <body class='bg-light'>
                <div class='container'>
                    <div class='row mt-5 '>  <!-- cho be cai o lai -->
                        <div class='col-lg-4 bg-white m-auto rounded-top wrapper'>
                            <h2 class='text-center pt-3'>Reset Password</h2>

                            <form action='' class='py-3' method='post' autocomplete='off'>
                                <div class='input-group mb-3'>
                                    <span class='input-group-text'><i class='bx bx-lock-alt'></i></span>
                                    <input type='password' name='Password' class='form-control' placeholder=' New Password'> 
                                </div>            
                                <div class='d-grid'>
                                    <button type='submit' name='updatepassword' class='btn btn-success'>UPDATE</button>
                                    <p class='text-center mt-3'>
                                        Back to!<a href='Login.php'>Login</a>
                                    </p>
                                </div>
                                <div>
                                    <input type='hidden' name='email' value='$_GET[email]'
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                </body>
            </section>
            
            ";
        }
        else
        {
            echo"
            <script>
                alert('Invalid or Expired Link');
                window.location.href='index.php';
            </script>
            ";
        }
    }
    else
    {
        echo"
        <script>
        alert('Sever Down! try again later');
        window.location.href='index.php';
        </script>
        ";
    }
}



?>

<?php

if(isset($_POST['updatepassword']))
{
    $pass=password_hash($_POST['Password'],PASSWORD_BCRYPT);
    $update="UPDATE `registered_users` SET `password`='$pass',`resettoken`=NULL,`resettokenexpired`=NULL WHERE `email`='$_POST[email]' ";
    if(mysqli_query($con,$update))
    {
        echo"
        <script>
        alert('Password Update Successfully');
        window.location.href='index.php';
        </script>
        ";
    }
    else
    {
        echo"
        <script>
        alert('Sever Down! try again later');
        window.location.href='index.php';
        </script>
        ";
    }
}


?>

</body>
</html>