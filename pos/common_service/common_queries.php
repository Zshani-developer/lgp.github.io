<?php

function getDailyReturnQuery($from, $to, $branchId) {

    $query = "select `c`.`category_name`, `p`.`product_id`, `p`.`product_name`,  
`ds`.`sale_price`, SUM(`ds`.`quantity`) AS `total_quantity`, 
date_format(`ds`.`transaction_date`, '%d %b %Y') as `transaction_date`,
sum(`ds`.`discount_amount`) as `total_discount`, 
sum(`ds`.`net_amount`) as `total_net_amount` 
from `categories` as `c`, `products` as `p`, 
`daily_sales` as `ds` 
where `c`.`category_id` = `p`.`category_id` and 
`p`.`product_id` = `ds`.`product_id` and 
`ds`.`transaction_date` between '$from' and '$to' and 
`ds`.`branch_id` = $branchId and 
`ds`.`net_amount` < 0 
group by `p`.`product_id` 
order by `c`.`category_name` asc, 
`p`.`product_name` asc;";

    return $query;
}

function getDailySalesQuery($from, $to, $branchId) {
    $query = "SELECT `c`.`category_name`, `p`.`product_name`, 
date_format(`d`.`transaction_date`, '%d %b %Y') as `transaction_date`, 
`d`.`purchase_price`, `d`.`sale_price`, 
`d`.`discount_amount`, `d`.`net_amount`, `d`.`quantity`,
`d`.`product_id` 
FROM `categories` AS `c`, `products` AS `p`, `daily_sales` AS `d`
WHERE `c`.`category_id` = `p`.`category_id` AND 
`p`.`product_id` = `d`.`product_id` AND 
`d`.`branch_id` = $branchId AND 
`d`.`transaction_date` between '$from' and '$to' 
ORDER BY `d`.`transaction_date` asc, `d`.`product_id` ASC;";

    return $query;
}

function getZeroStockQuery($branchId) {
    $query = "select `c`.`category_name`, `p`.`product_name`, `p`.`purchase_price`,
    `p`.`sale_price`, `b`.`quantity`  
    FROM `categories` as `c`, `products` as `p`, `branch_stock` as `b`
    where `c`.`category_id` = `p`.`category_id` AND 
    `p`.`product_id` = `b`.`product_id` AND 
    `b`.`branch_id` =  $branchId AND 
    `b`.`quantity` = 0 ORDER BY `b`.`product_id` ASC;";

    return $query;
}

function getDailySalesQuantityAndAmountBasedQuery($from, $to, $branchId, $type) {

    $orderBy = "";

    if ($type == 'QUNTITY_BASED') {
        $orderBy = "order by `total_quantity` desc;";
    } else if ($type == 'AMOUNT_BASED') {
        $orderBy = "order by `total_net_amount` desc;";
    }

    //`d`.`net_amount` > 0 AND 
   
    $query = "select `w`.`category_name`, `w`.`product_name`, 
`w`.`purchase_price`, `w`.`sale_price`, `w`.`product_id`, 
SUM(`w`.`discount_amount`) AS `total_discount`, 
SUM(`w`.`net_amount`) AS `total_net_amount`, 
SUM(`w`.`quantity`) AS `total_quantity` 
from (
SELECT `c`.`category_name`, `p`.`product_name`, 
`p`.`purchase_price`, `p`.`sale_price`, `d`.`product_id`, 
(case when `d`.`net_amount` > 0 then `d`.`discount_amount` else (-1 * `d`.`discount_amount`) end) as `discount_amount`, 
(case when `d`.`net_amount` > 0 then `d`.`quantity` else (-1 * `d`.`quantity`) end) as `quantity`, 
`d`.`net_amount` 
FROM `categories` AS `c`, `products` AS `p`, `daily_sales` AS `d` 
WHERE `c`.`category_id` = `p`.`category_id` AND 
`d`.`product_id` = `p`.`product_id` AND 
`d`.`branch_id` = $branchId AND 
`d`.`transaction_date` between '$from' and '$to' 
) as `w` 
group by `w`.`product_id`" . $orderBy;

    return $query;
}

function getTotalDailyReturnQuery($from, $to, $branchId) {

    $query = "select ifnull(sum(`ds`.`net_amount`), 0) as `total_refunds` from
`daily_sales` as `ds` 
where `ds`.`branch_id` = $branchId and 
`ds`.`net_amount` < 0 and 
`ds`.`transaction_date` between '$from' and '$to';";

    return $query;
}

function getDailySalesCategoryBasedQuery($from, $to, $branchId) {

    $query = "SELECT IFNULL(SUM(`d`.`total`), 0) AS `total_amount`, 
IFNULL(SUM(`d`.`net_amount`), 0) AS `total_net_amount`, `c`.`category_name`, `p`.`category_id` 
FROM `daily_sales` AS `d`, `categories` AS `c`, `products` AS `p`
WHERE `d`.`branch_id` = $branchId AND 
`d`.`product_id` = `p`.`product_id` AND 
`p`.`category_id` = `c`.`category_id` AND 
`d`.`transaction_date` BETWEEN '$from' AND '$to' 
GROUP BY `c`.`category_id` 
ORDER BY `c`.`category_name` ASC;";

    return $query;
}

function getBranchGraphData($from, $to, $branchId) {
    
$query = "select concat(`w`.`category_name`, ' (',`w`.`product_name`, ')') as `cat_prod`, 
`w`.`total` from ( 
select `c`.`category_name`, `p`.`product_name`, 
SUM(`ds`.`net_amount`) AS `total` 
from `categories` as `c`, `products` as `p`, `daily_sales` as `ds` 
where `c`.`category_id` = `p`.`category_id` and 
`p`.`product_id` = `ds`.`product_id` and 
`ds`.`branch_id` = $branchId and 
`ds`.`net_amount` > 0 and 
`ds`.`transaction_date` between '$from' and '$to' 
group by `ds`.`product_id` 
) as `w` 
order by  `w`.`total` desc;"; 
    
    return $query;
    
}

function getBranchGraphCategoryBasedData($from, $to, $branchId, $categoryId) {
    
$query = " select `c`.`category_name`, `p`.`product_name`, 
SUM(`ds`.`net_amount`) AS `total` 
from `categories` as `c`, `products` as `p`, `daily_sales` as `ds` 
where `c`.`category_id` = `p`.`category_id` and 
`p`.`product_id` = `ds`.`product_id` and 
`ds`.`branch_id` = $branchId and 
`c`.`category_id` = $categoryId and    
`ds`.`transaction_date` between '$from' and '$to' 
group by `ds`.`product_id` order by `total` desc;"; 
    
    return $query;
    
}

?>
