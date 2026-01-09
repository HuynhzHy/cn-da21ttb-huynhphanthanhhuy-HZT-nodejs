<?php
include_once '../config/db.php'; 

if (isset($_POST['btn_login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Truy vấn thông tin user
    $sql = "SELECT u.*, r.role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.role_id 
            WHERE u.username = '$username'";
    
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // 1. Kiểm tra mật khẩu
        if (password_verify($password, $row['password']) || $password == $row['password']) {
            
            $is_banned = false;

            // 2. Kiểm tra khóa tài khoản
            if ($row['is_locked'] == 1) {
                if ($row['locked_until'] == NULL) {
                    $is_banned = true;
                    $error = "❌ Tài khoản của bạn đã bị KHÓA VĨNH VIỄN!";
                } else {
                    $unlock_time = strtotime($row['locked_until']);
                    if ($unlock_time > time()) {
                        $is_banned = true;
                        $error = "⚠️ Tài khoản đang bị tạm khóa đến: <b>".date('H:i d/m/Y', $unlock_time)."</b>";
                    } else {
                        mysqli_query($conn, "UPDATE users SET is_locked = 0, locked_until = NULL WHERE user_id = " . $row['user_id']);
                    }
                }
            }

            // 3. Nếu không bị khóa -> Lưu Session và Log
            if (!$is_banned) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                
                $_SESSION['user_id']   = $row['user_id'];
                $_SESSION['username']  = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['role_id']   = $row['role_id']; 
                $_SESSION['role_name'] = $row['role_name'];
                $_SESSION['dept_id']   = $row['dept_id'];

                // Ghi nhật ký (Dùng đúng biến $row)
                if (function_exists('save_log')) {
                    save_log($conn, $row['user_id'], 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.');
                }

                // Điều hướng
                if ($row['role_name'] == 'super admin') {
                    header("Location: " . $BASE_URL . "admin/statistics.php");
                } elseif ($row['role_name'] == 'admin') {
                    header("Location: " . $BASE_URL . "admin/approve_products.php");
                } else {
                    header("Location: " . $BASE_URL . "index.php");
                }
                exit();
            }
        } else {
            $error = "❌ Mật khẩu không chính xác!";
        }
    } else {
        $error = "❌ Tài khoản không tồn tại!";
    }
}
include '../includes/header.php'; 
?>

<style>
    body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 0; }
    .card { border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 86, 179, 0.15) !important; width: 100%; max-width: 420px; border-top: 5px solid #0056b3; background: #ffffff; }
    .btn-tvu { background: linear-gradient(to right, #0056b3, #007bff); color: white; border: none; border-radius: 12px; transition: all 0.3s; font-weight: 700; }
    .btn-tvu:hover { background: linear-gradient(to right, #004494, #0056b3); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 86, 179, 0.3); }
    .input-group-text { background-color: #f8f9fa; color: #0056b3; border-radius: 12px 0 0 12px !important; }
    .form-control { border-radius: 0 12px 12px 0 !important; background-color: #f8f9fa !important; padding: 12px; }
    .text-tvu-blue { color: #0056b3 !important; font-weight: 800; }
    .text-tvu-gold { color: #ffc107 !important; font-weight: 800; }
</style>

<div class="main-content">
    <div class="card p-4 p-md-5">
        <div class="text-center mb-4">
            <h3 class="fw-bold"><span class="text-tvu-blue">TVU Media</span><span class="text-tvu-gold">Hub</span></h3>
            <div style="width: 60px; height: 3px; background: #ffc107; margin: 8px auto 15px; border-radius: 10px;"></div>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger text-center py-2 small mb-4" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                    <input type="text" name="username" class="form-control" required placeholder="Nhập tên tài khoản">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-shield-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="********">
                    <span class="input-group-text" id="togglePassword" style="cursor:pointer; border-radius: 0 12px 12px 0 !important;">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
            </div>
            <button type="submit" name="btn_login" class="btn btn-tvu w-100 py-3">ĐĂNG NHẬP NGAY</button>
        </form>

        <div class="text-center mt-4 pt-2">
            <p class="mb-2"><small class="text-muted">Chưa có tài khoản? <a href="register.php" class="fw-bold text-primary text-decoration-none">Đăng ký ngay</a></small></p>
            <p class="mb-0"><a href="forgot_password.php" class="text-decoration-none small text-muted">Quên mật khẩu?</a></p>
        </div>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });
</script>

<?php include '../includes/footer.php'; ?>