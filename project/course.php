<DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<?php
date_default_timezone_set('America/New_York');
echo "Current time: ".date("M/d/Y h:ia")."<br>";
echo "<form action='./index.php'><input type='submit' value='Go back to index' class='btn btn-primary'></form>";
$cid=$_GET['id'];
$token = $_COOKIE['Attendancerus_Session_Token'];
require_once('MoodleRest.php');
$MoodleRest = new MoodleRest('http://attendancerus.moodlecloud.com/webservice/rest/server.php', $token);
$dashboard = $MoodleRest->request('core_enrol_get_enrolled_users',array('courseid'=>$cid));
$teacher='/';
$name=$_GET['name'];
echo "<h3>$name</h3>";
echo "<div class='card'>";
echo "<div class='card-body d-inline-flex flex-column'>";

for ($i=0; $i<sizeof($dashboard); $i++){
    echo "<p>";
    print_r($dashboard[$i]['fullname']);
    echo ': ';
    print_r ($dashboard[$i]['roles'][0]['shortname']);
    echo "</p>";
}
echo "</div></div>";
if (array_key_exists('username', $dashboard[0])){
    $username=$dashboard[0]['username'];
    echo "<div class='card'><div class='card-body d-inline-flex flex-column'>";
    echo "<h5 class='card-title'>Track new class session</h5>";
    echo "<form method='POST' action=./newclass.php?course=$cid&name=$name&username=$username><p>Date:<input type='datetime-local' class='form-control' name='classDate'></p><p>Session ID:<input type='number' class='form-control' name='class'></p><input type='submit' class='btn btn-primary' value='Track new session'></form>";
    echo "</div></div>";
    require_once('./database.php');
    $res = $conn->query("SELECT course,code,class,classDate FROM teachercourseattendance WHERE course='$cid' ");
    while ($row = $res->fetch_assoc()){
        echo "<div class='card'><div class='card-body d-inline-flex flex-column'>";
        $classID=$row['class'];
        echo "<h5 class='card-title'>".$row['class'] ." . ". $row['classDate']."</h5>";
        echo "<p>Current code: ". $row['code']."</p>";
        echo "<form method='POST' action=./newcode.php?id=$cid&class=$classID&name=$name><input type='text' name='code'><input type='submit' value='Change Code' class='btn btn-primary'></form>";
        $res1 = $conn->query("SELECT count(username) FROM studentcourseattendance WHERE course='$cid' AND class='$classID'");
        while ($row2=$res1->fetch_assoc()){
            echo $row2["count(username)"]." students attended";
        }
        echo "</div></div>";
    }
    $conn->close();
    
}
else {
    require_once('./database.php');
    $user=$_COOKIE['Attendancerus_Session'];
    $res2 = $conn->query("SELECT course,class, classDate FROM teachercourseattendance WHERE course='$cid' AND classDate>CURRENT_TIME");
    while($row=$res2->fetch_assoc()){
        echo "<div class='card'><div class='card-body d-inline-flex flex-column'>";
        echo "<h5 class='card-title'>".$row['class'] ." . ". $row['classDate']."</h5>";
        $class=$row['class'];
        $res3=$conn->query("SELECT * FROM studentcourseattendance WHERE course='$cid' AND class='$class' AND username='$user'");
        if ($res3->num_rows>0){
            echo "<p>Attendance confirmed</p>";
        }
        else {
            echo "<p>Attendance not confirmed</p>";
            echo "<p>Put in code to confirm attendance:<form method='POST' action=./validate.php?username=$user&course=$cid&class=$class&name=$name><input type='password' name='code'><input type='submit' class='btn btn-primary' name='Confirm'></form></p>";

        }
        echo "</div></div>";
    }
    $conn->close();
}
?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>