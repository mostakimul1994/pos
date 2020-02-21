@extends('layouts.app')
@section('title', __('lang_v1.'.$type.'s'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1> @lang('lang_v1.'.$type.'s')
        <small>@lang( 'contact.manage_your_contact', ['contacts' =>  __('lang_v1.'.$type.'s') ])</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    <input type="hidden" value="{{$type}}" id="contact_type">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang( 'contact.all_your_contact', ['contacts' => __('lang_v1.'.$type.'s') ])</h3>
            @if(auth()->user()->can('supplier.create') || auth()->user()->can('customer.create'))
            	<div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" 
                    data-href="{{action('ContactController@create', ['type' => $type])}}" 
                    data-container=".contact_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')</button>
                </div>
            @endif
        </div>
        <div class="box-body">
            @if(auth()->user()->can('supplier.view') || auth()->user()->can('customer.view'))
                <div class="table-responsive">
            	<table class="table table-bordered table-striped" id="contact_table">
            		<thead>
            			<tr>
                            <th>@lang('lang_v1.contact_id')</th>
                            @if($type == 'supplier') 
                				<th>@lang('business.business_name')</th>
                				<th>@lang('contact.name')</th>
                				<th>@lang('contact.contact')</th>
                                <th>@lang('contact.total_purchase_due')</th>
                                <th>@lang('messages.action')</th>
                            @elseif( $type == 'customer')
                                <th>@lang('user.name')</th>
                                <th>@lang('lang_v1.customer_group')</th>
                                <th>@lang('business.address')</th>
                                <th>@lang('contact.contact')</th>
                                <th>@lang('contact.total_sale_due')</th>
                                <th>@lang('messages.action')</th>
                            @endif
            			</tr>
            		</thead>
            	</table>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade contact_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection
