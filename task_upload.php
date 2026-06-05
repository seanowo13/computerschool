<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = (int)$_GET['id'];

/* DELETE POST */
if(isset($_GET['delete'])){

    $delete_id = (int)$_GET['delete'];

    // เช็กว่าเป็นโพสต์ของตัวเองไหม
    $check = mysqli_query($conn,"
        SELECT * FROM posts
        WHERE id='$delete_id'
        AND user_id='$user_id'
    ");

    if(mysqli_num_rows($check) > 0){

        $post = mysqli_fetch_assoc($check);

        // ลบรูป
        if(file_exists("uploads/".$post['image'])){
            unlink("uploads/".$post['image']);
        }

        // ลบโพสต์
        mysqli_query($conn,"
            DELETE FROM posts
            WHERE id='$delete_id'
        ");
    }

    header("Location: upload.php?id=".$task_id);
    exit();
}

/* UPLOAD */
if(isset($_POST['upload'])){

    $caption = $_POST['caption'];

    $image = time()."_".$_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp,"uploads/".$image);

    mysqli_query($conn,"
        INSERT INTO posts(user_id, task_id, image, caption)
        VALUES('$user_id','$task_id','$image','$caption')
    ");

    header("Location: upload.php?id=".$task_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ส่งงาน</title>

<style>
*{
    font-family:Arial,sans-serif;
    box-sizing:border-box;
}

body{
    margin:0;
    background:#fafafa;
    padding:20px;
}

.container{
    max-width:600px;
    margin:auto;
}

.box{
    background:#fff;
    border:1px solid #eee;
    border-radius:15px;
    padding:20px;
    margin-bottom:20px;
}

input, textarea{
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ddd;
    border-radius:10px;
}

button{
    padding:10px 15px;
    border:none;
    background:#111;
    color:#fff;
    border-radius:10px;
    cursor:pointer;
}

img{
    width:100%;
    border-radius:10px;
    margin-top:10px;
}

a.delete{
    display:inline-block;
    margin-top:10px;
    color:red;
    text-decoration:none;
}
</style>
</head>

<body>

<div class="container">

<div class="box">

<h2>ส่งงาน</h2>

<form method="POST" enctype="multipart/form-data">

    <input type="file" name="image" required>

    <textarea name="caption" placeholder="คำอธิบาย"></textarea>

    <button name="upload">ส่งงาน</button>

</form>

</div>

<div class="box">

<h3>ผลงานของฉัน</h3>

<?php

$my_posts = mysqli_query($conn,"
    SELECT * FROM posts
    WHERE user_id='$user_id'
    AND task_id='$task_id'
    ORDER BY id DESC
");

if(mysqli_num_rows($my_posts) == 0){
    echo "ยังไม่มีผลงาน";
}

while($p = mysqli_fetch_assoc($my_posts)){
?>

<div style="margin-bottom:25px;">

    <img src="uploads/<?php echo $p['image']; ?>">

    <p><?php echo $p['caption']; ?></p>

    <a class="delete"
       href="?id=<?php echo $task_id; ?>&delete=<?php echo $p['id']; ?>"
       onclick="return confirm('ลบผลงานนี้?')">
       ลบผลงาน
    </a>

</div>

<?php } ?>

</div>

</div>

</body>
</html>