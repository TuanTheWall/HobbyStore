<?php
session_start();
require_once "config.php";
$cate_sql = "SELECT * FROM categories";
$cate_result = $conn->query($cate_sql);      // dùng cho popup
$cate_result2 = $conn->query($cate_sql); // dùng cho category grid
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

/* ====== SEARCH PARAMS ====== */
$keyword   = $_GET['keyword']   ?? '';
$grade     = $_GET['grade'] ?? '';
$brand     = $_GET['brand']     ?? '';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';

// ===== XỬ LÝ FORMAT 1.000.000 =====
// bỏ dấu chấm và ký tự lạ
$price_min = preg_replace('/[^0-9]/', '', $price_min);
$price_max = preg_replace('/[^0-9]/', '', $price_max);

// ép về số (nếu có nhập)
$price_min = ($price_min !== '') ? (float)$price_min : '';
$price_max = ($price_max !== '') ? (float)$price_max : '';

$error = '';

if ($price_min !== '' && $price_max !== '') {
    if ($price_min >= $price_max) {
        $error = 'Giá từ phải nhỏ hơn giá đến';
    }
}

$where = [];
$params = [];
$types = "";

if ($error === '') {

    /* ====== SEARCH THƯỜNG ====== */
    if ($keyword !== '') {
        $where[] = "(ProductName LIKE ?)";
        $params[] = "%$keyword%";
        $types .= "s";
    }

    /* ====== SEARCH NÂNG CAO ====== */
    if ($price_min !== '') {
        $where[] = "Price >= ?";
        $params[] = $price_min;
        $types .= "d";
    }

    if ($price_max !== '') {
        $where[] = "Price <= ?";
        $params[] = $price_max;
        $types .= "d";
    }

    if ($grade !== '') {
        $where[] = "Grade = ?";
        $params[] = $grade;
        $types .= "s";
    }

    if ($brand !== '') {
        $where[] = "Producer = ?";
        $params[] = $brand;
        $types .= "s";
    }
}

/* ====== BUILD SQL ====== */
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$sql = "
  SELECT * FROM product_list
  $where_sql
  ORDER BY ProductID DESC
  LIMIT ?, ?
";

$params[] = $start;
$params[] = $limit;
$types .= "ii";

if ($error === '') {

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL lỗi: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

} else {
    $result = false;
    $total_page = 0;
}

