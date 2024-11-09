<?php
require('headadmin.php');
ob_start();
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <?php

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // Phương thức GET: Hiển thị dữ liệu của bác sĩ
                if (!isset($_GET["id"])) {
                    header("location:doctor.php");
                    exit;
                }

                $id = $_GET["id"];

                $sql = "SELECT * FROM `doctor` WHERE id_doctor=$id";
                $result = $con->query($sql);
                $row = $result->fetch_assoc();

                if (!$row) {
                    header("location: doctor.php");
                    exit;
                }

                $name_doctor = $row["name_doctor"];
                $email_doctor = $row["email_doctor"];
                $phone_doctor = $row["phone_doctor"];
                $address_doctor = $row["address_doctor"];
                $image_doctor = $row["image_doctor"];
                $intro_doctor = $row["intro_doctor"];
                $facebook_link = $row["facebook_link"];
                $instagram_link = $row["instagram_link"];
                $twitter_link = $row["twitter_link"];
                $username = $row["username"]; // Lấy giá trị username từ cơ sở dữ liệu
                $services_doctor = isset($row["services_doctor"]) ? explode(',', $row["services_doctor"]) : [];

            } else {
                // Phương thức POST: cập nhật dữ liệu của bác sĩ
                $id_doctor = $_POST["id"];
                $username = $_POST["username"];
                $password = $_POST["password"];
                $name_doctor = $_POST["name"];
                $email_doctor = $_POST["email"];
                $phone_doctor = $_POST["phone"];
                $address_doctor = $_POST["address"];
                $intro_doctor = $_POST["intro"];
                $facebook_link = $_POST["facebook"];
                $instagram_link = $_POST["instagram"];
                $twitter_link = $_POST["twitter"];

                // Không cần xử lý dịch vụ, chỉ hiển thị dịch vụ hiện tại

                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && $_FILES['image']['size'] > 0) {
                    $fileTmpPath = $_FILES['image']['tmp_name'];
                    $fileName = $_FILES['image']['name'];
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $uploadFileDir = '../uploads/doctor/';
                        $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
                        $dest_path = $uploadFileDir . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            $image_doctor = $newFileName;
                        } else {
                            $errorMessage = "There was an error moving the uploaded file.";
                        }
                    } else {
                        $errorMessage = "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
                    }
                }

                do {
                    if (empty($username) || empty($name_doctor) || empty($email_doctor) || empty($phone_doctor) || empty($address_doctor)) {
                        $errorMessage = "All the fields are required";
                        break;
                    }

                    // Cập nhật các trường
                    $sql = "UPDATE `doctor` SET 
                    username='$username', 
                    name_doctor='$name_doctor', 
                    email_doctor='$email_doctor', 
                    phone_doctor='$phone_doctor', 
                    address_doctor='$address_doctor', 
                    intro_doctor='$intro_doctor', 
                    facebook_link='$facebook_link', 
                    instagram_link='$instagram_link', 
                    twitter_link='$twitter_link'";

                    // Kiểm tra nếu có thay đổi password
                    if (!empty($password)) {
                        // Mã hóa mật khẩu trước khi lưu vào cơ sở dữ liệu
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $sql .= ", password='$hashed_password'";
                    }

                    // Kiểm tra nếu có thay đổi ảnh đại diện
                    if (!empty($image_doctor)) {
                        $sql .= ", image_doctor='$image_doctor'";
                    }

                    $sql .= " WHERE id_doctor=$id_doctor";
                    $result = $con->query($sql);

                    if (!$result) {
                        $errorMessage = "Invalid query: "  . $con->error;
                        break;
                    }

                
                    header("location: doctor.php?message=update_success");
                    exit();
                } while (false);
            }
            ob_end_flush();

            ?>

            <h2>Edit Doctor</h2>

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

            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <!-- Username -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Username </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    </div>
                </div>

                <!-- Password -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Password </label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" name="password" placeholder="Enter new password">
                    </div>
                </div>

                <!-- Name -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Name </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name_doctor) ?>">
                    </div>
                </div>
                <!-- Email -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Email </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars($email_doctor) ?>">
                    </div>
                </div>
                <!-- Phone -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Phone </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($phone_doctor) ?>">
                    </div>
                </div>
                <!-- Address -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Address </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($address_doctor) ?>">
                    </div>
                </div>
                <!-- Image -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Image </label>
                    <div class="col-sm-6">
                        <input type="file" class="form-control" name="image">
                        <?php if ($image_doctor) : ?>
                            <img src="../uploads/doctor/<?php echo htmlspecialchars($image_doctor); ?>" alt="Doctor Image" class="img-thumbnail mt-2" width="150">
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Introduction -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Introduction </label>
                    <div class="col-sm-6">
                        <textarea class="form-control" name="intro"><?php echo htmlspecialchars($intro_doctor) ?></textarea>
                    </div>
                </div>
                <!-- Facebook Link -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Facebook Link </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="facebook" value="<?php echo htmlspecialchars($facebook_link) ?>">
                    </div>
                </div>
                <!-- Instagram Link -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Instagram Link </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="instagram" value="<?php echo htmlspecialchars($instagram_link) ?>">
                    </div>
                </div>
                <!-- Twitter Link -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Twitter Link </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="twitter" value="<?php echo htmlspecialchars($twitter_link) ?>">
                    </div>
                </div>
                <!-- Services -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label"> Services </label>
                    <div class="col-sm-6">
                        <?php
                        // Lấy danh sách dịch vụ từ bảng services
                        $sql_services = "SELECT * FROM services WHERE id_service IN (" . implode(',', array_map('intval', $services_doctor)) . ")";
                        $result_services = $con->query($sql_services);
                        while ($service = $result_services->fetch_assoc()) {
                            echo "
                            <div class='form-check'>
                                <input class='form-check-input' type='checkbox' name='services[]' value='{$service['id_service']}' checked disabled>
                                <label class='form-check-label'>
                                    {$service['name_service']}
                                </label>
                            </div>
                            ";
                        }
                        ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-9 offset-sm-3">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
