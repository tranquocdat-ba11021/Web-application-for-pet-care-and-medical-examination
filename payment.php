<?php
ob_start();
require('head.php');

// Hàm gọi API của Casso để lấy danh sách giao dịch
function getCassoTransactions()
{
    $url = 'https://oauth.casso.vn/v2/transactions?page=&pageSize=100&sort=DESC';
    $headers = [
        'Authorization: Apikey AK_CS.d542e640457b11ef9068f9e08e26656f.dYaXYWeblkXfeVHv29FpcRbagD13jY3Kzw5Jn53wOHddlLF0DMDthCj339GJGjIqHOke3r6A',
        'Content-Type: application/json',
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        die("Failed to fetch transactions from Casso API.");
    }

    $data = json_decode($response, true);

    if (!isset($data['data']['records'])) {
        die("Invalid response from Casso API.");
    }
    return $data;
}
// if else

// Hàm để trích xuất mã đơn hàng từ mô tả
function extractOrderIdFromDescription($description)
{
    // Giả sử mã đơn hàng là số cuối cùng trong mô tả, tách bằng khoảng trắng
    preg_match('/MDH(\d+)/', $description, $matches);
    if (!isset($matches[1])) {
        return false;
    }
    return $matches[1];
}

// Hàm để cập nhật trạng thái thanh toán trong cơ sở dữ liệu
function updatePaymentStatus($orderId, $con)
{
    $payment_status = '';
    // Kiểm tra trạng thái thanh toán hiện tại
    $sql_check_payment_status = "SELECT payment_status FROM payments WHERE appointment_id = ?";
    $stmt_check_payment_status = $con->prepare($sql_check_payment_status);
    $stmt_check_payment_status->bind_param("i", $orderId);
    $stmt_check_payment_status->execute();
    $stmt_check_payment_status->bind_result($payment_status);
    $stmt_check_payment_status->fetch();
    $stmt_check_payment_status->close();

    // Nếu trạng thái là 'pending', cập nhật thành 'completed'
    if ($payment_status === 'pending') {
        $sql_update_payment = "UPDATE payments SET payment_status = 'completed' WHERE appointment_id = ?";
        $stmt_update_payment = $con->prepare($sql_update_payment);
        $stmt_update_payment->bind_param("i", $orderId);
        $stmt_update_payment->execute();
        $stmt_update_payment->close();
    }
}

// Hàm để kiểm tra và xử lý các thanh toán
// sửa lại thằng này là lấy id ơ poi ment check ktra trong danh sách giao dịch, nêú mà có id đấy thì mình xử lý , chứ không phải check hàng loạt
// thằng nào check thằng đấy thôi!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! vẫn chạy  foreach
// done 
function checkAndProcessPayments($con, $appointment_id)
{
    $transactions = getCassoTransactions();
    foreach ($transactions['data']['records'] as $record) {
        $orderId = extractOrderIdFromDescription($record['description']);
        if ($orderId == $appointment_id) {
            updatePaymentStatus($orderId, $con);
            return true;
        }
    }
}


// Kiểm tra nếu không có dữ liệu POST hoặc nếu người dùng đã nhấn nút thanh toán
if (!isset($_POST['service_id']) && !isset($_POST['appointment_id']))
////
{
    header('Location: sdichvubook.php');
    exit;
}

