<?php
// user_nav.php
?>
<nav class="navbar navbar-expand-lg user-nav">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand fw-bold" href="user.php">MyDashboard</a>

    <!-- Mobile Toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbar" aria-controls="userNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav Links -->
    <div class="collapse navbar-collapse" id="userNavbar">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="user.php"><i class="bi bi-house-door"></i> Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="edit_profile.php"><i class="bi bi-pencil-square"></i> Edit Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  .user-nav {
    background: #3498db;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
  .user-nav .navbar-brand, 
  .user-nav .nav-link {
    color: #fff !important;
    font-weight: 500;
  }
  .user-nav .nav-link:hover {
    color: #ffd700 !important;
  }
  .navbar-toggler {
    border-color: rgba(255,255,255,0.5);
  }
  
</style>

<script>
  document.querySelectorAll('.navbar-nav .nav-link').forEach(function(navLink) {
    navLink.addEventListener('click', function() {
      let navbar = document.querySelector('#userNavbar');
      let bsCollapse = bootstrap.Collapse.getInstance(navbar);
      if (bsCollapse) {
        bsCollapse.hide();
      }
    });
  });
</script>


<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->