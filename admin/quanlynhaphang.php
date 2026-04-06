<?php
session_start();
$conn = new mysqli("localhost","root","","hobbystore");
$conn->set_charset("utf8");

$from = isset($_GET['from']) ? $_GET['from'] : "";
$to   = isset($_GET['to'])   ? $_GET['to']   : "";

/* Pagination */
$per_page = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

$where = "WHERE 1";
if($from != "" && $to != "") $where .= " AND import_date BETWEEN '$from' AND '$to'";

$total_rows  = $conn->query("SELECT COUNT(*) as total FROM purchase_receipts $where")->fetch_assoc()['total'];
$total_pages = max(1, ceil($total_rows / $per_page));

$result = $conn->query("SELECT * FROM purchase_receipts $where ORDER BY import_date DESC LIMIT $per_page OFFSET $offset");

$qp = [];
if($from != "") $qp[] = "from=".urlencode($from);
if($to   != "") $qp[] = "to=".urlencode($to);
$qs = count($qp) ? '&'.implode('&',$qp) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý nhập hàng</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:"Josefin Sans",sans-serif; }
body { background-color:#f5f5f5; }
.bt-them { background:#04a4b3; color:white; font-size:15px; padding:6px 14px; border:none; border-radius:8px; cursor:pointer; transition:0.3s; }
.bt-them:hover { opacity:0.85; }
.themsp { margin:20px 0 0 5%; display:inline-block; }
.pagination { text-align:center; }
.pagination a { color:black; text-decoration:none; padding:8px 15px; display:inline-block; }
.pagination a.active { background-color:green; font-weight:bold; border-radius:5px; }
.pagination a:hover:not(.active) { background-color:gray; border-radius:5px; }
nav.navbar { display:flex !important; justify-content:space-between !important; align-items:center !important; background-color:cyan !important; height:80px !important; min-height:80px !important; padding:0 24px !important; box-sizing:border-box !important; gap:12px; z-index:50; }
.navbar .navbar-logo { width:64px !important; height:64px !important; min-width:64px !important; min-height:64px !important; border-radius:50% !important; object-fit:cover !important; display:inline-block !important; filter:none !important; -webkit-filter:none !important; mix-blend-mode:normal !important; background:transparent !important; opacity:1 !important; }
.nav-left { display:flex !important; align-items:center !important; gap:18px; }
.nav-left .nav-home { text-decoration:none; color:black; font-size:20px; font-weight:600; }
.nav-right { display:flex !important; align-items:center !important; gap:14px; }
.nav-right .hello { font-weight:600; color:black; }
.logout-btn { background-color:rgb(221,99,225) !important; border:2px solid black !important; border-radius:5px !important; padding:8px 12px !important; color:black !important; text-decoration:none !important; font-weight:700 !important; }
.nav-left a:hover,.nav-right a:hover,.nav-right .hello:hover { color:red !important; }
.overview-menu { padding:30px; background-color:white; }
.overview-menu h2 { font-size:22px; color:black; margin-bottom:20px; }
.menu-grid { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; }
.menu-card { background:linear-gradient(to bottom,cyan,blue); color:#fff; text-align:center; padding:30px 20px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.1); transition:all 0.25s ease; cursor:pointer; }
.menu-card h3 { font-size:16px; font-weight:600; }
.menu-card:hover { transform:translateY(-5px); box-shadow:0 6px 18px rgba(0,0,0,0.15); }
.menu-card a { color:white; text-decoration:none; }
table { border-collapse:collapse; width:90%; margin:0 auto; margin-top:30px; }
td { border:1px solid #dddddd; text-align:center; padding:8px; }
th { border:3px solid #dddddd; text-align:center; padding:8px; background:#96dee0; }
.search-advanced { display:flex; align-items:center; justify-content:center; gap:20px; margin:30px auto; flex-wrap:wrap; }
.search-advanced label { font-size:20px; font-weight:bold; margin-right:6px; }
.search-advanced input,.search-advanced select { width:220px; height:38px; padding:6px 10px; font-size:16px; border:1px solid #aaa; border-radius:6px; }
.search-advanced button { background-color:blue; color:white; padding:8px 20px; font-size:16px; border:none; border-radius:6px; cursor:pointer; height:40px; transition:0.25s; }
.search-advanced button:hover { background-color:darkblue; }
</style>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<nav class="navbar">
  <div class="nav-left">
    <a href="admin.php" class="logo-link"><img src="assets/img/logo.png" alt="Logo" class="navbar-logo"></a>
    <a href="admin.php" class="nav-home">Trang chủ</a>
  </div>
  <div class="nav-right">
    <span class="hello">Xin chào, Admin</span>
    <a href="adminlogin.php" class="logout-btn">Đăng xuất</a>
  </div>
</nav>

<body>
<section class="overview-menu">
  <h2>Mục quản lý</h2>
  <div class="menu-grid">
    <a href="customer.php"><div class="menu-card"><h3>Quản lý khách hàng</h3></div></a>
    <a href="shiping.php"><div class="menu-card"><h3>Quản lý đơn hàng</h3></div></a>
    <a href="quanlysp.php"><div class="menu-card"><h3>Quản lý sản phẩm</h3></div></a>
    <a href="lndm.php"><div class="menu-card"><h3>Quản lý giá bán</h3></div></a>
    <a href="quanlydm.php"><div class="menu-card"><h3>Quản lý danh mục</h3></div></a>
    <a href="tracuusoluong.php"><div class="menu-card"><h3>Quản lý tồn kho</h3></div></a>
    <a href="quanlynhaphang.php"><div class="menu-card" style="background:linear-gradient(to bottom,#19113b,blue);"><h3>Quản lý nhập hàng</h3></div></a>
  </div>
</section>

<form action="thempn.php" method="get" class="themsp">
  <button type="submit" class="bt-them"><i class="fa-solid fa-plus"></i>Thêm phiếu nhập</button>
</form>

<div class="search-box">
  <form action="quanlynhaphang.php" method="get" class="search-advanced">
    <label style="font-size:25px;">Từ</label>
    <input type="date" name="from" value="<?php echo $from; ?>">
    <label style="font-size:25px;">Đến</label>
    <input type="date" name="to" value="<?php echo $to; ?>">
    <button type="submit" class="btn-tim">Tìm</button>
    <a href="quanlynhaphang.php"><button type="button" style="height:38px;background:gray;color:white;border:none;border-radius:6px;padding:8px 20px;font-size:16px;cursor:pointer;">Đặt lại</button></a>
  </form>

  <table>
    <tr>
      <th>Mã phiếu nhập</th>
      <th>Ngày nhập</th>
      <th>Số lượng</th>
      <th>Tổng tiền</th>
      <th>Thao tác</th>
    </tr>
    <?php if($result->num_rows > 0): ?>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['receipt_code']); ?></td>
        <td><?php echo $row['import_date']; ?></td>
        <td><?php echo $row['total_quantity']; ?></td>
        <td><?php echo number_format($row['total_value'], 0, ',', '.'); ?> VNĐ</td>
        <td>
          <a href="receipt_detail_page.php?code=<?php echo urlencode($row['receipt_code']); ?>">
            <button class="bt-them" style="background:#04a4b3;">Xem</button>
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="5">Không có dữ liệu</td></tr>
    <?php endif; ?>
  </table>

  <div class="pagination" style="margin-top:100px;margin-bottom:50px;">
    <a href="quanlynhaphang.php?page=1<?php echo $qs; ?>">&laquo;</a>
    <?php for($i=1; $i<=$total_pages; $i++): ?>
      <a href="quanlynhaphang.php?page=<?php echo $i.$qs; ?>" <?php echo $i==$page?'class="active"':''; ?>><?php echo $i; ?></a>
    <?php endfor; ?>
    <a href="quanlynhaphang.php?page=<?php echo $total_pages.$qs; ?>">&raquo;</a>
  </div>
</div>
</body>
</html>