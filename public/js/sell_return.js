$(document).ready( function(){

	//For edit pos form
	if($('form#sell_return_form').length > 0){
		pos_total_row();
		pos_form_obj = $('form#sell_return_form');
	} else {
		pos_form_obj = $('form#add_pos_sell_form');
	}
	if($('form#sell_return_form').length > 0 || $('form#add_pos_sell_form').length > 0){
		initialize_printer();
	}

	$('select#select_location_id').change(function(){
		reset_pos_form();
	});

	//Date picker
    $('#transaction_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });

	//get customer
    $('#customer_id').select2({
    	ajax: {
      		url: '/contacts/customers',
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
      	minimumInputLength: 1
    });
    set_default_customer();

    //Add Product
	$( "#search_product" ).autocomplete({
		source: function(request, response) {
    		$.getJSON("/products/list", { location_id: $('input#location_id').val(), term: request.term }, response);
  			},
		minLength: 2,
		response: function(event,ui) {
			if (ui.content.length == 1)
			{
				ui.item = ui.content[0];
				if(ui.item.qty_available > 0){
					$(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
					$(this).autocomplete('close');
				}
			} else if (ui.content.length == 0) {
            	swal(LANG.no_products_found)
            	.then((value) => {
  					$('input#search_product').select();
				});
            }
		},
		select: function( event, ui ) {
			if(ui.item.enable_stock == 1){
				$(this).val(null);
                pos_product_row(ui.item.variation_id);
			} else{
				alert(LANG.out_of_stock);
			}
		}
	})
	.autocomplete( "instance" )._renderItem = function( ul, item ) {
		if(item.enable_stock != 1){
			
			var string = '<li class="ui-state-disabled">'+ item.name;
			if(item.type == 'variable'){
        		string += '-' + item.variation;
        	}
        	string += ' (' + item.sub_sku + ')' + "<br> Price: " + item.selling_price + ' (Out of stock) </li>';
            return $(string).appendTo(ul);
        } else {
        	var string =  "<div>" + item.name;
        	if(item.type == 'variable'){
        		string += '-' + item.variation;
        	}
        	string += ' (' + item.sub_sku + ')' + "<br> Price: " + item.selling_price + "</div>";
    		return $( "<li>" )
        		.append(string)
        		.appendTo( ul );
        }
    };

    //Updates for add sell
	$('select#discount_type, input#discount_amount').change( function(){
		pos_total_row();
	});

	//Update line total and check for quantity not greater than max quantity
	$('table#purchase_entry_table tbody').on('change', 'input.pos_quantity', function(){
		// var max_qty = parseFloat($(this).data('rule-max'));
		var entered_qty = __read_number($(this));

		var tr = $(this).parents('tr');

		var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));
		var line_total = entered_qty * unit_price_inc_tax;

		__write_number(tr.find('input.pos_line_total'), line_total, false, 2);
		
		pos_total_row();
	});

	//If change in unit price update price including tax and line total
	$('table#purchase_entry_table tbody').on('change', 'input.pos_unit_price', function(){

		var unit_price = __read_number($(this));
		var tr = $(this).parents('tr');

		var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var quantity = __read_number(tr.find('input.pos_quantity'));

		var unit_price_inc_tax = __add_percent(unit_price, tax_rate);
		var line_total = quantity * unit_price_inc_tax;

		__write_number(tr.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);
		__write_number(tr.find('input.pos_line_total'), line_total, false, 2);
		pos_each_row(tr);
		pos_total_row();
	});

	//If change in tax rate then update unit price according to it.
	$('table#purchase_entry_table tbody').on('change', 'select.tax_id', function(){

		var tr = $(this).parents('tr');

		var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));

		var unit_price = __get_principle(unit_price_inc_tax, tax_rate);
		__write_number(tr.find('input.pos_unit_price'), unit_price);
		pos_each_row(tr);
	});

	//If change in unit price including tax, update unit price
	$('table#purchase_entry_table tbody').on('change', 'input.pos_unit_price_inc_tax', function(){

		var unit_price_inc_tax = __read_number($(this));
		
		if(iraqi_selling_price_adjustment){
			unit_price_inc_tax = round_to_iraqi_dinnar(unit_price_inc_tax);
			__write_number($(this), unit_price_inc_tax);
		}

		var tr = $(this).parents('tr');

		var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var quantity = __read_number(tr.find('input.pos_quantity'));

		var line_total = quantity * unit_price_inc_tax;
		var unit_price = __get_principle(unit_price_inc_tax, tax_rate);

		__write_number(tr.find('input.pos_unit_price'), unit_price);
		__write_number(tr.find('input.pos_line_total'), line_total, false, 2);

		pos_each_row(tr);
		pos_total_row();
	});

	//Remove row on click on remove row
	$('table#purchase_entry_table tbody').on('click', 'i.pos_remove_row', function(){
		swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	$(this).parents('tr').remove();
				pos_total_row();
            }
        });
	});

	//Save
	$('button#submit_sell_return_form').click(function(){

		//Check if product is present or not.
		if($('table#purchase_entry_table tbody').find('.product_row').length <= 0){
			toastr.warning(LANG.no_products_added);
			return false;
		}

		pos_form_obj.submit();
	});

	pos_form_validator = pos_form_obj.validate({
		submitHandler: function(form) {
			var cnf = true;
			
			if(cnf){
			 	var data = $(form).serialize();
				var url = $(form).attr('action');
				$.ajax({
					method: "POST",
					url: url,
					data: data,
					dataType: "json",
					success: function(result){
						if(result.success == 1){
							toastr.success(result.msg);

							reset_pos_form();

							//Check if enabled or not
							if(result.receipt.is_enabled){
								pos_print(result.receipt);
							}
						} else {
							toastr.error(result.msg);
						}
					}
				});
			}
			return false;
		}
	});

	$(document).on('change', '.payment-amount', function(){
		calculate_balance_due();
	});

	$(document).on('click', 'div.product_cell_div', function(){
		//Check if location is not set then show error message.
		if($('input#location_id').val() == ''){
			toastr.warning(LANG.select_location);
		} else {
			pos_product_row($(this).data('variation_id'));
		}
	});
});

