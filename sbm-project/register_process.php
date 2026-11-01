<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('/auth.php'));
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$ward_id = (int)($_POST['ward_id'] ?? 1);
$password = trim($_POST['password'] ?? '');

$db = get_db();

// Check existing email
$chk = $db->prepare("SELECT id FROM users WHERE email = ?");
$chk->execute([$email]);
if ($chk->fetch()) {
    set_flash("An account with this email already exists. Please login.", "error");
    header('Location: ' . url('/auth.php'));
    exit;
}

$stmt = $db->prepare("
    INSERT INTO users (name, email, password, role, phone, ward_id, points, badge, avatar)
    VALUES (?, ?, ?, 'citizen', ?, ?, 50, 'Swachhta Mitra', '/assets/images/avatar_citizen1.svg')
");
$stmt->execute([$name, $email, $password, $phone, $ward_id]);
$new_id = $db->lastInsertId();

$_SESSION['user_id'] = $new_id;
$_SESSION['name'] = $name;
$_SESSION['email'] = $email;
$_SESSION['role'] = 'citizen';
$_SESSION['avatar'] = '/assets/images/avatar_citizen1.svg';

log_activity("New Citizen Enrolled", "Citizen {$name} joined Swachh Bharat Mission.", "User", "user-plus");

set_flash("Welcome to Swachh Bharat Mission, {$name}! You earned +50 welcome points.", "success");
header('Location: ' . url('/index.php'));
exit;
?>
