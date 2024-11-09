<?php
require('headadmin.php');

if (isset($_GET['id'])) {
    $slide_id = $_GET['id'];

    // Lấy tên ảnh của slide cần xóa
    $sql_get_image = "SELECT image FROM slides WHERE id = ?";
    $stmt_get_image = $con->prepare($sql_get_image);
    $stmt_get_image->bind_param('i', $slide_id);
    $stmt_get_image->execute();
    $result_get_image = $stmt_get_image->get_result();
    $slide = $result_get_image->fetch_assoc();

    if ($slide) {
        $image_path = '../uploads/slides/' . $slide['image'];

        // Xóa slide từ cơ sở dữ liệu
        $sql_delete = "DELETE FROM slides WHERE id = ?";
        $stmt_delete = $con->prepare($sql_delete);
        $stmt_delete->bind_param('i', $slide_id);
        
        if ($stmt_delete->execute()) {
            // Xóa ảnh từ thư mục uploads
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            header("Location: manage_slides.php?message=delete_success");
            exit();
        } else {
            echo "Error: " . $stmt_delete->error;
        }
    } else {
        echo "Slide not found.";
    }
} else {
    echo "Invalid slide ID.";
}
?>
