<?php
include "connect.php";

$search = "";
$grade = "";
$producer = "";

if(isset($_GET['search'])) {
    $search = $_GET['search'];
}

if(isset($_GET['grade'])) {
    $grade = $_GET['grade'];
}

if(isset($_GET['producer'])) {
    $producer = $_GET['producer'];
}

$sql = "SELECT * FROM product_list WHERE 1";

if($search != ""){
    $sql .= " AND ProductName LIKE '%$search%'";
}

if($grade != ""){
    $sql .= " AND Grade='$grade'";
}

if($producer != ""){
    $sql .= " AND Producer='$producer'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Product List</title>

<style>

body{
font-family: Arial;
background:#f5f5f5;
}

.container{
width:1200px;
margin:auto;
}

h1{
text-align:center;
}

.filter{
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
background:white;
}

th,td{
border:1px solid #ddd;
padding:10px;
text-align:center;
}

th{
background:#333;
color:white;
}

img{
width:80px;
}

.btn{
padding:6px 12px;
border:none;
cursor:pointer;
}

.btn-edit{
background:#3498db;
color:white;
}

.btn-delete{
background:#e74c3c;
color:white;
}

</style>

</head>

<body>

<div class="container">

<h1>Danh sách sản phẩm</h1>

<form method="GET" class="filter">

<input type="text" name="search" placeholder="Tìm sản phẩm">

<select name="grade">
<option value="">Tất cả Grade</option>
<option value="HG">HG</option>
<option value="RG">RG</option>
<option value="MG">MG</option>
<option value="PG">PG</option>
<option value="Figure">Figure</option>
</select>

<select name="producer">
<option value="">Tất cả hãng</option>
<option value="Bandai">Bandai</option>
<option value="Banpresto">Banpresto</option>
<option value="SEGA">SEGA</option>
</select>

<button type="submit">Tìm</button>

</form>

<table>

<tr>
<th>ID</th>
<th>Hình</th>
<th>Tên sản phẩm</th>
<th>Grade</th>
<th>Hãng</th>
<th>Giá</th>
<th>Tồn kho</th>
<th>Lợi nhuận</th>
<th>Chức năng</th>
</tr>

<?php

if ($result->num_rows > 0) {

while($row = $result->fetch_assoc()) {

echo "<tr>";

echo "<td>".$row['ProductID']."</td>";

echo "<td>
<img src='images/".$row['Product_image']."'>
</td>";

echo "<td>".$row['ProductName']."</td>";

echo "<td>".$row['Grade']."</td>";

echo "<td>".$row['Producer']."</td>";

echo "<td>".number_format($row['Price'])." VND</td>";

echo "<td>".$row['Quantity']."</td>";

echo "<td>".$row['Profit']."</td>";

echo "<td>

<a href='edit_product.php?id=".$row['ProductID']."'>
<button class='btn btn-edit'>Sửa</button>
</a>

<a href='delete_product.php?id=".$row['ProductID']."' 
onclick=\"return confirm('Bạn có chắc muốn xóa?')\">

<button class='btn btn-delete'>Xóa</button>

</a>

</td>";

echo "</tr>";

}

}

?>

</table>

</div>

</body>
</html>