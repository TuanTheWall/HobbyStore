<?php

$conn = new mysqli("localhost","root","","hobbystore");
$conn->set_charset("utf8");

/* lấy toàn bộ sản phẩm từ DB cho dropdown */
$products_result = $conn->query("SELECT ProductID, ProductName FROM product_list ORDER BY Grade, ProductName");
$products = [];
while($p = $products_result->fetch_assoc()){
    $products[] = $p;
}

/* tạo option HTML để dùng trong PHP và JS */
$options_html = '<option value="">Chọn sản phẩm</option>';
foreach($products as $p){
    $options_html .= '<option value="' . htmlspecialchars($p['ProductID']) . '">' . htmlspecialchars($p['ProductID'] . ' - ' . $p['ProductName']) . '</option>';
}

if(isset($_POST['receipt_code'])){

  $receipt_code = $conn->real_escape_string($_POST['receipt_code']);
  $import_date  = $conn->real_escape_string($_POST['import_date']);

  $sql = "INSERT INTO purchase_receipts(receipt_code,import_date)
          VALUES('$receipt_code','$import_date')";

  $conn->query($sql);

  if(isset($_POST['product_id'])){

    $product = $_POST['product_id'];
    $price   = $_POST['price'];
    $qty     = $_POST['quantity'];

    for($i = 0; $i < count($product); $i++){

      $p  = $conn->real_escape_string($product[$i]);
      $pr = preg_replace('/[^\d]/', '', $price[$i]);
      $q  = intval($qty[$i]);

      if($p === '' || $pr === '' || $q <= 0) continue;

      // Chỉ INSERT vào purchase_receipt_items
      // Trigger trg_import_product        → tự UPDATE Quantity + tính giá bình quân
      // Trigger trg_insert_history_import → tự ghi history
      // Trigger trg_insert_receipt_item   → tự cập nhật total_quantity, total_value
      $sql2 = "INSERT INTO purchase_receipt_items(receipt_code,product_id,quantity,price)
               VALUES('$receipt_code','$p','$q','$pr')";

      $conn->query($sql2);
    }
  }

  header("Location: quanlynhaphang.php");
  exit();
  
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thêm phiếu nhập</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.pagination{ text-align:center; }
.pagination a{ color: black; text-decoration: none; padding: 8px 15px; display: inline-block; }
.pagination a.active{ background-color: green; font-weight: bold; border-radius: 5px; }
.pagination a:hover:not(.active){ background-color: gray; border-radius: 5px; }
.cch{ color:black; } .cch:hover{ color:red; }

nav.navbar {
  display: flex !important; justify-content: space-between !important; align-items: center !important;
  background-color: cyan !important; height: 80px !important; min-height: 80px !important;
  padding: 0 24px !important; box-sizing: border-box !important; gap: 12px; z-index: 50;
}
.navbar .navbar-logo {
  width: 64px !important; height: 64px !important; min-width: 64px !important; min-height: 64px !important;
  border-radius: 50% !important; object-fit: cover !important; display: inline-block !important;
  filter: none !important; mix-blend-mode: normal !important; background: transparent !important; opacity: 1 !important;
}
.nav-left { display: flex !important; align-items: center !important; gap: 18px; }
.nav-left .nav-home { text-decoration: none; color: black; font-size: 20px; font-weight: 600; }
.nav-right { display: flex !important; align-items: center !important; gap: 14px; }
.nav-right .hello { font-weight: 600; color: black; }
.logout-btn {
  background-color: rgb(221, 99, 225) !important; border: 2px solid black !important;
  border-radius: 5px !important; padding: 8px 12px !important; color: black !important;
  text-decoration: none !important; font-weight: 700 !important;
}
.nav-left a:hover, .nav-right a:hover, .nav-right .hello:hover { color: red !important; }

.overview-menu { padding: 30px; background-color: white; }
.overview-menu h2 { font-size: 22px; color: black; margin-bottom: 20px; }
.menu-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
.menu-card {
  background: linear-gradient(to bottom,cyan,blue); color: #fff; text-align: center;
  padding: 30px 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: all 0.25s ease; cursor: pointer;
}
.menu-card h3 { font-size: 16px; font-weight: 600; }
.menu-card:hover { transform: translateY(-5px); box-shadow: 0 6px 18px rgba(0,0,0,0.15); }
.menu-card a { color:white; text-decoration:none; }

.wrap {
  position: relative; z-index: 1; background: rgba(255,255,255,0.95); padding: 28px;
  border-radius: 12px; max-width: 1100px; margin: 36px auto;
  box-shadow: 0 14px 30px rgba(0,0,0,0.12); backdrop-filter: blur(4px);
}
.left-po { width: 650px; flex-shrink: 0; }
.add-info { margin-top:50px; font-size:25px; padding-bottom:10px; width:90%; }
.add-info input, .add-info select {
  display: block; margin-bottom: 1px; font-size: 20px;
  width:600px !important; height: 40px; border: 1px solid #aaa; border-radius: 6px;
}
.add-info select, .add-info textarea { width: 600px; font-size: 20px; border: 1px solid #aaa; border-radius: 6px; }
.bt-them {
  background: #04a4b3; color: white; font-size: 18px; padding: 10px 25px;
  border: none; border-radius: 8px; cursor: pointer; transition: 0.3s;
}
.khung { display: flex; justify-content: center; gap: 20px; margin-top: 50px; }
.add-info label { display: block; margin-top: 15px; margin-bottom: 8px; font-weight: 600; }
.add-info input { margin-bottom: 20px; }

.nhap-hang-box {
  background-color: #f2f2f2; padding: 20px; border-radius: 10px;
  max-width: 100%; margin-top: 10px; margin-bottom: 20px; box-sizing: border-box;
}
.nhap-hang-box select, .nhap-hang-box input {
  font-size: 18px; padding: 10px; border-radius: 6px; border: 1px solid #ccc; box-sizing: border-box;
}

.summary-box {
  background-color: #f8f9fa; border-radius: 10px; padding: 20px 30px;
  max-width: 600px; margin-top: 20px; margin-left: 150px;
}
.summary-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 10px 0; border-bottom: 1px solid #ddd; font-size: 18px; color: #22314e;
}

.product-item {
  display: flex; align-items: center; justify-content: flex-start; gap: 18px;
  background: #fff; border: 1px solid #d9d9d9; border-radius: 10px; padding: 12px;
  margin-top: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); flex-wrap: wrap;
}
.product-item select.sanpham-select {
  width: 320px; min-width: 180px; max-width: 45%; font-size: 16px; padding: 8px 10px;
  border-radius: 6px; border: 1px solid #ccc; background: #fff; box-sizing: border-box;
}
.product-item .gia {
  width: 200px; min-width: 120px; font-size: 16px; padding: 8px 10px;
  border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;
}
.product-item .soluong {
  width: 80px; min-width: 60px; font-size: 16px; padding: 8px 10px;
  border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box;
}
.product-item .xoa {
  background: #ff2b2b; color: #fff; border: none; border-radius: 6px;
  font-size: 14px; font-weight: 600; padding: 8px 12px; cursor: pointer;
  transition: background 0.2s; margin-left: auto; flex-shrink: 0;
}
.product-item .xoa:hover { background: #cc2222; }

@media (max-width: 700px) {
  .product-item { gap: 12px; padding: 10px; }
  .product-item select.sanpham-select { width: 100%; max-width: 100%; }
  .product-item .gia { width: 48%; }
  .product-item .soluong { width: 40%; }
  .product-item .xoa { margin-left: 0; }
}
</style>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<nav class="navbar">
  <div class="nav-left">
    <a href="admin.php" class="logo-link">
      <img src="assets/img/logo.png" alt="Logo" class="navbar-logo">
    </a>
    <a href="admin.php" class="nav-home">Trang chủ</a>
  </div>
  <div class="nav-right">
    <span class="hello">Xin chào, Admin</span>
    <a href="adminlogin.php" class="logout-btn">Đăng xuất</a>
  </div>
</nav>

<body>
<section class="overview-menu">
  <h2>Mục quản lý</h2>
  <div class="menu-grid">
    <a href="customer.php"><div class="menu-card"><h3>Quản lý khách hàng</h3></div></a>
    <a href="shiping.php"><div class="menu-card"><h3>Quản lý đơn hàng</h3></div></a>
    <a href="quanlysp.php"><div class="menu-card"><h3>Quản lý sản phẩm</h3></div></a>
    <a href="lndm.php"><div class="menu-card"><h3>Quản lý giá bán</h3></div></a>
    <a href="quanlydm.php"><div class="menu-card"><h3>Quản lý danh mục</h3></div></a>
    <a href="tracuusoluong.php"><div class="menu-card"><h3>Quản lý tồn kho</h3></div></a>
    <a href="quanlynhaphang.php">
      <div class="menu-card" style="background: linear-gradient(to bottom,#19113b,blue);">
        <h3>Quản lý nhập hàng</h3>
      </div>
    </a>
  </div>
</section>

<div class="wrap">
  <div style="display:flex;gap:10px;align-items:center;margin-bottom:14px;">
    <h1>Thêm phiếu nhập</h1>
  </div>

  <form action="thempn.php" method="post" id="mainForm">
    <div class="add-info">
      <div class="left-po">
        <label for="import_date">Ngày nhập</label>
        <input type="date" name="import_date" style="font-size:20px;">
        <label for="receipt_code">Mã phiếu nhập</label>
        <input style="color:gray;" type="text" name="receipt_code" placeholder="Mã">
        <label style="font-size:25px;">Danh sách sản phẩm nhập</label>
        <button type="button" id="addProductBtn" class="bt-them" style="background:Green;color:white;margin-top:-10px;">Thêm sản phẩm</button>

        <div class="nhap-hang-box">
          <div class="product-item">
            <select name="product_id[]" class="sanpham-select">
              <?php echo $options_html; ?>
            </select>
            <input type="text"   name="price[]"    class="gia"     placeholder="Nhập giá...">
            <input type="number" name="quantity[]" class="soluong" min="1" value="1">
            <button type="button" class="xoa">Xóa</button>
          </div>
        </div>
      </div>
    </div>

    <div class="summary-box">
      <div class="summary-row">
        <span class="label">Tổng số sản phẩm:</span>
        <span class="value">1</span>
      </div>
      <div class="summary-row">
        <span class="label">Tổng số lượng:</span>
        <span class="value">1</span>
      </div>
      <div class="summary-row total">
        <span class="label">Tổng giá trị:</span>
        <span class="value highlight">0 VNĐ</span>
      </div>
    </div>

    <div class="khung">
      <button type="button" class="bt-them" style="background:gray;color:white;"
        onclick="window.location='quanlynhaphang.php'">Hủy</button>
      <button type="submit" class="bt-them" style="background:rgb(19, 42, 127);color:white;">Thêm phiếu nhập</button>
    </div>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const addBtn       = document.getElementById("addProductBtn");
  const boxContainer = document.querySelector(".nhap-hang-box");
  const optionsHtml  = <?php echo json_encode($options_html); ?>;

  function createProductBox() {
    const box = document.createElement("div");
    box.className = "product-item";
    box.innerHTML = `
      <select name="product_id[]" class="sanpham-select">${optionsHtml}</select>
      <input type="text"   name="price[]"    class="gia"     placeholder="Nhập giá..." />
      <input type="number" name="quantity[]" class="soluong" min="1" value="1" />
      <button type="button" class="xoa">Xóa</button>
    `;
    box.querySelector(".xoa").addEventListener("click", function() {
      box.remove();
      tinhTong();
    });
    return box;
  }

  function tinhTong() {
    const items = document.querySelectorAll(".product-item");
    let tongSP = items.length, tongSL = 0, tongGia = 0;
    items.forEach(item => {
      const raw = item.querySelector(".gia").getAttribute("data-raw") ||
                  item.querySelector(".gia").value.replace(/[^\d]/g, "");
      const gia = parseFloat(raw) || 0;
      const sl  = parseInt(item.querySelector(".soluong").value) || 0;
      tongSL  += sl;
      tongGia += gia * sl;
    });
    document.querySelector(".summary-row:nth-child(1) .value").textContent = tongSP;
    document.querySelector(".summary-row:nth-child(2) .value").textContent = tongSL;
    document.querySelector(".summary-row:nth-child(3) .value").textContent = tongGia.toLocaleString("vi-VN") + " VNĐ";
  }

  boxContainer.addEventListener("input", function(e) {
    if (e.target.classList.contains("gia")) {
      let raw = e.target.value.replace(/[^\d]/g, "");
      e.target.setAttribute("data-raw", raw);
      e.target.value = raw.length > 0 ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, ".") : "";
    }
    tinhTong();
  });

  boxContainer.addEventListener("click", function(e) {
    if (e.target.classList.contains("xoa")) {
      e.target.closest(".product-item").remove();
      tinhTong();
    }
  });

  addBtn.addEventListener("click", function() {
    const newBox = createProductBox();
    newBox.querySelector(".gia").addEventListener("input", function(e) {
      let raw = e.target.value.replace(/[^\d]/g, "");
      e.target.setAttribute("data-raw", raw);
      e.target.value = raw.length > 0 ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, ".") : "";
      tinhTong();
    });
    newBox.querySelector(".soluong").addEventListener("input", tinhTong);
    boxContainer.appendChild(newBox);
    tinhTong();
  });

  // Trước khi submit: đổi giá về số thuần để PHP nhận đúng
  document.getElementById("mainForm").addEventListener("submit", function() {
    document.querySelectorAll(".gia").forEach(function(input) {
      const raw = input.getAttribute("data-raw") || input.value.replace(/[^\d]/g, "");
      input.value = raw;
    });
  });

  tinhTong();
});
</script>

</body>
</html>