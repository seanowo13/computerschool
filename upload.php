<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$room = $_GET['room'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload</title>

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

/* ================= NAVBAR SAME ================= */
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

.topbar a{
    text-decoration:none;
    color:#111;
    font-weight:500;
}

/* ================= CONTENT ================= */
.container{
    max-width:900px;
    margin:30px auto;
    padding:0 5%;
}

/* ================= UPLOAD BOX ================= */
.upload-box{
    background:#fff;
    border:1px solid #eee;
    border-radius:16px;

    padding:20px;
}

.upload-box textarea{
    width:100%;
    height:120px;
    padding:10px;

    border:1px solid #ddd;
    border-radius:10px;

    margin-top:10px;
}

.upload-box input[type="file"]{
    margin-top:10px;
}

.upload-box button{
    margin-top:15px;
    width:100%;

    padding:10px;
    border-radius:10px;

    border:1px solid #111;
    background:#fff;
    cursor:pointer;
}

.upload-box button:hover{
    background:#111;
    color:#fff;
}

</style>

</head>

<body>

<div class="wrapper">

    <div class="topbar">

        <a href="room.php?room=<?php echo $room; ?>">⬅ Back</a>

        <div style="font-weight:700;">UPLOAD</div>

        <div></div>

    </div>

</div>

<div class="container">

    <div class="upload-box">

        <form method="POST" enctype="multipart/form-data">

            <input type="file" name="image" required>

            <textarea name="caption" placeholder="คำอธิบายผลงาน..." required></textarea>

            <button type="submit" name="upload">อัปโหลด</button>

        </form>

        <?php
        if(isset($_POST['upload'])){

            $caption = $_POST['caption'];

            $image = $_FILES['image']['name'];
            $tmp = $_FILES['image']['tmp_name'];

            $folder = "uploads/" . $image;

            if(move_uploaded_file($tmp, $folder)){

                $sql = "INSERT INTO posts (user_id, room, image, caption)
                        VALUES ('$user_id', '$room', '$image', '$caption')";

                mysqli_query($conn,$sql);

                echo "<p>อัปโหลดสำเร็จ</p>";
                echo "<script>window.location='room.php?room=$room';</script>";
            } else {
                echo "<p>อัปโหลดไม่สำเร็จ</p>";
            }
        }
        ?>

    </div>

</div>

</body>
</html>