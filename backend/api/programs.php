<?php
// backend/api/programs.php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM programs WHERE status = 'active' ORDER BY id ASC");
    $programs = $stmt->fetchAll();
    
    echo json_encode([
        'status' => 'success',
        'data' => $programs
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch programs: ' . $e->getMessage()
    ]);
}
?>
