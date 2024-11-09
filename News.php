<?php require('head.php');

// Nhận giá trị từ khóa tìm kiếm từ URL
$query = isset($_GET['query']) ? $con->real_escape_string($_GET['query']) : '';

// Truy vấn tìm kiếm các bài viết có tiêu đề chứa từ khóa
$sql = "SELECT * FROM posts WHERE title LIKE '%$query%' ORDER BY date DESC";
$result = $con->query($sql);

// Số bài viết trên mỗi trang
$posts_per_page = 6;

// Nhận số trang hiện tại từ URL, nếu không đặt mặc định là trang 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Tính toán offset cho truy vấn SQL
$offset = ($page - 1) * $posts_per_page;

// Truy vấn tổng số bài viết để tính số trang
$total_posts_sql = "SELECT COUNT(*) FROM posts WHERE title LIKE '%$query%'";
$total_posts_result = $con->query($total_posts_sql);
$total_posts = $total_posts_result->fetch_row()[0];
$total_pages = ceil($total_posts / $posts_per_page);

// Truy vấn cơ sở dữ liệu để lấy các bài viết cho trang hiện tại
$sql = "SELECT * FROM posts WHERE title LIKE '%$query%' ORDER BY date DESC LIMIT $posts_per_page OFFSET $offset";
$result = $con->query($sql);
?>

<section id="blog" class="blog">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">
      <!-- Main content area -->
      <div class="col-lg-8">
        <div class="row gy-4 posts-list">
          <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="col-xl-6 col-md-12">
              <div class="news-post-item position-relative h-100">
                <div class="news-post-img position-relative overflow-hidden">
                  <img src="./uploads/New/<?= htmlspecialchars($row['image_url']) ?>" class="img-fluid" alt="">
                  <span class="news-post-date"><?= htmlspecialchars($row['date']) ?></span>
                </div>
                <div class="news-post-content d-flex flex-column h-100">
                  <h3 class="news-post-title"><?= htmlspecialchars($row['title']) ?></h3>
                  <?php
                  // Sử dụng summary_content nếu có, nếu không thì cắt nội dung chính
                  $summary = isset($row['summary_content']) && !empty($row['summary_content']) 
                      ? strip_tags($row['summary_content'])
                      : strip_tags($row['content']);
                  ?>
                  <p class="news-post-text"><?= htmlspecialchars($summary) ?></p>
                  <hr class="news-divider">
                  <a href="Newsdetail.php?id=<?= $row['id'] ?>" class="news-readmore stretched-link">
                    <span>Read More</span><i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
        <!-- Pagination -->
        <div class="blog-pagination">
          <ul class="pagination justify-content-center">
            <?php if ($page > 1) : ?>
              <li class="page-item">
                <a class="page-link" href="?query=<?= urlencode($query) ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?query=<?= urlencode($query) ?>&page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <?php if ($page < $total_pages) : ?>
              <li class="page-item">
                <a class="page-link" href="?query=<?= urlencode($query) ?>&page=<?= $page + 1 ?>" aria-label="Next">
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="sidebar">
          <div class="sidebar-item search-form">
            <h3 class="sidebar-title">Search</h3>
            <form action="Newsearch.php" method="GET" class="mt-3">
              <input type="text" name="query" placeholder="Search by title">
              <button type="submit"><i class="bi bi-search"></i></button>
            </form>
          </div>
          <div class="sidebar-item categories">
            <h3 class="sidebar-title">Categories</h3>
            <ul class="mt-3">
              <?php
              $categoryQuery = "SELECT c.id, c.name, COUNT(pc.category_id) AS count 
                                FROM categories c 
                                LEFT JOIN post_categories pc ON c.id = pc.category_id 
                                GROUP BY c.id, c.name";
              $categoryResult = $con->query($categoryQuery);
              if ($categoryResult->num_rows > 0) {
                while ($categoryRow = $categoryResult->fetch_assoc()) {
                  echo "<li><a href='Newcategory.php?id=" . urlencode($categoryRow['id']) . "'>" . htmlspecialchars($categoryRow['name']) . " <span>(" . htmlspecialchars($categoryRow['count']) . ")</span></a></li>";
                }
              } else {
                echo "<li>No categories available</li>";
              }
              ?>
            </ul>
          </div>
          <div class="sidebar-item tags">
            <h3 class="sidebar-title">Tags</h3>
            <ul class="mt-3">
              <?php
              $tagQuery = "SELECT * FROM tags";
              $tagResult = $con->query($tagQuery);
              if ($tagResult->num_rows > 0) {
                while ($tagRow = $tagResult->fetch_assoc()) {
                  echo "<li><a href='Newtag.php?name=" . urlencode($tagRow['name']) . "'>" . htmlspecialchars($tagRow['name']) . "</a></li>";
                }
              } else {
                echo "<li>No tags available</li>";
              }
              ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- footer -->
<?php require('footer.php'); ?>
