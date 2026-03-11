<?php
$cacheFile = 'repo_cache.json';
$cacheTime = 3600; // Lưu cache trong 1 giờ (3600 giây)

// 1. Kiểm tra Cache để load tức thì
if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTime)) {
    $allApps = json_decode(file_get_contents($cacheFile), true);
} else {
    // 2. Nếu không có cache, tải song song bằng Multi-Curl
    $repos = [
        "AppTesters" => "https://repository.apptesters.org/repo.json",
        "ChungChi365" => "https://repo.chungchi365.com/repo.json",
        "Cypwn" => "https://ipa.cypwn.xyz/cypwn.json",
        "FastSign" => "https://fastsign.dev/repo.json",
        "NabzClan" => "https://appstore.nabzclan.vip/repos/esign.php"
    ];

    $mh = curl_multi_init();
    $curl_array = [];
    foreach ($repos as $name => $url) {
        $curl_array[$name] = curl_init($url);
        curl_setopt($curl_array[$name], CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_array[$name], CURLOPT_TIMEOUT, 5); // Timeout 5 giây để tránh treo
        curl_setopt($curl_array[$name], CURLOPT_SSL_VERIFYPEER, false);
        curl_multi_add_handle($mh, $curl_array[$name]);
    }

    $running = null;
    do { curl_multi_exec($mh, $running); } while ($running > 0);

    $allApps = [];
    foreach ($repos as $name => $url) {
        $content = curl_multi_getcontent($curl_array[$name]);
        $data = json_decode($content, true);
        $items = $data['apps'] ?? $data['applications'] ?? $data['items'] ?? [];
        foreach ($items as $app) {
            $allApps[] = [
                'source' => $name,
                'name' => $app['name'] ?? $app['title'] ?? 'Unknown',
                'icon' => $app['icon'] ?? $app['iconURL'] ?? 'https://apple.com/favicon.ico',
                'down' => $app['down'] ?? $app['downloadURL'] ?? $app['url'] ?? '#',
                'ver' => $app['version'] ?? 'Latest'
            ];
        }
        curl_multi_remove_handle($mh, $curl_array[$name]);
    }
    curl_multi_close($mh);

    // Lưu lại vào file cache
    file_put_contents($cacheFile, json_encode($allApps));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>App Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f2f2f7; font-family: -apple-system, sans-serif; }
        .app-icon { border-radius: 22%; width: 60px; height: 60px; object-fit: cover; border: 0.5px solid #d1d1d6; }
        .btn-get { background: #f0f0f7; color: #007aff; font-weight: 800; border-radius: 20px; padding: 6px 22px; font-size: 14px; }
        .ios-blur { backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); background: rgba(255,255,255,0.8); border-top: 0.5px solid #a7a7aa; }
    </style>
</head>
<body class="pb-24">

    <header class="px-5 pt-14 pb-4">
        <h1 class="text-4xl font-extrabold tracking-tight text-black">Hôm nay</h1>
        <p class="text-gray-400 font-semibold uppercase text-[11px] mt-1"><?php echo date('d F'); ?></p>
    </header>

    <div class="px-5 mb-6">
        <input type="text" id="searchInput" placeholder="Tìm kiếm ứng dụng..." 
               class="w-full bg-gray-200/70 pl-4 pr-4 py-2.5 rounded-xl outline-none text-[16px]">
    </div>

    <div id="appList" class="px-5 space-y-5">
        <?php foreach ($allApps as $app): ?>
            <div class="flex items-center justify-between app-item">
                <div class="flex items-center space-x-4 flex-1 overflow-hidden">
                    <img src="<?= $app['icon'] ?>" class="app-icon" loading="lazy">
                    <div class="flex-1 overflow-hidden">
                        <h3 class="font-bold text-[16px] text-black truncate leading-tight"><?= htmlspecialchars($app['name']) ?></h3>
                        <p class="text-[12px] text-gray-500 truncate"><?= $app['source'] ?></p>
                        <p class="text-[10px] text-blue-500 font-bold uppercase mt-0.5">v<?= $app['ver'] ?></p>
                    </div>
                </div>
                <a href="<?= $app['down'] ?>" download class="btn-get ml-3">NHẬN</a>
            </div>
        <?php endforeach; ?>
    </div>

    <nav class="fixed bottom-0 left-0 right-0 ios-blur flex justify-around pt-3 pb-8">
        <div class="text-blue-500 text-center"><i class="fa-solid fa-note-sticky text-xl"></i><p class="text-[10px] mt-1">Hôm nay</p></div>
        <div class="text-gray-400 text-center"><i class="fa-solid fa-gamepad text-xl"></i><p class="text-[10px] mt-1">Trò chơi</p></div>
        <div class="text-gray-400 text-center"><i class="fa-solid fa-layer-group text-xl"></i><p class="text-[10px] mt-1">Ứng dụng</p></div>
        <div class="text-gray-400 text-center"><i class="fa-solid fa-magnifying-glass text-xl"></i><p class="text-[10px] mt-1">Tìm kiếm</p></div>
    </nav>

    <script>
        document.getElementById('searchInput').oninput = (e) => {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.app-item').forEach(app => {
                const name = app.querySelector('h3').innerText.toLowerCase();
                app.style.display = name.includes(term) ? 'flex' : 'none';
            });
        };
    </script>
</body>
</html>
