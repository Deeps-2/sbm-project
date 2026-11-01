/* SBM Portal – clock, global search, Leaflet maps, before/after comparator */

/* ---------- Live clock ---------- */
(function clock() {
  const t = document.getElementById('live-clock'), d = document.getElementById('live-date');
  if (!t) return;
  const tick = () => {
    const n = new Date();
    t.textContent = n.toLocaleTimeString('en-GB');
    d.textContent = n.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, ' - ');
  };
  tick(); setInterval(tick, 1000);
})();

/* ---------- Global search: tracking code -> track page, otherwise ward search ---------- */
(function globalSearch() {
  const form = document.getElementById('global-search-form');
  if (!form) return;
  form.addEventListener('submit', e => {
    e.preventDefault();
    const q = document.getElementById('global-search').value.trim();
    if (!q) return;
    const base = window.SBM_BASE || '';
    location.href = /^SBM-/i.test(q)
      ? base + '/track.php?code=' + encodeURIComponent(q)
      : base + '/areas.php?q=' + encodeURIComponent(q);
  });
  // Pre-fill ward search on areas.php from ?q=
  const box = document.getElementById('area-search-input');
  const q = new URLSearchParams(location.search).get('q');
  if (box && q) { box.value = q; box.dispatchEvent(new Event('input')); }
})();

/* ---------- Generic table filter (optional) ---------- */
document.querySelectorAll('input[data-filter-table]').forEach(inp => {
  inp.addEventListener('input', () => {
    const t = document.querySelector(inp.dataset.filterTable);
    t && t.querySelectorAll('tbody tr').forEach(tr => {
      tr.style.display = tr.innerText.toLowerCase().includes(inp.value.toLowerCase()) ? '' : 'none';
    });
  });
});

/* ---------- Maps ---------- */
const OSM = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const OSM_ATTR = '&copy; OpenStreetMap contributors';
const scoreColor = s => s >= 80 ? '#16a34a' : (s >= 60 ? '#ca8a04' : '#dc2626');

function initCleanlinessMap(areas) {
  const el = document.getElementById('cleanliness-map');
  if (!el || typeof L === 'undefined') return;
  const map = L.map(el).setView([28.63, 77.21], 11);
  L.tileLayer(OSM, { attribution: OSM_ATTR, maxZoom: 19 }).addTo(map);

  const markers = areas.map(a => {
    const icon = L.divIcon({
      className: '',
      html: `<div class="sbm-map-pin" style="background:${scoreColor(a.cleanliness_score)}">${a.cleanliness_score}</div>`,
      iconSize: [34, 34], iconAnchor: [17, 17]
    });
    const m = L.marker([+a.latitude, +a.longitude], { icon }).addTo(map);
    m.bindPopup(
      `<strong>${a.name}</strong><br>${a.ward_number} • ${a.zone}<br>` +
      `Score: <b style="color:${scoreColor(a.cleanliness_score)}">${a.cleanliness_score}/100</b> (${a.status})<br>` +
      `Bins: ${a.bins_functional}/${a.total_bins}<br>Officer: ${a.in_charge_officer || '—'}<br>` +
      `<a href="${(window.SBM_BASE || '')}/report.php?ward=${encodeURIComponent(a.ward_number)}">Report issue →</a>`
    );
    return m;
  });

  // clicking a ward in the sidebar flies to its pin (same order as $areas)
  document.querySelectorAll('.ward-list-item').forEach((item, i) => {
    item.addEventListener('click', e => {
      if (e.target.closest('a') || !markers[i]) return;
      map.flyTo(markers[i].getLatLng(), 15);
      markers[i].openPopup();
    });
  });
  if (markers.length) map.fitBounds(L.featureGroup(markers).getBounds().pad(0.2));
}

function initLocationPickerMap(lat, lng) {
  const el = document.getElementById('report-picker-map');
  if (!el || typeof L === 'undefined') return;
  const latIn = document.getElementById('input-lat'), lngIn = document.getElementById('input-lng');
  const map = L.map(el).setView([lat, lng], 14);
  L.tileLayer(OSM, { attribution: OSM_ATTR, maxZoom: 19 }).addTo(map);
  const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
  const set = ll => { latIn.value = ll.lat.toFixed(6); lngIn.value = ll.lng.toFixed(6); };
  marker.on('dragend', () => set(marker.getLatLng()));
  map.on('click', e => { marker.setLatLng(e.latlng); set(e.latlng); });
}

/* ---------- Before / After comparator ---------- */
function initImageComparator(id) {
  const box = document.getElementById(id);
  if (!box || box.dataset.ready) return;
  box.dataset.ready = '1';
  const after = box.querySelector('.sbm-comparator-after');
  const handle = box.querySelector('.sbm-comparator-handle');
  const afterImg = after.querySelector('img');

  const fitImg = () => { afterImg.style.width = afterImg.style.maxWidth = box.offsetWidth + 'px'; };
  const setPos = x => {
    const r = box.getBoundingClientRect();
    const pct = Math.max(0, Math.min(100, ((x - r.left) / r.width) * 100));
    after.style.width = pct + '%';
    handle.style.left = pct + '%';
  };
  let drag = false;
  const start = e => { drag = true; e.preventDefault(); };
  const move = e => { if (drag) setPos((e.touches ? e.touches[0] : e).clientX); };
  const end = () => { drag = false; };

  handle.addEventListener('mousedown', start);
  handle.addEventListener('touchstart', start, { passive: false });
  box.addEventListener('mousedown', e => { drag = true; setPos(e.clientX); });
  box.addEventListener('touchstart', e => { drag = true; setPos(e.touches[0].clientX); }, { passive: true });
  window.addEventListener('mousemove', move);
  window.addEventListener('touchmove', move, { passive: true });
  window.addEventListener('mouseup', end);
  window.addEventListener('touchend', end);
  window.addEventListener('resize', fitImg);
  fitImg();
}

// Home page slider has no explicit init call in index.php, so start it automatically
document.addEventListener('DOMContentLoaded', () => initImageComparator('comparator-box'));
