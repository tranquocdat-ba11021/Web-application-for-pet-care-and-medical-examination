<?php require('headadmin.php');

ob_start(); ?>
<body>
<div class="wrapper">
<?php require('navbaradmin.php') ?>

<div id="content2">

<?php
// Khởi tạo các biến để lưu thông tin thẻ và thông báo lỗi và thành công
$tag_name = "";
$errorMessage = "";
$successMessage = "";

// Kiểm tra xem có dữ liệu được gửi từ form không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy giá trị từ form
    $tag_name = $_POST["tag_name"];

    // Kiểm tra các trường thông tin có bị bỏ trống không
    if (empty($tag_name)) {
        $errorMessage = "Tag name is required";
    } else {
        // Thêm thẻ mới vào cơ sở dữ liệu
        $sql = "INSERT INTO tags (name) VALUES ('$tag_name')";
        $result = $con->query($sql);

        // Kiểm tra kết quả truy vấn
        if ($result) {
            // Redirect to manage_categories.php with success message
            header("Location: manage_tag.php?message=add_success");
            exit(); // Ensure no further code is executed after the redirect
        } else {
            $errorMessage = "Invalid query: " . $con->error;
        }
    }
}
ob_end_flush();

?>

<!-- Hiển thị thông báo lỗi nếu có -->
<?php if (!empty($errorMessage)): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><?php echo $errorMessage; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Hiển thị thông báo thành công nếu có -->
<?php if (!empty($successMessage)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong><?php echo $successMessage; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>


            <h2>Add New Tag</h2>
            <form method="POST">
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Tag Name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="tag_name" value="<?php echo htmlspecialchars($tag_name); ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="offset-sm-3 col-sm-3 d-grid">
                        <button type="submit" class="btn btn-primary">Add Tag</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require('footeradmin.php'); ?>
