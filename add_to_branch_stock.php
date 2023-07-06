<?php 
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {
    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $productIds = $_POST['productIds'];
    $branchIds = $_POST['branchIds'];
    $purchasedQtys = $_POST['purchasedQtys'];

    $message = '';
    try {

        $con->beginTransaction();

        $size = sizeof($branchIds);
        for($i = 0; $i < $size; $i++) {
            $currentbranchId = $branchIds[$i];
            $currentproductId = $productIds[$i];
            $currentpurchasedQty = $purchasedQtys[$i];

            //logic
            $queryS = "select count(*) as `count` 
            from `branch_stock` 
            where `product_id` = $currentproductId and 
            `branch_id` = $currentbranchId;";
            $stmtS = $con->prepare($queryS);
            $stmtS->execute();

            $r = $stmtS->fetch(PDO::FETCH_ASSOC);
            $count = $r['count'];

            if($count > 0) {
                $query = "update `branch_stock` set 
	`quantity` = `quantity` + $currentpurchasedQty 
	where `product_id` = $currentproductId and 
	`branch_id` = $currentbranchId;";
                $stmt = $con->prepare($query);
                $stmt->execute();
            } else {
                $query = "INSERT INTO `branch_stock`(`product_id`,
                    `branch_id`, `quantity`)
                    VALUES ($currentproductId, '$currentbranchId', '$currentpurchasedQty');";
                $stmt = $con->prepare($query);
                $stmt->execute();
            }

        }
        $con->commit();

        $message = 'Branch stock has been saved successfully.';

    } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }
    $goto = "add_to_branch_stock";
    header("location:congratulation?go_to=".$goto."&success_message=".$message); 
    exit;
}

$allVendors = getVendors($con);
$allBranches = getBranches($con);

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
                                <a href="#">Products</a>
                            </li>
                            <li>
                                <a href="#">Add To Branch Stock</a>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="box  col-md-12 ">
                            <div class="box-inner">
                                <div class="box-header well" data-original-title="">
                                    <h2>Add To Branch Stock</h2>
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
                                                    <select id="select_branch" name="select_branch" class="form-control" data-rel="chosen">
                                                        <?php echo $allBranches;?>
                                                    </select>
                                                </div>
                                                <div class="clearfix">&nbsp;</div>
                                                <div class="clearfix">&nbsp;</div>
                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                    <label>Vendors</label>
                                                    <select id="select_vendor" class="form-control">
                                                        <?php echo $allVendors;?>
                                                    </select>
                                                </div>

                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                    <label>&nbsp;</label>
                                                    <label>Products</label>
                                                    <select id="products" name="products" class="form-control" >

                                                    </select>
                                                </div>

                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Available QTY</label>
                                                    <input type="text" readonly class="form-control" id="available_qty"  value="0">
                                                </div>

                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Purchased QTY</label>
                                                    <input type="text" class="form-control" id="purchased_qty" name="purchased_qty" maxlength="50" value="0">
                                                </div>

                                                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                                    <label>&nbsp;</label>
                                                    <button type="button" id="add"
                                                            class="btn btn-primary btn-block"
                                                            >
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                    </button>
                                                </div>





                                                <div class="clearfix">&nbsp;</div>

                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                                                    <table id="add_branch_table" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>S.No</th>
                                                                <th>Branch Name</th>
                                                                <th>Product Name</th>
                                                                <th>Purchased QTY</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="add_list">

                                                        </tbody>

                                                    </table>
                                                </div>

                                                <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                                    <div class="clearfix">&nbsp;</div>

                                                    <button type="submit" id="submit" name="submit" class="btn btn-primary btn-block" data-toggle="confirmation" data-placement="top" title="" data-original-title="Are you sure?">Save</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
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


        <script>
            let addSerial = 0;
            $(document).ready(function(){

                $("#select_vendor").change(function(){
                    let vendorId = $(this).val();

                    if(vendorId !== '') {
                        getVendorProducts(vendorId);
                    }
                });


                $("#products").change(function(){
                    let productId = $(this).val();
                    let branchId = $("#select_branch").val();
                    if(productId !== '' && branchId !== '') {
                        let availableQuantity = checkavailableQty(productId, branchId);
                        $("#available_qty").val(availableQuantity);
                    }
                });
            });


            $("#add").click(function() {
                let branchId  = $("#select_branch").val();
                let branchName  = $('#select_branch :selected').text();
                let productId  = $("#products").val();
                let productName  = $('#products :selected').text();
                let purchasedQty  = $('#purchased_qty').val().trim();
                if(branchId !== '' && productId !== '' && purchasedQty !== '' && purchasedQty > 0) {

                    addSerial++;

                    let newData = '<tr>';
                    newData = newData + '<td>' + addSerial + '</td>';

                    let branchInput = "<input type='hidden' name='branchIds[]' value='" + branchId + "' />";
                    let productInput = "<input type='hidden' name='productIds[]' value='" + productId + "' />";
                    let purchasedInput = "<input type='hidden' name='purchasedQtys[]' value='" + purchasedQty + "' />";

                    newData = newData + '<td>' + branchName + branchInput + productInput + purchasedInput +'</td>';
                    newData = newData + '<td>' + productName + '</td>';
                    newData = newData + '<td>' + purchasedQty + '</td>';

                    let btnDel = "<button onclick='deleteRow(this, \"add_branch_table\")' type='button' class='btn btn-danger'>X</button>"

                    newData = newData + '<td>' + btnDel + '</td>';
                    newData = newData + '</tr>';
                    let oldData = $("#add_list").html();
                    newData = oldData + newData;

                    $("#add_list").html(newData);

                    //$('#select_branch').val('');
                    $('#select_vendor').val('');
                    $('#products').val('');
                    $('#purchased_qty').val('0');
                    $('#available_qty').val('0');
                } else {
                    showCustomMessage("Information!", 'Empty data can not be added.', "error");
                }


            });


            function deleteRow(obj, tableId) {
                let index = obj.parentNode.parentNode.rowIndex;
                document.getElementById(tableId).deleteRow(index);
                reArrangeSerials(tableId);
            }

            function reArrangeSerials(tableId) {
                let i = 0;
                let k = 1;
                var size = document.getElementById(tableId).rows.length - 1;
                let my_table = document.getElementById(tableId);
                for (; i < size; i++, k++) {
                    my_table.rows[k].cells[0].innerHTML = k;
                }

                if(tableId === "add_branch_table") {
                    addSerial = k - 1;
                }
            }
        </script>

        <!-- external javascript -->


    </body>
</html>
