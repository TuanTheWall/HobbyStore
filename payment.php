<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}

$user = $_SESSION['user_id'];

if(empty($_POST['selected_items'])){
header("Location: cart.php");
exit();
}

$selected_items = $_POST['selected_items'];
$qty = $_POST['qty'] ?? [];

$items = [];
$total = 0;

foreach($selected_items as $cart_item_id){

$cart_item_id = intval($cart_item_id);

$sql = "SELECT 
product_list.ProductID,
product_list.ProductName,
product_list.Product_image,
product_list.Price,
product_list.Profit
FROM cart_item
JOIN product_list 
ON cart_item.product_id = product_list.ProductID
WHERE cart_item.cart_item_id = $cart_item_id";

$result = mysqli_query($conn,$sql);

if(!$result){
die(mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
if(!$row){
continue;
}

$quantity = intval($qty[$cart_item_id] ?? 1);

if($quantity < 1){
$quantity = 1;
}

$price = $row['Price'] + ($row['Price'] * $row['Profit']);

$subtotal = $price * $quantity;

$total += $subtotal;

$row['quantity'] = $quantity;
$row['sell_price'] = $price;
$row['product_id'] = $row['ProductID'];

$items[] = $row;

}

/* lấy thông tin user */
$sql_user = "SELECT username, address, phone 
FROM customers 
WHERE customer_id = '$user'";

$result_user = mysqli_query($conn,$sql_user);

$user_info = [
'username' => '',
'address' => '',
'phone' => ''
];

if($result_user && mysqli_num_rows($result_user) > 0){
$user_info = mysqli_fetch_assoc($result_user);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thanh toán</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
  <link rel="stylesheet" href="payment.css">
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
        <input type="text" name="price_min" class="price-input" placeholder="VD: 500.000">
        <label>Giá đến:</label>
        <input type="text" name="price_max" class="price-input" placeholder="VD: 1.000.000">
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
    <div class="payment-layout" id="paymentSection">
        <div class="cart-section">
      <h1>Giỏ hàng của bạn</h1>
      <?php foreach($items as $item): ?>

<div class="cart-item">

<img src="assets/img/<?= htmlspecialchars($item['Product_image']) ?>">

<div class="item-info">
<p class="item-title"><?= $item['ProductName'] ?></p>
<p class="price"><?= number_format($item['sell_price']) ?> VNĐ</p>
</div>

<div class="controls">
<label>Số lượng</label>
<p><?= $item['quantity'] ?></p>
</div>

</div>

<?php endforeach; ?>

      <div class="summary">
Tổng cộng: <?= number_format($total) ?> VNĐ
</div>
    </div>

        <div class="checkout-container">
      <h1>Thông tin thanh toán</h1>
      <form id="checkoutForm" method="POST" action="place_order.php">
<label>Tên</label>
<input type="text" name="name" 
value="<?= $user_info['username'] ?>" required>

<label>Địa chỉ giao hàng</label>
<input type="text" name="address" 
value="<?= $user_info['address'] ?>" required>

<label>Số điện thoại</label>
<input type="tel" name="phone" 
value="<?= $user_info['phone'] ?>" required>
        <select name="payment_method">
         <option value="COD">Thanh toán khi nhận hàng (COD)</option>
          <option value="BANK">Chuyển khoản ngân hàng</option>
        </select>
       <?php foreach($items as $item): ?>

<input type="hidden" name="product_id[]" value="<?= $item['product_id'] ?>">
<input type="hidden" name="price[]" value="<?= $item['sell_price'] ?>">
<input type="hidden" name="quantity[]" value="<?= $item['quantity'] ?>">

<?php endforeach; ?>

        <button type="submit">Xác nhận đặt hàng</button>
      </form>
    </div>
  </div>
  <!-- ===== Popup xác nhận đơn hàng ===== -->
<div class="popup-overlay" id="orderConfirmPopup">
  <div class="popup-content">
    <h2>Xác nhận đơn hàng</h2>
    <div id="orderSummary">
      <p><strong>Tên:</strong> <?= $user_info['username'] ?></p>
      <p><strong>Địa chỉ:</strong> <?= $user_info['address'] ?></p>
      <p><strong>Số điện thoại:</strong> <?= $user_info['phone'] ?></p>
      <p><strong>Phương thức thanh toán:</strong> Thanh toán khi nhận hàng (COD)</p>
      <hr>
      <h3>Sản phẩm:</h3>
<br>

<?php foreach($items as $item): ?>

<p>
• <?= $item['ProductName'] ?> 
(<?= $item['quantity'] ?>x) - 
<?= number_format($item['sell_price'] * $item['quantity']) ?> VNĐ
</p>

<?php endforeach; ?>
      <hr><br>
      <p><strong>Tổng cộng: <?= number_format($total) ?> VNĐ</strong></p>
    </div>
    <div class="popup-buttons">
      <button id="confirmOrderBtn" class="search-btn">Xác nhận</button>
      <button id="cancelOrderBtn" class="close-btn">Hủy</button>
    </div>
  </div>
</div>
    <div class="thankyou-message" id="thankyouMessage">
    <h2>🎉 Cảm ơn bạn đã mua hàng! 🎉</h2>
    <p>Đơn hàng của bạn đã được ghi nhận. Chúng tôi sẽ liên hệ sớm nhất để giao hàng.</p>
    <a href="index.php">Trở về trang chủ</a>
  </div>

  <script>
  const form = document.getElementById("checkoutForm");
  const paymentSection = document.getElementById("paymentSection");
  const thankyouMessage = document.getElementById("thankyouMessage");

  const orderConfirmPopup = document.getElementById("orderConfirmPopup");
  const confirmOrderBtn = document.getElementById("confirmOrderBtn");
  const cancelOrderBtn = document.getElementById("cancelOrderBtn");

  // Khi nhấn "Xác nhận đặt hàng"
form.addEventListener("submit", function (e) {
  if(!form.dataset.confirmed){
    e.preventDefault();
    orderConfirmPopup.style.display = "flex";
  }
});

  // Khi nhấn "Xác nhận" trong popup
confirmOrderBtn.addEventListener("click", () => {
  form.dataset.confirmed = "true";
  form.submit();
});

  // Khi nhấn "Hủy"
  cancelOrderBtn.addEventListener("click", () => {
    orderConfirmPopup.style.display = "none";
  });

  // Đóng popup khi click ra ngoài
  orderConfirmPopup.addEventListener("click", (e) => {
    if (e.target === orderConfirmPopup) orderConfirmPopup.style.display = "none";
  });

  // Popup tìm kiếm nâng cao (giữ nguyên)
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