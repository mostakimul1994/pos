<div class="modal-dialog modal-xl no-print" role="document">
  <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="modalTitle"> @lang('lang_v1.sell_return') (<b>@lang('purchase.ref_no'):</b> {{ $purchase->invoice_no }})
    </h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-xs-12">
        <p class="pull-right"><b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}</p>
    </div>
  </div>
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col word-wrap">
      @lang('purchase.supplier'):
      <address>
        <strong>{{ $purchase->contact->supplier_business_name }}</strong>
        {{ $purchase->contact->name }}
        @if(!empty($purchase->contact->landmark))
          <br>{{$purchase->contact->landmark}}
        @endif
        @if(!empty($purchase->contact->city) || !empty($purchase->contact->state) || !empty($purchase->contact->country))
          <br>{{implode(',', array_filter([$purchase->contact->city, $purchase->contact->state, $purchase->contact->country]))}}
        @endif
        @if(!empty($purchase->contact->tax_number))
          <br>@lang('contact.tax_no'): {{$purchase->contact->tax_number}}
        @endif
        @if(!empty($purchase->contact->mobile))
          <br>@lang('contact.mobile'): {{$purchase->contact->mobile}}
        @endif
        @if(!empty($purchase->contact->email))
          <br>Email: {{$purchase->contact->email}}
        @endif
      </address>
    </div>

    <div class="col-md-4 invoice-col word-wrap">
      @lang('business.business'):
      <address>
        <strong>{{ $purchase->business->name }}</strong>
        <br/>{{ $purchase->location->name }}
        @if(!empty($purchase->location->landmark))
          <br>{{$purchase->location->landmark}}
        @endif
        @if(!empty($purchase->location->city) || !empty($purchase->location->state) || !empty($purchase->location->country))
          <br>{{implode(',', array_filter([$purchase->location->city, $purchase->location->state, $purchase->location->country]))}}
        @endif
        
        @if(!empty($purchase->business->tax_number_1))
          <br>{{$purchase->business->tax_label_1}}: {{$purchase->business->tax_number_1}}
        @endif

        @if(!empty($purchase->business->tax_number_2))
          <br>{{$purchase->business->tax_label_2}}: {{$purchase->business->tax_number_2}}
        @endif

        @if(!empty($purchase->location->mobile))
          <br>@lang('contact.mobile'): {{$purchase->location->mobile}}
        @endif
        @if(!empty($purchase->location->email))
          <br>@lang('business.email'): {{$purchase->location->email}}
        @endif
      </address>
    </div>

  <div class="col-sm-4 invoice-col">
    <b>@lang('purchase.ref_no'):</b> #{{ $purchase->invoice_no }}<br/>
    <b>@lang('messages.date'):</b> {{ @format_date($purchase->transaction_date) }}<br/>
  </div>
</div>

@php
  $hide_tax = '';
  if( session()->get('business.enable_inline_tax') == 0){
    $hide_tax = 'hide';
  }
@endphp

  <br>
  <div class="row">
    <div class="col-xs-12">
      <div class="table-responsive">
        <table class="table bg-gray">
          <tr class="bg-green">
            <th>#</th>
            <th class="text-center">@lang('sale.product')</th>
            <th class="text-center">@lang('sale.qty')</th>
            <th class="text-center">@lang('sale.unit_price')</th>
            <th class="text-center {{$hide_tax}}">@lang('sale.tax')</th>
            <th class="text-center {{$hide_tax}}">@lang('sale.price_inc_tax')</th>
            <th class="text-center">@lang('sale.subtotal')</th>
          </tr>

          @php 
            $total_before_tax = 0.00;
          @endphp

          @foreach($purchase->purchase_lines as $purchase_line)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>
                {{ $purchase_line->product->name }}
                 @if( $purchase_line->product->type == 'variable')
                  - {{ $purchase_line->variations->product_variation->name}}
                  - {{ $purchase_line->variations->name}}
                 @endif
              </td>
              <td>{{ $purchase_line->quantity }}</td>

              <td>
                <span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price}}</span>
              </td>
              
              <td><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->item_tax }} </span> <br/><small>@if($purchase_line->tax_id) ( {{ $taxes[$purchase_line->tax_id]}} ) </small>@endif</td>
              
              <td><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price_inc_tax }}</span></td>

              
              <td><span class="display_currency" data-currency_symbol="true">{{ $purchase_line->purchase_price_inc_tax * $purchase_line->quantity }}</span></td>
            </tr>
            @php 
              $total_before_tax += ($purchase_line->quantity * $purchase_line->purchase_price);
            @endphp
          @endforeach
        </table>
      </div>
    </div>
  </div>

  <br>
  <div class="row">
    <div class="col-xs-6 col-md-offset-6">
      <div class="table-responsive">
        <table class="table">
          <!-- <tr class="hide">
            <th>@lang('purchase.total_before_tax'): </th>
            <td></td>
            <td><span class="display_currency pull-right">{{ $total_before_tax }}</span></td>
          </tr> -->
          <tr>
            <th>@lang('purchase.net_total_amount'): </th>
            <td></td>
            <td><span class="display_currency pull-right">{{ $total_before_tax }}</span></td>
          </tr>
          <tr>
            <th>@lang('purchase.discount'):</th>
            <td>
              <b>(-)</b>
              @if($purchase->discount_type == 'percentage')
                ({{$purchase->discount_amount}} %)
              @endif
            </td>
            <td>
              <span class="display_currency pull-right">
                @if($purchase->discount_type == 'percentage')
                  {{$purchase->discount_amount * $total_before_tax / 100}}
                @else
                  {{$purchase->discount_amount}}
                @endif                  
              </span>
            </td>
          </tr>
          <tr>
            <th>@lang('lang_v1.total_credit_amt'):</th>
            <td></td>
            <td><span class="display_currency pull-right" data-currency_symbol="true" >{{ $purchase->final_total }}</span></td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-6 col-md-offset-6">
      <strong>@lang('purchase.additional_notes'):</strong><br>
      <p class="well well-sm no-shadow bg-gray">
        @if($purchase->additional_notes)
          {{ $purchase->additional_notes }}
        @else
          --
        @endif
      </p>
    </div>
  </div>

  {{-- Barcode --}}
  <div class="row print_section">
    <div class="col-xs-12">
      <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($purchase->ref_no, 'C128', 2,30,array(39, 48, 54), true)}}">
    </div>
  </div>
</div>
<div class="modal-footer">
    <a href="#" class="print-invoice btn btn-primary" data-href="{{action('SellReturnController@printInvoice', [$purchase->id])}}"><i class="fa fa-print" aria-hidden="true"></i> @lang("messages.print")</a>
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