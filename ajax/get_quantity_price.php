<?php 
include '../config/connection.php';

$productId = $_GET['product_id'];
$branchId = $_GET['branch_id'];

$query = "select `p`.`sale_price`, `bs`.`quantity` 
from `products` as `p`, `branch_stock` as `bs` 
where `p`.`id` = $productId and 
`bs`.`product_id` = `p`.`id` and 
`bs`.`branch_id` = $branchId;";
$stmt = $con->prepare($query);
$stmt->execute();
$data = array('quantity' => 0, 
                  'salePrice' => 0);

$count = $stmt->rowCount();
if($count > 0) {
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $data = array('quantity' => $r['quantity'], 
                  'salePrice' => $r['sale_price']);
}
$json = json_encode($data);
echo $json;
exit;

?>