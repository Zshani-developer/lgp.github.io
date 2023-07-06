<?php 
include 'config/connection.php';
if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}
$id = $_GET['id'];
$isActive = $_GET['is_active']; 

$message = 'Products has been Banned.';

$newStatus = ($isActive + 1) % 2;
if($newStatus == 1) {
    $message = 'Products has been unbanned.';
}
$query = "update `vendors` set `is_active` = $newStatus 
where `id` = $id;";

    try {

        $con->beginTransaction();
        
        $stmt = $con->prepare($query);
        $stmt->execute();
        
        $con->commit();
        
 } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }
    $goto = "vendors";
    header("location:congratulation?go_to=".$goto."&success_message=".$message); 
    exit;
?>