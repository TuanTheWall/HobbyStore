-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 04, 2026 lúc 09:12 AM
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
-- Cơ sở dữ liệu: `hobbystore`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `adminname` varchar(50) NOT NULL,
  `password` varchar(20) NOT NULL,
  `id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`adminname`, `password`, `id`) VALUES
('rioshortking', 'RBS01', 'AD01'),
('windsenpai', 'RBS02', 'AD02'),
('middlelight', 'RBS03', 'AD03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_settings`
--

CREATE TABLE `admin_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) DEFAULT NULL,
  `setting_value` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_settings`
--

INSERT INTO `admin_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'alert_threshold', '20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `cart_id` varchar(50) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `created_at`) VALUES
('CART_69abe5cf21d96', 'CM01', '2026-03-07 15:46:07'),
('CART_69abf8dce4bcd', 'CM03', '2026-03-07 17:07:24'),
('CART_69ac02e2b7b17', 'CM05', '2026-03-07 17:50:10'),
('CART_69d0b70cd58f6', 'CM06', '2026-04-04 14:00:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_item`
--

CREATE TABLE `cart_item` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_item`
--

INSERT INTO `cart_item` (`cart_item_id`, `cart_id`, `product_id`, `quantity`) VALUES
(1, 'CART_69abe5cf21d96', 'RG-003', 5),
(2, 'CART_69abe5cf21d96', 'HG-004', 1),
(27, 'CART_69abf8dce4bcd', 'SD-01', 1),
(30, 'CART_69d0b70cd58f6', 'vvvv', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_temp`
--

CREATE TABLE `cart_temp` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `cart_item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `name` varchar(50) NOT NULL,
  `ID` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`name`, `ID`, `description`) VALUES
('HG', 1, 'Mô hình có chi tiết bề mặt thấp dễ lắp có kích thước dao động từ 12cm-15cm'),
('RG', 2, 'Mô hình có chi tiết thấp cao khó lắp có kích thước dao động từ 12cm-15cm'),
('MG', 3, 'Mô hình có chi tiết thấp cao khó lắp có kích thước dao động từ 18cm-21cm'),
('PG', 4, 'Mô hình có chi tiết thấp cao khó lắp có kích thước dao động từ 30cm-32cm'),
('Figure', 5, 'Mô hình tỉnh không cần tự lắp được dựa các nhân vật 2d'),
('SD', 6, 'mini hg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `customer_id` varchar(10) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `register_date` date NOT NULL,
  `status` enum('Hoạt động','Bị khóa') DEFAULT 'Hoạt động',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`customer_id`, `phone`, `email`, `address`, `register_date`, `status`, `created_at`, `username`, `password`) VALUES
('CM01', '0912345678', 'vana@gmail.com', 'HCM', '2024-01-01', 'Bị khóa', '2026-02-21 14:47:59', 'CE5323', 'Xy7!pQ9@mR2t'),
('CM02', '0987654321', 'thib@example.com', 'TP. Hồ Chí Minh', '2024-02-05', 'Bị khóa', '2026-02-21 14:47:59', 'CM001-Nexus', 'Blue$Sky#8800'),
('CM03', '0978123456', 'minhc@example.com', 'Đà Nẵng', '2024-03-10', 'Hoạt động', '2026-02-21 14:47:59', 'CM001.Alpha', 'K3pt@Safe_2025'),
('CM04', '0965123789', 'thid@example.com', 'Cần Thơ', '2024-04-22', 'Hoạt động', '2026-02-21 14:47:59', 'CM001_Shadow', 'vB*6nH&2mL!9'),
('CM05', '0933456789', 'vane@example.com', 'Hải Phòng', '2024-05-02', 'Hoạt động', '2026-02-21 14:47:59', 'CM001_Legend', 'C0ffee_Lover!26'),
('CM06', '88888888888', 'ilovecake888@gmail.com', 'Trái Đất', '2026-04-04', 'Hoạt động', '2026-04-04 07:00:04', 'user01', '88888888');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `history`
--

CREATE TABLE `history` (
  `ProductID` varchar(255) NOT NULL,
  `export_num` int(11) DEFAULT NULL,
  `import_num` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `update_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `history`
--

