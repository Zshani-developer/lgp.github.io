<?php 
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $branchId = $_POST['select_branch'];
    $Date = changeDateToMysql($_POST['date']);
    $productIds = $_POST['productIds'];
    $prices = $_POST['prices'];
    $quantities = $_POST['quantities'];

    $totals = $_POST['totals'];
    $discounts = $_POST['discounts'];
    $netAmounts = $_POST['netAmounts'];
    $salemenIds = $_POST['salemenIds'];

    $totalDiscount = 0;
    $totalnetAmount = 0;
    $createdBy = $_SESSION['user_id'];
    $createdAt = date('Y-m-d H:i:s');

    $size = sizeof($discounts);
    for($i = 0; $i < $size; $i++) {
        $totalDiscount = $totalDiscount + $discounts[$i];
        $totalnetAmount = $totalnetAmount + $netAmounts[$i];
    }

    $message = '';
    try {

        $con->beginTransaction();

        $query = "INSERT INTO `daily_sales`(`branch_id`, 
        `transaction_date`, `total_discount`, 
        `total_net_amount`, `created_by`, 
        `created_at`)
        VALUES ($branchId, 
        '$Date', $totalDiscount, 
        $totalnetAmount, $createdBy, 
        '$createdAt');";
        $stmt = $con->prepare($query);
        $stmt->execute();

        $lastInsertId = $con->lastInsertId();
        $size = sizeof($salemenIds);
        for($i = 0; $i < $size; $i++) {
            $currentsalemenId = $salemenIds[$i];
            $currentproductId = $productIds[$i];
            $currentquantity = $quantities[$i];

            $querypurchaseprice = "select `purchase_price` from `products` where `id` = $currentproductId;";
            $stmtp = $con->prepare($querypurchaseprice);
            $stmtp->execute();
            $r = $stmtp->fetch(PDO::FETCH_ASSOC);
            $currentpurchasePrice = $r['purchase_price'];

            $currentsalePrice = $prices[$i];
            $currentdiscount = $discounts[$i];
            $currentnetAmount = $netAmounts[$i];

            $query = "INSERT INTO `daily_sales_details`
            (`daily_sale_id`, `saleman_id`, `product_id`, 
            `quantity`, `purchase_price`, `sale_price`,
            `discount`, `total_net_amount`)
            VALUES ($lastInsertId, $currentsalemenId, $currentproductId, 
            $currentquantity, $currentpurchasePrice, $currentsalePrice, 
            $currentdiscount, $currentnetAmount);";
            $stmt = $con->prepare($query);
            $stmt->execute();
            
            //and who will subtract the quantity form
            // the branch stock table
            $queryStock = "update `branch_stock` set 
	`quantity` = `quantity` - $currentquantity 
	where `product_id` = $currentproductId and 
	`branch_id` = $branchId;";
                $stmtStock = $con->prepare($queryStock);
                $stmtStock->execute();
            
        }

        $con->commit();

        $message = 'Daily sale has been saved successfully.';

    } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }
    $goto = "sales";
    header("location:congratulation?go_to=".$goto."&success_message=".$message); 
    exit;    

}


