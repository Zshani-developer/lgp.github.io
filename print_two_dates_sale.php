<?php
include("config/connection.php");
include("common_service/common_functions.php");
include ('./pdflib/logics-builder-pdf.php');

if (!(isset($_SESSION['user_id']))) {
    header("location:index");
    exit;
}

$branch_id = $_GET['branch_id'];

$queryBranch = "select * from `branches` 
where `id` = '$branch_id';";
$stmtBranch = $con->prepare($queryBranch);
$stmtBranch->execute();
$rowBranch = $stmtBranch->fetch(PDO::FETCH_ASSOC);

$branchName = $rowBranch['branch_name'];
$branchAddress = $rowBranch['address'];

$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$mysqlStart = changeDateToMysql($startDate);
$mysqlEnd = changeDateToMysql($endDate);

$fromDateDisp = date("d M Y", strtotime($startDate));
$toDateDisp = date("d M Y", strtotime($endDate));

$reportTitle = 'Products Daily Sale Report';
$pdf = new LB_PDF('P', false, $branchName, $branchAddress, $reportTitle, $fromDateDisp, $toDateDisp, true, '');
$pdf->SetMargins(13, 10, 13);
$pdf->AliasNbPages();
$pdf->AddPage();

$total_sale = 0;

$total_cost_price = 0;

$pdf->SetAligns(array('L', 'L', 'L', 'L', 'C', 'C', 'C', 'C'));
$pdf->SetWidths(array(13, 25, 35, 42, 18, 15, 18, 18));
$titlesArray = array("S.No", "Date", "Company Name", "Product Name", "P Price", "QTY", "Discount", "Net Amt");
$pdf->AddTableHeader($titlesArray);

$query = "SELECT `c`.`vendor_name`, `p`.`product_name`, 
date_format(`d`.`transaction_date`, '%d %b %Y') as `transaction_date`, 
`dd`.`purchase_price`, `dd`.`sale_price`, 
`dd`.`discount`, `dd`.`total_net_amount`, `dd`.`quantity`,
`dd`.`product_id` 
FROM `vendors` AS `c`, `products` AS `p`, 
`daily_sales` AS `d`, 
`daily_sales_details` as `dd` 
WHERE `c`.`id` = `p`.`vendor_id` AND 
`p`.`id` = `dd`.`product_id` AND 
`d`.`branch_id` = $branch_id AND 
`d`.`id` = `dd`.`daily_sale_id` and 
`d`.`transaction_date` between '$mysqlStart' and '$mysqlEnd' and 
`dd`.`total_net_amount` > 0 
ORDER BY `d`.`transaction_date` asc, `dd`.`product_id` ASC;";
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
$transactionDate = '';

$pdf->SetAligns(array('L', 'L', 'L', 'L', 'R', 'R', 'R', 'R'));

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $serial++;
    $category = $row['vendor_name'];
    $product = $row['product_name'];
    $quantity = $row['quantity'];
    $discount = $row['discount'];
    $purchasePrice = $row['purchase_price'];
    $salePrice = $row['sale_price'];
    $netAmount = $row['total_net_amount']; 
    $transactionDate = $row['transaction_date']; 

    $totalSale = $totalSale + $netAmount;
    $totalDiscount = $totalDiscount + $discount;
    $totalQuantity = $totalQuantity + $quantity;
    $totalPurchasePrice = $totalPurchasePrice + ($purchasePrice * $quantity);
    $data = array($serial, $transactionDate, $category, $product, $salePrice, $quantity, $discount, $netAmount);
    $pdf->AddRow($data);

}

$pdf->SetAligns(array('R', 'R', 'R', 'R'));
$pdf->SetWidths(array(133, 15, 18, 18));
$pdf->AddRow(array('Grand Total', $totalQuantity, $totalDiscount, $totalSale), true, true);

$pdf->Ln();

$totalProfit = $totalSale - $totalPurchasePrice; 

$pdf->SetLeftMargin(127); 
$pdf->SetWidths(array(70));
$pdf->SetAligns(array('C'));
$pdf->AddTableHeader(array("Summary"));

$pdf->SetWidths(array(40, 30));
$pdf->SetAligns(array('L', 'R'));
$pdf->AddRow(array('Total Sale', $totalSale));
$pdf->AddRow(array('Total Discount', $totalDiscount));
$pdf->AddRow(array('Total Purchase Price', $totalPurchasePrice));
$pdf->AddRow(array('Total Profit', $totalProfit), true, true);

$pdf->SetLeftMargin(13);
$pdf->Ln(10);
$pdf->AddRow(array('Admin Signature'), false);
$pdf->Line(45, $pdf->GetY() - 1, 90, $pdf->GetY() - 1);

$pdf->Ln(10);
$pdf->AddRow(array('Branch Incharge'), false);
$pdf->Line(45, $pdf->GetY() - 1, 90, $pdf->GetY() - 1);

$pdf->Output('I', 'print_branch_daily_sale_beteen_two_dates.pdf');

?>