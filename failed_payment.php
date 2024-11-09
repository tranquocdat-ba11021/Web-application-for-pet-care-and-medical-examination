<?php
ob_start();
require('head.php');
?>

<div class="main-content">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="content">
            <div class="card mx-auto" style="width: 100%; max-width: 600px;">
                <div class="card-body text-center">
                    <h3 class="card-title">Payment Failed</h3>
                    <p class="card-text">Sorry, your transaction was not completed. Please check the information and make payment again.</p>
                    <p class="card-text">If you have problems or need further assistance, please contact us.</p>
                    <div class="mt-3">
                        <!-- Redirect to appointment_history.php instead of payment.php -->
                        <a href="appointment_history.php" class="btn btn-primary">Retry Payment</a>
                        <a href="service.php" class="btn btn-secondary">Return to Service</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require('footer.php');
ob_end_flush(); // Send all content in the buffer and turn off output buffering
?>
