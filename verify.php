<?php
session_start();
require 'db.php';
require 'send_mail.php';

$message = '';
$type = $_GET['type'] ?? 'register';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp'])) {
    $otp = trim($_POST['otp']);

    if (isset($_SESSION['pending_user'])) {
        $user = $_SESSION['pending_user'];

        // Validate OTP input
        if (!ctype_digit($otp) || strlen($otp) != 6) {
            $message = "Enter a valid 6-digit OTP.";
        }
        // Expired?
        elseif (time() - $user['otp_time'] > 300) {
            $message = "OTP expired. Please resend.";
            unset($_SESSION['pending_user']); // clear expired session
        }
        // Success
        elseif ($otp == $user['otp']) {
            if ($type == 'register') {
                $insert = $pdo->prepare("INSERT INTO pending_user 
                    (name, email, phone, address, gender, dob, occupation, qualification, password, role)
                    VALUES (:name, :email, :phone, :address, :gender, :dob, :occupation, :qualification, :password, :role)");
                $insert->execute([
                    ':name' => $user['name'],
                    ':email' => $user['email'],
                    ':phone' => $user['phone'],
                    ':address' => $user['address'],
                    ':gender' => $user['gender'],
                    ':dob' => $user['dob'],
                    ':occupation' => $user['occupation'],
                    ':qualification' => $user['qualification'],
                    ':password' => $user['password'],
                    ':role' => $user['role']
                ]);
                $message = "Email verified successfully! Your registration is under review.";
            } else {
                // forgot password reset
                $update = $pdo->prepare("UPDATE users SET password=:password WHERE email=:email AND role=:role");
                $update->execute([
                    ':password' => $user['password'],
                    ':email' => $user['email'],
                    ':role' => $user['role']
                ]);
                $message = "Password reset successful! <a href='login.php'>Login</a>";
            }
            unset($_SESSION['pending_user']); // clear after success
        } else {
            $message = "Invalid OTP.";
        }
    } else {
        $message = "No pending request.";
    }
}

// Resend OTP
if (isset($_POST['resend']) && isset($_SESSION['pending_user'])) {
    $user = $_SESSION['pending_user'];
    if (time() - $user['otp_time'] < 60) {
        $message = "Please wait before resending OTP.";
    } else {
        $otp = rand(100000, 999999);
        $_SESSION['pending_user']['otp'] = $otp;
        $_SESSION['pending_user']['otp_time'] = time();
        sendOTP($user['email'], $user['name'] ?? 'User', $otp);
        $message = "New OTP sent.";
    }
}
?>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media (max-width:768px) {
            .p-4 {
                padding: 1.5rem !important;
                margin: 10px;
            }
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;background:#f7f7f7;">
    <div class="card p-4" style="max-width:400px;">
        <h4 class="text-center">Enter OTP</h4>
        <?php if ($message): ?><div class="alert alert-info"><?= $message ?></div><?php endif; ?>

        <form method="POST">
            <input class="form-control mb-3" type="text" name="otp" placeholder="6-digit OTP" required>
            <button class="btn btn-success w-100 mb-2">Verify</button>
        </form>
        <!-- Timer -->
        <div class="text-center mb-2" id="timer">Resend OTP in 60s</div>
        <form method="POST">
            <button name="resend" class="btn btn-link w-100" id="resendBtn" disabled>Resend OTP</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php" class="btn btn-outline-primary w-100">← Back to Login</a>
        </div>
    </div>

    <script>
        // Enable resend button after 60s
        let btn = document.getElementById("resendBtn");
        setTimeout(() => btn.disabled = false, 60000);

        // Countdown timer for resend OTP
        let countdown = 60;
        let timerEl = document.getElementById("timer");
        let resendBtn = document.getElementById("resendBtn");

        let interval = setInterval(() => {
            countdown--;
            timerEl.innerText = `Resend OTP in ${countdown}s`;
            if (countdown <= 0) {
                clearInterval(interval);
                timerEl.innerText = "You can resend OTP now";
                resendBtn.disabled = false;
            }
        }, 1000);
    </script>
</body>

</html>