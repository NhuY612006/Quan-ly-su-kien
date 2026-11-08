<?php
// false: khong gui mail that, chi ghi noi dung vao storage/mail.log (dung de test tren XAMPP)
// true : gui qua SMTP Gmail bang PHPMailer (can thu muc includes/PHPMailer)
define('MAIL_ENABLED', false);

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'email@gmail.com');
define('MAIL_PASS', 'mat-khau-ung-dung');   // App Password cua Google, KHONG phai mat khau Gmail
define('MAIL_FROM_NAME', 'Music4Life');
?>