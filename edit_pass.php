<?php
require('head.php');
require('connection.php'); // Kết nối cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_logged_in'])) {
    include('404.php');
    exit; // Đảm bảo dừng luồng xử lý tiếp sau khi chuyển hướng
}

$user_id = $_SESSION['user_id'];
$errorMessage = '';

// Xử lý khi nhấn nút "Save"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate dữ liệu
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $errorMessage = "Please fill in all fields.";
    } elseif ($new_password != $confirm_password) {
        $errorMessage = "New password and confirm password do not match.";
    } else {
        // Kiểm tra mật khẩu hiện tại
        $sql_select = "SELECT password FROM registered_users WHERE id = ?";
        $stmt = $con->prepare($sql_select);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];

            // Verify current password
            if (password_verify($current_password, $stored_password)) {
                // Kiểm tra nếu mật khẩu mới trùng với mật khẩu hiện tại
                if (password_verify($new_password, $stored_password)) {
                    $errorMessage = "New password cannot be the same as the current password.";
                } else {
                    // Hash new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update password in database
                    $sql_update = "UPDATE registered_users SET password = ? WHERE id = ?";
                    $stmt = $con->prepare($sql_update);
                    $stmt->bind_param('si', $hashed_password, $user_id);

                    if ($stmt->execute()) {
                        // Password updated successfully
                        $successMessage = "Password updated successfully.";
                    } else {
                        $errorMessage = "Error updating password: " . $stmt->error;
                    }
                }
            } else {
                $errorMessage = "Current password is incorrect.";
            }
        } else {
            $errorMessage = "User not found.";
        }
    }
}
?>

<div class="main-content">
    <div class="container d-flex">
        <!-- SIDEBAR -->
        <?php require('sidebar.php'); ?>

        <!-- CONTENT -->
        <div class="content ">
        <h3>Change Password</h3>
            <div class="card d2_card mx-3 flex-grow-1">
                <div class="card-body d_card">
                    <form action="edit_pass.php" method="POST" class="mt-4">
                        <?php if (!empty($errorMessage)) { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><?php echo $errorMessage; ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php } elseif (isset($successMessage)) { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong><?php echo $successMessage; ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php } ?>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>
