
<?php
require_once('./database.php');
date_default_timezone_set('America/New_York');
if (array_key_exists('request', $_POST)){
    $request = $_POST['request'];
}
$lat = null;
$long = null;
if(isset($_COOKIE['Latitude'])){
    $lat = $_COOKIE['Latitude'];
}
if (isset($_COOKIE['Longitude'])){
    $long = $_COOKIE['Longitude'];
}
$name=$_GET['name'];
$class= $_GET['class'];
if (array_key_exists('code', $_GET)){
    $code = $_GET['code'];
}
if (isset($_COOKIE['Attendancerus_Session'])){
$username = $_COOKIE['Attendancerus_Session'];
$cid = $_GET['course'];
$res = $conn->query("SELECT code, distance, classLate, classDate, latitude, longitude FROM teachercourseattendance WHERE course='$cid' AND class='$class'");
$row = $res->fetch_assoc();
$date = $row['classDate'];
$realcode = $row['code'];
$distanceLimit = $row['distance'];
$classLatitude=$row['latitude'];
$classLongitude = $row['longitude'];
$notes='On time';
if (!is_null($row['classLate'])){
    $late = strtotime($row['classLate']);
    if (time()>$late){
        $notes='Late';
    }
    else {
        $notes='On time';
    }
}
if (!empty($code)){
    if ($code==$realcode){
        function distanceFromUser($latitude, $longitude){
            $userLat = $_COOKIE['Latitude'];
            $radUserLat = deg2rad($userLat);
            $userLong = $_COOKIE['Longitude'];
            $radUserLong = deg2rad($userLong);
            $radius = 6371000;
            $radLat = deg2rad($latitude);
            $delLat = deg2rad($userLat-$latitude);
            $delLong = deg2rad($userLong-$longitude);
            $tempVar = sin($delLat/2)*sin($delLat/2)+cos($radUserLat)*cos($radLat)*sin($delLong/2)*sin($delLong/2);
            $tempVar2 = 2*atan2(sqrt($tempVar), sqrt(1-$tempVar));
            $distance = $radius*$tempVar2;
            return round($distance,4);
        }
        $dist = -2;
        if ($classLatitude!=null and $distanceLimit>=0){$dist = distanceFromUser($classLatitude, $classLongitude);}
        if ($dist<=$distanceLimit){
        $conn->query("INSERT INTO studentcourseattendance (username, course, class, notes, latitude, longitude) VALUES ('$username','$cid','$class','$notes', '$lat', '$long')");
        echo "Attendance submit for $name on $date, return after 5 secs";
        header ("Refresh:5; url=./course.php?name=$name&id=$cid");
        }
        else {
            echo "Too far away";
            header ("Refresh:5; url=./course.php?name=$name&id=$cid");
        }
        exit;
    } else{
        echo 'Wrong code, return after 5 secs';
        header ("Refresh:5; url=./course.php?name=$name&id=$cid");
        }
    }

if (!empty($request)){
    if (!$conn->query("INSERT INTO studentcourseattendance (username, course, class, notes, excuses, latitude, longitude) VALUES ('$username', '$cid', '$class', 'Pending', '$request', '$lat', '$long')")){
        echo $conn->error.", return after 5 secs";
        header ("Refresh:5; url=./course.php?name=$name&id=$cid");
    } else {
        echo 'Success, return after 5 secs';
        header ("Refresh:5; url=./course.php?id=$cid&name=$name");
        exit;
        }
    }
} else {
    $uri = $_SERVER['REQUEST_URI'];
    $full = urlencode("$uri");
    header ("Location: ./login.php?redirect=$full");
}
$conn->close();
?>