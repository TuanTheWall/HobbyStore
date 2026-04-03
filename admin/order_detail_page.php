<?php
session_start();
if(!isset($_SESSION['admin'])){ header("Location: adminlogin.php"); exit(); }

$conn = new mysqli("localhost","root","","hobbystore");
$conn->set_charset("utf8");

/* cập nhật trạng thái đơn hàng */
if(isset($_POST['update_status'])){
    $id     = $conn->real_escape_string($_POST['id_order']);
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status='$status' WHERE id_order='$id'");
    header("Location: order_detail_page.php?id=$id");
    exit;
}

$id_order = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
if(empty($id_order)){ header("Location: shiping.php"); exit; }

$order = $conn->query("SELECT o.*, c.email, c.username FROM orders o JOIN customers c ON o.customer_id = c.customer_id WHERE o.id_order='$id_order'")->fetch_assoc();
if(!$order){ header("Location: shiping.php"); exit; }

$items = $conn->query("SELECT oi.*, p.ProductName, p.Price FROM order_item oi JOIN product_list p ON oi.ProductID = p.ProductID WHERE oi.id_order='$id_order'");

$next_status = [
    'Chờ xử lý'   => ['label' => 'Xác nhận',  'value' => 'Đã xác nhận', 'class' => 'btn-confirmed'],
    'Đã xác nhận'  => ['label' => 'Đang giao', 'value' => 'Đang giao',   'class' => 'btn-shipping'],
    'Đang giao'    => ['label' => 'Đã giao',   'value' => 'Đã giao',     'class' => 'btn-delivered'],
    'Đã giao'      => null,
    'Đã huỷ'       => null,
];

