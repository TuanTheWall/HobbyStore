<?php
$conn = new mysqli("localhost","root","","hobbystore");
$conn->set_charset("utf8");

$from = isset($_GET['from']) ? $_GET['from'] : "";
$to = isset($_GET['to']) ? $_GET['to'] : "";

$sql = "SELECT * FROM purchase_receipts WHERE 1";

if($from != "" && $to != ""){
    $sql .= " AND import_date BETWEEN '$from' AND '$to'";
}

$sql .= " ORDER BY import_date DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE php>
<php lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý nhập hàng</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.pagination{
	text-align:center;
}
.pagination a{
	color: black;
	text-decoration: none;
	padding: 8px 15px;
	display: inline-block;
}
.pagination a.active{
	background-color: green;
	font-weight: bold;
	border-radius: 5px;
}
.pagination a:hover:not(.active){
	background-color: gray;
	border-radius: 5px;
}
.search{
	text-align:center;
	margin: 20px;
}
.search-advanced {
  text-align: center; /* canh giữa nội dung bên trong form */
  margin: 20px 0;     /* khoảng cách trên/dưới */
}
.search-advanced button {
  background-color: blue;
  color: white;
  padding: 12px 24px;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.cch{
 color:black;
}
.cch:hover{
color:red;
}
nav.navbar {
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  background-color: cyan !important;
  height: 80px !important;
  min-height: 80px !important;
  padding: 0 24px !important;
  box-sizing: border-box !important;
  gap: 12px;
  z-index: 50;
}

/* LOGO: ép kích thước, loại mọi filter và giữ tỉ lệ */
.navbar .navbar-logo {
  width: 64px !important;
  height: 64px !important;
  min-width: 64px !important;
  min-height: 64px !important;
  border-radius: 50% !important;
  object-fit: cover !important;
  display: inline-block !important;

  /* vô hiệu hóa mọi filter / blend mode từ file khác */
  filter: none !important;
  -webkit-filter: none !important;
  mix-blend-mode: normal !important;
  background: transparent !important;
  opacity: 1 !important;
}

/* bố cục trái */
.nav-left {
  display: flex !important;
  align-items: center !important;
  gap: 18px;
}

/* chữ Trang chủ */
.nav-left .nav-home {
  text-decoration: none;
  color: black;
  font-size: 20px;
  font-weight: 600;
}

/* phải */
.nav-right {
  display: flex !important;
  align-items: center !important;
  gap: 14px;
}

/* "Xin chào" */
.nav-right .hello {
  font-weight: 600;
  color: black;
}

/* Nút đăng xuất */
.logout-btn {
  background-color: rgb(221, 99, 225) !important;
  border: 2px solid black !important;
  border-radius: 5px !important;
  padding: 8px 12px !important;
  color: black !important;
  text-decoration: none !important;
  font-weight: 700 !important;
}

/* hover */
.nav-left a:hover, .nav-right a:hover, .nav-right .hello:hover {
  color: red !important;
}

.btn-col{
margin-top:auto;
}
.price {
  font-size: 14px;
  margin-bottom: 10px;
}


.overview-menu {
  padding: 30px;
  background-color: white;
}

.overview-menu h2 {
  font-size: 22px;
  color: black;
  margin-bottom: 20px;
}

.menu-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center; /* canh giữa toàn bộ các ô */
  gap: 20px;
}

.menu-card {
  background: linear-gradient(to bottom,cyan,blue);
  color: #fff;
  text-align: center;
  padding: 30px 20px;
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  transition: all 0.25s ease;
  cursor: pointer;
}

.menu-card i {
  font-size: 36px;
  margin-bottom: 15px;
}

.menu-card h3 {
  font-size: 16px;
  font-weight: 600;
}

.menu-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

.menu-card a{
color:white;
text-decoration:none;
}
.bt-them{
	background : #04a4b3;
}
.bt-them i{
	margin-right : 8px;
	width : 25px;
	height : 25px;
	font-size : 25px;
	background : white;
	color : black;
}
table{
border-collapse: collapse;
width:90%;
margin: 0 auto;
margin-top: 30px;
}
td{
border:1px solid #dddddd;
text-align: center;
padding: 8px;
}
th{
border:3px solid #dddddd;
text-align: center;
padding: 8px;
background:#96dee0;
}
.search-advanced {
  display: flex;
  align-items: center;       /* căn giữa theo chiều dọc */
  justify-content: center;   /* căn giữa theo chiều ngang */
  gap: 20px;                 /* khoảng cách giữa các phần tử */
  margin: 30px auto;
  flex-wrap: wrap;           /* tự xuống dòng nếu màn hình nhỏ */
}

