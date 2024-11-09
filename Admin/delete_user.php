<?php
require('../connection.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Sử dụng prepared statements để an toàn hơn
    $stmt = $con->prepare("DELETE FROM `registered_users` WHERE `id` = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Xóa thành công
        echo "Record deleted successfully.";
    } else {
        echo "Error deleting record: " . $con->error;
    }

    $stmt->close();
    $con->close();

    // Chuyển hướng về trang user.php sau khi xóa thành công
    header("Location: user.php?message=delete_success");
    exit();
} else {
    echo "No id parameter provided";
}
?>
