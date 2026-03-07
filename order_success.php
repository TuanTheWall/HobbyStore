<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
      rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/settings.css">
    <link rel="stylesheet" href="assets/css/product-page.css">
    <link rel="stylesheet" href="assets/css/view-cart.css">
    <link rel="stylesheet" href="cart.css">
  </head>
  <style>
.order-success{
    display:flex;
    justify-content:center;
    align-items:center;
    height:70vh;
}

.success-box{
    background:white;
    padding:40px 60px;
    border-radius:10px;
    text-align:center;
    box-shadow:0 5px 20px rgba(0,0,0,0.15);
    font-family: "Josefin Sans", sans-serif;
}

.success-box h1{
    color:#28a745;
    margin-bottom:20px;
}

.success-box p{
    font-size:18px;
    margin-bottom:25px;
}

.btn-home{
    text-decoration:none;
    background:#ff6b6b;
    color:white;
    padding:12px 25px;
    border-radius:6px;
    transition:0.3s;
}

.btn-home:hover{
    background:#e74c3c;
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
<div class="order-success">
  <div class="success-box">
    <h1>🎉 Đặt hàng thành công</h1>
    <p>Cảm ơn bạn đã mua hàng tại cửa hàng của chúng tôi.</p>
    <a href="index.php" class="btn-home">Về trang chủ</a>
  </div>
</div>