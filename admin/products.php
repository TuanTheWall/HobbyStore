<?php
include "db.php";

$sql = "SELECT p.ProductID, p.ProductName, p.price, c.CategoryName
        FROM product_list p
        JOIN categories c ON p.CategoryID = c.CategoryID";

$stmt = $conn->query($sql);

echo "<h2>Danh sách sản phẩm</h2>";

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo "ID: ".$row['ProductID']."<br>";
    echo "Tên: ".$row['ProductName']."<br>";
    echo "Danh mục: ".$row['CategoryName']."<br>";
    echo "Giá: ".$row['price']." VNĐ<br>";
    echo "<hr>";
}
?>