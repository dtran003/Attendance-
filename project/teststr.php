<?php
$str='Database=localdb;Data Source=127.0.0.1:56705;User Id=azure;Password=6#vWHD_$';
$arr = explode(';',$str);
$arr1=array();
foreach($arr as $key=>$value){
    $buf=explode('=',$value);
    $arr1[$buf[0]]=$buf[1];
}
print_r($arr1);
?>