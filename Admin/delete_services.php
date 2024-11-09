<?php
require('headadmin.php');

if (isset($_GET['id'])) {
    $id_service = $_GET['id'];

    // Tạm thời vô hiệu hóa kiểm tra khóa ngoại
    $con->query('SET FOREIGN_KEY_CHECKS=0');

    // Tiến hành xóa dịch vụ
    $sql = "DELETE FROM services WHERE id_service = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $id_service);

    if ($stmt->execute()) {
        // Xóa thành công, chuyển hướng về trang quản lý dịch vụ với thông báo thành công
        header("Location: manage_services.php?message=delete_success");
        exit;
    } else {
        echo "Error deleting service: " . $stmt->error;
    }

    // Kích hoạt lại kiểm tra khóa ngoại
    $con->query('SET FOREIGN_KEY_CHECKS=1');

    // Đóng statement
    $stmt->close();
} else {
    // Nếu không có ID dịch vụ, chuyển hướng về trang quản lý dịch vụ
    header("Location: manage_services.php");
}

// Đóng kết nối
$con->close();
?>
