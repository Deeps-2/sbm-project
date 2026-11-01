<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';

/* ---------- URL helper (works in XAMPP sub-folder or php -S) ---------- */
function base_url(): string {
    $root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $proj = str_replace('\\', '/', dirname(__DIR__));
    return ($root !== '' && strpos($proj, $root) === 0) ? substr($proj, strlen($root)) : '';
}
function url(string $path = ''): string {
    if ($path === '') return base_url() . '/';
    if (preg_match('#^https?://#', $path)) return $path;
    // SQLite seed data uses /static/*.png paths; map them to this project's /assets/*.svg
    if (strpos($path, '/static/') === 0) {
        $path = '/assets/' . substr($path, 8);
        $path = preg_replace('/(avatar_[a-z0-9]+)\.png$/', '$1.svg', $path);
    }
    return base_url() . $path;
}

/* ---------- Flash messages ---------- */
function set_flash(string $msg, string $type = 'success'): void { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; }
function get_flash(): ?array { $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f; }

/* ---------- Activity log ---------- */
function log_activity(string $title, string $desc, string $cat = 'General', string $icon = 'activity'): void {
    $db = get_db();
    $db->prepare("INSERT INTO activities (title, description, category, icon) VALUES (?, ?, ?, ?)")
       ->execute([$title, $desc, $cat, $icon]);
}

/* ---------- Points & badges ---------- */
function update_user_points(int $user_id, int $add): void {
    $db = get_db();
    $st = $db->prepare("SELECT points FROM users WHERE id = ?");
    $st->execute([$user_id]);
    $u = $st->fetch();
    if (!$u) return;
    $pts = (int)$u['points'] + $add;
    $badge = $pts >= 600 ? 'National Swachh Icon' : ($pts >= 300 ? 'Green Warrior' : ($pts >= 100 ? 'Cleanliness Champion' : 'Swachhta Mitra'));
    $db->prepare("UPDATE users SET points = ?, badge = ? WHERE id = ?")->execute([$pts, $badge, $user_id]);
}

/* ---------- Cleanliness score (0-100) ---------- */
function calculate_area_score(int $area_id): array {
    $db = get_db();
    $st = $db->prepare("SELECT * FROM areas WHERE id = ?");
    $st->execute([$area_id]);
    $area = $st->fetch();
    if (!$area) return [70, 'Moderate'];

    $inspection = (int)($area['inspection_rating'] ?: 75);
    $total = max((int)$area['total_bins'], 1);
    $ratio = min((int)$area['bins_functional'], $total) / $total;
    $base  = ($inspection * 0.40) + ($ratio * 100 * 0.20);          // 40% + 20%

    $st = $db->prepare("SELECT priority FROM complaints WHERE area_id = ?
                        AND status IN ('Pending','Assigned','In Progress','Verification Required')");
    $st->execute([$area_id]);
    $penalty = 0;
    foreach ($st->fetchAll() as $c) {
        $penalty += ['Urgent' => 12, 'High' => 8, 'Medium' => 5][$c['priority']] ?? 3;
    }

    $st = $db->prepare("SELECT COUNT(*) AS n FROM complaints WHERE area_id = ? AND status = 'Resolved'");
    $st->execute([$area_id]);
    $reward = min(((int)$st->fetch()['n']) * 5, 25);

    $score  = max(15, min(100, (int)($base + 40 - $penalty + $reward)));
    $status = $score >= 80 ? 'Clean' : ($score >= 60 ? 'Moderate' : 'Not Clean');

    $db->prepare("UPDATE areas SET cleanliness_score = ?, status = ? WHERE id = ?")->execute([$score, $status, $area_id]);
    return [$score, $status];
}
