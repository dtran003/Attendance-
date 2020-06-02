<?php
if (!isset($_COOKIE['Attendancerus_Session'])){
    header('Location: ./login.php');
    exit;
}
$teach = $_COOKIE['Attendancerus_Session'];

$code = $_POST['code'];
$course = $_GET['id'];
$class = $_GET['class'];
$name= $_GET['name'];
echo "Instructor: $teach<br>";
echo "New code: $code <br>";
echo $course;
require_once('./database.php');
$conn->query("UPDATE teachercourseattendance SET code='$code' WHERE course='$course' AND class='$class'");
$conn->close();
header("Location: ./course.php?id=$course&name=$name"); 
exit;

?>