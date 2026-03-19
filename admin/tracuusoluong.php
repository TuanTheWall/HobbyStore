<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

$fname = "";
$category = "";

if(isset($_GET['fname'])){
    $fname = $_GET['fname'];
}

if(isset($_GET['chat'])){
    $category = $_GET['chat'];
}

# Pagination
$limit = 5;

if(isset($_GET['page'])){
    $page = $_GET['page'];
}else{
    $page = 1;
}

$start = ($page - 1) * $limit;

$sql = "SELECT * FROM product_list WHERE 1";

if($fname != ""){
    $sql .= " AND ProductName LIKE '%$fname%'";
}

if($category != "" && $category != "cl"){
    $sql .= " AND Grade='$category'";
}

# Đếm tổng sản phẩm
$total_query = mysqli_query($conn,$sql);
$total_rows = mysqli_num_rows($total_query);
$total_page = ceil($total_rows / $limit);

# Lấy dữ liệu theo trang
$sql .= " LIMIT $start,$limit";

$result = mysqli_query($conn,$sql);

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

<option value="cl">Tất cả</option>

<option value="hg" <?php if($category=="hg") echo "selected"; ?>>High Grade</option>
<option value="rg" <?php if($category=="rg") echo "selected"; ?>>Real Grade</option>
<option value="mg" <?php if($category=="mg") echo "selected"; ?>>Master Grade</option>
<option value="pg" <?php if($category=="pg") echo "selected"; ?>>Perfect Grade</option>
<option value="ag" <?php if($category=="ag") echo "selected"; ?>>Anime Figure</option>

</select>

<br><br>

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
elseif($qty <= 5){
$status = "Sắp hết hàng";
$color = "#f5f25f";
}
else{
$status = "Còn hàng";
$color = "#36f77a";
}

?>

<tr>

<td><?php echo $stt++ ?></td>

<td><?php echo $row['ProductName'] ?></td>

<td>
<img src="assets/img/<?php echo $row['Product_image'] ?>" width="200">
</td>

<td><?php echo $row['Quantity'] ?></td>

<td style="background:<?php echo $color ?>">
<?php echo $status ?>
</td>

</tr>

<?php } ?>

</table>


<div class="pagination" style="margin-top:100px;margin-bottom:50px;">

<?php if($page > 1){ ?>

<a href="?page=<?php echo $page-1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>">&laquo;</a>

<?php } ?>

<?php
for($i=1; $i <= $total_page; $i++){
?>

<a
href="?page=<?php echo $i ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>"
class="<?php if($i == $page) echo 'active'; ?>"
>
<?php echo $i ?>
</a>

<?php } ?>

<?php if($page < $total_page){ ?>

<a href="?page=<?php echo $page+1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>">&raquo;</a>

<?php } ?>

</div>

</body>
</html>