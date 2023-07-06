function showCustomMessage(title, message, type) {
    swal(title, message, type);
}


function checkGenericUniqueness(tableName, columnName, value, messageColumnName, id) {
    
    $.ajax({
        url: "ajax/check_generic_uniqueness",
        type: "GET",
        data:{
            "table_name":tableName,
            "column_name":columnName,
            "value":value,
            "id":id
        },
        cache:false,
        async:false,
        success:function(data) {
            let count = data * 1;
            if(count > 0) {
                $('#submit').attr("disabled", "disabled");
                let message = "The provided " + messageColumnName + " already exist.";
                showCustomMessage("Warning", message, 'error');
            }
            else{
                $('#submit').removeAttr("disabled");
            }
        }
        
    });
}

function getVendorProducts(vendorId) {
    
    $.ajax({
        url: "ajax/get_vendor_products",
        type: "GET",
        data:{
            "vendor_id":vendorId
        },
        cache:false,
        async:false,
        success:function(data) {
           $("#products").html(data);
        }
        
    });
}


function checkavailableQty(productId, branchId) {
    let availableQuantity = 0;
    
    $.ajax({
        url: "ajax/get_vendor_products",
        type: "GET",
        data:{
            "product_id":productId,
            "branch_id":branchId
        },
        cache:false,
        async:false,
        success:function(data) {
           availableQuantity = data;
        }
        
    });
    
    return availableQuantity;
}

function checkQuantityAndSalePrice(productId, branchId) {
    let json = null;
    
    $.ajax({
        url: "ajax/get_quantity_price",
        type: "GET",
        data:{
            "product_id":productId,
            "branch_id":branchId
        },
        cache:false,
        async:false,
        success:function(data) {
           json = data;
        }
        
    });
    
    return json;
}

function getSalemen(branchId) {
    $.ajax({
        url: "ajax/get_sale_men",
        type: "GET",
        data:{
            "branch_id":branchId
        },
        cache:false,
        async:false,
        success:function(data) {
           $("#salemen").html(data);
            console.log(data);
            $('#salemen').trigger("chosen:updated");
        }
        
    });
}
