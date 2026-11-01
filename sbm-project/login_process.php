<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('/auth.php'));
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$db = get_db();
$stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
$stmt->execute([$email, $password]);
$user = $stmt->fetch();

if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['avatar'] = $user['avatar'];

    set_flash("Welcome back, {$user['name']}! Logged in as " . ucfirst($user['role']) . ".", "success");

    if ($user['role'] === 'admin') {
        header('Location: ' . url('/admin/index.php'));
    } elseif ($user['role'] === 'worker') {
        header('Location: ' . url('/worker/tasks.php'));
    } else {
        header('Location: ' . url('/index.php'));
    }
    exit;
} else {
    set_flash("Invalid email or password. Please try again.", "error");
    header('Location: ' . url('/auth.php'));
    exit;
}
?>
