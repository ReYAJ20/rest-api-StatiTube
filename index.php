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