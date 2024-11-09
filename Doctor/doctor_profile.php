<?php

require('headdoctor.php'); 
// Kiểm tra xem bác sĩ đã đăng nhập chưa
if (!isset($_SESSION['doctor_id'])) {
    header('Location: doctorlogin.php'); // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

// Lấy thông tin của bác sĩ từ cơ sở dữ liệu
$sql = "SELECT * FROM `doctor` WHERE id_doctor = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    header('Location: doctorlogin.php'); // Nếu không tìm thấy thông tin bác sĩ, chuyển hướng đến trang đăng nhập
    exit();
}

// Đóng kết nối cơ sở dữ liệu
$stmt->close();
$con->close();
?>
<body>
    <div class="wrapper">
        <?php require('navbardoctor.php'); ?>

        <div id="content2">
            <div class="container">
                <h2 class="header-title">Doctor Profile</h2>
                
                <!-- Thêm thông báo sau khi cập nhật -->
                <?php if (isset($_SESSION['update_success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo $_SESSION['update_success']; 
                        unset($_SESSION['update_success']);
                        ?>
                    </div>
                <?php elseif (isset($_SESSION['update_error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        echo $_SESSION['update_error']; 
                        unset($_SESSION['update_error']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <form action="update_doctor_profile.php" method="POST" enctype="multipart/form-data" class="profile-info">
                        <div class="text-center">
                            <img src="../uploads/doctor/<?php echo htmlspecialchars($doctor['image_doctor']); ?>" alt="Doctor Image" class="img-thumbnail">
                        </div>
                        <!-- Các trường input như cũ -->
                        <div class="form-group">
                            <label for="image_doctor">Profile Image:</label>
                            <input type="file" id="image_doctor" name="image_doctor">
                        </div>
                        <div class="form-group">
                            <label for="name_doctor">Name:</label>
                            <input type="text" id="name_doctor" name="name_doctor" value="<?php echo htmlspecialchars($doctor['name_doctor']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone_doctor">Phone:</label>
                            <input type="text" id="phone_doctor" name="phone_doctor" value="<?php echo htmlspecialchars($doctor['phone_doctor']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address_doctor">Address:</label>
                            <input type="text" id="address_doctor" name="address_doctor" value="<?php echo htmlspecialchars($doctor['address_doctor']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="intro_doctor">Introduction:</label>
                            <textarea id="intro_doctor" name="intro_doctor" rows="4"><?php echo htmlspecialchars($doctor['intro_doctor']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="facebook_link">Facebook:</label>
                            <input type="text" id="facebook_link" name="facebook_link" value="<?php echo htmlspecialchars($doctor['facebook_link']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="instagram_link">Instagram:</label>
                            <input type="text" id="instagram_link" name="instagram_link" value="<?php echo htmlspecialchars($doctor['instagram_link']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="twitter_link">Twitter:</label>
                            <input type="text" id="twitter_link" name="twitter_link" value="<?php echo htmlspecialchars($doctor['twitter_link']); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
    <?php require('footerdoctor.php'); ?>
