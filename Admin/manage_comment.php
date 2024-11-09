<?php 
require('headadmin.php'); 
?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <h2>Manage Comments</h2>
            <?php
            if (isset($_GET['message']) && $_GET['message'] == 'delete_success') {
                echo "
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                     Deleted successfully.
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>";
            }
            ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Comment ID</th>
                        <th>User Name</th>
                        <th>Comment</th>
                        <th>Post Title</th>
                        <th>Posted On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all comments along with post and user info
                    $sql = "SELECT c.id AS comment_id, c.content, c.created_at, u.full_name, p.title 
                            FROM comments c 
                            JOIN registered_users u ON c.user_id = u.id 
                            JOIN posts p ON c.post_id = p.id 
                            ORDER BY c.created_at DESC";
                    $result = $con->query($sql);
                    $no = 1; // Row number counter
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Giới hạn tiêu đề bài viết thành 30 chữ
                            $post_title = $row['title'];
                            $words = explode(" ", $post_title);
                            $limited_title = implode(" ", array_slice($words, 0, 5)); // Lấy 30 chữ

                            if (count($words) > 30) {
                                $limited_title .= "..."; // Thêm dấu "..." nếu tiêu đề dài hơn 30 chữ
                            }

                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$row['comment_id']}</td>
                                    <td>{$row['full_name']}</td>
                                    <td>{$row['content']}</td>
                                    <td>{$limited_title}</td>
                                    <td>" . date("M j, Y", strtotime($row['created_at'])) . "</td>
                                    <td>
                                        <a href='view_comment.php?id={$row['comment_id']}' class='btn btn-info btn-sm'>View</a>
                                        <a href='delete_comment.php?id={$row['comment_id']}' class='btn btn-danger btn-sm'>Delete</a>
                                    </td>
                                  </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='7'>No comments found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

<?php require('footeradmin.php'); ?>
