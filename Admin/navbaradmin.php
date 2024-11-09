<nav id="sidebar">
    <div class="sidebar-header">
        <h3 class="text-light">CarePaws Admin</h3>
    </div>
    <ul class="list-unstyled components">
        <li>
            <a class="nav-link text-white" href="dashboard.php">
                <i class='bx bx-home' style="margin-right: 8px;"></i> Dashboard
            </a>
        </li>
        <li>
            <a class="nav-link text-white" href="user.php">
                <i class="bx bx-user" style="margin-right: 8px;"></i> Customer
            </a>
        </li>
        <li>
            <a class="nav-link text-white" href="Pets.php">
                <i class='bx bxs-dog' style="margin-right: 8px;"></i> Pets
            </a>
        </li>
        <li>
            <a class="nav-link text-white" href="manage_services.php">
                <i class="bx bx-briefcase" style="margin-right: 8px;"></i> Services
            </a>
        </li>

        <li>
            <a class="nav-link text-white" href="manage_slides.php">
                <i class="bx bx-images" style="margin-right: 8px;"></i> Slider
            </a>
        </li>
        <li>
            <a class="nav-link text-white d-flex align-items-center" data-bs-toggle="collapse" href="#doctorDropdown" role="button" aria-expanded="false" aria-controls="doctorDropdown">
                <i class="bi bi-person-badge" style="margin-right: 8px;"></i> Doctor
                <i class="bx bx-chevron-down ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled" id="doctorDropdown">
                <li>
                    <a class="nav-link text-white" href="doctor.php">
                        <i class="bi bi-person-badge" style="margin-right: 8px;"></i> Management Doctor
                    </a>
                </li>
                <li>
                    <a class="nav-link text-white" href="add_schedule.php">
                        <i class="fas fa-stethoscope" style="margin-right: 8px;"></i> Doctor schedule
                    </a>
                </li>
                <li>
                    <a class="nav-link text-white" href="view_schedule.php">
                        <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i> View schedule
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a class="nav-link text-white d-flex align-items-center" data-bs-toggle="collapse" href="#historyDropdown" role="button" aria-expanded="false" aria-controls="historyDropdown">
                <i class="bx bx-history" style="margin-right: 8px;"></i> History
                <i class="bx bx-chevron-down ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled" id="historyDropdown">
                <li>
                    <a class="nav-link text-white" href="history_appointments.php?type=1">
                        <i class="bx bx-calendar-check" style="margin-right: 8px;"></i> Medical appointment history
                    </a>
                </li>
                <li>
                    <a class="nav-link text-white" href="history_appointments.php?type=2">
                        <i class="bx bx-calendar" style="margin-right: 8px;"></i> Service booking history
                    </a>
                </li>
                <li>
                    <a class="nav-link text-white" href="history_appointments.php?">
                        <i class="bx bx-list" style="margin-right: 8px;"></i> All history
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a class="nav-link text-white d-flex align-items-center" data-bs-toggle="collapse" href="#manageDropdown" role="button" aria-expanded="false" aria-controls="manageDropdown">
                <i class="bx bx-wrench" style="margin-right: 8px;"></i> Manage
                <i class="bx bx-chevron-down ms-auto"></i>
            </a>
            <ul class="collapse list-unstyled" id="manageDropdown">
                <li>
                    <a class="nav-link text-white" href="New.php">
                        <i class="bx bx-news" style="margin-right: 8px;"></i> News
                    </a>
                </li>

                <li>
                    <a class="nav-link text-white" href="manage_categories.php">
                        <i class="bx bx-list-ul" style="margin-right: 8px;"></i> Categories
                    </a>
                </li>
                <li>
                    <a class="nav-link text-white" href="manage_tag.php">
                        <i class="bx bx-tag" style="margin-right: 8px;"></i> Tag
                    </a>
                </li>
                <li>
                    <a class="nav-link text-white" href="manage_rating.php">
                    <i class="bx bx-star" style="margin-right: 8px;"></i> Rating
                    </a>
                </li>
                <li>
                    <a class="nav-link text-white" href="manage_comment.php">
                    <i class="bx bxs-comment" style="margin-right: 8px;"></i> Comment
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a class="nav-link text-white" href="settings.php">
                <i class="bx bx-cog" style="margin-right: 8px;"></i> Setting
            </a>
        </li>
    </ul>
</nav>

<div class="main w-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-light" id="stickyNavbar">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle btn btn-light btn-sm d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="margin-right: 30px;">
                        <i class="bi bi-person-circle" style="margin-right: 8px;"></i> <?php echo $_SESSION['admin_username']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="profile_admin.php"><i class="bi bi-person" style="margin-right: 8px;"></i> Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout_admin.php"><i class="bi bi-box-arrow-right" style="margin-right: 8px;"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>