<?php
// Tăng thời gian chờ và cho phép đọc file từ xa
ini_set('default_socket_timeout', 15);

$repos = [
    "AppTesters" => "https://repository.apptesters.org/repo.json",
    "ChungChi365" => "https://repo.chungchi365.com/repo.json",
    "Cypwn" => "https://ipa.cypwn.xyz/cypwn.json",
    "FastSign" => "https://fastsign.dev/repo.json",
    "NabzClan" => "https://appstore.nabzclan.vip/repos/esign.php"
];

$allApps = [];

// Hàm lấy dữ liệu bằng PHP (Server-side) giúp tránh lỗi CORS
function getRepoData($url) {
    $ctx = stream_context_create(['http' => ['timeout' => 5]]);
    $content = @file_get_contents($url, false, $ctx);
    if ($content === false) return null;
    return json_decode($content, true);
}

foreach ($repos as $name => $url) {
    $data = getRepoData($url);
    if ($data) {
        $items = $data['apps'] ?? $data['applications'] ?? $data['items'] ?? [];
        foreach ($items as $app) {
            $allApps[] = [
                'source' => $name,
                'name'   => $app['name'] ?? $app['title'] ?? 'Unknown',
                'icon'   => $app['icon'] ?? $app['iconURL'] ?? '',
                'down'   => $app['down'] ?? $app['downloadURL'] ?? $app['url'] ?? '#',
                'ver'    => $app['version'] ?? 'N/A'
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP IPA Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #f2f2f7; font-family: -apple-system, sans-serif; }
        .app-card { background: white; border-radius: 16px; display: flex; align-items: center; padding: 12px; margin-bottom: 10px; }
        .btn-down { background: #f0f0f7; color: #007aff; font-weight: bold; border-radius: 20px; padding: 6px 15px; font-size: 13px; }
    </style>
</head>
<body class="p-4">
    <h1 class="text-3xl font-bold mb-6 text-center">IPA Store (PHP)</h1>
    
    <div class="max-w-2xl mx-auto">
        <?php foreach ($allApps as $app): ?>
            <div class="app-card shadow-sm">
                <img src="<?= $app['icon'] ?>" class="w-14 h-14 rounded-2xl border mr-4" onerror="this.src='https://apple.com/favicon.ico'">
                <div class="flex-1">
                    <h3 class="font-bold text-gray-800 line-clamp-1"><?= htmlspecialchars($app['name']) ?></h3>
                    <p class="text-xs text-gray-500"><?= $app['source'] ?> • v<?= $app['ver'] ?></p>
                </div>
                <a href="<?= $app['down'] ?>" class="btn-down">TẢI VỀ</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
