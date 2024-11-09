<?php
require('headadmin.php');

// Check if pet ID is passed and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Pet ID");
}

$pet_id = intval($_GET['id']);

// Fetch pet details
$sql = "SELECT up.id AS pet_id, ru.full_name AS owner_name, up.pet_name, up.pet_type, up.pet_gender, up.pet_age, up.pet_description
        FROM user_pets up
        LEFT JOIN registered_users ru ON up.user_id = ru.id
        WHERE up.id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Pet not found");
}

$pet = $result->fetch_assoc();
$stmt->close();
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <h2>View Pet Details</h2>
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Owner: <?php echo htmlspecialchars($pet['owner_name']); ?></h4>
                </div>
                <div class="card-body">
                    <p><strong>Pet Name:</strong> <?php echo htmlspecialchars($pet['pet_name']); ?></p>
                    <p><strong>Pet Type:</strong> <?php echo htmlspecialchars($pet['pet_type']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($pet['pet_gender']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['pet_age']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($pet['pet_description']); ?></p>
                </div>
                <div class="card-footer">
                    <a href="Pets.php" class="btn btn-secondary">Back to Pets</a>
                    <a href="delete_pets.php?id=<?php echo $pet['pet_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this pet?')">Delete Pet</a>
                </div>
            </div>
        </div>
    </div>

    <?php require('footeradmin.php') ?>
</body>
