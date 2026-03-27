<?php
session_start();
if(!isset($_SESSION['admin'])){ header("Location: adminlogin.php"); exit(); }

$conn = new mysqli("localhost","root","","hobbystore");
if($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
if(empty($id)){ header("Location: quanlysp.php"); exit; }

/* lưu thay đổi */
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name        = $conn->real_escape_string($_POST['ProductName']);
    $grade       = $conn->real_escape_string($_POST['Grade']);
    $producer    = $conn->real_escape_string($_POST['Producer']);
    $price       = (int)$_POST['Price'];
    $source      = $conn->real_escape_string($_POST['Product_source']);
    $desc        = $conn->real_escape_string($_POST['Product_description']);
    $detail      = $conn->real_escape_string($_POST['Product_detail']);
    $qty         = (int)$_POST['Quantity'];

    $image = $conn->real_escape_string($_POST['old_image']);
    if(!empty($_FILES['Product_image']['name'])){
        $ext      = pathinfo($_FILES['Product_image']['name'], PATHINFO_EXTENSION);
        $newname  = uniqid('sp_') . '.' . $ext;
        move_uploaded_file($_FILES['Product_image']['tmp_name'], "assets/img/" . $newname);
        $image = $newname;
    }

    $conn->query("UPDATE product_list SET
        ProductName='$name',
        Grade='$grade',
        Producer='$producer',
        Price=$price,
        Product_source='$source',
        Product_description='$desc',
        Product_detail='$detail',
        Product_image='$image',
        Quantity=$qty
        WHERE ProductID='$id'");

    header("Location: quanlysp.php");
    exit;
}

/* lấy dữ liệu sản phẩm */
$row = $conn->query("SELECT * FROM product_list WHERE ProductID='$id'")->fetch_assoc();
if(!$row){ header("Location: quanlysp.php"); exit; }

/* lấy danh mục từ DB */
$grades = [];
$grade_result = $conn->query("SELECT name FROM categories ORDER BY ID ASC");
while($g = $grade_result->fetch_assoc()){
    $grades[] = $g['name'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chỉnh sửa sản phẩm</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:"Josefin Sans",sans-serif; }
    body { background-color:#f8f9fa; }
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
    main { display:flex; justify-content:center; align-items:flex-start; padding:2rem 1rem; min-height:100vh; }
    .card { background:white; border-radius:16px; box-shadow:0 6px 25px rgba(0,0,0,0.1); padding:2rem; width:750px; max-width:100%; margin:1.5rem auto; }
    .card-header h4 { font-weight:600; font-size:1.4rem; color:#344767; margin:0 0 0.3rem 0; }
    .card-header p { color:#6c757d; font-size:0.875rem; margin:0 0 1.5rem 0; }
    .form-group { margin-bottom:1.25rem; }
    .form-label { display:block; font-weight:600; margin-bottom:0.5rem; color:#333; }
    .form-control { width:100%; padding:0.8rem 1rem; border-radius:10px; border:1px solid #ccc; font-size:1rem; box-sizing:border-box; transition:border-color 0.2s,box-shadow 0.2s; }
    .form-control:focus { border-color:#00b4ff; outline:none; box-shadow:0 0 0 2px rgba(0,180,255,0.2); }
    textarea.form-control { resize:vertical; }
    .btn { border:none; border-radius:10px; cursor:pointer; padding:0.8rem 1.6rem; font-size:1rem; transition:all 0.3s ease; display:inline-flex; align-items:center; gap:6px; }
    .btn-secondary { background-color:#6c757d; color:white; }
    .btn-secondary:hover { background-color:#5a6268; }
    .btn-gradient { background:linear-gradient(90deg,#00c6ff,#0072ff); color:white; font-weight:600; }
    .btn-gradient:hover { opacity:0.9; }
    .form-buttons { display:flex; justify-content:center; gap:1.5rem; margin-top:2.5rem; padding-top:2rem; border-top:1px solid #dee2e6; }
    .img-preview { width:150px; height:200px; object-fit:cover; border-radius:8px; border:1px solid #ccc; margin-top:10px; display:block; }
    .form-row { display:flex; gap:1rem; }
    .form-row .form-group { flex:1; }
  </style>
</head>
<body>

<?php include "navbar.php"; ?>

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

<main>
  <div style="width:750px; max-width:100%;">
    <nav style="margin-bottom:1rem; padding:0.75rem 1.5rem; border-radius:0.75rem; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1); background-color:white;">
      <b style="color:#e91e63;">Chỉnh sửa sản phẩm</b>
    </nav>

    <div class="card">
      <div class="card-header">
        <h4>Thông tin sản phẩm</h4>
        <p>Cập nhật các thông tin của sản phẩm — ID: <b><?php echo htmlspecialchars($id); ?></b></p>
      </div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($row['Product_image']); ?>">

          <div class="form-group">
            <label class="form-label">Tên sản phẩm <span style="color:#dc3545;">*</span></label>
            <input type="text" class="form-control" name="ProductName" value="<?php echo htmlspecialchars($row['ProductName']); ?>" required>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Dòng (Grade) <span style="color:#dc3545;">*</span></label>
              <select class="form-control" name="Grade" required>
                <?php foreach($grades as $g): ?>
                <option value="<?php echo htmlspecialchars($g); ?>" <?php echo $row['Grade']===$g ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($g); ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Hãng sản xuất <span style="color:#dc3545;">*</span></label>
              <select class="form-control" name="Producer" required>
                <?php
                $producers = ['Bandai','SEGA','Banpresto'];
                foreach($producers as $p):
                ?>
                <option value="<?php echo $p; ?>" <?php echo $row['Producer']===$p?'selected':''; ?>><?php echo $p; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Giá bán (VNĐ) <span style="color:#dc3545;">*</span></label>
              <input type="text" class="form-control" id="priceDisplay" 
                placeholder="Nhập giá..."
                value="<?php echo number_format($row['Price'], 0, ',', '.'); ?> VNĐ">
              <input type="hidden" name="Price" id="priceRaw" value="<?php echo $row['Price']; ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Số lượng <span style="color:#dc3545;">*</span></label>
              <input type="number" class="form-control" name="Quantity" value="<?php echo $row['Quantity']; ?>" min="0" required>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Nguồn gốc</label>
            <input type="text" class="form-control" name="Product_source" value="<?php echo htmlspecialchars($row['Product_source'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label class="form-label">Mô tả ngắn</label>
            <textarea class="form-control" name="Product_description" rows="3"><?php echo htmlspecialchars($row['Product_description'] ?? ''); ?></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Chi tiết sản phẩm</label>
            <textarea class="form-control" name="Product_detail" rows="3"><?php echo htmlspecialchars($row['Product_detail'] ?? ''); ?></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Hình ảnh <small style="color:#999;">(để trống nếu không đổi)</small></label>
            <img id="imgPreview" src="assets/img/<?php echo htmlspecialchars($row['Product_image']); ?>" class="img-preview" alt="Ảnh sản phẩm">
            <input type="file" class="form-control" name="Product_image" accept="image/*" style="margin-top:10px;" onchange="previewImg(this)">
          </div>

          <div class="form-buttons">
            <a href="quanlysp.php">
              <button type="button" class="btn btn-secondary">Quay về</button>
            </a>
            <button type="submit" class="btn btn-gradient">Lưu thay đổi</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<script>
function previewImg(input){
  if(input.files && input.files[0]){
    const reader = new FileReader();
    reader.onload = e => document.getElementById('imgPreview').src = e.target.result;
    reader.readAsDataURL(input.files[0]);
  }
}

const priceDisplay = document.getElementById('priceDisplay');
const priceRaw     = document.getElementById('priceRaw');

priceDisplay.addEventListener('input', function(){
  let raw = this.value.replace(/[^\d]/g, '');
  priceRaw.value = raw;
  if(raw.length > 0){
    this.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' VNĐ';
  } else {
    this.value = '';
  }
});

priceDisplay.addEventListener('focus', function(){
  let raw = priceRaw.value;
  this.value = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
});

priceDisplay.addEventListener('blur', function(){
  let raw = priceRaw.value;
  if(raw){
    this.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' VNĐ';
  }
});
</script>
</body>
</html>