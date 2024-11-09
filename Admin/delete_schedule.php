<?php
require('../connection.php'); // Ensure you have this file for database connection

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $eventId = $data['id'];

    // Prepare and execute delete statement
    $sql = "DELETE FROM calendar WHERE doctor_id = ?"; // Ensure this matches your table structure
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $eventId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}

$con->close();
?>
