<?php
include 'config/connection.php';

$message = '';

if(isset($_POST['submit'])) {
    $userName = $_POST['user_name'];
    $password = $_POST['password'];

    $query = "select `id`, `user_name`, `email`, 
    `user_type_id`, `branch_id` 
    from `users` 
    where `user_name`='$userName' and 
    `password`='$password' and 
    `is_active`=1;";

    $count = 0;
    $stmt = null;
    $goto = "dashboard";
    $message = '';

    try {
        $stmt = $con->prepare($query);
        $stmt->execute();
        $count = $stmt->rowCount();

        if($count > 0) {
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $_SESSION['user_id'] = $r['id'];
            $_SESSION['user_name'] = $r['user_name'];
            $_SESSION['email'] = $r['email'];
            $_SESSION['user_type_id'] = $r['user_type_id'];
            $_SESSION['branch_id'] = $r['branch_id'];

            header("location:$goto");
            exit;
        } else {
            $message = 'Incorrect username or password.';
        }

    } catch(PDOException $ex) {
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'config/site-css.php';?>
    </head>
    <body>
        <div class="ch-container">
            <div class="row">

                <div class="row">
                    <div class="col-md-12 center login-header">
                        <h2>Point of Sale</h2>
                    </div>
                </div><!--/row-->

                <div class="row">
                    <div class="well col-md-5 center login-box">
                        <div class="alert alert-info">
                            Please login with your Username and Password.
                        </div>
                        <form class="form-horizontal" method="post">
                            <fieldset>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user red"></i></span>
                                    <input type="text" class="form-control" name="user_name" placeholder="Username">
                                </div>
                                <div class="clearfix"></div><br>

                                <div class="input-group input-group-lg">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock red"></i></span>
                                    <input type="password" class="form-control" name="password" placeholder="Password">
                                </div>
                                <div class="clearfix"></div>

                                <p class="center col-md-5">
                                    <button type="submit" name="submit" class="btn btn-primary">Login</button>
                                </p>
                            </fieldset>
                        </form>
                    </div>
                </div><!--/row-->
            </div><!--/fluid-row-->

        </div><!--/.fluid-container-->

        <?php include './config/site-js.php';?>

    </body>
</html>
