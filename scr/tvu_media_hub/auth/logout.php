<?php
// Bắt đầu session để có thể hủy nó
session_start();

// 1. Xóa sạch các biến session
$_SESSION = array();

// 2. Hủy hoàn toàn session
session_destroy();

// 3. Chuyển hướng thẳng về trang chủ của đồ án
// Ghi rõ tên thư mục đồ án ở đây để tránh lỗi Not Found
header("Location: /tvu_media_hub/index.php");
exit();
?>