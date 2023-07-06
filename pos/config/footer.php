<footer class="row">

    <p class="col-md-9 col-sm-9 col-xs-12 copyright">&copy; <a href="http://usman.it" target="_blank">Muhammad
        Usman</a> 2012 - 2015</p>

    <p class="col-md-3 col-sm-3 col-xs-12 powered-by">Powered by: <a
                                                                     href="http://usman.it/free-responsive-admin-template">Charisma</a></p>
</footer>

<?php 
$message = '';
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<script>
    let message = '';
    message = '<?php echo $message;?>';
    
    $(document).ready(function() {
        $('[data-toggle=confirmation]').confirmation();
        
        if(message !== '') {
            showCustomMessage("Information!", message, "success");
        }
        
        $("input[type=text]").blur(function() {
            let data = $(this).val().trim();
            $(this).val(data);
        });
        
    });
</script>