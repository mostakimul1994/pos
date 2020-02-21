<div class="row">
	<div class="col-md-12">
		<h4>@lang('product.variations'):</h4>
	</div>
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table bg-gray">
				<tr class="bg-green">
					<th>@lang('product.variations')</th>
					<th>@lang('product.sku')</th>
					<th>@lang('product.default_purchase_price') (@lang('product.exc_of_tax'))</th>
					<th>@lang('product.default_purchase_price') (@lang('product.inc_of_tax'))</th>
			        <th>@lang('product.profit_percent')</th>
			        <th>@lang('product.default_selling_price') (@lang('product.exc_of_tax'))</th>
			        <th>@lang('product.default_selling_price') (@lang('product.inc_of_tax'))</th>
				</tr>
				@foreach($product->variations as $variation)
				<tr>
					<td>
						{{$variation->product_variation->name}} - {{ $variation->name }}
					</td>
					<td>
						{{ $variation->sub_sku }}
					</td>
					<td>
						<span class="display_currency" data-currency_symbol="true">{{ $variation->default_purchase_price }}</span>
					</td>
					<td>
						<span class="display_currency" data-currency_symbol="true">{{ $variation->dpp_inc_tax }}</span>
					</td>
					<td>
						{{ $variation->profit_percent }}
					</td>
					<td>
						<span class="display_currency" data-currency_symbol="true">{{ $variation->default_sell_price }}</span>
					</td>
					<td>
						<span class="display_currency" data-currency_symbol="true">{{ $variation->sell_price_inc_tax }}</span>
					</td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>