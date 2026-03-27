<?php
$conn = new mysqli("localhost","root","","hobbystore");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id          = trim($_POST['product_id']);
    $name        = trim($_POST['fname']);
    $grade_map   = ['hg'=>'HG','rg'=>'RG','mg'=>'MG','pg'=>'PG','ag'=>'Figure'];
    $grade       = isset($grade_map[$_POST['chat']]) ? $grade_map[$_POST['chat']] : '';
    $hang_map    = ['bandai'=>'Bandai','sega'=>'SEGA','banpresto'=>'Banpresto'];
    $producer    = isset($hang_map[$_POST['hang']]) ? $hang_map[$_POST['hang']] : '';
    $price       = 0;
    $origin      = trim($_POST['origin']);
    $description = trim($_POST['description']);
    $info        = trim($_POST['info']);
    $image_name  = '';

    /* upload ảnh */
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
        $allowed = ['jpg','jpeg','png','webp','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)){
            $image_name = basename($_FILES['image']['name']);
            $target = "assets/img/" . $image_name;
            if(!move_uploaded_file($_FILES['image']['tmp_name'], $target)){
                $error = "Lỗi upload ảnh!";
            }
        } else {
            $error = "Định dạng ảnh không hợp lệ!";
        }
    }

    if(empty($error)){
        $id_safe    = $conn->real_escape_string($id);
        $name_safe  = $conn->real_escape_string($name);
        $grade_safe = $conn->real_escape_string($grade);
        $prod_safe  = $conn->real_escape_string($producer);
        $orig_safe  = $conn->real_escape_string($origin);
        $desc_safe  = $conn->real_escape_string($description);
        $info_safe  = $conn->real_escape_string($info);
        $img_safe   = $conn->real_escape_string($image_name);

        $sql = "INSERT INTO product_list (ProductID, ProductName, Grade, Producer, Product_source, Product_description, Product_detail, Product_image, Price, Profit, Quantity)
                VALUES ('$id_safe','$name_safe','$grade_safe','$prod_safe','$orig_safe','$desc_safe','$info_safe','$img_safe','$price',0.30,0)";

        if($conn->query($sql)){
            header("Location: quanlysp.php");
            exit;
        } else {
            $error = "Lỗi thêm sản phẩm: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thêm sản phẩm</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
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
.overview-menu { padding:30px; background-color:white; }
.overview-menu h2 { font-size:22px; color:black; margin-bottom:20px; }
.menu-grid { display:flex; flex-wrap:wrap; justify-content:center; gap:20px; }
.menu-card {
  background:linear-gradient(to bottom,cyan,blue); color:#fff;
  text-align:center; padding:30px 20px; border-radius:15px;
  box-shadow:0 4px 10px rgba(0,0,0,0.1); transition:all 0.25s ease; cursor:pointer;
}
.menu-card h3 { font-size:16px; font-weight:600; }
.menu-card:hover { transform:translateY(-5px); box-shadow:0 6px 18px rgba(0,0,0,0.15); }
.wrap {
  position:relative; z-index:1;
  background:rgba(255,255,255,0.95);
  padding:28px; border-radius:12px;
  max-width:1100px; margin:36px auto;
  box-shadow:0 14px 30px rgba(0,0,0,0.12);
}
.add-img { padding-bottom:50px; }
.left-po { display:flex; flex-direction:column; align-items:center; }
.add-info {
  margin-top:-500px; font-size:25px;
  padding-bottom:10px; margin-left:300px;
}
.add-info input, .add-info select {
  display:block; margin-bottom:1px;
  font-size:20px; width:600px !important; height:40px;
  border:1px solid #aaa; border-radius:6px;
}
textarea::placeholder { color:gray; font-style:italic; }
.add-info select, .add-info textarea {
  width:600px; font-size:20px;
  border:1px solid #aaa; border-radius:6px;
}
.bt-them {
  background:#04a4b3; color:white;
  font-size:18px; padding:10px 25px;
  border:none; border-radius:8px;
  cursor:pointer; transition:0.3s;
}
.bt-them i {
  margin-right:8px; font-size:20px;
  background:white; color:black;
  border-radius:4px; width:25px; height:25px;
  text-align:center; line-height:25px;
}
.themsp { display:inline-block; margin-right:100px; }
.khung {
  display:flex; justify-content:center;
  gap:20px; margin-top:20px;
}
.preview-img {
  width:300px; height:400px; object-fit:cover;
  border:2px dashed #aaa; border-radius:8px;
  display:block; cursor:pointer;
}
.error-msg { color:red; font-weight:bold; text-align:center; margin:10px 0; }
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

<div class="wrap">
  <div style="display:flex;gap:10px;align-items:center;margin-bottom:14px;">
    <h1>Thêm sản phẩm</h1>
  </div>

  <?php if($error): ?>
    <div class="error-msg"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">

    <div class="add-img">
      <input type="file" id="fileInput" name="image" accept="image/*" style="display:none;" onchange="previewImage(this)">
      <button type="button" style="display:flex;width:240px;justify-content:center;font-size:20px;background:#56e37c;color:black;" onclick="document.getElementById('fileInput').click()">Hình ảnh sản phẩm</button>
      <img id="imgPreview" class="preview-img" src="assets/img/folder-icon-in-line-style-design-isolated-on-white-background-editable-stroke-vector.jpg" onclick="document.getElementById('fileInput').click()">
    </div>

    <div class="add-info">
      <div class="left-po">

        <label for="product_id">Mã sản phẩm</label><br>
        <input style="color:gray;" type="text" id="product_id" name="product_id" placeholder="VD: HG-008" required><br>

        <label for="fname">Tên sản phẩm</label><br>
        <input style="color:gray;" type="text" id="fname" name="fname" placeholder="Tên sản phẩm" required><br>

        <label for="chat">Chọn dòng</label>
        <select id="chat" name="chat" style="font-size:20px;width:600px;">
          <option value="cl">[Chọn dòng]</option>
          <option value="hg">High Grade</option>
          <option value="rg">Real Grade</option>
          <option value="mg">Master Grade</option>
          <option value="pg">Perfect Grade</option>
          <option value="ag">Anime Figure</option>
        </select><br>

        <label for="hang">Chọn hãng</label>
        <select id="hang" name="hang" style="font-size:20px;width:600px;">
          <option value="hang">[Chọn hãng]</option>
          <option value="bandai">Bandai</option>
          <option value="sega">Sega</option>
          <option value="banpresto">Banpresto</option>
        </select><br>


        <h2 style="font-size:20px;color:#333;">Giới thiệu mô hình</h2>

        <label for="origin" style="font-weight:bold;">Nguồn gốc:</label><br>
        <textarea id="origin" name="origin" rows="2" cols="60"
          style="font-size:16px;color:black;padding:10px;"
          placeholder="Nhập nguồn gốc mô hình"></textarea><br><br>

        <label for="description" style="font-weight:bold;">Mô tả:</label><br>
        <textarea id="description" name="description" rows="6" cols="60"
          style="font-size:16px;color:black;padding:10px;"
          placeholder="Nhập mô tả chi tiết về mô hình"></textarea><br><br>

        <label for="info" style="font-weight:bold;">Thông tin mô hình:</label><br>
        <textarea id="info" name="info" rows="10" cols="50"
          style="font-size:16px;color:black;padding-top:10px;"
          placeholder="Nhập thông tin về cấp độ, chiều cao, tỉ lệ..."></textarea>

      </div>

      <div class="khung">
        <a href="quanlysp.php">
          <button type="button" class="bt-them" style="background:gray;color:white;">Quay về</button>
        </a>
        <button type="submit" class="bt-them" style="background:rgb(19,42,127);color:white;">Thêm sản phẩm</button>
      </div>
    </div>

  </form>
</div>

<script>
function previewImage(input) {
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){
            document.getElementById('imgPreview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>