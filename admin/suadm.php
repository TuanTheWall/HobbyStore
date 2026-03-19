<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

$id = $_GET['id'] ?? "";

# Lấy dữ liệu danh mục
$sql = "SELECT * FROM categories WHERE id='$id'";
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);

# Khi nhấn lưu
if(isset($_POST['save'])){

$ten = $_POST['name'];
$mota = $_POST['description'];

$update = "UPDATE categories
SET name='$ten',
description='$mota'
WHERE id='$id'";

mysqli_query($conn,$update);

header("Location: quanlydm.php");
exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Sửa danh mục</title>
<link rel="stylesheet" href="assets/css/dmedit.css">
</head>

<body>

<?php 
include "navbar.php";
include "menucard.php";
?>

<div class="wrap">

<div style="display:flex;gap:10px;align-items:center;margin-bottom:14px;">
<h1>Sửa danh mục</h1>
</div>

<form method="POST">

<div class="add-info">

<div class="left-po">

<label for="fname">Tên danh mục</label><br>

<input
type="text"
name="name"
value="<?php echo $row['name']; ?>"
style="font-size:30px;color:black;"
required
><br>

<label for="info">Mô tả</label><br>

<textarea
name="description"
rows="10"
cols="50"
style="font-size:30px;color:black;padding-top:10px;"
><?php echo $row['description']; ?></textarea>

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
name="save"
class="bt-them"
style="background:rgb(19,42,127);color:white;"
>
Lưu thay đổi
</button>

</div>

</form>

</div>

</body>

</html>