INSERT INTO `history` (`ProductID`, `export_num`, `import_num`, `quantity`, `update_date`) VALUES
('PG001', 0, 10, 0, '2026-03-13'),
('MG-001', 0, 5, 5, '2026-03-13'),
('MG-001', 0, 5, 5, '2026-03-13'),
('HG-007', 0, 5, 5, '2026-03-19'),
('HG-007', 0, 5, 5, '2026-03-19'),
('AG-001', 0, 4, 4, '2026-03-19'),
('HG-004', 0, 5, 5, '2026-03-19'),
('HG-004', 0, 5, 5, '2026-03-19'),
('HG-002', 0, 5, 5, '2026-03-19'),
('HG-002', 0, 5, 5, '2026-03-19'),
('FG-005', 1, 0, -1, '2026-03-19'),
('FG-002', NULL, 5, 5, '2026-03-20'),
('FG-002', 0, 5, 5, '2026-03-20'),
('FG-002', NULL, 1, 1, '2026-03-20'),
('FG-002', 1, 0, -1, '2026-03-20'),
('FG-005', NULL, 10, 10, '0000-00-00'),
('FG-005', 0, 10, 10, '2026-03-27'),
('RG-003', NULL, 1, 1, '2026-03-27'),
('FG-005', NULL, 1, 1, '2026-03-27'),
('RG-003', 1, 0, -1, '2026-03-27'),
('FG-005', 1, 0, -1, '2026-03-27'),
('zzz', NULL, 10, 10, '2026-03-01'),
('zzz', 0, 10, 10, '2026-03-27'),
('zzz', NULL, 3, 3, '2026-03-27'),
('zzz', 3, 0, -3, '2026-03-27'),
('vvvv', NULL, 10, 10, '2026-03-27'),
('vvvv', 0, 10, 10, '2026-03-27'),
('rrrrr', NULL, 10, 10, '2026-03-27'),
('rrrrr', 0, 10, 10, '2026-03-27'),
('SD-01', NULL, 10, 10, '2026-03-27'),
('SD-01', 0, 10, 10, '2026-03-27'),
('SD-01', NULL, 1, 1, '2026-03-27'),
('SD-01', 1, 0, -1, '2026-03-27'),
('SD-01', NULL, 2, 2, '2026-03-27'),
('rrrrr', NULL, 3, 3, '2026-03-27'),
('acn', NULL, 10, 10, '2026-04-02'),
('acn', 0, 10, 10, '2026-04-02'),
('acn', NULL, 10, 10, '2026-04-03'),
('acn', 0, 10, 10, '2026-04-02'),
('acn', NULL, 10, 10, '2026-04-03'),
('acn', NULL, 10, 10, '2026-04-02'),
('acn', 10, 0, -10, '2026-04-02'),
('acn', NULL, 1, 1, '2026-04-02'),
('acn', 1, 0, -1, '2026-04-02'),
('RG-003', NULL, 5, 5, '2026-04-01'),
('FG-005', NULL, 5, 5, '2026-04-03'),
('HG-002', NULL, 5, 5, '2026-04-04'),
('vvvv', NULL, 1, 1, '2026-04-03'),
('vvvv', 1, 0, -1, '2026-04-03'),
('vvvv', NULL, 1, 1, '2026-04-03'),
('vvvv', 1, 0, -1, '2026-04-03'),
('RG-002', NULL, 5, 5, '2026-04-04'),
('RG-001', NULL, 5, 5, '2026-04-04'),
('FG-003', NULL, 5, 5, '2026-04-04'),
('FG-004', NULL, 5, 5, '2026-04-04'),
('FG-001', NULL, 2, 2, '0000-00-00'),
('PG-001', 1, 0, -1, '2026-03-19'),
('FG-001', NULL, 1, 1, '2026-04-05'),
('FG-001', NULL, 4, 4, '2026-04-05'),
('SD-01', NULL, 2, 2, '2026-04-04'),
('SD-01', NULL, 1, 1, '2026-04-04'),
('SD-01', 1, 0, -1, '2026-04-04'),
('SD-01', 2, 0, -2, '2026-04-04');

--
-- Bẫy `history`
--
DELIMITER $$
CREATE TRIGGER `history_quantity` BEFORE INSERT ON `history` FOR EACH ROW BEGIN

