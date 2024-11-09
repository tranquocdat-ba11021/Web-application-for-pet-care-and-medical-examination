<?php require('headadmin.php') ?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <h2>Manage Users</h2>
            <?php
            if (isset($_GET['message']) && $_GET['message'] == 'update_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    update successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            if (isset($_GET['message']) && $_GET['message'] == 'add_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Added successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            if (isset($_GET['message']) && $_GET['message'] == 'delete_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Post deleted successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            ?>
            <div class="row mb-3">
                <!-- <div class="col">
                    <a href="add_user.php" class="btn btn-primary mt-3">Add Customer</a>
                </div> -->
                <!-- Thêm chức năng tìm kiếm -->
                <div class="col-md-6">
                    <form method="GET" action="user.php" class="d-flex align-items-center">
                        <input type="text" class="form-control mt-3 me-2" name="search" placeholder="Search by Full Name or Phone" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn mt-3 me-2" type="submit" style="background-color: #0d6efd; color: white; border: none; width: 100px; padding: 5px;">Search</button>
                        <a href="user.php" class="btn mt-3" style="background-color: #6c757d; color: white; text-decoration: none; width: 80px; padding: 5px; text-align: center; display: inline-block;">Reset</a>
                    </form>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Phone</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Xử lý chức năng tìm kiếm
                    $search = isset($_GET['search']) ? $_GET['search'] : '';

                    // Truy vấn cơ sở dữ liệu từ bảng registered_users với điều kiện tìm kiếm, loại trừ admin
                    $sql = "SELECT * FROM `registered_users` WHERE `role` != 0 AND (`full_name` LIKE ? OR `phone` LIKE ?)";
                    $stmt = $con->prepare($sql);
                    $search_param = "%" . $search . "%";
                    $stmt->bind_param("ss", $search_param, $search_param);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if (!$result) {
                        die("Invalid query: " . $con->error);
                    }

                    // Hiển thị dữ liệu cho từng dòng
                    while ($row = $result->fetch_assoc()) {
                        echo "
                    <tr>
                        <td>{$row['id']}</td>
                        <td>{$row['full_name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['phone']}</td>
                        <td><img src='../uploads/user/{$row['image_url']}' style='width: 50px; height: 50px;' alt='User Image'></td>
                        <td>";
                        echo "<a class='btn btn-warning btn-sm' href='edit_user.php?id={$row['id']}'>View</a> ";
                        echo "<a class='btn btn-danger btn-sm' href='delete_user.php?id={$row['id']}'>Delete</a>";
                        echo "</td>
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