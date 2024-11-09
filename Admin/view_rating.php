<?php
require('headadmin.php');

// Check if rating ID is passed and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Rating ID");
}

$rating_id = intval($_GET['id']);

// Fetch rating details
$sql = "SELECT r.id AS rating_id, r.user_name, s.name_service, r.rating, r.comment, r.appointment_id, a.appointment_date, a.appointment_time
        FROM ratings r
        LEFT JOIN services s ON r.service_id = s.id_service
        LEFT JOIN appointments a ON r.appointment_id = a.id
        WHERE r.id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $rating_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Rating not found");
}

$rating = $result->fetch_assoc();
$stmt->close();
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <h2>View Rating Details</h2>
            <div class="card mb-4">
                <div class="card-header">
                    <h4>User: <?php echo htmlspecialchars($rating['user_name']); ?></h4>
                </div>
                <div class="card-body">
                    <p><strong>Service:</strong> <?php echo htmlspecialchars($rating['name_service']); ?></p>
                    <p><strong>Rating:</strong> <?php echo $rating['rating']; ?> / 5</p>
                    <p><strong>Comment:</strong> <?php echo htmlspecialchars($rating['comment']); ?></p>
                    <p><strong>Appointment ID:</strong> <?php echo $rating['appointment_id']; ?></p>
                    <p><strong>Appointment Date:</strong> <?php echo $rating['appointment_date']; ?></p>
                    <p><strong>Appointment Time:</strong> <?php echo $rating['appointment_time']; ?></p>
                </div>
                <div class="card-footer">
                    <a href="manage_rating.php" class="btn btn-secondary">Back to Ratings</a>
                    <a href="delete_rating.php?id=<?php echo $rating['rating_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this rating?')">Delete Rating</a>
                </div>
            </div>
        </div>
    </div>

    <?php require('footeradmin.php') ?>
