<?php
$conn = new mysqli("localhost","root","","hobbystore");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

/* cập nhật profit và giá bán — giá vốn lấy từ DB, không cho sửa */
if(isset($_POST['product_id'])){
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $giaban     = (int)$_POST['giaban'];

    // Lấy cost_price từ DB để tính profit chính xác
    $r = $conn->query("SELECT cost_price FROM product_list WHERE ProductID='$product_id'");
    $cost_row = $r->fetch_assoc();
    $cost = (float)$cost_row['cost_price'];

    $profit = ($cost > 0) ? round(($giaban / $cost) - 1, 4) : 0;

    $conn->query("UPDATE product_list SET Price='$giaban', Profit='$profit' WHERE ProductID='$product_id'");
}

/* tìm kiếm */
$fname = isset($_GET['fname']) ? $_GET['fname'] : '';
$grade = isset($_GET['chat']) ? $_GET['chat'] : 'cl';
$range = isset($_GET['range']) ? $_GET['range'] : 'cl';

$where = "WHERE 1=1";
if(!empty($fname)){
    $fname_safe = $conn->real_escape_string($fname);
    $where .= " AND ProductName LIKE '%$fname_safe%'";
}
$grade_map = ['hg'=>'HG','rg'=>'RG','mg'=>'MG','pg'=>'PG','ag'=>'Figure'];
if($grade !== 'cl' && isset($grade_map[$grade])){
    $where .= " AND Grade='" . $grade_map[$grade] . "'";
} elseif($grade !== 'cl'){
    $where .= " AND Grade='" . $conn->real_escape_string($grade) . "'";
}
switch($range){
    case 'r1': $where .= " AND Profit >= 0 AND Profit < 0.1"; break;
    case 'r2': $where .= " AND Profit >= 0.1 AND Profit < 0.3"; break;
    case 'r3': $where .= " AND Profit >= 0.3 AND Profit < 0.5"; break;
    case 'r4': $where .= " AND Profit >= 0.5"; break;
}

/* phân trang */
$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

$count_result = $conn->query("SELECT COUNT(*) as total FROM product_list $where");
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = max(1, ceil($total_rows / $per_page));

$sql = "SELECT ProductID, ProductName, Grade, Price, Profit, cost_price FROM product_list $where LIMIT $per_page OFFSET $offset";
$result = $conn->query($sql);

$query_params = [];
if(!empty($fname)) $query_params[] = "fname=" . urlencode($fname);
if($grade !== 'cl') $query_params[] = "chat=" . urlencode($grade);
if($range !== 'cl') $query_params[] = "range=" . urlencode($range);
$query_string = count($query_params) ? '&' . implode('&', $query_params) : '';

