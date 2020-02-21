@extends('layouts.app')
@section('title', __('expense.expenses'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('expense.expenses')
        <small></small>
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
        	<h3 class="box-title">@lang('expense.all_expenses')</h3>
            <div class="box-tools">
                <a class="btn btn-block btn-primary" href="{{action('ExpenseController@create')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
        	<table class="table table-bordered table-striped" id="expense_table">
        		<thead>
        			<tr>
        				<th>@lang('messages.date')</th>
						<th>@lang('purchase.ref_no')</th>
                        <th>@lang('expense.expense_category')</th>
                        <th>@lang('business.location')</th>
                        <th>@lang('sale.payment_status')</th>
                        <th>@lang('sale.total_amount')</th>
                        <th>@lang('expense.expense_for')</th>
                        <th>@lang('expense.expense_note')</th>
						<th>@lang('messages.action')</th>
        			</tr>
        		</thead>
                <tfoot>
                    <tr class="bg-gray font-17 text-center footer-total">
                        <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                        <td id="footer_payment_status_count"></td>
                        <td><span class="display_currency" id="footer_expense_total" data-currency_symbol ="true"></span></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
        	</table>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->
@endsection