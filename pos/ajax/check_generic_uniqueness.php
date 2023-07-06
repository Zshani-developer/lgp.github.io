<?php

include'../config/connection.php';

$tableName = $_GET['table_name'];
$columnName = $_GET['column_name'];
$value = $_GET['value'];
$id = $_GET['id'];

$query = "select count(*) as `count` 
    from `".$tableName."` 
    where `".$columnName."` like('$value') and 
    `id` <> $id;";
$stmt = $con->prepare($query);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$count = $r['count'];

echo $count;
?>