<?php
session_start();
$conn = new mysqli("localhost", "root", "", "hobbystore");
mysqli_set_charset($conn, "utf8mb4");

if ($conn->connect_error) {
    die("Lỗi kết nối CSDL");
}

if (!isset($_GET['id'])) {
    die("Thiếu mã sản phẩm");
}
$id = $_GET['id'];
$stmt = $conn->prepare(
    "SELECT * FROM product_list WHERE ProductID = ?"
);
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Không tìm thấy sản phẩm");
}

$product = $result->fetch_assoc();
$is_out_of_stock = ($product['Quantity'] == 0);

/* ===== XỬ LÝ THÊM GIỎ HÀNG ===== */
if(isset($_POST['add_cart'])){
    if($product['Quantity'] == 0){
        exit("Sản phẩm đã hết hàng");
    }

    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
        exit();
    }

    $customer_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $qty = 1;

    /* tìm cart của user */
   $stmt = $conn->prepare("SELECT cart_id FROM cart WHERE customer_id=? LIMIT 1");
if(!$stmt){
    die("SQL lỗi: " . $conn->error);
}
    $stmt->bind_param("s",$customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $cart = $result->fetch_assoc();
        $cart_id = $cart['cart_id'];
    }else{

        $cart_id = "CART_" . uniqid();

        $stmt = $conn->prepare("INSERT INTO cart(cart_id,customer_id) VALUES(?,?)");
        $stmt->bind_param("ss",$cart_id,$customer_id);
        $stmt->execute();
    }

    /* kiểm tra sản phẩm đã có trong cart chưa */
    $stmt = $conn->prepare(
        "SELECT quantity FROM cart_item 
         WHERE cart_id=? AND product_id=?"
    );
    $stmt->bind_param("ss",$cart_id,$product_id);
    $stmt->execute();
    $check = $stmt->get_result();

    if($check->num_rows > 0){

        $row = $check->fetch_assoc();
        $new_qty = $row['quantity'] + 1;

        $stmt = $conn->prepare(
            "UPDATE cart_item 
             SET quantity=? 
             WHERE cart_id=? AND product_id=?"
        );
        $stmt->bind_param("iss",$new_qty,$cart_id,$product_id);
        $stmt->execute();

    }else{

        $stmt = $conn->prepare(
            "INSERT INTO cart_item(cart_id,product_id,quantity)
             VALUES (?,?,?)"
        );
        $stmt->bind_param("ssi",$cart_id,$product_id,$qty);
        $stmt->execute();
    }

    header("Location: product.php?id=".$product_id);
    exit();
}



$price  = (float)$product['cotton_price']; // ví dụ: 5000001
$profit = (float)$product['Profit']; // ví dụ: 0.3 = 30%

$sell_price = $price * (1 + $profit);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi tiết sản phẩm</title>
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="product.css">
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
  </nav>

  <a href="index.php" class="back-home">← Trở về</a>
 <div class="container">
  <div class="product-image">
    <img src="assets/img/<?= $product['Product_image'] ?>" alt="<?= $product['ProductName'] ?>">
  </div>

  <div class="product-details">
    <h2>
<?= $product['ProductName'] ?>
<?php if($is_out_of_stock): ?>
<span style="color:red; font-size:18px;">(Hết hàng)</span>
<?php endif; ?>
</h2>
    
    <div class="meta-info" aria-hidden="true">
      <div class="badge"><?= $product['Grade'] ?></div>
    </div>
    <div class="small-muted">Nhà sản xuất: <?= $product['Producer'] ?></div>
    
    <br><br><br>
   <div id="total-price" style="font-size:20px; text-indent:10px; color:#007bff;">
  Giá: <?= number_format($sell_price, 0, ',', '.') ?> VNĐ
</div>
    
    <form method="POST" action="product.php?id=<?= $product['ProductID'] ?>">
    <input type="hidden" name="product_id" value="<?= $product['ProductID'] ?>">
    <button type="submit" name="add_cart" id="addCartBtn"
<?php if($is_out_of_stock) echo 'disabled style="opacity:0.5; cursor:not-allowed;"'; ?>>
    Thêm vào giỏ hàng
</button>
</form>
    <br><br>
    
  </div>
  
   <!--Giới thiệu mô hình được đưa ra dưới đây -->
  <div class="product-description">
    <h1>Giới thiệu mô hình:</h1>
    <ul>
<br><h2 style="text-indent: 0px;font-size: 30px;">Nguồn gốc:</h2><p style="display:grid; text-align: left; text-indent: 30px;font-size: 20px;"><?= $product['Product_source'] ?></p>

<h2 style="text-indent: 0px;font-size: 30px;">Mô tả :</h2>
      <p style="text-indent: 30px;" style="font-size:medium;"><?= nl2br($product['Product_description']) ?></p>
      <br>
      <h1>Thông tin mô hình</h1>
         <p><?= nl2br($product['Product_detail']) ?></p>
    </ul>
  </div>
</div>
      <br><br>
  <div class="product-description-tab">
    <div class="content-entry add-height-img max-height-ct lazyload-addclass">
    <div class="view-all-btn">
    </div>

  </div>
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
      const basePrice = 560000;
      const addon = document.getElementById('addon');
      const totalPrice = document.getElementById('total-price');
    
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
    popup.addEventListener('click', (e) => {
      if (e.target === popup) popup.style.display = 'none';
    });
  </script>
</body>
</html>