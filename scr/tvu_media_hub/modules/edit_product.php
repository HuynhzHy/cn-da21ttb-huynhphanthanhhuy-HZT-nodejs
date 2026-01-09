<?php
include_once '../config/db.php';
include_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php"); exit();
}

$user_id = $_SESSION['user_id'];
$prod_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. LẤY DỮ LIỆU CŨ
$sql_get = "SELECT * FROM products WHERE prod_id = $prod_id AND user_id = $user_id";
$res_get = mysqli_query($conn, $sql_get);
$data = mysqli_fetch_assoc($res_get);

if (!$data) {
    echo "<div class='container mt-5'><div class='alert alert-danger shadow-sm'>Sản phẩm không tồn tại hoặc bạn không có quyền sửa.</div></div>";
    include_once '../includes/footer.php';
    exit();
}

$msg = "";

// 2. XỬ LÝ CẬP NHẬT
if (isset($_POST['btn_update'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $yt_link = mysqli_real_escape_string($conn, $_POST['youtube_link']);
    
    $thumbnail_name = $data['thumbnail']; 
    $file_path_name = $data['file_path']; 
    $target_dir = "../uploads/"; // Đồng bộ thư mục chung

    // Nếu upload Thumbnail mới
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $thumbnail_name = "thumb_" . time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_dir . $thumbnail_name);
    }

    // Nếu upload File đính kèm mới
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $f_ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $file_path_name = "file_" . time() . "_" . rand(100, 999) . "." . $f_ext;
        move_uploaded_file($_FILES['attachment']['tmp_name'], $target_dir . $file_path_name);
    }

    // --- QUAN TRỌNG: Chuyển status về 'cho_duyet' (Chuẩn Tiếng Việt) ---
    $sql_update = "UPDATE products SET 
                    title = '$title', 
                    description = '$desc', 
                    youtube_link = '$yt_link', 
                    thumbnail = '$thumbnail_name', 
                    file_path = '$file_path_name',
                    status = 'cho_duyet' 
                   WHERE prod_id = $prod_id AND user_id = $user_id";

    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('Cập nhật thành công! Bài viết đã được chuyển về trạng thái Chờ duyệt.'); window.location.href='my_products.php';</script>";
        exit();
    } else {
        $msg = "<div class='alert alert-danger'>Lỗi: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3 border-0">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>CHỈNH SỬA SẢN PHẨM</h5>
                </div>
                <div class="card-body p-4">
                    <?php echo $msg; ?>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Tiêu đề sản phẩm</label>
                            <input type="text" name="title" class="form-control border-2" value="<?php echo htmlspecialchars($data['title']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Mô tả sản phẩm</label>
                            <textarea name="description" class="form-control border-2" rows="4"><?php echo htmlspecialchars($data['description']); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Link Video Youtube</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa-brands fa-youtube text-danger"></i></span>
                                <input type="url" name="youtube_link" class="form-control border-start-0 ps-0" value="<?php echo $data['youtube_link']; ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Thay ảnh đại diện mới</label>
                                <input type="file" name="thumbnail" class="form-control mb-2" accept="image/*">
                                <?php if($data['thumbnail']): ?>
                                    <div class="p-2 bg-light rounded border text-center">
                                        <small class="text-muted d-block mb-1">Ảnh hiện tại:</small>
                                        <img src="../uploads/<?php echo $data['thumbnail']; ?>" style="height: 60px;" class="rounded shadow-sm">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small text-uppercase">Thay file đính kèm mới</label>
                                <input type="file" name="attachment" class="form-control mb-2">
                                <?php if($data['file_path']): ?>
                                    <div class="p-2 bg-light rounded border">
                                        <small class="text-muted d-block">File cũ:</small>
                                        <span class="small text-truncate d-block text-primary"><i class="fa-solid fa-paperclip me-1"></i><?php echo $data['file_path']; ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="my_products.php" class="btn btn-light rounded-pill px-4 fw-bold">Hủy bỏ</a>
                            <button type="submit" name="btn_update" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">LƯU THAY ĐỔI</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus { border-color: #0d6efd; box-shadow: none; }
</style>

<?php include_once '../includes/footer.php'; ?>