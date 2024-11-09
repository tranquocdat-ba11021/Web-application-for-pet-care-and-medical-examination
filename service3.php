<?php
ob_start();
require('head.php');

// Fetch the doctor ID and service ID from the URL
$doctor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;

// Initialize variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$name = $email = $phone = $selected_date = $selected_time = $service_name = '';
$showPetWarning = false;

// Fetch user details if logged in
if ($user_id) {
    $sql_user = "SELECT full_name, email, phone FROM registered_users WHERE id = ?";
    $stmt_user = $con->prepare($sql_user);
    $stmt_user->bind_param('i', $user_id);
    $stmt_user->execute();
    $stmt_user->bind_result($name, $email, $phone);
    $stmt_user->fetch();
    $stmt_user->close();
    
    // Fetch list of pets
    $sql_pets = "SELECT id, pet_name FROM user_pets WHERE user_id = ?";
    $stmt_pets = $con->prepare($sql_pets);
    $stmt_pets->bind_param('i', $user_id);
    $stmt_pets->execute();
    $pets = $stmt_pets->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_pets->close();
    
    // Check if user has pets
    if (empty($pets)) {
        $showPetWarning = true;
    }
}

// Fetch doctor details
$sql_doctor = "SELECT name_doctor, intro_doctor, image_doctor FROM doctor WHERE id_doctor = ?";
$stmt_doctor = $con->prepare($sql_doctor);
$stmt_doctor->bind_param('i', $doctor_id);
$stmt_doctor->execute();
$doctor = $stmt_doctor->get_result()->fetch_assoc();
$stmt_doctor->close();

// Default values for doctor information
$doctor_name = htmlspecialchars($doctor['name_doctor'] ?? 'Default Doctor Name');
$doctor_intro = htmlspecialchars($doctor['intro_doctor'] ?? 'Default Doctor Introduction');
$doctor_image = htmlspecialchars($doctor['image_doctor'] ?? 'default.jpg');

