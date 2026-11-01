<?php
require_once __DIR__ . '/includes/functions.php';

$role = $_GET['role'] ?? 'citizen';
$db = get_db();

if ($role === 'admin') {
    $stmt = $db->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
    $target = '/admin/index.php';
} elseif ($role === 'worker') {
    $stmt = $db->query("SELECT * FROM users WHERE role = 'worker' LIMIT 1");
    $target = '/worker/tasks.php';
} else {
    $stmt = $db->query("SELECT * FROM users WHERE role = 'citizen' LIMIT 1");
    $target = '/index.php';
}

$user = $stmt->fetch();

if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['avatar'] = $user['avatar'];
    set_flash("Switched persona to: {$user['name']} (" . ucfirst($user['role']) . ")", "success");
}

header("Location: " . url($target));
exit;
?>
