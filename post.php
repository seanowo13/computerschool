<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];

$sql = "SELECT * FROM posts WHERE id='$id'";
$result = mysqli_query($conn,$sql);
$post = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<title>Post</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="topbar">
    <a href="room.php?room=<?php echo $post['room']; ?>">⬅ กลับห้อง</a>
    <h2>ผลงาน</h2>
</div>

<div class="post-view">

    <img src="uploads/<?php echo $post['image']; ?>" class="full-img">

    <p class="caption"><?php echo $post['caption']; ?></p>

    <?php if($post['user_id'] == $user_id){ ?>

        <div class="owner-actions">
            <a href="edit_post.php?id=<?php echo $post['id']; ?>">✏️ แก้ไข</a>
            <a href="delete_post.php?id=<?php echo $post['id']; ?>" 
               onclick="return confirm('ลบโพสต์นี้?')">🗑 ลบ</a>
        </div>

    <?php } ?>

</div>

</body>
</html>