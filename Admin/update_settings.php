<?php
require('../connection.php');
session_start();

$user_id = $_POST['user_id'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];

// Xử lý ảnh nếu có
$image_url = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $upload_dir = './uploads/';
    $upload_file = $upload_dir . basename($_FILES['image']['name']);
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
        $image_url = $upload_file;
    }
}

// Cập nhật thông tin người dùng vào cơ sở dữ liệu
$sql = "UPDATE registered_users SET full_name = ?, email = ?, phone = ?, image_url = ? WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('ssssi', $full_name, $email, $phone, $image_url, $user_id);

if ($stmt->execute()) {
    header('Location: edit_profile.php?success=1');
} else {
    echo "Error updating record: " . $stmt->error;
}

$stmt->close();
$con->close();
?>
