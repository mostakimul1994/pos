$(document).ready(function(){

	var start = $('input[name="date-filter"]:checked').data('start');
	var end = $('input[name="date-filter"]:checked').data('end');
	update_statistics(start, end);
	$(document).on('change', 'input[name="date-filter"]', function(){
		var start = $('input[name="date-filter"]:checked').data('start');
		var end = $('input[name="date-filter"]:checked').data('end');
		update_statistics(start, end);
	});

	//atock alert datatables
	var stock_alert_table = $('#stock_alert_table').DataTable({
					processing: true,
					serverSide: true,
					ordering: false,
					searching: false,
					dom: 'tirp',
					buttons:[],
					ajax: '/home/product-stock-alert'
			    });
	//payment dues datatables
	var payment_dues_table = $('#payment_dues_table').DataTable({
					processing: true,
					serverSide: true,
					ordering: false,
					searching: false,
					dom: 'tirp',
					buttons:[],
					ajax: '/home/payment-dues',
					"fnDrawCallback": function (oSettings) {
			            __currency_convert_recursively($('#payment_dues_table'));
			        }
			    });

	//Stock expiry report table
    stock_expiry_alert_table = $('#stock_expiry_alert_table').DataTable({
                    processing: true,
					serverSide: true,
					searching: false,
					dom: 'tirp',
                    "ajax": {
                        "url": "/reports/stock-expiry",
                        "data": function ( d ) {
                            d.exp_date_filter = $('#stock_expiry_alert_days').val();
                        }
                    },
                    "order": [[ 3, "asc" ]],
                    columns: [
                        {data: 'product', name: 'p.name'},
                        {data: 'location', name: 'l.name'},
                        {data: 'stock_left', name: 'stock_left'},
                        {data: 'exp_date', name: 'exp_date'},
                    ],
                    "fnDrawCallback": function (oSettings) {
                        __show_date_diff_for_human($('#stock_expiry_alert_table'));
                    }
                });
});

function update_statistics( start, end ){
	var data = { start: start, end: end };
	//get purchase details
	var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';
	$('.total_purchase').html(loader);
	$('.purchase_due').html(loader);
	$('.total_sell').html(loader);
	$('.invoice_due').html(loader);
	$.ajax({
		method: "POST",
		url: '/home/get-purchase-details',
		dataType: "json",
		data: data,
		success: function(data){
			$('.total_purchase').html(__currency_trans_from_en(data.total_purchase_inc_tax, true ));
			$('.purchase_due').html( __currency_trans_from_en(data.purchase_due, true));
		}
	});
	//get sell details
	$.ajax({
		method: "POST",
		url: '/home/get-sell-details',
		dataType: "json",
		data: data,
		success: function(data){
			$('.total_sell').html(__currency_trans_from_en(data.total_sell_inc_tax, true ));
			$('.invoice_due').html( __currency_trans_from_en(data.invoice_due, true));
		}
	});
}