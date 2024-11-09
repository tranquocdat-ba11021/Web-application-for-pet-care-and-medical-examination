<?php
require('head.php');

// Fetch the service ID from the URL
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch service details
$sql_service = "SELECT * FROM `services` WHERE `id_service` = ?";
$stmt_service = $con->prepare($sql_service);
$stmt_service->bind_param("i", $service_id);
$stmt_service->execute();
$result_service = $stmt_service->get_result();
$service = $result_service->fetch_assoc();

// Check if no service is found
if (!$service) {
    echo "<p> Not found.</p>";
    exit;
}

// Fetch doctors for the service
$sql_doctors = "SELECT * FROM `doctor` WHERE FIND_IN_SET(?, services_doctor)";
$stmt_doctors = $con->prepare($sql_doctors);
$stmt_doctors->bind_param("i", $service_id);
$stmt_doctors->execute();
$result_doctors = $stmt_doctors->get_result();
?>

<section class="section about-surgery">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h2 class="text-md"><?php echo htmlspecialchars($service['name_service']); ?></h2>
                <div class="divider my-4"></div>
                <p><?php echo nl2br(($service['description'])); ?></p>
                <div class="more-content" style="display: none;">
                    <!-- Additional content if any -->
                </div>
                <div class="buttons">
                    <button class="btn btn-main-2 btn-round-full mt-3 show-more-btn" onclick="toggleContent()">See more</button>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section doctor-single" style="padding-top: 0;">
    <div class="container mb-4" style="margin-top: -20px;">
        <div class="row">
            <div class="col-lg-12">
                <?php if ($result_doctors->num_rows > 0): ?>
                    <?php while ($doctor = $result_doctors->fetch_assoc()): ?>
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6">
                                        <div class="doctor-img-block">
                                            <img src="../uploads/doctor/<?php echo htmlspecialchars($doctor['image_doctor']); ?>" alt="" class="img-fluid" style="width: 320px;">
                                            <div class="ainfo-block mt-4">
                                                <h4 class="mb-0"><a href="service3.php?id=<?php echo htmlspecialchars($doctor['id_doctor']); ?>"><?php echo htmlspecialchars($doctor['name_doctor']); ?></a></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 col-md-6 px-0">
                                        <div class="doctor-details mt-4 mt-lg-0">
                                            <h2 class="text-md">Introducing to myself</h2>
                                            <div class="divider my-4"></div>
                                            <p><?php echo htmlspecialchars($doctor['intro_doctor']); ?></p>
                                            <ul class="list-inline mt-4 doctor-social-links">
                                                <li class="list-inline-item"><a href="<?php echo htmlspecialchars($doctor['instagram_link']); ?>" target="_blank"><i class="bx bxl-instagram"></i></a></li>
                                                <li class="list-inline-item"><a href="<?php echo htmlspecialchars($doctor['twitter_link']); ?>" target="_blank"><i class="bx bxl-twitter"></i></a></li>
                                                <li class="list-inline-item"><a href="<?php echo htmlspecialchars($doctor['facebook_link']); ?>" target="_blank"><i class="bx bxl-facebook"></i></a></li>
                                            </ul>
                                            <a href="service3.php?id=<?php echo htmlspecialchars($doctor['id_doctor']); ?>&service_id=<?php echo htmlspecialchars($service_id); ?>" class="btn btn-main-2 btn-round-full mt-3">Make an Appointment<i class="icofont-simple-right ml-2"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>There is no doctor who can perform this service.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<div class="section">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <h2 class="font-weight-bold heading mb-4">User Reviews</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <?php
                // Số đánh giá trên mỗi trang
                $ratings_per_page = 3;

                // Nhận số trang hiện tại từ URL, nếu không đặt mặc định là trang 1
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                // Tính toán offset cho truy vấn SQL
                $offset = ($page - 1) * $ratings_per_page;

                // Truy vấn tổng số đánh giá để tính số trang
                $total_ratings_sql = "SELECT COUNT(*) FROM ratings WHERE service_id = ?";
                $stmt_total_ratings = $con->prepare($total_ratings_sql);
                $stmt_total_ratings->bind_param("i", $service_id);
                $stmt_total_ratings->execute();
                $total_ratings_result = $stmt_total_ratings->get_result();
                $total_ratings = $total_ratings_result->fetch_row()[0];
                $total_pages = ceil($total_ratings / $ratings_per_page);
                $stmt_total_ratings->close();

                // Truy vấn các đánh giá của dịch vụ từ cơ sở dữ liệu cho trang hiện tại
                $sql_ratings = "SELECT r.*, u.image_url FROM ratings r JOIN registered_users u ON r.user_id = u.id WHERE r.service_id = ? ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
                $stmt_ratings = $con->prepare($sql_ratings);
                $stmt_ratings->bind_param("iii", $service_id, $ratings_per_page, $offset);
                $stmt_ratings->execute();
                $result_ratings = $stmt_ratings->get_result();

                // Hiển thị các đánh giá
                if ($result_ratings->num_rows > 0) {
                    while ($rating = $result_ratings->fetch_assoc()) {
                        $user_name = htmlspecialchars($rating['user_name']);
                        $rating_value = intval($rating['rating']);
                        $comment = htmlspecialchars($rating['comment']);
                        $created_at = date('d/m/Y', strtotime($rating['created_at']));
                        $avatar_url = htmlspecialchars($rating['image_url']);
                ?>
                        <div class="rating-box mb-4">
                            <div class="d-flex align-items-start">
                                <img src="/uploads/user/<?php echo $avatar_url; ?>" alt="Avatar" class="img-fluid rounded-circle avatar">
                                <div class="rating-content ml-3">
                                    <h5><?php echo $user_name; ?> <small class="text-muted">- <?php echo $created_at; ?></small></h5>
                                    <div class="rating">
                                        <?php
                                        // Giả sử $rating_value được thiết lập từ cơ sở dữ liệu hoặc nguồn khác
                                        // Ví dụ giá trị có thể là 3, 4, hoặc 5 sao
                                        $rating_value = isset($rating_value) ? intval($rating_value) : 0;

                                        // Giới hạn giá trị $rating_value trong khoảng từ 0 đến 5
                                        $rating_value = max(0, min(5, $rating_value));

                                        // Hiển thị sao đánh giá
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating_value) {
                                                echo '<span class="fa fa-star checked"></span>';
                                            } else {
                                                echo '<span class="fa fa-star"></span>';
                                            }
                                        }
                                        ?>
                                    </div>

                                    <p><?php echo $comment; ?></p>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>Chưa có đánh giá nào.</p>";
                }
                $stmt_ratings->close();
                ?>

                <!-- Phân trang -->
                <div class="blog-pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1) : ?>
                            <li class="page-item">
                                <a class="page-link" href="?id=<?php echo $service_id; ?>&page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?id=<?php echo $service_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages) : ?>
                            <li class="page-item">
                                <a class="page-link" href="?id=<?php echo $service_id; ?>&page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('footer.php'); ?>

<script>
    function toggleContent() {
        var moreContent = document.querySelector('.more-content');
        var btn = document.querySelector('.show-more-btn');
        if (moreContent.style.display === "none") {
            moreContent.style.display = "block";
            btn.textContent = "See less";
        } else {
            moreContent.style.display = "none";
            btn.textContent = "See more";
        }
    }
</script>


