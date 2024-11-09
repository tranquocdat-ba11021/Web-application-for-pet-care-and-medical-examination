<?php 
session_start(); // Start session

require('connection.php'); // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Check if pet ID exists
if (!isset($_GET['id'])) {
    header('Location: inforpets.php');
    exit;
}

$pet_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Begin transaction
$con->begin_transaction();

try {
    // Set pet_id to NULL in appointments
    $sql_update_appointments = "UPDATE appointments SET pet_id = NULL WHERE pet_id = ?";
    $stmt = $con->prepare($sql_update_appointments);
    $stmt->bind_param('i', $pet_id);
    $stmt->execute();
    $stmt->close();

    // Delete pet record
    $sql_delete_pet = "DELETE FROM user_pets WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($sql_delete_pet);
    $stmt->bind_param('ii', $pet_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Commit transaction
    $con->commit();

    // Redirect after successful deletion
    header('Location: inforpets.php');
    exit;
} catch (Exception $e) {
    // Rollback transaction if there's an error
    $con->rollback();
    echo "Error: " . $e->getMessage();
}
?>
