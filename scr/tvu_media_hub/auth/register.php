<?php
include_once '../config/db.php'; // Đã bao gồm session_start() và kết nối $conn

// 1. Lấy danh sách Đơn vị từ DB để hiện lên Form
$sql_dept = "SELECT * FROM departments";
$result_dept = mysqli_query($conn, $sql_dept);

// 2. Xử lý khi bấm nút Đăng ký
if (isset($_POST['btn_register'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $password  = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // Đơn vị có thể để trống theo yêu cầu của bạn
    $dept_id   = !empty($_POST['dept_id']) ? $_POST['dept_id'] : "NULL";

    if ($password !== $confirm_password) {
        $error = "❌ Mật khẩu xác nhận không khớp!";
    } else {
        // Kiểm tra username hoặc email đã tồn tại chưa
        $check_sql = "SELECT user_id FROM users WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "❌ Tên đăng nhập hoặc Email đã được sử dụng!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Lấy role_id của 'user' từ bảng roles
            $res_role = mysqli_query($conn, "SELECT role_id FROM roles WHERE role_name = 'user'");
            $role_row = mysqli_fetch_assoc($res_role);
            $role_id  = $role_row['role_id'];

            // Thêm vào Database theo cấu trúc ERD
            $sql = "INSERT INTO users (username, password, email, full_name, role_id, dept_id) 
                    VALUES ('$username', '$hashed_password', '$email', '$full_name', $role_id, $dept_id)";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Đăng ký thành công! Hãy đăng nhập để bắt đầu.'); window.location.href='login.php';</script>";
            } else {
                $error = "Lỗi hệ thống: " . mysqli_error($conn);
            }
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-color: #f4f7f6; display: flex; flex-direction: column; min-height: 100vh; }
    .main-content { flex: 1; display: flex; align-items: center; padding: 40px 0; }
    .card { border-radius: 20px; overflow: hidden; }
    .btn-register { background-color: #0056b3; color: white; border: none; }
    .btn-register:hover { background-color: #004494; color: white; }
</style>

<div class="main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0 fw-bold">THÀNH VIÊN MỚI</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Họ và tên:</label>
                                    <input type="text" name="full_name" class="form-control" required placeholder="Nguyễn Văn A">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tên đăng nhập:</label>
                                    <input type="text" name="username" class="form-control" required placeholder="sv_trv_01">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email:</label>
                                <input type="email" name="email" class="form-control" required placeholder="email@tvu.edu.vn">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Đơn vị (Khoa/Phòng ban):</label>
                                <select name="dept_id" class="form-select">
                                    <option value="">-- Để trống (Thành viên tự do) --</option>
                                    <?php while($d = mysqli_fetch_assoc($result_dept)): ?>
                                        <option value="<?php echo $d['dept_id']; ?>"><?php echo $d['dept_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Mật khẩu:</label>
                                    <input type="password" name="password" class="form-control" required placeholder="********">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Xác nhận mật khẩu:</label>
                                    <input type="password" name="confirm_password" class="form-control" required placeholder="********">
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="btn_register" class="btn btn-register fw-bold py-2">HOÀN TẤT ĐĂNG KÝ</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <span>Đã có tài khoản? <a href="login.php" class="text-primary fw-bold text-decoration-none">Đăng nhập ngay</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>