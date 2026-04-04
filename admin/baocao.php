<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

$grade_options = [];
$grade_query = mysqli_query($conn, "SELECT DISTINCT Grade FROM product_list ORDER BY Grade");
while($grade_row = mysqli_fetch_assoc($grade_query)){
    $grade_options[] = $grade_row['Grade'];
}

$fname    = $_GET['fname'] ?? "";
$category = $_GET['chat']  ?? "";
$to       = $_GET['to']    ?? date("Y-m-d");
$from     = $_GET['from']  ?? date("Y-m-d", strtotime("-7 days"));

$limit = 5;
$page  = $_GET['page'] ?? 1;
$start = ($page-1)*$limit;

$sql = "SELECT DISTINCT p.* FROM product_list p LEFT JOIN history h ON p.ProductID = h.ProductID WHERE 1";
if($fname != "") $sql .= " AND p.ProductName LIKE '%$fname%'";
if($category != "" && $category != "cl") $sql .= " AND p.Grade='$category'";
if($from != "") $sql .= " AND h.update_date >= '$from'";
if($to != "")   $sql .= " AND h.update_date <= '$to'";

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
  * { margin:0; padding:0; box-sizing:border-box; font-family:"Josefin Sans",sans-serif; }
  body { background-color:#f5f5f5; }

  .btn-detail { display:inline-block; margin-top:6px; padding:4px 12px; font-size:13px; border:none; border-radius:5px; cursor:pointer; font-weight:600; transition:0.2s; }
  .btn-detail.nhap { background:#d4edda; color:#1a6630; }
  .btn-detail.nhap:hover { background:#b8dfc4; }
  .btn-detail.xuat { background:#fde8d8; color:#a0410d; }
  .btn-detail.xuat:hover { background:#f9cfb5; }

  /* Modal */
  .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55); justify-content:center; align-items:center; z-index:9999; }
  .modal-overlay.show { display:flex; }
  .modal-box { background:white; border-radius:14px; padding:28px; width:780px; max-width:95%; max-height:80vh; overflow-y:auto; box-shadow:0 8px 30px rgba(0,0,0,0.2); }
  .modal-box h3 { font-size:20px; margin-bottom:16px; color:#22314e; border-bottom:2px solid #96dee0; padding-bottom:10px; }
  .modal-table { width:100%; border-collapse:collapse; font-size:15px; }
  .modal-table th { background:#22314e; color:#fff; padding:10px 12px; text-align:left; }
  .modal-table td { padding:9px 12px; border-bottom:1px solid #eee; }
  .modal-table tr:hover td { background:#f0f7ff; }
  .modal-empty { text-align:center; color:#888; padding:30px 0; font-size:16px; }
  .modal-close { display:block; margin-top:18px; margin-left:auto; background:#e94b3c; color:white; border:none; padding:8px 22px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; }
  .modal-close:hover { background:#c0392b; }
  .loading { text-align:center; padding:30px; color:#888; font-size:16px; }
  .link-detail { color:#0072ff; text-decoration:none; font-weight:600; }
  .link-detail:hover { text-decoration:underline; }
</style>
</head>
<body>

<?php include "navbar.php"; include "menucard.php"; ?>

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
  <th>STT</th><th>Tên</th><th>Hình ảnh</th>
  <th>Tồn đầu kỳ</th><th>Nhập trong kỳ</th>
  <th>Xuất trong kỳ</th><th>Tồn cuối kỳ</th>
</tr>
<?php
$stt = $start + 1;
while($row = mysqli_fetch_assoc($result)){
    $product_id = $row['ProductID'];
    $toncuoi    = (int)$row['Quantity'];

    $where_nhap = "product_id = '$product_id'";
    if($from != "") $where_nhap .= " AND pr.import_date >= '$from'";
    if($to   != "") $where_nhap .= " AND pr.import_date <= '$to'";
    $q_nhap  = mysqli_query($conn, "SELECT COALESCE(SUM(pri.quantity),0) AS tong FROM purchase_receipt_items pri JOIN purchase_receipts pr ON pri.receipt_code=pr.receipt_code WHERE $where_nhap");
    $import  = (int)mysqli_fetch_assoc($q_nhap)['tong'];

    $where_xuat = "oi.ProductID = '$product_id' AND o.status = 'Đã giao'";
    if($from != "") $where_xuat .= " AND o.order_date >= '$from'";
    if($to   != "") $where_xuat .= " AND o.order_date <= '$to'";
    $q_xuat  = mysqli_query($conn, "SELECT COALESCE(SUM(oi.quantity),0) AS tong FROM order_item oi JOIN orders o ON oi.id_order=o.id_order WHERE $where_xuat");
    $export  = (int)mysqli_fetch_assoc($q_xuat)['tong'];

    $tondau   = $toncuoi - $import + $export;
    $pid_safe = htmlspecialchars($product_id);
    $pname    = htmlspecialchars($row['ProductName']);
?>
<tr>
  <td><?php echo $stt++ ?></td>
  <td><?php echo $pname ?></td>
  <td><img src="assets/img/<?php echo $row['Product_image'] ?>" width="200"></td>
  <td><?php echo $tondau ?></td>
  <td>
    <?php echo $import ?>
    <?php if($import > 0): ?>
      <br><button class="btn-detail nhap" onclick="xemChiTiet('nhap','<?php echo $pid_safe ?>','<?php echo $pname ?>')">Xem phiếu nhập</button>
    <?php endif; ?>
  </td>
  <td>
    <?php echo $export ?>
    <?php if($export > 0): ?>
      <br><button class="btn-detail xuat" onclick="xemChiTiet('xuat','<?php echo $pid_safe ?>','<?php echo $pname ?>')">Xem đơn hàng</button>
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

<!-- Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box">
    <h3 id="modalTitle">Chi tiết</h3>
    <div id="modalContent"><div class="loading">Đang tải...</div></div>
    <button class="modal-close" onclick="closeModal()">✕ Đóng</button>
  </div>
</div>

<script>
const FROM = "<?php echo $from ?>";
const TO   = "<?php echo $to ?>";

function xemChiTiet(loai, productId, productName){
  const overlay = document.getElementById('modalOverlay');
  const title   = document.getElementById('modalTitle');
  const content = document.getElementById('modalContent');

  title.textContent = loai === 'nhap'
    ? '📦 Phiếu nhập — ' + productName
    : '🚚 Đơn hàng xuất — ' + productName;

  content.innerHTML = '<div class="loading">Đang tải...</div>';
  overlay.classList.add('show');

  fetch('baocao_detail.php?loai=' + loai
    + '&product_id=' + encodeURIComponent(productId)
    + '&from=' + encodeURIComponent(FROM)
    + '&to='   + encodeURIComponent(TO))
    .then(r => r.json())
    .then(data => {
      if(data.error){
        content.innerHTML = '<p class="modal-empty">' + data.error + '</p>';
        return;
      }
      if(data.data.length === 0){
        content.innerHTML = '<p class="modal-empty">Không có dữ liệu trong khoảng thời gian này.</p>';
        return;
      }

      let html = '<table class="modal-table">';

      if(data.type === 'nhap'){
        html += `<thead><tr>
          <th>Mã phiếu</th><th>Ngày nhập</th>
          <th>Số lượng</th><th>Tổng tiền</th><th>Chi tiết</th>
        </tr></thead><tbody>`;
        data.data.forEach(r => {
          html += `<tr>
            <td>${r.receipt_code}</td>
            <td>${r.import_date}</td>
            <td>${r.tong_sl}</td>
            <td>${parseInt(r.tong_tien).toLocaleString('vi-VN')} VNĐ</td>
            <td><a class="link-detail" href="receipt_detail_page.php?code=${encodeURIComponent(r.receipt_code)}&from=baocao" target="_blank">Xem →</a></td>
          </tr>`;
        });
      } else {
        html += `<thead><tr>
          <th>Mã đơn</th><th>Ngày giao</th>
          <th>Khách hàng</th><th>SĐT</th><th>SL</th><th>Chi tiết</th>
        </tr></thead><tbody>`;
        data.data.forEach(r => {
          html += `<tr>
            <td>${r.id_order}</td>
            <td>${r.order_date}</td>
            <td>${r.receiver_name ?? '—'}</td>
            <td>${r.receiver_phone ?? '—'}</td>
            <td>${r.quantity}</td>
            <td><a class="link-detail" href="order_detail_page.php?id=${encodeURIComponent(r.id_order)}&from=baocao" target="_blank">Xem →</a></td>
          </tr>`;
        });
      }

      html += '</tbody></table>';
      content.innerHTML = html;
    })
    .catch(() => {
      content.innerHTML = '<p class="modal-empty">Lỗi tải dữ liệu.</p>';
    });
}

function closeModal(){
  document.getElementById('modalOverlay').classList.remove('show');
}

document.getElementById('modalOverlay').addEventListener('click', function(e){
  if(e.target === this) closeModal();
});
</script>

</body>
</html>