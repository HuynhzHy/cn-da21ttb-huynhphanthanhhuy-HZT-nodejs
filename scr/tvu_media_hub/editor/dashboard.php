<?php
include_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$role = isset($_SESSION['role_name']) ? strtolower($_SESSION['role_name']) : '';
if (!isset($_SESSION['user_id']) || ($role !== 'super_admin' && $role !== 'admin')) {
    header("Location: ../index.php"); exit();
}

// Lấy các chỉ số thống kê (Ví dụ)
$count_prod = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products"))['total'];
$count_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE status = 'pending'"))['total'];
$count_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$count_video = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE youtube_link IS NOT NULL AND youtube_link != ''"))['total'];

include_once '../includes/header.php';
?>

<div class="container py-4">
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3"><i class="fa-solid fa-box-archive fs-4"></i></div>
                    <div><small class="d-block opacity-75">Sản phẩm</small><h3 class="mb-0 fw-bold"><?php echo $count_prod; ?></h3></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3"><i class="fa-solid fa-clock fs-4"></i></div>
                    <div><small class="d-block opacity-75">Chờ duyệt</small><h3 class="mb-0 fw-bold"><?php echo $count_pending; ?></h3></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3"><i class="fa-solid fa-users fs-4"></i></div>
                    <div><small class="d-block opacity-75">Sinh viên</small><h3 class="mb-0 fw-bold"><?php echo $count_users; ?></h3></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3"><i class="fa-solid fa-video fs-4"></i></div>
                    <div><small class="d-block opacity-75">Video Clip</small><h3 class="mb-0 fw-bold"><?php echo $count_video; ?></h3></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-primary border-5">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-warning"><i class="fa-solid fa-bolt me-2"></i>Lối tắt quản trị</h5>
                    <div class="list-group list-group-flush">
                        <a href="approve_posts.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-3 ps-0">
                            <span><i class="fa-solid fa-check-double text-primary me-3"></i>Phê duyệt sản phẩm mới</span>
                            <span class="badge bg-danger rounded-pill"><?php echo $count_pending; ?></span>
                        </a>
                        
                        <a href="manage_all_products.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-3 ps-0">
                            <span><i class="fa-solid fa-trash-can text-danger me-3"></i>Quản lý xóa bài đăng</span>
                            <i class="fa-solid fa-chevron-right small text-muted"></i>
                        </a>

                        <a href="../auth/logout.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 py-3 ps-0">
                            <span class="text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-3"></i>Đăng xuất hệ thống</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-light bg-opacity-50">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-dark">Ghi chú quản trị</h5>
                    <ul class="text-muted small">
                        <li class="mb-2">Kiểm tra kỹ nội dung và bản quyền trước khi nhấn duyệt.</li>
                        <li class="mb-2">Video từ Youtube sẽ được hiển thị với khung trình chiếu riêng.</li>
                        <li class="mb-2 text-danger fw-bold">Xóa bài đăng sẽ xóa vĩnh viễn tệp đính kèm trên máy chủ.</li>
                        <li class="mb-2">Liên hệ IT TVU nếu hệ thống gặp lỗi truy xuất dữ liệu.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>