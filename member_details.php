<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Admin') {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = "";

// ===== Handle Edit =====
if (isset($_POST['edit_member'])) {
    $id      = $_POST['id'];
    $name    = trim($_POST['business_name']);
    $email   = trim($_POST['business_email']);
    $phone   = trim($_POST['business_contact']);
    $address = trim($_POST['business_address']);
    $details = trim($_POST['business_details']);
    $status  = $_POST['status'] ?? 0;
    $old_pic = $_POST['old_pic'];

    // --- Required validation ---
    if (empty($name))    $errors[] = "Business name is required.";
    if (empty($email))   $errors[] = "Email is required.";
    if (empty($phone))   $errors[] = "Phone number is required.";
    if (empty($address)) $errors[] = "Address is required.";

    // --- Format validation ---
    if (!empty($phone) && !preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone must be exactly 10 digits.";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // --- Duplicate email check ---
    if (empty($errors)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM businesses WHERE business_email = ? AND id != ?");
        $check->execute([$email, $id]);
        if ($check->fetchColumn() > 0) {
            $errors[] = "Email already registered!";
        }
    }

    $newPic = $old_pic;

    // --- Handle new image upload ---
    if (empty($errors) && !empty($_FILES['business_pic']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $newPic = time() . "_" . basename($_FILES['business_pic']['name']);
        $targetFile = $targetDir . $newPic;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "gif"];

        if (!in_array($fileType, $allowed)) {
            $errors[] = "Only JPG, JPEG, PNG, GIF files are allowed.";
        } elseif (!move_uploaded_file($_FILES['business_pic']['tmp_name'], $targetFile)) {
            $errors[] = "Failed to upload image.";
        } else {
            // Delete old file if new uploaded
            if ($old_pic && file_exists("uploads/" . $old_pic)) {
                unlink("uploads/" . $old_pic);
            }
        }
    }

    // --- Update DB if no errors ---
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE businesses 
            SET business_name=?, business_email=?, business_contact=?, business_address=?, business_details=?, business_pic=?, status=? 
            WHERE id=?");
        $stmt->execute([$name, $email, $phone, $address, $details, $newPic, $status, $id]);

        $success = "Member updated successfully!";
    }
}

// ===== Handle Delete =====
if (isset($_POST['delete_member'])) {
    $id = $_POST['id'];
    $pic = $_POST['pic'];

    $stmt = $pdo->prepare("DELETE FROM businesses WHERE id=?");
    $stmt->execute([$id]);

    if ($pic && file_exists("uploads/" . $pic)) {
        unlink("uploads/" . $pic);
    }

    $success = "Member deleted successfully!";
}

// ===== Fetch All Members =====
$stmt = $pdo->query("SELECT * FROM businesses ORDER BY id ASC");
$members = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Member Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .container {
            max-width: 100vw;
        }

        .table-responsive-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        @media (max-width: 920px) {
            table.table {
                width: 100%;
                border: 0;
            }
            table.table thead {
                display: none;
            }
            table.table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 10px;
                padding: 0.5rem 1rem;
                background: #fff;
            }
            table.table tbody td {
                display: block;
                text-align: justify;
                padding: 0.4rem 0;
                border: 0;
                word-break: break-word;
            }
            table.table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                display: block;
                margin-bottom: 0.2rem;
            }
            table.table tbody td img {
                max-width: 100%;
                height: auto;
                border-radius: 5px;
            }
            table.table tbody td[data-label="Actions"] .d-flex {
                flex-direction: row;
                justify-content: flex-start;
                gap: 0.5rem;
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body class="bg-primary-subtle">
    <?php include 'admin_nav.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Member Details</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $err) echo "<p>$err</p>"; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="table-responsive-wrapper">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Business Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Business Details</th>
                        <th>Status</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $m): ?>
                        <tr>
                            <td data-label="ID"><?= $m['id'] ?></td>
                            <td data-label="Business Name"><?= htmlspecialchars($m['business_name']) ?></td>
                            <td data-label="Email"><?= htmlspecialchars($m['business_email']) ?></td>
                            <td data-label="Phone"><?= htmlspecialchars($m['business_contact']) ?></td>
                            <td data-label="Address"><?= htmlspecialchars($m['business_address']) ?></td>
                            <td data-label="Business Details"><?= htmlspecialchars($m['business_details']) ?></td>
                            <td data-label="Status">
                                <?php if ($m['status'] == 1): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php elseif ($m['status'] == 2): ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Image">
                                <?php if (!empty($m['business_pic'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($m['business_pic']) ?>"
                                        width="60" height="60" class="rounded"
                                        style="cursor:pointer;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#imageModal"
                                        onclick="showImage('uploads/<?= htmlspecialchars($m['business_pic']) ?>')">
                                <?php endif; ?>
                            </td>
                            <td data-label="Actions">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary editBtn"
                                        data-id="<?= $m['id'] ?>"
                                        data-name="<?= htmlspecialchars($m['business_name']) ?>"
                                        data-email="<?= htmlspecialchars($m['business_email']) ?>"
                                        data-phone="<?= htmlspecialchars($m['business_contact']) ?>"
                                        data-address="<?= htmlspecialchars($m['business_address']) ?>"
                                        data-details="<?= htmlspecialchars($m['business_details']) ?>"
                                        data-status="<?= $m['status'] ?>"
                                        data-pic="<?= htmlspecialchars($m['business_pic']) ?>">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger deleteBtn"
                                        data-id="<?= $m['id'] ?>"
                                        data-pic="<?= htmlspecialchars($m['business_pic']) ?>">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="old_pic" id="edit_old_pic">

                    <div class="mb-3">
                        <label>Business Name</label>
                        <input type="text" name="business_name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="business_email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" name="business_contact" id="edit_phone" class="form-control" pattern="[0-9]{10}" required>
                        <div class="form-text">Must be exactly 10 digits.</div>
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="business_address" id="edit_address" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Business Details</label>
                        <textarea name="business_details" id="edit_details" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="0">Pending</option>
                            <option value="1">Approved</option>
                            <option value="2">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Image (leave blank to keep old)</label>
                        <input type="file" name="business_pic" class="form-control" accept=".jpg,.jpeg,.png,.gif">
                        <img id="edit_preview" src="" class="mt-2 rounded" width="80">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit_member" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="delete_id">
                    <input type="hidden" name="pic" id="delete_pic">
                    <p>Are you sure you want to delete this member?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="delete_member" class="btn btn-danger">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center">
                    <img id="modalImage" class="rounded shadow" style="max-width:100%; max-height:90vh;">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showImage(src) {
            document.getElementById('modalImage').src = src;
        }

        document.querySelectorAll('.editBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('edit_id').value = btn.dataset.id;
                document.getElementById('edit_name').value = btn.dataset.name;
                document.getElementById('edit_email').value = btn.dataset.email;
                document.getElementById('edit_phone').value = btn.dataset.phone;
                document.getElementById('edit_address').value = btn.dataset.address;
                document.getElementById('edit_details').value = btn.dataset.details;
                document.getElementById('edit_status').value = btn.dataset.status;
                document.getElementById('edit_old_pic').value = btn.dataset.pic;
                document.getElementById('edit_preview').src = "uploads/" + btn.dataset.pic;
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
        });

        document.querySelectorAll('.deleteBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('delete_id').value = btn.dataset.id;
                document.getElementById('delete_pic').value = btn.dataset.pic;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });
    </script>
</body>
</html>
