<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

session_start();
require 'db.php';

// Only admin can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Admin') {
    header("Location: login.php");
    exit;
}

$message = "";

//-----function to send email for localhost----
function sendUserEmail($toEmail, $toName, $role, $status)
{
    $mail = new PHPMailer(true);
    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "dubaispace1234@gmail.com";   // your Gmail
        $mail->Password = "qocz bivr nowk aetw";       // your Gmail App Password
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        // Sender details
        $mail->setFrom("dubaispace1234@gmail.com", "SKGST");

        // Recipient
        $mail->addAddress($toEmail, $toName);

        // (Optional) CC & BCC
        // $mail->addCC("acpitzone@gmail.com");
        // $mail->addBCC("praveshgulati@gmail.com");

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "SKGST - Account " . ucfirst($status);

        if ($status === "approved") {
            $mail->Body = "
            <html>
            <body>
                <h3>Dear $toName,</h3>
                <p>Your email is now active as <b>$role</b>. You can login into <b>SKGST</b>.</p>
                <p><a href='http://localhost/SKGST_NGO/login.php'>Click here to login</a></p>
            </body>
            </html>";
        } else {
            $mail->Body = "
            <html>
            <body>
                <h3>Dear $toName,</h3>
                <p>We regret to inform you that your registration as <b>$role</b> has been denied by the admin.</p>
                <p>You may try again with valid details.</p>
            </body>
            </html>";
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo); // debug if needed
        return false;
    }
}

// ---- Function to send email for cpanel ----
// function sendUserEmail($toEmail, $toName, $role, $status) {
//     $subject = "SKGST - Account " . ucfirst($status);

//     if ($status === "approved") {
//         $body = "
//         <html>
//         <body>
//             <h3>Dear $toName,</h3>
//             <p>Your email is now active as <b>$role</b>. You can login into <b>SKGST</b>.</p>
//             <p><a href='http://skgst.in/login.php'>Click here to login</a></p>
//         </body>
//         </html>";
//     } else {
//         $body = "
//         <html>
//         <body>
//             <h3>Dear $toName,</h3>
//             <p>We regret to inform you that your registration as <b>$role</b> has been denied by the admin.</p>
//             <p>You may try again with valid details.</p>
//         </body>
//         </html>";
//     }

//     $headers  = "MIME-Version: 1.0\r\n";
//     $headers .= "Content-type: text/html; charset=UTF-8\r\n";
//     $headers .= "From: SKGST <noreply@skgst.in>\r\n";

//     return mail($toEmail, $subject, $body, $headers);
// }

// ---- Handle Approve/Deny actions ----
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];

    $stmt = $pdo->prepare("SELECT * FROM pending_user WHERE id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user) {
        if ($action === "approve") {
            // Insert into users
            $stmt = $pdo->prepare("INSERT INTO users (name,email,phone,address,gender,dob,occupation,qualification,password,role)
                                   VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $user['name'],
                $user['email'],
                $user['phone'],
                $user['address'],
                $user['gender'],
                $user['dob'],
                $user['occupation'],
                $user['qualification'],
                $user['password'], // already stored
                $user['role']
            ]);

            // Delete from pending_user
            $pdo->prepare("DELETE FROM pending_user WHERE id=?")->execute([$id]);

            // Send email
            sendUserEmail($user['email'], $user['name'], $user['role'], "approved");

            $message = "User approved and moved to users table.";
        } elseif ($action === "deny") {
            // Delete only
            $pdo->prepare("DELETE FROM pending_user WHERE id=?")->execute([$id]);

            // Send denial email
            sendUserEmail($user['email'], $user['name'], $user['role'], "denied");

            $message = "User denied and removed from pending list.";
        }
    }
}

// Fetch all pending users
$stmt = $pdo->query("SELECT * FROM pending_user ORDER BY created_at DESC");
$pending_users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pending Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .container {
            max-width: 100vw;
        }

        /* Make table container scrollable on small screens */
        .table-responsive-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .d-flex{
            justify-content: space-evenly;
        }

        /* Card-style view on small screens */
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
                padding: 0.8rem 1rem;
                background: #fff;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            }

            table.table tbody td {
                display: block;
                text-align: left;
                padding: 0.4rem 0;
                border: 0;
                word-break: break-word;
            }

            table.table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                display: block;
                margin-bottom: 0.2rem;
                color: #555;
            }

            /* Buttons horizontal + wrapping */
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
        <h2 class="mb-4">Pending User Requests</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($pending_users): ?>
            <div class="table-responsive-wrapper">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Applied On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_users as $u): ?>
                            <tr>
                                <td data-label="Name"><?= htmlspecialchars($u['name']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($u['email']) ?></td>
                                <td data-label="Role"><?= ucfirst($u['role']) ?></td>
                                <td data-label="Applied On"><?= $u['created_at'] ?></td>
                                <td data-label="Actions">
                                    <div class="d-flex">
                                        <a class="btn btn-sm btn-success" href="?action=approve&id=<?= $u['id'] ?>">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </a>
                                        <a class="btn btn-sm btn-danger" href="?action=deny&id=<?= $u['id'] ?>">
                                            <i class="bi bi-x-circle"></i> Deny
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="alert alert-info">No pending users right now.</div>
        <?php endif; ?>
    </div>

</body>

</html>