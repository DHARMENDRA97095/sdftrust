<?php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

function normalizePhoneForWhatsApp($phone)
{
    $phone = preg_replace('/\D+/', '', $phone);

    // If Indian local 10 digit number, prepend 91
    if (strlen($phone) === 10) {
        $phone = '91' . $phone;
    }

    return $phone;
}

function buildWhatsAppReceiptMessage($donation)
{
    $firstName     = trim($donation['first_name'] ?? '');
    $lastName      = trim($donation['last_name'] ?? '');
    $fullName      = trim($firstName . ' ' . $lastName);
    $amount        = $donation['donation_amount'] ?? '0';
    $transactionId = $donation['transaction_id'] ?? '-';
    $createdAt     = $donation['created_at'] ?? date('Y-m-d H:i:s');
    $panNumber     = $donation['pan_number'] ?? '-';
    $receiptNo     = 'RCPT-' . str_pad($donation['id'], 5, '0', STR_PAD_LEFT);

    $message = "80G Donation Receipt - SDF Trust\n\n";
    $message .= "Dear " . ($fullName ?: 'Donor') . ",\n\n";
    $message .= "Thank you for your generous contribution to SDF Trust.\n\n";
    $message .= "Donation Details:\n";
    $message .= "Receipt No: " . $receiptNo . "\n";
    $message .= "Transaction ID: " . ($transactionId ?: '-') . "\n";
    $message .= "Donor Name: " . ($fullName ?: 'Donor') . "\n";
    $message .= "Phone: " . ($donation['phone'] ?? '-') . "\n";
    $message .= "Amount: ₹" . $amount . "\n";
    $message .= "PAN: " . ($panNumber ?: '-') . "\n";
    $message .= "Date: " . $createdAt . "\n\n";
    $message .= "This receipt is issued for donation acknowledgement and 80G reference purpose.\n\n";
    $message .= "Regards,\nSDF Trust";

    return $message;
}

/*
|--------------------------------------------------------------------------
| Handle Manual Status + Transaction Update
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id            = isset($_POST['donation_id']) ? (int) $_POST['donation_id'] : 0;
    $adminStatus   = trim($_POST['admin_status'] ?? '');
    $adminNote     = trim($_POST['admin_note'] ?? '');
    $transactionId = trim($_POST['transaction_id'] ?? '');

    $allowedStatuses = ['pending', 'approved', 'rejected'];

    if ($id <= 0 || !in_array($adminStatus, $allowedStatuses, true)) {
        $_SESSION['error_message'] = "Invalid donation ID or status.";
        header("location: donations.php");
        exit;
    }

    try {
        $checkStmt = $pdo->prepare("SELECT id FROM donations WHERE transaction_id = ? AND id != ? LIMIT 1");
        $checkStmt->execute([$transactionId, $id]);
        $existingTransaction = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($transactionId) && $existingTransaction) {
            $_SESSION['error_message'] = "Transaction ID already exists for another donation.";
            header("location: donations.php");
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE donations 
            SET transaction_id = ?, admin_status = ?, admin_note = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $transactionId !== '' ? $transactionId : null,
            $adminStatus,
            $adminNote !== '' ? $adminNote : null,
            $id
        ]);

        $_SESSION['success_message'] = "Donation updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Failed to update donation.";
    }

    header("location: donations.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Handle WhatsApp Receipt Send
|--------------------------------------------------------------------------
*/
if (isset($_GET['send_whatsapp_receipt']) && is_numeric($_GET['send_whatsapp_receipt'])) {
    $id = (int) $_GET['send_whatsapp_receipt'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ?");
        $stmt->execute([$id]);
        $donation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$donation) {
            $_SESSION['error_message'] = "Donation record not found.";
            header("location: donations.php");
            exit;
        }

        if (($donation['admin_status'] ?? 'pending') !== 'approved') {
            $_SESSION['error_message'] = "Only approved donations can send WhatsApp receipt.";
            header("location: donations.php");
            exit;
        }

        if (empty(trim($donation['transaction_id'] ?? ''))) {
            $_SESSION['error_message'] = "Please add transaction ID before sending receipt.";
            header("location: donations.php");
            exit;
        }

        $phone = trim($donation['phone'] ?? '');
        $phone = normalizePhoneForWhatsApp($phone);

        if (empty($phone)) {
            $_SESSION['error_message'] = "Valid donor phone number not found.";
            header("location: donations.php");
            exit;
        }

        $message = buildWhatsAppReceiptMessage($donation);
        $waLink = "https://wa.me/" . urlencode($phone) . "?text=" . urlencode($message);

        $updateStmt = $pdo->prepare("
            UPDATE donations 
            SET receipt_sent = 1, receipt_sent_at = NOW()
            WHERE id = ?
        ");
        $updateStmt->execute([$id]);

        header("Location: " . $waLink);
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error occurred.";
        header("location: donations.php");
        exit;
    }
}

