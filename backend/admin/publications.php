<?php
// backend/admin/publications.php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Handle publication deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM publications WHERE id = ?");
    $stmt->execute([$id]);
    header("location: publications.php");
    exit;
}

// Handle new publication submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $type = trim($_POST['type']);
    $title = isset($_POST['title']) ? trim($_POST['title']) : null;
    $category = isset($_POST['category']) ? trim($_POST['category']) : null;
    
    $image_url_for_db = null;
    $file_url_for_db = null;
    $file_size_for_db = null;

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed_img = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_img)) {
            $new_name = "pub_img_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
            $upload_path = $upload_dir . $new_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url_for_db = $upload_path; 
            }
        }
    }

    // Handle Document Upload
    if (isset($_FILES['document']) && $_FILES['document']['error'] === 0) {
        $allowed_doc = ['pdf', 'doc', 'docx'];
        $filename = $_FILES['document']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed_doc)) {
            $new_name = "pub_doc_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
            $upload_path = $upload_dir . $new_name;
            if (move_uploaded_file($_FILES['document']['tmp_name'], $upload_path)) {
                $file_url_for_db = $upload_path; 
                // calculate size in MB
                $size_bytes = filesize($upload_path);
                $size_mb = round($size_bytes / 1048576, 1);
                $file_size_for_db = $size_mb . ' MB';
            }
        }
    }

    if (!empty($type)) {
        $stmt = $pdo->prepare("INSERT INTO publications (type, title, category, image_url, file_url, file_size) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$type, $title, $category, $image_url_for_db, $file_url_for_db, $file_size_for_db]);
        
        header("location: publications.php");
        exit;
    }
}

// Fetch all publications
try {
    $stmt = $pdo->query("SELECT * FROM publications ORDER BY created_at DESC");
    $publications = $stmt->fetchAll();
    
    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Publications - SDF Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleModal() {
            const modal = document.getElementById('addPubModal');
            modal.classList.toggle('hidden');
        }

        function handleTypeChange() {
            const type = document.getElementById('pub_type').value;
            const titleField = document.getElementById('field_title');
            const categoryField = document.getElementById('field_category');
            const imageField = document.getElementById('field_image');
            const documentField = document.getElementById('field_document');

            titleField.classList.remove('hidden');
            
            if (type === 'report') {
                categoryField.classList.add('hidden');
                imageField.classList.add('hidden');
                documentField.classList.remove('hidden');
            } else if (type === 'case_study') {
                categoryField.classList.remove('hidden');
                imageField.classList.remove('hidden');
                documentField.classList.remove('hidden');
            } else if (type === 'gallery') {
                titleField.classList.add('hidden');
                categoryField.classList.add('hidden');
                imageField.classList.remove('hidden');
                documentField.classList.add('hidden');
            }
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
            <a href="projects.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🏢</span> Projects
            </a>
            <a href="programs.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎯</span> Programs
            </a>
            <a href="publications.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
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
                <span>💰</span> Donation
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden relative">
        <header class="bg-white shadow border-b border-gray-200 p-4 shrink-0 flex items-center justify-between">
            <h1 class="text-2xl font-bold font-serif text-gray-800">Manage Publications</h1>
            <button onclick="toggleModal()" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-4 py-2 rounded shadow text-sm font-bold flex items-center gap-2">
                <span>➕</span> Add New
            </button>
        </header>

        <div class="p-8 flex-1 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($publications as $pub): ?>
                <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden flex flex-col hover:shadow-lg transition-shadow relative">
                    <span class="absolute top-4 right-4 bg-gray-500 text-white text-xs font-bold px-2 py-1 rounded uppercase shadow z-10"><?php echo htmlspecialchars($pub['type']); ?></span>
                    
                    <?php if($pub['image_url']): ?>
                    <div class="h-48 relative overflow-hidden bg-gray-100">
                       <img src="<?php echo htmlspecialchars($pub['image_url']); ?>" class="w-full h-full object-cover">
                    </div>
                    <?php endif; ?>
                    
                    <div class="p-6 flex-1 flex flex-col pt-10">
                        <?php if($pub['category']): ?>
                            <div class="text-xs font-bold text-accent uppercase tracking-wider mb-2"><?php echo htmlspecialchars($pub['category']); ?></div>
                        <?php endif; ?>
                        
                        <?php if($pub['title']): ?>
                            <h3 class="font-serif font-bold text-xl text-gray-900 mb-2 leading-tight"><?php echo htmlspecialchars($pub['title']); ?></h3>
                        <?php endif; ?>
                        
                        <?php if($pub['file_url']): ?>
                            <a href="<?php echo htmlspecialchars($pub['file_url']); ?>" target="_blank" class="text-blue-500 hover:underline text-sm font-bold flex items-center gap-1 mt-2">View Document <?php if($pub['file_size']) echo '('.$pub['file_size'].')'; ?> <span class="text-lg">→</span></a>
                        <?php endif; ?>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <a href="publications.php?delete=<?php echo $pub['id']; ?>" onclick="return confirm('Delete this item?');" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($publications) === 0): ?>
                    <div class="col-span-3 text-center py-12 text-gray-500 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                        No publications found.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add Modal -->
        <div id="addPubModal" class="hidden fixed inset-0 bg-black/50 z-50 justify-center items-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <h2 class="text-xl font-bold font-serif">Add Publication</h2>
                    <button onclick="toggleModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
                <form action="publications.php" method="POST" class="p-6 space-y-4" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select name="type" id="pub_type" onchange="handleTypeChange()" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                            <option value="report">Annual / Impact Report</option>
                            <option value="case_study">Case Study</option>
                            <option value="gallery">Photo Gallery</option>
                        </select>
                    </div>

                    <div id="field_title">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    
                    <div id="field_category" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category (Case Study only)</label>
                        <input type="text" name="category" placeholder="e.g. Environment, Education" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                    </div>

                    <div id="field_image" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Image Cover</label>
                        <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 rounded-lg border border-gray-300 outline-none">
                    </div>

                    <div id="field_document">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Document (PDF/DOC)</label>
                        <input type="file" name="document" accept=".pdf,.doc,.docx" class="w-full px-4 py-2 rounded-lg border border-gray-300 outline-none">
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-3 mt-2 border-t border-gray-100">
                        <button type="button" onclick="toggleModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded font-medium transition-colors">Cancel</button>
                        <button type="submit" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-6 py-2 rounded font-bold shadow transition-colors">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        // Init form state
        handleTypeChange();
    </script>
</body>
</html>
