<?php require('headadmin.php') ?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <h2>Manage Pets</h2>
            <?php
            if (isset($_GET['message']) && $_GET['message'] == 'delete_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                     deleted successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Pet Name</th>
                        <th>Type</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Truy vấn SQL để lấy thông tin thú cưng và người dùng
                    $sql = "SELECT up.id, ru.full_name, up.pet_name, up.pet_type, up.pet_gender, up.pet_age, up.pet_description
                            FROM user_pets up
                            INNER JOIN registered_users ru ON up.user_id = ru.id";
                    $result = $con->query($sql);

                    if (!$result) {
                        die("Invalid query: " . $con->error);
                    }

                    // Hiển thị dữ liệu cho mỗi thú cưng
                    while ($row = $result->fetch_assoc()) {
                        echo "
                            <tr>
                                <td>{$row['id']}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['pet_name']}</td>
                                <td>{$row['pet_type']}</td>
                                <td>{$row['pet_gender']}</td>
                                <td>{$row['pet_age']}</td>
                                <td>{$row['pet_description']}</td>
                                <td>
                                    <a href='view_pet.php?id={$row['id']}' class='btn btn-info btn-sm'>View</a>
                                    <a href='delete_pets.php?id={$row['id']}' class='btn btn-danger btn-sm'>Delete</a>
                                </td>
                            </tr>
                        ";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require('footeradmin.php') ?>
</body>
