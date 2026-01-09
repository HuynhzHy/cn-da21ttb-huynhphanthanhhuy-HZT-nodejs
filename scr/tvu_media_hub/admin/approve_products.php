<?php
include_once '../config/db.php';
include_once '../includes/header.php';

// 1. KIỂM TRA QUYỀN
$role = isset($_SESSION['role_name']) ? strtolower(trim($_SESSION['role_name'])) : '';
$allowed_roles = ['admin', 'super admin', 'super_admin'];

if (!isset($_SESSION['user_id']) || !in_array($role, $allowed_roles)) {
    echo "<div class='container mt-5'><div class='alert alert-danger shadow-sm'>
            <i class='fa-solid fa-triangle-exclamation me-2'></i>
            Bạn không có quyền thực hiện chức năng này!
          </div></div>";
    include_once '../includes/footer.php';
    exit();
}

// --- CẤU HÌNH MAPPING TRẠNG THÁI ---
$status_map = [
    'cho_duyet' => ['class' => 'bg-warning text-dark', 'text' => 'Chờ duyệt'],
    'pending'   => ['class' => 'bg-warning text-dark', 'text' => 'Chờ duyệt'], // Hỗ trợ cả tên cũ
    'da_duyet'  => ['class' => 'bg-success', 'text' => 'Đã duyệt'],
    'approved'  => ['class' => 'bg-success', 'text' => 'Đã duyệt'],
    'tu_choi'   => ['class' => 'bg-danger',  'text' => 'Từ chối'],
    'rejected'  => ['class' => 'bg-danger',  'text' => 'Từ chối']
];

// 2. XỬ LÝ DUYỆT BÀI
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    // Chuyển thành 'da_duyet' (Chuẩn tiếng Việt mới)
    mysqli_query($conn, "UPDATE products SET status = 'da_duyet', admin_note = NULL WHERE prod_id = $id");
    echo "<script>alert('Đã duyệt bài thành công!'); window.location.href='approve_products.php';</script>";
}

// 3. XỬ LÝ TỪ CHỐI
if (isset($_POST['btn_reject'])) {
    $id = intval($_POST['prod_id']);
    $note = mysqli_real_escape_string($conn, $_POST['admin_note']);
    mysqli_query($conn, "UPDATE products SET status = 'tu_choi', admin_note = '$note' WHERE prod_id = $id");
    echo "<script>alert('Đã gửi phản hồi từ chối!'); window.location.href='approve_products.php';</script>";
}

// 4. LẤY DANH SÁCH BÀI ĐANG CHỜ (Hỗ trợ cả 'pending' và 'cho_duyet')
$sql = "SELECT p.*, u.full_name 
        FROM products p 
        LEFT JOIN users u ON p.user_id = u.user_id 
        WHERE LOWER(p.status) IN ('pending', 'cho_duyet') 
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-0">KIỂM DUYỆT NỘI DUNG</h3>
            <p class="text-muted small mb-0">Phê duyệt sản phẩm đang ở trạng thái <strong>Chờ duyệt</strong></p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase fw-bold">
                    <tr>
                        <th class="ps-4 py-3">Sản phẩm</th>
                        <th class="py-3">Trạng thái</th>
                        <th class="py-3">Tác giả</th>
                        <th class="text-center py-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): 
                            $st = strtolower($row['status']);
                            $config = $status_map[$st] ?? $status_map['cho_duyet'];
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="../uploads/<?php echo $row['thumbnail'] ?: 'default.jpg'; ?>" 
                                             class="rounded-3 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></div>
                                            <small class="text-muted"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill <?php echo $config['class']; ?>">
                                        <?php echo $config['text']; ?>
                                    </span>
                                </td>
                                <td><span class="text-dark fw-medium"><?php echo htmlspecialchars($row['full_name'] ?: 'Ẩn danh'); ?></span></td>
                                <td class="text-center pe-4">
                                    <div class="btn-group shadow-sm">
                                        <a href="?approve=<?php echo $row['prod_id']; ?>" class="btn btn-success btn-sm fw-bold">Duyệt</a>
                                        <button type="button" class="btn btn-warning btn-sm text-white fw-bold" 
                                                onclick="openRejectModal(<?php echo $row['prod_id']; ?>, '<?php echo addslashes($row['title']); ?>')">
                                            Từ chối
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fa-solid fa-folder-open fa-3x text-light mb-3 d-block"></i>
                                <span class="text-muted">Hiện tại không có bài viết nào đang chờ duyệt.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-warning text-white">
                <h5 class="modal-title fw-bold">LÝ DO TỪ CHỐI DUYỆT</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="prod_id" id="modal_id">
                <p class="mb-3">Bài viết: <strong id="modal_title" class="text-primary"></strong></p>
                <textarea name="admin_note" class="form-control" rows="4" required placeholder="Ghi chú lý do..."></textarea>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="btn_reject" class="btn btn-warning text-white rounded-pill px-4 fw-bold">Gửi phản hồi</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id, title) {
    document.getElementById('modal_id').value = id;
    document.getElementById('modal_title').innerText = title;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
<?php include_once '../includes/footer.php'; ?>