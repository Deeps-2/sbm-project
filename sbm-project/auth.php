<?php
$page_title = "Login & Registration";
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$areas = $db->query("SELECT * FROM areas ORDER BY ward_number ASC")->fetchAll();
?>

<div class="sbm-container" style="max-width: 840px;">
  
  <div style="text-align: center; margin-bottom: 28px;">
    <span class="badge badge-clean">OFFICIAL ACCESS PORTAL</span>
    <h1 style="font-size: 2.2rem; font-weight: 900; color: #0d5377; margin-top: 6px;">
      Swachh Bharat Mission Single Sign-On
    </h1>
    <p style="color: #64748b; font-size: 0.95rem;">
      Access your personalized portal as a Citizen, Sanitation Worker, or Municipal Administrator.
    </p>
  </div>

  <!-- Quick 1-Click Demo Login Bar -->
  <div class="sbm-card" style="background: linear-gradient(135deg, #0d5377 0%, #073b54 100%); color: #ffffff; padding: 22px 28px; border: none;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
      <div>
        <div style="font-size: 0.75rem; font-weight: 800; letter-spacing: 1px; color: #38bdf8;">EVALUATION SPEEDPASS</div>
        <h3 style="font-size: 1.15rem; font-weight: 800; margin-top: 2px;">⚡ 1-Click Instant Demo Login</h3>
        <p style="font-size: 0.82rem; color: #e0f2fe;">Jump straight into any role with pre-loaded mock tasks & data:</p>
      </div>
      <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="<?php echo url('/quick_login.php?role=citizen'); ?>" class="btn btn-sm" style="background: #16a34a; color: #fff;">
          👤 Citizen (Pooja)
        </a>
        <a href="<?php echo url('/quick_login.php?role=worker'); ?>" class="btn btn-sm" style="background: #ea580c; color: #fff;">
          👷 Worker (Ramesh)
        </a>
        <a href="<?php echo url('/quick_login.php?role=admin'); ?>" class="btn btn-sm" style="background: #38bdf8; color: #073b54;">
          🛡️ Admin (Commissioner)
        </a>
      </div>
    </div>
  </div>

  <!-- Tabbed Login & Register Card -->
  <div class="sbm-card" style="padding: 32px;">
    
    <div style="display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px;">
      <button id="tab-btn-login" class="btn" style="flex: 1; border-radius: 0; border-bottom: 3px solid #0d5377; font-size: 1.05rem; background: none; color: #0d5377; font-weight: 800;" onclick="switchAuthTab('login')">
        Existing User Login
      </button>
      <button id="tab-btn-register" class="btn" style="flex: 1; border-radius: 0; border-bottom: 3px solid transparent; font-size: 1.05rem; background: none; color: #64748b; font-weight: 700;" onclick="switchAuthTab('register')">
        New Citizen Registration
      </button>
    </div>

    <!-- Login Form -->
    <div id="auth-login-box">
      <form action="<?php echo url('/login_process.php'); ?>" method="POST">
        <div class="form-group">
          <label class="form-label" for="login-email">Registered Email Address</label>
          <input type="email" id="login-email" name="email" class="form-control" placeholder="e.g. citizen@sbm.gov.in" value="citizen@sbm.gov.in" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="login-password">Account Password</label>
          <input type="password" id="login-password" name="password" class="form-control" placeholder="••••••••" value="citizen123" required>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; font-size: 0.85rem;">
          <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
            <input type="checkbox" checked> Remember this device
          </label>
          <span style="color: #64748b;">Default demo password: <strong>citizen123 / worker123 / admin123</strong></span>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem;">
          Secure Login &rarr;
        </button>
      </form>
    </div>

    <!-- Register Form -->
    <div id="auth-register-box" style="display: none;">
      <form action="<?php echo url('/register_process.php'); ?>" method="POST">
        <div class="sbm-grid-2">
          <div class="form-group">
            <label class="form-label" for="reg-name">Full Name *</label>
            <input type="text" id="reg-name" name="name" class="form-control" placeholder="e.g. Amit Sengupta" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="reg-phone">Mobile Phone (for SMS alerts) *</label>
            <input type="tel" id="reg-phone" name="phone" class="form-control" placeholder="e.g. 9812345678" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="reg-email">Email Address *</label>
          <input type="email" id="reg-email" name="email" class="form-control" placeholder="e.g. amit@example.com" required>
        </div>

        <div class="sbm-grid-2">
          <div class="form-group">
            <label class="form-label" for="reg-ward">Residential Municipal Ward</label>
            <select id="reg-ward" name="ward_id" class="form-control">
              <?php foreach ($areas as $a): ?>
                <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['ward_number'] . ' - ' . $a['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="reg-password">Create Password *</label>
            <input type="password" id="reg-password" name="password" class="form-control" placeholder="Min 6 characters" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem;">
          Create Free Citizen Account & Join (+50 Bonus Pts) &rarr;
        </button>
      </form>
    </div>

  </div>

</div>

<?php 
$extra_scripts = '
<script>
  function switchAuthTab(tab) {
    const loginBox = document.getElementById("auth-login-box");
    const registerBox = document.getElementById("auth-register-box");
    const btnLogin = document.getElementById("tab-btn-login");
    const btnRegister = document.getElementById("tab-btn-register");

    if (tab === "login") {
      loginBox.style.display = "block";
      registerBox.style.display = "none";
      btnLogin.style.borderBottomColor = "#0d5377";
      btnLogin.style.color = "#0d5377";
      btnLogin.style.fontWeight = "800";
      btnRegister.style.borderBottomColor = "transparent";
      btnRegister.style.color = "#64748b";
      btnRegister.style.fontWeight = "700";
    } else {
      loginBox.style.display = "none";
      registerBox.style.display = "block";
      btnRegister.style.borderBottomColor = "#0d5377";
      btnRegister.style.color = "#0d5377";
      btnRegister.style.fontWeight = "800";
      btnLogin.style.borderBottomColor = "transparent";
      btnLogin.style.color = "#64748b";
      btnLogin.style.fontWeight = "700";
    }
  }
</script>
';
require_once __DIR__ . '/includes/footer.php'; 
?>
