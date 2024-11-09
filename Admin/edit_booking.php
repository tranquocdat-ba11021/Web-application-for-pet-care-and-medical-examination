<?php
require('headadmin.php');
ob_start();
$update_success = false; // Initialize the variable

?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php'); ?>

        <div id="content2">
            <?php
            $appointment_id = intval($_GET['id']);

            // Fetch the booking details including payment information
            $sql = "SELECT a.id, u.full_name, u.email, u.phone, p.pet_name, s.name_service, a.appointment_date, a.appointment_start_time, a.appointment_end_time, a.appointment_time, a.additional_info, s.price, a.status,
            pay.payment_method, pay.payment_status, pay.amount, d.name_doctor, s.type AS service_type, a.doctor_note
            FROM appointments a
            JOIN registered_users u ON a.user_id = u.id
            JOIN user_pets p ON a.pet_id = p.id
            JOIN services s ON a.service = s.id_service
            LEFT JOIN payments pay ON a.id = pay.appointment_id
            LEFT JOIN doctor d ON a.doctor_id = d.id_doctor
            WHERE a.id = ?";

            $stmt = $con->prepare($sql);
            if ($stmt === false) {
                die("Error preparing statement: " . $con->error);
            }
            $stmt->bind_param('i', $appointment_id);
            $stmt->execute();
            $stmt->bind_result($id, $full_name, $email, $phone, $pet_name, $name_service, $appointment_date, $appointment_start_time, $appointment_end_time, $appointment_time, $additional_info, $price, $status, $payment_method, $payment_status, $amount, $name_doctor, $service_type, $doctor_note);
            $stmt->fetch();
            $stmt->close();

            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $new_status = $_POST['status'];
                $new_payment_status = $_POST['payment_status'];
                $new_price = $_POST['price']; // New price value

                // Start transaction
                $con->begin_transaction();

                try {
                    // Update booking status
                    $sql_update_status = "UPDATE appointments SET status = ? WHERE id = ?";
                    $stmt_update_status = $con->prepare($sql_update_status);
                    if ($stmt_update_status === false) {
                        throw new Exception("Error preparing status update statement: " . $con->error);
                    }
                    $stmt_update_status->bind_param('si', $new_status, $appointment_id);
                    $stmt_update_status->execute();
                    $stmt_update_status->close();

                    // Update payment status and amount
                    $sql_update_payment_status = "UPDATE payments SET payment_status = ?, amount = ? WHERE appointment_id = ?";
                    $stmt_update_payment_status = $con->prepare($sql_update_payment_status);
                    if ($stmt_update_payment_status === false) {
                        throw new Exception("Error preparing payment status update statement: " . $con->error);
                    }
                    $stmt_update_payment_status->bind_param('sdi', $new_payment_status, $new_price, $appointment_id);
                    $stmt_update_payment_status->execute();
                    $stmt_update_payment_status->close();

                    // Commit transaction
                    $con->commit();
                    $update_success = true;

                    // Fetch updated details
                    $stmt = $con->prepare($sql);
                    if ($stmt === false) {
                        die("Error preparing statement: " . $con->error);
                    }
                    $stmt->bind_param('i', $appointment_id);
                    $stmt->execute();
                    $stmt->bind_result($id, $full_name, $email, $phone, $pet_name, $name_service, $appointment_date, $appointment_start_time, $appointment_end_time, $appointment_time, $additional_info, $price, $status, $payment_method, $payment_status, $amount, $name_doctor, $service_type, $doctor_note);
                    $stmt->fetch();
                    $stmt->close();
                } catch (Exception $e) {
                    // Rollback transaction if there's an error
                    $con->rollback();
                    die("Error updating record: " . $e->getMessage());
                }
            }
            ob_end_flush();
            ?>

            <h2>Edit Booking Status</h2>
            <?php if ($update_success) {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                     update successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            ?>
            <form action="" method="POST">
                <div class="row">
                    <!-- Customer Information Column -->
                    <div class="col-md-6 mb-3">
                        <h3>Customer Information</h3>
                        <div class="row">
                            <!-- Column 1 -->
                            <div class="col-md-6">
                                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($full_name ?? ''); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($email ?? ''); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone ?? ''); ?></p>
                            </div>
                            <!-- Column 2 -->
                            <div class="col-md-6">
                                <p><strong>Pet Name:</strong> <?php echo htmlspecialchars($pet_name ?? ''); ?></p>
                                <p><strong>Service Name:</strong> <?php echo htmlspecialchars($name_service ?? ''); ?></p>
                                <?php if (!empty($name_doctor)) : ?>
                                    <p><strong>Doctor Name:</strong> <?php echo htmlspecialchars($name_doctor ?? ''); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Column 1 -->
                            <div class="col-md-6">
                                <p><strong>Appointment Date:</strong> <?php echo htmlspecialchars($appointment_date ?? ''); ?></p>
                                <?php if ($service_type == 1) : ?>
                                    <p><strong>Start Time:</strong> <?php echo htmlspecialchars($appointment_start_time ?? ''); ?></p>
                                    <p><strong>End Time:</strong> <?php echo htmlspecialchars($appointment_end_time ?? ''); ?></p>
                                    <!-- Hide Appointment Time for service_type 1 -->
                                <?php else : ?>
                                    <!-- Show Appointment Time for other types -->
                                    <p><strong>Appointment Time:</strong> <?php echo htmlspecialchars($appointment_time ?? ''); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Column 2 -->
                            <div class="col-md-6">
                                <p><strong>Additional Info:</strong> <?php echo htmlspecialchars($additional_info ?? ''); ?></p>
                                <?php
                                // Định dạng số tiền và loại bỏ .00 nếu không cần thiết
                                $formatted_amount = number_format($amount, 2);
                                if (strpos($formatted_amount, '.00') !== false) {
                                    $formatted_amount = rtrim($formatted_amount, '0');
                                    $formatted_amount = rtrim($formatted_amount, '.');
                                }
                                ?>
                                <p><strong>Current Amount:</strong> <?php echo $formatted_amount; ?> VND</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label"><strong>Status:</strong></label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $status === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="canceled" <?php echo $status === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                            </select>
                        </div>

                    </div>

                    <!-- Payment Information Column -->
                    <div class="col-md-6 mb-3">
                        <h3>Payment Information</h3>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment_method ?? 'Offline'); ?></p>
                        <p><strong>Payment Status:</strong> <span id="payment-status"><?php echo htmlspecialchars($payment_status ?? 'Pending'); ?></span></p>
                        <p><strong>Amount:</strong> <?php echo $formatted_amount; ?> VND</p> <!-- Display the amount -->

                        <!-- Doctor Notes -->


                        <div class="mb-3">
                            <label for="payment_status" class="form-label"><strong>Update Payment Status</strong></label>
                            <select name="payment_status" id="payment_status" class="form-control">
                                <option value="pending" <?php echo $payment_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="completed" <?php echo $payment_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="failed" <?php echo $payment_status === 'failed' ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label"><strong>Update Price:</strong></label>
                            <input type="number" name="price" id="price" class="form-control" value="<?php echo htmlspecialchars($formatted_amount ?? ''); ?>" step="0.01" min="0"> <!-- Use formatted_amount here -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="doctor_note" class="form-label"><strong>Doctor's Notes:</strong></label>
                        <textarea class="form-control" id="doctor_note" name="doctor_note
                            " readonly><?php echo htmlspecialchars($doctor_note ?? ''); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
        </div>
    </div>

    <!-- <script>
        function reloadPaymentStatus(appointmentId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "../fetch_payment_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        document.getElementById('payment-status').innerText = response.payment_status;
                    } else {
                        console.error('Error fetching payment status:', response.error);
                    }
                }
            };
            xhr.send("appointment_id=" + encodeURIComponent(appointmentId));
        }
    </script> -->

</body>

</html>