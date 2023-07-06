<?php
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {
    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $branchId = trim($_POST['select_branch']);
    $salemenName = trim($_POST['name']);
    $fatherName = trim($_POST['father_name']);
    $contactNumber = trim($_POST['contact_number']);
    $cnic = trim($_POST['cnic_number']);
    $salaryPercentage = trim($_POST['salary_percentage']);
    $message = '';

    $salemenName = ucwords(strtolower($salemenName));
    $fatherName = ucwords(strtolower($fatherName));
    try {

        $con->beginTransaction();

        $query = "insert into `sale_men` (`name`, `contact_number`, `cnic_number`, `father_name` , `salary_percentage`, `branch_id`) 
        values ('$salemenName', '$contactNumber', '$cnic', '$fatherName', '$salaryPercentage', '$branchId' );";
        $statement = $con->prepare($query);
        $statement->execute();

        $con->commit();

        $message = 'Salemen has been saved successfully.';

    } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }
    $goto = "salemen";
    header("location:congratulation?go_to=".$goto."&success_message=".$message);
}

$query = "select `s`.`id`, `s`.`name`, `s`.`contact_number`, `s`.`cnic_number`, 
`s`.`father_name`, `s`.`is_active`, `s`.`salary_percentage`, 
`b`.`branch_name`, `s`.`branch_id` 
 from `sale_men` as `s`, `branches` as `b` where `s`.`branch_id` = `b`.`id`
 order by `s`.`name` asc;";
$stmt = $con->prepare($query);
$stmt->execute();

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

                    <div class="row">
                        <div class="box col-md-12">
                            <div class="box-inner">
                                <div class="box-header well" data-original-title="">
                                    <h2>Add Salemen</h2>
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
                                                    <label>Branches</label>
                                                    <select id="select_branch" name="select_branch" class="form-control" data-rel="chosen">
                                                        <?php echo $allBranches;?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control" id="name" name="name" require maxlength="50">
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Father Name</label>
                                                    <input type="text" class="form-control" id="father_name" name="father_name" require maxlength="50">
                                                </div>


                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Contact Number</label>
                                                    <input type="text" class="form-control" id="contact_number" name="contact_number" require maxlength="17">
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Cnic</label>
                                                    <input type="text" class="form-control" id="cnic_number" name="cnic_number" require maxlength="17">
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Salary Percentage</label>
                                                    <input type="text" class="form-control" id="salary_percentage" name="salary_percentage" require maxlength="17">
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
                    <div class="row">
                        <div class="box col-md-12">
                            <div class="box-inner">
                                <div class="box-header well" data-original-title="">
                                    <h2>All Salemen</h2>
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
                                                <table id="salemen_table" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>S.no</th>
                                                            <th>Name</th>
                                                            <th>Father Name</th>
                                                            <th>Contact Number</th>
                                                            <th>Cnic</th>
                                                            <th>Salary %</th>
                                                            <th>Branch Name</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $counter = 0;
                                                        while($r = $stmt->fetch(PDO::FETCH_ASSOC)  ){
                                                            $counter++;
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $counter;?></td>
                                                            <td><?php echo $r['name'];?></td>
                                                            <td><?php echo $r['father_name'];?></td>
                                                            <td><?php echo $r['contact_number'];?></td>
                                                            <td><?php echo $r['cnic_number'];?></td>
                                                            <td><?php echo $r['salary_percentage'];?></td>
                                                            <td><?php echo $r['branch_name'];?></td>
                                                            <td> <a class="btn btn-primary" href="update_salemen?id=<?php echo $r['id'];?>&name=<?php echo $r['name'];?>&father_name=<?php echo $r['father_name'];?>&contact_number=<?php echo $r['contact_number'];?>&cnic_number=<?php echo $r['cnic_number'];?>&salary_percentage=<?php echo $r['salary_percentage'];?>&select_branch=<?php echo $r['branch_id'];?>"><i class="glyphicon glyphicon-edit glyphicon-white"></i></a> </td>
                                                        </tr>
                                                        <?php
                                                        }
                                                        ?>
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
            $(document).ready(function(){
                $(document).ready(function(){
                    $("#contact_number").blur(function(){
                        let contact_number = $(this).val().trim();
                        $(this).val(contact_number);
                        if(contact_number !== ''){
                            checkGenericUniqueness("sale_men", "contact_number", contact_number, "Contact Number", 0);
                        }
                    });
                });

                $("#cnic_number").blur(function(){
                    let cnic_number = $(this).val().trim();
                    $(this).val(cnic_number);
                    if(cnic_number !== ''){
                        checkGenericUniqueness("sale_men", "cnic_number", cnic_number, "Cnic Number", 0);
                    }
                });

            });


            function validate() {
                let cnicStatus = false;
                let status = false;
                let cnic = $("#cnic_number").val().trim();

                let cnicRegex = /^[0-9+]{5}-[0-9+]{7}-[0-9]{1}$/;
                if (cnic !== "") {
                    cnicStatus = cnicRegex.test(cnic);
                    if (!(cnicStatus)) {
                        showCustomMessage("Warning", "Invalid cnic / format.", 'error');
                    }
                }

                return cnicStatus;

            }
        </script>


    </body>
</html>
