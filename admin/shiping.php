<?php
$conn = new mysqli("localhost","root","","hobbystore");
if($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

/* cập nhật trạng thái đơn hàng */
if(isset($_POST['update_status'])){
    $id     = $conn->real_escape_string($_POST['id_order']);
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status='$status' WHERE id_order='$id'");
    header("Location: shiping.php");
    exit;
}

/* tìm kiếm */
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to   = isset($_GET['date_to'])   ? $_GET['date_to']   : '';
$status_f  = isset($_GET['sanpham'])   ? $_GET['sanpham']   : '';

$where = "WHERE 1=1";
if(!empty($date_from)) $where .= " AND o.order_date >= '" . $conn->real_escape_string($date_from) . "'";
if(!empty($date_to))   $where .= " AND o.order_date <= '" . $conn->real_escape_string($date_to)   . "'";
$status_map = ['dxn'=>'Đã xác nhận','dg'=>'Đã giao','cxl'=>'Chờ xử lý','dh'=>'Đã huỷ','dag'=>'Đang giao'];
if(!empty($status_f) && isset($status_map[$status_f])){
    $where .= " AND o.status='" . $status_map[$status_f] . "'";
}

/* phân trang */
$per_page = 5;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

$count_res  = $conn->query("SELECT COUNT(*) as total FROM orders o $where");
$total_rows = $count_res->fetch_assoc()['total'];
$total_pages = max(1, ceil($total_rows / $per_page));

$sql = "SELECT o.id_order, o.order_date, o.status, o.total,
               o.receiver_name, o.receiver_phone, o.receiver_address,
               o.payment_method, o.note,
               c.email, c.username
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        $where
        ORDER BY o.order_date DESC
        LIMIT $per_page OFFSET $offset";
$result = $conn->query($sql);

/* query string cho pagination */
$qp = [];
if(!empty($date_from)) $qp[] = "date_from=".urlencode($date_from);
if(!empty($date_to))   $qp[] = "date_to=".urlencode($date_to);
if(!empty($status_f))  $qp[] = "sanpham=".urlencode($status_f);
$qs = count($qp) ? '&'.implode('&',$qp) : '';

/* định nghĩa flow trạng thái */
$next_status = [
    'Chờ xử lý'  => ['label' => 'Xác nhận',   'value' => 'Đã xác nhận', 'class' => 'btn-confirm'],
    'Đã xác nhận' => ['label' => 'Đang giao',  'value' => 'Đang giao',   'class' => 'btn-delivering'],
    'Đang giao'   => ['label' => 'Đã giao',    'value' => 'Đã giao',     'class' => 'btn-deliver'],
    'Đã giao'     => null, // không có nút tiếp theo
    'Đã huỷ'      => null, // không có nút tiếp theo
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Quản lý đơn hàng</title>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body { font-family:"Josefin Sans",Arial,sans-serif; background:#f8f9fa; margin:0; padding:0; color:#222; }
    nav.navbar { display:flex !important; justify-content:space-between !important; align-items:center !important; background-color:cyan !important; height:80px !important; min-height:80px !important; padding:0 24px !important; box-sizing:border-box !important; gap:12px; z-index:50; }
    .navbar .navbar-logo { width:64px !important; height:64px !important; min-width:64px !important; min-height:64px !important; border-radius:50% !important; object-fit:cover !important; display:inline-block !important; filter:none !important; mix-blend-mode:normal !important; background:transparent !important; opacity:1 !important; }
    .nav-left { display:flex !important; align-items:center !important; gap:18px; }
    .nav-left .nav-home { text-decoration:none; color:black; font-size:20px; font-weight:600; }
    .nav-right { display:flex !important; align-items:center !important; gap:14px; }
    .nav-right .hello { font-weight:600; color:black; }
    .logout-btn { background-color:rgb(221,99,225) !important; border:2px solid black !important; border-radius:5px !important; padding:8px 12px !important; color:black !important; text-decoration:none !important; font-weight:700 !important; }
    .nav-left a:hover,.nav-right a:hover,.nav-right .hello:hover { color:red !important; }
    .overview-menu { padding:30px; background-color:white; }
    .overview-menu h2 { font-size:22px; color:black; margin:0 0 20px 0; }
    .menu-grid { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; }
    .menu-card { background:linear-gradient(to bottom,cyan,blue); color:#fff; text-align:center; padding:30px 20px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.1); transition:all 0.25s ease; cursor:pointer; min-width:180px; }
    .menu-card h3 { font-size:16px; font-weight:600; margin:0; }
    .menu-card:hover { transform:translateY(-5px); box-shadow:0 6px 18px rgba(0,0,0,0.15); }
    .menu-card a { color:white; text-decoration:none; display:block; }
    .search-advanced { display:flex; align-items:center; justify-content:center; gap:20px; margin:30px auto; flex-wrap:wrap; }
    .search-advanced label { font-size:20px; font-weight:bold; margin-right:6px; }
    .search-advanced input, .search-advanced select { width:220px; height:38px; padding:6px 10px; font-size:16px; border:1px solid #aaa; border-radius:6px; }
    .search-advanced button { background-color:blue; color:white; padding:8px 20px; font-size:16px; border:none; border-radius:6px; cursor:pointer; height:40px; transition:0.25s; }
    .search-advanced button:hover { background-color:darkblue; }
    .shipping-table { margin:30px; background-color:#fff; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.1); padding:20px; }
    .shipping-table h2 { margin:0 0 14px 0; font-size:31px; color:#222; }
    table { width:100%; border-collapse:collapse; font-size:14px; text-align:left; }
    thead th { text-align:left; padding:12px 10px; border-bottom:1px solid #eef2f8; color:#000; font-weight:700; background:#96dee0; }
    tbody td { padding:12px 10px; border-bottom:1px solid #f1f3f6; vertical-align:middle; }
    tbody tr:hover { background:#fafcff; }
    .btn { padding:8px 12px; border-radius:8px; border:none; cursor:pointer; font-weight:700; font-size:13px; margin-right:4px; margin-bottom:4px; }
    .btn:active { transform:translateY(1px); }
    .btn-deliver    { background:#28a745; color:#fff; }
    .btn-cancel     { background:#e94b3c; color:#fff; }
    .btn-detail     { background:#f59e0b; color:#111; }
    .btn-delivering { background:#17a2b8; color:#fff; }
    .btn-confirm    { background:#0d6efd; color:#fff; }
    .status-badge { padding:6px 8px; border-radius:8px; font-weight:700; font-size:13px; display:inline-block; }
    .status-pending    { background:#fff7e6; color:#b36b00; }
    .status-shipped    { background:#e8f8f0; color:#14723b; }
    .status-cancel     { background:#fff0f0; color:#b33; }
    .status-delivering { background:#e0f7fa; color:#006064; }
    .pagination { text-align:center; margin-top:20px; margin-bottom:50px; }
    .pagination a { color:black; text-decoration:none; padding:8px 15px; display:inline-block; }
    .pagination a.active { background-color:green; font-weight:bold; border-radius:5px; }
    .pagination a:hover:not(.active) { background-color:gray; border-radius:5px; }
    .muted { color:#667; font-size:13px; }
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
    <a href="customer.php"><div class="menu-card"><h3>Quản lý khách hàng</h3></div></a>
    <a href="shiping.php"><div class="menu-card" style="background:linear-gradient(to bottom,#19113b,blue);"><h3>Quản lý đơn hàng</h3></div></a>
    <a href="quanlysp.php"><div class="menu-card"><h3>Quản lý sản phẩm</h3></div></a>
    <a href="lndm.php"><div class="menu-card"><h3>Quản lý giá bán</h3></div></a>
    <a href="quanlydm.php"><div class="menu-card"><h3>Quản lý danh mục</h3></div></a>
    <a href="tracuusoluong.php"><div class="menu-card"><h3>Quản lý tồn kho</h3></div></a>
    <a href="quanlynhaphang.php"><div class="menu-card"><h3>Quản lý nhập hàng</h3></div></a>
  </div>
</section>

<form action="shiping.php" method="get" class="search-advanced">
  <label>Từ</label>
  <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
  <label>Đến</label>
  <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
  <label>Tình trạng đơn hàng</label>
  <select name="sanpham">
    <option value="">Tất cả trạng thái</option>
    <option value="dxn" <?php echo $status_f=='dxn'?'selected':''; ?>>Đã xác nhận</option>
    <option value="dg"  <?php echo $status_f=='dg' ?'selected':''; ?>>Đã giao</option>
    <option value="dag" <?php echo $status_f=='dag'?'selected':''; ?>>Đang giao</option>
    <option value="cxl" <?php echo $status_f=='cxl'?'selected':''; ?>>Chờ xử lý</option>
    <option value="dh"  <?php echo $status_f=='dh' ?'selected':''; ?>>Đã huỷ</option>
  </select>
  <button type="submit" style="height:38px;">Tìm</button>
  <a href="shiping.php"><button type="button" style="height:38px;background:gray;color:white;border:none;border-radius:6px;padding:8px 20px;font-size:16px;cursor:pointer;">Đặt lại</button></a>
</form>

<div class="shipping-table">
  <h2>Danh sách đơn hàng</h2>
  <table>
    <thead>
      <tr>
        <th>Mã đơn</th>
        <th>Khách</th>
        <th>Sản phẩm</th>
        <th>Tổng tiền</th>
        <th>Ngày đặt</th>
        <th>Trạng thái</th>
        <th style="text-align:right">Hành động</th>
      </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()):
        $oid = $conn->real_escape_string($row['id_order']);
        $items_res = $conn->query("SELECT p.ProductName, oi.quantity
                                   FROM order_item oi
                                   JOIN product_list p ON oi.ProductID = p.ProductID
                                   WHERE oi.id_order='$oid'");
        $items = [];
        while($it = $items_res->fetch_assoc()) $items[] = $it;
        $product_names = implode('<br>', array_map(fn($i) => htmlspecialchars($i['ProductName']) . ' x' . $i['quantity'], $items));

        $badge = 'status-pending';
        if($row['status']==='Đã giao' || $row['status']==='Đã xác nhận') $badge = 'status-shipped';
        elseif($row['status']==='Đang giao') $badge = 'status-delivering';
        elseif($row['status']==='Đã huỷ') $badge = 'status-cancel';

        $current_status = $row['status'];
        $next = $next_status[$current_status] ?? null;

        // Nút hủy hiện khi chưa Đã giao và chưa Đã huỷ
        $show_cancel = ($current_status !== 'Đã giao' && $current_status !== 'Đã huỷ');
    ?>
    <tr>
      <td><?php echo htmlspecialchars($row['id_order']); ?></td>
      <td>
        <div style="font-weight:600"><?php echo htmlspecialchars($row['receiver_name'] ?? $row['username']); ?></div>
        <div class="muted"><?php echo htmlspecialchars($row['email']); ?></div>
      </td>
      <td><?php echo $product_names; ?></td>
      <td><?php echo $row['total'] ? number_format($row['total'],0,',','.') . 'đ' : '—'; ?></td>
      <td class="muted"><?php echo date('d/m/Y', strtotime($row['order_date'])); ?></td>
      <td><span class="status-badge <?php echo $badge; ?>"><?php echo htmlspecialchars($current_status); ?></span></td>
      <td style="text-align:right">
        <form method="post" style="display:inline;">
          <input type="hidden" name="id_order" value="<?php echo htmlspecialchars($row['id_order']); ?>">
          <input type="hidden" name="update_status" value="1">

          <?php if($next): ?>
            <button type="submit" name="status" value="<?php echo $next['value']; ?>"
              class="btn <?php echo $next['class']; ?>">
              <?php echo $next['label']; ?>
            </button>
          <?php endif; ?>

          <?php if($show_cancel): ?>
            <button type="submit" name="status" value="Đã huỷ"
              class="btn btn-cancel"
              onclick="return confirm('Bạn chắc chắn muốn huỷ đơn <?php echo $row['id_order']; ?>?')">
              Huỷ đơn
            </button>
          <?php endif; ?>
        </form>

        <a href="order_detail_page.php?id=<?php echo urlencode($row['id_order']); ?>"
           class="btn btn-detail"
           style="text-decoration:none;display:inline-flex;align-items:center;justify-content:center;">
          Xem chi tiết
        </a>
      </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<div class="pagination">
  <a href="shiping.php?page=1<?php echo $qs; ?>">&laquo;</a>
  <?php for($i=1;$i<=$total_pages;$i++): ?>
    <a href="shiping.php?page=<?php echo $i.$qs; ?>" <?php echo $i==$page?'class="active"':''; ?>><?php echo $i; ?></a>
  <?php endfor; ?>
  <a href="shiping.php?page=<?php echo $total_pages.$qs; ?>">&raquo;</a>
</div>

</body>
</html>