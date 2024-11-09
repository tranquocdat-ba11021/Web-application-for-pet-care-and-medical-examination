<?php
ob_start();
require('headadmin.php');
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php'); ?>

        <div id="content2">
            <?php
            // Initialize variables
            $full_name = "";
            $email = "";
            $username = "";
            $phone = "";
            $image_url = "";
            $password = "";
            $errorMessage = "";
            $successMessage = "";

            // Check if form data is submitted
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Get values from the form
                $full_name = $_POST["full_name"];
                $email = $_POST["email"];
                $username = $_POST["username"];
                $phone = $_POST["phone"];
                $password = $_POST["password"];

                // Handle file upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['image']['tmp_name'];
                    $fileName = $_FILES['image']['name'];
                    $fileSize = $_FILES['image']['size'];
                    $fileType = $_FILES['image']['type'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $uploadFileDir = '../uploads/user/';
                        $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
                        $dest_path = $uploadFileDir . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            $image_url = $newFileName;
                        } else {
                            $errorMessage = "There was an error moving the uploaded file.";
                        }
                    } else {
                        $errorMessage = "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
                    }
                }

                // Check if any fields are empty
                do {
                    if (empty($full_name) || empty($email) || empty($username) || empty($phone) || empty($password)) {
                        $errorMessage = "All the fields are required";
                        break;
                    }

                    // Hash the password
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                    // Insert user into the database with default role as User (role = 1)
                    $sql = "INSERT INTO registered_users (full_name, username, image_url, email, phone, password, role, is_verified)
                            VALUES ('$full_name', '$username', '$image_url', '$email', '$phone', '$hashed_password', 1, 1)";

                    // Execute the query
                    $result = $con->query($sql);

                    if (!$result) {
                        $errorMessage = "Invalid query: " . $con->error;
                        break;
                    }

                    // Clear input fields
                    $full_name = "";
                    $email = "";
                    $username = "";
                    $phone = "";
                    $image_url = "";
                    $password = "";

                    $successMessage = "User added correctly";
                    header("Location:user.php?message=add_success");
                    exit;
                } while (false);
            }
            ob_end_flush();
            ?>

            <?php
            if (!empty($errorMessage)) {
                echo "
                <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                    <strong>$errorMessage</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
                ";
            }
            ?>

            <h2>Add New User</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Full Name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="full_name" value="<?php echo $full_name; ?>"required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-6">
                        <input type="email" class="form-control" name="email" value="<?php echo $email; ?>"required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Username</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="username" value="<?php echo $username; ?>"required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Phone</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>"required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" name="password" value="<?php echo $password; ?>"required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Image</label>
                    <div class="col-sm-6">
                        <input type="file" class="form-control" name="image"required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="offset-sm-3 col-sm-3 d-grid">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php require('footeradmin.php'); ?>

