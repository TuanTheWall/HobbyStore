<?php
session_start();
require_once "config.php";

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}

$customer_id = $_SESSION['user_id'];

$product_ids = $_POST['product_id'];
$prices = $_POST['price'];
$quantities = $_POST['quantity'];

$name = $_POST['name'];
$address = $_POST['address'];
$phone = $_POST['phone'];
$payment = $_POST['payment_method'];

if(empty($product_ids)){
die("Không có sản phẩm trong đơn hàng");
}

$total = 0;

for($i=0;$i<count($product_ids);$i++){
$total += $prices[$i] * $quantities[$i];
}

date_default_timezone_set("Asia/Ho_Chi_Minh");
$date = date("Y-m-d H:i:s");

/* ===== TẠO ID ORDER ===== */

$result = mysqli_query($conn,"SELECT id_order FROM orders ORDER BY id_order DESC LIMIT 1");
$row = mysqli_fetch_assoc($result);

if($row){
$num = intval(substr($row['id_order'],2)) + 1;
}else{
$num = 1;
}

$order_id = "DH".str_pad($num,3,"0",STR_PAD_LEFT);

/* ===== INSERT ORDER ===== */

$sql_order = "INSERT INTO orders
(id_order, customer_id, order_date, status, created_at, total,
receiver_name, receiver_phone, receiver_address, payment_method)
VALUES
('$order_id','$customer_id',NOW(),'Chờ xử lý','$date','$total',
'$name','$phone','$address','$payment')";

if(!mysqli_query($conn,$sql_order)){
die("Lỗi tạo đơn hàng: ".mysqli_error($conn));
}

/* ===== INSERT ORDER ITEM ===== */

for($i=0;$i<count($product_ids);$i++){

$product_id = $product_ids[$i];
$quantity = $quantities[$i];

$sql_item = "INSERT INTO order_item
(id_order, ProductID, quantity)
VALUES
('$order_id','$product_id','$quantity')";

mysqli_query($conn,$sql_item);

/* ===== TRỪ KHO ===== */

mysqli_query($conn,"
UPDATE product_list
SET Quantity = Quantity - $quantity
WHERE ProductID = '$product_id'
");

}

/* ===== XÓA CART ITEM ===== */

$sql_cart = "SELECT cart_id FROM cart WHERE customer_id='$customer_id'";
$res_cart = mysqli_query($conn,$sql_cart);
$cart = mysqli_fetch_assoc($res_cart);

if($cart){

$cart_id = $cart['cart_id'];

foreach($product_ids as $pid){

mysqli_query($conn,"
DELETE FROM cart_item
WHERE cart_id='$cart_id'
AND product_id='$pid'
");

}

}

header("Location: order_success.php");
exit();
?>