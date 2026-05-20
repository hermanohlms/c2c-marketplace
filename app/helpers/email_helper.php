<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

function sendEmail($to, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host = getenv('MAIL_HOST');
        $mail->SMTPAuth = true;

        $mail->Username = getenv('MAIL_USERNAME');
        $mail->Password = getenv('MAIL_PASSWORD');

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = getenv('MAIL_PORT');

        $mail->setFrom(
            getenv('MAIL_FROM'),
            getenv('MAIL_FROM_NAME')
        );

        $mail->addAddress($to);

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $body;

        return $mail->send();
    } catch (Exception $e) {

        error_log('Email failed: ' . $mail->ErrorInfo);

        return false;
    }
}
