<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../config/db.php';
include_once '../includes/header.php';

/**
 * 1. KIỂM TRA QUYỀN TRUY CẬP 
 */
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'super_admin') {
    echo "<div class='container py-5 text-center'>
            <div class='alert alert-danger shadow rounded-4 p-5'>
                <i class='fa-solid fa-lock fa-4x mb-4 text-danger'></i>
                <h2 class='fw-bold'>TRUY CẬP BỊ TỪ CHỐI</h2>
                <p class='lead text-muted'>Khu vực này chỉ dành riêng cho Quản trị viên hệ thống.</p>
                <hr class='my-4'>
                <p>Vai trò hiện tại của bạn: <span class='badge bg-secondary px-3'>" . ($_SESSION['role_name'] ?? 'Khách') . "</span></p>
                <a href='../index.php' class='btn btn-primary btn-lg rounded-pill px-5 mt-3 shadow-sm'>
                    <i class='fa-solid fa-house me-2'></i>Về trang chủ
                </a>
            </div>
          </div>";
    include_once '../includes/footer.php';
    exit();
}

/**
 * 2. TRUY VẤN DỮ LIỆU THỐNG KÊ (Sửa logic đếm để hiện thông báo Badge chuẩn)
 */
// Tổng sản phẩm
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];

// Đếm số bài CHỜ DUYỆT (Hiện Badge thông báo)
$pending_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE status IN ('pending', 'cho_duyet')"))['total'];

// Đếm số yêu cầu XÓA BÀI (Hiện Badge thông báo)
$delete_requests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE status IN ('pending_delete', 'cho_xoa')"))['total'];

// Tổng người dùng (vai trò user)
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM users u 
    JOIN roles r ON u.role_id = r.role_id 
    WHERE r.role_name = 'user'"))['total'];

// Tổng video clip
$total_videos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE youtube_link IS NOT NULL AND youtube_link != ''"))['total'];

// Đếm yêu cầu ĐỔI MẬT KHẨU (Hiện Badge thông báo)
$total_pw_requests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE reset_status = 1"))['total'];
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-0">BẢNG ĐIỀU KHIỂN QUẢN TRỊ</h3>
            <p class="text-muted small mb-0">Hệ thống TVU Media Hub - Thống kê dữ liệu thời gian thực</p>
        </div>
        <div class="text-end">
            <span class="badge bg-warning text-dark border border-warning px-3 py-2 rounded-pill shadow-sm">
                <i class="fa-solid fa-user-shield me-2"></i>Super Admin: <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            </span>
        </div>
    </div>
    
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 rounded-circle p-3 me-3">
                        <i class="fa-solid fa-box fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 opacity-75 small">Sản phẩm</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $total_products; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-dark bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="fa-solid fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 opacity-75 small">Chờ duyệt</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $pending_products; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 rounded-circle p-3 me-3">
                        <i class="fa-solid fa-users fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 opacity-75 small">Người Dùng</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $total_users; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white p-3 h-100" style="background-color: #4ac5eaff;"> 
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-white bg-opacity-25 rounded-circle p-3 me-3">
                        <i class="fa-solid fa-film fa-2x"></i> 
                    </div>
                    <div>
                        <h6 class="mb-0 opacity-75 small">Video Clip</h6>
                        <h2 class="mb-0 fw-bold"><?php echo $total_videos; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 border-start border-primary border-4 h-100">
                <h5 class="fw-bold mb-4 d-flex align-items-center">
                    <i class="fa-solid fa-bolt-lightning text-warning me-2"></i> Lối tắt quản trị
                </h5>
                <div class="list-group list-group-flush">
                    <a href="approve_products.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-2 py-3 border-bottom border-light rounded-3 bg-hover-light">
                        <span><i class="fa-solid fa-check-double me-3 text-primary"></i> Phê duyệt sản phẩm mới</span>
                        <span class="badge <?php echo ($pending_products > 0) ? 'bg-danger' : 'bg-secondary'; ?> rounded-pill px-3 shadow-sm">
                            <?php echo $pending_products; ?>
                        </span>
                    </a>
                    
                    <a href="admin.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-2 py-3 border-bottom border-light rounded-3 bg-hover-light">
                        <span><i class="fa-solid fa-trash-can me-3 text-danger"></i> Yêu cầu xóa bài viết</span>
                        <span class="badge <?php echo ($delete_requests > 0) ? 'bg-danger' : 'bg-secondary'; ?> rounded-pill px-3 shadow-sm">
                            <?php echo $delete_requests; ?>
                        </span>
                    </a>

                    <a href="approve_resets.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-2 py-3 border-bottom border-light rounded-3 bg-hover-light">
                        <span><i class="fa-solid fa-key me-3 text-warning"></i> Yêu cầu đổi mật khẩu</span>
                        <span class="badge <?php echo ($total_pw_requests > 0) ? 'bg-danger' : 'bg-secondary'; ?> rounded-pill px-3 shadow-sm">
                            <?php echo $total_pw_requests; ?>
                        </span>
                    </a>

                    <a href="admin.php" class="list-group-item list-group-item-action px-2 py-3 rounded-3 bg-hover-light">
                        <i class="fa-solid fa-users-gear me-3 text-success"></i> Quản lý người dùng
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-light-subtle border">
                <h5 class="fw-bold mb-3 text-dark">Ghi chú quản trị</h5>
                <ul class="text-muted small lh-lg">
                    <li><i class="fa-solid fa-circle-info me-2 text-primary"></i>Kiểm tra kỹ nội dung bài viết trước khi nhấn duyệt.</li>
                    <li><i class="fa-solid fa-circle-info me-2 text-primary"></i>Hệ thống tự động đồng bộ Video từ link Youtube.</li>
                    <li><i class="fa-solid fa-circle-info me-2 text-primary"></i>Yêu cầu xóa bài cần được Admin xác nhận mới mất hoàn toàn.</li>
                    <li><i class="fa-solid fa-circle-info me-2 text-primary"></i>Mọi thao tác xóa dữ liệu đều không thể khôi phục.</li>
                    <li><i class="fa-solid fa-circle-info me-2 text-primary"></i>Yêu cầu đổi mật khẩu chỉ thực hiện cho SV chính chủ.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1.25rem !important; }
    .bg-hover-light:hover { background-color: #f8f9fa; transition: 0.3s ease; }
    .list-group-item { transition: all 0.2s ease; border: none !important; margin-bottom: 5px; }
    .list-group-item:hover { transform: translateX(5px); }
    .bg-warning { background-color: #ffc107 !important; color: #000 !important; font-weight: bold; }
    /* Hiệu ứng nhấp nháy cho thông báo quan trọng */
    .bg-danger { animation: pulse 2s infinite; }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>

<?php include_once '../includes/footer.php'; ?>