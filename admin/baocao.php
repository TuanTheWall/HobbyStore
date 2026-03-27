<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","hobbystore");

$fname = $_GET['fname'] ?? "";
$category = $_GET['chat'] ?? "";
$from = $_GET['from'] ?? "";
$to = $_GET['to'] ?? "";

# Pagination
$limit = 5;
$page = $_GET['page'] ?? 1;
$start = ($page-1)*$limit;

$sql = "
SELECT DISTINCT p.*
FROM product_list p
LEFT JOIN history h ON p.ProductID = h.ProductID
WHERE 1
";

if($fname!=""){
    $sql .= " AND p.ProductName LIKE '%$fname%'";
}
if($category!="" && $category!="cl"){
    $sql .= " AND p.Grade='$category'";
}
if($from != ""){
    $sql .= " AND h.update_date >= '$from'";
}
if($to != ""){
    $sql .= " AND h.update_date <= '$to'";
}

$total_query = mysqli_query($conn,$sql);
$total_rows = mysqli_num_rows($total_query);
$total_page = ceil($total_rows/$limit);

$sql .= " LIMIT $start,$limit";
$result = mysqli_query($conn,$sql);
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

  /* ===== MODAL ===== */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
  }
  .modal-overlay.active { display: flex; }

  .modal-box {
    background: #fff;
    border-radius: 12px;
    padding: 28px 32px;
    width: 700px;
    max-width: 95vw;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    position: relative;
  }
  .modal-box h3 {
    font-size: 20px;
    margin-bottom: 16px;
    color: #22314e;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 10px;
  }
  .modal-close {
    position: absolute;
    top: 14px; right: 18px;
    background: none; border: none;
    font-size: 24px; cursor: pointer;
    color: #666;
  }
  .modal-close:hover { color: red; }

  .modal-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 15px;
  }
  .modal-table th {
    background: #22314e;
    color: #fff;
    padding: 10px 12px;
    text-align: left;
  }
  .modal-table td {
    padding: 9px 12px;
    border-bottom: 1px solid #eee;
  }
  .modal-table tr:hover td { background: #f0f7ff; }

  .modal-empty {
    text-align: center;
    color: #888;
    padding: 30px 0;
    font-size: 16px;
  }

  /* Nút xem chi tiết */
  .btn-detail {
    display: inline-block;
    margin-top: 6px;
    padding: 4px 12px;
    font-size: 13px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.2s;
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
    <option value="cl">Tất cả</option>
    <option value="HG" <?php if($category=="HG") echo "selected"; ?>>High Grade</option>
    <option value="RG" <?php if($category=="RG") echo "selected"; ?>>Real Grade</option>
    <option value="MG" <?php if($category=="MG") echo "selected"; ?>>Master Grade</option>
    <option value="PG" <?php if($category=="PG") echo "selected"; ?>>Perfect Grade</option>
    <option value="Figure" <?php if($category=="Figure") echo "selected"; ?>>Anime Figure</option>
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

    $q = mysqli_query($conn, "
        SELECT import_num, export_num, quantity
        FROM history
        WHERE ProductID = '$product_id'
        ORDER BY update_date DESC
        LIMIT 1
    ");
    $data = mysqli_fetch_assoc($q);

    $q_product = mysqli_query($conn, "SELECT Quantity FROM product_list WHERE ProductID = '$product_id'");
    $product = mysqli_fetch_assoc($q_product);
    $product_quantity = $product ? $product['Quantity'] : 0;

    if ($data) {
        $tondau = $data['quantity'];
        $import = $data['import_num'];
        $export = $data['export_num'];
        if ($tondau == 0 && $product_quantity != 0) {
            $tondau = $product_quantity;
        }
    } else {
        $tondau = $product_quantity;
        $import = 0;
        $export = 0;
    }

    $toncuoi = $tondau + $import - $export;
    $pid_safe = htmlspecialchars($product_id);

    // Tính xuất trong kỳ trực tiếp từ order_item (có lọc ngày nếu có)
    $where_xuat = "oi.ProductID = '$product_id' AND o.status = 'Đã giao'";
    if ($from != "") $where_xuat .= " AND o.order_date >= '$from'";
    if ($to   != "") $where_xuat .= " AND o.order_date <= '$to'";

    $q_xuat = mysqli_query($conn, "
        SELECT COUNT(*) as cnt, COALESCE(SUM(oi.quantity), 0) as tong_xuat
        FROM order_item oi
        JOIN orders o ON oi.id_order = o.id_order
        WHERE $where_xuat
    ");
    $xuat_data = mysqli_fetch_assoc($q_xuat);
    $has_xuat  = $xuat_data['cnt'] > 0;
    $export    = (int)$xuat_data['tong_xuat']; // ghi đè export từ history
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
    <?php if($has_xuat): ?>
      <br><button class="btn-detail xuat" onclick="xemChiTiet('xuat','<?php echo $pid_safe ?>','<?php echo htmlspecialchars($row['ProductName']) ?>')">Xem đơn hàng</button>
    <?php endif; ?>
  </td>
  <td><?php echo $toncuoi ?></td>
</tr>
<?php } ?>
</table>

<div class="pagination" style="margin-top:100px;margin-bottom:50px;">
<?php if($page>1){ ?>
  <a href="?page=<?php echo $page-1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>">&laquo;</a>
<?php } ?>
<?php for($i=1;$i<=$total_page;$i++){ ?>
  <a href="?page=<?php echo $i ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>" class="<?php if($i==$page) echo 'active'; ?>"><?php echo $i ?></a>
<?php } ?>
<?php if($page<$total_page){ ?>
  <a href="?page=<?php echo $page+1 ?>&fname=<?php echo $fname ?>&chat=<?php echo $category ?>">&raquo;</a>
<?php } ?>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="dongModal(event)">
  <div class="modal-box">
    <button class="modal-close" onclick="closeModal()">&times;</button>
    <h3 id="modalTitle">Chi tiết</h3>
    <div id="modalContent"><div class="loading">Đang tải...</div></div>
  </div>
</div>

<script>
function xemChiTiet(loai, productId, productName) {
  document.getElementById('modalTitle').textContent =
    loai === 'nhap'
      ? 'Phiếu nhập — ' + productName
      : 'Đơn hàng đã giao — ' + productName;

  document.getElementById('modalContent').innerHTML = '<div class="loading">Đang tải...</div>';
  document.getElementById('modalOverlay').classList.add('active');

  fetch('baocao_detail.php?loai=' + loai + '&product_id=' + encodeURIComponent(productId))
    .then(r => r.text())
    .then(html => { document.getElementById('modalContent').innerHTML = html; })
    .catch(() => { document.getElementById('modalContent').innerHTML = '<div class="modal-empty">Lỗi tải dữ liệu.</div>'; });
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('active');
}

function dongModal(e) {
  if (e.target === document.getElementById('modalOverlay')) closeModal();
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeModal();
});
</script>

</body>
</html>