.search-advanced label {
  font-size: 20px;
  font-weight: bold;
  margin-right: 6px;
}

.search-advanced input,
.search-advanced select {
  width: 220px;              /* đồng đều chiều rộng */
  height: 38px;              /* đồng đều chiều cao */
  padding: 6px 10px;
  font-size: 16px;
  border: 1px solid #aaa;
  border-radius: 6px;
}

.search-advanced button {
  background-color: blue;
  color: white;
  padding: 8px 20px;
  font-size: 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  height: 40px;
  transition: 0.25s;
}

.search-advanced button:hover {
  background-color: darkblue;
}

.input-box input{
  height:30px;
  border-radius: 8px;
  width:100px;
  
  
}
.underline-text {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  text-decoration: underline; /* gạch chân chữ */
  text-underline-offset: 4px; /* khoảng cách giữa chữ và gạch */
}
</style>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<!--Navbar-->
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
  
  <a href="customer.php">
    <div class="menu-card">
      <h3>Quản lý khách hàng</h3>
    </div>
    </a>
    
    <a href="shiping.php">
    <div class="menu-card">
      <h3>Quản lý đơn hàng</h3>
    </div>
    </a>
    
    <a href="quanlysp.php">
    <div class="menu-card" >  
      <h3>Quản lý sản phẩm</h3>
    </div>
    </a>
    
    <a href="lndm.php">
    <div class="menu-card" >   
      <h3>Quản lý giá bán</h3>
    </div>
    </a>
    
    <a href="quanlydm.php">
    <div class="menu-card" > 
      <h3>Quản lý danh mục</h3>
    </div>
    </a>
    
    <a href="tracuusoluong.php">
    <div class="menu-card" ">  
      <h3>Quản lý tồn kho</h3>
    </div>
    </a>
    
    <a href="quanlynhaphang.php">
    <div class="menu-card" style="background: linear-gradient(to bottom,#19113b,blue);">
      <h3>Quản lý nhập hàng</h3>
    </div>
    </a>
  </div>
</section>

<form action="thempn.php" method="get" class="themsp">
<button type="submit" class="bt-them"><i class="fa-solid fa-plus"></i>Thêm phiếu nhập</button>
</form>

	<div class="search-box">
  <form action="quanlynhaphang.php" class="search-advanced">
    <label style="font-size:25px;">Từ</label>
<input type="date" name="from" value="<?php echo $from; ?>">

<label style="font-size:25px;">Đến</label>
<input type="date" name="to" value="<?php echo $to; ?>">

<button type="submit" class="btn-tim">Tìm</button>
<a href="quanlynhaphang.php">
<button type="button" style="height:38px;background:gray;">Đặt lại</button>
</a>
</form> 
	<table>
    <tr>
        <th>Mã phiếu nhập</th>
        <th>Ngày nhập</th>
        <th>Số lượng</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["receipt_code"] . "</td>";
            echo "<td>" . $row["import_date"] . "</td>";
            echo "<td>" . $row["total_quantity"] . "</td>";
            echo "<td>" . number_format($row["total_value"], 0, ',', '.') . " VND</td>";
            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>Không có dữ liệu</td></tr>";
    }
    ?>
		
	</table>
<div class="pagination" style="margin-top:100px;margin-bottom:50px;">
  <a href="quanlynhaphang.php">&laquo;</a>
  <a href="quanlynhaphang.php" class="active">1</a>
  <a href="quanlynhaphang.php">2</a>
  <a href="quanlynhaphang.php">3</a>
  <a href="quanlynhaphang.php">4</a>
  <a href="quanlynhaphang.php">5</a>
  <a href="quanlynhaphang.php">6</a>
  <a href="quanlynhaphang.php">&raquo;</a>
</div>
</body>
</php>
