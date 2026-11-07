<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../config/mail.php';

function sendMail(string $to, string $subject, string $body): bool {
    $dir = __DIR__ . '/PHPMailer/src';

    if (MAIL_ENABLED && is_file($dir . '/PHPMailer.php')) {
        require_once $dir . '/Exception.php';
        require_once $dir . '/PHPMailer.php';
        require_once $dir . '/SMTP.php';
        try {
            $m = new PHPMailer(true);
            $m->isSMTP();
            $m->Host       = MAIL_HOST;
            $m->SMTPAuth   = true;
            $m->Username   = MAIL_USER;
            $m->Password   = MAIL_PASS;
            $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $m->Port       = MAIL_PORT;
            $m->CharSet    = 'UTF-8';
            $m->setFrom(MAIL_USER, MAIL_FROM_NAME);
            $m->addAddress($to);
            $m->Subject = $subject;
            $m->Body    = $body;
            $m->send();
            return true;
        } catch (Exception $e) {
            error_log('Mail error: ' . $e->getMessage());
            return false;
        }
    }

    // Che do test: ghi ra file de lay ma xac nhan
    $line = "[" . date('Y-m-d H:i:s') . "] To: $to | $subject\n$body\n" . str_repeat('-', 40) . "\n";
    return file_put_contents(__DIR__ . '/../storage/mail.log', $line, FILE_APPEND | LOCK_EX) !== false;
}