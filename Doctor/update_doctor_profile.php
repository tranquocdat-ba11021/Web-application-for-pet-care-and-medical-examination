<?php
session_start();
require('../connection.php'); // Kết nối đến database của bạn

// Kiểm tra xem bác sĩ đã đăng nhập chưa
if (!isset($_SESSION['doctor_id'])) {
    header('Location: doctorlogin.php'); // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Kiểm tra nếu form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name_doctor'];
    $email = $_POST['email_doctor'];
    $phone = $_POST['phone_doctor'];
    $address = $_POST['address_doctor'];
    $intro = $_POST['intro_doctor'];
    $facebook = $_POST['facebook_link'];
    $instagram = $_POST['instagram_link'];
    $twitter = $_POST['twitter_link'];
    
    // Xử lý upload hình ảnh
    if (isset($_FILES['image_doctor']) && $_FILES['image_doctor']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['image_doctor']['name'];
        $image_tmp = $_FILES['image_doctor']['tmp_name'];
        $image_path = "../uploads/doctor/" . basename($image_name);
        
        // Di chuyển file ảnh tới thư mục lưu trữ
        if (move_uploaded_file($image_tmp, $image_path)) {
            // Cập nhật thông tin bác sĩ bao gồm hình ảnh
            $sql = "UPDATE `doctor` SET 
                        name_doctor = ?, 
                        email_doctor = ?, 
                        phone_doctor = ?, 
                        address_doctor = ?, 
                        intro_doctor = ?, 
                        facebook_link = ?, 
                        instagram_link = ?, 
                        twitter_link = ?, 
                        image_doctor = ? 
                    WHERE id_doctor = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sssssssssi", $name, $email, $phone, $address, $intro, $facebook, $instagram, $twitter, $image_name, $doctor_id);
        }
    } else {
        // Cập nhật thông tin bác sĩ mà không thay đổi hình ảnh
        $sql = "UPDATE `doctor` SET 
                    name_doctor = ?, 
                    email_doctor = ?, 
                    phone_doctor = ?, 
                    address_doctor = ?, 
                    intro_doctor = ?, 
                    facebook_link = ?, 
                    instagram_link = ?, 
                    twitter_link = ? 
                WHERE id_doctor = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssssssi", $name, $email, $phone, $address, $intro, $facebook, $instagram, $twitter, $doctor_id);
    }

    // Thực thi truy vấn và kiểm tra kết quả
    if ($stmt->execute()) {
        $_SESSION['update_success'] = "Profile updated successfully!";
    } else {
        $_SESSION['update_error'] = "Error updating profile. Please try again.";
    }

    // Đóng statement và connection
    $stmt->close();
    $con->close();

    // Redirect về trang profile sau khi cập nhật
    header('Location: doctor_profile.php');
    exit();
} else {
    header('Location: doctor_profile.php'); // Chuyển hướng nếu truy cập file trực tiếp
    exit();
}
