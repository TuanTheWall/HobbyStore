<?php
session_start();
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

  <!-- ===== SIDEBAR ===== -->
  <div class="sidebar">
    <br><br><br><br>
    <a href="profile.php">Thông tin cá nhân</a>
    <a href="history.php">Lịch sử mua hàng</a>
    <a href="edit.php">Chỉnh sửa</a>
    <a href="logout.php">Đăng xuất</a>
  </div>

  <!-- ===== MAIN CONTENT ===== -->
  <div class="main-content" >
    <h2>Đơn hàng của tôi</h2>
    <div id="ordersList">

<?php
$conn = new mysqli("localhost","root","","hobbystore");
mysqli_set_charset($conn,"utf8mb4");

$customer_id = $_SESSION['user_id'];

$sql = "
SELECT 
orders.id_order,
orders.order_date,
orders.status,
orders.total,
product_list.ProductName,
order_item.ProductID,
order_item.quantity

FROM orders

JOIN order_item 
ON orders.id_order = order_item.id_order

JOIN product_list
ON order_item.ProductID = product_list.ProductID

WHERE orders.customer_id='$customer_id'

ORDER BY orders.order_date DESC
";

$result = $conn->query($sql);
$current_order = null;

while($row = $result->fetch_assoc()){

if($current_order != $row['id_order']){

    if($current_order != null){
    echo "<div class='order-total'><b>Tổng đơn hàng:</b> ".number_format($last_total)." VNĐ</div>";

    echo "<div class='order-button'>
<a href='detail.php?id=".$current_order."' class='detail-btn'>
Xem chi tiết
</a>
</div>";

    echo "</div></div>";
}

    $current_order = $row['id_order'];
    $last_total = $row['total'];

    echo "<div class='order-item'>";
    echo "<div class='order-header'>";
    echo "<span>Mã đơn: ".$row['id_order']."</span>";
    echo "<span>".$row['status']."</span>";
    echo "</div>";

    echo "<div class='order-details'>";
    echo "<div><b>Ngày đặt:</b> ".$row['order_date']."</div>";
}

echo "<div><b>Sản phẩm:</b> ".$row['ProductName']." (".$row['ProductID'].") × ".$row['quantity']."</div>";

}

if($current_order != null){

echo "<div class='order-total'><b>Tổng đơn hàng:</b> ".number_format($last_total)." VNĐ</div>";

echo "<div class='order-button'>
<a href='detail.php?id=".$current_order."' class='detail-btn'>
Xem chi tiết
</a>
</div>";

echo "</div></div>";
}
?>

</div>

    </div>
    <div id="pagination" class="pagination"></div>
  </div>

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

popup.addEventListener('click', (e) => {
  if (e.target === popup) popup.style.display = 'none';
});
  </script>
</body>
</html>
