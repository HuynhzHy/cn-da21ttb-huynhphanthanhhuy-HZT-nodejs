<?php 
include_once 'config/db.php'; 
include_once 'includes/header.php'; 

/**
 * 1. HÀM HIỂN THỊ CARD SẢN PHẨM (Đã sửa lỗi hiển thị ảnh)
 */
function renderProductCards($result, $BASE_URL) {
    // Đảm bảo BASE_URL có dấu / ở cuối nếu nó không trống
    $url_prefix = (!empty($BASE_URL)) ? rtrim($BASE_URL, '/') . '/' : '';

    if ($result && $result instanceof mysqli_result && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $thumb_url = null;
            $file_path = $row['file_path'] ?? '';
            $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

            // --- TÍNH NĂNG 1: TỰ ĐỘNG LẤY ẢNH TỪ YOUTUBE ---
            if (!empty($row['youtube_link'])) {
                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $row['youtube_link'], $match);
                $youtube_id = $match[1] ?? null;
                if ($youtube_id) {
                    $thumb_url = "https://img.youtube.com/vi/$youtube_id/mqdefault.jpg";
                }
            }
// --- TÍNH NĂNG 2: XỬ LÝ HÌNH ẢNH THÔNG MINH (BẢN FIX CHUẨN) ---

// 1. Ưu tiên 1: Thumbnail do sinh viên tải lên
if (!empty($row['thumbnail'])) {
    // Đường dẫn để PHP kiểm tra trên ổ cứng (Tính từ file index.php)
    $path_to_check = "uploads/" . $row['thumbnail']; 
    
    if (file_exists($path_to_check)) {
        // Nếu thấy file, dùng BASE_URL để hiển thị lên Web
        $thumb_url = $BASE_URL . "uploads/" . $row['thumbnail'];
    }
} 

// 2. Ưu tiên 2: Nếu chưa có thumbnail mà file đính kèm là hình ảnh
if (empty($thumb_url) && !empty($file_path)) {
    $is_image_file = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif']);
    $file_to_check = "uploads/files/" . $file_path;
    
    if ($is_image_file && file_exists($file_to_check)) {
        $thumb_url = $BASE_URL . "uploads/files/" . $file_path;
    }
}

// 3. Nếu vẫn không tìm thấy ảnh (File không tồn tại trên ổ cứng)
if (empty($thumb_url)) {
    // Tự động lấy ảnh YouTube nếu có link
    if (!empty($row['youtube_link'])) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $row['youtube_link'], $match);
        $youtube_id = $match[1] ?? null;
        if ($youtube_id) {
            $thumb_url = "https://img.youtube.com/vi/$youtube_id/mqdefault.jpg";
        }
    }
}
            $featured_class = ($row['is_featured'] == 1) ? 'featured-card border-warning shadow' : 'border-light';
            ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm border-2 rounded-4 overflow-hidden product-card <?php echo $featured_class; ?>">
                    <div class="position-relative thumb-container" style="background: #f8f9fa;">
                        <?php if ($thumb_url): ?>
                            <img src="<?php echo $thumb_url; ?>" class="card-img-top" style="height: 180px; width: 100%; object-fit: cover;" onerror="this.src='assets/img/no-image.png';">
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="fa-solid <?php 
                                    if(!empty($row['youtube_link'])) echo 'fa-film text-primary'; 
                                    elseif(in_array($file_ext, ['pdf', 'doc', 'docx'])) echo 'fa-file-lines text-success';
                                    else echo 'fa-image text-warning'; 
                                ?> fa-3x opacity-25"></i>
                            </div>
                        <?php endif; ?>

                        <?php if ($row['is_featured'] == 1): ?>
                            <span class="position-absolute top-0 end-0 m-2 badge bg-danger rounded-pill shadow-sm animate-pulse">
                                <i class="fa-solid fa-fire me-1"></i> HOT
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($row['camp_name'])): ?>
                            <span class="position-absolute bottom-0 end-0 m-2 badge bg-warning text-dark rounded-pill shadow-sm" style="font-size: 0.7rem;">
                                <i class="fa-solid fa-trophy me-1"></i> <?php echo htmlspecialchars($row['camp_name']); ?>
                            </span>
                        <?php endif; ?>
                        
                        <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 rounded-pill small">
                            <?php echo htmlspecialchars($row['cat_name'] ?? 'Chung'); ?>
                        </span>
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <h6 class="card-title fw-bold text-dark text-truncate mb-1"><?php echo htmlspecialchars($row['title']); ?></h6>
                        <p class="text-muted small mb-3 overflow-hidden" style="font-size: 0.8rem; height: 35px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <small class="text-secondary text-truncate" style="max-width: 100px;">
                                <i class="fa-solid fa-user-circle me-1"></i><?php echo htmlspecialchars($row['username']); ?>
                            </small>
                            <a href="detail.php?id=<?php echo $row['prod_id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm">Chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="col-12 text-center text-muted py-5"><i class="fa-solid fa-box-open fa-3x mb-3 opacity-25"></i><br>Chưa có nội dung mục này.</div>';
    }
}

