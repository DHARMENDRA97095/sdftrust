<?php
// backend/api/projects.php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Fetch all active projects, ordered by newest first
    $stmt = $pdo->query("SELECT id, title, category, location, description, image_url, status FROM projects WHERE status = 'active' ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $projects
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch projects: ' . $e->getMessage()
    ]);
}
?>
