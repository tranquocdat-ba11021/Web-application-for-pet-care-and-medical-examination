<?php
ob_start();
require('head.php');

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Get the post ID from the URL
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($post_id === 0) {
  echo "Post ID is missing.";
  exit;
}

// Fetch post data from the database
$sql = "SELECT * FROM posts WHERE id = $post_id";
$result = $con->query($sql);

if ($result->num_rows > 0) {
  $post = $result->fetch_assoc();
} else {
  echo "Post not found.";
  exit;
}

// Fetch categories for the post
$categoriesSql = "SELECT c.name, COUNT(pc.category_id) AS count 
                   FROM categories c 
                   INNER JOIN post_categories pc ON c.id = pc.category_id 
                   WHERE pc.post_id = $post_id 
                   GROUP BY c.id";
$categoriesResult = $con->query($categoriesSql);
$categories = [];
while ($categoryRow = $categoriesResult->fetch_assoc()) {
  $categories[] = $categoryRow;
}

// Fetch tags for the post
$tagsSql = "SELECT t.name 
            FROM tags t 
            INNER JOIN post_tags pt ON t.id = pt.tag_id 
            WHERE pt.post_id = $post_id";
$tagsResult = $con->query($tagsSql);
$tags = [];
while ($tagRow = $tagsResult->fetch_assoc()) {
  $tags[] = $tagRow['name'];
}

// Fetch comments for the post
$commentsSql = "SELECT c.*, u.full_name, u.image_url
                FROM comments c 
                JOIN registered_users u ON c.user_id = u.id 
                WHERE c.post_id = $post_id 
                ORDER BY c.created_at DESC";
$commentsResult = $con->query($commentsSql);
$comments = [];
while ($commentRow = $commentsResult->fetch_assoc()) {
  $comments[] = $commentRow;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
  if ($is_logged_in) {
    $user_id = intval($_SESSION['user_id']);
    $comment_content = $con->real_escape_string(trim($_POST['comment']));
    $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
    $comment_sql = "INSERT INTO comments (post_id, user_id, content, parent_id) 
                    VALUES ($post_id, $user_id, '$comment_content', $parent_id)";
    if ($con->query($comment_sql) === TRUE) {
      // Redirect to the same page to prevent re-submission
      header("Location: Newsdetail.php?id=$post_id");
      exit;
    }
  }
}
ob_end_flush(); // Flush output buffer and turn off output buffering
?>

<section id="blog" class="blog">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-5">
      <div class="col-lg-8">
        <article class="blog-details">
          <div class="post-img">
            <img src="./uploads/New/<?php echo htmlspecialchars($post['image_url']); ?>" alt="" class="img-fluid" style="width: 100%;">
          </div>
          <h2 class="title"><?php echo htmlspecialchars($post['title']); ?></h2>
          <div class="meta-top">
            <ul>
              <li class="d-flex align-items-center">
                <i class="bi bi-clock"></i>
                <a href="#"><time datetime="<?php echo htmlspecialchars($post['date']); ?>"><?php echo date("M j, Y", strtotime($post['date'])); ?></time></a>
              </li>
            </ul>
          </div>
          <div class="content">
            <p><?php echo nl2br(($post['content'])); ?></p>
          </div>
          <div class="meta-bottom">
            <i class="bi bi-folder"></i>
            <ul class="cats">
              <?php foreach ($categories as $category) : ?>
                <li><a href="#"><?php echo htmlspecialchars($category['name']); ?></a></li>
              <?php endforeach; ?>
            </ul>
            <i class="bi bi-tags"></i>
            <ul class="tags">
              <?php foreach ($tags as $tag) : ?>
                <li><a href="#"><?php echo htmlspecialchars($tag); ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </article>
        <div class="comments">
          <h4 class="comments-count"><?php echo count($comments); ?> Comments</h4>

          <?php
          // Display the first 3 comments
          $visible_comments = array_slice($comments, 0, 3);
          $hidden_comments = array_slice($comments, 3);

          // Recursive function to display comments and replies
          function display_comments($comments, $post_id, $parent_id = 0, $level = 0)
          {
            foreach ($comments as $comment) {
              if ($comment['parent_id'] == $parent_id) {
                echo '<div class="comment" style="margin-left: ' . ($level * 20) . 'px;">';
                echo '<div class="d-flex">';
                echo '<div class="comment-img">';
                $avatar_url = !empty($comment['image_url']) ? './uploads/user/' . htmlspecialchars($comment['image_url']) : 'default_profile.png';
                echo '<img src="' . $avatar_url . '" alt="" class="img-fluid rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">';
                echo '</div>';
                echo '<div>';
                echo '<h5><a href="#">' . htmlspecialchars($comment['full_name']) . '</a></h5>';
                echo '<time datetime="' . htmlspecialchars($comment['created_at']) . '">' . date("M j, Y", strtotime($comment['created_at'])) . '</time>';
                echo '<p>' . htmlspecialchars($comment['content']) . '</p>';
                echo '<a href="#" class="reply" data-comment-id="' . $comment['id'] . '">Reply</a>';
                if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']) {
                  echo ' | <a href="delete_comment.php?id=' . $comment['id'] . '&post_id=' . $post_id . '" class="delete">Delete</a>';
                }
                echo '</div></div>';
                display_comments($comments, $post_id, $comment['id'], $level + 1);
                echo '</div>';
              }
            }
          }

          // Display visible comments
          display_comments($visible_comments, $post_id);

          // Display hidden comments
          echo '<div id="hidden-comments" style="display: none;">';
          display_comments($hidden_comments, $post_id);
          echo '</div>';
          ?>

          <?php if (count($hidden_comments) > 0) : ?>
            <button id="toggle-comments" class="btn btn-secondary">See More</button>
          <?php endif; ?>

          <?php if ($is_logged_in) : ?>
            <div class="reply-form">
              <h4>Leave a Reply</h4>
              <p>Your email address will not be published. Required fields are marked *</p>
              <form action="Newsdetail.php?id=<?php echo $post_id; ?>" method="POST">
                <div class="row">
                  <div class="col form-group">
                    <input type="hidden" name="parent_id" value="0" id="parent_id">
                    <textarea name="comment" class="form-control" placeholder="Your Comment*" required></textarea>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
              </form>
            </div>
          <?php else : ?>
            <p>You must be <a href="login.php">logged in</a> to post a comment.</p>
          <?php endif; ?>
        </div>


      </div>

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

<script>
  // JavaScript to handle reply functionality
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.reply').forEach(function(replyLink) {
      replyLink.addEventListener('click', function(event) {
        event.preventDefault();
        var commentId = this.getAttribute('data-comment-id');
        document.getElementById('parent_id').value = commentId;
        document.querySelector('textarea[name="comment"]').focus();
      });
    });
  });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var toggleButton = document.getElementById('toggle-comments');
  var hiddenComments = document.getElementById('hidden-comments');
  
  if (toggleButton) {
    toggleButton.addEventListener('click', function () {
      hiddenComments.style.display = 'block';  // Show all hidden comments
      toggleButton.style.display = 'none';  // Hide the "See More" button
    });
  }

  document.querySelectorAll('.reply').forEach(function (replyLink) {
    replyLink.addEventListener('click', function (event) {
      event.preventDefault();
      var commentId = this.getAttribute('data-comment-id');
      document.getElementById('parent_id').value = commentId;
      document.querySelector('textarea[name="comment"]').focus();
    });
  });
});
</script>



<?php require('footer.php'); ?>