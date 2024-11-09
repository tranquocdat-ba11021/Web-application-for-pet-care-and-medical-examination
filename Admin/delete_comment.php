<?php
require('headadmin.php');

// Kiểm tra xem ID của bình luận có được truyền qua URL hay không
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Comment ID");
}

$comment_id = intval($_GET['id']);

// Xóa bình luận khỏi cơ sở dữ liệu
$sql = "DELETE FROM comments WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $comment_id);

if ($stmt->execute()) {
    // Nếu xóa thành công, chuyển hướng người dùng về trang quản lý bình luận với thông báo thành công
    header("Location: manage_comment.php?message=delete_success");
    exit();
} else {
    // Nếu có lỗi, hiển thị thông báo lỗi
    die("Error deleting comment: " . $stmt->error);
}

$stmt->close();
$con->close();
?>
