<?php
require('headadmin.php');
if (isset($_SESSION['role']) && $_SESSION['role'] == 2) {
    header('Location: restricted.php'); // Redirect to a restricted access page or a different page
    exit();
}
ob_start();
?>
<body>
<div class="wrapper">
    <?php require('navbaradmin.php') ?>

    <div id="content2">

    <?php
    // Khởi tạo biến lưu thông tin người dùng
    $user_id = $_GET['id']; // Lấy ID của người dùng từ URL

    // Truy vấn cơ sở dữ liệu để lấy thông tin của người dùng
    $stmt = $con->prepare("SELECT * FROM `registered_users` WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("User not found.");
    }

    // Lấy dữ liệu người dùng từ kết quả truy vấn
    $row = $result->fetch_assoc();
    $full_name = $row['full_name'];
    $email = $row['email'];
    $username = $row['username'];
    $phone = $row['phone'];
    $image_url = $row['image_url'];
    $role = $row['role']; // Thêm biến role

    ob_end_flush();
    ?>

    <h2>View User</h2>

    <!-- Hiển thị thông tin người dùng -->
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Full Name</label>
        <div class="col-sm-6">
            <p class="form-control-plaintext"><?php echo htmlspecialchars($full_name); ?></p>
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Email</label>
        <div class="col-sm-6">
            <p class="form-control-plaintext"><?php echo htmlspecialchars($email); ?></p>
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Username</label>
        <div class="col-sm-6">
            <p class="form-control-plaintext"><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Phone</label>
        <div class="col-sm-6">
            <p class="form-control-plaintext"><?php echo htmlspecialchars($phone); ?></p>
        </div>
    </div>

    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Image</label>
        <div class="col-sm-6">
            <img src="../uploads/user/<?php echo htmlspecialchars($image_url); ?>" class="img-thumbnail" style="width: 100px; height: 100px;" alt="User Image">
        </div>
    </div>

    </div>
</div>

<?php require('footeradmin.php') ?>
