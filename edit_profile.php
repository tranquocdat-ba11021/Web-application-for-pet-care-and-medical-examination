<?php
require('head.php');

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_logged_in'])) {
    include('404.php');
    exit; // Đảm bảo dừng luồng xử lý tiếp sau khi chuyển hướng
}

$user_id = $_SESSION['user_id'];
$username = '';
$email = '';
$phone = '';
$image_url = '';
$updateSuccess = false; // Biến để kiểm tra trạng thái cập nhật


// Lấy thông tin người dùng từ cơ sở dữ liệu
$sql_select = "SELECT username, email, phone, image_url FROM registered_users WHERE id = $user_id";
$result = $con->query($sql_select);

if ($result->num_rows > 0) {
    // Lấy thông tin người dùng
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $email = $row['email'];
    $phone = $row['phone'];
    $image_url = $row['image_url'];
} else {
    // Người dùng không tồn tại
    include('404.php');
    exit;
}

// Xử lý khi nhấn nút "Save"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Validate dữ liệu (ở đây bạn có thể thêm các kiểm tra hợp lệ)
    $errorMessage = '';

    // Xử lý upload ảnh nếu có thay đổi
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "./uploads/user/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra kiểu file
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check !== false) {
            // Kiểm tra nếu file đã tồn tại
            if (file_exists($target_file)) {
                $errorMessage = "Sorry, file already exists.";
            } else {
                // Kiểm tra kích thước file
                if ($_FILES["profile_image"]["size"] > 800000) { // Max size 800k
                    $errorMessage = "Sorry, your file is too large.";
                } else {
                    // Cho phép các định dạng file cụ thể
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                        $errorMessage = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    } else {
                        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                            $image_url = basename($_FILES["profile_image"]["name"]); // Lưu tên file vào cơ sở dữ liệu
                        } else {
                            $errorMessage = "Sorry, there was an error uploading your file.";
                        }
                    }
                }
            }
        } else {
            $errorMessage = "File is not an image.";
        }
    }

    // Nếu không có lỗi, tiến hành cập nhật dữ liệu vào cơ sở dữ liệu
    if (empty($errorMessage)) {
        $sql_update = "UPDATE registered_users SET username = ?, email = ?, phone = ?, image_url = ? WHERE id = ?";
        $stmt = $con->prepare($sql_update);
        $stmt->bind_param('ssssi', $username, $email, $phone, $image_url, $user_id);

        if ($stmt->execute() === TRUE) {
            // Cập nhật thành công
            $updateSuccess = true; // Đặt trạng thái cập nhật thành công
        } else {
            $errorMessage = "Lỗi: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<div class="main-content">
    <div class="container d-flex">
        <!-- SIDEBAR -->
<?php require('sidebar.php')?>
        <!-- CONTENT -->
        <div class="content">
        <h3>Profile</h3>
            <div class="card d2_card mx-3 flex-grow-1">
                <div class="card-body d_card ">
                    <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="mt-4">
                        <div class="row mb-4">
                            <label for="profile_image" class="col-sm-2 col-form-label">Avatar</label>
                            <div class="col-sm-10 d-flex align-items-center">
                                <div class="position-relative file-input">
                                    <?php if (!empty($image_url)) { ?>
                                        <img src="./uploads/user/<?= htmlspecialchars($image_url) ?>" alt="Profile Image" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                    <?php } else { ?>
                                        <img src="default_profile.png" alt="Default Profile Image" class="img-thumbnail rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php } ?>
                                </div>
                                <div class="ms-3">
                                    <span id="add-photo" class="text-primary" style="cursor: pointer;" onclick="document.getElementById('profile_image').click();">+ Add Photo</span><br>
                                    <small>JPG, GIF, or PNG, Max size of 800k</small>
                                </div>
                                <input type="file" class="form-control" id="profile_image" name="profile_image" style="display: none;" onchange="previewImage(this);">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="username" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="phone" class="col-sm-2 col-form-label">Phone</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                    <?php
                    // Hiển thị thông báo nếu cập nhật thành công
                    if ($updateSuccess) {
                        echo "
                            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                                <strong>Change profile successful!</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>
                        ";
                    }
                    // Hiển thị thông báo lỗi nếu có
                    if (!empty($errorMessage)) {
                        echo "
                            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                <strong>$errorMessage</strong>
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>
                        ";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>