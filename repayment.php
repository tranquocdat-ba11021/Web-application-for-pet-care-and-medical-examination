<?php
require('head.php');

// Đảm bảo người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Đảm bảo ID cuộc hẹn được cung cấp
if (!isset($_GET['id'])) {
    header('Location: appointment_history.php');
    exit;
}

$appointment_id = intval($_GET['id']);

// Kiểm tra xem cuộc hẹn có tồn tại trong cơ sở dữ liệu không
$sql_check_appointment = "SELECT id FROM appointments WHERE id = ?";
$stmt_check_appointment = $con->prepare($sql_check_appointment);
$stmt_check_appointment->bind_param("i", $appointment_id);
$stmt_check_appointment->execute();
$stmt_check_appointment->store_result();

if ($stmt_check_appointment->num_rows === 0) {
    header('Location: appointment_history.php');
    exit;
}

$stmt_check_appointment->close();
$order_number = sprintf('%06d', $appointment_id);
// ẩn nút khi quá thời gian đặt 
// bổ sung thằng payment xử lý biến repay thôi rồi chạy nhuư luông ban đầu 
$sql_appointment_details = "SELECT a.appointment_date, a.appointment_time, a.additional_info, s.name_service, s.price, u.full_name, u.phone, p.pet_name
FROM appointments a
JOIN services s ON a.service = s.id_service
JOIN registered_users u ON a.user_id = u.id
JOIN user_pets p ON a.pet_id = p.id
WHERE a.id = ?";
$stmt_details = $con->prepare($sql_appointment_details);
$stmt_details->bind_param("i", $appointment_id);
$stmt_details->execute();
$stmt_details->bind_result($appointment_date, $appointment_time, $additional_info, $service_name, $price, $full_name, $phone, $pet_name);
$stmt_details->fetch();
$stmt_details->close();

?>

<div class="main-content">
            <div class="container d-flex">
                <div class="content">
                    <h3>TDo not trust the order</h3>
                    <div class="card mx-auto" style="width: 100%; max-width: 800px;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p>Hello, <?php echo htmlspecialchars($full_name); ?>.</p>
                                    <p>Your order has been recorded.</p>
                                    <p><strong>Service:</strong> <?php echo htmlspecialchars($service_name); ?></p>
                                    <p><strong>Pet:</strong> <?php echo htmlspecialchars($pet_name); ?></p>
                                    <p><strong>Appointment date:</strong> <?php echo htmlspecialchars($appointment_date); ?></p>
                                    <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment_time); ?></p>
                                    <p><strong>Additional information:</strong> <?php echo htmlspecialchars($additional_info); ?></p>
                                    <p><strong>Price:</strong> <?php echo number_format($price); ?> VND</p>
                                    <p><strong>Order code:</strong> <?php echo htmlspecialchars($order_number); ?></p>
                                </div>
                                <div class="col-md-6 d-flex justify-content-center align-items-center">
                                    <img id="qr_code" src="https://img.vietqr.io/image/MB-0971622398-compact.png?amount=<?php echo $price; ?>&addInfo=MDH<?php echo number_format($order_number); ?>&accountName=<?php echo htmlspecialchars($full_name); ?>" alt="QR Code" class="img-fluid img-thumbnail" style="max-width: 300px;">
                                </div>
                            </div>
                            <div class="mt-3 text-center">
                                <form action="payment.php" method="POST" class="d-inline">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                                    <input type="hidden" name="payment_method" value="online">
                                    <button type="submit" class="btn btn-success">Complete payment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="history.php" class="btn btn-secondary">View booking history</a>
                        <a href="service.php" class="btn btn-secondary">Return to service</a>
                    </div>
                </div>
            </div>
        </div>


<?php
ob_end_flush(); // Gửi tất cả nội dung trong bộ đệm và tắt bộ đệm đầu ra
?>
<script>
    // Set the expiration time (5 minutes)
    var expirationTime = 10 * 60 * 1000; // 5 minutes in milliseconds
    var qrCodeElement = document.getElementById('qr_code');

    // Redirect to failed_payment.php if the QR code has expired
    setTimeout(function() {
        window.location.href = 'failed_payment.php';
    }, expirationTime);

    // Optionally, you can display a countdown timer for user awareness
    var countdownElement = document.createElement('p');
    countdownElement.id = 'countdown';
    document.querySelector('.card-body').appendChild(countdownElement);

    function updateCountdown() {
        var now = new Date().getTime();
        var distance = expirationTime - (now - startTime);

        if (distance < 0) {
            document.getElementById('countdown').innerHTML = "QR code has expired.";
            return;
        }

        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('countdown').innerHTML = minutes + "m " + seconds + "s ";

        setTimeout(updateCountdown, 1000);
    }

    var startTime = new Date().getTime();
    updateCountdown();
</script>
<?php require('footer.php'); ?>