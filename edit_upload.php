<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location:index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? "user";

$id = (int)($_GET['id'] ?? 0);

$from = $_GET['from'] ?? '';
$task_id = (int)($_GET['task'] ?? 0);

$upload = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM task_uploads
    WHERE id='$id'
"));

if(!$upload){
    die("ไม่พบโพสต์");
}

/* owner/admin only */
if($role != "admin" && $upload['user_id'] != $user_id){
    die("ไม่มีสิทธิ์");
}

/* update */
if(isset($_POST['save'])){

    $caption = $_POST['caption'];

    mysqli_query($conn,"
        UPDATE task_uploads
        SET caption='$caption'
        WHERE id='$id'
    ");

    if($from == "profile"){

        header("Location: profile.php");

    }else{

        header("Location: task.php?id=".$task_id);

    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit</title>

<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">

<style>
*{
    font-family:'Kanit',sans-serif;
    box-sizing:border-box;
}

body{
    margin:0;
    background:#fafafa;
    color:#111;
}

.wrapper{
    max-width:500px;
    margin:50px auto;
    padding:0 5%;
}

.box{
    background:#fff;
    border:1px solid #eee;
    border-radius:15px;
    padding:20px;
}

img{
    width:100%;
    border-radius:12px;
    margin-bottom:15px;
}

textarea{
    width:100%;
    min-height:120px;
    padding:10px;
    border:1px solid #ddd;
    border-radius:10px;
}

.btn{
    margin-top:10px;
    padding:10px 15px;
    border:1px solid #111;
    background:#fff;
    color:#111;
    border-radius:10px;
    cursor:pointer;
    text-decoration:none;
    display:inline-block;
}

.btn:hover{
    background:#111;
    color:#fff;
}
</style>
</head>

<body>

<div class="wrapper">

<div class="box">

    <img src="uploads/<?php echo $upload['image']; ?>">

    <form method="POST">

        <textarea name="caption"><?php echo htmlspecialchars($upload['caption']); ?></textarea>

        <br>

        <button class="btn" name="save">
            บันทึก
        </button>

        <a class="btn"
        <?php if($from == "profile"){ ?>

            href="profile.php"

        <?php }else{ ?>

            href="task.php?id=<?php echo $task_id; ?>"

        <?php } ?>
        >
           กลับ
        </a>

    </form>

</div>

</div>

</body>
</html>