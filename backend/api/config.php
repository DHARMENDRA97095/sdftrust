<?php
// backend/api/config.php

// CORS Headers - Allow React to communicate with this API
header('Access-Control-Allow-Origin: *'); // In production, change * to your frontend URL
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_NAME', 'sdf_database');

/*
|--------------------------------------------------------------------------
| Mail / SMTP Configuration
|--------------------------------------------------------------------------
| Local XAMPP par direct mail kaam nahi karta.
| Production me apne real domain/email credentials use karna.
*/
define('MAIL_HOST', 'mail.dharmendra97095@gmail.com');
define('MAIL_PORT', 465); // 465 = SSL, 587 = TLS
define('MAIL_USERNAME', 'dharmendra97095@gmail.com');
define('MAIL_PASSWORD', 'Dkp@97095');
define('MAIL_FROM_EMAIL', 'dharmendra97095@gmail.com');
define('MAIL_FROM_NAME', 'SDF Trust');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // If connection fails, output JSON error to frontend
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit();
}
?>