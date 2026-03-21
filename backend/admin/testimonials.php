<?php
// backend/admin/testimonials.php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
    header("location: testimonials.php");
    exit;
}

// Handle new testimonial submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $title = trim($_POST['title']);
    $message_text = trim($_POST['message']);
    
    // Default image if upload fails
    $image_path_for_db = 'https://via.placeholder.com/150';

    // File Upload Logic
    if (isset($_FILES['testimonial_image']) && $_FILES['testimonial_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['testimonial_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $upload_dir = 'uploads/testimonials/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $new_name = "user_" . time() . "." . $ext;
            $upload_path = $upload_dir . $new_name;

            if (move_uploaded_file($_FILES['testimonial_image']['tmp_name'], $upload_path)) {
                $image_path_for_db = $upload_path; 
            }
        }
    }

    if (!empty($name) && !empty($message_text)) {
        $stmt = $pdo->prepare("INSERT INTO testimonials (name, title, message, image_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $title, $message_text, $image_path_for_db]);
        header("location: testimonials.php");
        exit;
    }
}

// Fetch data
try {
    $testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll();
    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Testimonials - SDF Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleModal() {
            document.getElementById('addTestimonialModal').classList.toggle('hidden');
        }
    </script>
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
                <?php if($unreadContacts > 0): ?>
                  <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $unreadContacts; ?></span>
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
            <a href="testimonials.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
                <span>🎯</span> Testimonials
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden relative">
        <header class="bg-white shadow border-b p-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold font-serif text-gray-800">Stories of Impact</h1>
            <button onclick="toggleModal()" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-4 py-2 rounded shadow text-sm font-bold flex items-center gap-2">
                <span>➕</span> Add New Story
            </button>
        </header>

        <div class="p-8 flex-1 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($testimonials as $t): ?>
                <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden flex flex-col hover:shadow-lg transition-shadow relative">
                    <div class="h-32 bg-gray-100 relative">
                        <img src="<?php echo htmlspecialchars($t['image_url']); ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 flex items-center justify-center transition-opacity">
                            <a href="testimonials.php?delete=<?php echo $t['id']; ?>" onclick="return confirm('Delete this story?');" class="bg-red-600 text-white py-1 px-3 rounded text-xs font-bold">Delete</a>
                        </div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-lg text-gray-900 leading-tight"><?php echo htmlspecialchars($t['name']); ?></h3>
                        <p class="text-xs text-green-600 font-bold uppercase mb-3"><?php echo htmlspecialchars($t['title']); ?></p>
                        <p class="text-gray-600 text-sm italic flex-1 border-l-2 border-gray-200 pl-3">"<?php echo htmlspecialchars($t['message']); ?>"</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="addTestimonialModal" class="hidden fixed inset-0 bg-black/50 z-50 flex justify-center items-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
                <div class="flex justify-between items-center p-6 border-b">
                    <h2 class="text-xl font-bold font-serif">Add New Story</h2>
                    <button onclick="toggleModal()" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
                </div>
                <form action="testimonials.php" method="POST" class="p-6 space-y-4" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title / Designation</label>
                        <input type="text" name="title" placeholder="e.g. Local Farmer / Volunteer" class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                        <input type="file" name="testimonial_image" accept="image/*" class="w-full px-4 py-2 border rounded-lg text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-50 file:text-green-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                        <textarea name="message" rows="4" required class="w-full px-4 py-2 rounded-lg border focus:ring-2 focus:ring-green-500 outline-none resize-none"></textarea>
                    </div>
                    <div class="pt-4 flex justify-end gap-3 border-t">
                        <button type="button" onclick="toggleModal()" class="px-4 py-2 text-gray-600">Cancel</button>
                        <button type="submit" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-6 py-2 rounded font-bold shadow">Save Story</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>