$(function(){
    $('#li_product').addClass('active');
    productTable();
    generateUnitSelect();
    generateCategorySelect();
    $('[data-toggle="tooltip"]').tooltip();
    productModal();
   
    $('input[type=text]').on('keyup keypress keydown', function(e) { 
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            alert("Enter key is not valid");
            return false;
        }else if (keyCode === 222) {
            e.preventDefault();
            alert("Special character you've input is not valid");
            return false;
        }

    });
})

function productModal(){


    $('#productModal').on('hide.bs.modal', function(){
        $('#productForm').attr('action', 'controller/controller.product.php?mode=add');

        $('#product_id_update').val('');
        $('#product_code').val('');
        $('#product_description').val('');
        $('#unit_id').val('');
        $('#product_type').val('');
        $('#category_id').val('');
        $('#product_expiration').val('');
        $('#product_weight').val('');
        $('#product_length').val('');
        $('#product_width').val('');
        $('#product_height').val('');
        $('#frame').attr("src","static/default-placeholder.png");

    })

}

function editproduct(product_id, category_id, unit_id, product_type, product_code, product_description, w, l, ww, h, product_expiration){
    var d = new Date();
    $('#product_id_update').val(product_id);
    $('#category_id').val(category_id);
    $('#unit_id').val(unit_id);
    $('#product_type').val(product_type);
    $('#product_code').val(product_code);
    $('#product_description').val(product_description);
    $('#frame').attr("src","product_image/"+product_code+".jpg?"+d.getTime());

    find_productImage(product_code);

    $('#product_weight').val(w);
    $('#product_length').val(l);
    $('#product_width').val(ww);
    $('#product_height').val(h);

    $('#product_expiration').val(product_expiration);
    $('#productModal').modal('show');
    $('#productForm').attr('action', 'controller/controller.product.php?mode=update');

}

function find_productImage(product_code){
    var d = new Date();

    $.ajax({
        url:'http://localhost/wms/product_image/'+product_code+'.jpg?'+d.getTime(),
        type:'HEAD',
        error: function()
        {
            $('#frame').attr("src","static/default-placeholder.png");
            //file does not exist
        },
        success: function()
        {
            $('#frame').attr("src","product_image/"+product_code+".jpg");
            //file exists do something here
        }
    });

}

function deleteproduct(id,name,product_code){
    $('#product_id').val(id);
    $('#deleteName').text(name);
    $('#del_product_code').val(product_code);
    $('#productDelete').modal('show');
}

function generateUnitSelect(){
    
    $.ajax({
        url: "controller/controller.unit.php?mode=option",
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data)
            $("select[name='unit_id']").empty();
            $("select[name='unit_id']").append("<option value='0' selected='' disabled>Select UOM</option>");
            $('#unit_id').append(data.html);
        }
    })
}

function generateCategorySelect(){
    $.ajax({
        url: "controller/controller.category.php?mode=option",
        type: 'GET',
        processData: false,
        contentType: false,
        success: function(data) { 
            var data = JSON.parse(data)
            $("select[name='category_id']").empty();
            $("select[name='category_id']").append("<option value='0' selected='' disabled>Select Category</option>");
            $('#category_id').append(data.html);
        }
    })
}

function productTable(){
    $('#productTable').DataTable().destroy();
    $('#productTable').DataTable({
        "bLengthChange": false,
        "pageLength": 5,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "ajax" : "controller/controller.product.php?mode=table",
        "columns" : [
        
            { "data" : "product_code"},
            { "data" : "product_description"},
            { "data" : "unit_name"},
            { "data" : "product_type"},
            { "data" : "category_name"},
            { "data" : "product_expiration"},
            { "data" : "action"}
        ]
    });

    $('#dataTableSearch').on('keyup', function(){
        $('#productTable').DataTable().search($(this).val()).draw();
    })
}

function previewImage(){
    frame.src=URL.createObjectURL(event.target.files[0]);
    return
}