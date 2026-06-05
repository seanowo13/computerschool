<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php";

/* CHECK ADMIN */
if(
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== "admin"
){
    header("Location: home.php");
    exit();
}

/* เลือกห้อง */
$room = isset($_GET['room']) ? (int)$_GET['room'] : 1;

/* ADD TASK */
if(isset($_POST['add_task'])){

    $title = $_POST['title'];
    $desc  = $_POST['description'];
    $task_room = (int)$_POST['room'];

    mysqli_query($conn,"
        INSERT INTO tasks(title, description, room)
        VALUES('$title','$desc','$task_room')
    ");
}

/* DELETE TASK */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];
    mysqli_query($conn,"DELETE FROM tasks WHERE id='$id'");
}

/* GET TASKS */
$tasks = mysqli_query($conn,"
    SELECT * FROM tasks
    WHERE room='$room'
    ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<style>
*{
    font-family: Arial, sans-serif;
    box-sizing:border-box;
}

body{
    margin:0;
    background:#fafafa;
    color:#111;
}

/* ✅ เพิ่ม padding ไม่ให้ชิดขอบ */
.container{
    max-width:900px;
    margin:40px auto;
    padding:20px;
}

/* ✅ header แยกหัวข้อกับ logout */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.box, .task{
    background:#fff;
    border:1px solid #eee;
    border-radius:16px;
    padding:15px;
}

input, textarea, select{
    width:100%;
    padding:10px;
    margin:6px 0;
    border:1px solid #ddd;
    border-radius:12px;
}

button{
    padding:10px 15px;
    border:1px solid #111;
    background:#111;
    color:#fff;
    border-radius:12px;
    cursor:pointer;
}

button:hover{
    background:#fff;
    color:#111;
}

.task{
    margin-top:12px;
}

a{
    color:#111;
    text-decoration:none;
    border:1px solid #ddd;
    padding:6px 10px;
    border-radius:10px;
    margin-right:5px;
    display:inline-block;
}

a:hover{
    background:#111;
    color:#fff;
}
</style>
</head>

<body>

<div class="container">

<!-- ✅ HEADER (แก้ตรงนี้) -->
<div class="header">
    <h2>Admin Dashboard - Tasks (ห้อง <?php echo $room; ?>)</h2>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<!-- เลือกห้อง -->
<div class="box">
    <b>เลือกห้อง</b><br><br>
    <?php for($i=1;$i<=7;$i++){ ?>
        <a href="?room=<?php echo $i; ?>">ป.4/<?php echo $i; ?></a>
    <?php } ?>
</div>

<br>

<!-- ADD TASK -->
<div class="box">

<form method="POST">

    <input name="title" placeholder="ชื่องาน" required>

    <select name="room" required>
        <option value="">เลือกห้อง</option>
        <?php for($i=1;$i<=7;$i++){ ?>
            <option value="<?php echo $i; ?>">ป.4/<?php echo $i; ?></option>
        <?php } ?>
    </select>

    <textarea name="description" placeholder="รายละเอียด" required></textarea>

    <button name="add_task">+ เพิ่มงาน</button>

</form>

</div>

<hr>

<!-- TASK LIST -->
<?php while($t = mysqli_fetch_assoc($tasks)){ ?>

<div class="task">

    <b><?php echo $t['title']; ?></b><br>
    <small>ห้อง ป.4/<?php echo $t['room']; ?></small><br><br>

    <?php echo $t['description']; ?>

    <br><br>

    <a href="task.php?id=<?php echo $t['id']; ?>">ดูงาน</a>
    <a href="?delete=<?php echo $t['id']; ?>&room=<?php echo $room; ?>" 
       onclick="return confirm('ลบงานนี้?')">ลบ</a>

</div>

<?php } ?>

</div>

</body>
</html>