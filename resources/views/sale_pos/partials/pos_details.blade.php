<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-body bg-gray disabled" style="margin-bottom: 0px !important">
				<div class="table-responsive">
				<table class="table table-condensed" 
					style="margin-bottom: 0px !important">
					<tbody>
					<tr>
						<td class="col-md-1">
							<b>@lang('sale.item'):</b> 
							<br/>
							<span class="total_quantity">0</span>
						</td>

						<td class="col-md-2">
							<b>@lang('sale.total'):</b> 
							<br/>
							<span class="price_total">0</span>
						</td>

						<td class="col-md-2">

							<span class="@if($pos_settings['disable_discount'] != 0) hide @endif">

							<b>@lang('sale.discount')(-): @show_tooltip(__('tooltip.sale_discount'))</b> 
							<br/>
							<i class="fa fa-pencil-square-o cursor-pointer" id="pos-edit-discount" title="@lang('sale.edit_discount')" aria-hidden="true" data-toggle="modal" data-target="#posEditDiscountModal"></i>
							<span id="total_discount">0</span>
							<input type="hidden" name="discount_type" id="discount_type" value="@if(empty($edit)){{'percentage'}}@else{{$transaction->discount_type}}@endif" data-default="percentage">

							<input type="hidden" name="discount_amount" id="discount_amount" value="@if(empty($edit)) {{@num_format($business_details->default_sales_discount)}} @else {{@num_format($transaction->discount_amount)}} @endif" data-default="{{$business_details->default_sales_discount}}">

							</span>
						</td>

						<td class="col-md-2">

							<span class="@if($pos_settings['disable_order_tax'] != 0) hide @endif">

							<b>@lang('sale.order_tax')(+): @show_tooltip(__('tooltip.sale_tax'))</b>
							<br/>
							<i class="fa fa-pencil-square-o cursor-pointer" title="@lang('sale.edit_order_tax')" aria-hidden="true" data-toggle="modal" data-target="#posEditOrderTaxModal" id="pos-edit-tax" ></i> 
							<span id="order_tax">
								@if(empty($edit))
									0
								@else
									{{$transaction->tax_amount}}
								@endif
							</span>

							<input type="hidden" name="tax_rate_id" 
								id="tax_rate_id" 
								value="@if(empty($edit)) {{$business_details->default_sales_tax}} @else {{$transaction->tax_id}} @endif" 
								data-default="{{$business_details->default_sales_tax}}">

							<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
								value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format(optional($transaction->tax)->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">

							</span>
						</td>
						
						<!-- shipping -->
						<td class="col-md-2">

							<span class="@if($pos_settings['disable_discount'] != 0) hide @endif">

							<b>@lang('sale.shipping')(+): @show_tooltip(__('tooltip.shipping'))</b> 
							<br/>
							<i class="fa fa-pencil-square-o cursor-pointer"  title="@lang('sale.shipping')" aria-hidden="true" data-toggle="modal" data-target="#posShippingModal"></i>
							<span id="shipping_charges_amount">0</span>
							<input type="hidden" name="shipping_details" id="shipping_details" value="@if(empty($edit)){{""}}@else{{$transaction->shipping_details}}@endif" data-default="">

							<input type="hidden" name="shipping_charges" id="shipping_charges" value="@if(empty($edit)){{@num_format(0.00)}} @else{{@num_format($transaction->shipping_charges)}} @endif" data-default="0.00">

							</span>
						</td>

						
						<td class="col-md-3">
							<b>@lang('sale.total_payable'):</b>
							@if(empty($edit))
								<button type="button" class="btn btn-danger btn-flat btn-xs pull-right" id="pos-cancel">@lang('sale.cancel')</button>
							@else
								<button type="button" class="btn btn-danger btn-flat hide btn-xs pull-right" id="pos-delete">@lang('messages.delete')</button>
							@endif
							<br/>
							<input type="hidden" name="final_total" 
								id="final_total_input" value=0>
							<span id="total_payable" class="text-success lead text-bold">0</span>
						</td>
					</tr>

					<tr>
						<td colspan="6">
						<div class="">

							<div class="col-sm-2 col-no-padding">

								<button type="button" 
									class="btn btn-warning btn-block btn-flat @if($pos_settings['disable_draft'] != 0) hide @endif" 
									id="pos-draft">@lang('sale.draft')</button>

								<button type="button" 
									class="btn btn-info btn-block btn-flat" 
									id="pos-quotation">@lang('lang_v1.quotation')</button>
							</div>

							<div class="col-sm-10 col-no-padding">

								<div class="col-sm-4 col-2px-padding">
									<button type="button" class="btn bg-navy  btn-block btn-flat btn-lg no-print @if($pos_settings['disable_pay_checkout'] != 0) hide @endif pos-express-btn" id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')">
									<div class="text-center">
										<i class="fa fa-check" aria-hidden="true"></i>
	    								<b>@lang('lang_v1.checkout_multi_pay')</b>
	    							</div>
									</button>
								</div>

								<div class="col-sm-4 col-2px-padding">
									<button type="button" 
									class="btn bg-maroon btn-block btn-flat btn-lg no-print pos-express-btn pos-express-finalize" 
									data-pay_method="card"
									title="@lang('lang_v1.tooltip_express_checkout_card')" >
									<div class="text-center">
										<i class="fa fa-check" aria-hidden="true"></i>
	    								<b>@lang('lang_v1.express_checkout_card')</b>
	    							</div>
									</button>
								</div>

								<div class="col-sm-4 col-2px-padding">
									<button type="button" class="btn btn-success btn-block btn-flat btn-lg no-print @if($pos_settings['disable_express_checkout'] != 0) hide @endif pos-express-btn pos-express-finalize"
									data-pay_method="cash"
									title="@lang('tooltip.express_checkout')">
									<div class="col-md-9 text-center">
										<i class="fa fa-check" aria-hidden="true"></i>
	    								<b>@lang('lang_v1.express_checkout_cash')</b>
	    							</div>
									</button>
								</div>
							</div>

						</div>

						</td>
					</tr>

					</tbody>
				</table>
				</div>

				<!-- Button to perform various actions -->
				<div class="row">
					
				</div>
			</div>
		</div>
	</div>
</div>

@if(isset($transaction))
	@include('sale_pos.partials.edit_discount_modal', ['sales_discount' => $transaction->discount_amount, 'discount_type' => $transaction->discount_type])
@else
	@include('sale_pos.partials.edit_discount_modal', ['sales_discount' => $business_details->default_sales_discount, 'discount_type' => 'percentage'])
@endif

@if(isset($transaction))
	@include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $transaction->tax_id])
@else
	@include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $business_details->default_sales_tax])
@endif

@if(isset($transaction))
	@include('sale_pos.partials.edit_shipping_modal', ['shipping_charges' => $transaction->shipping_charges, 'shipping_details' => $transaction->shipping_details])
@else
	@include('sale_pos.partials.edit_shipping_modal', ['shipping_charges' => '0.00', 'shipping_details' => ''])
@endif