<?php
session_start();
include "connect.php";
if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

/* tổng khách hàng */
$sql_users = "SELECT COUNT(*) AS total_users FROM customers";
$result_users = $conn->query($sql_users);
$total_users = $result_users->fetch_assoc()['total_users'];


/* tổng sản phẩm */
$sql_products = "SELECT COUNT(*) AS total_products FROM product_list";
$result_products = $conn->query($sql_products);
$total_products = $result_products->fetch_assoc()['total_products'];


/* tổng đơn hàng */
$sql_orders = "SELECT COUNT(*) AS total_orders FROM orders";
$result_orders = $conn->query($sql_orders);
$total_orders = $result_orders->fetch_assoc()['total_orders'];


/* tổng doanh thu */
$sql_revenue = "SELECT SUM(total) AS revenue FROM orders WHERE status='Đã giao'";
$result_revenue = $conn->query($sql_revenue);
$row_revenue = $result_revenue->fetch_assoc();

$revenue = $row_revenue['revenue'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Trang chủ</title>
  <link rel="stylesheet" href="astyle.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Josefin Sans", sans-serif; }
    body { background-color: #f5f5f5; }

  </style>
</head>

<body>
<?php include "navbar.php"?>
<?php include "menucard.php"?>

<section class="dashboard">

<h2 style="text-align:center;font-size: 40px;">Tổng quan hệ thống</h2><br><br>

<div class="stat-grid">

<div class="stat-card">
<div class="icon-circle">
<i class="fas fa-users"></i>
</div>
<h3>Tổng khách hàng</h3>
<p><?php echo $total_users; ?></p>
</div>


<div class="stat-card">
<div class="icon-circle">
<i class="fas fa-box-open"></i>
</div>
<h3>Tổng sản phẩm</h3>
<p><?php echo $total_products; ?></p>
</div>


<div class="stat-card">
<div class="icon-circle">
<i class="fas fa-dollar-sign"></i>
</div>
<h3>Tổng doanh thu</h3>
<p><?php echo number_format($revenue,0,",","."); ?> đ</p>
</div>


<div class="stat-card">
<div class="icon-circle">
<i class="fas fa-shopping-cart"></i>
</div>
<h3>Tổng đơn hàng</h3>
<p><?php echo $total_orders; ?></p>
</div>

</div>

</section>
</body>

</html>
</body>

</html>