<?php 
include 'config/connection.php';
if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

$today = date('Y-m-d');
//$firstday = date('l - Y-m-d', strtotime("this week"));
$weekStartDate = date('Y-m-d', strtotime("this week"));
 
//2023-02-24
$start = substr($today, 0, 8);
//2023-02-
$startOfMonth = $start.'01';

$start = substr($today, 0, 4);
$startOfYear = $start.'-01-01';

$query = "select ifnull(sum(`total_net_amount`), 0) as `total` 
from `daily_sales` 
where `transaction_date` = '$today' 
UNION ALL 
select ifnull(sum(`total_net_amount`), 0) as `total` 
from `daily_sales` 
where `transaction_date` between '$weekStartDate' and '$today' 
UNION ALL 
select ifnull(sum(`total_net_amount`), 0) as `total` 
from `daily_sales` 
where `transaction_date` between '$startOfMonth' and '$today' 
UNION ALL 
select ifnull(sum(`total_net_amount`), 0) as `total` 
from `daily_sales` 
where `transaction_date` between '$startOfYear' and '$today';";
$stmt = $con->prepare($query);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$todaySale = $r['total'];
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$weeklySale = $r['total'];
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$monthlySale = $r['total'];
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$yearlySale = $r['total'];
    
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
                    <div class=" row">
                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <a data-toggle="tooltip" title="6 new members." class="well top-block" href="#">
                                <i class="glyphicon glyphicon-shopping-cart blue"></i>

                                <div>Daily Sales</div>
                                <div><?php echo $todaySale;?></div>
                                <span class="notification"><?php echo $todaySale;?></span>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <a data-toggle="tooltip" title="4 new pro members." class="well top-block" href="#">
                                <i class="glyphicon glyphicon-shopping-cart green"></i>
                                <div>Weekly Sales</div>
                                <div><?php echo $weeklySale;?></div>
                                <span class="notification green"><?php echo $weeklySale;?></span>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <a data-toggle="tooltip" title="$34 new sales." class="well top-block" href="#">
                                <i class="glyphicon glyphicon-shopping-cart yellow"></i>

                                <div>Monthly Sales</div>
                                <div><?php echo $monthlySale;?></div>
                                <span class="notification yellow"><?php echo $monthlySale;?></span>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-3 col-xs-6">
                            <a data-toggle="tooltip" title="12 new messages." class="well top-block" href="#">
                                <i class="glyphicon glyphicon-shopping-cart red"></i>

                                <div>Yearly Sales</div>
                                <div><?php echo $yearlySale;?></div>
                                <span class="notification red"><?php echo $yearlySale;?></span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- content ends -->
                </div><!--/#content.col-md-0-->
            </div><!--/fluid-row-->

            <!-- Ad ends -->

            <hr>

<?php include 'config/site-js.php';?>
<?php include 'config/footer.php';?>

        </div><!--/.fluid-container-->

        <!-- external javascript -->

        
    </body>
</html>
