<?php
include "connect.php";

$id=$_GET['id'];

$sql="SELECT * FROM categories WHERE category_id=$id";
$result=mysqli_query($conn,$sql);
$row=mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

$name=$_POST['name'];
$description=$_POST['description'];

$sql="UPDATE categories
SET name='$name',
description='$description'
WHERE category_id=$id";

mysqli_query($conn,$sql);

header("location:quanlydm.php");
}
?>

<form method="POST">

<h2>Sửa danh mục</h2>

Tên <br>
<input type="text" name="name" value="<?php echo $row['name']; ?>">

<br><br>

Mô tả <br>
<textarea name="description"><?php echo $row['description']; ?></textarea>

<br><br>

<button name="update">Cập nhật</button>

</form>