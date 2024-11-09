<?php 
require('head.php'); 

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit;
}

$successMessage = ""; // Khởi tạo thông báo thành công

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ biểu mẫu
    $user_id = $_SESSION['user_id'];
    $pet_name = $_POST['pet_name'];
    $pet_type = $_POST['pet_type'];
    $pet_gender = $_POST['pet_gender'];
    $pet_age = $_POST['pet_age'];
    $pet_description = $_POST['pet_description'];

    // Chuẩn bị và thực thi truy vấn chèn dữ liệu
    $sql_insert = "INSERT INTO user_pets (user_id, pet_name, pet_type, pet_gender, pet_age, pet_description) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql_insert);
    $stmt->bind_param('isssss', $user_id, $pet_name, $pet_type, $pet_gender, $pet_age, $pet_description);

    if ($stmt->execute()) {
        // Đặt thông báo thành công
        $successMessage = "Add pet successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<div class="main-content">
    <div class="container d-flex">
        <!-- SIDEBAR -->
        <?php require('sidebar.php'); ?>

        <!-- CONTENT -->
        <div class="content">
            <h3>Add Pets</h3>
            <div class="card mx-5" style="width: 100%; max-width: 800px;">
                <div class="card-body">

                    <!-- Hiển thị thông báo thành công nếu có -->
                    <?php if (!empty($successMessage)) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $successMessage; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="add_pets.php" method="POST">
                        <div class="row mb-4">
                            <label for="pet_name" class="col-sm-2 col-form-label">Pet Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pet_name" name="pet_name" placeholder="Enter pet name" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="pet_type" class="col-sm-2 col-form-label">Pet Type</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="pet_type" name="pet_type" required>
                                    <option value=""></option>
                                    <option value="dog">Dog</option>
                                    <option value="cat">Cat</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="pet_gender" class="col-sm-2 col-form-label">Pet Gender</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="pet_gender" name="pet_gender" required>
                                    <option value=""></option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="pet_age" class="col-sm-2 col-form-label">Pet Age</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pet_age" name="pet_age" placeholder="Enter pet age" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="pet_description" class="col-sm-2 col-form-label">Pet Description</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="pet_description" name="pet_description" rows="3" placeholder="Enter pet description" required></textarea>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>
