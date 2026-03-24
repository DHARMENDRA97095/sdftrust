<?php
// backend/admin/medias.php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Handle project deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM photo_gallery WHERE id = ?");
    $stmt->execute([$id]);
    header("location: medias.php");
    exit;
}

// Handle new project submission
// Handle new project submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title']);
    
    
    
    // 2. Handle File Upload
    if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['gallery_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            // Create the directory if it doesn't exist
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Create unique name
            $new_name = "media_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
            $upload_path = $upload_dir . $new_name;

            if (move_uploaded_file($_FILES['gallery_image']['tmp_name'], $upload_path)) {
                // Update variable with the local path for the DB
                $image_path_for_db = $upload_path; 
            }
        }
    }

    // 3. Database Insertion
    if (!empty($title) ) {
        $stmt = $pdo->prepare("INSERT INTO photo_gallery (title, image_url) VALUES (? , ?)");
        $stmt->execute([$title,  $image_path_for_db]);
        
        header("location: medias.php");
        exit;
    }
}

// Fetch all projects
try {
    $stmt = $pdo->query("SELECT * FROM photo_gallery ORDER BY created_at DESC");
    $medias = $stmt->fetchAll();
    
    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Photo Gallery - SDF Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleModal() {
            const modal = document.getElementById('addPhotoModal');
            modal.classList.toggle('hidden');
        }
    </script>
</head>


<body class="bg-gray-50 font-sans flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#233520] text-white flex flex-col md:flex shrink-0">
        <div class="p-6 flex items-center gap-3 border-b border-gray-700">
            <span class="text-3xl">🌿</span>
            <div>
                <h2 class="font-serif font-bold text-xl leading-tight">SDF</h2>
                <span class="text-xs text-green-300">Admin Panel</span>
            </div>
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
            <a href="programs.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🏢</span> Projects
                
<a href="programs.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎯</span> Programs
            </a>
                        <a href="publications.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>📝</span> Publications
            </a>
<a href="testimonials.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎯</span> Testimonials
            </a>
            <a href="medias.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
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


    <main class="flex-1 flex flex-col overflow-hidden relative">
        <header class="bg-white shadow border-b border-gray-200 p-4 shrink-0 flex items-center justify-between">
            <h1 class="text-2xl font-bold font-serif text-gray-800">Manage Photo Gallery</h1>
            <button onclick="toggleModal()" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-4 py-2 rounded shadow text-sm font-bold flex items-center gap-2">
                <span>➕</span> Add New Photo
            </button>
        </header>


        <div class="p-8 flex-1 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($medias as $med): ?>
                <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden flex flex-col hover:shadow-lg transition-shadow">
                    <div class="h-48 relative overflow-hidden group">
                        <img src="<?php echo htmlspecialchars($med['image_url']); ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                            <a href="medias.php?delete=<?php echo $med['id']; ?>" onclick="return confirm('Are you sure you want to delete this image?');" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm shadow">Delete Photo</a>
                        </div>
                        
                           
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        
                        <h3 class="font-serif font-bold text-xl text-gray-900 mb-2 leading-tight"><?php echo htmlspecialchars($med['title']); ?></h3>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($medias) === 0): ?>
                    <div class="col-span-3 text-center py-12 text-gray-500 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                        No Photo found. Click the button above to add one.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Photo Modal -->
        <div id="addPhotoModal" class="hidden fixed inset-0 bg-black/50 z-50 flex justify-center items-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white">
                    <h2 class="text-xl font-bold font-serif">Add New Photo</h2>
                    <button onclick="toggleModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
                <form action="medias.php" method="POST" class="p-6 space-y-4" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1"> Title *</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
    <div class="relative">
        <input 
            type="file" 
            name="gallery_image" 
            accept="image/*"
            class="w-full px-4 py-2 rounded-lg border border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 outline-none cursor-pointer"
        >
    </div>
    <p class="text-xs text-gray-400 mt-1">Recommended size: 1200x800px (Max 2MB).</p>
</div>
                    
                    
                    <div class="pt-4 flex justify-end gap-3 mt-2 border-t border-gray-100">
                        <button type="button" onclick="toggleModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded font-medium transition-colors">Cancel</button>
                        <button type="submit" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-6 py-2 rounded font-bold shadow transition-colors">Save Image</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</body>
</html>