function pos_product_row(variation_id){

	//Get item addition method
    var item_addtn_method = 0; 
    var add_via_ajax = true;
    
    if($('#item_addition_method').length){
        item_addtn_method = $('#item_addition_method').val();
    }

    if(item_addtn_method == 0){
    	add_via_ajax = true;
    } else {

    	//Search for variation id in each row of pos table
        $('#purchase_entry_table tbody').find('tr').each( function(){

            var row_v_id = $(this).find('.row_variation_id').val();
            var enable_sr_no = $(this).find('.enable_sr_no').val();

            if(row_v_id == variation_id && enable_sr_no !== '1'){
            	add_via_ajax = false;

            	//Increment product quantity
                qty_element = $(this).find('.pos_quantity');
                var qty = __read_number(qty_element);
                __write_number(qty_element, qty + 1);
                qty_element.change();

                $('input#search_product').focus().select();
            }
        });
    }

	if(add_via_ajax){

		var product_row = $('input#product_row_count').val();
		var location_id = $('input#location_id').val();
		var customer_id = $('select#customer_id').val();

		$.ajax({
			method: "GET",
			url: "/sells/pos/get_product_row/" + variation_id + '/' + location_id,
			data: {product_row: product_row, customer_id: customer_id, 
					type: 'sell-return'},
			dataType: "json",
			success: function(result){
				if(result.success){
					$('table#purchase_entry_table tbody').append(result.html_content).find('input.pos_quantity');
					//increment row count
					$('input#product_row_count').val(parseInt(product_row) + 1);
					var this_row = $('table#purchase_entry_table tbody').find("tr").last();
					pos_each_row(this_row);
					pos_total_row();

					$('input#search_product').focus().select();
					
				} else {
					swal(result.msg).then((value) => {
  						$('input#search_product').focus().select();
					});
				}
			}
		});
	}
}