/**
 * 2. TRUY VẤN DỮ LIỆU
 */
$base_sql = "SELECT p.*, c.cat_name, u.username, cam.camp_name 
             FROM products p 
             LEFT JOIN categories c ON p.cat_id = c.cat_id 
             LEFT JOIN users u ON p.user_id = u.user_id 
             LEFT JOIN campaigns cam ON p.camp_id = cam.camp_id 
             WHERE p.status = 'da_duyet'"; 

// Phân loại để đổ vào các mục
$res_latest = mysqli_query($conn, $base_sql . " ORDER BY p.created_at DESC LIMIT 4");
$res_videos = mysqli_query($conn, $base_sql . " AND p.youtube_link != '' ORDER BY p.created_at DESC LIMIT 4");
$res_docs   = mysqli_query($conn, $base_sql . " AND (p.file_path LIKE '%.pdf' OR p.file_path LIKE '%.doc%') ORDER BY p.created_at DESC LIMIT 4");
$res_images = mysqli_query($conn, $base_sql . " AND (p.file_path LIKE '%.jpg' OR p.file_path LIKE '%.png' OR p.file_path LIKE '%.jpeg') AND (p.youtube_link = '' OR p.youtube_link IS NULL) ORDER BY p.created_at DESC LIMIT 8");
?>

<div class="container mt-5 mb-5">
    <div class="section-container mb-5 border-navy">
        <h4 class="fw-bold section-title mb-4 color-navy"><i class="fa-solid fa-bolt-lightning text-warning me-2"></i>MỚI CẬP NHẬT</h4>
        <div class="row"><?php renderProductCards($res_latest, $BASE_URL); ?></div>
    </div>

    <div class="section-container mb-5 border-blue bg-light-blue">
        <h4 class="fw-bold section-title mb-4 color-blue"><i class="fa-solid fa-circle-play me-2"></i>VIDEO SÁNG TẠO</h4>
        <div class="row"><?php renderProductCards($res_videos, $BASE_URL); ?></div>
    </div>

    <div class="section-container mb-5 border-green">
        <h4 class="fw-bold section-title mb-4 color-green"><i class="fa-solid fa-file-invoice me-2"></i>TÀI LIỆU & ẤN PHẨM</h4>
        <div class="row"><?php renderProductCards($res_docs, $BASE_URL); ?></div>
    </div>

    <div class="section-container mb-5 border-yellow">
        <h4 class="fw-bold section-title mb-4 color-yellow"><i class="fa-solid fa-images me-2"></i>KHO HÌNH ẢNH</h4>
        <div class="row"><?php renderProductCards($res_images, $BASE_URL); ?></div>
    </div>
</div>

<style>
    :root { --tvu-blue: #0056b3; --tvu-gold: #ffc107; --tvu-green: #198754; --tvu-navy: #002d5f; }
    .section-container { padding: 30px; border-radius: 25px; background: #ffffff; border: 1px solid #eee; margin-bottom: 40px; }
    .featured-card { transform: scale(1.02); z-index: 1; border: 2px solid var(--tvu-gold) !important; }
    .animate-pulse { animation: pulse 2s infinite; }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .product-card { transition: all 0.3s ease; border: 1px solid #f0f0f0; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    .color-navy { color: var(--tvu-navy); }
    .color-blue { color: var(--tvu-blue); }
    .color-green { color: var(--tvu-green); }
    .color-yellow { color: #856404; }
    body {
    background-color: #f8f8f8ff; /* Màu xám nhạt dịu mắt */
    /* Hoặc dùng màu xanh rất nhẹ nếu muốn vibe công nghệ */
    /* background-color: #93e1f6ff; */
}

/* Đảm bảo các section container vẫn nổi bật trên nền mới */
.section-container {
    background: #c2e1ecff; /* Giữ nền trắng cho các khung nội dung */
    box-shadow: 0 4px 15px rgba(0,0,0,0.05); /* Thêm đổ bóng cho đẹp */
}
</style>

<?php include_once 'includes/footer.php'; ?>