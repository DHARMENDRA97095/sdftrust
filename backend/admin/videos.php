<?php
// backend/admin/videos.php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// 1. Handle Deletion (Now deletes the physical thumbnail image too!)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Find the image path first
    $stmt = $pdo->prepare("SELECT image_url FROM video_gallery WHERE id = ?");
    $stmt->execute([$id]);
    $video = $stmt->fetch();
    
    // Delete the file from server
    if ($video && !empty($video['image_url']) && file_exists($video['image_url'])) {
        unlink($video['image_url']); 
    }
    
    // Delete record
    $stmt = $pdo->prepare("DELETE FROM video_gallery WHERE id = ?");
    $stmt->execute([$id]);
    header("location: videos.php");
    exit;
}

// 2. Handle New Video + Thumbnail Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title']);
    $video_url = trim($_POST['video_url']);
    $duration = trim($_POST['duration']);
    $views = isset($_POST['views']) ? (int)$_POST['views'] : 0;
    
    $image_path_for_db = null;

    // Handle File Upload
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['thumbnail']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $upload_dir = 'uploads/thumbnails/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $new_name = "thumb_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
            $upload_path = $upload_dir . $new_name;

            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
                $image_path_for_db = $upload_path; 
            }
        }
    }
    
    // Database Insertion
    if (!empty($title) && !empty($video_url) && $image_path_for_db) {
        $stmt = $pdo->prepare("INSERT INTO video_gallery (title, video_url, image_url, duration, views) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $video_url, $image_path_for_db, $duration, $views]);
        header("location: videos.php");
        exit;
    }
}

// Fetch all videos
try {
    $stmt = $pdo->query("SELECT * FROM video_gallery ORDER BY created_at DESC");
    $videos = $stmt->fetchAll();
    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Video Gallery - SDF Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleModal() {
            document.getElementById('addVideoModal').classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-50 font-sans flex min-h-screen">
    <aside class="w-64 bg-[#233520] text-white flex flex-col md:flex shrink-0">
        <div class="p-6 flex items-center gap-3 border-b border-gray-700">
            <span class="text-3xl">🌿</span>
            <div><h2 class="font-serif font-bold text-xl leading-tight">SDF</h2><span class="text-xs text-green-300">Admin Panel</span></div>
        </div>
        <nav class="grow py-6">
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
            <a href="videos.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
                <span>🎥</span> Video Gallery
            </a>
            <a href="donations.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎥</span> Donation
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden relative">
        <header class="bg-white shadow border-b border-gray-200 p-4 shrink-0 flex items-center justify-between">
            <h1 class="text-2xl font-bold font-serif text-gray-800">Manage Video Gallery</h1>
            <button onclick="toggleModal()" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-4 py-2 rounded shadow text-sm font-bold flex items-center gap-2">
                <span>➕</span> Add New Video
            </button>
        </header>

        <div class="p-8 flex-1 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($videos as $vid): ?>
                <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden flex flex-col hover:shadow-lg transition-shadow">
                    
                    <div class="h-48 relative overflow-hidden group bg-gray-900 flex items-center justify-center">
                        <img src="<?php echo htmlspecialchars($vid['image_url']); ?>" class="absolute inset-0 w-full h-full object-cover opacity-60">
                        <div class="w-16 h-16 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center transition-colors group-hover:bg-white/50 z-10 cursor-pointer">
                            <span class="text-white text-2xl ml-1">▶</span>
                        </div>
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity z-20">
                            <a href="videos.php?delete=<?php echo $vid['id']; ?>" onclick="return confirm('Delete this video?');" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow">Delete</a>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="font-serif font-bold text-xl text-gray-900 mb-2 leading-tight"><?php echo htmlspecialchars($vid['title']); ?></h3>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="addVideoModal" class="hidden fixed inset-0 bg-black/50 z-50 flex justify-center items-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
                <div class="flex justify-between items-center p-6 border-b border-gray-100">
                    <h2 class="text-xl font-bold font-serif">Add New Video</h2>
                    <button onclick="toggleModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
                
                <form action="videos.php" method="POST" class="p-6 space-y-4" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
                    <div><label class="block text-sm font-medium mb-1">Title *</label><input type="text" name="title" required class="w-full px-4 py-2 border rounded-lg outline-none"></div>
                    <div><label class="block text-sm font-medium mb-1">YouTube URL *</label><input type="url" name="video_url" required class="w-full px-4 py-2 border rounded-lg outline-none"></div>
                    
                    <div>
                        <label class="block text-sm font-medium mb-1">Upload Thumbnail Image *</label>
                        <input type="file" name="thumbnail" accept="image/*" required class="w-full px-4 py-2 border rounded-lg cursor-pointer">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium mb-1">Duration</label><input type="text" name="duration" class="w-full px-4 py-2 border rounded-lg outline-none"></div>
                        <div><label class="block text-sm font-medium mb-1">Views</label><input type="number" name="views" value="0" class="w-full px-4 py-2 border rounded-lg outline-none"></div>
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-3"><button type="submit" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-6 py-2 rounded font-bold">Save</button></div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>