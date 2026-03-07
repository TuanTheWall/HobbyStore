
<?php
session_start();
require_once "config.php";
$user = $_SESSION['user_id'] ?? null;
$cart_items = [];
if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}
if($user){

$sql = "SELECT 
        cart_item.cart_item_id,
        cart_item.quantity,
        product_list.ProductID,
        product_list.ProductName,
        product_list.Product_image,
        product_list.Price,
        product_list.Profit,
        product_list.Quantity AS stock
        FROM cart
        JOIN cart_item ON cart.cart_id = cart_item.cart_id
        JOIN product_list ON cart_item.product_id = product_list.ProductID
        WHERE cart.customer_id = '$user'";

$result = mysqli_query($conn,$sql);

while($row = mysqli_fetch_assoc($result)){
$cart_items[] = $row;
}

}
?>
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
<form method="POST" action="payment.php" id="cartForm">
<div class="container">
  <?php foreach($cart_items as $item): ?>
    <div class="cart-item" data-id="<?= $item['cart_item_id'] ?>" data-stock="<?= $item['stock'] ?>">
      <input type="checkbox" name="selected_items[]" value="<?= $item['cart_item_id'] ?>">
      <img src="assets/img/<?= $item['Product_image'] ?>">
      <div class="item-info">
      <p class="item-title"><?= $item['ProductName'] ?></p>
      <?php 
$price  = (float)$item['Price'];
$profit = (float)$item['Profit'];
$sell_price = $price * (1 + $profit);
?>

<p class="price"><?= number_format($sell_price) ?> VNĐ</p>
    </div>
    <div class="controls">
      <label>Số lượng:</label>
        <span class="item-qty"><?= $item['quantity'] ?></span>
        <input type="hidden" name="qty[<?= $item['cart_item_id'] ?>]" 
value="<?= $item['quantity'] ?>" 
class="qty-input">
        <div class="qty-wrap">
          <button class="btn-dec" type="button">-</button>
          <button class="btn-add" type="button">+</button>
        </div>
      </div>
  </div>
<?php endforeach; ?>
  </div>
<?php if(empty($cart_items)): ?>
<p style="text-align:center;padding:40px;">Giỏ hàng trống</p>
<?php endif; ?>

<div class="summary">
Tổng cộng: <span id="totalPrice">0 VNĐ</span>
</div>

<div class="actions">
<button type="submit" class="btn-pay">
Thanh toán
</button>
</div>
</form>
    <script>
    function parsePrice(text) {
  const digits = text.replace(/[^\d]/g, '');
  return Number(digits) || 0;
}

function formatPrice(n){
return n.toLocaleString('vi-VN') + " VNĐ";
}

function updateTotals() {

const items = document.querySelectorAll('.cart-item');

let total = 0;

items.forEach(item => {

const checkbox = item.querySelector("input[type='checkbox']");

if(!checkbox.checked) return;

const price = parsePrice(item.querySelector('.price').textContent);
const qty = parseInt(item.querySelector('.item-qty').textContent);

total += price * qty;

});

document.getElementById("totalPrice").textContent = formatPrice(total);

}

document.addEventListener('click', function(e){

if(e.target.matches('.btn-add') || e.target.matches('.btn-dec')){

const item = e.target.closest('.cart-item');

const qtyEl = item.querySelector('.item-qty');

let qty = parseInt(qtyEl.textContent);

const stock = parseInt(item.dataset.stock);

if(e.target.matches('.btn-add')){

if(qty < stock){
qty++;
}

}else{

qty--;

if(qty <= 0){

fetch("delete_cart_item.php",{
method:"POST",
headers:{
"Content-Type":"application/x-www-form-urlencoded"
},
body:"id="+item.dataset.id
});

item.remove();
updateTotals();
return;
}

}

qtyEl.textContent = qty;
item.querySelector('.qty-input').value = qty;

fetch("update_cart_qty.php",{
method:"POST",
headers:{
"Content-Type":"application/x-www-form-urlencoded"
},
body:"id="+item.dataset.id+"&qty="+qty
});

updateTotals();

}

});

document.addEventListener("change", function(e){

if(e.target.matches("input[type='checkbox']")){
updateTotals();
}

});

document.addEventListener("DOMContentLoaded", function(){

updateTotals();

document.querySelectorAll("input[type='checkbox']").forEach(cb=>{
cb.addEventListener("change",updateTotals);
});
document.getElementById("cartForm").addEventListener("submit",function(e){

const checked = document.querySelectorAll("input[type='checkbox']:checked");

if(checked.length === 0){
alert("Vui lòng chọn sản phẩm để thanh toán");
e.preventDefault();
}

});
});

</script>


  </body>
</html>
