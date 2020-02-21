<div class="row">
	<div class="col-md-10 col-md-offset-1 col-xs-12">
		<div class="table-responsive">
			<table class="table table-condensed bg-gray">
				<tr>
					<th>SKU</th>
					<th>Variation</th>
					<th>@lang('sale.unit_price')</th>
					<th>@lang('report.current_stock')</th>
					<th>@lang('report.total_unit_sold')</th>
				</tr>
				@foreach( $product_details as $details )
					<tr>
						<td>{{ $details->sub_sku}}</td>
						<td>
							{{ $details->product . '-' . $details->product_variation . 
							'-' .  $details->variation }}
						</td>
						<td><span class="display_currency" data-currency_symbol=true>{{$details->sell_price_inc_tax}}</span></td>
						<td>
							@if($details->stock)
								{{ (float)$details->stock }} {{$details->unit}}
							@else
							 0
							@endif
						</td>
						<td>
							@if($details->total_sold)
								{{ (float)$details->total_sold }} {{$details->unit}}
							@else
							 0
							@endif
						</td>
					</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>