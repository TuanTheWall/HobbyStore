<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

$id = $_GET['id'] ?? '';

if($id === ''){
    header("Location: quanlydm.php");
    exit();
}

$sql = "DELETE FROM categories WHERE ID='$id'";
mysqli_query($conn, $sql);

header("Location: quanlydm.php");
exit();
?>