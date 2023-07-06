<?php
include 'config/connection.php';
include 'common_service/common_functions.php';

if (!(isset($_SESSION['user_id']))) {

    header("location:index");
    exit;
}

if(isset($_POST['submit'])) {
    $productId = $_POST['hidden_id'];
    $productName = trim($_POST['product_name']);
    $vendorId = trim($_POST['vendor']);
    $purchasePrice = trim($_POST['purchase_price']);
    $markup = trim($_POST['markup']);
    $salePrice = trim($_POST['sale_price']);
    $message = '';

    try {
        $con->beginTransaction();
        $query = "update `products` set `product_name` = '$productName',
        `vendor_id` =  '$vendorId',
        `purchase_price` = '$purchasePrice',
        `markup` = '$markup',
        `sale_price` = '$salePrice'
        where `id` = '$productId'
        ; ";        

        $statement = $con->prepare($query);
            $statement->execute();
            $con->commit();
           $message = 'Product has been updated successfully.';


    } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }
    $goto = "products";
    header("location:congratulation?go_to=".$goto."&success_message=".$message);
}
$productId = 0;
$productName = '';
$vendorId = 0;
$purchasePrice = 0;
$markup = 0;
$salePrice = 0;
if(isset($_GET['id'])) {
    $productId = $_GET['id'];
    $productName = $_GET['product_name'];
    $vendorId = $_GET['vendor'];
    $purchasePrice = $_GET['purchase_price'];
    $markup = $_GET['markup'];
    $salePrice = $_GET['sale_price'];
}
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
                                    <h2>Update Product</h2>
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

                                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                                    <label>Vendor</label>
                                                    <select name="vendor" id="vendor" required class="form-control" data-rel="chosen">
                                                       <?php echo $allVendors;?>
                                                     </select>
                                                </div>

                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                <input type="hidden" value="<?php echo $productId?>" name="hidden_id" id="hidden_id" >
                                                   <div>       
                                                    <label>Name</label>
                                                    <input type="text" value="<?php echo $productName?>" class="form-control" id="product_name" name="product_name" require maxlength="50">
                                                    </div>
                                                </div>
                                              

                                                
                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Purchase Price</label>
                                                    <input type="text"  value="<?php echo $purchasePrice?>" class="form-control" id="purchase_price" name="purchase_price" require >
                                                </div>
                                                
                                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                            <label>Markup</label>
                                            <input type="text" value="<?php echo $markup?>" id="markup" name="markup" class="form-control" required/>
                                            </div> 
                                            </div>
                                            
                                            <div class="clearfix">&nbsp;</div>
                                            
                                            <div class="row">
                                            <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                                    <label>Sale Price</label>
                                                    <input type="text"  value="<?php echo $salePrice?>" class="form-control" id="sale_price" name="sale_price" required>
                                                </div>

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
            $(document).ready(function(){
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