<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

// Load danh mục động từ database
$grade_options = [];
$grade_query = mysqli_query($conn, "SELECT DISTINCT Grade FROM product_list ORDER BY Grade");
while($grade_row = mysqli_fetch_assoc($grade_query)){
    $grade_options[] = $grade_row['Grade'];
}

$fname    = $_GET['fname'] ?? "";
$category = $_GET['chat']  ?? "";
$from     = $_GET['from']  ?? "";
$to       = $_GET['to']    ?? "";

# Pagination
$limit = 5;
$page  = $_GET['page'] ?? 1;
$start = ($page-1)*$limit;

$sql = "
SELECT DISTINCT p.*
FROM product_list p
LEFT JOIN history h ON p.ProductID = h.ProductID
WHERE 1
";

if($fname != ""){
    $sql .= " AND p.ProductName LIKE '%$fname%'";
}
if($category != "" && $category != "cl"){
    $sql .= " AND p.Grade='$category'";
}
if($from != ""){
    $sql .= " AND h.update_date >= '$from'";
}
if($to != ""){
    $sql .= " AND h.update_date <= '$to'";
}

$total_query = mysqli_query($conn, $sql);
$total_rows  = mysqli_num_rows($total_query);
$total_page  = ceil($total_rows / $limit);

$sql .= " LIMIT $start,$limit";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Báo cáo nhập-xuất-tồn</title>
<link rel="stylesheet" href="assets/css/storagestyle.css">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Josefin Sans", sans-serif; }
  body { background-color: #f5f5f5; }

  .modal-table { width: 100%; border-collapse: collapse; font-size: 15px; }
  .modal-table th { background: #22314e; color: #fff; padding: 10px 12px; text-align: left; }
  .modal-table td { padding: 9px 12px; border-bottom: 1px solid #eee; }
  .modal-table tr:hover td { background: #f0f7ff; }
  .modal-empty { text-align: center; color: #888; padding: 30px 0; font-size: 16px; }

  .btn-detail {
    display: inline-block; margin-top: 6px; padding: 4px 12px;
    font-size: 13px; border: none; border-radius: 5px; cursor: pointer;
    font-weight: 600; transition: 0.2s;
  }
  .btn-detail.nhap { background: #d4edda; color: #1a6630; }
  .btn-detail.nhap:hover { background: #b8dfc4; }
  .btn-detail.xuat { background: #fde8d8; color: #a0410d; }
  .btn-detail.xuat:hover { background: #f9cfb5; }
  .loading { text-align: center; padding: 20px; color: #888; }
</style>
</head>

<body>

<?php 
include "navbar.php";
include "menucard.php";
?>

<div class="headtext">
  <a href="tracuusoluong.php"><p>Tra cứu tồn kho</p></a>
  <a href="canhbao.php"><p>Cảnh báo tồn kho</p></a>
  <a href="baocao.php"><p class="underline-text" style="color:red;">Báo cáo nhập-xuất-tồn</p></a>
</div>

<div class="search-box">
<form method="GET" class="search-advanced">
  <label style="font-size:25px;">Tìm theo tên</label><br>
  <input type="text" name="fname" value="<?php echo $fname ?>" placeholder="Name" style="font-size:20px;width:220px;height:38px;"><br>
  <label style="font-size:25px;">Danh mục</label><br>
  <select name="chat">
    <option value="cl" <?php if($category=="cl") echo "selected"; ?>>Tất cả</option>
    <?php foreach($grade_options as $g): ?>
      <option value="<?php echo htmlspecialchars($g); ?>" <?php if($category===$g) echo "selected"; ?>><?php echo htmlspecialchars($g); ?></option>
    <?php endforeach; ?>
  </select><br>
  <label style="font-size:25px;">Từ</label><br>
  <input type="date" name="from" value="<?php echo $from ?>">
  <label style="font-size:25px;">Đến</label><br>
  <input type="date" name="to" value="<?php echo $to ?>">
  <br><br>
  <button type="submit" class="btn-tim">Tìm</button>
  <a href="baocao.php"><button type="button" class="btn-tim" style="background:gray;">Đặt lại</button></a>
</form>
</div>

<table>
<tr>
  <th>STT</th>
  <th>Tên</th>
  <th>Hình ảnh</th>
  <th>Tồn đầu kỳ</th>
  <th>Nhập trong kỳ</th>
  <th>Xuất trong kỳ</th>
  <th>Tồn cuối kỳ</th>
</tr>

<?php
$stt = $start + 1;
while ($row = mysqli_fetch_assoc($result)) {
    $product_id = $row['ProductID'];

    // ── Tồn cuối kỳ = số thực tế trong product_list (trigger luôn giữ đúng) ──
    $toncuoi = (int)$row['Quantity'];

    // ── Tổng nhập trong kỳ (lọc theo from/to nếu có) ──
    $where_nhap = "product_id = '$product_id'";
    if ($from != "") $where_nhap .= " AND pr.import_date >= '$from'";
    if ($to   != "") $where_nhap .= " AND pr.import_date <= '$to'";

    $q_nhap = mysqli_query($conn, "
        SELECT COALESCE(SUM(pri.quantity), 0) AS tong_nhap
        FROM purchase_receipt_items pri
        JOIN purchase_receipts pr ON pri.receipt_code = pr.receipt_code
        WHERE $where_nhap
    ");
    $nhap_data = mysqli_fetch_assoc($q_nhap);
    $import    = (int)$nhap_data['tong_nhap'];

    // ── Tổng xuất trong kỳ (chỉ đơn Đã giao, lọc theo from/to nếu có) ──
    $where_xuat = "oi.ProductID = '$product_id' AND o.status = 'Đã giao'";
    if ($from != "") $where_xuat .= " AND o.order_date >= '$from'";
    if ($to   != "") $where_xuat .= " AND o.order_date <= '$to'";

    $q_xuat   = mysqli_query($conn, "
        SELECT COALESCE(SUM(oi.quantity), 0) AS tong_xuat
        FROM order_item oi
        JOIN orders o ON oi.id_order = o.id_order
        WHERE $where_xuat
    ");
    $xuat_data = mysqli_fetch_assoc($q_xuat);
    $export    = (int)$xuat_data['tong_xuat'];

    // ── Tồn đầu kỳ = Tồn cuối kỳ - Nhập trong kỳ + Xuất trong kỳ ──
    $tondau = $toncuoi - $import + $export;

    $pid_safe = htmlspecialchars($product_id);
?>
<tr>
  <td><?php echo $stt++ ?></td>
  <td><?php echo $row['ProductName'] ?></td>
  <td><img src="assets/img/<?php echo $row['Product_image'] ?>" width="200"></td>
  <td><?php echo $tondau ?></td>
  <td>
    <?php echo $import ?>
    <?php if($import > 0): ?>
      <br><button class="btn-detail nhap" onclick="xemChiTiet('nhap','<?php echo $pid_safe ?>','<?php echo htmlspecialchars($row['ProductName']) ?>')">Xem phiếu nhập</button>
    <?php endif; ?>
  </td>
  <td>
    <?php echo $export ?>
    <?php if($export > 0): ?>
      <br><button class="btn-detail xuat" onclick="xemChiTiet('xuat','<?php echo $pid_safe ?>','<?php echo htmlspecialchars($row['ProductName']) ?>')">Xem đơn hàng</button>
    <?php endif; ?>
  </td>
  <td><?php echo $toncuoi ?></td>
</tr>
<?php } ?>
</table>

<div class="pagination" style="margin-top:100px;margin-bottom:50px;">
<?php if($page > 1): ?>
  <a href="?page=<?php echo $page-1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>&from=<?php echo $from ?>&to=<?php echo $to ?>">&laquo;</a>
<?php endif; ?>
<?php for($i=1; $i<=$total_page; $i++): ?>
  <a href="?page=<?php echo $i ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>&from=<?php echo $from ?>&to=<?php echo $to ?>" class="<?php if($i==$page) echo 'active'; ?>"><?php echo $i ?></a>
<?php endfor; ?>
<?php if($page < $total_page): ?>
  <a href="?page=<?php echo $page+1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>&from=<?php echo $from ?>&to=<?php echo $to ?>">&raquo;</a>
<?php endif; ?>
</div>

<script>
function xemChiTiet(loai, productId, productName) {
  const btn = event.target;
  const originalText = btn.textContent;
  btn.textContent = 'Đang tải...';
  btn.disabled = true;

  fetch('baocao_detail.php?loai=' + loai + '&product_id=' + encodeURIComponent(productId))
    .then(r => r.json())
    .then(data => {
      if (data.redirect) {
        window.location.href = data.redirect;
      } else {
        alert(data.error || 'Có lỗi xảy ra');
        btn.textContent = originalText;
        btn.disabled = false;
      }
    })
    .catch(() => {
      alert('Lỗi tải dữ liệu');
      btn.textContent = originalText;
      btn.disabled = false;
    });
}
</script>

</body>
</html>