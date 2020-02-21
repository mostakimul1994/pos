<div class="modal-dialog modal-xl no-print" role="document">
  <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('sale.sell_details') (<b>@lang('sale.invoice_no'):</b> {{ $sell->invoice_no }})
    </h4>
</div>
<div class="modal-body">
    <div class="row">
      <div class="col-xs-12">
          <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($sell->transaction_date) }}</p>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <b>{{ __('sale.invoice_no') }}:</b> #{{ $sell->invoice_no }}<br>
        <b>{{ __('sale.status') }}:</b> 
          @if($sell->status == 'draft' && $sell->is_quotation == 1)
            {{ __('lang_v1.quotation') }}
          @else
            {{ ucfirst( $sell->status ) }}
          @endif
        <br>
        <b>{{ __('sale.payment_status') }}:</b> {{ ucfirst( $sell->payment_status ) }}<br>
      </div>
      <div class="col-sm-4">
        <b>{{ __('sale.customer_name') }}:</b> {{ $sell->contact->name }}<br>
        <b>{{ __('business.address') }}:</b><br>
        @if($sell->contact->landmark)
            {{ $sell->contact->landmark }}
        @endif

        {{ ', ' . $sell->contact->city }}

        @if($sell->contact->state)
            {{ ', ' . $sell->contact->state }}
        @endif
        <br>
        @if($sell->contact->country)
            {{ $sell->contact->country }}
        @endif
      </div>
    </div>
    <br>
    <div class="row">
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.products') }}:</h4>
      </div>

      <div class="col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <tr class="bg-green">
              <th>#</th>
              <th>{{ __('sale.product') }}</th>
              @if( session()->get('business.enable_lot_number') == 1)
                <th>{{ __('lang_v1.lot_n_expiry') }}</th>
              @endif
              <th>{{ __('sale.qty') }}</th>
              <th>{{ __('sale.unit_price') }}</th>
              <th>{{ __('sale.discount') }}</th>
              <th>{{ __('sale.tax') }}</th>
              <th>{{ __('sale.price_inc_tax') }}</th>
              <th>{{ __('sale.subtotal') }}</th>
            </tr>
            @foreach($sell->sell_lines as $sell_line)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                  {{ $sell_line->product->name }}
                  @if( $sell_line->product->type == 'variable')
                    - {{ $sell_line->variations->product_variation->name or ''}}
                    - {{ $sell_line->variations->name or ''}},
                   @endif
                   {{ $sell_line->variations->sub_sku or ''}}
                    @php
                      $brand = $sell_line->product->brand;
                    @endphp
                    @if(!empty($brand->name))
                      , {{$brand->name}}
                    @endif

                    @if(!empty($sell_line->sell_line_note))
                    <br> {{$sell_line->sell_line_note}}
                    @endif
                </td>
                @if( session()->get('business.enable_lot_number') == 1)
                  <td>{{ $sell_line->lot_details->lot_number or '--' }}
                  @if( session()->get('business.enable_product_expiry') == 1 && !empty($sell_line->lot_details->exp_date))
                    ({{@format_date($sell_line->lot_details->exp_date)}})
                  @endif
                  </td>
                @endif
                <td>{{ $sell_line->quantity }}</td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ $sell_line->unit_price }}</span>
                </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ $sell_line->get_discount_amount() }}</span> @if($sell_line->line_discount_type == 'percentage') ({{$sell_line->line_discount_amount}}%) @endif
                </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ $sell_line->item_tax }}</span> 
                  @if(!empty($taxes[$sell_line->tax_id]))
                    ( {{ $taxes[$sell_line->tax_id]}} )
                  @endif
                </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ $sell_line->unit_price_inc_tax }}</span>
                </td>
                <td>
                  <span class="display_currency" data-currency_symbol="true">{{ $sell_line->quantity * $sell_line->unit_price_inc_tax }}</span>
                </td>
              </tr>
              @if(!empty($sell_line->modifiers))
                @foreach($sell_line->modifiers as $modifier)
                  <tr>
                    <td>&nbsp;</td>
                    <td>
                      {{ $modifier->product->name }} - {{ $modifier->variations->name or ''}},
                      {{ $modifier->variations->sub_sku or ''}}
                    </td>
                    @if( session()->get('business.enable_lot_number') == 1)
                    <td>&nbsp;</td>
                    @endif
                    <td>{{ $modifier->quantity }}</td>
                    <td>
                      <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price }}</span>
                    </td>
                    <td>
                      &nbsp;
                    </td>
                    <td>
                      <span class="display_currency" data-currency_symbol="true">{{ $modifier->item_tax }}</span> 
                      @if(!empty($taxes[$modifier->tax_id]))
                        ( {{ $taxes[$modifier->tax_id]}} )
                      @endif
                    </td>
                    <td>
                      <span class="display_currency" data-currency_symbol="true">{{ $modifier->unit_price_inc_tax }}</span>
                    </td>
                    <td>
                      <span class="display_currency" data-currency_symbol="true">{{ $modifier->quantity * $modifier->unit_price_inc_tax }}</span>
                    </td>
                  </tr>
                @endforeach
              @endif
            @endforeach
          </table>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12 col-xs-12">
        <h4>{{ __('sale.payment_info') }}:</h4>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <tr class="bg-green">
              <th>#</th>
              <th>{{ __('messages.date') }}</th>
              <th>{{ __('purchase.ref_no') }}</th>
              <th>{{ __('sale.amount') }}</th>
              <th>{{ __('sale.payment_mode') }}</th>
              <th>{{ __('sale.payment_note') }}</th>
            </tr>
            @php
              $total_paid = 0;
            @endphp
            @foreach($sell->payment_lines as $payment_line)
              @php
                if($payment_line->is_return == 1){
                  $total_paid -= $payment_line->amount;
                } else {
                  $total_paid += $payment_line->amount;
                }
              @endphp
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ @format_date($payment_line->paid_on) }}</td>
                <td>{{ $payment_line->payment_ref_no }}</td>
                <td><span class="display_currency" data-currency_symbol="true">{{ $payment_line->amount }}</span></td>
                <td>
                  {{ ucfirst($payment_line->method) }}
                  @if($payment_line->is_return == 1)
                    <br/>
                    ( {{ __('lang_v1.change_return') }} )
                  @endif
                </td>
                <td>@if($payment_line->note) 
                  {{ ucfirst($payment_line->note) }}
                  @else
                  --
                  @endif
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
      <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="table-responsive">
          <table class="table bg-gray">
            <tr>
              <th>{{ __('sale.total') }}: </th>
              <td></td>
              <td><span class="display_currency pull-right">{{ $sell->total_before_tax }}</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.order_tax') }}:</th>
              <td><b>(+)</b></td>
              <td><span class="display_currency pull-right">{{ $sell->tax_amount }}</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.discount') }}:</th>
              <td><b>(-)</b></td>
              <td><span class="pull-right">{{ $sell->discount_amount }} @if( $sell->discount_type == 'percentage') {{ '%'}} @endif</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.shipping') }}: @if($sell->shipping_details)({{$sell->shipping_details}}) @endif</th>
              <td><b>(+)</b></td>
              <td><span class="display_currency pull-right">{{ $sell->shipping_charges }}</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.total_payable') }}: </th>
              <td></td>
              <td><span class="display_currency pull-right">{{ $sell->final_total }}</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.total_paid') }}:</th>
              <td></td>
              <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ $total_paid }}</span></td>
            </tr>
            <tr>
              <th>{{ __('sale.total_remaining') }}:</th>
              <td></td>
              <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ $sell->final_total - $total_paid }}</span></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6">
        <strong>{{ __( 'sale.sell_note')}}:</strong><br>
        <p class="well well-sm no-shadow bg-gray">
          @if($sell->additional_notes)
            {{ $sell->additional_notes }}
          @else
            --
          @endif
        </p>
      </div>
      <div class="col-sm-6">
        <strong>{{ __( 'sale.staff_note')}}:</strong><br>
        <p class="well well-sm no-shadow bg-gray">
          @if($sell->staff_note)
            {{ $sell->staff_note }}
          @else
            --
          @endif
        </p>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#" class="print-invoice btn btn-primary" data-href="{{route('sell.printInvoice', [$sell->id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>
      <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    var element = $('div.modal-xl');
    __currency_convert_recursively(element);
  });
</script>
