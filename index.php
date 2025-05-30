<?php
$API_KEY = 'AIzaSyDw3IkEnA4UbxN5z-BeZsJmlcMQzTnYpkc';

// Variabel untuk template
$channelData = null;
$error = null;
$channel_input_value = "";
$error_message_display = "";

// Data channel untuk template
$channel_avatar_url = "";
$channel_title = "";
$channel_description = "";
$channel_join_date = "";
$channel_id = "";
$channel_country = "";
$channel_custom_url = "";

// Statistik channel
$subscribers_formatted = "";
$subscribers_total = "";
$views_formatted = "";
$views_total = "";
$videos_formatted = "";
$videos_total = "";
$avg_views_formatted = "";

// Status tampilan
$show_channel_data = false;
$show_instructions = true;

// Cek apakah fungsi belum dideklarasikan sebelumnya
if (!function_exists('getChannelStats')) {
    function getChannelStats($channelName, $apiKey) {
        // Pertama, cari channel berdasarkan nama
        $searchUrl = "https://www.googleapis.com/youtube/v3/search?part=id&type=channel&q=" . urlencode($channelName) . "&key=" . $apiKey;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $searchUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $searchResponse = curl_exec($ch);
        curl_close($ch);
        
        $searchData = json_decode($searchResponse, true);
        
        if (empty($searchData['items'])) {
            return null;
        }
        
        $channelId = $searchData['items'][0]['id']['channelId'];
        
        // Ambil statistik channel
        $statsUrl = "https://www.googleapis.com/youtube/v3/channels?part=statistics,snippet,brandingSettings&id=" . $channelId . "&key=" . $apiKey;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $statsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $statsResponse = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($statsResponse, true);
    }
}
if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return number_format($number);
    }
}
// Proses form jika ada request POST
if (isset($_POST['channel_name']) && !empty($_POST['channel_name'])) {
    $channelName = trim($_POST['channel_name']);
    $channel_input_value = htmlspecialchars($channelName);
    
    $channelData = getChannelStats($channelName, $API_KEY);
    
    if (!$channelData || empty($channelData['items'])) {
        $error = "Channel tidak ditemukan. Pastikan nama channel benar.";
        $error_message_display = $error;
        $show_instructions = true;
        $show_channel_data = false;
    } else {
        // Process channel data
        $channel = $channelData['items'][0];
        $stats = $channel['statistics'];
        $snippet = $channel['snippet'];
        
        // Set channel info
        $channel_title = htmlspecialchars($snippet['title']);
        $channel_id = $channel['id'];
        
        // Process description
        $description = $snippet['description'];
        $channel_description = htmlspecialchars(strlen($description) > 150 ? substr($description, 0, 150) . '...' : $description);
        
        // Format join date
        $channel_join_date = date('d F Y', strtotime($snippet['publishedAt']));
        
        // Set country and custom URL
        $channel_country = isset($snippet['country']) ? $snippet['country'] : 'Tidak tersedia';
        $channel_custom_url = isset($snippet['customUrl']) ? '@' . $snippet['customUrl'] : 'Tidak tersedia';
        
        // Process avatar URL with fallback
        if (isset($snippet['thumbnails']['high']['url'])) {
            $channel_avatar_url = $snippet['thumbnails']['high']['url'];
        } elseif (isset($snippet['thumbnails']['medium']['url'])) {
            $channel_avatar_url = $snippet['thumbnails']['medium']['url'];
        } elseif (isset($snippet['thumbnails']['default']['url'])) {
            $channel_avatar_url = $snippet['thumbnails']['default']['url'];
        } else {
            $channel_avatar_url = 'https://via.placeholder.com/100x100/ff6b6b/ffffff?text=' . urlencode(substr($snippet['title'], 0, 2));
        }
        // Format statistics
        $subscribers_formatted = formatNumber($stats['subscriberCount']);
        $subscribers_total = number_format($stats['subscriberCount']);
        $views_formatted = formatNumber($stats['viewCount']);
        $views_total = number_format($stats['viewCount']);
        $videos_formatted = formatNumber($stats['videoCount']);
        $videos_total = number_format($stats['videoCount']);
        
        // Calculate average views per video
        $avg_views = $stats['videoCount'] > 0 ? $stats['viewCount'] / $stats['videoCount'] : 0;
        $avg_views_formatted = formatNumber($avg_views);
        
        // Set display flags
        $show_channel_data = true;
        $show_instructions = false;
        $error = null;
    }

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StatiTube</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="header-container">
                    <h1 class="display-4 mb-4">
                        <i class="fab fa-youtube text-danger"></i>
                        StatiTube
                    </h1>
                    <p class="lead">Masukkan nama channel YouTube untuk melihat statistik lengkapnya</p>
                </div>
            </div>
        </div>
        <!-- Search Form -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 col-lg-6">
                <div class="search-container">
                    <form method="POST" class="d-flex gap-2">
                        <input type="text" 
                               name="channel_name" 
                               class="form-control form-control-lg" 
                               placeholder="Masukkan nama channel YouTube..." 
                               value="<?= $channel_input_value ?>"
                               required>
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= $error_message_display ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <!-- Channel Statistics -->
        <?php if ($show_channel_data): ?>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <!-- Channel Info -->
                <div class="channel-header mb-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="<?= $channel_avatar_url ?>" 
                                 alt="Channel Avatar" 
                                 class="channel-avatar"
                                 onerror="this.src='<?= $avatar_fallback_url ?>'">
                        </div>
                        <div class="col">
                            <h2 class="channel-title"><?= $channel_title ?></h2>
                            <p class="channel-description"><?= $channel_description ?></p>
                            <p class="channel-date">
                                <i class="fas fa-calendar-alt"></i>
                                Bergabung: <?= $channel_join_date ?>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Statistics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card subscribers">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $subscribers_formatted ?></h3>
                                <p>Subscribers</p>
                                <small><?= $subscribers_total ?> total</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card views">
                            <div class="stat-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $views_formatted ?></h3>
                                <p>Total Views</p>
                                <small><?= $views_total ?> views</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card videos">
                            <div class="stat-icon">
                                <i class="fas fa-play"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $videos_formatted ?></h3>
                                <p>Total Videos</p>
                                <small><?= $videos_total ?> videos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-card engagement">
                            <div class="stat-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $avg_views_formatted ?></h3>
                                <p>Avg Views/Video</p>
                                <small>Per video</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Additional Info -->
                <div class="row">
                    <div class="col-12">
                        <div class="info-card">
                            <h4><i class="fas fa-info-circle"></i> Informasi Channel</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nama Channel:</strong> <?= $channel_title ?></p>
                                    <p><strong>Country:</strong> <?= $channel_country ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Channel ID:</strong> <code><?= $channel_id ?></code></p>
                                    <p><strong>Custom URL:</strong> <?= $channel_custom_url ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <!-- Instructions -->
        <?php if ($show_instructions): ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="instructions-card">
                    <h4><i class="fas fa-question-circle"></i> Cara Menggunakan</h4>
                    <ol>
                        <li>Masukkan nama channel YouTube yang ingin Anda lihat statistiknya</li>
                        <li>Klik tombol pencarian untuk mengambil data</li>
                        <li>Statistik akan ditampilkan meliputi jumlah subscriber, total views, dan jumlah video</li>
                    </ol>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>