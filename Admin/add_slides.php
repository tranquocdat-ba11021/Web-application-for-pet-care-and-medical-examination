<?php
require('headadmin.php');

ob_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_slide'])) {
    $title = $_POST['title'];
    $sub_title = $_POST['sub_title'];
    $button_text = $_POST['button']; // Sử dụng button_text để phù hợp với cột trong bảng
    $link = $_POST['link'];
    
    // Xử lý ảnh tải lên
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imagePath = '../uploads/slides/' . basename($imageName);

        // Di chuyển ảnh từ tạm thời đến thư mục đích
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            // Lưu thông tin slide vào cơ sở dữ liệu
            $sql = "INSERT INTO slides (title, sub_title, button_text, link, image) VALUES (?, ?, ?, ?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param('sssss', $title, $sub_title, $button_text, $link, $imageName);

            if ($stmt->execute()) {
                header("Location: manage_slides.php?message=add_success");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "No image uploaded or upload error.";
    }
}
ob_end_flush();

?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php'); ?>

        <div id="content2">
            <h2>Add New Slide</h2>

            <!-- Form thêm slide -->
            <div class="mb-3">
                <form action="add_slides.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Slide Title" required>
                    </div>
                    <div class="form-group">
                        <label for="sub_title">Subtitle</label>
                        <input type="text" class="form-control" id="sub_title" name="sub_title" placeholder="Slide Subtitle" required>
                    </div>
                    <div class="form-group">
                        <label for="button">Button Text</label>
                        <input type="text" class="form-control" id="button" name="button" placeholder="Button Text" required>
                    </div>
                    <div class="form-group">
                        <label for="link">Link</label>
                        <input type="text" class="form-control" id="link" name="link" placeholder="Slide Link" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" class="form-control" id="image" name="image" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="add_slide">Add Slide</button>
                </form>
            </div>
        </div>
    </div>

    <?php require('footeradmin.php'); ?>
</body>
</html>
