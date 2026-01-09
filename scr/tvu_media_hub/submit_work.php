<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include_once 'config/db.php';
include_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Bạn cần đăng nhập!'); window.location.href='auth/login.php';</script>";
    exit();
}

$get_camp_id = isset($_GET['camp_id']) ? intval($_GET['camp_id']) : 0;
$msg = "";

// Lấy danh sách chiến dịch đang mở
$campaigns_res = mysqli_query($conn, "SELECT * FROM campaigns WHERE status = 'Active' ORDER BY camp_id DESC");

if (isset($_POST['btn_upload'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $yt_link = mysqli_real_escape_string($conn, $_POST['youtube_link']);
    
    // Quan trọng: Nếu không chọn chiến dịch thì lưu NULL (không có dấu nháy đơn)
    $camp_id = !empty($_POST['camp_id']) ? intval($_POST['camp_id']) : "NULL";

    $thumbnail_name = "";
    $target_dir = "uploads/products/";

    if (!empty($_FILES['thumbnail']['name'])) {
        $thumbnail_name = time() . "_" . $_FILES['thumbnail']['name'];
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target_dir . $thumbnail_name);
    }

    // Câu lệnh INSERT có camp_id
    $sql = "INSERT INTO products (user_id, camp_id, title, description, youtube_link, thumbnail, status) 
            VALUES ('$user_id', $camp_id, '$title', '$desc', '$yt_link', '$thumbnail_name', 'pending')";

    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success shadow-sm rounded-4'>Nộp bài thành công! Đang chờ duyệt.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Lỗi: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <?php echo $msg; ?>
            <form action="" method="POST" enctype="multipart/form-data" class="card shadow-lg border-0 rounded-5 p-4">
                <h4 class="fw-bold text-primary mb-4 text-center">NỘP SẢN PHẨM</h4>
                
                <div class="mb-3">
                    <label class="fw-bold small text-uppercase text-muted">Chiến dịch tham gia</label>
                    <select name="camp_id" class="form-select border-2">
                        <option value="">-- Đăng bài tự do --</option>
                        <?php while($cam = mysqli_fetch_assoc($campaigns_res)): ?>
                            <option value="<?php echo $cam['camp_id']; ?>" <?php echo ($cam['camp_id'] == $get_camp_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cam['camp_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="fw-bold small text-uppercase text-muted">Tiêu đề sản phẩm</label>
                    <input type="text" name="title" class="form-control border-2" required>
                </div>

                <div class="mb-3">
                    <label class="fw-bold small text-uppercase text-muted">Mô tả nội dung</label>
                    <textarea name="description" class="form-control border-2" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-bold small text-uppercase text-muted">Link Youtube</label>
                    <input type="url" name="youtube_link" class="form-control border-2">
                </div>

                <div class="mb-4">
                    <label class="fw-bold small text-uppercase text-muted">Ảnh đại diện</label>
                    <input type="file" name="thumbnail" class="form-control border-2" accept="image/*">
                </div>

                <button type="submit" name="btn_upload" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">XÁC NHẬN GỬI BÀI</button>
            </form>
        </div>
    </div>
</div>
<?php include_once 'includes/footer.php'; ?>