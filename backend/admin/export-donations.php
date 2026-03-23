<?php
require_once "../api/config.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=donations.csv');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'ID',
    'Transaction ID',
    'First Name',
    'Last Name',
    'Email',
    'Phone',
    'Donation Amount',
    'Address',
    'Message',
    '80G',
    'PAN Number',
    'Payment Status',
    'Created At'
]);

$stmt = $pdo->query("SELECT * FROM donations ORDER BY id DESC");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['id'],
        $row['transaction_id'],
        $row['first_name'],
        $row['last_name'],
        $row['email'],
        $row['phone'],
        $row['donation_amount'],
        $row['address'],
        $row['message'],
        $row['wants_80g'] ? 'Yes' : 'No',
        $row['pan_number'],
        $row['payment_status'],
        $row['created_at']
    ]);
}

fclose($output);
exit;
?>