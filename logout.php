<?php
session_start();
// Hủy bỏ và xóa bỏ tất cả các session của người dùng
unset($_SESSION['user_logged_in']);
unset($_SESSION['user_username']); // Xóa session của user username
unset($_SESSION['user_id']); // Xóa session của user ID
// session_destroy(); // Không cần thiết phải hủy toàn bộ session
header("location:index.php");
exit;
?>
