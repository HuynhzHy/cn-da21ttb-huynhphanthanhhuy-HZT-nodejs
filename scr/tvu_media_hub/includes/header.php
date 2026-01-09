<?php
// Header này cần biến $BASE_URL từ db.php. 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../config/db.php'; 

$isLoggedIn = isset($_SESSION['user_id']);
$role = $isLoggedIn ? $_SESSION['role_name'] : 'guest';
$displayName = $_SESSION['full_name'] ?? ($_SESSION['username'] ?? 'Thành viên');

if (!isset($BASE_URL)) {
    $BASE_URL = '/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', dirname(__DIR__))) . '/';
    $BASE_URL = str_replace('//', '/', $BASE_URL); 
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TVU Media Hub</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --tvu-blue: #0056b3;
        --tvu-gold: #ffc107;
        --tvu-bg-light: #e6f7ff; 
    }
    
    body { font-family: 'Roboto', sans-serif; background-color: #ffffff; }

    .navbar { 
        background-color: var(--tvu-bg-light) !important; 
        box-shadow: 0 2px 10px rgba(0, 86, 179, 0.1); 
        border-bottom: 2px solid #bae7ff; 
        padding: 10px 0; 
    }

    .navbar-brand { 
        font-family: 'Montserrat', sans-serif; 
        font-weight: 700; 
        color: var(--tvu-blue) !important; 
    }
    .navbar-brand span { color: var(--tvu-gold); }

    .nav-link { 
        font-weight: 600; 
        color: #004085 !important; 
        transition: 0.3s; 
    }
    
    .nav-link:hover { 
        color: var(--tvu-blue) !important; 
        background-color: rgba(255, 255, 255, 0.5); 
        border-radius: 8px;
    }

    .user-dropdown { 
        background: #ffffff; 
        border: 1px solid #bae7ff;
        color: #004085 !important;
        border-radius: 12px;
        padding: 5px 15px;
    }

    .dropdown-item:hover {
        background-color: #f0f8ff;
        color: var(--tvu-blue);
    }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $BASE_URL; ?>index.php">
            <i class="fa-solid fa-photo-film me-2"></i>TVU Media<span>Hub</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $BASE_URL; ?>index.php">
                        <i class="fa-solid fa-house"></i> Trang chủ
                    </a>
                </li>
                
                <?php if ($role === 'admin' || $role === 'super_admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $BASE_URL; ?>editor/manage_campaigns.php">
                            <i class="fa-solid fa-bullhorn"></i> Chiến dịch
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $BASE_URL; ?>index2.php?filter=featured">
                        <i class="fa-solid fa-star"></i> Nổi bật
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if ($isLoggedIn): ?>
                    
                    <?php if ($role === 'user'): ?>
                        <a href="<?php echo $BASE_URL; ?>modules/post_product.php" class="btn btn-primary rounded-pill d-none d-lg-block">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Gửi bài
                        </a>
                    <?php endif; ?>

                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown d-flex align-items-center" 
                           href="#" 
                           role="button" 
                           id="dropdownUser" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            <i class="fa-solid fa-circle-user fa-lg text-primary"></i>
                            <span class="ms-1 text-dark"><?php echo htmlspecialchars($displayName); ?></span>
                        </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-2" aria-labelledby="dropdownUser" style="border-radius: 15px; min-width: 220px;">
                            
                            <?php if ($role === 'super_admin'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?php echo $BASE_URL; ?>admin/statistics.php">
                                        <i class="fa-solid fa-chart-line me-2 text-primary"></i>Quản trị hệ thống
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($role === 'admin' || $role === 'super_admin'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="<?php echo $BASE_URL; ?>admin/approve_products.php">
                                        <i class="fa-solid fa-check-to-slot me-2 text-success"></i>Duyệt bài đăng
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if ($role === 'user'): ?>
                                <li><a class="dropdown-item py-2" href="<?php echo $BASE_URL; ?>modules/my_products.php"><i class="fa-solid fa-folder-open me-2 text-warning"></i>Sản phẩm của tôi</a></li>
                            <?php endif; ?>

                            <li><a class="dropdown-item py-2" href="<?php echo $BASE_URL; ?>profile.php"><i class="fa-solid fa-user-gear me-2 text-info"></i>Hồ sơ cá nhân</a></li>

                            <li>
                                <a class="dropdown-item py-2" href="<?php echo $BASE_URL; ?>activity_log.php">
                                    <i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i>Nhật ký hoạt động
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger fw-bold" href="<?php echo $BASE_URL; ?>auth/logout.php"><i class="fa-solid fa-power-off me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>

                <?php else: ?>
                    <a href="<?php echo $BASE_URL; ?>auth/login.php" class="text-decoration-none text-dark fw-bold me-2">Đăng nhập</a>
                    <a href="<?php echo $BASE_URL; ?>auth/register.php" class="btn btn-outline-primary rounded-pill px-4 fw-bold">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
