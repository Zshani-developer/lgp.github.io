<?php 

include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {
    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $productName = trim($_POST['product_name']);
    $vendorId = trim($_POST['vendor']);
    $purchasePrice = trim($_POST['purchase_price']);
    $salePrice = trim($_POST['sale_price']);
    $markup = trim($_POST['markup']);
    $message = '';

    try {

        $con->beginTransaction();
        
        $query = "insert into `products`(`product_name`, 
        `vendor_id`, `purchase_price`, `sale_price`, `markup`)
        values('$productName', $vendorId, $purchasePrice, $salePrice, $markup);";

        $statement = $con->prepare($query);
        $statement->execute();

        $con->commit();

        $message = 'Product has been saved successfully.';

    } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }

    $goto = "products";
    header("location:congratulation?go_to=".$goto."&success_message=".$message); 
    exit;

}
$query = "select `p`.`id`, `p`.`product_name`, 
`v`.`vendor_name`, `p`.`purchase_price`, `p`.`markup`, 
`p`.`sale_price`, `p`.`is_active` 
from `products` as `p`, `vendors` as `v` 
where `p`.`vendor_id` = `v`.`id` 
order by `p`.`product_name` asc;";
$stmt = $con->prepare($query);
$stmt->execute();

$allVendors = getVendors($con);

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
                                    <h2>Add Products</h2>
                                    <div class="box-icon">
                                        <a href="#" class="btn btn-minimize btn-round btn-default">
                                            <i class="glyphicon glyphicon-chevron-up"></i>
                                        </a>

                                    </div>
                                </div>
                                <div class="box-content">
                                    <!-- put your content here -->
                                    <div class="box-body">
                                        <form method="post">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                                                    <label>Vendor</label>
                                                    <select name="vendor" id="vendor" required class="form-control" data-rel="chosen" required>
                                                        <?php echo $allVendors;?>
                                                    </select>
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label>Product Name</label>
                                                    <input type="text" id="product_name" name="product_name" required class="form-control" maxlength="50" />
                                                </div>

                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Purchase Price</label>
                                                    <input type="text" id="purchase_price" name="purchase_price" required class="form-control"/>
                                                </div>

                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Markup</label>
                                                    <input type="text" id="markup" name="markup" class="form-control" required/>
                                                </div> 
                                                <div class="clearfix">&nbsp;</div>
                                                <div class="clearfix">&nbsp;</div>
                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Sale Price</label>
                                                    <input type="text" id="sale_price" name="sale_price" required class="form-control" />
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
                                    <h2>All Products</h2>
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
                                                            <th>Product Name</th>
                                                            <th>Vendor</th>
                                                            <th>Purchase Price</th>
                                                            <th>Sale Price</th>
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
                                                            <td><?php echo $r['product_name'];?></td>
                                                            <td><?php echo $r['vendor_name'];?></td>
                                                            <td><?php echo $r['purchase_price'];?></td>
                                                            <td><?php echo $r['sale_price'];?></td>

                                                            <td><a class="btn btn-primary" href="update_product?id=<?php echo $r['id'];?>&product_name=<?php echo $r['product_name'];?>&vendor=<?php echo $r['vendor_name'];?>&purchase_price=<?php echo $r['purchase_price'];?>&sale_price=<?php echo $r['sale_price'];?>&markup=<?php echo $r['markup'];?>">
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

                                                                <a class="btn <?php echo $btnClass;?>" href="block_unblock_product?id=<?php echo $id;?>&is_active=<?php echo $isActive;?>">
                                                                    <i class="glyphicon <?php echo $iconClass;?> glyphicon-white"></i>
                                                                </a>

                                                            </td>

                                                        </tr>
                                                        <?php } ?>

                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <!-- put your content here -->

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- content ends -->
                    </div><!--/#content.col-md-0-->
                </div><!--/fluid-row-->
            </div><!--/fluid-row-->

            <hr>

            <?php include 'config/site-js.php';?>
            <?php include 'config/footer.php';?>

        </div><!--/.fluid-container-->

        <!-- external javascript -->
        <script>

            $(document).ready(function(){
                $("#product_name").blur(function(){
                    let product_name = $(this).val().trim();
                    $(this).val(product_name);
                    if(product_name !== ''){
                        checkGenericUniqueness("products", "product_name", product_name, "Product Name");
                    }
                });

                $("#markup").blur(function(){
                    let purchase_price = $("#purchase_price").val() * 1;
                    let markup = $("#markup").val().trim();
                    
                    if(markup !== "" && markup > 0) {
                        let sale_price = markup/100 * purchase_price;
                        sale_price += purchase_price;
                        $("#sale_price").val(sale_price);
                    }

                });
            });
        </script>

    </body>
</html>
