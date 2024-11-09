<?php
// Kết nối đến cơ sở dữ liệu
require('../connection.php');

// Kiểm tra nếu có tham số id được gửi đến từ URL
if (isset($_GET['id'])) {
    // Lấy id của vật nuôi từ tham số id
    $id = $_GET['id'];

    // Xây dựng câu truy vấn DELETE
    $sql = "DELETE FROM user_pets WHERE id = $id";

    // Thực hiện truy vấn DELETE
    if ($con->query($sql) === TRUE) {
        // Nếu xóa thành công, chuyển hướng người dùng về trang danh sách pets
        header("Location: pets.php?message=delete_success");
        exit;
    } else {
        // Nếu có lỗi trong quá trình xóa, hiển thị thông báo lỗi
        echo "Error deleting record: " . $con->error;
    }
} else {
    // Nếu không có tham số id, hiển thị thông báo lỗi
    echo "Invalid request. Please provide a valid pet ID.";
}

// Đóng kết nối đến cơ sở dữ liệu
$con->close();
?>
