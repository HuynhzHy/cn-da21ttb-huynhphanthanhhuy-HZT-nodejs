<?php 
include_once 'config/db.php'; 
include_once 'includes/header.php'; 

$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// 1. Sửa trạng thái thành 'da_duyet'
$sql = "SELECT p.*, c.cat_name, u.full_name, u.username 
        FROM products p
        LEFT JOIN categories c ON p.cat_id = c.cat_id
        LEFT JOIN users u ON p.user_id = u.user_id
        WHERE p.status = 'da_duyet'"; // CHỖ NÀY ĐÃ SỬA

// 2. Xử lý logic lọc bài viết
if ($filter === 'featured') {
    // Ưu tiên bài nổi bật lên trước, sau đó mới đến ngày mới nhất
    $sql .= " AND p.is_featured = 1 ORDER BY p.created_at DESC";
    $title_display = "SẢN PHẨM TRUYỀN THÔNG NỔI BẬT";
} else {
    // Mặc định hiện tất cả, bài mới nhất lên đầu
    $sql .= " ORDER BY p.created_at DESC";
    $title_display = "TẤT CẢ SẢN PHẨM TRUYỀN THÔNG";
}

$result = mysqli_query($conn, $sql);
?>

<style>
    :root { --tvu-blue: #0056b3; --tvu-gold: #ffc107; }
    .card-hover:hover { transform: translateY(-5px); transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .dropdown-menu { z-index: 1050 !important; }
    /* Hiệu ứng hào quang cho bài nổi bật */
    .featured-border { border: 2px solid var(--tvu-gold) !important; position: relative; }
    .featured-border::after { content: "HOT"; position: absolute; top: 10px; right: -10px; background: red; color: white; padding: 2px 10px; font-size: 10px; font-weight: bold; border-radius: 5px; transform: rotate(15deg); }
</style>

<div class="container mt-5 mb-5">
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h2 class="fw-bold" style="color: var(--tvu-blue); font-family: 'Montserrat', sans-serif;">
                <?php echo $title_display; ?>
            </h2>
            <p class="text-muted">
                <?php echo ($filter === 'featured') ? "Danh sách các ấn phẩm tiêu biểu được Ban biên tập lựa chọn" : "Nơi trưng bày các ấn phẩm sáng tạo của sinh viên TVU"; ?>
            </p>
            <div style="width: 80px; height: 3px; background: var(--tvu-gold); margin: 10px auto;"></div>
        </div>
    </div>

    <div class="row">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden card-hover <?php echo ($row['is_featured'] == 1) ? 'featured-border' : 'border'; ?>">
                        
                        <div class="position-relative">
                            <?php 
                                $thumb_path = "uploads/" . $row['thumbnail']; 
                                if (!empty($row['thumbnail']) && file_exists($thumb_path)): ?>
                                    <img src="<?php echo $thumb_path; ?>" class="card-img-top" alt="<?php echo $row['title']; ?>" style="height: 220px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 220px;">
                                        <i class="fa-solid fa-file-lines fa-4x text-success opacity-75"></i>
                                    </div>
                                <?php endif; ?>

                            <span class="position-absolute top-0 start-0 m-3 badge bg-primary shadow-sm">
                                <i class="fa-solid fa-tag me-1"></i><?php echo htmlspecialchars($row['cat_name']); ?>
                            </span>

                            <?php if ($row['is_featured'] == 1): ?>
                                <span class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow-sm">
                                    <i class="fa-solid fa-star me-1"></i>Nổi bật
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold text-dark text-truncate"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="card-text text-muted small mb-4" style="height: 45px; overflow: hidden;">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-circle-user text-secondary me-2"></i>
                                    <small class="text-secondary">
                                        <b><?php echo htmlspecialchars(!empty($row['full_name']) ? $row['full_name'] : $row['username']); ?></b>
                                    </small>
                                </div>
                                <a href="detail.php?id=<?php echo $row['prod_id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-folder-open fa-4x text-light-emphasis mb-3"></i>
                <h4 class="text-muted">Không tìm thấy sản phẩm nào.</h4>
                <a href="index.php" class="btn btn-primary mt-3">Quay lại trang chủ</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>