<?php
include_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// CHỈ CHO PHÉP ADMIN VÀO TRANG NÀY
$role = isset($_SESSION['role_name']) ? strtolower($_SESSION['role_name']) : '';
if (!isset($_SESSION['user_id']) || $role !== 'super_admin') {
    echo "<script>alert('Chỉ Admin mới có quyền vào trang quản trị tổng thể!'); window.location.href='../index.php';</script>";
    exit();
}

// XỬ LÝ XÓA BÀI ĐĂNG (Dành cho bài đã duyệt hoặc bất kỳ bài nào)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // 1. Lấy thông tin file để xóa vật lý
    $res = mysqli_query($conn, "SELECT thumbnail, file_path FROM products WHERE prod_id = $id");
    $p = mysqli_fetch_assoc($res);
    if ($p) {
        if ($p['thumbnail']) @unlink("../uploads/" . $p['thumbnail']);
        if ($p['file_path']) @unlink("../uploads/" . $p['file_path']);
    }

    // 2. Xóa trong database
    if (mysqli_query($conn, "DELETE FROM products WHERE prod_id = $id")) {
        header("Location: manage_all_products.php?msg=success");
        exit();
    }
}

include_once '../includes/header.php';

// Lấy danh sách tất cả bài viết
$sql = "SELECT p.*, u.full_name, c.camp_name 
        FROM products p 
        LEFT JOIN users u ON p.user_id = u.user_id 
        LEFT JOIN campaigns c ON p.camp_id = c.camp_id 
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-danger"><i class="fa-solid fa-gears me-2"></i>QUẢN TRỊ TOÀN BỘ BÀI ĐĂNG</h3>
        <span class="badge bg-danger p-2 px-3 rounded-pill">Quyền Admin</span>
    </div>

    <?php if(isset($_GET['msg'])) echo "<div class='alert alert-success border-0 shadow-sm'>Đã xóa bài đăng và dữ liệu liên quan thành công!</div>"; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="ps-4 py-3">Sản phẩm</th>
                        <th>Chiến dịch</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="../uploads/<?php echo $row['thumbnail']; ?>" class="rounded-3 me-3" style="width: 45px; height: 45px; object-fit: cover;" onerror="this.src='https://placehold.co/45x45?text=Img'">
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['title']); ?></div>
                                        <small class="text-muted">Bởi: <?php echo htmlspecialchars($row['full_name']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?php echo $row['camp_name'] ?? 'Tự do'; ?></span>
                            </td>
                            <td>
                                <?php 
                                    $st = $row['status'];
                                    $class = ($st == 'Approved') ? 'bg-success' : (($st == 'pending') ? 'bg-warning' : 'bg-danger');
                                    echo "<span class='badge $class'>".ucfirst($st)."</span>";
                                ?>
                            </td>
                            <td class="small text-muted"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td class="text-center">
                                <a href="?delete_id=<?php echo $row['prod_id']; ?>" 
                                   class="btn btn-outline-danger btn-sm rounded-pill px-3" 
                                   onclick="return confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN bài này? Bài đã duyệt cũng sẽ biến mất khỏi trang chủ.')">
                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>