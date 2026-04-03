<?php
include "connect.php";

$sql = "SELECT * FROM categories";
$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Quản lý danh mục</title>
</head>

<body>

<h2>Quản lý danh mục</h2>

<a href="themdm.php">
<button>Thêm danh mục</button>
</a>

<table border="1" width="80%">
<tr>
<th>STT</th>
<th>Tên</th>
<th>Mô tả</th>
<th>Thao tác</th>
</tr>

<?php
$i=1;
while($row=mysqli_fetch_assoc($result)){
?>

<tr>
<td><?php echo $i++; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['description']; ?></td>

<td>

<a href="suadm.php?id=<?php echo $row['category_id']; ?>">
<button>Sửa</button>
</a>

<a href="xoadm.php?id=<?php echo $row['category_id']; ?>" onclick="return confirm('Bạn chắc chắn muốn xóa?')">
<button>Xóa</button>
</a>

</td>
</tr>

<?php
}
?>

</table>

</body>
</html>