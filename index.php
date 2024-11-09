<?php require('head.php');
// Số lượng bài viết hiển thị
$posts_to_display = 3;


// Truy vấn để lấy 3 bài viết mới nhất
$sql_posts = "SELECT * FROM posts ORDER BY date DESC LIMIT $posts_to_display";
$result_posts = $con->query($sql_posts);


// Truy vấn để lấy 3 bác sĩ đầu tiên
$sql_doctors = "SELECT * FROM doctor ORDER BY created_at DESC LIMIT 3";
$result_doctors = $con->query($sql_doctors);
function get_setting($key, $con)
{
    $sql = "SELECT `value` FROM `settings` WHERE `key`='$key'";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['value'];
    } else {
        return '';
    }
}
// Truy vấn để lấy tất cả các slide
$sql_slides = "SELECT * FROM slides ORDER BY id";
$result_slides = $con->query($sql_slides);

function get_slide_data($con)
{
    $slides = [];
    $sql = "SELECT * FROM slides ORDER BY id";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $slides[] = $row;
        }
    }
    return $slides;
}

$slides = get_slide_data($con);
?>
<!-- Hero section -->
<div class="slider-wrapper owl-carousel owl-theme" id="hero-slider">
    <?php foreach ($slides as $slide) : ?>
        <div class="slide min-vh-100 d-flex align-items-center" style="background-image: url('./uploads/slides/<?php echo htmlspecialchars($slide['image']); ?>');">
            <div class="slide-content container">
                <div class="row">
                    <div class="col-12">
                        <h4 class="text-uppercase text-white"><?php echo htmlspecialchars($slide['title']); ?></h4>
                        <h3 class="display-3 my-3 text-white text-uppercase"><?php echo htmlspecialchars($slide['sub_title']); ?></h3>
                        <a href="<?php echo htmlspecialchars($slide['link']); ?>" class="btn btn-outline-light"><?php echo htmlspecialchars($slide['button_text']); ?></a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<!----About--------->
<!-- Our Story Section -->
<section id="our-story">
    <div class="container">
    <div class="row">
            <div class="col-12 intro text-center">
                <small>
                    <img src="./image/slide-home-deco.png" alt="">
                    <h6 style="margin-top: 20px;">Featured article</h6>
                    <h1>What do we do?</h1>
                </small>
            </div>
        </div>
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-6">
                <div class="row">
                    <div class="col-12 info-box">
                        <img src="./image/h3.png" alt="Our Story Image 1" class="info-image">
                        <div class="info-text">
                            <h5>Veterinary Services</h5>
                            <p>We offer a wide range of high-quality services for your pets.</p>
                        </div>
                    </div>
                    <div class="col-12 info-box mt-4">
                        <img src="./image/h3.png" alt="Our Story Image 2" class="info-image">
                        <div class="info-text">
                            <h5>Experienced Staff</h5>
                            <p>Our team consists of highly experienced professionals.</p>
                        </div>
                    </div>
                    <div class="col-12 info-box mt-4">
                        <img src="./image/h3.png" alt="Our Story Image 3" class="info-image">
                        <div class="info-text">
                            <h5>State-of-the-Art Facilities</h5>
                            <p>We use the latest technology to ensure the best care for your pets.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="./image/h11.jpg" alt="Our Story Logo" class="about-logo" style="margin-left: 60px;">
            </div>
        </div>
    </div>
</section>

<!--MIlESTONE-->
<section id="milestone" class="bg-cover">
    <div class="container">
        <div class="row text-center justify-content-center">
            <div class="col-lg-2 col-sm-6 ">
                <div class="display-4"><?php echo get_setting('milestone_count1', $con); ?></div>
                <p class="mb-0"><?php echo get_setting('milestone_description1', $con); ?></p>
            </div>
            <div class="col-lg-2 col-sm-6 ">
                <div class="display-4"><?php echo get_setting('milestone_count2', $con); ?></div>
                <p class="mb-0"><?php echo get_setting('milestone_description2', $con); ?></p>
            </div>
            <div class="col-lg-2 col-sm-6 ">
                <div class="display-4"><?php echo get_setting('milestone_count3', $con); ?></div>
                <p class="mb-0"><?php echo get_setting('milestone_description3', $con); ?></p>
            </div>
            <div class="col-lg-2 col-sm-6 ">
                <div class="display-4"><?php echo get_setting('milestone_count4', $con); ?></div>
                <p class="mb-0"><?php echo get_setting('milestone_description4', $con); ?></p>
            </div>
        </div>
    </div>
