<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

$id = $_GET['name'] ?? "";

# Kiểm tra danh mục có sản phẩm không
$check = mysqli_query($conn,
"SELECT * FROM product_list WHERE Grade='$id'"
);

if(mysqli_num_rows($check) > 0){

echo "<script>
alert('Không thể xóa danh mục vì đang có sản phẩm!');
window.location='quanlydm.php';
</script>";

exit();

}

# Xóa danh mục
$sql = "DELETE FROM categories WHERE id='$id'";
mysqli_query($conn,$sql);

header("Location: quanlydm.php");
exit();
?>