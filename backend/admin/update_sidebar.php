<?php
$dir = __DIR__ . '/';
$files = glob($dir . '*.php');

$insert = '            <a href="publications.php" class="flex items-center gap-3 px-6 py-3 text-gray-300 hover:bg-[#2c4029] hover:text-white transition-colors">
                <span>📝</span> Publications
            </a>
';

foreach ($files as $file) {
    $base = basename($file);
    if ($base == 'publications.php' || $base == 'login.php' || $base == 'logout.php' || $base == 'export-donations.php') continue;
    $content = file_get_contents($file);
    if (strpos($content, '<a href="publications.php"') !== false) continue;
    
    $pattern = '/(<a href="programs\.php".*?<\/a>\s*)/si';
    $content = preg_replace($pattern, "$1" . $insert, $content);
    file_put_contents($file, $content);
}
echo "Done";
?>
