<?php
require('head.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ensure the appointment ID is provided
if (!isset($_GET['id'])) {
    header('Location: history.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$appointment_id = intval($_GET['id']);

// Fetch the appointment details including payment information
$sql = "SELECT a.appointment_date, a.appointment_time, a.additional_info, s.name_service, s.price, p.pet_name, a.status,
               pay.payment_method, pay.payment_status
        FROM appointments a
        JOIN services s ON a.service = s.id_service
        JOIN user_pets p ON a.pet_id = p.id
        LEFT JOIN payments pay ON a.id = pay.appointment_id
        WHERE a.user_id = ? AND a.id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $user_id, $appointment_id);
$stmt->execute();
$stmt->bind_result($appointment_date, $appointment_time, $additional_info, $service_name, $price, $pet_name, $status, $payment_method, $payment_status);
$stmt->fetch();
$stmt->close();
?>

<div class="main-content">
    <div class="container d-flex">
        <?php require('sidebar.php'); ?>

        <div class="content">
            <h3>Scheduling details</h3>
            <div class="card mx-5" style="width: 100%; max-width: 800px;">
                <div class="card-body">
                    <p><strong>Day:</strong> <?php echo htmlspecialchars($appointment_date); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment_time); ?></p>
                    <p><strong>Service:</strong> <?php echo htmlspecialchars($service_name); ?></p>
                    <p><strong>Pet:</strong> <?php echo htmlspecialchars($pet_name); ?></p>
                    <p><strong>More information:</strong> <?php echo htmlspecialchars($additional_info); ?></p>
                    <p><strong>Price:</strong> <?php echo number_format($price); ?> VND</p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>
                    <p><strong>Payment method:</strong> <?php echo htmlspecialchars($payment_method); ?></p>
                    <p><strong>Payment status:</strong> <span id="payment-status"><?php echo htmlspecialchars($payment_status); ?></span>
                    <a href="service_history.php" class="btn d_btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>
