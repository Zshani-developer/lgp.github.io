<?php

$host = "localhost";

//db user
$user = "fca_php";
//db user password
$password = "fca_php_user4A";

$db = "pos-fca-5pm-jan2023-online";

date_default_timezone_set('Asia/Karachi');

$con = new PDO("mysql:dbname=$db;port=3306;host=$host", $user, $password);
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

session_start();

?>