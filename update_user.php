<?php
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $userId = $_POST['hidden_id'];
    $userName = trim($_POST['user_name']);
    $userPassword = trim($_POST['password']);
    $email = trim($_POST['email']);
    $userTypeId = $_POST['user_type'];
    $branchNameId = $_POST['branch_name'];
    $message = '';
  

        try {

            $con->beginTransaction();
            
            $query = "update `users` set 
        `user_name` = '$userName', 
        `email` = '$email',
        `user_type_id` = '$userTypeId',
        `branch_id` = $branchNameId
        where `id` = $userId;";
        $statement = $con->prepare($query);
        $statement->execute();
            
            
            if($userPassword != '') {
            $query = "update `users` set 
            `password` = '$userPassword' 
            where `id` = $userId;";
            $stmtpassword = $con->prepare($query);
            $stmtpassword->execute();
        }

        $con->commit();

        $message = 'User has been updated successfully.';

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

$userId = 0;
if(isset($_GET['id'])) {
    $userId = $_GET['id'];
}
$query = "select `u`.`user_name` as `user_name`,`u`.`password` as `password`,`u`.`email` as `email`,`ut`.`id` as `type_id`,`b`.`id` as `branch_id`
from `users` as `u`, `user_types` as `ut`, `branches` as `b` 
where `u`.`user_type_id` = `ut`.`id` and `u`.`branch_id` = `b`.`id` and `u`.`id` = $userId;";
$stmt = $con->prepare($query);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$userType = $r['type_id'];
$branchId = $r['branch_id'];

$allUsers = getuserType($con,$userType);
$allBranches = getBranches($con,$branchId);


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
                                    <h2>Update User</h2>
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
                                        <input type="hidden"  name="hidden_id" required class="form-control" maxlength="50" value="<?php echo $userId; ?>" />
                                        <label>Username</label>
                                        <input type="text" id="user_name" name="user_name" required class="form-control" maxlength="50" value="<?php echo $r['user_name']; ?>" />
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <label>Password</label>
                                        <input type="password" id="password" name="password" class="form-control" maxlength="50"/>
                                    </div>

                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <label>Email</label>
                                        <input type="text" id="email" name="email" required class="form-control" maxlength="50" value="<?php echo $r['email']; ?>"/>
                                    </div>
                                    <div class="clearfix">&nbsp;</div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <label>Select User Type</label>
                                        <select name="user_type" id="user_type" required class="form-control">
                                            <?php echo $allUsers;?>
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
let userId = '<?php echo $userId;?>';

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
                        checkGenericUniqueness("users", "user_name", userName, "User Name", userId);
                    }
                });

                $("#email").blur(function(){
                    let email = $(this).val().trim();
                    $(this).val(email);
                    if(email !== ''){
                        checkGenericUniqueness("users", "email", email, "Email", userId);
                    }
                });
            });

        </script>
        
    </body>
</html>
