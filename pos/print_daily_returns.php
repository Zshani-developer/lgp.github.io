<?php 
include("./config/connection.php");
include('./pdflib/logics-builder-pdf.php');
include 'common_service/common_functions.php';

$branchId = $_GET['branch_id'];
$start = $_GET['start'];
$end = $_GET['end'];
$branchName = $_GET['branch_name'];

$startMysql = changeDateToMysql($start);
$endMysql = changeDateToMysql($end);

$title = "Returns";
$subTitle = "Daily Returns";

$today = date('d-M-Y');
$today = 'Generated On '.$today;
$pdf = new LB_PDF('P', false, $title, $start, $end, true, $subTitle);
$pdf->SetMargins(15, 20, 13); //LTR, mm
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->Ln();
$pdf->SetWidths(array(50, 50));
$pdf->SetAligns(array('L', 'L'));
$d = array("Branch Name: ".$branchName);
$pdf->AddRow($d,false);
$pdf->Ln();

$query = "select `d`.`branch_id`, `d`.`transaction_date`, 
`dd`.`daily_sale_id`, `dd`.`product_id`, 
`dd`.`quantity`, `dd`.`total_net_amount`, 
`p`.`product_name` 
from `daily_sales` as `d`, `daily_sales_details` as `dd`, 
`products` as `p` 
where `d`.`transaction_date` between '$startMysql' and '$endMysql' and 
`d`.`id` = `dd`.`daily_sale_id` and 
`d`.`branch_id` = $branchId and 
`dd`.`product_id` = `p`.`id` and 
`dd`.`total_net_amount` < 0 
order by `d`.`transaction_date` asc;";

$stmt = $con->prepare($query);
$stmt->execute();

$pdf->SetWidths(array(15, 50, 40, 30, 50));
$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
$titlesArr = array('S.No', 'Product name', 'Transactin date', 'Quantity', 'Total net amount');
$pdf->AddTableHeader($titlesArr);
$pdf->SetAligns(array('L', 'L', 'L', 'L', 'R'));

$serial = 0;
$totalnetAmount = 0;
while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $serial++;
    $singleAmount = (-1 * $r['total_net_amount']);
    $data = array($serial, 
                 $r['product_name'],
                  $r['transaction_date'],
                  $r['quantity'],
                  $singleAmount);
    $pdf->AddRow($data);
    $totalnetAmount = $totalnetAmount + $singleAmount;
}

$pdf->SetWidths(array(30,20));
$pdf->SetAligns(array('L','R'));
$d1 = array("Total amount",$totalnetAmount);
$pdf->SetX(150);
$pdf->AddRow($d1, true, true);

$pdf->Output('I', 'daily_returns_print.pdf');
?>