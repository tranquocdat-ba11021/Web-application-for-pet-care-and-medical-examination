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
    $name_service = $_POST['name_service'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $title_content = $_POST['title_content']; // Lấy giá trị title_content từ form

    // Xử lý upload hình ảnh
    $target_dir = "../uploads/Services/";
    $image_name = basename($_FILES["image"]["name"]);
    $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
    $new_image_name = time() . '_' . uniqid() . '.' . $image_extension;
    $target_file = $target_dir . $new_image_name;
    $image_url = "";

    // Di chuyển file đã upload vào thư mục đích
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_url = $new_image_name;
    } else {
        echo "Sorry, there was an error uploading your file.";
        exit;
    }

    // Kiểm tra giá trị của biến type và đặt tên loại dịch vụ tương ứng
    if ($type == 1) {
        $type_name = "examination";
        $price = NULL; // Không cần giá cho dịch vụ khám bệnh
    } elseif ($type == 2) {
        $type_name = "Services";
        $price = $_POST['price'];
        if (empty($price)) {
            echo "Price is required for services.";
            exit;
        }
    } else {
        // Giá trị không hợp lệ, có thể xử lý thông báo lỗi tùy ý
        echo "Invalid service type.";
        exit;
    }

    // Thêm dịch vụ mới vào cơ sở dữ liệu
    if ($type == 1) {
        $stmt = $con->prepare("INSERT INTO `services` (name_service, type, type_name, description, image_url, title_content) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $name_service, $type, $type_name, $description, $image_url, $title_content);
    } else {
        $stmt = $con->prepare("INSERT INTO `services` (name_service, type, type_name, price, description, image_url, title_content) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssss", $name_service, $type, $type_name, $price, $description, $image_url, $title_content);
    }

    if ($stmt->execute()) {
        // Chuyển hướng người dùng về trang quản lý dịch vụ sau khi thêm thành công
        header("Location: manage_services.php?message=add_success");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    // Đóng kết nối và giải phóng tài nguyên
    $stmt->close();
    $con->close();
}
ob_end_flush();
?>

<h2>Add New Service</h2>
<!-- Form để thêm dịch vụ mới -->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
    <div class="mb-3">
        <label for="name_service" class="form-label">Service Name</label>
        <input type="text" class="form-control" id="name_service" name="name_service" required>
    </div>
    <div class="mb-3">
        <label for="type" class="form-label">Type</label>
        <select class="form-control" id="type" name="type" onchange="toggleFields()" required>
            <option value=""></option>
            <option value="1">Examination</option>
            <option value="2">Service</option>
        </select>
    </div>
    <div class="mb-3" id="price_field" style="display: none;">
        <label for="price" class="form-label">Price</label>
        <input type="text" class="form-control" id="price" name="price">
    </div>
    <div class="mb-3" id="title_content_field" style="display: none;">
        <label for="title_content" class="form-label">Title Content</label>
        <textarea class="form-control" id="title_content" name="title_content" rows="4"></textarea>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea id="content" name="description" required></textarea>
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" class="form-control" id="image" name="image" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
</div>
</div>

<!-- Gọi file footeradmin.php để hiển thị footer -->
<?php require('footeradmin.php'); ?>

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

function validateForm() {
    var type = document.getElementById('type').value;
    var price = document.getElementById('price').value;

    if (type == '2' && price.trim() === '') {
        alert('Price is required for services.');
        return false;
    }

    return true;
}
</script>

<?php require('footeradmin.php'); ?>
