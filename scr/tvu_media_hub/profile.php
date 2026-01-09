<?php
include_once 'config/db.php';
include_once 'includes/header.php';

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $BASE_URL . "auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$msg = ""; 

// 2. XỬ LÝ CẬP NHẬT HỒ SƠ (Giữ nguyên logic cũ của Huy)
if (isset($_POST['btn_update_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dept_id = intval($_POST['dept_id']);

    $avatar_sql = ""; 
    if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
        $file_name = $_FILES['avatar']['name'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        if (!file_exists('uploads')) { mkdir('uploads', 0777, true); }
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_name = "avatar_" . $user_id . "_" . time() . "." . $ext; 
        
        if (move_uploaded_file($file_tmp, "uploads/" . $new_name)) {
            $old_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT avatar FROM users WHERE user_id = $user_id"));
            if (!empty($old_data['avatar']) && file_exists("uploads/" . $old_data['avatar'])) {
                unlink("uploads/" . $old_data['avatar']);
            }
            $avatar_sql = ", avatar='$new_name'";
            $_SESSION['avatar'] = $new_name; 
        }
    }

    $sql_update = "UPDATE users SET full_name='$full_name', phone='$phone', address='$address', dept_id='$dept_id' $avatar_sql WHERE user_id='$user_id'";
    if (mysqli_query($conn, $sql_update)) {
        $msg = "<div class='alert alert-success shadow-sm alert-dismissible fade show' role='alert'>
                    <i class='fa-solid fa-circle-check me-2'></i> Cập nhật hồ sơ thành công!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
        $_SESSION['full_name'] = $full_name;
    }
}

// 3. LẤY THÔNG TIN USER
$sql = "SELECT u.*, r.role_name, d.dept_name 
        FROM users u 
        LEFT JOIN roles r ON u.role_id = r.role_id 
        LEFT JOIN departments d ON u.dept_id = d.dept_id 
        WHERE u.user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

function get_val($data, $key) {
    return isset($data[$key]) ? htmlspecialchars($data[$key]) : '';
}
?>

<style>
    .profile-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        height: 150px;
        border-radius: 20px 20px 0 0;
    }
    .avatar-wrapper { position: relative; margin-top: -75px; text-align: center; }
    .profile-avatar {
        width: 150px; height: 150px;
        border-radius: 50%; border: 5px solid #fff;
        object-fit: cover; background: #f8f9fa; box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    /* CSS MÀU VÀNG KIM CHO SUPER ADMIN */
    .badge-super-admin {
        background: linear-gradient(45deg, #FFD700, #FFA500) !important;
        color: #000 !important;
        border: 1px solid #ccac00;
    }
    .badge-admin { background-color: #198754 !important; } /* Màu xanh lá cho Admin */
    .badge-user { background-color: #0d6efd !important; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <?php echo $msg; ?>
            
            <div class="card border-0 shadow-lg rounded-4">
                <div class="profile-header"></div>
                
                <div class="card-body px-4">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="avatar-wrapper mb-4">
                            <?php 
                                $avatar_db = get_val($user, 'avatar');
                                $avatar_path = !empty($avatar_db) ? $BASE_URL . "uploads/" . $avatar_db : $BASE_URL . "assets/images/default-avatar.png";
                            ?>
                            <img src="<?php echo $avatar_path; ?>" class="profile-avatar shadow" id="previewter">
                            <div class="mt-2">
                                <label class="btn btn-sm btn-dark rounded-pill px-3 shadow-sm">
                                    <i class="fa-solid fa-camera me-1"></i> Thay ảnh
                                    <input type="file" name="avatar" style="display: none;" onchange="previewImage(this)">
                                </label>
                            </div>
                        </div>

                        <div class="text-center mb-5">
                            <h3 class="fw-bold mb-2"><?php echo get_val($user, 'full_name') ?: get_val($user, 'username'); ?></h3>
                            
                            <?php 
                                $role_raw = strtolower(get_val($user, 'role_name'));
                                // Logic đổi màu badge
                                if ($role_raw == 'super_admin') {
                                    $role_class = 'badge-super-admin';
                                    $role_text = 'SUPER ADMIN';
                                } elseif ($role_raw == 'admin') {
                                    $role_class = 'badge-admin';
                                    $role_text = 'ADMIN';
                                } else {
                                    $role_class = 'badge-user';
                                    $role_text = 'USER';
                                }
                            ?>
                            <span class="badge <?php echo $role_class; ?> rounded-pill px-3 py-2 text-uppercase shadow-sm">
                                <i class="fa-solid fa-user-shield me-1"></i> <?php echo $role_text; ?>
                            </span>

                            <?php if (!empty($user['dept_name'])): ?>
                                <span class="badge bg-info text-dark rounded-pill px-3 py-2 shadow-sm ms-2">
                                    <i class="fa-solid fa-graduation-cap me-1"></i> <?php echo get_val($user, 'dept_name'); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small">HỌ VÀ TÊN</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-signature text-muted"></i></span>
                                    <input type="text" name="full_name" class="form-control border-start-0 ps-0" value="<?php echo get_val($user, 'full_name'); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">SỐ ĐIỆN THOẠI</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo get_val($user, 'phone'); ?>" placeholder="Chưa cập nhật">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">PHÒNG BAN / KHOA</label>
                                <select name="dept_id" class="form-select" required>
                                    <option value="">-- Chọn đơn vị --</option>
                                    <?php 
                                        $depts = mysqli_query($conn, "SELECT * FROM departments");
                                        while($d = mysqli_fetch_assoc($depts)):
                                    ?>
                                        <option value="<?php echo $d['dept_id']; ?>" <?php echo ($d['dept_id'] == $user['dept_id']) ? 'selected' : ''; ?>>
                                            <?php echo $d['dept_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">ĐỊA CHỈ LIÊN HỆ</label>
                                <input type="text" name="address" class="form-control" value="<?php echo get_val($user, 'address'); ?>" placeholder="Địa chỉ nơi ở hiện tại">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">EMAIL HỆ THỐNG</label>
                                <input type="text" class="form-control bg-light" value="<?php echo get_val($user, 'email'); ?>" readonly>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" name="btn_update_profile" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow">
                                <i class="fa-solid fa-floppy-disk me-2"></i> LƯU THAY ĐỔI
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) { document.getElementById('previewter').src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include_once 'includes/footer.php'; ?>