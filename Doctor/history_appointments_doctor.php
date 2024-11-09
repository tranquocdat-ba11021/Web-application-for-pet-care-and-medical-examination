<?php require('headdoctor.php'); ?>

<body>
    <div class="wrapper">
        <?php require('navbardoctor.php'); ?>

        <div id="content2" class="container mt-4">
            <?php
            $type = 1; // Mặc định chỉ hiển thị lịch sử khám bệnh

            $title = "List of Medical Appointments";

            // Giả sử ID của bác sĩ hiện tại đang đăng nhập được lưu trong biến session
            $doctor_id = $_SESSION['doctor_id']; 
            ?>

            <div class="d-flex justify-content-between mb-4">
                <h2><?php echo $title; ?></h2>
            </div>

            <form class="d-flex flex-wrap align-items-end mb-4" method="GET">
                <input type="hidden" name="type" value="<?php echo $type; ?>">

                <div class="me-2 mb-2">
                    <input class="form-control" type="text" placeholder="Order ID" aria-label="Order ID" name="order_id" value="<?php echo isset($_GET['order_id']) ? $_GET['order_id'] : ''; ?>">
                </div>
                <div class="me-2 mb-2">
                    <input class="form-control" type="date" placeholder="From Date" aria-label="From Date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>">
                </div>
                <div class="me-2 mb-2">
                    <input class="form-control" type="date" placeholder="To Date" aria-label="To Date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>">
                </div>
                <div class="me-2 mb-2">
                    <input class="form-control" type="text" placeholder="Phone (Last 3 digits)" aria-label="Phone" name="phone" value="<?php echo isset($_GET['phone']) ? $_GET['phone'] : ''; ?>">
                </div>
                <div class="me-2 mb-2">
                    <select class="form-select" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'confirmed') ? 'selected' : ''; ?>>confirmed</option>
                        <option value="completed" <?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="canceled" <?php echo (isset($_GET['status']) && $_GET['status'] == 'canceled') ? 'selected' : ''; ?>>canceled</option>
                    </select>
                </div>
                <div class="mb-2">
                    <button class="btn btn-success" type="submit" style="background-color:#0d6efd">Search</button>
                </div>
                <div class="mb-2">
                    <a class="btn btn-secondary ms-2" href="?type=<?php echo $type; ?>">Reset</a>
                </div>
            </form>

            <?php
            $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
            $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
            $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';
            $phone = isset($_GET['phone']) ? $_GET['phone'] : '';
            $status = isset($_GET['status']) ? $_GET['status'] : '';

            $appointments_per_page = 7;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $appointments_per_page;

            // Truy vấn để đếm tổng số lịch sử khám bệnh của bác sĩ hiện tại
            $total_appointments_sql = "SELECT COUNT(*) FROM appointments a
                                       JOIN registered_users u ON a.user_id = u.id
                                       JOIN services s ON a.service = s.id_service
                                       WHERE s.type = 1 AND a.doctor_id = '" . $con->real_escape_string($doctor_id) . "'";

            if ($order_id != '') {
                $total_appointments_sql .= " AND a.id = '" . $con->real_escape_string($order_id) . "'";
            }

            if ($from_date != '') {
                $total_appointments_sql .= " AND a.appointment_date >= '" . $con->real_escape_string($from_date) . "'";
            }

            if ($to_date != '') {
                $total_appointments_sql .= " AND a.appointment_date <= '" . $con->real_escape_string($to_date) . "'";
            }

            if ($phone != '') {
                $total_appointments_sql .= " AND RIGHT(u.phone, 3) = '" . $con->real_escape_string($phone) . "'";
            }

            if ($status != '') {
                $total_appointments_sql .= " AND a.status = '" . $con->real_escape_string($status) . "'";
            }

            $total_appointments_result = $con->query($total_appointments_sql);
            $total_appointments = $total_appointments_result->fetch_row()[0];
            $total_pages = ceil($total_appointments / $appointments_per_page);

            // Truy vấn để lấy danh sách lịch sử khám bệnh của bác sĩ hiện tại
            $sql_history = "SELECT a.id, u.full_name, a.appointment_date, a.appointment_start_time, a.appointment_end_time, s.type_name, a.status
                            FROM appointments a
                            JOIN registered_users u ON a.user_id = u.id
                            JOIN services s ON a.service = s.id_service
                            WHERE s.type = 1 AND a.doctor_id = '" . $con->real_escape_string($doctor_id) . "'";

            if ($order_id != '') {
                $sql_history .= " AND a.id = '" . $con->real_escape_string($order_id) . "'";
            }

            if ($from_date != '') {
                $sql_history .= " AND a.appointment_date >= '" . $con->real_escape_string($from_date) . "'";
            }

            if ($to_date != '') {
                $sql_history .= " AND a.appointment_date <= '" . $con->real_escape_string($to_date) . "'";
            }

            if ($phone != '') {
                $sql_history .= " AND RIGHT(u.phone, 3) = '" . $con->real_escape_string($phone) . "'";
            }

            if ($status != '') {
                $sql_history .= " AND a.status = '" . $con->real_escape_string($status) . "'";
            }

            $sql_history .= " ORDER BY a.appointment_date DESC, a.appointment_start_time DESC LIMIT $appointments_per_page OFFSET $offset";

            $result = $con->query($sql_history);

            if (!$result) {
                die("Invalid query: " . $con->error);
            }
            ?>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Appointment Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Service Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "
                            <tr>
                                <td>{$row['id']}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['appointment_date']}</td>
                                <td>{$row['appointment_start_time']}</td>
                                <td>{$row['appointment_end_time']}</td>
                                <td>{$row['type_name']}</td>
                                <td>{$row['status']}</td>
                                <td>
                                    <a class='btn btn-primary btn-sm' href='view_booking.php?id={$row['id']}'>View</a>
                                </td>
                            </tr>
                        ";
                    }
                    ?>
                </tbody>
            </table>

            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?type=<?= $type ?>&order_id=<?= urlencode($order_id) ?>&from_date=<?= urlencode($from_date) ?>&to_date=<?= urlencode($to_date) ?>&phone=<?= urlencode($phone) ?>&status=<?= urlencode($status) ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="visually-hidden">Previous</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?type=<?= $type ?>&order_id=<?= urlencode($order_id) ?>&from_date=<?= urlencode($from_date) ?>&to_date=<?= urlencode($to_date) ?>&phone=<?= urlencode($phone) ?>&status=<?= urlencode($status) ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?type=<?= $type ?>&order_id=<?= urlencode($order_id) ?>&from_date=<?= urlencode($from_date) ?>&to_date=<?= urlencode($to_date) ?>&phone=<?= urlencode($phone) ?>&status=<?= urlencode($status) ?>&page=<?= $page + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="visually-hidden">Next</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</body>
