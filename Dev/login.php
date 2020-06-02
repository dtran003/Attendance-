<!DOCTYPE HTML>
<html>
<head>
<title>Login</title>
<div w3-include-html="icon.html"></div>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body style ="background-color:grey;">
<div class="container-fluid" style="background-color:grey;">
<div class="row">
<div class="col">
<div class='text-center text-primary'><h3 style="color:rgb(50,205,50)">
<?php
    date_default_timezone_set('America/New_York');
    echo "Current time: ".date("M/d/Y h:ia");
    if (isset($_COOKIE['Attendancerus_Session'])){
        header('Location: ./index.php');
        exit;
    }
    if (array_key_exists('redirect', $_GET)){
        $redirect = $_GET['redirect'];
    } else {
        $redirect=urlencode("index.php");
    }
?></h3>
</div>
<div class="card" style="background-color:grey;" class="text-center">
<img src="favicon.png" class="mx-auto d-block" width="175" height="175">
<div class="card-body d-inline-flex justify-content-center">
<form action='login.php' method='POST'>
<div class="form-group">Username: <input type='text' name='name' class="form-control"></div>
<div class="form-group">Password: <input type='password' name='password' class="form-control"></div>
<?php
echo "<input type=hidden name='redirect' value=$redirect >";
require_once("geoplugin.php");
$geoplugin = new geoPlugin();
$geoplugin->locate();
$lat=$geoplugin->latitude;
$long = $geoplugin->longitude;
echo "<input type='hidden' name='lat' id='lat' value=$lat>";
echo "<input type='hidden' name='long' id='long' value=$long>";
?>
<p id='note'></p>
<div class='text-center'><input type="submit" value='Login' class="btn btn-primary"></div>
</form>
</div>
</div>
<div class='text-center'><a href='https://attendancerus.moodlecloud.com/login/forgot_password.php'>Forgotten username or password?</a></div>
</div>
</div>
</div>
<script>
let lat = document.getElementById('lat');
let long = document.getElementById('long');
let note = document.getElementById('note');
if (navigator.geolocation){
    navigator.geolocation.getCurrentPosition(showPosition, showError);
}
function showPosition(position){
    lat.value = position.coords.latitude;
    long.value = position.coords.longitude;
}
function showError(error) {
  switch(error.code) {
    case error.PERMISSION_DENIED:
      note.innerHTML = "User denied the request for Geolocation, ip geolocation will be less accurate."
      break;
    case error.POSITION_UNAVAILABLE:
      note.innerHTML = "Location information is unavailable, ip geolocation will be less accurate."
      break;
    case error.TIMEOUT:
      note.innerHTML = "The request to get user location timed out, ip geolocation will be less accurate."
      break;
    case error.UNKNOWN_ERROR:
      note.innerHTML = "An unknown error occurred, ip geolocation will be less accurate."
      break;
  }
}
</script>
<?php 

if (!empty($_POST)){
    $username=$_POST['name'];
    $password=$_POST['password'];
    $lat=$_POST['lat'];
    $long = $_POST['long'];
    $uri = $_POST['redirect'];
    $re = urldecode($uri);
    $url='http://attendancerus.moodlecloud.com/login/token.php?username='.$username.'&password='.$password.'&service=moodle_mobile_app';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $con = curl_exec($ch);
    curl_close($ch);
    $c = json_decode($con);
    if (property_exists($c, 'error')){
        print_r('<br>'.$c->error);
    }
    else{
        setcookie('Attendancerus_Session', $username,0);
        setcookie('Attendancerus_Session_Token', $c->token, 0);
        if ($_POST['lat']) {setcookie('Latitude', $lat, 0);}
        if ($_POST['long']) {setcookie('Longitude', $long, 0);}
        print_r($c->token);
        echo 'Success<br>';
        header("Location: ./$re");
        exit;
    }
}
?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>