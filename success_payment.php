<?php
require('head.php');

// Giả sử bạn dùng tham số 'type' trong URL để phân biệt loại giao dịch
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Kiểm tra loại giao dịch và thiết lập thông báo và liên kết phù hợp
if ($type === 'service') {
    $message = 'You have successfully paid for the service. Please see service transaction history <a href="service_history.php">view service transaction history</a>.';
    $back_link = 'service.php';
} elseif ($type === 'appointment') {
    $message = 'You have successfully scheduled your appointment. Please<a href="appointment_history.php">view appointment history</a>.';
    $back_link = 'appointments.php'; // hoặc trang khác nếu cần
} else {
    // Xử lý trường hợp không xác định loại giao dịch
    $message = 'Successful transaction. Please <a href="index.php">return to home page</a>.';
    $back_link = 'index.php';
}
?>

<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="custom-wrapper">
                    <h2 class="mb-4">Scheduled successfully</h2>
                    <p><?php echo $message; ?></p>
                    <a href="<?php echo htmlspecialchars($back_link); ?>" class="btn btn-primary mt-4">Return Page</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require('footer.php'); ?>
