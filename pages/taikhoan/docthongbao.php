<?php
require_once __DIR__ . '/../../includes/auth.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrfValid()) {
    http_response_code(400);
    exit('Yeu cau khong hop le.');
}

getDB()->prepare("UPDATE thongbao SET DaDoc = 1 WHERE ID_NguoiDung = ? AND DaDoc = 0")
       ->execute([$_SESSION['user_id']]);

echo 'ok';