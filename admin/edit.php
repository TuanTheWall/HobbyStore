<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: adminlogin.php");
    exit();
}

include "connect.php";

/* Lấy ID khách hàng */
$id = $_GET['customer_id'] ?? 0;

/* Lấy thông tin khách hàng */
$sql = "SELECT * FROM customers WHERE customer_id=$id";;
$result = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($result);

/* Khi submit form */
if(isset($_POST['update'])){

$id = $_POST['customer_id'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$status = $_POST['status'];

$update = "UPDATE customers SET
customer_id='$id',
username='$username',
email='$email',
password='$password',
phone='$phone',
address='$address',
status='$status'
WHERE customer_id='$id' ";

mysqli_query($conn,$update);

header("Location: customer.php");
exit();

}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Quản lý khách hàng</title>
  <link rel="stylesheet" href="assets/css/editstyle.css">
</head>

<body>
<?php include "navbar.php" ?>
<?php include "menucard.php"?>

<!-- Form cập nhật khách hàng -->
<main style="margin-left: -180px; min-height: 100vh; padding: 1rem;">
  <nav style="margin-bottom: 1 rem; padding: 0.75rem 1.5rem; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); background-color: white;">
    <div>
      <b style="color:#e91e63;">Cập nhật khách hàng</b>
    </div>
  </nav>

  <div style="padding-top: 1.5rem; padding-bottom: 1.5rem;">
    <div class="card">
      <div class="card-header">
        <h4 style="margin: 0; font-weight: 600; margin-bottom: 0.5rem; color: #344767;">Thông tin khách hàng</h4>
        <p style="margin: 0; color: #6c757d; font-size: 0.875rem;">Cập nhật các thông tin của khách hàng</p>
        <br>
      </div>
      <div class="card-body">
        <div class="form-container" style="max-width:600px;">
        <form method="post">
          <div class="form-group">
          <label>Mã tài khoản</label>
          <input type="text" class="form-control" name="customer_id"
          value="<?php echo $row['customer_id']; ?>">
          </div>

          <div class="form-group">
          <label>Tên tài khoản</label>
          <input type="text" class="form-control" name="username"
          value="<?php echo $row['username']; ?>">
          </div>

          <div class="form-group">
          <label>Email</label>
          <input type="email" class="form-control" name="email"
          value="<?php echo $row['email']; ?>">
          </div>

          <div class="form-group">
          <label>Mật khẩu</label>

          <div style="position:relative;">
          <input type="password" id="password" class="form-control" name="password"
          value="<?php echo $row['password']; ?>" style="padding-right:40px;">

          <span onclick="togglePassword()" 
          style="position:absolute; right:10px; top:50%; transform:translateY(-50%);
          cursor:pointer;">
          👁
          </span>

          </div>
          </div>

          <div class="form-group">
          <label>Số điện thoại</label>
          <input type="text" class="form-control" name="phone"
          value="<?php echo $row['phone']; ?>">
          </div>

          <div class="form-group">
          <label>Địa chỉ</label>
          <textarea class="form-control" name="address"><?php echo $row['address']; ?></textarea>
          </div>

          <div class="form-group">
          <label>Trạng thái</label>
          <select name="status" class="form-control">

          <option value="Hoạt động"
          <?php if($row['status']=="Hoạt động") echo "selected"; ?>>
          Hoạt động
          </option>

          <option value="Bị khóa"
          <?php if($row['status']=="Bị khóa") echo "selected"; ?>>
          Bị khóa
          </option>

          </select>
          </div>

          <div class="form-actions">
          <button type="submit" name="update" class="btn btn-gradient">
            Lưu thay đổi
          </button>
          </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
  // Hiện/ẩn mật khẩu
function togglePassword() {
  const pwdInput = document.getElementById("password");
  const eyeIcon = document.getElementById("eyeIcon");

  if (pwdInput.type === "password") {
    pwdInput.type = "text";
    eyeIcon.classList.remove("bi-eye-slash");
    eyeIcon.classList.add("bi-eye");
  } else {
    pwdInput.type = "password";
    eyeIcon.classList.remove("bi-eye");
    eyeIcon.classList.add("bi-eye-slash");
  }
}
</script>
</body>
</html>
