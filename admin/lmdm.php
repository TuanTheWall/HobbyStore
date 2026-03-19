<?php
$conn = new mysqli("localhost","root","","hobbystore");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* cập nhật profit */
if(isset($_POST['grade'])){
    $grade = $_POST['grade'];
    $profit = $_POST['profit']/100;

    $sql_update = "UPDATE product_list SET Profit='$profit' WHERE Grade='$grade'";
    $conn->query($sql_update);
}

/* lấy danh mục */
$sql = "SELECT Grade, Profit FROM product_list GROUP BY Grade";
$result = $conn->query($sql);
?>
<!DOCTYPE php>
<php lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý giá bán</title>

<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/settings.css">
<link rel="stylesheet" href="assets/css/product-page.css">
<link rel="stylesheet" href="assets/css/view-cart.css">

<style>

table{
border-collapse: collapse;
width:90%;
margin:0 auto;
margin-top:30px;
}

td{
border:1px solid #dddddd;
text-align:center;
padding:8px;
}

th{
border:3px solid #dddddd;
text-align:center;
padding:8px;
background:#96dee0;
}

.thaotac{
padding-bottom:40px;
padding-right:25px;
}

.input-box input{
height:30px;
border-radius:8px;
width:100px;
}

.bt-them{
background:green;
color:white;
border:none;
padding:8px 15px;
cursor:pointer;
}

</style>
</head>

<body>

<h2 style="text-align:center;margin-top:40px;">
TỶ LỆ LỢI NHUẬN THEO DANH MỤC
</h2>

<table>

<tr>
<th>STT</th>
<th>Tên</th>
<th>Tỷ lệ lợi nhuận</th>
<th>Thao tác</th>
</tr>

<?php
$i=1;

while($row = $result->fetch_assoc()){
?>

<tr>

<td><?php echo $i++; ?></td>

<td><?php echo $row['Grade']; ?></td>

<td>

<form method="post">

<input type="hidden" name="grade"
value="<?php echo $row['Grade']; ?>">

<div class="input-profit">
<div class="input-box">

<input
type="number"
name="profit"
min="0"
max="100"
value="<?php echo $row['Profit']*100; ?>"
>

<span>%</span>

</div>
</div>

</td>

<td class="thaotac">

<button type="submit" class="bt-them">
Lưu
</button>

</td>

</form>

</tr>

<?php } ?>

</table>

</body>
</php>