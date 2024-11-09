<?php require('head.php');
// Fetch settings from the database
$sql = "SELECT `key`, `value` FROM `settings` WHERE `key` IN ('navbar_email', 'navbar_phone', 'navbar_location')";
$result = $con->query($sql);

$settings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['key']] = $row['value'];
    }
}

// Set variables for settings
$navbar_email = isset($settings['navbar_email']) ? $settings['navbar_email'] : '';
$navbar_phone = isset($settings['navbar_phone']) ? $settings['navbar_phone'] : '';
$navbar_location = isset($settings['navbar_location']) ? $settings['navbar_location'] : '';
?>


<!-- ======= Contact Section ======= -->
<section id="contact" class="contact">
    <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">
            <div class="col-lg-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center">
                    <i class="bx bx-location-plus"></i>
                    <h3>Our Address</h3>
                    <span><?php echo $navbar_location; ?></span>
                </div>
            </div><!-- End Info Item -->

            <div class="col-lg-3 col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center">
                    <i class="bx bx-envelope"></i>
                    <h3>Email Us</h3>
                    <span><?php echo $navbar_email; ?></span>
                </div>
            </div><!-- End Info Item -->

            <div class="col-lg-3 col-md-6">
                <div class="info-item d-flex flex-column justify-content-center align-items-center">
                    <i class="bx bx-phone-call"></i>
                    <h3>Call Us</h3>
                    <span><?php echo $navbar_phone; ?></span>
                </div>
            </div><!-- End Info Item -->

        </div>

        <div class="row gy-4 mt-1">

            <div class="col-lg-6 ">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1861.9088146284787!2d105.8029615!3d21.0399819!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab3e6c439b7f%3A0x7af4dfdd1145b024!2zNDBiIFAuIMSQw7RuZyBRdWFuLCBOZ2jEqWEgxJDDtCwgQ-G6p3UgR2nhuqV5LCBIw6AgTuG7mWk!5e0!3m2!1sen!2s!4v1712823382623!5m2!1sen!2s" width="100%" height="384" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>                
            </div><!-- End Google Maps -->

            <div class="col-lg-6">
                <form action="contact.php" method="post" role="form" class="php-email-form">
                    <div class="row gy-4">
                        <div class="col-lg-6 form-group">
                            <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                        </div>
                        <div class="col-lg-6 form-group">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject"   >
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" name="message" rows="5" placeholder="Message" required></textarea>
                    </div>
                    <div class="my-3">
                        <div class="loading">Loading</div>
                        <div class="error-message"></div>
                        <div class="sent-message">Your message has been sent. Thank you!</div>
                    </div>
                    <div class="text-center"><button type="submit" name="send">Send Message</button></div>
                </form>
            </div><!-- End Contact Form -->

        </div>

    </div>
</section><!-- End Contact Section -->
<?php require('footer.php')?>

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/SMTP.php';

if(isset($_POST['send'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'dattq.ba11-021@st.usth.edu.vn';                 // SMTP username
        $mail->Password   = 'lufmmgpbeaovdvlt';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable implicit TLS encryption
        $mail->Port       = 465;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('dattq.ba11-021@st.usth.edu.vn', 'PETCARE');           // Add a recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Email Verification from PETCARE';
        $mail->Body    = "<h3>Name: $name <br>Email: $email <br>Message: $message</h3>";

        $mail->send();
        echo '<script>alert("Message has been sent successfully!");</script>';
    } catch (Exception $e) {
        echo '<script>alert("Message could not be sent. Mailer Error: '.$mail->ErrorInfo.'");</script>';
    }
}
?>
