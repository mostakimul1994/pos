@extends('layouts.app')
@section('title', __('business.business_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business.business_settings')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('BusinessController@postBusinessSettings'), 'method' => 'post', 'id' => 'bussiness_edit_form',
           'files' => true ]) !!}
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid"> <!--business info box start-->
                <div class="box-header">
                    <h3 class="box-title">@lang('business.business')</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('name',__('business.business_name') . ':*') !!}
                                {!! Form::text('name', $business->name, ['class' => 'form-control', 'required',
                                'placeholder' => __('business.business_name')]); !!}
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('start_date', __('business.start_date') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    @php
                                        $start_date = null;
                                        if(!empty($business->start_date)){
                                            $start_date = date('m/d/Y', strtotime($business->start_date));
                                        }
                                    @endphp
                                    {!! Form::text('start_date', $start_date, ['class' => 'form-control start-date-picker','placeholder' => __('business.start_date'), 'readonly']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('default_profit_percent', __('business.default_profit_percent') . ':*') !!} @show_tooltip(__('tooltip.default_profit_percent'))
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-plus-circle"></i>
                                    </span>
                                    {!! Form::number('default_profit_percent', $business->default_profit_percent, ['class' => 'form-control', 'min' => 0, 
                                    'step' => 0.01, 'max' => 100]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('currency_id', __('business.currency') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                    {!! Form::select('currency_id', $currencies, $business->currency_id, ['class' => 'form-control select2','placeholder' => __('business.currency'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('currency_symbol_placement', __('lang_v1.currency_symbol_placement') . ':') !!}
                                {!! Form::select('currency_symbol_placement', ['before' => __('lang_v1.before_amount'), 'after' => __('lang_v1.after_amount')], $business->currency_symbol_placement, ['class' => 'form-control select2', 'required']); !!}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('time_zone', __('business.time_zone') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </span>
                                    {!! Form::select('time_zone', $timezone_list, $business->time_zone, ['class' => 'form-control select2', 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('business_logo', __('business.upload_logo') . ':') !!}
                                    {!! Form::file('business_logo', ['accept' => 'image/*']); !!}
                                    <p class="help-block"><i> @lang('business.logo_help')</i></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('fy_start_month', __('business.fy_start_month') . ':') !!} @show_tooltip(__('tooltip.fy_start_month'))
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::select('fy_start_month', $months, $business->fy_start_month, ['class' => 'form-control select2', 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('accounting_method', __('business.accounting_method') . ':*') !!}
                                @show_tooltip(__('tooltip.accounting_method'))
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calculator"></i>
                                    </span>
                                    {!! Form::select('accounting_method', $accounting_methods, $business->accounting_method, ['class' => 'form-control select2', 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('transaction_edit_days', __('business.transaction_edit_days') . ':*') !!}
                                @show_tooltip(__('tooltip.transaction_edit_days'))
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-edit"></i>
                                    </span>
                                    {!! Form::number('transaction_edit_days', $business->transaction_edit_days, ['class' => 'form-control','placeholder' => __('business.transaction_edit_days'), 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('date_format', __('lang_v1.date_format') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::select('date_format', $date_formats, $business->date_format, ['class' => 'form-control select2', 'required']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('time_format', __('lang_v1.time_format') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </span>
                                    {!! Form::select('time_format', [12 => __('lang_v1.12_hour'), 24 => __('lang_v1.24_hour')], $business->time_format, ['class' => 'form-control select2', 'required']); !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div><!--business info box end-->
        </div>
        <div class="col-sm-12">
            <div class="box box-solid"> <!--Address info box start-->
                <div class="box-header">
                    <h3 class="box-title">@lang('business.tax') @show_tooltip(__('tooltip.business_tax'))</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('tax_label_1', __('business.tax_1_name') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::text('tax_label_1', $business->tax_label_1, ['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('tax_number_1', __('business.tax_1_no') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::text('tax_number_1', $business->tax_number_1, ['class' => 'form-control']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('tax_label_2', __('business.tax_2_name') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::text('tax_label_2', $business->tax_label_2, ['class' => 'form-control','placeholder' => __('business.tax_1_placeholder')]); !!}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('tax_number_2', __('business.tax_2_no') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::text('tax_number_2', $business->tax_number_2, ['class' => 'form-control']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <div class="checkbox">
                                <br>
                                  <label>
                                    {!! Form::checkbox('enable_inline_tax', 1, $business->enable_inline_tax , 
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.enable_inline_tax' ) }}
                                  </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('business.partials.settings_product')

        <div class="col-sm-12">
            <div class="box box-solid"> 
                <div class="box-header">
                    <h3 class="box-title">@lang('business.sale')</h3>
                </div>
                <div class="box-body">
                    <div class="row">

                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('default_sales_discount', __('business.default_sales_discount') . ':*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-percent"></i>
                                    </span>
                                    {!! Form::number('default_sales_discount', $business->default_sales_discount, ['class' => 'form-control', 'min' => 0, 'step' => 0.01, 'max' => 100]); !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('default_sales_tax', __('business.default_sales_tax') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::select('default_sales_tax', $tax_rates, $business->default_sales_tax, ['class' => 'form-control select2','placeholder' => __('business.default_sales_tax')]); !!}
                                </div>
                            </div>
                        </div>
                        <!-- <div class="clearfix"></div> -->

                        <div class="col-sm-12 hide">
                            <div class="form-group">
                                {!! Form::label('sell_price_tax', __('business.sell_price_tax') . ':') !!}
                                <div class="input-group">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="sell_price_tax" value="includes" 
                                            class="input-icheck" @if($business->sell_price_tax == 'includes') {{'checked'}} @endif> Includes the Sale Tax
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="sell_price_tax" value="excludes" 
                                            class="input-icheck" @if($business->sell_price_tax == 'excludes') {{'checked'}} @endif>Excludes the Sale Tax (Calculate sale tax on Selling Price provided in Add Purchase)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('sales_cmsn_agnt', __('lang_v1.sales_commission_agent') . ':') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-info"></i>
                                    </span>
                                    {!! Form::select('sales_cmsn_agnt', $commission_agent_dropdown, $business->sales_cmsn_agnt, ['class' => 'form-control select2']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('item_addition_method', __('lang_v1.sales_item_addition_method') . ':') !!}
                                {!! Form::select('item_addition_method', [ 0 => __('lang_v1.add_item_in_new_row'), 1 =>  __('lang_v1.increase_item_qty')], $business->item_addition_method, ['class' => 'form-control select2']); !!}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        @include('business.partials.settings_purchase')
        
        @if(!config('constants.disable_expiry', true))
            @include('business.partials.settings_dashboard')
        @endif

        @include('business.partials.settings_system')

        @include('business.partials.settings_prefixes')

    </div>

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-primary pull-right" type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
{!! Form::close() !!}
</section>
<!-- /.content -->

@endsection