$(document).ready( function(){

    if($('input#iraqi_selling_price_adjustment').length > 0){
        iraqi_selling_price_adjustment = true;
    } else {
        iraqi_selling_price_adjustment = false;
    }

    //Date picker
    $('#transaction_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });
    
    //get suppliers
    $('#supplier_id').select2({
        ajax: {
        url: '/purchases/get_suppliers',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
              q: params.term, // search term
              page: params.page
            };
        },
        processResults: function (data) {
          return {
            results: data
                };
            }
        },
        minimumInputLength: 1,
        escapeMarkup: function(m) {
            return m;
        },
        templateResult: function(data){
        if (!data.id) {
            return data.text;
        }
        var html = data.text + ' (<b>Business: </b>' + data.business_name + ')';
        return html;
        },
        language: {
                noResults: function(){
                    var name = $("#supplier_id").data("select2").dropdown.$search.val();
                    return '<button type="button" data-name="' + name + '" class="btn btn-link add_new_supplier"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' + __translate('add_name_as_new_supplier', {'name': name}) +'</button>';
                }
        },
    });

    //Quick add supplier
    $(document).on('click', '.add_new_supplier', function(){
        $("#supplier_id").select2("close");
        var name = $(this).data('name');
        $('.contact_modal').find('input#name').val(name);
        $('.contact_modal').find('select#contact_type').val('supplier').closest('div.contact_type_div').addClass('hide');
        $('.contact_modal').modal('show');
    });

    $("form#quick_add_contact").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            contact_id: {
                remote: {
                    url: "/contacts/check-contact-id",
                    type: "post",
                    data: {
                        contact_id: function() {
                            return $( "#contact_id" ).val();
                        },
                        hidden_id: function() {
                            if($('#hidden_id').length){
                                return $('#hidden_id').val();
                            } else {
                                return '';
                            }
                        }

                    }
            }
            }
        },
        messages:{
            contact_id: {
                remote: LANG.contact_id_already_exists
            }
        },
        submitHandler: function(form) {
            var data = $(form).serialize();
            $.ajax({
                method: "POST",
                url: $(form).attr("action"),
                dataType: "json",
                data: data,
                success: function(result){
                    if(result.success == true){
                        $("select#supplier_id").append($('<option>', {value: result.data.id, 
                            text: result.data.name}));
                        $('select#supplier_id').val(result.data.id).trigger("change");
                        $('div.contact_modal').modal('hide');
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        }
    });
    $('.contact_modal').on('hidden.bs.modal', function () {
        $('form#quick_add_contact')[0].reset();
    });
    
    //Add products
    if($( "#search_product" ).length > 0){
        $( "#search_product" ).autocomplete({
            source: "/purchases/get_products",
            minLength: 2,
            response: function(event,ui) {
                if (ui.content.length == 1)
                {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                } else if (ui.content.length == 0)
                {
                    var term = $(this).data('ui-autocomplete').term;
                    swal({ 
                        title: LANG.no_products_found,
                        text: __translate('add_name_as_new_product', { 'term': term}),
                        buttons: [LANG.cancel, LANG.ok]
                    }).then((value) => {
                        if(value){
                            var container = $(".quick_add_product_modal");
                            $.ajax({
                                url: '/products/quick_add?product_name=' + term,
                                dataType: "html",
                                success: function(result){
                                    $(container).html(result).modal('show');
                                }
                            });
                        }
                    }); 
                }
            },
            select: function( event, ui ) {
                $(this).val(null);
                get_purchase_entry_row( ui.item.product_id, ui.item.variation_id );
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            return $( "<li>" ).append( "<div>" + item.text + "</div>" ).appendTo( ul );
        };
    }

    $(document).on( 'click', '.remove_purchase_entry_row', function(){
        swal({ 
            title: LANG.sure,
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((value) => {
            if(value){
                $(this).closest('tr').remove();
                update_table_total();
                update_grand_total();
                update_table_sr_number();
            }
        });
    });

    //On Change of quantity
    $(document).on( 'change', '.purchase_quantity', function(){

        var row = $(this).closest('tr');
        var quantity = __read_number($(this), true);
        var purchase_before_tax = __read_number(row.find('input.purchase_unit_cost'), true);
        var purchase_after_tax = __read_number(row.find('input.purchase_unit_cost_after_tax'), true);

        //Calculate sub totals
        var sub_total_before_tax = quantity * purchase_before_tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, true);

        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        update_table_total();
        update_grand_total();
    });

    $(document).on( 'change', '.purchase_unit_cost_without_discount', function(){
        var purchase_before_discount = __read_number($(this), true);

        var row = $(this).closest('tr');
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        //Calculations.
        var purchase_before_tax = parseFloat(purchase_before_discount) - __calculate_amount('percentage', discount_percent, purchase_before_discount);

        __write_number(row.find('input.purchase_unit_cost'), purchase_before_tax, true);

        var sub_total_before_tax = quantity * purchase_before_tax;

        //Tax
        var tax_rate = parseFloat(row.find('select.purchase_line_tax_id').find(':selected').data('tax_amount'));
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, true);

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true)
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, false, true));
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        update_inline_profit_percentage(row);
        update_table_total();
        update_grand_total();
    });

    $(document).on( 'change', '.inline_discounts', function(){
        var row = $(this).closest('tr');

        var discount_percent = __read_number($(this), true);

        var quantity = __read_number(row.find('input.purchase_quantity'), true);
        var purchase_before_discount = __read_number(row.find('input.purchase_unit_cost_without_discount'), true);

        //Calculations.
        var purchase_before_tax = parseFloat(purchase_before_discount) - __calculate_amount('percentage', discount_percent, purchase_before_discount);

        __write_number(row.find('input.purchase_unit_cost'), purchase_before_tax, true);

        var sub_total_before_tax = quantity * purchase_before_tax;

        //Tax
        var tax_rate = parseFloat(row.find('select.purchase_line_tax_id').find(':selected').data('tax_amount'));
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, true);

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true)
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);
        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, false, true));
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        update_inline_profit_percentage(row);
        update_table_total();
        update_grand_total();
    });

    $(document).on( 'change', '.purchase_unit_cost', function(){
        var row = $(this).closest('tr');
        var quantity = __read_number(row.find('input.purchase_quantity'), true);
        var purchase_before_tax = __read_number($(this), true);

        var sub_total_before_tax = quantity * purchase_before_tax;

        //Update unit cost price before discount
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var purchase_before_discount = __get_principle(purchase_before_tax, discount_percent, true);
        __write_number(row.find('input.purchase_unit_cost_without_discount'), purchase_before_discount, true);

        //Tax
        var tax_rate = parseFloat(row.find('select.purchase_line_tax_id').find(':selected').data('tax_amount'));
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, true);

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, false, true));
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        //row.find('.purchase_product_unit_tax_text').text( tax );
        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true)
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        update_inline_profit_percentage(row);
        update_table_total();
        update_grand_total();
    });

    $(document).on( 'change', 'select.purchase_line_tax_id', function(){
        var row = $(this).closest('tr');
        var purchase_before_tax = __read_number(row.find('.purchase_unit_cost'), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        //Tax
        var tax_rate = parseFloat($(this).find(':selected').data('tax_amount'));
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        //Purchase price
        var purchase_after_tax = purchase_before_tax + tax;
        var sub_total_after_tax = quantity * purchase_after_tax;

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, false, true));
        __write_number(row.find('input.purchase_product_unit_tax'), tax, true);

        __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_after_tax, true);

        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        update_table_total();
        update_grand_total();
    });

    $(document).on( 'change', '.purchase_unit_cost_after_tax', function(){
        var row = $(this).closest('tr');
        var purchase_after_tax = __read_number($(this), true);
        var quantity = __read_number(row.find('input.purchase_quantity'), true);

        var sub_total_after_tax = purchase_after_tax * quantity;

        //Tax
        var tax_rate = parseFloat(row.find('select.purchase_line_tax_id').find(':selected').data('tax_amount'));
        var purchase_before_tax = __get_principle(purchase_after_tax, tax_rate);
        var sub_total_before_tax = quantity * purchase_before_tax;
        var tax = __calculate_amount('percentage', tax_rate, purchase_before_tax);

        //Update unit cost price before discount
        var discount_percent = __read_number(row.find('input.inline_discounts'), true);
        var purchase_before_discount = __get_principle(purchase_before_tax, discount_percent, true);
        __write_number(row.find('input.purchase_unit_cost_without_discount'), purchase_before_discount, true);
        
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(sub_total_after_tax, false, true));
        __write_number(row.find('input.row_subtotal_after_tax_hidden'), sub_total_after_tax, true);

        __write_number(row.find('.purchase_unit_cost'), purchase_before_tax, true);

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(sub_total_before_tax, false, true));
        __write_number(row.find('input.row_subtotal_before_tax_hidden'), sub_total_before_tax, true);

        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(tax, true, true));
        __write_number(row.find('input.purchase_product_unit_tax'), tax);

        update_table_total();
        update_grand_total();
    });

    $('#tax_id, #discount_type, #discount_amount, input#shipping_charges').change( function(){
        update_grand_total();
    });
    
    //Purchase table
    purchase_table = $('#purchase_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: '/purchases',
        columnDefs: [ {
            "targets": [7,8],
            "orderable": false,
            "searchable": false
        } ],
        columns: [
            { data: 'transaction_date', name: 'transaction_date'  },
            { data: 'ref_no', name: 'ref_no'},
            { data: 'location_name', name: 'BS.name'},
            { data: 'name', name: 'contacts.name'},
            { data: 'status', name: 'status'},
            { data: 'payment_status', name: 'payment_status'},
            { data: 'final_total', name: 'final_total'},
            { data: 'payment_due', name: 'payment_due'},
            { data: 'action', name: 'action'}
        ],
        "fnDrawCallback": function (oSettings) {
            var total_purchase = sum_table_col($('#purchase_table'), 'final_total');
            $('#footer_purchase_total').text(total_purchase);

            var total_due = sum_table_col($('#purchase_table'), 'payment_due');
            $('#footer_total_due').text(total_due);

            $('#footer_status_count').html(__sum_status_html($('#purchase_table'), 'status-label'));
            
            $('#footer_payment_status_count').html(__sum_status_html($('#purchase_table'), 'payment-status-label'));
            
            __currency_convert_recursively($('#purchase_table'));
        },
        createdRow: function( row, data, dataIndex ) {
            $( row ).find('td:eq(5)').attr('class', 'clickable_td');
        }
    });

    update_table_sr_number();

    $('.quick_add_product_modal').on('shown.bs.modal', function () {
        $('.quick_add_product_modal').find('.select2').each( function(){
            $(this).select2({ dropdownParent: $(".quick_add_product_modal") });
        });
        $('.quick_add_product_modal').find('input[type="checkbox"].input-icheck').each( function(){
            $(this).iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue'
            })
        });
    });
    
    $(document).on('click', '#submit_quick_product', function(e){
        e.preventDefault();
        $("form#quick_add_product_form").validate({
            rules: {
                sku: {
                    remote: {
                        url: "/products/check_product_sku",
                        type: "post",
                        data: {
                            sku: function() {
                                return $( "#sku" ).val();
                            },
                            product_id: function() {
                                if($('#product_id').length > 0 ){
                                    return $('#product_id').val();
                                } else {
                                    return '';
                                }
                            },
                        }
                    }
                },
                expiry_period:{
                    required: {
                        depends: function(element) {
                            return ($('#expiry_period_type').val().trim() != '');
                        }
                    }
                }
            },
            messages: {
                sku: {
                    remote: LANG.sku_already_exists
                }
            }
        });
        if($("form#quick_add_product_form").valid()) {
            var form = $("form#quick_add_product_form");
            var url = $(form).attr('action');
            $.ajax({
                method: "POST",
                url: url,
                dataType: 'json',
                data: $(form).serialize(),
                success: function(data){
                    $('.quick_add_product_modal').modal('hide');
                    if( data.success){
                        toastr.success(data.msg);
                        get_purchase_entry_row( data.product.id, 0 );
                    } else {
                        toastr.error(data.msg);
                    }
                }
            });
        }
    });
    $(document).on( 'change', '.mfg_date', function(){
        var this_date = $(this).val();
        var this_moment = moment(this_date, moment_date_format);
        var expiry_period = parseFloat($(this).closest('td').find('.row_product_expiry').val());
        var expiry_period_type = $(this).closest('td').find('.row_product_expiry_type').val();
        if(this_date){
            if(expiry_period && expiry_period_type){
                exp_date = this_moment.add(expiry_period, expiry_period_type).format(moment_date_format);
                $(this).closest('td').find('.exp_date').datepicker('update', exp_date);
            } else {
                $(this).closest('td').find('.exp_date').datepicker('update', '');
            }
        } else {
            $(this).closest('td').find('.exp_date').datepicker('update', '');
        }
    });
    
    $('#purchase_entry_table tbody').find('.expiry_datepicker').each( function(){
        $(this).datepicker({
            autoclose: true,
            format:datepicker_date_format
        });
    });

    $(document).on( 'change', '.profit_percent', function(){
        var row = $(this).closest('tr');
        var profit_percent = __read_number($(this), true);

        var purchase_unit_cost = __read_number(row.find('input.purchase_unit_cost'), true);
        var default_sell_price = parseFloat(purchase_unit_cost) + __calculate_amount('percentage', profit_percent, purchase_unit_cost);
        var exchange_rate = $('input#exchange_rate').val();
        __write_number(row.find('input.default_sell_price'), default_sell_price * exchange_rate, true);
    });

    $(document).on( 'change', '.default_sell_price', function(){
        var row = $(this).closest('tr');
        update_inline_profit_percentage(row);
    });

    $('table#purchase_table tbody').on('click', 'a.delete-purchase', function(e){
        e.preventDefault();
        swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var href = $(this).attr('href');
                $.ajax({
                    method: "DELETE",
                    url: href,
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            purchase_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });
});

