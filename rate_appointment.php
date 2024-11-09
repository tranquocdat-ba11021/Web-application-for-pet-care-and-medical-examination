<?php
ob_start();
require('head.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$type = intval($_GET['type']);
$user_name = $_SESSION['user_username'];
$appointment_id = intval($_GET['id'] ?? 0); // Use default value if 'id' is not set
$service_id = intval($_GET['service_id'] ?? 0); // Use default value if 'service_id' is not set
$rating_success = false;
$rating_error = false;
$existing_rating = false;


// Check if the user has already rated this appointment
$sql_check_rating = "SELECT rating, comment FROM ratings WHERE appointment_id = ? AND user_id = ?";
$stmt_check_rating = $con->prepare($sql_check_rating);
$stmt_check_rating->bind_param("ii", $appointment_id, $user_id);
$stmt_check_rating->execute();
$stmt_check_rating->store_result();

if ($stmt_check_rating->num_rows > 0) {
    $stmt_check_rating->bind_result($existing_rating_value, $existing_comment);
    $stmt_check_rating->fetch();
    $existing_rating = true;
} 
$stmt_check_rating->close();

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$existing_rating) {
    if (isset($_POST['rating']) && isset($_POST['comment'])) {
        $rating = intval($_POST['rating']);
        $comment = trim($_POST['comment']);

        // Limit comment to 150 characters
        if (strlen($comment) > 150) {
            $comment = substr($comment, 0, 150);
        }

        if ($rating >= 1 && $rating <= 5) {
            $sql_rate = "INSERT INTO ratings (appointment_id, user_id, user_name, service_id, rating, comment) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_rate = $con->prepare($sql_rate);
            $stmt_rate->bind_param("iisiis", $appointment_id, $user_id, $user_name, $service_id, $rating, $comment);

            if ($stmt_rate->execute()) {
                $rating_success = true;
                if ($type == 2) { // Redirect to service page after successful rating
                    header('Location: sdichvu.php?id=' . $service_id);
                } else {
                    header('Location: skhambenh.php?id=' . $service_id);
                }
                exit;
            } else {
                $rating_error = true;
                echo "Error executing statement: " . $stmt_rate->error;
            }
            $stmt_rate->close();
        } else {
            $rating_error = true;
            echo "Rating value is not within the expected range.";
        }
    } else {
        $rating_error = true;
        echo "Rating or comment is not set.";
    }
}
ob_end_flush();
?>

<div class="main-content">
    <div class="container d-flex">
        <?php require('sidebar.php'); ?>

        <div class="content">
            <h3>Service reviews</h3>

            <?php
            if ($rating_success) {
                echo "<div class='alert alert-success' id='rating-alert'>Đánh giá đã được gửi thành công.</div>";
            } elseif ($rating_error) {
                echo "<div class='alert alert-danger' id='rating-alert'>Lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.</div>";
            }

            if ($existing_rating) {
                echo "<div class='alert alert-info'>Bạn đã đánh giá dịch vụ này với rating $existing_rating_value sao và bình luận: $existing_comment</div>";

                // Fetch service details
                $sql_service = "SELECT name_service, image_url, description FROM services WHERE id_service = ?";
                $stmt_service = $con->prepare($sql_service);
                $stmt_service->bind_param("i", $service_id);
                $stmt_service->execute();
                $stmt_service->bind_result($service_name, $image_url, $description);
                $stmt_service->fetch();
                $stmt_service->close();

                // Display service details
                echo "<div class='service-details'>";
                echo "<h4>$service_name</h4>";
                echo "<img src='/uploads/Services/$image_url' alt='$service_name' class='img-fluid rounded' style='width: 50%; height: 200px; object-fit: cover;'>";
                echo "<p>$description</p>";
                echo "</div>";
            } else {
            ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="rating">Evaluate</label>
                        <div class="star-rating">
                            <input type="radio" name="rating" id="rating-5" value="5"><label for="rating-5" class="star">&#9733;</label>
                            <input type="radio" name="rating" id="rating-4" value="4"><label for="rating-4" class="star">&#9733;</label>
                            <input type="radio" name="rating" id="rating-3" value="3"><label for="rating-3" class="star">&#9733;</label>
                            <input type="radio" name="rating" id="rating-2" value="2"><label for="rating-2" class="star">&#9733;</label>
                            <input type="radio" name="rating" id="rating-1" value="1"><label for="rating-1" class="star">&#9733;</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3" maxlength="150" style="margin-bottom: 20px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit a review</button>
                </form>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>

<script>
    // Automatically hide alert after 3 seconds
    setTimeout(function() {
        var alert = document.getElementById('rating-alert');
        if (alert) {
            alert.style.display = 'none';
        }
    }, 3000);

    // Add event listeners to the star labels
    document.querySelectorAll('.star-rating .star').forEach(function(starLabel) {
        starLabel.addEventListener('click', function() {
            let ratingValue = this.htmlFor.split('-')[1];
            document.querySelector('input[name="rating"][value="' + ratingValue + '"]').checked = true;
        });
    });
</script>
