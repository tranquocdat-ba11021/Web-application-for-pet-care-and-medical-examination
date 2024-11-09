<?php
session_start();
require('connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = intval($_POST['id']);
    $user_id = intval($_SESSION['user_id']);
    $content = $con->real_escape_string(trim($_POST['comment']));

    if (!empty($content)) {
        $sql = "INSERT INTO comments (post_id, user_id, content) VALUES ($post_id, $user_id, '$content')";
        if ($con->query($sql) === TRUE) {
            header("Location: Newsdetail.php?id=$post_id");
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }
    } else {
        echo "Comment cannot be empty.";
    }
} else {
    header("Location: Newsdetail.php");
    exit;
}
?>
