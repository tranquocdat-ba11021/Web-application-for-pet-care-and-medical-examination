<?php
ob_start(); // Start output buffering
session_start();
require('./connection.php');

// Fetch settings from the database
$sql = "SELECT `key`, `value` FROM `settings` WHERE `key` IN ('navbar_email', 'navbar_phone', 'navbar_location', 'navbar_logo', 'navbar_facebook', 'navbar_instagram', 'navbar_twitter')";
$result = $con->query($sql);

$settings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['key']] = $row['value'];
    }
}

// Set variables for settings
$navbar_email = isset($settings['navbar_email']) ? $settings['navbar_email'] : '';
$navbar_phone = isset($settings['navbar_phone']) ? $settings['navbar_phone'] : '';
$navbar_location = isset($settings['navbar_location']) ? $settings['navbar_location'] : '';
$navbar_logo = isset($settings['navbar_logo']) ? $settings['navbar_logo'] : './image/default_logo.png'; // Default logo if not set
$navbar_facebook = isset($settings['navbar_facebook']) ? $settings['navbar_facebook'] : '#';
$navbar_instagram = isset($settings['navbar_instagram']) ? $settings['navbar_instagram'] : '#';
$navbar_twitter = isset($settings['navbar_twitter']) ? $settings['navbar_twitter'] : '#';
?>
<!DOCTYPE html>
<html lang="en">
<!-- ... -->


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./css/boxicons.min.css">
    <link rel="stylesheet" href="./css/owl.carousel.min.css">
    <link rel="stylesheet" href="./css/owl.theme.default.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="shortcut icon" href="<?php echo $navbar_logo; ?>">
    <link rel="stylesheet" href="./css/blog.css">
    <link rel="stylesheet" href="./css/New.css">
    <link rel="stylesheet" href="./css/sdichvu.css">
    <link rel="stylesheet" href="./css/profile.css">
    <link rel="stylesheet" href="./css/rate.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>CarePaws</title>
</head>


<!-- MAIN NAVBAR -->
<nav class="top-nav">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-auto">
                <p>
                    <i class='bx bx-envelope'></i>
                    <span><?php echo $navbar_email; ?></span>
                </p>
                <p>
                    <i class='bx bx-phone-call'></i>
                    <span><?php echo $navbar_phone; ?></span>
                </p>
                <p>
                    <i class='bx bx-location-plus'></i>
                    <span><?php echo $navbar_location; ?></span>
                </p>
            </div>
            <div class="col-auto d-flex align-items-end">
                <div class="custom-social-links">
                    <a href="<?php echo $navbar_facebook; ?>"><i class='bx bxl-facebook'></i></a>
                    <a href="<?php echo $navbar_instagram; ?>"><i class='bx bxl-instagram'></i></a>
                    <a href="<?php echo $navbar_twitter; ?>"><i class='bx bxl-twitter'></i></a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- NAVBAR 2 -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand me-auto" href="index.php"><img src="<?php echo $navbar_logo; ?>" alt="logo" width="60px" height="60px">CarePaws.com</a>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Offcanvas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" href="about.php">About us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" href="service.php">Service</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" href="News.php">News</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-lg-2" href="contact.php">Contact</a>
                    </li>
                </ul>
            </div>
        </div>

        <?php
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] == true) {
        ?>
            <div class="dropdown">
                <a href="#" class="login-button dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php echo $_SESSION['user_username']; ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="edit_profile.php">Profile</a>
                    <a class="dropdown-item" href="appointment_history.php">History</a>
                    <a class="dropdown-item" href="add_pets.php">Add Pet</a>

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php">Logout</a>
                </div>
            </div>

        <?php } else { ?>
            <a type='button' href='login.php' class='login-button' name='login'>LOGIN</a>
            <a type='button' href='register.php' class='login-button' name='register'>REGISTER</a>
        <?php } ?>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>