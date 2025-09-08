<?php

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

        $mail->setFrom("dubaispace1234@gmail.com", "Dubai Space");
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);
        $mail->Subject = "Your OTP Verification Code";
        $mail->Body = "<h3>Your OTP is: <b>$otp</b></h3><p>Valid for 5 minutes.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
