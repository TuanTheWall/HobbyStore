<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hobbystore";

$conn = new mysqli($servername,$username,$password,$dbname);
$conn->set_charset("utf8");

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

# cập nhật trạng thái đơn hàng
if(isset($_GET['update']) && isset($_GET['id'])){
    $id = $_GET['id'];
    $status = $_GET['update'];

    $sql_update = "UPDATE orders SET status='$status' WHERE OrderID='$id'";
    $conn->query($sql_update);

    header("Location: shipping.php");
}

# lấy danh sách đơn hàng
$sql = "SELECT 
orders.OrderID,
customers.customer_name,
customers.email,
product_list.ProductName,
orders.total_amount,
orders.order_date,
orders.status
FROM orders
JOIN customers ON orders.customer_id = customers.customer_id
JOIN product_list ON orders.product_id = product_list.ProductID
ORDER BY orders.order_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Quản lý đơn hàng</title>

<style>

body{
font-family: Arial;
background:#f4f6f9;
padding:40px;
}

h1{
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
background:white;
}

th,td{
padding:12px;
border-bottom:1px solid #ddd;
}

th{
background:#f0f0f0;
text-align:left;
}

tr:hover{
background:#fafafa;
}

.status{
padding:5px 10px;
border-radius:5px;
color:white;
font-size:13px;
}

.wait{background:#f39c12;}
.confirm{background:#3498db;}
.shipping{background:#8e44ad;}
.done{background:#27ae60;}
.cancel{background:#e74c3c;}

button{
padding:6px 10px;
border:none;
border-radius:4px;
cursor:pointer;
font-size:12px;
}

.btn-wait{background:#f39c12;color:white;}
.btn-ship{background:#8e44ad;color:white;}
.btn-done{background:#27ae60;color:white;}
.btn-cancel{background:#e74c3c;color:white;}

</style>

</head>

<body>

<h1>Quản lý đơn hàng</h1>

<table>

<tr>

<th>Mã đơn</th>
<th>Khách hàng</th>
<th>Sản phẩm</th>
<th>Tổng tiền</th>
<th>Ngày đặt</th>
<th>Trạng thái</th>
<th>Hành động</th>

</tr>

<?php

if($result->num_rows > 0){

while($row = $result->fetch_assoc()){

$status_class="wait";

if($row['status']=="Đã xác nhận") $status_class="confirm";
if($row['status']=="Đang giao") $status_class="shipping";
if($row['status']=="Đã giao") $status_class="done";
if($row['status']=="Đã huỷ") $status_class="cancel";

?>

<tr>

<td><?php echo $row['OrderID']; ?></td>

<td>
<b><?php echo $row['customer_name']; ?></b><br>
<small><?php echo $row['email']; ?></small>
</td>

<td><?php echo $row['ProductName']; ?></td>

<td><?php echo number_format($row['total_amount'],0,",","."); ?> đ</td>

<td><?php echo $row['order_date']; ?></td>

<td>
<span class="status <?php echo $status_class; ?>">
<?php echo $row['status']; ?>
</span>
</td>

<td>

<a href="shipping.php?id=<?php echo $row['OrderID']; ?>&update=Chờ xử lý">
<button class="btn-wait">Chờ</button>
</a>

<a href="shipping.php?id=<?php echo $row['OrderID']; ?>&update=Đang giao">
<button class="btn-ship">Giao</button>
</a>

<a href="shipping.php?id=<?php echo $row['OrderID']; ?>&update=Đã giao">
<button class="btn-done">Hoàn</button>
</a>

<a href="shipping.php?id=<?php echo $row['OrderID']; ?>&update=Đã huỷ">
<button class="btn-cancel">Huỷ</button>
</a>

</td>

</tr>

<?php
}
}
?>

</table>

</body>
</html>