<?php
// 1. Khai báo các thông số kết nối
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tvu_media"; 

// 2. Tạo kết nối
$conn = mysqli_connect($servername, $username, $password, $dbname);

// 3. Kiểm tra kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// 4. Thiết lập font chữ tiếng Việt
mysqli_set_charset($conn, "utf8mb4");

// 5. Khởi tạo Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 6. ĐỊNH NGHĨA ĐƯỜNG DẪN GỐC
$BASE_URL = "http://localhost/tvu_media_hub/"; 

/**
 * 7. Hàm ghi nhật ký hoạt động (Đã nâng cấp chống lỗi SQL)
 */
function save_log($conn, $user_id, $action, $details) {
    // Ép kiểu user_id về số nguyên (Nếu NULL/rỗng sẽ thành 0)
    $u_id = intval($user_id);
    
    // Làm sạch dữ liệu để tránh lỗi ký tự đặc biệt
    $act = mysqli_real_escape_string($conn, $action);
    $dt = mysqli_real_escape_string($conn, $details);
    
    // Câu lệnh SQL chuẩn
    $sql = "INSERT INTO activity_logs (user_id, action, details, created_at) 
            VALUES ($u_id, '$act', '$dt', NOW())";
            
    return mysqli_query($conn, $sql);
}
?>