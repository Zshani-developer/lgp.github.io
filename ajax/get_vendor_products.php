<?php 
include '../config/connection.php';

if(isset($_GET['vendor_id'])){
    $vendorId = $_GET['vendor_id'];
    $data = '<option value="">Select Product</option>';
    $query = "select `id`, `product_name` 
    from `products` 
    where `vendor_id` = $vendorId;";
    $stmt = $con->prepare($query);
    $stmt->execute();
    while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data = $data.'<option value="'.$r['id'].'">'.$r['product_name'].'</option>';
    }
    echo $data;
    exit;
}


if(isset($_GET['product_id'])){
    $productId = $_GET['product_id'];
    $branchId = $_GET['branch_id'];

    $query = "select `id`, `quantity` 
            from `branch_stock` 
            where `product_id` = $productId and 
            `branch_id` = $branchId;";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $data = 0;

    $count = $stmt->rowCount();
    if($count > 0) {
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        $data = $r['quantity'];
    }
    echo $data;
    exit;
}

if(isset($_GET['sale_price'])){
    $productId = $_GET['sale_price'];

    $query = "select `sale_price` 
            from `products` 
            where `id` = $productId;";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $data = 0;

    $count = $stmt->rowCount();
    if($count > 0) {
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        $data = $r['sale_price'];
    }
    echo $data;
    exit;
}
?>