<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? "user";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* TASK */
$task = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM tasks WHERE id='$id'
"));

if(!$task){
    die("ไม่พบงาน");
}

/* SEND */
if(isset($_POST['send'])){
    $caption = $_POST['caption'] ?? '';

    if(!empty($_FILES['image']['name'])){
        $img = time()."_".$_FILES['image']['name'];

        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$img);

        mysqli_query($conn,"
            INSERT INTO task_uploads(task_id,user_id,image,caption)
            VALUES('$id','$user_id','$img','$caption')
        ");
    }

    header("Location: task.php?id=$id");
    exit();
}

/* DELETE */
if(isset($_GET['delete'])){
    $delete_id = (int)$_GET['delete'];

    if($role=="admin"){
        $check = mysqli_query($conn,"SELECT * FROM task_uploads WHERE id='$delete_id'");
    }else{
        $check = mysqli_query($conn,"SELECT * FROM task_uploads WHERE id='$delete_id' AND user_id='$user_id'");
    }

    if(mysqli_num_rows($check)>0){
        $post = mysqli_fetch_assoc($check);

        if(file_exists("uploads/".$post['image'])){
            unlink("uploads/".$post['image']);
        }

        mysqli_query($conn,"DELETE FROM task_uploads WHERE id='$delete_id'");
    }

    header("Location: task.php?id=$id");
    exit();
}

/* SUBMISSIONS */
$subs = mysqli_query($conn,"
    SELECT tu.*, u.username, u.profile_image
    FROM task_uploads tu
    JOIN users u ON tu.user_id = u.id
    WHERE tu.task_id='$id'
    ORDER BY tu.id DESC
");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

<title>Task</title>

<style>
body{
    margin:0;
    font-family:'Inter','Kanit',sans-serif;
    background:#f5f6f8;
    color:#111;
}

/* wrapper */
.wrapper{
    max-width:780px;
    margin:0 auto;
    padding:28px 40px;
}

/* top */
.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.btn{
    padding:6px 12px;
    border:1px solid #111;
    border-radius:10px;
    background:#fff;
    text-decoration:none;
    color:#111;
}

/* box */
.box{
    background:#fff;
    border:1px solid #eee;
    padding:15px;
    border-radius:14px;
    margin-bottom:15px;
}

/* GRID */
#subs{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:12px;
}

.sub-item{
    background:#fff;
    border:1px solid #eee;
    border-radius:14px;
    padding:10px;
}

/* header */
.sub-header{
    display:flex;
    align-items:center;
    gap:8px;
    font-size:14px;
    margin-bottom:6px;
}

.sub-header img{
    width:32px;
    height:32px;
    border-radius:50%;
    object-fit:cover;
}

/* caption */
.caption{
    font-size:13px;
    color:#444;
    margin-bottom:8px;
}

/* thumbnail */
.thumb{
    width:100%;
    height:150px;
    object-fit:cover;
    border-radius:10px;
    cursor:pointer;
}

/* viewer */
.viewer{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.85);
    justify-content:center;
    align-items:center;
    z-index:9999;
}

.viewer img{
    max-width:85vw;
    max-height:85vh;
    object-fit:contain;
    border-radius:12px;
}

/* modal */
.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.4);
    justify-content:center;
    align-items:center;
}

.modal-content{
    background:#fff;
    padding:20px;
    width:90%;
    max-width:400px;
    border-radius:12px;
}

input, textarea{
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ddd;
    border-radius:10px;
}

/* FAB */
.fab{
    position:fixed;
    bottom:25px;
    left:50%;
    transform:translateX(-50%);
    width:55px;
    height:55px;
    border-radius:50%;
    border:1px solid #111;
    background:#fff;
    font-size:28px;
    cursor:pointer;
}

/* ===== MENU ===== */
.menu-wrap{
    margin-left:auto;
    position:relative;
}

