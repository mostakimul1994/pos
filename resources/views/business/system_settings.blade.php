@extends('layouts.app')
@section('title', __('business.system_settings'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('business.system_settings')</h1>
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action('BusinessController@postSystemSettings'), 'method' => 'post', 'id' => 'bussiness_edit_form',
           'files' => true ]) !!}
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-solid"> <!--business info box start-->
                <div class="box-header">
                    <h3 class="box-title">POS</h3>
                </div>
                <div class="box-body">
                    <h4>@lang('business.add_keyboard_shortcuts'):</h4>
                    <p class="help-block">@lang('lang_v1.shortcut_help'); @lang('lang_v1.example'): <b>ctrl+shift+b</b>, <b>ctrl+h</b></p>
                    <p class="help-block">
                        <b>@lang('lang_v1.available_key_names_are'):</b>
                        <br> shift, ctrl, alt, backspace, tab, enter, return, capslock, esc, escape, space, pageup, pagedown, end, home, <br>left, up, right, down, ins, del, and plus
                    </p>
                    <div class="row">
                        <div class="col-sm-6">
                            <table class="table table-striped">
                                <tr>
                                    <th>@lang('business.operations')</th>
                                    <th>@lang('business.keyboard_shortcut')</th>
                                </tr>
                                <tr>
                                    <td>@lang('sale.express_finalize'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][express_checkout]', 
                                        !empty($shortcuts["pos"]["express_checkout"]) ? $shortcuts["pos"]["express_checkout"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('sale.finalize'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][pay_n_ckeckout]', !empty($shortcuts["pos"]["pay_n_ckeckout"]) ? $shortcuts["pos"]["pay_n_ckeckout"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('sale.draft'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][draft]', !empty($shortcuts["pos"]["draft"]) ? $shortcuts["pos"]["draft"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('messages.cancel'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][cancel]', !empty($shortcuts["pos"]["cancel"]) ? $shortcuts["pos"]["cancel"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('lang_v1.recent_product_quantity'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][recent_product_quantity]', !empty($shortcuts["pos"]["recent_product_quantity"]) ? $shortcuts["pos"]["recent_product_quantity"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-striped">
                                <tr>
                                    <th>@lang('business.operations')</th>
                                    <th>@lang('business.keyboard_shortcut')</th>
                                </tr>
                                <tr>
                                    <td>@lang('sale.edit_discount'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][edit_discount]', !empty($shortcuts["pos"]["edit_discount"]) ? $shortcuts["pos"]["edit_discount"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('sale.edit_order_tax'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][edit_order_tax]', !empty($shortcuts["pos"]["edit_order_tax"]) ? $shortcuts["pos"]["edit_order_tax"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('sale.add_payment_row'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][add_payment_row]', !empty($shortcuts["pos"]["add_payment_row"]) ? $shortcuts["pos"]["add_payment_row"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('sale.finalize_payment'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][finalize_payment]', !empty($shortcuts["pos"]["finalize_payment"]) ? $shortcuts["pos"]["finalize_payment"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('lang_v1.add_new_product'):</td>
                                    <td>
                                        {!! Form::text('shortcuts[pos][add_new_product]', !empty($shortcuts["pos"]["add_new_product"]) ? $shortcuts["pos"]["add_new_product"] : null, ['class' => 'form-control']); !!}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <h4>@lang('lang_v1.pos_settings'):</h4>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="checkbox">
                                <br>
                                  <label>
                                    {!! Form::checkbox('pos_settings[disable_pay_checkout]', 1,  
                                        $pos_settings['disable_pay_checkout'] , 
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_pay_checkout' ) }}
                                  </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="checkbox">
                                <br>
                                  <label>
                                    {!! Form::checkbox('pos_settings[disable_draft]', 1,  
                                        $pos_settings['disable_draft'] , 
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_draft' ) }}
                                  </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="checkbox">
                                <br>
                                  <label>
                                    {!! Form::checkbox('pos_settings[disable_express_checkout]', 1,  
                                        $pos_settings['disable_express_checkout'] , 
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_express_checkout' ) }}
                                  </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="checkbox">
                                <br>
                                  <label>
                                    {!! Form::checkbox('pos_settings[hide_product_suggestion]', 1,  $pos_settings['hide_product_suggestion'] , 
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.hide_product_suggestion' ) }}
                                  </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="checkbox">
                                <br>
                                  <label>
                                    {!! Form::checkbox('pos_settings[hide_recent_trans]', 1,  $pos_settings['hide_recent_trans'] , 
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.hide_recent_trans' ) }}
                                  </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="checkbox">
                                <br>
                                  <label>
                                    {!! Form::checkbox('pos_settings[disable_discount]', 1,  $pos_settings['disable_discount'] , 
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_discount' ) }}
                                  </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <div class="checkbox">
                                <br>
                                  <label>
                                    {!! Form::checkbox('pos_settings[disable_order_tax]', 1,  $pos_settings['disable_order_tax'] , 
                                    [ 'class' => 'input-icheck']); !!} {{ __( 'lang_v1.disable_order_tax' ) }}
                                  </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div><!--business info box end-->
        </div>
    </div>

    @if(!empty($modules))
        <div class="row">
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">@lang('lang_v1.enable_disable_modules')</h3>
                    </div>
                    <div class="box-body">
                        @foreach($modules as $k => $v)
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div class="checkbox">
                                    <br>
                                      <label>
                                        {!! Form::checkbox('enabled_modules[]', $k,  in_array($k, $enabled_modules) , 
                                        ['class' => 'input-icheck']); !!} {{$v['name']}}
                                      </label>
                                      @if(!empty($v['tooltip'])) @show_tooltip($v['tooltip']) @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
            <div class="col-sm-12">
                <div class="box box-solid">
                    <div class="box-body">
                            <div class="col-sm-4">
                                <div class="form-group">
                                {!! Form::label('theme_color', __('lang_v1.theme_color')); !!}
                                {!! Form::select('theme_color', $theme_colors,   $business->theme_color, 
                                        ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-primary pull-right" type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
{!! Form::close() !!}
</section>
<!-- /.content -->
@stop
@section('javascript')
    <!-- <script src="{{ asset('plugins/mousetrap/mousetrap.min.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('plugins/mousetrap/mousetrap-record.min.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        function recordSequence(el) {
            var shortcut = '';
            Mousetrap.record(function(sequence) {
                shortcut = sequence.join('+');
                el.val(shortcut);
            });
        }
    </script> -->
@endsection