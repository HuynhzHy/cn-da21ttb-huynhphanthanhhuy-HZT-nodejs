<?php 
include_once '../config/db.php'; 

// Kiểm tra nếu session chưa chạy thì mới start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = "";
$status_type = ""; 
$show_reset_form = false;

// XỬ LÝ GỬI YÊU CẦU PHÊ DUYỆT
if (isset($_POST['btnAction'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "SELECT * FROM users WHERE username='$username' AND email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $current_status = $user['reset_status'];

        if ($current_status == 0) {
            mysqli_query($conn, "UPDATE users SET reset_status = 1 WHERE user_id = " . $user['user_id']);
            
            // --- GHI LOG: GỬI YÊU CẦU ---
            save_log($conn, $user['user_id'], 'Quên mật khẩu', 'Đã gửi yêu cầu khôi phục mật khẩu lên Admin.');
            
            $message = "Yêu cầu của bạn đã được gửi, xin vui lòng đợi Admin phê duyệt!";
            $status_type = "warning";
        } elseif ($current_status == 1) {
            $message = "Yêu cầu của bạn đang được gửi, xin vui lòng đợi!";
            $status_type = "warning";
        } elseif ($current_status == 2) {
            $show_reset_form = true;
            $_SESSION['reset_user_id'] = $user['user_id'];
        } elseif ($current_status == 3) {
            $message = "Yêu cầu của bạn đã bị từ chối. Vui lòng liên hệ trực tiếp Admin!";
            $status_type = "danger";
        }
    } else {
        $message = "Thông tin Username hoặc Email không khớp!";
        $status_type = "danger";
    }
}

// XỬ LÝ CẬP NHẬT MẬT KHẨU MỚI
if (isset($_POST['btnUpdate'])) {
    $new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
    $uid = $_SESSION['reset_user_id'];
    
    $sql_up = "UPDATE users SET password='$new_pass', reset_status = 0 WHERE user_id = $uid";
    if (mysqli_query($conn, $sql_up)) {
        
        // --- GHI LOG: ĐỔI MẬT KHẨU THÀNH CÔNG ---
        save_log($conn, $uid, 'Mật khẩu', 'Đã cập nhật mật khẩu mới thành công sau khi Admin phê duyệt.');
        
        $message = "Đổi mật khẩu thành công! Chuyển hướng sau 2s...";
        $status_type = "success";
        header("refresh:2; url=login.php");
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - TVU MediaHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Roboto', sans-serif; margin: 0; }
        .card-forgot { width: 420px; border: none; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,86,179,0.15); background: #fff; overflow: hidden; }
        .header-box { background: #0056b3; color: white; padding: 30px; text-align: center; border-bottom: 5px solid #ffc107; }
        .btn-tvu { background: linear-gradient(to right, #0056b3, #007bff); color: #fff; border: none; font-weight: 600; padding: 12px; transition: 0.3s; border-radius: 10px; }
        .btn-tvu:hover { background: linear-gradient(to right, #004494, #0056b3); transform: translateY(-2px); color: white; }
        .form-control { border-radius: 10px; background: #f8f9fa; border: 1px solid #dee2e6; }
        .hover-blue:hover { color: #004494 !important; }
    </style>
</head>
<body>

<div class="card card-forgot">
    <div class="header-box">
        <i class="fa-solid fa-shield-halved fa-3x mb-3 text-warning"></i>
        <h4 class="fw-bold mb-0">XÁC THỰC TÀI KHOẢN</h4>
    </div>

    <div class="p-4">
        <?php if($message): ?>
            <div class="alert alert-<?php echo $status_type; ?> text-center small fw-bold shadow-sm" style="border-radius: 10px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if(!$show_reset_form): ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="small fw-bold text-secondary ms-1">Tên đăng nhập (Username)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-user text-primary"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Nhập tên tài khoản" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="small fw-bold text-secondary ms-1">Email liên kết</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-primary"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="example@tvu.edu.vn" required>
                    </div>
                </div>
                <button type="submit" name="btnAction" class="btn btn-tvu w-100 shadow-sm">
                    GỬI YÊU CẦU PHÊ DUYỆT <i class="fa-solid fa-paper-plane ms-2"></i>
                </button>
            </form>
        <?php else: ?>
            <form method="POST">
                <div class="mb-4 text-center">
                    <span class="badge bg-success py-2 px-3 shadow-sm" style="border-radius: 20px;">
                        <i class="fa-solid fa-user-check me-2"></i> Đã được Admin phê duyệt
                    </span>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-secondary ms-1">Mật khẩu mới</label>
                    <input type="password" name="new_pass" class="form-control py-2" placeholder="Tối thiểu 6 ký tự" required minlength="6">
                </div>
                <button type="submit" name="btnUpdate" class="btn btn-warning w-100 fw-bold shadow-sm py-2">
                    CẬP NHẬT MẬT KHẨU
                </button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="login.php" class="text-decoration-none small text-primary fw-bold hover-blue">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại Đăng nhập
            </a>
        </div>
    </div>
</div>

</body>
</html>