<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

if(isset($_SESSION['role']) && $_SESSION['role'] == "admin"){
    header("Location: admin.php");
    exit();
}
$room = $_SESSION['room'] ?? '';
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home - Computer School</title>

<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">

<style>

/* ================= GLOBAL ================= */
*{
    font-family:'Kanit', sans-serif !important;
    box-sizing:border-box;
}

body{
    margin:0;
    background:#fafafa;
    color:#111;
}

/* ================= NAVBAR (SAME AS PROFILE) ================= */
.wrapper{
    max-width:900px;
    margin:0 auto;
    padding:0 5%;
}

.topbar{
    width:100%;
    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:25px 0;
    border-bottom:1px solid #eee;
}

.logo{
    font-weight:800;
    font-size:20px;
}

.menu{
    display:flex;
    gap:20px;
}

.menu a{
    text-decoration:none;
    color:#111;
    font-weight:500;
    padding:6px 10px;
    border-radius:8px;
}

.menu a:hover{
    background:#f2f2f2;
}

/* ================= CONTENT ================= */
.content{
    max-width:900px;
    margin:30px auto;
    padding:0 5%;
}

.room-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:15px;
    margin-top:20px;
}

.room-grid a{
    padding:25px;
    border:1px solid #ddd;
    border-radius:12px;
    text-align:center;
    text-decoration:none;
    color:#111;
    transition:0.2s;
}

.room-grid a:hover{
    background:#111;
    color:#fff;
    transform:scale(1.03);
}
.banner-small{
    max-width:900px;
    margin:15px auto;
    padding:0 5%;
}

.banner-small img{
    width:100%;
    height:auto;
    display:block;

    border-radius:12px;
    border:1px solid #ddd;   /* 👈 ขอบ */
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
}
</style>

</head>

<body>

<!-- NAVBAR -->
<div class="wrapper">
    <div class="topbar">

        <div class="logo">COMPUTER SCHOOL</div>

        <div class="menu">
            <a href="home.php">Home</a>
            <a href="feed.php">Feed</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>

    </div>
</div>

<!-- BANNER SMALL -->
<div class="banner-small">
    <img src="uploads/Learn.png" alt="banner">
    </div>
<!-- CONTENT -->
<div class="content">

    <h3>เลือกห้องของคุณ</h3>

    <p style="color:#666;">
        ห้องของคุณ: ป.4/<?php echo $room; ?>
    </p>

    <div class="room-grid">
        <a href="room.php?room=1">ป.4/1</a>
        <a href="room.php?room=2">ป.4/2</a>
        <a href="room.php?room=3">ป.4/3</a>
        <a href="room.php?room=4">ป.4/4</a>
        <a href="room.php?room=5">ป.4/5</a>
        <a href="room.php?room=6">ป.4/6</a>
        <a href="room.php?room=7">ป.4/7</a>
    </div>

</div>

</body>
</html>