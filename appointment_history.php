<?php
require('head.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cancel_success = false; // Variable to store cancellation success status
$cancel_error = false;   // Variable to store cancellation failure status

// Handle cancel appointment request
if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);

    // Update the status of the appointment to 'canceled'
    $sql_cancel = "UPDATE appointments SET status = 'canceled' WHERE id = ? AND user_id = ?";
    $stmt_cancel = $con->prepare($sql_cancel);
    $stmt_cancel->bind_param("ii", $cancel_id, $user_id);

    // Update payment status to 'failed'
    $sql_payment_cancel = "UPDATE payments SET payment_status = 'failed' WHERE appointment_id = ?";
    $stmt_payment_cancel = $con->prepare($sql_payment_cancel);
    $stmt_payment_cancel->bind_param("i", $cancel_id);

    if ($stmt_cancel->execute() && $stmt_payment_cancel->execute()) {
        $cancel_success = true;
    } else {
        $cancel_error = true;
    }
    $stmt_cancel->close();
    $stmt_payment_cancel->close();
}

$type = 0;
if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

// Fetch booking history for the logged-in user (only medical appointments)
if ($type) {
    $sql_history = "SELECT a.id, a.appointment_date, a.appointment_time, a.additional_info, s.name_service, s.price, s.type, p.pet_name, a.status, pay.amount, pay.payment_method, pay.payment_status, s.id_service
                FROM appointments a
                JOIN services s ON a.service = s.id_service
                JOIN user_pets p ON a.pet_id = p.id
                LEFT JOIN payments pay ON a.id = pay.appointment_id
                WHERE a.user_id = ? AND s.type = " . $type . " 
                ORDER BY a.id DESC";  // Sort by appointment ID in descending order
} else {
    $sql_history = "SELECT a.id, a.appointment_date, a.appointment_time, a.additional_info, s.name_service, s.price, s.type, p.pet_name, a.status, pay.amount, pay.payment_method, pay.payment_status, s.id_service
                FROM appointments a
                JOIN services s ON a.service = s.id_service
                JOIN user_pets p ON a.pet_id = p.id
                LEFT JOIN payments pay ON a.id = pay.appointment_id
                WHERE a.user_id = ?
                ORDER BY a.id DESC";  // Sort by appointment ID in descending order
}

$stmt_history = $con->prepare($sql_history);
$stmt_history->bind_param("i", $user_id);
$stmt_history->execute();
$stmt_history->store_result(); // Store the result to check if there are any rows
$stmt_history->bind_result($appointment_id, $appointment_date, $appointment_time, $additional_info, $service_name, $price, $s_type, $pet_name, $status, $amount, $payment_method, $payment_status, $service_id);
?>

<div class="main-content">
    <div class="container d-flex">
        <?php require('sidebar.php'); ?>

        <div class="content">
            <?php if ($type == 2) { ?>
                <h3>Service Schedule History</h3>
            <?php } else if ($type == 1) { ?>
                <h3>Medical Examination Booking History</h3>
            <?php } ?>

            <?php
            if ($cancel_success) {
                echo "<div class='alert alert-success' id='cancel-alert'>Lịch hẹn đã được hủy thành công.</div>";
            } elseif ($cancel_error) {
                echo "<div class='alert alert-danger' id='cancel-alert'>Lỗi xảy ra khi hủy lịch hẹn. Vui lòng thử lại.</div>";
            }
            ?>

            <script>
                // Automatically hide alert after 3 seconds
                setTimeout(function() {
                    var alert = document.getElementById('cancel-alert');
                    if (alert) {
                        alert.style.display = 'none';
                    }
                }, 3000);
            </script>

            <?php if ($stmt_history->num_rows > 0) { // Check if there are results 
            ?>
                <div class="card mx-5" style="width: 100%; max-width: 800px;">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Service</th>
                                    <th>Pet</th>
                                    <?php if ($type == 2) { ?>
                                        <th>Price</th>
                                    <?php } ?>
                                    <th>Status</th>
                                    <th>Operation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($stmt_history->fetch()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appointment_date); ?></td>
                                        <td><?php echo htmlspecialchars($service_name); ?></td>
                                        <td><?php echo htmlspecialchars($pet_name); ?></td>
                                        <?php if ($type == 2) { ?>
                                            <td><?php echo number_format($price); ?> VND</td>
                                        <?php } ?>
                                        <td><?php echo htmlspecialchars($status); ?></td>
                                        <td>
                                            <a href="view_appointment.php?id=<?php echo $appointment_id; ?>" class="btn d_btn btn-info btn-sm">Details</a>
                                            <?php if ($status !== 'canceled') { ?>
                                                <a href="appointment_history.php?cancel_id=<?php echo $appointment_id; ?>" class="btn d_btn btn-danger btn-sm">Cancel</a>
                                                <a href="rate_appointment.php?id=<?php echo $appointment_id; ?>&service_id=<?php echo $service_id; ?>&type=<?php echo $s_type;?>" class="btn d_btn btn-warning btn-sm">Evaluate</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } else { ?>
                <p>There is no booking history to display.</p>
            <?php } ?>
        </div>
    </div>
</div>

<?php
$stmt_history->close();
require('footer.php');
?>
