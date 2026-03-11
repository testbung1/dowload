<?php
$cacheFile = 'repo_cache.json';
$cacheTime = 1800; // Cache 30 phút

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

    $mh = curl_multi_init();
    $curl_array = [];
    foreach ($repos as $name => $url) {
        $curl_array[$name] = curl_init($url);
        curl_setopt($curl_array[$name], CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_array[$name], CURLOPT_TIMEOUT, 10);
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
                'icon' => $app['icon'] ?? $app['iconURL'] ?? 'https://vsacheat.com/assets/img/logo.png',
                'down' => $app['down'] ?? $app['downloadURL'] ?? $app['url'] ?? '#',
                'ver' => $app['version'] ?? 'Premium'
            ];
        }
        curl_multi_remove_handle($mh, $curl_array[$name]);
    }
    curl_multi_close($mh);
    file_put_contents($cacheFile, json_encode($allApps));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VSA Store - IPA Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0b0e14; color: #fff; font-family: 'Inter', sans-serif; }
        .vsa-gradient { background: linear-gradient(135deg, #007aff 0%, #00c6ff 100%); }
        .vsa-card { background: #161b22; border: 1px solid #30363d; border-radius: 12px; transition: 0.3s; }
        .vsa-card:hover { border-color: #58a6ff; transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
        .glow-button { background: #007aff; box-shadow: 0 0 15px rgba(0,122,255,0.5); border-radius: 8px; font-weight: bold; transition: 0.3s; }
        .glow-button:hover { box-shadow: 0 0 25px rgba(0,122,255,0.8); transform: scale(1.05); }
        .vsa-badge { background: rgba(0, 122, 255, 0.1); color: #58a6ff; border: 1px solid rgba(0, 122, 255, 0.3); font-size: 10px; padding: 2px 8px; border-radius: 20px; }
        .nav-blur { backdrop-filter: blur(10px); background: rgba(11, 14, 20, 0.8); border-bottom: 1px solid #30363d; }
    </style>
</head>
<body class="pb-10">

    <nav class="sticky top-0 z-50 nav-blur px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 vsa-gradient rounded-lg flex items-center justify-center font-bold text-white">V</div>
            <span class="text-xl font-black tracking-tighter">VSA<span class="text-blue-500">STORE</span></span>
        </div>
        <i class="fa fa-bars text-xl"></i>
    </nav>

    <header class="px-6 py-12 text-center relative overflow-hidden">
        <div class="absolute inset-0 vsa-gradient opacity-10 blur-3xl -z-10"></div>
        <h2 class="text-4xl font-black mb-4 uppercase leading-tight">Kho IPA <br><span class="text-blue-500 underline">Mod Premium</span></h2>
        <p class="text-gray-400 text-sm mb-8">Hỗ trợ ESign, TrollStore, Scarlet - Cập nhật hàng ngày từ các Repo hàng đầu thế giới.</p>
        
        <div class="relative max-w-md mx-auto">
            <input type="text" id="searchInput" placeholder="Tìm kiếm App hoặc Game..." 
                   class="w-full bg-[#0d1117] border border-[#30363d] rounded-xl px-12 py-4 outline-none focus:border-blue-500 transition-all">
            <i class="fa fa-search absolute left-4 top-4.5 text-gray-500"></i>
        </div>
    </header>

    <main class="px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="appList">
        <?php foreach ($allApps as $app): ?>
            <div class="vsa-card p-5 flex items-center space-x-4 app-item">
                <img src="<?= $app['icon'] ?>" class="w-16 h-16 rounded-xl border border-gray-700 shadow-lg" onerror="this.src='https://apple.com/favicon.ico'">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-1">
                        <h3 class="font-bold text-sm truncate"><?= htmlspecialchars($app['name']) ?></h3>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="vsa-badge uppercase"><?= $app['ver'] ?></span>
                        <span class="text-[10px] text-gray-500 italic"><?= $app['source'] ?></span>
                    </div>
                    <div class="mt-3">
                        <a href="<?= $app['down'] ?>" download class="glow-button px-6 py-1.5 text-xs inline-block text-white">TẢI IPA</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </main>

    <footer class="mt-16 text-center text-gray-600 text-xs px-6">
        <p>© 2024 VSA STORE - Mọi quyền được bảo lưu</p>
        <p class="mt-2 text-blue-500">Giao diện tối ưu cho iPhone/iPad</p>
    </footer>

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