</section>

<!--Services-->
<section id="services">
    <div class="container">
        <div class="row">
            <div class="col-12 intro text-center">
                <small>
                    <img src="./image/slide-home-deco.png" alt="">
                    <h6 style="margin-top: 20px;">Featured article</h6>
                    <h1>What do we do?</h1>
                    <p>3 THINGS ALWAYS COMMITTED TO CUSTOMERS</p>
                </small>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-sm--6">
                <div class="service-box">
                    <img src="./image/hình17.png" alt="">
                    <h5>DEDICATE TO YOUR WORK</h5>
                    <p>We work with all our heart, high responsibility and love for dogs and cats. Healthy pets are our happiness</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm--6">
                <div class="service-box">
                    <img src="./image/hinh18.png" alt="">
                    <h5>CHEAPEST SERVICE PRICE</h5>
                    <p>We are committed to offering the most preferential prices on the market for the opportunity to experience our services.</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm--6">
                <div class="service-box">
                    <img src="./image/hinh19.png" alt="">
                    <h5>TOP QUALITY</h5>
                    <p>We constantly improve and develop the skill level of our personnel to serve pets and bring the best results to the job.</p>
                </div>
            </div>
        </div>
    </div>
</section> 
<!--TEAM-->
<section id="team">
    <div class="container">
        <div class="row intro text-center">
            <div class="col-12">
                <small>
                    <img src="./image/slide-home-deco.png" alt="" class="img-fluid">
                    <h6 style="margin-top: 20px;">Outstanding doctor</h6>
                    <h1>Meet Our Team</h1>
                </small>
            </div>
        </div>
        <div class="row">
            <?php
            // Lặp qua các bác sĩ và hiển thị thông tin
            if ($result_doctors->num_rows > 0) {
                while ($row = $result_doctors->fetch_assoc()) {
            ?>
                    <div class="col-lg-4 col-sm-6 col-12 mb-5 text-center">
                        <div class="team-member">
                            <img src="./uploads/doctor/<?= htmlspecialchars($row['image_doctor']) ?>" alt="" class="team-img mb-3">
                            <div class="social-links">
                                <a href="<?= htmlspecialchars($row['facebook_link']) ?>"><i class='bx bxl-facebook'></i></a>
                                <a href="<?= htmlspecialchars($row['twitter_link']) ?>"><i class='bx bxl-twitter'></i></a>
                                <a href="<?= htmlspecialchars($row['instagram_link']) ?>"><i class='bx bxl-instagram'></i></a>
                            </div>
                        </div>
                        <h4><?= htmlspecialchars($row['name_doctor']) ?></h4>
                    </div>
            <?php
                }
            } else {
                echo "<p>No team members found.</p>";
            }
            ?>
        </div>
    </div>
</section>



<section id="blog" class="bg-light">
    <div class="container">
        <div class="row intro text-center">
            <div class="col-12">
                <img src="./image/slide-home-deco.png" alt="" class="img-fluid">
                <h6 style="margin-top: 20px;">Featured article</h6>
                <h1>Recent From Blog</h1>
            </div>
        </div>
        <div class="row justify-content-between">
            <?php while ($row = $result_posts->fetch_assoc()) : ?>
                <div class="col-xl-4 col-md-6">
                    <div class="news-post-item position-relative">
                        <div class="news-post-img position-relative overflow-hidden">
                            <img src="./uploads/New/<?= htmlspecialchars($row['image_url']) ?>" class="img-fluid" alt="">
                            <span class="news-new-tag">New</span>
                            <span class="news-post-date"><?= htmlspecialchars($row['date']) ?></span>
                        </div>
                        <div class="news-post-content d-flex flex-column h-100">
                            <h3 class="news-post-title"><?= htmlspecialchars($row['title']) ?></h3>
                            <?php
                            $content = strip_tags($row['content']);
                            if (strlen($content) > 30) {
                                $content = substr($content, 0, 30) . '...';
                            }
                            ?>
                            <p class="news-post-text"><?= htmlspecialchars($content) ?></p>
                            <hr class="news-divider">
                            <a href="Newsdetail.php?id=<?= $row['id'] ?>" class="news-readmore stretched-link">
                                <span>Read More</span><i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>


    </div>
</section>

<!--FOOTER-->
<?php require('footer.php'); ?>