//Update values for each row
function pos_each_row(row_obj){
	var unit_price = __read_number(row_obj.find('input.pos_unit_price'));

	var tax_rate = row_obj.find('select.tax_id').find(':selected').data('rate');

	var unit_price_inc_tax = unit_price + __calculate_amount('percentage', tax_rate, unit_price);
	__write_number(row_obj.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);

	//var unit_price_inc_tax = __read_number(row_obj.find('input.pos_unit_price_inc_tax'));

	__write_number(row_obj.find('input.item_tax'), unit_price_inc_tax - unit_price);
}

function pos_total_row(){
	var total_quantity = 0;
	var price_total = 0;

	$('table#purchase_entry_table tbody tr').each(function(){
		total_quantity = total_quantity + __read_number($(this).find('input.pos_quantity'));
		price_total = price_total + __read_number($(this).find('input.pos_line_total'));
	});

	$('span.total_quantity').each( function(){
		$(this).html(__number_f(total_quantity));
	});

	//$('span.unit_price_total').html(unit_price_total);
	$('span#price_total').html(__currency_trans_from_en(price_total, false));
	__write_number($('input#total_subtotal_input'), price_total);

	calculate_billing_details(price_total);
}

function calculate_billing_details(price_total){
	var discount = pos_discount(price_total);

	var total_payable = price_total - discount;
	
	__write_number($('input#final_total_input'), total_payable);
	$('span#total_payable').text(__currency_trans_from_en(total_payable, true));
	//$('span.total_payable_span').text(__currency_trans_from_en(total_payable, true));
}

function pos_discount(total_amount){
	var calculation_type = $('#discount_type').val();
	var calculation_amount = __read_number($('#discount_amount'));

	var discount = __calculate_amount(calculation_type, calculation_amount, total_amount);
	
	$('span#total_discount').text(__currency_trans_from_en(discount, false));

	return discount;
}

function isValidPosForm(){
	flag = true;
	$('span.error').remove();

	if($('select#customer_id').val() == null || $('select#customer_id').val() == ''){
		flag = false;
		error = '<span class="error">' + LANG.required + '</span>';
		$(error).insertAfter($('select#customer_id').parent('div'));
	}

	if($('tr.product_row').length == 0){		
		flag = false;
		error = '<span class="error">' + LANG.no_products + '</span>';
		$(error).insertAfter($('input#search_product').parent('div'));
	}

	return flag;
}

function reset_pos_form(){
	
	if(pos_form_obj[0]){
		pos_form_obj[0].reset();
	}

	set_default_customer();
	set_location();

	$('tr.product_row').remove();
	$('span#total_discount, span#total_payable').text(0);
	$('span#price_total').text(0);
}

function set_default_customer(){
	var default_customer_id = $('#default_customer_id').val();
	var default_customer_name = $('#default_customer_name').val();
	var exists  = $('select#customer_id option[value='+default_customer_id+']').length;
	if ( exists == 0 ) {
		$("select#customer_id").append($('<option>', {value: default_customer_id, text: default_customer_name}));
	}
	
	$('select#customer_id').val(default_customer_id).trigger("change");
}

//Set the location and initialize printer
function set_location(){
	if($('input#location_id').length == 1){
		$('input#location_id').val($('select#select_location_id').val());
		//$('input#location_id').data('receipt_printer_type', $('select#select_location_id').find(':selected').data('receipt_printer_type'));
	}
	
	if($('input#location_id').val()){
		$('input#search_product').prop( "disabled", false ).focus();
	} else {
		$('input#search_product').prop( "disabled", true );
	}

	initialize_printer();
}

function initialize_printer(){
	if($('input#location_id').data('receipt_printer_type') == 'printer'){
		initializeSocket();
	}
}

function pos_print(receipt){
	//If printer type then connect with websocket
	if(receipt.print_type == 'printer'){

		var content = receipt;
		content.type = 'print-receipt';

		//Check if ready or not, then print.
		if(socket.readyState != 1){
			initializeSocket();
			setTimeout(function() {
				socket.send(JSON.stringify(content));
			}, 700);
		} else {
			socket.send(JSON.stringify(content));
		}

	} else if(receipt.html_content != '') {
		//If printer type browser then print content
		$('#receipt_section').html(receipt.html_content);
		__currency_convert_recursively($('#receipt_section'));
		setTimeout(function(){window.print();}, 1000);
	}
}