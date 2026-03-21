<?php
// backend/api/subscribe.php
require_once 'config.php';

header('Content-Type: application/json');

// Get POST data (since React sends JSON payload, we read php://input)
$data = json_decode(file_get_contents("php://input"));

if (isset($data->email) && !empty(trim($data->email))) {
    $email = trim($data->email);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit();
    }

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Email is already subscribed']);
            exit();
        }

        // Insert new subscriber
        $insertQuery = "INSERT INTO newsletter_subscribers (email) VALUES (:email)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Successfully subscribed to the newsletter!'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to subscribe']);
        }
    } catch(PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
}
?>
