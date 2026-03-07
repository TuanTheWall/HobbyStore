<?php
session_start();

$conn = new mysqli("localhost","root","","hobbystore");
mysqli_set_charset($conn,"utf8mb4");

$id_order = $_GET['id'];

$sql = "
SELECT 
orders.id_order,
orders.order_date,
orders.status,
orders.total,
orders.payment_method,
orders.receiver_name,
orders.receiver_phone,
orders.receiver_address,

product_list.ProductName,
product_list.price,
product_list.Product_image,
order_item.quantity

FROM orders

JOIN order_item 
ON orders.id_order = order_item.id_order

JOIN product_list
ON order_item.ProductID = product_list.ProductID

WHERE orders.id_order='$id_order'
";

$result = $conn->query($sql);

if(!$result){
    die("SQL Error: " . $conn->error);
}

$order = $result->fetch_assoc();
$result->data_seek(0);
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
  <link rel="stylesheet" href="detail.css">
<style>


</style>
</head>
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

  <!-- ===== MAIN CONTENT ===== -->
  <div class="border">
    <div class="header">Chi tiết đơn hàng #<?php echo $order['id_order']; ?></div>

    <div class="info-section">
     <div class="info-box">
<h3>Thông tin khách hàng</h3>

<p><strong>Họ tên:</strong> <?php echo $order['receiver_name']; ?></p>

<p><strong>SĐT:</strong> <?php echo $order['receiver_phone']; ?></p>

<p><strong>Địa chỉ:</strong> <?php echo $order['receiver_address']; ?></p>

</div>

      <div class="info-box">
<h3>Thông tin đơn hàng</h3>

<p><strong>Mã đơn hàng:</strong> <?php echo $order['id_order']; ?></p>

<p><strong>Ngày đặt:</strong> <?php echo $order['order_date']; ?></p>

<p><strong>Trạng thái:</strong> <?php echo $order['status']; ?></p>

<p><strong>Phương thức thanh toán:</strong> <?php echo $order['payment_method']; ?></p>

</div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Sản phẩm</th>
          <th>Hình ảnh</th>
          <th>Đơn giá</th>
          <th>Số lượng</th>
          <th>Thành tiền</th>
        </tr>
      </thead>
      <tbody>

<?php
$result = $conn->query($sql);

while($row = $result->fetch_assoc()){
?>

<tr>

<td><?php echo $row['ProductName']; ?></td>

<td>
<img src="assets/img/<?php echo $row['Product_image']; ?>" width="80">
</td>

<td>
<?php echo number_format($row['price'],0,",","."); ?>đ
</td>

<td>
<?php echo $row['quantity']; ?>
</td>

<td>
<?php echo number_format($row['price'] * $row['quantity'],0,",","."); ?>đ
</td>

</tr>

<?php } ?>

</tbody>
    </table>

    <div class="summary">
      <p><strong>Tiền hàng:</strong> <?php echo number_format($order['total'],0,",","."); ?>đ</p>
<p><strong>Phí vận chuyển:</strong> Freeship</p>
<p><strong>Tổng cộng:</strong> <?php echo number_format($order['total'],0,",","."); ?>đ</p>
    </div>

    <a href="history.php" class="back-btn">← Quay lại lịch sử mua hàng</a>
  </div>
    <script>
    (function(){
      const toast = document.getElementById('toast');
      const btn = document.getElementById('addCartBtn');

      function show(text){
        toast.textContent = text;
        toast.classList.add('show');
        clearTimeout(toast._h);
        toast._h = setTimeout(()=> {
          toast.classList.remove('show');
        }, 1400);
      }

      if(btn){
        btn.addEventListener('click', function(e){
          e.preventDefault();
          btn.disabled = true;
          btn.style.transform = 'translateY(1px)';
          show('Đã thêm vô giỏ hàng');
          setTimeout(()=>{ btn.disabled = false; btn.style.transform = ''; }, 700);
        });
      }

      const basePrice = 560000;
      const addon = document.getElementById('addon');
      const totalPrice = document.getElementById('total-price');
      // if(addon && totalPrice){                     //Thay đổi giá theo phụ kiện
      //   addon.addEventListener('change', function(){
      //     let add = 0;
      //     if(this.value === 'decal') add = 20000;
      //     else if(this.value === 'kim') add = 50000;
      //     else if(this.value === 'combo') add = 65000;
      //     const total = basePrice + add;
      //     totalPrice.textContent = 'Tổng: ' + total.toLocaleString('vi-VN') + ' VNĐ';
      //   });
      // }
    })();
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
