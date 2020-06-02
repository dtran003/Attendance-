<?php
require_once('./database.php');
$name=$_GET['name'];
$class= $_GET['class'];
$code = $_POST['code'];
$username = $_GET['username'];
$cid = $_GET['course'];
$res = $conn->query("SELECT code FROM teachercourseattendance WHERE course='$cid' AND class='$class'");
$row = $res->fetch_assoc();
$realcode = $row['code'];
if ($code==$realcode){
    $conn->query("INSERT INTO studentcourseattendance (username, course, class) VALUES ('$username','$cid','$class')");
    header ("Location: ./course.php?id=$cid&name=$name");
    exit;
} else{
    echo 'Wrong code';
}
$conn->close();


?>