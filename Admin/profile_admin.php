<?php
require('headadmin.php');

// Kiểm tra xem admin đã đăng nhập chưa
$admin_id = $_SESSION['admin_username'];

// Lấy thông tin của admin từ cơ sở dữ liệu
$sql = "SELECT * FROM `registered_users` WHERE username = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    header('Location: adminlogin.php'); // Nếu không tìm thấy thông tin admin, chuyển hướng đến trang đăng nhập
    exit();
}

// Đóng kết nối cơ sở dữ liệu
$stmt->close();
$con->close();
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php'); ?>

        <div id="content2">
            <div class="container">
                <h2 class="header-title">Admin Profile</h2>

                <!-- Thêm thông báo sau khi cập nhật -->
                <?php if (isset($_SESSION['update_success'])): ?>
                    <div class="alert alert-success">
                        <?php
                        echo $_SESSION['update_success'];
                        unset($_SESSION['update_success']);
                        ?>
                    </div>
                <?php elseif (isset($_SESSION['update_error'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        echo $_SESSION['update_error'];
                        unset($_SESSION['update_error']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <form action="update_admin_profile.php" method="POST" enctype="multipart/form-data" class="profile-info">
                        <div class="text-center">
                            <img src="../uploads/admin/<?php echo htmlspecialchars($admin['image_url']); ?>" alt="Admin Image" class="img-thumbnail">
                        </div>
                        <div class="form-group">
                            <label for="image_url">Profile Image:</label>
                            <input type="file" id="image_url" name="image_url">
                        </div>
                        <div class="form-group">
                            <label for="full_name">Full Name:</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" class="custom-email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone:</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($admin['phone']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require('footeradmin.php'); ?>
</body>