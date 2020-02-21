@if(!session('business.enable_price_tax')) 
    @php
        $default = 0;
        $class = 'hide';
    @endphp
@else
    @php
        $default = null;
        $class = '';
    @endphp
@endif

<tr class="variation_row">
    <td>
        {!! Form::text('product_variation_edit[' . $row_index .'][name]', $product_variation->name, ['class' => 'form-control input-sm variation_name', 'required']); !!}
        <input type="hidden" class="row_index" value="{{$row_index}}">
        <input type="hidden" class="row_edit" value="edit">
    </td>

    <td>
        -
    </td>

    <td>
        <table class="table table-condensed table-bordered blue-header variation_value_table">
            <thead>
            <tr>
                <th>@lang('product.sku') @show_tooltip(__('tooltip.sub_sku'))</th>
                <th>@lang('product.value')</th>
                <th class="{{$class}}">@lang('product.default_purchase_price') 
                    <br/>
                    <span class="pull-left"><small><i>@lang('product.exc_of_tax')</i></small></span>

                    <span class="pull-right"><small><i>@lang('product.inc_of_tax')</i></small></span>
                </th>
                <th class="{{$class}}">@lang('product.profit_percent')</th>
                <th class="{{$class}}">@lang('product.default_selling_price') 
                <br/>
                <small><i><span class="dsp_label"></span></i></small>
                </th>
                <th><button type="button" class="btn btn-success btn-xs add_variation_value_row">+</button></th>
            </tr>
            </thead>

            <tbody>

            @forelse ($product_variation->variations as $variation)
                <tr>
                    <td>
                        {!! Form::text('product_variation_edit[' . $row_index .'][variations_edit][' . $variation->id . '][sub_sku]', $variation->sub_sku, ['class' => 'form-control input-sm variation_value_name', 'required']); !!}
                    </td>
                    <td>
                        {!! Form::text('product_variation_edit[' . $row_index .'][variations_edit][' . $variation->id . '][value]', $variation->name, ['class' => 'form-control input-sm variation_value_name', 'required']); !!}
                    </td>
                    <td class="{{$class}}">
                        <div class="col-sm-6">
                            {!! Form::text('product_variation_edit[' . $row_index .'][variations_edit][' . $variation->id . '][default_purchase_price]', @num_format($variation->default_purchase_price), ['class' => 'form-control input-sm variable_dpp input_number', 'placeholder' => 'Excluding Tax', 'required']); !!}
                        </div>

                        <div class="col-sm-6">
                            {!! Form::text('product_variation_edit[' . $row_index .'][variations_edit][' . $variation->id . '][dpp_inc_tax]', @num_format($variation->dpp_inc_tax), ['class' => 'form-control input-sm variable_dpp_inc_tax input_number', 'placeholder' => 'Including Tax', 'required']); !!}
                        </div>
                    </td>
                    <td class="{{$class}}">
                        {!! Form::text('product_variation_edit[' . $row_index .'][variations_edit][' . $variation->id . '][profit_percent]', @num_format($variation->profit_percent), ['class' => 'form-control input-sm variable_profit_percent input_number', 'required']); !!}
                    </td>
                    <td class="{{$class}}">
                        {!! Form::text('product_variation_edit[' . $row_index .'][variations_edit][' . $variation->id . '][default_sell_price]', @num_format($variation->default_sell_price), ['class' => 'form-control input-sm variable_dsp input_number', 'placeholder' => 'Excluding tax', 'required']); !!}

                        {!! Form::text('product_variation_edit[' . $row_index .'][variations_edit][' . $variation->id . '][sell_price_inc_tax]', @num_format($variation->sell_price_inc_tax), ['class' => 'form-control input-sm variable_dsp_inc_tax input_number', 'placeholder' => 'Including tax', 'required']); !!}
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-xs remove_variation_value_row">-</button>
                        <input type="hidden" class="variation_row_index" value="0">
                    </td>
                </tr>
            @empty
                &nbsp;
            @endforelse
            </tbody>
        </table>
    </td>
</tr>