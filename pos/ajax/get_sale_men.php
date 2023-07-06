<?php 
include '../config/connection.php';

    $branchId = $_GET['branch_id'];
    $data = '<option value="">Select Salemen</option>';
    $query = "select `id`, `name`, `contact_number`
    from `sale_men` 
    where `is_active` = 1 and `branch_id` = $branchId;";
    $stmt = $con->prepare($query);
    $stmt->execute();
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $S = $r['name'].' ('.$r['contact_number'].')';
        $data = $data.'<option value="'.$r['id'].'">'.$S.'</option>';
    }
    echo $data;

?>