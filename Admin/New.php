<?php 
require('headadmin.php'); 
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php'); ?>

        <div id="content2">
            <h2>Manage Posts</h2>
            <?php
            if (isset($_GET['message']) && $_GET['message'] == 'update_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    update successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            if (isset($_GET['message']) && $_GET['message'] == 'add_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    Added successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            
            if (isset($_GET['message']) && $_GET['message'] == 'delete_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                     Deleted successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            ?>
            <div class="row mb-3">
                <div class="col">
                    <a href="add_post.php" class="btn btn-primary mt-3">Add New Post</a>
                </div>
                <!-- Thêm chức năng tìm kiếm -->
                <div class="col-md-6">
                    <form method="GET" action="New.php" class="d-flex align-items-center">
                        <input type="text" class="form-control mt-3 me-2" name="search" placeholder="Search by ID or Title" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn mt-3 me-2" type="submit" style="background-color: #0d6efd; color: white; border: none; width: 100px; padding: 5px;">Search</button>
                        <a href="New.php" class="btn mt-3" style="background-color: #6c757d; color: white; text-decoration: none; width: 80px; padding: 5px; text-align: center; display: inline-block;">Reset</a>
                    </form>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Xử lý chức năng tìm kiếm
                    $search = isset($_GET['search']) ? $_GET['search'] : '';

                    // Number of posts per page
                    $posts_per_page = 5;

                    // Get current page number from URL, default to page 1 if not set
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                    // Calculate the offset for the SQL query
                    $offset = ($page - 1) * $posts_per_page;

                    // Truy vấn tổng số bài viết để tính số trang
                    $total_posts_sql = "SELECT COUNT(*) FROM posts WHERE id LIKE ? OR title LIKE ?";
                    $stmt = $con->prepare($total_posts_sql);
                    $search_param = "%" . $search . "%";
                    $stmt->bind_param("ss", $search_param, $search_param);
                    $stmt->execute();
                    $total_posts_result = $stmt->get_result();
                    $total_posts = $total_posts_result->fetch_row()[0];
                    $total_pages = ceil($total_posts / $posts_per_page);

                    // Truy vấn để lấy bài viết cho trang hiện tại
                    $sql = "SELECT * FROM posts WHERE id LIKE ? OR title LIKE ? ORDER BY date DESC LIMIT ? OFFSET ?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("ssii", $search_param, $search_param, $posts_per_page, $offset);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Kiểm tra nếu có dữ liệu trả về
                    if ($result && $result->num_rows > 0) {
                        // Lặp qua các hàng kết quả và hiển thị dữ liệu trong bảng
                        while ($row = $result->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>" . htmlspecialchars($row['id']) . "</td>
                                <td>" . htmlspecialchars($row['title']) . "</td>
                                <td>" . htmlspecialchars($row['date']) . "</td>
                                <td><img src='../uploads/New/" . htmlspecialchars($row['image_url']) . "' alt='Post Image' class='img-fluid' width='100'></td>
                                <td>
                                    <a href='edit_post.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_post.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Delete</a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No posts found.</td></tr>";
                    }

                    // Đóng kết nối đến cơ sở dữ liệu
                    $con->close();
                    ?>
                </tbody>
            </table>

            <div class="blog-pagination">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?><?php echo !empty($search) ? '&search=' . htmlspecialchars($search) : ''; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?php echo !empty($search) ? '&search=' . htmlspecialchars($search) : ''; ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?><?php echo !empty($search) ? '&search=' . htmlspecialchars($search) : ''; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <?php require('footeradmin.php'); ?>
