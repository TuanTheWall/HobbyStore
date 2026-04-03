<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông tin cá nhân</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
    rel="stylesheet">
  <link href="https://cdn.reflowhq.com/v2/toolkit.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
  <link rel="stylesheet" href="profile.css">
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
        <input type="text" name="keyword" placeholder="Search.." style="padding: 12px 20px; font-size: 15px; width: 400px; border:1px solid #ccc; border-radius:4px 0 0 4px;">
        <button type="submit" class="btn-tim">Tìm</button>
        <button type="button" class="btn-advanced" id="advancedSearchBtn">Tìm kiếm nâng cao</button>
      </form>
    </div>
  </div>
  <!-- POPUP -->
  <div class="popup-overlay" id="advancedSearchPopup">
    <div class="popup-content">
      <h2>Tìm kiếm nâng cao</h2>
      <form action="index.php" method="get">
        <label>Giá từ:</label>
        <input type="text" name="price_min" class="price-input" placeholder="VD: 500.000">

        <label>Giá đến:</label>
        <input type="text" name="price_max" class="price-input" placeholder="VD: 1.000.000">

        <label>Chọn dòng:</label>
        <label>Chọn dòng:</label>
<select name="grade">
    <option value="">-- Chọn dòng --</option>
    <?php
    $cate_q = $conn->query("SELECT name FROM categories ORDER BY ID");
    while($c = $cate_q->fetch_assoc()):
    ?>
        <option value="<?= htmlspecialchars($c['name']) ?>">
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
        <div class="border">
                <div>
                    <h class="header">Thông tin cá nhân</h>
                </div>
                <div class="subhead">
                    <p>ID: <?= $user['customer_id'] ?></p>
                </div><br>
                <div class="info">
                    <span class="label">Tên người dùng</span>
                    <span class="value"><?= $user['username'] ?></span>
                </div><br>
                <div class="info">
                    <span class="label">Mật khẩu</span>
                    <span class="value">********</span>
                </div><br>
                <div class="info">
                    <span class="label">Địa chỉ</span>
                    <span class="value"><?= $user['address'] ?></span>
                </div><br>
                <div class="info">
                    <span class="label">Gmail</span>
                    <span class="value"><?= $user['email'] ?></span>
                </div><br>
                <div class="info">
                    <span class="label">Số điện thoại</span>
                    <span class="value"><?= $user['phone'] ?></span>
                </div><br>
              </div>
        <div class="sidebar" >
            <a href="profile.php">Thông tin cá nhân</a>
            <a href="history.php">Lịch sử mua hàng</a>
            <a href="edit.php">Chỉnh sửa</a>
            <a href="logoutindex.php">Đăng xuất</a>
        </div>
</body>
        <script>
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
    function formatNumber(value) {
  return value.replace(/\D/g, "") // chỉ giữ số
              .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function unformatNumber(value) {
  return value.replace(/\./g, "");
}

document.querySelectorAll('.price-input').forEach(input => {

  // Khi nhập
  input.addEventListener('input', (e) => {
    let raw = unformatNumber(e.target.value);
    e.target.value = formatNumber(raw);
  });

  // Khi submit form → bỏ dấu chấm để gửi đúng số
  input.form.addEventListener('submit', () => {
    input.value = unformatNumber(input.value);
  });

});
        </script>
    </body>
</html>
