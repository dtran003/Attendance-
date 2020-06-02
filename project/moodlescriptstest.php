<?php
$url='http://attendancerus.moodlecloud.com/login/token.php?username=admin&password=CSC446attendance&service=moodle_mobile_app';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$con = curl_exec($ch);
curl_close($ch);
echo $con;
?>

