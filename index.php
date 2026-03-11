<?php
$repos = [
    "AppTesters" => "https://repository.apptesters.org/repo.json",
    "ChungChi365" => "https://repo.chungchi365.com/repo.json",
    "Cypwn" => "https://ipa.cypwn.xyz/cypwn.json",
    "FastSign" => "https://fastsign.dev/repo.json",
    "NabzClan" => "https://appstore.nabzclan.vip/repos/esign.php"
];

$allApps = [];
foreach ($repos as $name => $url) {
    $ctx = stream_context_create(['http' => ['timeout' => 5]]);
    $content = @file_get_contents($url, false, $ctx);
    if ($content) {
        $data = json_decode($content, true);
        $items = $data['apps'] ?? $data['applications'] ?? $data['items'] ?? [];
        foreach ($items as $app) {
            $allApps[] = [
                'source' => $name,
                'name' => $app['name'] ?? $app['title'] ?? 'Unknown',
                'icon' => $app['icon'] ?? $app['iconURL'] ?? 'https://apple.com/favicon.ico',
                'down' => $app['down'] ?? $app['downloadURL'] ?? $app['url'] ?? '#',
                'ver' => $app['version'] ?? 'Mới nhất'
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>App Store IPA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --ios-blue: #007aff; --bg-gray: #f2f2f7; }
        body { background: var(--bg-gray); font-family: -apple-system, system-ui, sans-serif; -webkit-tap-highlight-color: transparent; }
        .ios-blur { backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px); background: rgba(255,255,255,0.8); }
        .app-icon { border-radius: 22.37%; box-shadow: inset 0 0 1px rgba(0,0,0,0.1); width: 62px; height: 62px; object-fit: cover; }
        .btn-get { background: #e9e9eb; color: var(--ios-blue); font-weight: 800; border-radius: 20px; padding: 6px 20px; font-size: 15px; }
        .btn-get:active { background: #d1d1d6; }
        .bottom-nav { border-top: 0.5px solid #a7a7aa; }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        .section-title { font-size: 22px; font-weight: 700; padding: 15px 20px 5px; border-top: 0.5px solid #d1d1d6; margin-top: 20px; }
    </style>
</head>
<body class="pb-32">

    <header class="px-5 pt-12 pb-4 bg-[#f2f2f7]">
        <div class="flex justify-between items-center mb-2">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter"><?php echo date('D, d M'); ?></span>
            <img src="https://i.pravatar.cc/100" class="w-9 h-9 rounded-full border border-gray-300">
        </div>
        <h1 class="text-3xl font-extrabold tracking-tight">Hôm nay</h1>
    </header>

    <div class="px-5 mb-6">
        <div class="relative">
            <i class="fa fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
            <input type="text" id="searchInput" placeholder="Tìm kiếm ứng dụng..." 
                   class="w-full bg-white/70 pl-10 pr-4 py-2.5 rounded-xl outline-none text-sm shadow-sm border border-gray-200 focus:bg-white">
        </div>
    </div>

    <div class="section-title">Ứng dụng mới nhất</div>
    <div id="appList" class="px-5 space-y-4 mt-3">
        <?php foreach ($allApps as $index => $app): ?>
            <div class="flex items-center justify-between app-item pb-4 <?= ($index < count($allApps)-1) ? 'border-b border-gray-200' : '' ?>">
                <div class="flex items-center space-x-4 flex-1 overflow-hidden">
                    <img src="<?= $app['icon'] ?>" class="app-icon" onerror="this.src='https://apple.com/favicon.ico'">
                    <div class="flex-1 overflow-hidden">
                        <h3 class="font-bold text-[16px] text-black line-clamp-1 leading-tight"><?= htmlspecialchars($app['name']) ?></h3>
                        <p class="text-[13px] text-gray-500 truncate"><?= $app['source'] ?></p>
                        <p class="text-[11px] text-blue-500 mt-1 uppercase font-semibold">v<?= $app['ver'] ?></p>
                    </div>
                </div>
                <div class="ml-4 flex flex-col items-center">
                    <a href="<?= $app['down'] ?>" download class="btn-get mb-1">NHẬN</a>
                    <span class="text-[9px] text-gray-400">Mua trong APP</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <nav class="fixed bottom-0 left-0 right-0 ios-blur bottom-nav flex justify-around pt-2 pb-8 px-2">
        <div class="flex flex-col items-center text-blue-500">
            <i class="fa-solid fa-note-sticky text-xl"></i>
            <span class="text-[10px] mt-1">Hôm nay</span>
        </div>
        <div class="flex flex-col items-center text-gray-400">
            <i class="fa-solid fa-gamepad text-xl"></i>
            <span class="text-[10px] mt-1">Trò chơi</span>
        </div>
        <div class="flex flex-col items-center text-gray-400">
            <i class="fa-solid fa-layer-group text-xl"></i>
            <span class="text-[10px] mt-1">Ứng dụng</span>
        </div>
        <div class="flex flex-col items-center text-gray-400">
            <i class="fa-solid fa-rocket text-xl"></i>
            <span class="text-[10px] mt-1">Arcade</span>
        </div>
        <div class="flex flex-col items-center text-gray-400">
            <i class="fa-solid fa-magnifying-glass text-xl"></i>
            <span class="text-[10px] mt-1">Tìm kiếm</span>
        </div>
    </nav>

    <script>
        // Search Logic
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.app-item').forEach(app => {
                const name = app.querySelector('h3').innerText.toLowerCase();
                const source = app.querySelector('p').innerText.toLowerCase();
                app.style.display = (name.includes(term) || source.includes(term)) ? 'flex' : 'none';
            });
        });
    </script>
</body>
</html>
