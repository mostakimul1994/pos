$(document).ready( function(){
	$(document).on('click', '.add_payment_modal', function(e){
        e.preventDefault();
        var container = $('.payment_modal');

        $.ajax({
            url: $(this).attr("href"),
            dataType: "json",
            success: function(result){
                if(result.status == 'due'){
                    $(container).html(result.view).modal('show');
                    __currency_convert_recursively(container);
                    $('#paid_on').datepicker({
                        autoclose: true,
                    });
                } else {
                    toastr.error(result.msg);
                }
                
            }
        });
    });
    $(document).on('click', '.edit_payment', function(e){
        e.preventDefault();
        var container = $('.edit_payment_modal');

        $.ajax({
            url: $(this).data("href"),
            dataType: "html",
            success: function(result){
                $(container).html(result).modal('show');
                __currency_convert_recursively(container);
                $('#paid_on').datepicker({
                    autoclose: true,
                    toggleActive: false
                });
            }
        });
    });

    $(document).on('submit', 'form#transaction_payment_add_form', function(e){
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            method: $(this).attr("method"),
            url: $(this).attr("action"),
            dataType: "json",
            data: data,
            success: function(result){
                if(result.success === true){
                    $('div.payment_modal').modal('hide');
                    $('div.edit_payment_modal').modal('hide');
                    toastr.success(result.msg);
                    if(typeof purchase_table != 'undefined'){
                        purchase_table.ajax.reload();
                    }
                    if(typeof sell_table != 'undefined'){
                        sell_table.ajax.reload();
                    }
                } else {
                    toastr.error(result.msg);
                }
            }
        });
    });
    $(document).on('click', '.view_payment_modal', function(e){
        e.preventDefault();
        var container = $('.payment_modal');

        $.ajax({
            url: $(this).attr("href"),
            dataType: "html",
            success: function(result){
                $(container).html(result).modal('show');
                __currency_convert_recursively(container);
            }
        });
    });
    $(document).on('click', '.delete_payment', function(e){
        swal({
          title: LANG.sure,
          text: LANG.confirm_delete_payment,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                url: $(this).data("href"),
                method: 'delete',
                dataType: "json",
                success: function(result){
                    if(result.success === true){
                        $('div.payment_modal').modal('hide');
                        $('div.edit_payment_modal').modal('hide');
                        toastr.success(result.msg);
                        if(typeof purchase_table != 'undefined'){
                            purchase_table.ajax.reload();
                        }
                        if(typeof sell_table != 'undefined'){
                            sell_table.ajax.reload();
                        }
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
          }
        });
    });
});