<?php
include "db.php";

$sql = "SELECT * FROM categories";
$stmt = $conn->query($sql);

echo "<h2>Danh mục</h2>";

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    echo $row['CategoryID']." - ".$row['CategoryName']."<br>";
}
?>