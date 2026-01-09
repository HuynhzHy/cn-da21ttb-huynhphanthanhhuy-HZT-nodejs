<?php 
include_once 'config/db.php'; 
include_once 'includes/header.php'; 

// 1. Lấy ID từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. Truy vấn chi tiết sản phẩm - ĐÃ SỬA status = 'da_duyet'
$sql = "SELECT p.*, u.full_name FROM products p 
        JOIN users u ON p.user_id = u.user_id 
        WHERE p.prod_id = $id AND p.status = 'da_duyet'";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<div class='container mt-5'><div class='alert alert-danger shadow-sm rounded-4 p-4'><i class='fa-solid fa-triangle-exclamation me-2'></i>Sản phẩm không tồn tại hoặc chưa được duyệt.</div></div>";
    include_once 'includes/footer.php';
    exit();
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h1 class="fw-bold text-dark mb-3"><?php echo htmlspecialchars($product['title']); ?></h1>
            <p class="text-muted">
                <i class="fa-solid fa-user me-1 text-primary"></i> Đăng bởi: <b><?php echo htmlspecialchars($product['full_name']); ?></b> | 
                <i class="fa-solid fa-calendar-days ms-2 me-1 text-primary"></i> Ngày: <?php echo date('d/m/Y', strtotime($product['created_at'])); ?>
            </p>
            <hr class="opacity-10">

            <div class="content-display mb-4">
                <?php if (!empty($product['youtube_link'])): ?>
                    <?php 
                        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $product['youtube_link'], $match);
                        $youtube_id = $match[1] ?? '';
                    ?>
                    <?php if ($youtube_id): ?>
                    <div class="ratio ratio-16x9 shadow-sm rounded-4 overflow-hidden border">
                        <iframe src="https://www.youtube.com/embed/<?php echo $youtube_id; ?>" allowfullscreen></iframe>
                    </div>
                    <?php endif; ?>
                <?php elseif (!empty($product['thumbnail'])): ?>
                    <img src="uploads/products/<?php echo $product['thumbnail']; ?>" class="img-fluid rounded-4 shadow-sm w-100 border">
                <?php endif; ?>
            </div>

            <div class="bg-white p-4 rounded-4 shadow-sm mb-4 border">
                <h5 class="fw-bold border-bottom pb-2 mb-3"><i class="fa-solid fa-align-left text-primary me-2"></i>Mô tả sản phẩm</h5>
                <p style="white-space: pre-line; line-height: 1.8; color: #444;">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>
            </div>

            <?php if (!empty($product['file_path'])): ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4 bg-light border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="fa-solid fa-file-arrow-down fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">Tài liệu đính kèm</h6>
                                    <small class="text-muted">Định dạng file gốc: <?php echo pathinfo($product['file_path'], PATHINFO_EXTENSION); ?></small>
                                </div>
                            </div>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="uploads/files/<?php echo $product['file_path']; ?>" 
                                   class="btn btn-success fw-bold px-4 py-2 rounded-pill shadow-sm" download>
                                    <i class="fa-solid fa-download me-2"></i> Tải xuống ngay
                                </a>
                            <?php else: ?>
                                <div class="text-end">
                                    <button class="btn btn-secondary fw-bold px-4 py-2 rounded-pill mb-2" disabled>
                                        <i class="fa-solid fa-lock me-2"></i> Tải xuống (Đã khóa)
                                    </button>
                                    <br>
                                    <small class="text-danger fw-bold">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> 
                                        Vui lòng <a href="auth/login.php" class="text-primary text-decoration-underline">Đăng nhập</a> để tải tài liệu.
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="text-center mt-5">
                <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                    <i class="fa-solid fa-arrow-left me-2"></i> Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .content-display img { transition: transform 0.3s ease; }
    .content-display img:hover { transform: scale(1.01); }
    .btn-outline-secondary:hover { background-color: #6c757d; color: white; }
</style>

<?php include_once 'includes/footer.php'; ?>