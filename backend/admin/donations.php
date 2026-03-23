<?php
// backend/admin/donations.php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Handle 80G Receipt Email Send
|--------------------------------------------------------------------------
*/
if (isset($_GET['send_receipt']) && is_numeric($_GET['send_receipt'])) {
    $id = (int) $_GET['send_receipt'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ?");
        $stmt->execute([$id]);
        $donation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$donation) {
            $_SESSION['error_message'] = "Donation record not found.";
            header("location: donations.php");
            exit;
        }

        $email = trim($donation['email'] ?? '');
        $firstName = trim($donation['first_name'] ?? '');
        $lastName = trim($donation['last_name'] ?? '');
        $fullName = trim($firstName . ' ' . $lastName);
        $amount = $donation['donation_amount'] ?? '0';
        $transactionId = $donation['transaction_id'] ?? '-';
        $createdAt = $donation['created_at'] ?? date('Y-m-d H:i:s');
        $panNumber = $donation['pan_number'] ?? '-';
        $receiptNo = 'RCPT-' . str_pad($donation['id'], 5, '0', STR_PAD_LEFT);

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Valid donor email not found.";
            header("location: donations.php");
            exit;
        }

        $subject = "80G Donation Receipt - SDF Trust";

        $message = "
        <html>
        <head>
            <title>80G Donation Receipt</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #222;'>
            <div style='max-width: 700px; margin: 0 auto; border: 1px solid #e5e7eb; padding: 24px; border-radius: 12px;'>
                <h2 style='color: #166534; margin-bottom: 10px;'>80G Donation Receipt</h2>
                
                <p>Dear <strong>" . htmlspecialchars($fullName ?: 'Donor') . "</strong>,</p>

                <p>Thank you for your generous contribution to <strong>SDF Trust</strong>.</p>
                <p>Your donation details are given below:</p>

                <table style='width: 100%; border-collapse: collapse; margin-top: 16px;'>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 10px;'><strong>Receipt No.</strong></td>
                        <td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($receiptNo) . "</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 10px;'><strong>Transaction ID</strong></td>
                        <td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($transactionId) . "</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 10px;'><strong>Donor Name</strong></td>
                        <td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($fullName ?: 'Donor') . "</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 10px;'><strong>Email</strong></td>
                        <td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($email) . "</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 10px;'><strong>Donation Amount</strong></td>
                        <td style='border: 1px solid #ddd; padding: 10px;'>₹" . htmlspecialchars($amount) . "</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 10px;'><strong>PAN</strong></td>
                        <td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($panNumber ?: '-') . "</td>
                    </tr>
                    <tr>
                        <td style='border: 1px solid #ddd; padding: 10px;'><strong>Date</strong></td>
                        <td style='border: 1px solid #ddd; padding: 10px;'>" . htmlspecialchars($createdAt) . "</td>
                    </tr>
                </table>

                <p style='margin-top: 20px;'>
                    This receipt is issued for donation acknowledgement and 80G reference purpose.
                </p>

                <p style='margin-top: 24px;'>
                    Regards,<br>
                    <strong>SDF Trust</strong>
                </p>
            </div>
        </body>
        </html>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: SDF Trust <dharmendra97095@gmail.com>" . "\r\n";

        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['success_message'] = "80G receipt sent successfully to " . $email;
        } else {
            $_SESSION['error_message'] = "Failed to send email. Please check server mail configuration.";
        }

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error occurred.";
    }

    header("location: donations.php");
    exit;
}

