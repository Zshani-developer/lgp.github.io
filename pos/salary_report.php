<?php 
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {
    header("location:index");
    exit;
}

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
                                <a href="#">Home</a>
                            </li>
                            <li>
                                <a href="#">Dashboard</a>
                            </li>
                        </ul>
                    </div>
                    <div class="clearfix">&nbsp;</div>
                    <div class="row">
                        <div class="box col-md-12">
                            <div class="box-inner">
                                <div class="box-header well" data-original-title="">
                                    <h2>Salary Report</h2>
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
                                                <label>Select Branch</label>
                                                <select id="select_branch" class="form-control" id="select_branch">
                                                    <?php echo $allBranches;?>
                                                </select>
                                            </div>

                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                <label>Select Saleman</label>
                                                <select id="salemen" class="form-control" id="select_branch">

                                                </select>
                                            </div>

                                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                                                <label>&nbsp;</label>
                                                <button id="print_salary" type="button" class="btn btn-primary btn-block"> <i class="glyphicon glyphicon-print"></i></button>

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

        </div>
        <script>

            $(document).ready(function() {
                $('[data-toggle=confirmation]').confirmation();

                 $(".datemask").datepicker({
                    autoclose: true
                });
                
                $("#select_branch").change(function() {
                    let branchId = $('#select_branch').val();
                    if(branchId !== '') {
                        getSalemen(branchId);
                    }
                });

                $("#print_salary").click(function() {
                    let branchId = $("#select_branch").val();
                    let salemanId = $("#salemen").val();
                    let start = $("#start_date").val();
                    let end = $("#end_date").val();

                    if(branchId !== '' && salemanId !== '' && 
                      start !== '' && end !== '') {
                        let url = "print_salary?branch_id=" + branchId;
                        url = url + "&saleman_id=" + salemanId;
                        url = url + "&start=" + start;
                        url = url + "&end=" + end;
                        let salemanName = $('#salemen :selected').text();
                        url = url + "&saleman_name=" + salemanName;
                        var win = window.open(url, "_blank");
                        if(win) {
                            win.focus();
                        } else {
                            showCustomMessage("Warning", "Please allow popups for this site.", 'error');
                        }
                    }
                });
            });

        </script>
        <!-- external javascript -->


    </body>
</html>
