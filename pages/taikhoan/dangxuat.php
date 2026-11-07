<?php
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfValid()) {
    logoutUser();
    header('Location: dangnhap.php?loggedout=1');
} else {
    header('Location: ' . BASE_URL . '/index.php');
}
exit;
?>