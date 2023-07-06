<?php 
include("./config/connection.php");
include('./pdflib/logics-builder-pdf.php');
$branchId = $_GET['branch_id'];
$type = $_GET['type'];

$title = "Zero Stock";
$subTitle = "Stock List";
if($type == "NON_ZERO") {
    $title = "Available Stock";
}
$today = date('d-M-Y');
$today = 'Generated On '.$today;
$pdf = new LB_PDF('P', false, $title, $today, '', true, $subTitle);
$pdf->SetMargins(15, 20, 13); //LTR, mm
$pdf->AliasNbPages();
$pdf->AddPage();


$query = "select `p`.`product_name`, `v`.`vendor_name`,
`v`.`contact_number`, `bs`.`quantity` 
 from `products` as `p`, 
`vendors` as `v`, `branch_stock` as `bs` 
where `p`.`vendor_id` = `v`.`id` and 
`p`.`id` = `bs`.`product_id` and 
`bs`.`branch_id` = $branchId and 
`bs`.`quantity` = 0 
order by `v`.`id` asc,
`p`.`product_name` asc;";

if($type == "NON_ZERO") {
    $query = "select `p`.`product_name`, `v`.`vendor_name`,
`v`.`contact_number`, `bs`.`quantity` 
 from `products` as `p`, 
`vendors` as `v`, `branch_stock` as `bs` 
where `p`.`vendor_id` = `v`.`id` and 
`p`.`id` = `bs`.`product_id` and 
`bs`.`branch_id` = $branchId and 
`bs`.`quantity` > 0 
order by `v`.`id` asc,
`p`.`product_name` asc;";
}

$stmt = $con->prepare($query);
$stmt->execute();

$pdf->SetWidths(array(15, 50, 50, 50, 25));
$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C'));
$titlesArr = array('S.No', 'vendor name', 'Contact', 'Product name', 'Quantity');
$pdf->AddTableHeader($titlesArr);
$pdf->SetAligns(array('L', 'L', 'L', 'L', 'R'));

$serial = 0;
while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $serial++;
    $data = array($serial, 
                 $r['vendor_name'],
                  $r['contact_number'],
                  $r['product_name'],
                  $r['quantity']);
    $pdf->AddRow($data);
}

$pdf->Output('I', 'zero_stock.pdf');
?>