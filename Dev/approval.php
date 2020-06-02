<?php
$name = urlencode($_GET['name']);
$notes = $_POST['notes'];
$uname = $_GET['student'];
$cid = $_GET['course'];
$class = $_GET['class'];
echo "$notes $uname $cid $class";
require_once('./database.php');
$res = $conn->query("SELECT username FROM studentcourseattendance WHERE username='$uname' AND course='$cid' AND class='$class'");
if ($res->num_rows===0){
    if (!$conn->query("INSERT INTO studentcourseattendance (username, course, class, notes) VALUES ('$uname','$cid', '$class','$notes')")){
        echo "<p>Failed: ".$conn->error."</p>";
        header ("Refresh:5; url=./class.php?name=$name&course=$cid&class=$class");
        exit;
    }
    else {
        echo "<p>Success</p>";
    }
}
else if (!$conn->query("UPDATE studentcourseattendance SET notes='$notes' WHERE username='$uname' AND course='$cid' AND class='$class'")){
    echo "<p>Failed: ".$conn->error."</p>";
    header ("Refresh:5; url=./class.php?name=$name&course=$cid&class=$class");
}
else {
    echo "<p>Success</p>";
}

$conn->close();
header ("Location: ./class.php?name=$name&course=$cid&class=$class");
exit;
?>