<?php
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $branchId = $_POST['hidden_id'];
    $branchName = trim($_POST['branch_name']);
    $address = trim($_POST['address']);
    $phoneNumber = trim($_POST['phone_number']);
    $message = '';
  

        try {

            $con->beginTransaction();
            
            $query = "update `branches` set 
        `branch_name` = '$branchName', 
        `address` = '$address',
        `phone_number` = '$phoneNumber'
        where `id` = $branchId;";
        $statement = $con->prepare($query);
        $statement->execute();

        $con->commit();

        $message = 'Branch has been updated successfully.';

        } catch(PDOException $ex) {
            $con->rollback();
            echo $ex->getMessage();
            echo $ex->getTraceAsString();
            exit;
        }
    
    $goto = "branches";
    header("location:congratulation?go_to=".$goto."&success_message=".$message); 
    exit;

}





$medicineDetailId = 0;
if(isset($_GET['id'])) {
    $branchId = $_GET['id'];
}
$query = "select * from `branches` where `id` = $branchId;";
$stmt = $con->prepare($query);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);

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
                                    <h2>Update Branch</h2>
                                    <div class="box-icon">
                                        <a href="#" class="btn btn-minimize btn-round btn-default">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </a>
                                       
                                    </div>
                                </div>
                                <div class="box-content">
                                    
                                    <div class="box-body">
                            <form method="post" onsubmit="return validate();">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <input type="hidden"  name="hidden_id" required class="form-control" maxlength="50" value="<?php echo $branchId; ?>"/>
                                        <label>Branch Name</label>
                                        <input type="text" id="branch_name" name="branch_name" required class="form-control" maxlength="50" value="<?php echo$r['branch_name'];?>"/>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <label>Address</label>
                                        <input type="text" id="address" name="address" class="form-control" maxlength="50" value="<?php echo $r['address']; ?>"/>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <label>Phone Number</label>
                                        <input type="text" id="phone_number" name="phone_number" required class="form-control" maxlength="50" value="<?php echo $r['phone_number']; ?>"/>
                                    </div>
                                
                                    <div class="clearfix">&nbsp;</div>
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
<script>
        let branchId = '<?php echo $branchId; ?>';
        function validate() {
                let status = false;
                let passwordStatus = false;
                let emailStatus = false;
                let passwordLength = $("#password").val().length;
                let email = $("#email").val().trim();

                let regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

                if (email !== "") {
                    if (!regex.test(email)) {
                        showCustomMessage("Warning", "Invalid email / format.", 'error');
                    } else {
                        emailStatus = true;
                    }
                }

                if(passwordLength >= 4) {
                    passwordStatus = true;
                } else {
                    showCustomMessage("Warning", "Password should be at least 4 characters.", 'error');
                }

                if(passwordStatus && emailStatus) {
                    status = true;
                }

                return status;
            }
    
    
     $(document).ready(function(){
                $("#branch_name").blur(function(){
                    let branchName = $(this).val().trim();
                    $(this).val(branchName);
                    if(branchName !== '') {
                        checkGenericUniqueness("branches", "branch_name", branchName, "Branch Name", branchId);
                    }
                });


                $("#phone_number").blur(function(){
                    let phoneNumber = $(this).val().trim();
                    $(this).val(phoneNumber);

                    if(phoneNumber !== '') {
                        checkGenericUniqueness("branches", "phone_number", phoneNumber, "Phone Number", branchId);
                    }
                });
            });
        
</script>
        
    </body>
</html>
