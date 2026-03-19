<?php
include "connect.php";

/* kiểm tra id có tồn tại */
if(isset($_GET['id'])){

$id = $_GET['id'];

/* xóa khách hàng */
$sql = "DELETE FROM customers WHERE id=$id";

mysqli_query($conn,$sql);

/* quay lại trang danh sách */
header("Location: customer.php");
exit();

}
else{
echo "Không tìm thấy khách hàng";
}
?>