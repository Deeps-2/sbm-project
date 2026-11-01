<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('/report.php'));
    exit;
}

$title = trim($_POST['title'] ?? '');
$category = trim($_POST['category'] ?? 'Garbage Dump');
$area_id = (int)($_POST['area_id'] ?? 1);
$priority = trim($_POST['priority'] ?? 'Medium');
$location_address = trim($_POST['location_address'] ?? '');
$latitude = (float)($_POST['latitude'] ?? 28.6315);
$longitude = (float)($_POST['longitude'] ?? 77.2167);
$description = trim($_POST['description'] ?? '');

// Current logged in user or default citizen (Pooja Sharma, id = 6)
$user_id = $_SESSION['user_id'] ?? 6;

// Handle file upload
$photo_path = '/assets/uploads/sample_before.svg';
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/assets/uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $file_tmp = $_FILES['photo']['tmp_name'];
    $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['photo']['name']));
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        $photo_path = '/assets/uploads/' . $file_name;
    }
}

// Generate unique tracking code
$tracking_code = 'SBM-2026-' . rand(1050, 9999);

$db = get_db();
$stmt = $db->prepare("
    INSERT INTO complaints (
        tracking_code, user_id, area_id, title, category, description,
        location_address, latitude, longitude, citizen_photo, priority,
        status, points_awarded
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 50)
");

$stmt->execute([
    $tracking_code, $user_id, $area_id, $title, $category, $description,
    $location_address, $latitude, $longitude, $photo_path, $priority
]);

// Award +50 citizen points for genuine report
update_user_points($user_id, 50);

// Log activity
log_activity(
    "New Grievance {$tracking_code} Registered",
    "Citizen reported '{$title}' at {$location_address}.",
    "Complaint",
    "alert-circle"
);

// Recalculate area score (pending complaint affects score)
calculate_area_score($area_id);

set_flash("Complaint registered successfully with ID {$tracking_code}! +50 Citizen points awarded.", "success");
header("Location: " . url('/track.php?code=' . urlencode($tracking_code)));
exit;
?>
