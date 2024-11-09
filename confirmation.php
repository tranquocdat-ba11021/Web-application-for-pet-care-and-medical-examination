<?php require('head.php'); ?>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin từ session
    $appointment = $_SESSION['appointment'];
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['user_id'];

    // Lưu thông tin vào cơ sở dữ liệu
    $stmt = $con->prepare("INSERT INTO appointments (user_id, pet_id, service, appointment_date, appointment_time, additional_info, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iissss", $user_id, $appointment['pet_id'], $appointment['service'], $appointment['appointment_date'], $appointment['appointment_time'], $appointment['additional_info']);
    $stmt->execute();
    $appointment_id = $stmt->insert_id;
    $stmt->close();

    // Tính toán số tiền (giả sử có một hàm để tính toán số tiền)
    // $amount = calculateAmount($appointment['service']);

    // Lưu thông tin thanh toán
    $stmt = $con->prepare("INSERT INTO payments (appointment_id, payment_method, amount, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("isd", $appointment_id, $payment_method, $amount);
    $stmt->execute();
    $stmt->close();

    // Cập nhật trạng thái thanh toán và đặt lịch
    if ($payment_method == 'direct') {
        $stmt = $con->prepare("UPDATE payments SET status = 'completed' WHERE appointment_id = ?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();

        $stmt = $con->prepare("UPDATE appointments SET status = 'confirmed' WHERE id = ?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
    } else if ($payment_method == 'momo') {
        // Xử lý thanh toán qua MoMo (tích hợp API của MoMo)
        // ...
    }

    // Gửi email xác nhận (nếu cần)
    // ...
}
?>
<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2 class="mb-4">Confirmation</h2>
                <div class="custom-wrapper">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Your appointment has been booked successfully.</p>
                            <p>Thank you for your payment.</p>
                            <a href="index.php" class="btn btn-primary">Go Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require('footer.php'); ?>
