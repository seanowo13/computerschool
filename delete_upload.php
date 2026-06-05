<?php
session_start();
include "db.php";

if($_SESSION['role'] != "admin"){
    header("Location: home.php");
    exit();
}

$id = $_GET['id'];

mysqli_query($conn,"
    DELETE FROM task_uploads WHERE id='$id'
");

header("Location: ".$_SERVER['HTTP_REFERER']);
exit();
?>