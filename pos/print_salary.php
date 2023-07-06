<?php 
include("./config/connection.php");
include('./pdflib/logics-builder-pdf.php');
include 'common_service/common_functions.php';

$branchId = $_GET['branch_id'];
$salemanId = $_GET['saleman_id'];
$start = $_GET['start'];
$end = $_GET['end'];
$salemanName = $_GET['saleman_name'];

$startMysql = changeDateToMysql($start);
$endMysql = changeDateToMysql($end);

$title = "Salary";
$subTitle = "Saleman Salary";

$today = date('d-M-Y');
$today = 'Generated On '.$today;
$pdf = new LB_PDF('P', false, $title, $start, $end, true, $subTitle);
$pdf->SetMargins(15, 20, 13); //LTR, mm
$pdf->AliasNbPages();
$pdf->AddPage();



$pdf->Ln();
$pdf->SetWidths(array(50, 50));
$pdf->SetAligns(array('L', 'L'));
$d = array("Saleman Name", $salemanName);
$pdf->AddRow($d, true, true);

$query = "select ifnull(sum(`dd`.`total_net_amount`), 0) as `total_sale` 
from `daily_sales` as `d`, `daily_sales_details` as `dd` 
where `d`.`transaction_date` between '$startMysql' and '$endMysql' and 
`d`.`id` = `dd`.`daily_sale_id` and 
`d`.`branch_id` = $branchId and 
`dd`.`saleman_id` = $salemanId";

$stmt = $con->prepare($query);
$stmt->execute();
$count = $stmt->rowCount();
$totalSale = 0;
if($count > 0) {
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalSale = $r['total_sale'];
}
$queryPercentage = "select `salary_percentage` 
from `sale_men` 
where `id` = $salemanId;";
$stmtPer = $con->prepare($queryPercentage);
$stmtPer->execute();
$r = $stmtPer->fetch(PDO::FETCH_ASSOC);
$percentage = $r['salary_percentage'];

$salary = ($percentage * $totalSale) / 100;
$salary = round($salary);

$pdf->SetWidths(array(50, 50));
$pdf->SetAligns(array('L', 'R'));


$d1 = array("Total Sale", $totalSale);
$d2 = array("Percentage", $percentage);
$d3 = array("Salary", $salary);
$pdf->AddRow($d1);
$pdf->AddRow($d2);
$pdf->AddRow($d3, true, true);

$pdf->Output('I', 'salary_print.pdf');
?>