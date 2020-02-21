$(document).ready( function(){

    //Purchase & Sell report
    //Date range as a button
    if($('#purchase_sell_date_filter').length == 1){
        $('#purchase_sell_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#purchase_sell_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updatePurchaseSell();
            }
        );
        $('#purchase_sell_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#purchase_sell_date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
        });
        updatePurchaseSell();
    }

    //Supplier report
    supplier_report_tbl = $('#supplier_report_tbl').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: '/reports/customer-supplier',
                            columnDefs: [ 
                                {"targets": [3], "orderable": false, "searchable": false},
                                {"targets": [1, 2], "searchable": false},
                            ],
                            columns: [
                                {data: 'name', name: 'name'},
                                {data: 'total_purchase', name: 'total_purchase'},
                                {data: 'total_invoice', name: 'total_invoice'},
                                {data: 'due', name: 'due'}
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#supplier_report_tbl'));
                            }
                        });

    //Stock report table
    stock_report_table = $('#stock_report_table').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/stock-report",
                                "data": function ( d ) {
                                    d.location_id = $('#location_id').val();
                                    d.category_id = $('#category_id').val();
                                    d.sub_category_id = $('#sub_category_id').val();
                                    d.brand_id = $('#brand').val();
                                    d.unit_id = $('#unit').val();
                                }
                            },
                            columns: [
                                {
                                    "orderable": false,
                                    "searchable": false,
                                    "data": null,
                                    "defaultContent": ""
                                },
                                {data: 'sku', name: 'sku'},
                                {data: 'product', name: 'products.name'},
                                {data: 'unit_price', name: 'V.sell_price_inc_tax'},
                                {data: 'stock', name: 'stock', searchable: false},
                                {data: 'total_sold', name: 'total_sold', searchable: false},
                            ],
                            createdRow: function( row, data, dataIndex ) {
                                if( data.type == 'variable'){
                                    $( row ).find('td:eq(0)').addClass('details-control').attr( 'title', LANG.view_stock_details);
                                }
                            },
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#stock_report_table'));
                            }
                        });
    // Array to track the ids of the details displayed rows
    var detailRows = [];
 
    $('#stock_report_table tbody').on( 'click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = stock_report_table.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
 
            // Remove from the 'open' array
            detailRows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );

            row.child( get_stock_details( row.data() ) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    } );
 
    // On each draw, loop over the `detailRows` array and show any child rows
    stock_report_table.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td.details-control').trigger( 'click' );
        } );
    } );

    if($('#tax_report_date_filter').length == 1){
        $('#tax_report_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#tax_report_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updateTaxReport();
            }
        );
        $('#tax_report_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#tax_report_date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
        });
        updateTaxReport();
    }

    if($('#trending_product_date_range').length == 1){
        get_sub_categories();
        $('#trending_product_date_range').daterangepicker({
            ranges: ranges,
            autoUpdateInput: false,
            locale: {
                format: moment_date_format,
                cancelLabel: LANG.clear,
                applyLabel: LANG.apply,
                customRangeLabel: LANG.custom_range
            }
        });
        $('#trending_product_date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
        });

        $('#trending_product_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    }

    $('#stock_report_filter_form #location_id, #stock_report_filter_form #category_id, #stock_report_filter_form #sub_category_id, #stock_report_filter_form #brand, #stock_report_filter_form #unit,#stock_report_filter_form #view_stock_filter').change( function(){
        stock_report_table.ajax.reload();
        stock_expiry_report_table.ajax.reload();
    });

    $('#purchase_sell_location_filter').change( function(){
        updatePurchaseSell();
    });
    $('#tax_report_location_filter').change( function(){
        updateTaxReport();
    });

    //Stock Adjustment Report
    if($('#stock_adjustment_date_filter').length == 1){
        $('#stock_adjustment_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#stock_adjustment_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updateStockAdjustmentReport();
            }
        );
        $('#purchase_sell_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#purchase_sell_date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
        });
        updateStockAdjustmentReport();
    }

    $('#stock_adjustment_location_filter').change( function(){
        updateStockAdjustmentReport();
    });

    //Register report
    register_report_table = $('#register_report_table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: '/reports/register-report',
                            columnDefs: [ 
                                {"targets": [6], "orderable": false, "searchable": false},
                            ],
                            columns: [
                                {data: 'created_at', name: 'created_at'},
                                {data: 'closed_at', name: 'closed_at'},
                                {data: 'user_name', name: 'user_name'},
                                {data: 'total_card_slips', name: 'total_card_slips'},
                                {data: 'total_cheques', name: 'total_cheques'},
                                {data: 'closing_amount', name: 'closing_amount'},
                                {data: 'action', name: 'action'}
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#register_report_table'));
                            }
                        });
    $('.view_register').on('shown.bs.modal', function () {
        __currency_convert_recursively($(this));
    });
    $(document).on( 'submit', '#register_report_filter_form', function(e){
        e.preventDefault();
        updateRegisterReport();
    });

    $('#register_user_id, #register_status').change( function(){
        updateRegisterReport();
    });

    //Sales representative report
    if($('#sr_date_filter').length == 1){
        
        //date range setting
        $('input#sr_date_filter').daterangepicker(dateRangeSettings, 
            function (start, end) {
               $('input#sr_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updateSalesRepresentativeReport();
            }
        );
        $('input#sr_date_filter').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
        });

        $('input#sr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        //Sales representative report -> Total expense
        if($('span#sr_total_expenses').length > 0){
            salesRepresentativeTotalExpense();
        }
        //Sales representative report -> Total sales
        if($('span#sr_total_sales').length > 0){
            salesRepresentativeTotalSales();
        }

        //Sales representative report -> Sales
        sr_sales_report = $('table#sr_sales_report').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sells",
                "data": function ( d ) {
                    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    d.created_by = $('select#sr_id').val(),
                    d.location_id = $('select#sr_business_id').val(),
                    d.start_date = start,
                    d.end_date = end
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'total_paid', name: 'total_paid'},
                { data: 'total_remaining', name: 'total_remaining'}
            ],
            columnDefs: [
                    {
                        'searchable'    : false, 
                        'targets'       : [6] 
                    },
                ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sr_sales_report'));
            }
        });

        //Sales representative report -> Expenses
        sr_expenses_report = $('table#sr_expenses_report').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/expenses",
                "data": function ( d ) {
                    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    d.expense_for = $('select#sr_id').val(),
                    d.location_id = $('select#sr_business_id').val(),
                    d.start_date = start,
                    d.end_date = end
                }
            },
            columnDefs: [ {
                "targets": 7,
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'ref_no', name: 'ref_no'},
                { data: 'category', name: 'ec.name'},
                { data: 'location_name', name: 'bl.name'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'expense_for', name: 'expense_for'},
                { data: 'additional_notes', name: 'additional_notes'}
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sr_expenses_report'));
            }
        });

        //Sales representative report -> Sales with commission
        sr_sales_commission_report = $('table#sr_sales_with_commission_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sells",
                "data": function ( d ) {
                    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

                    d.commission_agent = $('select#sr_id').val(),
                    d.location_id = $('select#sr_business_id').val(),
                    d.start_date = start,
                    d.end_date = end
                }
            },
            columns: [
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'total_paid', name: 'total_paid'},
                { data: 'total_remaining', name: 'total_remaining'}
            ],
            columnDefs: [
                    {
                        'searchable'    : false, 
                        'targets'       : [6] 
                    },
                ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sr_sales_with_commission'));
            }
        });

        //Sales representive filter
        $('select#sr_id, select#sr_business_id').change( function(){
            updateSalesRepresentativeReport();
        });
    }

    //Stock expiry report table
    stock_expiry_report_table = $('table#stock_expiry_report_table').DataTable({
                    processing: true,
                    serverSide: true,
                    "ajax": {
                        "url": "/reports/stock-expiry",
                        "data": function ( d ) {
                            d.location_id = $('#location_id').val();
                            d.category_id = $('#category_id').val();
                            d.sub_category_id = $('#sub_category_id').val();
                            d.brand_id = $('#brand').val();
                            d.unit_id = $('#unit').val();
                            d.exp_date_filter = $('#view_stock_filter').val();
                        }
                    },
                    "order": [[ 5, "asc" ]],
                    columnDefs: [ 
                        {"targets": [7], "orderable": false, "searchable": false}
                    ],
                    columns: [
                        {data: 'product', name: 'p.name'},
                        {data: 'sku', name: 'p.sku'},
                        {data: 'ref_no', name: 't.ref_no'},
                        {data: 'location', name: 'l.name'},
                        {data: 'stock_left', name: 'stock_left', searchable: false},
                        {data: 'lot_number', name: 'lot_number'},
                        {data: 'exp_date', name: 'exp_date'},
                        {data: 'mfg_date', name: 'mfg_date'},
                        {data: 'edit', name: 'edit'},
                    ],
                    "fnDrawCallback": function (oSettings) {
                        __show_date_diff_for_human($('#stock_expiry_report_table'));
                        $('button.stock_expiry_edit_btn').click(function(){
                            var purchase_line_id = $(this).data('purchase_line_id');

                            $.ajax({
                                method: "GET",
                                url: '/reports/stock-expiry-edit-modal/' + purchase_line_id,
                                dataType: "html",
                                success: function(data){
                                    $('div.exp_update_modal').html(data).modal('show');
                                    $('input#exp_date_expiry_modal').datepicker({
                                        autoclose: true,
                                        format:datepicker_date_format
                                    });

                                    $('form#stock_exp_modal_form').submit(function(e){
                                        e.preventDefault();
                                        
                                        $.ajax({
                                            method: "POST",
                                            url:$('form#stock_exp_modal_form').attr('action'),
                                            dataType: "json",
                                            data: $('form#stock_exp_modal_form').serialize(),
                                            success: function(data){
                                                if(data.success == 1){
                                                    $('div.exp_update_modal').modal('hide');
                                                    toastr.success(data.msg);
                                                    stock_expiry_report_table.ajax.reload();
                                                } else {
                                                    toastr.error(data.msg);
                                                }
                                            }
                                        });
                                    })
                                }
                            });
                        })
                    }
                });

    //Profit / Loss
    if($('#profit_loss_date_filter').length == 1){
        $('#profit_loss_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#profit_loss_date_filter span').html(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                updateProfitLoss();
            }
        );
        $('#profit_loss_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#profit_loss_date_filter').html('<i class="fa fa-calendar"></i> ' + LANG.filter_by_date);
        });
        updateProfitLoss();
    }
    $('#profit_loss_location_filter').change( function(){
        updateProfitLoss();
    });

    //Product Purchase Report
    if($('#product_pr_date_filter').length == 1){
        $('#product_pr_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#product_pr_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                product_purchase_report.ajax.reload();
            }
        );
        $('#product_pr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_pr_date_filter').val('');
            product_purchase_report.ajax.reload();
        });
        $('#product_pr_date_filter').data('daterangepicker').setStartDate(moment());
        $('#product_pr_date_filter').data('daterangepicker').setEndDate(moment());
    }
    $('#product_purchase_report_form #variation_id, #product_purchase_report_form #location_id, #product_purchase_report_form #supplier_id, #product_purchase_report_form #product_pr_date_filter').change( function(){
        product_purchase_report.ajax.reload();
    });
    product_purchase_report = $('table#product_purchase_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[3, 'desc']],
        "ajax": {
            "url": "/reports/product-purchase-report",
            "data": function ( d ) {
                var start = '';
                var end = '';
                if($('#product_pr_date_filter').val()){
                    start = $('input#product_pr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#product_pr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
                d.variation_id = $('#variation_id').val();
                d.supplier_id = $('select#supplier_id').val();
                d.location_id = $('select#location_id').val();
            }
        },
        columns: [
            { data: 'product_name', name: 'p.name'  },
            { data: 'supplier', name: 'c.name'  },
            { data: 'ref_no', name: 't.ref_no'  },
            { data: 'transaction_date', name: 't.transaction_date'},
            { data: 'purchase_qty', name: 'purchase_lines.quantity'},
            { data: 'unit_purchase_price', name: 'purchase_lines.purchase_price_inc_tax' },
            { data: 'subtotal', name: 'subtotal', searchable: false}
        
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#product_purchase_report_table'));
        }
    });

    if($( "#search_product" ).length > 0){
        $( "#search_product" ).autocomplete({
            source: function( request, response ) {
                $.ajax( {
                  url: "/purchases/get_products",
                  dataType: "json",
                  data: {
                    term: request.term
                  },
                  success: function( data ) {
                    response( $.map(data, function(v,i) { 
                        if(v.variation_id){
                            return { label: v.text, value:v.variation_id }; 
                        } 
                        return false;
                     }));
                  }
                });
            },
            minLength: 2,
            select: function( event, ui ) {
                $('#variation_id').val(ui.item.value).change()
                event.preventDefault(); 
                $(this).val(ui.item.label);
            },
            focus: function(event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
            }
        });
    }

    //Product Sell Report
    if($('#product_sr_date_filter').length == 1){
        $('#product_sr_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#product_sr_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                product_sell_report.ajax.reload();
            }
        );
        $('#product_sr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
            product_sell_report.ajax.reload();
        });
        $('#product_sr_date_filter').data('daterangepicker').setStartDate(moment());
        $('#product_sr_date_filter').data('daterangepicker').setEndDate(moment());
    }
    product_sell_report = $('table#product_sell_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[3, 'desc']],
        "ajax": {
            "url": "/reports/product-sell-report",
            "data": function ( d ) {
                var start = '';
                var end = '';
                if($('#product_sr_date_filter').val()){
                    start = $('input#product_sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#product_sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;

                d.variation_id = $('#variation_id').val();
                d.customer_id = $('select#customer_id').val();
                d.location_id = $('select#location_id').val();
            }
        },
        columns: [
            { data: 'product_name', name: 'p.name'  },
            { data: 'customer', name: 'c.name'  },
            { data: 'invoice_no', name: 't.invoice_no'  },
            { data: 'transaction_date', name: 't.transaction_date'},
            { data: 'sell_qty', name: 'transaction_sell_lines.quantity'},
            { data: 'unit_sale_price', name: 'transaction_sell_lines.unit_price_inc_tax' },
            { data: 'subtotal', name: 'subtotal', searchable: false}
        
        ],
        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#product_sell_report_table'));
        }
    });

    $('#product_sell_report_form #variation_id, #product_sell_report_form #location_id, #product_sell_report_form #customer_id, #product_sell_report_form #product_sr_date_filter').change( function(){
        product_sell_report.ajax.reload();
    });
    
    $(document).on('click', '.remove_from_stock_btn', function(){
        swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    method: "GET",
                    url: $(this).data('href'),
                    dataType: "json",
                    success: function(result){
                        if(result.success == true){
                            toastr.success(result.msg);
                            stock_expiry_report_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
            }
        });
    });

    //Product lot Report
    lot_report = $('table#lot_report').DataTable({
        processing: true,
        serverSide: true,
        // aaSorting: [[3, 'desc']],

        "ajax": {
            "url": "/reports/lot-report",
            "data": function ( d ) {
                d.location_id = $('#location_id').val();
                d.category_id = $('#category_id').val();
                d.sub_category_id = $('#sub_category_id').val();
                d.brand_id = $('#brand').val();
                d.unit_id = $('#unit').val();
            }
        },
        columns: [
            {data: 'sub_sku', name: 'v.sub_sku'},
            {data: 'product', name: 'products.name'},
            {data: 'lot_number', name: 'pl.lot_number'},
            {data: 'exp_date', name: 'pl.exp_date'},
            {data: 'stock', name: 'stock', searchable: false},
            {data: 'total_sold', name: 'total_sold', searchable: false},
        ],

        "fnDrawCallback": function (oSettings) {
            __currency_convert_recursively($('#lot_report'));
            __show_date_diff_for_human($('#lot_report'));
        }
    });

    if($('table#lot_report').length == 1){
        $('#location_id, #category_id, #sub_category_id, #unit, #brand').change( function(){
        lot_report.ajax.reload();
        });
    }

    //Purchase Payment Report
    purchase_payment_report = $('table#purchase_payment_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/reports/purchase-payment-report",
            "data": function ( d ) {
                d.supplier_id = $('select#supplier_id').val();
                d.location_id = $('select#location_id').val();
                var start = '';
                var end = '';
                if($('input#ppr_date_filter').val()){
                    start = $('input#ppr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#ppr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
            }
        },
        columns: [
            { data: 'payment_ref_no', name: 'payment_ref_no'  },
            { data: 'paid_on', name: 'paid_on'  },
            { data: 'amount', name: 'transaction_payments.amount'  },
            { data: 'supplier', name: 'c.name'},
            { data: 'method', name: 'method' },
            { data: 'ref_no', name: 't.ref_no' },
        
        ],
        "fnDrawCallback": function (oSettings) {
            var total_amount = sum_table_col($('#purchase_payment_report_table'), 'paid-amount');
            $('#footer_total_amount').text(total_amount);
            __currency_convert_recursively($('#purchase_payment_report_table'));
        }
    });

    if($('#ppr_date_filter').length == 1){
        $('#ppr_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#ppr_date_filter span').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                purchase_payment_report.ajax.reload();
            }
        );
        $('#ppr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#ppr_date_filter').val('');
            purchase_payment_report.ajax.reload();
        });
    }

    $('#purchase_payment_report_form #location_id, #purchase_payment_report_form #supplier_id').change( function(){
        purchase_payment_report.ajax.reload();
    });

    //Sell Payment Report
    sell_payment_report = $('table#sell_payment_report_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/reports/sell-payment-report",
            "data": function ( d ) {
                d.supplier_id = $('select#customer_id').val();
                d.location_id = $('select#location_id').val();

                var start = '';
                var end = '';
                if($('input#spr_date_filter').val()){
                    start = $('input#spr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    end = $('input#spr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
                }
                d.start_date = start;
                d.end_date = end;
            }
        },
        columns: [
            { data: 'payment_ref_no', name: 'payment_ref_no'  },
            { data: 'paid_on', name: 'paid_on'  },
            { data: 'amount', name: 'transaction_payments.amount'  },
            { data: 'customer', name: 'c.name'},
            { data: 'method', name: 'method' },
            { data: 'invoice_no', name: 't.invoice_no' },
        
        ],
        "fnDrawCallback": function (oSettings) {
            var total_amount = sum_table_col($('#sell_payment_report_table'), 'paid-amount');
            $('#footer_total_amount').text(total_amount);
            __currency_convert_recursively($('#sell_payment_report_table'));
        }
    });

    if($('#spr_date_filter').length == 1){
        $('#spr_date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#spr_date_filter span').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_payment_report.ajax.reload();
            }
        );
        $('#spr_date_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#spr_date_filter').val('');
            sell_payment_report.ajax.reload();
        });
    }

    $('#sell_payment_report_form #location_id, #sell_payment_report_form #customer_id').change( function(){
        sell_payment_report.ajax.reload();
    });

});

