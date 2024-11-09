<?php require('head.php'); 
ob_start();?>

<?php
// Lấy ID của dịch vụ từ URL
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cmtTotal = isset($_GET['total']) ? $_GET['total'] : 1;
// Truy vấn thông tin dịch vụ từ cơ sở dữ liệu
$sql_service = "SELECT * FROM services WHERE id_service = ?";
$stmt_service = $con->prepare($sql_service);
$stmt_service->bind_param("i", $service_id);
$stmt_service->execute();
$result_service = $stmt_service->get_result();

// Kiểm tra xem dịch vụ có tồn tại không
if ($result_service->num_rows > 0) {
    $service = $result_service->fetch_assoc();
    $name_service = htmlspecialchars($service['name_service']);
    $title_content = htmlspecialchars($service['title_content']);
    $image_url = htmlspecialchars($service['image_url']);
    $description = nl2br($service['description']);
    $price = number_format($service['price']);
    ob_end_flush();
?>
    <div class="section" style="padding-top: 60px;">
        <div class="container">
            <div class="row text-left mb-3">
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <div class="service-heading mb-3" style="padding: 20px 0px 0px 100px;">
                            <h3 class="font-weight-bold heading text-black"><?php echo $name_service; ?></h3>
                        </div>
                        <div class="service-content mb-3 ml-3" style="background-color: rgb(203, 219, 203); padding: 20px; border-radius: 10px; margin-left:120px;">
                            <p class="text-black-50"><?php echo $title_content; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-3">
                <div class="col-12 text-center">
                    <img src="/uploads/Services/<?php echo $image_url; ?>" alt="Image" class="img-fluid rounded" style="width: 100%; height: 500px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8">
                    <p class="text"><?php echo $description; ?></p>
                </div>
                <div class="col-lg-4">
                    <div class="related-service-wrap">
                        <div class="text">
                            <h2 class="mb-0"><span class="from-text">from</span> <span class="vnd-text">VNĐ <?php echo $price; ?></span></h2>
                            <form id="bookForm" action="sdichvubook.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo $service_id; ?>">
                                <button type="submit" class="btn btn-success py-2 mr-1">Make an Appointment </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phần đánh giá của người dùng -->
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
                        echo "<p>No reviews yet.</p>";
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
    <!-- Danh sách các dịch vụ khác -->
    <section class="ftco-section bg-light">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-7 text-center">
                    <h2 class="font-weight-bold heading text-black mb-4">Other Services</h2>
                </div>
            </div>
            <div class="row">
                <?php
                // Truy vấn danh sách các dịch vụ khác cùng loại, ngoại trừ dịch vụ hiện tại
                $sql_other_services = "SELECT * FROM services WHERE id_service != ? AND type = 2";
                $stmt_other_services = $con->prepare($sql_other_services);
                $stmt_other_services->bind_param("i", $service_id);
                $stmt_other_services->execute();
                $result_other_services = $stmt_other_services->get_result();

                if ($result_other_services->num_rows > 0) {
                    while ($service_other = $result_other_services->fetch_assoc()) {
                        $other_service_id = $service_other['id_service'];
                        $other_service_name = htmlspecialchars($service_other['name_service']);
                        $other_service_image = htmlspecialchars($service_other['image_url']);
                        $other_service_price = number_format($service_other['price']);
                ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="service-card">
                                <img src="/uploads/Services/<?php echo $other_service_image; ?>" alt="Service" class="img-fluid">
                                <div class="service-info">
                                    <h3><?php echo $other_service_name; ?></h3>
                                    <p>VNĐ <?php echo $other_service_price; ?></p>
                                    <a href="sdichvu.php?id=<?php echo $other_service_id; ?>" class="btn btn-primary">See details</a>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }
                // Đóng kết nối và câu truy vấn
                $stmt_other_services->close();
                ?>
            </div>
        </div>
    </section>

<?php } else { ?>
    <p>Service not found.</p>
<?php } ?>

<?php require('footer.php'); ?>