<?php
require('headadmin.php');

// Xử lý xóa slide
if (isset($_GET['delete'])) {
    $slide_id = $_GET['delete'];

    // Lấy tên ảnh của slide cần xóa
    $sql_get_image = "SELECT image FROM slides WHERE id = ?";
    $stmt_get_image = $con->prepare($sql_get_image);
    $stmt_get_image->bind_param('i', $slide_id);
    $stmt_get_image->execute();
    $result_get_image = $stmt_get_image->get_result();
    $slide = $result_get_image->fetch_assoc();
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
        header("Location: manage_slides.php?success=Slide deleted successfully");
        exit();
    } else {
        echo "Error: " . $stmt_delete->error;
    }
}
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php'); ?>

        <div id="content2">
            <h2>Manage Slides</h2>
            <?php
            if (isset($_GET['message']) && $_GET['message'] == 'update_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    update successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            if (isset($_GET['message']) && $_GET['message'] == 'add_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Added successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            if (isset($_GET['message']) && $_GET['message'] == 'delete_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    deleted successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            ?>
            <!-- Nút thêm slide -->
            <div class="mb-3">
                <a href="add_slides.php" class="btn btn-primary">Add slides</a>
            </div>

            <!-- Slide Table -->
            <h3>Slide List</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th>Button Text</th>
                        <th>Link</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Kết nối với cơ sở dữ liệu và lấy dữ liệu slides
                    $sql_slides = "SELECT * FROM slides ORDER BY id";
                    $result_slides = $con->query($sql_slides);

                    if ($result_slides->num_rows > 0) {
                        while ($slide = $result_slides->fetch_assoc()) {
                            // Giới hạn độ dài của sub_title và link
                            $short_sub_title = (strlen($slide['sub_title']) > 30) ? substr($slide['sub_title'], 0, 30) . '...' : $slide['sub_title'];
                            $short_link = (strlen($slide['link']) > 30) ? substr($slide['link'], 0, 30) . '...' : $slide['link'];

                            echo "
                            <tr>
                                <td>{$slide['id']}</td>
                                <td>{$slide['title']}</td>
                                <td title='{$slide['sub_title']}'>{$short_sub_title}</td>
                                <td>{$slide['button_text']}</td>
                                <td title='{$slide['link']}'>{$short_link}</td>
                                <td><img src='../uploads/slides/{$slide['image']}' alt='' width='100'></td>
                                <td>
                                    <a href='edit_slide.php?id={$slide['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_slide.php?id={$slide['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this slide?\");'>Delete</a>
                                </td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No slides found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>