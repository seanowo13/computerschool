<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$room = $_GET['room'] ?? 1;

/* TASKS */
$tasks = mysqli_query($conn,"
    SELECT * FROM tasks 
    WHERE room='$room'
    ORDER BY id DESC
");

/* POSTS */
$posts = mysqli_query($conn,"
    SELECT * FROM posts 
    WHERE room='$room'
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Room <?php echo $room; ?></title>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap');
*{
    font-family: 'Kanit', sans-serif;
}

body{
    margin:0;
    background:#fafafa;
    color:#111;
}

/* กันชิดขอบ + จัดกลาง */
.wrapper{
    max-width:760px;
    margin:0 auto;
    padding:0 2%;
}

/* TOPBAR */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 0;
    border-bottom:1px solid #eee;
}

/* BUTTON */
.btn{
    padding:6px 12px;
    border:1px solid #111;
    border-radius:8px;
    text-decoration:none;
    color:#111;
    font-size:14px;
}

.btn:hover{
    background:#111;
    color:#fff;
}

/* TASK */
.task-section{
    margin-top:20px;
}

.task{
    background:#fff;
    border:1px solid #eee;
    padding:15px;
    border-radius:12px;
    margin-bottom:10px;
}

/* POSTS GRID */
.grid-posts{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:5px;
    margin-top:20px;
}

.post-box{
    aspect-ratio:1/1;
    overflow:hidden;
    border-radius:10px;
    border:1px solid #eee;
    background:#fff;
}

.post-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* LINK */
a{
    color:#111;
    text-decoration:none;
}

</style>
</head>

<body>

<div class="wrapper">

<!-- TOP -->
<div class="topbar">
    <a class="btn" href="home.php">⬅ กลับ</a>
    <b>ป.4/<?php echo $room; ?></b>
    <div></div>
</div>

<!-- TASKS -->
<div class="task-section">
    <h3>งานประจำห้อง</h3>

    <?php while($t = mysqli_fetch_assoc($tasks)){ ?>

        <a href="task.php?id=<?php echo $t['id']; ?>">
            <div class="task">
                <b><?php echo $t['title']; ?></b><br><br>
                <?php echo $t['description']; ?>
            </div>
        </a>

    <?php } ?>
</div>

<!-- POSTS -->
<div class="grid-posts">

    <?php while($row = mysqli_fetch_assoc($posts)){ ?>

        <div class="post-box">
            <a href="post.php?id=<?php echo $row['id']; ?>">
                <img src="uploads/<?php echo $row['image']; ?>">
            </a>
        </div>

    <?php } ?>

</div>

</div>

</body>
</html>