SET NEW.quantity = IFNULL(NEW.import_num,0) - IFNULL(NEW.export_num,0);

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `ProductID` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `seller_id`, `ProductID`, `quantity`) VALUES
(1, 1, 'HG-001', 20),
(2, 1, 'HG-002', 20),
(3, 1, 'RG-001', 10),
(4, 2, 'FG-001', 5),
(5, 2, 'MG-001', 8);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id_order` varchar(20) NOT NULL,
  `customer_id` varchar(10) NOT NULL,
  `order_date` date NOT NULL,
  `status` enum('Chờ xử lý','Đã xác nhận','Đang giao','Đã giao','Đã huỷ') DEFAULT 'Chờ xử lý',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total` int(11) DEFAULT NULL,
  `receiver_name` varchar(100) DEFAULT NULL,
  `receiver_phone` varchar(20) DEFAULT NULL,
  `receiver_address` varchar(255) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id_order`, `customer_id`, `order_date`, `status`, `created_at`, `total`, `receiver_name`, `receiver_phone`, `receiver_address`, `payment_method`, `note`) VALUES
('DH001', 'CM01', '2026-02-21', 'Đang giao', '2026-03-05 14:38:56', NULL, NULL, NULL, NULL, NULL, NULL),
('DH003', 'CM05', '2026-03-08', 'Đang giao', '2026-03-07 18:34:26', 2600000, 'CM001_Legend', '0933456789', 'Hải Phòng', 'COD', NULL),
('DH004', 'CM03', '2026-03-19', 'Đang giao', '2026-03-19 13:07:45', 4160000, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'COD', NULL),
('DH005', 'CM03', '2026-03-19', 'Đã giao', '2026-03-19 13:08:56', 1430000, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'COD', NULL),
('DH006', 'CM03', '2026-03-19', 'Đã giao', '2026-03-19 14:16:07', 520000, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'COD', NULL),
('DH007', 'CM03', '2026-03-20', 'Đã giao', '2026-03-20 07:17:48', 939250, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'BANK', NULL),
('DH008', 'CM03', '2026-03-27', 'Đã giao', '2026-03-27 07:58:59', 1425667, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'COD', NULL),
('DH009', 'CM03', '2026-03-27', 'Đã giao', '2026-03-27 08:11:41', 507, 'CM001.Alpha', '0978123456', 'ttt', 'COD', NULL),
('DH010', 'CM03', '2026-03-27', 'Đã giao', '2026-03-27 15:42:27', 169, 'CM001.Alpha', '0978123436', 'Tiểu Vương Quốc Thanh Hóa', 'BANK', NULL),
('DH011', 'CM03', '2026-03-27', 'Chờ xử lý', '2026-03-27 16:27:33', 338, 'Tes0t', '2222222222', 'Super Earth', 'COD', NULL),
('DH012', 'CM03', '2026-03-27', 'Chờ xử lý', '2026-03-27 16:30:47', 390, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'COD', NULL),
('DH013', 'CM03', '2026-04-02', 'Đã giao', '2026-04-02 07:47:22', 1300, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'COD', NULL),
('DH014', 'CM03', '2026-04-02', 'Đã giao', '2026-04-02 08:00:28', 130, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'COD', NULL),
('DH015', 'CM03', '2026-04-03', 'Đã giao', '2026-04-03 08:33:35', 130, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'BANK', NULL),
('DH016', 'CM03', '2026-04-03', 'Đã giao', '2026-04-03 08:35:33', 130, 'CM001.Alpha', '0978123456', 'Đà Nẵng', 'COD', NULL),
('DH017', 'CM06', '2026-04-04', 'Đã giao', '2026-04-04 07:05:21', 260, 'Lông nách chua lè', '7984728572472', 'Quán cơm mắc nhất quận 1 gần phố đi bộ', 'COD', NULL),
('DH018', 'CM06', '2026-04-04', 'Đã giao', '2026-04-04 07:06:15', 130, 'Khế sầu riêng', '88888888888', 'Trái Đất', 'COD', NULL);

--
-- Bẫy `orders`
--
DELIMITER $$
CREATE TRIGGER `trg_export_on_delivered` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    IF NEW.status = 'Đã giao' AND OLD.status != 'Đã giao' THEN

        UPDATE product_list p
        JOIN order_item oi ON p.ProductID = oi.ProductID
        SET p.Quantity = p.Quantity - oi.quantity
        WHERE oi.id_order = NEW.id_order;

        INSERT INTO history (ProductID, import_num, export_num, update_date)
        SELECT oi.ProductID, 0, oi.quantity, NEW.order_date
        FROM order_item oi
        WHERE oi.id_order = NEW.id_order
        ON DUPLICATE KEY UPDATE export_num = export_num + oi.quantity;

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_item`
--

