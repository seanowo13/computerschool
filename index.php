<?php
session_start();
include "db.php";

if(isset($_SESSION['user_id'])){
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Computer School</title>

<style>
*{
    font-family: Arial, sans-serif;
    box-sizing: border-box;
}

body{
    margin:0;
    background:#fafafa;
}

.wrapper{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.box{
    width:90%;
    max-width:380px;
    background:#fff;
    padding:25px;
    border:1px solid #eee;
    border-radius:15px;
    text-align:center;
}

.toggle{
    display:flex;
    gap:10px;
    margin-bottom:20px;
}

.toggle button{
    flex:1;
    padding:12px;
    border:1px solid #ccc;
    background:#fff;
    cursor:pointer;
    border-radius:10px;
}

.toggle button.active{
    border:1px solid #111;
    font-weight:bold;
}

form{ display:none; }
form.active{ display:block; }

input{
    width:100%;
    padding:10px;
    margin:6px 0;
    border:1px solid #ddd;
    border-radius:10px;
}

button.submit{
    width:100%;
    padding:10px;
    margin-top:10px;
    border:1px solid #111;
    background:#111;
    color:#fff;
    border-radius:10px;
}
</style>
</head>

<body>

<div class="wrapper">
<div class="box">

<h2>COMPUTER SCHOOL</h2>

<div class="toggle">
    <button onclick="showLogin()" id="btnLogin" class="active">Login</button>
    <button onclick="showRegister()" id="btnRegister">Register</button>
</div>

<!-- LOGIN -->
<form method="POST" id="loginForm" class="active">
    <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
    <input type="password" name="password" placeholder="รหัสผ่าน" required>
    <button class="submit" name="login">เข้าสู่ระบบ</button>
</form>

<!-- REGISTER -->
<form method="POST" id="registerForm" enctype="multipart/form-data">
    <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
    <input type="password" name="password" placeholder="รหัสผ่าน" required>
    <input type="number" name="room" placeholder="ห้อง 1-7" required>
    <input type="file" name="profile_image">
    <button class="submit" name="register">สมัคร</button>
</form>

<?php
/* REGISTER */
if(isset($_POST['register'])){

    $u = $_POST['username'];
    $p = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $r = $_POST['room'];

    $img = null;

    if(!empty($_FILES['profile_image']['name'])){
        $img = time()."_".$_FILES['profile_image']['name'];
        move_uploaded_file($_FILES['profile_image']['tmp_name'], "uploads/".$img);
    }

    // 🔥 FIX: ใส่ role = user
    mysqli_query($conn,"
        INSERT INTO users(username,password,room,profile_image,role)
        VALUES('$u','$p','$r','$img','user')
    ");

    echo "<script>alert('สมัครสำเร็จ');</script>";
}

/* LOGIN (FIX สำคัญ) */
if(isset($_POST['login'])){

    $u = $_POST['username'];
    $p = $_POST['password'];

    $sql = mysqli_query($conn,"SELECT * FROM users WHERE username='$u'");
    $row = mysqli_fetch_assoc($sql);

    if($row && password_verify($p, $row['password'])){

       if(isset($_SESSION['user_id'])){

    if($_SESSION['role'] == "admin"){
        header("Location: admin.php");
    } else {
        header("Location: home.php");
    }

    exit();
}
$_SESSION['user_id'] = $row['id'];
$_SESSION['room'] = $row['room'];
$_SESSION['role'] = $row['role'];

if($row['role'] == "admin"){
    header("Location: admin.php");
} else {
    header("Location: home.php");
}

exit();

    } else {
        echo "<p>ชื่อหรือรหัสผ่านไม่ถูกต้อง</p>";
    }
}
?>

</div>
</div>

<script>
function showLogin(){
    document.getElementById("loginForm").classList.add("active");
    document.getElementById("registerForm").classList.remove("active");
}

function showRegister(){
    document.getElementById("registerForm").classList.add("active");
    document.getElementById("loginForm").classList.remove("active");
}
</script>

</body>
</html>