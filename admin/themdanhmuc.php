<?php
include "connect.php";

if(isset($_POST['add'])){

$name=$_POST['name'];
$description=$_POST['description'];

$sql="INSERT INTO categories(name,description)
VALUES('$name','$description')";

mysqli_query($conn,$sql);

header("location:quanlydm.php");
}
?>

<form method="POST">

<h2>Thêm danh mục</h2>

Tên danh mục <br>
<input type="text" name="name" required>

<br><br>

Mô tả <br>
<textarea name="description"></textarea>

<br><br>

<button name="add">Thêm</button>

</form>