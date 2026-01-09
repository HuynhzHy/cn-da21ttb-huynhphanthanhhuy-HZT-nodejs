<?php 
include_once 'config/db.php'; 
include_once 'includes/header.php'; 

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. Lấy thông tin chiến dịch
$sql_camp = "SELECT * FROM campaigns WHERE camp_id = $id";
$res_camp = mysqli_query($conn, $sql_camp);
$camp = mysqli_fetch_assoc($res_camp);

if (!$camp) {
    echo "<div class='container mt-5 alert alert-danger'>Không tìm thấy dữ liệu chiến dịch.</div>";
    include_once 'includes/footer.php';
    exit;
}

// 2. Lấy các bài dự thi thuộc chiến dịch này (TUI ĐÃ SỬA status = 'da_duyet')
$sql_works = "SELECT p.*, u.username, c.cat_name 
              FROM products p 
              LEFT JOIN users u ON p.user_id = u.user_id 
              LEFT JOIN categories c ON p.cat_id = c.cat_id 
              WHERE p.camp_id = $id AND p.status = 'da_duyet' 
              ORDER BY p.created_at DESC";
$res_works = mysqli_query($conn, $sql_works);
?>

<div class="container mt-4 mb-5">
    <div class="card border-0 shadow-lg rounded-5 overflow-hidden mb-5">
        <div class="row g-0 align-items-center">
            <div class="col-md-5">
                <img src="uploads/products/<?php echo $camp['thumbnail']; ?>" class="img-fluid h-100" style="min-height: 350px; object-fit: cover;">
            </div>
            <div class="col-md-7 p-4 p-md-5 bg-white">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">CHIẾN DỊCH MEDIA</span>
                <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($camp['camp_name']); ?></h1>
                <p class="text-muted fs-5 mb-4" style="line-height: 1.6;"><?php echo nl2br(htmlspecialchars($camp['description'])); ?></p>
                <div class="d-flex flex-wrap gap-4 pt-3 border-top">
                    <div>
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold">Ngày bắt đầu</small>
                        <span class="fw-bold text-dark"><i class="fa-regular fa-calendar-check me-2 text-success"></i><?php echo date('d/m/Y', strtotime($camp['start_date'])); ?></span>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-1 text-uppercase fw-bold">Ngày kết thúc</small>
                        <span class="fw-bold text-dark"><i class="fa-regular fa-clock me-2 text-danger"></i><?php echo date('d/m/Y', strtotime($camp['end_date'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-container border-blue">
        <h4 class="fw-bold mb-4 color-blue">
            <i class="fa-solid fa-clapperboard me-2"></i>SẢN PHẨM DỰ THI TRONG CHIẾN DỊCH (<?php echo mysqli_num_rows($res_works); ?>)
        </h4>
        <div class="row">
            <?php 
            if (mysqli_num_rows($res_works) > 0) {
                while($row = mysqli_fetch_assoc($res_works)) {
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden border">
                            <img src="uploads/products/<?php echo $row['thumbnail'] ?: 'assets/img/default.jpg'; ?>" class="card-img-top" style="height: 160px; object-fit: cover;">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-truncate mb-1"><?php echo htmlspecialchars($row['title']); ?></h6>
                                <p class="small text-muted mb-2">Tác giả: <strong><?php echo htmlspecialchars($row['username']); ?></strong></p>
                                <a href="detail.php?id=<?php echo $row['prod_id']; ?>" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-bold">Xem bài thi</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12 text-center py-5 text-muted border rounded-4 bg-light">Chưa có sản phẩm nào tham gia chiến dịch này.</div>';
            }
            ?>
        </div>
    </div>
</div>

<style>
    .section-container { padding: 30px; border-radius: 25px; background: #fff; border: 1px solid #f0f0f0; }
    .border-blue { border-left: 8px solid #0056b3; }
    .color-blue { color: #0056b3; }
</style>

<?php include_once 'includes/footer.php'; ?>