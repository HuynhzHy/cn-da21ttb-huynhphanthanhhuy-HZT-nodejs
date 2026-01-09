<?php 
include_once '../config/db.php'; 
// Đảm bảo session đã chạy để kiểm tra quyền
if (session_status() === PHP_SESSION_NONE) session_start();

// 0. KIỂM TRA QUYỀN: Chỉ cho phép Admin vào trang này
if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'super_admin') {
    header("Location: ../index.php");
    exit();
}

$msg = "";

// 1. Xử lý khi Admin bấm nút Duyệt hoặc Từ chối
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        mysqli_query($conn, "UPDATE users SET reset_status = 2 WHERE user_id = $id");
        $msg = "Đã phê duyệt cho phép tài khoản đổi mật khẩu.";
    } elseif ($action == 'reject') {
        mysqli_query($conn, "UPDATE users SET reset_status = 3 WHERE user_id = $id");
        $msg = "Đã từ chối yêu cầu của tài khoản này.";
    } elseif ($action == 'reset_to_normal') {
        mysqli_query($conn, "UPDATE users SET reset_status = 0 WHERE user_id = $id");
        $msg = "Đã đưa trạng thái tài khoản về bình thường.";
    }
    // Chuyển hướng về trang cũ để mất các tham số trên URL
    header("Location: approve_resets.php?status=success");
    exit();
}

// 2. Lấy danh sách yêu cầu (Chỉ lấy những ai có status > 0)
$sql = "SELECT user_id, username, full_name, email, reset_status FROM users WHERE reset_status > 0 ORDER BY reset_status ASC";
$result = mysqli_query($conn, $sql);
?>

<?php include_once '../includes/header.php'; ?>

<div class="container mt-5" style="min-height: 70vh;">
    <?php if(isset($_GET['status'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 15px;">
            <i class="fa-solid fa-circle-check me-2"></i> Thao tác xử lý yêu cầu thành công!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary py-3">
            <h5 class="fw-bold mb-0 text-white"><i class="fa-solid fa-user-shield me-2"></i>DANH SÁCH PHÊ DUYỆT QUÊN MẬT KHẨU</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Tài khoản thành viên</th>
                            <th>Email liên kết</th>
                            <th>Trạng thái hiện tại</th>
                            <th class="text-end pe-4">Thao tác xử lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3 bg-soft-primary text-primary">
                                                <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo $row['full_name']; ?></div>
                                                <small class="text-muted">ID: #<?php echo $row['user_id']; ?> | @<?php echo $row['username']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-secondary"><?php echo $row['email']; ?></td>
                                    <td>
                                        <?php if ($row['reset_status'] == 1): ?>
                                            <span class="badge rounded-pill bg-warning text-dark px-3"><i class="fa-solid fa-spinner fa-spin me-1"></i> Đang chờ duyệt</span>
                                        <?php elseif ($row['reset_status'] == 2): ?>
                                            <span class="badge rounded-pill bg-success px-3"><i class="fa-solid fa-check-double me-1"></i> Đã duyệt cho đổi</span>
                                        <?php elseif ($row['reset_status'] == 3): ?>
                                            <span class="badge rounded-pill bg-danger px-3"><i class="fa-solid fa-ban me-1"></i> Đã từ chối</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($row['reset_status'] == 1): ?>
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                                <a href="?action=approve&id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-success px-3">Duyệt</a>
                                                <a href="?action=reject&id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-danger px-3">Từ chối</a>
                                            </div>
                                        <?php else: ?>
                                            <a href="?action=reset_to_normal&id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                <i class="fa-solid fa-rotate-left me-1"></i> Hoàn tác
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="opacity-25 mb-3">
                                    <p class="text-muted mb-0">Hiện tại không có yêu cầu nào đang chờ xử lý.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 1.1rem;
    }
    .bg-soft-primary { background-color: #e7f1ff; }
    .table thead th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .rounded-4 { border-radius: 1rem !important; }
</style>

<?php include_once '../includes/footer.php'; ?>