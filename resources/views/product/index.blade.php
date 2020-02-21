@extends('layouts.app')
@section('title', __('sale.products'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('sale.products')
        <small>@lang('lang_v1.manage_products')</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang('lang_v1.all_products')</h3>
            @can('product.create')
            	<div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{action('ProductController@create')}}">
    				<i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endcan
        </div>
        <div class="box-body">
            @can('product.view')
                <div class="table-responsive">
            	<table class="table table-bordered table-striped ajax_view table-text-center" id="product_table">
            		<thead>
            			<tr>
                            <th>&nbsp;</th>
            				<th>@lang('sale.product')</th>
    						<th>@lang('product.product_type')</th>
            				<th>@lang('product.category')</th>
    						<th>@lang('product.sub_category')</th>
                            <th>@lang('product.unit')</th>
    						<th>@lang('product.brand')</th>
    						<th>@lang('product.tax')</th>
    						<th>@lang('product.sku')</th>
    						<th>@lang('product.alert_quantity')</th>
    						<th>@lang('messages.action')</th>
            			</tr>
            		</thead>
            	</table>
                </div>
            @endcan
        </div>
    </div>

    <input type="hidden" id="is_rack_enabled" value="{{$rack_enabled}}">

    <div class="modal fade product_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" id="view_product_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade" id="opening_stock_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
@endsection