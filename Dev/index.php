<DOCTYPE HTML>
<html>
<head>
<div w3-include-html="icon.html"></div>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body style="background-color:grey;">
<?php
if (isset($_COOKIE['Attendancerus_Session'])){
?>
<img src='favicon.ico' height=75 width=75></img>
<?php
    $curDate = date("M/d/Y h:ia");
    echo "<nav class='navbar navbar-expand-lg fixed-bottom navbar-light bg-light'>";
    echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>';
    echo  "<a class='navbar-brand' href='#'><img src='./favicon.png' height=60 width=100/></a>";
    echo '<div class="collapse navbar-collapse" id="navbarSupportedContent"><ul class="navbar-nav">';
    echo "<li class='nav-item'><a class='nav-link'  href='logout.php'>Log out</a></li>";
    echo "<li class='nav-item'><a class='nav-link'  href='contact.html'>Contact us</a></li>";
    echo "<li class='nav-item'><a class='nav-link disabled' href='#'>$curDate</a></li>";
    echo' </ul></div></nav>';
    date_default_timezone_set('America/New_York');
    print_r("<h3 class='text-info'>Welcome ".$_COOKIE['Attendancerus_Session']."</h3>");
    require_once('MoodleRest.php');
    require_once('database.php');
    $username=$_COOKIE['Attendancerus_Session'];
    $token=$_COOKIE['Attendancerus_Session_Token'];
    $MoodleRest = new MoodleRest('http://attendancerus.moodlecloud.com/webservice/rest/server.php', $token);
    $response = $MoodleRest->request('core_course_get_enrolled_courses_by_timeline_classification',array('classification'=>'inprogress'));
    $courses = $response['courses'];
?>
<?php
    for ($i=0; $i<sizeof($courses);$i++){
        echo "<div class='row'>";
        $coursename=$courses[$i]['fullname'];
        $fullname = urlencode($coursename);
        $shortname=$courses[$i]['shortname'];
        $courseurl = $courses[$i]['viewurl'];
        $courseid = $courses[$i]['id'];
        $res = $conn->query("SELECT class, classDate, classLate FROM teachercourseattendance WHERE classDate>CURRENT_TIME and course='$courseid' ORDER BY classDate");
        echo "<div class='col-3 border'><p><b>$coursename</b></p></div><div class='col-3 border'><p><a href=$courseurl style='color:rgb(50,205,50)'>Moodle link</a></p></div><div class='col-3 border'><p><a href=../course.php?id=$courseid&name=$fullname style='color:rgb(50,205,50)'>Attendance</a></p></div>";
        echo '<div class="col-3 border">';
        if ($res->num_rows>0){
            $row = $res->fetch_assoc();
            echo "<p><a href=./course.php?id=$courseid&name=$fullname#Main{$row['class']} style='color:rgb(50,205,50)'>Next class on {$row['classDate']}</a></p>";
        }
        echo "</div>";
        echo "</div>";
    }
    echo "</div></div>";
}
else {
    header("Location: ../login.php");
    exit;
}
?>
<form action='../logout.php'>
<input type='submit' value='Logout' class="btn btn-primary">
</form>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
