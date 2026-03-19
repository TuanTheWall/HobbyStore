<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

if(isset($_POST['add'])){

$ten = $_POST['name'];
$mota = $_POST['description'];

$sql = "INSERT INTO categories(name,description)
VALUES('$ten','$mota')";

mysqli_query($conn,$sql);

header("Location: quanlydm.php");
exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Thêm danh mục</title>
<link rel="stylesheet" href="assets/css/dmadd.css">
</head>

<body>

<?php 
include "navbar.php";
include "menucard.php";
?>

<div class="wrap">

<div style="display:flex;gap:10px;align-items:center;margin-bottom:14px;">
<h1>Thêm danh mục</h1>
</div> 

<form method="POST">

<div class="add-info">

<div class="left-po">

<label for="fname">Tên danh mục</label><br>

<input
type="text"
name="name"
placeholder="Name"
style="font-size:30px;color:black;"
required
><br>

<label for="info">Mô tả</label><br>

<textarea
name="description"
style="width:600px;height:150px;font-size:30px;"
placeholder="Mô tả sản phẩm"
></textarea><br>

</div>

</div>

<div class="khung">

<a href="quanlydm.php">
<button type="button" class="bt-them" style="background:gray;color:white;">
Quay về
</button>
</a>

<button
type="submit"
name="add"
class="bt-them"
style="background:rgb(19,42,127);color:white;"
>
Thêm danh mục
</button>

</div>

</form>

</div>

</body>

</html>