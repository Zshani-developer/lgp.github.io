<?php
include 'config/connection.php';
include 'common_service/common_functions.php';
if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $branchName = trim($_POST['branch_name']);
    $address = trim($_POST['address']);
    $phoneNumber = trim($_POST['phone_number']);
    $message = '';


    try {

        $con->beginTransaction();

        $query = "insert into `branches`(`branch_name`, 
        `address`, `phone_number`)
        values('$branchName', '$address','$phoneNumber');";
        $statement = $con->prepare($query);
        $statement->execute();

        $con->commit();

        $message = 'Branch has been saved successfully.';

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


$allUsers = getuserType($con);
$allBranches = getBranches($con);

$query = "select * from `branches` order by `branch_name` asc;";
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
                                    <h2>Add Branch</h2>
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
                                                    <label>Branch Name</label>
                                                    <input type="text" id="branch_name" name="branch_name" required class="form-control" maxlength="50" />
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Address</label>
                                                    <input type="text" id="address" name="address" required class="form-control" maxlength="50" />
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Phone Number</label>
                                                    <input type="text" id="phone_number" name="phone_number" required class="form-control" maxlength="50" />
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
                                    <h2>All Branches</h2>
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
                                                            <th>Branch Name</th>
                                                            <th>Address</th>
                                                            <th>Phone Number</th>
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
                                                            <td><?php echo $r['branch_name'];?></td>
                                                            <td><?php echo $r['address'];?></td>
                                                            <td><?php echo $r['phone_number'];?></td>
                                                            <td><a class="btn btn-primary" href="update_branch?id=<?php echo $r['id'];?>">
                                                                <i class="glyphicon glyphicon-edit glyphicon-white"></i>
                                                                </a></td>

                                                        </tr>
                                                        <?php } ?>

                                                    </tbody>
                                                </table>
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

        <!-- external javascript -->

        <script>
            function validate() {
                let branchName = $("#branch_name").val().trim();
                let address = $("#address").val().trim();
                let phone = $("#phone_number").val().trim();
                let status = false;

                if (branchName !== "" && address !== "" && phone !== "") {
                    status = true;
                } else {
                    showCustomMessage("Warning", "Please fill all text boxes.", 'error');
                }

                return status;
            }

            $(document).ready(function(){
                $("#branch_name").blur(function(){
                    let branchName = $(this).val().trim();
                    $(this).val(branchName);
                    if(branchName !== '') {
                        checkGenericUniqueness("branches", "branch_name", branchName, "Branch Name", 0);
                    }
                });


                $("#phone_number").blur(function(){
                    let phoneNumber = $(this).val().trim();
                    $(this).val(phoneNumber);

                    if(phoneNumber !== '') {
                        checkGenericUniqueness("branches", "phone_number", phoneNumber, "Phone Number", 0);
                    }
                });
            });

        </script>
    </body>
</html>
