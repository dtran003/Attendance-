<?php
date_default_timezone_set('America/New_York');
require_once('database.php');
$username=$_GET['username'];
$name=$_GET['name'];
$cid=$_GET['course'];
$class=$_POST['class'];
$classDate=$_POST['classDate'];

if (!$conn->query("INSERT INTO teachercourseattendance (username,course,class,classDate) VALUES ('$username','$cid','$class','$classDate')")){
    echo "<p>Failed to insert $conn->error</p>";
}
else {
    echo "success";
    $conn->close();
    header("Location: ./course.php?name=$name&id=$cid");
}
?>