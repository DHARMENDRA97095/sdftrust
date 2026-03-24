<?php
// backend/admin/index.php
session_start();
require_once '../api/config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Fetch stats for dashboard
try {
    $subscribersCount = $pdo->query("SELECT count(*) FROM newsletter_subscribers")->fetchColumn();
    $contactsCount = $pdo->query("SELECT count(*) FROM contacts")->fetchColumn();
    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
    $projectsCount = $pdo->query("SELECT count(*) FROM projects")->fetchColumn();
    $donationCount = $pdo->query("SELECT count(*) FROM donations")->fetchColumn();
    $programCount = $pdo->query("SELECT count(*) FROM programs")->fetchColumn();
    

    
    // Fetch 5 most recent contacts
    $recentContacts = $pdo->query("SELECT name, email, subject, status, created_at FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SDF Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-[#233520] text-white flex flex-col hidden md:flex">
        <div class="p-6 flex items-center gap-3 border-b border-gray-700">
            <span class="text-3xl">🌿</span>
            <div>
                <h2 class="font-serif font-bold text-xl leading-tight">SDF</h2>
                <span class="text-xs text-green-300">Admin Panel</span>
            </div>
        </div>
        <nav class="flex-grow py-6">
            <a href="index.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
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
            <a href="donations.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎥</span> Donation
            </a>
        </nav>
        <div class="p-4 border-t border-gray-700">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-300">Welcome, <b><?php echo htmlspecialchars($_SESSION['admin_username']); ?></b></span>
                <a href="logout.php" class="text-red-400 hover:text-red-300 font-bold" title="Logout">🚪</a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Header -->
        <header class="bg-white shadow border-b border-gray-200 p-4 shrink-0 flex items-center justify-between">
            <h1 class="text-2xl font-bold font-serif text-gray-800">Overview</h1>
            <div class="md:hidden flex items-center gap-4">
               <a href="logout.php" class="text-red-500 text-sm font-bold border border-red-500 px-3 py-1 rounded">Logout</a>
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="p-8 flex-1 overflow-y-auto">
            
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 mt-4 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-2xl">👥</div>
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Subscribers</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $subscribersCount; ?></h3>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center text-2xl">✉️</div>
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Messages</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $contactsCount; ?></h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-2xl">⚠️</div>
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Unread Mails</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $unreadContacts; ?></h3>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-2xl">🏢</div>
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Projects</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $projectsCount; ?></h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-2xl">🏢</div>
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Donations</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $donationCount; ?></h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-2xl">🏢</div>
                    <div>
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Programs</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $programCount; ?></h3>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800">Recent Contact Submissions</h3>
                    <a href="contacts.php" class="text-sm text-blue-600 hover:text-blue-800 font-bold">View All &rarr;</a>
                </div>
                
                <?php if (count($recentContacts) > 0): ?>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-gray-500 text-sm uppercase tracking-wider border-b">
                                <th class="py-3 px-6">Name</th>
                                <th class="py-3 px-6">Subject</th>
                                <th class="py-3 px-6">Status</th>
                                <th class="py-3 px-6">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 divide-y divide-gray-100">
                            <?php foreach($recentContacts as $contact): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-6 font-medium">
                                    <?php echo htmlspecialchars($contact['name']); ?>
                                </td>
                                <td class="py-4 px-6 text-sm">
                                    <?php echo htmlspecialchars($contact['subject']); ?>
                                </td>
                                <td class="py-4 px-6">
                                    <?php if($contact['status'] == 'new'): ?>
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold uppercase">New</span>
                                    <?php elseif($contact['status'] == 'read'): ?>
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold uppercase">Read</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold uppercase">Replied</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($contact['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="p-8 text-center text-gray-500">
                        No contact messages received yet.
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</body>
</html>