// Fetch data
try {
    $stmt = $pdo->query("SELECT * FROM donations ORDER BY id DESC");
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error");
}

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'success':
            return 'bg-green-100 text-green-700 border border-green-200';
        case 'pending':
            return 'bg-yellow-100 text-yellow-700 border border-yellow-200';
        case 'failed':
            return 'bg-red-100 text-red-700 border border-red-200';
        case 'expired':
            return 'bg-gray-100 text-gray-700 border border-gray-200';
        default:
            return 'bg-blue-100 text-blue-700 border border-blue-200';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations - SDF Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans flex min-h-screen">

    <!-- sidebar -->
    <aside class="w-64 bg-[#233520] text-white flex flex-col hidden md:flex shrink-0">
        <div class="p-6 flex items-center gap-3 border-b border-gray-700">
            <span class="text-3xl">🌿</span>
            <div>
                <h2 class="font-serif font-bold text-xl leading-tight">SDF</h2>
                <span class="text-xs text-green-300">Admin Panel</span>
            </div>
        </div>

        <nav class="flex-grow py-6">
            <a href="index.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>📊</span> Dashboard
            </a>

            <a href="contacts.php" class="flex items-center justify-between px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <div class="flex items-center gap-3">
                    <span>✉️</span> Messages
                </div>
                <?php if ($unreadContacts > 0): ?>
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                        <?php echo $unreadContacts; ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="subscribers.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>👥</span> Subscribers
            </a>

            <a href="projects.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🏢</span> Projects
            </a>

            <a href="programs.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎯</span> Programs
            </a>

            <a href="testimonials.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>💬</span> Testimonials
            </a>

            <a href="donations.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
                <span>💰</span> Donations
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow border-b p-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold font-serif text-gray-800">Donations</h1>
                <p class="text-sm text-gray-500 mt-1">Manage all donation records and export them anytime.</p>
            </div>

            <a href="export-donations.php"
               class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-4 py-2 rounded shadow text-sm font-bold flex items-center gap-2">
                <span>📥</span> Export CSV
            </a>
        </header>

        <div class="p-8 flex-1 overflow-y-auto">

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                    <?php
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                    <?php
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Summary cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-sm text-gray-500">Total Donations</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2"><?php echo count($donations); ?></h3>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-sm text-gray-500">Successful Donations</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-2">
                        <?php echo count(array_filter($donations, fn($item) => $item['payment_status'] === 'success')); ?>
                    </h3>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-sm text-gray-500">Pending Donations</p>
                    <h3 class="text-3xl font-bold text-yellow-600 mt-2">
                        <?php echo count(array_filter($donations, fn($item) => $item['payment_status'] === 'pending')); ?>
                    </h3>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Donation Records</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-6 py-4 text-left">ID</th>
                                <th class="px-6 py-4 text-left">Transaction</th>
                                <th class="px-6 py-4 text-left">Donor</th>
                                <th class="px-6 py-4 text-left">Contact</th>
                                <th class="px-6 py-4 text-left">Amount</th>
                                <th class="px-6 py-4 text-left">80G / PAN</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-left">Date</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <?php if (!empty($donations)): ?>
                                <?php foreach ($donations as $row): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-semibold text-gray-800">
                                            #<?php echo htmlspecialchars($row['id']); ?>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900 break-all">
                                                <?php echo htmlspecialchars($row['transaction_id'] ?: '-'); ?>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900">
                                                <?php echo htmlspecialchars(trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''))); ?>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1 break-words max-w-[220px]">
                                                <?php echo htmlspecialchars($row['address'] ?? '-'); ?>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="text-gray-800"><?php echo htmlspecialchars($row['email'] ?? '-'); ?></div>
                                            <div class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-bold text-green-700">
                                                ₹<?php echo htmlspecialchars($row['donation_amount']); ?>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="text-gray-800 font-medium">
                                                <?php echo !empty($row['wants_80g']) ? 'Yes' : 'No'; ?>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                PAN: <?php echo htmlspecialchars(!empty($row['pan_number']) ? $row['pan_number'] : '-'); ?>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold capitalize <?php echo getStatusBadgeClass($row['payment_status']); ?>">
                                                <?php echo htmlspecialchars($row['payment_status']); ?>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                            <?php echo htmlspecialchars($row['created_at']); ?>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <a href="donations.php?send_receipt=<?php echo $row['id']; ?>"
                                               onclick="return confirm('Send 80G receipt to this donor email?');"
                                               class="inline-flex items-center rounded-lg bg-green-50 px-3 py-2 text-xs font-bold text-green-600 hover:bg-green-100 transition">
                                                Send 80G Receipt
                                            </a>
                                        </td>
                                    </tr>

                                    <?php if (!empty($row['message'])): ?>
                                        <tr class="bg-gray-50">
                                            <td></td>
                                            <td colspan="8" class="px-6 py-3 text-sm text-gray-600 italic">
                                                <span class="font-semibold text-gray-700">Message:</span>
                                                "<?php echo htmlspecialchars($row['message']); ?>"
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        No donation records found.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>