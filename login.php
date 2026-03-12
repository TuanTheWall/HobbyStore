<?php
session_start();
$error = "";

$conn = new mysqli("localhost","root","","hobbystore");
mysqli_set_charset($conn,"utf8mb4");

if($conn->connect_error){
    die("Lỗi kết nối CSDL");
}

if(isset($_POST['user']) && isset($_POST['pass'])){

    $username = trim($_POST['user']);
    $password = trim($_POST['pass']);

    $sql = "SELECT customer_id, status FROM customers 
        WHERE (email=? OR phone=?) AND password=?";

    $stmt = $conn->prepare($sql);

    if(!$stmt){
        die("SQL lỗi: ".$conn->error);
    }

    $stmt->bind_param("sss",$username,$username,$password);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows == 1){

    $row = $result->fetch_assoc();

    if($row['status'] == "Bị khóa"){
        $error = "Tài khoản của bạn đã bị khóa";
    }
    else{
        $_SESSION['user_id'] = $row['customer_id'];
        header("Location: index.php");
        exit();
    }
}
else{
    $error = "Sai tài khoản hoặc mật khẩu";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang chủ</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap"
    rel="stylesheet">
  <link href="https://cdn.reflowhq.com/v2/toolkit.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/settings.css">
  <link rel="stylesheet" href="assets/css/product-page.css">
  <link rel="stylesheet" href="assets/css/view-cart.css">
  <link rel="stylesheet" href="login.css">
</head>
<nav class="tren">

   <nav class="navbar">
  <div class="nav-left">
    <a href="index.php" class="logo">
      <img src="assets/img/logo.png" alt="Logo">
    </a>
    <a href="index.php" class="nav-item">Trang chủ</a>
  </div>
</nav>

</nav>
<body>
<div class="login-box">
    <h2>ĐĂNG NHẬP AOI SORA</h2>
    <?php if($error!=""){ ?>
      <p style="color:red"><?php echo $error; ?></p>
    <?php } ?>
    <form method="POST">
        <label for="user" style="font-size: larger;">EMAIL HOẶC SĐT</label><br>
        <input type="text" id="user" name="user" placeholder="Email / SĐT"><br>

        <label for="pass" style="font-size: larger;">MẬT KHẨU</label><br>
        <input type="password" id="pass" name="pass" placeholder="Mật khẩu"><br>

        <input type="submit" value="Đăng nhập">
        <hr>
    </form>

    <a href="signup.php">TẠO TÀI KHOẢN</a>
</div>

</body>
</html>
