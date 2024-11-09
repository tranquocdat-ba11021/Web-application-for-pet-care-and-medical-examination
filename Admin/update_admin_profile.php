<?php
require('headadmin.php');

// Kiểm tra xem admin đã đăng nhập chưa
if (!isset($_SESSION['admin_username'])) {
    header('Location: adminlogin.php'); // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    exit();
}

$admin_id = $_SESSION['admin_username'];

// Kết nối cơ sở dữ liệu

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $new_password = $_POST['new_password']; // Lấy mật khẩu mới từ form (nếu có)

    // Xử lý ảnh đại diện
    $image_url = $admin['image_url']; // Giữ lại ảnh cũ nếu không có ảnh mới được tải lên

    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK) {
        $image_name = basename($_FILES['image_url']['name']);
        $target_path = "../uploads/admin/" . $image_name;
        
        // Kiểm tra loại tệp tin và kích thước
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions) && $_FILES['image_url']['size'] <= 5 * 1024 * 1024) {
            // Xóa ảnh cũ nếu có
            if ($admin['image_url'] && file_exists("../uploads/admin/" . $admin['image_url'])) {
                unlink("../uploads/admin/" . $admin['image_url']);
            }
            
            // Di chuyển ảnh mới
            if (move_uploaded_file($_FILES['image_url']['tmp_name'], $target_path)) {
                $image_url = $image_name;
            } else {
                $_SESSION['update_error'] = 'Failed to upload image.';
                header('Location: profile_admin.php');
                exit();
            }
        } else {
            $_SESSION['update_error'] = 'Invalid image file. Allowed formats: jpg, jpeg, png. Max size: 5MB.';
            header('Location: profile_admin.php');
            exit();
        }
    }

    // Nếu có nhập mật khẩu mới, cập nhật mật khẩu đã mã hóa
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $sql = "UPDATE `registered_users` SET full_name = ?, email = ?, username = ?, phone = ?, image_url = ?, password = ? WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssssss", $full_name, $email, $username, $phone, $image_url, $hashed_password, $admin_id);
    } else {
        // Nếu không có mật khẩu mới, chỉ cập nhật thông tin khác
        $sql = "UPDATE `registered_users` SET full_name = ?, email = ?, username = ?, phone = ?, image_url = ? WHERE username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssss", $full_name, $email, $username, $phone, $image_url, $admin_id);
    }

    // Thực thi câu lệnh và xử lý kết quả
    if ($stmt->execute()) {
        $_SESSION['update_success'] = 'Profile updated successfully!';
    } else {
        $_SESSION['update_error'] = 'Failed to update profile.';
    }

    // Đóng kết nối cơ sở dữ liệu
    $stmt->close();
    $con->close();

    // Chuyển hướng về trang profile
    header('Location: profile_admin.php');
    exit();
}
?>
