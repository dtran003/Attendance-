<html>
<head>
<div w3-include-html="icon.html"></div>
<meta 
name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<style>
 iframe{
     display: none;
 }
 div {
     background-color: grey;
 }
 a {
     color: green; 
 }

</style>
</head>
<body style="background-color:grey;">
<?php

date_default_timezone_set('America/New_York');
$curDate = date("m/d/y h:ia");
$defaultDate = date("H:i:s");
$cid=$_GET['id'];
$token = $_COOKIE['Attendancerus_Session_Token'];
require_once('MoodleRest.php');
$opt = array(
    "ids"=>array(
        0=>$cid,
    ),
);
$MoodleRest = new MoodleRest('http://attendancerus.moodlecloud.com/webservice/rest/server.php', $token);

$dashboard = $MoodleRest->request('core_enrol_get_enrolled_users',array('courseid'=>$cid));
$teacher='/';
if (array_key_exists('username', $dashboard[0])){
    $dashboard1 = $MoodleRest->request('core_course_get_courses',array('options'=>$opt));
    $name = $dashboard1[0]['fullname'];
    $name2 = $dashboard1[0]['shortname'];
    $nameURL = urlencode($name);
    echo "<nav class='navbar navbar-expand-lg fixed-bottom navbar-light bg-light'>";
    echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>';
    echo  "<a class='navbar-brand' href='#'><img src='./favicon.png' height=60 width=100/></a>";
    echo  "<a class='navbar-brand' href='#'>$name</a>";
    echo '<div style="background-color:white" class="collapse navbar-collapse" id="navbarSupportedContent"><ul class="navbar-nav">';
    echo "<li class='nav-item active'><a class='nav-link' href='index.php'>Index</a></li>";
    echo '<li class="nav-item">';
    echo '<a class="nav-link" href="#Up">Upcoming sessions<span class="sr-only">(current)</span></a>';
    echo '</li>';
    echo '<li class="nav-item"><a class="nav-link" href="#end">Expired sessions</a></li>';
    echo '<li class="nav-item"><a class="nav-link" href="#mainForm">Track new sessions</a></li>';
    echo "<li class='nav-item'><a class='nav-link disabled' href='#'>$curDate</a></li>";
    echo "<li class='nav-item'><a class='nav-link' href='logout.php'>Log out</a></li>";
    echo "<li class='nav-item'><a class='nav-link'  href='contact.html'>Contact us</a></li>";
    echo' </ul></div></nav>';
    $username=$dashboard[0]['username'];
    echo "<div class='card'><div class='card-body d-inline-flex flex-column'>";
    echo "<h5 id='mainForm'>Track new class session</h5>";
    echo "<form id='createForm' class='d-block' method='POST' action=./newclass.php?course=$cid&name=$nameURL&username=$username style='color:rgb(50,205,50)'>";
    $d = date('Y-m-d');
    echo "<div class='form-group'><p>Date:<input type='date' class='form-control' name='date' value=$d>";
    echo "<p>Late limit (submission are marked as late after this date):<input type='time' class='form-control' name='Late' value='{$defaultDate}'></p>";
    echo "<p>End date (submission are locked after this date):<input type='time' class='form-control' name='End' value='{$defaultDate}'></p></div>";
    echo "<p>Code: <input type='text' class='form-control' name='code' value='default'</p>";
    echo "<div class='form-group'>Latitude: <input type='text' class='form-control' value={$_COOKIE['Latitude']} name='latitude'>";
    echo "Longitude: <input type='text' class='form-control' value={$_COOKIE['Longitude']} name='longitude'></div>";
    echo "Distance limit (in metres):<input type='number' class='form-control' value='50' name='distance'> ";
    echo "<input type='submit' class='btn btn-primary' value='Track new session'>";
    echo "</form>";
    $createForm = "createForm";
    echo "<button class='d-inline p-2 btn btn-info' id='formToggle'>Show/Hide form</button>";
    echo "</div></div>";
    require_once('./database.php');
    $res0 = $conn->query("SELECT course,code,class,classDate,classLate FROM teachercourseattendance WHERE course='$cid' AND classDate>=now() ORDER BY classDate");
    echo "<div class='card' id='Up'>";
    echo "<div class='card-header'><h3 style='color:rgb(50,205,50)'>Upcoming sessions</h3></div>";
    echo "<div class='card-body d-inline-flex flex-column'>";
    while ($row = $res0->fetch_assoc()){
        echo "<div class='card'>";
        echo "<div class='card-header'><h5>".$row['class'] ." . ". $row['classDate']."</h5></div>";
        $classID=$row['class'];
        echo "<div class='card-body d-inline-flex flex-column' id=$classID>";
        if (!is_null($row['classLate'])){
            echo "<p>Late limit: ".$row['classLate']."<p>";
        }
        echo "<p>Current code: ". $row['code']."</p>";
        $code = $row['code'];
        $currentUrl = $_SERVER['HTTP_HOST'];
        $url = "https://$currentUrl/validate.php?code=$code&class=$classID&course=$cid&name=$nameURL";
        $encoded = urlencode($url);
        echo "<p><a target='_blank' rel='noopenner noreferrer' href=http://api.qrserver.com/v1/create-qr-code/?data=$encoded&size=500x500>See QR code</a></p>";
        echo "<form method='POST' action=./newcode.php?id=$cid&class=$classID&name=$nameURL><input type='text' name='code'><input type='submit' value='Change Code' class='btn btn-primary'></form>";
        $count = $conn->query("SELECT count(username) FROM studentcourseattendance WHERE course='$cid' AND class='$classID'");
        while ($row2=$count->fetch_assoc()){
            echo "<a href=./class.php?course=$cid&name=$nameURL&class=$classID>".$row2['count(username)']." students checked in</a>";
        }
        echo "</div>";
        echo "<div class='card-footer'><button class='btn btn-info' onclick=toggle($classID)>Show/Hide details</button></div></div>";
    }
    echo "</div></div>";
    $res1 = $conn->query("SELECT course,code,class,classDate,classLate FROM teachercourseattendance WHERE course='$cid' AND classDate<now() ORDER BY classDate");
    echo "<div class='card' id='end'>";
    echo "<div class= 'card-header'><h3 style='color:rgb(50,205,50)'>Ended sessions</h3></div>";
    echo "<div class='card-body d-inline-flex flex-column'>";
    while ($row = $res1->fetch_assoc()){
        echo "<div class='card'>";
        $classID=$row['class'];
        echo "<div class='card-header'><h5>".$row['class'] ." . ". $row['classDate']."</h5></div>";
        echo "<div class='card-body d-inline-flex flex-column' id=$classID>";
        if (!is_null($row['classLate'])){
            echo "<p>Late limit: ".$row['classLate']."<p>";
        }
        echo "<p>Current code: ". $row['code']."</p>";
        $code = $row['code'];
        $currentUrl = $_SERVER['HTTP_HOST'];
        $url = "https:/$currentUrl/validate.php?code=$code&class=$classID&course=$cid&name=$name";
        $encoded = urlencode($url);
        echo "<p><a target='_blank' rel='noopenner noreferrer' style='color:rgb(50,205,50)' href=http://api.qrserver.com/v1/create-qr-code/?data=$encoded&size=500x500>See QR code</a></p>";
        echo "<form method='POST' action=./newcode.php?id=$cid&class=$classID&name=$nameURL><input type='text' name='code'><input type='submit' value='Change Code' class='btn btn-primary'></form>";
        $count = $conn->query("SELECT count(username) FROM studentcourseattendance WHERE course='$cid' AND class='$classID'");
        $row2=$count->fetch_assoc();
        echo "<a href='class.php?class=$classID&course=$cid&name=$nameURL' style='color:rgb(50,205,50)'>".$row2['count(username)']." students checked in</a>";
        echo "</div>";
        echo "<div class='card-footer'><button class='btn btn-info' onclick=toggle($classID)>Show/Hide details</button></div></div>";
    }
    $conn->close();
    
}
else {
    
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
    echo "<a target='_blank' href='https://zxing.org/w/decode.jspx'>Read QR image</a><br>";
    echo "<a target='_blank' href = 'https://qrcodescan.in'>Scan QR code</a>";
    $name = urldecode($_GET['name']);
    require_once('./database.php');
    $user=$_COOKIE['Attendancerus_Session'];
    echo "<nav class='navbar navbar-expand-lg fixed-bottom navbar-dark bg-dark'>";
    echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>';
    echo  "<a class='navbar-brand' href='#'>$name</a>";
    echo '<div class="collapse navbar-collapse" id="navbarSupportedContent"><ul class="navbar-nav">';
    echo "<li class='nav-item active'><a class='nav-link' href='index.php'>Index</a></li>";
    echo "<li class='nav-item'><a class='nav-link disabled' href='#'>$curDate</a></li>";
    echo "<li class='nav-item'><a class='nav-link' href='logout.php'>Log out</a></li>";
    echo "<li class='nav-item'><a class='nav-link'  href='contact.html'>Contact us</a></li>";
    echo' </ul></div></nav>';
    $date = date('Y-m-d H:i:s');
    $res2 = $conn->query("SELECT course,class, classDate, latitude, longitude, distance FROM teachercourseattendance WHERE course='$cid' AND classDate>CURRENT_TIME ORDER BY classDate");
    while($row=$res2->fetch_assoc()){
        $class=$row['class'];
        echo "<div class='card' id=Main{$class}>";
        echo "<h5 class='card-header'>".$row['class'] ." . ". $row['classDate']."</h5>";
        echo "<div class='card-body d-inline-flex flex-column' id=$class>";
        $res3=$conn->query("SELECT notes FROM studentcourseattendance WHERE course='$cid' AND class='$class' AND username='$user'");
        if ($res3->num_rows>0){
            echo "<p>Checked in</p>";
            $row = $res3->fetch_assoc();
            echo "Status: ".$row['notes'];
        }
        else {
            $allowed=true;
            $classLatitude = $row['latitude'];
            $classLongitude = $row['longitude'];
            echo "<p>Not confirmed</p>";
            $id = "code$class";
            $name2 = urlencode($name);
            echo "<p><a target='_blank' rel='noopener noreferrer' href='https://www.latlong.net/c/?lat={$classLatitude}&long={$classLongitude}'>Location</a></p>";
            if ($classLatitude!=null){
                $yourDistance=distanceFromUser($classLatitude, $classLongitude);
                if ($yourDistance>$row['distance'] && $row['distance']>0){
                    echo "<p>You are  not in range of the class</p>";
                    $allowed=false;
                }
                else {
                    echo "<p>You are in range of the class</p>";
                }
            }
            if ($allowed){
            echo "<p>Put in code to confirm attendance:<form method='GET' action=./validate.php>";
            echo "<input type='password' name='code' class='form-control'>";
            echo "<input type=hidden value=$cid name='course'>";
            echo "<input type=hidden value=$class name='class'>";
            echo "<input type=hidden value=$name2 name='name'>";
            echo "<input type='submit' class='btn btn-primary' value='Submit code'></form></p>";
            echo "<p>Request attendance:<form method='POST' action=./validate.php?course=$cid&class=$class&name=$name2><input type='textarea' name='request' class='form-control'><input type='submit' class='btn btn-primary' value='Submit excuse'></form></p>";
            }
        }
        echo "</div>";
        echo "<div class='card-footer'><button class='btn btn-info' onclick=toggle($class)>Show/Hide details</button></div></div>";
    }
    $conn->close();
    
}
?>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script>
    document.getElementById("formToggle").addEventListener("click", function(){
        console.log("clicked");
        let e = document.getElementById("createForm");
        if (e.className==="d-none"){
            e.className="d-block";
        }
        else {
            e.className="d-none";
        }
    }
    );
    function toggle(classID){
        let e = document.getElementById(classID);
        if (e.className==="d-none"){
            e.className="card-body d-inline-flex flex-column";
        }
        else{
            e.className="d-none";
        }
    }
</script>
<script>

</script>
</body>
</html>