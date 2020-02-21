@extends('layouts.app')
@section('title', __('lang_v1.sales_commission_agents'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.sales_commission_agents' )
    </h1>
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-body">
            @can('user.create')
                <div class="row">
                    <div class="col-sm-12">
                        <button type="button" class="btn btn-primary btn-modal pull-right" 
                            data-href="{{action('SalesCommissionAgentController@create')}}" 
                            data-container=".commission_agent_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                </div>
                <br>
            @endcan
            @can('user.view')
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                    	<table class="table table-bordered table-striped" id="sales_commission_agent_table">
                    		<thead>
                    			<tr>
                    				<th>@lang( 'user.name' )</th>
                    				<th>@lang( 'business.email' )</th>
                                    <th>@lang( 'lang_v1.contact_no' )</th>
                                    <th>@lang( 'business.address' )</th>
                                    <th>@lang( 'lang_v1.cmmsn_percent' )</th>
                    				<th>@lang( 'messages.action' )</th>
                    			</tr>
                    		</thead>
                    	</table>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <div class="modal fade commission_agent_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
