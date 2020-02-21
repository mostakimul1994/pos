@extends('layouts.app')
@section('title', __('lang_v1.import_opening_stock'))

@section('content')
<br/>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('lang_v1.import_opening_stock')</h1>
</section>

<!-- Main content -->
<section class="content">
    
@if (session('notification') || !empty($notification))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                @if(!empty($notification['msg']))
                    {{$notification['msg']}}
                @elseif(session('notification.msg'))
                    {{ session('notification.msg') }}
                @endif
              </div>
          </div>  
      </div>     
@endif
    <div class="row">
        <div class="col-sm-12">
        	<div class="box">
                <div class="box-body">
                    {!! Form::open(['url' => action('ImportOpeningStockController@store'), 'method' => 'post', 'enctype' => 'multipart/form-data' ]) !!}
                        <div class="row">
                            <div class="col-sm-6">
                            <div class="col-sm-8">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'product.file_to_import' ) . ':') !!}
                                    @show_tooltip(__('lang_v1.tooltip_import_opening_stock'))
                                    {!! Form::file('products_csv', ['accept'=> '.csv']); !!}
                                  </div>
                            </div>
                            <div class="col-sm-4">
                            <br>
                                <button type="submit" class="btn btn-primary">@lang('messages.submit')</button>
                            </div>
                            </div>
                        </div>

                    {!! Form::close() !!}
                    <br><br>
                    <div class="row">
                        <div class="col-sm-4">
                            <a href="{{ asset('uploads/files/import_opening_stock_csv_template.csv') }}" class="btn btn-success" download><i class="fa fa-download"></i> @lang('product.download_csv_file_template')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid">
                <div class="box-header">
                  <h3 class="box-title">@lang('lang_v1.instructions')</h3>
                </div>
                <div class="box-body">
                    <strong>@lang('lang_v1.instruction_line1')</strong><br>
                    @lang('lang_v1.instruction_line2')
                    <br><br>
                    <table class="table table-striped">
                        <tr>
                            <th>@lang('lang_v1.col_no')</th>
                            <th>@lang('lang_v1.col_name')</th>
                            <th>@lang('lang_v1.instruction')</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>@lang('product.sku')<small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>@lang('business.location') <small class="text-muted">(@lang('lang_v1.optional')) <br>@lang('lang_v1.location_ins')</small></td>
                            <td>@lang('lang_v1.location_ins1')<br>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>@lang('lang_v1.quantity') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>@lang('purchase.unit_cost_before_tax') <small class="text-muted">(@lang('lang_v1.required'))</small></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>@lang('lang_v1.lot_number') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>@lang('lang_v1.expiry_date') <small class="text-muted">(@lang('lang_v1.optional'))</small></td>
                            <td>@lang('lang_v1.expiry_date_ins')</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection