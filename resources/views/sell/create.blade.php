@extends('layouts.app')

@section('title', __('sale.add_sale'))

@section('content')
<input type="hidden" id="__precision" value="{{config('constants.currency_precision')}}">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('sale.add_sale')</h1>
</section>
<!-- Main content -->
<section class="content no-print">
@if(is_null($default_location))
<div class="row">
	<div class="col-sm-3">
		<div class="form-group">
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-map-marker"></i>
				</span>
			{!! Form::select('select_location_id', $business_locations, null, ['class' => 'form-control input-sm', 
			'placeholder' => __('lang_v1.select_location'),
			'id' => 'select_location_id', 
			'required', 'autofocus'], $bl_attributes); !!}
			<span class="input-group-addon">
					@show_tooltip(__('tooltip.sale_location'))
				</span> 
			</div>
		</div>
	</div>
</div>
@endif
<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
	{!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'id' => 'add_sell_form' ]) !!}
	<div class="row">
		<div class="col-md-12 col-sm-12">
			<div class="box box-solid">
				{!! Form::hidden('location_id', $default_location, ['id' => 'location_id', 'data-receipt_printer_type' => isset($bl_attributes[$default_location]['data-receipt_printer_type']) ? $bl_attributes[$default_location]['data-receipt_printer_type'] : 'browser']); !!}

				<!-- /.box-header -->
				<div class="box-body">

					<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-4 @endif">
						<div class="form-group">
							{!! Form::label('contact_id', __('contact.customer') . ':*') !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-user"></i>
								</span>
								<input type="hidden" id="default_customer_id" 
								value="{{ $walk_in_customer['id']}}" >
								<input type="hidden" id="default_customer_name" 
								value="{{ $walk_in_customer['name']}}" >
								{!! Form::select('contact_id', 
									[], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => 'Enter Customer name / phone', 'required']); !!}
								<span class="input-group-btn">
									<button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
								</span>
							</div>
						</div>
					</div>
					@if(!empty($commission_agent))
					<div class="col-sm-3">
						<div class="form-group">
						{!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':') !!}
						{!! Form::select('commission_agent', 
									$commission_agent, null, ['class' => 'form-control select2']); !!}
						</div>
					</div>
					@endif
					<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-4 @endif">
						<div class="form-group">
							{!! Form::label('transaction_date', __('sale.sale_date') . ':*') !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
								{!! Form::text('transaction_date', @format_date('now'), ['class' => 'form-control', 'readonly', 'required']); !!}
							</div>
						</div>
					</div>
					<div class="@if(!empty($commission_agent)) col-sm-3 @else col-sm-4 @endif">
						<div class="form-group">
							{!! Form::label('status', __('sale.status') . ':*') !!}
							{!! Form::select('status', ['final' => __('sale.final'), 'draft' => __('sale.draft'), 'quotation' => __('lang_v1.quotation')], null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
						</div>
					</div>
					
				</div>
				<!-- /.box-body -->
			</div>
			<!-- /.box -->
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</span>
								{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => __('lang_v1.search_product_placeholder'),
								'disabled' => is_null($default_location)? true : false,
								'autofocus' => is_null($default_location)? false : true,
								]); !!}
							</div>
						</div>
					</div>

					<div class="row col-sm-12 pos_product_div" style="min-height: 0">

						<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

						<!-- Keeps count of product rows -->
						<input type="hidden" id="product_row_count" 
							value="0">
						@php
							$hide_tax = '';
							if( session()->get('business.enable_inline_tax') == 0){
								$hide_tax = 'hide';
							}
						@endphp
						<div class="table-responsive">
						<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
							<thead>
								<tr>
									<th class="text-center">	
										@lang('sale.product')
									</th>
									<th class="text-center">
										@lang('sale.qty')
									</th>
									<th class="text-center {{$hide_tax}}">
										@lang('sale.price_inc_tax')
									</th>
									<th class="text-center">
										@lang('sale.subtotal')
									</th>
									<th class="text-center"><i class="fa fa-trash" aria-hidden="true"></i></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
						</div>
						<div class="table-responsive">
						<table class="table table-condensed table-bordered table-striped">
							<tr>
								<td>
									<div class="pull-right"><b>@lang('sale.total'): </b>
										<span class="price_total">0</span>
									</div>
								</td>
							</tr>
						</table>
						</div>
					</div>
				</div>
			</div><!-- /.box -->

			<div class="box box-solid">
				<div class="box-body">
					<div class="col-md-4">
				        <div class="form-group">
				            {!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
				            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-info"></i>
				                </span>
				                {!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], 'percentage' , ['class' => 'form-control','placeholder' => __('messages.please_select'), 'required', 'data-default' => 'percentage']); !!}
				            </div>
				        </div>
				    </div>
				    <div class="col-md-4">
				        <div class="form-group">
				            {!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!}
				            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-info"></i>
				                </span>
				                {!! Form::text('discount_amount', @num_format($business_details->default_sales_discount), ['class' => 'form-control input_number', 'data-default' => $business_details->default_sales_discount]); !!}
				            </div>
				        </div>
				    </div>
				    <div class="col-md-4"><br>
				    	<b>@lang( 'sale.discount_amount' ):</b>(-) 
						<span class="display_currency" id="total_discount">0</span>
				    </div>
				    <div class="clearfix"></div>
				    <div class="col-md-4">
				    	<div class="form-group">
				            {!! Form::label('tax_rate_id', __('sale.order_tax') . ':*' ) !!}
				            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-info"></i>
				                </span>
				                {!! Form::select('tax_rate_id', $taxes['tax_rates'], $business_details->default_sales_tax, ['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=> $business_details->default_sales_tax], $taxes['attributes']); !!}

								<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
								value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format(optional($transaction->tax)->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
				            </div>
				        </div>
				    </div>
				    <div class="col-md-4 col-md-offset-4">
				    	<b>@lang( 'sale.order_tax' ):</b>(+) 
						<span class="display_currency" id="order_tax">0</span>
				    </div>
				    <div class="clearfix"></div>
					<div class="col-md-4">
						<div class="form-group">
				            {!! Form::label('shipping_details', __('sale.shipping_details')) !!}
				            <div class="input-group">
								<span class="input-group-addon">
				                    <i class="fa fa-info"></i>
				                </span>
				                {!! Form::textarea('shipping_details',null, ['class' => 'form-control','placeholder' => __('sale.shipping_details') ,'rows' => '1', 'cols'=>'30']); !!}
				            </div>
				        </div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							{!!Form::label('shipping_charges', __('sale.shipping_charges'))!!}
							<div class="input-group">
							<span class="input-group-addon">
							<i class="fa fa-info"></i>
							</span>
							{!!Form::text('shipping_charges',@num_format(0.00),['class'=>'form-control input_number','placeholder'=> __('sale.shipping_charges')]);!!}
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				    <div class="col-md-4 col-md-offset-8">
				    	<div><b>@lang('sale.total_payable'): </b>
							<input type="hidden" name="final_total" id="final_total_input">
							<span id="total_payable">0</span>
						</div>
				    </div>
				    <div class="col-md-12">
				    	<div class="form-group">
							{!! Form::label('sell_note',__('sale.sell_note')) !!}
							{!! Form::textarea('sale_note', null, ['class' => 'form-control', 'rows' => 3]); !!}
						</div>
				    </div>
				    <input type="hidden" name="is_direct_sale" value="1">
				</div>
			</div><!-- /.box -->

		</div>
	</div>
	<!--box end-->
	<div class="box box-solid" id="payment_rows_div"><!--box start-->
		<div class="box-header">
			<h3 class="box-title">
				@lang('purchase.add_payment')
			</h3>
		</div>
		<div class="box-body payment_row">
			@include('sale_pos.partials.payment_row_form', ['row_index' => 0])
			<hr>
			<div class="row">
				<div class="col-sm-12">
					<div class="pull-right"><strong>@lang('lang_v1.balance'):</strong> <span class="balance_due">0.00</span></div>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-12">
					<button type="button" id="submit-sell" class="btn btn-primary pull-right btn-flat">@lang('messages.submit')</button>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</section>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
	@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>

@stop

@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
@endsection
