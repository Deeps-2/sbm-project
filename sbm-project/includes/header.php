<?php
require_once __DIR__ . '/functions.php';
$page_title = $page_title ?? 'Swachh Bharat Mission';
$current    = basename($_SERVER['SCRIPT_NAME']);
$flash      = get_flash();
$nav = [
    'index.php'       => 'Dashboard',
    'areas.php'       => 'Ward Scores',
    'map.php'         => 'Map',
    'report.php'      => 'Report Issue',
    'track.php'       => 'Track',
    'leaderboard.php' => 'Leaderboard',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($page_title); ?> | SBM Portal</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <link rel="stylesheet" href="<?php echo url('/assets/css/style.css'); ?>">
</head>
<body>

<header class="sbm-topbar">
  <a href="<?php echo url('/index.php'); ?>" class="sbm-logo">
    <img src="<?php echo url('/assets/images/sbm_logo.svg'); ?>" alt="SBM Portal">
  </a>

  <form class="sbm-topbar-search" id="global-search-form" autocomplete="off">
    <span aria-hidden="true">🔍</span>
    <input type="text" id="global-search" placeholder="Search a ward or paste a tracking code (SBM-2026-1042)">
  </form>

  <div class="sbm-clock">
    <div id="live-clock">--:--:--</div>
    <div id="live-date"></div>
  </div>
</header>

<nav class="sbm-nav">
  <?php foreach ($nav as $file => $label): ?>
    <a href="<?php echo url('/' . $file); ?>" class="<?php echo $current === $file ? 'active' : ''; ?>"><?php echo $label; ?></a>
  <?php endforeach; ?>

  <span class="sbm-nav-spacer"></span>

  <?php if (!empty($_SESSION['user_id'])): ?>
    <?php if ($_SESSION['role'] === 'admin'): ?><a href="<?php echo url('/admin/index.php'); ?>">Admin Panel</a><?php endif; ?>
    <?php if ($_SESSION['role'] === 'worker'): ?><a href="<?php echo url('/worker/tasks.php'); ?>">My Tasks</a><?php endif; ?>
    <span class="sbm-nav-user">
      <img src="<?php echo url($_SESSION['avatar'] ?? '/assets/images/avatar_citizen1.svg'); ?>" alt="">
      <?php echo htmlspecialchars($_SESSION['name']); ?>
    </span>
    <a href="<?php echo url('/logout.php'); ?>">Logout</a>
  <?php else: ?>
    <a href="<?php echo url('/auth.php'); ?>" class="sbm-nav-login <?php echo $current === 'auth.php' ? 'active' : ''; ?>">Login / Register</a>
  <?php endif; ?>
</nav>

<?php if ($flash): ?>
  <div class="sbm-flash sbm-flash-<?php echo htmlspecialchars($flash['type']); ?>"><?php echo htmlspecialchars($flash['msg']); ?></div>
<?php endif; ?>

<main class="sbm-main">
