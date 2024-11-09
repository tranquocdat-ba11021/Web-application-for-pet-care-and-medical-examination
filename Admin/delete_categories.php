<?php
require('../connection.php'); // Kết nối đến cơ sở dữ liệu

// Kiểm tra xem có tham số id được gửi từ URL không
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Xử lý delete category
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $id);

    // Thực thi truy vấn xóa
    if ($stmt->execute()) {
        // Redirect về trang quản lý categories với thông báo thành công
        header("Location: manage_categories.php?message=delete_success");
        exit;
    } else {
        // Nếu xóa không thành công, hiển thị thông báo lỗi
        echo "Error deleting record: " . $stmt->error;
    }

    // Đóng kết nối đến cơ sở dữ liệu
    $stmt->close();
    $con->close();
} else {
    // Nếu không có tham số id, chuyển hướng người dùng đến trang quản lý categories
    header("Location: manage_posts.php");
    exit;
}
?>

