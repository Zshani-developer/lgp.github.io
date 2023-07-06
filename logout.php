<?php 
include 'config/connection.php';
session_destroy();
header("location:index");
?>