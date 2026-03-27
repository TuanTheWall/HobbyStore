<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

// Load danh mục động từ database
$grade_options = [];
$grade_query = mysqli_query($conn, "SELECT DISTINCT Grade FROM product_list ORDER BY Grade");
while($grade_row = mysqli_fetch_assoc($grade_query)){
    $grade_options[] = $grade_row['Grade'];
}

// Tạo bảng admin_settings nếu chưa tồn tại
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'admin_settings'");
if(mysqli_num_rows($check_table) == 0){
    mysqli_query($conn, "CREATE TABLE admin_settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(255) UNIQUE,
        setting_value VARCHAR(255)
    )");
}

// Xử lý lưu ngưỡng cảnh báo
if(isset($_POST['save_threshold'])){
    $new_threshold = (int)$_POST['threshold_value'];
    if($new_threshold < 0) $new_threshold = 0;
    
    // Kiểm tra xem có record trong settings không
    $check = mysqli_query($conn, "SELECT * FROM admin_settings WHERE setting_key = 'alert_threshold'");
    
    if(mysqli_num_rows($check) > 0){
        // Update nếu đã tồn tại
        mysqli_query($conn, "UPDATE admin_settings SET setting_value = '$new_threshold' WHERE setting_key = 'alert_threshold'");
    } else {
        // Insert nếu chưa tồn tại
        mysqli_query($conn, "INSERT INTO admin_settings (setting_key, setting_value) VALUES ('alert_threshold', '$new_threshold')");
    }
}

$fname = "";
$category = "";
$from = "";
$to = "";
$threshold = 10;

// Lấy ngưỡng từ database
$threshold_result = mysqli_query($conn, "SELECT setting_value FROM admin_settings WHERE setting_key = 'alert_threshold'");
if($threshold_result && mysqli_num_rows($threshold_result) > 0){
    $threshold_row = mysqli_fetch_assoc($threshold_result);
    $threshold = (int)$threshold_row['setting_value'];
}

if(isset($_GET['fname'])){
    $fname = $_GET['fname'];
}

if(isset($_GET['chat'])){
    $category = $_GET['chat'];
}

if(isset($_GET['from'])){
    $from = $_GET['from'];
}

if(isset($_GET['to'])){
    $to = $_GET['to'];
}

if(isset($_GET['threshold'])){
    $threshold = (int)$_GET['threshold'];
    if($threshold < 0) $threshold = 0;
}

# Pagination
$limit = 5;

$page = isset($_GET['page']) ? $_GET['page'] : 1;

$start = ($page-1)*$limit;

$sql = "SELECT DISTINCT p.* FROM product_list p LEFT JOIN history h ON p.ProductID = h.ProductID WHERE (p.Quantity <= $threshold)";

if($fname != ""){
    $sql .= " AND p.ProductName LIKE '%$fname%'";
}

if($category != "" && $category != "cl"){
    $sql .= " AND p.Grade='$category'";
}

if($from != ""){
    $sql .= " AND h.update_date >= '$from'";
}

if($to != ""){
    $sql .= " AND h.update_date <= '$to'";
}

# Đếm tổng record
$total_query = mysqli_query($conn,$sql);
$total_rows = mysqli_num_rows($total_query);
$total_page = ceil($total_rows/$limit);

$sql .= " LIMIT $start,$limit";

$result = mysqli_query($conn,$sql);

# Thống kê
$total_products = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM product_list"))[0];

$total_stock = mysqli_fetch_row(mysqli_query($conn,"SELECT SUM(Quantity) FROM product_list"))[0];

$total_low = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM product_list WHERE Quantity <= $threshold AND Quantity > 0"))[0];

$total_out = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM product_list WHERE Quantity = 0"))[0];