CREATE TABLE `order_item` (
  `orderID` int(11) NOT NULL,
  `id_order` varchar(20) NOT NULL,
  `ProductID` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_item`
--

INSERT INTO `order_item` (`orderID`, `id_order`, `ProductID`, `quantity`) VALUES
(1, 'DH001', 'HG-001', 2),
(11, 'DH001', 'HG-005', 2),
(14, 'DH003', 'MG-001', 1),
(15, 'DH003', 'PG-001', 1),
(16, 'DH004', 'HG-003', 6),
(17, 'DH004', 'FG-005', 2),
(18, 'DH005', 'PG-001', 1),
(19, 'DH006', 'FG-005', 1),
(20, 'DH007', 'FG-002', 1),
(21, 'DH008', 'RG-003', 1),
(22, 'DH008', 'FG-005', 1),
(24, 'DH010', 'SD-01', 1),
(25, 'DH011', 'SD-01', 2),
(26, 'DH012', 'rrrrr', 3),
(29, 'DH015', 'vvvv', 1),
(30, 'DH016', 'vvvv', 1),
(31, 'DH017', 'SD-01', 2),
(32, 'DH018', 'SD-01', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_list`
--

CREATE TABLE `product_list` (
  `ProductID` varchar(255) NOT NULL,
  `ProductName` varchar(255) DEFAULT NULL,
  `Grade` varchar(255) DEFAULT NULL,
  `Producer` varchar(255) DEFAULT NULL,
  `Product_source` longtext DEFAULT NULL,
  `Product_description` longtext DEFAULT NULL,
  `Product_detail` text DEFAULT NULL,
  `Product_image` varchar(255) DEFAULT NULL,
  `Price` int(11) DEFAULT NULL,
  `Profit` decimal(5,2) DEFAULT NULL,
  `cost_price` decimal(12,2) DEFAULT 0.00,
  `Quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_list`
--

INSERT INTO `product_list` (`ProductID`, `ProductName`, `Grade`, `Producer`, `Product_source`, `Product_description`, `Product_detail`, `Product_image`, `Price`, `Profit`, `cost_price`, `Quantity`) VALUES
('FG-001', 'Hatsune Miku - Piapro Characters - Banpresto Evolve - Classical Tuning -Swan Lake', 'Figure', 'Bandai', 'Hatsune Miku Evolve Classical Tuning', 'Hatsune Miku – Banpresto Evolve – Classical Tuning: Swan Lake là mẫu figure thuộc dòng Piapro Characters, tái hiện hình ảnh nàng diva ảo trong tạo hình thiên nga trắng đầy thanh lịch và mềm mại, lấy cảm hứng từ vở ballet kinh điển.', 'Dòng: Prize Figure\r\nChất Liệu : PVC, ABS\r\nChiều Cao: 210mm', 'Swan.jpg', 1300833, 0.30, 1000641.02, 12),
('FG-002', 'Banpresto - Umamusume: Pretty Derby - Tamamo Cross', 'Figure', 'Banpresto', 'Umamusume: Pretty Derby', 'Một cô gái nhỏ nhắn với chất giọng Kansai đặc trưng. Dù vóc dáng nhỏ bé, cô lại tràn đầy năng lượng và luôn sẵn sàng hành động — dù là trên đường đua hay trong cuộc sống hằng ngày. Cô lớn lên trong hoàn cảnh không mấy thuận lợi, nhưng sức mạnh của cô đến từ tinh thần kiên cường — hiếm khi thấy cô nản lòng trước bất cứ điều gì. Cô và Oguri Cap, người có khiếu hài hước tự nhiên, tạo thành một cặp đôi tấu hài hoàn hảo.', 'Chiều cao: ~17cm\r\nChất liệu: PVC\r\nKhông yêu cầu lắp ráp', 'ph-11134207-7rasd-m3qdzaudvacv5e.jpg', 722500, 0.30, 555769.23, 14),
('FG-003', 'Hatsune Miku Project Diva Mega 39\'s - Luminasta - Project Diva 15th Ver', 'Figure', 'SEGA', 'Hatsune Miku Project Diva Mega 39\'s', 'Hatsune Miku Project Diva 15th Ver. Luminasta - Project Diva Mega 39\'s – figure kỷ niệm 15 năm Project Diva của SEGA, được thiết kế dựa trên Hatsune Miku từ tựa game Hatsune Miku: Project Diva Mega39’s. Đây là sản phẩm trong dòng Luminasta – nổi bật với tạo dáng năng động và chi tiết sắc nét, thích hợp trưng bày mọi bộ sưu tập anime & game', 'Kích thước:  21 cm\r\nChất liệu: PVC - ABS', 'Mikuvr15.png', 722500, 0.30, 555769.23, 10),
('FG-004', 'MÔ HÌNH Astolfo - Fate/Grand Order - Figurizm Alpha - Saber (SEGA) ', 'Figure', 'SEGA', 'Fate/Grand Order', 'FIGURIZMα \"Fate/Grand Order\" \"Saber/Astolfo\" – mô hình figure prize chính hãng của SEGA lấy cảm hứng từ nhân vật Astolfo trong game/anime đình đám Fate/Grand Order. Figure thuộc dòng Figurizm Alpha, thiết kế tái hiện Astolfo ở dạng Saber với trang phục tinh tế, pose năng động cùng biểu cảm rạng rỡ tràn đầy năng lượng – lý tưởng để trưng bày trong mọi bộ sưu tập anime & figure chất lượng cao.', 'Chất Liệu : PVC, ABS\r\nChiều Cao: 180mm', 'astolfo.webp', 787500, 0.30, 605769.23, 10),
('FG-005', 'Banpresto - Zaku-Gurumi Hatsune Miku Ver', 'Figure', 'Bandai', 'Hatsune Miku', 'Figure hợp tác đặc biệt kỷ niệm 45 năm Mobile Suit Gundam – sản phẩm được Banpresto & Bandai phân phối chính thức, tái hiện Hatsune Miku trong bộ trang phục Zaku-Gurumi độc đáo lấy cảm hứng từ dòng mecha Zaku nổi tiếng. Thiết kế figure tập trung vào sự dễ thương và tinh thần “crossover” giữa thế giới Vocaloid và Gundam, tạo điểm nhấn nổi bật trong bất kỳ bộ sưu tập anime nào.', 'Chiều cao mô hình: ~14cm', '5e759ee9e50d4348a0a0ad907206f0f9_c2aa7bce7a164478be622bafcdac5f6b_grande.jpg', 722241, 0.30, 555570.30, 31),
('HG-001', 'HG GTO 1/144 RX-78-02 Gundam', 'HG', 'Bandai', 'Xuất hiện trong: MOBILE SUIT GUNDAM THE ORIGIN', 'Bộ kit này mang đến một khuôn mẫu và định nghĩa hoàn toàn mới cho Gundam The Origin\'s RX-78-02, được trang bị nhiều loại vũ khí và gimmicks. Màu sắc và khả năng tháo lắp các bộ phận của bộ kit này cũng được cải tiến tốt hơn so với các dòng HG còn lại trong dòng GTO. Được cung cấp nhiều bộ phận chuyển đổi để tạo ra các biến thể của RX-78-02 Gundam và để đại diện cho tất cả các thiết kế có sẵn cho chính Gundam đó.', 'Cấp độ: HG\nYêu cầu tự lắp\nChiều cao mô hình: 13cm\nTỉ lệ: 1/144', 'rx782origin.jpg', 400000, 0.30, 307692.31, 22),
('HG-002', 'HG Char\'s Zaku II', 'HG', 'Bandai', 'Xuất hiện trong: MOBILE SUIT GUNDAM THE ORIGIN', 'HG Char\'s Zaku II là mẫu mô hình Gunpla tỉ lệ 1/144 tái hiện cỗ Mobile Suit huyền thoại của “Sao Đỏ” Char Aznable trong series Mobile Suit Gundam. Với tông màu đỏ đặc trưng và thiết kế mạnh mẽ, đây là lựa chọn không thể thiếu cho người hâm mộ Zeon cũng như các nhà sưu tầm Gunpla.', 'Cấp độ: HG\r\nYêu cầu tự lắp\r\nChiều cao mô hình: 13cm', 'charzaku6.jpg', 363492, 0.30, 279609.27, 35),
('HG-003', 'HG ZGOK - SEED FREEDOM Ver', 'HG', 'Bandai', 'Mobile Suit Gundam SEED Freedom', 'HG ZGOK – SEED FREEDOM Ver. là mẫu mô hình Gunpla tỉ lệ 1/144 thuộc dòng High Grade, tái hiện lại thiết kế Z’Gok xuất hiện trong thế giới Mobile Suit Gundam SEED FREEDOM với diện mạo hiện đại và sắc nét hơn.', 'Cấp độ: HG\r\nYêu cầu tự lắp\r\nChiều cao mô hình: 13cm', 'HGCE_Z_GOK_en_01m.jpg', 400000, 0.30, 307692.31, 26),
('HG-004', 'HG Acguy', 'HG', 'Bandai', 'Mobile Suit Gundam ', 'HG Acguy là mẫu Gunpla tỉ lệ 1/144 tái hiện mobile suit lặn biển huyền thoại của Zeon trong Mobile Suit Gundam. Với thiết kế tròn trịa đặc trưng và phong cách “đáng yêu nhưng nguy hiểm”, Acguy luôn là lựa chọn được yêu thích trong dòng HG.', 'Cấp độ: HG\r\nYêu cầu tự lắp\r\nChiều cao mô hình: 13cm', 'moe.jpg', 400000, 0.30, 307692.31, 10),
('HG-005', 'HG MS-09R Rick Dom', 'HG', 'Bandai', 'Mobile Suit Gundam', 'HG MS-09R Rick Dom là mẫu Gunpla tỉ lệ 1/144 tái hiện biến thể không gian của Dom – mobile suit hạng nặng nổi tiếng của Zeon trong Mobile Suit Gundam. Với thiết kế giáp dày, form to bản và tông màu tím đặc trưng, Rick Dom mang đến cảm giác mạnh mẽ và uy lực khi trưng bày.', 'Cấp độ: HG\r\nYêu cầu tự lắp\r\nChiều cao mô hình: 13cm', 'drcpgeh5suyysmyb__1__6774123962d3423eac136f2a4f86c8a8_grande.jpeg', 400000, 0.30, 307692.31, 21),
('HG-006', 'HG GQuuuuuuX Gundam – Limited Edition', 'HG', 'Bandai', 'Mobile Suit Gundam GQuuuuuuX', 'HG GQuuuuuuX Gundam – Limited Edition là phiên bản giới hạn tỉ lệ 1/144 thuộc dòng High Grade, nổi bật với phối màu/hoàn thiện đặc biệt (Clear Color / Metallic / Special Coating tùy đợt phát hành). Thiết kế độc đáo cùng số lượng phát hành giới hạn giúp mẫu kit này trở thành item sưu tầm đáng giá.', 'Cấp độ: HG\r\nYêu cầu tự lắp\r\nChiều cao mô hình: 13cm', 'hg-1144-mobile-suit-gundam-gquuuuuux-limited-edition-bandai-spirits-_df741980cb4c4850a346c790c234f2b5_grande.jpg', 400000, 0.30, 307692.31, 20),
('HG-007', 'HG Red Gundam (GQuuuuuuX)', 'HG', 'Bandai', 'Mobile Suit Gundam GQuuuuuuX', 'HG Red Gundam (GQuuuuuuX) là mẫu Gunpla tỉ lệ 1/144 thuộc dòng High Grade, nổi bật với phối màu đỏ rực cá tính và thiết kế hiện đại đặc trưng của dòng GQuuuuuuX. Phiên bản này mang phong cách mạnh mẽ, tốc độ và cực kỳ bắt mắt khi trưng bày.', 'Cấp độ: HG\r\nYêu cầu tự lắp\r\nChiều cao mô hình: 13cm', 'oj4qyp0dik0xnfd5_988680d6151e4ee2bf7c10240cd5ce71_master.jpg', 400000, 0.30, 307692.31, 30),
('MG-001', 'MGEX Strike Freedomaaaaaa', 'MG', 'Bandai', 'Mobile Suit Gundam SEED DESTINY', 'Bandai Spirits MGEX Strike Freedom Gundam 1/100 model kit – mô hình Gunpla MGEX (Master Grade Extreme) cực kỳ cao cấp của Bandai Spirits, tái hiện Strike Freedom Gundam từ Mobile Suit Gundam SEED DESTINY với mức chi tiết và hiệu ứng kim loại làm nổi bật khung nội bộ độc đáo cho trải nghiệm build và trưng bày đỉnh cao.', 'Tình trạng: Mới nguyên hộp\r\nKhung xương: Có\r\nChiều cao: 18-25cm\r\nYêu cầu tự lắp', 'mgexstrike.jpg', 800000, 0.30, 692307.69, 14),
('PG-001', ' PG 00 Raiser ', 'PG', 'Bandai', 'A Wakening of the Trailblazer', 'Bandai/MG Perfect Grade 00 Raiser – mô hình lắp ráp Perfect Grade (PG) 1/60 00 Raiser chính hãng từ Bandai – phiên bản đỉnh cao của dòng Gunpla dành cho bộ sưu tập nghiêm túc. Đây là mô hình Perfect Grade cực kỳ chi tiết, tỉ lệ 1/60 với kích thước lớn (khoảng 30 cm sau hoàn thiện), tích hợp hệ thống GN Drive có đèn LED & cơ chế quay, cho hiệu ứng ánh sáng chân thực và tạo dáng ấn tượng trên kệ trưng bày.', 'Cấp độ: PG\r\nYêu cầu tự lắp\r\nChiều cao mô hình:30 cm', 'raiser.jpg', 1100000, 0.30, 846153.85, 5),
('RG-001', 'RG RX-93 ν Gundam', 'RG', 'Bandai', 'Mobile Suit Gundam: Char\'s Counterattack', 'RG RX-93 ν Gundam là mẫu Gunpla tỉ lệ 1/144 thuộc dòng Real Grade, tái hiện mobile suit huyền thoại của Amuro Ray trong Mobile Suit Gundam: Char\'s Counterattack. Đây là một trong những kit RG được đánh giá cao nhất nhờ độ chi tiết, khung xương chắc chắn và hệ thống Fin Funnel ấn tượng.', 'Cấp độ: HG\r\nYêu cầu tự lắp\r\nChiều cao mô hình: 13cm', 'nugundam.jpg', 615000, 0.30, 473076.92, 15),
('RG-002', 'RG MSN-04 Sazabi', 'RG', 'Bandai', 'Mobile Suit Gundam: Char\'s Counterattack', 'RG MSN-04 Sazabi là mẫu Gunpla tỉ lệ 1/144 thuộc dòng Real Grade, tái hiện mobile suit đỏ huyền thoại của Char Aznable trong Mobile Suit Gundam: Char\'s Counterattack. Đây là một trong những kit RG có kích thước lớn và độ chi tiết ấn tượng bậc nhất phân khúc 1/144.', 'Cấp độ: RG\r\nYêu cầu tự lắp\r\nChiều cao mô hình: 13cm', 'sazabi.jpg', 615000, 0.30, 473076.92, 15),
('RG-003', 'RG God Gundam', 'RG', 'Bandai', 'Mobile Fighter G Gundam', 'Mô hình lắp ráp Gunpla Real Grade 1/144 God Gundam – Bandai chính hãng – kit thực tế tái hiện chiến binh God Gundam từ series Mobile Fighter G Gundam ở tỷ lệ 1/144 với độ chi tiết và khả năng tạo dáng vượt trội. Được thiết kế với khung nội bộ đa lớp và nhiều điểm khớp linh hoạt, sản phẩm cho phép bạn dễ dàng dựng các tư thế hành động đặc sắc như God Finger hay folded-arms chân thực như trong anime.', 'Cấp độ: RG\r\nYêu cầu tự lắp\r\nChiều cao mô hình: 13cm', 'godgundam.jpg', 582143, 0.30, 447802.20, 14),
('rrrrr', 'rrrrr', 'HG', 'SEGA', 'rr', 'rr', 'rr', 'Screenshot 2025-09-24 235909.png', 130, 0.30, 100.00, 20),
('SD-01', 'Super DUmber 67', 'SD', 'Bandai', 'Sea', '676', '6736', 'Screenshot 2026-03-17 003056 - Copy.png', 130, 0.30, 100.00, 13),
('vvvv', 'vvvv', 'RG', 'SEGA', 'vvv', 'vv', 'vv', 'Screenshot 2025-09-24 175606.png', 130, 0.30, 100.00, 18);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `purchase_receipts`
--

CREATE TABLE `purchase_receipts` (
  `id` int(11) NOT NULL,
  `receipt_code` varchar(20) DEFAULT NULL,
  `import_date` date DEFAULT NULL,
  `total_quantity` int(11) DEFAULT 0,
  `total_value` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `purchase_receipts`
--

INSERT INTO `purchase_receipts` (`id`, `receipt_code`, `import_date`, `total_quantity`, `total_value`) VALUES
(5, 'PN01', '2026-03-11', 10, 6500000.00),
(6, 'PN02', '2026-03-15', 5, 3250000.00),
(7, 'PN03', '2026-03-19', 5, 3250000.00),
(10, 'PN05', '2026-03-20', 10, 6500000.00),
(11, 'PN04', '2026-03-20', 5, 3250000.00),
(12, 'PN06', '0000-00-00', 10, 6500000.00),
(13, 'qqq', '2026-03-01', 10, 1000.00),
(14, 'vvvv', '2026-03-27', 10, 1000.00),
(15, 'ttt', '2026-03-27', 10, 1000.00),
(16, 'ggg', '2026-03-27', 10, 1000.00),
(17, 'aaaa', '2026-04-02', 10, 1000.00),
(18, 'bbb', '2026-04-03', 10, 1000.00),
(19, 'ccc', '2026-04-03', 10, 1000.00),
(20, 'PN07', '2026-04-01', 5, 3500000.00),
(21, 'pppp', '2026-04-03', 5, 3250000.00),
(22, 'q', '2026-04-04', 5, 555555.00),
(23, 'PN08', '2026-04-04', 20, 13500000.00),
(24, 'PN09', '0000-00-00', 2, 1300000.00),
(25, 'llllll', '2026-04-05', 1, 6000000.00),
(26, 'aa', '2026-04-05', 4, 2400000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `purchase_receipt_items`
--

CREATE TABLE `purchase_receipt_items` (
  `id` int(11) NOT NULL,
  `receipt_code` varchar(20) DEFAULT NULL,
  `product_id` varchar(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `purchase_receipt_items`
--

INSERT INTO `purchase_receipt_items` (`id`, `receipt_code`, `product_id`, `quantity`, `price`) VALUES
(3, 'PN01', 'PG001', 10, 650000.00),
(4, 'PN02', 'MG-001', 5, 650000.00),
(5, 'PN03', 'HG-007', 5, 650000.00),
(7, 'PN05', 'HG-004', 5, 650000.00),
(8, 'PN05', 'HG-002', 5, 650000.00),
(9, 'PN04', 'FG-002', 5, 650000.00),
(10, 'PN06', 'FG-005', 10, 650000.00),
(11, 'qqq', 'zzz', 10, 100.00),
(12, 'vvvv', 'vvvv', 10, 100.00),
(13, 'ttt', 'rrrrr', 10, 100.00),
(14, 'ggg', 'SD-01', 10, 100.00),
(15, 'aaaa', 'acn', 10, 100.00),
(16, 'bbb', 'acn', 10, 100.00),
(17, 'ccc', 'acn', 10, 100.00),
(18, 'PN07', 'RG-003', 5, 700000.00),
(19, 'pppp', 'FG-005', 5, 650000.00),
(20, 'q', 'HG-002', 5, 111111.00),
(21, 'PN08', 'RG-002', 5, 650000.00),
(22, 'PN08', 'RG-001', 5, 650000.00),
(23, 'PN08', 'FG-003', 5, 650000.00),
(24, 'PN08', 'FG-004', 5, 750000.00),
(25, 'PN09', 'FG-001', 2, 650000.00),
(26, 'llllll', 'FG-001', 1, 6000000.00),
(27, 'aa', 'FG-001', 4, 600000.00);

--
-- Bẫy `purchase_receipt_items`
--
DELIMITER $$
CREATE TRIGGER `trg_import_product` AFTER INSERT ON `purchase_receipt_items` FOR EACH ROW BEGIN
    DECLARE cur_qty INT;
    DECLARE cur_cost DECIMAL(12,2);
    DECLARE cur_profit DECIMAL(5,2);
    DECLARE new_cost DECIMAL(12,2);

    SELECT Quantity, cost_price, Profit
    INTO cur_qty, cur_cost, cur_profit
    FROM product_list
    WHERE ProductID = NEW.product_id;

    -- Tính giá bình quân
    IF (cur_qty + NEW.quantity) > 0 THEN
        SET new_cost = (cur_qty * cur_cost + NEW.quantity * NEW.price) 
                       / (cur_qty + NEW.quantity);
    ELSE
        SET new_cost = NEW.price;
    END IF;

    -- Update số lượng, giá nhập bình quân, giá bán mới
    UPDATE product_list SET
        Quantity   = Quantity + NEW.quantity,
        cost_price = ROUND(new_cost, 2),
        Price      = ROUND(new_cost * (1 + cur_profit))
    WHERE ProductID = NEW.product_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_insert_history_import` AFTER INSERT ON `purchase_receipt_items` FOR EACH ROW INSERT INTO history (ProductID, import_num, update_date)
SELECT NEW.product_id, NEW.quantity, import_date
FROM purchase_receipts
WHERE receipt_code = NEW.receipt_code
ON DUPLICATE KEY UPDATE
import_num = import_num + NEW.quantity
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_insert_receipt_item` AFTER INSERT ON `purchase_receipt_items` FOR EACH ROW BEGIN
    UPDATE purchase_receipts SET
        total_quantity = (SELECT SUM(quantity) FROM purchase_receipt_items WHERE receipt_code = NEW.receipt_code),
        total_value    = (SELECT SUM(quantity * price) FROM purchase_receipt_items WHERE receipt_code = NEW.receipt_code)
    WHERE receipt_code = NEW.receipt_code;
END
$$
DELIMITER ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `adminname` (`adminname`);

--
-- Chỉ mục cho bảng `admin_settings`
--
ALTER TABLE `admin_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `cart_id` (`cart_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `cart_temp`
--
ALTER TABLE `cart_temp`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `fk_orders_customer` (`customer_id`);

--
-- Chỉ mục cho bảng `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `fk_orderitem_order` (`id_order`),
  ADD KEY `fk_orderitem_product` (`ProductID`);

--
-- Chỉ mục cho bảng `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `fk_grade_category` (`Grade`);

--
-- Chỉ mục cho bảng `purchase_receipts`
--
ALTER TABLE `purchase_receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_code` (`receipt_code`);

--
-- Chỉ mục cho bảng `purchase_receipt_items`
--
ALTER TABLE `purchase_receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_code` (`receipt_code`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_settings`
--
ALTER TABLE `admin_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `cart_temp`
--
ALTER TABLE `cart_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `order_item`
--
ALTER TABLE `order_item`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `purchase_receipts`
--
ALTER TABLE `purchase_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `purchase_receipt_items`
--
ALTER TABLE `purchase_receipt_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product_list` (`ProductID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `fk_orderitem_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orderitem_product` FOREIGN KEY (`ProductID`) REFERENCES `product_list` (`ProductID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_list`
--
ALTER TABLE `product_list`
  ADD CONSTRAINT `fk_grade_category` FOREIGN KEY (`Grade`) REFERENCES `categories` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `purchase_receipt_items`
--
ALTER TABLE `purchase_receipt_items`
  ADD CONSTRAINT `purchase_receipt_items_ibfk_1` FOREIGN KEY (`receipt_code`) REFERENCES `purchase_receipts` (`receipt_code`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