$grades_result = $conn->query("SELECT DISTINCT Grade FROM product_list ORDER BY Grade");
$all_grades = [];
while($g = $grades_result->fetch_assoc()) $all_grades[] = $g['Grade'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý giá bán</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.pagination{ text-align:center; }
.pagination a{ color:black; text-decoration:none; padding:8px 15px; display:inline-block; }
.pagination a.active{ background-color:green; font-weight:bold; border-radius:5px; }
.pagination a:hover:not(.active){ background-color:gray; border-radius:5px; }
.search{ text-align:center; margin:20px; }
.cch{ color:black; } .cch:hover{ color:red; }
nav.navbar { display:flex !important; justify-content:space-between !important; align-items:center !important; background-color:cyan !important; height:80px !important; min-height:80px !important; padding:0 24px !important; box-sizing:border-box !important; gap:12px; z-index:50; }
.navbar .navbar-logo { width:64px !important; height:64px !important; min-width:64px !important; min-height:64px !important; border-radius:50% !important; object-fit:cover !important; display:inline-block !important; filter:none !important; -webkit-filter:none !important; mix-blend-mode:normal !important; background:transparent !important; opacity:1 !important; }
.nav-left { display:flex !important; align-items:center !important; gap:18px; }
.nav-left .nav-home { text-decoration:none; color:black; font-size:20px; font-weight:600; }
.nav-right { display:flex !important; align-items:center !important; gap:14px; }
.nav-right .hello { font-weight:600; color:black; }
.logout-btn { background-color:rgb(221,99,225) !important; border:2px solid black !important; border-radius:5px !important; padding:8px 12px !important; color:black !important; text-decoration:none !important; font-weight:700 !important; }
.nav-left a:hover,.nav-right a:hover,.nav-right .hello:hover { color:red !important; }
.btn-col{ margin-top:auto; }
.price { font-size:14px; margin-bottom:10px; }
.overview-menu { padding:30px; background-color:white; }
.overview-menu h2 { font-size:22px; color:black; margin-bottom:20px; }
.menu-grid { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; }
.menu-card { background:linear-gradient(to bottom,cyan,blue); color:#fff; text-align:center; padding:30px 20px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.1); transition:all 0.25s ease; cursor:pointer; }
.menu-card h3 { font-size:16px; font-weight:600; }
.menu-card:hover { transform:translateY(-5px); box-shadow:0 6px 18px rgba(0,0,0,0.15); }
.menu-card a{ color:white; text-decoration:none; }
.bt-them{ background:#04a4b3; }
.bt-them i{ margin-right:8px; width:25px; height:25px; font-size:25px; background:white; color:black; }
table{ border-collapse:collapse; width:90%; margin:0 auto; margin-top:30px; }
td{ border:1px solid #dddddd; text-align:center; padding:8px; }
th{ border:3px solid #dddddd; text-align:center; padding:8px; background:#96dee0; }
.thaotac{ padding-bottom:40px; padding-right:25px; }
.search-advanced { display:flex; align-items:center; justify-content:center; gap:20px; margin:30px auto; flex-wrap:wrap; }
.search-advanced label { font-size:20px; font-weight:bold; margin-right:6px; }
.search-advanced input,.search-advanced select { width:220px; height:38px; padding:6px 10px; font-size:16px; border:1px solid #aaa; border-radius:6px; }
.search-advanced button { background-color:blue; color:white; padding:8px 20px; font-size:16px; border:none; border-radius:6px; cursor:pointer; height:40px; transition:0.25s; }
.search-advanced button:hover { background-color:darkblue; }
.input-box input{ height:30px; border-radius:8px; width:100px; }
.input-price input{ height:30px; border-radius:8px; width:120px; }
.underline-text { font-size:18px; font-weight:bold; color:#333; text-decoration:underline; text-underline-offset:4px; }
.headtext { display:flex; gap:30px; justify-content:center; margin-top:20px; }
.giaban-cell { font-weight:bold; color:#0a7a00; }
.cost-display { color:#555; font-size:15px; font-weight:600; }
</style>
<script>
window.addEventListener('DOMContentLoaded', function(){
    function formatVND(num){
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' VNĐ';
    }

    document.querySelectorAll('tr.data-row').forEach(function(row){
        const cost        = parseFloat(row.dataset.cost) || 0;
        const inpProfit   = row.querySelector('.inp-profit');
        const inpGiaBan   = row.querySelector('.inp-giaban');
        const inpGiaBanRaw= row.querySelector('.inp-giaban-raw');

        // Giá bán thay đổi → cập nhật tỷ lệ lợi nhuận
        inpGiaBan.addEventListener('input', function(){
            let raw = this.value.replace(/[^\d]/g, '');
            inpGiaBanRaw.value = raw;
            this.setAttribute('data-raw', raw);
            if(raw) this.value = formatVND(raw);
            if(cost > 0){
                inpProfit.value = ((( parseFloat(raw) / cost) - 1) * 100).toFixed(2);
            }
        });
        inpGiaBan.addEventListener('focus', function(){
            this.value = this.getAttribute('data-raw') || '';
        });
        inpGiaBan.addEventListener('blur', function(){
            let raw = this.getAttribute('data-raw') || inpGiaBanRaw.value;
            if(raw) this.value = formatVND(raw);
        });

        // Tỷ lệ lợi nhuận thay đổi → cập nhật giá bán
        inpProfit.addEventListener('input', function(){
            const profit = parseFloat(this.value) || 0;
            const gb = Math.round(cost * (1 + profit / 100));
            inpGiaBanRaw.value = gb;
            inpGiaBan.setAttribute('data-raw', gb);
            inpGiaBan.value = formatVND(gb);
        });
    });
});
</script>
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
    <a href="lndm.php"><div class="menu-card" style="background:linear-gradient(to bottom,#19113b,blue);"><h3>Quản lý giá bán</h3></div></a>
    <a href="quanlydm.php"><div class="menu-card"><h3>Quản lý danh mục</h3></div></a>
    <a href="tracuusoluong.php"><div class="menu-card"><h3>Quản lý tồn kho</h3></div></a>
    <a href="quanlynhaphang.php"><div class="menu-card"><h3>Quản lý nhập hàng</h3></div></a>
  </div>
</section>

<div class="headtext">
  <a href="lndm.php"><p>TỶ LỆ LỢI NHUẬN THEO DANH MỤC</p></a>
  <!-- <a href="lnsp.php"><p>TỶ LỆ LỢI NHUẬN THEO SẢN PHẨM</p></a> -->
  <a href="ln.php"><p class="underline-text" style="color:red;">GIÁ BÁN</p></a>
</div>

<div class="search-box">
  <form action="ln.php" method="get" class="search-advanced">
    <label style="font-size:25px;" for="fname">Tìm theo tên:</label>
    <input style="color:gray;" type="text" id="fname" name="fname" placeholder="Tên sản phẩm" value="<?php echo htmlspecialchars($fname); ?>">
    <label style="font-size:25px;">Danh mục:</label>
    <select id="chat" name="chat">
      <option value="cl" <?php echo $grade=='cl'?'selected':''; ?>>Tất cả</option>
      <?php foreach($all_grades as $g):
          $grade_key = strtolower($g);
          if($g==='Figure') $grade_key='ag';
      ?>
      <option value="<?php echo $grade_key; ?>" <?php echo $grade==$grade_key?'selected':''; ?>><?php echo htmlspecialchars($g); ?></option>
      <?php endforeach; ?>
    </select>
    <label style="font-size:25px;">Khoảng lợi nhuận:</label>
    <select id="range" name="range">
      <option value="cl" <?php echo $range=='cl'?'selected':''; ?>>Tất cả</option>
      <option value="r1" <?php echo $range=='r1'?'selected':''; ?>>0 - 10%</option>
      <option value="r2" <?php echo $range=='r2'?'selected':''; ?>>10% - 30%</option>
      <option value="r3" <?php echo $range=='r3'?'selected':''; ?>>30% - 50%</option>
      <option value="r4" <?php echo $range=='r4'?'selected':''; ?>>Trên 50%</option>
    </select>
    <button type="submit" style="height:38px;margin-top:-4px;">Tìm</button>
    <a href="ln.php"><button type="button" style="height:38px;margin-top:-4px;background:gray;color:white;border:none;border-radius:6px;padding:8px 20px;font-size:16px;cursor:pointer;">Đặt lại</button></a>
  </form>

  <table>
    <tr>
      <th>STT</th>
      <th>Tên</th>
      <th>Danh mục</th>
      <th>Giá vốn (VNĐ)</th>
      <th>Tỷ lệ lợi nhuận</th>
      <th>Giá bán</th>
      <th>Thao tác</th>
    </tr>
    <?php
    $stt = $offset + 1;
    while($row = $result->fetch_assoc()):
        $cost   = (float)$row['cost_price'];
        $giaban = (int)$row['Price'];
        $profit_pct = round($row['Profit'] * 100, 2);
    ?>
    <tr class="data-row" data-cost="<?php echo $cost; ?>">
      <form method="post" action="ln.php?page=<?php echo $page.$query_string; ?>">
        <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
        <td><?php echo $stt++; ?></td>
        <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
        <td><?php echo htmlspecialchars($row['Grade']); ?></td>
        <td class="cost-display"><?php echo number_format($cost, 0, ',', '.'); ?> VNĐ</td>
        <td>
          <div class="input-box">
            <input class="inp-profit" type="number" name="profit" min="0" max="1000" step="0.01" value="<?php echo $profit_pct; ?>">
            <span>%</span>
          </div>
        </td>
        <td>
          <div class="input-price">
            <input class="inp-giaban" type="text" placeholder="Nhập giá..."
              value="<?php echo number_format($giaban, 0, ',', '.'); ?> VNĐ"
              data-raw="<?php echo $giaban; ?>">
            <input type="hidden" name="giaban" class="inp-giaban-raw" value="<?php echo $giaban; ?>">
          </div>
        </td>
        <td class="thaotac">
          <button type="submit" class="bt-them" style="background:green;color:white;">Lưu</button>
        </td>
      </form>
    </tr>
    <?php endwhile; ?>
  </table>

  <div class="pagination" style="margin-top:100px;margin-bottom:50px;">
    <a href="ln.php?page=1<?php echo $query_string; ?>">&laquo;</a>
    <?php for($i=1;$i<=$total_pages;$i++): ?>
      <a href="ln.php?page=<?php echo $i.$query_string; ?>" <?php echo $i==$page?'class="active"':''; ?>><?php echo $i; ?></a>
    <?php endfor; ?>
    <a href="ln.php?page=<?php echo $total_pages.$query_string; ?>">&raquo;</a>
  </div>
</div>
</body>
</html>