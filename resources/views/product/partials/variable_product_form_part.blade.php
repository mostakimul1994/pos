
<div class="col-sm-12">
<h4>@lang('product.add_variation'):* <button type="button" class="btn btn-primary" id="add_variation" data-action="add">+</button></h4>
</div>
<div class="col-sm-12">
    <div class="table-responsive">
    <table class="table table-bordered add-product-price-table table-condensed" id="product_variation_form_part">
        <thead>
          <tr>
            <th class="col-sm-2">@lang('product.variation_name')</th>
            <th class="col-sm-2">@lang('product.use_template')</th>
            <th class="col-sm-8">@lang('product.variation_values')</th>
          </tr>
        </thead>
        <tbody>
            @if($action == 'add')
                @include('product.partials.product_variation_row', ['row_index' => 0])
            @else

                @forelse ($product_variations as $product_variation)
                    @include('product.partials.edit_product_variation_row', ['row_index' => $product_variation->id])
                @empty
                    @include('product.partials.product_variation_row', ['row_index' => 0])
                @endforelse

            @endif
            
        </tbody>
    </table>
    </div>
</div>