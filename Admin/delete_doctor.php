<?php
// Import connection.php để kết nối cơ sở dữ liệu
require('../connection.php');

if (isset($_GET['id'])) {
    $id_doctor = $_GET['id'];

    // Tạm thời vô hiệu hóa kiểm tra khóa ngoại
    $con->query('SET FOREIGN_KEY_CHECKS=0');

    // Truy vấn xóa bác sĩ từ bảng doctor
    $deleteDoctorSql = "DELETE FROM doctor WHERE id_doctor = ?";
    $stmtDoctor = $con->prepare($deleteDoctorSql);
    $stmtDoctor->bind_param('i', $id_doctor);

    if ($stmtDoctor->execute()) {
        // Xóa thành công, chuyển hướng đến trang quản lý với thông báo thành công
        header("Location: doctor.php?message=delete_success");
        exit;
    } else {
        echo "Error: " . $stmtDoctor->error;
    }

    // Kích hoạt lại kiểm tra khóa ngoại
    $con->query('SET FOREIGN_KEY_CHECKS=1');

    $stmtDoctor->close();
} else {
    // Nếu không có ID bác sĩ, chuyển hướng về trang quản lý bác sĩ
    header("Location: manage_doctors.php");
    exit;
}

$con->close();
?>