function updatePurchaseSell(){

    var start = $('#purchase_sell_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('#purchase_sell_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var location_id = $('#purchase_sell_location_filter').val();

    var data = { start_date: start, end_date: end, location_id: location_id  };
    
    var loader = __fa_awesome();
    $('.total_purchase').html(loader);
    $('.purchase_due').html(loader);
    $('.total_sell').html(loader);
    $('.invoice_due').html(loader);

    $.ajax({
        method: "GET",
        url: '/reports/purchase-sell',
        dataType: "json",
        data: data,
        success: function(data){

            $('.total_purchase').html(__currency_trans_from_en( data.purchase.total_purchase_exc_tax, true ));
            $('.purchase_inc_tax').html(__currency_trans_from_en( data.purchase.total_purchase_inc_tax, true ));
            $('.purchase_due').html(__currency_trans_from_en( data.purchase.purchase_due, true ));

            $('.total_sell').html(__currency_trans_from_en( data.sell.total_sell_exc_tax, true ));
            $('.sell_inc_tax').html(__currency_trans_from_en( data.sell.total_sell_inc_tax, true ));
            $('.sell_due').html(__currency_trans_from_en( data.sell.invoice_due, true ));

            $('.sell_minus_purchase').html(__currency_trans_from_en( data.difference.total, true ));
            __highlight(data.difference.total, $('.sell_minus_purchase'));

            $('.difference_due').html(__currency_trans_from_en( data.difference.due, true ));
            __highlight(data.difference.due, $('.difference_due'));

            // $('.purchase_due').html( __currency_trans_from_en(data.purchase_due, true));
        }
    });
}

function get_stock_details ( rowData ) {
    var div = $('<div/>')
        .addClass( 'loading' )
        .text( 'Loading...' );
    var location_id = $('#location_id').val();
    $.ajax( {
        url: '/reports/stock-details?location_id=' + location_id,
        data: {
            product_id: rowData.DT_RowId
        },
        dataType: 'html',
        success: function ( data ) {
            div
                .html( data )
                .removeClass( 'loading' );
            __currency_convert_recursively(div);
        }
    } );
 
    return div;
}

function updateTaxReport(){
    var start = $('#tax_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('#tax_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var location_id = $('#tax_report_location_filter').val();
    var data = { start_date: start, end_date: end, location_id: location_id };
    
    var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';
    $('.input_tax').html(loader);
    $('.output_tax').html(loader);
    $('.tax_diff').html(loader);
    $.ajax({
        method: "GET",
        url: '/reports/tax-report',
        dataType: "json",
        data: data,
        success: function(data){
            $('.input_tax').html(data.input_tax);
            __currency_convert_recursively($('.input_tax'));
            $('.output_tax').html(data.output_tax);
             __currency_convert_recursively($('.output_tax'));
            $('.tax_diff').html(__currency_trans_from_en( data.tax_diff, true ));
            __highlight(data.tax_diff, $('.tax_diff'));
        }
    });
}

function updateStockAdjustmentReport(){

    var location_id = $('#stock_adjustment_location_filter').val();
    var start = $('#stock_adjustment_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('#stock_adjustment_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var data = { start_date: start, end_date: end, location_id: location_id  };
    
    var loader = __fa_awesome();
    $('.total_amount').html(loader);
    $('.total_recovered').html(loader);
    $('.total_normal').html(loader);
    $('.total_abnormal').html(loader);

    $.ajax({
        method: "GET",
        url: '/reports/stock-adjustment-report',
        dataType: "json",
        data: data,
        success: function(data){
            $('.total_amount').html(__currency_trans_from_en( data.total_amount, true ));
            $('.total_recovered').html(__currency_trans_from_en( data.total_recovered, true ));
            $('.total_normal').html(__currency_trans_from_en( data.total_normal, true ));
            $('.total_abnormal').html(__currency_trans_from_en( data.total_abnormal, true ));
        }
    });

    stock_adjustment_table.ajax.url( '/stock-adjustments?location_id=' + location_id + '&start_date=' + start +
                '&end_date=' + end ).load();
}

function updateRegisterReport(){
    var data = {
        user_id: $('#register_user_id').val(),
        status: $('#register_status').val(),
    }
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    register_report_table.ajax.url( '/reports/register-report?' + url_data).load();
}

function updateSalesRepresentativeReport(){
    //Update total expenses and total sales
    salesRepresentativeTotalExpense();
    salesRepresentativeTotalSales();
    salesRepresentativeTotalCommission();

    //Expense and expense table refresh
    sr_expenses_report.ajax.reload();
    sr_sales_report.ajax.reload();
    sr_sales_commission_report.ajax.reload();
}

function salesRepresentativeTotalExpense(){

    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var data_expense = {
        expense_for: $('select#sr_id').val(),
        location_id: $('select#sr_business_id').val(),
        start_date: start,
        end_date: end
    }

    $('span#sr_total_expenses').html(__fa_awesome());

    $.ajax({
        method: "GET",
        url: '/reports/sales-representative-total-expense',
        dataType: "json",
        data: data_expense,
        success: function(data){
            $('span#sr_total_expenses').html(__currency_trans_from_en(data.total_expense, true));
        }
    });
}

function salesRepresentativeTotalSales(){

    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var data_expense = {
        created_by: $('select#sr_id').val(),
        location_id: $('select#sr_business_id').val(),
        start_date: start,
        end_date: end
    }

    $('span#sr_total_sales').html(__fa_awesome());

    $.ajax({
        method: "GET",
        url: '/reports/sales-representative-total-sell',
        dataType: "json",
        data: data_expense,
        success: function(data){
            $('span#sr_total_sales').html(__currency_trans_from_en(data.total_sell_exc_tax, true));
        }
    });
}

function salesRepresentativeTotalCommission(){

    var start = $('input#sr_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('input#sr_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');

    var data_sell = {
        commission_agent: $('select#sr_id').val(),
        location_id: $('select#sr_business_id').val(),
        start_date: start,
        end_date: end
    }

    $('span#sr_total_commission').html(__fa_awesome());
    if(data_sell.commission_agent){
        $('div#total_commission_div').removeClass('hide');
        $.ajax({
            method: "GET",
            url: '/reports/sales-representative-total-commission',
            dataType: "json",
            data: data_sell,
            success: function(data){
                var str = '<div style="padding-right:15px; display: inline-block">' + __currency_trans_from_en(data.total_commission, true) + '</div>';
                if(data.commission_percentage != 0){
                    str += ' <small>(' + data.commission_percentage + '% of ' + __currency_trans_from_en(data.total_sales_with_commission) + ')</small>';
                }
                
                $('span#sr_total_commission').html(str);
            }
        });
    } else {
        $('div#total_commission_div').addClass('hide');
    }
}

function updateProfitLoss(){

    var start = $('#profit_loss_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var end = $('#profit_loss_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
    var location_id = $('#profit_loss_location_filter').val();

    var data = { start_date: start, end_date: end, location_id: location_id  };
    
    var loader = __fa_awesome();
    $('.opening_stock, .total_transfer_shipping_charges, .closing_stock, .total_sell, .total_purchase, .total_expense, .net_profit, .total_adjustment, .total_recovered, .total_sell_discount, .total_purchase_discount').html(loader);

    $.ajax({
        method: "GET",
        url: '/reports/profit-loss',
        dataType: "json",
        data: data,
        success: function(data){
            $('.opening_stock').html(__currency_trans_from_en( data.opening_stock, true ));
            $('.closing_stock').html(__currency_trans_from_en( data.closing_stock, true ));
            $('.total_sell').html(__currency_trans_from_en( data.total_sell, true ));
            $('.total_purchase').html(__currency_trans_from_en( data.total_purchase, true ));
            $('.total_expense').html(__currency_trans_from_en( data.total_expense, true ));
            $('.net_profit').html(__currency_trans_from_en( data.net_profit, true ));
            $('.total_adjustment').html(__currency_trans_from_en( data.total_adjustment, true ));
            $('.total_recovered').html(__currency_trans_from_en( data.total_recovered, true ));
            $('.total_transfer_shipping_charges').html(__currency_trans_from_en( data.total_transfer_shipping_charges, true ));
            $('.total_purchase_discount').html(__currency_trans_from_en( data.total_purchase_discount, true ));
            $('.total_sell_discount').html(__currency_trans_from_en( data.total_sell_discount, true ));
            __highlight(data.net_profit, $('.net_profit'));
        }
    });
}