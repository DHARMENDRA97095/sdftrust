<?php
// backend/setup_admin.php
require_once 'api/config.php';

try {
    // Generate a fresh password hash
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Empty the table
    $pdo->exec("TRUNCATE TABLE admin_users");

    // Insert the admin user with the correct hash
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES ('admin', ?)");
    
    if ($stmt->execute([$hashedPassword])) {
        echo "<div style='font-family: sans-serif; padding: 20px; background: #e0f8e0; border: 1px solid #0a0; border-radius: 5px;'>";
        echo "✅ <b>Admin User Reset Successfully!</b><br><br>";
        echo "Username: <b>admin</b><br>";
        echo "Password: <b>admin123</b><br><br>";
        echo "<a href='admin/login.php' style='padding: 8px 16px; background: #0a0; color: #fff; text-decoration: none; border-radius: 4px;'>Go to Login Page</a>";
        echo "</div>";
    } else {
        echo "❌ Failed to insert admin user.";
    }
} catch (Exception $e) {
    echo "❌ <b>Database Error:</b> " . $e->getMessage();
}
?>
