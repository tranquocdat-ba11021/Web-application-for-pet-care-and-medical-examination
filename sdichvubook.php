<?php
require('head.php');
if (!isset($_POST['id'])) {
    include('404.php');
    exit;
}

// Xử lý form
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    include('404.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$service_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$user_id = $_SESSION['user_id'];

// Truy vấn thông tin người dùng
$sql_user = "SELECT full_name, phone FROM registered_users WHERE id = ?";
$stmt_user = $con->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$stmt_user->bind_result($full_name, $phone);
$stmt_user->fetch();
$stmt_user->close();

// Truy vấn thông tin pet của người dùng
$sql_pets = "SELECT id, pet_name FROM user_pets WHERE user_id = ?";
$stmt_pets = $con->prepare($sql_pets);
$stmt_pets->bind_param("i", $user_id);
$stmt_pets->execute();
$result_pets = $stmt_pets->get_result();
$pets = $result_pets->fetch_all(MYSQLI_ASSOC);
$stmt_pets->close();

// Kiểm tra nếu không có thú cưng
$has_pets = !empty($pets);

// Nếu không có thông tin từ URL, truy vấn dịch vụ từ CSDL
if (empty($service_name) || $price <= 0.0) {
    $sql_service = "SELECT name_service, price FROM services WHERE id_service = ?";
    $stmt_service = $con->prepare($sql_service);
    $stmt_service->bind_param("i", $service_id);
    $stmt_service->execute();
    $stmt_service->bind_result($service_name, $price);
    $stmt_service->fetch();
    $stmt_service->close();
}
?>


<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h2 class="mb-4">Booking Detail & Payment</h2>
                <h4 class="mb-4">Service: <?php echo htmlspecialchars($service_name); ?></h4>
                <div class="custom-wrapper">
                    <?php if (!$has_pets) : ?>
                        <div class="alert alert-warning" role="alert">
                            You need to add at least one pet to book an appointment. <a href="add_pets.php">Add a pet now</a>.
                        </div>
                    <?php else : ?>
                        <form action="payment.php" id="contactForm" method="POST" onsubmit="return validateForm()">
                            <div class="row">
                                <!-- Form đặt lịch hẹn -->
                                <div class="col-md-7">
                                    <div class="custom-contact-wrap w-100 p-md-5 p-4" style="border: 1px solid white; border-radius: 5px 0 0 5px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">
                                        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
                                        <input type="hidden" name="price" value="<?php echo $price; ?>">
                                        <input type="hidden" name="service_name" value="<?php echo htmlspecialchars($service_name); ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="full_name" id="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                                                    <div class="invalid-feedback">
                                                        Please enter your full name.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                                                    <div class="invalid-feedback">
                                                        Please enter phone number.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control" name="pet_id" id="pet_id" required>
                                                        <option value="">Choose your pet</option>
                                                        <?php foreach ($pets as $pet) : ?>
                                                            <option value="<?php echo $pet['id']; ?>"><?php echo htmlspecialchars($pet['pet_name']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Please select your pet.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="date" class="form-control" name="appointment_date" id="appointment_date" placeholder="Appointment date" required>
                                                    <div class="invalid-feedback">
                                                    Please select an appointment date.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <select class="form-control" name="appointment_time" id="appointment_time" required>
                                                        <option value="">Choose time</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                    Please select an appointment time.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <textarea class="form-control" name="additional_info" id="additional_info" rows="5" placeholder="More information"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h3 class="mt-5 mb-3">Payment method</h3>
                                    <div class="custom-contact-wrap w-100 p-md-5 p-4" style="border: 1px solid white; border-radius: 5px 0 0 5px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">
                                        <div class="form-group">
                                            <label for="payment_method" style="font-weight: bold; font-size: 21px;">Choose payment method</label>
                                            <div>
                                                <input type="radio" id="offline_payment" name="payment_method" value="offline" required checked>
                                                <label for="offline_payment">Direct payment</label>
                                            </div>
                                            <div>
                                                <input type="radio" id="online_payment" name="payment_method" value="online" required>
                                                <label for="online_payment">Online payment</label>
                                            </div>
                                            <div class="invalid-feedback">
                                            Please select payment method.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 d-flex align-items-start">
                                    <div class="related-service-wrap w-100 p-md-5 p-4" style="border: 1px solid white; border-radius: 0 5px 5px 0; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);">
                                        <div class="text">
                                            <h2 class="mb-0"><span class="from-text">From</span> <span class="vnd-text"> <?php echo number_format($price); ?>VND</span></h2>
                                            <button type="submit" class="btn btn-success py-2 mr-1">Pay now</button>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- .row -->
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    option[disabled] {
        color: #b0b0b0;
        background-color: #f8f8f8;
        cursor: not-allowed;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var today = new Date().toISOString().split('T')[0];
        document.getElementById('appointment_date').setAttribute('min', today);

        var appointmentDate = document.getElementById('appointment_date');
        var appointmentTime = document.getElementById('appointment_time');

        appointmentDate.addEventListener('change', function() {
            var selectedDate = appointmentDate.value;
            var currentDate = new Date().toISOString().split('T')[0];
            var currentTime = new Date().toTimeString().split(' ')[0].substring(0, 5);

            var times = ["09:00", "10:00", "11:00", "13:00", "14:00", "15:00", "16:00", "17:00"];
            appointmentTime.innerHTML = '<option value="">Chọn thời gian</option>';

            times.forEach(function(time) {
                var disabled = (time < currentTime && selectedDate === currentDate) ? 'disabled' : '';
                if (!disabled) {
                    appointmentTime.innerHTML += `<option value="${time}">${time}</option>`;
                }
            });
        });
    });

    function validateForm() {
        var form = document.getElementById('contactForm');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return false;
        }
        return true;
    }
</script>


<?php require('footer.php');
ob_end_flush(); 
?>
