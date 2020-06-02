<?php
    if (isset($_COOKIE['Attendancerus_Session']))
    {
        unset($_COOKIE['Attendancerus_Session']);
        unset($_COOKIE['Attendancerus_Session_Token']);
        setcookie('Attendancerus_Session', '', time()-3600);
        setcookie('Attendancerus_Session_Token', '', time()-3600);
    }
    header('Location: ./login.php');
    exit;
?>