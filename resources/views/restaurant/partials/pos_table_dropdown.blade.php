@if($tables_enabled)
<div class="col-sm-6">
	<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon">
				<i class="fa fa-table"></i>
			</span>
			{!! Form::select('res_table_id', $tables, $view_data['res_table_id'], ['class' => 'form-control select2', 'placeholder' => __('restaurant.select_table')]); !!}
		</div>
	</div>
</div>
@endif
@if($waiters_enabled)
<div class="col-sm-6">
	<div class="form-group">
		<div class="input-group">
			<span class="input-group-addon">
				<i class="fa fa-user-secret"></i>
			</span>
			{!! Form::select('res_waiter_id', $waiters, $view_data['res_waiter_id'], ['class' => 'form-control select2', 'placeholder' => __('restaurant.select_service_staff')]); !!}
		</div>
	</div>
</div>
@endif