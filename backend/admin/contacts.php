<?php
// backend/admin/contacts.php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}


// Handle status updates
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $id = $_GET['mark_read'];
    $stmt = $pdo->prepare("UPDATE contacts SET status = 'read' WHERE id = ?");
    $stmt->execute([$id]);
    header("location: contacts.php");
    exit;
}

// Fetch all contacts
try {
    $stmt = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $contacts = $stmt->fetchAll();
    
    // For sidebar bubble
    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages - SDF Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans flex min-h-screen">

    <!-- Sidebar -->
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
            <a href="contacts.php" class="flex items-center justify-between px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
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
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow border-b border-gray-200 p-4 shrink-0 flex items-center justify-between">
            <h1 class="text-2xl font-bold font-serif text-gray-800">Contact Messages</h1>
        </header>

        <div class="p-8 flex-1 overflow-y-auto">
            <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="py-4 px-6 font-bold">Sender</th>
                            <th class="py-4 px-6 font-bold">Contact Info</th>
                            <th class="py-4 px-6 font-bold">Subject & Message</th>
                            <th class="py-4 px-6 font-bold">Date</th>
                            <th class="py-4 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($contacts as $msg): ?>
                        <tr class="hover:bg-blue-50/30 transition-colors <?php echo $msg['status'] === 'new' ? 'bg-orange-50/40' : ''; ?>">
                            <td class="py-4 px-6 align-top">
                                <div class="font-bold text-gray-800"><?php echo htmlspecialchars($msg['name']); ?></div>
                                <?php if($msg['status'] == 'new'): ?>
                                    <span class="inline-block mt-1 bg-red-100 text-red-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">New</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 align-top text-sm">
                                <div class="text-blue-600 font-medium"><a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>"><?php echo htmlspecialchars($msg['email']); ?></a></div>
                                <div class="text-gray-500 mt-1"><?php echo htmlspecialchars($msg['phone']); ?></div>
                            </td>
                            <td class="py-4 px-6 align-top max-w-md">
                                <div class="font-bold text-gray-800 text-sm mb-1"><?php echo htmlspecialchars($msg['subject']); ?></div>
                                <div class="text-gray-600 text-sm italic border-l-2 border-gray-200 pl-3 py-1 bg-gray-50 rounded-r">
                                   "<?php echo nl2br(htmlspecialchars($msg['message'])); ?>"
                                </div>
                            </td>
                            <td class="py-4 px-6 align-top text-xs text-gray-500 whitespace-nowrap">
                                <?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?>
                            </td>
                            <td class="py-4 px-6 align-top text-right">
                                <?php if($msg['status'] == 'new'): ?>
                                    <a href="contacts.php?mark_read=<?php echo $msg['id']; ?>" class="text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 px-3 py-1.5 rounded font-bold transition-colors">Mark as Read</a>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400 font-bold uppercase">Read</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($contacts) === 0): ?>
                        <tr><td colspan="5" class="py-8 text-center text-gray-500">No contact messages found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
