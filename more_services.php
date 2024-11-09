<?php require('head.php'); ?>

<!-- Start Blog Section -->
<div class="blog-section">
    <div class="container">

        <!-- Medical Services Section -->
        <h2 class="mb-5">All Medical Services</h2>
        <div class="row">
            <?php
            // Query to get all medical services (type = 1)
            $sql = "SELECT * FROM `services` WHERE `type` = 1";
            $result = $con->query($sql);

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
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
            else: ?>
                <p>There are no medical services available.</p>
            <?php endif; ?>
        </div>

        <!-- Pet Services Section -->
        <h2 class="mb-5">All Pet Services</h2>
        <div class="row">
            <?php
            // Query to get all pet services (type = 2)
            $sql = "SELECT * FROM `services` WHERE `type` = 2";
            $result = $con->query($sql);

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
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
            else: ?>
                <p>There are no pet services available.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php require('footer.php'); ?>
