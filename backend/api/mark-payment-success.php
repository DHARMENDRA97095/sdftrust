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

if ($transaction_id === '') {
    echo json_encode([
        "success" => false,
        "message" => "Transaction ID is required."
    ]);
    exit();
}

try {
    $stmt = $pdo->prepare("
        UPDATE donations
        SET payment_status = 'success'
        WHERE transaction_id = :transaction_id
          AND payment_status = 'pending'
    ");

    $stmt->execute([
        ':transaction_id' => $transaction_id
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Payment marked as success."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Transaction not found or already updated."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}