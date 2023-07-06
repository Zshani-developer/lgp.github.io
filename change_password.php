<?php
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $userName = $_POST['user_name'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $id = $_SESSION['user_id'];

    $message = '';
    $query = '';
    try {

        $con->beginTransaction();
        if($password == '') {
            $query = "update `users` set 
            `user_name` = '$userName',  
            `email` = '$email' 
            where `id` = $id;";
        } else {
            $query = "update `users` set 
            `user_name` = '$userName', 
            `email` = '$email', 
            `password` = '$password' 
            where `id` = $id;";
        }
        $statement = $con->prepare($query);
        $statement->execute();

        $con->commit();

        $message = 'User has been updated successfully.';

    } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }
    $goto = "dashboard";
    header("location:congratulation?go_to=".$goto."&success_message=".$message); 
    exit;
}

$id = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$email = $_SESSION['email'];

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

                <h1>
                    Change Password
                </h1>


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
                                    <h2>Update User Account</h2>
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

                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <input type="hidden" value="<?php echo $id;?>" name="hidden_id" id="hidden_id" >
                                                    <label>User Name</label>
                                                    <input type="text" value="<?php echo $userName;?>" class="form-control" id="user_name" name="user_name" required maxlength="50">
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>Password</label>
                                                    <input type="password"  value="" class="form-control" id="password" name="password" maxlength="50">
                                                </div>
                                                <div class="clearfix">&nbsp;</div>

                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label>Email</label>
                                                    <input type="text"  value="<?php echo $email;?>" class="form-control" id="email" name="email" required maxlength="30">
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

                if(passwordLength === 0) {
                    passwordStatus = true;
                } else {
                    if(passwordLength >= 4) {
                        passwordStatus = true;
                    } else {
                        showCustomMessage("Warning", "Password should be at least 4 characters.", 'error');
                    }
                }

                if(passwordStatus && emailStatus) {
                    status = true;
                }

                return status;
            }
        </script>
    </body>
</html>
