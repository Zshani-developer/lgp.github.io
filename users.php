<?php
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $userName = trim($_POST['user_name']);
    $userPassword = trim($_POST['password']);
    $email = trim($_POST['email']);
    $userTypeId = $_POST['user_type'];
    $branchNameId = $_POST['branch_name'];
    $message = '';


    try {

        $con->beginTransaction();

        $query = "insert into `users`(`user_name`, 
        `password`, `email`, `user_type_id`,`branch_id`)
        values('$userName', '$userPassword','$email', '$userTypeId', '$branchNameId');";
        $statement = $con->prepare($query);
        $statement->execute();

        $con->commit();

        $message = 'User has been saved successfully.';

    } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }

    $goto = "users";
    header("location:congratulation?go_to=".$goto."&success_message=".$message); 
    exit;

}


$allUsers = getuserType($con);
$allBranches = getBranches($con);

$query = "select `u`.`id` as `id`, `u`.`user_name` as `user_name`,`u`.`password` as `password`,`u`.`email` as `email`,`u`.`is_active`,`ut`.`type` as `type`,`b`.`branch_name` as `branch_name`
from `users` as `u`, `user_types` as `ut`, `branches` as `b` 
where `u`.`user_type_id` = `ut`.`id` and `u`.`branch_id` = `b`.`id` 
order by `u`.`id` asc, `b`.`id` asc;";
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
                                    <h2>Add User</h2>
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
                                                    <label>Username</label>
                                                    <input type="text" id="user_name" name="user_name" required class="form-control" maxlength="50" />
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Password</label>
                                                    <input type="password" id="password" name="password" required class="form-control" maxlength="50" />
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Email</label>
                                                    <input type="text" id="email" name="email" required class="form-control" maxlength="50" />
                                                </div>
                                                <div class="clearfix">&nbsp;</div>
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Select User Type</label>
                                                    <select id="user_type" name="user_type"  required class="form-control">
                                                        <?php echo $allUsers; ?>
                                                    </select>
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Select Branch Name</label>
                                                    <select id="branch_name" name="branch_name" required class="form-control">
                                                        <?php echo $allBranches; ?>
                                                    </select>
                                                </div>


                                                <div class="clearfix">&nbsp;</div>
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
                                    <h2>All Users</h2>
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
                                                            <th>User Name</th>
                                                            <th>Email</th>
                                                            <th>User Type</th>
                                                            <th>Branch Name</th>
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
                                                            <td><?php echo $r['user_name'];?></td>
                                                            <td><?php echo $r['email'];?></td>
                                                            <td><?php echo $r['type'];?></td>
                                                            <td><?php echo $r['branch_name'];?></td>
                                                            <td><a class="btn btn-primary" href="update_user?id=<?php echo $r['id'];?>">
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

                                                                <a class="btn <?php echo $btnClass;?>" href="block_unblock_user?id=<?php echo $id;?>&is_active=<?php echo $isActive;?>">
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
                $("#user_name").blur(function(){
                    let userName = $(this).val().trim();
                    $(this).val(userName);
                    if(userName !== '') {
                        checkGenericUniqueness("users", "user_name", userName, "User Name", 0);
                    }
                });

                $("#email").blur(function(){
                    let email = $(this).val().trim();
                    $(this).val(email);
                    if(email !== '') {
                        checkGenericUniqueness("users", "email", email, "Email", 0);
                    }
                });
            });

        </script>
    </body>
</html>
