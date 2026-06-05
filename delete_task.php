<?php
session_start();
include "db.php";

if($_SESSION['role'] != "admin"){
    header("Location: home.php");
    exit();
}

$id = $_GET['id'];

/* ลบผลงานก่อน */
mysqli_query($conn,"
    DELETE FROM task_uploads WHERE task_id='$id'
");

/* ลบตัวงาน */
mysqli_query($conn,"
    DELETE FROM tasks WHERE id='$id'
");

header("Location: admin.php");
exit();
?>