<?php
// admin_nav.php
?>
<nav class="navbar navbar-expand-lg navbar-dark admin-nav">
    <div class="container">
        <a class="navbar-brand fw-bold" href="admin.php">AdminPanel</a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar"
            aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="member_details.php"><i class="bi bi-person-lines-fill"></i> Member Details</a></li>
                <li class="nav-item"><a class="nav-link" href="admin_pending_users.php"><i class="bi bi-hourglass-split"></i> Pending Users</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Manage Users</a></li>
                <li class="nav-item"><a class="nav-link" href="reports.php"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .admin-nav {
        background: #2c3e50;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .admin-nav .navbar-brand,
    .admin-nav .nav-link {
        color: #ecf0f1 !important;
        font-weight: 500;
    }

    .admin-nav .nav-link:hover {
        color: #f1c40f !important;
    }

    .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.5);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    // Close navbar on link click (mobile only)
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            const navbar = document.querySelector('#adminNavbar');
            if (navbar.classList.contains('show')) {
                new bootstrap.Collapse(navbar).toggle();
            }
        });
    });
</script>