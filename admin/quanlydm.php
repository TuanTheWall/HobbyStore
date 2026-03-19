<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

$fname = $_GET['fname'] ?? "";
$desc = $_GET['desc'] ?? "";

# Pagination
$limit = 5;
$page = $_GET['page'] ?? 1;
$start = ($page-1)*$limit;

$sql = "SELECT * FROM categories WHERE 1";

if($fname!=""){
$sql .= " AND name LIKE '%$fname%'";
}

if($desc!=""){
$sql .= " AND Description LIKE '%$desc%'";
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
<title>Quản lý danh mục</title>
<link rel="stylesheet" href="assets/css/categorystyle.css">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Josefin Sans", sans-serif; }
  body { background-color: #f5f5f5; }
  .bt-them {
    background: #04a4b3;
    color: white;
    font-size: 16px;
    padding: 8px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
  }
  .bt-them:hover { opacity: 0.85; }
  .themsp { margin: 20px 0 0 30px; display: inline-block; }
</style>
</head>

<body>

<?php 
include "navbar.php";
include "menucard.php";
?>

<form action="themdm.php" method="get" class="themsp">
<button type="submit" class="bt-them">
<i class="fa-solid fa-plus"></i>Thêm danh mục
</button>
</form>


<div class="search-box">

<form method="GET" class="search-advanced">

<label style="font-size:25px;">Tìm theo tên:</label><br>

<input
type="text"
name="fname"
value="<?php echo $fname ?>"
placeholder="Name"
style="font-size:20px;width:220px;height:38px;"
><br>

<label style="font-size:25px;">Tìm theo mô tả:</label><br>

<input
type="text"
name="desc"
value="<?php echo $desc ?>"
placeholder="Description"
style="font-size:20px;width:220px;height:38px;"
><br>

<button type="submit" class="btn-tim" style="height:38px;">Tìm</button>

</form>

</div>


<table>

<tr>
<th>STT</th>
<th>Tên</th>
<th>Mô tả</th>
<th>Thao tác</th>
</tr>

<?php

$stt = $start+1;

while($row = mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $stt++ ?></td>

<td><?php echo $row['name'] ?></td>

<td><?php echo $row['description'] ?></td>

<td class="thaotac">

<form method="GET">

<input type="hidden" name="id" value="<?php echo $row['ID'] ?>">

<button
type="submit"
formaction="suadm.php"
class="bt-them"
style="background:yellow;color:black;"
>
Sửa
</button>

<button
type="submit"
formaction="xoadm.php"
class="bt-them"
style="background:red;color:black;"
onclick="return confirm('Bạn có chắc muốn xóa?')"
>
Xóa
</button>

</form>

</td>

</tr>

<?php } ?>

</table>


<div class="pagination" style="margin-top:100px;margin-bottom:50px;">

<?php if($page>1){ ?>

<a href="?page=<?php echo $page-1 ?>&fname=<?php echo $fname ?>&desc=<?php echo $desc ?>">&laquo;</a>

<?php } ?>

<?php

for($i=1;$i<=$total_page;$i++){

?>

<a
href="?page=<?php echo $i ?>&fname=<?php echo $fname ?>&desc=<?php echo $desc ?>"
class="<?php if($i==$page) echo 'active'; ?>"
>

<?php echo $i ?>

</a>

<?php } ?>

<?php if($page<$total_page){ ?>

<a href="?page=<?php echo $page+1 ?>&fname=<?php echo $fname ?>&desc=<?php echo $desc ?>">&raquo;</a>

<?php } ?>

</div>

</body>
</html>