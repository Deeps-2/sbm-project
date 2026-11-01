<?php
$page_title = "Report a Cleanliness Issue";
require_once __DIR__ . '/includes/header.php';

$selected_ward = $_GET['ward'] ?? '';
$db = get_db();
$stmt = $db->query("SELECT * FROM areas ORDER BY ward_number ASC");
$areas = $stmt->fetchAll();
?>

<div class="sbm-container" style="max-width: 960px;">
  
  <div style="margin-bottom: 24px;">
    <span class="badge badge-urgent">CITIZEN FEEDBACK PORTAL</span>
    <h1 style="font-size: 2rem; font-weight: 800; color: #0d5377; margin-top: 6px;">
      Report a Sanitation & Cleanliness Issue
    </h1>
    <p style="color: #64748b; font-size: 0.95rem;">
      Help keep our neighborhoods clean. Upload photos and provide location details so our sanitation workforce can take swift action.
    </p>
  </div>

  <!-- Points Incentive Banner -->
  <div class="sbm-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%); border-color: #fde68a; padding: 18px 24px; margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 16px;">
      <div style="font-size: 2rem;">🏆</div>
      <div>
        <h4 style="font-size: 1.05rem; font-weight: 800; color: #92400e;">Earn Swachhta Points & Badges!</h4>
        <p style="font-size: 0.85rem; color: #b45309; margin-top: 2px;">
          Genuine reports earn <strong>+50 Points</strong> upon initial admin approval and a bonus <strong>+25 Points</strong> once cleaning is completed and verified.
        </p>
      </div>
    </div>
  </div>

  <!-- Report Form Card -->
  <div class="sbm-card" style="padding: 32px;">
    <form action="<?php echo url('/report_process.php'); ?>" method="POST" enctype="multipart/form-data">
      
      <!-- Issue Title -->
      <div class="form-group">
        <label class="form-label" for="title">Issue Title / Short Summary *</label>
        <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Overflowing garbage dump blocking pedestrian walkway" required>
      </div>

      <!-- Category Selection -->
      <div class="form-group">
        <label class="form-label">Category of Issue *</label>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; margin-top: 8px;">
          <label style="border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #f8fafc;">
            <input type="radio" name="category" value="Garbage Dump" checked>
            <span>🗑️ Garbage Dump</span>
          </label>
          <label style="border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #f8fafc;">
            <input type="radio" name="category" value="Overflowing Dustbin">
            <span>♻️ Overflowing Dustbin</span>
          </label>
          <label style="border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #f8fafc;">
            <input type="radio" name="category" value="Plastic Waste">
            <span>🥤 Plastic Litter</span>
          </label>
          <label style="border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #f8fafc;">
            <input type="radio" name="category" value="Sewage Leak">
            <span>🚰 Sewage / Drain Leak</span>
          </label>
          <label style="border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #f8fafc;">
            <input type="radio" name="category" value="Dead Animal">
            <span>⚠️ Dead Animal</span>
          </label>
          <label style="border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px; cursor: pointer; background: #f8fafc;">
            <input type="radio" name="category" value="Open Defecation">
            <span>🚫 Open Defecation</span>
          </label>
        </div>
      </div>

      <!-- Ward and Priority Row -->
      <div class="sbm-grid-2">
        <div class="form-group">
          <label class="form-label" for="area_id">Select Municipal Ward / Area *</label>
          <select id="area_id" name="area_id" class="form-control" required>
            <option value="">-- Choose Ward --</option>
            <?php foreach ($areas as $area): ?>
              <option value="<?php echo $area['id']; ?>" <?php echo ($selected_ward && strpos($area['ward_number'], $selected_ward) !== false) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($area['ward_number'] . ' - ' . $area['name'] . ' (' . $area['zone'] . ')'); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="priority">Urgency / Priority</label>
          <select id="priority" name="priority" class="form-control">
            <option value="Medium">Medium Priority (Standard SLA)</option>
            <option value="High">High Priority (Urgent Cleanup)</option>
            <option value="Urgent">Critical / Health Hazard (Immediate Response)</option>
            <option value="Low">Low Priority (Minor Litter)</option>
          </select>
        </div>
      </div>

      <!-- Location Details & Interactive Pinpoint Map -->
      <div class="form-group">
        <label class="form-label" for="input-address">Exact Landmark / Street Address *</label>
        <input type="text" id="input-address" name="location_address" class="form-control" placeholder="e.g. Near Metro Pillar 142, Main Ring Road, opposite Post Office" required>
      </div>

      <!-- Interactive Map Picker -->
      <div class="form-group">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
          <label class="form-label" style="margin-bottom: 0;">Pinpoint Exact GPS Location on Map (Optional)</label>
          <span style="font-size: 0.75rem; color: #64748b;">Click or drag pin to update</span>
        </div>
        <div id="report-picker-map" style="height: 240px; border-radius: 10px;"></div>
        
        <input type="hidden" id="input-lat" name="latitude" value="28.6315">
        <input type="hidden" id="input-lng" name="longitude" value="77.2167">
      </div>

      <!-- Photo Upload with Preview -->
      <div class="form-group">
        <label class="form-label">Upload Photograph Proof *</label>
        <div style="border: 2px dashed #cbd5e1; border-radius: 12px; padding: 24px; text-align: center; background: #f8fafc; cursor: pointer;" onclick="document.getElementById('photo-input').click();">
          <input type="file" id="photo-input" name="photo" accept="image/*" style="display: none;" onchange="previewUpload(this);">
          
          <div id="upload-placeholder">
            <div style="font-size: 2.2rem; color: #94a3b8; margin-bottom: 8px;">📷</div>
            <div style="font-weight: 700; color: #1e293b;">Click to upload or snap photo</div>
            <div style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">PNG, JPG, or SVG up to 10MB</div>
            
            <div style="margin-top: 12px;">
              <span class="btn btn-sm btn-outline" style="pointer-events: none;">Browse Files</span>
            </div>
          </div>

          <div id="upload-preview-container" style="display: none;">
            <img id="upload-preview-img" src="" alt="Upload Preview" style="max-height: 200px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 10px;">
            <div style="font-size: 0.8rem; font-weight: 700; color: #16a34a;">✓ Photo selected successfully! Click to change.</div>
          </div>
        </div>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label class="form-label" for="description">Detailed Description</label>
        <textarea id="description" name="description" class="form-control" rows="3" placeholder="Provide extra details such as duration of issue, foul smell, stray animals, or specific access instructions for workers..." required></textarea>
      </div>

      <!-- Action Button -->
      <div style="display: flex; align-items: center; justify-content: flex-end; gap: 14px; margin-top: 24px;">
        <a href="<?php echo url('/index.php'); ?>" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-size: 1rem;">
          🚀 Submit Complaint & Claim Points
        </button>
      </div>

    </form>
  </div>

</div>

<?php 
$extra_scripts = '
<script>
  function previewUpload(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById("upload-preview-img").src = e.target.result;
        document.getElementById("upload-preview-container").style.display = "block";
        document.getElementById("upload-placeholder").style.display = "none";
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    initLocationPickerMap(28.6315, 77.2167);
  });
</script>
';
require_once __DIR__ . '/includes/footer.php'; 
?>
