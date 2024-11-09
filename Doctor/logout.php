<?php
session_start();
// Hủy bỏ và xóa bỏ tất cả các session của doctor
unset($_SESSION['doctor_logged_in']);
unset($_SESSION['doctor_username']); // Xóa session của doctor username
// session_destroy(); // Không cần thiết phải hủy toàn bộ session
header('Location: doctorlogin.php');
exit();
?>
