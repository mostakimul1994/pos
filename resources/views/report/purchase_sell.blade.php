@extends('layouts.app')
@section('title', __( 'report.purchase_sell' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'report.purchase_sell' )
        <small>@lang( 'report.purchase_sell_msg' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-3 col-md-offset-7 col-xs-6">
            <div class="input-group">
                <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
                 <select class="form-control select2" id="purchase_sell_location_filter">
                    @foreach($business_locations as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 col-xs-6">
            <div class="form-group pull-right">
                <div class="input-group">
                  <button type="button" class="btn btn-primary" id="purchase_sell_date_filter">
                    <span>
                      <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-6">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('purchase.purchases') }}</h3>
                </div>

                <div class="box-body">
                    <table class="table table-striped">
                        <tr>
                            <th>{{ __('report.total_purchase') }}:</th>
                            <td>
                                <span class="total_purchase">
                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('report.purchase_inc_tax') }}:</th>
                            <td>
                                 <span class="purchase_inc_tax">
                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('report.purchase_due') }}: @show_tooltip(__('tooltip.purchase_due'))</th>
                            <td>
                                 <span class="purchase_due">
                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('sale.sells') }}</h3>
                </div>

                <div class="box-body">
                    <table class="table table-striped">
                        <tr>
                            <th>{{ __('report.total_sell') }}:</th>
                            <td>
                                <span class="total_sell">
                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('report.sell_inc_tax') }}:</th>
                            <td>
                                 <span class="sell_inc_tax">
                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('report.sell_due') }}: @show_tooltip(__('tooltip.sell_due'))</th>
                            <td>
                                <span class="sell_due">
                                    <i class="fa fa-refresh fa-spin fa-fw"></i>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('report.overall') }}  @show_tooltip(__('tooltip.over_all_sell_purchase'))</h3>
                </div>

                <div class="box-body">

                    <h3 class="text-muted">
                        {{ __('report.sell_minus_purchase') }}: 
                        <span class="sell_minus_purchase">
                            <i class="fa fa-refresh fa-spin fa-fw"></i>
                        </span>
                    </h3>

                    <h3 class="text-muted">
                        {{ __('report.difference_due') }}: 
                        <span class="difference_due">
                            <i class="fa fa-refresh fa-spin fa-fw"></i>
                        </span>
                    </h3>

                </div>
            </div>
        </div>
    </div>
	

</section>
<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

@endsection
