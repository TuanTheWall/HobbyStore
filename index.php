<?php
session_start();
require_once "config.php";

$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

/* ====== SEARCH PARAMS ====== */
$keyword   = $_GET['keyword']   ?? '';
$grade     = $_GET['grade'] ?? '';
if($grade === 'anime'){
    $grade = 'Figure';
}
$brand     = $_GET['brand']     ?? '';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';

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
  <link rel="stylesheet" href="index.css">
</head>
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
        <input type="number" name="price_min" placeholder="VD: 500000">
        <label>Giá đến:</label>
        <input type="number" name="price_max" placeholder="VD: 1000000">
        <label>Chọn dòng:</label>
        <select name="grade">
            <option value="">-- Chọn dòng --</option>
            <option value="HG">High Grade</option>
            <option value="RG">Real Grade</option>
            <option value="MG">Master Grade</option>
            <option value="PG">Perfect Grade</option>
            <option value="Figure">Anime Figure</option>
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
          <a href="hg.php">
          <img class="card-img" src="assets/img/HGlogo.jpg" alt="product-image" width="610" height="100">
          <p class="cch">High Grade</p>
          </a>
          <a href="rg.php">
          <img class="card-img" src="assets/img/RGlogo.webp"alt="product-image" width="610" height="100" >
          <p class="cch">Real Grade</p>
          </a>
          <a href="mg.php">
          <img class="card-img" src="assets/img/MGlogo.webp"alt="product-image" width="610" height="100" >
          <p class="cch">Master Grade</p>
          </a>
          <a href="pg.php">
          <img class="card-img" src="assets/img/PGGundamLogo.webp"alt="product-image" width="610" height="100">
          <p class="cch">Perfect Grade</p>
          </a>
          <a href="ani.php">
          <img class="card-img" src="assets/img/db1b3nurblef1.jpeg"alt="product-image" width="610" height="100" >
          <p class="cch">Anime Figure</p>
          </a>
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
    $price  = (float)$row['Price'];
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
</script>

</body>

</html>
