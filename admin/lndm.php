<?php
$conn = new mysqli("localhost","root","","hobbystore");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

/* cập nhật profit */
if(isset($_POST['grade'])){
    $grade  = $conn->real_escape_string($_POST['grade']);
    $profit = $_POST['profit'] / 100;
    $conn->query("
        UPDATE product_list 
        SET Profit = '$profit',
            Price  = ROUND(cost_price * (1 + $profit))
        WHERE Grade = '$grade'
    ");
}

/* lấy giá trị tìm kiếm */
$fname = isset($_GET['fname']) ? $_GET['fname'] : '';
$chat = isset($_GET['chat']) ? $_GET['chat'] : '';

/* Pagination */
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$start = ($page - 1) * $limit;

/* lấy danh mục với filter */
$sql = "SELECT Grade, MIN(Profit) as Profit FROM product_list WHERE 1";

if($fname != ""){
    $sql .= " AND Grade LIKE '%$fname%'";
}

if($chat != "" && $chat != "cl"){
    // Tìm kiếm theo khoảng tỷ lệ
    if($chat == "hg"){
        $sql .= " AND Profit >= 0 AND Profit < 0.1";
    } elseif($chat == "rg"){
        $sql .= " AND Profit >= 0.1 AND Profit < 0.3";
    } elseif($chat == "mg"){
        $sql .= " AND Profit >= 0.3 AND Profit < 0.5";
    } elseif($chat == "pg"){
        $sql .= " AND Profit >= 0.5";
    }
}

$sql .= " GROUP BY Grade";

// Đếm tổng record
$total_result = $conn->query($sql);
$total_rows = $total_result->num_rows;
$total_page = ceil($total_rows / $limit);

// Lấy dữ liệu theo trang
$sql .= " LIMIT $start, $limit";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý giá bán</title>
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
.thaotac{
	padding-bottom:40px;
	padding-right:25px;
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

.headtext {
  display: flex;           /* cho 2 phần tử nằm ngang */
  gap: 30px;               /* khoảng cách giữa 2 mục */
  justify-content: center; /* căn giữa toàn bộ */
  margin-top: 20px;
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
    <div class="menu-card" style="background: linear-gradient(to bottom,#19113b,blue);">
      <h3>Quản lý giá bán</h3>
    </div>
    </a>
    <a href="quanlydm.php">
    <div class="menu-card" >
      <h3>Quản lý danh mục</h3>
    </div>
    </a>
    <a href="tracuusoluong.php">
    <div class="menu-card">
      <h3>Quản lý tồn kho</h3>
    </div>
    </a>
    <a href="quanlynhaphang.php">
    <div class="menu-card">
      <h3>Quản lý nhập hàng</h3>
    </div>
    </a>
  </div>
</section>
<div class="headtext">
	<a href="lndm.php">
		<p class="underline-text" style="color:red;">TỶ LỆ LỢI NHUẬN THEO DANH MỤC</p>
	</a>
	<!-- <a href="lnsp.php">
		<p >TỶ LỆ LỢI NHUẬN THEO SẢN PHẨM<p>
	</a> -->
	<a href="ln.php">
		<p>GIÁ BÁN<p>
	</a>
	
</div>
	<div class="search-box">
  <form action="lndm.php" method="GET" class="search-advanced">
  <label style="font-size:25px;" for="fname">Tìm theo tên:</label><br>
  	<input style="color:gray;" type="text" id="fname" name="fname" placeholder="Name" value="<?php echo htmlspecialchars($fname); ?>" style="font-size:20px; width:220px; height:38px;"><br>
  	<label style="font-size:25px;" for="chat">Khoảng tỷ lệ:</label><br>
  	  <select id="chat" name="chat">
      <option value="cl" <?php if($chat=="cl" || $chat=="") echo "selected"; ?>>Tất cả</option>
      <option value="hg" <?php if($chat=="hg") echo "selected"; ?>>0-10%</option>
      <option value="rg" <?php if($chat=="rg") echo "selected"; ?>>10%-30%</option>
      <option value="mg" <?php if($chat=="mg") echo "selected"; ?>>30%-50%</option>
      <option value="pg" <?php if($chat=="pg") echo "selected"; ?>>trên 50%</option>
    </select>
<button type="submit" class="btn-tim" style="height:38px; margin-top:-4px;">Tìm</button>
<a href="lndm.php"><button type="button" class="btn-tim" style="height:38px; margin-top:-4px;background:gray;">Đặt lại</button></a>
</form>
	<table>
		<tr>
			<th>STT</th>
			<th>Tên</th>
			<th>Tỷ lệ lợi nhuận</th>
			<th>Thao tác</th>
		</tr>
		<?php 
		if($result->num_rows > 0){
			$i = $start + 1; 
			while($row = $result->fetch_assoc()): 
		?>
		<tr>
			<form method="post" action="lndm.php">
			<td><?php echo $i++; ?></td>
			<td><?php echo $row['Grade']; ?></td>
			<td>
				<input type="hidden" name="grade" value="<?php echo $row['Grade']; ?>">
				<div class="input-profit">
					<div class="input-box">
						<input type="number" name="profit" min="0" max="100" value="<?php echo $row['Profit']*100; ?>">
						<span>%</span>
					</div>
				</div>
			</td>
			<td class="thaotac">
				<button type="submit" class="bt-them" style="background:green;color:white;">Lưu</button>
			</td>
			</form>
		</tr>
		<?php 
			endwhile; 
		} else {
			echo "<tr><td colspan='4' style='text-align:center; padding: 20px;'>Không tìm thấy dữ liệu</td></tr>";
		}
		?>
	</table>
<div class="pagination" style="margin-top:100px;margin-bottom:50px;">
  <?php if($page > 1){ ?>
  <a href="lndm.php?page=1&fname=<?php echo urlencode($fname); ?>&chat=<?php echo urlencode($chat); ?>">&laquo;</a>
  <?php } ?>
  
  <?php for($i = 1; $i <= $total_page; $i++){ ?>
  <a href="lndm.php?page=<?php echo $i; ?>&fname=<?php echo urlencode($fname); ?>&chat=<?php echo urlencode($chat); ?>" class="<?php if($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
  <?php } ?>
  
  <?php if($page < $total_page){ ?>
  <a href="lndm.php?page=<?php echo $total_page; ?>&fname=<?php echo urlencode($fname); ?>&chat=<?php echo urlencode($chat); ?>">&raquo;</a>
  <?php } ?>
</div>
</body>
</html>