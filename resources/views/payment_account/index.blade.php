@extends('layouts.app')
@section('title', 'Payment Account')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'lang_v1.payment_account' )
        <small>@lang( 'lang_v1.manage_payment_account' )</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">

	<div class="box">
        <div class="box-header">
        	<h3 class="box-title">@lang( 'lang_v1.all_payments' )</h3>
            @can('payment_account.create')
            	<div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary add_payment_account" 
                    data-container=".payment_account_model"
                    data-href="{{action('PaymentAccountController@create')}}">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
            @endcan
        </div>

        <div class="box-body">
            @can('payment_account.view')
                <div class="table-responsive">
            	<table class="table table-bordered table-striped" id="payment_account_table">
            		<thead>
            			<tr>
                            <th>@lang( 'lang_v1.name' )</th>
            				<th>@lang( 'lang_v1.payment_type' )</th>
                            <th>@lang( 'lang_v1.payment_note' )</th>
            				<th>@lang( 'messages.action' )</th>
            			</tr>
            		</thead>
            	</table>
                </div>
            @endcan
        </div>

    </div>
    

    <div class="modal fade payment_account_model" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    $(document).ready(function(){

        $(document).on('click', 'button.delete_payment_account', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                     var url = $(this).data('url');
                     var data = $(this).serialize();

                     $.ajax({
                         method: "DELETE",
                         url: url,
                         dataType: "json",
                         data: data,
                         success: function(result){
                             if(result.success == true){
                                toastr.success(result.msg);
                                payment_account_table.ajax.reload();
                             }else{
                                toastr.error(result.msg);
                            }

                        }
                    });
                }
            });
        });


        $(document).on('click', 'button.edit_payment_account', function(){
            $("div.payment_account_model").load($(this).data('url'),function(){
                $(this).modal('show');
                $('form#edit_payment_account_form').submit(function(e){
                    e.preventDefault();
                    var data = $(this).serialize();
                    $.ajax({
                        method: "POST",
                        url: $(this).attr("action"),
                        dataType: "json",
                        data: data,
                        success:function(result){
                            if(result.success == true){
                                $('div.payment_account_model').modal('hide');
                                toastr.success(result.msg);
                                payment_account_table.ajax.reload();
                            }else{
                                toastr.error(result.msg);
                            }
                        }
                    });
                });
            });
        });
        
        $('button.add_payment_account').click(function(){
            $("div.payment_account_model").load($(this).data('href'),function(){
                $(this).modal('show');
                $('form#payment_account_form').submit(function(e){
                    e.preventDefault();
                    var data = $(this).serialize();
                    $.ajax({
                        method: "post",
                        url: $(this).attr("action"),
                        dataType: "json",
                        data: data,
                        success:function(result){
                            if(result.success == true){
                                $('div.payment_account_model').modal('hide');
                                toastr.success(result.msg);
                                payment_account_table.ajax.reload();
                            }else{
                                toastr.error(result.msg);
                            }
                        }
                    });
                });

            });    
        });

        // payment_account_table
        var payment_account_table = $('#payment_account_table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: '/payment-account',
                        columnDefs:[{
                                "targets": 3,
                                "orderable": false,
                                "searchable": false
                            }]
                    });

    });
</script>
@endsection