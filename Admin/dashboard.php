<?php
include('headadmin.php');

// Query to get the total number of users with role = 1
$sql_users = "SELECT COUNT(*) as total_users FROM `registered_users` WHERE `role` = 1";
$result_users = $con->query($sql_users);
$row_users = $result_users->fetch_assoc();
$total_users = $row_users['total_users'];

// Query to get the total number of orders
$sql_orders = "SELECT COUNT(*) as total_orders FROM `appointments`";
$result_orders = $con->query($sql_orders);
$row_orders = $result_orders->fetch_assoc();
$total_orders = $row_orders['total_orders'];

// SQL query to sum payments based on their status in the payments table
$sql_sales = "SELECT SUM(amount) AS total_sales 
              FROM payments 
              WHERE payment_status = 'completed'";

// Execute the query
$result_sales = $con->query($sql_sales);

// Initialize total_sales
$total_sales = 0; // Default value in case of null result

// Check if the query was successful
if ($result_sales) {
  // Fetch the result
  $row_sales = $result_sales->fetch_assoc();
  $total_sales = $row_sales['total_sales'];
}

// Query to get the number of pending orders
$sql_pending_orders = "SELECT COUNT(*) as pending_orders FROM `appointments` WHERE `status` = 'pending'";
$result_pending_orders = $con->query($sql_pending_orders);
$row_pending_orders = $result_pending_orders->fetch_assoc();
$pending_orders = $row_pending_orders['pending_orders'];

// SQL query for Medical Examination History (type = 1)
$sql_medical = "SELECT a.id, 
                        CONCAT('Payment from #', a.id) AS payment_number, 
                        a.appointment_date, 
                        a.appointment_start_time, 
                        a.appointment_end_time, 
                        a.status
                 FROM appointments a
                 JOIN services s ON a.service = s.id_service
                 WHERE s.type = 1 
                 ORDER BY a.appointment_date DESC, a.appointment_start_time DESC 
                 LIMIT 5";



// SQL query for Service History (type = 2)
$sql_service = "SELECT a.id, 
                        CONCAT('Payment from #', a.id) AS payment_number, 
                        CONCAT(DATE_FORMAT(a.appointment_date, '%b %d, %Y'), ', ', DATE_FORMAT(a.appointment_time, '%l:%i%p')) AS date_time, 
                        a.status 
                 FROM appointments a
                 JOIN services s ON a.service = s.id_service
                 WHERE s.type = 2 
                 ORDER BY a.appointment_date DESC, a.appointment_time DESC 
                 LIMIT 5";

// Execute queries
$result_medical = $con->query($sql_medical);
$result_service = $con->query($sql_service);

// Check if queries are successful
if (!$result_medical || !$result_service) {
  die("Error: " . $con->error);
}

// Define the date range for the past week
$dates = [];
$completed_orders = [];
$failed_orders = [];

// Get today's date
$today = new DateTime();

// Loop through the past week and generate dates
for ($i = 6; $i >= 0; $i--) {
  $date = $today->modify('-1 day')->format('Y-m-d');
  $dates[] = $date;
  $completed_orders[$date] = 0;
  $failed_orders[$date] = 0;
}

// Fetch completed orders
$sql_completed_orders = "SELECT 
    DATE_FORMAT(payment_date, '%Y-%m-%d') as date,
    COUNT(*) as completed_count 
FROM payments 
WHERE payment_status = 'completed' 
AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
GROUP BY DATE_FORMAT(payment_date, '%Y-%m-%d')
ORDER BY DATE_FORMAT(payment_date, '%Y-%m-%d')";

$result_completed_orders = $con->query($sql_completed_orders);
while ($row = $result_completed_orders->fetch_assoc()) {
  $completed_orders[$row['date']] = $row['completed_count'];
}

// Fetch failed orders
$sql_failed_orders = "SELECT 
    DATE_FORMAT(payment_date, '%Y-%m-%d') as date,
    COUNT(*) as failed_count 
FROM payments 
WHERE payment_status = 'failed' 
AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
GROUP BY DATE_FORMAT(payment_date, '%Y-%m-%d')
ORDER BY DATE_FORMAT(payment_date, '%Y-%m-%d')";

