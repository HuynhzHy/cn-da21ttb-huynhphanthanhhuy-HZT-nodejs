<?php
session_start();
require_once '../config/db.php';

// 1. BẢO MẬT: Kiểm tra quyền
$role_name = strtolower($_SESSION['role_name'] ?? '');
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] != 1 && $role_name !== 'super_admin' && $role_name !== 'super admin')) {
    echo "<script>alert('Bạn không có quyền truy cập trang này!'); window.location.href='../index.php';</script>";
    exit();
}

// ---------------------------------------------------------
// 2. LOGIC XỬ LÝ DỮ LIỆU (PHP)
// ---------------------------------------------------------

// A. Duyệt mật khẩu
if (isset($_GET['processed_id'])) {
    $rid = intval($_GET['processed_id']);
    mysqli_query($conn, "UPDATE password_requests SET status = 'processed' WHERE request_id = $rid");
    header("Location: admin.php#password_requests"); exit();
}

// B. Xử lý bài viết (Duyệt xóa & Trạng thái)
if (isset($_GET['confirm_delete'])) { // Admin đồng ý xóa thật
    $id = intval($_GET['confirm_delete']);
    mysqli_query($conn, "DELETE FROM products WHERE prod_id = $id AND status = 'cho_xoa'");
    header("Location: admin.php"); exit();
}

if (isset($_GET['reject_delete'])) { // Admin từ chối xóa, trả về đã duyệt
    $id = intval($_GET['reject_delete']);
    mysqli_query($conn, "UPDATE products SET status = 'da_duyet' WHERE prod_id = $id");
    header("Location: admin.php"); exit();
}

if (isset($_GET['delete_post'])) { // Xóa thẳng bài viết (từ Admin)
    $id = intval($_GET['delete_post']);
    mysqli_query($conn, "DELETE FROM products WHERE prod_id = $id");
    header("Location: admin.php"); exit();
}

if (isset($_GET['toggle_featured'])) { // Đánh dấu nổi bật
    $id = intval($_GET['toggle_featured']);
    $curr = intval($_GET['current']);
    $new = $curr ? 0 : 1;
    mysqli_query($conn, "UPDATE products SET is_featured = $new WHERE prod_id = $id");
    header("Location: admin.php"); exit();
}

// C. Xử lý chiến dịch
if (isset($_GET['delete_camp'])) {
    $id = intval($_GET['delete_camp']);
    mysqli_query($conn, "UPDATE products SET camp_id = NULL WHERE camp_id = $id");
    mysqli_query($conn, "DELETE FROM campaigns WHERE camp_id = $id");
    header("Location: admin.php"); exit();
}

// D. Xử lý Khóa/Mở khóa thành viên
if (isset($_POST['btn_lock_user'])) {
    $uid = intval($_POST['user_id_lock']);
    $duration = $_POST['duration']; 
    if ($uid != $_SESSION['user_id']) {
        $status_sql = ($duration == 'forever') ? "is_locked = 1, locked_until = NULL" : "is_locked = 1, locked_until = '" . date('Y-m-d H:i:s', strtotime("+$duration days")) . "'";
        mysqli_query($conn, "UPDATE users SET $status_sql WHERE user_id = $uid");
        echo "<script>alert('Đã khóa tài khoản!'); window.location.href='admin.php';</script>";
    }
}

if (isset($_GET['unlock_user'])) {
    $uid = intval($_GET['unlock_user']);
    mysqli_query($conn, "UPDATE users SET is_locked = 0, locked_until = NULL WHERE user_id = $uid");
    header("Location: admin.php"); exit();
}

// E. Xóa thành viên
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    if($id != $_SESSION['user_id']) {
        mysqli_query($conn, "UPDATE products SET user_id = NULL WHERE user_id = $id");
        mysqli_query($conn, "DELETE FROM users WHERE user_id = $id");
    }
    header("Location: admin.php"); exit();
}

include '../includes/header.php';

// --- BỘ CẤU HÌNH TRẠNG THÁI TIẾNG VIỆT ---
$status_map = [
    'da_duyet'  => ['class' => 'bg-success', 'text' => 'Đã duyệt'],
    'tu_choi'   => ['class' => 'bg-danger',  'text' => 'Bị từ chối'],
    'cho_xoa'   => ['class' => 'bg-dark',    'text' => 'Đang chờ xóa'],
    'cho_duyet' => ['class' => 'bg-warning text-dark', 'text' => 'Đang chờ duyệt']
];
?>

