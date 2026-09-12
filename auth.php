<?php
/**
 * Auth Handler
 * Ibnu Wedding Invitation
 */

session_start();

// Kredensial default admin
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = 'Admin Panitia';
        $_SESSION['login_time'] = time();

        header('Location: admin.php');
        exit;
    } else {
        header('Location: login.php?error=1');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
