<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "config.php";

$data = json_decode(file_get_contents("php://input"), true);
$transaction_id = trim($data['transaction_id'] ?? '');

if (empty($transaction_id)) {
    echo json_encode([
        "success" => false,
        "message" => "Transaction ID is required."
    ]);
    exit();
}

try {
    $stmt = $pdo->prepare("
        UPDATE donations
        SET payment_status = 'expired'
        WHERE transaction_id = :transaction_id
        AND payment_status = 'pending'
    ");
    $stmt->execute([":transaction_id" => $transaction_id]);

    echo json_encode([
        "success" => true,
        "message" => "Payment expired."
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>