<style>
    :root { --tvu-blue: #0056b3; --tvu-gold: #ffc107; --tvu-green: #28a745; --tvu-red: #dc3545; }
    .admin-card-section { background: #fff; border-radius: 15px; margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e0e6ed; overflow: hidden; }
    .border-delete { border-top: 5px solid var(--tvu-red); }
    .border-posts { border-top: 5px solid var(--tvu-green); }
    .border-camps { border-top: 5px solid var(--tvu-blue); }
    .border-pass, .border-users { border-top: 5px solid var(--tvu-gold); }
    .section-header { padding: 1.25rem; background: #f8f9fa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .section-title { font-weight: 700; text-transform: uppercase; font-size: 0.85rem; margin: 0; }
    .btn-tvu-gold { background-color: var(--tvu-gold); color: #000; border: none; }
    .btn-tvu-gold:hover { background-color: #e5ad06; }
</style>

<div class="container py-5">
    <div class="bg-primary text-white p-4 rounded-4 mb-5 shadow-sm d-flex justify-content-between align-items-center">
        <div><h3 class="fw-bold mb-0">HỆ THỐNG QUẢN TRỊ TVU</h3><p class="mb-0 opacity-75">Media Hub Control Panel</p></div>
        <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold">Admin ID: #<?php echo $_SESSION['user_id']; ?></span>
    </div>

    <?php 
    $pending_del = mysqli_query($conn, "SELECT * FROM products WHERE status = 'cho_xoa' ORDER BY prod_id DESC");
    if(mysqli_num_rows($pending_del) > 0): 
    ?>
    <div class="admin-card-section border-delete animate__animated animate__shakeX">
        <div class="section-header bg-danger bg-opacity-10">
            <h5 class="section-title text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Yêu cầu xóa sản phẩm (Cần duyệt)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>ID</th><th>Tiêu đề bài viết</th><th>Hành động</th></tr></thead>
                <tbody>
                    <?php while ($pd = mysqli_fetch_assoc($pending_del)): ?>
                    <tr>
                        <td>#<?php echo $pd['prod_id']; ?></td>
                        <td><strong class="text-danger"><?php echo htmlspecialchars($pd['title']); ?></strong></td>
                        <td class="text-end pe-3">
                            <a href="?confirm_delete=<?php echo $pd['prod_id']; ?>" class="btn btn-sm btn-danger px-3 fw-bold" onclick="return confirm('XÓA VĨNH VIỄN bài này?')">ĐỒNG Ý XÓA</a>
                            <a href="?reject_delete=<?php echo $pd['prod_id']; ?>" class="btn btn-sm btn-outline-secondary px-3 ms-1">TỪ CHỐI XÓA</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="admin-card-section border-posts">
        <div class="section-header"><h5 class="section-title text-success"><i class="fa-solid fa-list-check me-2"></i>Tất cả bài viết</h5></div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>ID</th><th>Tiêu đề</th><th>Trạng thái</th><th class="text-end">Hành động</th></tr></thead>
            <tbody>
                <?php 
                // CHỈ HIỆN CÁC BÀI KHÁC TRẠNG THÁI CHO_XOA (Vì đã có bảng riêng ở trên)
                $prods = mysqli_query($conn, "SELECT * FROM products WHERE status != 'cho_xoa' ORDER BY prod_id DESC"); 
                while ($p = mysqli_fetch_assoc($prods)): 
                    $st = $p['status'];
                    $badge = $status_map[$st] ?? $status_map['cho_duyet'];
                ?>
                <tr>
                    <td>#<?php echo $p['prod_id']; ?></td>
                    <td><?php if ($p['is_featured'] ?? 0): ?><i class="fa-solid fa-star text-warning me-1"></i><?php endif; ?><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                    <td><span class="badge rounded-pill <?php echo $badge['class']; ?> px-3"><?php echo $badge['text']; ?></span></td>
                    <td class="text-end pe-3">
                        <div class="btn-group">
                            <a href="?toggle_featured=<?php echo $p['prod_id']; ?>&current=<?php echo $p['is_featured'] ?? 0; ?>" class="btn btn-sm <?php echo ($p['is_featured'] ?? 0) ? 'btn-warning' : 'btn-outline-secondary'; ?>"><i class="fa-star fa-solid"></i></a>
                            <a href="?delete_post=<?php echo $p['prod_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa bài viết này ngay lập tức?')"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table></div>
    </div>

    <div class="admin-card-section border-camps">
        <div class="section-header"><h5 class="section-title text-primary"><i class="fa-solid fa-bullhorn me-2"></i>Chiến dịch</h5></div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>ID</th><th>Tên chiến dịch</th><th class="text-end">Hành động</th></tr></thead>
            <tbody><?php $camps = mysqli_query($conn, "SELECT * FROM campaigns ORDER BY camp_id DESC"); while ($c = mysqli_fetch_assoc($camps)): ?>
                <tr>
                    <td>#<?php echo $c['camp_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($c['camp_name']); ?></strong></td>
                    <td class="text-end pe-3"><a href="?delete_camp=<?php echo $c['camp_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa chiến dịch?')"><i class="fa-solid fa-trash"></i></a></td>
                </tr>
            <?php endwhile; ?></tbody>
        </table></div>
    </div>

    <div id="password_requests" class="admin-card-section border-pass">
        <div class="section-header"><h5 class="section-title text-warning"><i class="fa-solid fa-key me-2"></i>Cấp lại mật khẩu</h5></div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <tbody><?php $reqs = mysqli_query($conn, "SELECT * FROM password_requests WHERE status = 'pending' ORDER BY created_at DESC"); if(mysqli_num_rows($reqs) > 0): while ($r = mysqli_fetch_assoc($reqs)): ?>
                <tr>
                    <td class="ps-3"><strong><?php echo $r['email']; ?></strong> <br><small class="text-muted"><?php echo date('H:i d/m', strtotime($r['created_at'])); ?></small></td>
                    <td class="text-end pe-3">
                        <a href="mailto:<?php echo $r['email']; ?>" class="btn btn-sm btn-outline-primary"><i class="fa-envelope fa-solid"></i></a>
                        <a href="?processed_id=<?php echo $r['request_id']; ?>" class="btn btn-sm btn-tvu-gold ms-1"><i class="fa-check fa-solid"></i> Xong</a>
                    </td>
                </tr>
            <?php endwhile; else: ?><tr><td class="text-center py-4 text-muted">Không có yêu cầu.</td></tr><?php endif; ?></tbody>
        </table></div>
    </div>

    <div class="admin-card-section border-users">
        <div class="section-header"><h5 class="section-title text-warning"><i class="fa-solid fa-users me-2"></i>Thành viên hệ thống</h5></div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>ID</th><th>Họ tên</th><th>Trạng thái</th><th class="text-end">Hành động</th></tr></thead>
            <tbody><?php $users = mysqli_query($conn, "SELECT * FROM users ORDER BY user_id DESC"); while ($u = mysqli_fetch_assoc($users)): $locked = ($u['is_locked'] ?? 0); ?>
                <tr>
                    <td>#<?php echo $u['user_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($u['full_name']); ?></strong><br><small><?php echo $u['email']; ?></small></td>
                    <td><?php if($locked): ?><span class="badge bg-warning text-dark">Đã khóa</span><?php else: ?><span class="badge bg-success">Đang hoạt động</span><?php endif; ?></td>
                    <td class="text-end pe-3">
                        <?php if($u['user_id'] != $_SESSION['user_id']): ?>
                            <div class="btn-group">
                                <?php if(!$locked): ?>
                                    <button onclick="openLockModal(<?php echo $u['user_id']; ?>, '<?php echo addslashes($u['full_name']); ?>')" class="btn btn-sm btn-tvu-gold" title="Khóa tài khoản"><i class="fa-lock fa-solid"></i></button>
                                <?php else: ?>
                                    <a href="?unlock_user=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-success text-white" title="Mở khóa"><i class="fa-unlock fa-solid"></i></a>
                                <?php endif; ?>
                                <a href="?delete_user=<?php echo $u['user_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa vĩnh viễn thành viên này?')"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        <?php else: ?>
                            <span class="badge bg-light text-dark border">Bạn (Admin)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?></tbody>
        </table></div>
    </div>
</div>

<div class="modal fade" id="lockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><form action="" method="POST" class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-warning border-0"><h5 class="modal-title fw-bold text-dark">KHÓA TÀI KHOẢN</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body p-4 text-center">
            <input type="hidden" name="user_id_lock" id="lock_uid">
            <p class="mb-3">Bạn đang thực hiện khóa tài khoản:<br><strong id="lock_uname" class="fs-5 text-primary"></strong></p>
            <select name="duration" class="form-select mb-3 border-2">
                <option value="3">Khóa 3 ngày</option>
                <option value="7">Khóa 7 ngày</option>
                <option value="30">Khóa 30 ngày</option>
                <option value="forever">Khóa vĩnh viễn</option>
            </select>
            <button type="submit" name="btn_lock_user" class="btn btn-dark w-100 fw-bold py-2">XÁC NHẬN KHÓA</button>
        </div>
    </form></div>
</div>

<script>
function openLockModal(id, name) {
    document.getElementById('lock_uid').value = id;
    document.getElementById('lock_uname').innerText = name;
    new bootstrap.Modal(document.getElementById('lockModal')).show();
}
</script>

<?php include_once '../includes/footer.php'; ?>