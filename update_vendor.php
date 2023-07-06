<?php
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $vendorId = $_POST['hidden_id'];
    $vendorName = trim($_POST['vendor_name']);
    $vendorCity = trim($_POST['vendor_city']);
    $contactNumber = trim($_POST['contact_number']);
    $message = '';

    try {
        $con->beginTransaction();
        $query = "update `vendors` set `vendor_name` = '$vendorName',
        `vendor_city` = '$vendorCity',
        `contact_number` =  '$contactNumber'
        where `id` = '$vendorId'
        ; ";
        $statement = $con->prepare($query);
            $statement->execute();
            $con->commit();
           $message = 'Vendor has been updated successfully.';


    } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }
    $goto = "vendors";
    header("location:congratulation?go_to=".$goto."&success_message=".$message);
}
$vendorId = 0;
$vendorName = '';
$vendorCity = '';
$contactNumber = '';
if(isset($_GET['id'])) {
    $vendorId = $_GET['id'];
    $vendorName = $_GET['vendor_name'];
    $vendorCity = $_GET['vendor_city'];
    $contactNumber = $_GET['contact_number'];
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
                                    <h2>Update Vendor</h2>
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
                                                
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <input type="hidden" value="<?php echo $vendorId?>" name="hidden_id" id="hidden_id" >
                                                    <label>Name</label>
                                                    <input type="text" value="<?php echo $vendorName?>" class="form-control" id="vendor_name" name="vendor_name" require maxlength="50">
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>City</label>
                                                    <input type="text"  value="<?php echo $vendorCity?>" class="form-control" id="vendor_city" name="vendor_city" require maxlength="50">
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Contact Number</label>
                                                    <input type="text"  value="<?php echo $contactNumber?>" class="form-control" id="contact_number" name="contact_number" require maxlength="17">
                                                </div>
                                            </div>
                                            <div class="clearfix">&nbsp;</div>
                                            <div class="row">
                                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                            <div class="clearfix">&nbsp;</div>
                                                    <button type="submit" id="submit" name="submit" 
                                                    class="btn btn-primary btn-block"
                                                    data-toggle="confirmation" data-placement="top" title="" data-original-title="Are you sure?">Update</button>
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

        </div><!--/.fluid-container-->

        <!-- external javascript -->
    
        
    </body>
</html>
