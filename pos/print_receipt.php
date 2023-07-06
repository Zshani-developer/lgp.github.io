<?php
include("./config/connection.php");
include('./pdflib/logics-builder-pdf.php');

$title = "Receipt";
$subTitle = "Customer Receipt";

$today = date('d-M-Y');
$today = 'Generated On '.$today;
$pdf = new LB_PDF('P', false, $title, $today, '', true, $subTitle);
$pdf->SetMargins(13, 20, 13); //LTR, mm
$pdf->AliasNbPages();
$pdf->AddPage();

$branchName = $_GET['branch_name'];
$date = $_GET['date'];
$product_ids = $_GET['product_ids'];
$prices = $_GET['prices'];
$quantities = $_GET['quantities'];
$discounts = $_GET['discounts'];
$totals = $_GET['totals'];
$netAmounts = $_GET['netAmounts'];

$pdf->SetWidths(array(100));
$pdf->SetAligns(array('L'));
$pdf->Ln();
$d1 = array('Branch Name: '.$branchName);
$d2 = array("Transaction Date: ". $date);
$pdf->AddRow($d1, false);
$pdf->Ln();
$pdf->AddRow($d2, false);
$pdf->Ln();
$pdf->SetWidths(array(13, 50, 25, 
                      25, 25, 25, 25));
$pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'L', 'L'));

$pdf->AddTableCaption('Product Details');
$titlesArr = array('S.No', 'Product Name', 'Quantity', 
                   'Price', 'Total', 'Discount', 
                   'Net Amount');
$pdf->AddTableHeader($titlesArr);

$pdf->SetAligns(array('L', 'L', 'C', 'R', 'R', 'R', 'R'));

/*
$b = "a-b-c";
$cc = explode("-", $b); //will produce an array like below
[a, b, c]
*/
$productIds = explode(",", $product_ids);
$prices = explode(",", $prices);
$quantities = explode(",", $quantities);
$discounts = explode(",", $discounts);
$totals = explode(",", $totals);
$netAmounts = explode(",", $netAmounts);

$size = sizeof($productIds);

$serial = 1;
$totalAmount = 0;
$totalDiscount = 0;
$netToPay = 0;
for($i = 0; $i < $size; $i++, $serial++) {
    $currentProductId = $productIds[$i];

    $queryProduct = "select `product_name` 
    from `products` 
    where `id` = $currentProductId;";

    $stmt = $con->prepare($queryProduct);
    $stmt->execute();
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    $productName = $r['product_name'];

    $data = array($serial, $productName, $quantities[$i], 
                  $prices[$i], $totals[$i], 
                  $discounts[$i], $netAmounts[$i]);
    $pdf->AddRow($data);

    $totalAmount = $totalAmount + $totals[$i];
    $totalDiscount = $totalDiscount + $discounts[$i];
    $netToPay = $netToPay + $netAmounts[$i];

}

$pdf->SetWidths(array(25, 25));
$pdf->SetAligns(array('L', 'R'));
$d1 = array("Total", $totalAmount);
$d2 = array("Total Discount", $totalDiscount);
$d3 = array("Net To Pay", $netToPay);

$pdf->SetX(151);
$pdf->AddRow($d1, true, true);
$pdf->SetX(151);
$pdf->AddRow($d2, true, true);
$pdf->SetX(151);
$pdf->AddRow($d3, true, true);

$pdf->Output('I', 'print_receipt.pdf');
?>