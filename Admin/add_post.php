<?php require('headadmin.php');

ob_start(); ?>
<body>
<div class="wrapper">
<?php require('navbaradmin.php') ?>

<div id="content2">
<?php
// Khởi tạo các biến để lưu thông tin của bài viết và thông báo lỗi và thành công
$title = "";
$summary_content = "";
$content = "";
$date = date('Y-m-d');
$image_url = "";
$category_ids = [];
$tags = [];
$errorMessage = "";
$successMessage = "";

// Truy vấn cơ sở dữ liệu để lấy danh sách danh mục và thẻ
$categories = [];
$tags_list = [];

$categories_sql = "SELECT * FROM categories";
$categories_result = $con->query($categories_sql);
if ($categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

$tags_sql = "SELECT * FROM tags";
$tags_result = $con->query($tags_sql);
if ($tags_result->num_rows > 0) {
    while ($row = $tags_result->fetch_assoc()) {
        $tags_list[] = $row;
    }
}

// Kiểm tra xem có dữ liệu được gửi từ form không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy các giá trị từ form
    $title = $_POST["title"];
    $summary_content = $_POST["summary_content"];
    $content = $_POST["content"];
    $category_ids = isset($_POST["category_ids"]) ? $_POST["category_ids"] : [];
    $tags = isset($_POST["tags"]) ? $_POST["tags"] : [];

    // Xử lý upload hình ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Lấy thông tin về file được upload
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        // Tách phần mở rộng của tên file
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Danh sách các phần mở rộng file được phép upload
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        // Kiểm tra xem phần mở rộng của file có trong danh sách được phép không
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Thư mục để lưu file đã upload
            $uploadFileDir = '../uploads/New/';
            // Tạo tên file mới để đảm bảo tính duy nhất
            $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            // Đường dẫn đến file sau khi upload
            $dest_path = $uploadFileDir . $newFileName;

            // Di chuyển file vào thư mục upload
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_url = $newFileName;
            } else {
                $errorMessage = "There was an error moving the uploaded file.";
            }
        } else {
            $errorMessage = "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
        }
    } else {
        $errorMessage = "There was an error with the file upload.";
    }

    // Kiểm tra các trường thông tin có bị bỏ trống không
    do {
        if (empty($title) || empty($summary_content) || empty($content) || empty($category_ids) || empty($tags) || empty($image_url)) {
            $errorMessage = "All the fields are required";
            break;
        }

        // Thêm bài viết mới vào cơ sở dữ liệu
        $sql = "INSERT INTO posts (title, summary_content, content, date, image_url) VALUES ('$title', '$summary_content', '$content', '$date', '$image_url')";
        $result = $con->query($sql);

        // Kiểm tra kết quả truy vấn
        if (!$result) {
            $errorMessage = "Invalid query: " . $con->error;
            break;
        }

        $post_id = $con->insert_id;

        // Insert các danh mục đã chọn vào bảng post_categories
        foreach ($category_ids as $category_id) {
            $category_id = intval($category_id);
            $post_category_sql = "INSERT INTO post_categories (post_id, category_id) VALUES ($post_id, $category_id)";
            $con->query($post_category_sql);
        }

        // Insert các thẻ đã chọn vào bảng post_tags
        foreach ($tags as $tag_id) {
            $tag_id = intval($tag_id);
            $post_tag_sql = "INSERT INTO post_tags (post_id, tag_id) VALUES ($post_id, $tag_id)";
            $con->query($post_tag_sql);
        }

        // Reset các biến để chuẩn bị cho việc thêm bài viết mới
        $title = "";
        $summary_content = "";
        $content = "";
        $image_url = "";
        $category_ids = [];
        $tags = [];

        $successMessage = "Post added correctly";
        // Chuyển hướng người dùng về trang New.php sau khi thêm bài viết thành công
        header("Location: New.php?message=add_success");
        exit;
    } while (false);
}
ob_end_flush();

?>

<!-- Hiển thị thông báo lỗi nếu có -->
<?php if (!empty($errorMessage)): ?>
    <div class='alert alert-warning alert-dismissible fade show' role='alert'>
        <strong><?php echo $errorMessage; ?></strong>
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
<?php endif; ?>

<!-- Hiển thị thông báo thành công nếu có -->
<?php if (!empty($successMessage)): ?>
    <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong><?php echo $successMessage; ?></strong>
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
<?php endif; ?>


            <h2>Add New Post</h2>
            <form action="add_post.php" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Title</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Summary Content</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" name="summary_content" rows="2" required><?php echo htmlspecialchars($summary_content); ?></textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Content</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" name="content" rows="4" required><?php echo htmlspecialchars($content); ?></textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Categories</label>
                    <div class="col-sm-6">
                        <?php
                        foreach ($categories as $category) {
                            echo "
                            <div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='category_ids[]' value='{$category['id']}' id='category{$category['id']}'>
                                <label class='form-check-label' for='category{$category['id']}'>
                                    {$category['name']}
                                </label>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Tags</label>
                    <div class="col-sm-6">
                        <?php
                        foreach ($tags_list as $tag) {
                            echo "
                            <div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='tags[]' value='{$tag['id']}' id='tag{$tag['id']}'>
                                <label class='form-check-label' for='tag{$tag['id']}'>
                                    {$tag['name']}
                                </label>
                            </div>";
                        }
                        ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Image</label>
                    <div class="col-sm-6">
                        <input type="file" class="form-control" name="image" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="offset-sm-3 col-sm-3 d-grid">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require('footeradmin.php'); ?>
