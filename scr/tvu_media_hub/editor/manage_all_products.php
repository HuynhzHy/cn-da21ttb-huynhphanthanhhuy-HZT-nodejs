<?php
include_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$role = isset($_SESSION['role_name']) ? strtolower($_SESSION['role_name']) : '';
// Editor ko có quyền: Đá ra Dashboard nếu ko phải admin
if ($role !== 'super_admin') {
    header("Location: dashboard.php"); exit();
}

// Xử lý xóa
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    
    // Xóa file vật lý trong folder uploads
    $res = mysqli_query($conn, "SELECT thumbnail, file_path FROM products WHERE prod_id = $id");
    $p = mysqli_fetch_assoc($res);
    if ($p) {
        if ($p['thumbnail']) @unlink("../uploads/" . $p['thumbnail']);
        if ($p['file_path']) @unlink("../uploads/" . $p['file_path']);
    }
    
    mysqli_query($conn, "DELETE FROM products WHERE prod_id = $id");
    header("Location: manage_all_products.php?msg=deleted"); exit();
}

include_once '../includes/header.php';
// JOIN với bảng campaigns dùng camp_id (có chữ p)
$sql = "SELECT p.*, u.full_name, c.camp_name 
        FROM products p 
        LEFT JOIN users u ON p.user_id = u.user_id 
        LEFT JOIN campaigns c ON p.camp_id = c.camp_id 
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<div class="container py-5">
    <h3 class="fw-bold text-danger mb-4">QUẢN TRỊ & XÓA BÀI ĐĂNG (DÀNH CHO ADMIN)</h3>
    <div class="card border-0 shadow rounded-4 overflow-hidden">
        <table class="table align-middle mb-0">
            <thead class="bg-dark text-white">
                <tr>
                    <th class="ps-4">Sản phẩm</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="ps-4">
                            <b><?php echo htmlspecialchars($row['title']); ?></b><br>
                            <small class="text-muted">Chiến dịch: <?php echo $row['camp_name'] ?? 'Tự do'; ?></small>
                        </td>
                        <td><span class="badge bg-info"><?php echo $row['status']; ?></span></td>
                        <td class="text-center">
                            <a href="?delete_id=<?php echo $row['prod_id']; ?>" class="btn btn-danger btn-sm rounded-pill" onclick="return confirm('Bạn là Admin, bạn chắc chắn muốn xóa bài này?')">Xóa vĩnh viễn</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include_once '../includes/footer.php'; ?>