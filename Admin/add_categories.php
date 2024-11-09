<?php require('headadmin.php');
ob_start(); ?>
<body>
<div class="wrapper">
<?php require('navbaradmin.php') ?>

<div id="content2">

<?php
// Khởi tạo các biến để lưu thông tin danh mục và thông báo lỗi và thành công
$category_name = "";
$errorMessage = "";
$successMessage = "";

// Kiểm tra xem có dữ liệu được gửi từ form không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy giá trị từ form
    $category_name = $_POST["category_name"];

    // Kiểm tra các trường thông tin có bị bỏ trống không
    if (empty($category_name)) {
        $errorMessage = "Category name is required";
    } else {
        // Thêm danh mục mới vào cơ sở dữ liệu
        $sql = "INSERT INTO categories (name) VALUES ('$category_name')";
        $result = $con->query($sql);

        // Kiểm tra kết quả truy vấn
        if ($result) {
            // Redirect to manage_categories.php with success message
            header("Location: manage_categories.php?message=add_success");
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


            <h2>Add New Category</h2>
            <form method="POST">
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Category Name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="category_name" value="<?php echo htmlspecialchars($category_name); ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="offset-sm-3 col-sm-3 d-grid">
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require('footeradmin.php'); ?>
