<?php
$page_title = "Track Sanitation Complaint";
require_once __DIR__ . '/includes/header.php';

$code = trim($_GET['code'] ?? '');
$complaint = null;

if (!empty($code)) {
    $db = get_db();
    $stmt = $db->prepare("
        SELECT c.*, a.name as area_name, a.ward_number,
               u.name as citizen_name, u.phone as citizen_phone,
               w.name as worker_name, w.phone as worker_phone
        FROM complaints c
        JOIN areas a ON c.area_id = a.id
        JOIN users u ON c.user_id = u.id
        LEFT JOIN users w ON c.assigned_worker_id = w.id
        WHERE c.tracking_code = ?
    ");
    $stmt->execute([strtoupper($code)]);
    $complaint = $stmt->fetch();
}
?>

<div class="sbm-container" style="max-width: 980px;">
  
  <div style="margin-bottom: 24px;">
    <span class="badge badge-info">TRANSPARENT GRIEVANCE TRACKER</span>
    <h1 style="font-size: 2rem; font-weight: 800; color: #0d5377; margin-top: 6px;">
      Track Complaint Status & Progress
    </h1>
    <p style="color: #64748b; font-size: 0.95rem;">
      Enter your grievance tracking number to monitor dispatch, worker cleaning stages, and photographic verification.
    </p>
  </div>

  <!-- Search Card with Quick Demo Sample Pills -->
  <div class="sbm-card" style="padding: 24px;">
    <form action="<?php echo url('/track.php'); ?>" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
      <input type="text" name="code" value="<?php echo htmlspecialchars($code); ?>" placeholder="Enter Tracking Code e.g. SBM-2026-1042" class="form-control" style="flex: 1; min-width: 260px; font-weight: 700; text-transform: uppercase;">
      <button type="submit" class="btn btn-primary" style="padding: 10px 28px;">
        🔍 Track Status
      </button>
    </form>

    <!-- Quick Pill Badges for demo click -->
    <div style="margin-top: 14px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; font-size: 0.82rem;">
      <span style="color: #64748b; font-weight: 600;">Try sample complaints:</span>
      <a href="<?php echo url('/track.php?code=SBM-2026-1042'); ?>" class="btn btn-sm btn-outline">
        SBM-2026-1042 (Verification Required)
      </a>
      <a href="<?php echo url('/track.php?code=SBM-2026-1044'); ?>" class="btn btn-sm btn-outline">
        SBM-2026-1044 (Resolved & Verified)
      </a>
      <a href="<?php echo url('/track.php?code=SBM-2026-1041'); ?>" class="btn btn-sm btn-outline">
        SBM-2026-1041 (Pending Dispatch)
      </a>
    </div>
  </div>

  <?php if ($complaint): ?>
  <!-- Complaint Status Card -->
  <div class="sbm-card" style="padding: 32px;">
    
    <!-- Header -->
    <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid #e2e8f0;">
      <div>
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
          <span style="font-family: var(--font-mono); font-size: 1.25rem; font-weight: 800; color: #0d5377;">
            <?php echo htmlspecialchars($complaint['tracking_code']); ?>
          </span>
          <span class="badge <?php echo ($complaint['priority'] === 'Urgent') ? 'badge-urgent' : (($complaint['priority'] === 'High') ? 'badge-moderate' : 'badge-info'); ?>">
            <?php echo htmlspecialchars($complaint['priority']); ?> Priority
          </span>
          <span class="badge badge-purple"><?php echo htmlspecialchars($complaint['category']); ?></span>
        </div>
        <h2 style="font-size: 1.45rem; font-weight: 800; color: #1e293b;"><?php echo htmlspecialchars($complaint['title']); ?></h2>
        <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px;">
          📍 <?php echo htmlspecialchars($complaint['location_address']); ?> (<?php echo htmlspecialchars($complaint['ward_number'] . ' - ' . $complaint['area_name']); ?>)
        </div>
      </div>

      <div style="text-align: right;">
        <div style="font-size: 0.78rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Current Status</div>
        <div style="font-size: 1.25rem; font-weight: 900; margin-top: 2px; color: <?php echo ($complaint['status'] === 'Resolved') ? '#16a34a' : (($complaint['status'] === 'Verification Required') ? '#0284c7' : (($complaint['status'] === 'In Progress') ? '#f59e0b' : '#64748b')); ?>;">
          <?php echo htmlspecialchars($complaint['status']); ?>
        </div>
        <?php if ($complaint['points_awarded'] > 0): ?>
          <span class="badge badge-clean" style="margin-top: 6px;">+<?php echo $complaint['points_awarded']; ?> Citizen Points Awarded</span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Visual 5-Step Timeline -->
    <?php $s = $complaint['status']; ?>
    <div class="sbm-timeline">
      <!-- Step 1: Submitted -->
      <div class="sbm-timeline-step completed">
        <div class="sbm-timeline-dot">✓</div>
        <div class="sbm-timeline-label">1. Reported</div>
      </div>
      <!-- Step 2: Assigned -->
      <div class="sbm-timeline-step <?php echo in_array($s, ['Assigned', 'In Progress', 'Verification Required', 'Resolved']) ? 'completed' : 'pending'; ?>">
        <div class="sbm-timeline-dot"><?php echo in_array($s, ['Assigned', 'In Progress', 'Verification Required', 'Resolved']) ? '✓' : '2'; ?></div>
        <div class="sbm-timeline-label">2. Assigned</div>
      </div>
      <!-- Step 3: In Progress -->
      <div class="sbm-timeline-step <?php echo in_array($s, ['In Progress', 'Verification Required', 'Resolved']) ? 'completed' : (($s === 'Assigned') ? 'active' : 'pending'); ?>">
        <div class="sbm-timeline-dot"><?php echo in_array($s, ['In Progress', 'Verification Required', 'Resolved']) ? '✓' : '3'; ?></div>
        <div class="sbm-timeline-label">3. Cleaning</div>
      </div>
      <!-- Step 4: Verification Required -->
      <div class="sbm-timeline-step <?php echo in_array($s, ['Verification Required', 'Resolved']) ? 'completed' : (($s === 'In Progress') ? 'active' : 'pending'); ?>">
        <div class="sbm-timeline-dot"><?php echo in_array($s, ['Verification Required', 'Resolved']) ? '✓' : '4'; ?></div>
        <div class="sbm-timeline-label">4. Photo Audit</div>
      </div>
      <!-- Step 5: Resolved -->
      <div class="sbm-timeline-step <?php echo ($s === 'Resolved') ? 'completed' : 'pending'; ?>">
        <div class="sbm-timeline-dot"><?php echo ($s === 'Resolved') ? '✓' : '5'; ?></div>
        <div class="sbm-timeline-label">5. Verified Clean</div>
      </div>
    </div>

    <!-- Personnel & Timestamps Grid -->
    <div class="sbm-grid-3" style="margin: 28px 0;">
      <div style="background: #f8fafc; border-radius: 10px; padding: 14px; border: 1px solid #e2e8f0;">
        <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Reporting Citizen</div>
        <div style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin-top: 4px;"><?php echo htmlspecialchars($complaint['citizen_name']); ?></div>
        <div style="font-size: 0.75rem; color: #94a3b8;">Logged: <?php echo htmlspecialchars($complaint['created_at']); ?></div>
      </div>
      <div style="background: #f8fafc; border-radius: 10px; padding: 14px; border: 1px solid #e2e8f0;">
        <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Assigned Sanitation Staff</div>
        <div style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin-top: 4px;">
          <?php echo htmlspecialchars($complaint['worker_name'] ?? 'Pending Staff Assignment'); ?>
        </div>
        <div style="font-size: 0.75rem; color: #94a3b8;"><?php echo htmlspecialchars($complaint['worker_phone'] ?? 'Municipal Deployment Desk'); ?></div>
      </div>
      <div style="background: #f8fafc; border-radius: 10px; padding: 14px; border: 1px solid #e2e8f0;">
        <div style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Inspection & Resolution</div>
        <div style="font-size: 0.95rem; font-weight: 800; color: #1e293b; margin-top: 4px;">
          <?php echo ($s === 'Resolved') ? 'Cleanliness Audit Passed' : (($s === 'Verification Required') ? 'Field Work Submitted' : 'In Process'); ?>
        </div>
        <div style="font-size: 0.75rem; color: #94a3b8;"><?php echo htmlspecialchars($complaint['worker_completed_at'] ?? 'Awaiting Completion'); ?></div>
      </div>
    </div>

    <!-- Before and After Photo Verification Display -->
    <?php if (!empty($complaint['worker_before_photo']) && !empty($complaint['worker_after_photo'])): ?>
    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
      <div style="text-align: center; margin-bottom: 16px;">
        <span class="badge badge-clean">VERIFIED AUDIT EVIDENCE</span>
        <h3 style="font-size: 1.35rem; font-weight: 800; color: #0d5377; margin-top: 4px;">
          Worker Before & After Cleaning Comparison
        </h3>
        <p style="font-size: 0.85rem; color: #64748b;">
          Submitted by sanitation worker <?php echo htmlspecialchars($complaint['worker_name'] ?? 'Worker'); ?>. Drag slider to inspect paver cleanliness:
        </p>
      </div>

      <!-- Comparator Widget -->
      <div class="sbm-comparator-container" id="track-comparator">
        <img src="<?php echo url($complaint['worker_before_photo']); ?>" alt="Before" class="sbm-comparator-img">
        <span class="sbm-comparator-label sbm-label-before">Before Cleaning</span>

        <div class="sbm-comparator-after" style="width: 50%;">
          <img src="<?php echo url($complaint['worker_after_photo']); ?>" alt="After" class="sbm-comparator-img" style="width: 680px; max-width: 680px;">
          <span class="sbm-comparator-label sbm-label-after">After Cleaning</span>
        </div>

        <div class="sbm-comparator-handle" style="left: 50%;">⬄</div>
      </div>

      <?php if (!empty($complaint['worker_remarks'])): ?>
      <div style="background: #f0f7fa; border-radius: 8px; padding: 12px 18px; margin-top: 16px; border-left: 4px solid #0d5377; font-size: 0.88rem;">
        <strong>Worker Remarks:</strong> <?php echo htmlspecialchars($complaint['worker_remarks']); ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($complaint['admin_notes'])): ?>
      <div style="background: #e8f5e9; border-radius: 8px; padding: 12px 18px; margin-top: 10px; border-left: 4px solid #16a34a; font-size: 0.88rem;">
        <strong>Supervisor Verification Note:</strong> <?php echo htmlspecialchars($complaint['admin_notes']); ?>
      </div>
      <?php endif; ?>
    </div>
    <?php elseif (!empty($complaint['citizen_photo'])): ?>
    <div style="margin-top: 24px;">
      <h4 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Original Citizen Report Photo:</h4>
      <img src="<?php echo url($complaint['citizen_photo']); ?>" alt="Citizen Report" style="max-height: 240px; border-radius: 10px; border: 1px solid #e2e8f0;">
    </div>
    <?php endif; ?>

  </div>
  <?php elseif (!empty($code)): ?>
  <div class="sbm-card" style="text-align: center; padding: 48px 20px;">
    <div style="font-size: 3rem; margin-bottom: 12px;">🔍</div>
    <h3 style="font-size: 1.3rem; font-weight: 800; color: #1e293b;">No Complaint Found</h3>
    <p style="color: #64748b; font-size: 0.95rem; margin-top: 4px;">
      No complaint matching code "<strong><?php echo htmlspecialchars($code); ?></strong>" exists in our records. Please verify your reference number.
    </p>
  </div>
  <?php endif; ?>

</div>

<?php 
$extra_scripts = '
<script>
  document.addEventListener("DOMContentLoaded", () => {
    initImageComparator("track-comparator");
  });
</script>
';
require_once __DIR__ . '/includes/footer.php'; 
?>
