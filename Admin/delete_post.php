<?php
// Kết nối đến cơ sở dữ liệu
require('../connection.php');

// Kiểm tra nếu có tham số id được gửi đến từ URL
if (isset($_GET['id'])) {
    // Lấy id của bài viết từ tham số id và kiểm tra xem nó có phải là số hợp lệ không
    $id = $_GET['id'];
    if (is_numeric($id)) {
        // Xây dựng câu truy vấn DELETE
        $sql = "DELETE FROM posts WHERE id = $id";

        // Thực hiện truy vấn DELETE
        if ($con->query($sql) === TRUE) {
            // Nếu xóa thành công, chuyển hướng người dùng về trang danh sách bài viết
            header("Location: New.php?message=delete_success");
            exit;
        } else {
            // Nếu có lỗi trong quá trình xóa, hiển thị thông báo lỗi
            echo "Error deleting record: " . $con->error;
        }
    } else {
        // Nếu id không phải là số hợp lệ, hiển thị thông báo lỗi
        echo "Invalid ID. Please provide a valid post ID.";
    }
} else {
    // Nếu không có tham số id, hiển thị thông báo lỗi
    echo "Invalid request. Please provide a valid post ID.";
}

// Đóng kết nối đến cơ sở dữ liệu
$con->close();
?>
