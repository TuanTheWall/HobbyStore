<?php
$conn = new mysqli("localhost","root","","hobbystore");

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

/* xóa sản phẩm */
if(isset($_GET['xoa'])){
    $id = $conn->real_escape_string($_GET['xoa']);
    $conn->query("DELETE FROM product_list WHERE ProductID='$id'");
    header("Location: quanlysp.php");
    exit;
}

/* tìm kiếm */
$fname = isset($_GET['fname']) ? $_GET['fname'] : '';
$grade = isset($_GET['chat']) ? $_GET['chat'] : 'cl';
$hang  = isset($_GET['hang']) ? $_GET['hang']  : 'hang';

$where = "WHERE 1=1";
if(!empty($fname)){
    $fname_safe = $conn->real_escape_string($fname);
    $where .= " AND ProductName LIKE '%$fname_safe%'";
}
$grade_map = [
    'hg' => 'HG', 'rg' => 'RG', 'mg' => 'MG', 'pg' => 'PG', 'ag' => 'Figure',
];
if($grade !== 'cl' && isset($grade_map[$grade])){
    $where .= " AND Grade='" . $grade_map[$grade] . "'";
}
$hang_map = [
    'bandai' => 'Bandai', 'sega' => 'SEGA', 'banpresto' => 'Banpresto',
];
if($hang !== 'hang' && isset($hang_map[$hang])){
    $where .= " AND Producer='" . $hang_map[$hang] . "'";
}

/* phân trang */
$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

$count_result = $conn->query("SELECT COUNT(*) as total FROM product_list $where");
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = max(1, ceil($total_rows / $per_page));

$result = $conn->query("SELECT * FROM product_list $where LIMIT $per_page OFFSET $offset");

