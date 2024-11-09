<?php
require('connection.php'); // Ensure this file contains the database connection

if (!isset($_SESSION['user_id']) || !isset($_POST['appointment_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$user_id = $_SESSION['user_id'];
$appointment_id = intval($_POST['appointment_id']);

$sql_payment_status = "SELECT payment_status FROM payments WHERE appointment_id = ?";
$stmt_payment_status = $con->prepare($sql_payment_status);
$stmt_payment_status->bind_param("i", $appointment_id);
$stmt_payment_status->execute();
$stmt_payment_status->bind_result($payment_status);
$stmt_payment_status->fetch();
$stmt_payment_status->close();

if ($payment_status) {
    echo json_encode(['status' => 'success', 'payment_status' => $payment_status]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Payment status not found']);
}
?>
