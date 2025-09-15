<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f0f2f5; }
    .card { border-radius: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
    .profile-info p { margin-bottom: 0.5rem; }
    @media (max-width: 768px) {
        .profile-card { margin-top: 20px; }
    }
  </style>
</head>
<body>

<!-- Include Navbar -->
<?php include 'user_nav.php'; ?>

<!-- User Profile Card -->
<div class="container">
  <div class="card profile-card p-4 mt-5 mx-auto" style="max-width: 700px;">
    <div class="text-center mb-4">
      <i class="bi bi-person-circle" style="font-size: 4rem; color:#3498db;"></i>
      <h2 class="mt-2"><?= htmlspecialchars($user['name']) ?></h2>
      <p class="text-muted">Welcome to your dashboard!</p>
    </div>

    <div class="row profile-info">
      <div class="col-md-6">
        <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
        <p><b>Phone:</b> <?= htmlspecialchars($user['phone']) ?></p>
        <p><b>Address:</b> <?= htmlspecialchars($user['address']) ?></p>
        <p><b>Gender:</b> <?= htmlspecialchars($user['gender']) ?></p>
      </div>
      <div class="col-md-6">
        <p><b>Date of Birth:</b> <?= htmlspecialchars($user['dob']) ?></p>
        <p><b>Occupation:</b> <?= htmlspecialchars($user['occupation']) ?></p>
        <p><b>Qualification:</b> <?= htmlspecialchars($user['qualification']) ?></p>
      </div>
    </div>

    <div class="text-center mt-4">
      <a href="edit_profile.php" class="btn btn-primary me-2"><i class="bi bi-pencil-square"></i> Edit Profile</a>
      <a href="logout.php" class="btn btn-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </div>
</div>

</body>
</html>
