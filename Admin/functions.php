<?php
require('headadmin.php');

function get_option($key) {
    global $con;
    $stmt = $con->prepare("SELECT `value` FROM `settings` WHERE `key` = ?");
    if ($stmt) {
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $stmt->bind_result($value);
        $stmt->fetch();
        $stmt->close();
        return $value;
    } else {
        return null;
    }
}

function update_option($key, $value) {
    global $con;

    // Kiểm tra xem key đã tồn tại chưa
    $stmt = $con->prepare("SELECT COUNT(*) FROM `settings` WHERE `key` = ?");
    if ($stmt) {
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Key đã tồn tại, cập nhật giá trị
            $stmt = $con->prepare("UPDATE `settings` SET `value` = ? WHERE `key` = ?");
        } else {
            // Key chưa tồn tại, chèn mới
            $stmt = $con->prepare("INSERT INTO `settings` (`key`, `value`) VALUES (?, ?)");
        }

        if ($stmt) {
            $stmt->bind_param("ss", $value, $key);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>
