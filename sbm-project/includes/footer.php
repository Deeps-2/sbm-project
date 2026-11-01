</main>

<footer class="sbm-footer">
  <div>🇮🇳 <strong>Swachh Bharat Mission – Urban Sanitation Portal</strong></div>
  <div>Helpline 1969 &nbsp;•&nbsp; Clean India, Healthy India</div>
</footer>

<script>window.SBM_BASE = <?php echo json_encode(base_url()); ?>;</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="<?php echo url('/assets/js/main.js'); ?>"></script>
<?php echo $extra_scripts ?? ''; ?>
</body>
</html>
