<?php
// 1. Nâng giới hạn tài nguyên ngay lập tức
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 60);

$cacheFile = 'repo_cache.json';
$cacheTime = 1800; // 30 phút

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    $allApps = json_decode(file_get_contents($cacheFile), true);
} else {
    $repos = [
        "AppTesters" => "https://repository.apptesters.org",
        "ChungChi365" => "https://repo.chungchi365.com/repo.json",
        "Cypwn" => "https://ipa.cypwn.xyz/cypwn.json",
        "FastSign" => "https://fastsign.dev/repo.json",
        "NabzClan" => "https://appstore.nabzclan.vip/repos/esign.php"
    ];

    $allApps = [];

    foreach ($repos as $name => $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $raw = curl_exec($ch);
        curl_close($ch);

        if ($raw) {
            $data = json_decode($raw, true);
            unset($raw); // Giải phóng chuỗi thô ngay

            $items = $data['apps'] ?? $data['applications'] ?? $data['items'] ?? [];
            unset($data); // Giải phóng mảng gốc ngay

            foreach ($items as $app) {
                // Chỉ giữ lại 4 trường thông tin quan trọng nhất
                $allApps[] = [
                    'n' => $app['name'] ?? $app['title'] ?? 'N/A',
                    'i' => $app['icon'] ?? $app['iconURL'] ?? 'https://apple.com/favicon.ico',
                    'd' => $app['down'] ?? $app['downloadURL'] ?? $app['url'] ?? '#',
                    'v' => $app['version'] ?? 'Mod'
                ];
            }
            unset($items); // Giải phóng danh sách tạm
        }
    }
    // Sắp xếp theo tên để tìm kiếm nhanh hơn
    usort($allApps, fn($a, $b) => strcmp($a['n'], $b['n']));
    file_put_contents($cacheFile, json_encode($allApps));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VSA STORE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #0b0e14; color: #fff; font-family: system-ui, -apple-system, sans-serif; }
        .vsa-card { background: #161b22; border: 1px solid #30363d; border-radius: 14px; }
        .btn-download { background: linear-gradient(90deg, #007aff, #00c6ff); font-weight: 700; transition: 0.2s; }
        .btn-download:active { transform: scale(0.95); opacity: 0.8; }
        .search-box { background: #0d1117; border: 1px solid #30363d; }
        /* Tối ưu hóa render danh sách lớn */
        #appList { content-visibility: auto; }
    </style>
</head>
<body class="p-4 pb-20">

    <div class="max-w-xl mx-auto">
        <header class="mb-8 mt-4">
            <h1 class="text-3xl font-black italic tracking-tighter">VSA<span class="text-blue-500">STORE</span></h1>
            <p class="text-gray-500 text-xs">Premium IPA Collections</p>
        </header>

        <div class="sticky top-4 z-50 mb-8">
            <input type="text" id="searchInput" placeholder="Tìm tên App..." 
                   class="search-box w-full px-5 py-4 rounded-2xl outline-none focus:border-blue-500 shadow-2xl shadow-blue-500/10 text-sm">
        </div>

        <div id="appList" class="space-y-3">
            <?php foreach (array_slice($allApps, 0, 300) as $app): // Chỉ hiện 300 app đầu để web không lag ?>
                <div class="vsa-card p-4 flex items-center justify-between app-item">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <img src="<?= $app['i'] ?>" class="w-14 h-14 rounded-2xl object-cover border border-gray-800" loading="lazy">
                        <div class="overflow-hidden">
                            <h3 class="font-bold text-[14px] truncate text-gray-100"><?= htmlspecialchars($app['n']) ?></h3>
                            <p class="text-[10px] text-blue-400 font-mono mt-0.5">VERSION: <?= $app['v'] ?></p>
                        </div>
                    </div>
                    <a href="<?= $app['d'] ?>" download class="btn-download text-[11px] px-5 py-2 rounded-lg ml-2 flex-shrink-0">TẢI VỀ</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Search cực nhanh với Vanilla JS
        const searchInput = document.getElementById('searchInput');
        const items = document.getElementsByClassName('app-item');

        searchInput.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            for (let i = 0; i < items.length; i++) {
                const name = items[i].getElementsByTagName('h3')[0].innerText.toLowerCase();
                items[i].style.display = name.includes(val) ? 'flex' : 'none';
            }
        });
    </script>
</body>
</html>
