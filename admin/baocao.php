<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

$fname = $_GET['fname'] ?? "";
$category = $_GET['chat'] ?? "";
$from = $_GET['from'] ?? "";
$to = $_GET['to'] ?? "";

# Pagination
$limit = 5;
$page = $_GET['page'] ?? 1;
$start = ($page-1)*$limit;

$sql = "SELECT * FROM product_list WHERE 1";

if($fname!=""){
$sql .= " AND ProductName LIKE '%$fname%'";
}

if($category!="" && $category!="cl"){
$sql .= " AND Grade='$category'";
}

$total_query = mysqli_query($conn,$sql);
$total_rows = mysqli_num_rows($total_query);
$total_page = ceil($total_rows/$limit);

$sql .= " LIMIT $start,$limit";
$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Báo cáo nhập-xuất-tồn</title>
<link rel="stylesheet" href="assets/css/storagestyle.css">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Josefin Sans", sans-serif; }
  body { background-color: #f5f5f5; }
</style>
</head>

<body>

<?php 
include "navbar.php";
include "menucard.php";
?>

<div class="headtext">

<a href="tracuusoluong.php">
<p>Tra cứu tồn kho</p>
</a>

<a href="canhbao.php">
<p>Cảnh báo tồn kho</p>
</a>

<a href="baocao.php">
<p class="underline-text" style="color:red;">Báo cáo nhập-xuất-tồn</p>
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

<option value="cl">Tất cả</option>

<option value="HG" <?php if($category=="HG") echo "selected"; ?>>High Grade</option>
<option value="RG" <?php if($category=="RG") echo "selected"; ?>>Real Grade</option>
<option value="MG" <?php if($category=="MG") echo "selected"; ?>>Master Grade</option>
<option value="PG" <?php if($category=="PG") echo "selected"; ?>>Perfect Grade</option>
<option value="Figure" <?php if($category=="Figure") echo "selected"; ?>>Anime Figure</option>

</select>

<br>

<label style="font-size:25px;">Từ</label><br>
<input type="date" name="from" value="<?php echo $from ?>">

<label style="font-size:25px;">Đến</label><br>
<input type="date" name="to" value="<?php echo $to ?>">

<br><br>

<button type="submit" class="btn-tim">Tìm</button>

<a href="baocao.php">
<button type="button" class="btn-tim" style="background:gray;">Đặt lại</button>
</a>

</form>

</div>


<table>

<tr>
<th>STT</th>
<th>Tên</th>
<th>Hình ảnh</th>
<th>Tồn đầu kỳ</th>
<th>Nhập trong kỳ</th>
<th>Xuất trong kỳ</th>
<th>Tồn cuối kỳ</th>
</tr>

<?php

$stt = $start+1;

while($row = mysqli_fetch_assoc($result)){

$product_id = $row['ProductID'];

$import = 0;
$export = 0;

$q1 = mysqli_query($conn,"
SELECT import_num 
FROM history 
WHERE ProductID='$product_id'

");

$q2 = mysqli_query($conn,"
SELECT export_num 
FROM history 
WHERE ProductID='$product_id'

");
$import = mysqli_fetch_row($q1)[0] ?? 0;
$export = mysqli_fetch_row($q2)[0] ?? 0;


$toncuoi = $row['Quantity'];

$tondau = $toncuoi - $import + $export;

?>

<tr>

<td><?php echo $stt++ ?></td>

<td><?php echo $row['ProductName'] ?></td>

<td>
<img src="assets/img/<?php echo $row['Product_image'] ?>" width="200">
</td>

<td><?php echo $tondau ?></td>

<td><?php echo $import ?></td>

<td><?php echo $export ?></td>

<td><?php echo $toncuoi ?></td>

</tr>

<?php } ?>

</table>


<div class="pagination" style="margin-top:100px;margin-bottom:50px;">

<?php if($page>1){ ?>

<a href="?page=<?php echo $page-1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>">&laquo;</a>

<?php } ?>

<?php

for($i=1;$i<=$total_page;$i++){

?>

<a
href="?page=<?php echo $i ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>"
class="<?php if($i==$page) echo 'active'; ?>"
>

<?php echo $i ?>

</a>

<?php } ?>

<?php if($page<$total_page){ ?>

<a href="?page=<?php echo $page+1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>">&raquo;</a>

<?php } ?>

</div>

</body>
</html>