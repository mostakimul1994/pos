$(document).ready( function(){

	$('form').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		if (keyCode === 13) { 
			e.preventDefault();
			return false;
		}
	});
	
	//For edit pos form
	if($('form#edit_pos_sell_form').length > 0){
		pos_total_row();
		pos_form_obj = $('form#edit_pos_sell_form');
	} else {
		pos_form_obj = $('form#add_pos_sell_form');
	}
	if($('form#edit_pos_sell_form').length > 0 || $('form#add_pos_sell_form').length > 0){
		initialize_printer();
	}

	$('select#select_location_id').change(function(){
		reset_pos_form();
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
      	minimumInputLength: 1,
      	language: {
		    noResults: function(){
		       	var name = $("#customer_id").data("select2").dropdown.$search.val();
		        return '<button type="button" data-name="' + name + '" class="btn btn-link add_new_customer"><i class="fa fa-plus-circle fa-lg" aria-hidden="true"></i>&nbsp; ' + __translate('add_name_as_new_customer', {'name': name}) +'</button>';
		    }
		},
	    escapeMarkup: function (markup) {
	        return markup;
	    }
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
		focus: function( event, ui ) {
			if(ui.item.qty_available <= 0){
				return false;
			}
		},
		select: function( event, ui ) {
			if(ui.item.enable_stock != 1 || ui.item.qty_available > 0){
				$(this).val(null);
                pos_product_row(ui.item.variation_id);
			} else{
				alert(LANG.out_of_stock);
			}
		}
	})
	.autocomplete( "instance" )._renderItem = function( ul, item ) {
		if(item.enable_stock == 1 && item.qty_available <= 0){
			
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

	//Update line total and check for quantity not greater than max quantity
	$('table#pos_table tbody').on('change', 'input.pos_quantity', function(){

		if(sell_form_validator){
			sell_form_validator.element($(this));
		}
		if(pos_form_validator){
			pos_form_validator.element($(this));
		}
		// var max_qty = parseFloat($(this).data('rule-max'));
		var entered_qty = __read_number($(this));

		var tr = $(this).parents('tr');

		var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));
		var line_total = entered_qty * unit_price_inc_tax;

		__write_number(tr.find('input.pos_line_total'), line_total, false, 2);
		tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true));
		
		pos_total_row();
	});

	//If change in unit price update price including tax and line total
	$('table#pos_table tbody').on('change', 'input.pos_unit_price', function(){

		var unit_price = __read_number($(this));
		var tr = $(this).parents('tr');

		//calculate discounted unit price
		var discounted_unit_price = calculate_discounted_unit_price(tr);

		var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var quantity = __read_number(tr.find('input.pos_quantity'));

		var unit_price_inc_tax = __add_percent(discounted_unit_price, tax_rate);
		var line_total = quantity * unit_price_inc_tax;

		__write_number(tr.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);
		__write_number(tr.find('input.pos_line_total'), line_total, false, 2);
		tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true));
		pos_each_row(tr);
		pos_total_row();
		round_row_to_iraqi_dinnar(tr);
	});

	//If change in tax rate then update unit price according to it.
	$('table#pos_table tbody').on('change', 'select.tax_id', function(){

		var tr = $(this).parents('tr');

		var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var unit_price_inc_tax = __read_number(tr.find('input.pos_unit_price_inc_tax'));

		var discounted_unit_price = __get_principle(unit_price_inc_tax, tax_rate);
		var unit_price = get_unit_price_from_discounted_unit_price(tr, discounted_unit_price);
		__write_number(tr.find('input.pos_unit_price'), unit_price);
		pos_each_row(tr);
	});

	//If change in unit price including tax, update unit price
	$('table#pos_table tbody').on('change', 'input.pos_unit_price_inc_tax', function(){

		var unit_price_inc_tax = __read_number($(this));
		
		if(iraqi_selling_price_adjustment){
			unit_price_inc_tax = round_to_iraqi_dinnar(unit_price_inc_tax);
			__write_number($(this), unit_price_inc_tax);
		}

		var tr = $(this).parents('tr');

		var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var quantity = __read_number(tr.find('input.pos_quantity'));

		var line_total = quantity * unit_price_inc_tax;
		var discounted_unit_price = __get_principle(unit_price_inc_tax, tax_rate);
		var unit_price = get_unit_price_from_discounted_unit_price(tr, discounted_unit_price);

		__write_number(tr.find('input.pos_unit_price'), unit_price);
		__write_number(tr.find('input.pos_line_total'), line_total, false, 2);
		tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true));

		pos_each_row(tr);
		pos_total_row();
	});

	//Change max quantity rule if lot number changes
	$('table#pos_table tbody').on('change', 'select.lot_number', function(){
		var qty_element = $(this).closest('tr').find('input.pos_quantity');
		if($(this).val()){
			var lot_qty = $('option:selected', $(this)).data('qty_available');
			var max_err_msg = $('option:selected', $(this)).data('msg-max');
			qty_element.attr( "data-rule-max-value", lot_qty);
			qty_element.attr( "data-msg-max-value",max_err_msg );

			qty_element.rules( "add", {
				'max-value': lot_qty,
				messages: {
				    'max-value': max_err_msg,
				}
			});

		} else {
			var default_qty = qty_element.data('qty_available');
			var default_err_msg = qty_element.data('msg_max_default');
			qty_element.attr( "data-rule-max-value",  );
			qty_element.attr( "data-msg-max-value", default_err_msg );

			qty_element.rules( "add", {
				'max-value': default_qty,
				messages: {
				    'max-value': default_err_msg,
				}
			});
		}
		qty_element.trigger('change');
	});

	//Change in row discount type or discount amount
	$('table#pos_table tbody').on('change', 'select.row_discount_type, input.row_discount_amount', 
	function(){
		var tr = $(this).parents('tr');

		//calculate discounted unit price
		var discounted_unit_price = calculate_discounted_unit_price(tr);

		var tax_rate = tr.find('select.tax_id').find(':selected').data('rate');
		var quantity = __read_number(tr.find('input.pos_quantity'));

		var unit_price_inc_tax = __add_percent(discounted_unit_price, tax_rate);
		var line_total = quantity * unit_price_inc_tax;

		__write_number(tr.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);
		__write_number(tr.find('input.pos_line_total'), line_total, false, 2);
		tr.find('span.pos_line_total_text').text(__currency_trans_from_en(line_total, true));
		pos_each_row(tr);
		pos_total_row();
		round_row_to_iraqi_dinnar(tr);
	});

	//Remove row on click on remove row
	$('table#pos_table tbody').on('click', 'i.pos_remove_row', function(){
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

	//Cancel the invoice
	$('button#pos-cancel').click(function(){
		swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	reset_pos_form();
            }
        });
	});

	//Save invoice as draft
	$('button#pos-draft').click(function(){

		//Check if product is present or not.
		if($('table#pos_table tbody').find('.product_row').length <= 0){
			toastr.warning(LANG.no_products_added);
			return false;
		}

		var is_valid = isValidPosForm();
		if(is_valid != true){
			return;
		}

		var data = pos_form_obj.serialize();
		data = data + '&status=draft&is_quotation=0';
		var url = pos_form_obj.attr('action');

		$.ajax({
			method: "POST",
			url: url,
			data: data,
			dataType: "json",
			success: function(result){
				if(result.success == 1){
					reset_pos_form();
					toastr.success(result.msg);
					get_recent_transactions('draft', $('div#tab_draft'));
				} else {
					toastr.error(result.msg);
				}
			}
		});
	});

	//Save invoice as Quotation
	$('button#pos-quotation').click(function(){

		//Check if product is present or not.
		if($('table#pos_table tbody').find('.product_row').length <= 0){
			toastr.warning(LANG.no_products_added);
			return false;
		}

		var is_valid = isValidPosForm();
		if(is_valid != true){
			return;
		}

		var data = pos_form_obj.serialize();
		data = data + '&status=draft&is_quotation=1';
		var url = pos_form_obj.attr('action');

		$.ajax({
			method: "POST",
			url: url,
			data: data,
			dataType: "json",
			success: function(result){
				if(result.success == 1){
					reset_pos_form();
					toastr.success(result.msg);

					//Check if enabled or not
					if(result.receipt.is_enabled){
						pos_print(result.receipt);
					}

					get_recent_transactions('quotation', $('div#tab_quotation'));
				} else {
					toastr.error(result.msg);
				}
			}
		});
	});

	//Finalize invoice, open payment modal
	$('button#pos-finalize').click(function(){

		//Check if product is present or not.
		if($('table#pos_table tbody').find('.product_row').length <= 0){
			toastr.warning(LANG.no_products_added);
			return false;
		}

		$('#modal_payment').modal('show');
	});

	$('#modal_payment').on('shown.bs.modal', function () {
  		$('#modal_payment').find('input').filter(':visible:first').focus().select();
	});

	//Finalize without showing payment options
	$('button.pos-express-finalize').click(function(){

		//Check if product is present or not.
		if($('table#pos_table tbody').find('.product_row').length <= 0){
			toastr.warning(LANG.no_products_added);
			return false;
		}

		var pay_method = $(this).data('pay_method');

		//Check for remaining balance & add it in 1st payment row
		var total_payable = __read_number($('input#final_total_input'));
		var total_paying = __read_number($('input#total_paying_input'));
		if(total_payable > total_paying){

			var bal_due = total_payable - total_paying;

			var first_row = $('#payment_rows_div').find('.payment-amount').first();
			var first_row_val = __read_number(first_row);
			first_row_val = first_row_val + bal_due;
			__write_number(first_row, first_row_val);
			first_row.trigger('change');
		}

		//Change payment method.
		$('#payment_rows_div').find('.payment_types_dropdown').first().val(pay_method);
		if(pay_method == 'card'){
			$('div#card_details_modal').modal('show');

		} else {
			pos_form_obj.submit();
		}
	});

	$('div#card_details_modal').on('shown.bs.modal', function (e) {
		$('input#card_number').focus();
	});

	//on save card details
	$('button#pos-save-card').click(function(){
		$('input#card_number_0').val($('#card_number').val());
		$('input#card_holder_name_0').val($('#card_holder_name').val());
		$('input#card_transaction_number_0').val($('#card_transaction_number').val());
		$('select#card_type_0').val($('#card_type').val());
		$('input#card_month_0').val($('#card_month').val());
		$('input#card_year_0').val($('#card_year').val());
		$('input#card_security_0').val($('#card_security').val());

		$('div#card_details_modal').modal('hide');
		pos_form_obj.submit();
	});

	//fix select2 input issue on modal 
	$('#modal_payment').find('.select2').each( function(){
		$(this).select2({
			dropdownParent: $('#modal_payment')
		});
	});

	$('button#add-payment-row').click(function(){
		var row_index = $('#payment_row_index').val();
		$.ajax({
			method: "POST",
			url: '/sells/pos/get_payment_row',
			data: { row_index: row_index },
			dataType: "html",
			success: function(result){
				if(result){
					var appended = $('#payment_rows_div').append(result);

					var total_payable = __read_number($('input#final_total_input'));
					var total_paying = __read_number($('input#total_paying_input'));
					var b_due = total_payable - total_paying;
					$(appended).find('input.payment-amount').focus();
					$(appended).find('input.payment-amount').last().val(__currency_trans_from_en(b_due, false)).change().select();
					__select2($(appended).find('.select2'));
					$('#payment_row_index').val( parseInt(row_index) + 1 );
				}
			}
		});
	});

	$(document).on('click', '.remove_payment_row',function(){
		swal({
          title: LANG.sure,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
            	$(this).closest('.payment_row').remove();
				calculate_balance_due();
            }
        });
	});

	pos_form_validator = pos_form_obj.validate({
		submitHandler: function(form) {

			// var total_payble = __read_number($('input#final_total_input'));
			// var total_paying = __read_number($('input#total_paying_input'));
			var cnf = true;

			//Ignore if the difference is less than 0.5
			if($('input#in_balance_due').val() >= 0.5) {
				cnf = confirm( LANG.paid_amount_is_less_than_payable );
				// if( total_payble > total_paying ){
				// 	cnf = confirm( LANG.paid_amount_is_less_than_payable );
				// } else if(total_payble < total_paying) {
				// 	alert( LANG.paid_amount_is_more_than_payable );
				// 	cnf = false;
				// }
			}
			
			if(cnf){
			 	var data = $(form).serialize();
				data = data + '&status=final&is_quotation=0';
				var url = $(form).attr('action');
				$.ajax({
					method: "POST",
					url: url,
					data: data,
					dataType: "json",
					success: function(result){
						if(result.success == 1){
							$('#modal_payment').modal('hide');
							toastr.success(result.msg);

							reset_pos_form();

							//Check if enabled or not
							if(result.receipt.is_enabled){
								pos_print(result.receipt);
							}

							get_recent_transactions('final', $('div#tab_final'));

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

	//Update discount
	$('button#posEditDiscountModalUpdate').click(function(){
		//Close modal
		$('div#posEditDiscountModal').modal('hide');

		//Update values
		$('input#discount_type').val($('select#discount_type_modal').val());
		__write_number($('input#discount_amount'), __read_number($('input#discount_amount_modal')));
		pos_total_row();
	});

	//Shipping
	$('button#posShippingModalUpdate').click(function(){
		//Close modal
		$('div#posShippingModal').modal('hide');

		//update shipping details
		$('input#shipping_details').val($('#shipping_details_modal').val());

		//Update shipping charges
		__write_number($('input#shipping_charges'), __read_number($('input#shipping_charges_modal')));

		//$('input#shipping_charges').val(__read_number($('input#shipping_charges_modal')));

		pos_total_row();
	});

	$('#posShippingModal').on('shown.bs.modal', function () {
		$('#posShippingModal').find('#shipping_details_modal').filter(':visible:first').focus().select();
	});
	
	$(document).on('shown.bs.modal', '.row_edit_product_price_model', function(){
		$('.row_edit_product_price_model').find('input').filter(':visible:first').focus().select();
	});

	//Update Order tax
	$('button#posEditOrderTaxModalUpdate').click(function(){
		//Close modal
		$('div#posEditOrderTaxModal').modal('hide');

		var tax_obj = $('select#order_tax_modal');
		var tax_id = tax_obj.val();
		var tax_rate = tax_obj.find(':selected').data('rate');

		$('input#tax_rate_id').val(tax_id);

		__write_number($('input#tax_calculation_amount'), tax_rate);
		pos_total_row();
	});

	//Displays list of recent transactions
	get_recent_transactions('final', $('div#tab_final'));
	get_recent_transactions('quotation', $('div#tab_quotation'));
	get_recent_transactions('draft', $('div#tab_draft'));

	$(document).on('click', '.add_new_customer', function(){
		$("#customer_id").select2("close");
		var name = $(this).data('name');
		$('.contact_modal').find('input#name').val(name);
		$('.contact_modal').find('select#contact_type').val('customer').closest('div.contact_type_div').addClass('hide');
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
						$("select#customer_id").append($('<option>', {value: result.data.id, 
							text: result.data.name}));
						$('select#customer_id').val(result.data.id).trigger("change");
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
	$('.register_details_modal, .close_register_modal').on('shown.bs.modal', function () {
	  	__currency_convert_recursively($(this));
	});

	//Updates for add sell
	$('select#discount_type, input#discount_amount, input#shipping_charges').change( function(){
		pos_total_row();
	});
	$('select#tax_rate_id').change( function(){
		var tax_rate = $(this).find(':selected').data('rate');
		__write_number($('input#tax_calculation_amount'), tax_rate);
		pos_total_row();
	});
	//Date picker
    $('#transaction_date').datepicker({
        autoclose: true,
        format: datepicker_date_format
    });

    //Direct sell submit
    sell_form = $('form#add_sell_form');
	if($('form#edit_sell_form').length){
		sell_form = $('form#edit_sell_form');
		pos_total_row();
	}
	sell_form_validator = sell_form.validate();

	$('button#submit-sell').click(function(){
		//Check if product is present or not.
		if($('table#pos_table tbody').find('.product_row').length <= 0){
			toastr.warning(LANG.no_products_added);
			return false;
		} 
		if(sell_form.valid()){
			sell_form.submit();
		}
	});

	//Show product list.
	get_product_suggestion_list($("select#product_category").val(), $('select#product_brand').val(), $('input#location_id').val(), null);
	$("select#product_category, select#product_brand").on("change", function(e) {
		var location_id = $('input#location_id').val();
		if(location_id != '' || location_id != undefined){
			get_product_suggestion_list($("select#product_category").val(), $('select#product_brand').val(), $('input#location_id').val(), null);
		}
	});
	//Product list pagination
	$(document).on('click', 'ul.pagination_ajax a', function(e){
		e.preventDefault();
		var location_id = $('input#location_id').val();
		var category_id = $("select#product_category").val();
		var brand_id = $("select#product_brand").val();

		var url = $(this).attr('href');
		get_product_suggestion_list(category_id, brand_id, location_id, url);
	});

	$(document).on('click', 'div.product_box', function(){
		//Check if location is not set then show error message.
		if($('input#location_id').val() == ''){
			toastr.warning(LANG.select_location);
		} else {
			pos_product_row($(this).data('variation_id'));
		}
	});

	$(document).on('shown.bs.modal', '.row_description_modal', function(){
		$(this).find('textarea').first().focus();
	});
	
	//Press enter on search product to jump into last quantty and vice-versa
	$('#search_product').keydown(function (e) {
	 var key = e.which;
	 if(key == 9)  // the tab key code
	  {
	  	e.preventDefault();
	   	if($('#pos_table tbody tr').length > 0){
	   		$('#pos_table tbody tr:last').find('input.pos_quantity').focus().select();
	   	};
	  }
	});
	$('#pos_table').on( 'keypress', 'input.pos_quantity', function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{
			$('#search_product').focus();
		}
	});
});

function get_product_suggestion_list(category_id, brand_id, location_id, url = null){
	if(url == null){
		url = "/sells/pos/get-product-suggestion";
	}
	$.ajax({
		method: "GET",
		url: url,
		data: {category_id: category_id, brand_id: brand_id, 
				location_id: location_id},
		dataType: "html",
		success: function(result){			
			$('div#product_list_body').html($(result).hide().fadeIn(700));
		}
	});
}

//Get recent transactions
function get_recent_transactions(status, element_obj){
	$.ajax({
		method: "GET",
		url: "/sells/pos/get-recent-transactions",
		data: {status: status},
		dataType: "html",
		success: function(result){
			element_obj.html(result);
			__currency_convert_recursively(element_obj);
		}
	});
}

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
        $('#pos_table tbody').find('tr').each( function(){

            var row_v_id = $(this).find('.row_variation_id').val();
            var enable_sr_no = $(this).find('.enable_sr_no').val();
            var modifiers_exist = false;
            if($(this).find('input.modifiers_exist').length > 0){
            	modifiers_exist = true;
            }

            if(row_v_id == variation_id && enable_sr_no !== '1' && !modifiers_exist){
            	add_via_ajax = false;

            	//Increment product quantity
                qty_element = $(this).find('.pos_quantity');
                var qty = __read_number(qty_element);
                __write_number(qty_element, qty + 1);
                qty_element.change();

                round_row_to_iraqi_dinnar($(this));

                $('input#search_product').focus().select();
            }
        });
    }

	if(add_via_ajax){

		var product_row = $('input#product_row_count').val();
		var location_id = $('input#location_id').val();
		var customer_id = $('select#customer_id').val();
		var is_direct_sell = false;
		if($('input[name="is_direct_sale"]').length > 0 && $('input[name="is_direct_sale"]').val() == 1){
			is_direct_sell = true;
		}

		$.ajax({
			method: "GET",
			url: "/sells/pos/get_product_row/" + variation_id + '/' + location_id,
			data: {product_row: product_row, customer_id: customer_id, is_direct_sell: is_direct_sell},
			dataType: "json",
			success: function(result){
				if(result.success){
					$('table#pos_table tbody').append(result.html_content).find('input.pos_quantity');
					//increment row count
					$('input#product_row_count').val(parseInt(product_row) + 1);
					var this_row = $('table#pos_table tbody').find("tr").last();
					pos_each_row(this_row);
					pos_total_row();
					if(result.enable_sr_no == '1'){
						var new_row = $('table#pos_table tbody').find("tr").last();
						new_row.find('.add-pos-row-description').trigger('click');
					}
					round_row_to_iraqi_dinnar(this_row);
					__currency_convert_recursively(this_row)

					$('input#search_product').focus().select();

					//Used in restaurant module
					if(result.html_modifier){
						$('table#pos_table tbody').find("tr").last().find("td:first").append(result.html_modifier);
					}

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

	var discounted_unit_price = calculate_discounted_unit_price(row_obj);

	var tax_rate = row_obj.find('select.tax_id').find(':selected').data('rate');

	var unit_price_inc_tax = discounted_unit_price + __calculate_amount('percentage', tax_rate, discounted_unit_price);
	__write_number(row_obj.find('input.pos_unit_price_inc_tax'), unit_price_inc_tax);

	//var unit_price_inc_tax = __read_number(row_obj.find('input.pos_unit_price_inc_tax'));

	__write_number(row_obj.find('input.item_tax'), unit_price_inc_tax - discounted_unit_price);
}

function pos_total_row(){
	var total_quantity = 0;
	var price_total = 0;

	$('table#pos_table tbody tr').each(function(){
		total_quantity = total_quantity + __read_number($(this).find('input.pos_quantity'));
		price_total = price_total + __read_number($(this).find('input.pos_line_total'));
	});

	//Go through the modifier prices.
	$('input.modifiers_price').each(function(){
		price_total = price_total + __read_number($(this));
	});

	//updating shipping charges
	$('span#shipping_charges_amount').text(__currency_trans_from_en(__read_number($('input#shipping_charges_modal')), false));
	

	$('span.total_quantity').each( function(){
		$(this).html(__number_f(total_quantity));
	});

	//$('span.unit_price_total').html(unit_price_total);
	$('span.price_total').html(__currency_trans_from_en(price_total, false));

	calculate_billing_details(price_total);
}

function calculate_billing_details(price_total){
	var discount = pos_discount(price_total);
	var order_tax = pos_order_tax( price_total, discount );

	//Add shipping charges.
	var shipping_charges = __read_number($('input#shipping_charges'));

	var total_payable = price_total + order_tax - discount + shipping_charges;

	__write_number($('input#final_total_input'), total_payable);
	$('span#total_payable').text(__currency_trans_from_en(total_payable, true));
	$('span.total_payable_span').text(__currency_trans_from_en(total_payable, true));

	//Check if edit form then don't update price.
	if( $('form#edit_pos_sell_form').length == 0 ){
		__write_number($('.payment-amount').first(), total_payable);
	}

	calculate_balance_due();
}

function pos_discount(total_amount){
	var calculation_type = $('#discount_type').val();
	var calculation_amount = __read_number($('#discount_amount'));

	var discount = __calculate_amount(calculation_type, calculation_amount, total_amount);
	
	$('span#total_discount').text(__currency_trans_from_en(discount, false));

	return discount;
}

function pos_order_tax(price_total, discount){

	var tax_rate_id = $('#tax_rate_id').val();
	var calculation_type = 'percentage';
	var calculation_amount = __read_number($('#tax_calculation_amount'));
	var total_amount = price_total - discount;

	if(tax_rate_id){
		var order_tax = __calculate_amount(calculation_type, calculation_amount, total_amount);
	} else {
		var order_tax = 0;
	}

	$('span#order_tax').text(__currency_trans_from_en(order_tax, false));

	return order_tax;
}

function calculate_balance_due(){
	var total_payable = __read_number($('#final_total_input'));
	var total_paying = 0;
	$('#payment_rows_div').find('.payment-amount').each( function(){
		if(parseFloat($(this).val())){
			total_paying += __read_number($(this));
		}
	});
	var bal_due = total_payable - total_paying;
	var change_return = 0;

	//change_return
	if(bal_due < 0 || Math.abs(bal_due) < 0.05){
		__write_number($('input#change_return'), bal_due*-1);
		$('span.change_return_span').text(__currency_trans_from_en(bal_due*-1, true));
		change_return = bal_due*-1;
		bal_due = 0;
	} else {
		__write_number($('input#change_return'), 0);
		$('span.change_return_span').text(__currency_trans_from_en(0, true));
		change_return = 0;
	}

	__write_number($('input#total_paying_input'), total_paying);
	$('span.total_paying').text(__currency_trans_from_en(total_paying, true));

	__write_number($('input#in_balance_due'), bal_due);
	$('span.balance_due').text(__currency_trans_from_en(bal_due, true));

	__highlight(bal_due*-1, $('span.balance_due'));
	__highlight(change_return*-1, $('span.change_return_span'));
}

function isValidPosForm(){
	flag = true;
	$('span.error').remove();

	if($('select#customer_id').val() == null){
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

	//If on edit page then redirect to Add POS page
	if($('form#edit_pos_sell_form').length > 0){
		setTimeout(function() {
			window.location = '/pos/create/';
		}, 4000);
		return true;
	}
	
	if(pos_form_obj[0]){
		pos_form_obj[0].reset();
	}
	if(sell_form[0]){
		sell_form[0].reset();
	}
	set_default_customer();
	set_location();

	$('tr.product_row').remove();
	$('span.total_quantity, span.price_total, span#total_discount, span#order_tax, span#total_payable').text(0);
	$('span.total_payable_span', 'span.total_paying', 'span.balance_due').text(0);

	$('#modal_payment').find('.remove_payment_row').each( function(){
		$(this).closest('.payment_row').remove();
	});

	//Reset discount
	__write_number($('input#discount_amount'), $('input#discount_amount').data('default'));
	$('input#discount_type').val($('input#discount_type').data('default'));

	//Reset tax rate
	$('input#tax_rate_id').val($('input#tax_rate_id').data('default'));
	__write_number($('input#tax_calculation_amount'), $('input#tax_calculation_amount').data('default'));

	$('select.payment_types_dropdown').val('cash').trigger('change');
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
	if($('select#select_location_id').length == 1){
		$('input#location_id').val($('select#select_location_id').val());
		$('input#location_id').data('receipt_printer_type', $('select#select_location_id').find(':selected').data('receipt_printer_type'));
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

$('body').on('click', 'label', function (e) {
    var field_id = $(this).attr('for');
    if (field_id) {
        if($("#"+field_id).hasClass('select2')) {
            $("#"+field_id).select2("open");
            return false;
        }
    }
});

$('body').on('focus', 'select', function (e) {
    var field_id = $(this).attr('id');
    if (field_id) {
        if($("#"+field_id).hasClass('select2')) {
            $("#"+field_id).select2("open");
            return false;
        }
    }
});

function round_row_to_iraqi_dinnar(row){
	if(iraqi_selling_price_adjustment){
		var element = row.find('input.pos_unit_price_inc_tax');
		var unit_price = round_to_iraqi_dinnar(__read_number(element));
		__write_number(element, unit_price);
		element.change();
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

function calculate_discounted_unit_price(row){
	var this_unit_price = __read_number(row.find('input.pos_unit_price'));
	var row_discounted_unit_price = this_unit_price;
	var row_discount_type = row.find('select.row_discount_type').val();
	var row_discount_amount = __read_number(row.find('input.row_discount_amount'));
	if(row_discount_amount){
		if(row_discount_type == 'fixed'){
			row_discounted_unit_price = this_unit_price - row_discount_amount;
		} else {
			row_discounted_unit_price = __substract_percent(this_unit_price, row_discount_amount);
		}
	}

	return row_discounted_unit_price;
}

function get_unit_price_from_discounted_unit_price(row, discounted_unit_price){
	var this_unit_price = discounted_unit_price;
	var row_discount_type = row.find('select.row_discount_type').val();
	var row_discount_amount = __read_number(row.find('input.row_discount_amount'));
	if(row_discount_amount){
		if(row_discount_type == 'fixed'){
			this_unit_price = discounted_unit_price + row_discount_amount;
		} else {
			this_unit_price = __get_principle(discounted_unit_price, row_discount_amount, true);
		}
	}

	return this_unit_price;
}