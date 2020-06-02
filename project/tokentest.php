<?php
$cid=$_GET['id'];
require_once('MoodleRest.php');
$token= '8806b29100d6d44e834e2d155c484376';
$MoodleRest = new MoodleRest('http://attendancerus.moodlecloud.com/webservice/rest/server.php', $token);
$dashboard = $MoodleRest->request('core_enrol_get_enrolled_users',array('courseid'=>$cid));
for ($i=0; $i<sizeof($dashboard); $i++){
    print_r($dashboard[$i]['username']);
    echo ': ';
    print_r ($dashboard[$i]['roles'][0]['shortname']);
    echo '<br>';
}
?>