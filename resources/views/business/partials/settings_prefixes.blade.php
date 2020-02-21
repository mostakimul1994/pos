<div class="col-sm-12">
    <div class="box box-solid"> <!--Sale info box start-->
        <div class="box-header">
            <h3 class="box-title">@lang('lang_v1.prefixes')</h3>
        </div>
        <div class="box-body">
             <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $purchase_prefix = '';
                            if(!empty($business->ref_no_prefixes['purchase'])){
                                $purchase_prefix = $business->ref_no_prefixes['purchase'];
                            }
                        @endphp
                        {!! Form::label('ref_no_prefixes[purchase]', __('lang_v1.purchase_order') . ':') !!}
                        {!! Form::text('ref_no_prefixes[purchase]', $purchase_prefix, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $stock_transfer_prefix = '';
                            if(!empty($business->ref_no_prefixes['stock_transfer'])){
                                $stock_transfer_prefix = $business->ref_no_prefixes['stock_transfer'];
                            }
                        @endphp
                        {!! Form::label('ref_no_prefixes[stock_transfer]', __('lang_v1.stock_transfer') . ':') !!}
                        {!! Form::text('ref_no_prefixes[stock_transfer]', $stock_transfer_prefix, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $stock_adjustment_prefix = '';
                            if(!empty($business->ref_no_prefixes['stock_adjustment'])){
                                $stock_adjustment_prefix = $business->ref_no_prefixes['stock_adjustment'];
                            }
                        @endphp
                        {!! Form::label('ref_no_prefixes[stock_adjustment]', __('stock_adjustment.stock_adjustment') . ':') !!}
                        {!! Form::text('ref_no_prefixes[stock_adjustment]', $stock_adjustment_prefix, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $sell_return_prefix = '';
                            if(!empty($business->ref_no_prefixes['sell_return'])){
                                $sell_return_prefix = $business->ref_no_prefixes['sell_return'];
                            }
                        @endphp
                        {!! Form::label('ref_no_prefixes[sell_return]', __('lang_v1.sell_return') . ':') !!}
                        {!! Form::text('ref_no_prefixes[sell_return]', $sell_return_prefix, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $expenses_prefix = '';
                            if(!empty($business->ref_no_prefixes['expense'])){
                                $expenses_prefix = $business->ref_no_prefixes['expense'];
                            }
                        @endphp
                        {!! Form::label('ref_no_prefixes[expense]', __('expense.expenses') . ':') !!}
                        {!! Form::text('ref_no_prefixes[expense]', $expenses_prefix, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $contacts_prefix = '';
                            if(!empty($business->ref_no_prefixes['contacts'])){
                                $contacts_prefix = $business->ref_no_prefixes['contacts'];
                            }
                        @endphp
                        {!! Form::label('ref_no_prefixes[contacts]', __('contact.contacts') . ':') !!}
                        {!! Form::text('ref_no_prefixes[contacts]', $contacts_prefix, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $purchase_payment = '';
                            if(!empty($business->ref_no_prefixes['purchase_payment'])){
                                $purchase_payment = $business->ref_no_prefixes['purchase_payment'];
                            }
                        @endphp
                        {!! Form::label('ref_no_prefixes[purchase_payment]', __('lang_v1.purchase_payment') . ':') !!}
                        {!! Form::text('ref_no_prefixes[purchase_payment]', $purchase_payment, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $sell_payment = '';
                            if(!empty($business->ref_no_prefixes['sell_payment'])){
                                $sell_payment = $business->ref_no_prefixes['sell_payment'];
                            }
                        @endphp
                        {!! Form::label('ref_no_prefixes[sell_payment]', __('lang_v1.sell_payment') . ':') !!}
                        {!! Form::text('ref_no_prefixes[sell_payment]', $sell_payment, ['class' => 'form-control']); !!}
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        @php
                            $business_location_prefix = '';
                            if(!empty($business->ref_no_prefixes['business_location'])){
                                $business_location_prefix = $business->ref_no_prefixes['business_location'];
                            }
                        @endphp
                        {!! Form::label('ref_no_prefixes[business_location]', __('business.business_location') . ':') !!}
                        {!! Form::text('ref_no_prefixes[business_location]', $business_location_prefix, ['class' => 'form-control']); !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>