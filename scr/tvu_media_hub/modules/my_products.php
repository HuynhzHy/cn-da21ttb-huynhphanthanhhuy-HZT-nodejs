<?php
include_once '../config/db.php';
include_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='../auth/login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. XỬ LÝ GỬI YÊU CẦU XÓA
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    
    // Đổi status thành 'cho_xoa' để đồng bộ với trang Admin tui đã viết cho Huy
    $sql_request_del = "UPDATE products SET status = 'cho_xoa' WHERE prod_id = $del_id AND user_id = $user_id";
    
    if (mysqli_query($conn, $sql_request_del)) {
        echo "<script>alert('Yêu cầu xóa đã được gửi! Vui lòng chờ Admin phê duyệt.'); window.location.href='my_products.php';</script>";
        exit();
    }
}

// 2. LẤY DANH SÁCH SẢN PHẨM CỦA SINH VIÊN
$sql = "SELECT p.*, c.cat_name 
        FROM products p 
        LEFT JOIN categories c ON p.cat_id = c.cat_id 
        WHERE p.user_id = $user_id 
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);

// --- BỘ MAPPING TRẠNG THÁI (Đồng bộ với Admin) ---
$status_map = [
    'da_duyet'  => ['class' => 'bg-success', 'text' => 'Đã duyệt'],
    'approved'  => ['class' => 'bg-success', 'text' => 'Đã duyệt'],
    'tu_choi'   => ['class' => 'bg-danger',  'text' => 'Bị từ chối'],
    'rejected'  => ['class' => 'bg-danger',  'text' => 'Bị từ chối'],
    'cho_xoa'   => ['class' => 'bg-dark',    'text' => 'Chờ xóa'],
    'pending_delete' => ['class' => 'bg-dark', 'text' => 'Chờ xóa'],
    'cho_duyet' => ['class' => 'bg-warning text-dark', 'text' => 'Chờ duyệt'],
    'pending'   => ['class' => 'bg-warning text-dark', 'text' => 'Chờ duyệt']
];
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">SẢN PHẨM CỦA TÔI</h3>
            <p class="text-muted small">Quản lý và theo dõi trạng thái bài đăng</p>
        </div>
        <a href="post_product.php" class="btn btn-primary rounded-pill shadow-sm px-4">
            <i class="fa-solid fa-plus me-1"></i> Nộp bài mới
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4">Sản phẩm</th>
                        <th>Định dạng</th>
                        <th>Ngày nộp</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): 
                            $st = strtolower($row['status']);
                            $badge = $status_map[$st] ?? $status_map['cho_duyet'];
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3" style="width: 55px; height: 55px;">
                                            <?php 
                                            // Chỉnh lại đường dẫn ảnh cho khớp với upload.php
                                            $img_path = "../uploads/" . $row['thumbnail'];
                                            if (!empty($row['thumbnail']) && file_exists($img_path)): ?>
                                                <img src="<?php echo $img_path; ?>" class="rounded-3 w-100 h-100 object-fit-cover shadow-sm">
                                            <?php else: ?>
                                                <div class="bg-light rounded-3 w-100 h-100 d-flex align-items-center justify-content-center border">
                                                    <i class="fa-solid fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 250px;">
                                                <?php echo htmlspecialchars($row['title']); ?>
                                            </div>
                                            <small class="badge bg-light text-primary border"><?php echo $row['cat_name'] ?: 'Chưa phân loại'; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($row['youtube_link'])): ?>
                                        <i class="fa-brands fa-youtube text-danger fs-5 me-2" title="Có Video Youtube"></i>
                                    <?php endif; ?>
                                    <?php if (!empty($row['file_path'])): ?>
                                        <i class="fa-solid fa-file-arrow-down text-success fs-5" title="Có file đính kèm"></i>
                                    <?php endif; ?>
                                </td>
                                <td><small class="text-muted fw-medium"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></small></td>
                                <td>
                                    <span class="badge rounded-pill <?php echo $badge['class']; ?> px-3 py-2">
                                        <?php echo $badge['text']; ?>
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <div class="btn-group shadow-sm rounded-3">
                                        <a href="../detail.php?id=<?php echo $row['prod_id']; ?>" class="btn btn-sm btn-outline-secondary" title="Xem chi tiết">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($st != 'cho_xoa' && $st != 'pending_delete'): ?>
                                            <a href="edit_product.php?id=<?php echo $row['prod_id']; ?>" class="btn btn-sm btn-outline-primary" title="Sửa bài">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="my_products.php?delete_id=<?php echo $row['prod_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Bạn có chắc muốn gửi yêu cầu XÓA bài viết này không?')" title="Yêu cầu xóa">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light text-muted" disabled title="Đang chờ Admin xử lý xóa">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Bạn chưa nộp sản phẩm nào.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>