function get_purchase_entry_row( product_id, variation_id){

    if(product_id ){
        var row_count = $('#row_count').val();
        $.ajax({
            method: "POST",
            url: '/purchases/get_purchase_entry_row',
            dataType: "html",
            data: { 'product_id' : product_id, 'row_count': row_count, 'variation_id': variation_id},
            success: function(result){
                $(result).find('.purchase_quantity').each( function(){
                    
                    row = $(this).closest('tr');

                    $('#purchase_entry_table tbody').append(update_purchase_entry_row_values(row));
                    update_row_price_for_exchange_rate(row);

                    update_inline_profit_percentage(row);
                                        
                    update_table_total();
                    update_grand_total();
                    update_table_sr_number();
                });
                if ($(result).find('.purchase_quantity').length) {
                    $('#row_count').val($(result).find('.purchase_quantity').length + parseInt(row_count) );
                }
            }
        });
    }
}

function update_purchase_entry_row_values( row ){
    if(typeof row != 'undefined'){

        var quantity = __read_number(row.find('.purchase_quantity'), true);
        var unit_cost_price = __read_number(row.find('.purchase_unit_cost'), true);
        var row_subtotal_before_tax = quantity * unit_cost_price;

        var tax_rate = parseFloat( $('option:selected', row.find('.purchase_line_tax_id')).attr('data-tax_amount') );

        var unit_product_tax = __calculate_amount('percentage', tax_rate, unit_cost_price);

        var unit_cost_price_after_tax = unit_cost_price + unit_product_tax;
        var row_subtotal_after_tax = quantity * unit_cost_price_after_tax;

        row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(row_subtotal_before_tax, false, true));
        __write_number(row.find('.row_subtotal_before_tax_hidden'), row_subtotal_before_tax, true);
        __write_number(row.find('.purchase_product_unit_tax'), unit_product_tax, true);
        row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(unit_product_tax, false, true));
        row.find('.purchase_unit_cost_after_tax').text( __currency_trans_from_en(unit_cost_price_after_tax, true));
        row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(row_subtotal_after_tax, false, true));
        __write_number(row.find('.row_subtotal_after_tax_hidden'), row_subtotal_after_tax, true);

        row.find('.expiry_datepicker').each( function(){
            $(this).datepicker({
                autoclose: true,
                format:datepicker_date_format
            });
        });
        return row;
    }
}

