@extends('layouts.app')
@section('title', __( 'tax_rate.tax_rates' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'tax_rate.tax_rates' )
        <small>@lang( 'tax_rate.manage_your_tax_rates' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang( 'tax_rate.all_your_tax_rates' )</h3>
            @can('tax_rate.create')
            	<div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                    	data-href="{{action('TaxRateController@create')}}" 
                    	data-container=".tax_rate_modal">
                    	<i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endcan
        </div>
        <div class="box-body">
            @can('tax_rate.view')
                <div class="table-responsive">
            	<table class="table table-bordered table-striped" id="tax_rates_table">
            		<thead>
            			<tr>
            				<th>@lang( 'tax_rate.name' )</th>
            				<th>@lang( 'tax_rate.rate' )</th>
                            <th>@lang( 'messages.action' )</th>
            			</tr>
            		</thead>
            	</table>
                </div>
            @endcan
        </div>
    </div>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">@lang( 'tax_rate.tax_groups' ) ( @lang('lang_v1.combination_of_taxes') ) @show_tooltip(__('tooltip.tax_groups'))</h3>
            @can('tax_rate.create')
            <div class="box-tools">
                <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action('GroupTaxController@create')}}" 
                    data-container=".tax_group_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
            @endcan
        </div>
        <div class="box-body">
        @can('tax_rate.view')
            <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tax_groups_table">
                <thead>
                    <tr>
                        <th>@lang( 'tax_rate.name' )</th>
                        <th>@lang( 'tax_rate.rate' )</th>
                        <th>@lang( 'tax_rate.sub_taxes' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
            </div>
            @endcan
        </div>
    </div>
    

    <div class="modal fade tax_rate_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade tax_group_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@endsection
