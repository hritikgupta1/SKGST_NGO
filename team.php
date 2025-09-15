<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Team member') {
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
    <title>Team Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <?php include 'team_nav.php'; ?>

    <div class="container mt-5">
        <div class="card p-4">
            <h2>Welcome, <?= htmlspecialchars($user['name']) ?> (Team Member)</h2>
            <p class="text-muted">Collaborate with your team and manage tasks efficiently.</p>
        </div>
    </div>

</body>

</html>