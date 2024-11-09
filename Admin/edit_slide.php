<?php
require('headadmin.php');

ob_start();


// Lấy thông tin slide cần chỉnh sửa
if (isset($_GET['id'])) {
    $slide_id = $_GET['id'];

    $sql_get_slide = "SELECT * FROM slides WHERE id = ?";
    $stmt_get_slide = $con->prepare($sql_get_slide);
    $stmt_get_slide->bind_param('i', $slide_id);
    $stmt_get_slide->execute();
    $result_get_slide = $stmt_get_slide->get_result();
    $slide = $result_get_slide->fetch_assoc();
} else {
    header("Location: manage_slides.php");
    exit();
}

// Xử lý cập nhật slide
if (isset($_POST['update_slide'])) {
    $title = $_POST['title'];
    $sub_title = $_POST['sub_title'];
    $button_text = $_POST['button_text'];
    $link = $_POST['link'];
    $image = $_FILES['image']['name'];

    if (!empty($image)) {
        // Xử lý ảnh mới
        $target_dir = "../uploads/silder/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

        // Xóa ảnh cũ
        $old_image_path = '../uploads/silder/' . $slide['image'];
        if (file_exists($old_image_path)) {
            unlink($old_image_path);
        }
        
        // Cập nhật slide với ảnh mới
        $sql_update = "UPDATE slides SET title = ?, sub_title = ?, button_text = ?, link = ?, image = ? WHERE id = ?";
        $stmt_update = $con->prepare($sql_update);
        $stmt_update->bind_param('sssssi', $title, $sub_title, $button_text, $link, $image, $slide_id);
    } else {
        // Cập nhật slide mà không thay đổi ảnh
        $sql_update = "UPDATE slides SET title = ?, sub_title = ?, button_text = ?, link = ? WHERE id = ?";
        $stmt_update = $con->prepare($sql_update);
        $stmt_update->bind_param('ssssi', $title, $sub_title, $button_text, $link, $slide_id);
    }
    
    if ($stmt_update->execute()) {
        header("Location: manage_slides.php?message=update_success");
        exit();
    } else {
        echo "Error: " . $stmt_update->error;
    }
}
ob_end_flush();

?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php'); ?>

        <div id="content2">
            <h2>Edit Slide</h2>

            <!-- Form chỉnh sửa slide -->
            <div class="mb-3">
                <form action="edit_slide.php?id=<?php echo $slide_id; ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($slide['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="sub_title">Subtitle</label>
                        <input type="text" class="form-control" id="sub_title" name="sub_title" value="<?php echo htmlspecialchars($slide['sub_title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="button_text">Button Text</label>
                        <input type="text" class="form-control" id="button_text" name="button_text" value="<?php echo htmlspecialchars($slide['button_text']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="link">Link</label>
                        <input type="text" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($slide['link']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Image (Leave blank if not changing)</label>
                        <input type="file" class="form-control" id="image" name="image">
                        <?php if ($slide['image']) { ?>
                            <img src="../uploads/silder/<?php echo htmlspecialchars($slide['image']); ?>" alt="" width="100">
                        <?php } ?>
                    </div>
                    <button type="submit" class="btn btn-primary" name="update_slide">Update Slide</button>
                </form>
            </div>
        </div>
    </div>

    <?php require('footeradmin.php'); ?>
</body>
</html>
