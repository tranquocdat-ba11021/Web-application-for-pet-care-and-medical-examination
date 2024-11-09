<?php
require('headadmin.php');



// Lấy danh sách bác sĩ từ bảng doctor
$sql = "SELECT id_doctor, name_doctor FROM doctor";
$doctors = $con->query($sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Chèn khung giờ vào bảng calendar
    $stmt = $con->prepare("INSERT INTO calendar (doctor_id, date, start_time, end_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $doctor_id, $date, $start_time, $end_time);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $successMessage = "Calendar has been updated successfully!";
    } else {
        $errorMessage = "An error occurred while updating the calendar.!";
    }

    $stmt->close();
}

$con->close();
?>

<div class="wrapper">
    <?php require('navbaradmin.php') ?>
    <div id="content2">
        <div class="container">
            <h2>Set Schedule for Doctors</h2>
            
            <?php
            if (!empty($errorMessage)) {
                echo "
                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    <strong>$errorMessage</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
                ";
            }

            if (!empty($successMessage)) {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <strong>$successMessage</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
                ";
            }
            ?>
            
            <form method="POST">
                <!-- Select Doctor -->
                <div class="mb-3">
                    <label for="doctor_id" class="form-label">Select Doctor</label>
                    <select class="form-control" id="doctor_id" name="doctor_id" required>
                        <?php while ($row = $doctors->fetch_assoc()): ?>
                            <option value="<?php echo $row['id_doctor']; ?>"><?php echo $row['name_doctor']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Select Date -->
                <div class="mb-3">
                    <label for="date" class="form-label">Select Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>

                <!-- Select Start Time -->
                <div class="mb-3">
                    <label for="start_time" class="form-label">Select Start Time</label>
                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                </div>

                <!-- Select End Time -->
                <div class="mb-3">
                    <label for="end_time" class="form-label">Select End Time</label>
                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                </div>

                <button type="submit" class="btn btn-primary">Set Schedule</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
