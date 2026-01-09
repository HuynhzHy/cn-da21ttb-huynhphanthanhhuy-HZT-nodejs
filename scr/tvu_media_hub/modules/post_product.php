<?php
include_once '../config/db.php';
include_once '../includes/header.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Bạn cần đăng nhập!'); window.location.href='../auth/login.php';</script>";
    exit();
}

$get_camp_id = isset($_GET['camp_id']) ? intval($_GET['camp_id']) : 0;
$msg = "";

// 2. LẤY DANH SÁCH CHIẾN DỊCH
$today = date('Y-m-d');
$sql_camp = "SELECT camp_id, camp_name FROM campaigns WHERE status = 'Active' AND '$today' >= start_date AND '$today' <= end_date ORDER BY camp_id DESC";
$campaigns_res = mysqli_query($conn, $sql_camp);

// 3. XỬ LÝ KHI NHẤN NÚT GỬI BÀI
if (isset($_POST['btn_upload'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $yt_link = mysqli_real_escape_string($conn, $_POST['youtube_link']);
    $camp_id = !empty($_POST['camp_id']) ? intval($_POST['camp_id']) : "NULL";

    $thumbnail_name = "";
    $file_path_name = ""; 
    $target_dir_thumb = "../uploads/products/";
    $target_dir_file = "../uploads/files/";

    // Tạo thư mục nếu chưa có
    if (!file_exists($target_dir_thumb)) { mkdir($target_dir_thumb, 0777, true); }
    if (!file_exists($target_dir_file)) { mkdir($target_dir_file, 0777, true); }

    // XỬ LÝ ẢNH ĐẠI DIỆN (Nếu sinh viên có upload)
    if (!empty($_FILES['thumbnail']['name'])) {
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $thumbnail_name = "thumb_" . time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_dir_thumb . $thumbnail_name);
    }

    // XỬ LÝ FILE ĐÍNH KÈM (Mở hoàn toàn - Không chặn đuôi file)
    if (!empty($_FILES['attachment']['name'])) {
        $f_name = $_FILES['attachment']['name'];
        $f_ext = strtolower(pathinfo($f_name, PATHINFO_EXTENSION));
        // Đặt tên file an toàn
        $file_path_name = "file_" . time() . "_" . rand(100, 999) . "." . $f_ext;
        move_uploaded_file($_FILES['attachment']['tmp_name'], $target_dir_file . $file_path_name);
    }

    /**
     * TỰ ĐỘNG PHÂN LOẠI (Logic này cực quan trọng để trang chủ tự lọc)
     * Giả định: 1 = Video, 2 = Tài liệu, 3 = Hình ảnh
     */
    $cat_id = 4; // Mặc định 4 là mục "Khác/Chung"
    
    // Nếu có link Youtube -> Chắc chắn là Video
    if (!empty($yt_link)) {
        $cat_id = 1; 
    } 
    // Nếu không có Youtube, check đuôi file đính kèm
    elseif (!empty($file_path_name)) {
        $img_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $doc_exts = ['pdf', 'doc', 'docx', 'zip', 'rar', 'ppt', 'pptx', 'xls', 'xlsx'];
        $vid_exts = ['mp4', 'mov', 'avi'];

        if (in_array($f_ext, $img_exts)) $cat_id = 3;
        elseif (in_array($f_ext, $doc_exts)) $cat_id = 2;
        elseif (in_array($f_ext, $vid_exts)) $cat_id = 1;
    }

    // LƯU VÀO DATABASE (Status mặc định là cho_duyet)
    $sql = "INSERT INTO products (user_id, camp_id, cat_id, title, description, youtube_link, thumbnail, file_path, status) 
            VALUES ('$user_id', $camp_id, '$cat_id', '$title', '$desc', '$yt_link', '$thumbnail_name', '$file_path_name', 'cho_duyet')";

    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success border-0 rounded-4 shadow-sm'>
                    <i class='fa-solid fa-check-circle me-2'></i> Nộp bài thành công! Sản phẩm của bạn đã được hệ thống phân loại tự động.
                </div>";
    } else {
        $msg = "<div class='alert alert-danger'>Lỗi: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php echo $msg; ?>
            
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold text-center"><i class="fa-solid fa-paper-plane me-2"></i>NỘP SẢN PHẨM TRUYỀN THÔNG</h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small uppercase">Chọn chiến dịch / Cuộc thi</label>
                            <select name="camp_id" class="form-select border-2">
                                <option value="">-- Tự do (Không thuộc chiến dịch) --</option>
                                <?php while($cam = mysqli_fetch_assoc($campaigns_res)): ?>
                                    <option value="<?= $cam['camp_id'] ?>" <?= ($cam['camp_id'] == $get_camp_id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cam['camp_name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control border-2" required placeholder="Nhập tên sản phẩm của bạn...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả bài đăng</label>
                            <textarea name="description" class="form-control border-2" rows="3" placeholder="Viết vài dòng giới thiệu..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-danger"><i class="fa-brands fa-youtube me-1"></i> Link Youtube</label>
                            <input type="url" name="youtube_link" class="form-control border-2" placeholder="https://www.youtube.com/watch?v=...">
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">ẢNH ĐẠI DIỆN (THUMBNAIL)</label>
                                <div class="upload-box border-dashed p-3 text-center bg-light rounded-3">
                                    <input type="file" name="thumbnail" class="form-control">
                                    <small class="text-muted d-block mt-2">Dùng để hiển thị ngoài trang chủ</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">FILE ĐÍNH KÈM</label>
                                <div class="upload-box border-dashed p-3 text-center bg-light rounded-3">
                                    <input type="file" name="attachment" class="form-control">
                                    <small class="text-muted d-block mt-2">PDF, ZIP, DOCX, MP4, PNG...</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="btn_upload" class="btn btn-primary btn-lg fw-bold rounded-pill py-3 shadow">
                                <i class="fa-solid fa-cloud-arrow-up me-2"></i> GỬI BÀI CHO ADMIN KIỂM DUYỆT
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-dashed { border: 2px dashed #dee2e6 !important; }
    .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: none; }
    .card { transition: 0.3s; }
</style>

<?php include_once '../includes/footer.php'; ?>