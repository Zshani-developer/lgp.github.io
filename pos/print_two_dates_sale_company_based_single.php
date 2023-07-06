<?php
include("config/connection.php");
include("common_service/common_functions.php");
include ('./pdflib/logics-builder-pdf.php');

if (!(isset($_SESSION['user_id']))) {
    header("location:index");
    exit;
}

$branch_id = $_GET['branch_id'];
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$categoryId = $_GET['category_id'];

$subTitle = "Company Based (Single)";

$queryBranch = "select * from `branches` where `id` = '$branch_id';";
$stmtBranch = $con->prepare($queryBranch);
$stmtBranch->execute();
$rowBranch = $stmtBranch->fetch(PDO::FETCH_ASSOC);

$queryCategoryName = "SELECT `vendor_name` 
FROM `vendors` 
WHERE `id` = $categoryId;";
$stmtCategoryName = $con->prepare($queryCategoryName);
$stmtCategoryName->execute();
$rowCategoryName = $stmtCategoryName->fetch(PDO::FETCH_ASSOC);
$categoryName = $rowCategoryName['vendor_name'];

$branchName = $rowBranch['branch_name'];
$branchAddress = $rowBranch['address'];


$fromDateDisp = date("d M Y", strtotime($startDate));
$toDateDisp = date("d M Y", strtotime($endDate));

$reportTitle = 'Products Daily Sale Report';
$pdf = new LB_PDF('P', false, $branchName, $branchAddress, $reportTitle, $fromDateDisp, $toDateDisp, true, $subTitle);
$pdf->SetMargins(25, 10, 13);
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->AddTableCaption($categoryName);
$pdf->SetAligns(array('L', 'L', 'L', 'C'));
$pdf->SetWidths(array(15, 25, 70, 30));
$titlesArray = array("S.No", "Date", "Prodcut Name", "Total Sale");
$pdf->AddTableHeader($titlesArray);

$mysqlStart = changeDateToMysql($startDate);
$mysqlEnd = changeDateToMysql($endDate);
$branch_id = $_GET['branch_id'];
$categoryId = $_GET['category_id'];

$query = "select `q`.* from (
SELECT `d`.`transaction_date`, `p`.`product_name`, 
IFNULL(SUM(`dd`.`total_net_amount`), 0) as `total` 
FROM `vendors` AS `c`, `products` AS `p`, 
`daily_sales` AS `d`, 
`daily_sales_details` as `dd` 
WHERE `c`.`id` = `p`.`vendor_id` AND 
`p`.`vendor_id` = $categoryId and 
`p`.`id` = `dd`.`product_id` AND 
`d`.`branch_id` = $branch_id AND 
`d`.`id` = `dd`.`daily_sale_id` and 
`d`.`transaction_date` between '$mysqlStart' and '$mysqlEnd' 
GROUP BY `dd`.`product_id`, 
`d`.`transaction_date`, 
`p`.`product_name` 
)
as `q` order by `q`.`transaction_date` asc;";
$stmt = $con->prepare($query);
$stmt->execute();

$serial = 0;
$totalSale = 0;
$prodcutName = '';
$saleGrandTotal = 0;
$data = '';

$pdf->SetAligns(array('L', 'L', 'L', 'R'));
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $serial++;
    $prodcutName = $row['product_name'];
    $totalSale = $row['total'];
    $date = $row['transaction_date'];

    $saleGrandTotal = $saleGrandTotal + $totalSale;
    $d = array($serial, $date, $prodcutName, $totalSale);
    $pdf->AddRow($d);
}

$pdf->SetAligns(array('R', 'R'));
$pdf->SetWidths(array(110, 30));
$pdf->AddRow(array('Grand Total', $saleGrandTotal), true, true);

$pdf->Ln(10);
$pdf->SetAlpha(array('L'));
$pdf->SetWidths(array(30));
$pdf->AddRow(array('Admin Signature'), false);
$pdf->Line(60, $pdf->GetY() - 1, 100, $pdf->GetY() - 1);

$pdf->Ln(10);

$pdf->AddRow(array('Branch Incharge'), false);
$pdf->Line(60, $pdf->GetY() - 1, 100, $pdf->GetY() - 1);


$pdf->Output('I', 'print_branch_daily_sale_beteen_two_dates.pdf');
?>