$current_status = $order['status'];
$next = $next_status[$current_status] ?? null;
$show_cancel = ($current_status !== 'Đã giao' && $current_status !== 'Đã huỷ');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi tiết đơn hàng</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:"Josefin Sans",sans-serif; }
    body { background-color:#f5f5f5; }
    nav.navbar { display:flex !important; justify-content:space-between !important; align-items:center !important; background-color:cyan !important; height:80px !important; min-height:80px !important; padding:0 24px !important; box-sizing:border-box !important; gap:12px; z-index:50; }
    .navbar .navbar-logo { width:64px !important; height:64px !important; min-width:64px !important; min-height:64px !important; border-radius:50% !important; object-fit:cover !important; display:inline-block !important; filter:none !important; mix-blend-mode:normal !important; background:transparent !important; opacity:1 !important; }
    .nav-left { display:flex !important; align-items:center !important; gap:18px; }
    .nav-left .nav-home { text-decoration:none; color:black; font-size:20px; font-weight:600; }
    .nav-right { display:flex !important; align-items:center !important; gap:14px; }
    .nav-right .hello { font-weight:600; color:black; }
    .logout-btn { background-color:rgb(221,99,225) !important; border:2px solid black !important; border-radius:5px !important; padding:8px 12px !important; color:black !important; text-decoration:none !important; font-weight:700 !important; }
    .nav-left a:hover,.nav-right a:hover { color:red !important; }
    .wrap { max-width:900px; margin:30px auto; background:white; border-radius:16px; box-shadow:0 6px 25px rgba(0,0,0,0.1); padding:2rem; }
    .order-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:2px solid #eee; }
    .order-header h2 { font-size:1.5rem; color:#344767; }
    .order-meta { font-size:15px; color:#555; line-height:1.8; }
    .order-meta span { font-weight:700; color:#333; }
    .section-title { font-size:16px; font-weight:700; color:#333; margin-top:1.5rem; margin-bottom:0.8rem; padding-bottom:0.6rem; border-bottom:2px solid #96dee0; }
    .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
    .info-row { font-size:14px; line-height:1.6; }
    .info-row strong { color:#333; }
    .info-row span { color:#666; }
    table { border-collapse:collapse; width:100%; margin-top:1rem; }
    th { border:2px solid #ddd; text-align:center; padding:10px; background:#96dee0; }
    td { border:1px solid #ddd; text-align:center; padding:10px; }
    .status-badge { display:inline-block; padding:6px 12px; border-radius:6px; font-weight:700; font-size:13px; }
    .status-pending   { background:#fff7e6; color:#b36b00; }
    .status-confirmed { background:#e8f8f0; color:#14723b; }
    .status-shipping  { background:#e0f7fa; color:#006064; }
    .status-delivered { background:#e8f5e9; color:#1b5e20; }
    .status-cancelled { background:#fff0f0; color:#b33; }
    .total-box { margin-top:1.5rem; padding:1rem 1.5rem; background:#f0fafe; border-radius:10px; display:flex; justify-content:space-between; font-size:18px; font-weight:700; color:#333; }
    .btn-back { display:inline-flex; align-items:center; gap:8px; margin-top:1.5rem; background:linear-gradient(90deg,#00c6ff,#0072ff); color:white; border:none; border-radius:10px; padding:0.8rem 1.6rem; font-size:1rem; cursor:pointer; font-weight:600; text-decoration:none; }
    .btn-back:hover { opacity:0.9; }
    .status-btn { padding:10px 18px; border:none; border-radius:8px; cursor:pointer; font-weight:700; font-size:14px; }
    .btn-confirmed { background:#0d6efd; color:#fff; }
    .btn-shipping  { background:#17a2b8; color:#fff; }
    .btn-delivered { background:#28a745; color:#fff; }
    .btn-cancelled { background:#e94b3c; color:#fff; }
    .no-action { color:#999; font-size:14px; font-style:italic; }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="wrap">
  <div class="order-header">
    <div>
      <h2>Chi tiết đơn hàng</h2>
      <p style="color:#999;font-size:14px;margin-top:4px;">Thông tin chi tiết về đơn hàng</p>
    </div>
    <div class="order-meta">
      <div>Mã đơn: <span><?php echo htmlspecialchars($order['id_order']); ?></span></div>
      <div>Ngày đặt: <span><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></span></div>
      <div>Trạng thái:
        <span class="status-badge status-<?php
          if($current_status==='Chờ xử lý')  echo 'pending';
          elseif($current_status==='Đã xác nhận') echo 'confirmed';
          elseif($current_status==='Đang giao')   echo 'shipping';
          elseif($current_status==='Đã giao')     echo 'delivered';
          elseif($current_status==='Đã huỷ')      echo 'cancelled';
        ?>"><?php echo htmlspecialchars($current_status); ?></span>
      </div>
    </div>
  </div>

  <div class="section-title">Thông tin khách hàng</div>
  <div class="info-grid">
    <div class="info-row"><strong>Tên khách:</strong><br><span><?php echo htmlspecialchars($order['receiver_name'] ?? $order['username']); ?></span></div>
    <div class="info-row"><strong>Email:</strong><br><span><?php echo htmlspecialchars($order['email']); ?></span></div>
    <div class="info-row"><strong>Số điện thoại:</strong><br><span><?php echo htmlspecialchars($order['receiver_phone'] ?? '—'); ?></span></div>
    <div class="info-row"><strong>Địa chỉ:</strong><br><span><?php echo htmlspecialchars($order['receiver_address'] ?? '—'); ?></span></div>
  </div>

  <div class="section-title">Thông tin đơn hàng</div>
  <div class="info-grid">
    <div class="info-row"><strong>Phương thức thanh toán:</strong><br><span><?php echo htmlspecialchars($order['payment_method'] ?? '—'); ?></span></div>
    <div class="info-row"><strong>Ghi chú:</strong><br><span><?php echo htmlspecialchars($order['note'] ?? '—'); ?></span></div>
  </div>

  <div class="section-title">Danh sách sản phẩm</div>
  <table>
    <thead>
      <tr>
        <th>STT</th>
        <th>Tên sản phẩm</th>
        <th>Số lượng</th>
        <th>Đơn giá</th>
        <th>Thành tiền</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $stt = 1;
    while($item = $items->fetch_assoc()):
        $subtotal = $item['quantity'] * $item['Price'];
    ?>
    <tr>
      <td><?php echo $stt++; ?></td>
      <td style="text-align:left;"><?php echo htmlspecialchars($item['ProductName']); ?></td>
      <td><?php echo $item['quantity']; ?></td>
      <td><?php echo number_format($item['Price'], 0, ',', '.'); ?> VNĐ</td>
      <td><?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <div class="total-box">
    <span>Tổng tiền đơn hàng:</span>
    <span style="color:#0072ff;"><?php echo number_format($order['total'], 0, ',', '.'); ?> VNĐ</span>
  </div>

  <div class="section-title">Cập nhật trạng thái</div>
  <form method="post" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
    <input type="hidden" name="id_order" value="<?php echo htmlspecialchars($order['id_order']); ?>">
    <input type="hidden" name="update_status" value="1">

    <?php if($next): ?>
      <button type="submit" name="status" value="<?php echo $next['value']; ?>"
        class="status-btn <?php echo $next['class']; ?>">
        ➜ <?php echo $next['label']; ?>
      </button>
    <?php endif; ?>

    <?php if($show_cancel): ?>
      <button type="submit" name="status" value="Đã huỷ"
        class="status-btn btn-cancelled"
        onclick="return confirm('Bạn chắc chắn muốn huỷ đơn này?')">
        Huỷ đơn
      </button>
    <?php endif; ?>

    <?php if(!$next && !$show_cancel): ?>
      <span class="no-action">Đơn hàng đã hoàn tất, không thể thay đổi trạng thái.</span>
    <?php endif; ?>
  </form>

  <?php
  $back_url  = isset($_GET['from']) && $_GET['from']==='baocao' ? 'baocao.php' : 'shiping.php';
  $back_text = isset($_GET['from']) && $_GET['from']==='baocao' ? '← Quay về báo cáo' : '← Quay về danh sách';
  ?>
  <a href="<?php echo $back_url; ?>" class="btn-back"><?php echo $back_text; ?></a>
</div>

</body>
</html>