<?php 
include("./config/connection.php");
include('./pdflib/logics-builder-pdf.php');
$branchId = $_GET['branch_id'];

$title = "Worth Report";
$subTitle = "Stock";

$today = date('d-M-Y');
$today = 'Generated On '.$today;
$pdf = new LB_PDF('P', false, $title, $today, '', true, $subTitle);
$pdf->SetMargins(15, 20, 13); //LTR, mm
$pdf->AliasNbPages();
$pdf->AddPage();


$query = "select 
ifnull(SUM(`p`.`purchase_price` * `bs`.`quantity`), 0) as `total_cost`, 
ifnull(sum(`p`.`sale_price` * `bs`.`quantity`), 0)  as `total_sale`  
from `products` as `p`, 
`branch_stock` as `bs` 
where `bs`.`branch_id` = $branchId and 
`bs`.`product_id` = `p`.`id` 
group by `bs`.`branch_id`;";

$stmt = $con->prepare($query);
$stmt->execute();

$pdf->Ln();

$pdf->SetWidths(array(70, 50));
$pdf->SetAligns(array('L', 'R'));
$count = $stmt->rowCount();
$totalCost = 0;
$totalSale = 0;
$difference = 0;
if($count > 0) {
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalCost = $r['total_cost'];
    $totalSale = $r['total_sale'];
    $difference = $totalSale - $totalCost;
}

$d1 = array("Total Cost Price", $totalCost);
$d2 = array("Total Sale Price", $totalSale);
$d3 = array("Difference", $difference);

$pdf->AddRow($d1);
$pdf->AddRow($d2);
$pdf->AddRow($d3);
$pdf->Output('I', 'worth_report.pdf');
?>