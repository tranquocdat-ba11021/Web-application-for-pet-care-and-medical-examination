<?php
require('headadmin.php');   

// Fetch all doctors
$sql_doctors = "SELECT id_doctor, name_doctor FROM doctor";
$result_doctors = $con->query($sql_doctors);

// Handle form submission for adding time slots
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slot'])) {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date']; // Add date
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Insert the new time slot into the database
    $sql_insert = "INSERT INTO calendar (doctor_id, date, start_time, end_time) VALUES (?, ?, ?, ?)";
    $stmt_insert = $con->prepare($sql_insert);
    $stmt_insert->bind_param('isss', $doctor_id, $date, $start_time, $end_time);
    $stmt_insert->execute();
    $stmt_insert->close();

    // Redirect to avoid resubmission
    header('Location: manage_time_slots.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Time Slots</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your stylesheet -->
</head>
<body>
    <header>
        <h1>Manage Time Slots</h1>
    </header>
    <main>
        <section>
            <h2>Add New Time Slot</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="doctor_id">Doctor</label>
                    <select id="doctor_id" name="doctor_id" class="form-control" required>
                        <?php while ($doctor = $result_doctors->fetch_assoc()): ?>
                            <option value="<?php echo $doctor['id_doctor']; ?>">
                                <?php echo htmlspecialchars($doctor['name_doctor']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="time" id="start_time" name="start_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <input type="time" id="end_time" name="end_time" class="form-control" required>
                </div>
                <button type="submit" name="add_slot" class="btn btn-primary">Add Time Slot</button>
            </form>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Your Company Name</p>
    </footer>
</body>
</html>
