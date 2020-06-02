<!DOCTYPE HTML>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div class="container-fluid">
<div class="row">
<div class="col">
<?php
    date_default_timezone_set('America/New_York');
    echo "Current time: ".date("M/d/Y h:ia");
    if (isset($_COOKIE['Attendancerus_Session'])){
        header('Location: ./index.php');
        exit;
    }
?>
<div class="card">
<div class="card-body d-inline-flex">
<form action='login.php' method='POST'>
<div class="form-group">Username: <input type='text' name='name' class="form-control"></div>
<div class="form-group">Password: <input type='password' name='password' class="form-control"></div>
<input type="submit" class="btn btn-primary">
</form>
</div>
</div>
<a href='https://attendancerus.moodlecloud.com/login/forgot_password.php'>Forgotten username or password?</a>
</div>
</div>
</div>
<?php 
if (!empty($_POST)){
    $username=$_POST['name'];
    $password=$_POST['password'];
    
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
        echo 'Success<br>';
        setcookie('Attendancerus_Session', $username,0);
        setcookie('Attendancerus_Session_Token', $c->token, 0);
        print_r($c->token);
        header('Location: ./index.php');
        exit;
    }
}
?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>