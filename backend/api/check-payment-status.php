<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once "config.php";

$transaction_id = trim($_GET['transaction_id'] ?? '');

if (empty($transaction_id)) {
    echo json_encode([
        "success" => false,
        "message" => "Transaction ID is required."
    ]);
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT transaction_id, payment_status
        FROM donations
        WHERE transaction_id = :transaction_id
        LIMIT 1
    ");
    $stmt->execute([
        ':transaction_id' => $transaction_id
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode([
            "success" => false,
            "message" => "Transaction not found."
        ]);
        exit();
    }

    echo json_encode([
        "success" => true,
        "transaction_id" => $row['transaction_id'],
        "payment_status" => $row['payment_status']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}