?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cảnh báo tồn kho</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:"Josefin Sans",sans-serif; }
    body { background-color:#f5f5f5; }
    nav.navbar { display:flex !important; justify-content:space-between !important; align-items:center !important; background-color:cyan !important; height:80px !important; min-height:80px !important; padding:0 24px !important; box-sizing:border-box !important; gap:12px; z-index:50; }
    .navbar .navbar-logo { width:64px !important; height:64px !important; min-width:64px !important; min-height:64px !important; border-radius:50% !important; object-fit:cover !important; display:inline-block !important; filter:none !important; mix-blend-mode:normal !important; background:transparent !important; opacity:1 !important; }
    .nav-left { display:flex !important; align-items:center !important; gap:18px; }
    .nav-left .nav-home { text-decoration:none; color:black; font-size:20px; font-weight:600; }
    .nav-right { display:flex !important; align-items:center !important; gap:14px; }
    .nav-right .hello { font-weight:600; color:black; }
    .logout-btn { background-color:rgb(221,99,225) !important; border:2px solid black !important; border-radius:5px !important; padding:8px 12px !important; color:black !important; text-decoration:none !important; font-weight:700 !important; }
    .nav-left a:hover,.nav-right a:hover { color:red !important; }

    .overview-menu { padding:30px; background-color:white; }
    .overview-menu h2 { font-size:22px; color:black; margin-bottom:20px; }
    .menu-grid { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; }
    .menu-card { background:linear-gradient(to bottom,cyan,blue); color:#fff; text-align:center; padding:30px 20px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.1); transition:all 0.25s ease; cursor:pointer; }
    .menu-card h3 { font-size:16px; font-weight:600; }
    .menu-card:hover { transform:translateY(-5px); }
    .menu-card a { color:white; text-decoration:none; display:block; }

    .headtext { text-align:center; margin:20px 0; }
    .headtext a { text-decoration:none; display:inline-block; margin:0 15px; }
    .headtext p { font-size:18px; color:#333; margin:5px 0; }
    .underline-text { font-size:18px; font-weight:bold; color:#333; text-decoration:underline; text-underline-offset:4px; }

    .search-box { text-align:center; margin:20px; }
    .search-advanced { display:flex; align-items:center; justify-content:center; gap:20px; flex-wrap:wrap; }
    .search-advanced label { font-size:20px; font-weight:bold; margin-right:6px; }
    .search-advanced input, .search-advanced select { width:200px; height:38px; padding:6px 10px; font-size:16px; border:1px solid #aaa; border-radius:6px; }
    .search-advanced button { background-color:blue; color:white; padding:8px 20px; font-size:16px; border:none; border-radius:6px; cursor:pointer; height:40px; transition:0.25s; }
    .search-advanced button:hover { background-color:darkblue; }

    .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; margin:30px auto; max-width:800px; }
    .stat-card { background:white; padding:20px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); text-align:center; }
    .stat-card h3 { font-size:24px; color:#333; margin-bottom:5px; }
    .stat-card p { color:#666; font-size:14px; }

    table { border-collapse:collapse; width:90%; margin:20px auto; background:white; }
    th, td { border:1px solid #ddd; padding:12px; text-align:center; }
    th { background:#96dee0; font-weight:600; }
    tr:hover { background:#f9f9f9; }

    .pagination { text-align:center; margin:30px 0; }
    .pagination a { color:black; text-decoration:none; padding:8px 15px; display:inline-block; }
    .pagination a.active { background-color:green; font-weight:bold; border-radius:5px; }
    .pagination a:hover:not(.active) { background-color:gray; border-radius:5px; }

    .low-stock { background:#fff3cd; }
    .out-stock { background:#f8d7da; }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="nav-left">
    <a href="admin.php" class="logo-link"><img src="assets/img/logo.png" alt="Logo" class="navbar-logo"></a>
    <a href="admin.php" class="nav-home">Trang chủ</a>
  </div>
  <div class="nav-right">
    <span class="hello">Xin chào, Admin</span>
    <a href="adminlogin.php" class="logout-btn">Đăng xuất</a>
  </div>
</nav>

<section class="overview-menu">
  <h2>Mục quản lý</h2>
  <div class="menu-grid">
    <a href="customer.php"><div class="menu-card"><h3>Quản lý khách hàng</h3></div></a>
    <a href="shiping.php"><div class="menu-card"><h3>Quản lý đơn hàng</h3></div></a>
    <a href="quanlysp.php"><div class="menu-card"><h3>Quản lý sản phẩm</h3></div></a>
    <a href="lndm.php"><div class="menu-card"><h3>Quản lý giá bán</h3></div></a>
    <a href="quanlydm.php"><div class="menu-card"><h3>Quản lý danh mục</h3></div></a>
    <a href="tracuusoluong.php"><div class="menu-card"><h3>Quản lý tồn kho</h3></div></a>
    <a href="quanlynhaphang.php"><div class="menu-card"><h3>Quản lý nhập hàng</h3></div></a>
  </div>
</section>

<div class="headtext">

<a href="tracuusoluong.php">
<p>Tra cứu tồn kho</p>
</a>

<a href="canhbao.php">
<p class="underline-text" style="color:red;">Cảnh báo tồn kho</p>
</a>

<a href="baocao.php">
<p>Báo cáo nhập-xuất-tồn</p>
</a>

</div>


<div class="search-box">

<form method="GET" class="search-advanced">

<label style="font-size:25px;">Tìm theo tên</label><br>

<input
type="text"
name="fname"
value="<?php echo $fname ?>"
placeholder="Name"
style="font-size:20px;width:220px;height:38px;"
><br>

<label style="font-size:25px;">Danh mục</label><br>

<select name="chat">

<option value="cl" <?php if($category=="cl") echo "selected"; ?>>Tất cả</option>
<?php foreach($grade_options as $g): ?>
  <option value="<?php echo htmlspecialchars($g); ?>" <?php if($category===$g) echo "selected"; ?>><?php echo htmlspecialchars($g); ?></option>
<?php endforeach; ?>

</select><br>

<label style="font-size:25px;">Từ</label><br>
<input type="date" name="from" value="<?php echo $from ?>">

<label style="font-size:25px;">Đến</label><br>
<input type="date" name="to" value="<?php echo $to ?>">

<br><br>

<button type="submit" class="btn-tim">Tìm</button>

<a href="canhbao.php">
<button type="button" class="btn-tim" style="background:gray;">Đặt lại</button>
</a>

</form>

</div>

<!-- Form lưu ngưỡng cảnh báo -->
<div class="search-box">
<form method="POST" style="display:flex; justify-content:center; gap:20px; align-items:flex-end; flex-wrap:wrap;">
<div>
<label style="font-size:20px; font-weight:bold;">Cài đặt ngưỡng cảnh báo (≤)</label><br>
<input type="number" name="threshold_value" value="<?php echo $threshold ?>" min="0" style="font-size:18px;width:150px;height:38px; margin-top:8px;">
</div>
<button type="submit" name="save_threshold" style="background-color:#28a745; color:white; padding:8px 20px; font-size:16px; border:none; border-radius:6px; cursor:pointer; height:40px; transition:0.25s;">Lưu ngưỡng</button>
</form>
</div>



<table>

<tr>
<th style="background:#545ceb;">Tổng số sản phẩm</th>
<th style="background:#545ceb;">Sản phẩm tồn kho</th>
<th style="background:#545ceb;">Tổng số sản phẩm sắp hết</th>
<th style="background:#545ceb;">Tổng số sản phẩm đã hết hàng</th>
</tr>

<tr>
<td><?php echo $total_products ?></td>
<td><?php echo $total_stock ?></td>
<td><?php echo $total_low ?></td>
<td><?php echo $total_out ?></td>
</tr>

</table>


<table>

<tr>
<th>STT</th>
<th>Tên</th>
<th>Hình ảnh</th>
<th>Số lượng</th>
<th>Tình trạng</th>
</tr>

<?php

$stt = $start + 1;

while($row = mysqli_fetch_assoc($result)){

$qty = $row['Quantity'];

if($qty == 0){
$status = "Hết hàng";
$color = "#e0102f";
}
else{
$status = "Sắp hết hàng";
$color = "#f5f25f";
}

?>

<tr>

<td><?php echo $stt++ ?></td>

<td><?php echo $row['ProductName'] ?></td>

<td>
<img src="assets/img/<?php echo $row['Product_image'] ?>" width="200">
</td>

<td><?php echo $qty ?></td>


<td style="background:<?php echo $color ?>">
<?php echo $status ?>
</td>

</tr>

<?php } ?>

</table>


<div class="pagination" style="margin-top:100px;margin-bottom:50px;">

<?php if($page>1){ ?>

<a href="?page=<?php echo $page-1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>&from=<?php echo $from ?>&to=<?php echo $to ?>&threshold=<?php echo $threshold ?>">&laquo;</a>

<?php } ?>

<?php

for($i=1;$i<=$total_page;$i++){

?>

<a
href="?page=<?php echo $i ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>&from=<?php echo $from ?>&to=<?php echo $to ?>&threshold=<?php echo $threshold ?>"
class="<?php if($i==$page) echo 'active'; ?>"
>

<?php echo $i ?>

</a>

<?php } ?>

<?php if($page<$total_page){ ?>

<a href="?page=<?php echo $page+1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>&from=<?php echo $from ?>&to=<?php echo $to ?>&threshold=<?php echo $threshold ?>">&raquo;</a>

<?php } ?>

</div>

</body>
</html>