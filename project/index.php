<DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>
<?php
if (isset($_COOKIE['Attendancerus_Session'])){
    date_default_timezone_set('America/New_York');
    echo "Current time: ".date("M/d/Y h:ia")."<br>";
    echo "<div class='card'>";
    echo "<div class='card-body d-inline-flex flex-column'>";
    require_once('MoodleRest.php');
    print_r("<p>Username: ".$_COOKIE['Attendancerus_Session']."</p>");
    $username=$_COOKIE['Attendancerus_Session'];
    $token=$_COOKIE['Attendancerus_Session_Token'];
    $MoodleRest = new MoodleRest('http://attendancerus.moodlecloud.com/webservice/rest/server.php', $token);
    $response = $MoodleRest->request('core_course_get_enrolled_courses_by_timeline_classification',array('classification'=>'inprogress'));
    $courses = $response['courses'];
    echo '<p>Your current courses:</p>';
    for ($i=0; $i<sizeof($courses);$i++){
        $coursename=$courses[$i]['fullname'];
        $shortname=$courses[$i]['shortname'];
        $courseurl = $courses[$i]['viewurl'];
        $courseid = $courses[$i]['id'];
        echo "<p>- $coursename: <a href=$courseurl>Moodle</a>    <a href=./course.php?id=$courseid&name=$shortname>Student list</a></p>";
    }
    echo "</div></div>";
    print_r("Token: $token<br>");
}
else {
    header("Location: ./login.php");
    exit;
}
?>
<form action='./logout.php'>
<input type='submit' value='Logout' class="btn btn-primary">
</form>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
