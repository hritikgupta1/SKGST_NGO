<?php
session_start();
require 'db.php'; // Make sure this connects to skgst_ngo database

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Member') {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['business_name']);
    $details = trim($_POST['business_details']);
    $address = trim($_POST['business_address']);
    $contact = trim($_POST['business_contact']);
    $email   = trim($_POST['business_email']);
    $filename = null;

    // --- Required field validation ---
    if (empty($name))    $errors[] = "Business name is required.";
    if (empty($details)) $errors[] = "Business details are required.";
    if (empty($address)) $errors[] = "Business address is required.";
    if (empty($contact)) $errors[] = "Business contact is required.";
    if (empty($email))   $errors[] = "Business email is required.";

    // --- Format validation ---
    if (!empty($contact) && !preg_match("/^[0-9]{10}$/", $contact)) {
        $errors[] = "Contact number must be exactly 10 digits.";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // --- File upload validation ---
    if (!empty($_FILES['business_pic']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES["business_pic"]["name"]);
        $target_file = $target_dir . $filename;

        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (!move_uploaded_file($_FILES["business_pic"]["tmp_name"], $target_file)) {
            $errors[] = "Failed to upload business picture.";
        }
    } else {
        $errors[] = "Business picture is required.";
    }

    // --- Duplicate email check ---
    if (empty($errors)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM businesses WHERE business_email = ?");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            $errors[] = "Email already registered!";
        }
    }

    // If no errors → Insert into DB
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO businesses 
                (business_name, business_details, business_address, business_contact, business_email, business_pic) 
                VALUES (?, ?, ?, ?, ?, ?)");

            $stmt->execute([$name, $details, $address, $contact, $email, $filename]);

            $success = "Business listing submitted successfully!";
        } catch (PDOException $e) {
            // Rollback file if DB insert fails
            if ($filename && file_exists("uploads/" . $filename)) {
                unlink("uploads/" . $filename);
            }
            $errors[] = "Database error: " . $e->getMessage();
        }
    } else {
        // If validation fails, delete uploaded file to avoid orphaned files
        if ($filename && file_exists("uploads/" . $filename)) {
            unlink("uploads/" . $filename);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Business Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            margin: 10px 10px 5% 10px;
        }

        @media (max-width:768px) {
            .card {
                margin: 10px 10px 10% 10px;
            }
        }
    </style>
</head>

<body class="bg-primary-subtle">
    <?php include 'member_nav.php'; ?>

    <div class="container mt-5">
        <div class="card p-4 shadow-lg bg-light rounded-4">
            <h2 class="text-center">Submit Your Business</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" novalidate>
                <div class="mb-3">
                    <label class="form-label">Business Name</label>
                    <input type="text" name="business_name" class="form-control" value="<?= htmlspecialchars($name ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Business Details</label>
                    <textarea name="business_details" class="form-control" required><?= htmlspecialchars($details ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Business Address</label>
                    <input type="text" name="business_address" class="form-control" value="<?= htmlspecialchars($address ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Business Contact</label>
                    <input type="text" name="business_contact" class="form-control" value="<?= htmlspecialchars($contact ?? '') ?>" pattern="[0-9]{10}" required>
                    <div class="form-text">Enter 10-digit phone number only.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Business Email</label>
                    <input type="email" name="business_email" class="form-control" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Business Picture</label>
                    <input type="file" name="business_pic" class="form-control" accept=".jpg,.jpeg,.png,.gif" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">Submit Business</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
