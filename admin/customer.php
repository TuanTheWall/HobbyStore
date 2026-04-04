<?php
$conn = new mysqli("localhost","root","","hobbystore");
if($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

/* xóa khách hàng — chỉ cho xóa nếu chưa có đơn hàng */
if(isset($_GET['xoa'])){
    $id = $conn->real_escape_string($_GET['xoa']);
    $has_order = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE customer_id='$id'")->fetch_assoc()['cnt'];
    if($has_order == 0){
        $conn->query("DELETE FROM customers WHERE customer_id='$id'");
    }
    header("Location: customer.php");
    exit;
}

/* khóa / mở khóa */
if(isset($_GET['toggle'])){
    $id = $conn->real_escape_string($_GET['toggle']);
    $cur = $conn->query("SELECT status FROM customers WHERE customer_id='$id'")->fetch_assoc();
    $new_status = $cur['status'] === 'Hoạt động' ? 'Bị khóa' : 'Hoạt động';
    $conn->query("UPDATE customers SET status='$new_status' WHERE customer_id='$id'");
    header("Location: customer.php" . (isset($_SERVER['QUERY_STRING']) ? '?' . preg_replace('/toggle=[^&]*&?/','',$_SERVER['QUERY_STRING']) : ''));
    exit;
}

/* reset mật khẩu về mặc định */
if(isset($_GET['reset_pw'])){
    $id = $conn->real_escape_string($_GET['reset_pw']);
    
    // Lấy SĐT của khách
    $phone = $conn->query("SELECT phone FROM customers WHERE customer_id='$id'")->fetch_assoc()['phone'];
    
    // $new_pw = password_hash($phone, PASSWORD_DEFAULT);
    $conn->query("UPDATE customers SET password='$phone' WHERE customer_id='$id'");
    header("Location: customer.php?msg=reset_ok");
    exit;
}


$msg = $_GET['msg'] ?? '';

/* tìm kiếm */
$fname    = isset($_GET['fname'])   ? $_GET['fname']   : '';
$status_f = isset($_GET['sanpham']) ? $_GET['sanpham'] : '';

$where = "WHERE 1=1";
if(!empty($fname)){
    $f = $conn->real_escape_string($fname);
    $where .= " AND (username LIKE '%$f%' OR email LIKE '%$f%')";
}
if($status_f === 'hd') $where .= " AND status='Hoạt động'";
if($status_f === 'bk') $where .= " AND status='Bị khóa'";

/* phân trang */
$per_page = 5;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

$total_rows  = $conn->query("SELECT COUNT(*) as total FROM customers $where")->fetch_assoc()['total'];
$total_pages = max(1, ceil($total_rows / $per_page));

$result = $conn->query("SELECT * FROM customers $where ORDER BY register_date DESC LIMIT $per_page OFFSET $offset");

$qp = [];
if(!empty($fname))    $qp[] = "fname=".urlencode($fname);
if(!empty($status_f)) $qp[] = "sanpham=".urlencode($status_f);
$qs = count($qp) ? '&'.implode('&',$qp) : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý khách hàng</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <style>
    body { font-family:"Josefin Sans",sans-serif; background-color:#f8f9fa; margin:0; padding:0; }
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
    .customer-table { margin:30px; background-color:white; padding:20px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
    table { width:100%; border-collapse:collapse; text-align:center; }
    th, td { padding:12px; border-bottom:1px solid #ddd; }
    th { background-color:#96dee0; color:rgb(0,0,0); }
    tr:hover { background-color:#f1f1f1; }
    .btn { padding:6px 12px; border:none; border-radius:5px; cursor:pointer; color:white; font-size:14px; }
    .btn-delete  { background-color:red; }
    .btn-lock    { background-color:rgb(43,244,255); color:black; }
    .btn-unlock  { background-color:green; }
    .btn-reset   { background-color:#f59e0b; color:#111; }
    .btn-disabled { background-color:#ccc; color:#666; cursor:not-allowed; }
    .pagination { text-align:center; }
    .pagination a { color:black; text-decoration:none; padding:8px 15px; display:inline-block; }
    .pagination a.active { background-color:green; font-weight:bold; border-radius:5px; }
    .pagination a:hover:not(.active) { background-color:gray; border-radius:5px; }
    .search-advanced { display:flex; align-items:center; justify-content:center; gap:20px; margin:30px auto; flex-wrap:wrap; }
    .search-advanced label { font-size:20px; font-weight:bold; margin-right:6px; }
    .search-advanced input, .search-advanced select { width:220px; height:38px; padding:6px 10px; font-size:16px; border:1px solid #aaa; border-radius:6px; }
    .search-advanced button { background-color:blue; color:white; padding:8px 20px; font-size:16px; border:none; border-radius:6px; cursor:pointer; height:40px; transition:0.25s; }
    .search-advanced button:hover { background-color:darkblue; }
    .badge-active { background:#e8f8f0; color:#14723b; padding:4px 10px; border-radius:8px; font-weight:700; }
    .badge-locked { background:#fff0f0; color:#b33; padding:4px 10px; border-radius:8px; font-weight:700; }
    .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; padding:12px 20px; border-radius:8px; margin:0 30px 10px 30px; font-weight:600; }
    .delete-note { font-size:12px; color:#999; margin-top:3px; }
  </style>
</head>
<body>

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

<section class="overview-menu">
  <h2>Mục quản lý</h2>
  <div class="menu-grid">
    <a href="customer.php"><div class="menu-card" style="background:linear-gradient(to bottom,#19113b,blue);"><h3>Quản lý khách hàng</h3></div></a>
    <a href="shiping.php"><div class="menu-card"><h3>Quản lý đơn hàng</h3></div></a>
    <a href="quanlysp.php"><div class="menu-card"><h3>Quản lý sản phẩm</h3></div></a>
    <a href="lndm.php"><div class="menu-card"><h3>Quản lý giá bán</h3></div></a>
    <a href="quanlydm.php"><div class="menu-card"><h3>Quản lý danh mục</h3></div></a>
    <a href="tracuusoluong.php"><div class="menu-card"><h3>Quản lý tồn kho</h3></div></a>
    <a href="quanlynhaphang.php"><div class="menu-card"><h3>Quản lý nhập hàng</h3></div></a>
  </div>
</section>


<?php if($msg === 'reset_ok'): ?>
  <div class="alert-success">✅ Reset mật khẩu thành công! Mật khẩu mới là số điện thoại của khách.</div>
<?php endif; ?>



<form action="customer.php" method="get" class="search-advanced">
  <label for="fname">Tìm theo họ tên</label>
  <input style="color:gray;" type="text" id="fname" name="fname" placeholder="Tên / Email" value="<?php echo htmlspecialchars($fname); ?>">
  <label>Trạng thái</label>
  <select name="sanpham">
    <option value="">Tất cả trạng thái</option>
    <option value="hd" <?php echo $status_f==='hd'?'selected':''; ?>>Hoạt động</option>
    <option value="bk" <?php echo $status_f==='bk'?'selected':''; ?>>Bị khóa</option>
  </select>
  <button type="submit" style="height:38px;">Tìm</button>
  <a href="customer.php"><button type="button" style="height:38px;background:gray;color:white;border:none;border-radius:6px;padding:8px 20px;font-size:16px;cursor:pointer;">Đặt lại</button></a>
</form>

<div class="customer-table">
  <h2>Danh sách khách hàng</h2>
  <table>
    <thead>
      <tr>
        <th>Tên khách hàng</th>
        <th>Số điện thoại</th>
        <th>Email</th>
        <th>Địa chỉ</th>
        <th>Ngày đăng ký</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()):
        $has_order = $conn->query("SELECT COUNT(*) as cnt FROM orders WHERE customer_id='{$row['customer_id']}'")->fetch_assoc()['cnt'];
    ?>
    <tr>
      <td><?php echo htmlspecialchars($row['username'] ?? $row['customer_id']); ?></td>
      <td><?php echo htmlspecialchars($row['phone']); ?></td>
      <td><?php echo htmlspecialchars($row['email']); ?></td>
      <td><?php echo htmlspecialchars($row['address'] ?? '—'); ?></td>
      <td><?php echo date('d/m/Y', strtotime($row['register_date'])); ?></td>
      <td>
        <?php if($row['status'] === 'Hoạt động'): ?>
          <span class="badge-active">Hoạt động</span>
        <?php else: ?>
          <span class="badge-locked">Bị khóa</span>
        <?php endif; ?>
      </td>
      <td>
        <!-- Khóa / Mở khóa -->
        <a href="customer.php?toggle=<?php echo urlencode($row['customer_id']).$qs; ?>&page=<?php echo $page; ?>">
          <?php if($row['status'] === 'Hoạt động'): ?>
            <button class="btn btn-lock">Khóa</button>
          <?php else: ?>
            <button class="btn btn-unlock">Mở khóa</button>
          <?php endif; ?>
        </a>

        <!-- Reset mật khẩu -->
        <a href="customer.php?reset_pw=<?php echo urlencode($row['customer_id']); ?>&page=<?php echo $page.$qs; ?>"
           onclick="return confirm('Reset mật khẩu về SĐT của khách này?')">
          <button class="btn btn-reset">Reset MK</button>
        </a>

        <!-- Xóa — chỉ cho xóa nếu chưa có đơn hàng -->
        <?php if($has_order == 0): ?>
          <a href="customer.php?xoa=<?php echo urlencode($row['customer_id']); ?>"
             onclick="return confirm('Bạn có chắc muốn xóa khách hàng này không?')">
            <button class="btn btn-delete">Xóa</button>
          </a>
        <?php else: ?>
          <button class="btn btn-disabled" disabled title="Không thể xóa vì đã có đơn hàng">Xóa</button>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <div class="pagination" style="margin-top:30px;margin-bottom:10px;">
    <a href="customer.php?page=1<?php echo $qs; ?>">&laquo;</a>
    <?php for($i=1;$i<=$total_pages;$i++): ?>
      <a href="customer.php?page=<?php echo $i.$qs; ?>" <?php echo $i==$page?'class="active"':''; ?>><?php echo $i; ?></a>
    <?php endfor; ?>
    <a href="customer.php?page=<?php echo $total_pages.$qs; ?>">&raquo;</a>
  </div>
</div>

</body>
</html>