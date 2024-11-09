<?php require('headadmin.php'); ?>

<body>
    <div class="wrapper">
        <?php require('navbaradmin.php') ?>

        <div id="content2">
            <h2>Manage Ratings</h2>
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
                        <th>Rating ID</th>
                        <th>User Name</th>
                        <th>Service</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Appointment ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all ratings and related information
                    $sql = "SELECT r.id AS rating_id, r.user_name, s.name_service, r.rating, r.comment, r.appointment_id 
                            FROM ratings r 
                            LEFT JOIN services s ON r.service_id = s.id_service";
                    $result = $con->query($sql);

                    if (!$result) {
                        die("Invalid query: " . $con->error);
                    }

                    // Counter for row numbering
                    $counter = 1;

                    // Display each rating
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$counter}</td>
                            <td>{$row['rating_id']}</td>
                            <td>{$row['user_name']}</td>
                            <td>{$row['name_service']}</td>
                            <td>{$row['rating']}</td>
                            <td>{$row['comment']}</td>
                            <td>{$row['appointment_id']}</td>
                            <td>
                                <a href='view_rating.php?id={$row['rating_id']}' class='btn btn-info btn-sm'  '>View</a>
                                <a href='delete_rating.php?id={$row['rating_id']}' class='btn btn-danger btn-sm'>Delete</a>
                            </td>
                        </tr>";
                        $counter++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require('footeradmin.php') ?>
</body>
</html>
