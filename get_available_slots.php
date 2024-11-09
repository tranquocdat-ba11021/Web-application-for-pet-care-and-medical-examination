<?php
require('./connection.php'); // Ensure this points to your database connection script

$date = isset($_GET['date']) ? $_GET['date'] : '';
$doctor_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0;

$available_slots = [];

// Query to get working hours for the selected doctor and date
$sql_working_hours = "SELECT start_time, end_time FROM calendar WHERE doctor_id = ? AND date = ?";
$stmt_working_hours = $con->prepare($sql_working_hours);
$stmt_working_hours->bind_param('is', $doctor_id, $date);
$stmt_working_hours->execute();
$result_working_hours = $stmt_working_hours->get_result();

while ($row = $result_working_hours->fetch_assoc()) {
    $start_time = new DateTime($row['start_time']);
    $end_time = new DateTime($row['end_time']);
    $interval = new DateInterval('PT1H'); // 1-hour intervals

    // Generate time slots in 1-hour intervals
    for ($time = $start_time; $time < $end_time; $time->add($interval)) {
        $end_slot = clone $time;
        $end_slot->add($interval);

        // Ensure that end_slot does not exceed the actual end_time
        if ($end_slot > $end_time) {
            $end_slot = $end_time;
        }

        $available_slots[] = $time->format('H:i') . ' - ' . $end_slot->format('H:i');
    }
}

$stmt_working_hours->close();

// Filter out already booked slots from the appointments table
$sql_booked = "SELECT appointment_start_time, appointment_end_time FROM appointments WHERE doctor_id = ? AND appointment_date = ?";
$stmt_booked = $con->prepare($sql_booked);
$stmt_booked->bind_param('is', $doctor_id, $date);
$stmt_booked->execute();
$result_booked = $stmt_booked->get_result();

$booked_slots = [];
while ($row_booked = $result_booked->fetch_assoc()) {
    $booked_start_time = new DateTime($row_booked['appointment_start_time']);
    $booked_end_time = new DateTime($row_booked['appointment_end_time']);
    $booked_time_str = $booked_start_time->format('H:i') . ' - ' . $booked_end_time->format('H:i');

    // Add booked time slots to an array for comparison
    $booked_slots[] = $booked_time_str;
}

$stmt_booked->close();

// Remove booked time slots from available slots
$available_slots = array_filter($available_slots, function($slot) use ($booked_slots) {
    list($slot_start, $slot_end) = explode(' - ', $slot);
    foreach ($booked_slots as $booked_time_str) {
        list($booked_start, $booked_end) = explode(' - ', $booked_time_str);
        if (($slot_start >= $booked_start && $slot_start < $booked_end) || ($slot_end > $booked_start && $slot_end <= $booked_end)) {
            return false; // Slot is booked
        }
    }
    return true; // Slot is available
});

echo json_encode(array_values($available_slots));
?>
