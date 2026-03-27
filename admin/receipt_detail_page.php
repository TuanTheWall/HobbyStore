<?php
session_start();
if(!isset($_SESSION['admin'])){ header("Location: adminlogin.php"); exit(); }

$conn = new mysqli("localhost","root","","hobbystore");
$conn->set_charset("utf8");

$code = isset($_GET['code']) ? $conn->real_escape_string($_GET['code']) : '';
if(empty($code)){ header("Location: quanlynhaphang.php"); exit; }

/* lấy thông tin phiếu nhập */
$receipt = $conn->query("SELECT * FROM purchase_receipts WHERE receipt_code='$code'")->fetch_assoc();
if(!$receipt){ header("Location: quanlynhaphang.php"); exit; }

/* lấy danh sách sản phẩm trong phiếu */
$items = $conn->query("
    SELECT pri.*, p.ProductName, p.Product_image, p.Grade
    FROM purchase_receipt_items pri
    LEFT JOIN product_list p ON pri.product_id = p.ProductID
    WHERE pri.receipt_code = '$code'
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chi tiết phiếu nhập</title>
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
    .receipt-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:2px solid #eee; }
    .receipt-header h2 { font-size:1.5rem; color:#344767; }
    .receipt-meta { font-size:15px; color:#555; line-height:1.8; }
    .receipt-meta span { font-weight:700; color:#333; }
    
    table { border-collapse:collapse; width:100%; margin-top:1rem; }
    th { border:2px solid #ddd; text-align:center; padding:10px; background:#96dee0; }
    td { border:1px solid #ddd; text-align:center; padding:10px; }
    td img { width:80px; border-radius:6px; }
    tr:hover { background:#f9f9f9; }
    
    .total-box { margin-top:1.5rem; padding:1rem 1.5rem; background:#f0fafe; border-radius:10px; display:flex; justify-content:space-between; font-size:18px; font-weight:700; color:#333; }
    .btn-back { display:inline-flex; align-items:center; gap:8px; margin-top:1.5rem; background:linear-gradient(90deg,#00c6ff,#0072ff); color:white; border:none; border-radius:10px; padding:0.8rem 1.6rem; font-size:1rem; cursor:pointer; font-weight:600; text-decoration:none; }
    .btn-back:hover { opacity:0.9; }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="wrap">
  <div class="receipt-header">
    <div>
      <h2>Chi tiết phiếu nhập</h2>
      <p style="color:#999;font-size:14px;margin-top:4px;">Thông tin chi tiết về phiếu nhập hàng</p>
    </div>
    <div class="receipt-meta">
      <div>Mã phiếu: <span><?php echo htmlspecialchars($receipt['receipt_code']); ?></span></div>
      <div>Ngày nhập: <span><?php echo date('d/m/Y', strtotime($receipt['import_date'])); ?></span></div>
      <div>Tổng số lượng: <span><?php echo $receipt['total_quantity']; ?> sản phẩm</span></div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>STT</th>
        <th>Hình ảnh</th>
        <th>Tên sản phẩm</th>
        <th>Mã SP</th>
        <th>Dòng</th>
        <th>Số lượng nhập</th>
        <th>Giá nhập</th>
        <th>Thành tiền</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $stt = 1;
    $grand_total = 0;
    while($item = $items->fetch_assoc()):
        $subtotal = $item['quantity'] * $item['price'];
        $grand_total += $subtotal;
    ?>
    <tr>
      <td><?php echo $stt++; ?></td>
      <td>
        <?php if(!empty($item['Product_image'])): ?>
          <img src="assets/img/<?php echo htmlspecialchars($item['Product_image']); ?>" alt="">
        <?php else: ?>
          <span style="color:#999;">—</span>
        <?php endif; ?>
      </td>
      <td style="text-align:left;"><?php echo htmlspecialchars($item['ProductName'] ?? $item['product_id']); ?></td>
      <td><?php echo htmlspecialchars($item['product_id']); ?></td>
      <td><?php echo htmlspecialchars($item['Grade'] ?? '—'); ?></td>
      <td><?php echo $item['quantity']; ?></td>
      <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
      <td><?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>

  <div class="total-box">
    <span>Tổng tiền phiếu nhập:</span>
    <span style="color:#0072ff;"><?php echo number_format($receipt['total_value'], 0, ',', '.'); ?> VNĐ</span>
  </div>

  <?php
  $back_url = isset($_GET['from']) && $_GET['from'] === 'baocao' ? 'baocao.php' : 'quanlynhaphang.php';
  $back_text = isset($_GET['from']) && $_GET['from'] === 'baocao' ? '← Quay về báo cáo' : '← Quay về danh sách';
  ?>
  <a href="<?php echo $back_url; ?>" class="btn-back"><?php echo $back_text; ?></a>
</div>

</body>
</html>
