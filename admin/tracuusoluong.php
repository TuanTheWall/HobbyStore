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

$fname    = $_GET['fname'] ?? "";
$category = $_GET['chat']  ?? "";
$ngay     = $_GET['ngay']  ?? date("Y-m-d");

# Pagination
$limit = 5;
$page  = $_GET['page'] ?? 1;
$start = ($page - 1) * $limit;

$sql = "SELECT DISTINCT p.* FROM product_list p WHERE 1";

if($fname != ""){
    $sql .= " AND p.ProductName LIKE '%$fname%'";
}

if($category != "" && $category != "cl"){
    $sql .= " AND p.Grade='$category'";
}

# Đếm tổng sản phẩm
$total_query = mysqli_query($conn, $sql);
$total_rows  = mysqli_num_rows($total_query);
$total_page  = ceil($total_rows / $limit);

# Lấy dữ liệu theo trang
$sql .= " LIMIT $start,$limit";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Tra cứu tồn kho</title>
<link rel="stylesheet" href="assets/css/storagestyle.css">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Josefin Sans", sans-serif; }
  body { background-color: #f5f5f5; }
</style>
</head>

<body>

<?php include "navbar.php" ?>
<?php include "menucard.php" ?>

<div class="headtext">

<a href="tracuusoluong.php">
<p class="underline-text" style="color:red;">Tra cứu tồn kho</p>
</a>

<a href="canhbao.php">
<p>Cảnh báo tồn kho</p>
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
  placeholder="Name"
  value="<?php echo $fname ?>"
  style="font-size:20px; width:220px; height:38px;"
><br>

<label style="font-size:25px;">Danh mục</label><br>
<select name="chat">
  <option value="cl" <?php if($category=="cl") echo "selected"; ?>>Tất cả</option>
  <?php foreach($grade_options as $g): ?>
    <option value="<?php echo htmlspecialchars($g); ?>" <?php if($category===$g) echo "selected"; ?>><?php echo htmlspecialchars($g); ?></option>
  <?php endforeach; ?>
</select><br>

<label style="font-size:25px;">Xem tồn tại ngày</label><br>
<input
  type="date"
  name="ngay"
  value="<?php echo $ngay ?>"
  style="font-size:20px;width:220px;height:38px;"
><br><br>

<button type="submit" class="btn-tim" style="height:38px;">Tìm</button>

<a href="tracuusoluong.php">
  <button type="button" class="btn-tim" style="height:38px;background:gray;">Đặt lại</button>
</a>

</form>

</div>


<table>

<tr>
<th>STT</th>
<th>Tên</th>
<th>Hình ảnh</th>
<th>Số lượng (tại <?php echo $ngay ?>)</th>
<th>Tình trạng</th>
</tr>

<?php

$stt = $start + 1;

while($row = mysqli_fetch_assoc($result)){

    $product_id  = $row['ProductID'];
    $qty_hientai = (int)$row['Quantity'];

    // Tổng nhập SAU ngày được chọn
    $q_nhap = mysqli_query($conn, "
        SELECT COALESCE(SUM(pri.quantity), 0) AS tong
        FROM purchase_receipt_items pri
        JOIN purchase_receipts pr ON pri.receipt_code = pr.receipt_code
        WHERE pri.product_id = '$product_id'
        AND pr.import_date > '$ngay'
    ");
    $nhap_sau = (int)mysqli_fetch_assoc($q_nhap)['tong'];

    // Tổng xuất SAU ngày được chọn (chỉ đơn Đã giao)
    $q_xuat = mysqli_query($conn, "
        SELECT COALESCE(SUM(oi.quantity), 0) AS tong
        FROM order_item oi
        JOIN orders o ON oi.id_order = o.id_order
        WHERE oi.ProductID = '$product_id'
        AND o.status = 'Đã giao'
        AND o.order_date > '$ngay'
    ");
    $xuat_sau = (int)mysqli_fetch_assoc($q_xuat)['tong'];

    // Tồn tại cuối ngày X = hiện tại - nhập sau ngày X + xuất sau ngày X
    $qty = $qty_hientai - $nhap_sau + $xuat_sau;

    if($qty == 0){
        $status = "Hết hàng";
        $color  = "#e0102f";
    } elseif($qty <= 5){
        $status = "Sắp hết hàng";
        $color  = "#f5f25f";
    } else {
        $status = "Còn hàng";
        $color  = "#36f77a";
    }
?>

<tr>
  <td><?php echo $stt++ ?></td>
  <td><?php echo $row['ProductName'] ?></td>
  <td><img src="assets/img/<?php echo $row['Product_image'] ?>" width="200"></td>
  <td><?php echo $qty ?></td>
  <td style="background:<?php echo $color ?>"><?php echo $status ?></td>
</tr>

<?php } ?>

</table>


<div class="pagination" style="margin-top:100px;margin-bottom:50px;">

<?php if($page > 1): ?>
  <a href="?page=<?php echo $page-1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>&ngay=<?php echo $ngay ?>">&laquo;</a>
<?php endif; ?>

<?php for($i=1; $i <= $total_page; $i++): ?>
  <a
    href="?page=<?php echo $i ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>&ngay=<?php echo $ngay ?>"
    class="<?php if($i == $page) echo 'active'; ?>"
  ><?php echo $i ?></a>
<?php endfor; ?>

<?php if($page < $total_page): ?>
  <a href="?page=<?php echo $page+1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>&ngay=<?php echo $ngay ?>">&raquo;</a>
<?php endif; ?>

</div>

</body>
</html>