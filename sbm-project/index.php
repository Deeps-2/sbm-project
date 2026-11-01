<?php
$page_title = "Building a Clean India Together";
require_once __DIR__ . '/includes/header.php';

$db = get_db();

// 1. Fetch live metrics
$areas_stmt = $db->query("SELECT * FROM areas ORDER BY cleanliness_score DESC");
$areas = $areas_stmt->fetchAll();
$total_areas = count($areas);

$total_score_sum = 0;
foreach ($areas as $a) {
    $total_score_sum += (int)$a['cleanliness_score'];
}
$city_average_score = $total_areas > 0 ? (int)round($total_score_sum / $total_areas) : 75;

$resolved_stmt = $db->query("SELECT COUNT(*) as cnt FROM complaints WHERE status = 'Resolved'");
$resolved_count = (int)($resolved_stmt->fetch()['cnt'] ?? 0);

$citizens_stmt = $db->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'citizen'");
$total_citizens = (int)($citizens_stmt->fetch()['cnt'] ?? 0);

// 2. Top Citizens for mini leaderboard
$top_stmt = $db->query("
    SELECT u.*, COUNT(c.id) as reports_count
    FROM users u
    LEFT JOIN complaints c ON u.id = c.user_id AND c.status = 'Resolved'
    WHERE u.role = 'citizen'
    GROUP BY u.id
    ORDER BY u.points DESC
    LIMIT 4
");
$top_citizens = $top_stmt->fetchAll();

// 3. Recent Activities
$act_stmt = $db->query("SELECT * FROM activities ORDER BY id DESC LIMIT 5");
$recent_activities = $act_stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="sbm-hero">
  <div class="sbm-hero-content">
    <div class="sbm-hero-badge">
      <span>🇮🇳 SWACHH BHARAT MISSION URBAN</span>
    </div>
    <h1 class="sbm-hero-title">
      Building a Spotless, <br><span>Clean India</span> Together
    </h1>
    <p class="sbm-hero-desc">
      Monitor real-time ward cleanliness scores, report sanitation grievances with instant photo uploads, earn citizen recognition points, and verify worker cleanings with transparent Before-and-After photo audits.
    </p>
    <div class="sbm-hero-actions">
      <a href="<?php echo url('/report.php'); ?>" class="btn btn-primary" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
        🚨 Report Cleanliness Issue
      </a>
      <a href="<?php echo url('/map.php'); ?>" class="btn btn-outline" style="color: #ffffff; border-color: #ffffff;">
        🗺️ Explore Cleanliness Map
      </a>
      <a href="<?php echo url('/track.php'); ?>" class="btn btn-outline" style="color: #ffffff; border-color: rgba(255,255,255,0.4);">
        🔍 Track Your Complaint
      </a>
    </div>
  </div>
</section>

<!-- Live Stats Strip -->
<div class="sbm-stats-strip">
  <div class="sbm-stats-container">
    <div class="sbm-stat-item">
      <div class="number" style="color: #16a34a;"><?php echo $city_average_score; ?>/100</div>
      <div class="label">City Cleanliness Index</div>
    </div>
    <div class="sbm-stat-item">
      <div class="number"><?php echo $total_areas; ?></div>
      <div class="label">Monitored Municipal Wards</div>
    </div>
    <div class="sbm-stat-item">
      <div class="number" style="color: #0284c7;"><?php echo $resolved_count; ?></div>
      <div class="label">Verified Cleanings Done</div>
    </div>
    <div class="sbm-stat-item">
      <div class="number" style="color: #eab308;"><?php echo $total_citizens; ?></div>
      <div class="label">Active Swachh Warriors</div>
    </div>
  </div>
</div>

<div class="sbm-container">

  <!-- Interactive Before & After Feature Showcase -->
  <div class="sbm-card" style="padding: 32px; background: linear-gradient(to right bottom, #ffffff, #f8fafc);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
      <div>
        <span class="badge badge-clean">FEATURED INNOVATION</span>
        <h2 style="font-size: 1.6rem; font-weight: 800; color: #0d5377; margin-top: 6px;">
          Before & After Image Verification System
        </h2>
        <p style="color: #64748b; font-size: 0.95rem;">
          Field workers upload authentic photographs before and after cleaning. Drag the slider below to inspect the verified transformation!
        </p>
      </div>
      <a href="<?php echo url('/track.php?code=SBM-2026-1042'); ?>" class="btn btn-outline btn-sm">
        View Live Verification Case &rarr;
      </a>
    </div>

    <!-- Interactive Comparator Slider -->
    <div class="sbm-comparator-container" id="comparator-box">
      <!-- Background Before Image -->
      <img src="<?php echo url('/assets/uploads/sample_before.svg'); ?>" alt="Before Cleaning" class="sbm-comparator-img">
      <span class="sbm-comparator-label sbm-label-before">Before Cleaning</span>

      <!-- Foreground After Image (Clipped) -->
      <div class="sbm-comparator-after" style="width: 50%;">
        <img src="<?php echo url('/assets/uploads/sample_after.svg'); ?>" alt="After Cleaning" class="sbm-comparator-img" style="width: 680px; max-width: 680px;">
        <span class="sbm-comparator-label sbm-label-after">After Cleaning</span>
      </div>

      <!-- Drag Handle -->
      <div class="sbm-comparator-handle" style="left: 50%;">
        ⬄
      </div>
    </div>
    <div style="text-align: center; margin-top: 14px; font-size: 0.82rem; color: #94a3b8; font-weight: 600;">
      👆 Drag the circular button left or right to inspect cleanliness standard
    </div>
  </div>

  <!-- Core Features Highlights (3 Columns) -->
  <div style="text-align: center; margin: 48px 0 24px;">
    <span class="badge badge-info">PILLARS OF SWACHHTA</span>
    <h2 style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin-top: 8px;">
      Transparent, Citizen-Powered Cleanliness
    </h2>
  </div>

  <div class="sbm-feature-grid">
    <!-- Feature 1 -->
    <div class="sbm-feature-card">
      <div class="sbm-feature-icon" style="background: #e0f2fe; color: #0284c7;">
        📊
      </div>
      <h3>Real-Time Cleanliness Score (0-100)</h3>
      <p>
        Every municipal ward receives a dynamic cleanliness score out of 100 calculated using inspection ratings, functioning smart dustbins, and penalty deductions for open complaints.
      </p>
      <a href="<?php echo url('/areas.php'); ?>" style="font-weight: 700; color: #0284c7;">View Ward Scores &rarr;</a>
    </div>

    <!-- Feature 2 -->
    <div class="sbm-feature-card">
      <div class="sbm-feature-icon" style="background: #e8f5e9; color: #16a34a;">
        📸
      </div>
      <h3>Photo-Verified Worker Accountability</h3>
      <p>
        No complaint is closed without photographic proof. Sanitation workers submit timestamped Before and After photos, which municipal supervisors strictly audit prior to task approval.
      </p>
      <a href="<?php echo url('/report.php'); ?>" style="font-weight: 700; color: #16a34a;">Report New Issue &rarr;</a>
    </div>

    <!-- Feature 3 -->
    <div class="sbm-feature-card">
      <div class="sbm-feature-icon" style="background: #fef3c7; color: #b45309;">
        🏆
      </div>
      <h3>Cleanliness Points & Leaderboard</h3>
      <p>
        Citizens are rewarded for being vigilant guardians of public health. Earn up to +75 Swachh Points per verified complaint and climb from Swachhta Mitra to National Swachh Icon!
      </p>
      <a href="<?php echo url('/leaderboard.php'); ?>" style="font-weight: 700; color: #b45309;">Check Leaderboard &rarr;</a>
    </div>
  </div>

  <!-- Live Ward Cleanliness Status Preview -->
  <div class="sbm-card">
    <div class="sbm-card-header">
      <div>
        <h3 class="sbm-card-title">📍 Ward Cleanliness Status Monitor</h3>
        <p class="sbm-card-subtitle">Real-time inspection scores across municipal zones</p>
      </div>
      <a href="<?php echo url('/areas.php'); ?>" class="btn btn-outline btn-sm">
        View All Wards &rarr;
      </a>
    </div>

    <div class="sbm-table-wrapper">
      <table class="sbm-table sbm-filterable-table">
        <thead>
          <tr>
            <th>Ward & Area Name</th>
            <th>Zone</th>
            <th>Cleanliness Status</th>
            <th>Score (out of 100)</th>
            <th>Smart Dustbins</th>
            <th>In-Charge Officer</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (array_slice($areas, 0, 6) as $area): ?>
          <tr>
            <td>
              <strong><?php echo htmlspecialchars($area['name']); ?></strong>
              <div style="font-size: 0.75rem; color: #64748b;"><?php echo htmlspecialchars($area['ward_number']); ?></div>
            </td>
            <td><?php echo htmlspecialchars($area['zone']); ?></td>
            <td>
              <?php if ($area['cleanliness_score'] >= 80): ?>
                <span class="badge badge-clean">🟢 Clean</span>
              <?php elseif ($area['cleanliness_score'] >= 60): ?>
                <span class="badge badge-moderate">🟡 Moderate</span>
              <?php else: ?>
                <span class="badge badge-urgent">🔴 Not Clean</span>
              <?php endif; ?>
            </td>
            <td>
              <span style="font-size: 1.1rem; font-weight: 800; color: <?php echo ($area['cleanliness_score'] >= 80) ? '#16a34a' : (($area['cleanliness_score'] >= 60) ? '#ca8a04' : '#dc2626'); ?>;">
                <?php echo $area['cleanliness_score']; ?> / 100
              </span>
            </td>
            <td>
              <span style="font-weight: 600;"><?php echo $area['bins_functional']; ?> / <?php echo $area['total_bins']; ?></span>
              <span style="font-size: 0.72rem; color: #16a34a;">(<?php echo (int)(($area['bins_functional'] / max($area['total_bins'], 1)) * 100); ?>%)</span>
            </td>
            <td><?php echo htmlspecialchars($area['in_charge_officer'] ?? 'Sanitation Officer'); ?></td>
            <td>
              <a href="<?php echo url('/report.php?ward=' . urlencode($area['ward_number'])); ?>" class="btn btn-sm btn-outline">
                Report Issue
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Citizen Leaderboard & Recent Activity Split Grid -->
  <div class="sbm-grid-sidebar">
    <!-- Left: Leaderboard Preview -->
    <div class="sbm-card">
      <div class="sbm-card-header">
        <h3 class="sbm-card-title">🏅 Top Swachh Warriors</h3>
        <a href="<?php echo url('/leaderboard.php'); ?>" class="btn btn-outline btn-sm">Full Leaderboard &rarr;</a>
      </div>
      <div class="sbm-table-wrapper">
        <table class="sbm-table">
          <thead>
            <tr>
              <th>Rank</th>
              <th>Citizen</th>
              <th>Badge Level</th>
              <th>Points</th>
            </tr>
          </thead>
          <tbody>
            <?php $rank = 1; foreach ($top_citizens as $citizen): ?>
            <tr>
              <td>
                <span style="font-weight: 800; font-size: 1.1rem; color: <?php echo ($rank === 1) ? '#eab308' : (($rank === 2) ? '#94a3b8' : (($rank === 3) ? '#b45309' : '#64748b')); ?>;">
                  #<?php echo $rank; ?>
                </span>
              </td>
              <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                  <img src="<?php echo htmlspecialchars($citizen['avatar'] ? url($citizen['avatar']) : url('/assets/images/avatar_citizen1.svg')); ?>" alt="" style="width: 32px; height: 32px; border-radius: 50%;">
                  <strong><?php echo htmlspecialchars($citizen['name']); ?></strong>
                </div>
              </td>
              <td><span class="badge badge-purple"><?php echo htmlspecialchars($citizen['badge']); ?></span></td>
              <td><strong style="color: #0d5377; font-size: 1.05rem;"><?php echo $citizen['points']; ?> pts</strong></td>
            </tr>
            <?php $rank++; endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Right: Live Activity Feed -->
    <div class="sbm-card">
      <div class="sbm-card-header">
        <h3 class="sbm-card-title">⚡ Live Activity</h3>
        <span class="badge badge-info">Real-time</span>
      </div>
      <div class="activity-feed">
        <?php foreach ($recent_activities as $act): ?>
        <div class="activity-item">
          <div class="activity-time">Just Now</div>
          <div class="activity-text">
            <h5><?php echo htmlspecialchars($act['title']); ?></h5>
            <p><?php echo htmlspecialchars($act['description']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
