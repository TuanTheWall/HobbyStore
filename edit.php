<?php
session_start();
$conn = new mysqli("localhost","root","","hobbystore");
mysqli_set_charset($conn,"utf8mb4");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* LẤY THÔNG TIN USER */
$sql = "SELECT * FROM customers WHERE customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* CẬP NHẬT THÔNG TIN */
if(isset($_POST['save'])){

    $username = $_POST['username'];
    $password = $_POST['password'];
    $address  = $_POST['address'];
    $email    = $_POST['email'];
    $phone    = $_POST['phone'];

    $update = "UPDATE customers 
               SET username=?, password=?, address=?, email=?, phone=? 
               WHERE customer_id=?";

    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssssss",
        $username,$password,$address,$email,$phone,$user_id
    );

    $stmt->execute();

    header("Location: profile.php");
}
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
  <link rel="stylesheet" href="history.css">
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
        <input type="number" name="price_min" placeholder="VD: 500000">

        <label>Giá đến:</label>
        <input type="number" name="price_max" placeholder="VD: 1000000">

        <label>Chọn dòng:</label>
        <select name="grade">
          <option value="">-- Chọn dòng --</option>
          <option value="hg">High Grade</option>
          <option value="rg">Real Grade</option>
          <option value="mg">Master Grade</option>
          <option value="pg">Perfect Grade</option>
          <option value="anime">Anime Figure</option>
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

<h2 class="header">Chỉnh sửa thông tin cá nhân</h2>

<form method="POST">

<div class="subhead">
<p>ID: <?php echo $user['customer_id']; ?></p>
</div>

<div class="info">
<span class="label">Tên người dùng</span>
<input class="value" name="username"
value="<?php echo $user['username']; ?>">
</div>

<div class="info">
<span class="label">Mật khẩu</span>
<input class="value" name="password"
value="<?php echo $user['password']; ?>">
</div>

<div class="info">
<span class="label">Địa chỉ</span>
<input class="value" name="address"
value="<?php echo $user['address']; ?>">
</div>

<div class="info">
<span class="label">Email</span>
<input class="value" name="email"
value="<?php echo $user['email']; ?>">
</div>

<div class="info">
<span class="label">Số điện thoại</span>
<input class="value" name="phone"
value="<?php echo $user['phone']; ?>">
</div>

<br>

              <div style="margin-left: 17.5cm;">
<a href="profile.php" class="cancelbutton">✖ Hủy</a>
<button type="submit" name="save" class="savebutton">💾 Lưu</button>
</div>

</form>

</div>
        <div class="sidebar">
            <a href="profile.php">Thông tin cá nhân</a>
            <a href="history.php">Lịch sử mua hàng</a>
            <a href="edit.php">Chỉnh sửa</a>
            <a href="index.php">Đăng xuất</a>
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
        </script>
    </body>
</html>