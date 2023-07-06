<?php 
$typeId = $_SESSION['user_type_id'];
?>
<div class="col-sm-2 col-lg-2">
    <div class="sidebar-nav">
        <div class="nav-canvas">
            <div class="nav-sm nav nav-stacked">

            </div>
            <ul class="nav nav-pills nav-stacked main-menu">
                <li class="nav-header">Main</li>
                <li>
                    <a class="ajax-link" href="dashboard"><i class="glyphicon glyphicon-home"></i><span> Dashboard</span></a>
                </li>
                
                <li class="accordion">
                    <a href="#"><i class="glyphicon glyphicon-shopping-cart"></i><span> Daily Sales</span></a>
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="sales">Sales</a></li>
                        <li><a href="returns">Returns</a></li>
                    </ul>
                </li>
                
                <?php 
                    if($typeId == "MASTER_USER") {
                ?>
                <li class="accordion">
                    <a href="#"><i class="glyphicon  glyphicon-gift"></i><span> Products</span></a>
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="vendors">Vendors</a></li>
                        <li><a href="products">Products</a></li>
                        <li><a href="add_to_branch_stock">Add to Branch Stock</a></li>
                    </ul>
                </li>
                <li class="accordion">
                    <a href="#"><i class="glyphicon glyphicon-file"></i><span> Reports</span></a>
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="sale_report_between_two_dates">Daily Sales</a></li>
                        <li><a href="daily_returns_report">Daily Returns</a></li>
                        <li><a href="salary_report">Salary</a></li>
                        <li><a href="worth_report">Worth</a></li>
                        <li><a href="zero_stock">Zero Stock</a></li>
                    </ul>
                </li>
                <li class="accordion">
                    <a href="#"><i class="glyphicon glyphicon-cog"></i><span> Settings</span></a>
                    <ul class="nav nav-pills nav-stacked">
                        <li><a href="branches">Branches</a></li>
                        <li><a href="users">Users</a></li>
                        <li><a href="salemen">Salemen</a></li>
                    </ul>
                </li>
                <?php } ?>
                
                <li>
                    <a href="logout"><i class="glyphicon glyphicon-off"></i><span> Logout</span></a>
                </li>
            </ul>
        </div>
    </div>
</div>

<noscript>
    <div class="alert alert-block col-md-12">
        <h4 class="alert-heading">Warning!</h4>

        <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a>
            enabled to use this site.</p>
    </div>
</noscript>
