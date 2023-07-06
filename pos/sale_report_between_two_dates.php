<?php 
include 'config/connection.php';
include 'common_service/common_functions.php';
if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}
$allBranches = getBranches($con);
$allVendors = getVendors($con);

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
                                <a href="#">Home</a>
                            </li>
                            <li>
                                <a href="#">Dashboard</a>
                            </li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="box col-md-12">
                            <div class="box-inner">
                                <div class="box-header well" data-original-title="">
                                    <h2>Between Two Dates Report</h2>
                                    <div class="box-icon">
                                        <a href="#" class="btn btn-minimize btn-round btn-default">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </a>

                                    </div>
                                </div>
                                <div class="box-content">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class=" col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>Start Date</label>
                                                <input type="text" class="form-control datemask" id="start_date" name="start_date">
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>End Date</label>
                                                <input type="text" class="form-control datemask" id="end_date" name="end_date">
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>Branch</label>
                                                <select name="select_branch" id="select_branch" class="form-control" data-rel="chosen">
                                                    <?php echo $allBranches?>
                                                </select>
                                            </div>

                                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                                                <label>&nbsp;</label>
                                                <button id="print" type="button" class="btn btn-primary btn-block">Print</button>
                                            </div>
                                        </div>                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix">&nbsp;</div>
                    <div class="row">
                        <div class="box col-md-12">
                            <div class="box-inner">
                                <div class="box-header well" data-original-title="">
                                    <h2>Between Two Dates Report Quantity and Amount Based</h2>
                                    <div class="box-icon">
                                        <a href="#" class="btn btn-minimize btn-round btn-default">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </a>

                                    </div>
                                </div>
                                <div class="box-content">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>Start Date</label>
                                                <input type="text" class="form-control datemask" id="start_date_qty">
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>End Date</label>
                                                <input type="text" class="form-control datemask" id="end_date_qty">
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>Branch</label>
                                                <select id="branch_quantity_based" class="form-control" data-rel="chosen">
                                                    <?php echo $allBranches;?>
                                                </select>
                                            </div>
                                        </div> 
                                        <div class="row">
                                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                                                <div class="clearfix">&nbsp;</div>
                                                <button type="button" class="btn btn-primary" id="print_quantity_based">Quantity Based</button>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                                                <div class="clearfix">&nbsp;</div>
                                                <button type="button" class="btn btn-primary" id="print_amount_based">Amount Based</button>
                                            </div>
                                        </div>                                   
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="row">
                        <div class="box col-md-12">
                            <div class="box-inner">
                                <div class="box-header well" data-original-title="">
                                    <h2>Between Two Dates Report Company Based</h2>
                                    <div class="box-icon">
                                        <a href="#" class="btn btn-minimize btn-round btn-default">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </a>

                                    </div>
                                </div>
                                <div class="box-content">
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>Start Date</label>
                                                <input type="text" class="form-control datemask" id="from_date_category_based">
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>End Date</label>
                                                <input type="text" class="form-control datemask" id="to_date_category_based">
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>Branch</label>
                                                <select id="branch_category_based" class="form-control" data-rel="chosen">
                                                    <?php echo $allBranches;?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>Vendor</label>
                                                <select id="category_id" class="form-control" data-rel="chosen">
                                                    <?php echo $allVendors;?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                                                <div class="clearfix">&nbsp;</div>
                                                <button type="button" class="btn btn-primary" id="print_category_based_single">Single</button>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                                                <div class="clearfix">&nbsp;</div>
                                                <button id="print_category_based_summary" type="button" class="btn btn-primary">Summary</button>
                                            </div>
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

        </div><!--/.fluid-container-->
        <script>

            $(".datemask").datepicker({
                autoclose: true
            });

            $(document).ready(function () {

                $("#print").click(function (e) {
                    var branch_id = $("#select_branch").val();
                    var start_date = $("#start_date").val();
                    var end_date = $("#end_date").val();
                    if (branch_id != "" && start_date != "" && end_date != "") {
                        window.open("print_two_dates_sale.php?branch_id=" + branch_id + "&start_date=" + start_date + "&end_date=" + end_date, "_blank");
                    } else {
                        showCustomMessage("Warning", "Please fill all fields.", 'error');
                    }
                });
                
                
                $("#print_quantity_based").click(function () {
                    var branch_id = $("#branch_quantity_based").val();
                    var start_date = $("#start_date_qty").val();
                    var end_date = $("#end_date_qty").val();
                    if (branch_id !== "" && start_date !== "" && end_date !== "") {
                        window.open("print_two_dates_sale_quantity_based?branch_id=" + branch_id
                                + "&start_date=" + start_date + "&end_date=" + end_date + "&type=QUNTITY_BASED");
                    } else {
                        showCustomMessage("Warning", "Please fill all fields.", 'error');
                    }
                });

                $("#print_amount_based").click(function () {
                    var branch_id = $("#branch_quantity_based").val();
                    var start_date = $("#start_date_qty").val();
                    var end_date = $("#end_date_qty").val();
                    
                    if (branch_id !== "" && start_date !== "" && end_date !== "") {
                        window.open("print_two_dates_sale_quantity_based?branch_id=" + branch_id
                                + "&start_date=" + start_date + "&end_date=" + end_date + "&type=AMOUNT_BASED");
                    } else {
                        showCustomMessage("Warning", "Please fill all fields.", 'error');
                    }
                });
                
                
                 $("#print_category_based_summary").click(function () {
                    var branch_id = $("#branch_category_based").val();
                    var start_date = $("#from_date_category_based").val();
                    var end_date = $("#to_date_category_based").val();
                    if (branch_id !== "" && start_date !== "" && end_date !== "") {
                        window.open("print_two_dates_sale_company_based?branch_id=" + branch_id
                                + "&start_date=" + start_date + "&end_date=" + end_date);
                    } else {
                        showCustomMessage("Warning", "Please fill all fields.", 'error');
                    }
                });

                $("#print_category_based_single").click(function () {
                    var branch_id = $("#branch_category_based").val();
                    var category_id = $("#category_id").val();
                    var start_date = $("#from_date_category_based").val();
                    var end_date = $("#to_date_category_based").val();
                    if (branch_id !== "" && start_date !== "" && end_date !== "" && category_id !== "") {
                        window.open("print_two_dates_sale_company_based_single?branch_id=" + branch_id
                                + "&start_date=" + start_date + "&end_date=" + end_date + "&category_id=" + category_id);
                    } else {
                        showCustomMessage("Warning", "Please fill all fields.", 'error');
                    }
                });
                

            });
        </script>
        <!-- external javascript -->


    </body>
</html>
