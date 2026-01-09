<?php
include_once 'config/db.php';
include_once 'includes/header.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Truy vấn lấy lịch sử hoạt động của người dùng này
// Sắp xếp theo thời gian mới nhất lên đầu, giới hạn 20 hoạt động
$sql = "SELECT * FROM activity_logs 
        WHERE user_id = $user_id 
        ORDER BY created_at DESC 
        LIMIT 20";
$result = mysqli_query($conn, $sql);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Nhật ký hoạt động
                    </h3>
                    <p class="text-muted mb-0 small">Theo dõi lịch sử tương tác của bạn với hệ thống TVU Media Hub</p>
                </div>
                <a href="index.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fa-solid fa-house me-1"></i> Về trang chủ
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-secondary small fw-bold">
                                <th class="ps-4 py-3" style="width: 15%;">THỜI GIAN</th>
                                <th style="width: 20%;">HÀNH ĐỘNG</th>
                                <th class="pe-4">CHI TIẾT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark small"><?php echo date('H:i', strtotime($row['created_at'])); ?></span>
                                                <span class="text-muted" style="font-size: 0.7rem;"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                $action = $row['action'];
                                                $badge_color = 'bg-secondary'; // Mặc định màu xám
                                                $icon = 'fa-circle-dot';

                                                // Phân loại màu sắc theo từ khóa hành động
                                                if (strpos($action, 'Đăng nhập') !== false) {
                                                    $badge_color = 'bg-primary';
                                                    $icon = 'fa-right-to-bracket';
                                                } elseif (strpos($action, 'Mật khẩu') !== false) {
                                                    $badge_color = 'bg-warning text-dark';
                                                    $icon = 'fa-key';
                                                } elseif (strpos($action, 'Đăng bài') !== false || strpos($action, 'sản phẩm') !== false) {
                                                    $badge_color = 'bg-success';
                                                    $icon = 'fa-cloud-arrow-up';
                                                } elseif (strpos($action, 'Tải') !== false) {
                                                    $badge_color = 'bg-info text-white';
                                                    $icon = 'fa-download';
                                                }
                                            ?>
                                            <span class="badge <?php echo $badge_color; ?> rounded-pill px-3 py-2 fw-semibold shadow-xs" style="font-size: 0.75rem;">
                                                <i class="fa-solid <?php echo $icon; ?> me-1"></i> <?php echo $action; ?>
                                            </span>
                                        </td>
                                        <td class="pe-4">
                                            <p class="mb-0 text-dark small" style="line-height: 1.5;">
                                                <?php echo htmlspecialchars($row['details']); ?>
                                            </p>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="opacity-50 mb-3">
                                            <i class="fa-solid fa-folder-open fa-3x text-muted"></i>
                                        </div>
                                        <p class="text-muted">Chưa có dữ liệu hoạt động nào được ghi lại.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 p-3 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25">
                <p class="small text-primary mb-0">
                    <i class="fa-solid fa-shield-halved me-2"></i>
                    <strong>Lưu ý bảo mật:</strong> Nếu bạn thấy các hoạt động lạ (đăng nhập từ nơi khác hoặc đổi mật khẩu không phải do bạn), vui lòng liên hệ Admin ngay lập tức.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Bo góc cho bảng và Card */
    .rounded-4 { border-radius: 1rem !important; }
    
    /* Hiệu ứng khi rê chuột vào dòng trong bảng */
    .table-hover tbody tr:hover {
        background-color: #f8fbff;
        transition: background-color 0.2s ease;
    }

    /* Tinh chỉnh Badge */
    .badge {
        letter-spacing: 0.3px;
    }

    /* Font chữ cho chi tiết nhật ký */
    .table td {
        vertical-align: middle;
    }
</style>

<?php include_once 'includes/footer.php'; ?>