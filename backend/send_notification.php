<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

function sendCustomerNotification(
    int $submissionId,
    string $fullName,
    string $subject,
    string $deviceBrand,
    string $deviceModel
): bool {

    $config = require __DIR__ . '/mail_config.php';

    if (!$config['username'] || !$config['password']) {
        error_log('SMTP credentials are not configured.');
        return false;
    }

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->SMTPDebug = 2;

$mail->Debugoutput = function ($message, $level) {
    error_log("PHPMailer [$level]: $message");
};

        $mail->Host = $config['host'];

        $mail->SMTPAuth = true;

        $mail->Username = $config['username'];

        $mail->Password = $config['password'];

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = $config['port'];

        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            $config['from_email'],
            $config['from_name']
        );

        $mail->addAddress(
            $config['notification_email']
        );

        $mail->isHTML(false);

        $mail->Subject = 'New Customer Enquiry - MasterTee Tech';

        $mail->Body =
            "A new customer enquiry has been received.\n\n" .
            "Submission ID: " . $submissionId . "\n" .
            "Customer Name: " . $fullName . "\n" .
            "Subject: " . $subject . "\n" .
            "Device Brand: " . $deviceBrand . "\n" .
            "Device Model: " . $deviceModel . "\n\n" .
            "Open your customer records to view the full details.";

        $mail->send();

        return true;

    } catch (Exception $e) {

        error_log('PHPMailer error: ' . $mail->ErrorInfo);

        return false;
    }
}