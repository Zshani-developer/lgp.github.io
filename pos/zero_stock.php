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
                                    <h2>Zero Stock</h2>
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
                                                <label>Select Branch</label>
                                                <select id="select_branch" name="select_branch" class="form-control" id="select_branch">
                                                    <?php echo $allBranches;?>
                                                </select>

                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                                                <label>&nbsp;</label>
                                                <button id="print_zero_stock" type="button" class="btn btn-primary btn-block"> Zero<i class="glyphicon glyphicon-print"></i></button>
                                            </div>
                                            
                                            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12">
                                                <label>&nbsp;</label>
                                                <button id="print_non_zero_stock" type="button" class="btn btn-primary btn-block"> NON Zero<i class="glyphicon glyphicon-print"></i></button>
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
                $("#print_zero_stock").click(function() {
                    let branchId = $("#select_branch").val();

                    if(branchId !== '') {
                        var win = window.open("print_zero_stock?branch_id=" + branchId + "&type=ZERO", "_blank");
                        if(win) {
                            win.focus();
                        } else {
                            showCustomMessage("Warning", "Please allow popups for this site.", 'error');
                        }
                    }
                });
                
                
                $("#print_non_zero_stock").click(function() {
                    let branchId = $("#select_branch").val();

                    if(branchId !== '') {
                        var win = window.open("print_zero_stock?branch_id=" + branchId + "&type=NON_ZERO", "_blank");
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
