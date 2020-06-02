<DOCTYPE HTML>
<html>
<head>
<div w3-include-html="icon.html"></div>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<?php
date_default_timezone_set('America/New_York');
$curDate = date("M/d/Y h:ia");
$cid = $_GET['course'];
$coursename=urlencode($_GET['name']);
$class = $_GET['class'];
echo "<nav class='navbar navbar-expand-lg fixed-bottom navbar-light bg-light'>";
echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>';
echo  "<a class='navbar-brand' href='#'><img src='./favicon.png' height=60 width=100/></a>";
echo  "<a class='navbar-brand' href='#'>{$_GET['name']}</a>";
echo '<div class="collapse navbar-collapse" id="navbarSupportedContent"><ul class="navbar-nav">';
echo "<li class='nav-item active'><a class='nav-link' href=./course.php?id=$cid&name=$coursename>Main Course</a></li>";
echo "<li class='nav-item active'><a class='nav-link' href='index.php'>Index</a></li>";
echo "<li class='nav-item'><a class='nav-link' href='logout.php'>Log out</a></li>";
echo "<li class='nav-item'><a class='nav-link'  href='contact.html'>Contact us</a></li>";
echo "<li class='nav-item'><a class='nav-link disabled' href='#'>$curDate</a></li>";
echo' </ul></div></nav>';
require_once('database.php');
$res = $conn->query("SELECT classDate, classLate from teachercourseattendance WHERE course='$cid' AND class='$class'");
if ($res->num_rows>0){
    $row = $res->fetch_assoc();
    $classDate=$row['classDate'];
    $classLate=$row['classLate'];
}
else{
    echo 'Class not found';
}
$time = strtotime($classDate);
$cname = urldecode($coursename);
echo "<h3>$cname on $classDate</h3>";
if (!is_null($classLate)){
    echo "<p>Late limit: $classLate</p>";
}
$stds = [];
$res2 = $conn->query("SELECT username, notes, excuses, submitTime, latitude, longitude from studentcourseattendance WHERE course='$cid' AND class='$class'");
if ($res2->num_rows>0){
    while ($row2 = $res2->fetch_assoc()){
        $st=[];
        $studentname=$row2['username'];
        $st['notes']=$row2['notes'];
        $st['submitTime']=$row2['submitTime'];
        $st['excuses']=$row2['excuses'];
        $st['latitude']=$row2['latitude'];
        $st['longitude']=$row2['longitude'];
        $stds[$studentname]=$st;
    }
}
$token = $_COOKIE['Attendancerus_Session_Token'];
require_once('MoodleRest.php');
$MoodleRest = new MoodleRest('http://attendancerus.moodlecloud.com/webservice/rest/server.php', $token);
$dashboard = $MoodleRest->request('core_enrol_get_enrolled_users',array('courseid'=>$cid));
$teacher=$dashboard[0]['fullname'];
echo "<h4>".$dashboard[0]['roles'][0]['shortname'].": ".$teacher."</h4>";

?>
<div class='row border'>
    <div class='col'>
        <b>Name</b>
    </div>
    <div class='col'>
        <b>Role</b>
    </div>
    <div class='col'>
        <b>Check in time</b>
    </div>
    <div class='col'>
        <b>Check in specifics</b>
    </div>
    <div class='col'>
        <b>Manual change</b>
    </div>
    <div class='col'></div>
</div>
<?php
for ($i=1; $i<sizeof($dashboard); $i++){
    echo "<div class='row border'>";
    echo "<div class='col'>";
    print_r($dashboard[$i]['fullname']);
    echo "</div>";
    echo '<div class="col">';
    print_r ($dashboard[$i]['roles'][0]['shortname']);
    echo "</div>";
    $uname = $dashboard[$i]['username'];
    $notes="Attendance not confirmed";
    $excuses=null;
    $latitude = null;
    $longitude = null;
    echo "<div class='col'>";
    if (array_key_exists($uname,$stds)){
        echo $stds[$uname]['submitTime'];
        $notes = $stds[$uname]['notes'];
        $excuses = $stds[$uname]['excuses'];
        $latitude = $stds[$uname]['latitude'];
        $longitude = $stds[$uname]['longitude'];
    }
    else {
        if (time()>$time){
            $notes= "Absent";
        }
    }
    echo "</div>";
    echo "<div class='col'>";
    if (!is_null($notes)){echo $notes;}
    if (!is_null($excuses)){echo '<br>Excuses: '. $excuses;}
    if (!is_null($latitude) & !is_null($longitude)){
        echo "<br><a target='_blank' rel='noopener noreferrer' href='https://www.latlong.net/c/?lat=$latitude&long=$longitude'>Location</a>";
    }
    echo "</div>";
    echo "<div class='col'><form method=POST action=./approval.php?student=$uname&course=$cid&class=$class&name=$coursename>";
    echo "<select class='custom-select' name='notes'>";
    echo "<option>On time</option>";
    echo "<option>Late</option>";
    echo "<option>Absent</option>";
    echo "<option selected='selected'>Excused</option>";
    echo "</select></div><div class='col'><input type='submit' class='btn btn-primary'></form></div>";
    echo "</div>";
}


$conn->close();

?>
</body>
</html>