$result_failed_orders = $con->query($sql_failed_orders);
while ($row = $result_failed_orders->fetch_assoc()) {
  $failed_orders[$row['date']] = $row['failed_count'];
}

// Convert arrays to indexed arrays for chart data
$completed_orders = array_values($completed_orders);
$failed_orders = array_values($failed_orders);

// Define the date range for the past week
$sales_dates = [];
$sales_amounts = [];

// Get today's date
$today = new DateTime();

// Loop through the past week and generate dates
for ($i = 6; $i >= 0; $i--) {
  $date = $today->modify('-1 day')->format('Y-m-d');
  $sales_dates[] = $date;
  $sales_amounts[$date] = 0;
}

// Fetch daily sales
$sql_sales = "SELECT 
    DATE_FORMAT(payment_date, '%Y-%m-%d') as date,
    SUM(amount) as daily_sales 
FROM payments 
WHERE payment_status = 'completed' 
AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
GROUP BY DATE_FORMAT(payment_date, '%Y-%m-%d')
ORDER BY DATE_FORMAT(payment_date, '%Y-%m-%d')";

$result_sales = $con->query($sql_sales);
while ($row = $result_sales->fetch_assoc()) {
  $sales_amounts[$row['date']] = $row['daily_sales'];
}

// Convert arrays to indexed arrays for chart data
$sales_data = array_values($sales_amounts);

// Fetch total sales amount for the past week
$sql_total_sales = "SELECT SUM(amount) AS total_sales 
                    FROM payments 
                    WHERE payment_status = 'completed' 
                    AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";

$result_total_sales = $con->query($sql_total_sales);
$total_sales_row = $result_total_sales->fetch_assoc();
$total_sales = $total_sales_row['total_sales'] ?? 0; // Default value if null

// Close the connection
$con->close();
?>



<body>
  <div class="wrapper">
    <?php require('navbaradmin.php') ?>

    <div id="content2">


      <div class="row">
        <!-- Ô đếm số lượng người dùng -->
        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card card-stats">
            <div class="card-body">
              <div class="row">
                <div class="col-5 col-md-4">
                  <div class="icon-big text-center icon-warning">
                    <i class="fas fa-users"></i>
                  </div>
                </div>
                <div class="col-7 col-md-8">
                  <div class="numbers">
                    <p class="card-category">Total Users</p>
                    <p class="card-title"><?php echo $total_users; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card card-stats">
            <div class="card-body">
              <div class="row">
                <div class="col-5 col-md-4">
                  <div class="icon-big text-center icon-warning">
                    <i class="fas fa-check-circle"></i>
                  </div>
                </div>
                <div class="col-7 col-md-8">
                  <div class="numbers">
                    <p class="card-category">Orders</p>
                    <p class="card-title"><?php echo $total_orders; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card card-stats">
            <div class="card-body">
              <div class="row">
                <div class="col-5 col-md-4">
                  <div class="icon-big text-center icon-warning">
                    <i class="fas fa-shopping-cart"></i>
                  </div>
                </div>
                <div class="col-7 col-md-8">
                  <div class="numbers">
                    <p class="card-category">Sales</p>
                    <p class="card-title">$<?php echo number_format($total_sales, 0); ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card card-stats">
            <div class="card-body">
              <div class="row">
                <div class="col-5 col-md-4">
                  <div class="icon-big text-center icon-warning">
                    <i class="fas fa-clock"></i>
                  </div>
                </div>
                <div class="col-7 col-md-8">
                  <div class="numbers">
                    <p class="card-category">Pending Orders</p>
                    <p class="card-title"><?php echo $pending_orders; ?></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-8 col-md-12 col-sm-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">User Statistics</h4>
            </div>
            <div class="card-body">
              <canvas id="ordersStatisticsChart" width="400" height="200"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12">
          <div class="card">
            <div class="card-header">
              <h4 class="card-title">Daily Sales</h4>
              <h3 class="card-title">Total: $<?php echo number_format($total_sales, 0); ?></h3>
              <p class="card-category">Last 7 Days</p> <!-- Updated category to reflect dynamic data -->
            </div>
            <div class="card-body">
              <canvas id="dailySalesChart"></canvas>
            </div>
          </div>
        </div>


      </div>
      <div class="row">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              Medical Examination History
            </div>
            <div class="card-body">
              <table class="table">
                <thead>
                  <tr>
                    <th>Payment Number</th>
                    <th>Date & Start_End Time</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result_medical->fetch_assoc()) : ?>
                    <tr>
                      <td><i class="fas fa-check-circle text-success"></i> <?php echo htmlspecialchars($row['payment_number'] ?? ''); ?></td>
                      <td>
                        <?php
                        if ($row['status'] !== 'cancelled') {
                          // Format date and time
                          $date = htmlspecialchars($row['appointment_date'] ?? '');
                          $start_time = htmlspecialchars($row['appointment_start_time'] ?? '');
                          $end_time = htmlspecialchars($row['appointment_end_time'] ?? '');
                          echo $date . ', ' . date('g:i a', strtotime($start_time)) . ' - ' . date('g:i a', strtotime($end_time));
                        } else {
                          // Display message for cancelled appointments
                          echo 'Cancelled';
                        }
                        ?>
                      </td>
                      <td>
                        <?php
                        // Determine badge class based on status
                        $badgeClass = '';
                        switch ($row['status']) {
                          case 'completed':
                            $badgeClass = 'bg-success'; // Green
                            break;
                          case 'confirmed':
                            $badgeClass = 'bg-warning'; // Yellow
                            break;
                          case 'pending':
                          default:
                            $badgeClass = 'bg-danger'; // Red
                            break;
                        }
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                          <?php echo htmlspecialchars($row['status'] ?? ''); ?>
                        </span>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-header">
              Service History
            </div>
            <div class="card-body">
              <table class="table">
                <thead>
                  <tr>
                    <th>Payment Number</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ($row = $result_service->fetch_assoc()) : ?>
                    <tr>
                      <td><i class="fas fa-check-circle text-success"></i> <?php echo htmlspecialchars($row['payment_number']); ?></td>
                      <td><?php echo htmlspecialchars($row['date_time']); ?></td>
                      <td>
                        <?php
                        // Xác định màu sắc dựa trên trạng thái
                        $badgeClass = '';
                        switch ($row['status']) {
                          case 'completed':
                            $badgeClass = 'bg-success'; // Màu xanh
                            break;
                          case 'confirmed':
                            $badgeClass = 'bg-warning'; // Màu vàng
                            break;
                          case 'pending':
                          default:
                            $badgeClass = 'bg-danger'; // Màu đỏ
                            break;
                        }
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                          <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>



    </div>
    </tbody>
    </table>
  </div>
