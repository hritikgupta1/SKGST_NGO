<?php
// in local host use below code for sending mails:
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendOTP($toEmail, $toName, $otp)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "dubaispace1234@gmail.com";   
        $mail->Password = "qocz bivr nowk aetw";       
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        // Sender details
        $mail->setFrom("dubaispace1234@gmail.com", "Dubai Space");

        // Recipient
        $mail->addAddress($toEmail, $toName);

        // Add CC & BCC
        // $mail->addCC("acpitzone@gmail.com");
        // $mail->addBCC("praveshgulati@gmail.com");

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Verification Code";
        $mail->Body = "<h3>Your OTP is: <b>$otp</b></h3><p>Valid for 5 minutes.</p>";

        // Send email
        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}


// in cpanel use below code for sending mails

// function sendOTP($toEmail, $toName, $otp)
// {
//     $subject = "Your OTP Verification Code";

//     $message = "
//     <html>
//     <head>
//         <title>SKGST - OTP</title>
//     </head>
//     <body>
//         <h3>Hi $toName,</h3>
//         <p>Your OTP is: <b>$otp</b>. It is valid for <b>5 minutes</b>.</p>
//     </body>
//     </html>";

//     // Email headers
//     $headers  = "MIME-Version: 1.0\r\n";
//     $headers .= "Content-type: text/html; charset=UTF-8\r\n";
//     $headers .= "From: SKGST <noreply@skgst.in>\r\n";
//     // $headers .= "Cc: acpitzone@gmail.com\r\n";
//     // $headers .= "Bcc: praveshgulati@gmail.com\r\n";

//     // Send email using PHP mail()
//     if (mail($toEmail, $subject, $message, $headers)) {
//         return true;
//     } else {
//         return false;
//     }
// }
?>


