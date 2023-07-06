<?php

include("config/connection.php");
include ('./pdflib/logics-builder-pdf.php');
include("common_service/common_functions.php");

if (!(isset($_SESSION['user_id']))) {
    header("location:index");
    exit;
}

$branch_id = $_GET['branch_id'];
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$type = $_GET['type'];

$mysqlStart = changeDateToMysql($startDate);
$mysqlEnd = changeDateToMysql($endDate);

$subTitle = "";
$query = "select `q`.* from (
SELECT `c`.`vendor_name`, `p`.`product_name`, 
IFNULL(SUM(`dd`.`quantity`), 0) as `total` 
FROM `vendors` AS `c`, `products` AS `p`, 
`daily_sales` AS `d`, 
`daily_sales_details` as `dd` 
WHERE `c`.`id` = `p`.`vendor_id` AND 
`p`.`id` = `dd`.`product_id` AND 
`d`.`branch_id` = $branch_id AND 
`d`.`id` = `dd`.`daily_sale_id` and 
`d`.`transaction_date` between '$mysqlStart' and '$mysqlEnd' 
GROUP BY `dd`.`product_id`, 
`p`.`product_name`, `c`.`vendor_name` 
)
as `q` order by `q`.`total` desc;";

$lastColumnTitle = 'Total Quantity Sold';

if ($type === "QUNTITY_BASED") {
    $subTitle = "Quantity Based";
    
} else if ($type === "AMOUNT_BASED") {
    $lastColumnTitle = 'Total Amount';
    $subTitle = "Amount Based";
    $query = "select `q`.* from (
SELECT `c`.`vendor_name`, `p`.`product_name`, 
IFNULL(SUM(`dd`.`total_net_amount`), 0) as `total` 
FROM `vendors` AS `c`, `products` AS `p`, `daily_sales` AS `d`, 
`daily_sales_details` as `dd` 
WHERE `c`.`id` = `p`.`vendor_id` AND 
`p`.`id` = `dd`.`product_id` AND 
`d`.`branch_id` = $branch_id AND 
`d`.`id` = `dd`.`daily_sale_id` and 
`d`.`transaction_date` between '$mysqlStart' and '$mysqlEnd' 
GROUP BY `dd`.`product_id`, 
`p`.`product_name` , `c`.`vendor_name` 
)
as `q` order by `q`.`total` desc;";
    
}

$queryBranch = "select * from `branches` 
where `id` = '$branch_id';";
$stmtBranch = $con->prepare($queryBranch);
$stmtBranch->execute();
$rowBranch = $stmtBranch->fetch(PDO::FETCH_ASSOC);

$branchName = $rowBranch['branch_name'];
$branchAddress = $rowBranch['address'];

$fromDateDisp = date("d M Y", strtotime($startDate));
$toDateDisp = date("d M Y", strtotime($endDate));

$reportTitle = 'Products Daily Sale Report';
$pdf = new LB_PDF('P', false, $branchName, $branchAddress, $reportTitle, $fromDateDisp, $toDateDisp, true, $subTitle);
$pdf->SetMargins(13, 10, 13);
$pdf->AliasNbPages();
$pdf->AddPage();


$total_sale = 0;

$total_cost_price = 0;

$pdf->SetAligns(array('L', 'L', 'L', 'L'));
$pdf->SetWidths(array(15, 55, 55, 45));
$pdf->AddTableCaption($subTitle);

$titlesArray = array("S.No", "Company Name", "Product Name", $lastColumnTitle);
$pdf->AddTableHeader($titlesArray);

$stmt = $con->prepare($query);
$stmt->execute();

$serial = 0;
$category = "";
$product = "";
$quantity = 0;
$total_discount = 0;
$total_amount = 0;
$count = 1;
$discount = 0;
$purchasePrice = 0;
$totalCostPrice = 0;
$totalSale = 0;
$totalPurchasePrice = 0;
$totalDiscount = 0;
$totalProfit = 0;
$totalQuantity = 0;

$pdf->SetAligns(array('L', 'L', 'L', 'R'));
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $serial++;
    $category = $row['vendor_name'];
    $product = $row['product_name'];
    $quantity = $row['total'];
    $d = array($serial, $category, $product, $quantity);
    $pdf->AddRow($d);
    $totalQuantity = $totalQuantity + $quantity;

}

$pdf->SetAligns(array('L', 'R'));
$pdf->SetWidths(array(55, 45));
$pdf->SetX(83);
$pdf->AddRow(array('Grand Total', $totalQuantity), true, true);

$pdf->Ln();

$pdf->Ln(10);
$pdf->AddRow(array('Admin Signature'), false);
$pdf->Line(45, $pdf->GetY() - 1, 90, $pdf->GetY() - 1);

$pdf->Ln(10);
$pdf->AddRow(array('Branch Incharge'), false);
$pdf->Line(45, $pdf->GetY() - 1, 90, $pdf->GetY() - 1);

$pdf->Output('I', 'print_branch_daily_sale_beteen_two_dates.pdf');
?>