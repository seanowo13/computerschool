<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* SAVE PROFILE */
if(isset($_POST['save_profile'])){

    $username = $_POST['username'];
    $bio = $_POST['bio'];

    $user_old = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT profile_image FROM users WHERE id='$user_id'
    "));

    $img = $user_old['profile_image'];

    if(!empty($_FILES['profile_image']['name'])){

        $filename = time()."_".basename($_FILES['profile_image']['name']);
        $folder = __DIR__ . "/uploads/";
        $target = $folder . $filename;

        if(!file_exists($folder)){
            mkdir($folder,0777,true);
        }

        if(move_uploaded_file($_FILES['profile_image']['tmp_name'],$target)){
            $img = $filename;
        }
    }

    mysqli_query($conn,"
        UPDATE users SET
        username='$username',
        bio='$bio',
        profile_image='$img'
        WHERE id='$user_id'
    ");

    header("Location: profile.php");
    exit();
}

/* USER */
$user = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM users
    WHERE id='$user_id'
"));

/* POSTS */
$post_result = mysqli_query($conn,"
    SELECT task_uploads.*, tasks.title AS task_title
    FROM task_uploads
    LEFT JOIN tasks ON task_uploads.task_id = tasks.id
    WHERE task_uploads.user_id='$user_id'
    ORDER BY task_uploads.id DESC
");

if(!$post_result){
    die(mysqli_error($conn));
}

$post_count = mysqli_num_rows($post_result);
?>

<!DOCTYPE html>
<html lang="th">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">

<title>Profile</title>

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

.topbar{
    width:100%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:25px 0;
    border-bottom:1px solid #eee;
}

.topbar .logo{
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

/* ================= CONTAINER ================= */
.container{
    max-width:900px;
    margin:30px auto;
    padding:0 5%;
}

/* ================= PROFILE CARD ================= */
.card{
    background:#fff;
    border:1px solid #eee;
    border-radius:16px;
    padding:20px;
    position:relative;
}

.gear{
    position:absolute;
    top:10px;
    right:10px;
    border:1px solid #ddd;
    padding:4px 8px;
    border-radius:8px;
    cursor:pointer;
    background:#fff;
}

/* ================= PROFILE ================= */
.top{
    display:flex;
    gap:15px;
    align-items:center;
}

.avatar{
    width:80px;
    height:80px;
    border-radius:50%;
    overflow:hidden;
    background:#eee;
}

.avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.username{
    font-size:18px;
    font-weight:600;
}

.bio{
    font-size:13px;
    color:#777;
}

.stats{
    margin-top:8px;
    font-size:13px;
}

/* ================= GRID ================= */
.grid{
    margin-top:20px;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:5px;
}

.post-wrap{
    position:relative;
}

.post-wrap img{
    width:100%;
    height:200px;
    object-fit:cover;
    border-radius:10px;
    cursor:pointer;
}

.post-menu{
    position:absolute;
    top:8px;
    right:8px;
}

.gear-post{
    width:32px;
    height:32px;
    border-radius:50%;
    border:1px solid #111;
    background:#fff;
    cursor:pointer;
}

.menu-box{
    display:none;
    position:absolute;
    top:38px;
    right:0;
    background:#fff;
    border:1px solid #111;
    border-radius:10px;
    overflow:hidden;
    min-width:110px;
    z-index:99;
}

.menu-box a{
    display:block;
    padding:10px;
    text-decoration:none;
    color:#111;
    background:#fff;
    font-size:14px;
}

.menu-box a:hover{
    background:#111;
    color:#fff;
}

/* ================= MODAL ================= */
.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.4);
    justify-content:center;
    align-items:center;
    z-index:999;
}

.box{
    background:#fff;
    padding:20px;
    border-radius:12px;
    width:90%;
    max-width:400px;
}

.box input,
.box textarea{
    width:100%;
    padding:10px;
    margin-top:10px;
    border:1px solid #ddd;
    border-radius:10px;
}

.box button{
    width:100%;
    margin-top:10px;
    padding:10px;
    border:1px solid #111;
    background:#fff;
    border-radius:10px;
    cursor:pointer;
}

.box button:hover{
    background:#111;
    color:#fff;
}

/* ================= POPUP IMAGE ================= */
.popup{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:9999;
    padding:20px;
}

.popup-box{
    background:#fff;
    padding:10px;
    border-radius:16px;
    max-width:500px;
    width:90%;
    animation:pop .2s ease;
}

.popup-box img{
    width:100%;
    max-height:70vh;
    object-fit:contain;
    border-radius:12px;
}

.popup-info{
    padding:10px 5px 5px;
}

.popup-caption{
    font-size:14px;
    color:#444;
    line-height:1.5;
    word-break:break-word;
}

@keyframes pop{
    from{
        transform:scale(.9);
        opacity:0;
    }
    to{
        transform:scale(1);
        opacity:1;
    }
}

</style>

</head>

<body>

<!-- POPUP -->
<div class="popup" id="popup" onclick="closePopup()">

    <div class="popup-box" onclick="event.stopPropagation()">

        <img id="popupImg">

        <div class="popup-info">
            

            <div class="popup-caption" id="popupCaption"></div>

        </div>

    </div>

</div>

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

<!-- CONTENT -->
<div class="container">

    <div class="card">

        <div class="gear" onclick="openEdit()">⚙</div>

        <div class="top">

            <div class="avatar">

                <?php if(!empty($user['profile_image']) && file_exists("uploads/".$user['profile_image'])){ ?>

                    <img src="uploads/<?php echo $user['profile_image']; ?>">

                <?php } else { ?>

                    <img src="default.png">

                <?php } ?>

            </div>

            <div>

                <div class="username">
                    <?php echo $user['username']; ?>
                </div>

                <div class="bio">
                    <?php echo $user['bio']; ?>
                </div>

                <div class="stats">
                    📌 Posts: <?php echo $post_count; ?><br>
                    🏫 ห้อง: ป.4/<?php echo $user['room']; ?>
                </div>

            </div>

        </div>

    </div>

    <!-- POSTS -->
    <div class="grid">

    <?php while($row = mysqli_fetch_assoc($post_result)){ ?>

        <div class="post-wrap">
<div class="task-title">
    <?php echo $row['task_title']; ?>
</div>
            <img src="uploads/<?php echo $row['image']; ?>"
                 onclick="openPopup(
                    'uploads/<?php echo $row['image']; ?>',
                    `<?php echo htmlspecialchars($row['caption']); ?>`
                 )">

            <div class="post-menu">

                <button class="gear-post"
                        onclick="event.stopPropagation(); toggleMenu(<?php echo $row['id']; ?>)">
                    ⚙
                </button>

                <div class="menu-box"
                     id="menu-<?php echo $row['id']; ?>">

                    <a href="edit_upload.php?id=<?php echo $row['id']; ?>&from=profile">
                        แก้ไข
                    </a>

                    <a href="task.php?id=<?php echo $row['task_id']; ?>&delete=<?php echo $row['id']; ?>"
                       onclick="return confirm('ลบผลงาน?')">
                        ลบ
                    </a>

                </div>

            </div>

        </div>

    <?php } ?>

    </div>

