<?php
ob_start();
require('head.php');

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Check if pet ID is provided
if (!isset($_GET['id'])) {
    header('Location: inforpets.php');
    exit;
}

$pet_id = $_GET['id'];

// Fetch pet information from the database
$sql_select = "SELECT * FROM user_pets WHERE id = ? AND user_id = ?";
$stmt = $con->prepare($sql_select);
$stmt->bind_param('ii', $pet_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Pet not found.";
    exit;
}

$pet = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $pet_name = $_POST['pet_name'];
    $pet_type = $_POST['pet_type'];
    $pet_gender = $_POST['pet_gender'];
    $pet_age = $_POST['pet_age'];
    $pet_description = $_POST['pet_description'];

    // Prepare and execute the update query
    $sql_update = "UPDATE user_pets SET pet_name = ?, pet_type = ?, pet_gender = ?, pet_age = ?, pet_description = ? WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($sql_update);
    $stmt->bind_param('sssssii', $pet_name, $pet_type, $pet_gender, $pet_age, $pet_description, $pet_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        // Redirect to pet information page after successful update
        header('Location: inforpets.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
ob_end_flush();
?>

<div class="main-content">
    <div class="container d-flex">
        <!-- SIDEBAR -->
        <?php require('sidebar.php'); ?>

        <!-- CONTENT -->
        <div class="content">
            <h3>Edit Pet</h3>
            <div class="card mx-5" style="width: 100%; max-width: 800px;">
                <div class="card-body">
                    <form action="edit_petsuser.php?id=<?php echo $pet_id; ?>" method="POST">
                        <div class="row mb-4">
                            <label for="pet_name" class="col-sm-2 col-form-label">Pet Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pet_name" name="pet_name" value="<?php echo htmlspecialchars($pet['pet_name']); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="pet_type" class="col-sm-2 col-form-label">Pet Type</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="pet_type" name="pet_type" required>
                                    <option value=""></option>
                                    <option value="dog" <?php if($pet['pet_type'] == 'dog') echo 'selected'; ?>>Dog</option>
                                    <option value="cat" <?php if($pet['pet_type'] == 'cat') echo 'selected'; ?>>Cat</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="pet_gender" class="col-sm-2 col-form-label">Pet Gender</label>
                            <div class="col-sm-10">
                                <select class="form-select" id="pet_gender" name="pet_gender" required>
                                    <option value=""></option>
                                    <option value="male" <?php if($pet['pet_gender'] == 'male') echo 'selected'; ?>>Male</option>
                                    <option value="female" <?php if($pet['pet_gender'] == 'female') echo 'selected'; ?>>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="pet_age" class="col-sm-2 col-form-label">Pet Age</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pet_age" name="pet_age" value="<?php echo htmlspecialchars($pet['pet_age']); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="pet_description" class="col-sm-2 col-form-label">Pet Description</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="pet_description" name="pet_description" rows="3" required><?php echo htmlspecialchars($pet['pet_description']); ?></textarea>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="inforpets.php" class="btn btn-secondary">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>
