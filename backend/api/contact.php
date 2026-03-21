<?php
// backend/api/contact.php
require_once 'config.php';

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents("php://input"));

// Check if all required fields are present
if (
    isset($data->name) &&
    isset($data->email) &&
    isset($data->subject) &&
    isset($data->message)
) {
    $name = trim($data->name);
    $email = trim($data->email);
    $phone = isset($data->phone) ? trim($data->phone) : null;
    $subject = trim($data->subject);
    $message = trim($data->message);

    // Basic Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit();
    }

    try {
        // Insert into database
        $insertQuery = "INSERT INTO contacts (name, email, phone, subject, message) VALUES (:name, :email, :phone, :subject, :message)";
        $stmt = $pdo->prepare($insertQuery);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Your message has been received! Our team will contact you shortly.'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save message. Please try again.']);
        }
    } catch(PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete data received']);
}
?>
