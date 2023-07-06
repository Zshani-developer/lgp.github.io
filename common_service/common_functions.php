<?php 

function getuserType($con, $userType = 0) {
    $query= "select * from `user_types` order by `type` asc;";
    $stmt = $con->prepare($query);
    $stmt->execute();

    $data = '<option value="">Select User Type</option>';
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($r['id'] == $userType) {
            $data = $data.'<option selected value="'.$r['id'].'">'.$r['type'].'</option>';
        } else {
            $data = $data.'<option value="'.$r['id'].'">'.$r['id'].'</option>';
        }
    }

    return $data;
}


function getBranches($con, $branchId = 0) {
    $query= "select * from `branches` 
    where `is_active` = 1 
    order by `branch_name` asc;";
    $stmt = $con->prepare($query);
    $stmt->execute();

    $data = '<option value="">Select Branch Name</option>';
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($r['id'] == $branchId) {
            $data = $data.'<option selected value="'.$r['id'].'">'.$r['branch_name'].'</option>';
        } else {
            $data = $data.'<option value="'.$r['id'].'">'.$r['branch_name'].'</option>';
        }
    }

    return $data;
}

function getVendors($con, $vendorId = 0) {
    $query= "select `id`, `vendor_name`, `contact_number` 
        from `vendors` 
        where `is_active` = 1 
        order by `vendor_name` asc;";
    $stmt = $con->prepare($query);
    $stmt->execute();

    $data = '<option value="">Select Vendor</option>';
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $v = $r['vendor_name'].' ('.$r['contact_number'].')';
        if($r['id'] == $vendorId) {
            $data = $data.'<option selected value="'.$r['id'].'">'.$v.'</option>';
        } else {
            $data = $data.'<option value="'.$r['id'].'">'.$v.'</option>';
        }
    }

    return $data;
}


function getLoggedInUserBranch($con) {

    $branchId = $_SESSION['branch_id'];
    $query = "select * from `branches` 
    where `id`=$branchId;";
    
    $stmt = $con->prepare($query);
    $stmt->execute();
    
    $data = '';
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $branchName = $r['branch_name'];
    
    $data = $data.'<option value="'.$branchId.'">'.$branchName.'</option>';

    return $data;
}

function changeDateToMysql($date) {
    $dateArr = explode("/", $date);
    //12/01/2022
    //0  1   2
    $mysqlDate = $dateArr[2]."-".$dateArr[0]."-".$dateArr[1];
    return $mysqlDate;
}
?>