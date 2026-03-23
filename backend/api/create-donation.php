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

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request data."
    ]);
    exit();
}

$first_name = trim($data['first_name'] ?? '');
$last_name = trim($data['last_name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$donation_amount = trim($data['donation_amount'] ?? '');
$address = trim($data['address'] ?? '');
$message = trim($data['message'] ?? '');
$wants_80g = !empty($data['wants_80g']) ? 1 : 0;
$pan_number = strtoupper(trim($data['pan_number'] ?? ''));

if (
    empty($first_name) ||
    empty($last_name) ||
    empty($email) ||
    empty($phone) ||
    empty($donation_amount) ||
    empty($address)
) {
    echo json_encode([
        "success" => false,
        "message" => "Please fill all required fields."
    ]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email address."
    ]);
    exit();
}

if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid 10-digit Indian phone number."
    ]);
    exit();
}

if (!is_numeric($donation_amount) || $donation_amount <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Donation amount must be greater than 0."
    ]);
    exit();
}

if ($wants_80g && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan_number)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid PAN number."
    ]);
    exit();
}

$transaction_id = "TXN" . date("YmdHis") . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

try {
    $stmt = $pdo->prepare("
        INSERT INTO donations (
            transaction_id,
            first_name,
            last_name,
            email,
            phone,
            donation_amount,
            address,
            message,
            wants_80g,
            pan_number,
            payment_status
        ) VALUES (
            :transaction_id,
            :first_name,
            :last_name,
            :email,
            :phone,
            :donation_amount,
            :address,
            :message,
            :wants_80g,
            :pan_number,
            'pending'
        )
    ");

    $stmt->execute([
        ":transaction_id" => $transaction_id,
        ":first_name" => $first_name,
        ":last_name" => $last_name,
        ":email" => $email,
        ":phone" => $phone,
        ":donation_amount" => $donation_amount,
        ":address" => $address,
        ":message" => $message,
        ":wants_80g" => $wants_80g,
        ":pan_number" => $pan_number ?: null
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Donation initiated.",
        "transaction_id" => $transaction_id
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>