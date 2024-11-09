<?php require('headadmin.php'); ?>


<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <h2>ManageTags</h2>
            <?php
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
            <!-- Add Tag Link -->
            <a href="add_tag.php" class="btn btn-primary mb-3">Add Tag</a>
            <!-- Tag Table -->
            <h3>Tags</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch tags from the database
                    $tagSql = "SELECT * FROM tags";
                    $tagResult = $con->query($tagSql);

                    if ($tagResult->num_rows > 0) {
                        while ($tag = $tagResult->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>{$tag['id']}</td>
                                <td>{$tag['name']}</td>
                                <td>
                                    <a href='delete_tag.php?id={$tag['id']}' class='btn btn-danger btn-sm'>Delete</a>
                                </td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='3'>No tags found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <?php require('footeradmin.php'); ?>