<?php
$repos = [
    "AppTesters" => "https://repository.apptesters.org/repo.json",
    "ChungChi365" => "https://repo.chungchi365.com/repo.json",
    "Cypwn" => "https://ipa.cypwn.xyz/cypwn.json",
    "FastSign" => "https://fastsign.dev/repo.json",
    "NabzClan" => "https://appstore.nabzclan.vip/repos/esign.php"
];

$allApps = [];

function getRepoData($url) {
    $ctx = stream_context_create(['http' => ['timeout' => 8]]);
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
                'name'   => $app['name'] ?? $app['title'] ?? 'Unknown App',
                'icon'   => $app['icon'] ?? $app['iconURL'] ?? 'https://apple.com/favicon.ico',
                'down'   => $app['down'] ?? $app['downloadURL'] ?? $app['url'] ?? '#',
                'ver'    => $app['version'] ?? 'Latest'
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>IPA Ultimate Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f2f2f7; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .ios-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border-radius: 20px; transition: all 0.3s ease; }
        .ios-card:active { transform: scale(0.96); background: rgba(235, 235, 245, 0.9); }
        .btn-get { background: #f0f0f7; color: #007aff; border-radius: 20px; font-weight: 800; padding: 6px 18px; font-size: 14px; text-transform: uppercase; }
        .sticky-search { position: sticky; top: 0; z-index: 50; background: rgba(242, 242, 247, 0.9); backdrop-filter: blur(20px); border-bottom: 0.5px solid #d1d1d6; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="pb-20">

    <div class="sticky-search px-5 pt-8 pb-4">
        <div class="flex justify-between items-end mb-4">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest"><?php echo date('d F'); ?></p>
                <h1 class="text-3xl font-extrabold text-black">Cho bạn</h1>
            </div>
            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                <i class="fa fa-user"></i>
            </div>
        </div>
        
        <div class="relative group">
            <i class="fa fa-search absolute left-4 top-3.5 text-gray-400"></i>
            <input type="text" id="searchInput" placeholder="App, Trò chơi, Nguồn..." 
                   class="w-full bg-gray-200/60 pl-11 pr-4 py-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-400 transition-all">
        </div>
    </div>

    <div class="px-5 mt-6 space-y-4" id="appList">
        <?php foreach ($allApps as $app): ?>
            <div class="ios-card p-4 flex items-center justify-between shadow-sm border border-white/50 app-item">
                <div class="flex items-center space-x-4 flex-1 min-w-0">
                    <img src="<?= $app['icon'] ?>" class="w-16 h-16 rounded-[22%] object-cover shadow-sm border border-gray-100" 
                         onerror="this.src='https://apple.com/favicon.ico'">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 text-[15px] truncate"><?= htmlspecialchars($app['name']) ?></h3>
                        <p class="text-[12px] text-gray-500"><?= $app['source'] ?> • v<?= $app['ver'] ?></p>
                        <div class="flex items-center mt-1">
                            <i class="fa fa-star text-blue-500 text-[10px]"></i>
                            <i class="fa fa-star text-blue-500 text-[10px]"></i>
                            <i class="fa fa-star text-blue-500 text-[10px]"></i>
                            <span class="text-[10px] text-gray-400 ml-1">4.9</span>
                        </div>
                    </div>
                </div>
                <a href="<?= $app['down'] ?>" download class="btn-get flex-shrink-0 ml-3">Nhận</a>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Tìm kiếm thời gian thực (Client-side)
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const apps = document.querySelectorAll('.app-item');
            
            apps.forEach(app => {
                const name = app.querySelector('h3').innerText.toLowerCase();
                const source = app.querySelector('p').innerText.toLowerCase();
                if (name.includes(term) || source.includes(term)) {
                    app.style.display = 'flex';
                } else {
                    app.style.display = 'none';
                }
            });
        });

        // Hiệu ứng khi nhấn nút Nhận
        document.querySelectorAll('.btn-get').forEach(btn => {
            btn.addEventListener('click', function() {
                this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
                setTimeout(() => { this.innerText = 'Xong'; }, 3000);
            });
        });
    </script>
</body>
</html>
