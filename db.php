<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "computer_school";

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("เชื่อมต่อฐานข้อมูลไม่ได้: " . mysqli_connect_error());
}
?>