</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">

    <div class="box">

        <form method="POST" enctype="multipart/form-data">

            <input type="text"
                   name="username"
                   value="<?php echo $user['username']; ?>">

            <textarea name="bio"><?php echo $user['bio']; ?></textarea>

            <input type="file" name="profile_image">

            <button name="save_profile">
                Save
            </button>

        </form>

    </div>

</div>

<script>

function openEdit(){
    document.getElementById("editModal").style.display = "flex";
}

window.onclick = function(e){

    if(e.target.id === "editModal"){
        document.getElementById("editModal").style.display = "none";
    }

    if(!e.target.matches('.gear-post')){

        document.querySelectorAll(".menu-box").forEach(box => {
            box.style.display = "none";
        });

    }
}

function toggleMenu(id){

    let menu = document.getElementById("menu-" + id);

    document.querySelectorAll(".menu-box").forEach(box => {

        if(box.id != "menu-" + id){
            box.style.display = "none";
        }

    });

    if(menu.style.display == "block"){
        menu.style.display = "none";
    }else{
        menu.style.display = "block";
    }
}

function openPopup(src, caption){

    document.getElementById("popup").style.display = "flex";

    document.getElementById("popupImg").src = src;

    document.getElementById("popupCaption").innerHTML = caption;
}

function closePopup(){

    document.getElementById("popup").style.display = "none";
}

</script>

</body>
</html>