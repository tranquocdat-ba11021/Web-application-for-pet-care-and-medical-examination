<?php require('headadmin.php');
ob_start(); ?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <h2>Manage Services</h2>
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
                    deleted successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            ?>
            <div class="row mb-3">
                <div class="col">
                    <a href="add_services.php" class="btn btn-primary mt-3">Add New Services</a>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="manage_services.php" class="d-flex align-items-center">
                        <input type="text" class="form-control mt-3 me-2" name="search" placeholder="Search by Name or Type (number)" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn mt-3 me-2" type="submit" style="background-color: #0d6efd; color: white; border: none; width: 100px; padding: 5px;">Search</button>
                        <a href="manage_services.php" class="btn mt-3" style="background-color: #6c757d; color: white; text-decoration: none; width: 80px; padding: 5px; text-align: center; display: inline-block;">Reset</a>
                    </form>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Title Content</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Xử lý chức năng tìm kiếm
                    // Xử lý chức năng tìm kiếm
                    $search = isset($_GET['search']) ? $_GET['search'] : '';

                    // Truy vấn SQL để lấy dữ liệu từ bảng 'services' với điều kiện tìm kiếm
                    $sql = "SELECT * FROM `services` WHERE `name_service` LIKE ? OR `type` LIKE ?";
                    $stmt = $con->prepare($sql);
                    $search_param = "%" . $search . "%";
                    $stmt->bind_param("ss", $search_param, $search_param);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Hàm để giới hạn số từ trong phần mô tả và title
                    function limit_words($text, $limit)
                    {
                        $words = explode(' ', $text);
                        if (count($words) > $limit) {
                            return implode(' ', array_slice($words, 0, $limit)) . '...';
                        }
                        return $text;
                    }

                    // Kiểm tra nếu có dữ liệu trả về
                    if ($result && $result->num_rows > 0) {
                        // Lặp qua các hàng kết quả và hiển thị dữ liệu trong bảng
                        while ($row = $result->fetch_assoc()) {
                            // Hiển thị tên loại dịch vụ dựa trên giá trị của 'type'
                            $type_name = ($row['type'] == 1) ? 'examination' : 'Services';

                            // Giới hạn phần mô tả chỉ 70 từ
                            $description_limited = limit_words($row['description'], 05);

                            // Giới hạn phần title_content chỉ 30 từ
                            $title_limited = limit_words($row['title_content'], 04);

                            echo "
                                <tr>
                                    <td>" . $row['id_service'] . "</td>
                                    <td>" . $row['name_service'] . "</td>
                                    <td>" . $type_name . "</td>
                                    <td>" . $description_limited . "</td>
                                    <td>" . $title_limited . "</td>
                                    <td><img src='../uploads/Services/" . $row['image_url'] . "' alt='' width='100'></td>
                                    <td>" . ($row['price'] ? $row['price'] : 'N/A') . "</td>
                                <td>
                                    <a href='edit_services.php?id=" . $row['id_service'] . "' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_services.php?id=" . $row['id_service'] . "' class='btn btn-danger btn-sm'>Delete</a>
                                </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No services found.</td></tr>";
                    }

                    // Đóng kết nối đến cơ sở dữ liệu
                    $con->close();



                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require('footeradmin.php') ?>