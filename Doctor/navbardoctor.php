<nav id="sidebar">
    <div class="sidebar-header">
        <h3 class="text-light">CarePaws Doctor</h3>
    </div>
    <ul class="list-unstyled components">
        <li>
            <a class="nav-link text-white" href="doctor_profile.php">
                <i class="bi bi-person-badge" style="margin-right: 8px;"></i> Profile
            </a>
        </li>
        <li>
            <a class="nav-link text-white" href="doctor_schedule.php">
                <i class="bi bi-person-badge" style="margin-right: 8px;"></i> Schedule
            </a>
        </li>
        <li>
            <a class="nav-link text-white" href="history_appointments_doctor.php">
                <i class="bx bx-history" style="margin-right: 8px;"></i> History
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
                <li class="nav-item">
                    <a href='../Doctor/logout.php' class='btn btn-light btn-sm'>
                        <i class="bi bi-box-arrow-right" style="margin-right: 8px;"></i> <?php echo $_SESSION['doctor_username']; ?> - LOGOUT
                    </a>
                </li>
            </ul>
        </div>
    </nav>
