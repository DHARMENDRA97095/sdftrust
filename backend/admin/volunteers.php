<?php
// backend/admin/volunteers.php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM volunteers WHERE id = ?");
    $stmt->execute([$id]);
    header("location: volunteers.php");
    exit;
}

// Fetch all volunteers
try {
    $stmt = $pdo->query("SELECT * FROM volunteers ORDER BY created_at DESC");
    $volunteers = $stmt->fetchAll();
    
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
    <title>Volunteers - SDF Admin</title>
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
            <a href="volunteers.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
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
            <h1 class="text-2xl font-bold font-serif text-gray-800">Volunteer Applications</h1>
        </header>

        <div class="p-8 flex-1 overflow-y-auto">
            <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="py-4 px-6 font-bold">Applicant</th>
                            <th class="py-4 px-6 font-bold">Details</th>
                            <th class="py-4 px-6 font-bold">Interest / Message</th>
                            <th class="py-4 px-6 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach($volunteers as $vol): ?>
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="py-4 px-6 align-top">
                                <div class="font-bold text-gray-800"><?php echo htmlspecialchars($vol['name']); ?></div>
                                <div class="text-xs text-gray-500 mt-1">Age: <?php echo htmlspecialchars($vol['age']); ?></div>
                                <div class="text-xs text-gray-500 mt-1"><?php echo date('M d, Y h:i A', strtotime($vol['created_at'])); ?></div>
                            </td>
                            <td class="py-4 px-6 align-top text-sm">
                                <div class="text-blue-600 font-medium"><a href="mailto:<?php echo htmlspecialchars($vol['email']); ?>"><?php echo htmlspecialchars($vol['email']); ?></a></div>
                                <div class="text-gray-500 mt-1"><?php echo htmlspecialchars($vol['phone']); ?></div>
                                <div class="text-gray-500 mt-2 text-xs border-l-2 pl-2 border-gray-300"><?php echo nl2br(htmlspecialchars($vol['address'])); ?></div>
                            </td>
                            <td class="py-4 px-6 align-top max-w-md">
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded uppercase tracking-wide mb-2 inline-block"><?php echo htmlspecialchars($vol['interest']); ?></span>
                                <div class="text-gray-600 text-sm italic border-l-2 border-gray-200 pl-3 py-1 bg-gray-50 rounded-r mt-2">
                                   "<?php echo nl2br(htmlspecialchars($vol['message'])); ?>"
                                </div>
                            </td>
                            <td class="py-4 px-6 align-top text-right">
                                <a href="volunteers.php?delete=<?php echo $vol['id']; ?>" onclick="return confirm('Are you sure you want to delete this application?');" class="text-xs bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded font-bold transition-colors">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($volunteers) === 0): ?>
                        <tr><td colspan="4" class="py-8 text-center text-gray-500">No volunteer applications found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
