<?php
require_once __DIR__ . '/includes/functions.php';

session_unset();
session_destroy();

session_start();
set_flash("You have been signed out successfully.", "success");
header('Location: ' . url('/index.php'));
exit;
?>
