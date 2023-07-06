<?php 
include 'config/connection.php';
if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
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
                                    <h2>Panel Title</h2>
                                    <div class="box-icon">
                                        <a href="#" class="btn btn-minimize btn-round btn-default">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </a>
                                       
                                    </div>
                                </div>
                                <div class="box-content">
                                    <!-- put your content here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="clearfix">&nbsp;</div>
                    <div class="row">
                        <div class="box col-md-12">
                            <div class="box-inner">
                                <div class="box-header well" data-original-title="">
                                    <h2>Panel Title</h2>
                                    <div class="box-icon">
                                        <a href="#" class="btn btn-minimize btn-round btn-default">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </a>
                                       
                                    </div>
                                </div>
                                <div class="box-content">
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

        
    </body>
</html>
