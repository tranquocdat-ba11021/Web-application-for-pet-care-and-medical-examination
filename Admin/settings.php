<?php
ob_start();
require('headadmin.php');


$navbar_email = "";
$navbar_phone = "";
$navbar_location = "";
$navbar_logo = "";
$navbar_facebook = "";
$navbar_instagram = "";
$navbar_twitter = "";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $navbar_email = $_POST["navbar_email"];
    $navbar_phone = $_POST["navbar_phone"];
    $navbar_location = $_POST["navbar_location"];
    $navbar_facebook = $_POST["navbar_facebook"];
    $navbar_instagram = $_POST["navbar_instagram"];
    $navbar_twitter = $_POST["navbar_twitter"];
    
    if (isset($_FILES['navbar_logo_file']) && $_FILES['navbar_logo_file']['error'] == UPLOAD_ERR_OK) {
        $logo_tmp_name = $_FILES['navbar_logo_file']['tmp_name'];
        $logo_name = basename($_FILES['navbar_logo_file']['name']);
        $logo_path = '../uploads/logo/' . $logo_name;
        if (move_uploaded_file($logo_tmp_name, $logo_path)) {
            $navbar_logo = $logo_path;
        } else {
            $errorMessage = "Failed to upload logo.";
        }
    } else {
        $navbar_logo = $_POST["navbar_logo_url"];
    }

    $sql = "UPDATE settings 
            SET value = CASE `key`
                WHEN 'navbar_email' THEN '$navbar_email'
                WHEN 'navbar_phone' THEN '$navbar_phone'
                WHEN 'navbar_location' THEN '$navbar_location'
                WHEN 'navbar_logo' THEN '$navbar_logo'
                WHEN 'navbar_facebook' THEN '$navbar_facebook'
                WHEN 'navbar_instagram' THEN '$navbar_instagram'
                WHEN 'navbar_twitter' THEN '$navbar_twitter'
            END
            WHERE `key` IN ('navbar_email', 'navbar_phone', 'navbar_location', 'navbar_logo', 'navbar_facebook', 'navbar_instagram', 'navbar_twitter')";
    if ($con->query($sql) === TRUE) {
        $successMessage = "Settings updated successfully.";
        header("Location: settings.php");
        exit;
    } else {
        $errorMessage = "Error: " . $sql . "<br>" . $con->error;
    }
} else {
    $sql = "SELECT `key`, `value` FROM settings WHERE `key` IN ('navbar_email', 'navbar_phone', 'navbar_location', 'navbar_logo', 'navbar_facebook', 'navbar_instagram', 'navbar_twitter')";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ${$row['key']} = $row['value'];
        }
    } else {
        $errorMessage = "Settings not found!";
    }
}

$con->close();
ob_end_flush(); 
?>

<body>
<div class="wrapper">
    <?php require('navbaradmin.php'); ?>

    <div id="content2">
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

        <h2>Update Settings</h2>
        <form action="settings.php" method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email Page</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="navbar_email" value="<?= htmlspecialchars($navbar_email) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Phone Page</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="navbar_phone" value="<?= htmlspecialchars($navbar_phone) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Location Page</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="navbar_location" value="<?= htmlspecialchars($navbar_location) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Logo Page (URL or Upload)</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="navbar_logo_url" value="<?= htmlspecialchars($navbar_logo) ?>">
                    <input type="file" class="form-control mt-2" name="navbar_logo_file">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Facebook Link</label>
                <div class="col-sm-6">
                    <input type="url" class="form-control" name="navbar_facebook" value="<?= htmlspecialchars($navbar_facebook) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Instagram Link</label>
                <div class="col-sm-6">
                    <input type="url" class="form-control" name="navbar_instagram" value="<?= htmlspecialchars($navbar_instagram) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Twitter Link</label>
                <div class="col-sm-6">
                    <input type="url" class="form-control" name="navbar_twitter" value="<?= htmlspecialchars($navbar_twitter) ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
