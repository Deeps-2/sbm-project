<?php
$page_title = "Cleanliness GIS Map";
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$stmt = $db->query("SELECT * FROM areas ORDER BY cleanliness_score DESC");
$areas = $stmt->fetchAll();
?>

<div class="sbm-container">
  
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
    <div>
      <span class="badge badge-clean">GEOSPATIAL SWACHHTA MONITOR</span>
      <h1 style="font-size: 2rem; font-weight: 800; color: #0d5377; margin-top: 6px;">
        Cleanliness GIS Map
      </h1>
      <p style="color: #64748b; font-size: 0.95rem;">
        Explore municipal ward status, pin-pointed cleanliness scores, and live active sanitation teams.
      </p>
    </div>
    <div style="display: flex; gap: 10px;">
      <a href="<?php echo url('/report.php'); ?>" class="btn btn-primary">
        🚨 Report Issue on Map
      </a>
    </div>
  </div>

  <!-- Map + Sidebar Layout -->
  <div class="sbm-grid-sidebar">
    
    <!-- Map Container -->
    <div class="sbm-card" style="padding: 16px;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="font-size: 0.85rem; font-weight: 700; color: #1e293b;">Legend:</span>
          <span class="badge badge-clean">🟢 Clean (80-100)</span>
          <span class="badge badge-moderate">🟡 Moderate (60-79)</span>
          <span class="badge badge-urgent">🔴 Critical (&lt;60)</span>
        </div>
        <span style="font-size: 0.75rem; color: #94a3b8;">Click any pin for ward audit details</span>
      </div>
      
      <div id="cleanliness-map" style="height: 560px; border-radius: 12px;"></div>
    </div>

    <!-- Ward Quick List Sidebar -->
    <div class="sbm-card" style="max-height: 640px; display: flex; flex-direction: column;">
      <div class="sbm-card-header">
        <h3 class="sbm-card-title">Wards (<?php echo count($areas); ?>)</h3>
        <span class="badge badge-info">Delhi NCT</span>
      </div>

      <div style="overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 10px; padding-right: 4px;">
        <?php foreach ($areas as $area): ?>
        <div class="ward-list-item" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.2s ease;">
          <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 6px;">
            <div>
              <div style="font-size: 0.75rem; font-weight: 700; color: #64748b;"><?php echo htmlspecialchars($area['ward_number']); ?></div>
              <strong style="font-size: 0.95rem; color: #1e293b;"><?php echo htmlspecialchars($area['name']); ?></strong>
            </div>
            <span style="font-weight: 900; font-size: 1.1rem; color: <?php echo ($area['cleanliness_score'] >= 80) ? '#16a34a' : (($area['cleanliness_score'] >= 60) ? '#ca8a04' : '#dc2626'); ?>;">
              <?php echo $area['cleanliness_score']; ?>
            </span>
          </div>
          <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.78rem;">
            <span style="color: #64748b;"><?php echo htmlspecialchars($area['zone']); ?></span>
            <a href="<?php echo url('/report.php?ward=' . urlencode($area['ward_number'])); ?>" style="color: #ea580c; font-weight: 700;">
              Report &rarr;
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

</div>

<?php 
$json_areas = json_encode($areas);
$extra_scripts = "
<script>
  const areasData = {$json_areas};
  document.addEventListener('DOMContentLoaded', () => {
    initCleanlinessMap(areasData);
  });
</script>
";
require_once __DIR__ . '/includes/footer.php'; 
?>
