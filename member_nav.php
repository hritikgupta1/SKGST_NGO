<?php
// member_nav.php
?>
<nav class="navbar navbar-expand-lg member-nav">
  <div class="container">
    <a class="navbar-brand fw-bold" href="member.php">MemberArea</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#memberNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="memberNavbar">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="member.php"><i class="bi bi-house"></i> Home</a></li>
        <li class="nav-item"><a class="nav-link" href="events.php"><i class="bi bi-calendar-event"></i> Events</a></li>
        <li class="nav-item"><a class="nav-link" href="resources.php"><i class="bi bi-journal-text"></i> Resources</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<style>
  .member-nav { background: #16a085; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
  .member-nav .navbar-brand, .member-nav .nav-link { color: #fff !important; font-weight: 500; }
  .member-nav .nav-link:hover { color: #f39c12 !important; }
</style>
