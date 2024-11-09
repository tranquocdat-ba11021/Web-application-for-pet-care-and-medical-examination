<?php
require('head.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['appointment_id'])) {
    header('Location: login.php');
    exit;
}

$full_name = $_SESSION['full_name'];
$service_name = $_SESSION['service_name'];
$price = $_SESSION['price'];
$appointment_id = $_SESSION['appointment_id'];

// Display QR code for payment (You need to integrate the MoMo QR code generation here)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Payment</title>
</head>
<body>
    <h2>Quét mã QR để thanh toán</h2>
    <p>Xin chào, <?php echo htmlspecialchars($full_name); ?></p>
    <p>Dịch vụ: <?php echo htmlspecialchars($service_name); ?></p>
    <p>Số tiền: <?php echo number_format($price); ?> VND</p>
    <div>
        <!-- QR code image -->
        <img src="path_to_qr_code_image" alt="QR Code">
    </div>
    <form action="success_payment.php" method="POST">
        <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
        <button type="submit">Hoàn tất thanh toán</button>
    </form>
</body>
</html>
