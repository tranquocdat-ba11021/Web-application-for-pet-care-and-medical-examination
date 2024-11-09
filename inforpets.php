<?php
require('head.php');
require('connection.php'); // Kết nối cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Truy vấn để lấy thông tin thú cưng của người dùng
$sql_select = "SELECT * FROM user_pets WHERE user_id = ?";
$stmt = $con->prepare($sql_select);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>



<div class="main-content">
    <div class="container d-flex">
        <?php require('sidebar.php'); ?>

        <!-- CONTENT -->
        <div class="content">
            <h3>Information Pets</h3>
            <?php if ($result->num_rows > 0) { ?>
                <div class="card mx-5" style="width: 100%; max-width: 800px;">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <!-- Removed Description header -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['pet_type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['pet_gender']); ?></td>
                                        <td><?php echo htmlspecialchars($row['pet_age']); ?></td>
                                        <!-- Removed Description cell -->
                                        <td>    
                                            <a href="edit_petsuser.php?id=<?php echo $row['id']; ?>" class="btn d_btn btn-primary btn-sm">Edit</a>
                                            <a href="delete_pets.php?id=<?php echo $row['id']; ?>" class="btn d_btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } else { ?>
                <p>No pets found. <a href="edit_pets.php">Add a pet</a></p>
            <?php } ?>
        </div>
    </div>
</div>

<?php
$stmt->close();
require('footer.php');
?>