/////
if (isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $payment_method = $_POST['payment_method'];
    ///
    if ($payment_method == 'online') {
        // Kiểm tra và xử lý thanh toán
        checkAndProcessPayments($con, $appointment_id);
        //////////////////////////////////////////////////////
        // Kiểm tra lại trạng thái thanh toán
        $sql_check_payment_status = "SELECT payment_status FROM payments WHERE appointment_id = ?";
        $stmt_check_payment_status = $con->prepare($sql_check_payment_status);
        $stmt_check_payment_status->bind_param("i", $appointment_id);
        $stmt_check_payment_status->execute();
        $stmt_check_payment_status->bind_result($payment_status);
        $stmt_check_payment_status->fetch();
        $stmt_check_payment_status->close();
        if ($payment_status === 'completed') {
            $sql_update_appointment = "UPDATE appointments SET status = 'confirmed' WHERE id = ?";
            $stmt_update_appointment = $con->prepare($sql_update_appointment);
            $stmt_update_appointment->bind_param("i", $appointment_id);
            $stmt_update_appointment->execute();
            $stmt_update_appointment->close();
        }
    } else {
        // Xử lý thanh toán offline
        $sql_update_appointment = "UPDATE appointments SET status = 'pending' WHERE id = ?";
        $stmt_update_appointment = $con->prepare($sql_update_appointment);
        $stmt_update_appointment->bind_param("i", $appointment_id);
        $stmt_update_appointment->execute();
        $stmt_update_appointment->close();
    }

    header('Location: success_payment.php');
    exit;
} else {
    // Hiển thị thông tin đơn hàng và mã QR
    $service_id = intval($_POST['service_id']);
    $user_id = $_SESSION['user_id'];
    $pet_id = intval($_POST['pet_id']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $additional_info = isset($_POST['additional_info']) ? $_POST['additional_info'] : '';
    $payment_method = $_POST['payment_method'];
    $price = floatval($_POST['price']); // Thêm dòng này để lấy giá từ dữ liệu POST

    // Kiểm tra nếu ngày hoặc giờ bị rỗng
    if (empty($appointment_date) || empty($appointment_time)) {
        die("Ngày hoặc giờ hẹn không hợp lệ. Vui lòng thử lại.");
    }

    // Truy vấn thông tin chi tiết của lịch hẹn
    $sql_appointment = "INSERT INTO appointments (user_id, pet_id, service, appointment_date, appointment_time, additional_info, doctor_id, appointment_start_time, appointment_end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_appointment = $con->prepare($sql_appointment);
    $stmt_appointment->bind_param("iisssssss", $user_id, $pet_id, $service_id, $appointment_date, $appointment_time, $additional_info, $doctor_id, $appointment_start_time, $appointment_end_time);
    $stmt_appointment->execute();
    $appointment_id = $stmt_appointment->insert_id; // Lấy ID của lịch hẹn mới tạo
    $stmt_appointment->close();


    // Chuyển đổi appointment_id thành mã đơn hàng với định dạng 6 số
    $order_number = sprintf('%06d', $appointment_id);

    // Thêm chi tiết thanh toán vào bảng payments
    $sql_payment = "INSERT INTO payments (appointment_id, amount, payment_method, payment_status) VALUES (?, ?, ?, 'pending')";
    $stmt_payment = $con->prepare($sql_payment);
    $stmt_payment->bind_param("ids", $appointment_id, $price, $payment_method); // Đảm bảo rằng amount được cung cấp
    $stmt_payment->execute();
    $stmt_payment->close();

    // Kiểm tra phương thức thanh toán và xử lý tương ứng
    if ($payment_method == 'offline') {
        // Nếu chọn thanh toán offline, cập nhật trạng thái cuộc hẹn và chuyển hướng đến success_payment.php
        $sql_update_appointment = "UPDATE appointments SET status = 'confirmed' WHERE id = ?";
        $stmt_update_appointment = $con->prepare($sql_update_appointment);
        $stmt_update_appointment->bind_param("i", $appointment_id);
        $stmt_update_appointment->execute();
        $stmt_update_appointment->close();

        header('Location: success_payment.php');
        exit;
    } elseif ($payment_method == 'online') {
        // Nếu chọn thanh toán online, hiển thị thông tin đơn hàng và mã QR để thanh toán
        $sql_appointment_details = "SELECT a.appointment_date, a.appointment_time, a.additional_info, s.name_service, s.price, u.full_name, u.phone, p.pet_name
                                    FROM appointments a
                                    JOIN services s ON a.service = s.id_service
                                    JOIN registered_users u ON a.user_id = u.id
                                    JOIN user_pets p ON a.pet_id = p.id
                                    WHERE a.id = ?";
        $stmt_details = $con->prepare($sql_appointment_details);
        $stmt_details->bind_param("i", $appointment_id);
        $stmt_details->execute();
        $stmt_details->bind_result($appointment_date, $appointment_time, $additional_info, $service_name, $price, $full_name, $phone, $pet_name);
        $stmt_details->fetch();
        $stmt_details->close();


?>

        <div class="main-content">
            <div class="container d-flex">
                <div class="content">
                    <h3>TDo not trust the order</h3>
                    <div class="card mx-auto" style="width: 100%; max-width: 800px;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p>Hello, <?php echo htmlspecialchars($full_name); ?>.</p>
                                    <p>Your order has been recorded.</p>
                                    <p><strong>Service:</strong> <?php echo htmlspecialchars($service_name); ?></p>
                                    <p><strong>Pet:</strong> <?php echo htmlspecialchars($pet_name); ?></p>
                                    <p><strong>Appointment date:</strong> <?php echo htmlspecialchars($appointment_date); ?></p>
                                    <p><strong>Time:</strong> <?php echo htmlspecialchars($appointment_time); ?></p>
                                    <p><strong>Additional information:</strong> <?php echo htmlspecialchars($additional_info); ?></p>
                                    <p><strong>Price:</strong> <?php echo number_format($price); ?> VND</p>
                                    <p><strong>Order code:</strong> <?php echo htmlspecialchars($order_number); ?></p>
                                </div>
                                <div class="col-md-6 d-flex justify-content-center align-items-center">
                                    <img id="qr_code" src="https://img.vietqr.io/image/MB-0971622398-compact.png?amount=<?php echo $price; ?>&addInfo=MDH<?php echo number_format($order_number); ?>&accountName=<?php echo htmlspecialchars($full_name); ?>" alt="QR Code" class="img-fluid img-thumbnail" style="max-width: 300px;">
                                </div>
                            </div>
                            <div class="mt-3 text-center">
                                <form action="payment.php" method="POST" class="d-inline">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                                    <input type="hidden" name="payment_method" value="online">
                                    <button type="submit" class="btn btn-success">Complete payment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="history.php" class="btn btn-secondary">View booking history</a>
                        <a href="service.php" class="btn btn-secondary">Return to service</a>
                    </div>
                </div>
            </div>
        </div>


<?php
    }
}
ob_end_flush(); // Gửi tất cả nội dung trong bộ đệm và tắt bộ đệm đầu ra
?>
<script>
    // Set the expiration time (5 minutes)
    var expirationTime = 1 * 60 * 1000; // 5 minutes in milliseconds
    var qrCodeElement = document.getElementById('qr_code');

    // Redirect to failed_payment.php if the QR code has expired
    setTimeout(function() {
        window.location.href = 'failed_payment.php';
    }, expirationTime);

    // Optionally, you can display a countdown timer for user awareness
    var countdownElement = document.createElement('p');
    countdownElement.id = 'countdown';
    document.querySelector('.card-body').appendChild(countdownElement);

    function updateCountdown() {
        var now = new Date().getTime();
        var distance = expirationTime - (now - startTime);

        if (distance < 0) {
            document.getElementById('countdown').innerHTML = "QR code has expired.";
            return;
        }

        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('countdown').innerHTML = minutes + "m " + seconds + "s ";

        setTimeout(updateCountdown, 1000);
    }

    var startTime = new Date().getTime();
    updateCountdown();
</script>
<?php require('footer.php'); ?>