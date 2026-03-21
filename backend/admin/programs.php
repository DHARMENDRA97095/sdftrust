<?php
// backend/admin/programs.php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Create slug from title
function createSlug($string)
{
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

// Generate unique slug
function generateUniqueSlug($pdo, $title)
{
    $baseSlug = createSlug($title);
    $slug = $baseSlug;
    $counter = 1;

    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM programs WHERE slug = ?");
        $stmt->execute([$slug]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            return $slug;
        }

        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
}

// Handle program deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM programs WHERE id = ?");
    $stmt->execute([$id]);
    header("location: programs.php");
    exit;
}

// Handle new program submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title']);
    $program_id = trim($_POST['program_id']);
    $icon = trim($_POST['icon']);
    $beneficiaries = trim($_POST['beneficiaries']);
    $regions = trim($_POST['regions']);
    $description = trim($_POST['description']);
    $status = trim($_POST['status']);

    // Auto generate slug from title
    $slug = generateUniqueSlug($pdo, $title);

    $image_path_for_db = 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&q=80';

    if (isset($_FILES['program_image']) && $_FILES['program_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['program_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $new_name = "program_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
            $upload_path = $upload_dir . $new_name;

            if (move_uploaded_file($_FILES['program_image']['tmp_name'], $upload_path)) {
                $image_path_for_db = $upload_path;
            }
        }
    }

    if (!empty($title) && !empty($program_id) && !empty($description)) {
        $stmt = $pdo->prepare("
            INSERT INTO programs 
            (title, slug, program_id, icon, beneficiaries, regions, description, image_url, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $title,
            $slug,
            $program_id,
            $icon,
            $beneficiaries,
            $regions,
            $description,
            $image_path_for_db,
            $status
        ]);

        header("location: programs.php");
        exit;
    }
}

// Fetch all programs
try {
    $stmt = $pdo->query("SELECT * FROM programs ORDER BY created_at DESC");
    $programs = $stmt->fetchAll();

    $unreadContacts = $pdo->query("SELECT count(*) FROM contacts WHERE status = 'new'")->fetchColumn();
} catch (PDOException $e) {
    die("Database Error");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Programs - SDF Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleModal() {
            const modal = document.getElementById('addProgramModal');
            modal.classList.toggle('hidden');
        }
    </script>
</head>

<body class="bg-gray-50 font-sans flex min-h-screen">

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
                <?php if ($unreadContacts > 0): ?>
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full"><?php echo $unreadContacts; ?></span>
                <?php endif; ?>
            </a>
            <a href="subscribers.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>👥</span> Subscribers
            </a>
            <a href="projects.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🏢</span> Projects
            </a>
            <a href="programs.php" class="flex items-center gap-3 px-6 py-3 bg-[#425032] border-l-4 border-green-400 text-white font-medium">
                <span>🎯</span> Programs
            </a>
            <a href="testimonials.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>🎯</span> Testimonials
            </a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden relative">
        <header class="bg-white shadow border-b border-gray-200 p-4 shrink-0 flex items-center justify-between">
            <h1 class="text-2xl font-bold font-serif text-gray-800">Manage Programs</h1>
            <button onclick="toggleModal()" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-4 py-2 rounded shadow text-sm font-bold flex items-center gap-2">
                <span>➕</span> Add New Program
            </button>
        </header>

        <div class="p-8 flex-1 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($programs as $prog): ?>
                    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden flex flex-col hover:shadow-lg transition-shadow relative pt-8 mt-6">
                        <div class="absolute -top-6 right-6 w-16 h-16 bg-white rounded-full flex items-center justify-center text-3xl shadow-xl z-20 border border-gray-50 mt-10">
                            <?php echo htmlspecialchars($prog['icon']); ?>
                        </div>
                        <div class="absolute top-2 left-2 z-20 flex gap-2">
                            <?php if ($prog['status'] === 'active'): ?>
                                <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase shadow">Active</span>
                            <?php else: ?>
                                <span class="bg-gray-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase shadow"><?php echo htmlspecialchars($prog['status']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="h-40 relative overflow-hidden group">
                            <img src="<?php echo htmlspecialchars($prog['image_url']); ?>" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                <a href="programs.php?delete=<?php echo $prog['id']; ?>" onclick="return confirm('Are you sure you want to delete this program?');" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm shadow">Delete</a>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-xs font-bold text-green-600 uppercase tracking-wider mb-2">
                                ID: <?php echo htmlspecialchars($prog['program_id']); ?>
                            </div>
                            <div class="text-xs text-gray-500 mb-2">
                                Slug: <?php echo htmlspecialchars($prog['slug']); ?>
                            </div>
                            <h3 class="font-serif font-bold text-xl text-gray-900 mb-2 leading-tight"><?php echo htmlspecialchars($prog['title']); ?></h3>
                            <p class="text-gray-600 text-sm flex-1 mb-4"><?php echo htmlspecialchars($prog['description']); ?></p>
                            <div class="flex items-center gap-2 mt-auto pt-4 border-t border-gray-100">
                                <span class="bg-[#E9EFE1] text-green-800 text-xs font-bold px-2 py-1 rounded-full">
                                    <?php echo htmlspecialchars($prog['beneficiaries']); ?> Beneficiaries
                                </span>
                                <span class="bg-blue-50 text-blue-800 text-xs font-bold px-2 py-1 rounded-full">
                                    <?php echo htmlspecialchars($prog['regions']); ?> Active
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (count($programs) === 0): ?>
                    <div class="col-span-3 text-center py-12 text-gray-500 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50">
                        No programs found. Click the button above to add one.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="addProgramModal" class="hidden fixed inset-0 bg-black/50 z-50 flex justify-center items-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white">
                    <h2 class="text-xl font-bold font-serif">Add New Program</h2>
                    <button onclick="toggleModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
                </div>
                <form action="programs.php" method="POST" class="p-6 space-y-4" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Program ID *</label>
                            <input type="text" name="program_id" placeholder="e.g. health" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Emoji)</label>
                            <input type="text" name="icon" placeholder="🏥" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Beneficiaries</label>
                            <input type="text" name="beneficiaries" placeholder="50k+" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Regions</label>
                            <input type="text" name="regions" placeholder="4 States" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Program Image</label>
                        <input
                            type="file"
                            name="program_image"
                            accept="image/*"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 outline-none cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea name="description" rows="4" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 outline-none resize-none"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 mt-2 border-t border-gray-100">
                        <button type="button" onclick="toggleModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded font-medium transition-colors">Cancel</button>
                        <button type="submit" class="bg-[#6a752b] hover:bg-[#5a6425] text-white px-6 py-2 rounded font-bold shadow transition-colors">Save Program</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>