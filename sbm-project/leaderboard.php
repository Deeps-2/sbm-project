<?php
$page_title = "Cleanliness Points & Citizen Leaderboard";
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$stmt = $db->query("
    SELECT u.*, a.name as ward_name, COUNT(c.id) as reports_count
    FROM users u
    LEFT JOIN areas a ON u.ward_id = a.id
    LEFT JOIN complaints c ON u.id = c.user_id AND c.status = 'Resolved'
    WHERE u.role = 'citizen'
    GROUP BY u.id
    ORDER BY u.points DESC
");
$citizens = $stmt->fetchAll();
?>

<div class="sbm-container">
  
  <div style="text-align: center; margin-bottom: 32px;">
    <span class="badge badge-clean">COMMUNITY SWACHHTA HONOURS</span>
    <h1 style="font-size: 2.4rem; font-weight: 900; color: #0d5377; margin-top: 8px;">
      Citizen Points & Leaderboard
    </h1>
    <p style="color: #64748b; font-size: 1.05rem; max-width: 620px; margin: 8px auto 0;">
      Recognizing proactive citizens who report genuine public sanitation grievances and keep our cities sparkling clean.
    </p>
  </div>

  <!-- Top 3 Podium -->
  <?php if (count($citizens) >= 3): ?>
  <div class="sbm-podium">
    <!-- Rank 2: Silver -->
    <div class="sbm-podium-card second">
      <div class="sbm-podium-rank">2</div>
      <img src="<?php echo url($citizens[1]['avatar']); ?>" alt="" class="sbm-podium-avatar">
      <div class="sbm-podium-name"><?php echo htmlspecialchars($citizens[1]['name']); ?></div>
      <span class="badge badge-purple" style="margin-top: 4px;"><?php echo htmlspecialchars($citizens[1]['badge']); ?></span>
      <div class="sbm-podium-points"><?php echo $citizens[1]['points']; ?> Pts</div>
      <div style="font-size: 0.72rem; color: #64748b; margin-top: 4px;"><?php echo $citizens[1]['reports_count']; ?> Verified Reports</div>
    </div>

    <!-- Rank 1: Gold Champion -->
    <div class="sbm-podium-card first">
      <div class="sbm-podium-rank">👑 1</div>
      <img src="<?php echo url($citizens[0]['avatar']); ?>" alt="" class="sbm-podium-avatar" style="width: 74px; height: 74px; border-color: #eab308;">
      <div class="sbm-podium-name" style="font-size: 1.15rem;"><?php echo htmlspecialchars($citizens[0]['name']); ?></div>
      <span class="badge badge-clean" style="margin-top: 4px;"><?php echo htmlspecialchars($citizens[0]['badge']); ?></span>
      <div class="sbm-podium-points" style="color: #ca8a04; font-size: 1.6rem;"><?php echo $citizens[0]['points']; ?> Pts</div>
      <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;"><?php echo $citizens[0]['reports_count']; ?> Verified Reports</div>
    </div>

    <!-- Rank 3: Bronze -->
    <div class="sbm-podium-card third">
      <div class="sbm-podium-rank">3</div>
      <img src="<?php echo url($citizens[2]['avatar']); ?>" alt="" class="sbm-podium-avatar">
      <div class="sbm-podium-name"><?php echo htmlspecialchars($citizens[2]['name']); ?></div>
      <span class="badge badge-purple" style="margin-top: 4px;"><?php echo htmlspecialchars($citizens[2]['badge']); ?></span>
      <div class="sbm-podium-points"><?php echo $citizens[2]['points']; ?> Pts</div>
      <div style="font-size: 0.72rem; color: #64748b; margin-top: 4px;"><?php echo $citizens[2]['reports_count']; ?> Verified Reports</div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Full Leaderboard Table -->
  <div class="sbm-card">
    <div class="sbm-card-header">
      <div>
        <h3 class="sbm-card-title">🏆 Top Cleanliness Guardians Ranking</h3>
        <p class="sbm-card-subtitle">Updated in real-time as grievances are approved and verified</p>
      </div>
      <a href="<?php echo url('/report.php'); ?>" class="btn btn-primary btn-sm">
        + Earn Points Now
      </a>
    </div>

    <div class="sbm-table-wrapper">
      <table class="sbm-table sbm-filterable-table">
        <thead>
          <tr>
            <th>Rank</th>
            <th>Citizen Guardian</th>
            <th>Registered Ward</th>
            <th>Badge Tier</th>
            <th>Verified Issues</th>
            <th>Total Points</th>
            <th>Impact Level</th>
          </tr>
        </thead>
        <tbody>
          <?php $rank = 1; foreach ($citizens as $citizen): ?>
          <tr>
            <td>
              <span style="font-weight: 800; font-size: 1.1rem; color: <?php echo ($rank === 1) ? '#eab308' : (($rank === 2) ? '#94a3b8' : (($rank === 3) ? '#b45309' : '#64748b')); ?>;">
                #<?php echo $rank; ?>
              </span>
            </td>
            <td>
              <div style="display: flex; align-items: center; gap: 12px;">
                <img src="<?php echo url($citizen['avatar']); ?>" alt="" style="width: 38px; height: 38px; border-radius: 50%;">
                <div>
                  <strong><?php echo htmlspecialchars($citizen['name']); ?></strong>
                  <div style="font-size: 0.74rem; color: #94a3b8;">Joined <?php echo substr($citizen['created_at'], 0, 10); ?></div>
                </div>
              </div>
            </td>
            <td><?php echo htmlspecialchars($citizen['ward_name'] ?? 'Central Municipal Zone'); ?></td>
            <td>
              <span class="badge <?php echo ($citizen['points'] >= 600) ? 'badge-clean' : (($citizen['points'] >= 300) ? 'badge-info' : (($citizen['points'] >= 100) ? 'badge-purple' : 'badge-moderate')); ?>">
                <?php echo htmlspecialchars($citizen['badge']); ?>
              </span>
            </td>
            <td>
              <strong><?php echo $citizen['reports_count']; ?></strong> issues resolved
            </td>
            <td>
              <span style="font-size: 1.15rem; font-weight: 900; color: #0d5377;"><?php echo $citizen['points']; ?></span>
              <span style="font-size: 0.75rem; color: #64748b;">pts</span>
            </td>
            <td>
              <div style="background: #e2e8f0; height: 8px; width: 100px; border-radius: 4px; overflow: hidden;">
                <div style="background: #16a34a; height: 100%; width: <?php echo min(100, (int)(($citizen['points'] / 700) * 100)); ?>%;"></div>
              </div>
            </td>
          </tr>
          <?php $rank++; endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- How to Earn Points Section -->
  <div class="sbm-card" style="background: #f8fafc; border: 1.5px dashed #cbd5e1;">
    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0d5377; margin-bottom: 14px;">
      💡 How Citizens Earn Swachhta Points
    </h3>
    <div class="sbm-grid-3">
      <div style="background: #ffffff; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <div style="font-size: 1.5rem; margin-bottom: 6px;">📸</div>
        <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e293b;">+50 Points</h4>
        <p style="font-size: 0.82rem; color: #64748b; margin-top: 4px;">
          Awarded automatically once a reported sanitation complaint with photo is reviewed and approved by municipal admins.
        </p>
      </div>
      <div style="background: #ffffff; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <div style="font-size: 1.5rem; margin-bottom: 6px;">✅</div>
        <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e293b;">+25 Bonus Points</h4>
        <p style="font-size: 0.82rem; color: #64748b; margin-top: 4px;">
          Awarded when sanitation workers complete the task and the Before & After verification passes municipal audit.
        </p>
      </div>
      <div style="background: #ffffff; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0;">
        <div style="font-size: 1.5rem; margin-bottom: 6px;">🎖️</div>
        <h4 style="font-size: 0.95rem; font-weight: 800; color: #1e293b;">Government Badges</h4>
        <p style="font-size: 0.82rem; color: #64748b; margin-top: 4px;">
          Progress from Swachhta Mitra (0-99 pts) &rarr; Cleanliness Champion (100-299) &rarr; Green Warrior (300-599) &rarr; National Swachh Icon (600+).
        </p>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
