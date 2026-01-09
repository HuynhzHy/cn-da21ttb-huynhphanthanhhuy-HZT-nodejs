<?php
include_once '../config/db.php';
include_once '../includes/header.php';

$role = isset($_SESSION['role_name']) ? strtolower($_SESSION['role_name']) : '';
if (!isset($_SESSION['user_id']) || ($role !== 'super_admin' && $role !== 'admin')) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Bạn không có quyền truy cập!</div></div>";
    include_once '../includes/footer.php'; exit();
}

// Xử lý Duyệt/Từ chối
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    mysqli_query($conn, "UPDATE products SET status = 'Approved', admin_note = NULL WHERE prod_id = $id");
    echo "<script>alert('Đã duyệt bài!'); window.location.href='./approve_posts.php';</script>"; exit();
}
if (isset($_POST['btn_reject'])) {
    $id = intval($_POST['prod_id']);
    $note = mysqli_real_escape_string($conn, $_POST['admin_note']);
    mysqli_query($conn, "UPDATE products SET status = 'Rejected', admin_note = '$note' WHERE prod_id = $id");
    echo "<script>alert('Đã từ chối bài!'); window.location.href='./approve_posts.php';</script>"; exit();
}

$sql = "SELECT p.*, u.full_name FROM products p LEFT JOIN users u ON p.user_id = u.user_id 
        WHERE p.status = 'Pending' OR p.status = 'pending' ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container py-5">
    <h3 class="fw-bold text-success mb-4"><i class="fa-solid fa-clipboard-check me-2"></i>DANH SÁCH CHỜ DUYỆT</h3>
    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-dark text-white">
                <tr>
                    <th class="ps-4">Sản phẩm (Bấm tên để xem)</th>
                    <th>Người đăng</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="../uploads/<?php echo $row['thumbnail']; ?>" class="rounded-2 me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <button type="button" class="btn btn-link p-0 fw-bold text-decoration-underline border-0 shadow-none btn-preview" 
                                            data-info='<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>'>
                                        <?php echo htmlspecialchars($row['title']); ?>
                                    </button>
                                    <br><small class="text-muted italic">Click để xem trước nội dung</small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <a href="./approve_posts.php?approve=<?php echo $row['prod_id']; ?>" class="btn btn-success btn-sm px-3" onclick="return confirm('Duyệt bài?')">Duyệt</a>
                                <button class="btn btn-warning btn-sm px-3 text-white btn-reject-modal" 
                                        data-id="<?php echo $row['prod_id']; ?>" 
                                        data-title="<?php echo htmlspecialchars($row['title']); ?>">Lỗi</button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="v_title"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="v_youtube" class="mb-3 d-none">
                    <div class="ratio ratio-16x9"><iframe id="yt_iframe" src="" allowfullscreen class="rounded shadow"></iframe></div>
                </div>
                <div class="text-center mb-3">
                    <img id="v_thumbnail" src="" class="img-fluid rounded shadow-sm" style="max-height: 350px;">
                </div>
                <div class="bg-light p-3 rounded">
                    <label class="fw-bold text-muted small">MÔ TẢ:</label>
                    <p id="v_desc" class="mb-0 mt-1" style="white-space: pre-wrap;"></p>
                </div>
                <div id="v_file" class="mt-3"></div>
            </div>
            <div class="modal-footer bg-light" id="v_footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="./approve_posts.php" method="POST" class="modal-content">
            <div class="modal-header bg-warning text-white"><h5>Phản hồi lỗi</h5></div>
            <div class="modal-body">
                <input type="hidden" name="prod_id" id="modal_id">
                <p>Bài đăng: <strong id="modal_title"></strong></p>
                <textarea name="admin_note" class="form-control" rows="4" required placeholder="Nhập lý do từ chối..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="submit" name="btn_reject" class="btn btn-warning text-white w-100 fw-bold">Xác nhận từ chối</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý khi bấm vào tên bài để XEM TRƯỚC
    const previewButtons = document.querySelectorAll('.btn-preview');
    previewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-info'));
            
            document.getElementById('v_title').innerText = data.title;
            document.getElementById('v_desc').innerText = data.description || 'Không có mô tả.';
            document.getElementById('v_thumbnail').src = '../uploads/' + data.thumbnail;

            // Xử lý Youtube
            const ytArea = document.getElementById('v_youtube');
            const ytIframe = document.getElementById('yt_iframe');
            if(data.youtube_link) {
                let videoId = '';
                if(data.youtube_link.includes('v=')) videoId = data.youtube_link.split('v=')[1].split('&')[0];
                else videoId = data.youtube_link.split('/').pop();
                ytIframe.src = "https://www.youtube.com/embed/" + videoId;
                ytArea.classList.remove('d-none');
            } else {
                ytIframe.src = ""; ytArea.classList.add('d-none');
            }

            // File đính kèm
            const fileArea = document.getElementById('v_file');
            if(data.file_path) {
                fileArea.innerHTML = `<div class="alert alert-info small"><i class="fa-solid fa-paperclip me-2"></i>File kèm: <a href="../uploads/${data.file_path}" target="_blank" class="fw-bold">Tải về kiểm tra</a></div>`;
            } else { fileArea.innerHTML = ''; }

            // Nút Duyệt trong Modal
            document.getElementById('v_footer').innerHTML = `
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Đóng</button>
                <a href="./approve_posts.php?approve=${data.prod_id}" class="btn btn-success rounded-pill px-4 fw-bold">Duyệt bài này</a>
            `;

            new bootstrap.Modal(document.getElementById('viewModal')).show();
        });
    });

    // Xử lý khi bấm nút LỖI (Từ chối)
    const rejectButtons = document.querySelectorAll('.btn-reject-modal');
    rejectButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal_id').value = this.getAttribute('data-id');
            document.getElementById('modal_title').innerText = this.getAttribute('data-title');
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        });
    });
});
//   XỬ LÝ ĐÁNH DẤU NỔI BẬT
if (isset($_GET['toggle_featured'])) {
    $pid = intval($_GET['toggle_featured']);
    $current = intval($_GET['current']);
    $new_val = ($current == 1) ? 0 : 1; // Đảo ngược trạng thái
    
    mysqli_query($conn, "UPDATE products SET is_featured = $new_val WHERE prod_id = $pid");
    
    // Quay lại trang trước đó
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
</script>

<?php include_once '../includes/footer.php'; ?>