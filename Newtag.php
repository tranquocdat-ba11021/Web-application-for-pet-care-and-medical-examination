<?php
require('head.php');
if (!isset($_GET['name'])) {
    echo "Tag not specified.";
    exit;
}

// Lấy tên tag từ URL và giải mã URL
$tag = urldecode($_GET['name']);

// Truy vấn cơ sở dữ liệu để lấy các bài viết chứa tag này
$posts_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

$total_posts_sql = "SELECT COUNT(*) FROM posts WHERE id IN (SELECT post_id FROM post_tags WHERE tag_id IN (SELECT id FROM tags WHERE name = '$tag'))";
$total_posts_result = $con->query($total_posts_sql);
$total_posts = $total_posts_result->fetch_row()[0];
$total_pages = ceil($total_posts / $posts_per_page);

$sql = "SELECT * FROM posts WHERE id IN (SELECT post_id FROM post_tags WHERE tag_id IN (SELECT id FROM tags WHERE name = '$tag')) ORDER BY date DESC LIMIT $posts_per_page OFFSET $offset";
$result = $con->query($sql);
?>

<section id="blog" class="blog">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">
      <!-- Main content area -->
      <div class="col-lg-8">
        <div class="row gy-4 posts-list">
          <?php while ($postRow = $result->fetch_assoc()) : ?>
            <div class="col-xl-6 col-md-12">
              <div class="news-post-item position-relative h-100">
                <div class="news-post-img position-relative overflow-hidden">
                  <img src="./uploads/New/<?= htmlspecialchars($postRow['image_url']) ?>" class="img-fluid" alt="">
                  <span class="news-post-date"><?= htmlspecialchars($postRow['date']) ?></span>
                </div>
                <div class="news-post-content d-flex flex-column h-100">
                  <h3 class="news-post-title"><?= htmlspecialchars($postRow['title']) ?></h3>
                  <?php
                  $content = strip_tags($postRow['content']);
                  if (strlen($content) > 90) {
                    $content = substr($content, 0, 90) . '...';
                  }
                  ?>
                  <p class="news-post-text"><?= htmlspecialchars($content) ?></p>
                  <hr class="news-divider">
                  <a href="Newsdetail.php?id=<?= $postRow['id'] ?>" class="news-readmore stretched-link">
                    <span>Read More</span><i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
        <!-- Pagination -->
        <?php if ($total_pages > 1) : ?>
          <div class="blog-pagination">
            <ul class="pagination justify-content-center">
              <?php if ($page > 1) : ?>
                <li class="page-item">
                  <a class="page-link" href="?name=<?= urlencode($tag) ?>&page=<?= $page - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                  </a>
                </li>
              <?php endif; ?>
              <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link" href="?name=<?= urlencode($tag) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
              <?php if ($page < $total_pages) : ?>
                <li class="page-item">
                  <a class="page-link" href="?name=<?= urlencode($tag) ?>&page=<?= $page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                  </a>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        <?php endif; ?>
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

<?php require('footer.php'); ?>
