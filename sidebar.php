<div class="sidebar d_sidebar">
    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] == true) { ?>

        <a class="nav-link" href="edit_profile.php">
            <i class='bx bxs-user' style="margin-right: 8px;"></i>Profile
        </a>
        <a class="nav-link" data-bs-toggle="collapse" href="#manageDropdown" role="button" aria-expanded="false" aria-controls="manageDropdown">
            <i class='bx bxs-dog' style="margin-right: 8px;"></i>Pet
            <i class="bx bx-chevron-down" style="margin-left: 120px;"></i> <!-- Mũi tên cho Pet -->
        </a>
        <div class="collapse" id="manageDropdown">
            <ul class="nav flex-column ms-3">
                <li class="nav-item">
                    <a class="" href="add_pets.php">Add Pets</a>
                </li>
                <li class="nav-item">
                    <a class="" href="inforpets.php">Information Pets</a>
                </li>
            </ul>
        </div>
        
        <!-- Dropdown cho History -->
        <a class="nav-link" data-bs-toggle="collapse" href="#historyDropdown" role="button" aria-expanded="false" aria-controls="historyDropdown">
            <i class='bx bx-notepad' style="margin-right: 8px;"></i>History
            <i class="bx bx-chevron-down" style="margin-left: 94px;"></i> <!-- Mũi tên cho History -->
        </a>
        <div class="collapse" id="historyDropdown">
            <ul class="nav flex-column ms-3">
                <li class="nav-item">
                    <a class="" href="appointment_history.php">All history</a>
                </li>
                <li class="nav-item">
                    <a class="" href="appointment_history.php?type=1">Medical appointment history</a>
                </li>
                <li class="nav-item">
                    <a class="" href="appointment_history.php?type=2">Service booking history</a>
                </li>
            </ul>
        </div>
        
        <a class="nav-link" href="edit_pass.php">
            <i class='bx bx-key' style="margin-right: 8px;"></i>Change password
        </a>
        <a class="nav-link" href="logout.php">
            <i class='bx bx-log-out' style="margin-right: 8px;"></i>Logout
        </a>

    <?php } else { ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php } ?>
</div>
