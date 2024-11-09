<?php require('headadmin.php');
ob_start(); ?>
<body>
<div class="wrapper">
<?php require('navbaradmin.php') ?>

<div id="content2">
<?php
// Khởi tạo các biến để lưu thông tin của bài đăng và thông báo lỗi và thành công
$id = "";
$title = "";
$summary_content = "";
$content = "";
$image_url = "";
$selected_category = []; // Khởi tạo như một mảng
$selected_tags = [];
$errorMessage = "";
$successMessage = "";

// Kiểm tra xem có dữ liệu được gửi từ form không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy các giá trị từ form
    $id = $_POST["id"];
    $title = $_POST["title"];
    $summary_content = $_POST["summary_content"];
    $content = $_POST["content"];
    $categories = isset($_POST["categories"]) ? $_POST["categories"] : []; // Lấy các danh mục đã chọn
    $tags = isset($_POST["tags"]) ? $_POST["tags"] : [];

    // Xử lý upload hình ảnh mới nếu có
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = '../uploads/New/';
            $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_url = $newFileName;
            } else {
                $errorMessage = "There was an error moving the uploaded file.";
            }
        } else {
            $errorMessage = "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
        }
    } else {
        $image_url = $_POST["existing_image"];
    }

    // Thực hiện câu lệnh SQL để cập nhật bài đăng
    $sql = "UPDATE posts SET title='$title', summary_content='$summary_content', content='$content', image_url='$image_url' WHERE id='$id'";
    if ($con->query($sql) === TRUE) {
        // Xóa các danh mục cũ của bài đăng
        $deleteCategoriesSql = "DELETE FROM post_categories WHERE post_id=$id";
        $con->query($deleteCategoriesSql);

        // Thêm các danh mục mới vào bảng post_categories
        if (is_array($categories)) {
            foreach ($categories as $category_id) {
                $category_id = intval($category_id);
                $post_category_sql = "INSERT INTO post_categories (post_id, category_id) VALUES ($id, $category_id)";
                $con->query($post_category_sql);
            }
        }

        // Xóa các tag cũ của bài đăng
        $deleteTagsSql = "DELETE FROM post_tags WHERE post_id=$id";
        $con->query($deleteTagsSql);

        // Thêm các tag mới vào bảng post_tags
        if (is_array($tags)) {
            foreach ($tags as $tag_id) {
                $tag_id = intval($tag_id);
                $post_tag_sql = "INSERT INTO post_tags (post_id, tag_id) VALUES ($id, $tag_id)";
                $con->query($post_tag_sql);
            }
        }

        // Chuyển hướng người dùng về trang New.php sau khi cập nhật thành công
        header("Location: New.php?message=update_success");
        exit;
    } else {
        $errorMessage = "Error: " . $sql . "<br>" . $con->error;
    }
} else {
    // Phương thức GET: Hiển thị dữ liệu của bài đăng
    if (!isset($_GET["id"])) {
        header("location:New.php");
        exit;
    }

    $id = $_GET["id"];

    // Truy vấn để lấy thông tin của bài đăng cần chỉnh sửa
    $sql = "SELECT * FROM posts WHERE id='$id'";
    $result = $con->query($sql);

    // Kiểm tra xem bài đăng có tồn tại hay không
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc(); // Lấy thông tin của bài đăng
        $image_url = $row['image_url'];
    } else {
        $errorMessage = "Post not found!";
    }

    // Lấy danh sách các categories đã được chọn cho bài đăng
    $categorySql = "SELECT category_id FROM post_categories WHERE post_id=$id";
    $categoryResult = $con->query($categorySql);
    while ($categoryRow = $categoryResult->fetch_assoc()) {
        $selected_category[] = $categoryRow['category_id']; // Lưu các category_id vào mảng $selected_category
    }

    // Lấy danh sách các tag đã được chọn cho bài đăng
    $tagSql = "SELECT tag_id FROM post_tags WHERE post_id=$id";
    $tagResult = $con->query($tagSql);
    while ($tagRow = $tagResult->fetch_assoc()) {
        $selected_tags[] = $tagRow['tag_id'];
    }
}
ob_end_flush();
?>

<!-- Hiển thị thông báo lỗi nếu có -->
<?php
if (!empty($errorMessage)) {
    echo "
        <div class='alert alert-warning alert-dismissible fade show' role='alert'>
            <strong>$errorMessage</strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";
}
?>

            <h2>Edit Post</h2>
            <form action="edit_post.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Title</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($row['title']) ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Summary Content</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" name="summary_content" rows="2" required><?= htmlspecialchars($row['summary_content']) ?></textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Content</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" name="content" rows="4" required><?= htmlspecialchars($row['content']) ?></textarea>
                    </div>
                </div>
                <!-- Bổ sung phần categories -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Categories</label>
                    <div class="col-sm-6">
                        <?php
                        // Truy vấn để lấy tất cả các danh mục từ bảng categories
                        $categoryQuery = "SELECT * FROM `categories`";
                        $categoryResult = $con->query($categoryQuery);

                        if ($categoryResult->num_rows > 0) {
                            while ($categoryRow = $categoryResult->fetch_assoc()) {
                                $category_id = $categoryRow['id'];
                                $category_name = $categoryRow['name'];
                                // Kiểm tra xem danh mục này đã được chọn cho bài đăng hay chưa
                                $checked = in_array($category_id, $selected_category) ? "checked" : "";
                                echo "<div class='form-check'>
                        <input class='form-check-input' type='checkbox' name='categories[]' value='$category_id' $checked id='category{$category_id}'>
                        <label class='form-check-label' for='category{$category_id}'>$category_name</label>
                      </div>";
                            }
                        } else {
                            echo "No categories available";
                        }
                        ?>
                    </div>
                </div>
                <!-- Kết thúc phần categories -->

                <!-- Bổ sung phần tags -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Tags</label>
                    <div class="col-sm-6">
                        <?php
                        // Truy vấn để lấy tất cả các tag từ bảng tags
                        $tagQuery = "SELECT * FROM `tags`";
                        $tagResult = $con->query($tagQuery);

                        if ($tagResult->num_rows > 0) {
                            while ($tagRow = $tagResult->fetch_assoc()) {
                                $tag_id = $tagRow['id'];
                                $tag_name = $tagRow['name'];
                                // Kiểm tra xem tag này đã được chọn cho bài đăng hay chưa
                                $checked = in_array($tag_id, $selected_tags) ? "checked" : "";
                                echo "<div class='form-check'>
                        <input class='form-check-input' type='checkbox' name='tags[]' value='$tag_id' $checked id='tag{$tag_id}'>
                        <label class='form-check-label' for='tag{$tag_id}'>$tag_name</label>
                      </div>";
                            }
                        } else {
                            echo "No tags available";
                        }
                        ?>
                    </div>
                </div>
                <!-- Kết thúc phần tags -->

                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Existing Image</label>
                    <div class="col-sm-6">
                        <img src="../uploads/New/<?= htmlspecialchars($image_url) ?>" alt="Current Image" style="max-width: 100%; height: auto;">
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($image_url) ?>">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Change Image</label>
                    <div class="col-sm-6">
                        <input type="file" class="form-control" name="image">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="offset-sm-3 col-sm-6 d-grid">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require('footeradmin.php') ?>