/* ====== ĐẾM TỔNG SP ====== */
if ($error === '') {

    $count_sql = "SELECT COUNT(*) AS total FROM product_list $where_sql";
    $count_stmt = $conn->prepare($count_sql);

    if ($where) {
        $count_stmt->bind_param(
            substr($types, 0, -2),
            ...array_slice($params, 0, -2)
        );
    }

    $count_stmt->execute();
    $total_product = $count_stmt->get_result()->fetch_assoc()['total'];
    $total_page = ceil($total_product / $limit);

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang chủ</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
</head>
<style>
  .category-grid {
  display: flex;
  justify-content: center;
  gap: 25px;
  flex-wrap: wrap;
  margin: -20px 0;
  margin-bottom:
}

/* bỏ gạch chân link */
.category-item {
  text-decoration: none;
}

/* card danh mục */
.category-card {
  width: 160px;
  height: 80px;
  background: linear-gradient(135deg, #00c6ff, #0072ff) !important;
  border-radius: 12px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
  font-weight: bold;
  font-size: 18px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.2);
  transition: all 0.3s ease;
}

/* chữ */
.category-name {
  text-align: center;
}

/* hover */
.category-card:hover {
  transform: translateY(-5px) scale(1.05);
  background: linear-gradient(135deg, #ff7e5f, #feb47b);
}

.category-grid img {
  width: 250px;
  border-radius: 10px;
  transition: transform 0.3s;
}
.category-grid img:hover {
  transform: scale(1.05);
}
.product-grid {
  display: grid;
  grid-template-columns: repeat(5, 220px);
  gap: 20px;
  justify-content: center;
}
.pagination{
	text-align:center;
}
.pagination a{
	color: black;
	text-decoration: none;
	padding: 8px 15px;
	display: inline-block;
}
.pagination a.active{
	background-color: green;
	font-weight: bold;
	border-radius: 5px;
}
.pagination a:hover:not(.active){
	background-color: gray;
	border-radius: 5px;
}
.card{
   padding: 10px; 
}
.product-section{
    display:flex;
    justify-content: center; 
	margin-top: -10px; 
}
.search{
	text-align:center;
	margin: 20px;
}
.search-advanced {
  text-align: center; /* canh giữa nội dung bên trong form */
  margin: 20px 0;     /* khoảng cách trên/dưới */
}
.search-advanced button {
  background-color: blue;
  color: white;
  padding: 12px 24px;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}
.product-card {
  width: 220px;
  height: 360px;           /* ⭐ QUAN TRỌNG */
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background: #DDDDDD;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);

  display: flex;
  flex-direction: column;
}

.product-card img {
  width: 100%;
  height: 180px;          /* cố định */
  object-fit: contain;
  border-radius: 8px;
}
.product-name {
  font-size: 14px;
  font-weight: bold;
  margin: 10px 0;
  min-height: 42px;

  display: -webkit-box;
  -webkit-line-clamp: 2;   /* tối đa 2 dòng */
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.cch{
 color:black;
}
.cch:hover{
color:red;
}
.navbar {
  background-color: cyan;
  height: 50px;
  display: flex;
  justify-content: space-between; /* chia trái - phải */
  align-items: center;            /* căn giữa dọc */
  padding: 0 40px;
  border-bottom: 2px solid #00bcd4;
}

/* nhóm bên trái */
.nav-left {
  display: flex;
  align-items: center;
  gap: 25px; /* khoảng cách giữa logo và chữ Trang chủ */
}

/* logo */
.logo img {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  object-fit: cover;
  filter: none !important;
}

/* nhóm bên phải */
.nav-right {
  display: flex;
  align-items: center;
  gap: 60px;
  list-style: none;
  margin: 0;
  padding: 0;
}

/* liên kết chung */
.navbar a {
  text-decoration: none;
  font-size: 18px;
  font-weight: bold;
  color: black;
  transition: color 0.2s;
}

.navbar a:hover {
  color: red;
}
.tren{
 position: relative;
top:0;
left:0;
width:100%;
}
.btn-col{
margin-top:auto;
}
.price {
  font-size: 14px;
  margin-bottom: 10px;
}
.search-bar {
  display: flex;
  align-items: center;     /* căn giữa theo chiều dọc */
  justify-content: center; /* căn giữa theo chiều ngang */
  gap: 10px;               /* khoảng cách giữa các phần tử */
  margin-top: -10px;
}

.search-input {
  width: 400px;
  height: 45px;            /* cùng chiều cao với nút */
  padding: 0 15px;
  font-size: 15px;
  border: 1px solid #ccc;
  border-radius: 6px;
  box-sizing: border-box;
}

.btn-tim,
.btn-advanced {
  height: 45px;            /* đảm bảo chiều cao bằng input */
  padding: 0 20px;
  font-size: 15px;
  font-weight: bold;
  background-color: blue;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.btn-tim:hover,
.btn-advanced:hover {
  background-color: darkblue;
}

.product-card .price {
  margin-top: auto;
  text-align: center;
  color: red;
  font-size: 20px;
}
.banner-slideshow {
  position: relative;
  width: 100%;
  height: 220px;
  overflow: hidden;
}

.banner-slideshow img {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-radius: 0;
  transition: opacity 0.8s ease;
}

.arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  font-size: 40px;
  background-color: rgba(0,0,0,0.3);
  color: white;
  border: none;
  cursor: pointer;
  padding: 5px 15px;
  border-radius: 50%;
  z-index: 10;
  transition: background-color 0.3s;
}

.arrow:hover {
  background-color: rgba(0,0,0,0.6);
}

.arrow.left { left: 20px; }
.arrow.right { right: 20px; }

.titlesp{
margin-top: -80px;
margin-left:-1000px;
color:#060270;
}
.newsp{
margin-top:-100px;
margin-bottom:40px;
color:#FED636;
margin-left:-960px;
}
/* Popup  */
    .popup-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .popup-content {
      background-color: white;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      width: 450px;
      animation: fadeIn 0.3s ease;
    }

    .popup-content h2 {
      margin-bottom: 20px;
      text-align: center;
    }

    .popup-content label {
      display: block;
      font-weight: bold;
      margin-top: 10px;
    }

    .popup-content input,
    .popup-content select {
      width: 100%;
      padding: 8px;
      border-radius: 6px;
      border: 1px solid #aaa;
      margin-top: 5px;
    }

    .popup-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .popup-buttons button {
      padding: 10px 20px;
      font-size: 15px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .popup-buttons .search-btn {
      background-color: blue;
      color: white;
    }

    .popup-buttons .close-btn {
      background-color: gray;
      color: white;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.9);
      }

      to {
        opacity: 1;
        transform: scale(1);
      }
    }
</style>
<body>
<nav class="tren">
  <nav class="navbar">
    <div class="nav-left">
      <a href="index.php" class="logo">
      <img src="assets/img/logo.png" alt="Logo">
      </a>
    <a href="index.php" class="nav-item">Trang chủ</a>
    </div>
    <ul class="nav-right">
    <?php if(isset($_SESSION['user_id'])): ?>
      <li><a href="profile.php">Hồ sơ</a></li>
    <?php else: ?>
      <li><a href="login.php">Đăng nhập</a></li>
    <?php endif; ?>
      <li><a href="cart.php">Giỏ hàng</a></li>
    </ul>
  </nav>
  <div class="timnangcao" style="background-color:white;padding-top:1px;padding-bottom:5px;">
    <div class="search" style="margin-top:10px;">
      <form action="index.php" method="get">
        <input type="text" name="keyword" placeholder="Search.."
        style="padding: 12px 20px; font-size: 15px; width: 400px; border:1px solid #ccc; border-radius:4px 0 0 4px;">
        <button type="submit" class="btn-tim">Tìm</button>
        <button type="button" class="btn-advanced" id="advancedSearchBtn">Tìm kiếm nâng cao</button>
      </form>
    </div>
  </div>
  <div class="popup-overlay" id="advancedSearchPopup">
    <div class="popup-content">
      <h2>Tìm kiếm nâng cao</h2>
      <form action="index.php" method="get">
        <label>Giá từ:</label>
        <input type="text" name="price_min" id="price_min" placeholder="VD: 1.000.000" inputmode="numeric">
        <label>Giá đến:</label>
        <input type="text" name="price_max" id="price_max" placeholder="VD: 2.000.000" inputmode="numeric">
        
        <label>Chọn dòng:</label>
<select name="grade">
    <option value="">-- Chọn dòng --</option>
<?php while($c = $cate_result2->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($c['name']) ?>"
            <?= ($grade === $c['name']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['name']) ?>
        </option>
    <?php endwhile; ?>
</select>
        <label>Chọn hãng:</label>
        <select name="brand">
          <option value="">-- Chọn hãng --</option>
          <option value="bandai">Bandai</option>
          <option value="sega">Sega</option>
        </select>
        <div class="popup-buttons">
          <button type="submit" class="search-btn">Tìm</button>
          <button type="button" class="close-btn" id="closePopup">Đóng</button>
        </div>
      </form>
    </div>
  </div>
</nav>
<div class="banner-slideshow">
  <button class="arrow left" onclick="changeBanner(-1)">&#10094;</button>
  <img id="bannerImage" src="assets/img/1080x540_Mobile_GQuuuuuuX_Banner.webp" alt="banner" width="100%" height="300px">
  <button class="arrow right" onclick="changeBanner(1)">&#10095;</button>
</div>
<div class="container">
  <section class="product-section">
    <div class="category-grid">
<?php while($c = $cate_result->fetch_assoc()): ?>

  <?php
    $query = $_GET;
    $query['grade'] = $c['name']; // gán vào filter
  ?>

  <a href="index.php?<?= http_build_query($query) ?>" class="category-item">
  <div class="category-card">
    <div class="category-name"><?= $c['name'] ?></div>
  </div>
</a>

<?php endwhile; ?>
</div>
  </section>
</div>
  
<?php
$is_search = $error === '' && (
    !empty($_GET['keyword']) ||
    !empty($_GET['price_min']) ||
    !empty($_GET['price_max']) ||
    !empty($_GET['grade']) ||
    !empty($_GET['brand'])
);
?>

<h2>
  <?= $is_search ? 'KẾT QUẢ TÌM KIẾM' : 'SẢN PHẨM' ?>
</h2>

<div class="product-grid">

<?php if ($error !== ''): ?>
    <p style="color:red; text-align:center;">
        <?= $error ?>
    </p>

<?php elseif ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>

<?php
    // ===== 6. TÍNH GIÁ BÁN =====
    $price  = (float)$row['cost_price'];
    $profit = (float)$row['Profit']; // ví dụ 0.3

    $sell_price = $price * (1 + $profit);
?>

<div class="product-card">
    <a href="product.php?id=<?= $row['ProductID'] ?>">
        <img src="assets/img/<?= $row['Product_image'] ?>" alt="">
    </a>

    <h3><?= $row['ProductName'] ?></h3>

    <p class="price">
        <?= number_format($sell_price) ?> VNĐ
    </p>

    <?php
if($row['Quantity'] == 0){
    echo '<p style="color:red; text-align:center;">Hết hàng</p>';
}
?>
</div>

<?php endwhile; ?>
<?php else: ?>
    <p style="text-align:center;">Không có sản phẩm phù hợp</p>
<?php endif; ?>
</div>

<!-- ===== 7. PHÂN TRANG ===== -->
<?php
$query_string = $_GET;
unset($query_string['page']);
$base_url = '?' . http_build_query($query_string);
?>

<div class="pagination">
<?php if ($page > 1): ?>
  <a href="<?= $base_url ?>&page=<?= $page - 1 ?>">&laquo;</a>
<?php endif; ?>

<?php for ($i = 1; $i <= $total_page; $i++): ?>
  <a href="<?= $base_url ?>&page=<?= $i ?>"
     class="<?= ($i == $page) ? 'active' : '' ?>">
    <?= $i ?>
  </a>
<?php endfor; ?>

<?php if ($page < $total_page): ?>
  <a href="<?= $base_url ?>&page=<?= $page + 1 ?>">&raquo;</a>
<?php endif; ?>
</div>

  </body>
<script>
  const bannerImages = [
    "assets/img/1080x540_Mobile_GQuuuuuuX_Banner.webp",
    "assets/img/Miku_Collection_Banner.webp",
    "assets/img/wallhaven-wyjj7x_m.png"
  ];

  let bannerIndex = 0;

  function changeBanner(step) {
    const img = document.getElementById("bannerImage");
    bannerIndex += step;

    // Quay vòng lại nếu đến cuối hoặc đầu
    if (bannerIndex >= bannerImages.length) bannerIndex = 0;
    if (bannerIndex < 0) bannerIndex = bannerImages.length - 1;

    // Hiệu ứng mờ dần khi đổi ảnh
    img.style.opacity = 0;
    setTimeout(() => {
      img.src = bannerImages[bannerIndex];
      img.style.opacity = 1;
    }, 300);
  }
  const advancedBtn = document.getElementById('advancedSearchBtn');
    const popup = document.getElementById('advancedSearchPopup');
    const closePopup = document.getElementById('closePopup');

    advancedBtn.addEventListener('click', () => {
      popup.style.display = 'flex';
    });

    closePopup.addEventListener('click', () => {
      popup.style.display = 'none';
    });

    // Đóng popup khi click ra ngoài
    popup.addEventListener('click', (e) => {
      if (e.target === popup) popup.style.display = 'none';
    });

    function formatCurrencyInput(input) {
  let value = input.value.replace(/\D/g, ''); // bỏ hết ký tự không phải số
  value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  input.value = value;
}

document.getElementById("price_min").addEventListener("input", function() {
  formatCurrencyInput(this);
});

document.getElementById("price_max").addEventListener("input", function() {
  formatCurrencyInput(this);
});
</script>

</body>

</html>