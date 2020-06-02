<?php
date_default_timezone_set('America/New_York');
require_once('database.php');
$latitude=0;
if ($_POST['latitude']){
    $latitude=$_POST['latitude'];
}
$longitude=0;
if ($_POST['longitude']){
    $longitude=$_POST['longitude'];
}
$distance = -1;
if ($_POST['distance']){
    $distance = $_POST['distance'];
}
$code = $_POST['code'];
$username=$_GET['username'];
$name=$_GET['name'];
$cid=$_GET['course'];
$classDate=$_POST['date']." ".$_POST['End'];
$date = date('Y-m-d H:i:s', strtotime($classDate)); 
$classLate=$_POST['date']." ".$_POST['Late'];
$late = date('Y-m-d H:i:s', strtotime($classLate)); 
$res = $conn->query("SELECT MAX(class) as curID FROM teachercourseattendance WHERE course='$cid'");
$row = $res->fetch_assoc();
$max = $row['curID']+1;
if (empty($classLate)){
    if (!$conn->query("INSERT INTO teachercourseattendance (username,course,class,classDate,classLate, code, latitude, longitude, distance) VALUES ('$username','$cid','$max','$date',NULL, '$code','$latitude','$longitude','$distance')")){
        echo "<p>Failed to insert $conn->error</p>";
        $conn->close();
        header ("Refresh:5; url=./course.php?name=$name&id=$cid");
        exit;
    }
    else {
        echo "success";
        $conn->close();
        header("Location: ./course.php?name=$name&id=$cid");
        exit;
    }

}
else if (strtotime($classDate)<strtotime($classLate)){
    echo "Error: late date after end date";
    $conn->close(); 
    header ("Refresh:5; url=./course.php?name=$name&id=$cid");
    exit;
}

if (!$conn->query("INSERT INTO teachercourseattendance (username,course,class,classDate,classLate, code, latitude, longitude, distance) VALUES ('$username','$cid','$max','$date','$late', '$code','$latitude','$longitude','$distance')")){
    echo "<p>Failed to insert $conn->error</p>";
    header ("Refresh:5; url=./course.php?name=$name&id=$cid");
    exit;
}
else {
    echo "success";
    $conn->close();
    header ("Refresh:5; url=./course.php?name=$name&id=$cid");
    exit;
}
?>