.gear-btn{
    width:32px;
    height:32px;
    border-radius:50%;
    border:1px solid #111;
    background:#fff;
    cursor:pointer;
    font-size:14px;
}

.menu-box{
    display:none;
    position:absolute;
    right:0;
    top:40px;
    background:#fff;
    border:1px solid #ddd;
    border-radius:10px;
    overflow:hidden;
    min-width:120px;
    z-index:999;
}

.menu-box a{
    display:block;
    padding:10px;
    text-decoration:none;
    color:#111;
    border-bottom:1px solid #eee;
}

.menu-box a:hover{
    background:#111;
    color:#fff;
}

/* TITLE */
.task-title{
    font-size:22px;
    font-weight:700;
    color:#000;
}
</style>
</head>

<body>

<div class="wrapper">

<div class="top">
    <a class="btn" href="room.php?room=<?php echo $task['room']; ?>">⬅ กลับ</a>

    <div class="task-title">
        <?php echo $task['title']; ?>
    </div>

    <?php if($role=="admin"){ ?>
        <a class="btn" href="delete_task.php?id=<?php echo $id; ?>">ลบ</a>
    <?php } ?>
</div>

<div class="box">
    <?php echo $task['description']; ?>
</div>

<div class="box">
<h3>ผลงานนักเรียน</h3>

<div id="subs">

<?php while($s = mysqli_fetch_assoc($subs)){ ?>

<div class="sub-item">

    <div class="sub-header">
        <img src="uploads/<?php echo !empty($s['profile_image']) ? $s['profile_image'] : 'default.png'; ?>">
        <b><?php echo htmlspecialchars($s['username']); ?></b>

        <?php if($role=="admin" || $s['user_id']==$user_id){ ?>

        <div class="menu-wrap">
            <button class="gear-btn" onclick="toggleMenu(<?php echo $s['id']; ?>)">⚙</button>

            <div class="menu-box" id="menu-<?php echo $s['id']; ?>">
                <a href="edit_upload.php?id=<?php echo $s['id']; ?>">แก้ไข</a>
                <a href="?id=<?php echo $id; ?>&delete=<?php echo $s['id']; ?>" onclick="return confirm('ลบ?')">ลบ</a>
            </div>
        </div>

        <?php } ?>
    </div>

    <?php if(!empty($s['caption'])){ ?>
        <div class="caption"><?php echo htmlspecialchars($s['caption']); ?></div>
    <?php } ?>

    <img class="thumb" src="uploads/<?php echo $s['image']; ?>" onclick="openViewer(this.src)">

</div>

<?php } ?>

</div>
</div>

</div>

<!-- viewer -->
<div class="viewer" id="viewer" onclick="closeViewer()">
    <img id="viewerImg">
</div>

<!-- modal -->
<button class="fab" onclick="openModal()">+</button>

<div class="modal" id="modal">
<div class="modal-content">
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <textarea name="caption"></textarea>
    <button class="btn" name="send">ส่งงาน</button>
    <button type="button" class="btn" onclick="closeModal()">ปิด</button>
</form>
</div>
</div>

<script>
function openViewer(src){
    document.getElementById("viewer").style.display="flex";
    document.getElementById("viewerImg").src=src;
}
function closeViewer(){
    document.getElementById("viewer").style.display="none";
}
function openModal(){
    document.getElementById("modal").style.display="flex";
}
function closeModal(){
    document.getElementById("modal").style.display="none";
}

function toggleMenu(id){
    let menu = document.getElementById("menu-"+id);

    document.querySelectorAll(".menu-box").forEach(m=>{
        if(m.id !== "menu-"+id) m.style.display="none";
    });

    menu.style.display = (menu.style.display==="block") ? "none" : "block";
}

window.onclick = function(e){
    if(!e.target.matches('.gear-btn')){
        document.querySelectorAll(".menu-box").forEach(m=>{
            m.style.display="none";
        });
    }
}
</script>

</body>
</html>