$userTypeId = $_SESSION['user_type_id'];
$allBranches = "";
if($userTypeId == "MASTER_USER") {
    $allBranches = getBranches($con);
} else {
    $allBranches = getLoggedInUserBranch($con);
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'config/site-css.php';?>
    </head>
    <body>
        <!-- topbar starts -->
        <?php include 'config/top-bar.php';?>
        <!-- topbar ends -->
        <div class="ch-container">
            <div class="row">
                <!-- left menu starts -->
                <?php include 'config/sidebar.php';?>
                <!--/span-->
                <!-- left menu ends -->



                <div id="content" class="col-lg-10 col-sm-10">
                    <!-- content starts -->
                    <div>
                        <ul class="breadcrumb">
                            <li>
                                <a href="#">Daily Sale</a>
                            </li>
                            <li>
                                <a href="#">Sale</a>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="box col-md-12">
                            <div class="box-inner">
                                <div class="box-header well" data-original-title="">
                                    <h2>Sale</h2>
                                    <div class="box-icon">
                                        <a href="#" class="btn btn-minimize btn-round btn-default">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </a>

                                    </div>
                                </div>
                                <div class="box-content">
                                    <div class="box-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                    <label>Branches</label>
                                                    <select id="select_branch" name="select_branch" required class="form-control" data-rel="chosen">
                                                        <?php echo $allBranches;?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                    <label>Transaction Date</label>
                                                    <input type="text" class="form-control datemask" id="date" name="date" maxlength="50" required>
                                                    <div class="clearfix">&nbsp;</div>
                                                </div>
                                                <div class="clearfix">&nbsp;</div>

                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                    <label>Product Code</label>
                                                    <input type="text" class="form-control" id="product_code" maxlength="50">
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>In Stock</label>
                                                    <input type="text" readonly class="form-control" id="in_stock" maxlength="50" value="0">
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Price</label>
                                                    <input type="text" readonly class="form-control" id="price" maxlength="50" value="0">
                                                </div>

                                                <div class="clearfix">&nbsp;</div>
                                                <div class="clearfix">&nbsp;</div>
                                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-12">
                                                    <label>QTY</label>
                                                    <input type="text" class="form-control" id="quantity" maxlength="50">
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Total</label>
                                                    <input type="text" readonly class="form-control" id="total" maxlength="50" value="0">
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Discount Type</label>
                                                    <select id="discount_type" class="form-control" data-rel="chosen">
                                                        <option value="">Select Type</option>
                                                        <option value="1">Fix amount</option>
                                                        <option value="2">Percentage</option>
                                                    </select>
                                                </div>
                                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-12">
                                                    <label>Disc%</label>
                                                    <input type="text" class="form-control" id="discount" maxlength="50">
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Net Amount</label>
                                                    <input type="text" readonly class="form-control" id="net_amount" maxlength="50" value="0">
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                    <label>&nbsp;</label>
                                                    <label>Salemen</label>
                                                    <select id="salemen" class="form-control" data-rel="chosen">

                                                    </select>
                                                </div>

                                                <div class="col-lg-1 col-md-1 col-sm-6 col-xs-12">
                                                    <label>&nbsp;</label>
                                                    <button type="button" id="add" name="add"
                                                            class="btn btn-primary btn-block"
                                                            >
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                    </button>
                                                </div>


                                                <div class="clearfix">&nbsp;</div>
                                                <div class="clearfix">&nbsp;</div>
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                                                    <table id="sale_table" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>S.No</th>
                                                                <th>Branch</th>
                                                                <th>Price</th>
                                                                <th>Quantity</th>
                                                                <th>Total</th>
                                                                <th>Disc%</th>
                                                                <th>Disc. Amt</th>
                                                                <th>Net Amount</th>
                                                                <th>Salemen</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="add_list">

                                                        </tbody>
                                                    </table>

                                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                                        <div class="clearfix">&nbsp;</div>
                                                        <label>Grand Total</label>
                                                        <input type="text" id="grand_total" 
                                                               class="form-control" readonly 
                                                               value="0"
                                                               />
                                                    </div>


                                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                                        <div class="clearfix">&nbsp;</div>
                                                        <label>&nbsp;</label>
                                                        <button type="button" id="print" name="print"
                                                                class="btn btn-primary btn-block"
                                                                >Print</button>
                                                    </div>

                                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                                        <div class="clearfix">&nbsp;</div>
                                                        <label>&nbsp;</label>
                                                        <button type="submit" id="submit" name="submit" disabled
                                                                class="btn btn-primary btn-block"
                                                                data-toggle="confirmation" data-placement="top" title="" data-original-title="Are you sure?">Save</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- content ends -->
                </div><!--/#content.col-md-0-->
            </div><!--/fluid-row-->

            <hr>

            <?php include 'config/site-js.php';?>
            <?php include 'config/footer.php';?>
            <?php 
            $userTypeId = $_SESSION['user_type_id'];
            ?>
        </div><!--/.fluid-container-->

        <script>
            let addSerial = 0;
            let userTypeId = '<?php echo $userTypeId;?>';

            $(".datemask").datepicker({
                autoclose: true
            });

            $(document).ready(function(){

                $("#product_code").blur(function(){

                    let branchId = $('#select_branch').val();
                    let productCode = $(this).val().trim();

                    if(productCode !== '' && branchId !== '') {
                        let jsonPhp = checkQuantityAndSalePrice(productCode, branchId);
                        //console.log(jsonPhp);
                        json = JSON.parse(jsonPhp);

                        $("#in_stock").val(json.quantity);
                        $("#price").val(json.salePrice);
                    }
                });


                $("#quantity").blur(function(){
                    let salePrice = $('#price').val() * 1;
                    let qty = $('#quantity').val().trim() * 1;

                    let total = salePrice * qty;
                    $("#total").val(total);
                });

                $("#discount").change(function(){
                    let total = $('#total').val() * 1;
                    let discount = $('#discount').val().trim() * 1;
                    let discountType = $('#discount_type').val();
                    let discountAmt = 0;
                    if(discountType == 2){
                        discountAmt = discount/100 * total;
                        $("#net_amount").val(total - discountAmt);
                    }
                    else{
                        discountAmt = total - discount;
                        $("#net_amount").val(discountAmt);
                    }

                });


                $("#add").click(function() {
                    let branchId  = $("#select_branch").val();
                    let branchName  = $('#select_branch :selected').text();
                    let date  = $("#date").val();
                    let productCode  = $("#product_code").val();
                    let price  = $("#price").val();
                    let quantity  = $("#quantity").val() * 1;
                    let total  = $("#total").val();
                    let discType  = $('#discount_type :selected').text();
                    let discount  = $('#discount').val().trim();
                    let netAmount  = $('#net_amount').val().trim();
                    let salemenId  = $('#salemen').val();
                    let salemen  = $('#salemen :selected').text();
                    let inStock = $('#in_stock').val() * 1;
                    let discountAmount = total - netAmount;

                    if(branchId !== '' && quantity !== '' 
                       && discount !== '' && salemenId !== '' 
                       && discType !== "" && inStock > 0 
                       && quantity <= inStock) {

                        addSerial++;

                        let newData = '<tr>';
                        newData = newData + '<td>' + addSerial + '</td>';

                        let productInput = "<input class='product_ids' type='hidden' name='productIds[]' value='" + productCode + "' />";
                        let priceInput = "<input class='prices' type='hidden' name='prices[]' value='" + price + "' />";
                        let quantitiesInput = "<input class='quantities' type='hidden' name='quantities[]' value='" + quantity + "' />";
                        let totalInput = "<input class='totals' type='hidden' name='totals[]' value='" + total + "' />";
                        let discountsInput = "<input type='hidden' class='discounts' name='discounts[]' value='" + discountAmount + "' />";
                        let netAmountInput = "<input type='hidden' class='netAmounts' name='netAmounts[]' value='" + netAmount + "' />";
                        let salemenIdsInput = "<input type='hidden' name='salemenIds[]' value='" + salemenId + "' />";

                        newData = newData + '<td>' + branchName + productInput + priceInput + quantitiesInput + totalInput + discountsInput + netAmountInput + salemenIdsInput +'</td>';
                        newData = newData + '<td>' + price + '</td>';
                        newData = newData + '<td>' + quantity + '</td>';
                        newData = newData + '<td>' + total + '</td>';
                        newData = newData + '<td>' + discType + '</td>';
                        newData = newData + '<td>' + discountAmount + '</td>';
                        newData = newData + '<td class="common_total">' + netAmount + '</td>';
                        newData = newData + '<td>' + salemen + '</td>';

                        let btnDel = "<button onclick='deleteRow(this, \"sale_table\")' type='button' class='btn btn-danger'>x</button>"

                        newData = newData + '<td>' + btnDel + '</td>';
                        newData = newData + '</tr>';
                        let oldData = $("#add_list").html();
                        newData = oldData + newData;

                        $("#add_list").html(newData);
                        calculateTotal();

                        $('#product_code').val('');
                        $('#in_stock').val('0');
                        $('#price').val('0');
                        $('#quantity').val('');
                        $('#total').val('0');
                        $('#discount').val('');
                        $('#net_amount').val('0');
                        $('#salemen').val('');
                        $('#salemen').trigger("chosen:updated");
                        $('#discount_type').val('');
                        $('#discount_type').trigger("chosen:updated");

                    } else {
                        showCustomMessage("Information!", 'Empty data or quantity is greater than available quantity.', "error");
                    }

                });

                $("#select_branch").change(function() {
                    let branchId = $('#select_branch').val();
                    if(branchId !== '') {
                        getSalemen(branchId);
                    }
                });
                if(userTypeId === "BRANCH_USER") {
                    $("#select_branch").trigger("change");
                    $('#salemen').trigger("chosen:updated");
                }

                //print button coding starts
                $("#print").click(function() {
                    let branchName = $('#select_branch :selected').text();
                    let transactionDate = $("#date").val();
                    let productIds = [];
                    let prices = [];
                    let quantities = [];
                    let discounts = [];
                    let totals = [];
                    let netAmounts = [];
                   
                    let productId = 0;
                    let price = 0;
                    let quantity = 0;
                    let discount = 0;
                    let total = 0;
                    let netAmount = 0;

                    $(".product_ids").each(function() {
                        productId = $(this).val();
                        productIds.push(productId);
                    });
                    
                    $(".prices").each(function() {
                        price = $(this).val();
                        prices.push(price);
                    });
                    $(".quantities").each(function() {
                        quantity = $(this).val();
                        quantities.push(quantity);
                    });
                    $(".discounts").each(function() {
                        discount = $(this).val();
                        discounts.push(discount);
                    });
                    $(".totals").each(function() {
                        total = $(this).val();
                        totals.push(total);
                    });
                    $(".netAmounts").each(function() {
                        netAmount = $(this).val();
                        netAmounts.push(netAmount);
                    });
                    
                    let url = "print_receipt?";
                    url = url + "branch_name=" + branchName;
                    url = url + "&date=" + transactionDate;
                    url = url + "&product_ids=" + productIds;
                    url = url + "&prices=" + prices;
                    url = url + "&quantities=" + quantities;
                    url = url + "&discounts=" + discounts;
                    url = url + "&totals=" + totals;
                    url = url + "&netAmounts=" + netAmounts;
                    
                    let win = window.open(url, "_blank");
                    
                    if(win) {
                        win.focus();
                    } else {
                        showCustomMessage("Information!", 'Please allow popups for this site.', "error");
                    }
                    $("#submit").removeAttr("disabled");
                });
                //print button coding ends


            });
            function deleteRow(obj, tableId) {
                let index = obj.parentNode.parentNode.rowIndex;
                document.getElementById(tableId).deleteRow(index);
                reArrangeSerials(tableId);
                calculateTotal();
            }

            function reArrangeSerials(tableId) {
                let i = 0;
                let k = 1;
                var size = document.getElementById(tableId).rows.length - 1;
                let my_table = document.getElementById(tableId);
                for (; i < size; i++, k++) {
                    my_table.rows[k].cells[0].innerHTML = k;
                }

                if(tableId === "sale_table") {
                    addSerial = k - 1;
                }
            }


            function calculateTotal() {
                let grandTotal = 0;
                $(".common_total").each(function() {
                    let curAmount = $(this).html() * 1;
                    grandTotal = grandTotal + curAmount;
                });

                $("#grand_total").val(grandTotal);
            }
        </script>

        <!-- external javascript -->


    </body>
</html>