// Fetch service details if service_id is provided
if ($service_id) {
    $sql_service = "SELECT name_service, description, image_url FROM services WHERE id_service = ?";
    $stmt_service = $con->prepare($sql_service);
    $stmt_service->bind_param('i', $service_id);
    $stmt_service->execute();
    $service = $stmt_service->get_result()->fetch_assoc();
    $service_name = htmlspecialchars($service['name_service'] ?? 'Service Name');
    $service_description = htmlspecialchars($service['description'] ?? 'Service Description');
    $service_image = htmlspecialchars($service['image_url'] ?? 'default.jpg');
    $stmt_service->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($showPetWarning) {
        die('Error: You must add at least one pet before making an appointment.');
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $message = $_POST['message'];
    $pet_id = isset($_POST['pet_id']) ? intval($_POST['pet_id']) : 0;
    $service_id = isset($_POST['service']) ? intval($_POST['service']) : 0;

    // Validate the selected date
    if ($date < date('Y-m-d')) {
        die('Error: Cannot select a past date.');
    }

    // Split the time slot into start and end times
    $time_slot = explode(' - ', $time);
    $appointment_start_time = $time_slot[0];
    $appointment_end_time = $time_slot[1];

    // Insert appointment into the database with start and end times
    $sql_insert = "INSERT INTO appointments (user_id, doctor_id, service, appointment_date, appointment_start_time, appointment_end_time, additional_info, pet_id, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')";
    $stmt_insert = $con->prepare($sql_insert);
    $stmt_insert->bind_param('iisssssi', $user_id, $doctor_id, $service_id, $date, $appointment_start_time, $appointment_end_time, $message, $pet_id);
    $stmt_insert->execute();

    // Get the ID of the newly inserted appointment
    $appointment_id = $stmt_insert->insert_id;
    $stmt_insert->close();

    // Insert payment information into the database
    $sql_payment = "INSERT INTO payments (appointment_id, amount, payment_method, payment_status) 
                    VALUES (?, ?, ?, 'pending')";
    $stmt_payment = $con->prepare($sql_payment);
    $amount = 0; // Set your amount here or fetch from service details
    $payment_method = 'offline';
    $stmt_payment->bind_param('ids', $appointment_id, $amount, $payment_method);
    $stmt_payment->execute();
    $stmt_payment->close();

    // Redirect to success_payment.php
    header('Location: success_payment.php');
    exit;
}
ob_end_flush(); // Send the output buffer and turn off output buffering
?>


    <section id="appointment" data-stellar-background-ratio="3">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <h2 class="mb-4">Booking Detail</h2>
                    <h4>Service: <?php echo htmlspecialchars($service_name); ?></h4>
                </div>
                <div class="col-md-12 mb-3 d-flex align-items-start doctor-info-container">
                    <div class="doctor-image-container">
                        <img src="../uploads/doctor/<?php echo htmlspecialchars($doctor_image); ?>" class="img-responsive doctor-image" alt="<?php echo htmlspecialchars($doctor_name); ?>">
                    </div>
                    <div class="doctor-info">
                        <h3><?php echo htmlspecialchars($doctor_name); ?></h3>
                        <p><?php echo htmlspecialchars($doctor_intro); ?></p>
                    </div>
                </div>
                <div class="col-12 intro text-center" style="margin-top: 15px;">
                    <img src="./image/slide-home-deco.png" alt="">
                </div>
                <div class="col-md-12 col-sm-12 info-block">
                <?php if ($showPetWarning): ?>
                        <div class="alert alert-warning" role="alert">
                            You must add at least one pet before making an appointment. Please <a href="add_pets.php">add a pet</a> first.
                        </div>
                <?php endif; ?>
                    <form id="appointment-form" role="form" method="post" action="#">
                        <div class="section-title wow fadeInUp" data-wow-delay="0.4s">
                            <h2>Make an Appointment</h2>
                        </div>
                        <div class="wow fadeInUp" data-wow-delay="0.8s">
                            <input type="hidden" name="service" value="<?php echo htmlspecialchars($service_id); ?>">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" value="<?php echo htmlspecialchars($name); ?>">
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" value="<?php echo htmlspecialchars($email); ?>">
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label for="date">Select Date</label>
                                    <input type="date" name="date" id="date" class="form-control" onchange="updateAvailableTimeSlots()" min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label for="time">Select Time Slot</label>
                                    <select class="form-control" id="time" name="time" required>
                                        <option value="">Select Time Slot</option>
                                        <!-- Time slots will be populated here by JavaScript -->
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a time slot.
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Your phone" value="<?php echo htmlspecialchars($phone); ?>">
                                </div>
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label for="pet_id">Select Pet</label>
                                    <select class="form-control" name="pet_id" id="pet_id" required>
                                        <option value="">Select Pet</option>
                                        <?php foreach ($pets as $pet): ?>
                                            <option value="<?php echo htmlspecialchars($pet['id']); ?>">
                                                <?php echo htmlspecialchars($pet['pet_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a pet.
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="message">Additional Information</label>
                                    <textarea class="form-control" id="message" rows="5" name="message" placeholder="Message"></textarea>
                                </div>
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <button type="submit" class="btn btn-primary">Book Now</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function updateAvailableTimeSlots() {
            const date = document.getElementById('date').value;
            const doctorId = '<?php echo $doctor_id; ?>';

            if (date) {
                fetch(`get_available_slots.php?date=${date}&doctor_id=${doctorId}`)
                    .then(response => response.json())
                    .then(data => {
                        const timeSelect = document.getElementById('time');
                        timeSelect.innerHTML = '<option value="">Select Time Slot</option>';

                        data.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = slot;
                            option.textContent = slot;
                            timeSelect.appendChild(option);
                        });
                    });
            }
        }
    </script>
    <?php require('footer.php'); ?>