<?php require('../connection.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-7d2w7dZT5wOj1BZ6ytub7PErhpcL+B3Tkf0v5Bjf5OVmgfzdo7IujFj12Xr/2qgG" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../css/boxicons.min.css">
  <link rel="stylesheet" href="../css/owl.carousel.min.css">
  <link rel="stylesheet" href="../css/owl.theme.default.min.css">
  <link rel="stylesheet" href="../css/doctor.css">
  <link rel="stylesheet" href="../css/styleadmin.css">
  <link rel="stylesheet" href="../css/schedule.css">
  <link rel="shortcut icon" href="../image/h3.png">
  <script src="../js/popper.min.js" ></script>


  <title>CarePaws</title>

</head>
<?php

session_start();

if (!isset($_SESSION['doctor_logged_in'])) {
  header("doctor_profile.php");
}


?>



