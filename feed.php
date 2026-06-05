<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

include "db.php";

/* ล่าสุดจากนักเรียนทุกคน */
$feed = mysqli_query($conn,"
    SELECT tu.*, u.username, u.profile_image
    FROM task_uploads tu
    JOIN users u ON tu.user_id = u.id
    ORDER BY tu.id DESC
");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feed</title>

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

/* ================= NAVBAR ================= */
.wrapper{
    max-width:900px;
    margin:0 auto;
    padding:0 5%;
}

/* ================= CONTENT ================= */
.container{
    max-width:760px;
    margin:20px auto;
    padding:0 2%;
}

.card{
    background:#fff;
    border:1px solid #eee;
    border-radius:14px;
    padding:14px;
    margin-bottom:14px;
}

.feed-img{
    width:100%;
    border-radius:10px;
    border:1px solid #eee;
    margin-top:8px;
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

.feed-top{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:15px;
}

.feed-top img{
    width:45px;
    height:45px;
    border-radius:50%;
    object-fit:cover;
}


.caption{
    margin-bottom:10px;
}

</style>

</head>

<body>

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

<div class="container">

<?php if(mysqli_num_rows($feed) == 0){ ?>

    <div class="card">
        <h3>Feed</h3>
        <p>ยังไม่มีผลงานจากนักเรียน</p>
    </div>

<?php } ?>

<?php while($post = mysqli_fetch_assoc($feed)){ ?>

    <div class="card">

        <div class="feed-top">

            <img 
            src="uploads/<?php echo !empty($post['profile_image']) ? $post['profile_image'] : 'default.png'; ?>">

            <div>
                <b><?php echo htmlspecialchars($post['username']); ?></b>
            </div>

        </div>

        <?php if(!empty($post['caption'])){ ?>

            <div class="caption">
                <?php echo htmlspecialchars($post['caption']); ?>
            </div>

        <?php } ?>

        <img 
        class="feed-img"
        src="uploads/<?php echo $post['image']; ?>">

    </div>

<?php } ?>

</div>

</body>
</html>