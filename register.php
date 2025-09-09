<?php
session_start();
require 'db.php';
require 'send_mail.php';

$errors = []; // Array to store errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $gender = trim($_POST['gender']);
    $dob = $_POST['dob'];
    $occupation = trim($_POST['occupation']);
    $qualification = trim($_POST['qualification']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Server-side validation
    if (empty($name)) $errors['name'] = "Full Name is required.";
    if (empty($email)) $errors['email'] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";
    if (empty($phone) || !ctype_digit($phone) || strlen($phone) !== 10) $errors['phone'] = "Phone must be exactly 10 digits.";
    if (empty($address)) $errors['address'] = "Address is required.";
    if (empty($gender)) $errors['gender'] = "Gender is required.";
    if (empty($dob)) $errors['dob'] = "Date of Birth is required.";
    if (empty($occupation)) $errors['occupation'] = "Occupation is required.";
    if (empty($qualification)) $errors['qualification'] = "Qualification is required.";
    if (empty($role)) $errors['role'] = "Role is required.";
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $errors['password'] = "Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.";
    }

    if (empty($errors)) {
        try {
            // Check duplicate email+role
            $check = $pdo->prepare("SELECT id FROM users WHERE email = :email AND role = :role");
            $check->execute([':email' => $email, ':role' => $role]);
            if ($check->fetch()) {
                $errors['email'] = "This email is already registered for the selected role.";
            } else {
                $otp = rand(100000, 999999);
                $_SESSION['pending_user'] = [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'gender' => $gender,
                    'dob' => $dob,
                    'occupation' => $occupation,
                    'qualification' => $qualification,
                    'password' => $password,
                    'role' => $role,
                    'otp' => $otp,
                    'otp_time' => time()
                ];

                if (sendOTP($email, $name, $otp)) {
                    header("Location: verify.php?type=register");
                    exit;
                } else {
                    $errors['otp'] = "Failed to send OTP email.";
                }
            }
        } catch (PDOException $e) {
            $errors['db'] = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1abc9c, #16a085);
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

        .btn-primary {
            border-radius: 10px;
            background: #2c3e50;
            border: none;
        }

        .btn-primary:hover {
            background: #1abc9c;
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
    <div class="card p-4 w-100" style="max-width: 600px;">
        <h3 class="text-center mb-3">User Registration</h3>

        <?php if (isset($errors['otp'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors['otp']) ?></div>
        <?php endif; ?>
        <?php if (isset($errors['db'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors['db']) ?></div>
        <?php endif; ?>

        <form method="POST" id="registerForm" novalidate>
            <div class="mb-2">
                <input class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                    type="text" name="name" placeholder="Full Name"
                    value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" required>
                <?php if (isset($errors['name'])): ?><div class="error"><?= $errors['name'] ?></div><?php endif; ?>
            </div>

            <div class="mb-2">
                <input class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                    type="email" name="email" placeholder="Email"
                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
                <?php if (isset($errors['email'])): ?><div class="error"><?= $errors['email'] ?></div><?php endif; ?>
            </div>

            <div class="mb-2">
                <input class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                    type="text" name="phone" placeholder="Phone (10 digits)" maxlength="10"
                    value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>" required>
                <?php if (isset($errors['phone'])): ?><div class="error"><?= $errors['phone'] ?></div><?php endif; ?>
            </div>

            <div class="mb-2">
                <input class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>"
                    type="text" name="address" placeholder="Address"
                    value="<?= isset($address) ? htmlspecialchars($address) : '' ?>" required>
                <?php if (isset($errors['address'])): ?><div class="error"><?= $errors['address'] ?></div><?php endif; ?>
            </div>

            <div class="mb-2">
                <select class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>"
                    name="gender" required>
                    <option value="">Select Gender</option>
                    <option <?= (isset($gender) && $gender == 'Male') ? 'selected' : '' ?>>Male</option>
                    <option <?= (isset($gender) && $gender == 'Female') ? 'selected' : '' ?>>Female</option>
                    <option <?= (isset($gender) && $gender == 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
                <?php if (isset($errors['gender'])): ?><div class="error"><?= $errors['gender'] ?></div><?php endif; ?>
            </div>

            <div class="mb-2">
                <input
                    class="form-control <?= isset($errors['dob']) ? 'is-invalid' : '' ?>"
                    type="text"
                    name="dob"
                    placeholder="DOB (dd/mm/yyyy)"
                    value="<?= isset($dob) ? htmlspecialchars($dob) : '' ?>"
                    required
                    onfocus="this.type='date'; this.max = new Date().toISOString().split('T')[0];"
                    onblur="if(!this.value)this.type='text'">
                <?php if (isset($errors['dob'])): ?><div class="error"><?= $errors['dob'] ?></div><?php endif; ?>
            </div>


            <div class="mb-2">
                <input class="form-control <?= isset($errors['occupation']) ? 'is-invalid' : '' ?>"
                    type="text" name="occupation" placeholder="Occupation"
                    value="<?= isset($occupation) ? htmlspecialchars($occupation) : '' ?>" required>
                <?php if (isset($errors['occupation'])): ?><div class="error"><?= $errors['occupation'] ?></div><?php endif; ?>
            </div>

            <div class="mb-2">
                <input class="form-control <?= isset($errors['qualification']) ? 'is-invalid' : '' ?>"
                    type="text" name="qualification" placeholder="Qualification"
                    value="<?= isset($qualification) ? htmlspecialchars($qualification) : '' ?>" required>
                <?php if (isset($errors['qualification'])): ?><div class="error"><?= $errors['qualification'] ?></div><?php endif; ?>
            </div>

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

            <div class="mb-3">
                <input class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                    type="password" name="password" placeholder="Password" required>
                <?php if (isset($errors['password'])): ?><div class="error"><?= $errors['password'] ?></div><?php endif; ?>
            </div>

            <button class="btn btn-primary w-100" type="submit">Register</button>
        </form>

        <p class="text-center mt-2">Already registered? <a href="login.php">Login here</a></p>
    </div>
</body>

</html>