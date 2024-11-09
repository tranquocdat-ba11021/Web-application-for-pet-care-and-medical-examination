<?php require('head.php'); ?>

<!-- Start Blog Section -->
<div class="blog-section">
    <div class="container">

        <!-- Medical Services Section -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="mb-0">Examination of the Disease</h2>
            <?php
            // Query to check if there are more medical services
            $sql = "SELECT COUNT(*) AS total FROM `services` WHERE `type` = 1";
            $result = $con->query($sql);
            $total_services = $result->fetch_assoc()['total'];

            if ($total_services > 3) : ?>
                <a href="more_services.php" class="view-all-button">View All</a>
            <?php endif; ?>
        </div>
        <div class="row">
            <?php
            // Fetch 3 medical services
            $sql = "SELECT * FROM `services` WHERE `type` = 1 LIMIT 3";
            $result = $con->query($sql);

            if ($result && $result->num_rows > 0) :
                while ($row = $result->fetch_assoc()) : ?>
                    <div class="col-12 col-sm-6 col-md-4 mb-5">
                        <div class="post-entry">
                            <a href="skhambenh.php?id=<?php echo htmlspecialchars($row['id_service']); ?>" class="post-thumbnail">
                                <img src="../uploads/Services/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name_service']); ?>" class="img-fluid">
                            </a>
                            <div class="post-content-entry">
                                <h3><a href="skhambenh.php?id=<?php echo htmlspecialchars($row['id_service']); ?>"><?php echo htmlspecialchars($row['name_service']); ?></a></h3>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else : ?>
                <p>There are no medical services available.</p>
            <?php endif; ?>
        </div>

        <!-- Pet Services Section -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="mb-0">Service</h2>
            <?php
            // Query to check if there are more pet services
            $sql = "SELECT COUNT(*) AS total FROM `services` WHERE `type` = 2";
            $result = $con->query($sql);
            $total_services = $result->fetch_assoc()['total'];

            if ($total_services > 3) : ?>
                <div class="view-all-container">
                    <a href="more_services.php" class="view-all-button">View All</a>
                </div>

            <?php endif; ?>
        </div>
        <div class="row">
            <?php
            // Fetch 3 pet services
            $sql = "SELECT * FROM `services` WHERE `type` = 2 LIMIT 3";
            $result = $con->query($sql);

            if ($result && $result->num_rows > 0) :
                while ($row = $result->fetch_assoc()) : ?>
                    <div class="col-12 col-sm-6 col-md-4 mb-5">
                        <div class="post-entry">
                            <a href="sdichvu.php?id=<?php echo htmlspecialchars($row['id_service']); ?>" class="post-thumbnail">
                                <img src="../uploads/Services/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name_service']); ?>" class="img-fluid">
                            </a>
                            <div class="post-content-entry">
                                <h3><a href="sdichvu.php?id=<?php echo htmlspecialchars($row['id_service']); ?>"><?php echo htmlspecialchars($row['name_service']); ?></a></h3>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            else : ?>
                <p>There are no services available.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require('footer.php'); ?>