function update_row_price_for_exchange_rate(row){
    var exchange_rate = $('input#exchange_rate').val();

    if(exchange_rate == 1) {
        return true;
    }

    var purchase_unit_cost_without_discount = __read_number(row.find('.purchase_unit_cost_without_discount'), true) / exchange_rate;
    __write_number(row.find('.purchase_unit_cost_without_discount'), purchase_unit_cost_without_discount, true);

    var purchase_unit_cost = __read_number(row.find('.purchase_unit_cost'), true) / exchange_rate;
    __write_number(row.find('.purchase_unit_cost'), purchase_unit_cost, true);


    var row_subtotal_before_tax_hidden = __read_number(row.find('.row_subtotal_before_tax_hidden'), true) / exchange_rate;
    row.find('.row_subtotal_before_tax').text(__currency_trans_from_en(row_subtotal_before_tax_hidden, false, true));
    __write_number(row.find('input.row_subtotal_before_tax_hidden'), row_subtotal_before_tax_hidden, true);

    var purchase_product_unit_tax = __read_number(row.find('.purchase_product_unit_tax'), true) / exchange_rate;
    __write_number(row.find('input.purchase_product_unit_tax'), purchase_product_unit_tax, true);
    row.find('.purchase_product_unit_tax_text').text(__currency_trans_from_en(purchase_product_unit_tax, false, true));

    var purchase_unit_cost_after_tax = __read_number(row.find('.purchase_unit_cost_after_tax'), true) / exchange_rate;
    __write_number(row.find('input.purchase_unit_cost_after_tax'), purchase_unit_cost_after_tax, true);

    var row_subtotal_after_tax_hidden = __read_number(row.find('.row_subtotal_after_tax_hidden'), true) / exchange_rate;
    __write_number(row.find('input.row_subtotal_after_tax_hidden'), row_subtotal_after_tax_hidden, true);
    row.find('.row_subtotal_after_tax').text(__currency_trans_from_en(row_subtotal_after_tax_hidden, false, true));
}

