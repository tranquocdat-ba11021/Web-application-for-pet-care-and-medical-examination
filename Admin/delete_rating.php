<?php
require('headadmin.php');

// Check if rating ID is passed and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Rating ID");
}

$rating_id = intval($_GET['id']);

// Delete rating from the database
$sql = "DELETE FROM ratings WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $rating_id);

if ($stmt->execute()) {
    header('Location: manage_rating.php?message=delete_success');
    exit;
} else {
    echo "Error deleting rating: " . $stmt->error;
}
$stmt->close();
?>
