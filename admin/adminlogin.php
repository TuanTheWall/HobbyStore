<?php
session_start();
include "connect.php";

if(isset($_POST['user']) && isset($_POST['pass'])){

    $user = $_POST['user'];
    $pass = $_POST['pass'];

    $sql = "SELECT * FROM admin WHERE adminname='$user' AND password='$pass'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) == 1){
        $_SESSION['admin'] = $user;
        header("Location: admin.php");
        exit();
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
<title>Đăng nhập</title>
<link rel="stylesheet" href="astyle.css">

</head>

<body>
<div style="border: #ffffff;width:400px;height:330px;margin: auto;background-color: #d81159;margin-top: 4cm;border-radius: 20px;">
<h2  style="color: rgb(255, 255, 255);text-align:center;font-family:Arial, Helvetica, sans-serif;font-size: 200%;"><br>ĐĂNG NHẬP QUẢN LÝ</h2>

<form method="POST">
    <div>

        <div style="text-align:center; font-family: sans-serif;color:#ffffff;font-size:150%">
    <label for="user" >TÊN QUẢN LÝ</label><br>
            </div>
        <div style="text-align: center;">
    <input type="text" id="user" name="user" placeholder="Tài khoản quản lý/tên quản lý" style="margin: auto;width:300px;height:25px;background-color: #8e9096;font-family: Arial, Helvetica, sans-serif;font-size:large;color:#ffffff"><br><br>
            </div>
        
        <div style="text-align:center; font-family: sans-serif;color:#ffffff;font-size:150%">
    <label for="pass">MẬT KHẨU</label><br>
            </div>
        <div style="text-align: center;">
    <input type="password" placeholder="Mật khẩu" style="margin: auto;width:300px;height:25px;background-color: #8e9096;font-family: Arial, Helvetica, sans-serif;font-size:large;color:#ffffff" id="pass" name="pass" ><br><br>
            </div><br>
    </div>
        <div style="text-align: center;">
  <input type="submit" style="width:310px;height:30px;font-family: Arial, Helvetica, sans-serif;font-size: larger;" value="Đăng nhập">
        </div><br>
</form> 
<?php
if(isset($error)){
    echo "<p style='color:red;text-align:center;font-size:larger;font-family:Arial'>$error</p>";
}
?>
    </div>
</body>
</html>