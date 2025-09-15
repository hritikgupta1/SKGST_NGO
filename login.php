<?php
session_start();

require 'db.php';

$errors = [];

// If user came from another page (like donate.php), save it in session
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validation
    if (empty($email)) $errors['email'] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";
    if (empty($password)) $errors['password'] = "Password is required.";
    if (empty($role)) $errors['role'] = "Role is required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND password = :password AND role = :role");
            $stmt->execute([':email' => $email, ':password' => $password, ':role' => $role]);
            $user = $stmt->fetch();

            if ($user) {
                // Save the full row in session (not just role)
                $_SESSION['user'] = [
                    'id'          => $user['id'],
                    'name'        => $user['name'],
                    'email'       => $user['email'],
                    'phone'       => $user['phone'],
                    'address'     => $user['address'],
                    'gender'      => $user['gender'],
                    'dob'         => $user['dob'],
                    'occupation'  => $user['occupation'],
                    'qualification' => $user['qualification'],
                    'role'        => $user['role']
                ];

                // Redirect (or back to donate if stored)
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirectPage = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: " . $redirectPage);
                    exit;
                }

                // Redirect by role
                if ($role == "Admin") header("Location: admin.php");
                elseif ($role == "User") header("Location: user.php");
                elseif ($role == "Member") header("Location: member.php");
                elseif ($role == "Team member") header("Location: team.php");
                exit;
            } else {
                $errors['email'] = "Invalid email, password, or role!";
                $errors['password'] = "Invalid email, password, or role!";
                $errors['role'] = "Invalid email, password, or role!";
            }
        } catch (PDOException $e) {
            $errors['email'] = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3498db, #2980b9);
            min-height: 100vh;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .2);
        }

        .form-control,
        .form-select {
            border-radius: 10px;
        }

        .btn-success,
        .btn-outline-primary {
            border-radius: 10px;
        }

        .toggle-password {
            cursor: pointer;
        }

        .error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        @media (max-width:768px) {
            .card {
                margin: 10px;
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center">

    <div class="card p-4 w-100" style="max-width: 400px;">
        <h3 class="text-center mb-3">User Login</h3>

        <form method="POST" id="loginForm" novalidate>
            <div class="mb-2">
                <input class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" type="email" name="email" placeholder="Email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                <?php if (isset($errors['email'])): ?><div class="error"><?= $errors['email'] ?></div><?php endif; ?>
            </div>

            <div class="mb-3 input-group">
                <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" type="password" name="password" id="password" placeholder="Password" required>
                <button class="btn btn-outline-secondary toggle-password" type="button">👁️</button>
            </div>
            <?php if (isset($errors['password'])): ?><div class="error mb-2"><?= $errors['password'] ?></div><?php endif; ?>

            <div class="mb-2">
                <select class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>" name="role" required>
                    <option value="">Select Role</option>
                    <option value="Admin" <?= (isset($role) && $role == 'Admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="User" <?= (isset($role) && $role == 'User') ? 'selected' : '' ?>>User</option>
                    <option value="Member" <?= (isset($role) && $role == 'Member') ? 'selected' : '' ?>>Member</option>
                    <option value="Team member" <?= (isset($role) && $role == 'Team member') ? 'selected' : '' ?>>Team member</option>
                </select>
                <?php if (isset($errors['role'])): ?><div class="error"><?= $errors['role'] ?></div><?php endif; ?>
            </div>

            <button class="btn btn-success w-100">Login</button>
        </form>

        <p class="text-center mt-2">
            Don't have an account? <a href="register.php">Register here</a><br>
            <a href="forgot.php">Forgot Password?</a>
        </p>

        <div class="text-center mt-3">
            <a href="index.html" class="btn btn-outline-primary w-50">← Back to Home</a>
        </div>
    </div>

    <script>
        // Show/Hide password toggle
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