function iraqi_dinnar_selling_price_adjustment(row){
    var default_sell_price = __read_number(row.find('input.default_sell_price'), true);

    //Adjsustment
    var remaining = default_sell_price % 250;
    if(remaining >= 125 ){
        default_sell_price += (250-remaining);
    } else {
        default_sell_price -= remaining;
    }

    __write_number(row.find('input.default_sell_price'), default_sell_price, true);

    update_inline_profit_percentage(row);
}

function update_inline_profit_percentage(row){
    //Update Profit percentage
    var default_sell_price = __read_number(row.find('input.default_sell_price'), true);
    var exchange_rate = $('input#exchange_rate').val();
    default_sell_price_in_base_currency = default_sell_price / parseFloat(exchange_rate);

    var purchase_before_tax = __read_number(row.find('input.purchase_unit_cost'), true);
    var profit_percent = __get_rate(purchase_before_tax, default_sell_price_in_base_currency);
    __write_number(row.find('input.profit_percent'), profit_percent, true);
}

function update_table_total() {
    var total_quantity = 0;
    var total_st_before_tax = 0;
    var total_subtotal = 0;

    $('#purchase_entry_table tbody').find('tr').each( function(){
        total_quantity += __read_number($(this).find('.purchase_quantity'), true);
        total_st_before_tax += __read_number($(this).find('.row_subtotal_before_tax_hidden'), true);
        total_subtotal += __read_number($(this).find('.row_subtotal_after_tax_hidden'), true);
    });

    $('#total_quantity').text(__number_f(total_quantity, true));
    $('#total_st_before_tax').text(__currency_trans_from_en(total_st_before_tax, true, true));
    __write_number($('input#st_before_tax_input'), total_st_before_tax, true);
    
    $('#total_subtotal').text(__currency_trans_from_en(total_subtotal, true, true));
    __write_number($('input#total_subtotal_input'), total_subtotal, true);
}

