<?php
session_start();

$conn = new mysqli("localhost","root","","hobbystore");
mysqli_set_charset($conn,"utf8mb4");

if($conn->connect_error){
    die("Lỗi kết nối");
}

if(isset($_POST['user'])){

    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // kiểm tra username hoặc email đã tồn tại
    $check = $conn->prepare("SELECT customer_id FROM customers WHERE username=? OR email=?");
    $check->bind_param("ss",$user,$email);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows>0){
        echo "Username hoặc Email đã tồn tại";
    }
    else{

        // tạo mã khách hàng CM001
        $res = $conn->query("SELECT COUNT(*) as total FROM customers");
        $row = $res->fetch_assoc();
        $num = $row['total'] + 1;

        $customer_id = "CM".str_pad($num,2,"0",STR_PAD_LEFT);
        $sql = "INSERT INTO customers(customer_id,username,password,email,phone,address,register_date,status,created_at)
VALUES(?,?,?,?,?, ?,CURDATE(),'Hoạt động',NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss",$customer_id,$user,$pass,$email,$phone,$address);;
        $stmt->execute();

        $_SESSION['user_id'] = $customer_id;

        header("Location: index.php");
        exit();
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
  <link rel="stylesheet" href="sign.css">
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
<h2>TẠO TÀI KHOẢN</h2>
<form action="signup.php" method="POST"> 
  <label for="user" >TÀI KHOẢN</label><br>
  <input type="text" name="user" placeholder="Tạo tên đăng nhập"><br>
  <label for="pass">MẬT KHẨU</label><br>
  <input type="password" name="pass" placeholder="Mật khẩu"><br>
  <label for="pass">ĐỊA CHỈ GIAO HÀNG</label><br>
  <input type="text" name="address" placeholder="Địa chỉ giao hàng"><br>
  <label for="pass">EMAIL</label><br>
  <input type="email" name="email" placeholder="Địa chỉ email"><br>
  <label for="pass">SỐ ĐIỆN THOẠI</label><br>
  <input type="text" name="phone" placeholder="Số điện thoại"><br>
  <input type="submit" value="Tạo tài khoản">

</form> 
</div>

    </body>
<script 
  src="https://cdn.reflowhq.com/v2/toolkit.min.js"
  data-reflow-project="1590878356"
  data-testmode="true"
  defer
></script>
</body>
</html>