// Fetch data
try {
    $stmt = $pdo->query("SELECT * FROM donations ORDER BY id DESC");
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error");
}

function getAdminStatusBadgeClass($status)
{
    switch ($status) {
        case 'approved':
            return 'bg-green-100 text-green-700 border border-green-200';
        case 'pending':
            return 'bg-yellow-100 text-yellow-700 border border-yellow-200';
        case 'rejected':
            return 'bg-red-100 text-red-700 border border-red-200';
        default:
            return 'bg-gray-100 text-gray-700 border border-gray-200';
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
                <?php if($unreadContacts > 0): ?>
                  <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $unreadContacts; ?></span>
                <?php endif; ?>
            </a>
            <a href="subscribers.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>👥</span> Subscribers
            </a>
            <a href="volunteers.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🤝</span> Volunteers
            </a>
            <a href="projects.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🏢</span> Projects
            </a>
            <a href="programs.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎯</span> Programs
            </a>
                        <a href="publications.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>📝</span> Publications
            </a>
<a href="testimonials.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎯</span> Testimonials
            </a>
            <a href="medias.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🏢</span> Photo Gallery
            </a>
            <a href="videos.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎥</span> Video Gallery
            </a>
            <a href="donations.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
                <span>🎥</span> Donation
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow border-b p-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold font-serif text-gray-800">Donations</h1>
                <p class="text-sm text-gray-500 mt-1">Manage all donation records and send WhatsApp receipts.</p>
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

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-sm text-gray-500">Total Donations</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-2"><?php echo count($donations); ?></h3>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-sm text-gray-500">Approved Donations</p>
                    <h3 class="text-3xl font-bold text-blue-600 mt-2">
                        <?php echo count(array_filter($donations, fn($item) => ($item['admin_status'] ?? 'pending') === 'approved')); ?>
                    </h3>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-sm text-gray-500">Pending Review</p>
                    <h3 class="text-3xl font-bold text-yellow-600 mt-2">
                        <?php echo count(array_filter($donations, fn($item) => ($item['admin_status'] ?? 'pending') === 'pending')); ?>
                    </h3>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-sm text-gray-500">Rejected Donations</p>
                    <h3 class="text-3xl font-bold text-red-600 mt-2">
                        <?php echo count(array_filter($donations, fn($item) => ($item['admin_status'] ?? 'pending') === 'rejected')); ?>
                    </h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b bg-gray-50">
                    <h2 class="text-lg font-bold text-gray-800">Donation Records</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-6 py-4 text-left">ID</th>
                                <th class="px-6 py-4 text-left">Transaction ID</th>
                                <th class="px-6 py-4 text-left">Donor</th>
                                <th class="px-6 py-4 text-left">Contact</th>
                                <th class="px-6 py-4 text-left">Amount</th>
                                <th class="px-6 py-4 text-left">80G / PAN</th>
                                <th class="px-6 py-4 text-left">Manual Status</th>
                                <th class="px-6 py-4 text-left">Date</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <?php if (!empty($donations)): ?>
                                <?php foreach ($donations as $row): ?>
                                    <tr class="hover:bg-gray-50 transition align-top">
                                        <td class="px-6 py-4 font-semibold text-gray-800">
                                            #<?php echo htmlspecialchars($row['id']); ?>
                                        </td>

                                        <td class="px-6 py-4 min-w-[220px]">
                                            <form method="POST" class="space-y-2">
                                                <input type="hidden" name="donation_id" value="<?php echo (int)$row['id']; ?>">

                                                <input
                                                    type="text"
                                                    name="transaction_id"
                                                    value="<?php echo htmlspecialchars($row['transaction_id'] ?? ''); ?>"
                                                    placeholder="Enter transaction ID"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                                >

                                                <select name="admin_status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                                    <option value="pending" <?php echo (($row['admin_status'] ?? 'pending') === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="approved" <?php echo (($row['admin_status'] ?? '') === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="rejected" <?php echo (($row['admin_status'] ?? '') === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                                </select>

                                                <!-- <textarea
                                                    name="admin_note"
                                                    rows="2"
                                                    placeholder="Add admin note (optional)"
                                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                                ><?php echo htmlspecialchars($row['admin_note'] ?? ''); ?></textarea> -->

                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold capitalize <?php echo getAdminStatusBadgeClass($row['admin_status'] ?? 'pending'); ?>">
                                                        <?php echo htmlspecialchars($row['admin_status'] ?? 'pending'); ?>
                                                    </span>

                                                    <button type="submit" name="update_status"
                                                        class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-600 hover:bg-blue-100 transition">
                                                        Update
                                                    </button>
                                                </div>
                                            </form>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900">
                                                <?php echo htmlspecialchars(trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: 'Donor'); ?>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1 break-words max-w-[220px]">
                                                <?php echo htmlspecialchars($row['address'] ?? '-'); ?>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <div class="text-gray-800 break-all"><?php echo htmlspecialchars($row['email'] ?? '-'); ?></div>
                                            <div class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($row['phone'] ?? '-'); ?></div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-bold text-green-700">
                                                ₹<?php echo htmlspecialchars($row['donation_amount'] ?? '0'); ?>
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
                                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold capitalize <?php echo getAdminStatusBadgeClass($row['admin_status'] ?? 'pending'); ?>">
                                                <?php echo htmlspecialchars($row['admin_status'] ?? 'pending'); ?>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                            <?php echo htmlspecialchars($row['created_at'] ?? '-'); ?>
                                            <?php if (!empty($row['receipt_sent']) && !empty($row['receipt_sent_at'])): ?>
                                                <div class="text-xs text-green-600 mt-1">
                                                    Receipt sent: <?php echo htmlspecialchars($row['receipt_sent_at']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <?php if (($row['admin_status'] ?? 'pending') === 'approved'): ?>
                                                <a href="donations.php?send_whatsapp_receipt=<?php echo $row['id']; ?>"
                                                   onclick="return confirm('Send WhatsApp 80G receipt to this donor number?');"
                                                   class="inline-flex items-center rounded-lg bg-green-50 px-3 py-2 text-xs font-bold text-green-600 hover:bg-green-100 transition">
                                                    Send WhatsApp Receipt
                                                </a>
                                            <?php else: ?>
                                                <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-2 text-xs font-bold text-gray-500">
                                                    Approve First
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <?php if (!empty($row['message']) || !empty($row['admin_note'])): ?>
                                        <tr class="bg-gray-50">
                                            <td></td>
                                            <td colspan="8" class="px-6 py-3 text-sm text-gray-600">
                                                <?php if (!empty($row['message'])): ?>
                                                    <div class="italic">
                                                        <span class="font-semibold text-gray-700">Donor Message:</span>
                                                        "<?php echo htmlspecialchars($row['message']); ?>"
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($row['admin_note'])): ?>
                                                    <div class="mt-2">
                                                        <span class="font-semibold text-gray-700">Admin Note:</span>
                                                        <?php echo htmlspecialchars($row['admin_note']); ?>
                                                    </div>
                                                <?php endif; ?>
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