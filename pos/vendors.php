<?php
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $vendorName = trim($_POST['vendor_name']);
    $vendorCity = trim($_POST['vendor_city']);
    $contactNumber = trim($_POST['contact_number']);
    $message = '';

        try {

            $con->beginTransaction();

            $query = "insert into `vendors`(`vendor_name`, 
        `vendor_city`, `contact_number`)
        values('$vendorName', '$vendorCity','$contactNumber');";
            $statement = $con->prepare($query);
            $statement->execute();

            $con->commit();

           $message = 'vendor has been saved successfully.';

        } catch(PDOException $ex) {
            $con->rollback();
            echo $ex->getMessage();
            echo $ex->getTraceAsString();
            exit;
        }
    
    $goto = "vendors";
    header("location:congratulation?go_to=".$goto."&success_message=".$message); 
    exit;
    
}
$query = "select * from `vendors` 
order by `vendor_name` asc;";
$stmt = $con->prepare($query);
$stmt->execute();

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
                                    <h2>Add Vendor</h2>
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
                                        <label>Name</label>
                                        <input type="text" id="vendor_name" name="vendor_name" required class="form-control" maxlength="50" />
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <label>City</label>
                                        <input type="text" id="vendor_city" name="vendor_city" required class="form-control" maxlength="50" />
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <label>Contact Number</label>
                                        <input type="text" id="contact_number" name="contact_number" required class="form-control" maxlength="17" />
                                    </div>                                    
                                    <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                        <div class="clearfix">&nbsp;</div>
                                        <button type="submit" id="submit" name="submit" 
                                                class="btn btn-primary btn-block" 
                                                data-toggle="confirmation" data-placement="top" title="" data-original-title="Are you sure?">Save</button>
                                    </div>
                                </div>
                            </form>
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
                                    <h2>All Vendors</h2>
                                    <div class="box-icon">
                                        <a href="#" class="btn btn-minimize btn-round btn-default">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </a>
                                       
                                    </div>
                                </div>
                                <div class="box-content">
                                    <div class="box-body">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                                    <table id="disease_table" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Name</th>
                                                <th>City</th>
                                                <th>Contact Number</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                             <?php 
                                            $counter = 0;
                                            while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                $counter++;
                                            ?>
                                            <tr>
                                                <td><?php echo $counter;?></td>
                                                <td><?php echo $r['vendor_name'];?></td>
                                                <td><?php echo $r['vendor_city'];?></td>
                                                <td><?php echo $r['contact_number'];?></td>
                                                <td><a class="btn btn-primary" href="update_vendor?id=<?php echo $r['id'];?>&vendor_name=<?php echo $r['vendor_name'];?>&vendor_city=<?php echo $r['vendor_city'];?>&contact_number=<?php echo $r['contact_number'];?>">
                                                    <i class="glyphicon glyphicon-edit glyphicon-white"></i>
                                                    </a>
                                                    
                                                        <?php 
                                                $id = $r['id'];
                                                $isActive = $r['is_active'];
                                                $btnClass = 'btn-primary';
                                                $iconClass = "glyphicon-lock";

                                                if($isActive == 0) {
                                                    $btnClass = 'btn-danger';
                                                    $iconClass = "glyphicon-unlock";
                                                }
                                                    ?>
                                                    
                                                    <a class="btn <?php echo $btnClass;?>" href="block_unblock_vendor?id=<?php echo $id;?>&is_active=<?php echo $isActive;?>">
                                                        <i class="glyphicon <?php echo $iconClass;?> glyphicon-white"></i>
                                                    </a>
                                                
                                                </td>
                                                
                                            </tr>
                                            <?php } ?>
                                            
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                                    <!-- put your content here -->
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
        <script>
                    $(document).ready(function(){
                $("#contact_number").blur(function(){
                    let contact_number = $(this).val().trim();
                    $(this).val(contact_number);
                    if(contact_number !== ''){
                        checkGenericUniqueness("vendors", "contact_number", contact_number, "Contact Number");
                    }
                    });
                });

        </script>

    </body>
</html>
