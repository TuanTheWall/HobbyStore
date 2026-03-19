<?php
include "connect.php";

$id=$_GET['id'];

$sql="DELETE FROM categories WHERE category_id=$id";

mysqli_query($conn,$sql);

header("location:quanlydm.php");
?>