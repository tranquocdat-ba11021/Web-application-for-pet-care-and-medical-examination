<?php
require('connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['post_id'])) {
    $comment_id = intval($_GET['id']);
    $post_id = intval($_GET['post_id']);
    $user_id = intval($_SESSION['user_id']);

    // Check if the comment belongs to the logged-in user or if the user is an admin
    $check_sql = "SELECT user_id FROM comments WHERE id = $comment_id";
    $check_result = $con->query($check_sql);
    if ($check_result->num_rows > 0) {
        $comment = $check_result->fetch_assoc();
        if ($comment['user_id'] == $user_id || $_SESSION['role'] == 'admin') {
            $delete_sql = "DELETE FROM comments WHERE id = $comment_id OR parent_id = $comment_id";
            if ($con->query($delete_sql) === TRUE) {
                header("Location: Newsdetail.php?id=$post_id");
                exit;
            } else {
                echo "Error deleting comment: " . $con->error;
            }
        } else {
            echo "You do not have permission to delete this comment.";
        }
    } else {
        echo "Comment not found.";
    }
} else {
    echo "Invalid request.";
}
?>
