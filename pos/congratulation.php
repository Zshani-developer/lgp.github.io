<?php
    include 'config/connection.php';
    $goto = 'dashboard';
    $message = '';
    if(isset($_GET['go_to'])) {
        $goto = $_GET['go_to'];
    }

    if(isset($_GET['success_message'])) {
        $message = $_GET['success_message'];
    }
    $_SESSION['message'] = $message;

header("location:".$goto);
exit;
?>
