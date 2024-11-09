<?php require('headadmin.php');
ob_start(); ?>
<body>
<div class="wrapper">
<?php require('navbaradmin.php') ?>

<div id="content2">
<?php
// Kiểm tra nếu phương thức gửi yêu cầu là POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy các giá trị từ form
    $id_service = $_POST['id_service'];
    $name_service = $_POST['name_service'];
    $type = $_POST['type'];
    $price = isset($_POST['price']) ? $_POST['price'] : NULL;
    $description = $_POST['description'];
    $title_content = $_POST['title_content']; // Lấy giá trị title_content từ form
    $image_url = ''; // Khởi tạo biến $image_url để đảm bảo không rỗng khi không có hình ảnh mới

    // Kiểm tra nếu có hình ảnh mới được tải lên
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK && $_FILES['image']['size'] > 0) {
        // Lấy tên tệp ảnh cũ từ cơ sở dữ liệu
        $stmt = $con->prepare("SELECT image_url FROM `services` WHERE id_service=?");
        $stmt->bind_param("i", $id_service);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($old_image_url);
            $stmt->fetch();
            
            // Xóa tệp ảnh cũ khỏi thư mục lưu trữ
            $old_image_path = "../uploads/Services/" . $old_image_url;
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }

        // Tạo tên tệp mới và di chuyển tệp mới vào thư mục lưu trữ
        $target_dir = "../uploads/Services/";
        $image_name = basename($_FILES["image"]["name"]);
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_image_name = time() . '_' . uniqid() . '.' . $image_extension;
        $target_file = $target_dir . $new_image_name;

        // Di chuyển file đã upload vào thư mục đích
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $new_image_name; // Cập nhật $image_url chỉ khi tải lên hình ảnh thành công
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;       
        }
    } else {
        // Nếu không có hình ảnh mới, giữ nguyên đường dẫn hình ảnh cũ từ cơ sở dữ liệu
        $stmt = $con->prepare("SELECT image_url FROM `services` WHERE id_service=?");
        $stmt->bind_param("i", $id_service);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($image_url);
            $stmt->fetch();
        }
    }

    // Kiểm tra giá trị của biến type và đặt tên loại dịch vụ tương ứng
    if ($type == 1) {
        $type_name = "Khám bệnh";
        $price = 0; // Không cần giá cho dịch vụ khám bệnh
    } elseif ($type == 2) {
        $type_name = "Dịch vụ";
        if (empty($price)) {
            echo "Price is required for services.";
            exit;
        }
    } else {
        echo "Invalid service type.";
        exit;
    }

    // Thực hiện truy vấn SQL để cập nhật dịch vụ
    $stmt = $con->prepare("UPDATE `services` SET name_service=?, type=?, type_name=?, price=?, description=?, image_url=?, title_content=? WHERE id_service=?");
    $stmt->bind_param("sisssssi", $name_service, $type, $type_name, $price, $description, $image_url, $title_content, $id_service);

    if ($stmt->execute()) {
        // Chuyển hướng người dùng về trang quản lý dịch vụ sau khi cập nhật thành công
        header("Location: manage_services.php?message=update_success");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Đóng kết nối và giải phóng tài nguyên
    $stmt->close();
    $con->close();
} else {
    // Hiển thị thông báo lỗi nếu không có id_service được truyền từ URL
    if (!isset($_GET['id'])) {
        echo "Service ID is missing.";
        exit;
    } else {
        $id_service = $_GET['id'];

        // Thực hiện truy vấn SQL để lấy thông tin của dịch vụ dựa trên id_service
        $stmt = $con->prepare("SELECT * FROM `services` WHERE id_service=?");
        $stmt->bind_param("i", $id_service);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Lấy dữ liệu dịch vụ từ kết quả truy vấn
            $row = $result->fetch_assoc();
            $name_service = $row['name_service'];
            $type = $row['type'];
            $price = $row['price'];
            $description = $row['description'];
            $image_url = $row['image_url'];
            $title_content = $row['title_content']; // Lấy giá trị title_content từ cơ sở dữ liệu
        } else {
            echo "Service not found.";
            exit;
        }

        $stmt->close();
    }
}
ob_end_flush();
?>
    <!-- Gọi file navbaradmin.php để hiển thị thanh điều hướng -->

                <h2>Edit Service</h2>
                <!-- Form để chỉnh sửa dịch vụ -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_service" value="<?php echo $id_service; ?>">
                    <div class="mb-3">
                        <label for="name_service" class="form-label">Service name</label>
                        <input type="text" class="form-control" id="name_service" name="name_service" value="<?php echo $name_service; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-control" id="type" name="type" onchange="toggleFields()" required>
                            <option value="1" <?php if ($type == 1) echo 'selected'; ?>>Examination of the Disease</option>
                            <option value="2" <?php if ($type == 2) echo 'selected'; ?>>Service</option>
                        </select>
                    </div>
                    <div class="mb-3" id="price_field" <?php if ($type != 2) echo 'style="display:none;"'; ?>>
                        <label for="price" class="form-label">Price</label>
                        <input type="text" class="form-control" id="price" name="price" value="<?php echo $price; ?>">
                    </div>
                    <div class="mb-3" id="title_content_field" <?php if ($type != 2) echo 'style="display:none;"'; ?>>
                        <label for="title_content" class="form-label">Title Content</label>
                        <textarea class="form-control" id="title_content" name="title_content" rows="4"><?php echo $title_content; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Describe</label>
                        <textarea class="form-control" id="content" name="description" rows="4" required><?php echo $description; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                        <?php if (!empty($image_url)) { ?>
                            <img src="../uploads/Services/<?php echo $image_url; ?>" alt="Service Image" class="img-fluid mt-2" style="max-height: 200px;">
                        <?php } ?>
                        <input type="hidden" name="image_url" value="<?php echo $image_url; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleFields() {
            var type = document.getElementById('type').value;
            var priceField = document.getElementById('price_field');
            var titleContentField = document.getElementById('title_content_field');
            if (type == '2') {
                priceField.style.display = 'block';
                titleContentField.style.display = 'block';
            } else {
                priceField.style.display = 'none';
                titleContentField.style.display = 'none';
            }
        }

        // Gọi hàm để hiển thị đúng các trường khi trang được tải lần đầu
        window.onload = function() {
            toggleFields();
        }
    </script>
<?php require('footeradmin.php') ?>
