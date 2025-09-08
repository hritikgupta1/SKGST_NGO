<?php
session_start();
require 'db.php';
require 'send_mail.php'; // your function sendOTP($to,$name,$otp)

$errors = []; // store errors for each field

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role'] ?? '');

    // Server-side validation
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors['password'] = "New password is required.";
    }

    if (empty($role)) {
        $errors['role'] = "Please select a role.";
    }

    if (empty($errors)) {
        try {
            $check = $pdo->prepare("SELECT id, name FROM users WHERE email = :email AND role = :role");
            $check->execute([':email' => $email, ':role' => $role]);
            $user = $check->fetch();

            if (!$user) {
                $errors['email'] = "No account found with this email and role!";
            } else {
                // Generate OTP
                $otp = rand(100000, 999999);
                $_SESSION['pending_user'] = [
                    'email' => $email,
                    'password' => $password, // plain as per your request
                    'role' => $role,
                    'otp' => $otp,
                    'otp_time' => time(),
                    'name' => $user['name']
                ];

                // Send OTP email
                sendOTP($email, $user['name'], $otp);

                // Redirect to OTP verification
                header("Location: verify.php?type=forgot");
                exit;
            }
        } catch (PDOException $e) {
            $errors['general'] = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
            min-height: 100vh;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .2);
        }

        .form-control, .form-select {
            border-radius: 10px;
        }

        .btn-warning {
            border-radius: 10px;
        }

        .toggle-password {
            cursor: pointer;
        }

        .error-msg {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }

        @media (max-width:768px) {
            .card {
                margin: 10px;
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">
    <div class="card p-4 w-100" style="max-width: 450px;">
        <h3 class="text-center mb-3">Forgot Password</h3>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?= $errors['general'] ?></div>
        <?php endif; ?>

        <form method="POST" id="forgotForm" novalidate>
            <div class="mb-3">
                <input class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                       type="email" name="email" placeholder="Registered Email" 
                       value="<?= htmlspecialchars($email ?? '') ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="error-msg"><?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                           type="password" name="password" id="password" placeholder="New Password">
                    <button class="btn btn-outline-secondary toggle-password" type="button">👁️</button>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <div class="error-msg"><?= $errors['password'] ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" name="role" required>
                    <option value="">Select Role</option>
                    <option <?= (isset($role) && $role == 'Admin') ? 'selected' : '' ?>>Admin</option>
                    <option <?= (isset($role) && $role == 'User') ? 'selected' : '' ?>>User</option>
                    <option <?= (isset($role) && $role == 'Member') ? 'selected' : '' ?>>Member</option>
                    <option <?= (isset($role) && $role == 'Team member') ? 'selected' : '' ?>>Team member</option>
                </select>
                <?php if (isset($errors['role'])): ?>
                    <div class="error-msg"><?= $errors['role'] ?></div>
                <?php endif; ?>
            </div>

            <button class="btn btn-warning w-100">Send OTP</button>
        </form>

        <p class="text-center mt-2">
            Remembered your password? <a href="login.php">Login here</a>
        </p>
    </div>

    <script>
        // Show/Hide password
        document.querySelector(".toggle-password").addEventListener("click", function() {
            let pwd = document.getElementById("password");
            if (pwd.type === "password") {
                pwd.type = "text";
                this.textContent = "🙈";
            } else {
                pwd.type = "password";
                this.textContent = "👁️";
            }
        });
    </script>
</body>
</html>
