@extends('layouts.app')
@section('title', __('restaurant.service_staff_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('restaurant.service_staff_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary" id="accordion">
              <div class="box-header with-border">
                <h3 class="box-title">
                  <a data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                    <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters')
                  </a>
                </h3>
              </div>
              <div id="collapseFilter" class="panel-collapse active collapse in" aria-expanded="true">
                <div class="box-body">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('ssr_location_id',  __('purchase.business_location') . ':') !!}
                            {!! Form::select('ssr_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('ssr_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'), ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'ssr_date_range', 'readonly']); !!}
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="service_staff_report">
                        <thead>
                            <tr>
                                <th>@lang('restaurant.service_staff')</th>
                                <th>@lang('report.total_sell')</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
    
    <script type="text/javascript">
        $(document).ready(function(){
            if($('#ssr_date_range').length == 1){
                $('#ssr_date_range').daterangepicker({
                    ranges: ranges,
                    autoUpdateInput: false,
                    startDate: moment().startOf('month'),
                    endDate: moment().endOf('month'),
                    locale: {
                        format: moment_date_format
                    }
                });
                $('#ssr_date_range').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format(moment_date_format) + ' ~ ' + picker.endDate.format(moment_date_format));
                    service_staff_report.ajax.reload();
                });

                $('#ssr_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $(this).val('');
                    service_staff_report.ajax.reload();
                });
            }

            service_staff_report = $('#service_staff_report').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/service-staff-report",
                                "data": function ( d ) {
                                    d.location_id = $('#ssr_location_id').val();
                                    d.start_date = $('#ssr_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#ssr_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [
                                {data: 'service_staff_name', name: 'service_staff_name'},
                                {data: 'total_sell', name: 'total_sell', searchable: false}
                            ],
                            "fnDrawCallback": function (oSettings) {
                                __currency_convert_recursively($('#service_staff_report'));
                            }
                        });
            //Customer Group report filter
            $('select#ssr_location_id, #ssr_date_range').change( function(){
                service_staff_report.ajax.reload();
            });
        })
    </script>
@endsection