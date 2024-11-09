<?php
require('headdoctor.php');
ob_start();
?>

<body>
    <div class="wrapper">
        <?php require('navbardoctor.php'); ?>

        <div id="content2" class="container mt-4">
            <?php
            $appointment_id = intval($_GET['id']);

            // Fetch the booking details including payment information
            $sql = "SELECT a.id, u.full_name, u.email, u.phone, p.pet_name, p.pet_type, p.pet_age,p.pet_description	, s.name_service, a.appointment_date, a.appointment_start_time, a.appointment_end_time, a.additional_info, a.doctor_note
            FROM appointments a
            JOIN registered_users u ON a.user_id = u.id
            JOIN user_pets p ON a.pet_id = p.id
            JOIN services s ON a.service = s.id_service
            WHERE a.id = ?";

            $stmt = $con->prepare($sql);
            if ($stmt === false) {
                die("Error preparing statement: " . $con->error);
            }
            $stmt->bind_param('i', $appointment_id);
            $stmt->execute();
            $stmt->bind_result($id, $full_name, $email, $phone, $pet_name, $pet_type, $pet_age,$pet_description	, $name_service, $appointment_date, $appointment_start_time, $appointment_end_time, $additional_info, $doctor_note);
            $stmt->fetch();
            $stmt->close();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $doctor_note = trim($_POST['doctor_note']);

                // Cập nhật ghi chú của bác sĩ vào cơ sở dữ liệu
                $update_sql = "UPDATE appointments SET doctor_note = ? WHERE id = ?";
                $stmt = $con->prepare($update_sql);
                if ($stmt === false) {
                    die("Error preparing statement: " . $con->error);
                }
                $stmt->bind_param('si', $doctor_note, $appointment_id);
                if ($stmt->execute()) {
                    echo "<div id='success-alert' class='alert alert-success alert-dismissible fade show' role='alert'>
                    Note saved successfully!
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
                } else {
                    echo "<div class='alert alert-danger'>Failed to save note. Please try again.</div>";
                }
                $stmt->close();
            }

            ob_end_flush();
            ?>

            <h2>Booking Details</h2>
            <div class="row">
                <!-- Column 1 -->
                <div class="col-md-6 mb-3">
                    <h3>Customer Information</h3>
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($full_name ?? ''); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email ?? ''); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone ?? ''); ?></p>
                    <p><strong>Additional Info:</strong> <?php echo htmlspecialchars($additional_info ?? ''); ?></p>
                    <h3>Pet Information</h3>
                    <p><strong>Pet Name:</strong> <?php echo htmlspecialchars($pet_name ?? ''); ?></p>
                    <p><strong>Pet Type:</strong> <?php echo htmlspecialchars($pet_type ?? ''); ?></p>
                    <p><strong>Pet Age:</strong> <?php echo htmlspecialchars($pet_age ?? ''); ?></p>
                    <p><strong>Pet Description:</strong> <?php echo htmlspecialchars($pet_description  ?? ''); ?></p>
                </div>

                <!-- Column 2 -->
                <div class="col-md-6 mb-3">
                    <h3>Booking Details</h3>
                    <p><strong>Appointment Date:</strong> <?php echo htmlspecialchars($appointment_date ?? ''); ?></p>
                    <p><strong>Start Time:</strong> <?php echo htmlspecialchars($appointment_start_time ?? ''); ?></p>
                    <p><strong>End Time:</strong> <?php echo htmlspecialchars($appointment_end_time ?? ''); ?></p>
                    <p><strong>Service Name:</strong> <?php echo htmlspecialchars($name_service ?? ''); ?></p>
                </div>
            </div>
            <h2>Doctor's Note</h2>
            <form method="post" action="view_booking.php?id=<?php echo $appointment_id; ?>">
                <div class="mb-3">
                    <label for="doctor_note" class="form-label">Enter your note:</label>
                    <textarea class="form-control" id="doctor_note" name="doctor_note" rows="4"><?php echo htmlspecialchars($doctor_note ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Note</button>
            </form>


            <!-- Back Button -->
            <a href="history_appointments_doctor.php" class="btn btn-secondary mt-3">Back</a>
        </div>
    </div>

    <?php require('footerdoctor.php'); ?>
</body>

</html>