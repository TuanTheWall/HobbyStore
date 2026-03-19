<?php
include "db.php";

$orderID = $_GET['id'];

$sql = "SELECT p.ProductName, oi.quantity, oi.price
        FROM order_item oi
        JOIN product_list p ON oi.ProductID = p.ProductID
        WHERE oi.OrderID = :id";

$stmt = $conn->prepare($sql);
$stmt->execute(['id'=>$orderID]);

echo "<h2>Chi tiết đơn hàng $orderID</h2>";

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo $row['ProductName']." - ";
    echo $row['quantity']." x ";
    echo $row['price']."<br>";
}
?>