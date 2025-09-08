<?php
// team_nav.php
?>
<nav class="navbar navbar-expand-lg team-nav">
  <div class="container">
    <a class="navbar-brand fw-bold" href="team.php">TeamPanel</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#teamNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="teamNavbar">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="team.php"><i class="bi bi-people"></i> Team Home</a></li>
        <li class="nav-item"><a class="nav-link" href="tasks.php"><i class="bi bi-list-task"></i> Tasks</a></li>
        <li class="nav-item"><a class="nav-link" href="chat.php"><i class="bi bi-chat-dots"></i> Chat</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<style>
  .team-nav { background: #e67e22; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
  .team-nav .navbar-brand, .team-nav .nav-link { color: #fff !important; font-weight: 500; }
  .team-nav .nav-link:hover { color: #2c3e50 !important; }
</style>
