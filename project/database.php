<?php
$conn = new mysqli("localhost",'root','',"testdb");
if ($conn->connect_errno){
    echo "Failed: ($conn->connect_errno) $conn->connect_error";
}
?>