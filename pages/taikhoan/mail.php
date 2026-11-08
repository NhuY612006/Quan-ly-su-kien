
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../config/mail.php';

function sendMail(string $to, string $subject, string $body): bool {

    // Chế độ gửi Gmail thật
    if (MAIL_ENABLED) {
        $dir = __DIR__ . '/../../PHPMailer/src';

        require_once $dir . '/Exception.php';
        require_once $dir . '/PHPMailer.php';
        require_once $dir . '/SMTP.php';

        try {
            $m = new PHPMailer(true);
            $m->isSMTP();
            $m->Host = MAIL_HOST;
            $m->SMTPAuth = true;
            $m->Username = MAIL_USER;
            $m->Password = MAIL_PASS;
            $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $m->Port = MAIL_PORT;
            $m->CharSet = 'UTF-8';

            $m->setFrom(MAIL_USER, MAIL_FROM_NAME);
            $m->addAddress($to);
            $m->Subject = $subject;
            $m->Body = $body;

            $m->send();
            return true;

        } catch (Exception $e) {
            error_log('Mail error: ' . $m->ErrorInfo);
            return false;
        }
    }

    // Chế độ test: lưu mã xác minh vào file
    $storage = __DIR__ . '/../../storage';

    if (!is_dir($storage)) {
        mkdir($storage, 0777, true);
    }

    $line = "[" . date('Y-m-d H:i:s') . "] To: $to | $subject\n";
    $line .= $body . "\n";
    $line .= str_repeat('-', 40) . "\n";

    return file_put_contents(
        $storage . '/mail.log',
        $line,
        FILE_APPEND | LOCK_EX
    ) !== false;
}
