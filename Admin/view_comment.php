<?php
require('headadmin.php');

// Kiểm tra xem ID của bình luận có được truyền qua URL hay không
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Comment ID");
}

$comment_id = intval($_GET['id']);

// Truy vấn lấy thông tin bình luận
$sql = "SELECT c.id AS comment_id, c.content, c.created_at, u.full_name, p.title 
        FROM comments c 
        JOIN registered_users u ON c.user_id = u.id 
        JOIN posts p ON c.post_id = p.id 
        WHERE c.id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Comment not found");
}

$comment = $result->fetch_assoc();
$stmt->close();
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <h2>View Comment Details</h2>
            <?php
            if (isset($_GET['message']) && $_GET['message'] == 'delete_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                     deleted successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h4>User: <?php echo htmlspecialchars($comment['full_name']); ?></h4>
                </div>
                <div class="card-body">
                    <p><strong>Post Title:</strong> <?php echo htmlspecialchars($comment['title']); ?></p>
                    <p><strong>Comment:</strong></p>
                    <blockquote class="blockquote">
                        <p><?php echo htmlspecialchars($comment['content']); ?></p>
                    </blockquote>
                    <p><strong>Posted On:</strong> <?php echo date("M j, Y", strtotime($comment['created_at'])); ?></p>
                </div>
                <div class="card-footer">
                    <a href="manage_comment.php" class="btn btn-secondary">Back to Comments</a>
                    <a href="delete_comment.php?id=<?php echo $comment['comment_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">Delete Comment</a>
                </div>
            </div>
        </div>
    </div>

    <?php require('footeradmin.php') ?>
