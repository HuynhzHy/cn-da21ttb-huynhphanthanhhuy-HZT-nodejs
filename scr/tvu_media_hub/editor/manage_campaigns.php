<?php
include_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// KIỂM TRA QUYỀN
$role = isset($_SESSION['role_name']) ? strtolower($_SESSION['role_name']) : '';
if (!isset($_SESSION['user_id']) || ($role !== 'super_admin' && $role !== 'admin')) {
    header("Location: ../index.php"); exit();
}

$msg = "";
// XỬ LÝ THÊM CHIẾN DỊCH (GIỐNG ĐĂNG BÀI)
if (isset($_POST['btn_add_campaign'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $user_id = $_SESSION['user_id'];
    
    // Xử lý Upload ảnh đại diện
    $thumbnail = "";
    if (!empty($_FILES['thumbnail']['name'])) {
        $thumbnail = time() . '_' . $_FILES['thumbnail']['name'];
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], "../uploads/products/" . $thumbnail);
    }

    // Lưu vào bảng campaigns (Đảm bảo bảng của Huy có cột description và thumbnail)
    // Nếu chưa có, Huy chạy lệnh SQL: ALTER TABLE campaigns ADD COLUMN description TEXT, ADD COLUMN thumbnail VARCHAR(255);
    $sql = "INSERT INTO campaigns (camp_name, description, thumbnail, start_date, end_date, status, created_by) 
            VALUES ('$title', '$desc', '$thumbnail', '$start', '$end', 'Active', '$user_id')";
    
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success'>Tạo chiến dịch thành công!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Lỗi: " . mysqli_error($conn) . "</div>";
    }
}

$result = mysqli_query($conn, "SELECT * FROM campaigns ORDER BY camp_id DESC");
include_once '../includes/header.php';
?>

<div class="container py-5">
    <?php echo $msg; ?>
    <div class="d-flex justify-content-between mb-4">
        <h3 class="fw-bold text-primary">QUẢN LÝ CHIẾN DỊCH</h3>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addCamModal">
            <i class="fa-solid fa-plus"></i> Tạo chiến dịch mới
        </button>
    </div>

    <div class="row">
        <?php 
        $today = date('Y-m-d');
        while ($cam = mysqli_fetch_assoc($result)): 
            // LOGIC KIỂM TRA TRẠNG THÁI TỰ ĐỘNG
            $status_text = "";
            $status_class = "";
            
            if ($today < $cam['start_date']) {
                $status_text = "Sắp diễn ra";
                $status_class = "bg-warning text-dark";
            } elseif ($today > $cam['end_date']) {
                $status_text = "Đã kết thúc";
                $status_class = "bg-secondary";
            } else {
                $status_text = "Đang diễn ra";
                $status_class = "bg-success";
            }
        ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                    <img src="../uploads/products/<?php echo $cam['thumbnail'] ?: 'default_camp.jpg'; ?>" class="card-img-top" style="height: 160px; object-fit: cover;">
                    <div class="card-body">
                        <span class="badge <?php echo $status_class; ?> mb-2"><?php echo $status_text; ?></span>
                        <h5 class="fw-bold text-truncate"><?php echo $cam['camp_name']; ?></h5>
                        <p class="small text-muted mb-2"><i class="fa-regular fa-calendar"></i> <?php echo date('d/m/Y', strtotime($cam['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($cam['end_date'])); ?></p>
                        <p class="small text-truncate-2"><?php echo $cam['description']; ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="modal fade" id="addCamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg border-0">
        <form action="" method="POST" enctype="multipart/form-data" class="modal-content rounded-4">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Chiến dịch mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên chiến dịch</label>
                    <input type="text" name="title" class="form-control" placeholder="Nhập tên chiến dịch..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Ảnh đại diện</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả nội dung</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Viết mô tả chiến dịch ở đây..."></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="btn_add_campaign" class="btn btn-primary w-100 fw-bold py-2 shadow">XÁC NHẬN TẠO CHIẾN DỊCH</button>
            </div>
        </form>
    </div>
</div>

<style> .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; } </style>
<?php include_once '../includes/footer.php'; ?>