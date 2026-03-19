<?php
$conn = new mysqli("localhost","root","","hobbystore");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

/* cập nhật profit theo từng sản phẩm */
if(isset($_POST['product_id'])){
    $product_id = $_POST['product_id'];
    $profit = $_POST['profit']/100;
    $sql_update = "UPDATE product_list SET Profit='$profit' WHERE ProductID='$product_id'";
    $conn->query($sql_update);
}

/* tìm kiếm */
$fname = isset($_GET['fname']) ? $_GET['fname'] : '';
$grade = isset($_GET['chat']) ? $_GET['chat'] : 'cl';

$where = "WHERE 1=1";
if(!empty($fname)){
    $fname_safe = $conn->real_escape_string($fname);
    $where .= " AND ProductName LIKE '%$fname_safe%'";
}
$grade_map = [
    'hg' => 'HG',
    'rg' => 'RG',
    'mg' => 'MG',
    'pg' => 'PG',
    'ag' => 'Figure',
];
if($grade !== 'cl' && isset($grade_map[$grade])){
    $grade_val = $grade_map[$grade];
    $where .= " AND Grade='$grade_val'";
}

/* phân trang */
$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

$count_result = $conn->query("SELECT COUNT(*) as total FROM product_list $where");
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $per_page);

$sql = "SELECT ProductID, ProductName, Grade, Profit FROM product_list $where LIMIT $per_page OFFSET $offset";
$result = $conn->query($sql);

/* giữ query string cho pagination */
$query_params = [];
if(!empty($fname)) $query_params[] = "fname=" . urlencode($fname);
if($grade !== 'cl') $query_params[] = "chat=" . urlencode($grade);
$query_string = count($query_params) ? '&' . implode('&', $query_params) : '';
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
  text-align: center;
  margin: 20px 0;
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
.navbar .navbar-logo {
  width: 64px !important;
  height: 64px !important;
  min-width: 64px !important;
  min-height: 64px !important;
  border-radius: 50% !important;
  object-fit: cover !important;
  display: inline-block !important;
  filter: none !important;
  -webkit-filter: none !important;
  mix-blend-mode: normal !important;
  background: transparent !important;
  opacity: 1 !important;
}
.nav-left {
  display: flex !important;
  align-items: center !important;
  gap: 18px;
}
.nav-left .nav-home {
  text-decoration: none;
  color: black;
  font-size: 20px;
  font-weight: 600;
}
.nav-right {
  display: flex !important;
  align-items: center !important;
  gap: 14px;
}
.nav-right .hello {
  font-weight: 600;
  color: black;
}
.logout-btn {
  background-color: rgb(221, 99, 225) !important;
  border: 2px solid black !important;
  border-radius: 5px !important;
  padding: 8px 12px !important;
  color: black !important;
  text-decoration: none !important;
  font-weight: 700 !important;
}
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
  justify-content: center;
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
  align-items: center;
  justify-content: center;
  gap: 20px;
  margin: 30px auto;
  flex-wrap: wrap;
}
.search-advanced label {
  font-size: 20px;
  font-weight: bold;
  margin-right: 6px;
}
.search-advanced input,
.search-advanced select {
  width: 220px;
  height: 38px;
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
  text-decoration: underline;
  text-underline-offset: 4px;
}
.headtext {
  display: flex;
  gap: 30px;
  justify-content: center;
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
      <div class="menu-card">
        <h3>Quản lý sản phẩm</h3>
      </div>
    </a>
    <a href="lndm.php">
      <div class="menu-card" style="background: linear-gradient(to bottom,#19113b,blue);">
        <h3>Quản lý giá bán</h3>
      </div>
    </a>
    <a href="quanlydm.php">
      <div class="menu-card">
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
    <p>TỶ LỆ LỢI NHUẬN THEO DANH MỤC</p>
  </a>
  <a href="lnsp.php">
    <p class="underline-text" style="color:red;">TỶ LỆ LỢI NHUẬN THEO SẢN PHẨM</p>
  </a>
  <a href="ln.php">
    <p>GIÁ BÁN</p>
  </a>
</div>

<div class="search-box">
  <form action="lnsp.php" method="get" class="search-advanced">
    <label style="font-size:25px;" for="fname">Tìm theo tên:</label>
    <input style="color:gray;" type="text" id="fname" name="fname" placeholder="Tên sản phẩm" value="<?php echo htmlspecialchars($fname); ?>">
    <label style="font-size:25px;">Danh mục:</label>
    <select id="chat" name="chat">
      <option value="cl" <?php echo $grade=='cl'?'selected':''; ?>>Tất cả</option>
      <option value="hg" <?php echo $grade=='hg'?'selected':''; ?>>High Grade</option>
      <option value="rg" <?php echo $grade=='rg'?'selected':''; ?>>Real Grade</option>
      <option value="mg" <?php echo $grade=='mg'?'selected':''; ?>>Master Grade</option>
      <option value="pg" <?php echo $grade=='pg'?'selected':''; ?>>Perfect Grade</option>
      <option value="ag" <?php echo $grade=='ag'?'selected':''; ?>>Anime Figure</option>
    </select>
    <button type="submit" class="btn-tim" style="height:38px; margin-top:-4px;">Tìm</button>
    <a href="lnsp.php"><button type="button" style="height:38px; margin-top:-4px; background:gray; color:white; border:none; border-radius:6px; padding:8px 20px; font-size:16px; cursor:pointer;">Đặt lại</button></a>
  </form>

  <table>
    <tr>
      <th>STT</th>
      <th>Tên</th>
      <th>Danh mục</th>
      <th>Tỷ lệ lợi nhuận</th>
      <th>Thao tác</th>
    </tr>
    <?php
    $stt = $offset + 1;
    while($row = $result->fetch_assoc()):
    ?>
    <tr>
      <form method="post" action="lnsp.php?page=<?php echo $page.$query_string; ?>">
        <td><?php echo $stt++; ?></td>
        <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
        <td><?php echo htmlspecialchars($row['Grade']); ?></td>
        <td>
          <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
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
    <?php endwhile; ?>
  </table>

  <div class="pagination" style="margin-top:100px;margin-bottom:50px;">
    <a href="lnsp.php?page=1<?php echo $query_string; ?>">&laquo;</a>
    <?php for($i = 1; $i <= $total_pages; $i++): ?>
      <a href="lnsp.php?page=<?php echo $i.$query_string; ?>" <?php echo $i==$page?'class="active"':''; ?>>
        <?php echo $i; ?>
      </a>
    <?php endfor; ?>
    <a href="lnsp.php?page=<?php echo $total_pages.$query_string; ?>">&raquo;</a>
  </div>
</div>

</body>
</html>