/* giữ query string cho pagination */
$query_params = [];
if(!empty($fname)) $query_params[] = "fname=" . urlencode($fname);
if($grade !== 'cl') $query_params[] = "chat=" . urlencode($grade);
if($hang !== 'hang') $query_params[] = "hang=" . urlencode($hang);
$query_string = count($query_params) ? '&' . implode('&', $query_params) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý sản phẩm</title>
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
.search-advanced { text-align:center; margin:20px 0; }
.search-advanced button { background-color:blue; color:white; padding:12px 24px; font-size:16px; border:none; border-radius:6px; cursor:pointer; }
.cch{ color:black; }
.cch:hover{ color:red; }
nav.navbar {
  display:flex !important; justify-content:space-between !important;
  align-items:center !important; background-color:cyan !important;
  height:80px !important; min-height:80px !important;
  padding:0 24px !important; box-sizing:border-box !important;
  gap:12px; z-index:50;
}
.navbar .navbar-logo {
  width:64px !important; height:64px !important;
  min-width:64px !important; min-height:64px !important;
  border-radius:50% !important; object-fit:cover !important;
  display:inline-block !important; filter:none !important;
  -webkit-filter:none !important; mix-blend-mode:normal !important;
  background:transparent !important; opacity:1 !important;
}
.nav-left { display:flex !important; align-items:center !important; gap:18px; }
.nav-left .nav-home { text-decoration:none; color:black; font-size:20px; font-weight:600; }
.nav-right { display:flex !important; align-items:center !important; gap:14px; }
.nav-right .hello { font-weight:600; color:black; }
.logout-btn {
  background-color:rgb(221,99,225) !important; border:2px solid black !important;
  border-radius:5px !important; padding:8px 12px !important;
  color:black !important; text-decoration:none !important; font-weight:700 !important;
}
.nav-left a:hover, .nav-right a:hover, .nav-right .hello:hover { color:red !important; }
.btn-col{ margin-top:auto; }
.price { font-size:14px; margin-bottom:10px; }
.overview-menu { padding:30px; background-color:white; }
.overview-menu h2 { font-size:22px; color:black; margin-bottom:20px; }
.menu-grid { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; }
.menu-card {
  background:linear-gradient(to bottom,cyan,blue); color:#fff;
  text-align:center; padding:30px 20px; border-radius:15px;
  box-shadow:0 4px 10px rgba(0,0,0,0.1); transition:all 0.25s ease; cursor:pointer;
}
.menu-card i { font-size:36px; margin-bottom:15px; }
.menu-card h3 { font-size:16px; font-weight:600; }
.menu-card:hover { transform:translateY(-5px); box-shadow:0 6px 18px rgba(0,0,0,0.15); }
.menu-card a{ color:white; text-decoration:none; }
.bt-them{ background:#04a4b3; }
.bt-them i{ margin-right:8px; width:25px; height:25px; font-size:25px; background:white; color:black; }
table{ border-collapse:collapse; width:90%; margin:0 auto; margin-top:30px; table-layout:fixed; }
td{ border:1px solid #dddddd; text-align:center; padding:8px; word-wrap:break-word; }
th{ border:3px solid #dddddd; text-align:center; padding:8px; background:#96dee0; }
table th:nth-child(1){ width:5%; }
table th:nth-child(2){ width:35%; }
table th:nth-child(3){ width:12%; }
table th:nth-child(4){ width:13%; }
table th:nth-child(5){ width:10%; }
table th:nth-child(6){ width:15%; }
.thaotac{ padding-bottom:40px; padding-right:25px; }
.search-advanced {
  display:flex; align-items:center; justify-content:center;
  gap:10px; margin:30px auto; flex-wrap:wrap;
}
.search-advanced label { font-size:20px; font-weight:bold; margin-right:6px; }
.search-advanced input, .search-advanced select {
  width:220px; height:38px; padding:6px 10px;
  font-size:16px; border:1px solid #aaa; border-radius:6px;
}
.search-advanced button {
  background-color:blue; color:white; padding:8px 20px;
  font-size:16px; border:none; border-radius:6px;
  cursor:pointer; height:40px; transition:0.25s;
}
.search-advanced button:hover { background-color:darkblue; }
.themsp { margin: 20px 0 0 5%; }
td img { width:80px; }
</style>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<!--Navbar-->
<nav class="navbar">
  <div class="nav-left">
    <a href="admin.php" class="logo-link">
      <img src="assets/img/logo.png" alt="Logo" class="navbar-logo">
    </a>
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
    <a href="quanlysp.php"><div class="menu-card" style="background:linear-gradient(to bottom,#19113b,blue);"><h3>Quản lý sản phẩm</h3></div></a>
    <a href="lndm.php"><div class="menu-card"><h3>Quản lý giá bán</h3></div></a>
    <a href="quanlydm.php"><div class="menu-card"><h3>Quản lý danh mục</h3></div></a>
    <a href="tracuusoluong.php"><div class="menu-card"><h3>Quản lý tồn kho</h3></div></a>
    <a href="quanlynhaphang.php"><div class="menu-card"><h3>Quản lý nhập hàng</h3></div></a>
  </div>
</section>

<div class="themsp">
  <a href="themsp.php">
    <button class="bt-them"><i class="fa-solid fa-plus"></i>Thêm sản phẩm</button>
  </a>
</div>

<div class="search-box">
  <form action="quanlysp.php" method="get" class="search-advanced">
    <label style="font-size:25px;" for="fname">Tìm theo tên:</label>
    <input style="color:gray;" type="text" id="fname" name="fname" placeholder="Tên sản phẩm" value="<?php echo htmlspecialchars($fname); ?>">
    <label style="font-size:25px;" for="chat">Tìm theo dòng:</label>
    <select id="chat" name="chat">
      <option value="cl" <?php echo $grade=='cl'?'selected':''; ?>>Tất cả</option>
      <option value="hg" <?php echo $grade=='hg'?'selected':''; ?>>High Grade</option>
      <option value="rg" <?php echo $grade=='rg'?'selected':''; ?>>Real Grade</option>
      <option value="mg" <?php echo $grade=='mg'?'selected':''; ?>>Master Grade</option>
      <option value="pg" <?php echo $grade=='pg'?'selected':''; ?>>Perfect Grade</option>
      <option value="ag" <?php echo $grade=='ag'?'selected':''; ?>>Anime Figure</option>
    </select>
    <label style="font-size:25px;" for="hang">Tìm theo hãng:</label>
    <select id="hang" name="hang">
      <option value="hang" <?php echo $hang=='hang'?'selected':''; ?>>Tất cả</option>
      <option value="bandai" <?php echo $hang=='bandai'?'selected':''; ?>>Bandai</option>
      <option value="sega" <?php echo $hang=='sega'?'selected':''; ?>>Sega</option>
      <option value="banpresto" <?php echo $hang=='banpresto'?'selected':''; ?>>Banpresto</option>
    </select>
    <button type="submit" style="height:38px; margin-top:-4px;">Tìm</button>
    <a href="quanlysp.php"><button type="button" style="height:38px; margin-top:-4px; background:gray; color:white; border:none; border-radius:6px; padding:8px 20px; font-size:16px; cursor:pointer;">Đặt lại</button></a>
  </form>
</div>

<table>
  <tr>
    <th>STT</th>
    <th>Tên</th>
    <th>Hình ảnh</th>
    <th>Giá tiền</th>
    <th>Số lượng</th>
    <th>Thao tác</th>
  </tr>
  <?php
  $stt = $offset + 1;
  while($row = $result->fetch_assoc()):
  ?>
  <tr>
    <td><?php echo $stt++; ?></td>
    <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
    <td><img src="assets/img/<?php echo htmlspecialchars($row['Product_image']); ?>"></td>
    <td><?php echo number_format($row['Price'], 0, ',', '.'); ?> VNĐ</td>
    <td><?php echo $row['Quantity']; ?></td>
    <td class="thaotac">
      <a href="suasp.php?id=<?php echo urlencode($row['ProductID']); ?>">
        <button class="bt-them" style="background:yellow;color:black;">Sửa</button>
      </a>
      <a href="quanlysp.php?xoa=<?php echo urlencode($row['ProductID']); ?><?php echo $query_string ? '&'.ltrim($query_string,'&') : ''; ?>&page=<?php echo $page; ?>"
         onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?')">
        <button class="bt-them" style="background:red;color:white;">Xóa</button>
      </a>
    </td>
  </tr>
  <?php endwhile; ?>
</table>

<div class="pagination" style="margin-top:100px;margin-bottom:50px;">
  <a href="quanlysp.php?page=1<?php echo $query_string; ?>">&laquo;</a>
  <?php for($i = 1; $i <= $total_pages; $i++): ?>
    <a href="quanlysp.php?page=<?php echo $i.$query_string; ?>" <?php echo $i==$page?'class="active"':''; ?>>
      <?php echo $i; ?>
    </a>
  <?php endfor; ?>
  <a href="quanlysp.php?page=<?php echo $total_pages.$query_string; ?>">&raquo;</a>
</div>

</body>
</html>