-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 09, 2026 lúc 05:30 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `tvu_media`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'Đăng bài mới', 'Đã nộp bài: 1213 (không ảnh)', '2025-12-25 18:32:45'),
(3, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:04:29'),
(4, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:04:57'),
(5, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:05:08'),
(6, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:14:06'),
(7, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:17:39'),
(8, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:17:51'),
(9, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:18:00'),
(10, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:24:17'),
(11, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:24:26'),
(12, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:27:08'),
(13, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:27:33'),
(14, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:27:43'),
(15, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:57:43'),
(16, 1, 'Đăng bài mới', 'Đã nộp bài: 123 (không ảnh)', '2025-12-26 15:57:57'),
(17, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 15:58:37'),
(18, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 16:06:22'),
(19, 1, 'Đăng bài mới', 'Đã nộp bài: 1222152', '2025-12-26 16:06:37'),
(20, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 16:06:49'),
(21, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 16:08:56'),
(22, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 16:14:42'),
(23, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 16:15:20'),
(24, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 16:29:01'),
(25, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 16:39:50'),
(26, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 16:49:35'),
(27, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 16:52:28'),
(28, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:11:05'),
(29, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:11:33'),
(30, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:11:45'),
(31, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:15:04'),
(32, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:15:37'),
(33, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:15:59'),
(34, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:16:13'),
(35, 3, 'Quên mật khẩu', 'Đã gửi yêu cầu khôi phục mật khẩu lên Admin.', '2025-12-26 17:29:42'),
(36, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:29:49'),
(37, 3, 'Mật khẩu', 'Đã cập nhật mật khẩu mới thành công sau khi Admin phê duyệt.', '2025-12-26 17:30:17'),
(38, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:30:22'),
(39, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:30:43'),
(40, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:30:57'),
(41, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:35:15'),
(42, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:35:28'),
(43, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:40:48'),
(44, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:41:06'),
(45, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:45:58'),
(46, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:49:41'),
(47, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:53:51'),
(48, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:54:46'),
(49, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 17:56:00'),
(50, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 18:07:01'),
(51, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-26 18:08:27'),
(52, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 02:00:40'),
(53, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 02:00:53'),
(54, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 02:01:54'),
(55, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 02:02:14'),
(56, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 02:08:57'),
(57, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 02:09:56'),
(58, 1, 'Quên mật khẩu', 'Đã gửi yêu cầu khôi phục mật khẩu lên Admin.', '2025-12-27 02:11:57'),
(59, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 02:12:16'),
(60, 1, 'Mật khẩu', 'Đã cập nhật mật khẩu mới thành công sau khi Admin phê duyệt.', '2025-12-27 02:16:26'),
(61, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 02:16:33'),
(62, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 02:17:29'),
(63, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-27 14:32:02'),
(64, 5, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:24:10'),
(65, 4, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:32:47'),
(66, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:35:43'),
(67, 5, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:36:49'),
(68, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:37:32'),
(69, 5, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:41:16'),
(70, 4, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:42:00'),
(71, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:42:18'),
(72, 6, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:45:54'),
(73, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:46:19'),
(74, 4, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:53:40'),
(75, 6, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:54:41'),
(76, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:56:31'),
(77, 6, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-28 16:58:11'),
(78, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-29 05:11:36'),
(79, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-29 05:12:18'),
(80, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-29 05:15:49'),
(81, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2025-12-29 05:22:18'),
(82, 2, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2026-01-09 14:00:19'),
(83, 1, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2026-01-09 14:00:40'),
(84, 3, 'Đăng nhập', 'Đã đăng nhập vào hệ thống TVU Media Hub.', '2026-01-09 14:01:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `campaigns`
--

CREATE TABLE `campaigns` (
  `camp_id` int(11) NOT NULL,
  `camp_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `campaigns`
--

INSERT INTO `campaigns` (`camp_id`, `camp_name`, `description`, `thumbnail`, `start_date`, `end_date`, `status`, `created_by`, `created_at`) VALUES
(7, 'TVU Events 2025', '', '1766940544_images (9).jpg', '2025-01-01', '2025-12-31', 'Active', 2, '2025-12-28 16:49:04'),
(8, 'OLP-TVU 2025', '', '1766940654_images (10).jpg', '2025-12-02', '2025-12-10', 'Active', 2, '2025-12-28 16:50:54'),
(9, 'Chào mừng tân SV', '', '1766940701_k25_tvu-scaled.jpg', '2025-07-31', '2025-12-28', 'Active', 2, '2025-12-28 16:51:41'),
(10, 'Trà Vinh đứng đầu danh sách các thành phố có chất lượng không khí sạch nhất Đông Nam Á', '', '1766940792_TV.jpg', '2025-12-01', '2025-12-29', 'Active', 2, '2025-12-28 16:53:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`) VALUES
(1, 'Video'),
(2, 'Hình ảnh'),
(3, 'Bài viết'),
(4, 'Ấn phẩm thiết kế');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `departments`
--

CREATE TABLE `departments` (
  `dept_id` int(11) NOT NULL,
  `dept_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `departments`
--

INSERT INTO `departments` (`dept_id`, `dept_name`) VALUES
(1, 'Khoa Kỹ thuật & Công nghệ'),
(2, 'Khoa Kinh tế - Luật'),
(3, 'Phòng Truyền thông'),
(4, 'Phòng Đào tạo'),
(5, 'Phòng Công tác Sinh viên'),
(6, 'Ban Quản lý Ký túc xá');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `feedbacks`
--

CREATE TABLE `feedbacks` (
  `fb_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `fb_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `prod_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_requests`
--

CREATE TABLE `password_requests` (
  `request_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` enum('pending','processed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `prod_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `drive_link` varchar(255) DEFAULT NULL,
  `youtube_link` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('cho_duyet','da_duyet','tu_choi','cho_xoa') DEFAULT 'cho_duyet',
  `admin_note` text DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `cat_id` int(11) DEFAULT NULL,
  `camp_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`prod_id`, `title`, `description`, `thumbnail`, `drive_link`, `youtube_link`, `file_path`, `status`, `admin_note`, `views`, `cat_id`, `camp_id`, `user_id`, `created_at`, `is_featured`) VALUES
(16, 'TVU Đại học Xanh', '', 'thumb_1766771442_694ecaf207e0b.jpg', NULL, '', 'file_1766772476_339.jpg', 'da_duyet', NULL, 0, NULL, NULL, 1, '2025-12-26 17:50:42', 0),
(17, 'Miễn giảm môn học', '', '', NULL, '', 'file_1766771480_672.pdf', 'da_duyet', NULL, 0, NULL, NULL, 1, '2025-12-26 17:51:20', 0),
(18, 'Sơ đồ khu 1', '', 'thumb_1766771701_694ecbf5aeb0f.jpg', NULL, '', 'file_1766771509_995.pdf', 'da_duyet', NULL, 0, NULL, NULL, 1, '2025-12-26 17:51:49', 1),
(19, 'Sơ đồ khu 2', '', 'thumb_1766771442_694ecaf207e0b.jpg', NULL, '', 'file_1766771550_230.pdf', 'da_duyet', NULL, 0, NULL, NULL, 1, '2025-12-26 17:52:30', 1),
(20, 'Đại học Trà Vinh', '', '', NULL, 'https://youtu.be/4fDm3169TX0?si=UXyX_1eUpI37Alky', '', 'da_duyet', NULL, 0, NULL, NULL, 1, '2025-12-26 17:52:57', 0),
(21, 'TVU-ers', '', '', NULL, 'https://youtu.be/-I613d-Z6JE?si=yw3kUYLCd_L0W6M6', '', 'da_duyet', NULL, 0, NULL, NULL, 1, '2025-12-26 17:53:43', 0),
(25, 'Đại học Trà Vinh đón chuyên gia từ Đại học San Francisco đến thảo luận về các phương pháp giảng dạy ngôn ngữ hiện đại.', 'Đại diện cho Đại học Trà Vinh tham dự cuộc họp gồm có Phó Giáo sư, Tiến sĩ Huỳnh Ngọc Tài, Trưởng khoa Ngoại ngữ, Phó Giáo sư, Tiến sĩ Châu Thị Hoàng Hoa, Giám đốc Văn phòng Hợp tác Quốc tế, cùng các giảng viên đến từ Khoa Ngoại ngữ. Phiên làm việc tập trung vào việc trao đổi nội dung hợp tác chuyên môn và cung cấp tổng quan về cơ cấu và định hướng đào tạo của Trường Giáo dục thuộc Đại học San Francisco, bao gồm Khoa Giáo dục Quốc tế và Đa văn hóa (IME), Khoa Học tập và Giảng dạy (L&I), Khoa Tổ chức và Lãnh đạo (O&L), và Chương trình Lãnh đạo Giáo dục Công giáo (CEL).', 'thumb_1766939166_69515a1e151e7.jpg', NULL, '', 'file_1766939844_356.jpg', 'da_duyet', NULL, 0, 4, NULL, 5, '2025-12-28 16:26:06', 0),
(26, 'Sinh viên TVU tỏa sáng tại Cuộc thi Hùng biện tiếng Trung Quốc toàn quốc năm 2025', 'Vòng chung kết Cuộc thi hùng biện tiếng Trung toàn quốc đã diễn ra vào ngày 15 tháng 11 tại thành phố Đà Nẵng; sự kiện do Liên hiệp các tổ chức hữu nghị Đà Nẵng, Hội Hữu nghị Việt Nam - Trung Quốc chi nhánh Đà Nẵng, Tổng Lãnh sự quán Trung Quốc tại Đà Nẵng, VTV8 và các đơn vị liên quan phối hợp tổ chức.', 'thumb_1766939290_69515a9ac389b.jpg', NULL, '', 'file_1766940105_964.jpg', 'da_duyet', NULL, 0, 4, NULL, 5, '2025-12-28 16:28:10', 0),
(27, 'Kết nối tương lai: Chương trình hợp tác Việt Nam - Hàn Quốc tại TVU', '', '', NULL, 'https://youtu.be/0NeprsLyvO8?si=c0M7HTTVqjk5A_Sx', '', 'da_duyet', NULL, 0, 1, NULL, 5, '2025-12-28 16:31:39', 0),
(28, 'Đại học Trà Vinh tổ chức chuyến đi thực tế cho Hội nghị Thượng đỉnh Thanh niên Toàn cầu 2025', '', 'thumb_1766939607_69515bd71d687.jpg', NULL, '', 'file_1766940131_318.jpg', 'da_duyet', NULL, 0, 4, NULL, 4, '2025-12-28 16:33:27', 0),
(29, 'Lễ trao giải “Sáng kiến ​​Giáo dục Thông minh – Giải thưởng SEI” năm 2025', '', 'thumb_1766940370_69515ed2a2d14.jpg', NULL, '', 'file_1766940370_975.jpg', 'da_duyet', NULL, 0, 3, NULL, 6, '2025-12-28 16:46:10', 0),
(30, 'Khuôn viên xanh tại Đại học Trà Vinh', '', 'thumb_1766940846_695160ae78a80.jpg', NULL, '', 'file_1766940846_984.jpg', 'da_duyet', NULL, 0, 3, 10, 4, '2025-12-28 16:54:06', 0),
(31, 'Tượng đài “Toàn dân đứng lên, đoàn kết, lập công”', '', 'thumb_1766940871_695160c728cc3.jpg', NULL, '', 'file_1766940871_794.jpg', 'da_duyet', NULL, 0, 3, 10, 4, '2025-12-28 16:54:31', 0),
(32, 'Thành phố Trà Vinh (tên cũ) nhìn từ trên cao', '', 'thumb_1766940905_695160e9ab4bf.jpg', NULL, '', 'file_1766940905_527.jpg', 'da_duyet', NULL, 0, 3, 10, 6, '2025-12-28 16:55:05', 0),
(33, 'Cảnh quan xanh tươi tại Di sản Quốc gia Ao Ba Om', '', 'thumb_1766940943_6951610f39624.jpg', NULL, '', 'file_1766940943_390.jpg', 'da_duyet', NULL, 0, 3, 10, 6, '2025-12-28 16:55:43', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'super_admin'),
(2, 'admin'),
(3, 'user');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `is_locked` tinyint(4) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `reset_status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `username`, `full_name`, `password`, `email`, `phone`, `address`, `avatar`, `role_id`, `dept_id`, `is_locked`, `locked_until`, `reset_status`) VALUES
(1, 'huynhhuy123', 'Huỳnh Huy', '$2y$10$8hK9tIjX2fxGBkYwCc73lOE2X4eVbooWr1MEkybtQLW0rbHqoOiwu', '111111111@tvu.edu.vn', '0942451122', 'Đường C11, Trà Vinh', 'avatar_1_1766153214.png', 3, 1, 0, NULL, 0),
(2, 'admin123', 'ADMIN', '$2y$10$MS1wFtSBi2EBMN/xOquwb.1qOIIz0R/uKXZrTnK2kAueUnrNzUJOG', 'admin123@gmail.com', '', '', 'avatar_2_1766156169.jpg', 1, 3, 0, NULL, 0),
(3, 'napoleon1', 'Nã Phá Luân', '$2y$10$rggY4ddsknwTXhT8PN1exuGmTLhsFZTOsV2QRHV8f2Gt4Rjd3PfoS', 'napoleone@gmail.com', NULL, NULL, NULL, 2, 3, 0, NULL, 0),
(4, 'truongky1', 'Trương Kỳ', '$2y$10$GQY3jgZnT.b0d3XilkiwUu3IOAcm/ahMROkVHAKX2NCeZ9e84gUES', 'truongky@gmail.com', NULL, NULL, NULL, 3, 2, 0, NULL, 0),
(5, 'thieu1', 'Nguyễn Văn Thiệu ', '$2y$10$Ky1KCsV8m.unGBixfE9XSuhCKrZS28lIE/8lnOIAMM31qb3WFR.lq', '11172211@tvu.edu.vn', NULL, NULL, NULL, 3, 3, 0, NULL, 0),
(6, 'minh112', 'Lý Chính Minh', '$2y$10$XjOraFDLNl96l08MDDEf8O6OXv81QB5jvU3eOjCS2n1YTvEIA6zCa', 'minh122@gmail.com', NULL, NULL, NULL, 3, NULL, 0, NULL, 0);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`camp_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Chỉ mục cho bảng `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`dept_id`);

--
-- Chỉ mục cho bảng `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`fb_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `prod_id` (`prod_id`);

--
-- Chỉ mục cho bảng `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Chỉ mục cho bảng `password_requests`
--
ALTER TABLE `password_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`prod_id`),
  ADD KEY `cat_id` (`cat_id`),
  ADD KEY `camp_id` (`camp_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `dept_id` (`dept_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT cho bảng `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `camp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `departments`
--
ALTER TABLE `departments`
  MODIFY `dept_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `fb_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `password_requests`
--
ALTER TABLE `password_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `prod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `campaigns`
--
ALTER TABLE `campaigns`
  ADD CONSTRAINT `campaigns_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `feedbacks_ibfk_2` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`);

--
-- Các ràng buộc cho bảng `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`cat_id`) REFERENCES `categories` (`cat_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`camp_id`) REFERENCES `campaigns` (`camp_id`),
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
