<?php
session_start();
// Hủy bỏ và xóa bỏ tất cả các session của admin
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']); // Xóa session của admin username
// session_destroy(); // Không cần thiết phải hủy toàn bộ session
header("location:../Admin/adminlogin.php");
exit;
?>
