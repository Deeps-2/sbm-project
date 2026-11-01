<?php
$page_title = "Area Cleanliness Status & Scores";
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$stmt = $db->query("SELECT * FROM areas ORDER BY cleanliness_score DESC");
$areas = $stmt->fetchAll();
?>

<div class="sbm-container">
  
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 28px;">
    <div>
      <span class="badge badge-info">TRANSPARENT SBM AUDIT</span>
      <h1 style="font-size: 2rem; font-weight: 800; color: #0d5377; margin-top: 6px;">
        Area Cleanliness Status & Scores
      </h1>
      <p style="color: #64748b; font-size: 0.95rem;">
        Monitor real-time cleanliness ratings (0-100) across all wards, zones, and commercial districts.
      </p>
    </div>
    <div style="display: flex; gap: 10px;">
      <a href="<?php echo url('/map.php'); ?>" class="btn btn-outline">
        🗺️ View on Map
      </a>
      <a href="<?php echo url('/report.php'); ?>" class="btn btn-primary">
        🚨 Report Issue
      </a>
    </div>
  </div>

  <!-- Cleanliness Score Calculation Explanation Card -->
  <div class="sbm-card" style="background: linear-gradient(135deg, #f0f7fa 0%, #ffffff 100%); border-left: 6px solid #0d5377;">
    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0d5377; margin-bottom: 8px;">
      📐 How is the Cleanliness Score (0–100) Calculated?
    </h3>
    <p style="color: #475569; font-size: 0.88rem; line-height: 1.6; margin-bottom: 14px;">
      Under the Swachh Bharat Mission guidelines, each municipal ward score is dynamically generated using algorithmic weighting to ensure complete objectivity:
    </p>
    <div class="sbm-grid-4" style="margin-bottom: 0;">
      <div style="background: #ffffff; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0;">
        <div style="font-weight: 800; color: #0d5377; font-size: 1.2rem;">40%</div>
        <div style="font-weight: 700; font-size: 0.82rem; color: #1e293b;">Field Inspection</div>
        <div style="font-size: 0.74rem; color: #64748b;">Periodic audits by municipal health inspectors.</div>
      </div>
      <div style="background: #ffffff; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0;">
        <div style="font-weight: 800; color: #0284c7; font-size: 1.2rem;">20%</div>
        <div style="font-weight: 700; font-size: 0.82rem; color: #1e293b;">Smart Dustbins</div>
        <div style="font-size: 0.74rem; color: #64748b;">Ratio of functional and segregated wet/dry waste bins.</div>
      </div>
      <div style="background: #ffffff; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0;">
        <div style="font-weight: 800; color: #dc2626; font-size: 1.2rem;">-5 to -12 Pts</div>
        <div style="font-weight: 700; font-size: 0.82rem; color: #1e293b;">Unresolved Penalty</div>
        <div style="font-size: 0.74rem; color: #64748b;">Deductions for each pending citizen complaint.</div>
      </div>
      <div style="background: #ffffff; padding: 14px; border-radius: 10px; border: 1px solid #e2e8f0;">
        <div style="font-weight: 800; color: #16a34a; font-size: 1.2rem;">+5 Pts Each</div>
        <div style="font-weight: 700; font-size: 0.82rem; color: #1e293b;">Verified Cleanings</div>
        <div style="font-size: 0.74rem; color: #64748b;">Bonus for approved Before/After worker completions.</div>
      </div>
    </div>
  </div>

  <!-- Filter & Search Controls -->
  <div class="sbm-card" style="padding: 16px 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
      <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <span style="font-size: 0.85rem; font-weight: 700; color: #64748b;">Filter Status:</span>
        <button class="btn btn-sm btn-outline status-filter-btn active" data-filter="all">All Wards (<?php echo count($areas); ?>)</button>
        <button class="btn btn-sm btn-outline status-filter-btn" data-filter="Clean" style="color: #16a34a; border-color: #16a34a;">🟢 Clean (80-100)</button>
        <button class="btn btn-sm btn-outline status-filter-btn" data-filter="Moderate" style="color: #ca8a04; border-color: #ca8a04;">🟡 Moderate (60-79)</button>
        <button class="btn btn-sm btn-outline status-filter-btn" data-filter="Not Clean" style="color: #dc2626; border-color: #dc2626;">🔴 Not Clean (&lt;60)</button>
      </div>
      <div style="flex: 0 1 300px;">
        <input type="text" id="area-search-input" class="form-control" placeholder="Search ward name, zone, or officer..." style="padding: 8px 14px;">
      </div>
    </div>
  </div>

  <!-- Area Cards Grid -->
  <div class="sbm-grid-3" id="area-cards-container">
    <?php foreach ($areas as $area): ?>
    <div class="sbm-card sbm-filterable-card area-item-card" data-status="<?php echo $area['status']; ?>" style="display: flex; flex-direction: column;">
      <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
        <div>
          <span style="font-size: 0.75rem; font-weight: 800; color: #0284c7; text-transform: uppercase;"><?php echo htmlspecialchars($area['ward_number']); ?> • <?php echo htmlspecialchars($area['zone']); ?></span>
          <h3 style="font-size: 1.18rem; font-weight: 800; color: #1e293b; margin-top: 2px;"><?php echo htmlspecialchars($area['name']); ?></h3>
        </div>
        <div class="sbm-score-badge <?php echo ($area['cleanliness_score'] >= 80) ? 'score-green' : (($area['cleanliness_score'] >= 60) ? 'score-yellow' : 'score-red'); ?>">
          <?php echo $area['cleanliness_score']; ?>
        </div>
      </div>

      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
        <?php if ($area['cleanliness_score'] >= 80): ?>
          <span class="badge badge-clean">🟢 Clean / Swachh Adarsh</span>
        <?php elseif ($area['cleanliness_score'] >= 60): ?>
          <span class="badge badge-moderate">🟡 Moderate Cleanliness</span>
        <?php else: ?>
          <span class="badge badge-urgent">🔴 Critical Attention Required</span>
        <?php endif; ?>
        <span style="font-size: 0.75rem; color: #94a3b8;">Inspected: <?php echo $area['last_inspection_date']; ?></span>
      </div>

      <div style="background: #f8fafc; border-radius: 10px; padding: 12px; margin-bottom: 16px; font-size: 0.82rem; display: flex; flex-direction: column; gap: 6px;">
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #64748b;">Functional Bins:</span>
          <strong><?php echo $area['bins_functional']; ?> of <?php echo $area['total_bins']; ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #64748b;">Sanitation In-Charge:</span>
          <strong><?php echo htmlspecialchars($area['in_charge_officer'] ?? 'Sanitation Officer'); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
          <span style="color: #64748b;">Zone Density:</span>
          <strong><?php echo htmlspecialchars($area['population_density'] ?? 'Medium'); ?></strong>
        </div>
      </div>

      <div style="margin-top: auto; display: flex; gap: 10px;">
        <a href="<?php echo url('/report.php?ward=' . urlencode($area['ward_number'])); ?>" class="btn btn-primary btn-sm" style="flex: 1;">
          🚨 Report Issue
        </a>
        <a href="<?php echo url('/map.php'); ?>" class="btn btn-outline btn-sm">
          Map Pin
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

</div>

<?php 
$extra_scripts = '
<script>
  const filterBtns = document.querySelectorAll(".status-filter-btn");
  const cards = document.querySelectorAll(".area-item-card");
  const searchInput = document.getElementById("area-search-input");

  function filterWards() {
    const activeFilter = document.querySelector(".status-filter-btn.active").dataset.filter;
    const term = searchInput.value.toLowerCase().trim();

    cards.forEach(card => {
      const status = card.dataset.status;
      const text = card.innerText.toLowerCase();

      const matchesStatus = (activeFilter === "all" || status === activeFilter);
      const matchesSearch = text.includes(term);

      card.style.display = (matchesStatus && matchesSearch) ? "flex" : "none";
    });
  }

  filterBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      filterBtns.forEach(b => b.classList.remove("active"));
      btn.classList.add("active");
      filterWards();
    });
  });

  if (searchInput) {
    searchInput.addEventListener("input", filterWards);
  }
</script>
';
require_once __DIR__ . '/includes/footer.php'; 
?>