</body>

<script>
  var dates = <?php echo json_encode($dates); ?>.reverse(); // Đảo ngược mảng ngày
  var completed_orders = <?php echo json_encode($completed_orders); ?>.reverse(); // Đảo ngược mảng đơn hàng hoàn thành
  var failed_orders = <?php echo json_encode($failed_orders); ?>.reverse(); // Đảo ngược mảng đơn hàng thất bại
  var salesData = <?php echo json_encode($sales_data); ?>.reverse(); // Đảo ngược mảng dữ liệu bán hàng

  var ctx = document.getElementById('ordersStatisticsChart').getContext('2d');
  var ordersStatisticsChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: dates, // Ngày sau khi đảo ngược
      datasets: [{
        label: 'Completed Orders',
        data: completed_orders, // Số lượng đơn hàng hoàn thành sau khi đảo ngược
        borderColor: 'green',
        fill: true
      }, {
        label: 'Failed Orders',
        data: failed_orders, // Số lượng đơn hàng thất bại sau khi đảo ngược
        borderColor: 'red',
        fill: true
      }]
    }
  });





  // Daily Sales Chart
  var salesDates = <?php echo json_encode($sales_dates); ?>.reverse(); // Đảo ngược mảng ngày
  var salesData = <?php echo json_encode($sales_data); ?>.reverse(); // Đảo ngược mảng dữ liệu bán hàng

  var ctx2 = document.getElementById('dailySalesChart').getContext('2d');
  var dailySalesChart = new Chart(ctx2, {
    type: 'line',
    data: {
      labels: salesDates, // Ngày sau khi đảo ngược
      datasets: [{
        label: 'Sales',
        data: salesData, // Dữ liệu bán hàng sau khi đảo ngược
        borderColor: 'blue',
        fill: true
      }]
    }
  });
</script>