function update_grand_total() {

    var st_before_tax = __read_number($('input#st_before_tax_input'), true);
    var total_subtotal = __read_number($('input#total_subtotal_input'), true);

    //Calculate Discount
    var discount_type = $('select#discount_type').val();
    var discount_amount = __read_number($('input#discount_amount'), true);
    var discount = __calculate_amount(discount_type, discount_amount, total_subtotal);
    $('#discount_calculated_amount').text(__currency_trans_from_en(discount, true, true));

    //Calculate Tax
    var tax_rate = parseFloat( $('option:selected', $('#tax_id') ).data('tax_amount'));
    var tax = __calculate_amount('percentage', tax_rate, total_subtotal - discount);
    __write_number($('input#tax_amount'), tax);
    $('#tax_calculated_amount').text(__currency_trans_from_en(tax, true, true));

    //Calculate shipping
    var shipping_charges = __read_number($('input#shipping_charges'), true);

    //Calculate Final total
    grand_total = (total_subtotal - discount) + tax + shipping_charges;

    __write_number($('input#grand_total_hidden'), grand_total, true);
    __write_number($('input.payment-amount'), grand_total, true);

    $('#grand_total').text(__currency_trans_from_en(grand_total, true, true));

     $('#payment_due').text(__currency_trans_from_en(0, true, true));

    //__currency_convert_recursively($(document));
}
$(document).on('change', 'input.payment-amount', function(){
    var payment = __read_number($(this), true);
    var grand_total = __read_number($('input#grand_total_hidden'), true);
    var bal = grand_total - payment;
    $('#payment_due').text(__currency_trans_from_en(bal, true, true));
});

function update_table_sr_number(){
    var sr_number = 1;
    $('table#purchase_entry_table tbody').find('.sr_number').each( function(){
        $(this).text(sr_number);
        sr_number++;
    });
}

$(document).on( 'click', 'button#submit_purchase_form', function(e){
    
    e.preventDefault();

    //Check if product is present or not.
    if($('table#purchase_entry_table tbody tr').length <= 0){
        toastr.warning(LANG.no_products_added);
        $('input#search_product').select();
        return false;
    }

    $('form#add_purchase_form').validate({
        rules: {
            ref_no: {
                remote: {
                    url: "/purchases/check_ref_number",
                    type: "post",
                    data: {
                        ref_no: function() {
                            return $( "#ref_no" ).val();
                        },
                        contact_id: function(){
                            return $( "#supplier_id" ).val();
                        },
                        purchase_id: function() {
                            if($('#purchase_id').length > 0 ){
                                return $('#purchase_id').val();
                            } else {
                                return '';
                            }
                        },
                    }
                }
            }
        },
        messages: {
            ref_no: {
                remote: LANG.ref_no_already_exists
            }
        }
    });

    if($('form#add_purchase_form').valid()) {
        $('form#add_purchase_form').submit();
    }
});