<?php
// backend/admin/subscribers.php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

try {
    $stmt = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
    $subscribers = $stmt->fetchAll();
    
    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscribers - SDF Admin</title>
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
            <a href="subscribers.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
                <span>👥</span> Subscribers
            </a>
            <a href="projects.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🏢</span> Projects
            </a>
             <a href="programs.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎯</span> Programs
            </a>
            <a href="testimonials.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎯</span> Testimonials
            </a>
            <a href="donations.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>💰</span> Donations
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow border-b border-gray-200 p-4 shrink-0 flex items-center justify-between">
            <h1 class="text-2xl font-bold font-serif text-gray-800">Newsletter Subscribers</h1>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow text-sm font-bold flex items-center gap-2">
                <span>📥</span> Export CSV
            </button>
        </header>

        <div class="p-8 flex-1 overflow-y-auto">
            <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden max-w-4xl">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr class="text-gray-500 text-xs uppercase tracking-wider">
                            <th class="py-4 px-6 font-bold w-16 text-center">ID</th>
                            <th class="py-4 px-6 font-bold">Email Address</th>
                            <th class="py-4 px-6 font-bold text-right">Subscribed Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($subscribers as $sub): ?>
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="py-4 px-6 text-center text-gray-400 font-mono text-sm">
                                #<?php echo $sub['id']; ?>
                            </td>
                            <td class="py-4 px-6 font-medium text-gray-800">
                                <a href="mailto:<?php echo htmlspecialchars($sub['email']); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($sub['email']); ?></a>
                            </td>
                            <td class="py-4 px-6 text-right text-sm text-gray-500 whitespace-nowrap">
                                <?php echo date('M d, Y', strtotime($sub['subscribed_at'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($subscribers) === 0): ?>
                        <tr><td colspan="3" class="py-8 text-center text-gray-500">No subscribers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
