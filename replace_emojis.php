<?php

$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/.*\.blade\.php$/', RegexIterator::GET_MATCH);

$replacements = [
    '🎉' => '<i class="bi bi-award-fill text-success me-1"></i>',
    '😔' => '<i class="bi bi-x-circle text-danger me-1"></i>',
    '📋' => '<i class="bi bi-clipboard-data text-warning me-1"></i>',
    '🔍' => '<i class="bi bi-search me-1"></i>',
    '📁' => '<i class="bi bi-folder-fill me-1"></i>',
    '📝' => '<i class="bi bi-pencil-square me-1"></i>',
    '🚀' => '<i class="bi bi-lightning-charge-fill text-warning me-1"></i>',
    '🏆' => '<i class="bi bi-trophy-fill text-warning me-1"></i>',
    '✨' => '<i class="bi bi-stars text-warning me-1"></i>',
    '✅' => '<i class="bi bi-check-circle-fill text-success me-1"></i>',
    '❌' => '<i class="bi bi-x-circle-fill text-danger me-1"></i>',
    '⚠️' => '<i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>',
    '🔥' => '<i class="bi bi-fire text-danger me-1"></i>',
];

$count = 0;
foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;
    
    foreach ($replacements as $emoji => $icon) {
        $content = str_replace($emoji, $icon, $content);
    }
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        $count++;
        echo "Replaced emojis in: $path\n";
    }
}

// Don't forget Admin\PengumumanController.php
$controller = 'app/Http/Controllers/Admin/PengumumanController.php';
if (file_exists($controller)) {
    $content = file_get_contents($controller);
    $original = $content;
    foreach ($replacements as $emoji => $icon) {
        $content = str_replace($emoji, $icon, $content);
    }
    if ($content !== $original) {
        file_put_contents($controller, $content);
        echo "Replaced emojis in: $controller\n";
    }
}

echo "Done! Modified $count blade files.\n";
